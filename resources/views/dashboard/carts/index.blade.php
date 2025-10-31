@extends('layouts.dashboard-new')

@section('title', 'إدارة السلة - متجر البطاقات الرقمية')

@section('page-title', 'إدارة السلة')
@section('page-subtitle', 'عرض وإدارة سلات التسوق')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="page-title">إدارة السلة</h3>
            <p class="page-subtitle">عرض وإدارة سلات التسوق والسلة المتروكة</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('dashboard.carts.export') }}" class="btn btn-outline-success">
                <i class="bi bi-download me-2"></i>
                تصدير CSV
            </a>
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#sendNotificationModal">
                <i class="bi bi-send me-2"></i>
                إرسال إشعارات
            </button>
            <form method="POST" action="{{ route('dashboard.carts.cleanup') }}" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف السلات المتروكة القديمة (أكثر من 90 يوم)؟')">
                @csrf
                <button type="submit" class="btn btn-outline-warning">
                    <i class="bi bi-trash me-2"></i>
                    تنظيف السلات القديمة
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="stats-grid">
    <x-dashboard.stats-card
        title="إجمالي السلات"
        :value="number_format($stats['total_carts'])"
        icon="bi-cart"
        change-type="positive"
        change-text="+12.5% من الشهر الماضي" />

    <x-dashboard.stats-card
        title="السلات النشطة"
        :value="number_format($stats['active_carts'])"
        icon="bi-cart-check"
        change-type="positive"
        change-text="+8.3% من الشهر الماضي" />

    <x-dashboard.stats-card
        title="السلات المتروكة"
        :value="number_format($stats['abandoned_carts'])"
        icon="bi-cart-x"
        change-type="warning"
        change-text="+15.7% من الشهر الماضي" />

    <x-dashboard.stats-card
        title="القيمة الإجمالية"
        :value="number_format($stats['total_value'], 2) . ' $'"
        icon="bi-currency-dollar"
        change-type="positive"
        change-text="+5.1% من الشهر الماضي" />

    <x-dashboard.stats-card
        title="قيمة السلات المتروكة"
        :value="number_format($stats['abandoned_value'], 2) . ' $'"
        icon="bi-exclamation-triangle"
        change-type="warning"
        change-text="+3.2% من الشهر الماضي" />

    <x-dashboard.stats-card
        title="متوسط قيمة السلة"
        :value="number_format($stats['avg_cart_value'], 2) . ' $'"
        icon="bi-graph-up"
        change-type="neutral"
        change-text="ثابت" />
</div>

<!-- Filters -->
<x-dashboard.filters
    :filters="[
        ['name' => 'status', 'label' => 'الحالة', 'type' => 'select', 'placeholder' => 'جميع الحالات', 'options' => ['active' => 'نشطة', 'abandoned' => 'متروكة']],
        ['name' => 'user_id', 'label' => 'المستخدم', 'type' => 'select', 'placeholder' => 'جميع المستخدمين', 'options' => $users->pluck('full_name', 'id')->toArray()],
        ['name' => 'period', 'label' => 'الفترة', 'type' => 'select', 'placeholder' => 'جميع الفترات', 'options' => ['day' => 'اليوم', 'week' => 'أسبوع', 'month' => 'شهر', 'quarter' => 'ربع سنة', 'year' => 'سنة']],
        ['name' => 'sort_by', 'label' => 'ترتيب حسب', 'type' => 'select', 'placeholder' => 'ترتيب حسب', 'options' => ['created_at' => 'تاريخ الإنشاء', 'total_amount' => 'القيمة الإجمالية', 'last_activity_at' => 'آخر نشاط']],
        ['name' => 'sort_order', 'label' => 'اتجاه الترتيب', 'type' => 'select', 'placeholder' => 'اتجاه الترتيب', 'options' => ['desc' => 'تنازلي', 'asc' => 'تصاعدي']]
    ]"
    search-placeholder="البحث في السلات..."
    :search-value="request('search')"
    :action-url="route('dashboard.carts.index')" />

<!-- Carts Table -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="bi bi-cart me-2"></i>
            قائمة السلات
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>المستخدم</th>
                        <th>معرف الجلسة</th>
                        <th>المنتجات</th>
                        <th>القيمة الإجمالية</th>
                        <th>الكوبون</th>
                        <th>تاريخ الإنشاء</th>
                        <th>آخر نشاط</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($carts as $cart)
                    <tr>
                        <td>
                            @if($cart->user)
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar me-3">
                                        @if($cart->user->avatar)
                                            <img src="{{ Storage::url($cart->user->avatar) }}" alt="{{ $cart->user->full_name }}" class="rounded-circle" width="32" height="32">
                                        @else
                                            <div class="avatar-placeholder">{{ $cart->user->display_name }}</div>
                                        @endif
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $cart->user->full_name }}</h6>
                                        <small class="text-muted">{{ $cart->user->email }}</small>
                                    </div>
                                </div>
                            @else
                                <span class="text-muted">زائر</span>
                            @endif
                        </td>
                        <td>
                            <span class="font-monospace">{{ $cart->session_id ? substr($cart->session_id, 0, 8) . '...' : 'غير محدد' }}</span>
                        </td>
                        <td>
                            <div>
                                <span class="fw-bold">{{ $cart->items_count }} منتج</span>
                                <br>
                                <small class="text-muted">{{ $cart->items->count() }} عنصر مختلف</small>
                            </div>
                        </td>
                        <td>
                            <div>
                                <span class="fw-bold">{{ number_format($cart->total_amount, 2) }} {{ $cart->currency }}</span>
                                @if($cart->discount_amount > 0)
                                    <br>
                                    <small class="text-success">خصم: {{ number_format($cart->discount_amount, 2) }}</small>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($cart->coupon_code)
                                <span class="badge badge-info">{{ $cart->coupon_code }}</span>
                            @else
                                <span class="text-muted">لا يوجد</span>
                            @endif
                        </td>
                        <td>
                            <span class="text-muted">{{ $cart->created_at->format('Y-m-d H:i:s') }}</span>
                            <br>
                            <small class="text-muted">{{ $cart->created_at->diffForHumans() }}</small>
                        </td>
                        <td>
                            @if($cart->last_activity_at)
                                <span class="text-muted">{{ $cart->last_activity_at->format('Y-m-d H:i:s') }}</span>
                                <br>
                                <small class="text-muted">{{ $cart->last_activity_at->diffForHumans() }}</small>
                            @else
                                <span class="text-muted">غير محدد</span>
                            @endif
                        </td>
                        <td>
                            @if($cart->is_abandoned)
                                <span class="badge badge-warning">متروكة</span>
                                @if($cart->abandoned_at)
                                    <br>
                                    <small class="text-muted">{{ $cart->abandoned_at->diffForHumans() }}</small>
                                @endif
                            @else
                                <span class="badge badge-success">نشطة</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('dashboard.carts.show', $cart) }}" class="btn btn-sm btn-outline-primary" title="عرض التفاصيل">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($cart->is_abandoned && $cart->user_id)
                                    <button type="button" class="btn btn-sm btn-outline-info" title="إرسال إشعار" onclick="sendIndividualNotification({{ $cart->id }})">
                                        <i class="bi bi-send"></i>
                                    </button>
                                @endif
                                @if($cart->is_abandoned)
                                    <form method="POST" action="{{ route('dashboard.carts.restore', $cart) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success" title="استعادة السلة">
                                            <i class="bi bi-arrow-clockwise"></i>
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('dashboard.carts.mark-abandoned', $cart) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-warning" title="وضع علامة كمتروكة">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('dashboard.carts.destroy', $cart) }}" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه السلة؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف السلة">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4">
                            <div class="text-muted">
                                <i class="bi bi-cart fs-1 d-block mb-3"></i>
                                <h5>لا توجد سلات</h5>
                                <p>لم يتم العثور على أي سلات مطابقة للبحث.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($carts->hasPages())
    <div class="card-footer">
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted">
                عرض {{ $carts->firstItem() }} إلى {{ $carts->lastItem() }} من {{ $carts->total() }} سلة
            </div>
            <div>
                {{ $carts->links() }}
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

.btn-group .btn {
    margin-left: 2px;
}

.btn-group .btn:first-child {
    margin-left: 0;
}
</style>
@endpush

<!-- Send Notification Modal -->
<div class="modal fade" id="sendNotificationModal" tabindex="-1" aria-labelledby="sendNotificationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sendNotificationModalLabel">
                    <i class="bi bi-send me-2"></i>
                    إرسال إشعارات للسلة المتروكة
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="bulkNotificationForm">
                    @csrf

                    <!-- Cart Selection -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">اختيار السلات</label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="selectAllCarts">
                                    <label class="form-check-label" for="selectAllCarts">
                                        تحديد الكل
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">السلات المتروكة فقط</small>
                            </div>
                        </div>
                        <div class="mt-2" id="cartSelectionList" style="max-height: 200px; overflow-y: auto;">
                            @foreach($carts->where('is_abandoned', true)->where('user_id', '!=', null) as $cart)
                            <div class="form-check">
                                <input class="form-check-input cart-checkbox" type="checkbox" name="cart_ids[]" value="{{ $cart->id }}" id="cart_{{ $cart->id }}">
                                <label class="form-check-label" for="cart_{{ $cart->id }}">
                                    {{ $cart->user->full_name ?? 'مستخدم غير محدد' }} - {{ number_format($cart->total_amount, 2) }} {{ $cart->currency }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Template Selection -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">نموذج الإشعار</label>
                        <select class="form-select" id="notificationTemplate" onchange="loadTemplate()">
                            <option value="">اختر نموذج...</option>
                            <option value="friendly_reminder">تذكير ودود</option>
                            <option value="urgent_reminder">تذكير عاجل</option>
                            <option value="discount_offer">عرض خصم</option>
                            <option value="last_chance">الفرصة الأخيرة</option>
                        </select>
                    </div>

                    <!-- Channels -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">قنوات الإرسال</label>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="channels[]" value="email" id="channel_email">
                                    <label class="form-check-label" for="channel_email">
                                        <i class="bi bi-envelope me-1"></i>البريد الإلكتروني
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="channels[]" value="sms" id="channel_sms">
                                    <label class="form-check-label" for="channel_sms">
                                        <i class="bi bi-phone me-1"></i>SMS
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="channels[]" value="whatsapp" id="channel_whatsapp">
                                    <label class="form-check-label" for="channel_whatsapp">
                                        <i class="bi bi-whatsapp me-1"></i>واتساب
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="channels[]" value="database" id="channel_database" checked>
                                    <label class="form-check-label" for="channel_database">
                                        <i class="bi bi-database me-1"></i>قاعدة البيانات
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Subject -->
                    <div class="mb-4">
                        <label for="notificationSubject" class="form-label fw-bold">عنوان الإشعار</label>
                        <input type="text" class="form-control" id="notificationSubject" name="subject" placeholder="عنوان الإشعار">
                    </div>

                    <!-- Message -->
                    <div class="mb-4">
                        <label for="notificationMessage" class="form-label fw-bold">نص الإشعار</label>
                        <textarea class="form-control" id="notificationMessage" name="message" rows="6" placeholder="نص الإشعار..."></textarea>
                        <div class="form-text">
                            <strong>متغيرات متاحة:</strong><br>
                            <code>@{{user_name}}</code> - <code>@{{cart_total}}</code> - <code>@{{cart_currency}}</code> - <code>@{{cart_items_count}}</code> - <code>@{{cart_items_list}}</code> - <code>@{{cart_url}}</code> - <code>@{{checkout_url}}</code> - <code>@{{site_name}}</code>
                        </div>
                    </div>

                    <!-- Priority -->
                    <div class="mb-4">
                        <label for="notificationPriority" class="form-label fw-bold">الأولوية</label>
                        <select class="form-select" id="notificationPriority" name="priority">
                            <option value="normal">عادي</option>
                            <option value="low">منخفض</option>
                            <option value="high">عالي</option>
                            <option value="urgent">عاجل</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-primary" onclick="sendBulkNotifications()">
                    <i class="bi bi-send me-1"></i>
                    إرسال الإشعارات
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Individual Notification Modal -->
<div class="modal fade" id="individualNotificationModal" tabindex="-1" aria-labelledby="individualNotificationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="individualNotificationModalLabel">
                    <i class="bi bi-send me-2"></i>
                    إرسال إشعار للسلة
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="individualNotificationForm">
                    @csrf
                    <input type="hidden" id="individualCartId" name="cart_id">

                    <!-- Template Selection -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">نموذج الإشعار</label>
                        <select class="form-select" id="individualTemplate" onchange="loadIndividualTemplate()">
                            <option value="">اختر نموذج...</option>
                            <option value="friendly_reminder">تذكير ودود</option>
                            <option value="urgent_reminder">تذكير عاجل</option>
                            <option value="discount_offer">عرض خصم</option>
                            <option value="last_chance">الفرصة الأخيرة</option>
                        </select>
                    </div>

                    <!-- Channels -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">قنوات الإرسال</label>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="channels[]" value="email" id="individual_channel_email">
                                    <label class="form-check-label" for="individual_channel_email">
                                        <i class="bi bi-envelope me-1"></i>البريد الإلكتروني
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="channels[]" value="sms" id="individual_channel_sms">
                                    <label class="form-check-label" for="individual_channel_sms">
                                        <i class="bi bi-phone me-1"></i>SMS
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="channels[]" value="whatsapp" id="individual_channel_whatsapp">
                                    <label class="form-check-label" for="individual_channel_whatsapp">
                                        <i class="bi bi-whatsapp me-1"></i>واتساب
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="channels[]" value="database" id="individual_channel_database" checked>
                                    <label class="form-check-label" for="individual_channel_database">
                                        <i class="bi bi-database me-1"></i>قاعدة البيانات
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Subject -->
                    <div class="mb-4">
                        <label for="individualSubject" class="form-label fw-bold">عنوان الإشعار</label>
                        <input type="text" class="form-control" id="individualSubject" name="subject" placeholder="عنوان الإشعار">
                    </div>

                    <!-- Message -->
                    <div class="mb-4">
                        <label for="individualMessage" class="form-label fw-bold">نص الإشعار</label>
                        <textarea class="form-control" id="individualMessage" name="message" rows="6" placeholder="نص الإشعار..."></textarea>
                    </div>

                    <!-- Priority -->
                    <div class="mb-4">
                        <label for="individualPriority" class="form-label fw-bold">الأولوية</label>
                        <select class="form-select" id="individualPriority" name="priority">
                            <option value="normal">عادي</option>
                            <option value="low">منخفض</option>
                            <option value="high">عالي</option>
                            <option value="urgent">عاجل</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-primary" onclick="sendIndividualNotificationSubmit()">
                    <i class="bi bi-send me-1"></i>
                    إرسال الإشعار
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let notificationTemplates = {};

// Load templates on page load
document.addEventListener('DOMContentLoaded', function() {
    loadNotificationTemplates();
});

function loadNotificationTemplates() {
    fetch('{{ route("dashboard.carts.notification-templates") }}')
        .then(response => response.json())
        .then(data => {
            notificationTemplates = data;
        })
        .catch(error => {
            console.error('Error loading templates:', error);
        });
}

function loadTemplate() {
    const templateSelect = document.getElementById('notificationTemplate');
    const template = templateSelect.value;

    if (template && notificationTemplates[template]) {
        document.getElementById('notificationSubject').value = notificationTemplates[template].subject;
        document.getElementById('notificationMessage').value = notificationTemplates[template].message;
    }
}

function loadIndividualTemplate() {
    const templateSelect = document.getElementById('individualTemplate');
    const template = templateSelect.value;

    if (template && notificationTemplates[template]) {
        document.getElementById('individualSubject').value = notificationTemplates[template].subject;
        document.getElementById('individualMessage').value = notificationTemplates[template].message;
    }
}

function sendIndividualNotification(cartId) {
    document.getElementById('individualCartId').value = cartId;
    const modal = new bootstrap.Modal(document.getElementById('individualNotificationModal'));
    modal.show();
}

function sendIndividualNotificationSubmit() {
    const form = document.getElementById('individualNotificationForm');
    const formData = new FormData(form);
    const cartId = document.getElementById('individualCartId').value;

    // Add cart_id to form data
    formData.append('cart_id', cartId);

    fetch(`/dashboard/carts/${cartId}/send-notification`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.message) {
            showAlert('success', data.message);
            bootstrap.Modal.getInstance(document.getElementById('individualNotificationModal')).hide();
        } else if (data.error) {
            showAlert('error', data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'حدث خطأ أثناء إرسال الإشعار');
    });
}

function sendBulkNotifications() {
    const form = document.getElementById('bulkNotificationForm');
    const formData = new FormData(form);

    // Get selected cart IDs
    const selectedCarts = Array.from(document.querySelectorAll('.cart-checkbox:checked')).map(cb => cb.value);

    if (selectedCarts.length === 0) {
        showAlert('warning', 'يرجى اختيار سلة واحدة على الأقل');
        return;
    }

    // Add cart IDs to form data
    selectedCarts.forEach(cartId => {
        formData.append('cart_ids[]', cartId);
    });

    fetch('{{ route("dashboard.carts.send-bulk-notifications") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.message) {
            showAlert('success', data.message);
            bootstrap.Modal.getInstance(document.getElementById('sendNotificationModal')).hide();

            // Show detailed results
            if (data.results) {
                const results = data.results;
                let message = `تم إرسال الإشعارات بنجاح!\n\n`;
                message += `إجمالي السلات: ${results.total_carts}\n`;
                message += `تم الإرسال بنجاح: ${results.successful_sends}\n`;
                message += `فشل الإرسال: ${results.failed_sends}`;

                showAlert('info', message);
            }
        } else if (data.error) {
            showAlert('error', data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'حدث خطأ أثناء إرسال الإشعارات');
    });
}

// Select all carts functionality
document.getElementById('selectAllCarts').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.cart-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

function showAlert(type, message) {
    const alertClass = type === 'success' ? 'alert-success' :
                     type === 'error' ? 'alert-danger' :
                     type === 'warning' ? 'alert-warning' : 'alert-info';

    const alertDiv = document.createElement('div');
    alertDiv.className = `alert ${alertClass} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    // Insert at the top of the page
    const container = document.querySelector('.page-header');
    container.parentNode.insertBefore(alertDiv, container);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}
</script>
@endpush
@endsection
