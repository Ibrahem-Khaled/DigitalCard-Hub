@extends('layouts.dashboard-new')

@section('title', 'عرض السلة - متجر البطاقات الرقمية')

@section('page-title', 'عرض السلة')
@section('page-subtitle', 'تفاصيل السلة: #' . $cart->id)

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="page-title">عرض السلة</h3>
            <p class="page-subtitle">تفاصيل السلة: #{{ $cart->id }}</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('dashboard.carts.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-right me-2"></i>
                العودة للقائمة
            </a>
            @if($cart->is_abandoned)
                <form method="POST" action="{{ route('dashboard.carts.restore', $cart) }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-arrow-clockwise me-2"></i>
                        استعادة السلة
                    </button>
                </form>
            @else
                <form method="POST" action="{{ route('dashboard.carts.mark-abandoned', $cart) }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-x-circle me-2"></i>
                        وضع علامة كمتروكة
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>

<div class="row">
    <!-- معلومات السلة الأساسية -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    معلومات السلة
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">معرف السلة:</label>
                    <p class="mb-0">#{{ $cart->id }}</p>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">المستخدم:</label>
                    @if($cart->user)
                        <div class="d-flex align-items-center">
                            <div class="user-avatar me-3">
                                @if($cart->user->avatar)
                                    <img src="{{ Storage::url($cart->user->avatar) }}" alt="{{ $cart->user->full_name }}" class="rounded-circle" width="40" height="40">
                                @else
                                    <div class="avatar-placeholder">{{ $cart->user->display_name }}</div>
                                @endif
                            </div>
                            <div>
                                <p class="mb-0 fw-bold">{{ $cart->user->full_name }}</p>
                                <small class="text-muted">{{ $cart->user->email }}</small>
                            </div>
                        </div>
                    @else
                        <p class="mb-0 text-muted">زائر</p>
                    @endif
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">معرف الجلسة:</label>
                    <p class="mb-0 font-monospace">{{ $cart->session_id ?? 'غير محدد' }}</p>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">العملة:</label>
                    <p class="mb-0">{{ $cart->currency }}</p>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">الحالة:</label>
                    @if($cart->is_abandoned)
                        <span class="badge badge-warning">متروكة</span>
                    @else
                        <span class="badge badge-success">نشطة</span>
                    @endif
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">تاريخ الإنشاء:</label>
                    <p class="mb-0">{{ $cart->created_at->format('Y-m-d H:i:s') }}</p>
                    <small class="text-muted">{{ $cart->created_at->diffForHumans() }}</small>
                </div>

                @if($cart->last_activity_at)
                <div class="mb-3">
                    <label class="form-label fw-bold">آخر نشاط:</label>
                    <p class="mb-0">{{ $cart->last_activity_at->format('Y-m-d H:i:s') }}</p>
                    <small class="text-muted">{{ $cart->last_activity_at->diffForHumans() }}</small>
                </div>
                @endif

                @if($cart->abandoned_at)
                <div class="mb-3">
                    <label class="form-label fw-bold">تاريخ التخلي:</label>
                    <p class="mb-0">{{ $cart->abandoned_at->format('Y-m-d H:i:s') }}</p>
                    <small class="text-muted">{{ $cart->abandoned_at->diffForHumans() }}</small>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- تفاصيل المنتجات -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-box-seam me-2"></i>
                    منتجات السلة
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>المنتج</th>
                                <th>السعر</th>
                                <th>الكمية</th>
                                <th>المجموع</th>
                                <th>ملاحظات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cart->items as $item)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($item->product->image)
                                            <img src="{{ Storage::url($item->product->image) }}" alt="{{ $item->product->name }}" class="rounded me-3" width="50" height="50">
                                        @else
                                            <div class="product-placeholder me-3">
                                                <i class="bi bi-image"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <h6 class="mb-0">{{ $item->product->name }}</h6>
                                            <small class="text-muted">{{ $item->product->sku }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-bold">{{ number_format($item->price, 2) }} {{ $cart->currency }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-primary">{{ $item->quantity }}</span>
                                </td>
                                <td>
                                    <span class="fw-bold">{{ number_format($item->total_price, 2) }} {{ $cart->currency }}</span>
                                </td>
                                <td>
                                    @if($item->notes)
                                        <span class="text-muted">{{ $item->notes }}</span>
                                    @else
                                        <span class="text-muted">لا توجد ملاحظات</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="bi bi-box-seam fs-1 d-block mb-3"></i>
                                        <h5>لا توجد منتجات</h5>
                                        <p>السلة فارغة.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ملخص السلة -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-calculator me-2"></i>
                    ملخص السلة
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">عدد المنتجات:</label>
                            <p class="mb-0">{{ $cartStats['items_count'] }} منتج</p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">المبلغ الفرعي:</label>
                            <p class="mb-0">{{ number_format($cartStats['subtotal'], 2) }} {{ $cart->currency }}</p>
                        </div>

                        @if($cartStats['discount_amount'] > 0)
                        <div class="mb-3">
                            <label class="form-label fw-bold text-success">مبلغ الخصم:</label>
                            <p class="mb-0 text-success">-{{ number_format($cartStats['discount_amount'], 2) }} {{ $cart->currency }}</p>
                        </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label fw-bold">مبلغ الضريبة:</label>
                            <p class="mb-0">{{ number_format($cartStats['tax_amount'], 2) }} {{ $cart->currency }}</p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">مبلغ الشحن:</label>
                            <p class="mb-0">{{ number_format($cartStats['shipping_amount'], 2) }} {{ $cart->currency }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">المبلغ الإجمالي:</label>
                            <h4 class="text-primary mb-0">{{ number_format($cartStats['total_amount'], 2) }} {{ $cart->currency }}</h4>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">الأيام منذ الإنشاء:</label>
                            <p class="mb-0">{{ $cartStats['days_since_created'] }} يوم</p>
                        </div>

                        @if($cartStats['days_since_last_activity'])
                        <div class="mb-3">
                            <label class="form-label fw-bold">الأيام منذ آخر نشاط:</label>
                            <p class="mb-0">{{ $cartStats['days_since_last_activity'] }} يوم</p>
                        </div>
                        @endif

                        @if($cart->coupon_code)
                        <div class="mb-3">
                            <label class="form-label fw-bold">كود الكوبون:</label>
                            <p class="mb-0">
                                <span class="badge badge-info">{{ $cart->coupon_code }}</span>
                            </p>
                        </div>
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
    width: 40px;
    height: 40px;
}

.avatar-placeholder {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-purple) 0%, var(--dark-purple) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 14px;
    font-weight: bold;
}

.product-placeholder {
    width: 50px;
    height: 50px;
    border-radius: 8px;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
    font-size: 20px;
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
