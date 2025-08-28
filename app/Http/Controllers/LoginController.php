<?php
// app/Http/Controllers/LoginController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user()->role);
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return back()
                ->withErrors(['email' => 'These credentials do not match our records.'])
                ->withInput($request->only('email','remember'));
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();
        return $this->redirectByRole($user->role);
    }

    private function redirectByRole(string $role)
    {
        return match (trim(strtolower($role))) {
            'admin'         => redirect()->route('admin.dashboard'),
            'admission'     => redirect()->route('admission.dashboard'),
            'pharmacy'      => redirect()->route('pharmacy.dashboard'),
            'doctor'        => redirect()->route('doctor.dashboard'),
            'patient'       => redirect()->route('patient.dashboard'),
            'laboratory'    => redirect()->route('laboratory.dashboard'),
            'supplies'      => redirect()->route('supplies.dashboard'),
            'operating_room'=> redirect()->route('operating.dashboard'),
            'billing'       => redirect()->route('billing.dashboard'),
            default         => redirect()->route('home'),
        };
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
