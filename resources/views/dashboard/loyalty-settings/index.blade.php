@extends('layouts.dashboard-new')

@section('title', 'إعدادات نقاط الولاء - متجر البطاقات الرقمية')

@section('page-title', 'إعدادات نقاط الولاء')
@section('page-subtitle', 'إدارة الإعدادات العامة لنظام نقاط الولاء')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="page-title">إعدادات نقاط الولاء</h3>
            <p class="page-subtitle">إدارة الإعدادات العامة لنظام نقاط الولاء</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('dashboard.loyalty-settings.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>
                إضافة إعداد جديد
            </a>
            <div class="btn-group">
                <button type="button" class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="bi bi-download me-2"></i>
                    تصدير/استيراد
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('dashboard.loyalty-settings.export') }}">
                        <i class="bi bi-download me-2"></i>تصدير الإعدادات
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('dashboard.loyalty-settings.import') }}" enctype="multipart/form-data" class="d-inline">
                            @csrf
                            <label class="dropdown-item" style="cursor: pointer;">
                                <i class="bi bi-upload me-2"></i>استيراد الإعدادات
                                <input type="file" name="settings_file" accept=".json" style="display: none;" onchange="this.form.submit()">
                            </label>
                        </form>
                    </li>
                </ul>
            </div>
            <form method="POST" action="{{ route('dashboard.loyalty-settings.reset-defaults') }}" class="d-inline" onsubmit="return confirm('هل أنت متأكد من إعادة تعيين جميع الإعدادات إلى القيم الافتراضية؟')">
                @csrf
                <button type="submit" class="btn btn-outline-warning">
                    <i class="bi bi-arrow-clockwise me-2"></i>
                    إعادة تعيين
                </button>
            </form>
        </div>
    </div>
</div>

<!-- إحصائيات سريعة -->
<div class="stats-grid mb-4">
    <x-dashboard.stats-card
        title="إجمالي الإعدادات"
        :value="collect($settingsByCategory)->sum(fn($category) => $category['settings']->count())"
        icon="bi-gear"
        change-type="info"
        change-text="إعدادات النظام" />

    <x-dashboard.stats-card
        title="الإعدادات النشطة"
        :value="collect($settingsByCategory)->sum(fn($category) => $category['settings']->where('is_active', true)->count())"
        icon="bi-check-circle"
        change-type="success"
        change-text="إعدادات مفعلة" />

    <x-dashboard.stats-card
        title="الإعدادات القابلة للتعديل"
        :value="collect($settingsByCategory)->sum(fn($category) => $category['settings']->where('is_editable', true)->count())"
        icon="bi-pencil"
        change-type="warning"
        change-text="قابلة للتعديل" />

    <x-dashboard.stats-card
        title="فئات الإعدادات"
        :value="count($settingsByCategory)"
        icon="bi-folder"
        change-type="primary"
        change-text="فئات مختلفة" />
</div>

<!-- الإعدادات حسب الفئات -->
@foreach($settingsByCategory as $categoryKey => $category)
@if($category['settings']->count() > 0)
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="bi bi-{{ $categoryKey === 'values' ? 'currency-dollar' : ($categoryKey === 'expiry' ? 'clock' : ($categoryKey === 'bonuses' ? 'gift' : ($categoryKey === 'system' ? 'cpu' : 'gear'))) }} me-2"></i>
            {{ $category['name'] }}
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>مفتاح الإعداد</th>
                        <th>القيمة</th>
                        <th>النوع</th>
                        <th>الوصف</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($category['settings'] as $setting)
                    <tr>
                        <td>
                            <code class="text-primary">{{ $setting->setting_key }}</code>
                        </td>
                        <td>
                            @if($setting->setting_type === 'boolean')
                                <span class="badge badge-{{ $setting->setting_value ? 'success' : 'danger' }}">
                                    {{ $setting->setting_value ? 'نعم' : 'لا' }}
                                </span>
                            @elseif($setting->setting_type === 'decimal')
                                <span class="fw-semibold">{{ number_format($setting->setting_value, 4) }}</span>
                            @elseif($setting->setting_type === 'integer')
                                <span class="fw-semibold">{{ number_format($setting->setting_value) }}</span>
                            @else
                                <span class="text-muted">{{ Str::limit($setting->setting_value, 50) }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-info">{{ $setting->setting_type }}</span>
                        </td>
                        <td>
                            <span class="text-muted">{{ Str::limit($setting->description ?? 'لا يوجد وصف', 40) }}</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @if($setting->is_active)
                                    <span class="badge badge-success">نشط</span>
                                @else
                                    <span class="badge badge-secondary">غير نشط</span>
                                @endif

                                @if($setting->is_editable)
                                    <span class="badge badge-warning">قابل للتعديل</span>
                                @else
                                    <span class="badge badge-danger">غير قابل للتعديل</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('dashboard.loyalty-settings.show', $setting) }}" class="btn btn-sm btn-outline-primary" title="عرض التفاصيل">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($setting->is_editable)
                                    <a href="{{ route('dashboard.loyalty-settings.edit', $setting) }}" class="btn btn-sm btn-outline-secondary" title="تعديل">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                @endif
                                <form method="POST" action="{{ route('dashboard.loyalty-settings.toggle-status', $setting) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-info" title="{{ $setting->is_active ? 'إلغاء تفعيل' : 'تفعيل' }}">
                                        <i class="bi bi-{{ $setting->is_active ? 'pause' : 'play' }}"></i>
                                    </button>
                                </form>
                                @if($setting->is_editable)
                                    <form method="POST" action="{{ route('dashboard.loyalty-settings.destroy', $setting) }}" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الإعداد؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
@endforeach

@if(collect($settingsByCategory)->sum(fn($category) => $category['settings']->count()) === 0)
<div class="card">
    <div class="card-body text-center py-5">
        <div class="text-muted">
            <i class="bi bi-gear fs-1 d-block mb-3"></i>
            <h5>لا توجد إعدادات</h5>
            <p>لم يتم العثور على أي إعدادات لنظام نقاط الولاء.</p>
            <a href="{{ route('dashboard.loyalty-settings.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>
                إضافة إعداد جديد
            </a>
        </div>
    </div>
</div>
@endif

@push('styles')
<style>
code {
    background: rgba(var(--primary-purple-rgb), 0.1);
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 0.85rem;
}

.badge {
    font-size: 0.75rem;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: var(--text-dark);
}

.btn-group .btn {
    border-radius: 4px;
}

.btn-group .btn:not(:last-child) {
    margin-left: 2px;
}
</style>
@endpush
@endsection



