@extends('layouts.dashboard-new')

@section('title', 'إدارة الفئات - متجر البطاقات الرقمية')

@section('page-title', 'إدارة الفئات')
@section('page-subtitle', 'إدارة جميع فئات المنتجات في النظام')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="page-title">إدارة الفئات</h3>
            <p class="page-subtitle">إدارة جميع فئات المنتجات في النظام</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('dashboard.categories.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>
                إضافة فئة جديدة
            </a>
            <a href="{{ route('dashboard.categories.export') }}" class="btn btn-outline-primary">
                <i class="bi bi-download me-2"></i>
                تصدير البيانات
            </a>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="stats-grid">
    <x-dashboard.stats-card
        title="إجمالي الفئات"
        :value="number_format($stats['total_categories'])"
        icon="bi-tags"
        change-type="positive"
        change-text="+3.2% من الشهر الماضي" />

    <x-dashboard.stats-card
        title="الفئات النشطة"
        :value="number_format($stats['active_categories'])"
        icon="bi-check-circle"
        change-type="positive"
        change-text="+5.1% من الشهر الماضي" />

    <x-dashboard.stats-card
        title="الفئات الرئيسية"
        :value="number_format($stats['parent_categories'])"
        icon="bi-folder"
        change-type="neutral"
        change-text="ثابت" />

    <x-dashboard.stats-card
        title="الفئات الفرعية"
        :value="number_format($stats['child_categories'])"
        icon="bi-folder2"
        change-type="positive"
        change-text="+8.3% من الشهر الماضي" />
</div>

<!-- Filters -->
<x-dashboard.filters
    :filters="[
        ['name' => 'status', 'label' => 'الحالة', 'type' => 'select', 'placeholder' => 'جميع الحالات', 'options' => ['active' => 'نشط', 'inactive' => 'معطل']],
        ['name' => 'type', 'label' => 'النوع', 'type' => 'select', 'placeholder' => 'جميع الأنواع', 'options' => ['parent' => 'فئة رئيسية', 'child' => 'فئة فرعية']],
        ['name' => 'sort_by', 'label' => 'ترتيب حسب', 'type' => 'select', 'placeholder' => 'ترتيب حسب', 'options' => ['sort_order' => 'ترتيب العرض', 'name' => 'الاسم', 'created_at' => 'تاريخ الإنشاء']],
        ['name' => 'sort_order', 'label' => 'اتجاه الترتيب', 'type' => 'select', 'placeholder' => 'اتجاه الترتيب', 'options' => ['asc' => 'تصاعدي', 'desc' => 'تنازلي']]
    ]"
    search-placeholder="البحث في الفئات..."
    :search-value="request('search')"
    :action-url="route('dashboard.categories.index')" />

<!-- Categories Table -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="bi bi-tags me-2"></i>
            قائمة الفئات
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>الفئة</th>
                        <th>النوع</th>
                        <th>الوصف</th>
                        <th>ترتيب العرض</th>
                        <th>الحالة</th>
                        <th>تاريخ الإنشاء</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($category->image)
                                    <img src="{{ Storage::url($category->image) }}" alt="{{ $category->name }}" class="rounded me-3" width="40" height="40">
                                @else
                                    <div class="category-icon me-3">
                                        <i class="bi bi-tag"></i>
                                    </div>
                                @endif
                                <div style="margin: 0 10px;">
                                    <h6 class="mb-0">{{ $category->name }}</h6>
                                    <small class="text-muted">{{ $category->slug }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($category->parent_id)
                                <span class="badge badge-info">فئة فرعية</span>
                                <br>
                                <small class="text-muted">{{ $category->parent->name }}</small>
                            @else
                                <span class="badge badge-primary">فئة رئيسية</span>
                            @endif
                        </td>
                        <td>
                            @if($category->description)
                                <span class="text-truncate d-inline-block" style="max-width: 200px;" title="{{ $category->description }}">
                                    {{ $category->description }}
                                </span>
                            @else
                                <span class="text-muted">لا يوجد وصف</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-secondary">{{ $category->sort_order }}</span>
                        </td>
                        <td>
                            @if($category->is_active)
                                <span class="badge badge-success">نشط</span>
                            @else
                                <span class="badge badge-danger">معطل</span>
                            @endif
                        </td>
                        <td>
                            <span class="text-muted">{{ $category->created_at->format('Y-m-d') }}</span>
                            <br>
                            <small class="text-muted">{{ $category->created_at->diffForHumans() }}</small>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('dashboard.categories.show', $category) }}" class="btn btn-sm btn-outline-primary" title="عرض">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('dashboard.categories.edit', $category) }}" class="btn btn-sm btn-outline-secondary" title="تعديل">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('dashboard.categories.toggle-status', $category) }}" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-outline-{{ $category->is_active ? 'warning' : 'success' }}" title="{{ $category->is_active ? 'تعطيل' : 'تفعيل' }}">
                                        <i class="bi bi-{{ $category->is_active ? 'pause' : 'play' }}"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('dashboard.categories.destroy', $category) }}" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه الفئة؟')">
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
                        <td colspan="7" class="text-center py-4">
                            <div class="text-muted">
                                <i class="bi bi-tags fs-1 d-block mb-3"></i>
                                <h5>لا توجد فئات</h5>
                                <p>لم يتم العثور على أي فئات مطابقة للبحث.</p>
                                <a href="{{ route('dashboard.categories.create') }}" class="btn btn-primary">
                                    <i class="bi bi-plus-circle me-2"></i>
                                    إضافة فئة جديدة
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($categories->hasPages())
    <div class="card-footer">
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted">
                عرض {{ $categories->firstItem() }} إلى {{ $categories->lastItem() }} من {{ $categories->total() }} فئة
            </div>
            <div>
                {{ $categories->links() }}
            </div>
        </div>
    </div>
    @endif
</div>

@push('styles')
<style>
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

.badge {
    font-size: 0.75rem;
}

.btn-group .btn {
    margin-left: 2px;
}

.btn-group .btn:first-child {
    margin-left: 0;
}

.text-truncate {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>
@endpush
@endsection
