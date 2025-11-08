<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\Api\NotificationResource;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends BaseController
{
    /**
     * Get user notifications
     */
    public function index(Request $request)
    {
        $query = Notification::where('user_id', $request->user()->id);

        // Filter by type if provided
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filter by read status
        if ($request->has('read')) {
            if ($request->read == 'true' || $request->read == '1') {
                $query->whereNotNull('read_at');
            } else {
                $query->whereNull('read_at');
            }
        }

        $notifications = $query->latest()->paginate(15);

        return $this->paginatedResponse($notifications, NotificationResource::class);
    }

    /**
     * Get unread notifications
     */
    public function unread(Request $request)
    {
        $notifications = Notification::where('user_id', $request->user()->id)
            ->whereNull('read_at')
            ->latest()
            ->paginate(15);

        return $this->paginatedResponse($notifications, NotificationResource::class);
    }

    /**
     * Get unread notifications count
     */
    public function unreadCount(Request $request)
    {
        $count = Notification::where('user_id', $request->user()->id)
            ->whereNull('read_at')
            ->count();

        return $this->successResponse(['count' => $count]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request, $id)
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$notification) {
            return $this->notFoundResponse('الإشعار غير موجود');
        }

        $notification->markAsRead();

        return $this->successResponse(new NotificationResource($notification), 'تم تحديد الإشعار كمقروء');
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request)
    {
        Notification::where('user_id', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return $this->successResponse(null, 'تم تحديد جميع الإشعارات كمقروءة');
    }

    /**
     * Delete notification
     */
    public function destroy(Request $request, $id)
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$notification) {
            return $this->notFoundResponse('الإشعار غير موجود');
        }

        $notification->delete();

        return $this->successResponse(null, 'تم حذف الإشعار بنجاح');
    }
}


