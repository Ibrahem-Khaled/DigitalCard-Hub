@extends('layouts.dashboard')

@section('title', 'إرسال بريد إلكتروني')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">إرسال بريد إلكتروني</h1>
            <p class="text-muted">أرسل رسائل بريد إلكتروني للاختبار أو التواصل مع العملاء</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-info" id="test-config-btn">
                <i class="fas fa-cog"></i> اختبار الإعدادات
            </button>
            <button type="button" class="btn btn-secondary" id="check-status-btn">
                <i class="fas fa-info-circle"></i> حالة الإعدادات
            </button>
        </div>
    </div>

    <!-- Email Status Alert -->
    <div id="email-status-alert" class="alert alert-info d-none">
        <i class="fas fa-info-circle"></i>
        <span id="email-status-text"></span>
    </div>

    <!-- Main Content -->
    <div class="row">
        <!-- Email Form -->
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-envelope"></i> إرسال رسالة جديدة
                    </h5>
                </div>
                <div class="card-body">
                    <form id="email-form" enctype="multipart/form-data">
                        @csrf

                        <!-- Recipient Information -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="recipient_email" class="form-label">
                                        <i class="fas fa-user"></i> البريد الإلكتروني للمستقبل *
                                    </label>
                                    <input type="email"
                                           class="form-control"
                                           id="recipient_email"
                                           name="recipient_email"
                                           placeholder="example@domain.com"
                                           required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="recipient_name" class="form-label">
                                        <i class="fas fa-user-tag"></i> اسم المستقبل
                                    </label>
                                    <input type="text"
                                           class="form-control"
                                           id="recipient_name"
                                           name="recipient_name"
                                           placeholder="اسم المستقبل (اختياري)">
                                </div>
                            </div>
                        </div>

                        <!-- Email Content -->
                        <div class="form-group mb-3">
                            <label for="subject" class="form-label">
                                <i class="fas fa-tag"></i> موضوع الرسالة *
                            </label>
                            <input type="text"
                                   class="form-control"
                                   id="subject"
                                   name="subject"
                                   placeholder="موضوع الرسالة"
                                   required>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="message" class="form-label">
                                <i class="fas fa-edit"></i> محتوى الرسالة *
                            </label>
                            <textarea class="form-control"
                                      id="message"
                                      name="message"
                                      rows="8"
                                      placeholder="اكتب محتوى الرسالة هنا..."
                                      required></textarea>
                            <div class="form-text">يمكنك استخدام HTML في محتوى الرسالة</div>
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Sender Information -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="sender_name" class="form-label">
                                        <i class="fas fa-signature"></i> اسم المرسل
                                    </label>
                                    <input type="text"
                                           class="form-control"
                                           id="sender_name"
                                           name="sender_name"
                                           placeholder="اسم المرسل (اختياري)"
                                           value="{{ config('mail.from.name', 'متجر البطاقات الرقمية') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="attachments" class="form-label">
                                        <i class="fas fa-paperclip"></i> المرفقات
                                    </label>
                                    <input type="file"
                                           class="form-control"
                                           id="attachments"
                                           name="attachments[]"
                                           multiple
                                           accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png,.gif">
                                    <div class="form-text">يمكنك إرفاق حتى 5 ملفات (10 ميجابايت لكل ملف)</div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <button type="submit" class="btn btn-primary" id="send-btn">
                                    <i class="fas fa-paper-plane"></i> إرسال الرسالة
                                </button>
                                <button type="button" class="btn btn-secondary" id="preview-btn">
                                    <i class="fas fa-eye"></i> معاينة
                                </button>
                            </div>
                            <div>
                                <button type="button" class="btn btn-outline-secondary" id="clear-btn">
                                    <i class="fas fa-trash"></i> مسح النموذج
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Email Templates & Info -->
        <div class="col-lg-4">
            <!-- Quick Templates -->
            <div class="card shadow mb-4">
                <div class="card-header bg-success text-white">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-file-alt"></i> قوالب سريعة
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-primary btn-sm template-btn" data-template="welcome">
                            <i class="fas fa-hand-wave"></i> رسالة ترحيب
                        </button>
                        <button type="button" class="btn btn-outline-success btn-sm template-btn" data-template="order-confirmation">
                            <i class="fas fa-check-circle"></i> تأكيد الطلب
                        </button>
                        <button type="button" class="btn btn-outline-info btn-sm template-btn" data-template="product-delivery">
                            <i class="fas fa-gift"></i> تسليم المنتج
                        </button>
                        <button type="button" class="btn btn-outline-warning btn-sm template-btn" data-template="promotion">
                            <i class="fas fa-percentage"></i> عرض ترويجي
                        </button>
                    </div>
                </div>
            </div>

            <!-- Email Statistics -->
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-chart-bar"></i> إحصائيات البريد
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h4 class="text-primary mb-0" id="emails-sent">0</h4>
                                <small class="text-muted">رسائل مرسلة</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="text-success mb-0" id="success-rate">0%</h4>
                            <small class="text-muted">معدل النجاح</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Email Configuration -->
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-cogs"></i> إعدادات البريد
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <small class="text-muted">الخادم:</small>
                        <span class="badge bg-secondary" id="mail-host">غير محدد</span>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">المنفذ:</small>
                        <span class="badge bg-secondary" id="mail-port">غير محدد</span>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">التشفير:</small>
                        <span class="badge bg-secondary" id="mail-encryption">غير محدد</span>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">البريد الافتراضي:</small>
                        <span class="badge bg-primary" id="mail-from">{{ config('mail.from.address', 'غير محدد') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Email Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-eye"></i> معاينة الرسالة
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="email-preview">
                    <div class="preview-header bg-light p-3 mb-3">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>إلى:</strong> <span id="preview-to"></span>
                            </div>
                            <div class="col-md-6">
                                <strong>من:</strong> <span id="preview-from"></span>
                            </div>
                        </div>
                        <div class="mt-2">
                            <strong>الموضوع:</strong> <span id="preview-subject"></span>
                        </div>
                    </div>
                    <div class="preview-content border p-3" id="preview-message">
                        <!-- Message content will be inserted here -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                <button type="button" class="btn btn-primary" id="send-from-preview">إرسال الرسالة</button>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loading-overlay" class="loading-overlay d-none">
    <div class="loading-spinner">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">جاري الإرسال...</span>
        </div>
        <p class="mt-2">جاري إرسال الرسالة...</p>
    </div>
</div>

<style>
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
}

.loading-spinner {
    text-align: center;
    color: white;
}

.email-preview {
    font-family: Arial, sans-serif;
}

.preview-content {
    min-height: 200px;
    background: white;
}

.template-btn {
    transition: all 0.3s ease;
}

.template-btn:hover {
    transform: translateY(-2px);
}

.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.form-control:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

.btn {
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-1px);
}
</style>
@endsection

@push('scripts')
<script>

document.addEventListener('DOMContentLoaded', () => {
    const emailStats = {
        sent: 0,
        success: 0,
    };

    const emailForm = document.getElementById('email-form');
    const previewBtn = document.getElementById('preview-btn');
    const sendFromPreviewBtn = document.getElementById('send-from-preview');
    const clearBtn = document.getElementById('clear-btn');
    const templateButtons = document.querySelectorAll('.template-btn');
    const testConfigBtn = document.getElementById('test-config-btn');
    const checkStatusBtn = document.getElementById('check-status-btn');
    const attachmentsInput = document.getElementById('attachments');
    const sendBtn = document.getElementById('send-btn');

    const previewModal = new bootstrap.Modal(document.getElementById('previewModal'));

    init();

    function init() {
        loadEmailStatus();
        loadEmailStats();

        if (emailForm) {
            emailForm.addEventListener('submit', (e) => {
                e.preventDefault();
                sendEmail();
            });
        }

        if (previewBtn) {
            previewBtn.addEventListener('click', showPreview);
        }

        if (sendFromPreviewBtn) {
            sendFromPreviewBtn.addEventListener('click', () => {
                previewModal.hide();
                sendEmail();
            });
        }

        if (clearBtn) {
            clearBtn.addEventListener('click', clearForm);
        }

        templateButtons.forEach((btn) => {
            btn.addEventListener('click', () => loadTemplate(btn.dataset.template));
        });

        if (testConfigBtn) {
            testConfigBtn.addEventListener('click', testEmailConfig);
        }

        if (checkStatusBtn) {
            checkStatusBtn.addEventListener('click', loadEmailStatus);
        }

        if (attachmentsInput) {
            attachmentsInput.addEventListener('change', validateAttachments);
        }
    }

    function sendEmail() {
        const formData = new FormData(emailForm);
        showLoading();
        clearValidation();

        fetch('{{ route("dashboard.email.send") }}', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: formData,
        })
            .then(async (response) => {
                hideLoading();

                const data = await response.json();

                if (response.ok && data.success) {
                    showAlert('success', data.message);
                    emailStats.sent += 1;
                    emailStats.success += 1;
                    updateEmailStats();
                    clearForm();
                } else {
                    showAlert('danger', data.message || 'حدث خطأ في إرسال الرسالة');
                    if (data.errors) {
                        showValidationErrors(data.errors);
                    }
                }
            })
            .catch((error) => {
                console.error(error);
                hideLoading();
                showAlert('danger', 'حدث خطأ في إرسال الرسالة');
            });
    }

    function showPreview() {
        const recipientEmail = document.getElementById('recipient_email').value;
        const recipientName = document.getElementById('recipient_name').value || 'عزيزي العميل';
        const subject = document.getElementById('subject').value;
        const message = document.getElementById('message').value;
        const senderName = document.getElementById('sender_name').value || '{{ config('mail.from.name', 'متجر البطاقات الرقمية') }}';

        if (!recipientEmail || !subject || !message) {
            showAlert('warning', 'يرجى ملء جميع الحقول المطلوبة');
            return;
        }

        document.getElementById('preview-to').textContent = recipientEmail;
        document.getElementById('preview-from').textContent = senderName;
        document.getElementById('preview-subject').textContent = subject;
        document.getElementById('preview-message').innerHTML = convertToPreview(message);

        previewModal.show();
    }

    function clearForm() {
        emailForm.reset();
        clearValidation();
    }

    function loadTemplate(template) {
        const templates = {
            welcome: {
                subject: 'مرحباً بك في متجر البطاقات الرقمية',
                message: 'مرحباً بك في متجر البطاقات الرقمية!\n\nنحن سعداء لانضمامك إلينا. يمكنك الآن الاستمتاع بأفضل العروض والمنتجات الرقمية.\n\nشكراً لك!',
            },
            'order-confirmation': {
                subject: 'تأكيد طلبك - متجر البطاقات الرقمية',
                message: 'تم تأكيد طلبك بنجاح!\n\nسيتم معالجة طلبك وإرسال المنتجات إلى بريدك الإلكتروني خلال 24 ساعة.\n\nشكراً لثقتك بنا!',
            },
            'product-delivery': {
                subject: 'منتجاتك جاهزة - متجر البطاقات الرقمية',
                message: 'مرحباً!\n\nمنتجاتك جاهزة الآن. يمكنك العثور على تفاصيل المنتجات في المرفقات.\n\nاستمتع بمنتجاتك!',
            },
            promotion: {
                subject: 'عرض خاص - خصم 20% على جميع المنتجات',
                message: 'عرض خاص لفترة محدودة!\n\nاحصل على خصم 20% على جميع المنتجات في متجر البطاقات الرقمية.\n\nاستخدم الكود: SPECIAL20\n\nالعرض ساري حتى نهاية الشهر!',
            },
        };

        if (templates[template]) {
            document.getElementById('subject').value = templates[template].subject;
            document.getElementById('message').value = templates[template].message;
        }
    }

    function testEmailConfig() {
        showLoading();

        fetch('{{ route("dashboard.email.test-config") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({}),
        })
            .then(async (response) => {
                hideLoading();
                const data = await response.json();
                showAlert(response.ok && data.success ? 'success' : 'danger', data.message || 'حدث خطأ في اختبار الإعدادات');
            })
            .catch(() => {
                hideLoading();
                showAlert('danger', 'حدث خطأ في اختبار الإعدادات');
            });
    }

    function loadEmailStatus() {
        fetch('{{ route("dashboard.email.status") }}', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    updateEmailStatusDisplay(data.data);
                }
            })
            .catch(() => {
                console.warn('Failed to load email status');
            });
    }

    function updateEmailStatusDisplay(data) {
        document.getElementById('mail-host').textContent = data.mail_host || 'غير محدد';
        document.getElementById('mail-port').textContent = data.mail_port || 'غير محدد';
        document.getElementById('mail-encryption').textContent = data.mail_encryption || 'غير محدد';
        document.getElementById('mail-from').textContent = data.mail_from_address || 'غير محدد';
    }

    function loadEmailStats() {
        const saved = localStorage.getItem('email_stats');
        if (saved) {
            const parsed = JSON.parse(saved);
            emailStats.sent = parsed.sent || 0;
            emailStats.success = parsed.success || 0;
            updateEmailStats();
        }
    }

    function updateEmailStats() {
        document.getElementById('emails-sent').textContent = emailStats.sent;
        const successRate = emailStats.sent > 0 ? Math.round((emailStats.success / emailStats.sent) * 100) : 0;
        document.getElementById('success-rate').textContent = `${successRate}%`;
        localStorage.setItem('email_stats', JSON.stringify(emailStats));
    }

    function validateAttachments(event) {
        const files = Array.from(event.target.files || []);
        if (files.length > 5) {
            alert('لا يمكن إرفاق أكثر من 5 ملفات');
            event.target.value = '';
            return;
        }

        for (const file of files) {
            if (file.size > 10 * 1024 * 1024) {
                alert(`الملف ${file.name} أكبر من 10 ميجابايت`);
                event.target.value = '';
                break;
            }
        }
    }

    function showValidationErrors(errors) {
        Object.keys(errors).forEach((field) => {
            const input = document.querySelector(`[name="${field}"]`);
            if (input) {
                input.classList.add('is-invalid');
                const feedback = input.nextElementSibling;
                if (feedback && feedback.classList.contains('invalid-feedback')) {
                    feedback.textContent = errors[field][0];
                }
            }
        });
    }

    function clearValidation() {
        document.querySelectorAll('.form-control').forEach((input) => {
            input.classList.remove('is-invalid');
            const feedback = input.nextElementSibling;
            if (feedback && feedback.classList.contains('invalid-feedback')) {
                feedback.textContent = '';
            }
        });
    }

    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success'
            : type === 'warning' ? 'alert-warning'
                : 'alert-danger';

        const icon = type === 'success'
            ? 'check-circle'
            : type === 'warning'
                ? 'exclamation-triangle'
                : 'times-circle';

        const alert = document.createElement('div');
        alert.className = `alert ${alertClass} alert-dismissible fade show`;
        alert.role = 'alert';
        alert.innerHTML = `
            <i class="fas fa-${icon}"></i> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        const container = document.querySelector('.container-fluid');
        container.insertBefore(alert, container.firstChild);

        setTimeout(() => {
            const bootstrapAlert = new bootstrap.Alert(alert);
            bootstrapAlert.close();
        }, 5000);
    }

    function showLoading() {
        document.getElementById('loading-overlay').classList.remove('d-none');
        sendBtn.disabled = true;
    }

    function hideLoading() {
        document.getElementById('loading-overlay').classList.add('d-none');
        sendBtn.disabled = false;
    }

    function convertToPreview(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML.replace(/\n/g, '<br>');
    }
});
</script>
@endpush


