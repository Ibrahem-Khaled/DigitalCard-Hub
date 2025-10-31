@extends('layouts.dashboard-new')

@section('title', 'تعديل السلايدر - ' . $slider->title . ' - متجر البطاقات الرقمية')

@section('page-title', 'تعديل السلايدر')
@section('page-subtitle', 'تعديل بيانات السلايدر: ' . $slider->title)

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="page-title">تعديل السلايدر</h3>
            <p class="page-subtitle">تعديل بيانات السلايدر: {{ $slider->title }}</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('dashboard.sliders.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-right me-2"></i>
                العودة للقائمة
            </a>
            <a href="{{ route('dashboard.sliders.show', $slider) }}" class="btn btn-outline-primary">
                <i class="bi bi-eye me-2"></i>
                عرض السلايدر
            </a>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('dashboard.sliders.update', $slider) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-image me-2"></i>
                    بيانات السلايدر
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- العنوان -->
                    <div class="col-md-6 mb-3">
                        <label for="title" class="form-label">عنوان السلايدر <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror"
                               id="title" name="title" value="{{ old('title', $slider->title) }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- الموقع -->
                    <div class="col-md-6 mb-3">
                        <label for="position" class="form-label">موقع العرض <span class="text-danger">*</span></label>
                        <select class="form-select @error('position') is-invalid @enderror"
                                id="position" name="position" required>
                            <option value="">اختر الموقع</option>
                            @foreach($positions as $key => $label)
                                <option value="{{ $key }}" {{ old('position', $slider->position) == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('position')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- الوصف -->
                    <div class="col-12 mb-3">
                        <label for="description" class="form-label">وصف السلايدر</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="3">{{ old('description', $slider->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- الصورة -->
                    <div class="col-12 mb-3">
                        <label for="image" class="form-label">صورة السلايدر</label>

                        @if($slider->image)
                        <div class="mb-2">
                            <img src="{{ $slider->image_url }}" alt="{{ $slider->title }}"
                                 class="img-thumbnail" style="max-width: 200px; max-height: 150px;">
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

                    <!-- نص الزر -->
                    <div class="col-md-6 mb-3">
                        <label for="button_text" class="form-label">نص الزر</label>
                        <input type="text" class="form-control @error('button_text') is-invalid @enderror"
                               id="button_text" name="button_text" value="{{ old('button_text', $slider->button_text) }}">
                        @error('button_text')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- رابط الزر -->
                    <div class="col-md-6 mb-3">
                        <label for="button_url" class="form-label">رابط الزر</label>
                        <input type="url" class="form-control @error('button_url') is-invalid @enderror"
                               id="button_url" name="button_url" value="{{ old('button_url', $slider->button_url) }}">
                        @error('button_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- ترتيب العرض -->
                    <div class="col-md-6 mb-3">
                        <label for="sort_order" class="form-label">ترتيب العرض</label>
                        <input type="number" class="form-control @error('sort_order') is-invalid @enderror"
                               id="sort_order" name="sort_order" value="{{ old('sort_order', $slider->sort_order) }}" min="0">
                        @error('sort_order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- حالة التفعيل -->
                    <div class="col-md-6 mb-3">
                        <div class="form-check form-switch mt-4">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                   value="1" {{ old('is_active', $slider->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                تفعيل السلايدر
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- إعدادات التوقيت -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-calendar me-2"></i>
                    إعدادات التوقيت
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="starts_at" class="form-label">تاريخ البداية</label>
                    <input type="datetime-local" class="form-control @error('starts_at') is-invalid @enderror"
                           id="starts_at" name="starts_at"
                           value="{{ old('starts_at', $slider->starts_at?->format('Y-m-d\TH:i')) }}">
                    @error('starts_at')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="ends_at" class="form-label">تاريخ النهاية</label>
                    <input type="datetime-local" class="form-control @error('ends_at') is-invalid @enderror"
                           id="ends_at" name="ends_at"
                           value="{{ old('ends_at', $slider->ends_at?->format('Y-m-d\TH:i')) }}">
                    @error('ends_at')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    <small>اتركهما فارغين للعرض المستمر</small>
                </div>
            </div>
        </div>

        <!-- إعدادات الإ动画 -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-gear me-2"></i>
                    إعدادات الإ动画
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="animation_type" class="form-label">نوع الإ动画</label>
                    <select class="form-select" id="animation_type" name="animation_type">
                        <option value="fade" {{ old('animation_type', $slider->getSetting('animation_type', 'fade')) == 'fade' ? 'selected' : '' }}>Fade</option>
                        <option value="slide" {{ old('animation_type', $slider->getSetting('animation_type', 'fade')) == 'slide' ? 'selected' : '' }}>Slide</option>
                        <option value="zoom" {{ old('animation_type', $slider->getSetting('animation_type', 'fade')) == 'zoom' ? 'selected' : '' }}>Zoom</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="animation_duration" class="form-label">مدة الإ动画 (ثانية)</label>
                    <input type="number" class="form-control" id="animation_duration" name="animation_duration"
                           value="{{ old('animation_duration', $slider->getSetting('animation_duration', 3)) }}" min="1" max="10" step="0.5">
                </div>
            </div>
        </div>

        <!-- معلومات النظام -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    معلومات النظام
                </h5>
            </div>
            <div class="card-body">
                <div class="info-item">
                    <label>تاريخ الإنشاء:</label>
                    <span>{{ $slider->created_at->format('Y-m-d H:i:s') }}</span>
                </div>
                <div class="info-item">
                    <label>آخر تحديث:</label>
                    <span>{{ $slider->updated_at->format('Y-m-d H:i:s') }}</span>
                </div>
                <div class="info-item">
                    <label>الحالة الحالية:</label>
                    <span class="badge bg-{{ $slider->isCurrentlyActive() ? 'success' : 'danger' }}">
                        {{ $slider->isCurrentlyActive() ? 'متاح للعرض' : 'غير متاح' }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- أزرار الحفظ -->
<div class="row mt-3">
    <div class="col-12">
        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('dashboard.sliders.show', $slider) }}" class="btn btn-outline-secondary">
                <i class="bi bi-x-lg me-2"></i>
                إلغاء
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-2"></i>
                حفظ التغييرات
            </button>
        </div>
    </div>
</div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // معاينة الصورة
    const imageInput = document.getElementById('image');
    const imagePreview = document.createElement('div');
    imagePreview.className = 'mt-2';
    imagePreview.innerHTML = '<img id="preview" class="img-thumbnail" style="max-width: 200px; max-height: 150px; display: none;">';
    imageInput.parentNode.appendChild(imagePreview);

    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('preview');
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });
});
</script>
@endpush
@endsection
