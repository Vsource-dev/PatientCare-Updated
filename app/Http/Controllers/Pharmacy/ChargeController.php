<?php
// app/Http/Controllers/Pharmacy/ChargeController.php
namespace App\Http\Controllers\Pharmacy;
use App\Http\Controllers\Controller;
use App\Models\PharmacyCharge;
use App\Models\PharmacyChargeItem;
use App\Models\Patient;
use App\Models\HospitalService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Doctor; 

class ChargeController extends Controller
{
  public function index()
    {
        // 1) Load existing charges
        $charges = PharmacyCharge::with('patient','items')
                    ->latest()
                    ->paginate(15);

        // 2) Load data for your “New Charge” modal
        $patients = Patient::orderBy('patient_last_name')->get();
        $services = HospitalService::where('service_type', 'medication')
                       ->orderBy('service_name')
                       ->get();

        // 3) Pass all three to the single index view
        return view('pharmacy.charges.index', compact(
            'charges',
            'patients',
            'services'
        ));
    }
 public function create()
    {
        // only medication‐type services
        $services = HospitalService::where('service_type', 'medication')
                       ->orderBy('service_name')
                       ->get();

        // all patients
        $patients = Patient::orderBy('patient_last_name')
                       ->get();

        // all doctors
        $doctors = Doctor::orderBy('doctor_name')
                   ->get();

        // pass all three to the view
        return view('pharmacy.charges.create', compact('services','patients','doctors'));
    }

    /**
     * Store a newly created charge in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_id'           => 'required|exists:patients,patient_id',
            'prescribing_doctor'   => 'required|string|max:255',
            'rx_number'            => 'required|string|max:100|unique:pharmacy_charges,rx_number',
            'notes'                => 'nullable|string',
            'medications'          => 'required|array|min:1',
            'medications.*.service_id'  => 'required|exists:hospital_services,service_id',
            'medications.*.quantity'    => 'required|integer|min:1',
        ]);

        // Create the charge (initial total_amount = 0)
        $charge = PharmacyCharge::create([
            'patient_id'         => $data['patient_id'],
            'prescribing_doctor' => $data['prescribing_doctor'],
            'rx_number'          => $data['rx_number'],
            'notes'              => $data['notes'] ?? null,
            'total_amount'       => 0,
        ]);

        // Attach items and accumulate total
        $total = 0;
        foreach ($data['medications'] as $item) {
            $svc = HospitalService::findOrFail($item['service_id']);
            $line = $svc->price * $item['quantity'];

            $charge->items()->create([
                'service_id' => $svc->service_id,
                'quantity'   => $item['quantity'],
                'unit_price' => $svc->price,
                'total'      => $line,
            ]);

            $total += $line;
        }

        // Update the real total
        $charge->update(['total_amount' => $total]);

        return redirect()
            ->route('pharmacy.dashboard', $charge)
            ->with('success','Medication charge created.');
    }

   public function show(PharmacyCharge $charge)
{
    $charge->load('patient','items.service');
    return view('pharmacy.charges.show', compact('charge'));
}


    /**
     * Show the form for editing the specified charge.
     */
    public function edit(PharmacyCharge $charge)
    {
        $patients = Patient::all();
        $services = HospitalService::all();
        $charge->load('items');

        return view('pharmacy.charges.edit', compact('charge','patients','services'));
    }

    /**
     * Update the specified charge in storage.
     */
    public function update(Request $request, PharmacyCharge $charge)
    {
        $data = $request->validate([
            'patient_id'           => 'required|exists:patients,patient_id',
            'prescribing_doctor'   => 'required|string|max:255',
            'rx_number'            => [
                'required','string','max:100',
                Rule::unique('pharmacy_charges','rx_number')->ignore($charge->id),
            ],
            'notes'                => 'nullable|string',
            'medications'          => 'required|array|min:1',
            'medications.*.service_id'  => 'required|exists:hospital_services,service_id',
            'medications.*.quantity'    => 'required|integer|min:1',
        ]);

        // Update charge header
        $charge->update([
            'patient_id'         => $data['patient_id'],
            'prescribing_doctor' => $data['prescribing_doctor'],
            'rx_number'          => $data['rx_number'],
            'notes'              => $data['notes'] ?? null,
            'total_amount'       => 0,
        ]);

        // Rebuild items
        $charge->items()->delete();
        $total = 0;
        foreach ($data['medications'] as $item) {
            $svc = HospitalService::findOrFail($item['service_id']);
            $line = $svc->price * $item['quantity'];
            $charge->items()->create([
                'service_id' => $svc->service_id,
                'quantity'   => $item['quantity'],
                'unit_price' => $svc->price,
                'total'      => $line,
            ]);
            $total += $line;
        }
        $charge->update(['total_amount' => $total]);

        return redirect()
            ->route('pharmacy.show', $charge)
            ->with('success','Charge updated.');
    }

    /**
     * Remove the specified charge from storage.
     */
    public function destroy(PharmacyCharge $charge)
    {
        $charge->delete();

        return redirect()
            ->route('pharmacy.charges.index')
            ->with('success','Charge deleted.');
    }
}
