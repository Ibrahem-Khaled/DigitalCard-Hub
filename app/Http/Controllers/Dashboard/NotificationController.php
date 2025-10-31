<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Notification::with(['user', 'notifiable']);

        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($subQ) use ($search) {
                      $subQ->where('first_name', 'like', "%{$search}%")
                           ->orWhere('last_name', 'like', "%{$search}%")
                           ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // فلترة حسب النوع
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // فلترة حسب القناة
        if ($request->filled('channel')) {
            $query->where('channel', $request->channel);
        }

        // فلترة حسب الأولوية
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // فلترة حسب المستخدم
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            if ($request->status === 'read') {
                $query->whereNotNull('read_at');
            } elseif ($request->status === 'unread') {
                $query->whereNull('read_at');
            } elseif ($request->status === 'sent') {
                $query->whereNotNull('sent_at');
            } elseif ($request->status === 'failed') {
                $query->whereNotNull('failed_at');
            } elseif ($request->status === 'scheduled') {
                $query->whereNotNull('scheduled_at')
                      ->where('scheduled_at', '>', now())
                      ->whereNull('sent_at');
            }
        }

        // فلترة حسب الفترة
        if ($request->filled('period')) {
            $days = match ($request->period) {
                'week' => 7,
                'month' => 30,
                'quarter' => 90,
                'year' => 365,
                default => 30
            };
            $query->where('created_at', '>=', now()->subDays($days));
        }

        // ترتيب
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        // التحقق من صحة معاملات الترتيب
        $allowedSortFields = ['created_at', 'title', 'type', 'priority', 'updated_at'];
        $allowedSortOrders = ['asc', 'desc'];

        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'created_at';
        }

        if (!in_array($sortOrder, $allowedSortOrders)) {
            $sortOrder = 'desc';
        }

        $query->orderBy($sortBy, $sortOrder);

        $notifications = $query->paginate(15)->withQueryString();

        // إحصائيات
        $stats = [
            'total_notifications' => Notification::count(),
            'unread_notifications' => Notification::unread()->count(),
            'read_notifications' => Notification::read()->count(),
            'sent_notifications' => Notification::whereNotNull('sent_at')->count(),
            'failed_notifications' => Notification::failed()->count(),
            'scheduled_notifications' => Notification::scheduled()->count(),
            'recent_notifications' => Notification::where('created_at', '>=', now()->subDays(7))->count(),
            'notifications_by_type' => Notification::select('type', DB::raw('count(*) as count'))
                ->groupBy('type')
                ->orderBy('count', 'desc')
                ->get(),
            'notifications_by_channel' => Notification::select('channel', DB::raw('count(*) as count'))
                ->groupBy('channel')
                ->orderBy('count', 'desc')
                ->get(),
        ];

        // المستخدمين للفلترة
        $users = User::select('id', 'first_name', 'last_name', 'email')->get();

        return view('dashboard.notifications.index', compact('notifications', 'stats', 'users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::select('id', 'first_name', 'last_name', 'email')->get();
        return view('dashboard.notifications.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'data' => 'nullable|array',
            'channel' => 'required|in:database,email,sms,push',
            'priority' => 'required|in:low,normal,high,urgent',
            'scheduled_at' => 'nullable|date|after:now',
        ], [
            'user_id.required' => 'يجب اختيار المستخدم',
            'user_id.exists' => 'المستخدم غير موجود',
            'type.required' => 'يجب إدخال نوع الإشعار',
            'title.required' => 'يجب إدخال عنوان الإشعار',
            'message.required' => 'يجب إدخال رسالة الإشعار',
            'channel.required' => 'يجب اختيار قناة الإشعار',
            'channel.in' => 'قناة الإشعار غير صحيحة',
            'priority.required' => 'يجب اختيار أولوية الإشعار',
            'priority.in' => 'أولوية الإشعار غير صحيحة',
        ]);

        $notificationData = $request->except(['scheduled_at']);

        // تحويل التاريخ
        if ($request->scheduled_at) {
            $notificationData['scheduled_at'] = $request->scheduled_at;
        }

        // إرسال فوري إذا لم يكن مجدول
        if (!$request->scheduled_at) {
            $notificationData['sent_at'] = now();
        }

        $notification = Notification::create($notificationData);

        return redirect()->route('dashboard.notifications.index')
            ->with('success', 'تم إنشاء الإشعار بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Notification $notification)
    {
        $notification->load(['user', 'notifiable']);

        // إحصائيات الإشعار
        $notificationStats = [
            'is_read' => $notification->isRead(),
            'is_sent' => $notification->isSent(),
            'is_failed' => $notification->isFailed(),
            'is_scheduled' => $notification->scheduled_at && $notification->scheduled_at->isFuture(),
            'days_since_created' => $notification->created_at->diffInDays(now()),
            'retry_count' => $notification->retry_count,
            'user_total_notifications' => Notification::forUser($notification->user_id)->count(),
            'user_unread_notifications' => Notification::forUser($notification->user_id)->unread()->count(),
        ];

        return view('dashboard.notifications.show', compact('notification', 'notificationStats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Notification $notification)
    {
        $users = User::select('id', 'first_name', 'last_name', 'email')->get();
        return view('dashboard.notifications.edit', compact('notification', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Notification $notification)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'data' => 'nullable|array',
            'channel' => 'required|in:database,email,sms,push',
            'priority' => 'required|in:low,normal,high,urgent',
            'scheduled_at' => 'nullable|date',
        ], [
            'user_id.required' => 'يجب اختيار المستخدم',
            'user_id.exists' => 'المستخدم غير موجود',
            'type.required' => 'يجب إدخال نوع الإشعار',
            'title.required' => 'يجب إدخال عنوان الإشعار',
            'message.required' => 'يجب إدخال رسالة الإشعار',
            'channel.required' => 'يجب اختيار قناة الإشعار',
            'channel.in' => 'قناة الإشعار غير صحيحة',
            'priority.required' => 'يجب اختيار أولوية الإشعار',
            'priority.in' => 'أولوية الإشعار غير صحيحة',
        ]);

        $notificationData = $request->except(['scheduled_at']);

        // تحويل التاريخ
        if ($request->scheduled_at) {
            $notificationData['scheduled_at'] = $request->scheduled_at;
        } else {
            $notificationData['scheduled_at'] = null;
        }

        $notification->update($notificationData);

        return redirect()->route('dashboard.notifications.index')
            ->with('success', 'تم تحديث الإشعار بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Notification $notification)
    {
        $notification->delete();

        return redirect()->route('dashboard.notifications.index')
            ->with('success', 'تم حذف الإشعار بنجاح');
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(Notification $notification)
    {
        if ($notification->isRead()) {
            return redirect()->back()
                ->with('error', 'الإشعار مقروء بالفعل');
        }

        $notification->markAsRead();

        return redirect()->back()
            ->with('success', 'تم وضع علامة على الإشعار كمقروء');
    }

    /**
     * Mark notification as unread.
     */
    public function markAsUnread(Notification $notification)
    {
        if ($notification->isUnread()) {
            return redirect()->back()
                ->with('error', 'الإشعار غير مقروء بالفعل');
        }

        $notification->markAsUnread();

        return redirect()->back()
            ->with('success', 'تم وضع علامة على الإشعار كغير مقروء');
    }

    /**
     * Mark notification as sent.
     */
    public function markAsSent(Notification $notification)
    {
        if ($notification->isSent()) {
            return redirect()->back()
                ->with('error', 'الإشعار مرسل بالفعل');
        }

        $notification->markAsSent();

        return redirect()->back()
            ->with('success', 'تم وضع علامة على الإشعار كمرسل');
    }

    /**
     * Mark notification as failed.
     */
    public function markAsFailed(Notification $notification)
    {
        if ($notification->isFailed()) {
            return redirect()->back()
                ->with('error', 'الإشعار فشل بالفعل');
        }

        $notification->markAsFailed();

        return redirect()->back()
            ->with('success', 'تم وضع علامة على الإشعار كفاشل');
    }

    /**
     * Retry failed notification.
     */
    public function retry(Notification $notification)
    {
        if (!$notification->isFailed()) {
            return redirect()->back()
                ->with('error', 'الإشعار لم يفشل');
        }

        $notification->update([
            'failed_at' => null,
            'retry_count' => $notification->retry_count + 1,
        ]);

        return redirect()->back()
            ->with('success', 'تم إعادة محاولة الإشعار');
    }

    /**
     * Send notification immediately.
     */
    public function sendNow(Notification $notification)
    {
        if ($notification->isSent()) {
            return redirect()->back()
                ->with('error', 'الإشعار مرسل بالفعل');
        }

        $notification->update([
            'sent_at' => now(),
            'scheduled_at' => null,
        ]);

        return redirect()->back()
            ->with('success', 'تم إرسال الإشعار فوراً');
    }

    /**
     * Bulk mark as read.
     */
    public function bulkMarkAsRead(Request $request)
    {
        $request->validate([
            'notification_ids' => 'required|array',
            'notification_ids.*' => 'exists:notifications,id',
        ]);

        Notification::whereIn('id', $request->notification_ids)
            ->update(['read_at' => now()]);

        return redirect()->back()
            ->with('success', 'تم وضع علامة على الإشعارات كمقروءة');
    }

    /**
     * Bulk delete.
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'notification_ids' => 'required|array',
            'notification_ids.*' => 'exists:notifications,id',
        ]);

        Notification::whereIn('id', $request->notification_ids)->delete();

        return redirect()->back()
            ->with('success', 'تم حذف الإشعارات بنجاح');
    }

    /**
     * Export notifications to CSV.
     */
    public function export(Request $request)
    {
        $query = Notification::with(['user']);

        // تطبيق نفس الفلاتر
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($subQ) use ($search) {
                      $subQ->where('first_name', 'like', "%{$search}%")
                           ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $notifications = $query->get();

        $filename = 'notifications_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($notifications) {
            $file = fopen('php://output', 'w');

            // إضافة BOM للدعم العربي
            fwrite($file, "\xEF\xBB\xBF");

            // رؤوس الأعمدة
            fputcsv($file, [
                'المستخدم',
                'النوع',
                'العنوان',
                'الرسالة',
                'القناة',
                'الأولوية',
                'تاريخ القراءة',
                'تاريخ الإرسال',
                'تاريخ الفشل',
                'عدد المحاولات',
                'تاريخ الإنشاء'
            ]);

            foreach ($notifications as $notification) {
                fputcsv($file, [
                    $notification->user->full_name ?? 'غير محدد',
                    $notification->type,
                    $notification->title,
                    $notification->message,
                    $this->getChannelText($notification->channel),
                    $this->getPriorityText($notification->priority),
                    $notification->read_at?->format('Y-m-d H:i:s'),
                    $notification->sent_at?->format('Y-m-d H:i:s'),
                    $notification->failed_at?->format('Y-m-d H:i:s'),
                    $notification->retry_count,
                    $notification->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get notification statistics.
     */
    public function stats()
    {
        $stats = [
            'total_notifications' => Notification::count(),
            'unread_notifications' => Notification::unread()->count(),
            'read_notifications' => Notification::read()->count(),
            'sent_notifications' => Notification::whereNotNull('sent_at')->count(),
            'failed_notifications' => Notification::failed()->count(),
            'scheduled_notifications' => Notification::scheduled()->count(),
            'notifications_last_24h' => Notification::where('created_at', '>=', now()->subDay())->count(),
            'notifications_last_week' => Notification::where('created_at', '>=', now()->subWeek())->count(),
            'notifications_last_month' => Notification::where('created_at', '>=', now()->subMonth())->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Get channel text in Arabic.
     */
    private function getChannelText($channel)
    {
        return match ($channel) {
            'database' => 'قاعدة البيانات',
            'email' => 'البريد الإلكتروني',
            'sms' => 'رسالة نصية',
            'push' => 'إشعار فوري',
            default => 'غير محدد',
        };
    }

    /**
     * Get priority text in Arabic.
     */
    private function getPriorityText($priority)
    {
        return match ($priority) {
            'low' => 'منخفض',
            'normal' => 'عادي',
            'high' => 'عالي',
            'urgent' => 'عاجل',
            default => 'غير محدد',
        };
    }
}
