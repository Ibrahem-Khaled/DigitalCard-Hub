@extends('layouts.dashboard')

@section('title', 'عرض الصلاحية - ' . $permission->display_name)

@section('content')
<div class="page-header">
    <div class="page-title">
        <h1>
            <div class="d-flex align-items-center">
                <i class="bi bi-key me-3 text-primary"></i>
                <div>
                    {{ $permission->display_name }}
                    @if($permission->is_system)
                        <span class="badge bg-primary ms-2">نظام</span>
                    @else
                        <span class="badge bg-success ms-2">مخصص</span>
                    @endif
                </div>
            </div>
        </h1>
        <p class="text-muted">{{ $permission->description ?: 'لا يوجد وصف' }}</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('dashboard.permissions.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-right me-2"></i>
            العودة للصلاحيات
        </a>
        @if(!$permission->is_system)
            <a href="{{ route('dashboard.permissions.edit', $permission) }}" class="btn btn-outline-warning">
                <i class="bi bi-pencil me-2"></i>
                تعديل الصلاحية
            </a>
        @endif
    </div>
</div>

<!-- إحصائيات الصلاحية -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-primary">
                <i class="bi bi-shield-check"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $permissionStats['roles_count'] }}</h3>
                <p>الأدوار</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-info">
                <i class="bi bi-folder"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $permission->module ? ucfirst($permission->module) : 'غير محدد' }}</h3>
                <p>الوحدة</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-secondary">
                <i class="bi bi-gear"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $permission->action ? ucfirst($permission->action) : 'غير محدد' }}</h3>
                <p>الإجراء</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon {{ $permission->is_active ? 'bg-success' : 'bg-danger' }}">
                <i class="bi bi-{{ $permission->is_active ? 'check-circle' : 'x-circle' }}"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $permission->is_active ? 'نشط' : 'معطل' }}</h3>
                <p>الحالة</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- معلومات الصلاحية -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">معلومات الصلاحية</h5>
            </div>
            <div class="card-body">
                <div class="info-item">
                    <label class="info-label">اسم الصلاحية:</label>
                    <span class="info-value">{{ $permission->name }}</span>
                </div>
                <div class="info-item">
                    <label class="info-label">اسم العرض:</label>
                    <span class="info-value">{{ $permission->display_name }}</span>
                </div>
                <div class="info-item">
                    <label class="info-label">الوصف:</label>
                    <span class="info-value">{{ $permission->description ?: 'لا يوجد وصف' }}</span>
                </div>
                <div class="info-item">
                    <label class="info-label">الوحدة:</label>
                    <span class="info-value">
                        @if($permission->module)
                            <span class="badge bg-info">{{ ucfirst($permission->module) }}</span>
                        @else
                            <span class="text-muted">غير محدد</span>
                        @endif
                    </span>
                </div>
                <div class="info-item">
                    <label class="info-label">الإجراء:</label>
                    <span class="info-value">
                        @if($permission->action)
                            <span class="badge bg-secondary">{{ ucfirst($permission->action) }}</span>
                        @else
                            <span class="text-muted">غير محدد</span>
                        @endif
                    </span>
                </div>
                <div class="info-item">
                    <label class="info-label">ترتيب العرض:</label>
                    <span class="info-value">{{ $permission->sort_order }}</span>
                </div>
                <div class="info-item">
                    <label class="info-label">نوع الصلاحية:</label>
                    <span class="info-value">
                        @if($permission->is_system)
                            <span class="badge bg-primary">نظام</span>
                        @else
                            <span class="badge bg-success">مخصص</span>
                        @endif
                    </span>
                </div>
                <div class="info-item">
                    <label class="info-label">الحالة:</label>
                    <span class="info-value">
                        @if($permission->is_active)
                            <span class="badge bg-success">نشط</span>
                        @else
                            <span class="badge bg-danger">معطل</span>
                        @endif
                    </span>
                </div>
                <div class="info-item">
                    <label class="info-label">تاريخ الإنشاء:</label>
                    <span class="info-value">{{ $permission->created_at->format('Y-m-d H:i:s') }}</span>
                </div>
                <div class="info-item">
                    <label class="info-label">آخر تحديث:</label>
                    <span class="info-value">{{ $permission->updated_at->format('Y-m-d H:i:s') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- الأدوار -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">الأدوار التي تحتوي على هذه الصلاحية</h5>
            </div>
            <div class="card-body">
                @if($permission->roles->count() > 0)
                    @foreach($permissionStats['roles_by_module'] as $type => $roles)
                        <div class="roles-section mb-4">
                            <h6 class="section-title">
                                <i class="bi bi-shield-check me-2"></i>
                                {{ $type }}
                                <span class="badge bg-secondary ms-2">{{ count($roles) }}</span>
                            </h6>

                            <div class="roles-list">
                                @foreach($roles as $role)
                                    <div class="role-item">
                                        <div class="d-flex align-items-center">
                                            <div class="role-color me-3" style="background-color: {{ $role->color }}"></div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">{{ $role->display_name }}</h6>
                                                <small class="text-muted">{{ $role->name }}</small>
                                                @if($role->description)
                                                    <small class="text-muted d-block">{{ Str::limit($role->description, 50) }}</small>
                                                @endif
                                            </div>
                                            <div class="role-actions">
                                                <a href="{{ route('dashboard.roles.show', $role) }}" class="btn btn-sm btn-outline-primary" title="عرض الدور">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h6>لا توجد أدوار</h6>
                        <p class="text-muted">هذه الصلاحية غير مخصصة لأي دور</p>
                        <a href="{{ route('dashboard.roles.index') }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-plus-circle me-1"></i>
                            إدارة الأدوار
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- معلومات إضافية -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">معلومات تقنية</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-section">
                            <h6 class="info-title">معرف الصلاحية:</h6>
                            <div class="info-content">
                                <code>{{ $permission->slug }}</code>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-section">
                            <h6 class="info-title">الاسم الكامل:</h6>
                            <div class="info-content">
                                <code>{{ $permission->full_name }}</code>
                            </div>
                        </div>
                    </div>
                </div>

                @if($permission->module && $permission->action)
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-section">
                                <h6 class="info-title">نمط الصلاحية:</h6>
                                <div class="info-content">
                                    <code>{{ $permission->module }}.{{ $permission->action }}</code>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-section">
                                <h6 class="info-title">يمكن حذفها:</h6>
                                <div class="info-content">
                                    @if($permission->canBeDeleted())
                                        <span class="badge bg-success">نعم</span>
                                    @else
                                        <span class="badge bg-danger">لا</span>
                                        <small class="text-muted d-block">مرتبطة بأدوار أو صلاحية نظام</small>
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

<style>
.role-color {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 1px rgba(0,0,0,0.1);
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid #f1f3f4;
}

.info-item:last-child {
    border-bottom: none;
}

.info-label {
    font-weight: 600;
    color: #495057;
    margin: 0;
}

.info-value {
    color: #6c757d;
}

.roles-section {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 15px;
    background: #f8f9fa;
}

.section-title {
    color: #495057;
    border-bottom: 1px solid #dee2e6;
    padding-bottom: 8px;
    margin-bottom: 15px;
}

.role-item {
    padding: 10px 0;
    border-bottom: 1px solid #e9ecef;
}

.role-item:last-child {
    border-bottom: none;
}

.empty-state {
    text-align: center;
    padding: 40px 20px;
}

.empty-icon {
    font-size: 3rem;
    color: #6c757d;
    margin-bottom: 15px;
}

.info-section {
    margin-bottom: 20px;
}

.info-title {
    color: #495057;
    font-weight: 600;
    margin-bottom: 8px;
}

.info-content {
    color: #6c757d;
}

.info-content code {
    background: #f8f9fa;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.9em;
}
</style>
@endsection
