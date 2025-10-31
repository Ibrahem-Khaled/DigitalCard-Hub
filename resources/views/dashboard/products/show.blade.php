@extends('layouts.dashboard-new')

@section('title', 'عرض المنتج - ' . $product->name . ' - متجر البطاقات الرقمية')

@section('page-title', 'عرض المنتج')
@section('page-subtitle', 'تفاصيل المنتج: ' . $product->name)

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="page-title">عرض المنتج</h3>
            <p class="page-subtitle">تفاصيل المنتج: {{ $product->name }}</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('dashboard.products.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-right me-2"></i>
                العودة للقائمة
            </a>
            <a href="{{ route('dashboard.products.edit', $product) }}" class="btn btn-primary">
                <i class="bi bi-pencil me-2"></i>
                تعديل المنتج
            </a>
        </div>
    </div>
</div>

<div class="row">
    <!-- معلومات المنتج الأساسية -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body text-center">
                <!-- صورة المنتج -->
                <div class="product-image-large mb-3">
                    @if($product->image)
                        <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="rounded" width="200" height="200">
                    @else
                        <div class="product-icon-large">
                            <i class="bi bi-box"></i>
                        </div>
                    @endif
                </div>

                <!-- الاسم والحالة -->
                <h4 class="mb-1">{{ $product->name }}</h4>
                <p class="text-muted mb-3">{{ $product->sku }}</p>

                <!-- الحالة -->
                <div class="mb-3">
                    @if($product->is_active)
                        <span class="badge badge-success fs-6">نشط</span>
                    @else
                        <span class="badge badge-danger fs-6">معطل</span>
                    @endif

                    @if($product->is_featured)
                        <span class="badge badge-warning fs-6 ms-2">مميز</span>
                    @endif

                    @if($product->is_digital)
                        <span class="badge badge-info fs-6 ms-2">رقمي</span>
                    @else
                        <span class="badge badge-secondary fs-6 ms-2">مادي</span>
                    @endif
                </div>

                <!-- السعر -->
                <div class="mb-3">
                    @if($product->sale_price && $product->sale_price < $product->price)
                        <h3 class="text-success mb-1">{{ number_format($product->sale_price, 2) }} $</h3>
                        <small class="text-muted text-decoration-line-through">{{ number_format($product->price, 2) }} $</small>
                    @else
                        <h3 class="text-primary mb-1">{{ number_format($product->price, 2) }} $</h3>
                    @endif
                </div>

                <!-- الإجراءات السريعة -->
                <div class="d-grid gap-2">
                    <form method="POST" action="{{ route('dashboard.products.toggle-status', $product) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-{{ $product->is_active ? 'warning' : 'success' }} btn-sm">
                            <i class="bi bi-{{ $product->is_active ? 'pause' : 'play' }} me-2"></i>
                            {{ $product->is_active ? 'تعطيل' : 'تفعيل' }}
                        </button>
                    </form>

                    <form method="POST" action="{{ route('dashboard.products.toggle-featured', $product) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-{{ $product->is_featured ? 'warning' : 'info' }} btn-sm">
                            <i class="bi bi-star{{ $product->is_featured ? '-fill' : '' }} me-2"></i>
                            {{ $product->is_featured ? 'إلغاء تمييز' : 'تمييز' }}
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
                    <label>SKU:</label>
                    <span class="font-monospace">{{ $product->sku }}</span>
                </div>

                <div class="info-item">
                    <label>الفئة:</label>
                    <span>{{ $product->category->name }}</span>
                </div>

                @if($product->brand)
                <div class="info-item">
                    <label>العلامة التجارية:</label>
                    <span>{{ $product->brand }}</span>
                </div>
                @endif

                <div class="info-item">
                    <label>تاريخ الإنشاء:</label>
                    <span>{{ $product->created_at->format('Y-m-d H:i:s') }}</span>
                </div>

                <div class="info-item">
                    <label>آخر تحديث:</label>
                    <span>{{ $product->updated_at->format('Y-m-d H:i:s') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- التفاصيل الإضافية -->
    <div class="col-lg-8">
        <!-- الوصف -->
        @if($product->description)
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-file-text me-2"></i>
                    الوصف الكامل
                </h5>
            </div>
            <div class="card-body">
                <p class="mb-0">{{ $product->description }}</p>
            </div>
        </div>
        @endif

        <!-- الوصف المختصر -->
        @if($product->short_description)
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-card-text me-2"></i>
                    الوصف المختصر
                </h5>
            </div>
            <div class="card-body">
                <p class="mb-0">{{ $product->short_description }}</p>
            </div>
        </div>
        @endif

        <!-- معرض الصور -->
        @if($product->gallery && count($product->gallery) > 0)
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-images me-2"></i>
                    معرض الصور
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($product->gallery as $image)
                    <div class="col-md-4 mb-3">
                        <img src="{{ Storage::url($image) }}" alt="{{ $product->name }}" class="img-fluid rounded">
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- معلومات البطاقة الرقمية -->
        <div class="row mt-3">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-gem me-2"></i>
                            نظام نقاط الولاء
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="info-item">
                            <label>النقاط المكتسبة:</label>
                            <span class="fw-bold text-primary">{{ $product->loyalty_points_earn ?? 0 }} نقطة</span>
                        </div>
                        <div class="info-item">
                            <label>النقاط المطلوبة:</label>
                            <span class="fw-bold text-success">{{ $product->loyalty_points_cost ?? 0 }} نقطة</span>
                        </div>
                        <div class="info-item">
                            <label>قابل للشراء بنقاط:</label>
                            @if(($product->loyalty_points_cost ?? 0) > 0)
                                <span class="badge badge-success">نعم</span>
                            @else
                                <span class="badge badge-secondary">لا</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-wallet2 me-2"></i>
                            معلومات البطاقة
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($product->card_type)
                        <div class="info-item">
                            <label>نوع البطاقة:</label>
                            <span class="badge badge-info">{{ ucfirst($product->card_type) }}</span>
                        </div>
                        @endif

                        @if($product->card_provider)
                        <div class="info-item">
                            <label>مزود البطاقة:</label>
                            <span>{{ $product->card_provider }}</span>
                        </div>
                        @endif

                        @if($product->card_region)
                        <div class="info-item">
                            <label>المنطقة:</label>
                            <span>{{ $product->card_region }}</span>
                        </div>
                        @endif

                        <div class="info-item">
                            <label>تسليم فوري:</label>
                            @if($product->is_instant_delivery ?? true)
                                <span class="badge badge-success">نعم</span>
                            @else
                                <span class="badge badge-warning">لا</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- الفئات المتاحة -->
        @if($product->card_denominations && count($product->card_denominations) > 0)
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-list-ul me-2"></i>
                    الفئات المتاحة
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($product->card_denominations as $denomination)
                    <div class="col-md-3 mb-2">
                        <span class="badge badge-primary fs-6">{{ $denomination }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- تعليمات التسليم -->
        @if($product->delivery_instructions)
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-truck me-2"></i>
                    تعليمات التسليم
                </h5>
            </div>
            <div class="card-body">
                <p class="mb-0">{{ $product->delivery_instructions }}</p>
            </div>
        </div>
        @endif

        <!-- معلومات SEO -->
        @if($product->meta_title || $product->meta_description || $product->tags)
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-search me-2"></i>
                    معلومات SEO
                </h5>
            </div>
            <div class="card-body">
                @if($product->meta_title)
                <div class="info-item">
                    <label>عنوان SEO:</label>
                    <span>{{ $product->meta_title }}</span>
                </div>
                @endif

                @if($product->meta_description)
                <div class="info-item">
                    <label>وصف SEO:</label>
                    <span>{{ $product->meta_description }}</span>
                </div>
                @endif

                @if($product->tags && count($product->tags) > 0)
                <div class="info-item">
                    <label>العلامات:</label>
                    <div>
                        @foreach($product->tags as $tag)
                            <span class="badge badge-secondary me-1">{{ $tag }}</span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- الإحصائيات -->
        <div class="row mt-3">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="bi bi-star fs-1 text-warning mb-2"></i>
                        <h4>{{ $product->reviews->count() }}</h4>
                        <p class="text-muted mb-0">تقييم</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="bi bi-cart-plus fs-1 text-primary mb-2"></i>
                        <h4>{{ $product->cartItems->count() }}</h4>
                        <p class="text-muted mb-0">في السلة</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="bi bi-receipt fs-1 text-success mb-2"></i>
                        <h4>{{ $product->orderItems->count() }}</h4>
                        <p class="text-muted mb-0">طلب</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.product-image-large .product-icon-large {
    width: 200px;
    height: 200px;
    border-radius: 12px;
    background: linear-gradient(135deg, var(--primary-purple) 0%, var(--dark-purple) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 80px;
    margin: 0 auto;
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

.font-monospace {
    font-family: 'Courier New', monospace;
}

.text-decoration-line-through {
    text-decoration: line-through;
}
</style>
@endpush
@endsection
