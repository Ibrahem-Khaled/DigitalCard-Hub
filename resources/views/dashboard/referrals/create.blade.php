@extends('layouts.dashboard-new')

@section('title', 'إضافة إحالة جديدة - متجر البطاقات الرقمية')

@section('page-title', 'إضافة إحالة جديدة')
@section('page-subtitle', 'إنشاء إحالة جديدة مع المكافآت والعمولات')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="page-title">إضافة إحالة جديدة</h3>
            <p class="page-subtitle">إنشاء إحالة جديدة مع المكافآت والعمولات</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('dashboard.referrals.index') }}" class="btn btn-outline-secondary">
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
                    معلومات الإحالة الأساسية
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('dashboard.referrals.store') }}">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="referrer_id" class="form-label">المستخدم المحيل <span class="text-danger">*</span></label>
                                <select class="form-select @error('referrer_id') is-invalid @enderror" id="referrer_id" name="referrer_id" required>
                                    <option value="">اختر المستخدم المحيل</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('referrer_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->full_name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('referrer_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="referred_id" class="form-label">المستخدم المحال إليه <span class="text-danger">*</span></label>
                                <select class="form-select @error('referred_id') is-invalid @enderror" id="referred_id" name="referred_id" required>
                                    <option value="">اختر المستخدم المحال إليه</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('referred_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->full_name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('referred_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="referral_code" class="form-label">كود الإحالة</label>
                                <div class="input-group">
                                    <input type="text" class="form-control @error('referral_code') is-invalid @enderror"
                                           id="referral_code" name="referral_code" value="{{ old('referral_code') }}"
                                           placeholder="سيتم إنشاؤه تلقائياً">
                                    <button type="button" class="btn btn-outline-secondary" id="generateCode">
                                        <i class="bi bi-arrow-clockwise"></i>
                                    </button>
                                </div>
                                @error('referral_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">اتركه فارغاً لإنشاء كود تلقائي</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">الحالة <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="">اختر الحالة</option>
                                    <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>نشط</option>
                                    <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>مكتمل</option>
                                    <option value="expired" {{ old('status') === 'expired' ? 'selected' : '' }}>منتهي</option>
                                    <option value="cancelled" {{ old('status') === 'cancelled' ? 'selected' : '' }}>ملغي</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="commission_amount" class="form-label">مبلغ العمولة</label>
                                <input type="number" step="0.01" min="0"
                                       class="form-control @error('commission_amount') is-invalid @enderror"
                                       id="commission_amount" name="commission_amount" value="{{ old('commission_amount') }}"
                                       placeholder="0.00">
                                @error('commission_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">المبلغ بالدولار</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="commission_percentage" class="form-label">نسبة العمولة</label>
                                <input type="number" step="0.01" min="0" max="100"
                                       class="form-control @error('commission_percentage') is-invalid @enderror"
                                       id="commission_percentage" name="commission_percentage" value="{{ old('commission_percentage') }}"
                                       placeholder="0.00">
                                @error('commission_percentage')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">النسبة المئوية (0-100)</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="reward_amount" class="form-label">مبلغ المكافأة</label>
                                <input type="number" step="0.01" min="0"
                                       class="form-control @error('reward_amount') is-invalid @enderror"
                                       id="reward_amount" name="reward_amount" value="{{ old('reward_amount') }}"
                                       placeholder="0.00">
                                @error('reward_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">المبلغ بالدولار</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="reward_percentage" class="form-label">نسبة المكافأة</label>
                                <input type="number" step="0.01" min="0" max="100"
                                       class="form-control @error('reward_percentage') is-invalid @enderror"
                                       id="reward_percentage" name="reward_percentage" value="{{ old('reward_percentage') }}"
                                       placeholder="0.00">
                                @error('reward_percentage')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">النسبة المئوية (0-100)</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="expires_at" class="form-label">تاريخ الانتهاء</label>
                                <input type="date"
                                       class="form-control @error('expires_at') is-invalid @enderror"
                                       id="expires_at" name="expires_at" value="{{ old('expires_at') }}">
                                @error('expires_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">اتركه فارغاً إذا لم ينتهِ</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">ملاحظات</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror"
                                  id="notes" name="notes" rows="3"
                                  placeholder="ملاحظات إضافية حول الإحالة">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>
                            إنشاء الإحالة
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
                    <h6><i class="bi bi-info-circle me-2"></i>نصائح لإنشاء إحالة فعالة:</h6>
                    <ul class="mb-0">
                        <li>تأكد من اختيار المستخدمين الصحيحين</li>
                        <li>حدد مبالغ أو نسب منطقية للمكافآت</li>
                        <li>ضع تاريخ انتهاء مناسب</li>
                        <li>أضف ملاحظات واضحة</li>
                    </ul>
                </div>

                <div class="alert alert-warning">
                    <h6><i class="bi bi-exclamation-triangle me-2"></i>تحذيرات مهمة:</h6>
                    <ul class="mb-0">
                        <li>لا يمكن للمستخدم أن يحيل نفسه</li>
                        <li>تأكد من صحة التواريخ</li>
                        <li>راقب الإحالات بانتظام</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- معاينة الإحالة -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-eye me-2"></i>
                    معاينة الإحالة
                </h5>
            </div>
            <div class="card-body">
                <div class="referral-preview">
                    <div class="referral-code">REF12345</div>
                    <div class="referral-details">
                        <div class="detail-item">
                            <span class="label">المحيل:</span>
                            <span class="value">اسم المستخدم</span>
                        </div>
                        <div class="detail-item">
                            <span class="label">المحال إليه:</span>
                            <span class="value">اسم المستخدم</span>
                        </div>
                        <div class="detail-item">
                            <span class="label">الحالة:</span>
                            <span class="value badge badge-success">نشط</span>
                        </div>
                        <div class="detail-item">
                            <span class="label">العمولة:</span>
                            <span class="value">$0.00</span>
                        </div>
                        <div class="detail-item">
                            <span class="label">المكافأة:</span>
                            <span class="value">$0.00</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.referral-preview {
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    padding: 20px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.referral-code {
    font-family: 'Courier New', monospace;
    font-size: 1.5rem;
    font-weight: bold;
    color: var(--primary-purple);
    margin-bottom: 15px;
    text-align: center;
}

.referral-details .detail-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
    padding: 5px 0;
    border-bottom: 1px solid #eee;
}

.referral-details .label {
    font-weight: 600;
    color: var(--text-dark);
}

.referral-details .value {
    color: var(--text-muted);
}

.badge {
    font-size: 0.75rem;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // إنشاء كود إحالة تلقائي
    const generateCodeBtn = document.getElementById('generateCode');
    const referralCodeInput = document.getElementById('referral_code');

    generateCodeBtn.addEventListener('click', function() {
        fetch('{{ route("dashboard.referrals.generate-code") }}')
            .then(response => response.json())
            .then(data => {
                referralCodeInput.value = data.code;
                updatePreview();
            })
            .catch(error => {
                console.error('Error generating code:', error);
            });
    });

    // تحديث معاينة الإحالة عند تغيير الحقول
    const formInputs = document.querySelectorAll('input, select, textarea');

    formInputs.forEach(input => {
        input.addEventListener('change', updatePreview);
        input.addEventListener('input', updatePreview);
    });

    function updatePreview() {
        const referralCode = document.getElementById('referral_code').value || 'REF12345';
        const referrerSelect = document.getElementById('referrer_id');
        const referredSelect = document.getElementById('referred_id');
        const statusSelect = document.getElementById('status');
        const commissionAmount = document.getElementById('commission_amount').value || '0.00';
        const rewardAmount = document.getElementById('reward_amount').value || '0.00';

        const referralCodeElement = document.querySelector('.referral-code');
        const previewDetails = document.querySelector('.referral-details');

        referralCodeElement.textContent = referralCode;

        const referrerName = referrerSelect.selectedOptions[0]?.textContent.split(' (')[0] || 'اسم المستخدم';
        const referredName = referredSelect.selectedOptions[0]?.textContent.split(' (')[0] || 'اسم المستخدم';
        const statusText = statusSelect.selectedOptions[0]?.textContent || 'نشط';

        previewDetails.innerHTML = `
            <div class="detail-item">
                <span class="label">المحيل:</span>
                <span class="value">${referrerName}</span>
            </div>
            <div class="detail-item">
                <span class="label">المحال إليه:</span>
                <span class="value">${referredName}</span>
            </div>
            <div class="detail-item">
                <span class="label">الحالة:</span>
                <span class="value badge badge-success">${statusText}</span>
            </div>
            <div class="detail-item">
                <span class="label">العمولة:</span>
                <span class="value">$${commissionAmount}</span>
            </div>
            <div class="detail-item">
                <span class="label">المكافأة:</span>
                <span class="value">$${rewardAmount}</span>
            </div>
        `;
    }

    // تحديث المعاينة عند تحميل الصفحة
    updatePreview();
});
</script>
@endpush
@endsection
