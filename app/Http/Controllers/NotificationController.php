<?php

namespace App\Http\Controllers;

use App\Models\UserNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(): View
    {
        $notifications = UserNotification::where('user_id', Auth::id())
            ->latest()
            ->paginate(20);

        // Mark all as read when the page is viewed
        UserNotification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark a single notification as read and redirect to its URL.
     */
    public function markRead(UserNotification $notification): RedirectResponse
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->markAsRead();

        return redirect($notification->url ?? route('notifications.index'));
    }

    /**
     * Return the unread count as JSON (for polling).
     */
    public function unreadCount(): JsonResponse
    {
        $count = UserNotification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllRead(): RedirectResponse
    {
        UserNotification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return redirect()->route('notifications.index')
            ->with('success', 'Semua notifikasi telah ditandai sebagai dibaca.');
    }
}
