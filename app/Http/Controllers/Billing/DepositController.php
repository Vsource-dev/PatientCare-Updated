<?php
// app/Http/Controllers/Billing/DepositController.php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Deposit;
use App\Models\Patient;                      // ← make sure to import
use App\Notifications\DepositReceived;       // ← import your new notification

class DepositController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','role:billing']);
    }

    public function create()
    {
        return view('billing.deposit.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id'   => 'required|exists:patients,patient_id',
            'amount'       => 'required|numeric|min:0',
            'deposited_at' => 'required|date',
        ]);

        // 1) Create the deposit
        $deposit = Deposit::create([
            'patient_id'   => $request->patient_id,
            'amount'       => $request->amount,
            'deposited_at' => $request->deposited_at,
        ]);

        // 2) Notify the patient
        $patient = Patient::where('patient_id', $request->patient_id)->first();
        if ($patient) {
            $patient->notify(new DepositReceived($deposit));
        }

        // 3) Redirect back
        return back()->with('success','Deposit has been recorded and patient notified.');
    }
}
