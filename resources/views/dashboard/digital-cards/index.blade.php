@extends('layouts.dashboard-new')

@section('title', 'إدارة البطاقات الرقمية - متجر البطاقات الرقمية')

@section('page-title', 'إدارة البطاقات الرقمية')
@section('page-subtitle', 'إدارة جميع البطاقات الرقمية في النظام')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="page-title">إدارة البطاقات الرقمية</h3>
            <p class="page-subtitle">إدارة جميع البطاقات الرقمية في النظام</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('dashboard.digital-cards.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>
                إضافة بطاقة جديدة
            </a>
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#bulkGenerateModal">
                <i class="bi bi-layers me-2"></i>
                إنشاء مجمع
            </button>
            <a href="{{ route('dashboard.digital-cards.export') }}" class="btn btn-outline-secondary">
                <i class="bi bi-download me-2"></i>
                تصدير البيانات
            </a>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="stats-grid">
    <x-dashboard.stats-card
        title="إجمالي البطاقات"
        :value="number_format($stats['total_cards'])"
        icon="bi-credit-card"
        change-type="positive"
        change-text="+15.3% من الشهر الماضي" />

    <x-dashboard.stats-card
        title="البطاقات المتاحة"
        :value="number_format($stats['available_cards'])"
        icon="bi-check-circle"
        change-type="positive"
        change-text="+8.7% من الشهر الماضي" />

    <x-dashboard.stats-card
        title="البطاقات المستخدمة"
        :value="number_format($stats['used_cards'])"
        icon="bi-check2-square"
        change-type="positive"
        change-text="+12.1% من الشهر الماضي" />

    <x-dashboard.stats-card
        title="البطاقات المنتهية"
        :value="number_format($stats['expired_cards'])"
        icon="bi-calendar-x"
        change-type="negative"
        change-text="تحتاج مراجعة" />

    <x-dashboard.stats-card
        title="البطاقات النشطة"
        :value="number_format($stats['active_cards'])"
        icon="bi-lightning"
        change-type="positive"
        change-text="+6.2% من الشهر الماضي" />

    <x-dashboard.stats-card
        title="البطاقات المعطلة"
        :value="number_format($stats['inactive_cards'])"
        icon="bi-pause-circle"
        change-type="neutral"
        change-text="ثابت" />
</div>

<!-- Filters -->
<x-dashboard.filters
    :filters="[
        ['name' => 'product', 'label' => 'المنتج', 'type' => 'select', 'placeholder' => 'جميع المنتجات', 'options' => $products->pluck('name', 'id')->toArray()],
        ['name' => 'status', 'label' => 'الحالة', 'type' => 'select', 'placeholder' => 'جميع الحالات', 'options' => ['active' => 'نشط', 'inactive' => 'معطل']],
        ['name' => 'usage', 'label' => 'الاستخدام', 'type' => 'select', 'placeholder' => 'جميع الاستخدامات', 'options' => ['available' => 'متاح', 'used' => 'مستخدم', 'expired' => 'منتهي']],
        ['name' => 'currency', 'label' => 'العملة', 'type' => 'select', 'placeholder' => 'جميع العملات', 'options' => ['USD' => 'دولار أمريكي', 'SAR' => 'ريال سعودي', 'EUR' => 'يورو']],
        ['name' => 'sort_by', 'label' => 'ترتيب حسب', 'type' => 'select', 'placeholder' => 'ترتيب حسب', 'options' => ['created_at' => 'تاريخ الإنشاء', 'card_code' => 'رمز البطاقة', 'value' => 'القيمة', 'expiry_date' => 'تاريخ الانتهاء']],
        ['name' => 'sort_order', 'label' => 'اتجاه الترتيب', 'type' => 'select', 'placeholder' => 'اتجاه الترتيب', 'options' => ['desc' => 'تنازلي', 'asc' => 'تصاعدي']]
    ]"
    search-placeholder="البحث في البطاقات الرقمية..."
    :search-value="request('search')"
    :action-url="route('dashboard.digital-cards.index')" />

<!-- Digital Cards Table -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="bi bi-credit-card me-2"></i>
            قائمة البطاقات الرقمية
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>رمز البطاقة</th>
                        <th>المنتج</th>
                        <th>القيمة</th>
                        <th>تاريخ الانتهاء</th>
                        <th>الحالة</th>
                        <th>الاستخدام</th>
                        <th>تاريخ الإنشاء</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($digitalCards as $card)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="card-icon me-3">
                                    <i class="bi bi-credit-card"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 font-monospace">{{ $card->card_code }}</h6>
                                    <small class="text-muted">{{ $card->serial_number }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div>
                                <h6 class="mb-0">{{ $card->product->name }}</h6>
                                <small class="text-muted">{{ $card->product->sku }}</small>
                            </div>
                        </td>
                        <td>
                            @if($card->value)
                                <span class="fw-bold">{{ number_format($card->value, 2) }} {{ $card->currency }}</span>
                            @else
                                <span class="text-muted">غير محدد</span>
                            @endif
                        </td>
                        <td>
                            @if($card->expiry_date)
                                <span class="{{ $card->isExpired() ? 'text-danger' : 'text-muted' }}">
                                    {{ $card->expiry_date->format('Y-m-d') }}
                                </span>
                                @if($card->isExpired())
                                    <br><small class="text-danger">منتهي</small>
                                @endif
                            @else
                                <span class="text-muted">بدون انتهاء</span>
                            @endif
                        </td>
                        <td>
                            @switch($card->status)
                                @case('active')
                                    <span class="badge badge-success">نشط</span>
                                    @break
                                @case('inactive')
                                    <span class="badge badge-secondary">معطل</span>
                                    @break
                                @case('used')
                                    <span class="badge badge-primary">مستخدم</span>
                                    @break
                                @case('expired')
                                    <span class="badge badge-danger">منتهي</span>
                                    @break
                            @endswitch
                        </td>
                        <td>
                            @if($card->is_used)
                                <div>
                                    <span class="badge badge-warning">مستخدم</span>
                                    @if($card->usedBy)
                                        <br><small class="text-muted">{{ $card->usedBy->name }}</small>
                                    @endif
                                    @if($card->used_at)
                                        <br><small class="text-muted">{{ $card->used_at->format('Y-m-d') }}</small>
                                    @endif
                                </div>
                            @elseif($card->isExpired())
                                <span class="badge badge-danger">منتهي</span>
                            @else
                                <span class="badge badge-success">متاح</span>
                            @endif
                        </td>
                        <td>
                            <span class="text-muted">{{ $card->created_at->format('Y-m-d') }}</span>
                            <br>
                            <small class="text-muted">{{ $card->created_at->diffForHumans() }}</small>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('dashboard.digital-cards.show', $card) }}" class="btn btn-sm btn-outline-primary" title="عرض">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('dashboard.digital-cards.edit', $card) }}" class="btn btn-sm btn-outline-secondary" title="تعديل">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if($card->status === 'active' || $card->status === 'inactive')
                                <form method="POST" action="{{ route('dashboard.digital-cards.toggle-status', $card) }}" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-outline-{{ $card->status === 'active' ? 'warning' : 'success' }}" title="{{ $card->status === 'active' ? 'تعطيل' : 'تفعيل' }}">
                                        <i class="bi bi-{{ $card->status === 'active' ? 'pause' : 'play' }}"></i>
                                    </button>
                                </form>
                                @endif
                                <form method="POST" action="{{ route('dashboard.digital-cards.destroy', $card) }}" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه البطاقة؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <div class="text-muted">
                                <i class="bi bi-credit-card fs-1 d-block mb-3"></i>
                                <h5>لا توجد بطاقات رقمية</h5>
                                <p>لم يتم العثور على أي بطاقات رقمية مطابقة للبحث.</p>
                                <a href="{{ route('dashboard.digital-cards.create') }}" class="btn btn-primary">
                                    <i class="bi bi-plus-circle me-2"></i>
                                    إضافة بطاقة جديدة
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($digitalCards->hasPages())
    <div class="card-footer">
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted">
                عرض {{ $digitalCards->firstItem() }} إلى {{ $digitalCards->lastItem() }} من {{ $digitalCards->total() }} بطاقة
            </div>
            <div>
                {{ $digitalCards->links() }}
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Bulk Generate Modal -->
<div class="modal fade" id="bulkGenerateModal" tabindex="-1" aria-labelledby="bulkGenerateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkGenerateModalLabel">
                    <i class="bi bi-layers me-2"></i>
                    إنشاء بطاقات مجمعة
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('dashboard.digital-cards.generate-bulk') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="product_id" class="form-label">المنتج <span class="text-danger">*</span></label>
                        <select class="form-select" id="product_id" name="product_id" required>
                            <option value="">اختر المنتج</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="quantity" class="form-label">الكمية <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="quantity" name="quantity" min="1" max="1000" required>
                        <div class="form-text">الحد الأقصى: 1000 بطاقة</div>
                    </div>
                    <div class="mb-3">
                        <label for="value" class="form-label">القيمة</label>
                        <input type="number" step="0.01" class="form-control" id="value" name="value" min="0">
                    </div>
                    <div class="mb-3">
                        <label for="currency" class="form-label">العملة <span class="text-danger">*</span></label>
                        <select class="form-select" id="currency" name="currency" required>
                            <option value="USD">دولار أمريكي (USD)</option>
                            <option value="SAR">ريال سعودي (SAR)</option>
                            <option value="EUR">يورو (EUR)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="expiry_date" class="form-label">تاريخ الانتهاء</label>
                        <input type="date" class="form-control" id="expiry_date" name="expiry_date" min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-layers me-2"></i>
                        إنشاء البطاقات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
.card-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    background: linear-gradient(135deg, var(--primary-purple) 0%, var(--dark-purple) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 16px;
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

.font-monospace {
    font-family: 'Courier New', monospace;
}
</style>
@endpush
@endsection
