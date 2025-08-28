<?php
//app/Http/Controllers/AdmissionController.php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Department;
use App\Models\MedicalDetail;
use App\Models\AdmissionDetail;
use App\Models\InsuranceProvider;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PDF;
use App\Models\Bed;
use App\Models\BillingInformation;

class AdmissionController extends Controller
{

    // Before accessing the controller, you must first log in
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function dashboard()
    {
        $totalPatients   = Patient::count();
        $availableBeds   = Bed::where('status', 'available')->count();
        $newAdmissions   = AdmissionDetail::whereDate('created_at', Carbon::today())->count();

        $recentAdmissions = AdmissionDetail::with([
            'patient', //Load patient details
            'doctor', //Load doctor details
            'room' //Load room details
        ])
        ->latest() // Most recent
        ->take(5)
        ->get();

        // Log for debugging
        Log::debug("Dashboard Data:", compact(
            'totalPatients','newAdmissions','availableBeds','recentAdmissions'
        ));

        return view('admission.dashboard', compact(
            'totalPatients','newAdmissions','availableBeds','recentAdmissions'
    ));
}
    public function login()
    {
        return view('auth.login');
    }

    // public function createPatient()
    // {
    //     $departments = Department::all();
    //     $doctors = Doctor::all();

    //     return view('admission.create', compact(
    //         'departments',
    //         'doctors',
    //     ));
    // }

// public function storePatient(Request $request)
// {
//     try {

//         DB::beginTransaction();

//         // 1) Validate input (doctor_id is nullable so we can override for emergencies)
//         $validated = $request->validate([

//             'first_name'             => 'required|string|max:100',
//             'last_name'              => 'required|string|max:100',
//             'birthday'               => 'nullable|date',
//             'civil_status'           => 'nullable|string',
//             'phone'                  => 'nullable|string|max:20',
//             'email'                  => 'nullable|email',
//             'address'                => 'nullable|string',
//             'city'                   => 'nullable|string',

//             'primary_reason'         => 'required|string',
//             'temperature'            => 'nullable|numeric|between:30,45',
//             'weight'                 => 'nullable|numeric|between:0,500',
//             'height'                 => 'nullable|numeric|between:0,300',
//             'heart_rate'             => 'nullable|numeric|between:20,250',
//             'blood_pressure'         => 'nullable|string',
//             'other_medical_history'  => 'nullable|string',
//             'medical_history'        => 'nullable|array',
//             'allergies'              => 'nullable|array',
//             'other_allergies'        => 'nullable|string',

//             'admission_date'         => 'required|date',
//             'admission_type'         => 'required|string|in:inpatient,outpatient',
//             'department_id'          => 'required|exists:departments,department_id',
//             'doctor_id'              => 'nullable|exists:doctors,doctor_id',
//             'room_id'                => 'required|exists:rooms,room_id',
//             'bed_id'                 => 'nullable|exists:beds,bed_id',
//             'admission_notes'        => 'nullable|string',

//             'guarantor_name'         => 'required|string',
//             'guarantor_relationship' => 'nullable|string',
//             'guarantor_phone'        => 'nullable|string|max:20',
//             'guarantor_location'     => 'nullable|string',

//         ]);

//         // 2) Determine which doctor to assign
//         if ($validated['admission_type'] === 'emergency') {
//             // Hard-code OR department and pick the on-call physician
//             $orDept = Department::where('department_name', 'OR')->first();
//             if (! $orDept) {
//                 throw new \Exception('OR department not found.');
//             }

//             $onCall = Doctor::where('department_id', $orDept->department_id)
//                             ->where('is_on_call', true)
//                             ->first();

//             if (! $onCall) {
//                 throw new \Exception('No on-call OR physician is currently available.');
//             }

//             $doctorId = $onCall->doctor_id;
//         } else {
//             $doctorId = $validated['doctor_id'];
//         }

//         // 3) Create the Patient record
//         $patient = Patient::create([
//             'patient_first_name' => $validated['first_name'],
//             'patient_last_name'  => $validated['last_name'],
//             'patient_birthday'   => $validated['birthday'] ?? null,
//             'phone_number'       => $validated['phone'] ?? null,
//             'civil_status'       => $validated['civil_status'] ?? null,
//             'address'            => $validated['address'] ?? null,
//             'city'               => $validated['city'] ?? null,
//             'email'              => null,  // will set after
//             'password'           => null,
//         ]);

//         // 4) Save Medical Details
//         MedicalDetail::create([
//             'patient_id'             => $patient->patient_id,
//             'primary_reason'         => $validated['primary_reason'],
//             'temperature'            => $validated['temperature'],
//             'blood_pressure'         => $validated['blood_pressure'],
//             'weight'                 => $validated['weight'],
//             'height'                 => $validated['height'],
//             'heart_rate'             => $validated['heart_rate'],
//             'medical_history'        => json_encode($validated['medical_history'] ?? []),
//             'other_medical_history'  => $validated['other_medical_history'],
//             'allergies'              => json_encode($validated['allergies'] ?? []),
//             'other_allergies'        => $validated['other_allergies'],
//         ]);

//         // 5) Create Admission Detail
//         AdmissionDetail::create([
//             'patient_id'      => $patient->patient_id,
//             'admission_date'  => $validated['admission_date'],
//             'admission_type'  => $validated['admission_type'],
//             'admission_source'=> $validated['admission_source'],
//             'department_id'   => $validated['department_id'],
//             'doctor_id'       => $doctorId,
//             'room_id'         => $validated['room_id'],
//             'bed_id'          => $validated['bed_id'] ?? null,
//             'admission_notes' => $validated['admission_notes'],
//         ]);

//         // Create the Billing Detail
//        BillingInformation::create([
//             'guarantor_name'         =>  $validated['guarantor_name'] ?? null,
//             'guarantor_relationship' =>  $validated['guarantor_relationship'] ??null,
//             'guarantor_phone'        =>  $validated['guarantor_phone'] ??null,
//             'guarantor_location'     =>  $validated['guarantor_location'] ??null,
//         ]);

//         // 6) Mark bed occupied (if assigned)
//         if (! empty($validated['bed_id'])) {
//             $bed = Bed::findOrFail($validated['bed_id']);
//             $bed->update([
//                 'status'     => 'occupied',
//                 'patient_id' => $patient->patient_id,
//             ]);
//         }

//         // 8) Generate portal credentials
//         $generatedEmail = "{$patient->patient_id}@patientcare.test";
//         $plainPassword  = 'password';

//         $patient->update([
//             'email'    => $generatedEmail,
//             'password' => $plainPassword,  // hashed in model mutator
//         ]);

//         User::create([
//             'patient_id' => $patient->patient_id,
//             'username'   => "{$patient->patient_first_name}.{$patient->patient_last_name}",
//             'email'      => $generatedEmail,
//             'password'   => $plainPassword,  // hashed in model mutator
//             'role'       => 'patient',
//         ]);

//         DB::commit();

//         return redirect()
//             ->route('admission.patients.show', $patient)
//             ->with([
//                 'generatedEmail' => $generatedEmail,
//                 'plainPassword'  => $plainPassword,
//                 'success'        => 'Patient admitted successfully.',
//             ]);

//     } catch (\Exception $e) {
//         DB::rollBack();
//         Log::error('Error admitting patient: ' . $e->getMessage());
//         return back()
//             ->with('error', 'Error admitting patient: ' . $e->getMessage())
//             ->withInput();
//     }
// }   

    public function patients()
    {
        $patients = Patient::with(['admissionDetails', 'medicalDetails', 'billingInformation'])
            ->latest()
            ->paginate(10);
        return view('admission.patients.index', compact('patients'));
    }

    public function getDepartments()
    {
        $departments = Department::all();
        return response()->json($departments);
    }

    public function getDoctorsByDepartment($departmentId)
    {
        $doctors = Doctor::where('department_id', $departmentId)->get();
        return response()->json($doctors);
    }

    public function getRoomsByDepartment($departmentId)
    {

        $rooms = Room::where('department_id', $departmentId)
            ->where('status', 'available')
            ->get();
        return response()->json($rooms);
    }

    public function getBedsByRoom($roomId)
    {
      
        $beds = Bed::where('room_id', $roomId)
            ->where('status', 'available')
            ->get();
        return response()->json($beds);
    }

    public function logout(Request $request)
    {
        Auth::guard('admission')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admission.login');
    }

    protected function generatePatientId()
    {
        $year = date('Y');
        $lastPatient = Patient::whereYear('created_at', $year)
            ->orderByDesc('patient_id')
            ->first();

        $sequence = $lastPatient ? intval(substr($lastPatient->patient_id, -4)) + 1 : 1;
        return sprintf('%d%04d', $year, $sequence);
    }

    protected function checkRoomAvailability($roomNumber, $admissionDate)
    {
        return !AdmissionDetail::where('room_number', $roomNumber)
            ->where('admission_date', $admissionDate)
            ->whereNull('discharge_date')
            ->exists();
    }

    protected function checkDoctorAvailability($doctorId, $admissionDate)
    {
      
        $maxPatientsPerDay = 10; 
        
        $currentPatients = AdmissionDetail::where('doctor_id', $doctorId)
            ->whereDate('admission_date', $admissionDate)
            ->count();

        return $currentPatients < $maxPatientsPerDay;
    }
}