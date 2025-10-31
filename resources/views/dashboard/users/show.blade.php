@extends('layouts.dashboard-new')

@section('title', 'عرض المستخدم - ' . $user->full_name . ' - متجر البطاقات الرقمية')

@section('page-title', 'عرض المستخدم')
@section('page-subtitle', 'تفاصيل المستخدم: ' . $user->full_name)

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="page-title">عرض المستخدم</h3>
            <p class="page-subtitle">تفاصيل المستخدم: {{ $user->full_name }}</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('dashboard.users.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-right me-2"></i>
                العودة للقائمة
            </a>
            <a href="{{ route('dashboard.users.sessions', $user) }}" class="btn btn-outline-info">
                <i class="bi bi-clock-history me-2"></i>
                جلسات المستخدم
            </a>
            <a href="{{ route('dashboard.users.edit', $user) }}" class="btn btn-primary">
                <i class="bi bi-pencil me-2"></i>
                تعديل المستخدم
            </a>
        </div>
    </div>
</div>

<div class="row">
    <!-- معلومات المستخدم الأساسية -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body text-center">
                <!-- الصورة الشخصية -->
                <div class="user-avatar-large mb-3">
                    @if($user->avatar)
                        <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->full_name }}" class="rounded-circle" width="120" height="120">
                    @else
                        <div class="avatar-placeholder-large">{{ $user->display_name }}</div>
                    @endif
                </div>

                <!-- الاسم والحالة -->
                <h4 class="mb-1">{{ $user->full_name }}</h4>
                <p class="text-muted mb-3">{{ $user->email }}</p>

                <!-- الحالة -->
                <div class="mb-3">
                    @if($user->is_active)
                        <span class="badge badge-success fs-6">نشط</span>
                    @else
                        <span class="badge badge-danger fs-6">معطل</span>
                    @endif
                </div>

                <!-- الأدوار -->
                <div class="mb-3">
                    @foreach($user->roles as $role)
                        <span class="badge badge-primary me-1">{{ $role->name }}</span>
                    @endforeach
                </div>

                <!-- الإجراءات السريعة -->
                <div class="d-grid gap-2">
                    @if($user->id !== auth()->id())
                        <form method="POST" action="{{ route('dashboard.users.toggle-status', $user) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-{{ $user->is_active ? 'warning' : 'success' }} btn-sm">
                                <i class="bi bi-{{ $user->is_active ? 'pause' : 'play' }} me-2"></i>
                                {{ $user->is_active ? 'تعطيل' : 'تفعيل' }}
                            </button>
                        </form>
                    @endif

                    <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#resetPasswordModal">
                        <i class="bi bi-key me-2"></i>
                        إعادة تعيين كلمة المرور
                    </button>
                </div>
            </div>
        </div>

        <!-- معلومات الاتصال -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-telephone me-2"></i>
                    معلومات الاتصال
                </h5>
            </div>
            <div class="card-body">
                <div class="info-item">
                    <label>البريد الإلكتروني:</label>
                    <div class="d-flex align-items-center">
                        <span>{{ $user->email }}</span>
                        @if($user->email_verified_at)
                            <i class="bi bi-check-circle text-success ms-2" title="مؤكد"></i>
                        @else
                            <i class="bi bi-exclamation-circle text-warning ms-2" title="غير مؤكد"></i>
                        @endif
                    </div>
                </div>

                <div class="info-item">
                    <label>رقم الهاتف:</label>
                    <div class="d-flex align-items-center">
                        <span class="me-2">{{ $user->phone ?? 'غير محدد' }}</span>
                        @if($user->phone && $user->phone_verified_at)
                            <i class="bi bi-check-circle text-success me-2" title="مؤكد"></i>
                        @elseif($user->phone)
                            <i class="bi bi-exclamation-circle text-warning me-2" title="غير مؤكد"></i>
                        @endif
                        @if($user->phone)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $user->phone) }}"
                               target="_blank"
                               class="btn btn-sm whatsapp-btn"
                               title="إرسال رسالة واتساب">
                                <i class="bi bi-whatsapp"></i>
                            </a>
                        @endif
                    </div>
                </div>

                <div class="info-item">
                    <label>العنوان:</label>
                    <span>{{ $user->address ?? 'غير محدد' }}</span>
                </div>

                <div class="info-item">
                    <label>المدينة:</label>
                    <span>{{ $user->city ?? 'غير محدد' }}</span>
                </div>

                <div class="info-item">
                    <label>الدولة:</label>
                    <span>{{ $user->country ?? 'غير محدد' }}</span>
                </div>

                <div class="info-item">
                    <label>الرمز البريدي:</label>
                    <span>{{ $user->postal_code ?? 'غير محدد' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- التفاصيل الإضافية -->
    <div class="col-lg-8">
        <!-- المعلومات الشخصية -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-person me-2"></i>
                    المعلومات الشخصية
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item">
                            <label>تاريخ الميلاد:</label>
                            <span>{{ $user->birth_date ? $user->birth_date->format('Y-m-d') : 'غير محدد' }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <label>الجنس:</label>
                            <span>
                                @if($user->gender === 'male')
                                    ذكر
                                @elseif($user->gender === 'female')
                                    أنثى
                                @else
                                    غير محدد
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- معلومات النظام -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-gear me-2"></i>
                    معلومات النظام
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item">
                            <label>تاريخ الإنشاء:</label>
                            <span>{{ $user->created_at->format('Y-m-d H:i:s') }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <label>آخر تحديث:</label>
                            <span>{{ $user->updated_at->format('Y-m-d H:i:s') }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <label>آخر تسجيل دخول:</label>
                            <span>{{ $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i:s') : 'لم يسجل دخول بعد' }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <label>آخر IP:</label>
                            <span>{{ $user->last_login_ip ?? 'غير محدد' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- الإحصائيات -->
        <div class="row mt-3">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="bi bi-cart-check fs-1 text-primary mb-2"></i>
                        <h4>{{ $user->orders->count() }}</h4>
                        <p class="text-muted mb-0">الطلبات</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="bi bi-currency-dollar fs-1 text-success mb-2"></i>
                        <h4>{{ number_format($user->payments->sum('amount'), 2) }} $</h4>
                        <p class="text-muted mb-0">إجمالي المدفوعات</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal إعادة تعيين كلمة المرور -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">إعادة تعيين كلمة المرور</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('dashboard.users.reset-password', $user) }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="password" class="form-label">كلمة المرور الجديدة</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">تأكيد كلمة المرور</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">إعادة تعيين</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
.user-avatar-large .avatar-placeholder-large {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-purple) 0%, var(--dark-purple) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 48px;
    margin: 0 auto;
}

.info-item {
    margin-bottom: 1rem;
}

.info-item label {
    font-weight: 600;
    color: var(--text-dark);
    display: block;
    margin-bottom: 0.25rem;
}

.info-item span {
    color: var(--text-muted);
}

.badge {
    font-size: 0.875rem;
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
