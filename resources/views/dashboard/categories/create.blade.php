@extends('layouts.dashboard-new')

@section('title', 'إضافة فئة جديدة - متجر البطاقات الرقمية')

@section('page-title', 'إضافة فئة جديدة')
@section('page-subtitle', 'إنشاء فئة جديدة في النظام')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="page-title">إضافة فئة جديدة</h3>
            <p class="page-subtitle">إنشاء فئة جديدة في النظام</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('dashboard.categories.index') }}" class="btn btn-outline-secondary">
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
                    بيانات الفئة
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('dashboard.categories.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <!-- اسم الفئة -->
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">اسم الفئة <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- ترتيب العرض -->
                        <div class="col-md-6 mb-3">
                            <label for="sort_order" class="form-label">ترتيب العرض</label>
                            <input type="number" class="form-control @error('sort_order') is-invalid @enderror"
                                   id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0">
                            <div class="form-text">رقم أقل يعني ظهور مبكر</div>
                            @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- الفئة الرئيسية -->
                        <div class="col-md-6 mb-3">
                            <label for="parent_id" class="form-label">الفئة الرئيسية</label>
                            <select class="form-select @error('parent_id') is-invalid @enderror" id="parent_id" name="parent_id">
                                <option value="">فئة رئيسية (بدون فئة رئيسية)</option>
                                @foreach($parentCategories as $parentCategory)
                                    <option value="{{ $parentCategory->id }}" {{ old('parent_id') == $parentCategory->id ? 'selected' : '' }}>
                                        {{ $parentCategory->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('parent_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- الحالة -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">الحالة</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                       value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    نشط
                                </label>
                            </div>
                        </div>

                        <!-- الوصف -->
                        <div class="col-12 mb-3">
                            <label for="description" class="form-label">الوصف</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="4">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- صورة الفئة -->
                        <div class="col-12 mb-3">
                            <label for="image" class="form-label">صورة الفئة</label>
                            <input type="file" class="form-control @error('image') is-invalid @enderror"
                                   id="image" name="image" accept="image/*">
                            <div class="form-text">الحد الأقصى للحجم: 2MB. الأنواع المسموحة: JPG, PNG, GIF</div>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <a href="{{ route('dashboard.categories.index') }}" class="btn btn-outline-secondary me-2">إلغاء</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-2"></i>
                            إنشاء الفئة
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- معلومات الفئة -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    معلومات الفئة
                </h5>
            </div>
            <div class="card-body">
                <div class="info-item">
                    <label>الاسم:</label>
                    <span id="preview-name">سيظهر هنا</span>
                </div>
                <div class="info-item">
                    <label>الرابط:</label>
                    <span id="preview-slug">سيظهر هنا</span>
                </div>
                <div class="info-item">
                    <label>النوع:</label>
                    <span id="preview-type">فئة رئيسية</span>
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
                        اسم الفئة يجب أن يكون واضحاً ومميزاً
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        الرابط يتم إنشاؤه تلقائياً من الاسم
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        يمكن تغيير ترتيب العرض لاحقاً
                    </li>
                    <li class="mb-0">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        يمكن إضافة فئات فرعية للفئة الرئيسية
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('name');
    const parentSelect = document.getElementById('parent_id');
    const statusCheckbox = document.getElementById('is_active');

    const previewName = document.getElementById('preview-name');
    const previewSlug = document.getElementById('preview-slug');
    const previewType = document.getElementById('preview-type');
    const previewStatus = document.getElementById('preview-status');

    function updatePreview() {
        const name = nameInput.value || 'اسم الفئة';
        const slug = name.toLowerCase().replace(/\s+/g, '-').replace(/[^\w\-]+/g, '');
        const isParent = !parentSelect.value;
        const isActive = statusCheckbox.checked;

        previewName.textContent = name;
        previewSlug.textContent = slug;
        previewType.textContent = isParent ? 'فئة رئيسية' : 'فئة فرعية';
        previewStatus.textContent = isActive ? 'نشط' : 'معطل';
    }

    nameInput.addEventListener('input', updatePreview);
    parentSelect.addEventListener('change', updatePreview);
    statusCheckbox.addEventListener('change', updatePreview);

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

.form-check-input:checked {
    background-color: var(--primary-purple);
    border-color: var(--primary-purple);
}

.form-check-input:focus {
    box-shadow: 0 0 0 0.25rem rgba(139, 92, 246, 0.25);
}
</style>
@endpush
@endsection
