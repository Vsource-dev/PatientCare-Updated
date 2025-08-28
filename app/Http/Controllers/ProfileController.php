<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    /**
     * Require authentication on all methods.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the "My Account" form.
     */
    public function edit()
    {
        $user    = Auth::user();
        $patient = $user->patient;  // assumes User->patient() relationship

        return view('patient.account', compact('user', 'patient'));
    }

    /**
     * Persist changes to profile (name, email, contact, etc.).
     */
    public function update(Request $request)
    {
        $user    = Auth::user();
        $patient = $user->patient;

        $data = $request->validate([
            'patient_last_name'  => 'required|string|max:100',
            'patient_first_name' => 'required|string|max:100',
            'middle_initial'     => 'nullable|string|size:1',
            'sex'                => 'nullable|in:male,female,other',
            'patient_birthday'   => 'nullable|date',
            'civil_status'       => 'nullable|in:single,married,widowed,divorced',
            'email'              => 'required|email|unique:patients,email,'.$patient->patient_id.',patient_id',
            'phone_number'       => 'nullable|string|max:20',
             'profile_photo'      => 'nullable|image|max:2048',
        ]);
          if ($request->hasFile('profile_photo')) {
            // delete old file if exists
            if ($patient->profile_photo) {
                Storage::disk('public')->delete('patient/images/'.$patient->profile_photo);
            }
            $file      = $request->file('profile_photo');
            $filename  = Str::uuid().'.'.$file->getClientOriginalExtension();
            $file->storeAs('patient/images', $filename, 'public');
            $data['profile_photo'] = $filename;
        }


        // 1) Update patient table
        $patient->update([
            'patient_first_name' => $data['patient_first_name'],
            'patient_last_name'  => $data['patient_last_name'],
            'middle_initial'     => $data['middle_initial'] ?? null,
            'sex'                => $data['sex'] ?? null,
            'patient_birthday'   => $data['patient_birthday'] ?? null,
            'civil_status'       => $data['civil_status'] ?? null,
            'phone_number'       => $data['phone_number'] ?? null,
            'email'              => $data['email'],
            'profile_photo'      => $data['profile_photo'] ?? $patient->profile_photo,
        ]);

        // 2) Keep the User record in sync
        $user->update([
            'email'    => $data['email'],
            'username' => explode('@', $data['email'])[0],
        ]);

        return Redirect::route('patient.account')
                       ->with('status', 'profile-updated');
    }

    /**
     * Persist a password change.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password'      => ['required', 'current-password'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = $request->user();

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return Redirect::route('patient.account')
                       ->with('status', 'password-updated');
    }
}
