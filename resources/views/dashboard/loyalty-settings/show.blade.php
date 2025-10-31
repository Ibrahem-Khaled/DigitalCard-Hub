@extends('layouts.dashboard-new')

@section('title', 'تفاصيل إعداد نقاط الولاء - متجر البطاقات الرقمية')

@section('page-title', 'تفاصيل إعداد نقاط الولاء')
@section('page-subtitle', 'عرض تفاصيل إعداد موجود في نظام نقاط الولاء')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="page-title">تفاصيل إعداد نقاط الولاء</h3>
            <p class="page-subtitle">عرض تفاصيل إعداد موجود في نظام نقاط الولاء</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('dashboard.loyalty-settings.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-right me-2"></i>
                العودة للقائمة
            </a>
            @if($loyaltySetting->is_editable)
                <a href="{{ route('dashboard.loyalty-settings.edit', $loyaltySetting) }}" class="btn btn-primary">
                    <i class="bi bi-pencil me-2"></i>
                    تعديل الإعداد
                </a>
            @endif
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- معلومات الإعداد الأساسية -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    معلومات الإعداد الأساسية
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-group">
                            <label class="info-label">مفتاح الإعداد</label>
                            <div class="info-value">
                                <code class="setting-key">{{ $loyaltySetting->setting_key }}</code>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-group">
                            <label class="info-label">فئة الإعداد</label>
                            <div class="info-value">
                                <span class="badge badge-primary">{{ $loyaltySetting->category }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="info-group">
                            <label class="info-label">نوع الإعداد</label>
                            <div class="info-value">
                                <span class="badge badge-info">{{ $loyaltySetting->setting_type }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-group">
                            <label class="info-label">ترتيب الإعداد</label>
                            <div class="info-value">
                                <span class="fw-semibold">{{ $loyaltySetting->sort_order }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="info-group">
                    <label class="info-label">قيمة الإعداد</label>
                    <div class="info-value">
                        @if($loyaltySetting->setting_type === 'boolean')
                            <span class="badge badge-{{ $loyaltySetting->setting_value ? 'success' : 'danger' }}">
                                {{ $loyaltySetting->setting_value ? 'نعم (true)' : 'لا (false)' }}
                            </span>
                        @elseif($loyaltySetting->setting_type === 'decimal')
                            <span class="fw-semibold text-success">{{ number_format($loyaltySetting->setting_value, 4) }}</span>
                        @elseif($loyaltySetting->setting_type === 'integer')
                            <span class="fw-semibold text-success">{{ number_format($loyaltySetting->setting_value) }}</span>
                        @elseif($loyaltySetting->setting_type === 'json')
                            <pre class="json-value">{{ json_encode(json_decode($loyaltySetting->setting_value), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        @else
                            <span class="text-muted">{{ $loyaltySetting->setting_value }}</span>
                        @endif
                    </div>
                </div>

                @if($loyaltySetting->description)
                <div class="info-group">
                    <label class="info-label">وصف الإعداد</label>
                    <div class="info-value">
                        <p class="text-muted">{{ $loyaltySetting->description }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- حالة الإعداد -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-gear me-2"></i>
                    حالة الإعداد
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="status-item">
                            <div class="status-label">حالة التفعيل</div>
                            <div class="status-value">
                                @if($loyaltySetting->is_active)
                                    <span class="badge badge-success">
                                        <i class="bi bi-check-circle me-1"></i>
                                        نشط
                                    </span>
                                @else
                                    <span class="badge badge-secondary">
                                        <i class="bi bi-pause-circle me-1"></i>
                                        غير نشط
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="status-item">
                            <div class="status-label">قابلية التعديل</div>
                            <div class="status-value">
                                @if($loyaltySetting->is_editable)
                                    <span class="badge badge-warning">
                                        <i class="bi bi-pencil me-1"></i>
                                        قابل للتعديل
                                    </span>
                                @else
                                    <span class="badge badge-danger">
                                        <i class="bi bi-shield-exclamation me-1"></i>
                                        محمي من التعديل
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- معلومات إضافية -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-clock me-2"></i>
                    معلومات إضافية
                </h5>
            </div>
            <div class="card-body">
                <div class="info-item">
                    <span class="label">تاريخ الإنشاء:</span>
                    <span class="value">{{ $loyaltySetting->created_at->format('Y-m-d H:i:s') }}</span>
                </div>
                <div class="info-item">
                    <span class="label">آخر تحديث:</span>
                    <span class="value">{{ $loyaltySetting->updated_at->format('Y-m-d H:i:s') }}</span>
                </div>
                <div class="info-item">
                    <span class="label">مدة الإنشاء:</span>
                    <span class="value">{{ $loyaltySetting->created_at->diffForHumans() }}</span>
                </div>
                <div class="info-item">
                    <span class="label">آخر تحديث:</span>
                    <span class="value">{{ $loyaltySetting->updated_at->diffForHumans() }}</span>
                </div>
            </div>
        </div>

        <!-- الإجراءات -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-tools me-2"></i>
                    الإجراءات المتاحة
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if($loyaltySetting->is_editable)
                        <a href="{{ route('dashboard.loyalty-settings.edit', $loyaltySetting) }}" class="btn btn-primary">
                            <i class="bi bi-pencil me-2"></i>
                            تعديل الإعداد
                        </a>
                    @endif

                    <form method="POST" action="{{ route('dashboard.loyalty-settings.toggle-status', $loyaltySetting) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-info w-100">
                            <i class="bi bi-{{ $loyaltySetting->is_active ? 'pause' : 'play' }} me-2"></i>
                            {{ $loyaltySetting->is_active ? 'إلغاء تفعيل' : 'تفعيل' }}
                        </button>
                    </form>

                    @if($loyaltySetting->is_editable)
                        <form method="POST" action="{{ route('dashboard.loyalty-settings.destroy', $loyaltySetting) }}"
                              onsubmit="return confirm('هل أنت متأكد من حذف هذا الإعداد؟')" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="bi bi-trash me-2"></i>
                                حذف الإعداد
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('dashboard.loyalty-settings.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-right me-2"></i>
                        العودة للقائمة
                    </a>
                </div>
            </div>
        </div>

        <!-- نصائح -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-lightbulb me-2"></i>
                    نصائح مفيدة
                </h5>
            </div>
            <div class="card-body">
                @if($loyaltySetting->setting_type === 'boolean')
                    <div class="alert alert-info">
                        <h6><i class="bi bi-info-circle me-2"></i>إعداد منطقي:</h6>
                        <p class="mb-0">هذا الإعداد يقبل قيم true أو false فقط.</p>
                    </div>
                @elseif($loyaltySetting->setting_type === 'decimal')
                    <div class="alert alert-info">
                        <h6><i class="bi bi-info-circle me-2"></i>إعداد عشري:</h6>
                        <p class="mb-0">هذا الإعداد يقبل الأرقام العشرية بدقة 4 خانات عشرية.</p>
                    </div>
                @elseif($loyaltySetting->setting_type === 'json')
                    <div class="alert alert-info">
                        <h6><i class="bi bi-info-circle me-2"></i>إعداد JSON:</h6>
                        <p class="mb-0">هذا الإعداد يحتوي على بيانات JSON منظمة.</p>
                    </div>
                @endif

                @if(!$loyaltySetting->is_editable)
                    <div class="alert alert-warning">
                        <h6><i class="bi bi-shield-exclamation me-2"></i>إعداد محمي:</h6>
                        <p class="mb-0">هذا الإعداد محمي من التعديل أو الحذف.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.info-group {
    margin-bottom: 20px;
}

.info-label {
    font-weight: 600;
    color: var(--text-dark);
    display: block;
    margin-bottom: 5px;
}

.info-value {
    color: var(--text-muted);
}

.setting-key {
    background: rgba(var(--primary-purple-rgb), 0.1);
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.9rem;
    color: var(--primary-purple);
}

.json-value {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    padding: 10px;
    font-size: 0.85rem;
    color: var(--text-dark);
    max-height: 200px;
    overflow-y: auto;
}

.status-item {
    margin-bottom: 15px;
}

.status-label {
    font-weight: 600;
    color: var(--text-dark);
    margin-bottom: 5px;
}

.status-value {
    color: var(--text-muted);
}

.info-item {
    margin-bottom: 15px;
    padding: 10px;
    background: #f8f9fa;
    border-radius: 6px;
}

.info-item .label {
    font-weight: 600;
    color: var(--text-dark);
    display: block;
    margin-bottom: 5px;
}

.info-item .value {
    color: var(--text-muted);
}

.badge {
    font-size: 0.75rem;
}

.d-grid .btn {
    margin-bottom: 8px;
}

.d-grid .btn:last-child {
    margin-bottom: 0;
}
</style>
@endpush
@endsection



