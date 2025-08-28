<?php
// app/Http/Controllers/OperatingRoomController.php

namespace App\Http\Controllers;

use App\Models\ServiceAssignment;
use App\Models\HospitalService;
use App\Models\Patient;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Notifications\ORChargeCreated;


class OperatingRoomController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /* --------------------------------------------------------
     *  DASHBOARD
     * ----------------------------------------------------- */
    public function dashboard()
    {
        $today          = Carbon::today();

        // counts
        $completedCount = ServiceAssignment::whereHas('service',
                fn ($q) => $q->where('service_type', 'service'))
            ->where('service_status', 'completed')
            ->count();

        $pendingCount   = ServiceAssignment::whereHas('service',
                fn ($q) => $q->where('service_type', 'service'))
            ->where('service_status', 'pending')
            ->count();

        $patientsServed = ServiceAssignment::whereHas('service',
                fn ($q) => $q->where('service_type', 'service'))
            ->distinct('patient_id')
            ->get();

        // today vs earlier
        $todayProcedures = ServiceAssignment::with(['patient', 'doctor', 'service.department'])
            ->whereHas('service', fn ($q) => $q->where('service_type', 'service'))
            ->whereDate('created_at', $today)
            ->latest()
            ->get();

        $earlierProcedures = ServiceAssignment::with(['patient', 'doctor', 'service.department'])
            ->whereHas('service', fn ($q) => $q->where('service_type', 'service'))
            ->whereDate('created_at', '<', $today)
            ->latest()
            ->take(10)
            ->get();

        return view('operatingroom.dashboard', compact(
            'completedCount',
            'pendingCount',
            'patientsServed',
            'todayProcedures',
            'earlierProcedures'
        ));
    }

    /* --------------------------------------------------------
     *  QUEUE / LIST
     * ----------------------------------------------------- */
 public function queue(Request $request)
{
    $status = $request->input('status', 'all');

    $query = ServiceAssignment::with(['patient','doctor','service.department'])
        ->whereHas('service', fn($q) => $q->where('service_type', 'service'));

    if ($status !== 'all') {
        $query->where('service_status', $status);
    }

    // paginate instead of get(); choose your per‐page count
    $procedures = $query->orderBy('created_at', 'desc')
                        ->paginate(10)
                        ->withQueryString(); 

    return view('operatingroom.queue', compact('procedures'));
}

    /* --------------------------------------------------------
     *  CREATE  (show form)
     * ----------------------------------------------------- */
    public function create()
    {
        $patients = Patient::select('patient_id','patient_first_name','patient_last_name')
                           ->orderBy('patient_last_name')->get();

        $doctors  = Doctor::select('doctor_id','doctor_name')
                          ->orderBy('doctor_name')->get();

        $services = HospitalService::where('service_type', 'service')->get();

        return view('operatingroom.create', compact('patients','doctors','services'));
    }

    /* --------------------------------------------------------
     *  STORE  (save new charges)
     * ----------------------------------------------------- */
    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_id'             => 'required|exists:patients,patient_id',
            'doctor_id'              => 'required|exists:doctors,doctor_id',
            'misc_item'              => 'required|array|min:1',
            'misc_item.*.service_id' => 'required|exists:hospital_services,service_id',
            'misc_item.*.orNumber'   => 'nullable|integer|min:1',
            'notes'                  => 'nullable|string',
        ]);
    
        $user     = Auth::user();
        $doctor   = Doctor::findOrFail($data['doctor_id']);
        $patient  = Patient::findOrFail($data['patient_id']);
    
        // 1) Find or create today's Bill
        $bill = \App\Models\Bill::firstOrCreate(
            ['patient_id'=>$patient->patient_id, 'billing_date'=>now()->toDateString()],
            ['payment_status'=>'pending']
        );
    
        foreach ($data['misc_item'] as $item) {
            $service  = HospitalService::findOrFail($item['service_id']);
            $amount   = $service->price;
            $orNumber = $item['orNumber'] ?? null;
    
            // 2) Create the ServiceAssignment first
            $assignment = ServiceAssignment::create([
                'patient_id'     => $patient->patient_id,
                'doctor_id'      => $doctor->doctor_id,
                'service_id'     => $service->service_id,
                'amount'         => $amount,
                'room'           => $orNumber,
                'service_status' => 'pending',
                'notes'          => $data['notes'] ?? null,
                'datetime'       => now(),
            ]);
    
            // 3) Then create the BillItem linking back to assignment_id
            $billItem = \App\Models\BillItem::create([
                'billing_id'    => $bill->billing_id,
                'service_id'    => $service->service_id,
                'assignment_id' => $assignment->assignment_id,
                'quantity'      => 1,
                'amount'        => $amount,
                'billing_date'  => $bill->billing_date,
            ]);
    
            // 4) Finally, log it
            \App\Models\AuditLog::create([
                'bill_item_id' => $billItem->billing_item_id,
                'action'       => 'create',
                'message'      => "OR charge {$service->service_name} (₱{$amount}) added by {$user->username} for Dr. {$doctor->doctor_name}",
                'actor'        => $user->username,
                'icon'         => 'fa-user-md',
            ]);
        }

        $patient->notify(new ORChargeCreated($assignment));
    
        return redirect()->route('operating.queue')
                         ->with('success','Operating-room charges recorded.');
    }
    

    /* --------------------------------------------------------
     *  DETAILS & STATUS
     * ----------------------------------------------------- */
    public function show(ServiceAssignment $assignment)
    {
        $assignment->load(['patient','doctor','service.department']);
        return view('operatingroom.details', compact('assignment'));
    }

    public function markCompleted(ServiceAssignment $assignment)
    {
        $assignment->update(['service_status' => 'completed']);
        return redirect()->route('operating.details', $assignment)
                         ->with('success', 'Procedure marked completed.');
    }
}
