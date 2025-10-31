@extends('layouts.dashboard-new')

@section('title', 'إضافة نقاط ولاء جديدة - متجر البطاقات الرقمية')

@section('page-title', 'إضافة نقاط ولاء جديدة')
@section('page-subtitle', 'إنشاء نقاط ولاء جديدة مع التفاصيل')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="page-title">إضافة نقاط ولاء جديدة</h3>
            <p class="page-subtitle">إنشاء نقاط ولاء جديدة مع التفاصيل</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('dashboard.loyalty-points.index') }}" class="btn btn-outline-secondary">
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
                    معلومات النقاط الأساسية
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('dashboard.loyalty-points.store') }}">
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
                                <label for="points" class="form-label">عدد النقاط <span class="text-danger">*</span></label>
                                <input type="number" min="1"
                                       class="form-control @error('points') is-invalid @enderror"
                                       id="points" name="points" value="{{ old('points') }}"
                                       placeholder="100" required>
                                @error('points')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">عدد النقاط المراد إضافتها أو خصمها</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="point_value_usd" class="form-label">قيمة النقطة بالدولار <span class="text-danger">*</span></label>
                                <input type="number" step="0.0001" min="0.001" max="100"
                                       class="form-control @error('point_value_usd') is-invalid @enderror"
                                       id="point_value_usd" name="point_value_usd" value="{{ old('point_value_usd', '0.01') }}"
                                       placeholder="0.01" required>
                                @error('point_value_usd')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">قيمة كل نقطة بالدولار الأمريكي (مثال: 0.01 = سنت واحد)</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="type" class="form-label">نوع النقاط <span class="text-danger">*</span></label>
                                <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                    <option value="">اختر نوع النقاط</option>
                                    <option value="earned" {{ old('type') === 'earned' ? 'selected' : '' }}>مكتسب</option>
                                    <option value="redeemed" {{ old('type') === 'redeemed' ? 'selected' : '' }}>مسترد</option>
                                    <option value="expired" {{ old('type') === 'expired' ? 'selected' : '' }}>منتهي</option>
                                    <option value="bonus" {{ old('type') === 'bonus' ? 'selected' : '' }}>مكافأة</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="source" class="form-label">المصدر <span class="text-danger">*</span></label>
                                <select class="form-select @error('source') is-invalid @enderror" id="source" name="source" required>
                                    <option value="">اختر المصدر</option>
                                    <option value="purchase" {{ old('source') === 'purchase' ? 'selected' : '' }}>شراء</option>
                                    <option value="referral" {{ old('source') === 'referral' ? 'selected' : '' }}>إحالة</option>
                                    <option value="review" {{ old('source') === 'review' ? 'selected' : '' }}>تقييم</option>
                                    <option value="bonus" {{ old('source') === 'bonus' ? 'selected' : '' }}>مكافأة</option>
                                    <option value="manual" {{ old('source') === 'manual' ? 'selected' : '' }}>يدوي</option>
                                    <option value="other" {{ old('source') === 'other' ? 'selected' : '' }}>أخرى</option>
                                </select>
                                @error('source')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="source_id" class="form-label">معرف المصدر</label>
                                <input type="number"
                                       class="form-control @error('source_id') is-invalid @enderror"
                                       id="source_id" name="source_id" value="{{ old('source_id') }}"
                                       placeholder="123">
                                @error('source_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">معرف العنصر المرتبط (اختياري)</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="expires_at" class="form-label">تاريخ الانتهاء</label>
                                <input type="date"
                                       class="form-control @error('expires_at') is-invalid @enderror"
                                       id="expires_at" name="expires_at" value="{{ old('expires_at') }}">
                                @error('expires_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">اتركه فارغاً إذا لم تنتهِ النقاط</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">الوصف</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="3"
                                  placeholder="وصف تفصيلي للنقاط">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>
                            إنشاء النقاط
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
                    <h6><i class="bi bi-info-circle me-2"></i>أنواع النقاط:</h6>
                    <ul class="mb-0">
                        <li><strong>مكتسب:</strong> نقاط مكتسبة من الشراء أو الأنشطة</li>
                        <li><strong>مسترد:</strong> نقاط مستخدمة في الشراء</li>
                        <li><strong>منتهي:</strong> نقاط انتهت صلاحيتها</li>
                        <li><strong>مكافأة:</strong> نقاط مكافأة إضافية</li>
                    </ul>
                </div>

                <div class="alert alert-warning">
                    <h6><i class="bi bi-exclamation-triangle me-2"></i>تحذيرات مهمة:</h6>
                    <ul class="mb-0">
                        <li>تأكد من صحة عدد النقاط</li>
                        <li>اختر النوع المناسب للنقاط</li>
                        <li>ضع تاريخ انتهاء منطقي</li>
                        <li>أضف وصف واضح للنقاط</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- معاينة النقاط -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-eye me-2"></i>
                    معاينة النقاط
                </h5>
            </div>
            <div class="card-body">
                <div class="loyalty-point-preview">
                    <div class="points-display">
                        <span class="points-value">+100</span>
                        <span class="points-label">نقطة</span>
                    </div>
                    <div class="points-details">
                        <div class="detail-item">
                            <span class="label">المستخدم:</span>
                            <span class="value">اسم المستخدم</span>
                        </div>
                        <div class="detail-item">
                            <span class="label">النوع:</span>
                            <span class="value badge badge-success">مكتسب</span>
                        </div>
                        <div class="detail-item">
                            <span class="label">المصدر:</span>
                            <span class="value">شراء</span>
                        </div>
                        <div class="detail-item">
                            <span class="label">قيمة النقطة:</span>
                            <span class="value text-info">0.01 $</span>
                        </div>
                        <div class="detail-item">
                            <span class="label">القيمة الإجمالية:</span>
                            <span class="value text-success">1.00 $</span>
                        </div>
                        <div class="detail-item">
                            <span class="label">تاريخ الانتهاء:</span>
                            <span class="value">لا ينتهي</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const pointsInput = document.getElementById('points');
    const pointValueInput = document.getElementById('point_value_usd');
    const pointsValueSpan = document.querySelector('.points-value');
    const pointValueSpan = document.querySelector('.detail-item:nth-child(4) .value');
    const totalValueSpan = document.querySelector('.detail-item:nth-child(5) .value');

    function updatePreview() {
        const points = parseInt(pointsInput.value) || 0;
        const pointValue = parseFloat(pointValueInput.value) || 0;
        const totalValue = points * pointValue;

        // تحديث عرض النقاط
        pointsValueSpan.textContent = (points > 0 ? '+' : '') + points;

        // تحديث قيمة النقطة
        pointValueSpan.textContent = pointValue.toFixed(4) + ' $';

        // تحديث القيمة الإجمالية
        totalValueSpan.textContent = totalValue.toFixed(2) + ' $';
    }

    pointsInput.addEventListener('input', updatePreview);
    pointValueInput.addEventListener('input', updatePreview);

    // تحديث أولي
    updatePreview();
});
</script>
@endpush

@push('styles')
<style>
.loyalty-point-preview {
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    padding: 20px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.points-display {
    text-align: center;
    margin-bottom: 20px;
}

.points-value {
    font-size: 2rem;
    font-weight: bold;
    color: var(--primary-purple);
}

.points-label {
    font-size: 1rem;
    color: var(--text-muted);
    margin-right: 10px;
}

.points-details .detail-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
    padding: 5px 0;
    border-bottom: 1px solid #eee;
}

.points-details .label {
    font-weight: 600;
    color: var(--text-dark);
}

.points-details .value {
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
    // تحديث معاينة النقاط عند تغيير الحقول
    const formInputs = document.querySelectorAll('input, select, textarea');

    formInputs.forEach(input => {
        input.addEventListener('change', updatePreview);
        input.addEventListener('input', updatePreview);
    });

    function updatePreview() {
        const points = document.getElementById('points').value || '100';
        const type = document.getElementById('type').selectedOptions[0]?.textContent || 'مكتسب';
        const source = document.getElementById('source').selectedOptions[0]?.textContent || 'شراء';
        const userSelect = document.getElementById('user_id');
        const expiresAt = document.getElementById('expires_at').value;

        const pointsDisplay = document.querySelector('.points-value');
        const previewDetails = document.querySelector('.points-details');

        // تحديث عرض النقاط
        const pointsValue = document.getElementById('type').value === 'redeemed' || document.getElementById('type').value === 'expired'
            ? `-${points}`
            : `+${points}`;
        pointsDisplay.textContent = pointsValue;

        // تحديث لون النقاط
        if (document.getElementById('type').value === 'redeemed' || document.getElementById('type').value === 'expired') {
            pointsDisplay.style.color = 'var(--bs-danger)';
        } else {
            pointsDisplay.style.color = 'var(--primary-purple)';
        }

        const userName = userSelect.selectedOptions[0]?.textContent.split(' (')[0] || 'اسم المستخدم';
        const expiresText = expiresAt ? new Date(expiresAt).toLocaleDateString('ar-SA') : 'لا ينتهي';

        // تحديث نوع النقاط
        let typeBadge = '';
        switch(document.getElementById('type').value) {
            case 'earned':
                typeBadge = '<span class="badge badge-success">مكتسب</span>';
                break;
            case 'redeemed':
                typeBadge = '<span class="badge badge-warning">مسترد</span>';
                break;
            case 'expired':
                typeBadge = '<span class="badge badge-secondary">منتهي</span>';
                break;
            case 'bonus':
                typeBadge = '<span class="badge badge-info">مكافأة</span>';
                break;
            default:
                typeBadge = '<span class="badge badge-success">مكتسب</span>';
        }

        previewDetails.innerHTML = `
            <div class="detail-item">
                <span class="label">المستخدم:</span>
                <span class="value">${userName}</span>
            </div>
            <div class="detail-item">
                <span class="label">النوع:</span>
                <span class="value">${typeBadge}</span>
            </div>
            <div class="detail-item">
                <span class="label">المصدر:</span>
                <span class="value">${source}</span>
            </div>
            <div class="detail-item">
                <span class="label">تاريخ الانتهاء:</span>
                <span class="value">${expiresText}</span>
            </div>
        `;
    }

    // تحديث المعاينة عند تحميل الصفحة
    updatePreview();
});
</script>
@endpush
@endsection
