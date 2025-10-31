@extends('layouts.dashboard')

@section('title', 'إضافة دور جديد')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h1>
            <i class="bi bi-plus-circle me-2"></i>
            إضافة دور جديد
        </h1>
        <p class="text-muted">إنشاء دور جديد مع تحديد الصلاحيات</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('dashboard.roles.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-right me-2"></i>
            العودة للأدوار
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">معلومات الدور</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('dashboard.roles.store') }}" id="roleForm">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label fw-bold">اسم الدور <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name') }}"
                                       placeholder="مثال: مدير المبيعات" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">اسم فريد للدور (سيتم إنشاء slug تلقائياً)</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="display_name" class="form-label fw-bold">اسم العرض <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('display_name') is-invalid @enderror"
                                       id="display_name" name="display_name" value="{{ old('display_name') }}"
                                       placeholder="مثال: مدير المبيعات" required>
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
                                  placeholder="وصف مختصر للدور...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="color" class="form-label fw-bold">لون الدور</label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color @error('color') is-invalid @enderror"
                                           id="color" name="color" value="{{ old('color', '#8B5CF6') }}">
                                    <input type="text" class="form-control" id="colorText" value="{{ old('color', '#8B5CF6') }}" readonly>
                                </div>
                                @error('color')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sort_order" class="form-label fw-bold">ترتيب العرض</label>
                                <input type="number" class="form-control @error('sort_order') is-invalid @enderror"
                                       id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}"
                                       min="0" placeholder="0">
                                @error('sort_order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">رقم الترتيب (الأقل يظهر أولاً)</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="is_active">
                                تفعيل الدور
                            </label>
                        </div>
                        <div class="form-text">الدور النشط يمكن استخدامه في النظام</div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">الصلاحيات</h5>
            </div>
            <div class="card-body">
                @if($permissions->count() > 0)
                    <div class="permissions-container">
                        @foreach($permissions as $module => $modulePermissions)
                            <div class="permission-module mb-4">
                                <h6 class="module-title">
                                    <i class="bi bi-folder me-2"></i>
                                    {{ ucfirst($module) }}
                                    <span class="badge bg-secondary ms-2">{{ count($modulePermissions) }}</span>
                                </h6>

                                <div class="permission-list">
                                    @foreach($modulePermissions as $permission)
                                        <div class="form-check mb-2">
                                            <input class="form-check-input permission-checkbox"
                                                   type="checkbox"
                                                   name="permissions[]"
                                                   value="{{ $permission->id }}"
                                                   id="permission_{{ $permission->id }}"
                                                   {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                {{ $permission->display_name }}
                                                @if($permission->description)
                                                    <small class="text-muted d-block">{{ $permission->description }}</small>
                                                @endif
                                            </label>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="module-actions mt-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary select-module"
                                            data-module="{{ $module }}">
                                        <i class="bi bi-check-all me-1"></i>
                                        تحديد الكل
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary deselect-module"
                                            data-module="{{ $module }}">
                                        <i class="bi bi-x-square me-1"></i>
                                        إلغاء الكل
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="permission-actions mt-3">
                        <button type="button" class="btn btn-sm btn-success" id="selectAllPermissions">
                            <i class="bi bi-check-all me-1"></i>
                            تحديد جميع الصلاحيات
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAllPermissions">
                            <i class="bi bi-x-square me-1"></i>
                            إلغاء جميع الصلاحيات
                        </button>
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="bi bi-key"></i>
                        </div>
                        <h6>لا توجد صلاحيات</h6>
                        <p class="text-muted">يجب إنشاء صلاحيات أولاً</p>
                        <a href="{{ route('dashboard.permissions.create') }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-plus-circle me-1"></i>
                            إضافة صلاحية
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('dashboard.roles.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-right me-2"></i>
                        إلغاء
                    </a>
                    <button type="submit" form="roleForm" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>
                        إنشاء الدور
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.permission-module {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 15px;
    background: #f8f9fa;
}

.module-title {
    color: #495057;
    border-bottom: 1px solid #dee2e6;
    padding-bottom: 8px;
    margin-bottom: 15px;
}

.permission-list {
    max-height: 200px;
    overflow-y: auto;
}

.permission-list .form-check {
    padding-left: 0;
}

.permission-list .form-check-input {
    margin-left: 0;
    margin-right: 8px;
}

.module-actions {
    border-top: 1px solid #dee2e6;
    padding-top: 10px;
}

.permission-actions {
    border-top: 2px solid #dee2e6;
    padding-top: 15px;
}

.empty-state {
    text-align: center;
    padding: 20px;
}

.empty-icon {
    font-size: 2rem;
    color: #6c757d;
    margin-bottom: 10px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // تحديث نص اللون عند تغيير اللون
    const colorInput = document.getElementById('color');
    const colorText = document.getElementById('colorText');

    colorInput.addEventListener('change', function() {
        colorText.value = this.value;
    });

    // تحديد جميع صلاحيات الوحدة
    document.querySelectorAll('.select-module').forEach(button => {
        button.addEventListener('click', function() {
            const module = this.dataset.module;
            const checkboxes = document.querySelectorAll(`input[name="permissions[]"][data-module="${module}"]`);
            checkboxes.forEach(checkbox => checkbox.checked = true);
        });
    });

    // إلغاء تحديد جميع صلاحيات الوحدة
    document.querySelectorAll('.deselect-module').forEach(button => {
        button.addEventListener('click', function() {
            const module = this.dataset.module;
            const checkboxes = document.querySelectorAll(`input[name="permissions[]"][data-module="${module}"]`);
            checkboxes.forEach(checkbox => checkbox.checked = false);
        });
    });

    // تحديد جميع الصلاحيات
    document.getElementById('selectAllPermissions').addEventListener('click', function() {
        document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
            checkbox.checked = true;
        });
    });

    // إلغاء تحديد جميع الصلاحيات
    document.getElementById('deselectAllPermissions').addEventListener('click', function() {
        document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
            checkbox.checked = false;
        });
    });

    // إضافة data-module للـ checkboxes
    document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
        const module = checkbox.closest('.permission-module').querySelector('.module-title').textContent.trim();
        checkbox.setAttribute('data-module', module);
    });
});
</script>
@endsection
