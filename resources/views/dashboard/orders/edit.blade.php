@extends('layouts.dashboard-new')

@section('title', 'تعديل الطلب - ' . $order->order_number)

@section('page-title', 'تعديل الطلب')
@section('page-subtitle', $order->order_number)

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="page-title">تعديل الطلب</h3>
            <p class="page-subtitle">{{ $order->order_number }}</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('dashboard.orders.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-right me-2"></i>
                العودة للقائمة
            </a>
            <a href="{{ route('dashboard.orders.show', $order) }}" class="btn btn-outline-primary">
                <i class="bi bi-eye me-2"></i>
                عرض التفاصيل
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-pencil me-2"></i>
                    بيانات الطلب
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('dashboard.orders.update', $order) }}" id="orderForm">
                    @csrf
                    @method('PUT')

                    <!-- معلومات العميل -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="mb-3">معلومات العميل</h6>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="user_id" class="form-label">العميل <span class="text-danger">*</span></label>
                            <select class="form-select @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                                <option value="">اختر العميل</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id', $order->user_id) == $user->id ? 'selected' : '' }}>
                                        {{ $user->first_name }} {{ $user->last_name }} - {{ $user->email }}
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- معلومات الطلب الأساسية -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="mb-3">معلومات الطلب</h6>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">حالة الطلب <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="pending" {{ old('status', $order->status) == 'pending' ? 'selected' : '' }}>في الانتظار</option>
                                <option value="processing" {{ old('status', $order->status) == 'processing' ? 'selected' : '' }}>قيد المعالجة</option>
                                <option value="shipped" {{ old('status', $order->status) == 'shipped' ? 'selected' : '' }}>تم الشحن</option>
                                <option value="delivered" {{ old('status', $order->status) == 'delivered' ? 'selected' : '' }}>تم التسليم</option>
                                <option value="cancelled" {{ old('status', $order->status) == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                                <option value="refunded" {{ old('status', $order->status) == 'refunded' ? 'selected' : '' }}>مسترد</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- معلومات الدفع -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="mb-3">معلومات الدفع</h6>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="payment_method" class="form-label">طريقة الدفع <span class="text-danger">*</span></label>
                            <select class="form-select @error('payment_method') is-invalid @enderror" id="payment_method" name="payment_method" required>
                                <option value="">اختر طريقة الدفع</option>
                                <option value="credit_card" {{ old('payment_method', $order->payment_method) == 'credit_card' ? 'selected' : '' }}>بطاقة ائتمان</option>
                                <option value="debit_card" {{ old('payment_method', $order->payment_method) == 'debit_card' ? 'selected' : '' }}>بطاقة خصم</option>
                                <option value="bank_transfer" {{ old('payment_method', $order->payment_method) == 'bank_transfer' ? 'selected' : '' }}>تحويل بنكي</option>
                                <option value="paypal" {{ old('payment_method', $order->payment_method) == 'paypal' ? 'selected' : '' }}>باي بال</option>
                                <option value="stripe" {{ old('payment_method', $order->payment_method) == 'stripe' ? 'selected' : '' }}>سترايب</option>
                                <option value="cash_on_delivery" {{ old('payment_method', $order->payment_method) == 'cash_on_delivery' ? 'selected' : '' }}>الدفع عند الاستلام</option>
                                <option value="wallet" {{ old('payment_method', $order->payment_method) == 'wallet' ? 'selected' : '' }}>محفظة رقمية</option>
                                <option value="loyalty_points" {{ old('payment_method', $order->payment_method) == 'loyalty_points' ? 'selected' : '' }}>نقاط الولاء</option>
                            </select>
                            @error('payment_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="payment_status" class="form-label">حالة الدفع <span class="text-danger">*</span></label>
                            <select class="form-select @error('payment_status') is-invalid @enderror" id="payment_status" name="payment_status" required>
                                <option value="pending" {{ old('payment_status', $order->payment_status) == 'pending' ? 'selected' : '' }}>في الانتظار</option>
                                <option value="paid" {{ old('payment_status', $order->payment_status) == 'paid' ? 'selected' : '' }}>مدفوع</option>
                                <option value="failed" {{ old('payment_status', $order->payment_status) == 'failed' ? 'selected' : '' }}>فشل</option>
                                <option value="refunded" {{ old('payment_status', $order->payment_status) == 'refunded' ? 'selected' : '' }}>مسترد</option>
                            </select>
                            @error('payment_status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- العناوين -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="mb-3">العناوين</h6>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">عنوان الشحن</label>
                            <div class="border p-3 bg-light rounded">
                                @if($order->shipping_address && is_array($order->shipping_address))
                                    @foreach($order->shipping_address as $key => $value)
                                        @if($value)
                                            <div class="mb-2">
                                                <strong>{{ ucfirst($key) }}:</strong> {{ $value }}
                                            </div>
                                        @endif
                                    @endforeach
                                @else
                                    <p class="text-muted mb-0">لا يوجد عنوان شحن</p>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">عنوان الفاتورة</label>
                            <div class="border p-3 bg-light rounded">
                                @if($order->billing_address && is_array($order->billing_address))
                                    @foreach($order->billing_address as $key => $value)
                                        @if($value)
                                            <div class="mb-2">
                                                <strong>{{ ucfirst($key) }}:</strong> {{ $value }}
                                            </div>
                                        @endif
                                    @endforeach
                                @else
                                    <p class="text-muted mb-0">لا يوجد عنوان فاتورة</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- ملاحظات -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="mb-3">ملاحظات إضافية</h6>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="notes" class="form-label">ملاحظات</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror"
                                      id="notes" name="notes" rows="3"
                                      placeholder="أي ملاحظات إضافية">{{ old('notes', $order->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <a href="{{ route('dashboard.orders.show', $order) }}" class="btn btn-outline-secondary me-2">إلغاء</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-2"></i>
                            حفظ التعديلات
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- عناصر الطلب -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-list-ul me-2"></i>
                    عناصر الطلب
                </h5>
            </div>
            <div class="card-body">
                @if($order->orderItems->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>المنتج</th>
                                    <th>الكمية</th>
                                    <th>السعر</th>
                                    <th>المجموع</th>
                                    <th>الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->orderItems as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($item->product->image)
                                            <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}" class="img-thumbnail me-2" style="width: 50px; height: 50px; object-fit: cover;">
                                            @endif
                                            <div>
                                                <div class="fw-bold">{{ $item->product->name }}</div>
                                                <small class="text-muted">SKU: {{ $item->product->sku }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ number_format($item->price, 2) }} {{ $order->currency }}</td>
                                    <td class="fw-bold text-primary">{{ number_format($item->total_price, 2) }} {{ $order->currency }}</td>
                                    <td>
                                        <span class="badge badge-{{ $item->getStatusColor() }}">
                                            {{ $item->getStatusInArabic() }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center mb-0">لا توجد عناصر في هذا الطلب</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- ملخص الطلب -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-calculator me-2"></i>
                    ملخص الطلب
                </h5>
            </div>
            <div class="card-body">
                <div class="summary-item">
                    <label>رقم الطلب:</label>
                    <span class="font-monospace">{{ $order->order_number }}</span>
                </div>
                <div class="summary-item">
                    <label>المجموع الفرعي:</label>
                    <span>{{ number_format($order->subtotal, 2) }} {{ $order->currency }}</span>
                </div>
                @if($order->tax_amount > 0)
                <div class="summary-item">
                    <label>الضريبة:</label>
                    <span>{{ number_format($order->tax_amount, 2) }} {{ $order->currency }}</span>
                </div>
                @endif
                @if($order->shipping_amount > 0)
                <div class="summary-item">
                    <label>رسوم الشحن:</label>
                    <span>{{ number_format($order->shipping_amount, 2) }} {{ $order->currency }}</span>
                </div>
                @endif
                @if($order->discount_amount > 0)
                <div class="summary-item">
                    <label>الخصم:</label>
                    <span class="text-success">-{{ number_format($order->discount_amount, 2) }} {{ $order->currency }}</span>
                </div>
                @endif
                <hr>
                <div class="summary-item">
                    <label class="fw-bold">المبلغ الإجمالي:</label>
                    <span class="fw-bold text-primary fs-5">{{ number_format($order->total_amount, 2) }} {{ $order->currency }}</span>
                </div>
            </div>
        </div>

        <!-- معلومات إضافية -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    معلومات إضافية
                </h5>
            </div>
            <div class="card-body">
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
                @if($order->shipped_at)
                <div class="info-item">
                    <label>تاريخ الشحن:</label>
                    <span>{{ $order->shipped_at->format('Y-m-d H:i:s') }}</span>
                </div>
                @endif
                @if($order->delivered_at)
                <div class="info-item">
                    <label>تاريخ التسليم:</label>
                    <span>{{ $order->delivered_at->format('Y-m-d H:i:s') }}</span>
                </div>
                @endif
                @if($order->payment_reference)
                <div class="info-item">
                    <label>مرجع الدفع:</label>
                    <span class="font-monospace">{{ $order->payment_reference }}</span>
                </div>
                @endif
                @if($order->coupon_code)
                <div class="info-item">
                    <label>كود الخصم:</label>
                    <span class="badge bg-success">{{ $order->coupon_code }}</span>
                </div>
                @endif
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
                <div class="payment-item border-bottom pb-2 mb-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-bold">{{ number_format($payment->amount, 2) }} {{ $payment->currency }}</div>
                            <small class="text-muted">{{ $payment->getPaymentMethodInArabic() }}</small>
                        </div>
                        <div>
                            <span class="badge badge-{{ $payment->getStatusColor() }}">
                                {{ $payment->getStatusInArabic() }}
                            </span>
                        </div>
                    </div>
                    @if($payment->getPaymentGatewayInArabic())
                    <div class="mt-1">
                        <small class="text-muted">{{ $payment->getPaymentGatewayInArabic() }}</small>
                    </div>
                    @endif
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
.summary-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid #e9ecef;
}

.summary-item:last-child {
    border-bottom: none;
}

.summary-item label {
    margin: 0;
    font-weight: 500;
}

.summary-item span {
    font-weight: 600;
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

.payment-item:last-child {
    border-bottom: none !important;
    margin-bottom: 0 !important;
    padding-bottom: 0 !important;
}
</style>
@endpush

