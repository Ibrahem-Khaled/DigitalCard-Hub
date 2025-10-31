@extends('layouts.dashboard-new')

@section('title', 'عرض البطاقة الرقمية - ' . $digitalCard->card_code . ' - متجر البطاقات الرقمية')

@section('page-title', 'عرض البطاقة الرقمية')
@section('page-subtitle', 'تفاصيل البطاقة الرقمية: ' . $digitalCard->card_code)

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="page-title">عرض البطاقة الرقمية</h3>
            <p class="page-subtitle">تفاصيل البطاقة الرقمية: {{ $digitalCard->card_code }}</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('dashboard.digital-cards.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-right me-2"></i>
                العودة للقائمة
            </a>
            <a href="{{ route('dashboard.digital-cards.edit', $digitalCard) }}" class="btn btn-primary">
                <i class="bi bi-pencil me-2"></i>
                تعديل البطاقة
            </a>
        </div>
    </div>
</div>

<div class="row">
    <!-- معلومات البطاقة الأساسية -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body text-center">
                <!-- أيقونة البطاقة -->
                <div class="card-icon-large mb-3">
                    <i class="bi bi-credit-card"></i>
                </div>

                <!-- رمز البطاقة -->
                <h4 class="mb-1 font-monospace">{{ $digitalCard->card_code }}</h4>
                <p class="text-muted mb-3">{{ $digitalCard->serial_number }}</p>

                <!-- الحالة -->
                <div class="mb-3">
                    @switch($digitalCard->status)
                        @case('active')
                            <span class="badge badge-success fs-6">نشط</span>
                            @break
                        @case('inactive')
                            <span class="badge badge-secondary fs-6">معطل</span>
                            @break
                        @case('used')
                            <span class="badge badge-primary fs-6">مستخدم</span>
                            @break
                        @case('expired')
                            <span class="badge badge-danger fs-6">منتهي</span>
                            @break
                    @endswitch
                </div>

                <!-- القيمة -->
                @if($digitalCard->value)
                <div class="mb-3">
                    <h3 class="text-primary mb-1">{{ number_format($digitalCard->value, 2) }} {{ $digitalCard->currency }}</h3>
                </div>
                @endif

                <!-- الإجراءات السريعة -->
                <div class="d-grid gap-2">
                    @if($digitalCard->status === 'active' || $digitalCard->status === 'inactive')
                    <form method="POST" action="{{ route('dashboard.digital-cards.toggle-status', $digitalCard) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-{{ $digitalCard->status === 'active' ? 'warning' : 'success' }} btn-sm">
                            <i class="bi bi-{{ $digitalCard->status === 'active' ? 'pause' : 'play' }} me-2"></i>
                            {{ $digitalCard->status === 'active' ? 'تعطيل' : 'تفعيل' }}
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- معلومات إضافية -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    معلومات إضافية
                </h5>
            </div>
            <div class="card-body">
                <div class="info-item">
                    <label>PIN:</label>
                    <span class="font-monospace">{{ $digitalCard->card_pin }}</span>
                </div>

                <div class="info-item">
                    <label>رقم البطاقة:</label>
                    <span class="font-monospace">{{ $digitalCard->card_number }}</span>
                </div>

                <div class="info-item">
                    <label>المنتج:</label>
                    <span>{{ $digitalCard->product->name }}</span>
                </div>

                <div class="info-item">
                    <label>تاريخ الإنشاء:</label>
                    <span>{{ $digitalCard->created_at->format('Y-m-d H:i:s') }}</span>
                </div>

                <div class="info-item">
                    <label>آخر تحديث:</label>
                    <span>{{ $digitalCard->updated_at->format('Y-m-d H:i:s') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- التفاصيل الإضافية -->
    <div class="col-lg-8">
        <!-- معلومات المنتج -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-box-seam me-2"></i>
                    معلومات المنتج
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item">
                            <label>اسم المنتج:</label>
                            <span>{{ $digitalCard->product->name }}</span>
                        </div>
                        <div class="info-item">
                            <label>SKU:</label>
                            <span class="font-monospace">{{ $digitalCard->product->sku }}</span>
                        </div>
                        <div class="info-item">
                            <label>السعر:</label>
                            <span>{{ number_format($digitalCard->product->price, 2) }} $</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <label>الفئة:</label>
                            <span>{{ $digitalCard->product->category->name }}</span>
                        </div>
                        <div class="info-item">
                            <label>العلامة التجارية:</label>
                            <span>{{ $digitalCard->product->brand ?? 'غير محدد' }}</span>
                        </div>
                        <div class="info-item">
                            <label>نوع المنتج:</label>
                            <span>{{ $digitalCard->product->is_digital ? 'رقمي' : 'مادي' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- معلومات الاستخدام -->
        @if($digitalCard->is_used)
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-person-check me-2"></i>
                    معلومات الاستخدام
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item">
                            <label>المستخدم:</label>
                            <span>{{ $digitalCard->usedBy ? $digitalCard->usedBy->name : 'غير محدد' }}</span>
                        </div>
                        <div class="info-item">
                            <label>تاريخ الاستخدام:</label>
                            <span>{{ $digitalCard->used_at ? $digitalCard->used_at->format('Y-m-d H:i:s') : 'غير محدد' }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        @if($digitalCard->orderItem)
                        <div class="info-item">
                            <label>رقم الطلب:</label>
                            <span>{{ $digitalCard->orderItem->order_id ?? 'غير محدد' }}</span>
                        </div>
                        @endif
                        <div class="info-item">
                            <label>حالة الاستخدام:</label>
                            <span class="badge badge-warning">مستخدم</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- معلومات الانتهاء -->
        @if($digitalCard->expiry_date)
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-calendar-event me-2"></i>
                    معلومات الانتهاء
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item">
                            <label>تاريخ الانتهاء:</label>
                            <span class="{{ $digitalCard->isExpired() ? 'text-danger' : 'text-success' }}">
                                {{ $digitalCard->expiry_date->format('Y-m-d') }}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <label>الحالة:</label>
                            @if($digitalCard->isExpired())
                                <span class="badge badge-danger">منتهي</span>
                            @else
                                <span class="badge badge-success">صالح</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- الملاحظات -->
        @if($digitalCard->notes)
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-file-text me-2"></i>
                    الملاحظات
                </h5>
            </div>
            <div class="card-body">
                <p class="mb-0">{{ $digitalCard->notes }}</p>
            </div>
        </div>
        @endif

        <!-- الإحصائيات -->
        <div class="row mt-3">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="bi bi-clock fs-1 text-primary mb-2"></i>
                        <h4>{{ $digitalCard->created_at->diffInDays(now()) }}</h4>
                        <p class="text-muted mb-0">يوم منذ الإنشاء</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="bi bi-currency-dollar fs-1 text-success mb-2"></i>
                        <h4>{{ $digitalCard->value ? number_format($digitalCard->value, 2) : '0.00' }}</h4>
                        <p class="text-muted mb-0">{{ $digitalCard->currency }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="bi bi-check-circle fs-1 text-info mb-2"></i>
                        <h4>{{ $digitalCard->is_used ? 'مستخدم' : 'متاح' }}</h4>
                        <p class="text-muted mb-0">حالة الاستخدام</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.card-icon-large {
    width: 120px;
    height: 120px;
    border-radius: 12px;
    background: linear-gradient(135deg, var(--primary-purple) 0%, var(--dark-purple) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
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

.font-monospace {
    font-family: 'Courier New', monospace;
}
</style>
@endpush
@endsection
