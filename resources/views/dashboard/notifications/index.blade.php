@extends('layouts.dashboard-new')

@section('title', 'إدارة الإشعارات - متجر البطاقات الرقمية')

@section('page-title', 'إدارة الإشعارات')
@section('page-subtitle', 'عرض وإدارة نظام الإشعارات')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="page-title">إدارة الإشعارات</h3>
            <p class="page-subtitle">عرض وإدارة نظام الإشعارات</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('dashboard.notifications.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>
                إرسال إشعار جديد
            </a>
            <a href="{{ route('dashboard.notifications.export') }}" class="btn btn-outline-success">
                <i class="bi bi-download me-2"></i>
                تصدير CSV
            </a>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="stats-grid">
    <x-dashboard.stats-card
        title="إجمالي الإشعارات"
        :value="number_format($stats['total_notifications'])"
        icon="bi-bell"
        change-type="positive"
        change-text="+18.5% من الشهر الماضي" />

    <x-dashboard.stats-card
        title="الإشعارات غير المقروءة"
        :value="number_format($stats['unread_notifications'])"
        icon="bi-bell-fill"
        change-type="warning"
        change-text="+12.3% من الشهر الماضي" />

    <x-dashboard.stats-card
        title="الإشعارات المقروءة"
        :value="number_format($stats['read_notifications'])"
        icon="bi-check-circle"
        change-type="positive"
        change-text="+15.7% من الشهر الماضي" />

    <x-dashboard.stats-card
        title="الإشعارات المرسلة"
        :value="number_format($stats['sent_notifications'])"
        icon="bi-send"
        change-type="positive"
        change-text="+22.1% من الشهر الماضي" />

    <x-dashboard.stats-card
        title="الإشعارات الفاشلة"
        :value="number_format($stats['failed_notifications'])"
        icon="bi-exclamation-triangle"
        change-type="negative"
        change-text="+5.2% من الشهر الماضي" />

    <x-dashboard.stats-card
        title="الإشعارات المجدولة"
        :value="number_format($stats['scheduled_notifications'])"
        icon="bi-clock"
        change-type="info"
        change-text="+8.9% من الشهر الماضي" />
</div>

<!-- Notifications by Type -->
@if($stats['notifications_by_type']->count() > 0)
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="bi bi-pie-chart me-2"></i>
            الإشعارات حسب النوع
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            @foreach($stats['notifications_by_type'] as $typeStat)
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="d-flex align-items-center p-3 border rounded">
                    <div class="notification-type-icon me-3">
                        <i class="bi bi-{{ match($typeStat->type) { 'order' => 'cart', 'payment' => 'credit-card', 'shipping' => 'truck', 'promotion' => 'gift', 'system' => 'gear', default => 'bell' } }} fs-4 text-primary"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-0">{{ $typeStat->type }}</h6>
                        <small class="text-muted">{{ number_format($typeStat->count) }} إشعار</small>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

<!-- Notifications by Channel -->
@if($stats['notifications_by_channel']->count() > 0)
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="bi bi-broadcast me-2"></i>
            الإشعارات حسب القناة
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            @foreach($stats['notifications_by_channel'] as $channelStat)
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="d-flex align-items-center p-3 border rounded">
                    <div class="notification-channel-icon me-3">
                        <i class="bi bi-{{ match($channelStat->channel) { 'database' => 'database', 'email' => 'envelope', 'sms' => 'phone', 'push' => 'bell', default => 'broadcast' } }} fs-4 text-info"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-0">{{ match($channelStat->channel) { 'database' => 'قاعدة البيانات', 'email' => 'البريد الإلكتروني', 'sms' => 'رسالة نصية', 'push' => 'إشعار فوري', default => 'غير محدد' } }}</h6>
                        <small class="text-muted">{{ number_format($channelStat->count) }} إشعار</small>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

<!-- Filters -->
<x-dashboard.filters
    :filters="[
        ['name' => 'type', 'label' => 'النوع', 'type' => 'select', 'placeholder' => 'جميع الأنواع', 'options' => ['order' => 'طلب', 'payment' => 'دفع', 'shipping' => 'شحن', 'promotion' => 'ترويج', 'system' => 'نظام']],
        ['name' => 'channel', 'label' => 'القناة', 'type' => 'select', 'placeholder' => 'جميع القنوات', 'options' => ['database' => 'قاعدة البيانات', 'email' => 'البريد الإلكتروني', 'sms' => 'رسالة نصية', 'push' => 'إشعار فوري']],
        ['name' => 'priority', 'label' => 'الأولوية', 'type' => 'select', 'placeholder' => 'جميع الأولويات', 'options' => ['low' => 'منخفض', 'normal' => 'عادي', 'high' => 'عالي', 'urgent' => 'عاجل']],
        ['name' => 'user_id', 'label' => 'المستخدم', 'type' => 'select', 'placeholder' => 'جميع المستخدمين', 'options' => $users->pluck('full_name', 'id')->toArray()],
        ['name' => 'status', 'label' => 'الحالة', 'type' => 'select', 'placeholder' => 'جميع الحالات', 'options' => ['read' => 'مقروء', 'unread' => 'غير مقروء', 'sent' => 'مرسل', 'failed' => 'فاشل', 'scheduled' => 'مجدول']],
        ['name' => 'period', 'label' => 'الفترة', 'type' => 'select', 'placeholder' => 'جميع الفترات', 'options' => ['week' => 'أسبوع', 'month' => 'شهر', 'quarter' => 'ربع سنة', 'year' => 'سنة']],
        ['name' => 'sort_by', 'label' => 'ترتيب حسب', 'type' => 'select', 'placeholder' => 'ترتيب حسب', 'options' => ['created_at' => 'تاريخ الإنشاء', 'title' => 'العنوان', 'type' => 'النوع', 'priority' => 'الأولوية']],
        ['name' => 'sort_order', 'label' => 'اتجاه الترتيب', 'type' => 'select', 'placeholder' => 'اتجاه الترتيب', 'options' => ['desc' => 'تنازلي', 'asc' => 'تصاعدي']]
    ]"
    search-placeholder="البحث في الإشعارات..."
    :search-value="request('search')"
    :action-url="route('dashboard.notifications.index')" />

<!-- Notifications Table -->
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="bi bi-bell me-2"></i>
                قائمة الإشعارات
            </h5>
            <div class="bulk-actions">
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAll()">
                    <i class="bi bi-check-all me-1"></i>
                    تحديد الكل
                </button>
                <button type="button" class="btn btn-sm btn-outline-success" onclick="bulkMarkAsRead()">
                    <i class="bi bi-check-circle me-1"></i>
                    وضع علامة كمقروء
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="bulkDelete()">
                    <i class="bi bi-trash me-1"></i>
                    حذف
                </button>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">
                            <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                        </th>
                        <th>المستخدم</th>
                        <th>النوع</th>
                        <th>العنوان</th>
                        <th>القناة</th>
                        <th>الأولوية</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notifications as $notification)
                    <tr class="{{ $notification->isUnread() ? 'table-warning' : '' }}">
                        <td>
                            <input type="checkbox" class="notification-checkbox" value="{{ $notification->id }}">
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="user-avatar me-3">
                                    @if($notification->user->avatar)
                                        <img src="{{ Storage::url($notification->user->avatar) }}" alt="{{ $notification->user->full_name }}" class="rounded-circle" width="32" height="32">
                                    @else
                                        <div class="avatar-placeholder">{{ $notification->user->display_name }}</div>
                                    @endif
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $notification->user->full_name }}</h6>
                                    <small class="text-muted">{{ $notification->user->email }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-secondary">{{ $notification->type }}</span>
                        </td>
                        <td>
                            <div>
                                <span class="fw-bold">{{ Str::limit($notification->title, 30) }}</span>
                                <br>
                                <small class="text-muted">{{ Str::limit($notification->message, 50) }}</small>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-info">{{ match($notification->channel) { 'database' => 'قاعدة البيانات', 'email' => 'البريد الإلكتروني', 'sms' => 'رسالة نصية', 'push' => 'إشعار فوري', default => 'غير محدد' } }}</span>
                        </td>
                        <td>
                            @if($notification->priority === 'urgent')
                                <span class="badge badge-danger">عاجل</span>
                            @elseif($notification->priority === 'high')
                                <span class="badge badge-warning">عالي</span>
                            @elseif($notification->priority === 'normal')
                                <span class="badge badge-primary">عادي</span>
                            @elseif($notification->priority === 'low')
                                <span class="badge badge-secondary">منخفض</span>
                            @endif
                        </td>
                        <td>
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
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('dashboard.notifications.show', $notification) }}" class="btn btn-sm btn-outline-primary" title="عرض التفاصيل">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('dashboard.notifications.edit', $notification) }}" class="btn btn-sm btn-outline-secondary" title="تعديل">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if($notification->isUnread())
                                    <form method="POST" action="{{ route('dashboard.notifications.mark-read', $notification) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success" title="وضع علامة كمقروء">
                                            <i class="bi bi-check-circle"></i>
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('dashboard.notifications.mark-unread', $notification) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-warning" title="وضع علامة كغير مقروء">
                                            <i class="bi bi-circle"></i>
                                        </button>
                                    </form>
                                @endif
                                @if($notification->isFailed())
                                    <form method="POST" action="{{ route('dashboard.notifications.retry', $notification) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-info" title="إعادة المحاولة">
                                            <i class="bi bi-arrow-clockwise"></i>
                                        </button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('dashboard.notifications.destroy', $notification) }}" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الإشعار؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <div class="text-muted">
                                <i class="bi bi-bell fs-1 d-block mb-3"></i>
                                <h5>لا توجد إشعارات</h5>
                                <p>لم يتم العثور على أي إشعارات مطابقة للبحث.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($notifications->hasPages())
    <div class="card-footer">
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted">
                عرض {{ $notifications->firstItem() }} إلى {{ $notifications->lastItem() }} من {{ $notifications->total() }} إشعار
            </div>
            <div>
                {{ $notifications->links() }}
            </div>
        </div>
    </div>
    @endif
</div>

@push('styles')
<style>
.user-avatar {
    width: 32px;
    height: 32px;
}

.avatar-placeholder {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-purple) 0%, var(--dark-purple) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 12px;
    font-weight: bold;
}

.badge {
    font-size: 0.75rem;
}

.notification-type-icon,
.notification-channel-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
}

.bulk-actions {
    display: flex;
    gap: 5px;
}

.table-warning {
    background-color: rgba(255, 193, 7, 0.1) !important;
}
</style>
@endpush

@push('scripts')
<script>
function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.notification-checkbox');

    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
}

function selectAll() {
    const checkboxes = document.querySelectorAll('.notification-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
    });
    document.getElementById('selectAll').checked = true;
}

function bulkMarkAsRead() {
    const checkedBoxes = document.querySelectorAll('.notification-checkbox:checked');
    if (checkedBoxes.length === 0) {
        alert('يرجى اختيار إشعار واحد على الأقل');
        return;
    }

    const ids = Array.from(checkedBoxes).map(cb => cb.value);

    fetch('{{ route("dashboard.notifications.bulk-mark-read") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ notification_ids: ids })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function bulkDelete() {
    const checkedBoxes = document.querySelectorAll('.notification-checkbox:checked');
    if (checkedBoxes.length === 0) {
        alert('يرجى اختيار إشعار واحد على الأقل');
        return;
    }

    if (!confirm('هل أنت متأكد من حذف الإشعارات المحددة؟')) {
        return;
    }

    const ids = Array.from(checkedBoxes).map(cb => cb.value);

    fetch('{{ route("dashboard.notifications.bulk-delete") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ notification_ids: ids })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}
</script>
@endpush
@endsection
