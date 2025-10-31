@extends('layouts.dashboard-new')

@section('title', 'إدارة نقاط الولاء - متجر البطاقات الرقمية')

@section('page-title', 'إدارة نقاط الولاء')
@section('page-subtitle', 'عرض وإدارة نظام نقاط الولاء والمكافآت')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="page-title">إدارة نقاط الولاء</h3>
            <p class="page-subtitle">عرض وإدارة نظام نقاط الولاء والمكافآت</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('dashboard.loyalty-points.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>
                إضافة نقاط جديدة
            </a>
            <a href="{{ route('dashboard.loyalty-points.export') }}" class="btn btn-outline-success">
                <i class="bi bi-download me-2"></i>
                تصدير CSV
            </a>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="stats-grid">
    <x-dashboard.stats-card
        title="إجمالي النقاط"
        :value="number_format($stats['total_points'])"
        icon="bi-star"
        change-type="positive"
        change-text="+15.3% من الشهر الماضي" />

    <x-dashboard.stats-card
        title="إجمالي القيمة"
        :value="number_format($stats['total_value_usd'], 2) . ' $'"
        icon="bi-currency-dollar"
        change-type="success"
        change-text="القيمة الإجمالية" />

    <x-dashboard.stats-card
        title="النقاط النشطة"
        :value="number_format($stats['active_points'])"
        icon="bi-check-circle"
        change-type="positive"
        change-text="+12.7% من الشهر الماضي" />

    <x-dashboard.stats-card
        title="القيمة النشطة"
        :value="number_format($stats['active_value_usd'], 2) . ' $'"
        icon="bi-graph-up"
        change-type="success"
        change-text="القيمة المتاحة" />

    <x-dashboard.stats-card
        title="النقاط المكتسبة"
        :value="number_format($stats['earned_points'])"
        icon="bi-trophy"
        change-type="positive"
        change-text="+18.9% من الشهر الماضي" />

    <x-dashboard.stats-card
        title="قيمة المكتسب"
        :value="number_format($stats['earned_value_usd'], 2) . ' $'"
        icon="bi-cash-stack"
        change-type="success"
        change-text="قيمة المكتسب" />

    <x-dashboard.stats-card
        title="النقاط المستردة"
        :value="number_format($stats['redeemed_points'])"
        icon="bi-arrow-left-circle"
        change-type="negative"
        change-text="+8.2% من الشهر الماضي" />

    <x-dashboard.stats-card
        title="قيمة المسترد"
        :value="number_format($stats['redeemed_value_usd'], 2) . ' $'"
        icon="bi-arrow-down-circle"
        change-type="warning"
        change-text="قيمة المسترد" />

    <x-dashboard.stats-card
        title="النقاط المنتهية"
        :value="number_format($stats['expired_points'])"
        icon="bi-clock"
        change-type="negative"
        change-text="+5.1% من الشهر الماضي" />

    <x-dashboard.stats-card
        title="قيمة المنتهي"
        :value="number_format($stats['expired_value_usd'], 2) . ' $'"
        icon="bi-hourglass-split"
        change-type="danger"
        change-text="قيمة المنتهي" />

    <x-dashboard.stats-card
        title="نقاط المكافآت"
        :value="number_format($stats['bonus_points'])"
        icon="bi-gift"
        change-type="positive"
        change-text="+22.4% من الشهر الماضي" />

    <x-dashboard.stats-card
        title="قيمة المكافآت"
        :value="number_format($stats['bonus_value_usd'], 2) . ' $'"
        icon="bi-gift-fill"
        change-type="info"
        change-text="قيمة المكافآت" />
</div>

<!-- Top Users -->
@if($stats['top_users']->count() > 0)
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="bi bi-trophy me-2"></i>
            أفضل المستخدمين بالنقاط
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            @foreach($stats['top_users'] as $topUser)
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="d-flex align-items-center p-3 border rounded">
                    <div class="user-avatar me-3">
                        @if($topUser->user->avatar)
                            <img src="{{ Storage::url($topUser->user->avatar) }}" alt="{{ $topUser->user->full_name }}" class="rounded-circle" width="40" height="40">
                        @else
                            <div class="avatar-placeholder">{{ $topUser->user->display_name }}</div>
                        @endif
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-0">{{ $topUser->user->full_name }}</h6>
                        <small class="text-muted">{{ $topUser->user->email }}</small>
                    </div>
                    <div class="text-end">
                        <span class="badge badge-primary">{{ number_format($topUser->total_points) }} نقطة</span>
                        <br>
                        <small class="text-success">{{ number_format($topUser->total_value_usd, 2) }} $</small>
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
        ['name' => 'type', 'label' => 'النوع', 'type' => 'select', 'placeholder' => 'جميع الأنواع', 'options' => ['earned' => 'مكتسب', 'redeemed' => 'مسترد', 'expired' => 'منتهي', 'bonus' => 'مكافأة']],
        ['name' => 'source', 'label' => 'المصدر', 'type' => 'select', 'placeholder' => 'جميع المصادر', 'options' => ['purchase' => 'شراء', 'referral' => 'إحالة', 'review' => 'تقييم', 'bonus' => 'مكافأة', 'manual' => 'يدوي']],
        ['name' => 'user_id', 'label' => 'المستخدم', 'type' => 'select', 'placeholder' => 'جميع المستخدمين', 'options' => $users->pluck('full_name', 'id')->toArray()],
        ['name' => 'status', 'label' => 'الحالة', 'type' => 'select', 'placeholder' => 'جميع الحالات', 'options' => ['active' => 'نشط', 'expired' => 'منتهي']],
        ['name' => 'period', 'label' => 'الفترة', 'type' => 'select', 'placeholder' => 'جميع الفترات', 'options' => ['week' => 'أسبوع', 'month' => 'شهر', 'quarter' => 'ربع سنة', 'year' => 'سنة']],
        ['name' => 'sort_by', 'label' => 'ترتيب حسب', 'type' => 'select', 'placeholder' => 'ترتيب حسب', 'options' => ['created_at' => 'تاريخ الإنشاء', 'points' => 'عدد النقاط', 'type' => 'النوع', 'source' => 'المصدر']],
        ['name' => 'sort_order', 'label' => 'اتجاه الترتيب', 'type' => 'select', 'placeholder' => 'اتجاه الترتيب', 'options' => ['desc' => 'تنازلي', 'asc' => 'تصاعدي']]
    ]"
    search-placeholder="البحث في نقاط الولاء..."
    :search-value="request('search')"
    :action-url="route('dashboard.loyalty-points.index')" />

<!-- Loyalty Points Table -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="bi bi-star me-2"></i>
            قائمة نقاط الولاء
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>المستخدم</th>
                        <th>النقاط</th>
                        <th>قيمة النقطة</th>
                        <th>القيمة الإجمالية</th>
                        <th>النوع</th>
                        <th>المصدر</th>
                        <th>الوصف</th>
                        <th>تاريخ الانتهاء</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($loyaltyPoints as $point)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="user-avatar me-3">
                                    @if($point->user->avatar)
                                        <img src="{{ Storage::url($point->user->avatar) }}" alt="{{ $point->user->full_name }}" class="rounded-circle" width="32" height="32">
                                    @else
                                        <div class="avatar-placeholder">{{ $point->user->display_name }}</div>
                                    @endif
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $point->user->full_name }}</h6>
                                    <small class="text-muted">{{ $point->user->email }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="fw-bold {{ $point->points > 0 ? 'text-success' : 'text-danger' }}">
                                {{ $point->points > 0 ? '+' : '' }}{{ number_format($point->points) }}
                            </span>
                        </td>
                        <td>
                            <span class="text-info fw-semibold">{{ number_format($point->point_value_usd, 4) }} $</span>
                        </td>
                        <td>
                            <span class="fw-bold {{ $point->total_value_usd > 0 ? 'text-success' : 'text-danger' }}">
                                {{ $point->total_value_usd > 0 ? '+' : '' }}{{ number_format($point->total_value_usd, 2) }} $
                            </span>
                        </td>
                        <td>
                            @if($point->type === 'earned')
                                <span class="badge badge-success">مكتسب</span>
                            @elseif($point->type === 'redeemed')
                                <span class="badge badge-warning">مسترد</span>
                            @elseif($point->type === 'expired')
                                <span class="badge badge-secondary">منتهي</span>
                            @elseif($point->type === 'bonus')
                                <span class="badge badge-info">مكافأة</span>
                            @endif
                        </td>
                        <td>
                            <span class="text-muted">{{ $point->source }}</span>
                            @if($point->source_id)
                                <br><small class="text-muted">ID: {{ $point->source_id }}</small>
                            @endif
                        </td>
                        <td>
                            <span class="text-muted">{{ Str::limit($point->description ?? 'لا يوجد وصف', 30) }}</span>
                        </td>
                        <td>
                            @if($point->expires_at)
                                <span class="text-muted">{{ $point->expires_at->format('Y-m-d') }}</span>
                                <br>
                                <small class="text-muted">{{ $point->expires_at->diffForHumans() }}</small>
                            @else
                                <span class="text-muted">لا ينتهي</span>
                            @endif
                        </td>
                        <td>
                            @if($point->isActive())
                                <span class="badge badge-success">نشط</span>
                            @elseif($point->isExpired())
                                <span class="badge badge-warning">منتهي</span>
                            @else
                                <span class="badge badge-secondary">غير نشط</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('dashboard.loyalty-points.show', $point) }}" class="btn btn-sm btn-outline-primary" title="عرض التفاصيل">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('dashboard.loyalty-points.edit', $point) }}" class="btn btn-sm btn-outline-secondary" title="تعديل">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if(!$point->isExpired())
                                    <form method="POST" action="{{ route('dashboard.loyalty-points.mark-expired', $point) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-warning" title="وضع علامة كمنتهي">
                                            <i class="bi bi-clock"></i>
                                        </button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('dashboard.loyalty-points.toggle-status', $point) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-info" title="{{ $point->is_active ? 'إلغاء تفعيل' : 'تفعيل' }}">
                                        <i class="bi bi-{{ $point->is_active ? 'pause' : 'play' }}"></i>
                                    </button>
                                </form>
                                @if($point->transactions()->count() == 0)
                                    <form method="POST" action="{{ route('dashboard.loyalty-points.destroy', $point) }}" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه النقاط؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-4">
                            <div class="text-muted">
                                <i class="bi bi-star fs-1 d-block mb-3"></i>
                                <h5>لا توجد نقاط ولاء</h5>
                                <p>لم يتم العثور على أي نقاط ولاء مطابقة للبحث.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($loyaltyPoints->hasPages())
    <div class="card-footer">
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted">
                عرض {{ $loyaltyPoints->firstItem() }} إلى {{ $loyaltyPoints->lastItem() }} من {{ $loyaltyPoints->total() }} نقطة ولاء
            </div>
            <div>
                {{ $loyaltyPoints->links() }}
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
</style>
@endpush
@endsection
