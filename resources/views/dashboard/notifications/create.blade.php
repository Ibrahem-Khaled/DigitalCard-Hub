@extends('layouts.dashboard-new')

@section('title', 'إرسال إشعار جديد - متجر البطاقات الرقمية')

@section('page-title', 'إرسال إشعار جديد')
@section('page-subtitle', 'إنشاء وإرسال إشعار جديد')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="page-title">إرسال إشعار جديد</h3>
            <p class="page-subtitle">إنشاء وإرسال إشعار جديد</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('dashboard.notifications.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-right me-2"></i>
                العودة للقائمة
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-plus-circle me-2"></i>
                    معلومات الإشعار الأساسية
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('dashboard.notifications.store') }}">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="user_id" class="form-label">المستخدم <span class="text-danger">*</span></label>
                                <select class="form-select @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                                    <option value="">اختر المستخدم</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
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
                                    <option value="order" {{ old('type') === 'order' ? 'selected' : '' }}>طلب</option>
                                    <option value="payment" {{ old('type') === 'payment' ? 'selected' : '' }}>دفع</option>
                                    <option value="shipping" {{ old('type') === 'shipping' ? 'selected' : '' }}>شحن</option>
                                    <option value="promotion" {{ old('type') === 'promotion' ? 'selected' : '' }}>ترويج</option>
                                    <option value="system" {{ old('type') === 'system' ? 'selected' : '' }}>نظام</option>
                                    <option value="other" {{ old('type') === 'other' ? 'selected' : '' }}>أخرى</option>
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
                               id="title" name="title" value="{{ old('title') }}"
                               placeholder="عنوان الإشعار" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="message" class="form-label">رسالة الإشعار <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('message') is-invalid @enderror"
                                  id="message" name="message" rows="4"
                                  placeholder="رسالة الإشعار" required>{{ old('message') }}</textarea>
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
                                    <option value="database" {{ old('channel') === 'database' ? 'selected' : '' }}>قاعدة البيانات</option>
                                    <option value="email" {{ old('channel') === 'email' ? 'selected' : '' }}>البريد الإلكتروني</option>
                                    <option value="sms" {{ old('channel') === 'sms' ? 'selected' : '' }}>رسالة نصية</option>
                                    <option value="push" {{ old('channel') === 'push' ? 'selected' : '' }}>إشعار فوري</option>
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
                                    <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>منخفض</option>
                                    <option value="normal" {{ old('priority') === 'normal' ? 'selected' : '' }}>عادي</option>
                                    <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>عالي</option>
                                    <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>عاجل</option>
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
                               id="scheduled_at" name="scheduled_at" value="{{ old('scheduled_at') }}">
                        @error('scheduled_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">اتركه فارغاً للإرسال الفوري</div>
                    </div>

                    <div class="mb-3">
                        <label for="data" class="form-label">بيانات إضافية (JSON)</label>
                        <textarea class="form-control @error('data') is-invalid @enderror"
                                  id="data" name="data" rows="3"
                                  placeholder='{"key": "value"}'>{{ old('data') }}</textarea>
                        @error('data')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">بيانات إضافية بصيغة JSON (اختياري)</div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send me-2"></i>
                            إرسال الإشعار
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- نصائح وإرشادات -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-lightbulb me-2"></i>
                    نصائح وإرشادات
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h6><i class="bi bi-info-circle me-2"></i>أنواع الإشعارات:</h6>
                    <ul class="mb-0">
                        <li><strong>طلب:</strong> إشعارات متعلقة بالطلبات</li>
                        <li><strong>دفع:</strong> إشعارات متعلقة بالمدفوعات</li>
                        <li><strong>شحن:</strong> إشعارات متعلقة بالشحن</li>
                        <li><strong>ترويج:</strong> إشعارات ترويجية</li>
                        <li><strong>نظام:</strong> إشعارات النظام</li>
                    </ul>
                </div>

                <div class="alert alert-warning">
                    <h6><i class="bi bi-exclamation-triangle me-2"></i>قنوات الإشعارات:</h6>
                    <ul class="mb-0">
                        <li><strong>قاعدة البيانات:</strong> إشعارات داخلية</li>
                        <li><strong>البريد الإلكتروني:</strong> إرسال عبر الإيميل</li>
                        <li><strong>رسالة نصية:</strong> إرسال عبر SMS</li>
                        <li><strong>إشعار فوري:</strong> إشعارات Push</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- معاينة الإشعار -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-eye me-2"></i>
                    معاينة الإشعار
                </h5>
            </div>
            <div class="card-body">
                <div class="notification-preview">
                    <div class="notification-header">
                        <div class="notification-icon">
                            <i class="bi bi-bell"></i>
                        </div>
                        <div class="notification-title">عنوان الإشعار</div>
                        <div class="notification-time">الآن</div>
                    </div>
                    <div class="notification-body">
                        <p class="notification-message">رسالة الإشعار</p>
                    </div>
                    <div class="notification-footer">
                        <span class="notification-type">نوع الإشعار</span>
                        <span class="notification-channel">قناة الإشعار</span>
                        <span class="notification-priority">أولوية الإشعار</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.notification-preview {
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    padding: 20px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.notification-header {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1px solid #dee2e6;
}

.notification-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--primary-purple);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    margin-left: 15px;
}

.notification-title {
    flex-grow: 1;
    font-weight: bold;
    font-size: 1.1rem;
}

.notification-time {
    color: var(--text-muted);
    font-size: 0.9rem;
}

.notification-message {
    margin-bottom: 15px;
    color: var(--text-dark);
    line-height: 1.5;
}

.notification-footer {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.notification-type,
.notification-channel,
.notification-priority {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.8rem;
    font-weight: 500;
}

.notification-type {
    background: #e9ecef;
    color: var(--text-dark);
}

.notification-channel {
    background: #d1ecf1;
    color: #0c5460;
}

.notification-priority {
    background: #f8d7da;
    color: #721c24;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // تحديث معاينة الإشعار عند تغيير الحقول
    const formInputs = document.querySelectorAll('input, select, textarea');

    formInputs.forEach(input => {
        input.addEventListener('change', updatePreview);
        input.addEventListener('input', updatePreview);
    });

    function updatePreview() {
        const title = document.getElementById('title').value || 'عنوان الإشعار';
        const message = document.getElementById('message').value || 'رسالة الإشعار';
        const type = document.getElementById('type').selectedOptions[0]?.textContent || 'نوع الإشعار';
        const channel = document.getElementById('channel').selectedOptions[0]?.textContent || 'قناة الإشعار';
        const priority = document.getElementById('priority').selectedOptions[0]?.textContent || 'أولوية الإشعار';
        const scheduledAt = document.getElementById('scheduled_at').value;

        const titleElement = document.querySelector('.notification-title');
        const messageElement = document.querySelector('.notification-message');
        const typeElement = document.querySelector('.notification-type');
        const channelElement = document.querySelector('.notification-channel');
        const priorityElement = document.querySelector('.notification-priority');
        const timeElement = document.querySelector('.notification-time');

        titleElement.textContent = title;
        messageElement.textContent = message;
        typeElement.textContent = type;
        channelElement.textContent = channel;
        priorityElement.textContent = priority;

        // تحديث الوقت
        if (scheduledAt) {
            const scheduledDate = new Date(scheduledAt);
            timeElement.textContent = scheduledDate.toLocaleString('ar-SA');
        } else {
            timeElement.textContent = 'الآن';
        }

        // تحديث لون الأولوية
        const priorityValue = document.getElementById('priority').value;
        priorityElement.className = 'notification-priority';

        switch(priorityValue) {
            case 'urgent':
                priorityElement.style.background = '#f8d7da';
                priorityElement.style.color = '#721c24';
                break;
            case 'high':
                priorityElement.style.background = '#fff3cd';
                priorityElement.style.color = '#856404';
                break;
            case 'normal':
                priorityElement.style.background = '#d1ecf1';
                priorityElement.style.color = '#0c5460';
                break;
            case 'low':
                priorityElement.style.background = '#e9ecef';
                priorityElement.style.color = '#495057';
                break;
        }
    }

    // تحديث المعاينة عند تحميل الصفحة
    updatePreview();
});
</script>
@endpush
@endsection
