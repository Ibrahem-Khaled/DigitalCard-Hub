@extends('layouts.dashboard-new')

@section('title', 'تعديل الفئة - ' . $category->name . ' - متجر البطاقات الرقمية')

@section('page-title', 'تعديل الفئة')
@section('page-subtitle', 'تعديل بيانات الفئة: ' . $category->name)

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="page-title">تعديل الفئة</h3>
            <p class="page-subtitle">تعديل بيانات الفئة: {{ $category->name }}</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('dashboard.categories.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-right me-2"></i>
                العودة للقائمة
            </a>
            <a href="{{ route('dashboard.categories.show', $category) }}" class="btn btn-outline-primary">
                <i class="bi bi-eye me-2"></i>
                عرض الفئة
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-pencil-square me-2"></i>
                    بيانات الفئة
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('dashboard.categories.update', $category) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <!-- اسم الفئة -->
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">اسم الفئة <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name', $category->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- ترتيب العرض -->
                        <div class="col-md-6 mb-3">
                            <label for="sort_order" class="form-label">ترتيب العرض</label>
                            <input type="number" class="form-control @error('sort_order') is-invalid @enderror"
                                   id="sort_order" name="sort_order" value="{{ old('sort_order', $category->sort_order) }}" min="0">
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
                                    <option value="{{ $parentCategory->id }}" {{ old('parent_id', $category->parent_id) == $parentCategory->id ? 'selected' : '' }}>
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
                                       value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    نشط
                                </label>
                            </div>
                        </div>

                        <!-- الوصف -->
                        <div class="col-12 mb-3">
                            <label for="description" class="form-label">الوصف</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="4">{{ old('description', $category->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- صورة الفئة -->
                        <div class="col-12 mb-3">
                            <label for="image" class="form-label">صورة الفئة</label>

                            @if($category->image)
                                <div class="mb-2">
                                    <img src="{{ Storage::url($category->image) }}" alt="{{ $category->name }}"
                                         class="rounded" width="100" height="100">
                                    <div class="form-text">الصورة الحالية</div>
                                </div>
                            @endif

                            <input type="file" class="form-control @error('image') is-invalid @enderror"
                                   id="image" name="image" accept="image/*">
                            <div class="form-text">الحد الأقصى للحجم: 2MB. الأنواع المسموحة: JPG, PNG, GIF</div>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <a href="{{ route('dashboard.categories.show', $category) }}" class="btn btn-outline-secondary me-2">إلغاء</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-2"></i>
                            حفظ التغييرات
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
                    <label>الرابط الحالي:</label>
                    <span>{{ $category->slug }}</span>
                </div>
                <div class="info-item">
                    <label>تاريخ الإنشاء:</label>
                    <span>{{ $category->created_at->format('Y-m-d H:i:s') }}</span>
                </div>
                <div class="info-item">
                    <label>آخر تحديث:</label>
                    <span>{{ $category->updated_at->format('Y-m-d H:i:s') }}</span>
                </div>
                <div class="info-item">
                    <label>عدد الفئات الفرعية:</label>
                    <span>{{ $category->children->count() }}</span>
                </div>
                <div class="info-item">
                    <label>عدد المنتجات:</label>
                    <span>{{ $category->products->count() }}</span>
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
                        تأكد من صحة البيانات المدخلة
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        الرابط يتم تحديثه تلقائياً عند تغيير الاسم
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        يمكن تغيير ترتيب العرض في أي وقت
                    </li>
                    <li class="mb-0">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        جميع التغييرات محفوظة تلقائياً
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
                    <label>الاسم الجديد:</label>
                    <span id="preview-name">{{ $category->name }}</span>
                </div>
                <div class="info-item">
                    <label>الرابط الجديد:</label>
                    <span id="preview-slug">{{ $category->slug }}</span>
                </div>
                <div class="info-item">
                    <label>النوع:</label>
                    <span id="preview-type">{{ $category->parent_id ? 'فئة فرعية' : 'فئة رئيسية' }}</span>
                </div>
                <div class="info-item">
                    <label>الحالة:</label>
                    <span id="preview-status">{{ $category->is_active ? 'نشط' : 'معطل' }}</span>
                </div>
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
        const name = nameInput.value || '{{ $category->name }}';
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
