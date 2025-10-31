@extends('layouts.dashboard-new')

@section('title', 'إدارة الإحالات - متجر البطاقات الرقمية')

@section('page-title', 'إدارة الإحالات')
@section('page-subtitle', 'عرض وإدارة نظام الإحالات والمكافآت')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="page-title">إدارة الإحالات</h3>
            <p class="page-subtitle">عرض وإدارة نظام الإحالات والمكافآت</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('dashboard.referrals.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>
                إضافة إحالة جديدة
            </a>
            <a href="{{ route('dashboard.referrals.export') }}" class="btn btn-outline-success">
                <i class="bi bi-download me-2"></i>
                تصدير CSV
            </a>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="stats-grid">
    <x-dashboard.stats-card
        title="إجمالي الإحالات"
        :value="number_format($stats['total_referrals'])"
        icon="bi-people"
        change-type="positive"
        change-text="+12.5% من الشهر الماضي" />

    <x-dashboard.stats-card
        title="الإحالات النشطة"
        :value="number_format($stats['active_referrals'])"
        icon="bi-check-circle"
        change-type="positive"
        change-text="+8.3% من الشهر الماضي" />

    <x-dashboard.stats-card
        title="الإحالات المكتملة"
        :value="number_format($stats['completed_referrals'])"
        icon="bi-trophy"
        change-type="positive"
        change-text="+15.7% من الشهر الماضي" />

    <x-dashboard.stats-card
        title="إجمالي العمولات"
        :value="number_format($stats['total_commission'], 2) . ' $'"
        icon="bi-currency-dollar"
        change-type="positive"
        change-text="+22.1% من الشهر الماضي" />

    <x-dashboard.stats-card
        title="إجمالي المكافآت"
        :value="number_format($stats['total_rewards'], 2) . ' $'"
        icon="bi-gift"
        change-type="positive"
        change-text="+18.9% من الشهر الماضي" />

    <x-dashboard.stats-card
        title="الإحالات الحديثة"
        :value="number_format($stats['recent_referrals'])"
        icon="bi-clock"
        change-type="positive"
        change-text="+5.2% من الشهر الماضي" />
</div>

<!-- Top Referrers -->
@if($stats['top_referrers']->count() > 0)
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="bi bi-trophy me-2"></i>
            أفضل المحيلين
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            @foreach($stats['top_referrers'] as $topReferrer)
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="d-flex align-items-center p-3 border rounded">
                    <div class="user-avatar me-3">
                        @if($topReferrer->referrer->avatar)
                            <img src="{{ Storage::url($topReferrer->referrer->avatar) }}" alt="{{ $topReferrer->referrer->full_name }}" class="rounded-circle" width="40" height="40">
                        @else
                            <div class="avatar-placeholder">{{ $topReferrer->referrer->display_name }}</div>
                        @endif
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-0">{{ $topReferrer->referrer->full_name }}</h6>
                        <small class="text-muted">{{ $topReferrer->referrer->email }}</small>
                    </div>
                    <div class="text-end">
                        <span class="badge badge-primary">{{ $topReferrer->count }} إحالة</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

<!-- Filters -->
<x-dashboard.filters
    :filters="[
        ['name' => 'status', 'label' => 'الحالة', 'type' => 'select', 'placeholder' => 'جميع الحالات', 'options' => ['active' => 'نشط', 'completed' => 'مكتمل', 'expired' => 'منتهي', 'cancelled' => 'ملغي']],
        ['name' => 'referrer_id', 'label' => 'المحيل', 'type' => 'select', 'placeholder' => 'جميع المحيلين', 'options' => $users->pluck('full_name', 'id')->toArray()],
        ['name' => 'period', 'label' => 'الفترة', 'type' => 'select', 'placeholder' => 'جميع الفترات', 'options' => ['week' => 'أسبوع', 'month' => 'شهر', 'quarter' => 'ربع سنة', 'year' => 'سنة']],
        ['name' => 'sort_by', 'label' => 'ترتيب حسب', 'type' => 'select', 'placeholder' => 'ترتيب حسب', 'options' => ['created_at' => 'تاريخ الإنشاء', 'referral_code' => 'كود الإحالة', 'commission_amount' => 'مبلغ العمولة', 'status' => 'الحالة']],
        ['name' => 'sort_order', 'label' => 'اتجاه الترتيب', 'type' => 'select', 'placeholder' => 'اتجاه الترتيب', 'options' => ['desc' => 'تنازلي', 'asc' => 'تصاعدي']]
    ]"
    search-placeholder="البحث في الإحالات..."
    :search-value="request('search')"
    :action-url="route('dashboard.referrals.index')" />

<!-- Referrals Table -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="bi bi-people me-2"></i>
            قائمة الإحالات
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>كود الإحالة</th>
                        <th>المستخدم المحيل</th>
                        <th>المستخدم المحال إليه</th>
                        <th>الحالة</th>
                        <th>العمولة</th>
                        <th>المكافأة</th>
                        <th>تاريخ الانتهاء</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($referrals as $referral)
                    <tr>
                        <td>
                            <div>
                                <span class="fw-bold font-monospace">{{ $referral->referral_code }}</span>
                                <br>
                                <small class="text-muted">{{ $referral->created_at->format('Y-m-d') }}</small>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="user-avatar me-3">
                                    @if($referral->referrer->avatar)
                                        <img src="{{ Storage::url($referral->referrer->avatar) }}" alt="{{ $referral->referrer->full_name }}" class="rounded-circle" width="32" height="32">
                                    @else
                                        <div class="avatar-placeholder">{{ $referral->referrer->display_name }}</div>
                                    @endif
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $referral->referrer->full_name }}</h6>
                                    <small class="text-muted">{{ $referral->referrer->email }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="user-avatar me-3">
                                    @if($referral->referred->avatar)
                                        <img src="{{ Storage::url($referral->referred->avatar) }}" alt="{{ $referral->referred->full_name }}" class="rounded-circle" width="32" height="32">
                                    @else
                                        <div class="avatar-placeholder">{{ $referral->referred->display_name }}</div>
                                    @endif
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $referral->referred->full_name }}</h6>
                                    <small class="text-muted">{{ $referral->referred->email }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($referral->status === 'active')
                                <span class="badge badge-success">نشط</span>
                            @elseif($referral->status === 'completed')
                                <span class="badge badge-primary">مكتمل</span>
                            @elseif($referral->status === 'expired')
                                <span class="badge badge-warning">منتهي</span>
                            @elseif($referral->status === 'cancelled')
                                <span class="badge badge-secondary">ملغي</span>
                            @endif
                            @if($referral->isExpired())
                                <br><small class="text-danger">منتهي الصلاحية</small>
                            @endif
                        </td>
                        <td>
                            <div>
                                @if($referral->commission_amount > 0)
                                    <span class="fw-bold text-success">${{ number_format($referral->commission_amount, 2) }}</span>
                                @endif
                                @if($referral->commission_percentage > 0)
                                    <br><small class="text-muted">{{ $referral->commission_percentage }}%</small>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div>
                                @if($referral->reward_amount > 0)
                                    <span class="fw-bold text-info">${{ number_format($referral->reward_amount, 2) }}</span>
                                @endif
                                @if($referral->reward_percentage > 0)
                                    <br><small class="text-muted">{{ $referral->reward_percentage }}%</small>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($referral->expires_at)
                                <span class="text-muted">{{ $referral->expires_at->format('Y-m-d') }}</span>
                                <br>
                                <small class="text-muted">{{ $referral->expires_at->diffForHumans() }}</small>
                            @else
                                <span class="text-muted">لا ينتهي</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('dashboard.referrals.show', $referral) }}" class="btn btn-sm btn-outline-primary" title="عرض التفاصيل">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('dashboard.referrals.edit', $referral) }}" class="btn btn-sm btn-outline-secondary" title="تعديل">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if(!$referral->isCompleted())
                                    <form method="POST" action="{{ route('dashboard.referrals.mark-completed', $referral) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success" title="وضع علامة كمكتمل">
                                            <i class="bi bi-check-circle"></i>
                                        </button>
                                    </form>
                                @endif
                                @if($referral->status !== 'cancelled')
                                    <form method="POST" action="{{ route('dashboard.referrals.cancel', $referral) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-warning" title="إلغاء">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    </form>
                                @endif
                                @if($referral->rewards()->processed()->count() == 0)
                                    <form method="POST" action="{{ route('dashboard.referrals.destroy', $referral) }}" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه الإحالة؟')">
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
                        <td colspan="8" class="text-center py-4">
                            <div class="text-muted">
                                <i class="bi bi-people fs-1 d-block mb-3"></i>
                                <h5>لا توجد إحالات</h5>
                                <p>لم يتم العثور على أي إحالات مطابقة للبحث.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($referrals->hasPages())
    <div class="card-footer">
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted">
                عرض {{ $referrals->firstItem() }} إلى {{ $referrals->lastItem() }} من {{ $referrals->total() }} إحالة
            </div>
            <div>
                {{ $referrals->links() }}
            </div>
        </div>
    </div>
    @endif
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
