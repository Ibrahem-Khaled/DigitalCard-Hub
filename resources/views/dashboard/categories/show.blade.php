@extends('layouts.dashboard-new')

@section('title', 'عرض الفئة - ' . $category->name . ' - متجر البطاقات الرقمية')

@section('page-title', 'عرض الفئة')
@section('page-subtitle', 'تفاصيل الفئة: ' . $category->name)

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="page-title">عرض الفئة</h3>
            <p class="page-subtitle">تفاصيل الفئة: {{ $category->name }}</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('dashboard.categories.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-right me-2"></i>
                العودة للقائمة
            </a>
            <a href="{{ route('dashboard.categories.edit', $category) }}" class="btn btn-primary">
                <i class="bi bi-pencil me-2"></i>
                تعديل الفئة
            </a>
        </div>
    </div>
</div>

<div class="row">
    <!-- معلومات الفئة الأساسية -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body text-center">
                <!-- صورة الفئة -->
                <div class="category-image-large mb-3">
                    @if($category->image)
                        <img src="{{ Storage::url($category->image) }}" alt="{{ $category->name }}" class="rounded" width="120" height="120">
                    @else
                        <div class="category-icon-large">
                            <i class="bi bi-tag"></i>
                        </div>
                    @endif
                </div>

                <!-- الاسم والحالة -->
                <h4 class="mb-1">{{ $category->name }}</h4>
                <p class="text-muted mb-3">{{ $category->slug }}</p>

                <!-- الحالة -->
                <div class="mb-3">
                    @if($category->is_active)
                        <span class="badge badge-success fs-6">نشط</span>
                    @else
                        <span class="badge badge-danger fs-6">معطل</span>
                    @endif
                </div>

                <!-- النوع -->
                <div class="mb-3">
                    @if($category->parent_id)
                        <span class="badge badge-info fs-6">فئة فرعية</span>
                    @else
                        <span class="badge badge-primary fs-6">فئة رئيسية</span>
                    @endif
                </div>

                <!-- الإجراءات السريعة -->
                <div class="d-grid gap-2">
                    <form method="POST" action="{{ route('dashboard.categories.toggle-status', $category) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-{{ $category->is_active ? 'warning' : 'success' }} btn-sm">
                            <i class="bi bi-{{ $category->is_active ? 'pause' : 'play' }} me-2"></i>
                            {{ $category->is_active ? 'تعطيل' : 'تفعيل' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- معلومات إضافية -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    معلومات إضافية
                </h5>
            </div>
            <div class="card-body">
                <div class="info-item">
                    <label>الرابط:</label>
                    <span>{{ $category->slug }}</span>
                </div>

                <div class="info-item">
                    <label>ترتيب العرض:</label>
                    <span>{{ $category->sort_order }}</span>
                </div>

                @if($category->parent)
                <div class="info-item">
                    <label>الفئة الرئيسية:</label>
                    <span>{{ $category->parent->name }}</span>
                </div>
                @endif

                <div class="info-item">
                    <label>تاريخ الإنشاء:</label>
                    <span>{{ $category->created_at->format('Y-m-d H:i:s') }}</span>
                </div>

                <div class="info-item">
                    <label>آخر تحديث:</label>
                    <span>{{ $category->updated_at->format('Y-m-d H:i:s') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- التفاصيل الإضافية -->
    <div class="col-lg-8">
        <!-- الوصف -->
        @if($category->description)
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-file-text me-2"></i>
                    الوصف
                </h5>
            </div>
            <div class="card-body">
                <p class="mb-0">{{ $category->description }}</p>
            </div>
        </div>
        @endif

        <!-- الفئات الفرعية -->
        @if($category->children->count() > 0)
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-folder2 me-2"></i>
                    الفئات الفرعية
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($category->children as $child)
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-center p-3 border rounded">
                            @if($child->image)
                                <img src="{{ Storage::url($child->image) }}" alt="{{ $child->name }}" class="rounded me-3" width="40" height="40">
                            @else
                                <div class="category-icon me-3">
                                    <i class="bi bi-tag"></i>
                                </div>
                            @endif
                            <div class="flex-grow-1">
                                <h6 class="mb-0">{{ $child->name }}</h6>
                                <small class="text-muted">{{ $child->slug }}</small>
                            </div>
                            <div class="ms-2">
                                @if($child->is_active)
                                    <span class="badge badge-success">نشط</span>
                                @else
                                    <span class="badge badge-danger">معطل</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- المنتجات -->
        @if($category->products->count() > 0)
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-box-seam me-2"></i>
                    المنتجات في هذه الفئة
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($category->products->take(6) as $product)
                    <div class="col-md-4 mb-3">
                        <div class="d-flex align-items-center p-3 border rounded">
                            @if($product->image)
                                <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="rounded me-3" width="40" height="40">
                            @else
                                <div class="product-icon me-3">
                                    <i class="bi bi-box"></i>
                                </div>
                            @endif
                            <div class="flex-grow-1">
                                <h6 class="mb-0">{{ $product->name }}</h6>
                                <small class="text-muted">{{ number_format($product->price, 2) }} $</small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @if($category->products->count() > 6)
                <div class="text-center mt-3">
                    <small class="text-muted">و {{ $category->products->count() - 6 }} منتج آخر</small>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- الإحصائيات -->
        <div class="row mt-3">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="bi bi-folder2 fs-1 text-primary mb-2"></i>
                        <h4>{{ $category->children->count() }}</h4>
                        <p class="text-muted mb-0">فئة فرعية</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="bi bi-box-seam fs-1 text-success mb-2"></i>
                        <h4>{{ $category->products->count() }}</h4>
                        <p class="text-muted mb-0">منتج</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.category-image-large .category-icon-large {
    width: 120px;
    height: 120px;
    border-radius: 12px;
    background: linear-gradient(135deg, var(--primary-purple) 0%, var(--dark-purple) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 48px;
    margin: 0 auto;
}

.category-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    background: linear-gradient(135deg, var(--primary-purple) 0%, var(--dark-purple) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 16px;
}

.product-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    background: linear-gradient(135deg, var(--success-color) 0%, #059669 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 16px;
}

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

.badge {
    font-size: 0.875rem;
}
</style>
@endpush
@endsection
