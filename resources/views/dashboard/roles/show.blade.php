@extends('layouts.dashboard')

@section('title', 'عرض الدور - ' . $role->display_name)

@section('content')
<div class="page-header">
    <div class="page-title">
        <h1>
            <div class="d-flex align-items-center">
                <div class="role-color me-3" style="background-color: {{ $role->color }}"></div>
                <div>
                    {{ $role->display_name }}
                    @if($role->is_system)
                        <span class="badge bg-primary ms-2">نظام</span>
                    @else
                        <span class="badge bg-success ms-2">مخصص</span>
                    @endif
                </div>
            </div>
        </h1>
        <p class="text-muted">{{ $role->description ?: 'لا يوجد وصف' }}</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('dashboard.roles.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-right me-2"></i>
            العودة للأدوار
        </a>
        @if(!$role->is_system)
            <a href="{{ route('dashboard.roles.edit', $role) }}" class="btn btn-outline-warning">
                <i class="bi bi-pencil me-2"></i>
                تعديل الدور
            </a>
        @endif
    </div>
</div>

<!-- إحصائيات الدور -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-primary">
                <i class="bi bi-key"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $roleStats['permissions_count'] }}</h3>
                <p>الصلاحيات</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-success">
                <i class="bi bi-people"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $roleStats['users_count'] }}</h3>
                <p>المستخدمين</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-info">
                <i class="bi bi-folder"></i>
            </div>
            <div class="stat-content">
                <h3>{{ count($roleStats['permissions_by_module']) }}</h3>
                <p>الوحدات</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon {{ $role->is_active ? 'bg-success' : 'bg-danger' }}">
                <i class="bi bi-{{ $role->is_active ? 'check-circle' : 'x-circle' }}"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $role->is_active ? 'نشط' : 'معطل' }}</h3>
                <p>الحالة</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- معلومات الدور -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">معلومات الدور</h5>
            </div>
            <div class="card-body">
                <div class="info-item">
                    <label class="info-label">اسم الدور:</label>
                    <span class="info-value">{{ $role->name }}</span>
                </div>
                <div class="info-item">
                    <label class="info-label">اسم العرض:</label>
                    <span class="info-value">{{ $role->display_name }}</span>
                </div>
                <div class="info-item">
                    <label class="info-label">الوصف:</label>
                    <span class="info-value">{{ $role->description ?: 'لا يوجد وصف' }}</span>
                </div>
                <div class="info-item">
                    <label class="info-label">اللون:</label>
                    <div class="d-flex align-items-center">
                        <div class="role-color me-2" style="background-color: {{ $role->color }}"></div>
                        <span class="info-value">{{ $role->color }}</span>
                    </div>
                </div>
                <div class="info-item">
                    <label class="info-label">ترتيب العرض:</label>
                    <span class="info-value">{{ $role->sort_order }}</span>
                </div>
                <div class="info-item">
                    <label class="info-label">نوع الدور:</label>
                    <span class="info-value">
                        @if($role->is_system)
                            <span class="badge bg-primary">نظام</span>
                        @else
                            <span class="badge bg-success">مخصص</span>
                        @endif
                    </span>
                </div>
                <div class="info-item">
                    <label class="info-label">الحالة:</label>
                    <span class="info-value">
                        @if($role->is_active)
                            <span class="badge bg-success">نشط</span>
                        @else
                            <span class="badge bg-danger">معطل</span>
                        @endif
                    </span>
                </div>
                <div class="info-item">
                    <label class="info-label">تاريخ الإنشاء:</label>
                    <span class="info-value">{{ $role->created_at->format('Y-m-d H:i:s') }}</span>
                </div>
                <div class="info-item">
                    <label class="info-label">آخر تحديث:</label>
                    <span class="info-value">{{ $role->updated_at->format('Y-m-d H:i:s') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- الصلاحيات -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">صلاحيات الدور</h5>
            </div>
            <div class="card-body">
                @if($role->permissions->count() > 0)
                    @foreach($roleStats['permissions_by_module'] as $module => $permissions)
                        <div class="permission-module mb-4">
                            <h6 class="module-title">
                                <i class="bi bi-folder me-2"></i>
                                {{ ucfirst($module) }}
                                <span class="badge bg-secondary ms-2">{{ count($permissions) }}</span>
                            </h6>

                            <div class="permission-list">
                                @foreach($permissions as $permission)
                                    <div class="permission-item">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-check-circle text-success me-2"></i>
                                            <div>
                                                <strong>{{ $permission['display_name'] }}</strong>
                                                @if($permission['description'])
                                                    <small class="text-muted d-block">{{ $permission['description'] }}</small>
                                                @endif
                                                <small class="text-muted">
                                                    <code>{{ $permission['slug'] }}</code>
                                                    @if($permission['action'])
                                                        - {{ ucfirst($permission['action']) }}
                                                    @endif
                                                </small>
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
                            <i class="bi bi-key"></i>
                        </div>
                        <h6>لا توجد صلاحيات</h6>
                        <p class="text-muted">هذا الدور لا يحتوي على أي صلاحيات</p>
                        @if(!$role->is_system)
                            <a href="{{ route('dashboard.roles.edit', $role) }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-plus-circle me-1"></i>
                                إضافة صلاحيات
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- المستخدمين -->
@if($role->users->count() > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">المستخدمين الذين لديهم هذا الدور</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>المستخدم</th>
                                    <th>البريد الإلكتروني</th>
                                    <th>الهاتف</th>
                                    <th>الحالة</th>
                                    <th>تاريخ التعيين</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($role->users as $user)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="user-avatar me-3">
                                                    @if($user->avatar)
                                                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->full_name }}" class="rounded-circle" width="40" height="40">
                                                    @else
                                                        <div class="avatar-placeholder bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                            {{ $user->display_name }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $user->full_name }}</h6>
                                                    <small class="text-muted">{{ $user->name }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->phone ?: 'غير محدد' }}</td>
                                        <td>
                                            @if($user->is_active)
                                                <span class="badge bg-success">نشط</span>
                                            @else
                                                <span class="badge bg-danger">معطل</span>
                                            @endif
                                        </td>
                                        <td>{{ $user->pivot->created_at->format('Y-m-d H:i:s') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

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

.permission-item {
    padding: 8px 0;
    border-bottom: 1px solid #e9ecef;
}

.permission-item:last-child {
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

.user-avatar img {
    object-fit: cover;
}

.avatar-placeholder {
    font-size: 14px;
    font-weight: 600;
}
</style>
@endsection
