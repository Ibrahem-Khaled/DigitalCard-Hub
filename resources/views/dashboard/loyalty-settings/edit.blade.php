@extends('layouts.dashboard-new')

@section('title', 'تعديل إعداد نقاط الولاء - متجر البطاقات الرقمية')

@section('page-title', 'تعديل إعداد نقاط الولاء')
@section('page-subtitle', 'تعديل إعداد موجود في نظام نقاط الولاء')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="page-title">تعديل إعداد نقاط الولاء</h3>
            <p class="page-subtitle">تعديل إعداد موجود في نظام نقاط الولاء</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('dashboard.loyalty-settings.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-right me-2"></i>
                العودة للقائمة
            </a>
            <a href="{{ route('dashboard.loyalty-settings.show', $loyaltySetting) }}" class="btn btn-outline-info">
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
                    تعديل معلومات الإعداد
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('dashboard.loyalty-settings.update', $loyaltySetting) }}">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="setting_key" class="form-label">مفتاح الإعداد <span class="text-danger">*</span></label>
                                <input type="text"
                                       class="form-control @error('setting_key') is-invalid @enderror"
                                       id="setting_key" name="setting_key" value="{{ old('setting_key', $loyaltySetting->setting_key) }}"
                                       placeholder="example_setting_key" required>
                                @error('setting_key')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">يجب أن يكون فريداً ولا يحتوي على مسافات</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category" class="form-label">فئة الإعداد <span class="text-danger">*</span></label>
                                <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                                    <option value="">اختر الفئة</option>
                                    @foreach($categories as $key => $name)
                                        <option value="{{ $key }}" {{ old('category', $loyaltySetting->category) === $key ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="setting_type" class="form-label">نوع الإعداد <span class="text-danger">*</span></label>
                                <select class="form-select @error('setting_type') is-invalid @enderror" id="setting_type" name="setting_type" required>
                                    <option value="">اختر النوع</option>
                                    @foreach($types as $key => $name)
                                        <option value="{{ $key }}" {{ old('setting_type', $loyaltySetting->setting_type) === $key ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('setting_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sort_order" class="form-label">ترتيب الإعداد</label>
                                <input type="number" min="0"
                                       class="form-control @error('sort_order') is-invalid @enderror"
                                       id="sort_order" name="sort_order" value="{{ old('sort_order', $loyaltySetting->sort_order) }}"
                                       placeholder="0">
                                @error('sort_order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">ترتيب الإعداد في القائمة (0 = الأول)</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="setting_value" class="form-label">قيمة الإعداد <span class="text-danger">*</span></label>
                        <div id="value-input-container">
                            <input type="text"
                                   class="form-control @error('setting_value') is-invalid @enderror"
                                   id="setting_value" name="setting_value" value="{{ old('setting_value', $loyaltySetting->setting_value) }}"
                                   placeholder="أدخل قيمة الإعداد" required>
                        </div>
                        @error('setting_value')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">سيتم تحويل القيمة حسب نوع الإعداد المحدد</div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">وصف الإعداد</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="3"
                                  placeholder="وصف تفصيلي للإعداد">{{ old('description', $loyaltySetting->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                           {{ old('is_active', $loyaltySetting->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        الإعداد نشط
                                    </label>
                                </div>
                                <div class="form-text">تحديد ما إذا كان الإعداد مفعل أم لا</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_editable" name="is_editable" value="1"
                                           {{ old('is_editable', $loyaltySetting->is_editable) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_editable">
                                        الإعداد قابل للتعديل
                                    </label>
                                </div>
                                <div class="form-text">تحديد ما إذا كان يمكن تعديل الإعداد أم لا</div>
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
        <!-- معلومات الإعداد الحالي -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    معلومات الإعداد الحالي
                </h5>
            </div>
            <div class="card-body">
                <div class="setting-info">
                    <div class="info-item">
                        <span class="label">مفتاح الإعداد:</span>
                        <code class="value">{{ $loyaltySetting->setting_key }}</code>
                    </div>
                    <div class="info-item">
                        <span class="label">القيمة الحالية:</span>
                        <span class="value">
                            @if($loyaltySetting->setting_type === 'boolean')
                                <span class="badge badge-{{ $loyaltySetting->setting_value ? 'success' : 'danger' }}">
                                    {{ $loyaltySetting->setting_value ? 'نعم' : 'لا' }}
                                </span>
                            @elseif($loyaltySetting->setting_type === 'decimal')
                                <span class="fw-semibold">{{ number_format($loyaltySetting->setting_value, 4) }}</span>
                            @elseif($loyaltySetting->setting_type === 'integer')
                                <span class="fw-semibold">{{ number_format($loyaltySetting->setting_value) }}</span>
                            @else
                                <span class="text-muted">{{ Str::limit($loyaltySetting->setting_value, 30) }}</span>
                            @endif
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="label">النوع:</span>
                        <span class="badge badge-info">{{ $loyaltySetting->setting_type }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">الفئة:</span>
                        <span class="badge badge-primary">{{ $categories[$loyaltySetting->category] ?? $loyaltySetting->category }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">الحالة:</span>
                        <div class="d-flex gap-2">
                            @if($loyaltySetting->is_active)
                                <span class="badge badge-success">نشط</span>
                            @else
                                <span class="badge badge-secondary">غير نشط</span>
                            @endif

                            @if($loyaltySetting->is_editable)
                                <span class="badge badge-warning">قابل للتعديل</span>
                            @else
                                <span class="badge badge-danger">غير قابل للتعديل</span>
                            @endif
                        </div>
                    </div>
                    <div class="info-item">
                        <span class="label">تاريخ الإنشاء:</span>
                        <span class="text-muted">{{ $loyaltySetting->created_at->format('Y-m-d H:i') }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">آخر تحديث:</span>
                        <span class="text-muted">{{ $loyaltySetting->updated_at->format('Y-m-d H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- نصائح وإرشادات -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-lightbulb me-2"></i>
                    نصائح وإرشادات
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <h6><i class="bi bi-exclamation-triangle me-2"></i>تحذيرات مهمة:</h6>
                    <ul class="mb-0">
                        <li>تغيير نوع الإعداد قد يؤثر على القيمة</li>
                        <li>الإعدادات غير القابلة للتعديل محمية</li>
                        <li>تأكد من صحة القيمة الجديدة</li>
                        <li>بعض الإعدادات قد تحتاج إعادة تشغيل النظام</li>
                    </ul>
                </div>

                @if(!$loyaltySetting->is_editable)
                <div class="alert alert-danger">
                    <h6><i class="bi bi-shield-exclamation me-2"></i>إعداد محمي:</h6>
                    <p class="mb-0">هذا الإعداد محمي من التعديل. لا يمكن تغيير قيمته أو حذفه.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('setting_type');
    const valueInput = document.getElementById('setting_value');
    const container = document.getElementById('value-input-container');

    function updateValueInput() {
        const type = typeSelect.value;
        const currentValue = valueInput.value;

        // إزالة الحقل الحالي
        container.innerHTML = '';

        let newInput;

        switch(type) {
            case 'integer':
                newInput = document.createElement('input');
                newInput.type = 'number';
                newInput.step = '1';
                newInput.placeholder = 'أدخل رقم صحيح';
                break;
            case 'decimal':
                newInput = document.createElement('input');
                newInput.type = 'number';
                newInput.step = '0.0001';
                newInput.placeholder = 'أدخل رقم عشري';
                break;
            case 'boolean':
                newInput = document.createElement('select');
                newInput.innerHTML = `
                    <option value="">اختر القيمة</option>
                    <option value="true">نعم (true)</option>
                    <option value="false">لا (false)</option>
                `;
                break;
            case 'json':
                newInput = document.createElement('textarea');
                newInput.rows = 4;
                newInput.placeholder = '{"key": "value"}';
                break;
            case 'array':
                newInput = document.createElement('input');
                newInput.type = 'text';
                newInput.placeholder = 'قيمة1, قيمة2, قيمة3';
                break;
            default:
                newInput = document.createElement('input');
                newInput.type = 'text';
                newInput.placeholder = 'أدخل قيمة نصية';
        }

        newInput.className = 'form-control @error("setting_value") is-invalid @enderror';
        newInput.id = 'setting_value';
        newInput.name = 'setting_value';
        newInput.required = true;

        if (currentValue && type !== 'boolean') {
            newInput.value = currentValue;
        } else if (type === 'boolean' && currentValue) {
            newInput.value = currentValue;
        }

        container.appendChild(newInput);
    }

    typeSelect.addEventListener('change', updateValueInput);

    // تحديث أولي إذا كان هناك قيمة محفوظة
    if (typeSelect.value) {
        updateValueInput();
    }
});
</script>
@endpush

@push('styles')
<style>
.setting-info .info-item {
    margin-bottom: 15px;
    padding: 10px;
    background: #f8f9fa;
    border-radius: 6px;
}

.setting-info .label {
    font-weight: 600;
    color: var(--text-dark);
    display: block;
    margin-bottom: 5px;
}

.setting-info .value {
    color: var(--text-muted);
}

.setting-info code {
    background: rgba(var(--primary-purple-rgb), 0.1);
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 0.85rem;
    color: var(--primary-purple);
}

.badge {
    font-size: 0.75rem;
}

.form-text {
    font-size: 0.8rem;
    color: var(--text-muted);
}

.form-check-label {
    font-weight: 500;
}
</style>
@endpush
@endsection



