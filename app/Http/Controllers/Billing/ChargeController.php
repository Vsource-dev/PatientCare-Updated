<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bill;
use App\Models\BillItem;
use App\Models\Patient;
use App\Models\HospitalService;
use Illuminate\Support\Facades\Auth;

class ChargeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:billing']);
    }

    // List all manual charges (bill items)
    public function index()
    {
        $items = BillItem::with(['bill.patient', 'service.department'])
                    ->latest('billing_item_id')
                    ->paginate(15);

        return view('billing.charges.index', compact('items'));
    }

    // Show form to create a new manual charge
    public function create()
    {
        $patients = Patient::orderBy('patient_last_name')->get();
        // allow any service type for manual charges
        $services = HospitalService::orderBy('service_name')->get();

        return view('billing.charges.create', compact('patients','services'));
    }
public function store(Request $request)
{
    $patient = Patient::findOrFail($request->patient_id);
    if ($patient->billing_closed_at) {
        return back()->with('error', 'Action failed: The patient\'s bill is locked.');
    }
    $data = $request->validate([
        'patient_id'           => 'required|exists:patients,patient_id',
        'charges'              => 'required|array|min:1',
        'charges.*.service_id' => 'required|exists:hospital_services,service_id',
        'charges.*.quantity'   => 'required|integer|min:1',
    ]);

    // 1) One Bill per day per patient
    $bill = Bill::firstOrCreate([
        'patient_id'   => $data['patient_id'],
        'billing_date' => now()->toDateString(),
    ],[
        'admission_id'   => null,        // if you allow manual charges outside admissions
        'payment_status' => 'pending',
    ]);

    // 2) Create each BillItem
    foreach ($data['charges'] as $row) {
        $svc     = HospitalService::findOrFail($row['service_id']);
        $qty     = $row['quantity'];
        $amount  = $svc->price * $qty;

        BillItem::create([
            'billing_id'   => $bill->billing_id,
            'service_id'   => $svc->service_id,
            'quantity'     => $qty,
            'amount'       => $amount,
            'billing_date' => $bill->billing_date, // satisfies non-null constraint
        ]);
    }

    return redirect()
        ->route('billing.charges.index')
        ->with('success', 'Manual charges posted successfully.');
}



    // Show a single manual charge
    public function show($itemId)
    {
        $item = BillItem::with(['bill.patient','service.department','logs'])
                ->findOrFail($itemId);

        return view('billing.charges.show', compact('item'));
    }

    // Edit a manual charge
    public function edit($itemId)
    {
        $item = BillItem::findOrFail($itemId);
        $patients = Patient::all();
        $services = HospitalService::all();

        return view('billing.charges.edit', compact('item','patients','services'));
    }

    // Update a manual charge
    public function update(Request $request, $itemId)
    {
        $item = BillItem::findOrFail($itemId);

        $data = $request->validate([
            'patient_id' => 'required|exists:patients,patient_id',
            'service_id' => 'required|exists:hospital_services,service_id',
            'quantity'   => 'required|integer|min:1',
            'amount'     => 'required|numeric|min:0',
        ]);

        $item->update($data);

        return redirect()->route('billing.charges.index')
                         ->with('success','Charge updated successfully.');
    }

    // Delete a manual charge
    public function destroy($itemId)
    {
        BillItem::findOrFail($itemId)->delete();
        return back()->with('success','Charge deleted.');
    }

    // Audit log for a manual charge
    public function audit($itemId)
    {
        $item = BillItem::with('logs')->findOrFail($itemId);
        return view('billing.charges.audit', compact('item'));
    }
    public function toggleLock(Patient $patient)
    {
        if ($patient->billing_closed_at) {
            // Bill is currently LOCKED, so UNLOCK it.
            $patient->billing_closed_at = null;
            $message = 'unlocked';
        } else {
            // Bill is currently UNLOCKED, so LOCK it.
            $patient->billing_closed_at = now();
            $message = 'locked';
        }

        $patient->save();

        return back()->with('success', "Patient's bill has been successfully {$message}.");
    }
}
