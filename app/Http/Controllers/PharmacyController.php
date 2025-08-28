<?php
//app/Http/Controllers/PharmacyController.php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\HospitalService as Service;
use App\Models\PharmacyCharge;
use App\Models\PharmacyChargeItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PharmacyController extends Controller
{
    // Restrict access to pharmacy role
    public function __construct()
    {
        $this->middleware(['auth','role:pharmacy']);
    }

    /**
     * Show pharmacy dashboard with stats and recent charges.
     */
    public function index(Request $request)
    {
        $totalCharges   = PharmacyCharge::completed()->count();
        $pendingCharges = PharmacyCharge::pending()->count();
        $patientsServed = PharmacyCharge::completed()->distinct('patient_id')->count('patient_id');

        // Build query for searching/filtering
        $query = PharmacyCharge::with('patient','items');

        // Search by RX number or patient name
        if ($q = $request->input('q')) {
            $query->where(function($sub) use ($q) {
                $sub->where('rx_number','like',"%$q%")
                    ->orWhereHas('patient', fn($p) =>
                        $p->where(DB::raw("CONCAT(patient_first_name,' ',patient_last_name)"), 'like', "%$q%"));
            });
        }

        // Filter by date range
        if ($from = $request->input('from')) {
            $query->whereDate('created_at','>=',$from);
        }
        if ($to = $request->input('to')) {
            $query->whereDate('created_at','<=',$to);
        }

        // Today's charges
        $todayCharges = (clone $query)
            ->whereDate('created_at', now()->toDateString())
            ->orderByDesc('created_at')
            ->get();

        // Earlier charges (before today, limit 10)
        $earlierCharges = (clone $query)
            ->whereDate('created_at','<', now()->toDateString())
            ->orderByDesc('created_at')
            ->take(10)
            ->get();

        return view('pharmacy.dashboard', compact(
            'totalCharges',
            'patientsServed',
            'pendingCharges',
            'todayCharges',
            'earlierCharges'
        ));
    }

    /**
     * Mark a whole charge as dispensed (all items).
     * Used for full dispensing, not partial.
     */
    public function dispense(PharmacyCharge $charge)
    {
        if ($charge->status === 'completed') {
            return back()->with('info','Already marked as dispensed.');
        }

        // Mark all items as dispensed
        foreach ($charge->items as $item) {
            $item->status = 'dispensed';
            $item->save();
        }

        $charge->update([
            'status'       => 'completed',
            'dispensed_at' => now(),
        ]);

        // Optionally notify the patient
        // $charge->patient->notify(new PharmacyChargeDispensed($charge));

        return back()->with('success','Medication dispensed & flagged for billing.');
    }

    /**
     * Show form to create a new charge (manual/walk-in).
     */
    public function create()
    {
        $patients = Patient::where('status','active')
                    ->orderBy('patient_last_name')
                    ->get();

        $services = Service::with('department')->get();

        return view('pharmacy.create', compact('patients','services'));
    }

    /**
     * Store a new charge (manual/walk-in).
     */
    public function store(Request $request)
    {
        $patient = Patient::findOrFail($request->patient_id);
        if ($patient->billing_closed_at) {
            return back()->with('error', 'Action failed: The patient\'s bill is locked.');
        }

        $data = $request->validate([
            'patient_id'         => 'required|exists:patients,patient_id',
            'prescribing_doctor' => 'required|string|max:255',
            'rx_number'          => 'required|string|max:100',
            'notes'              => 'nullable|string',
            'medications'        => 'required|array|min:1',
            'medications.*.service_id' => 'required|exists:hospital_services,service_id',
            'medications.*.quantity'   => 'required|integer|min:1',
        ]);

        // Transaction: create charge and items
        DB::transaction(function() use($data, &$charge) {
            $charge = PharmacyCharge::create([
                'patient_id'         => $data['patient_id'],
                'prescribing_doctor' => $data['prescribing_doctor'],
                'rx_number'          => $data['rx_number'],
                'notes'              => $data['notes'] ?? null,
                'total_amount'       => 0,
                'status'             => 'pending', // Ensure status is set
            ]);

            $grandTotal = 0;
            foreach ($data['medications'] as $item) {
                $service   = Service::findOrFail($item['service_id']);
                $lineTotal = $service->price * $item['quantity'];
                $grandTotal += $lineTotal;

                PharmacyChargeItem::create([
                    'charge_id'   => $charge->id,
                    'service_id'  => $service->service_id,
                    'quantity'    => $item['quantity'],
                    'unit_price'  => $service->price,
                    'total'       => $lineTotal,
                    'status'      => 'pending', // Track per-item status
                ]);
            }

            $charge->update(['total_amount' => $grandTotal]);
        });

        // Optionally notify the patient
        // $charge->patient->notify(new PharmacyChargeCreated($charge));

        return redirect()
            ->route('pharmacy.index')
            ->with('success', 'Medication charge created successfully.');
    }

    /**
     * Show details for a single medication charge.
     */
    public function show(PharmacyCharge $charge)
    {
        $charge->load([
            'patient',
            'items.service.department'
        ]);

        return view('pharmacy.show', compact('charge'));
    }

    /**
     * Show the queue of pending approvals (requests from doctors).
     */
    public function queue()
    {
        $pendingCharges = PharmacyCharge::where('status', 'pending')
            ->with(['patient', 'items.service'])
            ->orderBy('created_at', 'asc')
            ->get();

        return view('pharmacy.queue', compact('pendingCharges'));
    }

    /**
     * Show modal/page to select which items to dispense for a charge.
     * Used for partial dispensing.
     */
    public function selectItems(PharmacyCharge $charge)
    {
        $charge->load(['items.service', 'patient']);
        return view('pharmacy.select-items', compact('charge'));
    }

    /**
     * Handle partial dispensing: only selected items are dispensed.
     * Updates per-item status and charge status if all are dispensed.
     */
    public function partialDispense(Request $request, PharmacyCharge $charge)
    {
        $selected = $request->input('items', []); // array of item IDs to dispense

        foreach ($charge->items as $item) {
            if (in_array($item->id, $selected) && $item->status !== 'dispensed') {
                $item->status = 'dispensed';
                $item->save();
            }
            // Do NOT set others back to pending!
        }

        // If all items are dispensed, mark charge as completed
        if ($charge->items()->where('status', 'pending')->count() == 0) {
            $charge->status = 'completed';
            $charge->dispensed_at = now();
            $charge->save();
        } else {
            $charge->status = 'pending';
            $charge->save();
        }

        return redirect()->route('pharmacy.queue')->with('success', 'Selected items dispensed and billed.');
    }

    public function history()
    {
        $completedCharges = PharmacyCharge::where('status', 'completed')
            ->with(['patient', 'items.service'])
            ->orderByDesc('dispensed_at')
            ->get();

        $partialCharges = PharmacyCharge::where('status', 'pending')
            ->whereHas('items', fn($q) => $q->where('status', 'dispensed'))
            ->with(['patient', 'items.service'])
            ->orderByDesc('updated_at')
            ->get();

        return view('pharmacy.history', compact('completedCharges', 'partialCharges'));
    }

}