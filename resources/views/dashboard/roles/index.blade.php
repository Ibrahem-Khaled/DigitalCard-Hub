@extends('layouts.dashboard')

@section('title', 'إدارة الأدوار')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h1>
            <i class="bi bi-shield-check me-2"></i>
            إدارة الأدوار
        </h1>
        <p class="text-muted">إدارة أدوار المستخدمين وصلاحياتهم</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('dashboard.permissions.index') }}" class="btn btn-outline-info">
            <i class="bi bi-key me-2"></i>
            إدارة الصلاحيات
        </a>
        <a href="{{ route('dashboard.roles.export') }}" class="btn btn-outline-success">
            <i class="bi bi-download me-2"></i>
            تصدير CSV
        </a>
        <a href="{{ route('dashboard.roles.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>
            إضافة دور جديد
        </a>
    </div>
</div>

<!-- إحصائيات -->
<div class="stats-grid mb-4">
    <x-dashboard.stats-card
        title="إجمالي الأدوار"
        :value="number_format($stats['total_roles'])"
        icon="bi-shield-check"
        change-type="neutral"
        change-text="جميع الأدوار" />

    <x-dashboard.stats-card
        title="أدوار نشطة"
        :value="number_format($stats['active_roles'])"
        icon="bi-check-circle"
        change-type="positive"
        change-text="نشطة" />

    <x-dashboard.stats-card
        title="أدوار النظام"
        :value="number_format($stats['system_roles'])"
        icon="bi-gear"
        change-type="info"
        change-text="نظامية" />

    <x-dashboard.stats-card
        title="أدوار مخصصة"
        :value="number_format($stats['custom_roles'])"
        icon="bi-person-plus"
        change-type="neutral"
        change-text="مخصصة" />

    <x-dashboard.stats-card
        title="أدوار لها صلاحيات"
        :value="number_format($stats['roles_with_permissions'])"
        icon="bi-key"
        change-type="success"
        change-text="مع صلاحيات" />

    <x-dashboard.stats-card
        title="أدوار لها مستخدمين"
        :value="number_format($stats['roles_with_users'])"
        icon="bi-people"
        change-type="primary"
        change-text="مع مستخدمين" />
</div>

<!-- الفلاتر -->
<x-dashboard.filters
    :filters="[
        ['name' => 'status', 'label' => 'الحالة', 'type' => 'select', 'placeholder' => 'جميع الحالات', 'options' => ['active' => 'نشط', 'inactive' => 'معطل']],
        ['name' => 'type', 'label' => 'النوع', 'type' => 'select', 'placeholder' => 'جميع الأنواع', 'options' => ['system' => 'نظام', 'custom' => 'مخصص']],
        ['name' => 'sort_by', 'label' => 'ترتيب حسب', 'type' => 'select', 'placeholder' => 'ترتيب حسب', 'options' => ['sort_order' => 'ترتيب العرض', 'name' => 'الاسم', 'display_name' => 'اسم العرض', 'created_at' => 'تاريخ الإنشاء']],
        ['name' => 'sort_order', 'label' => 'اتجاه الترتيب', 'type' => 'select', 'placeholder' => 'اتجاه الترتيب', 'options' => ['asc' => 'تصاعدي', 'desc' => 'تنازلي']]
    ]"
    search-placeholder="البحث في الأدوار..."
    :search-value="request('search')"
    :action-url="route('dashboard.roles.index')" />

<!-- جدول الأدوار -->
<div class="card">
    <div class="card-body">
        @if($roles->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>الدور</th>
                            <th>الوصف</th>
                            <th>الصلاحيات</th>
                            <th>المستخدمين</th>
                            <th>النوع</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $role)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="role-color me-3" style="background-color: {{ $role->color }}"></div>
                                        <div>
                                            <h6 class="mb-0">{{ $role->display_name }}</h6>
                                            <small class="text-muted">{{ $role->name }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-muted">{{ Str::limit($role->description, 50) }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $role->permissions->count() }}</span>
                                    @if($role->permissions->count() > 0)
                                        <small class="text-muted d-block">
                                            {{ Str::limit($role->permissions->pluck('display_name')->join(', '), 30) }}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $role->users()->count() }}</span>
                                </td>
                                <td>
                                    @if($role->is_system)
                                        <span class="badge bg-primary">نظام</span>
                                    @else
                                        <span class="badge bg-success">مخصص</span>
                                    @endif
                                </td>
                                <td>
                                    @if($role->is_active)
                                        <span class="badge bg-success">نشط</span>
                                    @else
                                        <span class="badge bg-danger">معطل</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('dashboard.roles.show', $role) }}" class="btn btn-sm btn-outline-primary" title="عرض التفاصيل">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if(!$role->is_system)
                                            <a href="{{ route('dashboard.roles.edit', $role) }}" class="btn btn-sm btn-outline-warning" title="تعديل">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-info" title="نسخ" onclick="duplicateRole({{ $role->id }})">
                                                <i class="bi bi-files"></i>
                                            </button>
                                            <form method="POST" action="{{ route('dashboard.roles.toggle-status', $role) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-{{ $role->is_active ? 'warning' : 'success' }}" title="{{ $role->is_active ? 'تعطيل' : 'تفعيل' }}">
                                                    <i class="bi bi-{{ $role->is_active ? 'pause' : 'play' }}"></i>
                                                </button>
                                            </form>
                                            @if($role->canBeDeleted())
                                                <form method="POST" action="{{ route('dashboard.roles.destroy', $role) }}" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الدور؟')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $roles->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="bi bi-shield-check"></i>
                </div>
                <h3>لا توجد أدوار</h3>
                <p>لم يتم إنشاء أي أدوار بعد. ابدأ بإنشاء دور جديد.</p>
                <a href="{{ route('dashboard.roles.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>
                    إضافة دور جديد
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Modal للنسخ -->
<div class="modal fade" id="duplicateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">نسخ الدور</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>هل تريد نسخ هذا الدور مع جميع صلاحياته؟</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <form id="duplicateForm" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary">نسخ</button>
                </form>
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
</style>

<script>
function duplicateRole(roleId) {
    document.getElementById('duplicateForm').action = `/dashboard/roles/${roleId}/duplicate`;
    new bootstrap.Modal(document.getElementById('duplicateModal')).show();
}
</script>
@endsection
