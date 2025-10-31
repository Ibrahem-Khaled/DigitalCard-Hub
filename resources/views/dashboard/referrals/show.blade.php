@extends('layouts.dashboard-new')

@section('title', 'عرض الإحالة - ' . $referral->referral_code . ' - متجر البطاقات الرقمية')

@section('page-title', 'عرض الإحالة')
@section('page-subtitle', 'تفاصيل الإحالة: ' . $referral->referral_code)

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="page-title">عرض الإحالة</h3>
            <p class="page-subtitle">تفاصيل الإحالة: {{ $referral->referral_code }}</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('dashboard.referrals.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-right me-2"></i>
                العودة للقائمة
            </a>
            <a href="{{ route('dashboard.referrals.edit', $referral) }}" class="btn btn-primary">
                <i class="bi bi-pencil me-2"></i>
                تعديل الإحالة
            </a>
        </div>
    </div>
</div>

<div class="row">
    <!-- معلومات الإحالة الأساسية -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    معلومات الإحالة
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">كود الإحالة:</label>
                    <p class="mb-0 font-monospace fs-5 text-primary">{{ $referral->referral_code }}</p>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">الحالة:</label>
                    <p class="mb-0">
                        @if($referral->status === 'active')
                            <span class="badge badge-success">نشط</span>
                        @elseif($referral->status === 'completed')
                            <span class="badge badge-primary">مكتمل</span>
                        @elseif($referral->status === 'expired')
                            <span class="badge badge-warning">منتهي</span>
                        @elseif($referral->status === 'cancelled')
                            <span class="badge badge-secondary">ملغي</span>
                        @endif
                    </p>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">مبلغ العمولة:</label>
                    <p class="mb-0 fs-5 text-success">
                        ${{ number_format($referral->commission_amount, 2) }}
                        @if($referral->commission_percentage > 0)
                            <small class="text-muted">({{ $referral->commission_percentage }}%)</small>
                        @endif
                    </p>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">مبلغ المكافأة:</label>
                    <p class="mb-0 fs-5 text-info">
                        ${{ number_format($referral->reward_amount, 2) }}
                        @if($referral->reward_percentage > 0)
                            <small class="text-muted">({{ $referral->reward_percentage }}%)</small>
                        @endif
                    </p>
                </div>

                @if($referral->expires_at)
                <div class="mb-3">
                    <label class="form-label fw-bold">تاريخ الانتهاء:</label>
                    <p class="mb-0">{{ $referral->expires_at->format('Y-m-d H:i:s') }}</p>
                    <small class="text-muted">{{ $referral->expires_at->diffForHumans() }}</small>
                </div>
                @endif

                @if($referral->completed_at)
                <div class="mb-3">
                    <label class="form-label fw-bold">تاريخ الإكمال:</label>
                    <p class="mb-0">{{ $referral->completed_at->format('Y-m-d H:i:s') }}</p>
                    <small class="text-muted">{{ $referral->completed_at->diffForHumans() }}</small>
                </div>
                @endif

                @if($referral->notes)
                <div class="mb-3">
                    <label class="form-label fw-bold">ملاحظات:</label>
                    <p class="mb-0">{{ $referral->notes }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- إحصائيات الإحالة -->
    <div class="col-lg-8">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="bi bi-currency-dollar fs-1 text-success mb-3"></i>
                        <h3 class="text-success">${{ number_format($referralStats['total_commission'], 2) }}</h3>
                        <p class="text-muted mb-0">إجمالي العمولة</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="bi bi-gift fs-1 text-info mb-3"></i>
                        <h3 class="text-info">${{ number_format($referralStats['total_rewards'], 2) }}</h3>
                        <p class="text-muted mb-0">إجمالي المكافآت</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="bi bi-list-check fs-1 text-primary mb-3"></i>
                        <h3 class="text-primary">{{ number_format($referralStats['rewards_count']) }}</h3>
                        <p class="text-muted mb-0">إجمالي المكافآت</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="bi bi-clock fs-1 text-warning mb-3"></i>
                        <h3 class="text-warning">{{ number_format($referralStats['days_since_created']) }}</h3>
                        <p class="text-muted mb-0">الأيام منذ الإنشاء</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- معلومات المستخدمين -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-people me-2"></i>
                    معلومات المستخدمين
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="user-info">
                            <h6 class="fw-bold text-success">المستخدم المحيل</h6>
                            <div class="d-flex align-items-center mb-3">
                                <div class="user-avatar me-3">
                                    @if($referral->referrer->avatar)
                                        <img src="{{ Storage::url($referral->referrer->avatar) }}" alt="{{ $referral->referrer->full_name }}" class="rounded-circle" width="50" height="50">
                                    @else
                                        <div class="avatar-placeholder">{{ $referral->referrer->display_name }}</div>
                                    @endif
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $referral->referrer->full_name }}</h6>
                                    <small class="text-muted">{{ $referral->referrer->email }}</small>
                                    <br>
                                    <small class="text-muted">{{ $referral->referrer->phone ?? 'لا يوجد رقم هاتف' }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="user-info">
                            <h6 class="fw-bold text-info">المستخدم المحال إليه</h6>
                            <div class="d-flex align-items-center mb-3">
                                <div class="user-avatar me-3">
                                    @if($referral->referred->avatar)
                                        <img src="{{ Storage::url($referral->referred->avatar) }}" alt="{{ $referral->referred->full_name }}" class="rounded-circle" width="50" height="50">
                                    @else
                                        <div class="avatar-placeholder">{{ $referral->referred->display_name }}</div>
                                    @endif
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $referral->referred->full_name }}</h6>
                                    <small class="text-muted">{{ $referral->referred->email }}</small>
                                    <br>
                                    <small class="text-muted">{{ $referral->referred->phone ?? 'لا يوجد رقم هاتف' }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- المكافآت المرتبطة -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-gift me-2"></i>
                    المكافآت المرتبطة
                </h5>
            </div>
            <div class="card-body p-0">
                @if($rewards->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>النوع</th>
                                <th>المبلغ</th>
                                <th>النقاط</th>
                                <th>الحالة</th>
                                <th>تاريخ المعالجة</th>
                                <th>الوصف</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rewards as $reward)
                            <tr>
                                <td>
                                    @if($reward->type === 'commission')
                                        <span class="badge badge-success">عمولة</span>
                                    @elseif($reward->type === 'reward')
                                        <span class="badge badge-info">مكافأة</span>
                                    @elseif($reward->type === 'bonus')
                                        <span class="badge badge-warning">مكافأة إضافية</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-bold">${{ number_format($reward->amount, 2) }}</span>
                                </td>
                                <td>
                                    <span class="text-muted">{{ number_format($reward->points) }}</span>
                                </td>
                                <td>
                                    @if($reward->status === 'pending')
                                        <span class="badge badge-warning">معلق</span>
                                    @elseif($reward->status === 'processed')
                                        <span class="badge badge-success">معالج</span>
                                    @elseif($reward->status === 'cancelled')
                                        <span class="badge badge-secondary">ملغي</span>
                                    @endif
                                </td>
                                <td>
                                    @if($reward->processed_at)
                                        <span class="text-muted">{{ $reward->processed_at->format('Y-m-d H:i:s') }}</span>
                                        <br>
                                        <small class="text-muted">{{ $reward->processed_at->diffForHumans() }}</small>
                                    @else
                                        <span class="text-muted">لم يتم المعالجة</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-muted">{{ $reward->description ?? 'لا يوجد وصف' }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <div class="text-muted">
                        <i class="bi bi-gift fs-1 d-block mb-3"></i>
                        <h5>لا توجد مكافآت</h5>
                        <p>لم يتم إنشاء أي مكافآت لهذه الإحالة بعد.</p>
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
                            <p class="mb-0">{{ $referral->created_at->format('Y-m-d H:i:s') }}</p>
                            <small class="text-muted">{{ $referral->created_at->diffForHumans() }}</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">آخر تحديث:</label>
                            <p class="mb-0">{{ $referral->updated_at->format('Y-m-d H:i:s') }}</p>
                            <small class="text-muted">{{ $referral->updated_at->diffForHumans() }}</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">المكافآت المعالجة:</label>
                            <p class="mb-0">{{ $referralStats['processed_rewards'] }} من {{ $referralStats['rewards_count'] }}</p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">المكافآت المعلقة:</label>
                            <p class="mb-0">{{ $referralStats['pending_rewards'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <h6 class="fw-bold">حالة الإحالة:</h6>
                    <div class="d-flex gap-2">
                        @if($referralStats['is_expired'])
                            <span class="badge badge-warning">منتهي الصلاحية</span>
                        @endif
                        @if($referralStats['is_completed'])
                            <span class="badge badge-success">مكتمل</span>
                        @endif
                        @if($referralStats['days_since_created'] > 30)
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
    width: 50px;
    height: 50px;
}

.avatar-placeholder {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-purple) 0%, var(--dark-purple) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 14px;
    font-weight: bold;
}

.font-monospace {
    font-family: 'Courier New', monospace;
}

.badge {
    font-size: 0.75rem;
}

.user-info {
    padding: 15px;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    background: #f8f9fa;
}
</style>
@endpush
@endsection
