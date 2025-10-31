@extends('layouts.dashboard')

@section('title', 'إدارة الصلاحيات')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h1>
            <i class="bi bi-key me-2"></i>
            إدارة الصلاحيات
        </h1>
        <p class="text-muted">إدارة صلاحيات النظام وتنظيمها</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('dashboard.roles.index') }}" class="btn btn-outline-info">
            <i class="bi bi-shield-check me-2"></i>
            إدارة الأدوار
        </a>
        <a href="{{ route('dashboard.permissions.export') }}" class="btn btn-outline-success">
            <i class="bi bi-download me-2"></i>
            تصدير CSV
        </a>
        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#bulkCreateModal">
            <i class="bi bi-plus-square me-2"></i>
            إنشاء جماعي
        </button>
        <a href="{{ route('dashboard.permissions.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>
            إضافة صلاحية جديدة
        </a>
    </div>
</div>

<!-- إحصائيات -->
<div class="stats-grid mb-4">
    <x-dashboard.stats-card
        title="إجمالي الصلاحيات"
        :value="number_format($stats['total_permissions'])"
        icon="bi-key"
        change-type="neutral"
        change-text="جميع الصلاحيات" />

    <x-dashboard.stats-card
        title="صلاحيات نشطة"
        :value="number_format($stats['active_permissions'])"
        icon="bi-check-circle"
        change-type="positive"
        change-text="نشطة" />

    <x-dashboard.stats-card
        title="صلاحيات النظام"
        :value="number_format($stats['system_permissions'])"
        icon="bi-gear"
        change-type="info"
        change-text="نظامية" />

    <x-dashboard.stats-card
        title="صلاحيات مخصصة"
        :value="number_format($stats['custom_permissions'])"
        icon="bi-person-plus"
        change-type="neutral"
        change-text="مخصصة" />

    <x-dashboard.stats-card
        title="الوحدات المختلفة"
        :value="count($stats['permissions_by_module'])"
        icon="bi-grid"
        change-type="success"
        change-text="وحدات" />

    <x-dashboard.stats-card
        title="أنواع الإجراءات"
        :value="count($stats['permissions_by_action'])"
        icon="bi-list-task"
        change-type="primary"
        change-text="إجراءات" />
</div>

<!-- الفلاتر -->
<x-dashboard.filters
    :filters="[
        ['name' => 'status', 'label' => 'الحالة', 'type' => 'select', 'placeholder' => 'جميع الحالات', 'options' => ['active' => 'نشط', 'inactive' => 'معطل']],
        ['name' => 'type', 'label' => 'النوع', 'type' => 'select', 'placeholder' => 'جميع الأنواع', 'options' => ['system' => 'نظام', 'custom' => 'مخصص']],
        ['name' => 'module', 'label' => 'الوحدة', 'type' => 'select', 'placeholder' => 'جميع الوحدات', 'options' => array_combine($filterOptions['modules'], $filterOptions['modules'])],
        ['name' => 'action', 'label' => 'الإجراء', 'type' => 'select', 'placeholder' => 'جميع الإجراءات', 'options' => array_combine($filterOptions['actions'], $filterOptions['actions'])],
        ['name' => 'sort_by', 'label' => 'ترتيب حسب', 'type' => 'select', 'placeholder' => 'ترتيب حسب', 'options' => ['sort_order' => 'ترتيب العرض', 'name' => 'الاسم', 'display_name' => 'اسم العرض', 'module' => 'الوحدة', 'action' => 'الإجراء', 'created_at' => 'تاريخ الإنشاء']],
        ['name' => 'sort_order', 'label' => 'اتجاه الترتيب', 'type' => 'select', 'placeholder' => 'اتجاه الترتيب', 'options' => ['asc' => 'تصاعدي', 'desc' => 'تنازلي']]
    ]"
    search-placeholder="البحث في الصلاحيات..."
    :search-value="request('search')"
    :action-url="route('dashboard.permissions.index')" />

<!-- جدول الصلاحيات -->
<div class="card">
    <div class="card-body">
        @if($permissions->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>الصلاحية</th>
                            <th>الوحدة</th>
                            <th>الإجراء</th>
                            <th>الأدوار</th>
                            <th>النوع</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($permissions as $permission)
                            <tr>
                                <td>
                                    <div>
                                        <h6 class="mb-0">{{ $permission->display_name }}</h6>
                                        <small class="text-muted">{{ $permission->name }}</small>
                                        @if($permission->description)
                                            <small class="text-muted d-block">{{ Str::limit($permission->description, 50) }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($permission->module)
                                        <span class="badge bg-info">{{ ucfirst($permission->module) }}</span>
                                    @else
                                        <span class="text-muted">غير محدد</span>
                                    @endif
                                </td>
                                <td>
                                    @if($permission->action)
                                        <span class="badge bg-secondary">{{ ucfirst($permission->action) }}</span>
                                    @else
                                        <span class="text-muted">غير محدد</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $permission->roles->count() }}</span>
                                    @if($permission->roles->count() > 0)
                                        <small class="text-muted d-block">
                                            {{ Str::limit($permission->roles->pluck('display_name')->join(', '), 30) }}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    @if($permission->is_system)
                                        <span class="badge bg-primary">نظام</span>
                                    @else
                                        <span class="badge bg-success">مخصص</span>
                                    @endif
                                </td>
                                <td>
                                    @if($permission->is_active)
                                        <span class="badge bg-success">نشط</span>
                                    @else
                                        <span class="badge bg-danger">معطل</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('dashboard.permissions.show', $permission) }}" class="btn btn-sm btn-outline-primary" title="عرض التفاصيل">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if(!$permission->is_system)
                                            <a href="{{ route('dashboard.permissions.edit', $permission) }}" class="btn btn-sm btn-outline-warning" title="تعديل">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-info" title="نسخ" onclick="duplicatePermission({{ $permission->id }})">
                                                <i class="bi bi-files"></i>
                                            </button>
                                            <form method="POST" action="{{ route('dashboard.permissions.toggle-status', $permission) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-{{ $permission->is_active ? 'warning' : 'success' }}" title="{{ $permission->is_active ? 'تعطيل' : 'تفعيل' }}">
                                                    <i class="bi bi-{{ $permission->is_active ? 'pause' : 'play' }}"></i>
                                                </button>
                                            </form>
                                            @if($permission->canBeDeleted())
                                                <form method="POST" action="{{ route('dashboard.permissions.destroy', $permission) }}" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه الصلاحية؟')">
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
                {{ $permissions->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="bi bi-key"></i>
                </div>
                <h3>لا توجد صلاحيات</h3>
                <p>لم يتم إنشاء أي صلاحيات بعد. ابدأ بإنشاء صلاحية جديدة.</p>
                <a href="{{ route('dashboard.permissions.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>
                    إضافة صلاحية جديدة
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
                <h5 class="modal-title">نسخ الصلاحية</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>هل تريد نسخ هذه الصلاحية؟</p>
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

<!-- Modal للإنشاء الجماعي -->
<div class="modal fade" id="bulkCreateModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">إنشاء صلاحيات جماعي</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('dashboard.permissions.bulk-create') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="module" class="form-label fw-bold">اسم الوحدة <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="module" name="module" required
                               placeholder="مثال: products, orders, users">
                        <div class="form-text">اسم الوحدة التي تنتمي إليها الصلاحيات</div>
                    </div>

                    <div class="mb-3">
                        <label for="prefix" class="form-label fw-bold">بادئة الصلاحيات</label>
                        <input type="text" class="form-control" id="prefix" name="prefix"
                               placeholder="مثال: product, order, user">
                        <div class="form-text">سيتم استخدام اسم الوحدة إذا لم يتم تحديد بادئة</div>
                    </div>

                    <div class="mb-3">
                        <label for="actions" class="form-label fw-bold">الإجراءات <span class="text-danger">*</span></label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="actions[]" value="create" id="action_create">
                                    <label class="form-check-label" for="action_create">إنشاء (Create)</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="actions[]" value="read" id="action_read">
                                    <label class="form-check-label" for="action_read">قراءة (Read)</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="actions[]" value="update" id="action_update">
                                    <label class="form-check-label" for="action_update">تحديث (Update)</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="actions[]" value="delete" id="action_delete">
                                    <label class="form-check-label" for="action_delete">حذف (Delete)</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="actions[]" value="export" id="action_export">
                                    <label class="form-check-label" for="action_export">تصدير (Export)</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="actions[]" value="import" id="action_import">
                                    <label class="form-check-label" for="action_import">استيراد (Import)</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="actions[]" value="manage" id="action_manage">
                                    <label class="form-check-label" for="action_manage">إدارة (Manage)</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="actions[]" value="view" id="action_view">
                                    <label class="form-check-label" for="action_view">عرض (View)</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-text">اختر الإجراءات التي تريد إنشاء صلاحيات لها</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">إنشاء الصلاحيات</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.empty-state {
    text-align: center;
    padding: 40px 20px;
}

.empty-icon {
    font-size: 3rem;
    color: #6c757d;
    margin-bottom: 15px;
}
</style>

<script>
function duplicatePermission(permissionId) {
    document.getElementById('duplicateForm').action = `/dashboard/permissions/${permissionId}/duplicate`;
    new bootstrap.Modal(document.getElementById('duplicateModal')).show();
}
</script>
@endsection
