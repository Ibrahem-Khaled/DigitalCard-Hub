@extends('layouts.dashboard-new')

@section('title', 'تعديل الكوبون - ' . $coupon->name . ' - متجر البطاقات الرقمية')

@section('page-title', 'تعديل الكوبون')
@section('page-subtitle', 'تعديل الكوبون: ' . $coupon->name)

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="page-title">تعديل الكوبون</h3>
            <p class="page-subtitle">تعديل الكوبون: {{ $coupon->name }}</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('dashboard.coupons.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-right me-2"></i>
                العودة للقائمة
            </a>
            <a href="{{ route('dashboard.coupons.show', $coupon) }}" class="btn btn-outline-info">
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
                    تعديل معلومات الكوبون
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('dashboard.coupons.update', $coupon) }}">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="code" class="form-label">كود الكوبون <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror"
                                       id="code" name="code" value="{{ old('code', $coupon->code) }}"
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
                                       id="name" name="name" value="{{ old('name', $coupon->name) }}"
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
                                  placeholder="وصف مختصر للكوبون">{{ old('description', $coupon->description) }}</textarea>
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
                                    <option value="percentage" {{ old('type', $coupon->type) === 'percentage' ? 'selected' : '' }}>نسبة مئوية</option>
                                    <option value="fixed" {{ old('type', $coupon->type) === 'fixed' ? 'selected' : '' }}>مبلغ ثابت</option>
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
                                       id="value" name="value" value="{{ old('value', $coupon->value) }}"
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
                                       id="minimum_amount" name="minimum_amount" value="{{ old('minimum_amount', $coupon->minimum_amount) }}"
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
                                       id="maximum_discount" name="maximum_discount" value="{{ old('maximum_discount', $coupon->maximum_discount) }}"
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
                                       id="usage_limit" name="usage_limit" value="{{ old('usage_limit', $coupon->usage_limit) }}"
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
                                       id="user_limit" name="user_limit" value="{{ old('user_limit', $coupon->user_limit) }}"
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
                                       id="starts_at" name="starts_at"
                                       value="{{ old('starts_at', $coupon->starts_at?->format('Y-m-d\TH:i')) }}">
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
                                       id="expires_at" name="expires_at"
                                       value="{{ old('expires_at', $coupon->expires_at?->format('Y-m-d\TH:i')) }}">
                                @error('expires_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">خيارات إضافية</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $coupon->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        تفعيل الكوبون
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="first_time_only" name="first_time_only" value="1" {{ old('first_time_only', $coupon->first_time_only) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="first_time_only">
                                        للمرة الأولى فقط
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="stackable" name="stackable" value="1" {{ old('stackable', $coupon->stackable) ? 'checked' : '' }}>
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
                            حفظ التغييرات
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- معلومات الكوبون الحالية -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    معلومات الكوبون الحالية
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">كود الكوبون:</label>
                    <p class="mb-0 font-monospace">{{ $coupon->code }}</p>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">عدد الاستخدامات:</label>
                    <p class="mb-0">{{ $coupon->used_count }} استخدام</p>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">تاريخ الإنشاء:</label>
                    <p class="mb-0">{{ $coupon->created_at->format('Y-m-d H:i:s') }}</p>
                    <small class="text-muted">{{ $coupon->created_at->diffForHumans() }}</small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">آخر تحديث:</label>
                    <p class="mb-0">{{ $coupon->updated_at->format('Y-m-d H:i:s') }}</p>
                    <small class="text-muted">{{ $coupon->updated_at->diffForHumans() }}</small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">الحالة الحالية:</label>
                    <p class="mb-0">
                        @if($coupon->is_active)
                            @if($coupon->isValid())
                                <span class="badge badge-success">نشط</span>
                            @else
                                <span class="badge badge-warning">منتهي</span>
                            @endif
                        @else
                            <span class="badge badge-secondary">معطل</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- تحذيرات مهمة -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    تحذيرات مهمة
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <h6><i class="bi bi-info-circle me-2"></i>تنبيهات:</h6>
                    <ul class="mb-0">
                        <li>تغيير كود الكوبون قد يؤثر على المستخدمين</li>
                        <li>تعديل الحدود قد يمنع استخدامات جديدة</li>
                        <li>تغيير التواريخ يؤثر على صحة الكوبون</li>
                        <li>لا يمكن تقليل عدد الاستخدامات الحالي</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- إجراءات سريعة -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-lightning me-2"></i>
                    إجراءات سريعة
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <form method="POST" action="{{ route('dashboard.coupons.toggle-status', $coupon) }}" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-{{ $coupon->is_active ? 'warning' : 'success' }} w-100">
                            <i class="bi bi-{{ $coupon->is_active ? 'pause' : 'play' }} me-2"></i>
                            {{ $coupon->is_active ? 'تعطيل الكوبون' : 'تفعيل الكوبون' }}
                        </button>
                    </form>

                    <form method="POST" action="{{ route('dashboard.coupons.duplicate', $coupon) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-info w-100">
                            <i class="bi bi-copy me-2"></i>
                            نسخ الكوبون
                        </button>
                    </form>

                    @if($coupon->usages()->count() == 0)
                        <form method="POST" action="{{ route('dashboard.coupons.destroy', $coupon) }}" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الكوبون؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="bi bi-trash me-2"></i>
                                حذف الكوبون
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
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
