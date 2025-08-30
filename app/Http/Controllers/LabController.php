<?php
//app/Http/Controllers/LabController.php
namespace App\Http\Controllers;

use App\Models\HospitalService;
use Illuminate\Http\Request;
use App\Models\ServiceAssignment;
use Illuminate\Support\Facades\Auth;
use App\Models\Doctor;
use App\Models\Patient;
use App\Notifications\LabChargeCreated;
use App\Notifications\LabChargeCompleted;
use Carbon\Carbon;

class LabController extends Controller
{
    public function __construct()
    {
        // Require authentication for all methods in this controller
        $this->middleware('auth');
    }

    // Dashboard: stats and recent admissions
    public function dashboard()
    {
        $today = Carbon::today();

        $completedCount = ServiceAssignment::whereHas('service', fn($q) => $q->where('service_type', 'lab'))
            ->where('service_status', 'completed')->count();

        $pendingCount = ServiceAssignment::whereHas('service', fn($q) => $q->where('service_type', 'lab'))
            ->where('service_status', 'pending')->count();

        $patientsServed = ServiceAssignment::whereHas('service', fn($q) => $q->where('service_type', 'lab'))
            ->distinct('patient_id')->get();

        $todayAdmissions = ServiceAssignment::with('patient','doctor')
            ->whereHas('service', fn($q) => $q->where('service_type', 'lab'))
            ->whereDate('created_at', $today)
            ->latest()->get();

        $earlierAdmissions = ServiceAssignment::with('patient','doctor')
            ->whereHas('service', fn($q) => $q->where('service_type', 'lab'))
            ->whereDate('created_at', '<', $today)
            ->latest()->take(10)->get();

        // Optional: Upcoming dispense (scheduled for future, if you use scheduled_at)
        $upcomingDispense = ServiceAssignment::with(['patient','doctor','service'])
            ->whereHas('service', fn($q) => $q->where('service_type', 'lab'))
            ->where('service_status', 'pending')
            ->where('created_at', '>', now())
            ->orderBy('created_at')
            ->get();

        return view('laboratory.dashboard', compact(
            'completedCount',
            'pendingCount',
            'patientsServed',
            'todayAdmissions',
            'earlierAdmissions',
            'upcomingDispense'
        ));
    }

    // Show the lab queue (pending lab requests)
    public function queue(Request $request)
    {
        $statusFilter = $request->input('status', 'all');
        $labRequests = ServiceAssignment::with('patient', 'doctor', 'service')
            ->whereHas('service', function ($query) {
                $query->where('service_type', 'lab');
            })
            ->where('service_status', 'pending')
            ->get();

        return view('laboratory.queue', compact('labRequests'));
    }

    // Store a new lab charge/request (if you allow creation from lab UI)
    public function store(Request $request)
    {
        $data = $request->validate([
            'search_patient'       => 'required|exists:patients,patient_id',
            'doctor_id'            => 'required|exists:doctors,doctor_id',
            'charges'              => 'required|array|min:1',
            'charges.*.service_id' => 'required|exists:hospital_services,service_id',
            'charges.*.amount'     => 'required|numeric|min:0',
            'notes'                => 'nullable|string',
        ]);

        $patient = Patient::findOrFail($data['search_patient']);
        $doctor  = Doctor::findOrFail($data['doctor_id']);
        $user    = Auth::user();

        $bill = \App\Models\Bill::firstOrCreate([
            'patient_id'   => $patient->patient_id,
            'billing_date' => now()->toDateString(),
        ], ['payment_status'=>'pending']);

        foreach ($data['charges'] as $row) {
            $service = HospitalService::findOrFail($row['service_id']);
            $amount  = $service->price; // Always use the price from the DB

            $billItem = \App\Models\BillItem::create([
                'billing_id'   => $bill->billing_id,
                'service_id'   => $service->service_id,
                'quantity'     => 1,
                'amount'       => $amount,
                'billing_date' => $bill->billing_date,
            ]);

            $assignment = ServiceAssignment::create([
                'patient_id'     => $patient->patient_id,
                'doctor_id'      => $doctor->doctor_id,
                'service_id'     => $service->service_id,
                'amount'         => $amount,
                'service_status' => 'pending',
                'notes'          => $data['notes'] ?? null,
                'bill_item_id'   => $billItem->billing_item_id,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            \App\Models\AuditLog::create([
                'bill_item_id' => $billItem->billing_item_id,
                'action'       => 'create',
                'message'      => "Lab “{$service->service_name}” (₱{$amount}) added by {$user->username} for Dr. {$doctor->doctor_name}",
                'actor'        => $user->username,
                'icon'         => 'fa-vials',
            ]);

            $assignment->patient->notify(new LabChargeCreated($assignment));
        }

        return redirect()
            ->route('laboratory.queue')
            ->with('success','Lab charges have been successfully created.');
    }

    // Mark a lab request as completed (single action)
    public function markCompleted(ServiceAssignment $assignment)
    {
        $assignment->service_status = 'completed';
        $assignment->save();

        $assignment->patient->notify(new LabChargeCompleted($assignment));

        return redirect()
            ->route('laboratory.queue', $assignment)
            ->with('success','Request marked as completed.');
    }

    // Show the laboratory history (completed and pending lab requests)
    public function history()
    {
        $completedOrders = ServiceAssignment::with(['patient','service','doctor'])
            ->whereHas('service', fn($q) => $q->where('service_type', 'lab'))
            ->where('service_status', 'completed')
            ->orderByDesc('updated_at')
            ->get();

        return view('laboratory.history', compact('completedOrders'));
    }

    public function show(ServiceAssignment $assignment)
    {
        $assignment->load(['patient', 'doctor', 'service']);
        return view('laboratory.view', compact('assignment'));
    }
}
