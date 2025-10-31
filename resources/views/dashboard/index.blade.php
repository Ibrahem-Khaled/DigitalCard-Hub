@extends('layouts.dashboard-new')

@section('title', 'لوحة التحكم - متجر البطاقات الرقمية')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="page-title">مرحباً بك، {{ auth()->user()->first_name ?? 'المستخدم' }}!</h3>
            <p class="page-subtitle">إليك نظرة عامة على أداء متجرك</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('dashboard.products.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>
                إضافة منتج جديد
            </a>
            <a href="{{ route('dashboard.orders.create') }}" class="btn btn-outline-primary">
                <i class="bi bi-cart-plus me-2"></i>
                إنشاء طلب جديد
            </a>
        </div>
    </div>
</div>

<!-- إحصائيات المبيعات الرئيسية -->
<div class="stats-grid">
    <x-dashboard.stats-card
        title="إجمالي المبيعات"
        :value="number_format($stats['total_sales'], 2) . ' $'"
        icon="bi-currency-dollar"
        change-type="positive"
        change-text="إجمالي الإيرادات" />

    <x-dashboard.stats-card
        title="الطلبات الجديدة اليوم"
        :value="number_format($stats['new_orders'])"
        icon="bi-cart-check"
        change-type="positive"
        change-text="طلبات اليوم" />

    <x-dashboard.stats-card
        title="المنتجات النشطة"
        :value="number_format($stats['products_count'])"
        icon="bi-box-seam"
        change-type="neutral"
        change-text="منتجات متاحة" />

    <x-dashboard.stats-card
        title="العملاء النشطين"
        :value="number_format($stats['customers_count'])"
        icon="bi-people"
        change-type="positive"
        change-text="عملاء نشطين" />

    <x-dashboard.stats-card
        title="إجمالي الطلبات"
        :value="number_format($stats['total_orders'])"
        icon="bi-receipt"
        change-type="info"
        change-text="جميع الطلبات" />

    <x-dashboard.stats-card
        title="طلبات في الانتظار"
        :value="number_format($stats['pending_orders'])"
        icon="bi-clock"
        change-type="warning"
        change-text="تحتاج مراجعة" />

    <x-dashboard.stats-card
        title="طلبات مدفوعة"
        :value="number_format($stats['paid_orders'])"
        icon="bi-check-circle"
        change-type="success"
        change-text="مدفوعة" />

    <x-dashboard.stats-card
        title="متوسط قيمة الطلب"
        :value="number_format($stats['average_order_value'], 2) . ' $'"
        icon="bi-graph-up"
        change-type="info"
        change-text="متوسط الطلب" />
</div>

<!-- إحصائيات إضافية -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-wallet2 text-primary fs-1"></i>
                <h5 class="mt-2">{{ number_format($stats['digital_cards_count']) }}</h5>
                <p class="text-muted mb-0">البطاقات الرقمية</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-percent text-success fs-1"></i>
                <h5 class="mt-2">{{ number_format($stats['active_coupons']) }}</h5>
                <p class="text-muted mb-0">كوبونات نشطة</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-gem text-warning fs-1"></i>
                <h5 class="mt-2">{{ number_format($stats['loyalty_points_total']) }}</h5>
                <p class="text-muted mb-0">نقاط الولاء</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-bell text-info fs-1"></i>
                <h5 class="mt-2">{{ number_format($stats['unread_notifications']) }}</h5>
                <p class="text-muted mb-0">إشعارات غير مقروءة</p>
            </div>
        </div>
    </div>
</div>

<!-- Charts and Tables Row -->
<div class="row">
    <!-- Recent Orders -->
    <div class="col-xl-8 mb-4">
        <x-dashboard.data-table
            title="الطلبات الأخيرة"
            icon="bi-clock-history"
            :headers="['رقم الطلب', 'العميل', 'المبلغ', 'الحالة', 'التاريخ', 'الإجراءات']"
            :data="$recentOrdersData"
            :actions="[['text' => 'عرض جميع الطلبات', 'url' => route('dashboard.orders.index'), 'icon' => 'bi-eye']]" />
    </div>

    <!-- Quick Actions -->
    <div class="col-xl-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-lightning me-2"></i>
                    الإجراءات السريعة
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-3">
                    <a href="{{ route('dashboard.products.create') }}" class="btn btn-primary btn-lg">
                        <i class="bi bi-plus-circle me-2"></i>
                        إضافة منتج جديد
                    </a>
                    <a href="{{ route('dashboard.orders.create') }}" class="btn btn-outline-primary btn-lg">
                        <i class="bi bi-cart-plus me-2"></i>
                        إنشاء طلب جديد
                    </a>
                    <a href="{{ route('dashboard.users.create') }}" class="btn btn-outline-primary btn-lg">
                        <i class="bi bi-person-plus me-2"></i>
                        إضافة عميل جديد
                    </a>
                    <a href="{{ route('dashboard.reports') }}" class="btn btn-outline-primary btn-lg">
                        <i class="bi bi-graph-up me-2"></i>
                        عرض التقارير
                    </a>
                </div>
            </div>
        </div>

        <!-- Top Products -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-star me-2"></i>
                    المنتجات الأكثر مبيعاً
                </h5>
            </div>
            <div class="card-body">
                @if($top_products->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($top_products as $product)
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <h6 class="mb-1">{{ $product['name'] }}</h6>
                                <small class="text-muted">{{ number_format($product['price'], 2) }} $</small>
                            </div>
                            <span class="badge badge-primary">{{ $product['sales'] }} مبيعة</span>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-3">
                        <i class="bi bi-box-seam text-muted fs-1"></i>
                        <p class="text-muted mt-2">لا توجد مبيعات بعد</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- إحصائيات المبيعات الشهرية -->
@if($monthly_sales->count() > 0)
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-graph-up me-2"></i>
                    إحصائيات المبيعات الشهرية
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($monthly_sales as $month)
                    <div class="col-md-2 mb-3">
                        <div class="text-center">
                            <h6 class="text-muted">{{ $month->month }}</h6>
                            <h4 class="text-primary">{{ number_format($month->revenue, 2) }}</h4>
                            <small class="text-muted">{{ $month->orders_count }} طلب</small>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Recent Activity -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-activity me-2"></i>
                    النشاط الأخير
                </h5>
            </div>
            <div class="card-body">
                @if($recent_activity->count() > 0)
                    <div class="timeline">
                        @foreach($recent_activity as $activity)
                        <div class="timeline-item d-flex mb-3">
                            <div class="timeline-marker bg-{{ $activity['color'] }} rounded-circle me-3" style="width: 12px; height: 12px; margin-top: 6px;"></div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <strong>{{ $activity['title'] }}</strong>
                                        <p class="text-muted mb-0">{{ $activity['description'] }}</p>
                                        @if(isset($activity['amount']))
                                            <small class="text-success">{{ number_format($activity['amount'], 2) }} {{ $activity['currency'] ?? '$' }}</small>
                                        @endif
                                    </div>
                                    <small class="text-muted">{{ $activity['time'] }}</small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-activity text-muted fs-1"></i>
                        <p class="text-muted mt-2">لا يوجد نشاط حديث</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.timeline {
    position: relative;
}

.timeline::before {
    content: '';
    position: absolute;
    right: 6px;
    top: 0;
    bottom: 0;
    width: 2px;
    background-color: var(--border-color);
}

.timeline-item {
    position: relative;
}

.timeline-marker {
    position: relative;
    z-index: 1;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}
</style>
@endpush
@endsection
