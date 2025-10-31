@extends('layouts.dashboard')

@section('title', 'تعديل الصلاحية - ' . $permission->display_name)

@section('content')
<div class="page-header">
    <div class="page-title">
        <h1>
            <div class="d-flex align-items-center">
                <i class="bi bi-pencil me-3 text-warning"></i>
                <div>
                    تعديل الصلاحية: {{ $permission->display_name }}
                </div>
            </div>
        </h1>
        <p class="text-muted">تعديل معلومات الصلاحية</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('dashboard.permissions.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-right me-2"></i>
            العودة للصلاحيات
        </a>
        <a href="{{ route('dashboard.permissions.show', $permission) }}" class="btn btn-outline-info">
            <i class="bi bi-eye me-2"></i>
            عرض الصلاحية
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">معلومات الصلاحية</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('dashboard.permissions.update', $permission) }}" id="permissionForm">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label fw-bold">اسم الصلاحية <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name', $permission->name) }}"
                                       placeholder="مثال: products.create" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">اسم فريد للصلاحية (سيتم إنشاء slug تلقائياً)</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="display_name" class="form-label fw-bold">اسم العرض <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('display_name') is-invalid @enderror"
                                       id="display_name" name="display_name" value="{{ old('display_name', $permission->display_name) }}"
                                       placeholder="مثال: إنشاء منتج" required>
                                @error('display_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">الاسم المعروض في الواجهة</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label fw-bold">الوصف</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="3"
                                  placeholder="وصف مختصر للصلاحية...">{{ old('description', $permission->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="module" class="form-label fw-bold">الوحدة</label>
                                <select class="form-select @error('module') is-invalid @enderror" id="module" name="module">
                                    <option value="">اختر الوحدة</option>
                                    @foreach($modules as $module)
                                        <option value="{{ $module }}" {{ old('module', $permission->module) == $module ? 'selected' : '' }}>
                                            {{ ucfirst($module) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('module')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">الوحدة التي تنتمي إليها الصلاحية</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="action" class="form-label fw-bold">الإجراء</label>
                                <select class="form-select @error('action') is-invalid @enderror" id="action" name="action">
                                    <option value="">اختر الإجراء</option>
                                    @foreach($actions as $action)
                                        <option value="{{ $action }}" {{ old('action', $permission->action) == $action ? 'selected' : '' }}>
                                            {{ ucfirst($action) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('action')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">نوع الإجراء الذي تسمح به الصلاحية</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="sort_order" class="form-label fw-bold">ترتيب العرض</label>
                        <input type="number" class="form-control @error('sort_order') is-invalid @enderror"
                               id="sort_order" name="sort_order" value="{{ old('sort_order', $permission->sort_order) }}"
                               min="0" placeholder="0">
                        @error('sort_order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">رقم الترتيب (الأقل يظهر أولاً)</div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                   {{ old('is_active', $permission->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="is_active">
                                تفعيل الصلاحية
                            </label>
                        </div>
                        <div class="form-text">الصلاحية النشطة يمكن استخدامها في النظام</div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">معلومات إضافية</h5>
            </div>
            <div class="card-body">
                <div class="info-section">
                    <h6 class="info-title">الوحدات المتاحة:</h6>
                    <div class="info-content">
                        @if(count($modules) > 0)
                            @foreach($modules as $module)
                                <span class="badge bg-info me-1 mb-1">{{ ucfirst($module) }}</span>
                            @endforeach
                        @else
                            <p class="text-muted">لا توجد وحدات متاحة</p>
                        @endif
                    </div>
                </div>

                <div class="info-section">
                    <h6 class="info-title">الإجراءات المتاحة:</h6>
                    <div class="info-content">
                        @if(count($actions) > 0)
                            @foreach($actions as $action)
                                <span class="badge bg-secondary me-1 mb-1">{{ ucfirst($action) }}</span>
                            @endforeach
                        @else
                            <p class="text-muted">لا توجد إجراءات متاحة</p>
                        @endif
                    </div>
                </div>

                <div class="info-section">
                    <h6 class="info-title">معلومات الصلاحية:</h6>
                    <div class="info-content">
                        <div class="info-item">
                            <span class="info-label">النوع:</span>
                            <span class="info-value">
                                @if($permission->is_system)
                                    <span class="badge bg-primary">نظام</span>
                                @else
                                    <span class="badge bg-success">مخصص</span>
                                @endif
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">الأدوار:</span>
                            <span class="info-value">
                                <span class="badge bg-info">{{ $permission->roles->count() }}</span>
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">يمكن حذفها:</span>
                            <span class="info-value">
                                @if($permission->canBeDeleted())
                                    <span class="badge bg-success">نعم</span>
                                @else
                                    <span class="badge bg-danger">لا</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                <div class="info-section">
                    <h6 class="info-title">نصائح:</h6>
                    <div class="info-content">
                        <ul class="tips-list">
                            <li>استخدم أسماء واضحة ومفهومة</li>
                            <li>اجمع الصلاحيات المتشابهة في وحدة واحدة</li>
                            <li>استخدم الإجراءات القياسية</li>
                            <li>أضف وصف مفيد للصلاحية</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('dashboard.permissions.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-right me-2"></i>
                        إلغاء
                    </a>
                    <div>
                        <button type="submit" form="permissionForm" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>
                            حفظ التغييرات
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.info-section {
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #e9ecef;
}

.info-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.info-title {
    color: #495057;
    font-weight: 600;
    margin-bottom: 10px;
}

.info-content {
    color: #6c757d;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 4px 0;
}

.info-label {
    font-weight: 500;
    color: #495057;
    margin: 0;
}

.info-value {
    color: #6c757d;
}

.tips-list {
    margin: 0;
    padding-left: 20px;
}

.tips-list li {
    margin-bottom: 5px;
    color: #6c757d;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // تحديث اسم العرض تلقائياً عند تغيير الوحدة والإجراء
    const moduleSelect = document.getElementById('module');
    const actionSelect = document.getElementById('action');
    const displayNameInput = document.getElementById('display_name');

    function updateDisplayName() {
        const module = moduleSelect.value;
        const action = actionSelect.value;

        if (module && action) {
            const moduleText = module.charAt(0).toUpperCase() + module.slice(1);
            const actionText = action.charAt(0).toUpperCase() + action.slice(1);
            displayNameInput.value = `${actionText} ${moduleText}`;
        }
    }

    moduleSelect.addEventListener('change', updateDisplayName);
    actionSelect.addEventListener('change', updateDisplayName);

    // تحديث اسم الصلاحية تلقائياً
    const nameInput = document.getElementById('name');

    function updateName() {
        const module = moduleSelect.value;
        const action = actionSelect.value;

        if (module && action) {
            nameInput.value = `${module}.${action}`;
        }
    }

    moduleSelect.addEventListener('change', updateName);
    actionSelect.addEventListener('change', updateName);
});
</script>
@endsection
