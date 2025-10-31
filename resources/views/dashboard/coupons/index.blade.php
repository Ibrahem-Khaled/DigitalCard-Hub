@extends('layouts.dashboard-new')

@section('title', 'إدارة الكوبونات - متجر البطاقات الرقمية')

@section('page-title', 'إدارة الكوبونات')
@section('page-subtitle', 'عرض وإدارة كوبونات الخصم')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="page-title">إدارة الكوبونات</h3>
            <p class="page-subtitle">عرض وإدارة كوبونات الخصم والعروض الخاصة</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('dashboard.coupons.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>
                إضافة كوبون جديد
            </a>
            <a href="{{ route('dashboard.coupons.export') }}" class="btn btn-outline-success">
                <i class="bi bi-download me-2"></i>
                تصدير CSV
            </a>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="stats-grid">
    <x-dashboard.stats-card
        title="إجمالي الكوبونات"
        :value="number_format($stats['total_coupons'])"
        icon="bi-ticket-perforated"
        change-type="positive"
        change-text="+8.5% من الشهر الماضي" />

    <x-dashboard.stats-card
        title="الكوبونات النشطة"
        :value="number_format($stats['active_coupons'])"
        icon="bi-check-circle"
        change-type="positive"
        change-text="+12.3% من الشهر الماضي" />

    <x-dashboard.stats-card
        title="الكوبونات المنتهية"
        :value="number_format($stats['expired_coupons'])"
        icon="bi-x-circle"
        change-type="warning"
        change-text="+5.7% من الشهر الماضي" />

    <x-dashboard.stats-card
        title="الكوبونات الصالحة"
        :value="number_format($stats['valid_coupons'])"
        icon="bi-shield-check"
        change-type="positive"
        change-text="+15.1% من الشهر الماضي" />

    <x-dashboard.stats-card
        title="إجمالي الاستخدامات"
        :value="number_format($stats['total_usage'])"
        icon="bi-graph-up"
        change-type="positive"
        change-text="+22.5% من الشهر الماضي" />

    <x-dashboard.stats-card
        title="إجمالي الخصومات"
        :value="number_format($stats['total_discount_given'], 2) . ' $'"
        icon="bi-currency-dollar"
        change-type="positive"
        change-text="+18.3% من الشهر الماضي" />
</div>

<!-- Filters -->
<x-dashboard.filters
    :filters="[
        ['name' => 'type', 'label' => 'النوع', 'type' => 'select', 'placeholder' => 'جميع الأنواع', 'options' => ['percentage' => 'نسبة مئوية', 'fixed' => 'مبلغ ثابت']],
        ['name' => 'status', 'label' => 'الحالة', 'type' => 'select', 'placeholder' => 'جميع الحالات', 'options' => ['active' => 'نشط', 'expired' => 'منتهي', 'valid' => 'صالح']],
        ['name' => 'period', 'label' => 'الفترة', 'type' => 'select', 'placeholder' => 'جميع الفترات', 'options' => ['week' => 'أسبوع', 'month' => 'شهر', 'quarter' => 'ربع سنة', 'year' => 'سنة']],
        ['name' => 'sort_by', 'label' => 'ترتيب حسب', 'type' => 'select', 'placeholder' => 'ترتيب حسب', 'options' => ['created_at' => 'تاريخ الإنشاء', 'code' => 'الكود', 'used_count' => 'عدد الاستخدامات', 'expires_at' => 'تاريخ الانتهاء']],
        ['name' => 'sort_order', 'label' => 'اتجاه الترتيب', 'type' => 'select', 'placeholder' => 'اتجاه الترتيب', 'options' => ['desc' => 'تنازلي', 'asc' => 'تصاعدي']]
    ]"
    search-placeholder="البحث في الكوبونات..."
    :search-value="request('search')"
    :action-url="route('dashboard.coupons.index')" />

<!-- Coupons Table -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="bi bi-ticket-perforated me-2"></i>
            قائمة الكوبونات
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>الكود</th>
                        <th>الاسم</th>
                        <th>النوع</th>
                        <th>القيمة</th>
                        <th>الحد الأدنى</th>
                        <th>الاستخدامات</th>
                        <th>تاريخ الانتهاء</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($coupons as $coupon)
                    <tr>
                        <td>
                            <div>
                                <span class="fw-bold font-monospace">{{ $coupon->code }}</span>
                                @if($coupon->stackable)
                                    <br><small class="text-info">قابل للتجميع</small>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div>
                                <h6 class="mb-0">{{ $coupon->name }}</h6>
                                @if($coupon->description)
                                    <small class="text-muted">{{ Str::limit($coupon->description, 50) }}</small>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($coupon->type === 'percentage')
                                <span class="badge badge-info">
                                    <i class="bi bi-percent me-1"></i>
                                    نسبة مئوية
                                </span>
                            @else
                                <span class="badge badge-success">
                                    <i class="bi bi-currency-dollar me-1"></i>
                                    مبلغ ثابت
                                </span>
                            @endif
                        </td>
                        <td>
                            <div>
                                <span class="fw-bold">{{ $coupon->value }}</span>
                                @if($coupon->type === 'percentage')
                                    <small class="text-muted">%</small>
                                @else
                                    <small class="text-muted">$</small>
                                @endif
                                @if($coupon->maximum_discount)
                                    <br><small class="text-muted">حد أقصى: {{ $coupon->maximum_discount }}$</small>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($coupon->minimum_amount)
                                <span class="text-muted">{{ $coupon->minimum_amount }}$</span>
                            @else
                                <span class="text-muted">لا يوجد</span>
                            @endif
                        </td>
                        <td>
                            <div>
                                <span class="fw-bold">{{ $coupon->used_count }}</span>
                                @if($coupon->usage_limit)
                                    <small class="text-muted">/ {{ $coupon->usage_limit }}</small>
                                @endif
                                <br>
                                <small class="text-muted">{{ $coupon->usages_count }} استخدام</small>
                            </div>
                        </td>
                        <td>
                            @if($coupon->expires_at)
                                <span class="text-muted">{{ $coupon->expires_at->format('Y-m-d') }}</span>
                                <br>
                                <small class="text-muted">{{ $coupon->expires_at->diffForHumans() }}</small>
                            @else
                                <span class="text-muted">لا ينتهي</span>
                            @endif
                        </td>
                        <td>
                            @if($coupon->is_active)
                                @if($coupon->isValid())
                                    <span class="badge badge-success">نشط</span>
                                @else
                                    <span class="badge badge-warning">منتهي</span>
                                @endif
                            @else
                                <span class="badge badge-secondary">معطل</span>
                            @endif
                            @if($coupon->first_time_only)
                                <br><small class="text-info">للمرة الأولى فقط</small>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('dashboard.coupons.show', $coupon) }}" class="btn btn-sm btn-outline-primary" title="عرض التفاصيل">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('dashboard.coupons.edit', $coupon) }}" class="btn btn-sm btn-outline-secondary" title="تعديل">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                {{-- <form method="POST" action="{{ route('dashboard.coupons.duplicate', $coupon) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-info" title="نسخ">
                                        <i class="bi bi-copy"></i>
                                    </button>
                                </form> --}}
                                {{-- <form method="POST" action="{{ route('dashboard.coupons.toggle-status', $coupon) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-{{ $coupon->is_active ? 'warning' : 'success' }}" title="{{ $coupon->is_active ? 'تعطيل' : 'تفعيل' }}">
                                        <i class="bi bi-{{ $coupon->is_active ? 'pause' : 'play' }}"></i>
                                    </button>
                                </form> --}}
                                @if($coupon->usages_count == 0)
                                    <form method="POST" action="{{ route('dashboard.coupons.destroy', $coupon) }}" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الكوبون؟')">
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
                        <td colspan="9" class="text-center py-4">
                            <div class="text-muted">
                                <i class="bi bi-ticket-perforated fs-1 d-block mb-3"></i>
                                <h5>لا توجد كوبونات</h5>
                                <p>لم يتم العثور على أي كوبونات مطابقة للبحث.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($coupons->hasPages())
    <div class="card-footer">
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted">
                عرض {{ $coupons->firstItem() }} إلى {{ $coupons->lastItem() }} من {{ $coupons->total() }} كوبون
            </div>
            <div>
                {{ $coupons->links() }}
            </div>
        </div>
    </div>
    @endif
</div>

@push('styles')
<style>
.font-monospace {
    font-family: 'Courier New', monospace;
}

.badge {
    font-size: 0.75rem;
}
</style>
@endpush
@endsection
