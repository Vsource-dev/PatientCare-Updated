<?php

namespace App\Http\Controllers;

use App\Models\Dispute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class DisputeController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:patient')->only(['store', 'myDisputes']);
        $this->middleware('role:billing')->only(['queue', 'show', 'update']); // 'index' was redundant
    }

    /**
     * For Billing Staff: Shows a queue of disputes.
     */
    public function queue(Request $request)
    {
        $query = Dispute::query();

        if ($q = $request->input('q')) {
            $query->whereHas('patient', fn($q2) =>
                $q2->where('patient_first_name', 'like', "%{$q}%")
                  ->orWhere('patient_last_name', 'like', "%{$q}%")
                  ->orWhere('patient_id', $q)
            );
        }

        if ($status = $request->input('status')) {
            if ($status !== 'all') {
                $query->where('status', $status);
            }
        }

        // ✅ FIX: Eager load the polymorphic 'disputable' relationship
        $disputes = $query->with(['patient', 'disputable'])
                         ->latest('datetime')
                         ->paginate(15)
                         ->withQueryString();

        return view('billing.dispute.queue', compact('disputes'));
    }

    /**
     * For Patients: Stores a new dispute request.
     */
    public function store(Request $request)
    {
        \Log::debug('⏳ DisputeController@store start', $request->all());

        $request->validate([
            'bill_item_id' => 'required|string',
            'reason'       => 'required|string|max:2000',
        ]);

        $disputableId   = $request->bill_item_id;
        $disputableType = null;
        $modelId        = null;

        if (Str::startsWith($disputableId, 'SA-')) {
            $disputableType = \App\Models\ServiceAssignment::class;
            $modelId        = intval(Str::after($disputableId, 'SA-'));
        } elseif (Str::startsWith($disputableId, 'RX-')) {
            $disputableType = \App\Models\PharmacyChargeItem::class;
            // ✅ FIX: This was incorrectly looking for 'SA-' instead of 'RX-'
            $modelId        = intval(Str::after($disputableId, 'RX-'));
        } else {
            $disputableType = \App\Models\BillItem::class;
            $modelId        = intval($disputableId);
        }

        $disputable = $disputableType::findOrFail($modelId);

        // Authorization check
        $patientId = $disputable->patient_id ?? $disputable->charge->patient_id ?? $disputable->bill->patient_id ?? null;
        abort_if($patientId !== Auth::user()->patient_id, 403, 'You may dispute only your own charges.');

        $dispute = $disputable->disputes()->create([
            'patient_id' => Auth::user()->patient_id,
            'datetime'   => now(),
            'reason'     => $request->reason,
            'status'     => 'pending',
        ]);

        \Log::debug('✅ Dispute created', ['dispute_id' => $dispute->dispute_id]);

        $billingUsers = \App\Models\User::where('role', 'billing')->get();
        Notification::send($billingUsers, new \App\Notifications\DisputeFiled($dispute));

        return redirect()
            ->route('patient.disputes.mine')
            ->with('success', 'Your dispute has been filed! We’ll let you know when billing reviews it.');
    }

    public function myDisputes()
    {
        $disputes = Dispute::where('patient_id', auth()->user()->patient_id)
                            // ✅ FIX: Eager load the nested 'service' relationship
                            ->with(['disputable.service'])
                            ->latest('datetime')
                            ->paginate(10);
    
        return view('patient.disputes.index', compact('disputes'));
    }

    public function show(Dispute $dispute)
    {
        // 1. Load main dispute data and patient info with their latest admission details
        $dispute->load(['disputable.service.department']);
        $patient = $dispute->patient()->with('admissionDetail')->first();
        $disputed_charge = $dispute->disputable;
    
        // 2. Fetch ALL charges for this patient to display in the transaction history
        // Note: This logic can be moved to a dedicated service class later for cleanliness
        $bill_items = \App\Models\BillItem::whereHas('bill', fn($q) => $q->where('patient_id', $patient->patient_id))
            ->with('service.department')->get();
        
        // In a real-world scenario, you would also merge ServiceAssignments and PharmacyCharges here
        // For now, we will display the bill_items as the main transaction history.
        $all_charges = $bill_items;
    
        // 3. Calculate totals from the fetched charges
        $totalCharges = $all_charges->sum(function($item) {
            return $item->amount - ($item->discount_amount ?? 0);
        });
        $totalDeposits = \App\Models\Deposit::where('patient_id', $patient->patient_id)->sum('amount');
        $balance = $totalCharges - $totalDeposits;
    
        // 4. Get services for the "Manual Charge" modal
        $services = \App\Models\HospitalService::orderBy('service_type')->get();
    
        // 5. Pass all data to the view
        return view('billing.show', compact(
            'dispute',
            'patient',
            'disputed_charge',
            'all_charges',
            'totalCharges',
            'totalDeposits',
            'balance',
            'services'
        ));
    }
    /**
     * For Billing Staff: Approves or rejects a dispute.
     */
    public function update(Request $request, Dispute $dispute)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'notes'  => 'nullable|string|max:2000',
        ]);
        
        // ✅ FIX: Use the polymorphic relationship to get the disputed item.
        $disputedItem = $dispute->disputable;

        // Ensure the item is a model that has 'amount' and 'discount_amount' fields before updating.
        if (!($disputedItem instanceof \App\Models\BillItem)) {
             // For now, we can only auto-adjust BillItems.
             // You could add logic here for other types if needed.
            return back()->with('error', 'Cannot automatically adjust this charge type.');
        }

        DB::transaction(function () use ($request, $dispute, $disputedItem) {
            $dispute->update([
                'status'      => $request->action === 'approve' ? 'approved' : 'rejected',
                'approved_by' => auth()->id(),
            ]);

            if ($request->action === 'approve') {
                $disputedItem->update([
                    'discount_amount' => $disputedItem->amount,
                    'notes' => $request->notes ?? $disputedItem->notes,
                ]);
            }
        });

        $dispute->patient->notify(new \App\Notifications\DisputeResolved($dispute));

        // Redirect to the main queue for billing staff
        return redirect()
            ->route('billing.dispute.queue')
            ->with('success', 'Dispute processed.');
    }
}