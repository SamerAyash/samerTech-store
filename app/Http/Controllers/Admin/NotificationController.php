<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get unread notifications count
     */
    public function unreadCount(): JsonResponse
    {
        $admin = admin();
        if (!$admin) {
            return response()->json(['count' => 0], 401);
        }

        $count = $admin->unreadNotifications()->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Get latest notifications
     */
    public function latest(): JsonResponse
    {
        $admin = admin();
        if (!$admin) {
            return response()->json(['notifications' => []], 401);
        }

        $notifications = $admin->notifications()
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($notification) {
                $data = $notification->data;
                return [
                    'id' => $notification->id,
                    'type' => $data['type'] ?? 'unknown',
                    'title' => $data['title'] ?? 'إشعار',
                    'message' => $data['message'] ?? '',
                    'read_at' => $notification->read_at,
                    'created_at' => $notification->created_at->diffForHumans(),
                    'created_at_full' => $notification->created_at->toDateTimeString(),
                    'data' => $data,
                ];
            });

        return response()->json(['notifications' => $notifications]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request, string $id): JsonResponse
    {
        $admin = admin();
        if (!$admin) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $notification = $admin->notifications()->where('id', $id)->first();

        if (!$notification) {
            return response()->json(['success' => false, 'message' => 'Notification not found'], 404);
        }

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(): JsonResponse
    {
        $admin = admin();
        if (!$admin) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $admin->unreadNotifications->markAsRead();

        return response()->json(['success' => true]);
    }
}
