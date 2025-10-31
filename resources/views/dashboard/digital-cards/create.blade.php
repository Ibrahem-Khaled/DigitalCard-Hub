@extends('layouts.dashboard-new')

@section('title', 'إضافة بطاقة رقمية جديدة - متجر البطاقات الرقمية')

@section('page-title', 'إضافة بطاقة رقمية جديدة')
@section('page-subtitle', 'إنشاء بطاقة رقمية جديدة في النظام')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="page-title">إضافة بطاقة رقمية جديدة</h3>
            <p class="page-subtitle">إنشاء بطاقة رقمية جديدة في النظام</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('dashboard.digital-cards.index') }}" class="btn btn-outline-secondary">
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
                    بيانات البطاقة الرقمية
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('dashboard.digital-cards.store') }}">
                    @csrf

                    <div class="row">
                        <!-- المنتج -->
                        <div class="col-md-6 mb-3">
                            <label for="product_id" class="form-label">المنتج <span class="text-danger">*</span></label>
                            <select class="form-select @error('product_id') is-invalid @enderror" id="product_id" name="product_id" required>
                                <option value="">اختر المنتج</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('product_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- العملة -->
                        <div class="col-md-6 mb-3">
                            <label for="currency" class="form-label">العملة <span class="text-danger">*</span></label>
                            <select class="form-select @error('currency') is-invalid @enderror" id="currency" name="currency" required>
                                <option value="USD" {{ old('currency', 'USD') == 'USD' ? 'selected' : '' }}>دولار أمريكي (USD)</option>
                                <option value="SAR" {{ old('currency') == 'SAR' ? 'selected' : '' }}>ريال سعودي (SAR)</option>
                                <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>يورو (EUR)</option>
                            </select>
                            @error('currency')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- رمز البطاقة -->
                        <div class="col-md-6 mb-3">
                            <label for="card_code" class="form-label">رمز البطاقة</label>
                            <input type="text" class="form-control @error('card_code') is-invalid @enderror"
                                   id="card_code" name="card_code" value="{{ old('card_code') }}" placeholder="سيتم إنشاؤه تلقائياً">
                            <div class="form-text">اتركه فارغاً لإنشاء رمز تلقائي</div>
                            @error('card_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- PIN -->
                        <div class="col-md-6 mb-3">
                            <label for="card_pin" class="form-label">PIN</label>
                            <input type="text" class="form-control @error('card_pin') is-invalid @enderror"
                                   id="card_pin" name="card_pin" value="{{ old('card_pin') }}" placeholder="سيتم إنشاؤه تلقائياً">
                            <div class="form-text">اتركه فارغاً لإنشاء PIN تلقائي</div>
                            @error('card_pin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- رقم البطاقة -->
                        <div class="col-md-6 mb-3">
                            <label for="card_number" class="form-label">رقم البطاقة</label>
                            <input type="text" class="form-control @error('card_number') is-invalid @enderror"
                                   id="card_number" name="card_number" value="{{ old('card_number') }}" placeholder="سيتم إنشاؤه تلقائياً">
                            <div class="form-text">اتركه فارغاً لإنشاء رقم تلقائي</div>
                            @error('card_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- الرقم التسلسلي -->
                        <div class="col-md-6 mb-3">
                            <label for="serial_number" class="form-label">الرقم التسلسلي</label>
                            <input type="text" class="form-control @error('serial_number') is-invalid @enderror"
                                   id="serial_number" name="serial_number" value="{{ old('serial_number') }}" placeholder="سيتم إنشاؤه تلقائياً">
                            <div class="form-text">اتركه فارغاً لإنشاء رقم تسلسلي تلقائي</div>
                            @error('serial_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- القيمة -->
                        <div class="col-md-6 mb-3">
                            <label for="value" class="form-label">القيمة</label>
                            <input type="number" step="0.01" class="form-control @error('value') is-invalid @enderror"
                                   id="value" name="value" value="{{ old('value') }}" min="0">
                            @error('value')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- تاريخ الانتهاء -->
                        <div class="col-md-6 mb-3">
                            <label for="expiry_date" class="form-label">تاريخ الانتهاء</label>
                            <input type="date" class="form-control @error('expiry_date') is-invalid @enderror"
                                   id="expiry_date" name="expiry_date" value="{{ old('expiry_date') }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                            @error('expiry_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- الكمية -->
                        <div class="col-md-6 mb-3">
                            <label for="quantity" class="form-label">الكمية <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('quantity') is-invalid @enderror"
                                   id="quantity" name="quantity" value="{{ old('quantity', 1) }}" min="1" max="1000" required>
                            <div class="form-text">عدد البطاقات المراد إنشاؤها</div>
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- الحالة -->
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">الحالة <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>نشط</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>معطل</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- الملاحظات -->
                        <div class="col-12 mb-3">
                            <label for="notes" class="form-label">الملاحظات</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror"
                                      id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <a href="{{ route('dashboard.digital-cards.index') }}" class="btn btn-outline-secondary me-2">إلغاء</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-2"></i>
                            إنشاء البطاقة
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- معلومات البطاقة -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    معلومات البطاقة
                </h5>
            </div>
            <div class="card-body">
                <div class="info-item">
                    <label>المنتج:</label>
                    <span id="preview-product">سيظهر هنا</span>
                </div>
                <div class="info-item">
                    <label>العملة:</label>
                    <span id="preview-currency">USD</span>
                </div>
                <div class="info-item">
                    <label>القيمة:</label>
                    <span id="preview-value">غير محدد</span>
                </div>
                <div class="info-item">
                    <label>تاريخ الانتهاء:</label>
                    <span id="preview-expiry">بدون انتهاء</span>
                </div>
                <div class="info-item">
                    <label>الكمية:</label>
                    <span id="preview-quantity">1</span>
                </div>
                <div class="info-item">
                    <label>الحالة:</label>
                    <span id="preview-status">نشط</span>
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
                        يمكن إنشاء عدة بطاقات بنفس البيانات
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        الرموز والأرقام يتم إنشاؤها تلقائياً إذا تركت فارغة
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        تاريخ الانتهاء اختياري
                    </li>
                    <li class="mb-0">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        يمكن تعديل البطاقة بعد الإنشاء
                    </li>
                </ul>
            </div>
        </div>

        <!-- معاينة التغييرات -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-eye me-2"></i>
                    معاينة التغييرات
                </h5>
            </div>
            <div class="card-body">
                <div class="info-item">
                    <label>رمز البطاقة:</label>
                    <span id="preview-card-code" class="font-monospace">سيتم إنشاؤه</span>
                </div>
                <div class="info-item">
                    <label>PIN:</label>
                    <span id="preview-card-pin" class="font-monospace">سيتم إنشاؤه</span>
                </div>
                <div class="info-item">
                    <label>رقم البطاقة:</label>
                    <span id="preview-card-number" class="font-monospace">سيتم إنشاؤه</span>
                </div>
                <div class="info-item">
                    <label>الرقم التسلسلي:</label>
                    <span id="preview-serial-number" class="font-monospace">سيتم إنشاؤه</span>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const productSelect = document.getElementById('product_id');
    const currencySelect = document.getElementById('currency');
    const valueInput = document.getElementById('value');
    const expiryInput = document.getElementById('expiry_date');
    const quantityInput = document.getElementById('quantity');
    const statusSelect = document.getElementById('status');

    const previewProduct = document.getElementById('preview-product');
    const previewCurrency = document.getElementById('preview-currency');
    const previewValue = document.getElementById('preview-value');
    const previewExpiry = document.getElementById('preview-expiry');
    const previewQuantity = document.getElementById('preview-quantity');
    const previewStatus = document.getElementById('preview-status');

    function updatePreview() {
        const productText = productSelect.options[productSelect.selectedIndex]?.text || 'لم يتم الاختيار';
        const currency = currencySelect.value;
        const value = valueInput.value || 'غير محدد';
        const expiry = expiryInput.value || 'بدون انتهاء';
        const quantity = quantityInput.value || '1';
        const status = statusSelect.options[statusSelect.selectedIndex]?.text || 'نشط';

        previewProduct.textContent = productText;
        previewCurrency.textContent = currency;
        previewValue.textContent = value;
        previewExpiry.textContent = expiry;
        previewQuantity.textContent = quantity;
        previewStatus.textContent = status;
    }

    productSelect.addEventListener('change', updatePreview);
    currencySelect.addEventListener('change', updatePreview);
    valueInput.addEventListener('input', updatePreview);
    expiryInput.addEventListener('change', updatePreview);
    quantityInput.addEventListener('input', updatePreview);
    statusSelect.addEventListener('change', updatePreview);

    // تحديث المعاينة عند التحميل
    updatePreview();
});
</script>
@endpush

@push('styles')
<style>
.info-item {
    margin-bottom: 1rem;
}

.info-item label {
    font-weight: 600;
    color: var(--text-dark);
    display: block;
    margin-bottom: 0.25rem;
}

.info-item span {
    color: var(--text-muted);
}

.font-monospace {
    font-family: 'Courier New', monospace;
}
</style>
@endpush
@endsection
