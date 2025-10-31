@extends('layouts.dashboard-new')

@section('title', 'عرض الإشعار - متجر البطاقات الرقمية')

@section('page-title', 'عرض الإشعار')
@section('page-subtitle', 'تفاصيل الإشعار')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="page-title">عرض الإشعار</h3>
            <p class="page-subtitle">تفاصيل الإشعار</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('dashboard.notifications.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-right me-2"></i>
                العودة للقائمة
            </a>
            <a href="{{ route('dashboard.notifications.edit', $notification) }}" class="btn btn-primary">
                <i class="bi bi-pencil me-2"></i>
                تعديل الإشعار
            </a>
        </div>
    </div>
</div>

<div class="row">
    <!-- معلومات الإشعار الأساسية -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    معلومات الإشعار
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">العنوان:</label>
                    <p class="mb-0 fs-5">{{ $notification->title }}</p>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">النوع:</label>
                    <p class="mb-0">
                        <span class="badge badge-secondary">{{ $notification->type }}</span>
                    </p>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">القناة:</label>
                    <p class="mb-0">
                        <span class="badge badge-info">{{ $this->getChannelText($notification->channel) }}</span>
                    </p>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">الأولوية:</label>
                    <p class="mb-0">
                        @if($notification->priority === 'urgent')
                            <span class="badge badge-danger">عاجل</span>
                        @elseif($notification->priority === 'high')
                            <span class="badge badge-warning">عالي</span>
                        @elseif($notification->priority === 'normal')
                            <span class="badge badge-primary">عادي</span>
                        @elseif($notification->priority === 'low')
                            <span class="badge badge-secondary">منخفض</span>
                        @endif
                    </p>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">الحالة:</label>
                    <p class="mb-0">
                        @if($notification->isUnread())
                            <span class="badge badge-warning">غير مقروء</span>
                        @elseif($notification->isRead())
                            <span class="badge badge-success">مقروء</span>
                        @endif
                        @if($notification->isSent())
                            <br><span class="badge badge-info">مرسل</span>
                        @elseif($notification->isFailed())
                            <br><span class="badge badge-danger">فاشل</span>
                        @elseif($notification->scheduled_at && $notification->scheduled_at->isFuture())
                            <br><span class="badge badge-secondary">مجدول</span>
                        @endif
                    </p>
                </div>

                @if($notification->scheduled_at)
                <div class="mb-3">
                    <label class="form-label fw-bold">تاريخ الجدولة:</label>
                    <p class="mb-0">{{ $notification->scheduled_at->format('Y-m-d H:i:s') }}</p>
                    <small class="text-muted">{{ $notification->scheduled_at->diffForHumans() }}</small>
                </div>
                @endif

                @if($notification->sent_at)
                <div class="mb-3">
                    <label class="form-label fw-bold">تاريخ الإرسال:</label>
                    <p class="mb-0">{{ $notification->sent_at->format('Y-m-d H:i:s') }}</p>
                    <small class="text-muted">{{ $notification->sent_at->diffForHumans() }}</small>
                </div>
                @endif

                @if($notification->read_at)
                <div class="mb-3">
                    <label class="form-label fw-bold">تاريخ القراءة:</label>
                    <p class="mb-0">{{ $notification->read_at->format('Y-m-d H:i:s') }}</p>
                    <small class="text-muted">{{ $notification->read_at->diffForHumans() }}</small>
                </div>
                @endif

                @if($notification->failed_at)
                <div class="mb-3">
                    <label class="form-label fw-bold">تاريخ الفشل:</label>
                    <p class="mb-0">{{ $notification->failed_at->format('Y-m-d H:i:s') }}</p>
                    <small class="text-muted">{{ $notification->failed_at->diffForHumans() }}</small>
                </div>
                @endif

                @if($notification->retry_count > 0)
                <div class="mb-3">
                    <label class="form-label fw-bold">عدد المحاولات:</label>
                    <p class="mb-0">{{ $notification->retry_count }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- إحصائيات الإشعار -->
    <div class="col-lg-8">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="bi bi-bell fs-1 text-primary mb-3"></i>
                        <h3 class="text-primary">{{ $notificationStats['is_read'] ? 'مقروء' : 'غير مقروء' }}</h3>
                        <p class="text-muted mb-0">حالة القراءة</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="bi bi-send fs-1 text-success mb-3"></i>
                        <h3 class="text-success">{{ $notificationStats['is_sent'] ? 'مرسل' : 'لم يرسل' }}</h3>
                        <p class="text-muted mb-0">حالة الإرسال</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="bi bi-clock fs-1 text-warning mb-3"></i>
                        <h3 class="text-warning">{{ number_format($notificationStats['days_since_created']) }}</h3>
                        <p class="text-muted mb-0">الأيام منذ الإنشاء</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="bi bi-person fs-1 text-info mb-3"></i>
                        <h3 class="text-info">{{ number_format($notificationStats['user_total_notifications']) }}</h3>
                        <p class="text-muted mb-0">إجمالي إشعارات المستخدم</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- رسالة الإشعار -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-chat-text me-2"></i>
                    رسالة الإشعار
                </h5>
            </div>
            <div class="card-body">
                <div class="notification-message-display">
                    <p class="mb-0">{{ $notification->message }}</p>
                </div>
            </div>
        </div>

        <!-- معلومات المستخدم -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-person me-2"></i>
                    معلومات المستخدم
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="user-avatar me-3">
                        @if($notification->user->avatar)
                            <img src="{{ Storage::url($notification->user->avatar) }}" alt="{{ $notification->user->full_name }}" class="rounded-circle" width="60" height="60">
                        @else
                            <div class="avatar-placeholder">{{ $notification->user->display_name }}</div>
                        @endif
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="mb-1">{{ $notification->user->full_name }}</h5>
                        <p class="text-muted mb-1">{{ $notification->user->email }}</p>
                        <p class="text-muted mb-0">{{ $notification->user->phone ?? 'لا يوجد رقم هاتف' }}</p>
                    </div>
                    <div class="text-end">
                        <div class="badge badge-primary fs-6">{{ number_format($notificationStats['user_total_notifications']) }} إشعار</div>
                        <br>
                        <div class="badge badge-warning fs-6">{{ number_format($notificationStats['user_unread_notifications']) }} غير مقروء</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- البيانات الإضافية -->
        @if($notification->data)
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-code-square me-2"></i>
                    البيانات الإضافية
                </h5>
            </div>
            <div class="card-body">
                <pre class="bg-light p-3 rounded"><code>{{ json_encode($notification->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
            </div>
        </div>
        @endif

        <!-- تفاصيل إضافية -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-list-ul me-2"></i>
                    تفاصيل إضافية
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">تاريخ الإنشاء:</label>
                            <p class="mb-0">{{ $notification->created_at->format('Y-m-d H:i:s') }}</p>
                            <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">آخر تحديث:</label>
                            <p class="mb-0">{{ $notification->updated_at->format('Y-m-d H:i:s') }}</p>
                            <small class="text-muted">{{ $notification->updated_at->diffForHumans() }}</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">حالة الجدولة:</label>
                            <p class="mb-0">
                                @if($notificationStats['is_scheduled'])
                                    <span class="badge badge-info">مجدول</span>
                                @else
                                    <span class="badge badge-secondary">غير مجدول</span>
                                @endif
                            </p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">حالة الفشل:</label>
                            <p class="mb-0">
                                @if($notificationStats['is_failed'])
                                    <span class="badge badge-danger">فاشل</span>
                                @else
                                    <span class="badge badge-success">نجح</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <h6 class="fw-bold">حالة الإشعار:</h6>
                    <div class="d-flex gap-2">
                        @if($notificationStats['is_read'])
                            <span class="badge badge-success">مقروء</span>
                        @else
                            <span class="badge badge-warning">غير مقروء</span>
                        @endif
                        @if($notificationStats['is_sent'])
                            <span class="badge badge-info">مرسل</span>
                        @endif
                        @if($notificationStats['is_failed'])
                            <span class="badge badge-danger">فاشل</span>
                        @endif
                        @if($notificationStats['is_scheduled'])
                            <span class="badge badge-secondary">مجدول</span>
                        @endif
                        @if($notificationStats['days_since_created'] > 30)
                            <span class="badge badge-info">قديم</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.user-avatar {
    width: 60px;
    height: 60px;
}

.avatar-placeholder {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-purple) 0%, var(--dark-purple) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 16px;
    font-weight: bold;
}

.badge {
    font-size: 0.75rem;
}

.notification-message-display {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 20px;
    font-size: 1.1rem;
    line-height: 1.6;
}

pre {
    font-size: 0.9rem;
    max-height: 300px;
    overflow-y: auto;
}
</style>
@endpush
@endsection
