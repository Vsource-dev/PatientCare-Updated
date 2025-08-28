<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Patient;

class DischargeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:billing']);
    }
public function index(Request $request)
{
    $inner = Patient::query()
        ->where('status', 'active');

    if ($search = $request->input('search')) {
        $inner->where(function($q) use ($search) {
            $q->where(DB::raw("LPAD(patient_id,8,'0')"), 'like', "%{$search}%")
              ->orWhere('patient_first_name', 'like', "%{$search}%")
              ->orWhere('patient_last_name',  'like', "%{$search}%");
        });
    }

    $inner->select([
        'patient_id',
        'patient_first_name',
        'patient_last_name',
        DB::raw("(
            SELECT COALESCE(SUM(bi.amount - COALESCE(bi.discount_amount,0)),0)
              FROM bills b
              JOIN bill_items bi ON bi.billing_id = b.billing_id
             WHERE b.patient_id = patients.patient_id
        ) AS bill_items_total"),
        DB::raw("(
            SELECT COALESCE(SUM(pci.total),0)
              FROM pharmacy_charges pc
              JOIN pharmacy_charge_items pci ON pci.charge_id = pc.id
             WHERE pc.patient_id = patients.patient_id
        ) AS rx_total"),
        DB::raw("(
            SELECT COALESCE(MAX(COALESCE(NULLIF(b.rate,0), r.rate,0)),0)
              FROM beds b
              LEFT JOIN rooms r ON r.room_id = b.room_id
             WHERE b.patient_id = patients.patient_id
               AND b.status = 'occupied'
        ) AS bed_room_rate"),
        DB::raw("(
            SELECT COALESCE(SUM(d.amount),0)
              FROM deposits d
             WHERE d.patient_id = patients.patient_id
        ) AS total_deposits"),
    ]);

    $query = DB::query()
        ->fromSub($inner, 'p')
        ->select([
            'p.*',
            DB::raw('(bill_items_total + rx_total + bed_room_rate)            AS total_charges'),
            DB::raw('(bill_items_total + rx_total + bed_room_rate) - total_deposits AS balance'),
        ]);

    $allowed = [
        'patient_id'        => 'patient_id',
        'patient_last_name' => 'patient_last_name',
        'balance'           => 'balance',
    ];

    $sortKey = $request->input('sort_by', 'patient_last_name');
    $sortBy  = $allowed[$sortKey] ?? 'patient_last_name';
    $dir     = $request->input('sort_dir') === 'desc' ? 'desc' : 'asc';
    $query->orderBy($sortBy, $dir);

    $patients = $query->paginate(15)
        ->appends($request->only(['search','sort_by','sort_dir']));

    $patients->getCollection()->transform(function($p) {
        $p->deposits = \App\Models\Deposit::where('patient_id', $p->patient_id)
            ->latest('deposited_at')
            ->limit(5)
            ->get();
        return $p;
    });

    return view('billing.discharge.index', compact('patients'));
}


    /*--------------------------------------------------------------*/
    /*   Settle  (= finish discharge after balance is fully paid)   */
    /*--------------------------------------------------------------*/
    public function settle(Request $request, Patient $patient)
    {
        // Re-compute with the same logic as above (safer than trusting the UI)
        $bal = DB::table('patients')
            ->leftJoinSub(
                DB::table('bill_items as bi')
                  ->join('bills as b','b.billing_id','=','bi.billing_id')
                  ->selectRaw('b.patient_id as pid,
                               SUM(bi.amount - COALESCE(bi.discount_amount,0)) AS bill_total')
                  ->groupBy('b.patient_id'),
                'bi', 'bi.pid', '=', 'patients.patient_id'
            )
            ->leftJoinSub(
                DB::table('pharmacy_charges as pc')
                  ->join('pharmacy_charge_items as pci','pci.charge_id','=','pc.id')
                  ->selectRaw('pc.patient_id as pid,
                               SUM(pci.total) AS rx_total')
                  ->groupBy('pc.patient_id'),
                'rx', 'rx.pid', '=', 'patients.patient_id'
            )
            ->leftJoinSub(
                DB::table('beds as b')
                  ->leftJoin('rooms as r','r.room_id','=','b.room_id')
                  ->selectRaw('b.patient_id as pid,
                               MAX( COALESCE(NULLIF(b.rate,0), r.rate, 0) ) AS bed_rate')
                  ->where('b.status','occupied')
                  ->groupBy('b.patient_id'),
                'bed','bed.pid','=','patients.patient_id'
            )
            ->leftJoinSub(
                DB::table('deposits')
                  ->selectRaw('patient_id as pid, SUM(amount) AS deposits_total')
                  ->groupBy('patient_id'),
                'dep','dep.pid','=','patients.patient_id'
            )
            ->where('patients.patient_id',$patient->patient_id)
            ->value(DB::raw('
                ( COALESCE(bi.bill_total,0) +
                  COALESCE(rx.rx_total ,0) +
                  COALESCE(bed.bed_rate,0) ) -
                COALESCE(dep.deposits_total,0)
            '));

        if ($bal > 0) {
            return back()->withErrors(
                'Patient still has an outstanding balance of ₱' .
                number_format($bal, 2)
            );
        }

        $patient->update([
            'status'          => 'finished',
            'billing_status'  => 'finished',
            'billing_closed_at' => now(),
        ]);

        return back()->with('success', 'Patient settled and discharged.');
    }
}
