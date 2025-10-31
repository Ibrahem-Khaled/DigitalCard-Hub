@extends('layouts.dashboard-new')

@section('title', 'إضافة كوبون جديد - متجر البطاقات الرقمية')

@section('page-title', 'إضافة كوبون جديد')
@section('page-subtitle', 'إنشاء كوبون خصم جديد')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="page-title">إضافة كوبون جديد</h3>
            <p class="page-subtitle">إنشاء كوبون خصم جديد مع جميع الخيارات المتقدمة</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('dashboard.coupons.index') }}" class="btn btn-outline-secondary">
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
                    معلومات الكوبون الأساسية
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('dashboard.coupons.store') }}">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="code" class="form-label">كود الكوبون <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror"
                                       id="code" name="code" value="{{ old('code') }}"
                                       placeholder="مثال: DISCOUNT20" required>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">يجب أن يكون الكود فريداً وسهل التذكر</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">اسم الكوبون <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name') }}"
                                       placeholder="مثال: خصم 20%" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">وصف الكوبون</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="3"
                                  placeholder="وصف مختصر للكوبون">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="type" class="form-label">نوع الخصم <span class="text-danger">*</span></label>
                                <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                    <option value="">اختر النوع</option>
                                    <option value="percentage" {{ old('type') === 'percentage' ? 'selected' : '' }}>نسبة مئوية</option>
                                    <option value="fixed" {{ old('type') === 'fixed' ? 'selected' : '' }}>مبلغ ثابت</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="value" class="form-label">قيمة الخصم <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0"
                                       class="form-control @error('value') is-invalid @enderror"
                                       id="value" name="value" value="{{ old('value') }}"
                                       placeholder="20" required>
                                @error('value')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">النسبة المئوية أو المبلغ بالدولار</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="minimum_amount" class="form-label">الحد الأدنى للطلب</label>
                                <input type="number" step="0.01" min="0"
                                       class="form-control @error('minimum_amount') is-invalid @enderror"
                                       id="minimum_amount" name="minimum_amount" value="{{ old('minimum_amount') }}"
                                       placeholder="100">
                                @error('minimum_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">الحد الأدنى لقيمة الطلب</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="maximum_discount" class="form-label">الحد الأقصى للخصم</label>
                                <input type="number" step="0.01" min="0"
                                       class="form-control @error('maximum_discount') is-invalid @enderror"
                                       id="maximum_discount" name="maximum_discount" value="{{ old('maximum_discount') }}"
                                       placeholder="50">
                                @error('maximum_discount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">الحد الأقصى لمبلغ الخصم</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="usage_limit" class="form-label">حد الاستخدام</label>
                                <input type="number" min="1"
                                       class="form-control @error('usage_limit') is-invalid @enderror"
                                       id="usage_limit" name="usage_limit" value="{{ old('usage_limit') }}"
                                       placeholder="100">
                                @error('usage_limit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">عدد المرات التي يمكن استخدام الكوبون</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="user_limit" class="form-label">حد المستخدم</label>
                                <input type="number" min="1"
                                       class="form-control @error('user_limit') is-invalid @enderror"
                                       id="user_limit" name="user_limit" value="{{ old('user_limit') }}"
                                       placeholder="1">
                                @error('user_limit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">عدد المرات التي يمكن للمستخدم استخدام الكوبون</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="starts_at" class="form-label">تاريخ البداية</label>
                                <input type="datetime-local"
                                       class="form-control @error('starts_at') is-invalid @enderror"
                                       id="starts_at" name="starts_at" value="{{ old('starts_at') }}">
                                @error('starts_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="expires_at" class="form-label">تاريخ الانتهاء</label>
                                <input type="datetime-local"
                                       class="form-control @error('expires_at') is-invalid @enderror"
                                       id="expires_at" name="expires_at" value="{{ old('expires_at') }}">
                                @error('expires_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">خيارات إضافية</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        تفعيل الكوبون فوراً
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="first_time_only" name="first_time_only" value="1" {{ old('first_time_only') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="first_time_only">
                                        للمرة الأولى فقط
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="stackable" name="stackable" value="1" {{ old('stackable') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="stackable">
                                        قابل للتجميع مع كوبونات أخرى
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>
                            إنشاء الكوبون
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- نصائح وإرشادات -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-lightbulb me-2"></i>
                    نصائح وإرشادات
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h6><i class="bi bi-info-circle me-2"></i>نصائح لإنشاء كوبون فعال:</h6>
                    <ul class="mb-0">
                        <li>استخدم أكواد سهلة التذكر</li>
                        <li>حدد فترة زمنية مناسبة</li>
                        <li>ضع حدود منطقية للاستخدام</li>
                        <li>اختبر الكوبون قبل النشر</li>
                    </ul>
                </div>

                <div class="alert alert-warning">
                    <h6><i class="bi bi-exclamation-triangle me-2"></i>تحذيرات مهمة:</h6>
                    <ul class="mb-0">
                        <li>لا يمكن حذف الكوبونات المستخدمة</li>
                        <li>تأكد من صحة التواريخ</li>
                        <li>راقب استخدام الكوبونات بانتظام</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- معاينة الكوبون -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-eye me-2"></i>
                    معاينة الكوبون
                </h5>
            </div>
            <div class="card-body">
                <div class="coupon-preview">
                    <div class="coupon-code">DISCOUNT20</div>
                    <div class="coupon-name">خصم 20%</div>
                    <div class="coupon-description">خصم خاص على جميع المنتجات</div>
                    <div class="coupon-details">
                        <small class="text-muted">صالح حتى: 31/12/2024</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.coupon-preview {
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.coupon-code {
    font-family: 'Courier New', monospace;
    font-size: 1.5rem;
    font-weight: bold;
    color: var(--primary-purple);
    margin-bottom: 10px;
}

.coupon-name {
    font-size: 1.2rem;
    font-weight: 600;
    margin-bottom: 5px;
}

.coupon-description {
    color: #6c757d;
    margin-bottom: 10px;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // تحديث معاينة الكوبون عند تغيير الحقول
    const codeInput = document.getElementById('code');
    const nameInput = document.getElementById('name');
    const descriptionInput = document.getElementById('description');
    const expiresAtInput = document.getElementById('expires_at');

    const couponCode = document.querySelector('.coupon-code');
    const couponName = document.querySelector('.coupon-name');
    const couponDescription = document.querySelector('.coupon-description');
    const couponDetails = document.querySelector('.coupon-details');

    function updatePreview() {
        if (codeInput.value) {
            couponCode.textContent = codeInput.value;
        }
        if (nameInput.value) {
            couponName.textContent = nameInput.value;
        }
        if (descriptionInput.value) {
            couponDescription.textContent = descriptionInput.value;
        }
        if (expiresAtInput.value) {
            const date = new Date(expiresAtInput.value);
            couponDetails.innerHTML = `<small class="text-muted">صالح حتى: ${date.toLocaleDateString('ar-SA')}</small>`;
        }
    }

    codeInput.addEventListener('input', updatePreview);
    nameInput.addEventListener('input', updatePreview);
    descriptionInput.addEventListener('input', updatePreview);
    expiresAtInput.addEventListener('input', updatePreview);
});
</script>
@endpush
@endsection
