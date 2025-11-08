@extends('layouts.dashboard-new')

@section('title', 'تفاصيل الطلب - ' . $order->order_number)

@section('page-title', 'تفاصيل الطلب')
@section('page-subtitle', $order->order_number)

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="page-title">تفاصيل الطلب</h3>
            <p class="page-subtitle">{{ $order->order_number }}</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('dashboard.orders.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-right me-2"></i>
                العودة للقائمة
            </a>
            <a href="{{ route('dashboard.orders.edit', $order) }}" class="btn btn-primary">
                <i class="bi bi-pencil me-2"></i>
                تعديل الطلب
            </a>
        </div>
    </div>
</div>

<div class="row">
    <!-- معلومات الطلب الأساسية -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    معلومات الطلب
                </h5>
            </div>
            <div class="card-body">
                <div class="info-item">
                    <label>رقم الطلب:</label>
                    <span class="font-monospace">{{ $order->order_number }}</span>
                </div>

                <div class="info-item">
                    <label>الحالة:</label>
                    <span class="badge badge-{{ $order->getStatusColor() }}">
                        {{ $order->getStatusInArabic() }}
                    </span>
                </div>

                <div class="info-item">
                    <label>حالة الدفع:</label>
                    <span class="badge badge-{{ $order->getPaymentStatusColor() }}">
                        {{ $order->getPaymentStatusInArabic() }}
                    </span>
                </div>

                <div class="info-item">
                    <label>طريقة الدفع:</label>
                    <span>{{ $order->getPaymentMethodInArabic() }}</span>
                </div>

                @if($order->payment_reference)
                <div class="info-item">
                    <label>مرجع الدفع:</label>
                    <span class="font-monospace">{{ $order->payment_reference }}</span>
                </div>
                @endif

                <div class="info-item">
                    <label>تاريخ الإنشاء:</label>
                    <span>{{ $order->created_at->format('Y-m-d H:i:s') }}</span>
                </div>

                @if($order->processed_at)
                <div class="info-item">
                    <label>تاريخ المعالجة:</label>
                    <span>{{ $order->processed_at->format('Y-m-d H:i:s') }}</span>
                </div>
                @endif

                @if($order->delivered_at)
                <div class="info-item">
                    <label>تاريخ التسليم:</label>
                    <span>{{ $order->delivered_at->format('Y-m-d H:i:s') }}</span>
                </div>
                @endif

                @if($order->notes)
                <div class="info-item">
                    <label>ملاحظات:</label>
                    <span>{{ $order->notes }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- معلومات العميل -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-person me-2"></i>
                    معلومات العميل
                </h5>
            </div>
            <div class="card-body">
                @if($order->user)
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-lg me-3">
                        @if($order->user->avatar)
                            <img src="{{ asset('storage/' . $order->user->avatar) }}" alt="Avatar" class="avatar-img">
                        @else
                            <div class="avatar-placeholder">
                                <i class="bi bi-person"></i>
                            </div>
                        @endif
                    </div>
                    <div>
                        <h6 class="mb-1">{{ $order->user->first_name }} {{ $order->user->last_name }}</h6>
                        <small class="text-muted">{{ $order->user->email }}</small>
                    </div>
                </div>

                @if($order->user->phone)
                <div class="info-item">
                    <label>الهاتف:</label>
                    <span>{{ $order->user->phone }}</span>
                </div>
                @endif

                @if($order->user->address)
                <div class="info-item">
                    <label>العنوان:</label>
                    <span>{{ $order->user->address }}</span>
                </div>
                @endif
                @else
                <!-- Guest Order - Show Billing Address Info -->
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-lg me-3">
                        <div class="avatar-placeholder">
                            <i class="bi bi-person"></i>
                        </div>
                    </div>
                    <div>
                        <h6 class="mb-1">{{ $order->billing_address['first_name'] ?? '' }} {{ $order->billing_address['last_name'] ?? '' }}</h6>
                        <small class="text-muted">{{ $order->billing_address['email'] ?? 'N/A' }}</small>
                        <span class="badge bg-secondary ms-2">عميل ضيف</span>
                    </div>
                </div>

                @if(isset($order->billing_address['phone']))
                <div class="info-item">
                    <label>الهاتف:</label>
                    <span>{{ $order->billing_address['phone'] }}</span>
                </div>
                @endif

                @if(isset($order->billing_address['address']))
                <div class="info-item">
                    <label>العنوان:</label>
                    <span>{{ $order->billing_address['address'] }}</span>
                </div>
                @endif
                @endif
            </div>
        </div>

        <!-- تحديث الحالة -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-gear me-2"></i>
                    تحديث الحالة
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('dashboard.orders.update-status', $order) }}" method="POST" class="mb-3">
                    @csrf
                    @method('PATCH')
                    <div class="mb-3">
                        <label for="status" class="form-label">حالة الطلب</label>
                        <select class="form-select" id="status" name="status">
                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>في الانتظار</option>
                            <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>قيد المعالجة</option>
                            <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>تم الشحن</option>
                            <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>تم التسليم</option>
                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>ملغي</option>
                            <option value="refunded" {{ $order->status === 'refunded' ? 'selected' : '' }}>مسترد</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100">تحديث الحالة</button>
                </form>

                <form action="{{ route('dashboard.orders.update-payment-status', $order) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="mb-3">
                        <label for="payment_status" class="form-label">حالة الدفع</label>
                        <select class="form-select" id="payment_status" name="payment_status">
                            <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>في الانتظار</option>
                            <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>مدفوع</option>
                            <option value="failed" {{ $order->payment_status === 'failed' ? 'selected' : '' }}>فشل</option>
                            <option value="refunded" {{ $order->payment_status === 'refunded' ? 'selected' : '' }}>مسترد</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success btn-sm w-100">تحديث حالة الدفع</button>
                </form>
            </div>
        </div>
    </div>

    <!-- تفاصيل الطلب -->
    <div class="col-lg-8">
        <!-- عناصر الطلب -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-list-ul me-2"></i>
                    عناصر الطلب
                </h5>
            </div>
            <div class="card-body">
                @foreach($order->orderItems as $item)
                <div class="order-item border-bottom pb-3 mb-3">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                @if($item->product->image)
                                <div class="product-image me-3">
                                    <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                </div>
                                @endif
                                <div>
                                    <h6 class="mb-1">{{ $item->product->name }}</h6>
                                    <small class="text-muted">SKU: {{ $item->product->sku }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="text-center">
                                <span class="fw-bold">{{ $item->quantity }}</span>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="text-center">
                                <span class="fw-bold">{{ number_format($item->price, 2) }} {{ $order->currency }}</span>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="text-center">
                                <span class="fw-bold text-primary">{{ number_format($item->total_price, 2) }} {{ $order->currency }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <span class="badge badge-{{ $item->getStatusColor() }}">
                                {{ $item->getStatusInArabic() }}
                            </span>
                            @if($item->digitalCards->count() > 0)
                                <span class="badge bg-success ms-2">
                                    <i class="bi bi-credit-card-2-front me-1"></i>
                                    {{ $item->digitalCards->count() }} بطاقة رقمية
                                </span>
                            @endif
                        </div>
                        <div class="col-md-6 text-end">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <form action="{{ route('dashboard.orders.update-item-status', $item) }}" method="POST" class="d-inline">
                                            @csrf
                                            <div class="px-3 py-2">
                                                <label class="form-label small">تحديث الحالة:</label>
                                                <select class="form-select form-select-sm" name="status">
                                                    <option value="pending" {{ $item->status === 'pending' ? 'selected' : '' }}>في الانتظار</option>
                                                    <option value="processing" {{ $item->status === 'processing' ? 'selected' : '' }}>قيد المعالجة</option>
                                                    <option value="delivered" {{ $item->status === 'delivered' ? 'selected' : '' }}>تم التسليم</option>
                                                    <option value="cancelled" {{ $item->status === 'cancelled' ? 'selected' : '' }}>ملغي</option>
                                                </select>
                                                <button type="submit" class="btn btn-primary btn-sm w-100 mt-2">تحديث</button>
                                            </div>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- البطاقات الرقمية -->
        @php
            $hasDigitalCards = $order->orderItems->some(function($item) {
                return $item->digitalCards->count() > 0;
            });
        @endphp

        @if($hasDigitalCards)
        <div class="card mt-3">
            <div class="card-header bg-gradient-primary">
                <h5 class="card-title mb-0 text-white">
                    <i class="bi bi-credit-card-2-front me-2"></i>
                    البطاقات الرقمية المرسلة
                </h5>
            </div>
            <div class="card-body">
                @foreach($order->orderItems as $item)
                    @if($item->digitalCards->count() > 0)
                        <div class="digital-cards-group mb-4">
                            <div class="d-flex align-items-center mb-3 p-3 bg-light rounded">
                                <i class="bi bi-box-seam text-primary fs-4 me-3"></i>
                                <div>
                                    <h6 class="mb-1 fw-bold">{{ $item->product->name }}</h6>
                                    <small class="text-muted">{{ $item->digitalCards->count() }} بطاقة</small>
                                </div>
                            </div>

                            <div class="row">
                                @foreach($item->digitalCards as $card)
                                <div class="col-md-6 mb-3">
                                    <div class="digital-card-item border rounded p-3 h-100 shadow-sm">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="badge bg-primary">
                                                <i class="bi bi-credit-card me-1"></i>
                                                بطاقة رقمية
                                            </span>
                                            <span class="badge bg-{{ $card->is_used ? 'warning' : 'success' }}">
                                                {{ $card->is_used ? 'مستخدمة' : 'متاحة' }}
                                            </span>
                                        </div>

                                        <div class="card-details mt-3">
                                            <div class="detail-row mb-2">
                                                <small class="text-muted d-block">كود البطاقة</small>
                                                <div class="text-muted">
                                                    <i class="bi bi-lock me-1"></i>
                                                    مخفي لأسباب أمنية
                                                </div>
                                                <small class="text-muted">سيتم إرسال الأكواد للعميل فقط</small>
                                            </div>

                                            <div class="detail-row mb-2">
                                                <small class="text-muted d-block">رقم PIN</small>
                                                <div class="text-muted">
                                                    <i class="bi bi-lock me-1"></i>
                                                    مخفي لأسباب أمنية
                                                </div>
                                            </div>

                                            <div class="detail-row mb-2">
                                                <small class="text-muted d-block">رقم البطاقة</small>
                                                <div class="text-muted">
                                                    <i class="bi bi-lock me-1"></i>
                                                    مخفي لأسباب أمنية
                                                </div>
                                            </div>

                                            @if($card->serial_number)
                                            <div class="detail-row mb-2">
                                                <small class="text-muted d-block">الرقم التسلسلي</small>
                                                <code class="fs-6 fw-bold text-dark">{{ $card->serial_number }}</code>
                                            </div>
                                            @endif

                                            @if($card->value)
                                            <div class="detail-row mb-2">
                                                <small class="text-muted d-block">القيمة</small>
                                                <span class="badge bg-success fs-6">
                                                    {{ number_format($card->value, 2) }} {{ $card->currency ?? 'USD' }}
                                                </span>
                                            </div>
                                            @endif

                                            @if($card->expiry_date)
                                            <div class="detail-row mb-2">
                                                <small class="text-muted d-block">تاريخ الانتهاء</small>
                                                <span class="text-danger">
                                                    <i class="bi bi-calendar-event me-1"></i>
                                                    {{ $card->expiry_date->format('Y-m-d') }}
                                                </span>
                                            </div>
                                            @endif

                                            @if($card->used_at)
                                            <div class="detail-row mb-2">
                                                <small class="text-muted d-block">تاريخ الاستخدام</small>
                                                <span class="text-info">
                                                    <i class="bi bi-clock me-1"></i>
                                                    {{ $card->used_at->format('Y-m-d H:i') }}
                                                </span>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @if(!$loop->last)
                        <hr class="my-4">
                        @endif
                    @endif
                @endforeach
            </div>
        </div>
        @endif

        <!-- ملخص المبالغ -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-calculator me-2"></i>
                    ملخص المبالغ
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item">
                            <label>المجموع الفرعي:</label>
                            <span>{{ number_format($order->subtotal, 2) }} {{ $order->currency }}</span>
                        </div>
                        @if($order->tax_amount > 0)
                        <div class="info-item">
                            <label>الضريبة:</label>
                            <span>{{ number_format($order->tax_amount, 2) }} {{ $order->currency }}</span>
                        </div>
                        @endif
                        @if($order->shipping_amount > 0)
                        <div class="info-item">
                            <label>رسوم الشحن:</label>
                            <span>{{ number_format($order->shipping_amount, 2) }} {{ $order->currency }}</span>
                        </div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        @if($order->discount_amount > 0)
                        <div class="info-item">
                            <label>الخصم:</label>
                            <span class="text-success">-{{ number_format($order->discount_amount, 2) }} {{ $order->currency }}</span>
                        </div>
                        @endif
                        <div class="info-item">
                            <label class="fw-bold">المبلغ الإجمالي:</label>
                            <span class="fw-bold text-primary fs-5">{{ number_format($order->total_amount, 2) }} {{ $order->currency }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- عناوين الشحن والفواتير -->
        <div class="row mt-3">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-truck me-2"></i>
                            عنوان الشحن
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($order->shipping_address)
                            @foreach($order->shipping_address as $key => $value)
                                @if($value)
                                <div class="info-item">
                                    <label>{{ ucfirst($key) }}:</label>
                                    <span>{{ $value }}</span>
                                </div>
                                @endif
                            @endforeach
                        @else
                            <p class="text-muted">لا يوجد عنوان شحن</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-receipt me-2"></i>
                            عنوان الفاتورة
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($order->billing_address)
                            @foreach($order->billing_address as $key => $value)
                                @if($value)
                                <div class="info-item">
                                    <label>{{ ucfirst($key) }}:</label>
                                    <span>{{ $value }}</span>
                                </div>
                                @endif
                            @endforeach
                        @else
                            <p class="text-muted">لا يوجد عنوان فاتورة</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- المدفوعات -->
        @if($order->payments->count() > 0)
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-credit-card me-2"></i>
                    المدفوعات
                </h5>
            </div>
            <div class="card-body">
                @foreach($order->payments as $payment)
                <div class="payment-item border-bottom pb-3 mb-3">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <div class="fw-bold">{{ number_format($payment->amount, 2) }} {{ $payment->currency }}</div>
                            <small class="text-muted">{{ $payment->getPaymentMethodInArabic() }}</small>
                        </div>
                        <div class="col-md-3">
                            <span class="badge badge-{{ $payment->getStatusColor() }}">
                                {{ $payment->getStatusInArabic() }}
                            </span>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">{{ $payment->getPaymentGatewayInArabic() }}</small>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">{{ $payment->created_at->format('Y-m-d H:i:s') }}</small>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
.avatar-lg {
    width: 60px;
    height: 60px;
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
    font-size: 1.5rem;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid #f8f9fa;
}

.info-item:last-child {
    border-bottom: none;
}

.info-item label {
    font-weight: 600;
    color: #6c757d;
    margin: 0;
}

.order-item:last-child {
    border-bottom: none !important;
    margin-bottom: 0 !important;
    padding-bottom: 0 !important;
}

.payment-item:last-child {
    border-bottom: none !important;
    margin-bottom: 0 !important;
    padding-bottom: 0 !important;
}

/* Digital Cards Styling */
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.digital-card-item {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    transition: all 0.3s ease;
}

.digital-card-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
}

.digital-card-item code {
    background-color: rgba(102, 126, 234, 0.1);
    padding: 4px 8px;
    border-radius: 4px;
    border: 1px solid rgba(102, 126, 234, 0.2);
}

.digital-cards-group {
    position: relative;
}

.digital-cards-group::after {
    content: '';
    position: absolute;
    bottom: -20px;
    left: 50%;
    transform: translateX(-50%);
    width: 80%;
    height: 1px;
    background: linear-gradient(to right, transparent, #dee2e6, transparent);
}

.digital-cards-group:last-child::after {
    display: none;
}

.detail-row {
    padding: 8px 0;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.detail-row:last-child {
    border-bottom: none;
}
</style>
@endpush
