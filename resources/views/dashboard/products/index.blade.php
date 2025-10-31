@extends('layouts.dashboard-new')

@section('title', 'إدارة المنتجات - متجر البطاقات الرقمية')

@section('page-title', 'إدارة المنتجات')
@section('page-subtitle', 'إدارة جميع المنتجات في النظام')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="page-title">إدارة المنتجات</h3>
            <p class="page-subtitle">إدارة جميع المنتجات في النظام</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('dashboard.products.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>
                إضافة منتج جديد
            </a>
            <a href="{{ route('dashboard.products.export') }}" class="btn btn-outline-primary">
                <i class="bi bi-download me-2"></i>
                تصدير البيانات
            </a>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="stats-grid">
    <x-dashboard.stats-card
        title="إجمالي المنتجات"
        :value="number_format($stats['total_products'])"
        icon="bi-box-seam"
        change-type="positive"
        change-text="جميع المنتجات" />

    <x-dashboard.stats-card
        title="المنتجات النشطة"
        :value="number_format($stats['active_products'])"
        icon="bi-check-circle"
        change-type="positive"
        change-text="نشطة" />

    <x-dashboard.stats-card
        title="المنتجات الرقمية"
        :value="number_format($stats['digital_products'])"
        icon="bi-laptop"
        change-type="positive"
        change-text="رقمية" />

    <x-dashboard.stats-card
        title="المنتجات المميزة"
        :value="number_format($stats['featured_products'])"
        icon="bi-star"
        change-type="neutral"
        change-text="مميزة" />

    <x-dashboard.stats-card
        title="تسليم فوري"
        :value="number_format($stats['instant_delivery_products'])"
        icon="bi-lightning"
        change-type="success"
        change-text="فوري" />

    <x-dashboard.stats-card
        title="مع نقاط ولاء"
        :value="number_format($stats['products_with_loyalty_points'])"
        icon="bi-gift"
        change-type="primary"
        change-text="نقاط مكتسبة" />

    <x-dashboard.stats-card
        title="قابلة للشراء بنقاط"
        :value="number_format($stats['purchasable_with_points'])"
        icon="bi-coin"
        change-type="info"
        change-text="بنقاط الولاء" />

    <x-dashboard.stats-card
        title="أنواع البطاقات"
        :value="number_format($stats['card_types_count'])"
        icon="bi-grid"
        change-type="neutral"
        change-text="أنواع مختلفة" />

    <x-dashboard.stats-card
        title="مزودي البطاقات"
        :value="number_format($stats['card_providers_count'])"
        icon="bi-building"
        change-type="success"
        change-text="مزودين" />
</div>

<!-- Filters -->
<x-dashboard.filters
    :filters="[
        ['name' => 'category', 'label' => 'الفئة', 'type' => 'select', 'placeholder' => 'جميع الفئات', 'options' => $categories->pluck('name', 'id')->toArray()],
        ['name' => 'status', 'label' => 'الحالة', 'type' => 'select', 'placeholder' => 'جميع الحالات', 'options' => ['active' => 'نشط', 'inactive' => 'معطل']],
        ['name' => 'type', 'label' => 'النوع', 'type' => 'select', 'placeholder' => 'جميع الأنواع', 'options' => ['digital' => 'رقمي', 'physical' => 'مادي']],
        ['name' => 'featured', 'label' => 'مميز', 'type' => 'select', 'placeholder' => 'جميع المنتجات', 'options' => ['yes' => 'مميز', 'no' => 'غير مميز']],
        ['name' => 'card_type', 'label' => 'نوع البطاقة', 'type' => 'select', 'placeholder' => 'جميع الأنواع', 'options' => ['gift_card' => 'بطاقة هدايا', 'gaming' => 'ألعاب', 'subscription' => 'اشتراكات', 'entertainment' => 'ترفيه']],
        ['name' => 'card_provider', 'label' => 'مزود البطاقة', 'type' => 'select', 'placeholder' => 'جميع المزودين', 'options' => ['amazon' => 'أمازون', 'steam' => 'ستيم', 'netflix' => 'نتفليكس', 'spotify' => 'سبوتيفاي']],
        ['name' => 'loyalty_points', 'label' => 'نقاط الولاء', 'type' => 'select', 'placeholder' => 'جميع المنتجات', 'options' => ['earn' => 'يكسب نقاط', 'cost' => 'يشتري بنقاط']],
        ['name' => 'instant_delivery', 'label' => 'تسليم فوري', 'type' => 'select', 'placeholder' => 'جميع المنتجات', 'options' => ['yes' => 'فوري', 'no' => 'غير فوري']],
        ['name' => 'sort_by', 'label' => 'ترتيب حسب', 'type' => 'select', 'placeholder' => 'ترتيب حسب', 'options' => ['created_at' => 'تاريخ الإنشاء', 'name' => 'الاسم', 'price' => 'السعر', 'loyalty_points_earn' => 'نقاط الولاء']],
        ['name' => 'sort_order', 'label' => 'اتجاه الترتيب', 'type' => 'select', 'placeholder' => 'اتجاه الترتيب', 'options' => ['desc' => 'تنازلي', 'asc' => 'تصاعدي']]
    ]"
    search-placeholder="البحث في المنتجات..."
    :search-value="request('search')"
    :action-url="route('dashboard.products.index')" />

<!-- Products Table -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="bi bi-box-seam me-2"></i>
            قائمة المنتجات
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>المنتج</th>
                        <th>SKU</th>
                        <th>الفئة</th>
                        <th>السعر</th>
                        <th>نوع البطاقة</th>
                        <th>النوع</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($product->image)
                                    <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="rounded me-3" width="50" height="50">
                                @else
                                    <div class="product-icon me-3">
                                        <i class="bi bi-box"></i>
                                    </div>
                                @endif
                                <div>
                                    <h6 class="mb-0">{{ $product->name }}</h6>
                                    <small class="text-muted">{{ $product->brand ?? 'بدون علامة تجارية' }}</small>
                                    @if($product->is_featured)
                                        <i class="bi bi-star-fill text-warning ms-1" title="منتج مميز"></i>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="text-primary font-monospace">{{ $product->sku }}</span>
                        </td>
                        <td>
                            <span class="badge badge-primary">{{ $product->category->name }}</span>
                        </td>
                        <td>
                            <div>
                                @if($product->sale_price && $product->sale_price < $product->price)
                                    <span class="text-success fw-bold">{{ number_format($product->sale_price, 2) }} $</span>
                                    <br>
                                    <small class="text-muted text-decoration-line-through">{{ number_format($product->price, 2) }} $</small>
                                @else
                                    <span class="fw-bold">{{ number_format($product->price, 2) }} $</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($product->card_type)
                                <span class="badge badge-info">{{ ucfirst($product->card_type) }}</span>
                            @else
                                <span class="text-muted">غير محدد</span>
                            @endif
                        </td>
                        <td>
                            @if($product->is_digital)
                                <span class="badge badge-info">رقمي</span>
                            @else
                                <span class="badge badge-secondary">مادي</span>
                            @endif
                        </td>
                        <td>
                            @if($product->is_active)
                                <span class="badge badge-success">نشط</span>
                            @else
                                <span class="badge badge-danger">معطل</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('dashboard.products.show', $product) }}" class="btn btn-sm btn-outline-primary" title="عرض">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('dashboard.products.edit', $product) }}" class="btn btn-sm btn-outline-secondary" title="تعديل">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('dashboard.products.toggle-status', $product) }}" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-outline-{{ $product->is_active ? 'warning' : 'success' }}" title="{{ $product->is_active ? 'تعطيل' : 'تفعيل' }}">
                                        <i class="bi bi-{{ $product->is_active ? 'pause' : 'play' }}"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('dashboard.products.toggle-featured', $product) }}" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-outline-{{ $product->is_featured ? 'warning' : 'info' }}" title="{{ $product->is_featured ? 'إلغاء تمييز' : 'تمييز' }}">
                                        <i class="bi bi-star{{ $product->is_featured ? '-fill' : '' }}"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('dashboard.products.destroy', $product) }}" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا المنتج؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <div class="text-muted">
                                <i class="bi bi-box-seam fs-1 d-block mb-3"></i>
                                <h5>لا توجد منتجات</h5>
                                <p>لم يتم العثور على أي منتجات مطابقة للبحث.</p>
                                <a href="{{ route('dashboard.products.create') }}" class="btn btn-primary">
                                    <i class="bi bi-plus-circle me-2"></i>
                                    إضافة منتج جديد
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($products->hasPages())
    <div class="card-footer">
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted">
                عرض {{ $products->firstItem() }} إلى {{ $products->lastItem() }} من {{ $products->total() }} منتج
            </div>
            <div>
                {{ $products->links() }}
            </div>
        </div>
    </div>
    @endif
</div>

@push('styles')
<style>
.product-icon {
    width: 50px;
    height: 50px;
    border-radius: 8px;
    background: linear-gradient(135deg, var(--primary-purple) 0%, var(--dark-purple) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 20px;
}

.badge {
    font-size: 0.75rem;
}

.btn-group .btn {
    margin-left: 2px;
}

.btn-group .btn:first-child {
    margin-left: 0;
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
