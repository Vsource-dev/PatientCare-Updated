<?php
// app/Http/Controllers/PatientNotificationController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\DatabaseNotification;

class PatientNotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request)
    {
        $patient = Auth::user()->patient ?? abort(404, 'No patient attached.');
        $filter  = $request->input('filter', 'all');
    
        $query = $patient
            ->notifications()              // ← pull from patient, not user
            ->orderBy('created_at', 'desc');
    
        if ($filter === 'read') {
            $query->whereNotNull('read_at');
        } elseif ($filter === 'unread') {
            $query->whereNull('read_at');
        }
    
        $notifications = $query->get();
    
        return view('patient.notifications', compact('notifications','filter'));
    }
    

    public function update(Request $request, DatabaseNotification $notification)
    {
        $patient = Auth::user()->patient ?? abort(403);
        if ($notification->notifiable_id !== $patient->patient_id) {
            abort(403);
        }
        return back();
    }

    public function markAllRead()
    {
        $patient = Auth::user()->patient ?? abort(403);
        $patient->unreadNotifications->markAsRead();
        return back();
    }
}
