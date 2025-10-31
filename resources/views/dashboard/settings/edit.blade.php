@extends('layouts.dashboard-new')

@section('title', 'تعديل الإعداد')

@section('content')
<div class="page-header">
    <div class="page-header-content">
        <div class="page-title">
            <h4>
                <i class="bi bi-pencil me-2"></i>
                تعديل الإعداد
            </h4>
            <p class="text-muted mb-0">تعديل إعداد: {{ $setting->name }}</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('dashboard.settings.index', ['group' => $setting->group]) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-right me-1"></i>
                العودة للإعدادات
            </a>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-gear me-2"></i>
                    معلومات الإعداد
                </h5>
            </div>

            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('dashboard.settings.update-setting', $setting) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="key" class="form-label">مفتاح الإعداد <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('key') is-invalid @enderror"
                                   id="key"
                                   name="key"
                                   value="{{ old('key', $setting->key) }}"
                                   required>
                            <div class="form-text">يجب أن يكون فريداً (مثل: site_name)</div>
                            @error('key')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="group" class="form-label">المجموعة <span class="text-danger">*</span></label>
                            <select class="form-select @error('group') is-invalid @enderror"
                                    id="group"
                                    name="group"
                                    required>
                                <option value="">اختر المجموعة</option>
                                @foreach($groups as $value => $label)
                                    <option value="{{ $value }}" {{ old('group', $setting->group) == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('group')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">اسم الإعداد <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('name') is-invalid @enderror"
                                   id="name"
                                   name="name"
                                   value="{{ old('name', $setting->name) }}"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="type" class="form-label">نوع الإعداد <span class="text-danger">*</span></label>
                            <select class="form-select @error('type') is-invalid @enderror"
                                    id="type"
                                    name="type"
                                    required>
                                <option value="">اختر النوع</option>
                                @foreach($types as $value => $label)
                                    <option value="{{ $value }}" {{ old('type', $setting->type) == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">الوصف</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description"
                                  name="description"
                                  rows="3">{{ old('description', $setting->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="value" class="form-label">القيمة الحالية</label>
                        <textarea class="form-control @error('value') is-invalid @enderror"
                                  id="value"
                                  name="value"
                                  rows="3">{{ old('value', $setting->value) }}</textarea>
                        @error('value')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3" id="options-section" style="display: {{ $setting->type === 'select' ? 'block' : 'none' }};">
                        <label for="options" class="form-label">خيارات القائمة المنسدلة</label>
                        <div class="options-container">
                            @if($setting->options)
                                @foreach($setting->options as $value => $text)
                                    <div class="row mb-2">
                                        <div class="col-md-5">
                                            <input type="text" class="form-control" placeholder="القيمة" name="option_value[]" value="{{ $value }}">
                                        </div>
                                        <div class="col-md-5">
                                            <input type="text" class="form-control" placeholder="النص" name="option_text[]" value="{{ $text }}">
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeOption(this)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="row mb-2">
                                    <div class="col-md-5">
                                        <input type="text" class="form-control" placeholder="القيمة" name="option_value[]">
                                    </div>
                                    <div class="col-md-5">
                                        <input type="text" class="form-control" placeholder="النص" name="option_text[]">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeOption(this)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="addOption()">
                            <i class="bi bi-plus"></i>
                            إضافة خيار
                        </button>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="validation_rules" class="form-label">قواعد التحقق</label>
                            <input type="text"
                                   class="form-control @error('validation_rules') is-invalid @enderror"
                                   id="validation_rules"
                                   name="validation_rules"
                                   value="{{ old('validation_rules', $setting->validation_rules) }}"
                                   placeholder="مثل: required|email|max:255">
                            <div class="form-text">مفصولة بخط عمودي (|)</div>
                            @error('validation_rules')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="sort_order" class="form-label">ترتيب العرض</label>
                            <input type="number"
                                   class="form-control @error('sort_order') is-invalid @enderror"
                                   id="sort_order"
                                   name="sort_order"
                                   value="{{ old('sort_order', $setting->sort_order) }}"
                                   min="0">
                            @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-check">
                                <input class="form-check-input"
                                       type="checkbox"
                                       id="is_public"
                                       name="is_public"
                                       value="1"
                                       {{ old('is_public', $setting->is_public) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_public">
                                    إعداد عام (يمكن الوصول إليه من الواجهة العامة)
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-check">
                                <input class="form-check-input"
                                       type="checkbox"
                                       id="is_required"
                                       name="is_required"
                                       value="1"
                                       {{ old('is_required', $setting->is_required) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_required">
                                    إعداد مطلوب (لا يمكن حذفه)
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <div>
                            @if(!$setting->is_required)
                                <button type="button" class="btn btn-outline-danger" onclick="deleteSetting({{ $setting->id }})">
                                    <i class="bi bi-trash me-1"></i>
                                    حذف الإعداد
                                </button>
                            @endif
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('dashboard.settings.index', ['group' => $setting->group]) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x me-1"></i>
                                إلغاء
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check me-1"></i>
                                حفظ التغييرات
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('type').addEventListener('change', function() {
    const optionsSection = document.getElementById('options-section');
    if (this.value === 'select') {
        optionsSection.style.display = 'block';
    } else {
        optionsSection.style.display = 'none';
    }
});

function addOption() {
    const container = document.querySelector('.options-container');
    const optionHtml = `
        <div class="row mb-2">
            <div class="col-md-5">
                <input type="text" class="form-control" placeholder="القيمة" name="option_value[]">
            </div>
            <div class="col-md-5">
                <input type="text" class="form-control" placeholder="النص" name="option_text[]">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeOption(this)">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', optionHtml);
}

function removeOption(button) {
    button.closest('.row').remove();
}

function deleteSetting(id) {
    if (confirm('هل أنت متأكد من حذف هذا الإعداد؟')) {
        fetch(`/dashboard/settings/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '/dashboard/settings';
            } else {
                alert('حدث خطأ أثناء حذف الإعداد');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ أثناء حذف الإعداد');
        });
    }
}
</script>
@endpush
@endsection
