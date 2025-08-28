<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MiscellaneousCharge;
use App\Models\Patient;
use Illuminate\Support\Facades\DB; 
use App\Models\HospitalService;
use App\Models\Department;
use App\Models\BillItem;
use App\Models\AuditLog;
use App\Models\Bill;
use App\Models\AdmissionDetail;
use App\Notifications\SupplyChargeCreated;
use App\Notifications\SupplyChargeCompleted;

class SuppliesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // only logged-in staff
    }

    /**
     * GET /supplies/dashboard
     * Show a summary panel + recent 5 supplies
     */
  public function dashboard()
    {
        // 1) All supplies ever given
        $suppliesGiven = MiscellaneousCharge::all();

        // 2) Unique patients served
        $patientsServe = MiscellaneousCharge::distinct('patient_id')->count('patient_id');

        // 3) Pending orders count
        $pendingOrders = MiscellaneousCharge::where('status', 'pending')->count();
        $services = HospitalService::with('department')
        ->whereHas('department', fn($q) =>
            $q->where('department_name', 'Supplies')
        )
        ->orderBy('service_name')
        ->get();

    // only the Supplies department itself
    $departments = Department::where('department_name', 'Supplies')->get();

        $recentSupplies = MiscellaneousCharge::with(['patient','service','creator'])
            ->latest('created_at')
            ->take(5)
            ->get();

        // 5) Top‐served items (by quantity)
        $mostServedSupply = MiscellaneousCharge::select('service_id', DB::raw('SUM(quantity) as total_qty'))
            ->groupBy('service_id')
            ->orderByDesc('total_qty')
            ->with('service')
            ->take(5)
            ->get();
            return view('supplies.dashboard', compact(
                'suppliesGiven',
                'patientsServe',
                'pendingOrders',
                'recentSupplies',
                'mostServedSupply',
                'services',
                'departments'
            ));
    }

    public function queue(Request $request)
    {
        $query = MiscellaneousCharge::with([
                'patient.admissionDetail.doctor',
                'service.department',
                'creator',
                'completer',
            ])
            // ensure we’re only touching supplies
            ->whereHas('service.department', fn ($q) =>
                $q->where('department_name', 'Supplies')
            );
    
        // ── status filter ───────────────────────────────────────────────
        if ($request->filled('status')) {
            $query->where('status', $request->status);   // pending / completed
        }
    
        // ── name / MRN search ──────────────────────────────────────────
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->whereHas('patient', fn ($qp) =>
                        $qp->where(DB::raw("CONCAT(patient_first_name,' ',patient_last_name)"),
                                   'LIKE', "%{$s}%")
                           ->orWhere('patient_id', 'LIKE', "%{$s}%")
                );
            });
        }
    
        // ── date sort (default :newest first) ───────────────────────────
        $direction = in_array($request->date_range, ['asc', 'desc'])
                   ? $request->date_range
                   : 'desc';
        $query->orderBy('created_at', $direction);
    
        // paginate so the table never explodes
        $miscReq = $query->paginate(20)->withQueryString();
    
        return view('supplies.queue', compact('miscReq'));
    }
    

public function create()
{
    // only load services in the Supplies department
    $services = HospitalService::with('department')
        ->whereHas('department', fn($q) =>
            $q->where('department_name', 'Supplies')
        )
        ->orderBy('service_name')
        ->get();

    $patients = Patient::orderBy('patient_last_name')->get();

    return view('supplies.create', compact('patients', 'services'));
}


        public function destroyItem(HospitalService $service)
    {
        $service->delete();
        return redirect()->back()->with('success', 'Item deleted.');
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_id'             => 'required|exists:patients,patient_id',
            'notes'                  => 'nullable|string',
            'misc_item'              => 'required|array|min:1',
            'misc_item.*.service_id' => 'required|exists:hospital_services,service_id',
            'misc_item.*.quantity'   => 'required|integer|min:1',
        ]);

        $userId = Auth::id();

        $bill = Bill::firstOrCreate(
            ['patient_id'   => $data['patient_id'], 'billing_date' => now()->toDateString()],
            ['payment_status'=> 'pending']
        );

        foreach ($data['misc_item'] as $item) {
            $service = HospitalService::findOrFail($item['service_id']);
            $qty     = $item['quantity'];
            $total   = $service->price * $qty;

            // 1) BillItem
            $billItem = BillItem::create([
                'billing_id'   => $bill->billing_id,
                'service_id'   => $service->service_id,
                'quantity'     => $qty,
                'amount'       => $total,
                'billing_date' => $bill->billing_date,
            ]);

            // 2) MiscellaneousCharge
            $charge = MiscellaneousCharge::create([
                'patient_id' => $data['patient_id'],
                'service_id' => $service->service_id,
                'quantity'   => $qty,
                'unit_price' => $service->price,
                'total'      => $total,
                'status'     => 'pending',
                'notes'      => $data['notes'] ?? null,
                'created_by' => $userId,
            ]);

            // 3) Audit log
            AuditLog::create([
                'bill_item_id' => $billItem->billing_item_id,
                'action'       => 'create',
                'message'      => "Supply {$service->service_name} ×{$qty} (₱{$total}) added by ".Auth::user()->username,
                'actor'        => Auth::user()->username,
                'icon'         => 'fa-box',
            ]);

            // 4) Notify patient
            $charge->patient->notify(new SupplyChargeCreated($charge));
        }

        return redirect()
            ->route('supplies.queue')
            ->with('success', 'Supply charge(s) created.');
    }

       public function show($id)
    {
        $charge = MiscellaneousCharge::with([
                'patient.admissionDetail.doctor',  // ← same here
                'service',
                'creator',
                'completer',
            ])
            ->findOrFail($id);

        return view('supplies.show', compact('charge'));
    }
    public function markCompleted($id)
    {
        $charge = MiscellaneousCharge::findOrFail($id);
        $charge->update([
            'status'       => 'completed',
            'completed_by' => Auth::id(),
        ]);

        // notify patient that their supply request was completed
        $charge->patient->notify(new SupplyChargeCompleted($charge));

        return redirect()
            ->route('supplies.queue')
            ->with('success','Supply request marked completed.');
    }

    // OR, if you also use `checkout()`:
    public function checkout($id)
    {
        $charge = MiscellaneousCharge::findOrFail($id);
        $charge->update([
            'status'       => 'completed',
            'completed_by' => Auth::id(),
        ]);

        $charge->patient->notify(new SupplyChargeCompleted($charge));

        return redirect()
            ->route('supplies.queue')
            ->with('success', 'Supply charge has been checked out.');
    }
}
