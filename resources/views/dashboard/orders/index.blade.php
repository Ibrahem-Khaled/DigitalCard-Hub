@extends('layouts.dashboard-new')

@section('title', 'إدارة الطلبات - متجر البطاقات الرقمية')

@section('page-title', 'إدارة الطلبات')
@section('page-subtitle', 'عرض وإدارة جميع الطلبات')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="page-title">إدارة الطلبات</h3>
            <p class="page-subtitle">عرض وإدارة جميع الطلبات</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('dashboard.orders.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-2"></i>
                إضافة طلب جديد
            </a>
            <a href="{{ route('dashboard.orders.export', request()->query()) }}" class="btn btn-outline-success">
                <i class="bi bi-download me-2"></i>
                تصدير
            </a>
        </div>
    </div>
</div>

<!-- إحصائيات الطلبات -->
<div class="stats-grid">
    <x-dashboard.stats-card title="إجمالي الطلبات" :value="number_format($stats['total_orders'])" icon="bi-cart" change-type="positive" change-text="جميع الطلبات" />
    <x-dashboard.stats-card title="طلبات في الانتظار" :value="number_format($stats['pending_orders'])" icon="bi-clock" change-type="warning" change-text="في الانتظار" />
    <x-dashboard.stats-card title="طلبات قيد المعالجة" :value="number_format($stats['processing_orders'])" icon="bi-gear" change-type="info" change-text="قيد المعالجة" />
    <x-dashboard.stats-card title="طلبات تم تسليمها" :value="number_format($stats['delivered_orders'])" icon="bi-check-circle" change-type="success" change-text="تم التسليم" />
    <x-dashboard.stats-card title="طلبات ملغية" :value="number_format($stats['cancelled_orders'])" icon="bi-x-circle" change-type="danger" change-text="ملغية" />
    <x-dashboard.stats-card title="طلبات مدفوعة" :value="number_format($stats['paid_orders'])" icon="bi-credit-card" change-type="success" change-text="مدفوعة" />
    <x-dashboard.stats-card title="مدفوعات في الانتظار" :value="number_format($stats['pending_payments'])" icon="bi-hourglass" change-type="warning" change-text="في الانتظار" />
    <x-dashboard.stats-card title="إجمالي الإيرادات" :value="number_format($stats['total_revenue'], 2) . ' $'" icon="bi-currency-dollar" change-type="success" change-text="إيرادات" />
    <x-dashboard.stats-card title="متوسط قيمة الطلب" :value="number_format($stats['average_order_value'], 2) . ' $'" icon="bi-graph-up" change-type="info" change-text="متوسط" />
</div>

<!-- فلاتر البحث -->
<x-dashboard.filters
    :filters="[
        ['name' => 'user_id', 'label' => 'المستخدم', 'type' => 'select', 'placeholder' => 'جميع المستخدمين', 'options' => $users->pluck('first_name', 'id')->toArray()],
        ['name' => 'status', 'label' => 'حالة الطلب', 'type' => 'select', 'placeholder' => 'جميع الحالات', 'options' => $statuses],
        ['name' => 'payment_status', 'label' => 'حالة الدفع', 'type' => 'select', 'placeholder' => 'جميع حالات الدفع', 'options' => $paymentStatuses],
        ['name' => 'payment_method', 'label' => 'طريقة الدفع', 'type' => 'select', 'placeholder' => 'جميع الطرق', 'options' => $paymentMethods],
        ['name' => 'date_from', 'label' => 'من تاريخ', 'type' => 'date', 'placeholder' => 'تاريخ البداية'],
        ['name' => 'date_to', 'label' => 'إلى تاريخ', 'type' => 'date', 'placeholder' => 'تاريخ النهاية'],
        ['name' => 'amount_from', 'label' => 'من مبلغ', 'type' => 'number', 'placeholder' => 'الحد الأدنى'],
        ['name' => 'amount_to', 'label' => 'إلى مبلغ', 'type' => 'number', 'placeholder' => 'الحد الأقصى'],
        ['name' => 'sort_by', 'label' => 'ترتيب حسب', 'type' => 'select', 'placeholder' => 'ترتيب حسب', 'options' => ['created_at' => 'تاريخ الإنشاء', 'total_amount' => 'المبلغ الإجمالي', 'order_number' => 'رقم الطلب']],
        ['name' => 'sort_order', 'label' => 'اتجاه الترتيب', 'type' => 'select', 'placeholder' => 'اتجاه الترتيب', 'options' => ['desc' => 'تنازلي', 'asc' => 'تصاعدي']]
    ]"
    search-placeholder="البحث في الطلبات..."
    :search-value="request('search')"
    :action-url="route('dashboard.orders.index')" />

<!-- جدول الطلبات -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="bi bi-list-ul me-2"></i>
            قائمة الطلبات
        </h5>
    </div>
    <div class="card-body">
        @if($orders->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>رقم الطلب</th>
                            <th>المستخدم</th>
                            <th>الحالة</th>
                            <th>حالة الدفع</th>
                            <th>المبلغ الإجمالي</th>
                            <th>طريقة الدفع</th>
                            <th>تاريخ الإنشاء</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="me-2">
                                        <i class="bi bi-receipt text-primary"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $order->order_number }}</div>
                                        @if($order->coupon_code)
                                            <small class="text-muted">كوبون: {{ $order->coupon_code }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm me-2">
                                        @if($order->user->avatar)
                                            <img src="{{ asset('storage/' . $order->user->avatar) }}" alt="Avatar" class="avatar-img">
                                        @else
                                            <div class="avatar-placeholder">
                                                <i class="bi bi-person"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $order->user->first_name }} {{ $order->user->last_name }}</div>
                                        <small class="text-muted">{{ $order->user->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-{{ $order->getStatusColor() }}">
                                    {{ $order->getStatusInArabic() }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-{{ $order->getPaymentStatusColor() }}">
                                    {{ $order->getPaymentStatusInArabic() }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold">{{ number_format($order->total_amount, 2) }} {{ $order->currency }}</div>
                                @if($order->discount_amount > 0)
                                    <small class="text-success">خصم: {{ number_format($order->discount_amount, 2) }}</small>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-credit-card me-1"></i>
                                    <span>{{ $order->getPaymentMethodInArabic() }}</span>
                                </div>
                            </td>
                            <td>
                                <div>{{ $order->created_at->format('Y-m-d') }}</div>
                                <small class="text-muted">{{ $order->created_at->format('H:i:s') }}</small>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('dashboard.orders.show', $order) }}">
                                                <i class="bi bi-eye me-2"></i>
                                                عرض التفاصيل
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('dashboard.orders.edit', $order) }}">
                                                <i class="bi bi-pencil me-2"></i>
                                                تعديل
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('dashboard.orders.destroy', $order) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger"
                                                        onclick="return confirm('هل أنت متأكد من حذف هذا الطلب؟')">
                                                    <i class="bi bi-trash me-2"></i>
                                                    حذف
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $orders->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <div class="mb-3">
                    <i class="bi bi-cart-x display-1 text-muted"></i>
                </div>
                <h5 class="text-muted">لا توجد طلبات</h5>
                <p class="text-muted">لم يتم العثور على أي طلبات تطابق معايير البحث.</p>
                <a href="{{ route('dashboard.orders.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-2"></i>
                    إضافة طلب جديد
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
.avatar-sm {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    overflow: hidden;
}

.avatar-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.avatar-placeholder {
    width: 100%;
    height: 100%;
    background-color: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}
</style>
@endpush
