<?php
// Controller for doctor-related actions

namespace App\Http\Controllers;

// Import necessary models and classes
use App\Models\Doctor;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Medication;      // Medication model
use App\Models\LabTest;         // LabTest model
use App\Models\ImagingStudy;    // ImagingStudy model
use App\Models\HospitalService; // HospitalService model
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\AdmissionDetail; // AdmissionDetail model
use App\Models\Bill;   
use Illuminate\Support\Facades\Auth;
use App\Models\ServiceAssignment;
use Carbon\Carbon;  
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\PharmacyCharge;
use App\Models\PharmacyChargeItem;
use Illuminate\Support\Str;

class DoctorController extends Controller
{
    // Doctor dashboard: shows patients and recent admissions
    public function dashboard(Request $request)
    {
        $q = $request->input('q'); // Get search query (Search bar)
        $user = Auth::user(); // Get current logged-in user
        $doctor = $user->doctor; // Get doctor profile
        $doctorId = optional($doctor)->doctor_id; // Doctor ID or null
        Log::debug("[DoctorDashboard] user_id {$user->user_id} doctorId {$doctorId}");

        // Query patients assigned to this doctor (Find patients assigned to this doctor)
        // whereHas: Filter a model based on conditions on its relationship
        $patientsQuery = Patient::whereHas('admissionDetail', function($admissionQuery) use ($doctorId) {
            $admissionQuery->where('doctor_id', $doctorId);
        });

        // Run the query and counts how many patients are matched
        $initialCount = $patientsQuery->count();

        // Debug Log: storage/logs/laravel.log.
        Log::debug("[DoctorDashboard] patientsQuery count before search: {$initialCount}");

        // If searching, filter by patient ID or name
        if ($q) { //Runs only if user typed on something
            $patientsQuery->where(function($w) use ($q) { // Groups multiple OR Conditions
                $w->where('patient_id', 'like', "%{$q}%") //Search patient ID
                  ->orWhere('patient_first_name', 'like', "%{$q}%") //Search Firstname
                  ->orWhere('patient_last_name', 'like', "%{$q}%"); //Search Lastname
            });

            // Debug Log: storage/logs/laravel.log.
            $countAfterSearch = $patientsQuery->count();
            Log::debug("[DoctorDashboard] patientsQuery count after search '{$q}': {$countAfterSearch}");
        }

        // Get paginated patients with room info
        $patients = $patientsQuery
            ->with('admissionDetail.room')
            ->orderBy('patient_last_name')
            ->paginate(10)
            ->withQueryString();
        Log::debug("[DoctorDashboard] paginated total {$patients->total()} current page count {$patients->count()}");

        // Get today's recent admissions for this doctor
        $recentAdmissions = AdmissionDetail::with('patient','room')
            ->where('doctor_id', $doctorId)
            ->whereDate('admission_date', Carbon::today())
            ->latest('admission_date')
            ->take(10)
            ->get();
        Log::debug("[DoctorDashboard] recentAdmissions count: " . count($recentAdmissions));

        // Return dashboard view with data
        return view('doctor.dashboard', [
            'patients' => $patients,
            'q' => $q,
            'recentAdmissions' => $recentAdmissions,
        ]);
    }

    // Show a single patient's details
    public function show(Patient $patient)
    {
        $patient->load('admissionDetail.room', 'medicalDetail'); // Fetch Room and Medical Detail
        return view('doctor.show', compact('patient')); // Reference in view as $patient
    }

    // Show order entry form for a patient
    public function orderEntry(Patient $patient)
    {
        $services = HospitalService::all(); // Get all hospital services MEDICATION/LAB/OR

        return view('doctor.order-entry', [
            // Removed Imaging and Added OR (Operating Room)
            'patient'        => $patient->load('medicalDetail','admissionDetail.room'),
            'medications'    => $services->where('service_type','medication'),
            'labTests'       => $services->where('service_type','lab'),
            'otherServices'  => $services->where('service_type','operation'),
        ]);
    }

    // Store an order (medication, laboratory, or OR)
    public function storeOrder(Request $request, Patient $patient)
    {
        // Prevent changes if billing is closed
        if ($patient->billing_closed_at) {
            return back()->with('error', 'Action failed: The patient\'s bill is locked.');
        }

        $rawPayload = $request->all(); // Get all input data from the request (for logging/debugging)
        $type       = $request->input('type'); // Get the type of order being submitted (medication, lab, service)
        $doctorId   = optional(Auth::user()->doctor)->doctor_id // Get the current user's doctor_id, or fallback to the first doctor in DB if not set
                    ?? Doctor::first()?->doctor_id; // Fallback if no doctor profile

        Log::debug('[OrderEntry] incoming request', [
            'user_id'  => Auth::id(), // Log the current user ID
            'patient'  => $patient->patient_id, // Log the patient ID
            'type'     => $type, // Log the order type
            'payload'  => $rawPayload, // Log the full request payload
        ]);

        if (! $doctorId) { // If no doctor ID could be resolved
            Log::warning('[OrderEntry] NO DOCTOR ID RESOLVED!');
            return back()->withErrors('No doctor profile found.'); // Return with error
        }

        // Handle medication orders
        if ($type === 'medication') {
            $data = $request->validate([ // Validate medication order input
                'medications'                 => 'required|array|min:1', // Must have at least one medication
                'medications.*.medication_id' => 'required|exists:hospital_services,service_id', // Each medication must exist
                'medications.*.quantity'      => 'required|integer|min:1', // Quantity must be at least 1
                'medications.*.duration'      => 'required|integer|min:1', // Duration must be at least 1
                'medications.*.duration_unit' => 'required|in:days,weeks', // Duration unit must be days or weeks
                'medications.*.instructions'  => 'nullable|string', // Instructions are optional
                'refills' => 'nullable|integer|min:0', // Refills are optional
                'daw'     => 'nullable|boolean', // DAW (dispense as written) is optional
            ]);

            $refills = $data['refills'] ?? 0; // Default refills to 0 if not set
            $daw     = $data['daw'] ?? false; // Default DAW to false if not set

            DB::beginTransaction(); // Start DB transaction
            try {
                // Create or get today's bill for this patient
                $bill = Bill::firstOrCreate(
                    [
                        'patient_id'   => $patient->patient_id, // Bill for this patient
                        'admission_id' => optional($patient->admissionDetail)->admission_id, // For this admission
                        'billing_date' => today(), // For today
                    ],
                    ['payment_status' => 'pending'] // Default status
                );

                // Create prescription header
                $prescription = Prescription::create([
                    'patient_id' => $patient->patient_id, // Link to patient
                    'doctor_id'  => $doctorId, // Link to doctor
                    'refills'    => $refills, // Number of refills
                    'daw'        => $daw, // Dispense as written
                ]);

                // Create pharmacy charge header
                $rxNumber = 'RX' . now()->format('YmdHis') . Str::upper(Str::random(3)); // Generate RX number
                $pharmCharge = PharmacyCharge::create([
                    'patient_id'         => $patient->patient_id, // Link to patient
                    'prescribing_doctor' => Doctor::find($doctorId)->doctor_name ?? '-', // Doctor name
                    'rx_number'          => $rxNumber, // RX number
                    'notes'              => $data['medications'][0]['instructions'] ?? null, // Notes from first medication
                    'total_amount'       => 0, // Will be updated later
                    'status'             => 'pending', // Initial status
                ]);

                $grandTotal = 0; // Track total charge

                // Loop through each medication row
                foreach ($data['medications'] as $row) {
                    $svc   = HospitalService::findOrFail($row['medication_id']); // Get service (medication) details
                    $line  = $svc->price * $row['quantity']; // Calculate line total
                    $grandTotal += $line; // Add to grand total

                    // Add prescription item
                    $prescription->items()->create([
                        'service_id'     => $svc->service_id, // Medication ID
                        'name'           => $svc->service_name, // Medication name
                        'datetime'       => now(), // Current time
                        'quantity_asked' => $row['quantity'], // Quantity requested
                        'quantity_given' => 0, // Not yet dispensed
                        'duration'       => $row['duration'], // Duration
                        'duration_unit'  => $row['duration_unit'], // Duration unit
                        'instructions'   => $row['instructions'] ?? '', // Instructions
                        'status'         => 'pending', // Initial status
                    ]);

                    // Add bill item
                    $bill->items()->create([
                        'service_id'      => $svc->service_id, // Medication ID
                        'amount'          => $line, // Line total
                        'billing_date'    => now(), // Current date
                        'discount_amount' => 0, // No discount
                        'status'          => 'pending', // Initial status
                    ]);

                    // Add pharmacy charge item
                    PharmacyChargeItem::create([
                        'charge_id'  => $pharmCharge->id, // Link to pharmacy charge
                        'service_id' => $svc->service_id, // Medication ID
                        'quantity'   => $row['quantity'], // Quantity
                        'unit_price' => $svc->price, // Price per unit
                        'total'      => $line, // Line total
                        'status' => 'pending',
                    ]);
                }

                // Update pharmacy charge total
                $pharmCharge->update(['total_amount' => $grandTotal]);

                DB::commit(); // Commit transaction

                Log::debug('[OrderEntry] MED + PHARM OK', [
                    'bill_id'      => $bill->billing_id, // Bill ID
                    'rx'           => $pharmCharge->rx_number, // RX number
                    'presc_id'     => $prescription->id, // Prescription ID
                ]);

                return back()->with('success', 'Medication orders submitted, Sent to the Pharmacy.');

            } catch (\Throwable $e) {
                DB::rollBack(); // Rollback on error
                Log::error('[OrderEntry] MED FAIL', [
                    'error' => $e->getMessage(), // Log error message
                    'trace' => $e->getTraceAsString(), // Log stack trace
                ]);
                return back()->withErrors('Unable to submit medication orders.');
            }
        }

        // Handle lab and imaging orders
        if ($type === 'lab') {
            $data = $request->validate([ // Validate lab order input
                'labs'            => 'nullable|array', // Labs array is optional
                'labs.*'          => 'exists:hospital_services,service_id', // Each lab must exist
                'studies'         => 'nullable|array', // Imaging studies array is optional
                'studies.*'       => 'exists:hospital_services,service_id', // Each study must exist
                'diagnosis'       => 'nullable|string', // Diagnosis is optional
                'collection_date' => 'required|date', // Collection date required
                'priority'        => 'required|in:routine,urgent,stat', // Priority required
            ]);

            // Merge labs and imaging studies into one list
            $serviceIDs = collect($data['labs']   ?? [])
                        ->merge($data['studies'] ?? [])
                        ->unique()
                        ->values();

            if ($serviceIDs->isEmpty()) { // If no labs or studies selected
                Log::info('[OrderEntry] LAB form submitted with no items');
                return back()->withErrors('Select at least one Lab / Imaging study.');
            }

            // Create service assignments for each selected service
            foreach ($serviceIDs as $service_id) {
                $service = HospitalService::findOrFail($service_id); // Get service details

                ServiceAssignment::create([
                    'patient_id'     => $patient->patient_id, // Link to patient
                    'doctor_id'      => $doctorId, // Link to doctor
                    'service_id'     => $service->service_id, // Service ID
                    'datetime'       => $data['collection_date'], // Collection date
                    'service_status' => 'pending', // Initial status
                ]);

                Log::debug('[OrderEntry] LAB/IMG assignment created', [
                    'service' => $service->service_name, // Log service name
                ]);
            }

            return back()->with('success', 'Lab / Imaging order saved.');
        }

        // Handle other service orders (e.g. operations)
        if ($type === 'service') {
            $data = $request->validate([ // Validate service order input
                'services'       => 'required|array|min:1', // At least one service required
                'services.*'     => 'exists:hospital_services,service_id', // Each service must exist
                'diagnosis'      => 'nullable|string', // Diagnosis optional
                'scheduled_date' => 'required|date', // Scheduled date required
                'priority'       => 'required|in:routine,urgent,stat', // Priority required
                'frequency'      => 'nullable|string', // Frequency optional
                'duration'       => 'nullable|integer|min:1', // Duration optional
                'duration_unit'  => 'nullable|string', // Duration unit optional
                'instructions'   => 'nullable|string', // Instructions optional
            ]);

            DB::beginTransaction(); // Start DB transaction
            try {
                // Create service assignments for each selected service
                foreach ($data['services'] as $service_id) {
                    $service = HospitalService::findOrFail($service_id); // Get service details

                    ServiceAssignment::create([
                        'patient_id'     => $patient->patient_id, // Link to patient
                        'doctor_id'      => $doctorId, // Link to doctor
                        'service_id'     => $service->service_id, // Service ID
                        'datetime'       => $data['scheduled_date'], // Scheduled date
                        'service_status' => 'pending', // Initial status
                    ]);
                }
                DB::commit(); // Commit transaction
                Log::debug('[OrderEntry] OTHER services OK', $data['services']); // Log services
                return back()->with('success', 'Service order submitted.');
            } catch (\Throwable $e) {
                DB::rollBack(); // Rollback on error
                Log::error('[OrderEntry] OTHER services FAILED', [
                    'error' => $e->getMessage(), // Log error
                ]);
                return back()->withErrors('Unable to submit service order.');
            }
        }

        // Unknown order type
        Log::warning('[OrderEntry] Unknown type supplied', ['type' => $type]); // Log warning
        abort(400, 'Unknown order type'); // Abort with error

        // (Unreachable, but fallback)
        return redirect()
           ->route('doctor.orders.index') // Redirect to orders index
           ->with('success', 'Order saved.')
           ->with('show_patient', $patient->patient_id); // Pass patient ID to view
    }

    // List all patients with orders for this doctor
    public function ordersIndex(Request $request)
    {
        $doctorId = optional(Auth::user()->doctor)->doctor_id; // Get current doctor ID
        $patients = Patient::whereHas('admissionDetail', function($q) use ($doctorId) {
                $q->where('doctor_id', $doctorId); // Only patients assigned to this doctor
            })
            ->where(function($q) {
                $q->whereHas('serviceAssignments') // Patients with service assignments
                  ->orWhereHas('prescriptions'); // Or with prescriptions
            })
            ->withCount(['serviceAssignments','prescriptions']) // Count assignments and prescriptions
            ->orderBy('service_assignments_count','desc') // Order by most assignments
            ->paginate(12); // Paginate results

        return view('doctor.orders-index', compact('patients')); // Show in view
    }

    // Show all orders for a specific patient (services and medications)
    public function patientOrders(Patient $patient)
    {
        try {
            // Get all service assignments for this patient
            $serviceOrders = ServiceAssignment::where('patient_id', $patient->patient_id)
                ->with('service') // Eager load service details
                ->latest() // Most recent first
                ->get();

            // Get all prescription items for this patient
            $medOrders = PrescriptionItem::whereHas('prescription', function($q) use ($patient) {
                    $q->where('patient_id', $patient->patient_id); // Only this patient's prescriptions
                })
                ->with('service')   // Eager load service details
                ->orderByDesc('datetime') // Most recent first
                ->get();

            // Return partial view with both lists
            return view('doctor.partials.orders-list', compact('serviceOrders','medOrders'));
        } catch (\Throwable $e) {
            Log::error('Error in patientOrders: '.$e->getMessage(), ['trace'=>$e->getTraceAsString()]);
            // Show error in browser (for debugging)
            return response("Error loading orders: ".$e->getMessage(), 500);
        }
    }
}
