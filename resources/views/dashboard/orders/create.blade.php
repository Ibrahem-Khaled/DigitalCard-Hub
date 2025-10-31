@extends('layouts.dashboard-new')

@section('title', 'إضافة طلب جديد - متجر البطاقات الرقمية')

@section('page-title', 'إضافة طلب جديد')
@section('page-subtitle', 'إنشاء طلب جديد في النظام')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="page-title">إضافة طلب جديد</h3>
            <p class="page-subtitle">إنشاء طلب جديد في النظام</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('dashboard.orders.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-right me-2"></i>
                العودة للقائمة
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-plus-circle me-2"></i>
                    بيانات الطلب
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('dashboard.orders.store') }}" id="orderForm">
                    @csrf

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
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->first_name }} {{ $user->last_name }} - {{ $user->email }}
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- عناصر الطلب -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="mb-3">عناصر الطلب</h6>
                        </div>
                        <div class="col-12">
                            <div id="orderItems">
                                <div class="order-item border p-3 mb-3">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <label class="form-label">المنتج <span class="text-danger">*</span></label>
                                            <select class="form-select product-select" name="items[0][product_id]" required>
                                                <option value="">اختر المنتج</option>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->id }}" data-price="{{ $product->price }}" data-sale-price="{{ $product->sale_price }}">
                                                        {{ $product->name }} - {{ number_format($product->price, 2) }} $
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">الكمية <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control quantity-input" name="items[0][quantity]" value="1" min="1" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">السعر</label>
                                            <input type="number" step="0.01" class="form-control price-input" name="items[0][price]" readonly>
                                        </div>
                                        <div class="col-md-1">
                                            <label class="form-label">&nbsp;</label>
                                            <button type="button" class="btn btn-outline-danger btn-sm w-100 remove-item" style="display: none;">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-primary" id="addItem">
                                <i class="bi bi-plus me-2"></i>
                                إضافة منتج
                            </button>
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
                                <option value="credit_card" {{ old('payment_method') == 'credit_card' ? 'selected' : '' }}>بطاقة ائتمان</option>
                                <option value="debit_card" {{ old('payment_method') == 'debit_card' ? 'selected' : '' }}>بطاقة خصم</option>
                                <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>تحويل بنكي</option>
                                <option value="paypal" {{ old('payment_method') == 'paypal' ? 'selected' : '' }}>باي بال</option>
                                <option value="stripe" {{ old('payment_method') == 'stripe' ? 'selected' : '' }}>سترايب</option>
                                <option value="cash_on_delivery" {{ old('payment_method') == 'cash_on_delivery' ? 'selected' : '' }}>الدفع عند الاستلام</option>
                                <option value="wallet" {{ old('payment_method') == 'wallet' ? 'selected' : '' }}>محفظة رقمية</option>
                                <option value="loyalty_points" {{ old('payment_method') == 'loyalty_points' ? 'selected' : '' }}>نقاط الولاء</option>
                            </select>
                            @error('payment_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="payment_status" class="form-label">حالة الدفع <span class="text-danger">*</span></label>
                            <select class="form-select @error('payment_status') is-invalid @enderror" id="payment_status" name="payment_status" required>
                                <option value="pending" {{ old('payment_status') == 'pending' ? 'selected' : '' }}>في الانتظار</option>
                                <option value="paid" {{ old('payment_status') == 'paid' ? 'selected' : '' }}>مدفوع</option>
                                <option value="failed" {{ old('payment_status') == 'failed' ? 'selected' : '' }}>فشل</option>
                                <option value="refunded" {{ old('payment_status') == 'refunded' ? 'selected' : '' }}>مسترد</option>
                            </select>
                            @error('payment_status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="payment_gateway" class="form-label">بوابة الدفع</label>
                            <select class="form-select" id="payment_gateway" name="payment_gateway">
                                <option value="manual">يدوي</option>
                                <option value="stripe">سترايب</option>
                                <option value="paypal">باي بال</option>
                                <option value="square">سكوير</option>
                                <option value="razorpay">رازور باي</option>
                                <option value="moyasar">مويصر</option>
                                <option value="tap">تاب</option>
                                <option value="fawry">فوري</option>
                                <option value="valu">فاليو</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="currency" class="form-label">العملة</label>
                            <select class="form-select" id="currency" name="currency">
                                <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>دولار أمريكي (USD)</option>
                                <option value="SAR" {{ old('currency') == 'SAR' ? 'selected' : '' }}>ريال سعودي (SAR)</option>
                                <option value="AED" {{ old('currency') == 'AED' ? 'selected' : '' }}>درهم إماراتي (AED)</option>
                                <option value="EGP" {{ old('currency') == 'EGP' ? 'selected' : '' }}>جنيه مصري (EGP)</option>
                                <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>يورو (EUR)</option>
                            </select>
                        </div>
                    </div>

                    <!-- العناوين -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="mb-3">العناوين</h6>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="shipping_address" class="form-label">عنوان الشحن <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('shipping_address') is-invalid @enderror"
                                      id="shipping_address" name="shipping_address" rows="3" required
                                      placeholder="أدخل عنوان الشحن">{{ old('shipping_address') }}</textarea>
                            @error('shipping_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="billing_address" class="form-label">عنوان الفاتورة <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('billing_address') is-invalid @enderror"
                                      id="billing_address" name="billing_address" rows="3" required
                                      placeholder="أدخل عنوان الفاتورة">{{ old('billing_address') }}</textarea>
                            @error('billing_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
                                      placeholder="أي ملاحظات إضافية">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <a href="{{ route('dashboard.orders.index') }}" class="btn btn-outline-secondary me-2">إلغاء</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-2"></i>
                            إنشاء الطلب
                        </button>
                    </div>
                </form>
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
                <div id="orderSummary">
                    <div class="summary-item">
                        <label>المجموع الفرعي:</label>
                        <span id="subtotal">0.00 $</span>
                    </div>
                    <div class="summary-item">
                        <label>الضريبة:</label>
                        <span id="tax">0.00 $</span>
                    </div>
                    <div class="summary-item">
                        <label>رسوم الشحن:</label>
                        <span id="shipping">0.00 $</span>
                    </div>
                    <div class="summary-item">
                        <label>الخصم:</label>
                        <span id="discount">0.00 $</span>
                    </div>
                    <hr>
                    <div class="summary-item">
                        <label class="fw-bold">المبلغ الإجمالي:</label>
                        <span id="total" class="fw-bold text-primary">0.00 $</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- نصائح -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-lightbulb me-2"></i>
                    نصائح
                </h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        تأكد من اختيار العميل الصحيح
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        تحقق من صحة عناصر الطلب
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        أدخل العناوين بشكل صحيح
                    </li>
                    <li class="mb-0">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        راجع المبلغ الإجمالي قبل الحفظ
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let itemIndex = 0;

// إضافة عنصر جديد
document.getElementById('addItem').addEventListener('click', function() {
    itemIndex++;
    const orderItems = document.getElementById('orderItems');
    const newItem = document.createElement('div');
    newItem.className = 'order-item border p-3 mb-3';
    newItem.innerHTML = `
        <div class="row">
            <div class="col-md-5">
                <label class="form-label">المنتج <span class="text-danger">*</span></label>
                <select class="form-select product-select" name="items[${itemIndex}][product_id]" required>
                    <option value="">اختر المنتج</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" data-price="{{ $product->price }}" data-sale-price="{{ $product->sale_price }}">
                            {{ $product->name }} - {{ number_format($product->price, 2) }} $
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">الكمية <span class="text-danger">*</span></label>
                <input type="number" class="form-control quantity-input" name="items[${itemIndex}][quantity]" value="1" min="1" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">السعر</label>
                <input type="number" step="0.01" class="form-control price-input" name="items[${itemIndex}][price]" readonly>
            </div>
            <div class="col-md-1">
                <label class="form-label">&nbsp;</label>
                <button type="button" class="btn btn-outline-danger btn-sm w-100 remove-item">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
    `;
    orderItems.appendChild(newItem);

    // إظهار أزرار الحذف
    document.querySelectorAll('.remove-item').forEach(btn => {
        btn.style.display = 'block';
    });

    // إضافة مستمعي الأحداث للعنصر الجديد
    addEventListeners(newItem);
});

// حذف عنصر
document.addEventListener('click', function(e) {
    if (e.target.closest('.remove-item')) {
        e.target.closest('.order-item').remove();
        updateSummary();

        // إخفاء أزرار الحذف إذا كان هناك عنصر واحد فقط
        const items = document.querySelectorAll('.order-item');
        if (items.length === 1) {
            document.querySelector('.remove-item').style.display = 'none';
        }
    }
});

// إضافة مستمعي الأحداث
function addEventListeners(item) {
    const productSelect = item.querySelector('.product-select');
    const quantityInput = item.querySelector('.quantity-input');
    const priceInput = item.querySelector('.price-input');

    productSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const price = selectedOption.dataset.salePrice || selectedOption.dataset.price;
        priceInput.value = price || 0;
        updateSummary();
    });

    quantityInput.addEventListener('input', updateSummary);
}

// تحديث الملخص
function updateSummary() {
    let subtotal = 0;

    document.querySelectorAll('.order-item').forEach(item => {
        const quantity = parseFloat(item.querySelector('.quantity-input').value) || 0;
        const price = parseFloat(item.querySelector('.price-input').value) || 0;
        subtotal += quantity * price;
    });

    const tax = 0; // يمكن إضافة حساب الضريبة هنا
    const shipping = 0; // يمكن إضافة حساب الشحن هنا
    const discount = 0; // يمكن إضافة حساب الخصم هنا
    const total = subtotal + tax + shipping - discount;

    document.getElementById('subtotal').textContent = subtotal.toFixed(2) + ' $';
    document.getElementById('tax').textContent = tax.toFixed(2) + ' $';
    document.getElementById('shipping').textContent = shipping.toFixed(2) + ' $';
    document.getElementById('discount').textContent = discount.toFixed(2) + ' $';
    document.getElementById('total').textContent = total.toFixed(2) + ' $';
}

// إضافة مستمعي الأحداث للعنصر الأول
document.addEventListener('DOMContentLoaded', function() {
    addEventListeners(document.querySelector('.order-item'));
});
</script>
@endpush

@push('styles')
<style>
.order-item {
    background-color: #f8f9fa;
    border-radius: 0.375rem;
}

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
</style>
@endpush
