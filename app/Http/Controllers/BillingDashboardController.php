<?php

namespace App\Http\Controllers;

use App\Models\BillItem;
use App\Models\Patient;
use App\Models\Dispute;
use App\Models\ServiceAssignment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Deposit;
use App\Models\PharmacyCharge;


class BillingDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','role:billing']);
    }

    public function index()
    {
        // Metrics
        $totalRevenue = BillItem::sum(DB::raw('amount - COALESCE(discount_amount,0)'));
        $outstandingBalance = BillItem::whereHas('bill', function($q) {
                $q->where('payment_status','!=','paid');
            })->sum(DB::raw('amount - COALESCE(discount_amount,0)'));
        $activePatients = Patient::where('status','active')->count();
        $pendingDisputes = Dispute::where('status','pending')->count();

        // Recent billing items
        $recentBillItems = BillItem::whereNotNull('service_id')
            ->with(['bill.patient','service.department'])
            ->latest('billing_item_id')
            ->take(10)
            ->get();
        $billItemsTotal = $recentBillItems
            ->sum(fn($item)=> $item->amount - ($item->discount_amount ?? 0));

        // Recent service assignments
        $recentServiceAssignments = ServiceAssignment::with(['patient','service.department'])
            ->latest('assignment_id')
            ->take(10)
            ->get();
        $servicesTotal = $recentServiceAssignments
            ->sum(fn($sa)=> optional($sa->service)->price ?? 0);

        return view('billing.dashboard', [
            'totalRevenue'             => $totalRevenue,
            'outstandingBalance'       => $outstandingBalance,
            'activePatients'           => $activePatients,
            'pendingDisputes'          => $pendingDisputes,
            'recentBillItems'          => $recentBillItems,
            'billItemsTotal'           => $billItemsTotal,
            'recentServiceAssignments' => $recentServiceAssignments,
            'servicesTotal'            => $servicesTotal,
        ]);
    }

    public function print(Patient $patient)
    {
        // 1. Eager load patient details for efficiency
        $patient->load('admissionDetail');

        // 2. Fetch all billable items for the patient
        $all_charges = BillItem::whereHas('bill', fn($q) => $q->where('patient_id', $patient->patient_id))
            ->with('service')
            ->get();
        // Note: For a complete statement, you would also merge in ServiceAssignments and PharmacyCharges here.

        // 3. Calculate totals
        $totalCharges = $all_charges->sum('amount');
        $totalDeposits = Deposit::where('patient_id', $patient->patient_id)->sum('amount');
        $balance = $totalCharges - $totalDeposits;

        $data = [
            'patient'     => $patient,
            'all_charges' => $all_charges,
            'totals'      => [
                'charges'  => $totalCharges,
                'deposits' => $totalDeposits,
                'balance'  => $balance,
            ],
        ];

        // 4. Load the view and generate the PDF
        $pdf = Pdf::loadView('billing.pdf.statement', $data);

        // 5. Stream the PDF to the browser
        return $pdf->stream('SOA-' . $patient->patient_id . '-' . now()->format('Ymd') . '.pdf');
    }

    // likewise for lock
    public function lock(Request $request, Patient $patient)
    {
        $patient->billing_locked = true;
        $patient->save();

        return back()->with('success','Billing locked.');
    }
}
