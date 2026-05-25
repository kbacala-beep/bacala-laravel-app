<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get unread notifications for the current user (AJAX)
     */
    public function getUnread()
    {
        $notifications = Auth::user()->notifications()
            ->where('read', false)
            ->latest()
            ->limit(10)
            ->get()
            ->map(fn($n) => [
                'id' => $n->id,
                'type' => $n->type,
                'title' => $n->title,
                'message' => $n->message,
                'created_at' => $n->created_at->diffForHumans(),
            ]);

        return response()->json([
            'count' => Auth::user()->notifications()->where('read', false)->count(),
            'notifications' => $notifications,
        ]);
    }

    /**
     * Get all notifications with pagination
     */
    public function index()
    {
        $notifications = Auth::user()->notifications()
            ->latest()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead(Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $notification->markAsRead();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return back();
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        Auth::user()->notifications()
            ->where('read', false)
            ->update([
                'read' => true,
                'read_at' => now(),
            ]);

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'All notifications marked as read.']);
        }

        return back()->with('success', 'All notifications marked as read.');
    }

    /**
     * Delete a notification
     */
    public function delete(Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $notification->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return back();
    }

    /**
     * Show notification preferences/settings
     */
    public function preferences()
    {
        $prefs = Auth::user()->notificationPreference()->updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'report_created' => true,
                'report_status_changed' => true,
                'user_suspended' => true,
                'user_activated' => true,
                'role_changed' => true,
            ]
        );

        return view('notifications.preferences', compact('prefs'));
    }

    /**
     * Update notification preferences
     */
    public function updatePreferences(Request $request)
    {
        $validated = $request->validate([
            'report_created' => 'boolean',
            'report_status_changed' => 'boolean',
            'user_suspended' => 'boolean',
            'user_activated' => 'boolean',
            'role_changed' => 'boolean',
        ]);

        Auth::user()->notificationPreference()->updateOrCreate(
            ['user_id' => Auth::id()],
            $validated
        );

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Notification preferences updated.']);
        }

        return back()->with('success', 'Notification preferences updated.');
    }
}
