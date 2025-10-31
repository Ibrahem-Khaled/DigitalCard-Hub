@extends('layouts.dashboard-new')

@section('title', 'تعديل الإحالة - ' . $referral->referral_code . ' - متجر البطاقات الرقمية')

@section('page-title', 'تعديل الإحالة')
@section('page-subtitle', 'تعديل الإحالة: ' . $referral->referral_code)

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="page-title">تعديل الإحالة</h3>
            <p class="page-subtitle">تعديل الإحالة: {{ $referral->referral_code }}</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('dashboard.referrals.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-right me-2"></i>
                العودة للقائمة
            </a>
            <a href="{{ route('dashboard.referrals.show', $referral) }}" class="btn btn-outline-info">
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
                    تعديل معلومات الإحالة
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('dashboard.referrals.update', $referral) }}">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="referrer_id" class="form-label">المستخدم المحيل <span class="text-danger">*</span></label>
                                <select class="form-select @error('referrer_id') is-invalid @enderror" id="referrer_id" name="referrer_id" required>
                                    <option value="">اختر المستخدم المحيل</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('referrer_id', $referral->referrer_id) == $user->id ? 'selected' : '' }}>
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
                                        <option value="{{ $user->id }}" {{ old('referred_id', $referral->referred_id) == $user->id ? 'selected' : '' }}>
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
                                <label for="referral_code" class="form-label">كود الإحالة <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control @error('referral_code') is-invalid @enderror"
                                           id="referral_code" name="referral_code" value="{{ old('referral_code', $referral->referral_code) }}"
                                           placeholder="كود الإحالة" required>
                                    <button type="button" class="btn btn-outline-secondary" id="generateCode">
                                        <i class="bi bi-arrow-clockwise"></i>
                                    </button>
                                </div>
                                @error('referral_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">الحالة <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="">اختر الحالة</option>
                                    <option value="active" {{ old('status', $referral->status) === 'active' ? 'selected' : '' }}>نشط</option>
                                    <option value="completed" {{ old('status', $referral->status) === 'completed' ? 'selected' : '' }}>مكتمل</option>
                                    <option value="expired" {{ old('status', $referral->status) === 'expired' ? 'selected' : '' }}>منتهي</option>
                                    <option value="cancelled" {{ old('status', $referral->status) === 'cancelled' ? 'selected' : '' }}>ملغي</option>
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
                                       id="commission_amount" name="commission_amount" value="{{ old('commission_amount', $referral->commission_amount) }}"
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
                                       id="commission_percentage" name="commission_percentage" value="{{ old('commission_percentage', $referral->commission_percentage) }}"
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
                                       id="reward_amount" name="reward_amount" value="{{ old('reward_amount', $referral->reward_amount) }}"
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
                                       id="reward_percentage" name="reward_percentage" value="{{ old('reward_percentage', $referral->reward_percentage) }}"
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
                                       id="expires_at" name="expires_at"
                                       value="{{ old('expires_at', $referral->expires_at?->format('Y-m-d')) }}">
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
                                  placeholder="ملاحظات إضافية حول الإحالة">{{ old('notes', $referral->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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
        <!-- معلومات الإحالة الحالية -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    معلومات الإحالة الحالية
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">كود الإحالة:</label>
                    <p class="mb-0 font-monospace">{{ $referral->referral_code }}</p>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">الحالة الحالية:</label>
                    <p class="mb-0">
                        @if($referral->status === 'active')
                            <span class="badge badge-success">نشط</span>
                        @elseif($referral->status === 'completed')
                            <span class="badge badge-primary">مكتمل</span>
                        @elseif($referral->status === 'expired')
                            <span class="badge badge-warning">منتهي</span>
                        @elseif($referral->status === 'cancelled')
                            <span class="badge badge-secondary">ملغي</span>
                        @endif
                    </p>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">تاريخ الإنشاء:</label>
                    <p class="mb-0">{{ $referral->created_at->format('Y-m-d H:i:s') }}</p>
                    <small class="text-muted">{{ $referral->created_at->diffForHumans() }}</small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">آخر تحديث:</label>
                    <p class="mb-0">{{ $referral->updated_at->format('Y-m-d H:i:s') }}</p>
                    <small class="text-muted">{{ $referral->updated_at->diffForHumans() }}</small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">عدد المكافآت:</label>
                    <p class="mb-0">{{ $referral->rewards->count() }} مكافأة</p>
                </div>
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
                        <li>تغيير كود الإحالة قد يؤثر على المستخدمين</li>
                        <li>تعديل المبالغ قد يؤثر على المكافآت</li>
                        <li>تغيير الحالة قد يؤثر على المعالجة</li>
                        <li>لا يمكن حذف الإحالات التي لها مكافآت معالجة</li>
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
                    @if(!$referral->isCompleted())
                        <form method="POST" action="{{ route('dashboard.referrals.mark-completed', $referral) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-check-circle me-2"></i>
                                وضع علامة كمكتمل
                            </button>
                        </form>
                    @endif

                    @if($referral->status !== 'cancelled')
                        <form method="POST" action="{{ route('dashboard.referrals.cancel', $referral) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-warning w-100">
                                <i class="bi bi-x-circle me-2"></i>
                                إلغاء الإحالة
                            </button>
                        </form>
                    @endif

                    @if($referral->rewards()->processed()->count() == 0)
                        <form method="POST" action="{{ route('dashboard.referrals.destroy', $referral) }}" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه الإحالة؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="bi bi-trash me-2"></i>
                                حذف الإحالة
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.font-monospace {
    font-family: 'Courier New', monospace;
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
            })
            .catch(error => {
                console.error('Error generating code:', error);
            });
    });
});
</script>
@endpush
@endsection
