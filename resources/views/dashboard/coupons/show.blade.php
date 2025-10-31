@extends('layouts.dashboard-new')

@section('title', 'عرض الكوبون - ' . $coupon->name . ' - متجر البطاقات الرقمية')

@section('page-title', 'عرض الكوبون')
@section('page-subtitle', 'تفاصيل الكوبون: ' . $coupon->name)

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="page-title">عرض الكوبون</h3>
            <p class="page-subtitle">تفاصيل الكوبون: {{ $coupon->name }}</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('dashboard.coupons.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-right me-2"></i>
                العودة للقائمة
            </a>
            <a href="{{ route('dashboard.coupons.edit', $coupon) }}" class="btn btn-primary">
                <i class="bi bi-pencil me-2"></i>
                تعديل الكوبون
            </a>
        </div>
    </div>
</div>

<div class="row">
    <!-- معلومات الكوبون الأساسية -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    معلومات الكوبون
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">كود الكوبون:</label>
                    <p class="mb-0 font-monospace fs-5 text-primary">{{ $coupon->code }}</p>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">اسم الكوبون:</label>
                    <p class="mb-0">{{ $coupon->name }}</p>
                </div>

                @if($coupon->description)
                <div class="mb-3">
                    <label class="form-label fw-bold">الوصف:</label>
                    <p class="mb-0">{{ $coupon->description }}</p>
                </div>
                @endif

                <div class="mb-3">
                    <label class="form-label fw-bold">نوع الخصم:</label>
                    <p class="mb-0">
                        @if($coupon->type === 'percentage')
                            <span class="badge badge-info">
                                <i class="bi bi-percent me-1"></i>
                                نسبة مئوية
                            </span>
                        @else
                            <span class="badge badge-success">
                                <i class="bi bi-currency-dollar me-1"></i>
                                مبلغ ثابت
                            </span>
                        @endif
                    </p>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">قيمة الخصم:</label>
                    <p class="mb-0 fs-5 text-success">
                        {{ $coupon->value }}
                        @if($coupon->type === 'percentage')
                            %
                        @else
                            $
                        @endif
                    </p>
                </div>

                @if($coupon->minimum_amount)
                <div class="mb-3">
                    <label class="form-label fw-bold">الحد الأدنى للطلب:</label>
                    <p class="mb-0">{{ $coupon->minimum_amount }}$</p>
                </div>
                @endif

                @if($coupon->maximum_discount)
                <div class="mb-3">
                    <label class="form-label fw-bold">الحد الأقصى للخصم:</label>
                    <p class="mb-0">{{ $coupon->maximum_discount }}$</p>
                </div>
                @endif

                <div class="mb-3">
                    <label class="form-label fw-bold">الحالة:</label>
                    <p class="mb-0">
                        @if($coupon->is_active)
                            @if($coupon->isValid())
                                <span class="badge badge-success">نشط</span>
                            @else
                                <span class="badge badge-warning">منتهي</span>
                            @endif
                        @else
                            <span class="badge badge-secondary">معطل</span>
                        @endif
                    </p>
                </div>

                @if($coupon->starts_at)
                <div class="mb-3">
                    <label class="form-label fw-bold">تاريخ البداية:</label>
                    <p class="mb-0">{{ $coupon->starts_at->format('Y-m-d H:i:s') }}</p>
                    <small class="text-muted">{{ $coupon->starts_at->diffForHumans() }}</small>
                </div>
                @endif

                @if($coupon->expires_at)
                <div class="mb-3">
                    <label class="form-label fw-bold">تاريخ الانتهاء:</label>
                    <p class="mb-0">{{ $coupon->expires_at->format('Y-m-d H:i:s') }}</p>
                    <small class="text-muted">{{ $coupon->expires_at->diffForHumans() }}</small>
                </div>
                @endif

                <div class="mb-3">
                    <label class="form-label fw-bold">الخيارات:</label>
                    <div>
                        @if($coupon->first_time_only)
                            <span class="badge badge-info me-2">للمرة الأولى فقط</span>
                        @endif
                        @if($coupon->stackable)
                            <span class="badge badge-success">قابل للتجميع</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- إحصائيات الكوبون -->
    <div class="col-lg-8">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="bi bi-graph-up fs-1 text-primary mb-3"></i>
                        <h3 class="text-primary">{{ number_format($couponStats['total_usage']) }}</h3>
                        <p class="text-muted mb-0">إجمالي الاستخدامات</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="bi bi-currency-dollar fs-1 text-success mb-3"></i>
                        <h3 class="text-success">${{ number_format($couponStats['total_discount_given'], 2) }}</h3>
                        <p class="text-muted mb-0">إجمالي الخصومات</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="bi bi-people fs-1 text-info mb-3"></i>
                        <h3 class="text-info">{{ number_format($couponStats['unique_users']) }}</h3>
                        <p class="text-muted mb-0">المستخدمين الفريدين</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="bi bi-percent fs-1 text-warning mb-3"></i>
                        <h3 class="text-warning">{{ number_format($couponStats['usage_rate'], 1) }}%</h3>
                        <p class="text-muted mb-0">معدل الاستخدام</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- تفاصيل الاستخدام -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-clock-history me-2"></i>
                    الاستخدامات الحديثة
                </h5>
            </div>
            <div class="card-body p-0">
                @if($recentUsages->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>المستخدم</th>
                                <th>مبلغ الخصم</th>
                                <th>تاريخ الاستخدام</th>
                                <th>عنوان IP</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentUsages as $usage)
                            <tr>
                                <td>
                                    @if($usage->user)
                                        <div class="d-flex align-items-center">
                                            <div class="user-avatar me-3">
                                                @if($usage->user->avatar)
                                                    <img src="{{ Storage::url($usage->user->avatar) }}" alt="{{ $usage->user->full_name }}" class="rounded-circle" width="32" height="32">
                                                @else
                                                    <div class="avatar-placeholder">{{ $usage->user->display_name }}</div>
                                                @endif
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $usage->user->full_name }}</h6>
                                                <small class="text-muted">{{ $usage->user->email }}</small>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">زائر</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-bold text-success">${{ number_format($usage->discount_amount, 2) }}</span>
                                </td>
                                <td>
                                    <span class="text-muted">{{ $usage->used_at->format('Y-m-d H:i:s') }}</span>
                                    <br>
                                    <small class="text-muted">{{ $usage->used_at->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <span class="font-monospace">{{ $usage->ip_address ?? 'غير محدد' }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <div class="text-muted">
                        <i class="bi bi-clock-history fs-1 d-block mb-3"></i>
                        <h5>لا توجد استخدامات</h5>
                        <p>لم يتم استخدام هذا الكوبون بعد.</p>
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
                            <label class="form-label fw-bold">حد الاستخدام:</label>
                            <p class="mb-0">
                                {{ $coupon->used_count }}
                                @if($coupon->usage_limit)
                                    / {{ $coupon->usage_limit }}
                                @else
                                    / لا يوجد حد
                                @endif
                            </p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">حد المستخدم:</label>
                            <p class="mb-0">
                                @if($coupon->user_limit)
                                    {{ $coupon->user_limit }} استخدام لكل مستخدم
                                @else
                                    لا يوجد حد
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">متوسط الخصم:</label>
                            <p class="mb-0">${{ number_format($couponStats['avg_discount_per_use'], 2) }}</p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">الاستخدامات الأخيرة:</label>
                            <p class="mb-0">{{ $couponStats['recent_usage'] }} في آخر 30 يوم</p>
                        </div>
                    </div>
                </div>

                @if($coupon->applicable_products || $coupon->applicable_categories || $coupon->applicable_users)
                <div class="mt-4">
                    <h6 class="fw-bold">القيود:</h6>
                    @if($coupon->applicable_products)
                        <div class="mb-2">
                            <span class="badge badge-info">منتجات محددة: {{ count($coupon->applicable_products) }} منتج</span>
                        </div>
                    @endif
                    @if($coupon->applicable_categories)
                        <div class="mb-2">
                            <span class="badge badge-info">فئات محددة: {{ count($coupon->applicable_categories) }} فئة</span>
                        </div>
                    @endif
                    @if($coupon->applicable_users)
                        <div class="mb-2">
                            <span class="badge badge-info">مستخدمين محددين: {{ count($coupon->applicable_users) }} مستخدم</span>
                        </div>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.user-avatar {
    width: 32px;
    height: 32px;
}

.avatar-placeholder {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-purple) 0%, var(--dark-purple) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 12px;
    font-weight: bold;
}

.font-monospace {
    font-family: 'Courier New', monospace;
}

.badge {
    font-size: 0.75rem;
}
</style>
@endpush
@endsection
