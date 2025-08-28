<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a list of the authenticated user's notifications.
     */
    public function index()
    {
        $notifications = Auth::user()
                             ->notifications()
                             ->paginate(20);

        return view('patient.notifications', compact('notifications'));
    }

    /**
     * Mark all of the user's unread notifications as read.
     */
    public function markAllRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return back()->with('success', 'All notifications marked as read.');
    }
}