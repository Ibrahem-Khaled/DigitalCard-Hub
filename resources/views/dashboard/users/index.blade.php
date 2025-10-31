@extends('layouts.dashboard-new')

@section('title', 'إدارة المستخدمين - متجر البطاقات الرقمية')

@section('page-title', 'إدارة المستخدمين')
@section('page-subtitle', 'إدارة جميع المستخدمين في النظام')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="page-title">إدارة المستخدمين</h3>
            <p class="page-subtitle">إدارة جميع المستخدمين في النظام</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('dashboard.users.create') }}" class="btn btn-primary">
                <i class="bi bi-person-plus me-2"></i>
                إضافة مستخدم جديد
            </a>
            <a href="{{ route('dashboard.users.export') }}" class="btn btn-outline-primary">
                <i class="bi bi-download me-2"></i>
                تصدير البيانات
            </a>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="stats-grid">
    <x-dashboard.stats-card
        title="إجمالي المستخدمين"
        :value="number_format($stats['total_users'])"
        icon="bi-people"
        change-type="positive"
        change-text="+5.2% من الشهر الماضي" />

    <x-dashboard.stats-card
        title="المستخدمين النشطين"
        :value="number_format($stats['active_users'])"
        icon="bi-person-check"
        change-type="positive"
        change-text="+8.1% من الشهر الماضي" />

    <x-dashboard.stats-card
        title="المديرين"
        :value="number_format($stats['admin_users'])"
        icon="bi-person-gear"
        change-type="neutral"
        change-text="ثابت" />

    <x-dashboard.stats-card
        title="مستخدمين جدد هذا الشهر"
        :value="number_format($stats['new_users_this_month'])"
        icon="bi-person-plus"
        change-type="positive"
        change-text="+12.3% من الشهر الماضي" />
</div>

<!-- Filters -->
<x-dashboard.filters
    :filters="[
        ['name' => 'role', 'label' => 'الدور', 'type' => 'select', 'placeholder' => 'جميع الأدوار', 'options' => $roles->pluck('name', 'slug')->toArray()],
        ['name' => 'status', 'label' => 'الحالة', 'type' => 'select', 'placeholder' => 'جميع الحالات', 'options' => ['active' => 'نشط', 'inactive' => 'معطل']],
        ['name' => 'sort_by', 'label' => 'ترتيب حسب', 'type' => 'select', 'placeholder' => 'ترتيب حسب', 'options' => ['created_at' => 'تاريخ الإنشاء', 'first_name' => 'الاسم الأول', 'last_name' => 'الاسم الأخير', 'email' => 'البريد الإلكتروني']],
        ['name' => 'sort_order', 'label' => 'اتجاه الترتيب', 'type' => 'select', 'placeholder' => 'اتجاه الترتيب', 'options' => ['desc' => 'تنازلي', 'asc' => 'تصاعدي']]
    ]"
    search-placeholder="البحث في المستخدمين..."
    :search-value="request('search')"
    :action-url="route('dashboard.users.index')" />

<!-- Users Table -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="bi bi-people me-2"></i>
            قائمة المستخدمين
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>المستخدم</th>
                        <th>البريد الإلكتروني</th>
                        <th>رقم الهاتف</th>
                        <th>الأدوار</th>
                        <th>الحالة</th>
                        <th>تاريخ الإنشاء</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="user-avatar me-3">
                                    @if($user->avatar)
                                        <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->full_name }}" class="rounded-circle" width="40" height="40">
                                    @else
                                        <div class="avatar-placeholder">{{ $user->display_name }}</div>
                                    @endif
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $user->full_name }}</h6>
                                    <small class="text-muted">{{ $user->city ?? 'غير محدد' }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="text-primary">{{ $user->email }}</span>
                            @if($user->email_verified_at)
                                <i class="bi bi-check-circle text-success ms-1" title="مؤكد"></i>
                            @endif
                        </td>
                        <td>
                            @if($user->phone)
                                <div class="d-flex align-items-center">
                                    <span class="me-2">{{ $user->phone }}</span>
                                    @if($user->phone_verified_at)
                                        <i class="bi bi-check-circle text-success me-2" title="مؤكد"></i>
                                    @endif
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $user->phone) }}"
                                       target="_blank"
                                       class="btn btn-sm whatsapp-btn"
                                       title="إرسال رسالة واتساب">
                                        <i class="bi bi-whatsapp"></i>
                                    </a>
                                </div>
                            @else
                                <span class="text-muted">غير محدد</span>
                            @endif
                        </td>
                        <td>
                            @foreach($user->roles as $role)
                                <span class="badge badge-primary me-1">{{ $role->name }}</span>
                            @endforeach
                        </td>
                        <td>
                            @if($user->is_active)
                                <span class="badge badge-success">نشط</span>
                            @else
                                <span class="badge badge-danger">معطل</span>
                            @endif
                        </td>
                        <td>
                            <span class="text-muted">{{ $user->created_at->format('Y-m-d') }}</span>
                            <br>
                            <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('dashboard.users.show', $user) }}" class="btn btn-sm btn-outline-primary" title="عرض">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('dashboard.users.edit', $user) }}" class="btn btn-sm btn-outline-secondary" title="تعديل">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('dashboard.users.toggle-status', $user) }}" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-outline-{{ $user->is_active ? 'warning' : 'success' }}" title="{{ $user->is_active ? 'تعطيل' : 'تفعيل' }}">
                                            <i class="bi bi-{{ $user->is_active ? 'pause' : 'play' }}"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('dashboard.users.destroy', $user) }}" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا المستخدم؟')">
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
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <div class="text-muted">
                                <i class="bi bi-people fs-1 d-block mb-3"></i>
                                <h5>لا توجد مستخدمين</h5>
                                <p>لم يتم العثور على أي مستخدمين مطابقين للبحث.</p>
                                <a href="{{ route('dashboard.users.create') }}" class="btn btn-primary">
                                    <i class="bi bi-person-plus me-2"></i>
                                    إضافة مستخدم جديد
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($users->hasPages())
    <div class="card-footer">
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted">
                عرض {{ $users->firstItem() }} إلى {{ $users->lastItem() }} من {{ $users->total() }} مستخدم
            </div>
            <div>
                {{ $users->links() }}
            </div>
        </div>
    </div>
    @endif
</div>

@push('styles')
<style>
.user-avatar .avatar-placeholder {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-purple) 0%, var(--dark-purple) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 14px;
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

.whatsapp-btn {
    background-color: #25D366 !important;
    border-color: #25D366 !important;
    color: white !important;
    padding: 4px 8px;
    font-size: 12px;
}

.whatsapp-btn:hover {
    background-color: #128C7E !important;
    border-color: #128C7E !important;
    color: white !important;
}
</style>
@endpush
@endsection
