@extends('layouts.dashboard-new')

@section('title', 'إدارة السلايدرات - متجر البطاقات الرقمية')

@section('page-title', 'إدارة السلايدرات')
@section('page-subtitle', 'إدارة السلايدرات والعروض الترويجية')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="page-title">إدارة السلايدرات</h3>
            <p class="page-subtitle">إدارة السلايدرات والعروض الترويجية</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('dashboard.sliders.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-2"></i>
                إضافة سلايدر جديد
            </a>
        </div>
    </div>
</div>

<!-- الفلاتر -->
@include('components.dashboard.filters', [
    'filters' => [
        [
            'name' => 'status',
            'label' => 'الحالة',
            'type' => 'select',
            'placeholder' => 'جميع الحالات',
            'options' => [
                'active' => 'نشط',
                'inactive' => 'غير نشط'
            ]
        ],
        [
            'name' => 'position',
            'label' => 'الموقع',
            'type' => 'select',
            'placeholder' => 'جميع المواقع',
            'options' => [
                'homepage' => 'الصفحة الرئيسية',
                'category' => 'صفحات الفئات',
                'product' => 'صفحات المنتجات',
                'footer' => 'الفوتر'
            ]
        ]
    ],
    'searchPlaceholder' => 'البحث في السلايدرات...',
    'searchValue' => request('search'),
    'actionUrl' => route('dashboard.sliders.index')
])

<!-- قائمة السلايدرات -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="bi bi-images me-2"></i>
            السلايدرات ({{ $sliders->total() }})
        </h5>
    </div>
    <div class="card-body p-0">
        @if($sliders->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="80">الصورة</th>
                        <th>العنوان</th>
                        <th>الموقع</th>
                        <th>الترتيب</th>
                        <th>الحالة</th>
                        <th>تاريخ الإنشاء</th>
                        <th width="200">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sliders as $slider)
                    <tr>
                        <td>
                            <div class="slider-image-preview">
                                <img src="{{ $slider->image_url }}" alt="{{ $slider->title }}"
                                     class="rounded" width="60" height="40" style="object-fit: cover;">
                            </div>
                        </td>
                        <td>
                            <div>
                                <h6 class="mb-1">{{ $slider->title }}</h6>
                                @if($slider->description)
                                    <small class="text-muted">{{ Str::limit($slider->description, 50) }}</small>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-info">
                                @switch($slider->position)
                                    @case('homepage') الصفحة الرئيسية @break
                                    @case('category') صفحات الفئات @break
                                    @case('product') صفحات المنتجات @break
                                    @case('footer') الفوتر @break
                                    @default {{ $slider->position }}
                                @endswitch
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ $slider->sort_order }}</span>
                        </td>
                        <td>
                            @if($slider->is_active)
                                <span class="badge bg-success">نشط</span>
                            @else
                                <span class="badge bg-danger">غير نشط</span>
                            @endif
                        </td>
                        <td>
                            <small class="text-muted">{{ $slider->created_at->format('Y-m-d') }}</small>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('dashboard.sliders.show', $slider) }}"
                                   class="btn btn-outline-info" title="عرض">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('dashboard.sliders.edit', $slider) }}"
                                   class="btn btn-outline-primary" title="تعديل">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('dashboard.sliders.toggle-status', $slider) }}"
                                      method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit"
                                            class="btn btn-outline-{{ $slider->is_active ? 'warning' : 'success' }}"
                                            title="{{ $slider->is_active ? 'إلغاء التفعيل' : 'تفعيل' }}">
                                        <i class="bi bi-{{ $slider->is_active ? 'pause' : 'play' }}"></i>
                                    </button>
                                </form>
                                <form action="{{ route('dashboard.sliders.destroy', $slider) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('هل أنت متأكد من حذف هذا السلايدر؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="حذف">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-5">
            <i class="bi bi-images text-muted" style="font-size: 3rem;"></i>
            <h5 class="text-muted mt-3">لا توجد سلايدرات</h5>
            <p class="text-muted">ابدأ بإنشاء سلايدر جديد لعرض العروض الترويجية</p>
            <a href="{{ route('dashboard.sliders.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-2"></i>
                إضافة سلايدر جديد
            </a>
        </div>
        @endif
    </div>

    @if($sliders->hasPages())
    <div class="card-footer">
        {{ $sliders->links() }}
    </div>
    @endif
</div>

@push('styles')
<style>
.slider-image-preview img {
    border: 1px solid #dee2e6;
    transition: transform 0.2s;
}

.slider-image-preview img:hover {
    transform: scale(1.1);
}

.table td {
    vertical-align: middle;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
}
</style>
@endpush
@endsection
