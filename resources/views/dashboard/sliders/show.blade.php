@extends('layouts.dashboard-new')

@section('title', 'عرض السلايدر - ' . $slider->title . ' - متجر البطاقات الرقمية')

@section('page-title', 'عرض السلايدر')
@section('page-subtitle', 'تفاصيل السلايدر: ' . $slider->title)

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="page-title">عرض السلايدر</h3>
            <p class="page-subtitle">تفاصيل السلايدر: {{ $slider->title }}</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('dashboard.sliders.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-right me-2"></i>
                العودة للقائمة
            </a>
            <a href="{{ route('dashboard.sliders.edit', $slider) }}" class="btn btn-outline-primary">
                <i class="bi bi-pencil me-2"></i>
                تعديل السلايدر
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- معاينة السلايدر -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-eye me-2"></i>
                    معاينة السلايدر
                </h5>
            </div>
            <div class="card-body">
                <div class="slider-preview">
                    <div class="slider-image-container">
                        <img src="{{ $slider->image_url }}" alt="{{ $slider->title }}"
                             class="img-fluid rounded" style="max-height: 400px; width: 100%; object-fit: cover;">

                        @if($slider->title || $slider->description || $slider->button_text)
                        <div class="slider-content-overlay">
                            <div class="container">
                                <div class="row align-items-center" style="min-height: 200px;">
                                    <div class="col-md-8">
                                        @if($slider->title)
                                        <h2 class="slider-title text-white mb-3">{{ $slider->title }}</h2>
                                        @endif

                                        @if($slider->description)
                                        <p class="slider-description text-white mb-4">{{ $slider->description }}</p>
                                        @endif

                                        @if($slider->button_text && $slider->button_url)
                                        <a href="{{ $slider->button_url }}" class="btn btn-primary btn-lg">
                                            {{ $slider->button_text }}
                                        </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- تفاصيل السلايدر -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    تفاصيل السلايدر
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item">
                            <label>العنوان:</label>
                            <span>{{ $slider->title }}</span>
                        </div>
                        <div class="info-item">
                            <label>الموقع:</label>
                            <span class="badge bg-info">
                                @switch($slider->position)
                                    @case('homepage') الصفحة الرئيسية @break
                                    @case('category') صفحات الفئات @break
                                    @case('product') صفحات المنتجات @break
                                    @case('footer') الفوتر @break
                                    @default {{ $slider->position }}
                                @endswitch
                            </span>
                        </div>
                        <div class="info-item">
                            <label>الترتيب:</label>
                            <span class="badge bg-secondary">{{ $slider->sort_order }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <label>الحالة:</label>
                            <span class="badge bg-{{ $slider->is_active ? 'success' : 'danger' }}">
                                {{ $slider->is_active ? 'نشط' : 'غير نشط' }}
                            </span>
                        </div>
                        <div class="info-item">
                            <label>متاح للعرض:</label>
                            <span class="badge bg-{{ $slider->isCurrentlyActive() ? 'success' : 'warning' }}">
                                {{ $slider->isCurrentlyActive() ? 'نعم' : 'لا' }}
                            </span>
                        </div>
                        <div class="info-item">
                            <label>تاريخ الإنشاء:</label>
                            <span>{{ $slider->created_at->format('Y-m-d H:i:s') }}</span>
                        </div>
                    </div>
                </div>

                @if($slider->description)
                <div class="mt-3">
                    <label>الوصف:</label>
                    <p class="text-muted">{{ $slider->description }}</p>
                </div>
                @endif

                @if($slider->button_text && $slider->button_url)
                <div class="mt-3">
                    <label>الزر:</label>
                    <p>
                        <strong>النص:</strong> {{ $slider->button_text }}<br>
                        <strong>الرابط:</strong> <a href="{{ $slider->button_url }}" target="_blank">{{ $slider->button_url }}</a>
                    </p>
                </div>
                @endif
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
                <div class="info-item">
                    <label>تاريخ البداية:</label>
                    <span>{{ $slider->starts_at ? $slider->starts_at->format('Y-m-d H:i:s') : 'غير محدد' }}</span>
                </div>
                <div class="info-item">
                    <label>تاريخ النهاية:</label>
                    <span>{{ $slider->ends_at ? $slider->ends_at->format('Y-m-d H:i:s') : 'غير محدد' }}</span>
                </div>

                @if($slider->starts_at || $slider->ends_at)
                <div class="mt-3">
                    <div class="progress" style="height: 8px;">
                        @php
                            $now = now();
                            $start = $slider->starts_at ?? $slider->created_at;
                            $end = $slider->ends_at ?? $now->addYear();
                            $total = $end->diffInDays($start);
                            $elapsed = $now->diffInDays($start);
                            $percentage = $total > 0 ? min(100, max(0, ($elapsed / $total) * 100)) : 0;
                        @endphp
                        <div class="progress-bar" role="progressbar" style="width: {{ $percentage }}%"></div>
                    </div>
                    <small class="text-muted">مدة العرض: {{ $total }} يوم</small>
                </div>
                @endif
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
                <div class="info-item">
                    <label>نوع الإ动画:</label>
                    <span class="badge bg-primary">{{ $slider->getSetting('animation_type', 'fade') }}</span>
                </div>
                <div class="info-item">
                    <label>مدة الإ动画:</label>
                    <span>{{ $slider->getSetting('animation_duration', 3) }} ثانية</span>
                </div>
            </div>
        </div>

        <!-- الإجراءات -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-lightning me-2"></i>
                    الإجراءات السريعة
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('dashboard.sliders.edit', $slider) }}" class="btn btn-primary">
                        <i class="bi bi-pencil me-2"></i>
                        تعديل السلايدر
                    </a>

                    <form action="{{ route('dashboard.sliders.toggle-status', $slider) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-{{ $slider->is_active ? 'warning' : 'success' }} w-100">
                            <i class="bi bi-{{ $slider->is_active ? 'pause' : 'play' }} me-2"></i>
                            {{ $slider->is_active ? 'إلغاء التفعيل' : 'تفعيل' }}
                        </button>
                    </form>

                    <form action="{{ route('dashboard.sliders.destroy', $slider) }}" method="POST"
                          onsubmit="return confirm('هل أنت متأكد من حذف هذا السلايدر؟')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="bi bi-trash me-2"></i>
                            حذف السلايدر
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.slider-preview {
    position: relative;
    overflow: hidden;
    border-radius: 8px;
}

.slider-image-container {
    position: relative;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.slider-content-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, rgba(0,0,0,0.3), rgba(0,0,0,0.1));
    display: flex;
    align-items: center;
}

.slider-title {
    font-size: 2.5rem;
    font-weight: bold;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
}

.slider-description {
    font-size: 1.2rem;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid #f0f0f0;
}

.info-item:last-child {
    border-bottom: none;
}

.info-item label {
    font-weight: 600;
    color: #666;
    margin: 0;
}

.info-item span {
    color: #333;
}
</style>
@endpush
@endsection
