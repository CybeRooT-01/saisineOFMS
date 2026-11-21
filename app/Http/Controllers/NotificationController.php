<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display all notifications for the authenticated user
     */
    public function index()
    {
        $notifications = Auth::user()->notifications()->paginate(20);
        
        return view('notifications.index', compact('notifications'));
    }

    /**
     * Show a specific notification and mark it as read
     */
    public function show($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        
        // Mark as read
        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }
        
        // Redirect to the related saisine if available
        if (isset($notification->data['saisine_id'])) {
            return redirect()->route('saisines.show', $notification->data['saisine_id']);
        }
        
        return redirect()->route('notifications.index');
    }

    /**
     * Mark all notifications as read
     */
    public function markAllRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        
        return back()->with('success', 'All notifications marked as read');
    }

    /**
     * Mark a specific notification as read
     */
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        
        return back()->with('success', 'Notification marked as read');
    }
}
