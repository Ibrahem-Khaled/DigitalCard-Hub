@extends('layouts.dashboard-new')

@section('title', 'تعديل الإشعار - متجر البطاقات الرقمية')

@section('page-title', 'تعديل الإشعار')
@section('page-subtitle', 'تعديل الإشعار')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="page-title">تعديل الإشعار</h3>
            <p class="page-subtitle">تعديل الإشعار</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('dashboard.notifications.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-right me-2"></i>
                العودة للقائمة
            </a>
            <a href="{{ route('dashboard.notifications.show', $notification) }}" class="btn btn-outline-info">
                <i class="bi bi-eye me-2"></i>
                عرض التفاصيل
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-pencil me-2"></i>
                    تعديل معلومات الإشعار
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('dashboard.notifications.update', $notification) }}">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="user_id" class="form-label">المستخدم <span class="text-danger">*</span></label>
                                <select class="form-select @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                                    <option value="">اختر المستخدم</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('user_id', $notification->user_id) == $user->id ? 'selected' : '' }}>
                                            {{ $user->full_name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="type" class="form-label">نوع الإشعار <span class="text-danger">*</span></label>
                                <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                    <option value="">اختر نوع الإشعار</option>
                                    <option value="order" {{ old('type', $notification->type) === 'order' ? 'selected' : '' }}>طلب</option>
                                    <option value="payment" {{ old('type', $notification->type) === 'payment' ? 'selected' : '' }}>دفع</option>
                                    <option value="shipping" {{ old('type', $notification->type) === 'shipping' ? 'selected' : '' }}>شحن</option>
                                    <option value="promotion" {{ old('type', $notification->type) === 'promotion' ? 'selected' : '' }}>ترويج</option>
                                    <option value="system" {{ old('type', $notification->type) === 'system' ? 'selected' : '' }}>نظام</option>
                                    <option value="other" {{ old('type', $notification->type) === 'other' ? 'selected' : '' }}>أخرى</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="title" class="form-label">عنوان الإشعار <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control @error('title') is-invalid @enderror"
                               id="title" name="title" value="{{ old('title', $notification->title) }}"
                               placeholder="عنوان الإشعار" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="message" class="form-label">رسالة الإشعار <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('message') is-invalid @enderror"
                                  id="message" name="message" rows="4"
                                  placeholder="رسالة الإشعار" required>{{ old('message', $notification->message) }}</textarea>
                        @error('message')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="channel" class="form-label">قناة الإشعار <span class="text-danger">*</span></label>
                                <select class="form-select @error('channel') is-invalid @enderror" id="channel" name="channel" required>
                                    <option value="">اختر قناة الإشعار</option>
                                    <option value="database" {{ old('channel', $notification->channel) === 'database' ? 'selected' : '' }}>قاعدة البيانات</option>
                                    <option value="email" {{ old('channel', $notification->channel) === 'email' ? 'selected' : '' }}>البريد الإلكتروني</option>
                                    <option value="sms" {{ old('channel', $notification->channel) === 'sms' ? 'selected' : '' }}>رسالة نصية</option>
                                    <option value="push" {{ old('channel', $notification->channel) === 'push' ? 'selected' : '' }}>إشعار فوري</option>
                                </select>
                                @error('channel')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="priority" class="form-label">أولوية الإشعار <span class="text-danger">*</span></label>
                                <select class="form-select @error('priority') is-invalid @enderror" id="priority" name="priority" required>
                                    <option value="">اختر أولوية الإشعار</option>
                                    <option value="low" {{ old('priority', $notification->priority) === 'low' ? 'selected' : '' }}>منخفض</option>
                                    <option value="normal" {{ old('priority', $notification->priority) === 'normal' ? 'selected' : '' }}>عادي</option>
                                    <option value="high" {{ old('priority', $notification->priority) === 'high' ? 'selected' : '' }}>عالي</option>
                                    <option value="urgent" {{ old('priority', $notification->priority) === 'urgent' ? 'selected' : '' }}>عاجل</option>
                                </select>
                                @error('priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="scheduled_at" class="form-label">جدولة الإرسال</label>
                        <input type="datetime-local"
                               class="form-control @error('scheduled_at') is-invalid @enderror"
                               id="scheduled_at" name="scheduled_at"
                               value="{{ old('scheduled_at', $notification->scheduled_at?->format('Y-m-d\TH:i')) }}">
                        @error('scheduled_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">اتركه فارغاً للإرسال الفوري</div>
                    </div>

                    <div class="mb-3">
                        <label for="data" class="form-label">بيانات إضافية (JSON)</label>
                        <textarea class="form-control @error('data') is-invalid @enderror"
                                  id="data" name="data" rows="3"
                                  placeholder='{"key": "value"}'>{{ old('data', $notification->data ? json_encode($notification->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '') }}</textarea>
                        @error('data')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">بيانات إضافية بصيغة JSON (اختياري)</div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>
                            حفظ التغييرات
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- معلومات الإشعار الحالية -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    معلومات الإشعار الحالية
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">العنوان:</label>
                    <p class="mb-0 fs-6">{{ $notification->title }}</p>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">النوع:</label>
                    <p class="mb-0">
                        <span class="badge badge-secondary">{{ $notification->type }}</span>
                    </p>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">القناة:</label>
                    <p class="mb-0">
                        <span class="badge badge-info">{{ $this->getChannelText($notification->channel) }}</span>
                    </p>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">الأولوية:</label>
                    <p class="mb-0">
                        @if($notification->priority === 'urgent')
                            <span class="badge badge-danger">عاجل</span>
                        @elseif($notification->priority === 'high')
                            <span class="badge badge-warning">عالي</span>
                        @elseif($notification->priority === 'normal')
                            <span class="badge badge-primary">عادي</span>
                        @elseif($notification->priority === 'low')
                            <span class="badge badge-secondary">منخفض</span>
                        @endif
                    </p>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">الحالة:</label>
                    <p class="mb-0">
                        @if($notification->isUnread())
                            <span class="badge badge-warning">غير مقروء</span>
                        @elseif($notification->isRead())
                            <span class="badge badge-success">مقروء</span>
                        @endif
                        @if($notification->isSent())
                            <br><span class="badge badge-info">مرسل</span>
                        @elseif($notification->isFailed())
                            <br><span class="badge badge-danger">فاشل</span>
                        @elseif($notification->scheduled_at && $notification->scheduled_at->isFuture())
                            <br><span class="badge badge-secondary">مجدول</span>
                        @endif
                    </p>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">تاريخ الإنشاء:</label>
                    <p class="mb-0">{{ $notification->created_at->format('Y-m-d H:i:s') }}</p>
                    <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">آخر تحديث:</label>
                    <p class="mb-0">{{ $notification->updated_at->format('Y-m-d H:i:s') }}</p>
                    <small class="text-muted">{{ $notification->updated_at->diffForHumans() }}</small>
                </div>

                @if($notification->retry_count > 0)
                <div class="mb-3">
                    <label class="form-label fw-bold">عدد المحاولات:</label>
                    <p class="mb-0">{{ $notification->retry_count }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- تحذيرات مهمة -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    تحذيرات مهمة
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <h6><i class="bi bi-info-circle me-2"></i>تنبيهات:</h6>
                    <ul class="mb-0">
                        <li>تعديل الإشعارات المرسلة قد يؤثر على المستخدمين</li>
                        <li>تغيير القناة قد يؤثر على طريقة الإرسال</li>
                        <li>تعديل الجدولة قد يؤثر على وقت الإرسال</li>
                        <li>تأكد من صحة البيانات قبل الحفظ</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- إجراءات سريعة -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-lightning me-2"></i>
                    إجراءات سريعة
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if($notification->isUnread())
                        <form method="POST" action="{{ route('dashboard.notifications.mark-read', $notification) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-check-circle me-2"></i>
                                وضع علامة كمقروء
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('dashboard.notifications.mark-unread', $notification) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-warning w-100">
                                <i class="bi bi-circle me-2"></i>
                                وضع علامة كغير مقروء
                            </button>
                        </form>
                    @endif

                    @if($notification->isFailed())
                        <form method="POST" action="{{ route('dashboard.notifications.retry', $notification) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-info w-100">
                                <i class="bi bi-arrow-clockwise me-2"></i>
                                إعادة المحاولة
                            </button>
                        </form>
                    @endif

                    @if(!$notification->isSent())
                        <form method="POST" action="{{ route('dashboard.notifications.send-now', $notification) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-send me-2"></i>
                                إرسال فوراً
                            </button>
                        </form>
                    @endif

                    <form method="POST" action="{{ route('dashboard.notifications.destroy', $notification) }}" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الإشعار؟')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="bi bi-trash me-2"></i>
                            حذف الإشعار
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.badge {
    font-size: 0.75rem;
}
</style>
@endpush
@endsection
