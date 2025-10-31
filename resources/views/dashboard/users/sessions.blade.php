@extends('layouts.dashboard-new')

@section('title', 'جلسات المستخدم - ' . $user->full_name . ' - متجر البطاقات الرقمية')

@section('page-title', 'جلسات المستخدم')
@section('page-subtitle', 'تتبع جلسات المستخدم: ' . $user->full_name)

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="page-title">جلسات المستخدم</h3>
            <p class="page-subtitle">تتبع جلسات المستخدم: {{ $user->full_name }}</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('dashboard.users.show', $user) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-right me-2"></i>
                العودة للمستخدم
            </a>
            <form method="POST" action="{{ route('dashboard.users.terminate-all-sessions', $user) }}" class="d-inline" onsubmit="return confirm('هل أنت متأكد من إنهاء جميع جلسات هذا المستخدم؟')">
                @csrf
                <button type="submit" class="btn btn-outline-danger">
                    <i class="bi bi-stop-circle me-2"></i>
                    إنهاء جميع الجلسات
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="stats-grid">
    <x-dashboard.stats-card
        title="إجمالي الجلسات"
        :value="number_format($stats['total_sessions'])"
        icon="bi-clock-history"
        change-type="positive"
        change-text="+12.5% من الشهر الماضي" />

    <x-dashboard.stats-card
        title="الجلسات النشطة"
        :value="number_format($stats['active_sessions'])"
        icon="bi-play-circle"
        change-type="positive"
        change-text="+8.3% من الشهر الماضي" />

    <x-dashboard.stats-card
        title="إجمالي الوقت"
        :value="number_format($stats['total_duration']) . ' دقيقة'"
        icon="bi-stopwatch"
        change-type="positive"
        change-text="+15.7% من الشهر الماضي" />

    <x-dashboard.stats-card
        title="متوسط الجلسة"
        :value="number_format($stats['avg_session_duration'], 1) . ' دقيقة'"
        icon="bi-graph-up"
        change-type="neutral"
        change-text="ثابت" />

    <x-dashboard.stats-card
        title="الأجهزة المختلفة"
        :value="number_format($stats['unique_devices'])"
        icon="bi-device-hdd"
        change-type="positive"
        change-text="+3.2% من الشهر الماضي" />

    <x-dashboard.stats-card
        title="المواقع المختلفة"
        :value="number_format($stats['unique_locations'])"
        icon="bi-geo-alt"
        change-type="positive"
        change-text="+5.1% من الشهر الماضي" />
</div>

<!-- Filters -->
<x-dashboard.filters
    :filters="[
        ['name' => 'status', 'label' => 'الحالة', 'type' => 'select', 'placeholder' => 'جميع الحالات', 'options' => ['active' => 'نشط', 'inactive' => 'منتهي'], 'value' => request('status')],
        ['name' => 'period', 'label' => 'الفترة', 'type' => 'select', 'placeholder' => 'جميع الفترات', 'options' => ['week' => 'أسبوع', 'month' => 'شهر', 'quarter' => 'ربع سنة', 'year' => 'سنة'], 'value' => request('period')]
    ]"
    search-placeholder="البحث في الجلسات..." />

<!-- Sessions Table -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="bi bi-clock-history me-2"></i>
            قائمة الجلسات
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>الجهاز</th>
                        <th>المتصفح</th>
                        <th>الموقع</th>
                        <th>عنوان IP</th>
                        <th>تاريخ تسجيل الدخول</th>
                        <th>آخر نشاط</th>
                        <th>المدة</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sessions as $session)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="device-icon me-3">
                                    <i class="{{ $session->device_icon }}"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ ucfirst($session->device_type ?? 'غير محدد') }}</h6>
                                    <small class="text-muted">{{ $session->os ?? 'غير محدد' }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="{{ $session->browser_icon }} me-2"></i>
                                <span>{{ $session->browser ?? 'غير محدد' }}</span>
                            </div>
                        </td>
                        <td>
                            <div>
                                <span class="fw-bold">{{ $session->country ?? 'غير محدد' }}</span>
                                @if($session->city)
                                    <br><small class="text-muted">{{ $session->city }}</small>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="font-monospace">{{ $session->ip_address }}</span>
                        </td>
                        <td>
                            <span class="text-muted">{{ $session->login_at->format('Y-m-d H:i:s') }}</span>
                            <br>
                            <small class="text-muted">{{ $session->login_at->diffForHumans() }}</small>
                        </td>
                        <td>
                            @if($session->last_activity_at)
                                <span class="text-muted">{{ $session->last_activity_at->format('Y-m-d H:i:s') }}</span>
                                <br>
                                <small class="text-muted">{{ $session->last_activity_at->diffForHumans() }}</small>
                            @else
                                <span class="text-muted">غير محدد</span>
                            @endif
                        </td>
                        <td>
                            @if($session->duration)
                                <span class="fw-bold">{{ $session->formatted_duration }}</span>
                            @else
                                <span class="text-muted">غير محدد</span>
                            @endif
                        </td>
                        <td>
                            @if($session->isActive())
                                <span class="badge badge-success">نشط</span>
                            @else
                                <span class="badge badge-secondary">منتهي</span>
                            @endif
                        </td>
                        <td>
                            @if($session->isActive())
                                <form method="POST" action="{{ route('dashboard.users.terminate-session', $session) }}" class="d-inline" onsubmit="return confirm('هل أنت متأكد من إنهاء هذه الجلسة؟')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="إنهاء الجلسة">
                                        <i class="bi bi-stop-circle"></i>
                                    </button>
                                </form>
                            @else
                                <span class="text-muted">منتهية</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4">
                            <div class="text-muted">
                                <i class="bi bi-clock-history fs-1 d-block mb-3"></i>
                                <h5>لا توجد جلسات</h5>
                                <p>لم يتم العثور على أي جلسات مطابقة للبحث.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($sessions->hasPages())
    <div class="card-footer">
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted">
                عرض {{ $sessions->firstItem() }} إلى {{ $sessions->lastItem() }} من {{ $sessions->total() }} جلسة
            </div>
            <div>
                {{ $sessions->links() }}
            </div>
        </div>
    </div>
    @endif
</div>

@push('styles')
<style>
.device-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    background: linear-gradient(135deg, var(--primary-purple) 0%, var(--dark-purple) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 16px;
}

.badge {
    font-size: 0.75rem;
}

.font-monospace {
    font-family: 'Courier New', monospace;
}

.whatsapp-btn {
    background-color: #25D366 !important;
    border-color: #25D366 !important;
    color: white !important;
    padding: 4px 8px;
    font-size: 12px;
}

.whatsapp-btn:hover {
    background-color: #128C7E !important;
    border-color: #128C7E !important;
    color: white !important;
}
</style>
@endpush
@endsection
