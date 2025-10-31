@extends('layouts.dashboard-new')

@section('title', 'لوحة التحكم - ' . config('app.name'))

@section('page-title', 'لوحة التحكم')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3 class="mb-1">مرحباً بك، {{ auth()->user()->full_name ?? 'المستخدم' }}!</h3>
                <p class="text-muted mb-0">إليك نظرة عامة على أداء متجرك</p>
            </div>
            <div>
                <button class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>
                    إضافة منتج جديد
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stats-number">{{ number_format($stats['total_sales'] ?? 0) }}</div>
                        <div class="stats-label">إجمالي المبيعات</div>
                    </div>
                    <div class="text-primary">
                        <i class="bi bi-currency-dollar fs-1"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="badge badge-success">
                        <i class="bi bi-arrow-up me-1"></i>
                        +12.5%
                    </span>
                    <small class="text-muted ms-2">من الشهر الماضي</small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stats-number">{{ number_format($stats['new_orders'] ?? 0) }}</div>
                        <div class="stats-label">الطلبات الجديدة</div>
                    </div>
                    <div class="text-primary">
                        <i class="bi bi-cart-check fs-1"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="badge badge-success">
                        <i class="bi bi-arrow-up me-1"></i>
                        +8.2%
                    </span>
                    <small class="text-muted ms-2">من الشهر الماضي</small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stats-number">{{ number_format($stats['products_count'] ?? 0) }}</div>
                        <div class="stats-label">المنتجات</div>
                    </div>
                    <div class="text-primary">
                        <i class="bi bi-box-seam fs-1"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="badge badge-info">
                        <i class="bi bi-plus me-1"></i>
                        +3 منتجات جديدة
                    </span>
                    <small class="text-muted ms-2">هذا الشهر</small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stats-number">{{ number_format($stats['customers_count'] ?? 0) }}</div>
                        <div class="stats-label">العملاء</div>
                    </div>
                    <div class="text-primary">
                        <i class="bi bi-people fs-1"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="badge badge-success">
                        <i class="bi bi-arrow-up me-1"></i>
                        +15.3%
                    </span>
                    <small class="text-muted ms-2">من الشهر الماضي</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts and Tables Row -->
<div class="row">
    <!-- Recent Orders -->
    <div class="col-xl-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-clock-history me-2"></i>
                    الطلبات الأخيرة
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>رقم الطلب</th>
                                <th>العميل</th>
                                <th>المبلغ</th>
                                <th>الحالة</th>
                                <th>التاريخ</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(($recent_orders ?? []) as $order)
                            <tr>
                                <td><strong>#{{ $order['id'] }}</strong></td>
                                <td>{{ $order['customer'] }}</td>
                                <td>{{ number_format($order['amount'], 2) }} $</td>
                                <td>
                                    @if($order['status'] == 'completed')
                                        <span class="badge badge-success">مكتمل</span>
                                    @elseif($order['status'] == 'pending')
                                        <span class="badge badge-warning">قيد المراجعة</span>
                                    @elseif($order['status'] == 'cancelled')
                                        <span class="badge badge-danger">ملغي</span>
                                    @else
                                        <span class="badge badge-info">{{ $order['status'] }}</span>
                                    @endif
                                </td>
                                <td>{{ $order['date'] }}</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="text-center mt-3">
                    <a href="#" class="btn btn-outline-primary">عرض جميع الطلبات</a>
                </div>
            </div>
        </div>
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
                    <button class="btn btn-primary btn-lg">
                        <i class="bi bi-plus-circle me-2"></i>
                        إضافة منتج جديد
                    </button>
                    <button class="btn btn-outline-primary btn-lg">
                        <i class="bi bi-cart-plus me-2"></i>
                        إنشاء طلب جديد
                    </button>
                    <button class="btn btn-outline-primary btn-lg">
                        <i class="bi bi-person-plus me-2"></i>
                        إضافة عميل جديد
                    </button>
                    <button class="btn btn-outline-primary btn-lg">
                        <i class="bi bi-graph-up me-2"></i>
                        عرض التقارير
                    </button>
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
                <div class="list-group list-group-flush">
                    @foreach(($top_products ?? []) as $product)
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <div>
                            <h6 class="mb-1">{{ $product['name'] }}</h6>
                            <small class="text-muted">{{ number_format($product['price']) }} $</small>
                        </div>
                        <span class="badge badge-primary">{{ $product['sales'] }} مبيعة</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

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
                <div class="timeline">
                    <div class="timeline-item d-flex mb-3">
                        <div class="timeline-marker bg-primary rounded-circle me-3" style="width: 12px; height: 12px; margin-top: 6px;"></div>
                        <div class="timeline-content">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong>طلب جديد #1234</strong>
                                    <p class="text-muted mb-0">تم إنشاء طلب جديد من العميل أحمد محمد</p>
                                </div>
                                <small class="text-muted">منذ 5 دقائق</small>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-item d-flex mb-3">
                        <div class="timeline-marker bg-success rounded-circle me-3" style="width: 12px; height: 12px; margin-top: 6px;"></div>
                        <div class="timeline-content">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong>دفعة جديدة</strong>
                                    <p class="text-muted mb-0">تم استلام دفعة بقيمة 150.00 $</p>
                                </div>
                                <small class="text-muted">منذ 15 دقيقة</small>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-item d-flex mb-3">
                        <div class="timeline-marker bg-info rounded-circle me-3" style="width: 12px; height: 12px; margin-top: 6px;"></div>
                        <div class="timeline-content">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong>منتج جديد</strong>
                                    <p class="text-muted mb-0">تم إضافة منتج جديد: بطاقة هدايا نتفليكس</p>
                                </div>
                                <small class="text-muted">منذ ساعة</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

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
</style>
@endpush
