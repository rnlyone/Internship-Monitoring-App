<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Return the 20 most recent notifications for the current user.
     */
    public function index(): JsonResponse
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        $unread = $notifications->whereNull('read_at')->count();

        return response()->json([
            'unread'        => $unread,
            'notifications' => $notifications->map(fn($n) => [
                'id'         => $n->id,
                'type'       => $n->type,
                'title'      => $n->title,
                'message'    => $n->message,
                'url'        => $n->url,
                'read'       => $n->read_at !== null,
                'created_at' => $n->created_at->diffForHumans(),
            ]),
        ]);
    }

    /**
     * Mark a single notification as read.
     */
    public function markRead(Notification $notification): JsonResponse
    {
        if ($notification->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $notification->update(['read_at' => now()]);

        return response()->json(['message' => 'Marked as read.']);
    }

    /**
     * Mark all notifications for the current user as read.
     */
    public function readAll(): JsonResponse
    {
        Notification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['message' => 'All marked as read.']);
    }
}
