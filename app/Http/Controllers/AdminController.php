<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Room;
use App\Models\Bed;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function dashboard()
    {
        $totalActiveUsers = User::where('role', '!=', 'admin')->count(); // or filter by status if you track it
        $totalCreatedRooms = Room::count();
        $recentUsers = User::orderBy('user_id', 'asc')->take(10)->get();
        $totalCreatedBeds = Bed::count();

        return view('admin.dashboard', compact(
            'totalActiveUsers',
            'totalCreatedRooms',
            'recentUsers',
            'totalCreatedBeds',
        ));
    }


    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}
