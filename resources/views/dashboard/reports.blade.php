@extends('layouts.dashboard-new')

@section('title', 'التقارير والإحصائيات - متجر البطاقات الرقمية')

@section('page-title', 'التقارير والإحصائيات')
@section('page-subtitle', 'تحليلات متقدمة لأداء المتجر')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h3 class="page-title">التقارير والإحصائيات</h3>
            <p class="page-subtitle mb-0">تابع أداء البطاقات الرقمية، العملاء، والمبيعات مع إمكانيات فلترة متقدمة</p>
        </div>
        <div class="page-actions d-flex gap-2">
            <a href="{{ route('dashboard.reports.export', $export_params) }}" class="btn btn-success">
                <i class="bi bi-download me-2"></i>
                تصدير النتائج الحالية
            </a>
            <a href="{{ route('dashboard.reports') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-clockwise me-2"></i>
                إعادة التعيين
            </a>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0"><i class="bi bi-sliders me-2"></i>معايير الفلترة</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('dashboard.reports') }}">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="date_from" class="form-label">من تاريخ</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" value="{{ $dateFrom }}">
                </div>
                <div class="col-md-3">
                    <label for="date_to" class="form-label">إلى تاريخ</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" value="{{ $dateTo }}">
                </div>
                <div class="col-md-3">
                    <label for="category_id" class="form-label">الفئة</label>
                    <select id="category_id" name="category_id" class="form-select">
                        <option value="">جميع الفئات</option>
                        @foreach($filters_options['categories'] as $id => $name)
                            <option value="{{ $id }}" {{ $filters['category_id'] == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="card_provider" class="form-label">مزود البطاقة</label>
                    <select id="card_provider" name="card_provider" class="form-select">
                        <option value="">جميع المزودين</option>
                        @foreach($filters_options['providers'] as $provider)
                            <option value="{{ $provider }}" {{ $filters['card_provider'] === $provider ? 'selected' : '' }}>{{ $provider }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="card_type" class="form-label">نوع البطاقة</label>
                    <select id="card_type" name="card_type" class="form-select">
                        <option value="">جميع الأنواع</option>
                        @foreach($filters_options['types'] as $type)
                            <option value="{{ $type }}" {{ $filters['card_type'] === $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="card_region" class="form-label">المنطقة</label>
                    <select id="card_region" name="card_region" class="form-select">
                        <option value="">جميع المناطق</option>
                        @foreach($filters_options['regions'] as $region)
                            <option value="{{ $region }}" {{ $filters['card_region'] === $region ? 'selected' : '' }}>{{ $region }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="payment_method" class="form-label">طريقة الدفع</label>
                    <select id="payment_method" name="payment_method" class="form-select">
                        <option value="">جميع الطرق</option>
                        @foreach($filters_options['payment_methods'] as $value => $label)
                            <option value="{{ $value }}" {{ $filters['payment_method'] === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-filter-circle me-2"></i>
                        تطبيق الفلاتر
                    </button>
                </div>
            </div>
        </form>

        @if($active_filters->isNotEmpty())
        <div class="active-filters mt-3">
            <span class="me-2 text-muted">الفلاتر المفعلة:</span>
            @foreach($active_filters as $chip)
                <span class="badge bg-light text-dark border">{{ $chip }}</span>
            @endforeach
        </div>
        @endif
    </div>
</div>

<div class="stats-grid mb-4">
    <x-dashboard.stats-card title="إجمالي الطلبات" :value="number_format($period_stats['total_orders'])" icon="bi-receipt" change-type="info" change-text="عدد الطلبات المستوفية للشروط" />
    <x-dashboard.stats-card title="إجمالي الإيرادات" :value="number_format($period_stats['total_revenue'], 2) . ' $'" icon="bi-currency-dollar" change-type="success" change-text="قيمة الطلبات المدفوعة" />
    <x-dashboard.stats-card title="متوسط قيمة الطلب" :value="number_format($period_stats['average_order_value'] ?? 0, 2) . ' $'" icon="bi-graph-up" change-type="info" change-text="إيراد لكل طلب" />
    <x-dashboard.stats-card title="عدد المنتجات المباعة" :value="number_format($period_stats['total_products_sold'])" icon="bi-bag-check" change-type="neutral" change-text="إجمالي الوحدات" />
    <x-dashboard.stats-card title="العملاء الفريدون" :value="number_format($period_stats['unique_customers'])" icon="bi-people" change-type="positive" change-text="عدد العملاء المختلفين" />
    <x-dashboard.stats-card title="متوسط عناصر الطلب" :value="number_format($period_stats['average_items_per_order'] ?? 0, 2)" icon="bi-list-ol" change-type="info" change-text="متوسط عدد العناصر" />
</div>

<div class="row mb-4 g-3">
    <div class="col-xl-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="bi bi-lightning-charge me-2"></i>أبرز الملاحظات</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0 insights-list">
                    <li>
                        <span class="label">أفضل يوم للمبيعات</span>
                        <span class="value">@if($top_insights['best_day']) {{ $top_insights['best_day']->date_label }} - {{ number_format($top_insights['best_day']->total_revenue, 2) }} $ @else <span class="text-muted">لا يوجد بيانات</span> @endif</span>
                    </li>
                    <li>
                        <span class="label">أعلى فئة</span>
                        <span class="value">@if($top_insights['top_category']) {{ $top_insights['top_category']->name }} - {{ number_format($top_insights['top_category']->total_revenue, 2) }} $ @else <span class="text-muted">لا يوجد بيانات</span> @endif</span>
                    </li>
                    <li>
                        <span class="label">أقوى مزود</span>
                        <span class="value">@if($top_insights['top_provider']) {{ $top_insights['top_provider']->card_provider }} - {{ number_format($top_insights['top_provider']->total_revenue, 2) }} $ @else <span class="text-muted">لا يوجد بيانات</span> @endif</span>
                    </li>
                    <li>
                        <span class="label">أنشط منطقة</span>
                        <span class="value">@if($top_insights['top_region']) {{ $top_insights['top_region']->card_region }} - {{ number_format($top_insights['top_region']->total_revenue, 2) }} $ @else <span class="text-muted">لا يوجد بيانات</span> @endif</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-xl-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><i class="bi bi-activity me-2"></i>نظرة على المبيعات اليومية</h5>
                <span class="text-muted small">{{ $dateFrom }} - {{ $dateTo }}</span>
            </div>
            <div class="card-body">
                <canvas id="dailySalesChart" height="160"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4 g-3">
    <div class="col-xl-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="bi bi-pie-chart me-2"></i>توزيع حالات الطلبات</h5>
            </div>
            <div class="card-body">
                <canvas id="ordersStatusChart" height="220"></canvas>
                @if(collect($order_status_chart['data'])->sum() === 0)
                    <div class="text-center text-muted mt-3">لا توجد طلبات مستوفية لعرض التوزيع.</div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-xl-8">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="bi bi-calendar-week me-2"></i>تفاصيل المبيعات اليومية</h5>
            </div>
            <div class="card-body">
                @if($sales_report->count())
                <div class="table-responsive">
                    <table class="table table-striped align-middle mb-0">
                        <thead>
                            <tr>
                                <th>التاريخ</th>
                                <th>عدد الطلبات</th>
                                <th>عدد العناصر</th>
                                <th>الإيراد</th>
                                <th>متوسط الطلب</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sales_report as $row)
                            <tr>
                                <td>{{ $row->date_label }}</td>
                                <td><span class="badge bg-primary">{{ $row->orders_count }}</span></td>
                                <td>{{ number_format($row->items_count) }}</td>
                                <td>{{ number_format($row->total_revenue, 2) }} $</td>
                                <td>{{ number_format($row->average_order_value, 2) }} $</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center text-muted py-4">لا توجد بيانات لعرضها في الفترة المحددة.</div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row mb-4 g-3">
    <div class="col-xl-6">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="bi bi-stars me-2"></i>أفضل المنتجات أداءً</h5>
            </div>
            <div class="card-body">
                @if($products_report->count())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>المنتج</th>
                                <th>المزود</th>
                                <th>النوع</th>
                                <th>الكمية</th>
                                <th>الإيراد</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products_report->take(15) as $product)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="product-avatar me-2">
                                            @if($product->image)
                                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                                            @else
                                                <span class="placeholder">{{ mb_substr($product->name, 0, 1) }}</span>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $product->name }}</div>
                                            <small class="text-muted">SKU: {{ $product->sku }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $product->card_provider ?? '—' }}</td>
                                <td>{{ $product->card_type ?? '—' }}</td>
                                <td>{{ number_format($product->total_quantity) }}</td>
                                <td>{{ number_format($product->total_revenue, 2) }} $</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center text-muted py-4">لا توجد منتجات مطابقة للفلاتر الحالية.</div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-xl-6">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="bi bi-people me-2"></i>العملاء الأكثر إنفاقاً</h5>
            </div>
            <div class="card-body">
                @if($customers_report->count())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>العميل</th>
                                <th>الطلبات</th>
                                <th>العناصر</th>
                                <th>إجمالي الإنفاق</th>
                                <th>آخر طلب</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customers_report->take(15) as $customer)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm rounded-circle bg-light me-2 overflow-hidden">
                                            @if($customer->avatar)
                                                <img src="{{ asset('storage/' . $customer->avatar) }}" alt="{{ $customer->first_name }}" class="w-100 h-100 object-fit-cover">
                                            @else
                                                <span class="placeholder">{{ mb_substr($customer->first_name, 0, 1) }}</span>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $customer->first_name }} {{ $customer->last_name }}</div>
                                            <small class="text-muted">{{ $customer->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ number_format($customer->orders_count) }}</td>
                                <td>{{ number_format($customer->total_items) }}</td>
                                <td>{{ number_format($customer->total_spent, 2) }} $</td>
                                <td>{{ $customer->last_order_at ? \Carbon\Carbon::parse($customer->last_order_at)->format('Y-m-d') : '—' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center text-muted py-4">لا توجد مشتريات ضمن نطاق الفلاتر المحدد.</div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-xl-3 col-md-6">
        <div class="card breakdown-card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="bi bi-grid me-2"></i>الفئات</h5>
            </div>
            <div class="card-body">
                @if($category_breakdown->count())
                    <ul class="list-unstyled mb-0">
                        @foreach($category_breakdown as $category)
                        <li>
                            <span>{{ $category->name }}</span>
                            <strong>{{ number_format($category->total_revenue, 2) }} $</strong>
                            <small>{{ number_format($category->total_items) }} بطاقة</small>
                        </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-center text-muted">لا توجد بيانات</div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card breakdown-card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="bi bi-building me-2"></i>المزودون</h5>
            </div>
            <div class="card-body">
                @if($provider_breakdown->count())
                    <ul class="list-unstyled mb-0">
                        @foreach($provider_breakdown as $provider)
                        <li>
                            <span>{{ $provider->card_provider }}</span>
                            <strong>{{ number_format($provider->total_revenue, 2) }} $</strong>
                            <small>{{ number_format($provider->total_items) }} بطاقة</small>
                        </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-center text-muted">لا توجد بيانات</div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card breakdown-card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="bi bi-geo-alt me-2"></i>المناطق</h5>
            </div>
            <div class="card-body">
                @if($region_breakdown->count())
                    <ul class="list-unstyled mb-0">
                        @foreach($region_breakdown as $region)
                        <li>
                            <span>{{ $region->card_region }}</span>
                            <strong>{{ number_format($region->total_revenue, 2) }} $</strong>
                            <small>{{ number_format($region->total_items) }} بطاقة</small>
                        </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-center text-muted">لا توجد بيانات</div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card breakdown-card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="bi bi-card-heading me-2"></i>أنواع البطاقات</h5>
            </div>
            <div class="card-body">
                @if($type_breakdown->count())
                    <ul class="list-unstyled mb-0">
                        @foreach($type_breakdown as $type)
                        <li>
                            <span>{{ $type->card_type }}</span>
                            <strong>{{ number_format($type->total_revenue, 2) }} $</strong>
                            <small>{{ number_format($type->total_items) }} بطاقة</small>
                        </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-center text-muted">لا توجد بيانات</div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const salesChartData = @json($sales_chart_data);
const statusChartData = @json($order_status_chart);

const salesCtx = document.getElementById('dailySalesChart');
if (salesCtx) {
    new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: salesChartData.labels,
            datasets: [
                {
                    label: 'الإيرادات ($)',
                    data: salesChartData.revenue,
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    tension: 0.35,
                    fill: true,
                    pointRadius: 4,
                    pointHoverRadius: 6
                },
                {
                    label: 'عدد الطلبات',
                    data: salesChartData.orders,
                    borderColor: '#20c997',
                    backgroundColor: 'rgba(32, 201, 151, 0.1)',
                    tension: 0.35,
                    yAxisID: 'ordersAxis'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: value => value.toLocaleString() + ' $'
                    }
                },
                ordersAxis: {
                    position: 'right',
                    beginAtZero: true,
                    grid: { drawOnChartArea: false }
                }
            }
        }
    });
}

const statusCtx = document.getElementById('ordersStatusChart');
if (statusCtx && statusChartData.data.length) {
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: statusChartData.labels,
            datasets: [{
                data: statusChartData.data,
                backgroundColor: statusChartData.colors,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true
                    }
                }
            }
        }
    });
}
</script>
@endpush

@push('styles')
<style>
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 1rem;
}

.active-filters .badge {
    margin-inline-end: 0.5rem;
    padding: 0.45rem 0.75rem;
    border-radius: 1.5rem;
}

.insights-list li {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.6rem 0;
    border-bottom: 1px solid #f1f3f5;
}

.insights-list li:last-child {
    border-bottom: none;
}

.insights-list .label {
    color: #6c757d;
    font-size: 0.875rem;
}

.insights-list .value {
    font-weight: 600;
}

.product-avatar {
    width: 40px;
    height: 40px;
    border-radius: 0.75rem;
    background-color: #f1f3f5;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    font-weight: 600;
}

.product-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.product-avatar .placeholder,
.avatar-sm .placeholder {
    color: #6c757d;
}

.breakdown-card ul li {
    display: flex;
    flex-direction: column;
    gap: 0.15rem;
    padding: 0.5rem 0;
    border-bottom: 1px solid #f1f3f5;
}

.breakdown-card ul li:last-child {
    border-bottom: none;
}

.breakdown-card ul li span {
    font-weight: 500;
}

.breakdown-card ul li strong {
    color: #0d6efd;
}

.breakdown-card ul li small {
    color: #6c757d;
}

.avatar-sm {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
}

.object-fit-cover {
    object-fit: cover;
}

@media (max-width: 991.98px) {
    .page-actions {
        width: 100%;
        justify-content: flex-start;
    }
}
</style>
@endpush
@endsection
