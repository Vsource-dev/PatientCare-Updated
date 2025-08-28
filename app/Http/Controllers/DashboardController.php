<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1) Admin guard?
        if (Auth::guard('admin')->check()) {
            return view('admin.dashboard');          // uses layouts/admin.blade.php
        }

        // 2) Web guard → check role
        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            if ($user->role === 'admission') {
                return view('admission.dashboard');  // uses layouts/admission.blade.php
            }
            if ($user->role === 'pharmacy') {
                return view('pharmacy.dashboard');  // uses layouts/pharmacy.blade.php
            }
        }

        // 3) Fallback to login
        return redirect()->route('login');
    }
}
