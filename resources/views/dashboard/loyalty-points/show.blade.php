@extends('layouts.dashboard-new')

@section('title', 'عرض نقاط الولاء - متجر البطاقات الرقمية')

@section('page-title', 'عرض نقاط الولاء')
@section('page-subtitle', 'تفاصيل نقاط الولاء')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="page-title">عرض نقاط الولاء</h3>
            <p class="page-subtitle">تفاصيل نقاط الولاء</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('dashboard.loyalty-points.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-right me-2"></i>
                العودة للقائمة
            </a>
            <a href="{{ route('dashboard.loyalty-points.edit', $loyaltyPoint) }}" class="btn btn-primary">
                <i class="bi bi-pencil me-2"></i>
                تعديل النقاط
            </a>
        </div>
    </div>
</div>

<div class="row">
    <!-- معلومات النقاط الأساسية -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    معلومات النقاط
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">عدد النقاط:</label>
                    <p class="mb-0 fs-4 {{ $loyaltyPoint->points > 0 ? 'text-success' : 'text-danger' }}">
                        {{ $loyaltyPoint->points > 0 ? '+' : '' }}{{ number_format($loyaltyPoint->points) }}
                    </p>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">النوع:</label>
                    <p class="mb-0">
                        @if($loyaltyPoint->type === 'earned')
                            <span class="badge badge-success">مكتسب</span>
                        @elseif($loyaltyPoint->type === 'redeemed')
                            <span class="badge badge-warning">مسترد</span>
                        @elseif($loyaltyPoint->type === 'expired')
                            <span class="badge badge-secondary">منتهي</span>
                        @elseif($loyaltyPoint->type === 'bonus')
                            <span class="badge badge-info">مكافأة</span>
                        @endif
                    </p>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">المصدر:</label>
                    <p class="mb-0">{{ $loyaltyPoint->source }}</p>
                    @if($loyaltyPoint->source_id)
                        <small class="text-muted">معرف المصدر: {{ $loyaltyPoint->source_id }}</small>
                    @endif
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">الحالة:</label>
                    <p class="mb-0">
                        @if($loyaltyPoint->isActive())
                            <span class="badge badge-success">نشط</span>
                        @elseif($loyaltyPoint->isExpired())
                            <span class="badge badge-warning">منتهي</span>
                        @else
                            <span class="badge badge-secondary">غير نشط</span>
                        @endif
                    </p>
                </div>

                @if($loyaltyPoint->expires_at)
                <div class="mb-3">
                    <label class="form-label fw-bold">تاريخ الانتهاء:</label>
                    <p class="mb-0">{{ $loyaltyPoint->expires_at->format('Y-m-d H:i:s') }}</p>
                    <small class="text-muted">{{ $loyaltyPoint->expires_at->diffForHumans() }}</small>
                </div>
                @endif

                @if($loyaltyPoint->description)
                <div class="mb-3">
                    <label class="form-label fw-bold">الوصف:</label>
                    <p class="mb-0">{{ $loyaltyPoint->description }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- إحصائيات النقاط -->
    <div class="col-lg-8">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="bi bi-star fs-1 text-primary mb-3"></i>
                        <h3 class="text-primary">{{ number_format($pointStats['total_points']) }}</h3>
                        <p class="text-muted mb-0">إجمالي النقاط</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="bi bi-check-circle fs-1 text-success mb-3"></i>
                        <h3 class="text-success">{{ $pointStats['is_active'] ? 'نشط' : 'غير نشط' }}</h3>
                        <p class="text-muted mb-0">حالة النقاط</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="bi bi-clock fs-1 text-warning mb-3"></i>
                        <h3 class="text-warning">{{ number_format($pointStats['days_since_created']) }}</h3>
                        <p class="text-muted mb-0">الأيام منذ الإنشاء</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="bi bi-person fs-1 text-info mb-3"></i>
                        <h3 class="text-info">{{ number_format($pointStats['user_total_points']) }}</h3>
                        <p class="text-muted mb-0">إجمالي نقاط المستخدم</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- معلومات المستخدم -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-person me-2"></i>
                    معلومات المستخدم
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="user-avatar me-3">
                        @if($loyaltyPoint->user->avatar)
                            <img src="{{ Storage::url($loyaltyPoint->user->avatar) }}" alt="{{ $loyaltyPoint->user->full_name }}" class="rounded-circle" width="60" height="60">
                        @else
                            <div class="avatar-placeholder">{{ $loyaltyPoint->user->display_name }}</div>
                        @endif
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="mb-1">{{ $loyaltyPoint->user->full_name }}</h5>
                        <p class="text-muted mb-1">{{ $loyaltyPoint->user->email }}</p>
                        <p class="text-muted mb-0">{{ $loyaltyPoint->user->phone ?? 'لا يوجد رقم هاتف' }}</p>
                    </div>
                    <div class="text-end">
                        <div class="badge badge-primary fs-6">{{ number_format($pointStats['user_total_points']) }} نقطة</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- إحصائيات المستخدم -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-bar-chart me-2"></i>
                    إحصائيات المستخدم
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="stat-item">
                            <div class="stat-icon text-success">
                                <i class="bi bi-trophy"></i>
                            </div>
                            <div class="stat-content">
                                <h6 class="stat-value">{{ number_format($pointStats['user_earned_points']) }}</h6>
                                <p class="stat-label">النقاط المكتسبة</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="stat-item">
                            <div class="stat-icon text-warning">
                                <i class="bi bi-arrow-left-circle"></i>
                            </div>
                            <div class="stat-content">
                                <h6 class="stat-value">{{ number_format($pointStats['user_redeemed_points']) }}</h6>
                                <p class="stat-label">النقاط المستردة</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- المعاملات المرتبطة -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-list-ul me-2"></i>
                    المعاملات المرتبطة
                </h5>
            </div>
            <div class="card-body p-0">
                @if($transactions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>النقاط</th>
                                <th>النوع</th>
                                <th>المصدر</th>
                                <th>الرصيد قبل</th>
                                <th>الرصيد بعد</th>
                                <th>تاريخ المعالجة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $transaction)
                            <tr>
                                <td>
                                    <span class="fw-bold {{ $transaction->points > 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $transaction->points > 0 ? '+' : '' }}{{ number_format($transaction->points) }}
                                    </span>
                                </td>
                                <td>
                                    @if($transaction->type === 'earned')
                                        <span class="badge badge-success">مكتسب</span>
                                    @elseif($transaction->type === 'redeemed')
                                        <span class="badge badge-warning">مسترد</span>
                                    @elseif($transaction->type === 'expired')
                                        <span class="badge badge-secondary">منتهي</span>
                                    @elseif($transaction->type === 'bonus')
                                        <span class="badge badge-info">مكافأة</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-muted">{{ $transaction->source }}</span>
                                </td>
                                <td>
                                    <span class="text-muted">{{ number_format($transaction->balance_before) }}</span>
                                </td>
                                <td>
                                    <span class="text-muted">{{ number_format($transaction->balance_after) }}</span>
                                </td>
                                <td>
                                    <span class="text-muted">{{ $transaction->processed_at->format('Y-m-d H:i:s') }}</span>
                                    <br>
                                    <small class="text-muted">{{ $transaction->processed_at->diffForHumans() }}</small>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <div class="text-muted">
                        <i class="bi bi-list-ul fs-1 d-block mb-3"></i>
                        <h5>لا توجد معاملات</h5>
                        <p>لم يتم إنشاء أي معاملات لهذه النقاط بعد.</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- تفاصيل إضافية -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-list-ul me-2"></i>
                    تفاصيل إضافية
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">تاريخ الإنشاء:</label>
                            <p class="mb-0">{{ $loyaltyPoint->created_at->format('Y-m-d H:i:s') }}</p>
                            <small class="text-muted">{{ $loyaltyPoint->created_at->diffForHumans() }}</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">آخر تحديث:</label>
                            <p class="mb-0">{{ $loyaltyPoint->updated_at->format('Y-m-d H:i:s') }}</p>
                            <small class="text-muted">{{ $loyaltyPoint->updated_at->diffForHumans() }}</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">عدد المعاملات:</label>
                            <p class="mb-0">{{ $transactions->count() }}</p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">حالة الانتهاء:</label>
                            <p class="mb-0">
                                @if($pointStats['is_expired'])
                                    <span class="badge badge-warning">منتهي الصلاحية</span>
                                @else
                                    <span class="badge badge-success">صالح</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <h6 class="fw-bold">حالة النقاط:</h6>
                    <div class="d-flex gap-2">
                        @if($pointStats['is_active'])
                            <span class="badge badge-success">نشط</span>
                        @else
                            <span class="badge badge-secondary">غير نشط</span>
                        @endif
                        @if($pointStats['is_expired'])
                            <span class="badge badge-warning">منتهي الصلاحية</span>
                        @endif
                        @if($pointStats['days_since_created'] > 30)
                            <span class="badge badge-info">قديم</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.user-avatar {
    width: 60px;
    height: 60px;
}

.avatar-placeholder {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-purple) 0%, var(--dark-purple) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 16px;
    font-weight: bold;
}

.badge {
    font-size: 0.75rem;
}

.stat-item {
    display: flex;
    align-items: center;
    padding: 15px;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    background: #f8f9fa;
}

.stat-icon {
    font-size: 2rem;
    margin-left: 15px;
}

.stat-content {
    flex-grow: 1;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: bold;
    margin-bottom: 5px;
}

.stat-label {
    color: var(--text-muted);
    margin-bottom: 0;
}
</style>
@endpush
@endsection
