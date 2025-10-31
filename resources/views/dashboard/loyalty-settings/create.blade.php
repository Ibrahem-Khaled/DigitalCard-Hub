@extends('layouts.dashboard-new')

@section('title', 'إضافة إعداد جديد - متجر البطاقات الرقمية')

@section('page-title', 'إضافة إعداد جديد')
@section('page-subtitle', 'إنشاء إعداد جديد لنظام نقاط الولاء')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="page-title">إضافة إعداد جديد</h3>
            <p class="page-subtitle">إنشاء إعداد جديد لنظام نقاط الولاء</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('dashboard.loyalty-settings.index') }}" class="btn btn-outline-secondary">
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
                    معلومات الإعداد الأساسية
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('dashboard.loyalty-settings.store') }}">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="setting_key" class="form-label">مفتاح الإعداد <span class="text-danger">*</span></label>
                                <input type="text"
                                       class="form-control @error('setting_key') is-invalid @enderror"
                                       id="setting_key" name="setting_key" value="{{ old('setting_key') }}"
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
                                        <option value="{{ $key }}" {{ old('category') === $key ? 'selected' : '' }}>{{ $name }}</option>
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
                                        <option value="{{ $key }}" {{ old('setting_type') === $key ? 'selected' : '' }}>{{ $name }}</option>
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
                                       id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}"
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
                                   id="setting_value" name="setting_value" value="{{ old('setting_value') }}"
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
                                  placeholder="وصف تفصيلي للإعداد">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>
                            إنشاء الإعداد
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
                    <h6><i class="bi bi-info-circle me-2"></i>أنواع الإعدادات:</h6>
                    <ul class="mb-0">
                        <li><strong>نص:</strong> قيمة نصية عادية</li>
                        <li><strong>رقم صحيح:</strong> أرقام صحيحة فقط</li>
                        <li><strong>رقم عشري:</strong> أرقام عشرية</li>
                        <li><strong>نعم/لا:</strong> true أو false</li>
                        <li><strong>JSON:</strong> بيانات JSON صحيحة</li>
                        <li><strong>مصفوفة:</strong> قيم مفصولة بفواصل</li>
                    </ul>
                </div>

                <div class="alert alert-warning">
                    <h6><i class="bi bi-exclamation-triangle me-2"></i>تحذيرات مهمة:</h6>
                    <ul class="mb-0">
                        <li>تأكد من صحة مفتاح الإعداد</li>
                        <li>اختر النوع المناسب للقيمة</li>
                        <li>أضف وصف واضح للإعداد</li>
                        <li>تجنب استخدام مفاتيح مكررة</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- أمثلة على الإعدادات -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-book me-2"></i>
                    أمثلة على الإعدادات
                </h5>
            </div>
            <div class="card-body">
                <div class="setting-examples">
                    <div class="example-item">
                        <h6>إعدادات القيم:</h6>
                        <code>default_point_value_usd</code> = <span class="text-success">0.01</span>
                    </div>
                    <div class="example-item">
                        <h6>إعدادات المكافآت:</h6>
                        <code>referral_bonus_points</code> = <span class="text-success">500</span>
                    </div>
                    <div class="example-item">
                        <h6>إعدادات النظام:</h6>
                        <code>loyalty_system_enabled</code> = <span class="text-success">true</span>
                    </div>
                </div>
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
.setting-examples .example-item {
    margin-bottom: 15px;
    padding: 10px;
    background: #f8f9fa;
    border-radius: 6px;
}

.setting-examples h6 {
    margin-bottom: 5px;
    color: var(--text-dark);
    font-size: 0.9rem;
}

.setting-examples code {
    background: rgba(var(--primary-purple-rgb), 0.1);
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 0.85rem;
    color: var(--primary-purple);
}

.form-text {
    font-size: 0.8rem;
    color: var(--text-muted);
}
</style>
@endpush
@endsection



