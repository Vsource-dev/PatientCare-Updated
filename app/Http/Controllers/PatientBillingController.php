<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Bill;
use App\Models\BillingInformation;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Dispute;
use App\Models\ServiceAssignment;
use App\Models\BillItem;
use App\Models\Deposit;

use App\Helpers\Audit;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Patient;
use App\Models\AdmissionDetail;
use App\Models\PharmacyCharge;
use App\Models\PharmacyChargeItem;
use App\Models\AuditLog;

class PatientBillingController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth']); // Webguard
    }

    public function index(Request $request)
    {
        // 1. Get Current Patient and Admission

        // Find Logged Patient
        $patient = Auth::user()-> patient ?? abort(404,'No patient profile.');

        
        $admissions  = $patient->admissionDetail()
                               ->orderByDesc('admission_date')
                               ->get();


        $admissionId = $request->input('admission_id') 
            ?? $admissions->first()?->admission_id;

        $admission   = AdmissionDetail::with('doctor')->find($admissionId);

        // Debugging: Log to verify fetched data
        \Log::debug("Fetched Data:", [
            'patient_id' => $patient->patient_id,
            'admission_id' => $admissionId,
            'admissions' => $admissions,
            'admission' => $admission
        ]);

        // 2️⃣ money figures --------------------------------------------------

        // bill_items (less discounts, optionally admission-scoped)
        $billTotal = DB::table('bill_items as bi')
            ->join('bills as b', 'bi.billing_id','=','b.billing_id')
            ->where('b.patient_id', $patient->patient_id)
            ->when($admissionId, fn($q) => $q->where('b.admission_id', $admissionId))
            ->sum(DB::raw('bi.amount - COALESCE(bi.discount_amount,0)'));

        // Debugging: Log the totals
        \Log::debug("Billing Total:", ['billTotal' => $billTotal]);

        // Pharmacy total (only completed pharmacy charges)
        $rxTotal = DB::table('pharmacy_charge_items as pci')
            ->join('pharmacy_charges as pc', 'pc.id', '=', 'pci.charge_id')
            ->where('pc.patient_id', $patient->patient_id)
            ->where('pci.status', 'dispensed') // sum only dispensed items
            ->sum('pci.total');

        // Debugging: Log pharmacy total
        \Log::debug("Pharmacy Total:", ['rxTotal' => $rxTotal]);

        // Current occupied-bed (falls back to room rate if bed rate is 0)
        $bedRate = DB::table('beds as b')
            ->leftJoin('rooms as r', 'r.room_id', '=', 'b.room_id')
            ->where('b.patient_id', $patient->patient_id)
            ->where('b.status', 'occupied')
            ->max(DB::raw('COALESCE(NULLIF(b.rate,0), r.rate, 0)'));

        // Debugging: Log bed rate
        \Log::debug("Bed Rate:", ['bedRate' => $bedRate]);

        // Attending doctor professional fee
        $doctorFee = optional($admission?->doctor)->rate ?? 0;

        // Debugging: Log doctor fee
        \Log::debug("Doctor Fee:", ['doctorFee' => $doctorFee]);

        // Deposits already paid
        $paymentsMade = DB::table('deposits')
            ->where('patient_id', $patient->patient_id)
            ->sum('amount');

        // Grand totals
        $grandTotal = $billTotal + $rxTotal + $bedRate + $doctorFee;
        $balance    = $grandTotal - $paymentsMade;

        // Debugging: Log grand total and balance
        \Log::debug("Grand Total:", ['grandTotal' => $grandTotal, 'balance' => $balance]);

        $totals = [
            'total'    => $grandTotal,
            'balance'  => $balance,
            'discount' => 0,
        ];

        // 3️⃣ itemized rows --------------------------------------------------
/* ---------- a) Bill-item rows (manual & admission scoped) ---------- */
// in PatientBillingController@index

$billRows = Bill::with([
    'items.service.department',
    'items.logs',   // ← load the logs collection
])
->where('patient_id',$patient->patient_id)
->when($admissionId,fn($q)=>$q->where('admission_id',$admissionId))
->get()
->flatMap(function ($bill) {
    return $bill->items->map(function ($it) use ($bill) {
        $timeline = $it->logs->map(fn($l)=> (object)[
            'stamp' => $l->created_at,
            'actor' => $l->actor,
            'dept'  => $it->service?->department?->department_name ?? '—',   // 👈 NEW
            'text'  => $l->message,
        ]);
        return (object)[
            'billing_item_id' => $it->billing_item_id,
            'billing_date'    => $bill->billing_date,
            'ref_no'          => $bill->billing_id,
            'description'     => $it->service?->service_name ?? '—',
            'provider'        => $it->service?->department?->department_name ?? '—',
            'amount'          => $it->amount,
            'status'          => $it->dispute?->status ?? ($it->status ?: $bill->payment_status),
            'timeline'        => $timeline,
        ];
    });
});

/* ---------- b) Service-assignment rows ---------- */
$assignmentRows = ServiceAssignment::with(['service.department','doctor'])
->where('patient_id',$patient->patient_id)
->get()
->map(function ($as) {
    $timeline = collect([
        (object)[
            'stamp' => $as->created_at,
            'actor' => optional($as->doctor)->doctor_name ?? '—',
            'dept'  => optional($as->doctor->department)->department_name ?? '—', // 👈 NEW
            'text'  => 'Ordered',
        ],
        $as->service_status === 'completed'
            ? (object)[
                'stamp' => $as->updated_at,
                'actor' => optional($as->doctor)->doctor_name ?? '—',
                'dept'  => optional($as->doctor->department)->department_name ?? '—', // 👈 NEW
                'text'  => 'Marked completed',
              ]
            : null,
    ])->filter();

        return (object)[
            'billing_item_id' => 'SA-'.$as->assignment_id,
            'billing_date'    => $as->datetime ?? $as->created_at,
            'ref_no'          => 'SA'.$as->assignment_id,
            'description'     => $as->service?->service_name ?? '—',
            'provider'        => $as->service?->department?->department_name ?? '—',
            'amount'          => $as->amount ?? 0,
            'status'          => $as->service_status,
            'timeline'        => $timeline,
        ];
    });

/* ---------- c) Pharmacy rows (no separate audit table yet) ---------- */
$rxRows = PharmacyChargeItem::with(['service', 'charge'])
    ->whereHas('charge', function($q) use ($patient) {
        $q->where('patient_id', $patient->patient_id);
    })
    ->where('status', 'dispensed')
    ->get()
    ->map(function ($it) {
        return (object)[
            'billing_item_id' => 'RX-'.$it->id,
            'billing_date'    => $it->charge->created_at,
            'ref_no'          => $it->charge->rx_number,
            'description'     => $it->service?->service_name ?? '—',
            'provider'        => 'Pharmacy',
            'amount'          => $it->total,
            'status'          => 'dispensed',
            'timeline'        => collect([
                (object)[
                    'stamp' => $it->updated_at,
                    'actor' => 'Pharmacy',
                    'dept'  => 'Pharmacy',
                    'text'  => 'Dispensed',
                ],
            ]),
        ];
    });


     // 4️⃣ Merge – filter – collapse – paginate -----------------------

$rows = collect()
->concat($billRows)
->concat($assignmentRows)
->concat($rxRows);

// only collapse non-pharmacy rows by ref/provider; keep each RX item separate
$rows = $rows->groupBy(function($r) {
// our $rxRows objects all set ->is_rx = true
if (!empty($r->is_rx)) {
    // group by the unique item id → no collapsing
    return $r->billing_item_id;
}
// everything else: collapse by ref/provider
return $r->ref_no.'|'.$r->provider;
})
->map(function($grp) {
$first = $grp->first();
$count = $grp->count();

return (object)[
    'billing_date' => $grp->min('billing_date'),
    'ref_no'       => $first->ref_no,
    'description'  => $count === 1
                        ? $first->description
                        : "{$count} items",
    'provider'     => $first->provider,
    'amount'       => $grp->sum('amount'),
    'status'       => $grp->pluck('status')->unique()->count() === 1
                        ? $first->status
                        : 'mixed',
    'children'     => $grp->values(),
    // if you rely on a top‐level billing_item_id in the view, you can set it here:
    'billing_item_id' => $first->billing_item_id,
    // preserve is_rx if you use it later
    'is_rx'        => $first->is_rx ?? false,
];
})
->values();

        // Simple LengthAwarePaginator
        $perPage = 10;
        $page = $request->input('page', 1);
        $paginator = new LengthAwarePaginator(
            $rows->forPage($page, $perPage),
            $rows->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // 5️⃣ Return view
        return view('patient.billing', [
            'admissions'    => $admissions,
            'admissionId'   => $admissionId,
            'items'         => $paginator,
            'totals'        => $totals,
            'bedRate'       => $bedRate,
            'doctorFee'     => $doctorFee,
            'pharmacyTotal' => $rxTotal,  
            'paymentsMade'  => $paymentsMade,
        ]);
    }

    public function disputeRequest($billItemId)
{
    $charge = BillItem::with(['service.department','bill.doctor'])
              ->findOrFail($billItemId);

    // Ensure the logged-in patient owns it
    abort_unless(
        $charge->bill->patient_id === Auth::user()->patient_id,
        403
    );

    return view('patient.billing.disputeRequest', compact('charge'));
}



  public function downloadStatement(Request $request)
    {
        $patient = Auth::user()->patient;

        // 1) Determine admission
        $admissionId = $request->input('admission_id')
            ?? $patient->admissionDetail()->latest('admission_date')->first()->admission_id;

        // 2) Fetch billing info
        $billingInfo = BillingInformation::where('patient_id', $patient->patient_id)->first();

        $totals = [
            'total'    => $billingInfo?->total_charges    ?? 0,
            'balance'  => ($billingInfo?->total_charges ?? 0) - ($billingInfo?->payments_made ?? 0),
            'discount' => $billingInfo?->discount_amount ?? 0,
        ];

       $items = Bill::with(['items.service.department'])
    ->where('patient_id', $patient->patient_id)
    ->where('admission_id', $admissionId)
    ->get()
    ->flatMap(fn($bill) => $bill->items->map(fn($item) => [
        'date'        => $bill->billing_date->format('Y-m-d'),
        'ref_no'      => $bill->billing_id,
        'description' => optional($item->service)->service_name ?: '—',
        'provider'    => optional(optional($item->service)->department)->department_name ?: '-',
        'amount'      => $item->amount,
        'status'      => $item->status ?? $bill->payment_status,
    ]));

        // 4) Load a simple Blade for PDF generation
        $pdf = Pdf::loadView('patient.pdf.statement', [
            'patient'     => $patient,
            'totals'      => $totals,
            'items'       => $items,
            'admission'   => $patient->admissionDetail()->find($admissionId),
        ])->setPaper('a4', 'portrait');

        // 5) Download with a filename
        $filename = 'statement_adm'.$admissionId.'_'.now()->format('Ymd').'.pdf';
        return $pdf->download($filename);
    }






    public function chargeTrace(string $key)
{
    if (Str::startsWith($key, 'SA-')) {
        /* ---------- ServiceAssignment branch ---------- */
        $assignmentId = intval(Str::after($key, 'SA-'));

        $as = ServiceAssignment::with(['service.department','doctor'])
                 ->findOrFail($assignmentId);

        // synthesise a pseudo-charge object so the same Blade works
        $charge = (object) [
            'is_assignment'   => true,
            'billing_item_id' => 'SA-'.$as->assignment_id,
            'service'         => $as->service,
            'amount'          => $as->amount ?? 0,
            'status'          => $as->service_status,
            'billing_date'    => $as->datetime ?? $as->created_at,
            'logs'            => collect([
                (object)[
                    'action'     => 'created',
                    'actor'      => optional($as->doctor)->doctor_name ?? '—',
                    'created_at' => $as->datetime ?? $as->created_at,
                ],
                $as->service_status === 'completed'
                    ? (object)[
                        'action'     => 'completed',
                        'actor'      => optional($as->doctor)->doctor_name ?? '—',
                        'created_at' => $as->updated_at ?? $as->datetime,
                      ]
                    : null,
            ])->filter(),
        ];
    }
    
    elseif (Str::startsWith($key, 'RX-')) {
    $itemId = intval(Str::after($key,'RX-'));

    $rxItem = PharmacyChargeItem::with(['service', 'charge'])
               ->findOrFail($itemId);

    $charge = (object)[
        'is_rx'          => true,
        'billing_item_id'=> 'RX-'.$rxItem->id,
        'service'        => $rxItem->service,
        'amount'         => $rxItem->total,
        'status'         => 'completed',
        'billing_date'   => $rxItem->charge->created_at,
        'logs'           => collect([]),        // fill if you keep audit logs
    ];
}

    
    else {
        /* ---------- BillItem branch ---------- */
        $charge = BillItem::with(['service.department','logs'])
                  ->findOrFail(intval($key));
    }

    return view('patient.billing.chargeTrace', compact('charge'));
}
    public function show(Bill $bill)
    {
        $this->authorize('view', $bill); // optional policy

        $bill->load(['items.service.department']);
        return view('patient.bill-show', compact('bill'));
    }



    public function store(Request $request)
    {
        $data = $request->validate([
            'bill_item_id' => 'required|exists:bill_items,id',
            'reason'       => 'required|string|max:255',
            'details'      => 'nullable|string',
            'documents.*'  => 'file|max:10240',
        ]);

        // save dispute
        $dispute = Dispute::create([
            'bill_item_id' => $data['bill_item_id'],
            'patient_id'   => Auth::user()->patient->patient_id,
            'reason'       => $data['reason'],
            'details'      => $data['details'] ?? '',
            'status'       => 'pending',
        ]);

        // store docs (optional)
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $file->storeAs('disputes/'.$dispute->id, $file->getClientOriginalName(), 'public');
            }
        }

        return back()->with('success','Your dispute request has been submitted.');
    }
}
