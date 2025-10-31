@extends('layouts.dashboard-new')

@section('title', 'تعديل نقاط الولاء - متجر البطاقات الرقمية')

@section('page-title', 'تعديل نقاط الولاء')
@section('page-subtitle', 'تعديل نقاط الولاء')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="page-title">تعديل نقاط الولاء</h3>
            <p class="page-subtitle">تعديل نقاط الولاء</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('dashboard.loyalty-points.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-right me-2"></i>
                العودة للقائمة
            </a>
            <a href="{{ route('dashboard.loyalty-points.show', $loyaltyPoint) }}" class="btn btn-outline-info">
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
                    تعديل معلومات النقاط
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('dashboard.loyalty-points.update', $loyaltyPoint) }}">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="user_id" class="form-label">المستخدم <span class="text-danger">*</span></label>
                                <select class="form-select @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                                    <option value="">اختر المستخدم</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('user_id', $loyaltyPoint->user_id) == $user->id ? 'selected' : '' }}>
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
                                       id="points" name="points" value="{{ old('points', abs($loyaltyPoint->points)) }}"
                                       placeholder="100" required>
                                @error('points')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">عدد النقاط المراد إضافتها أو خصمها</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="type" class="form-label">نوع النقاط <span class="text-danger">*</span></label>
                                <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                    <option value="">اختر نوع النقاط</option>
                                    <option value="earned" {{ old('type', $loyaltyPoint->type) === 'earned' ? 'selected' : '' }}>مكتسب</option>
                                    <option value="redeemed" {{ old('type', $loyaltyPoint->type) === 'redeemed' ? 'selected' : '' }}>مسترد</option>
                                    <option value="expired" {{ old('type', $loyaltyPoint->type) === 'expired' ? 'selected' : '' }}>منتهي</option>
                                    <option value="bonus" {{ old('type', $loyaltyPoint->type) === 'bonus' ? 'selected' : '' }}>مكافأة</option>
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
                                    <option value="purchase" {{ old('source', $loyaltyPoint->source) === 'purchase' ? 'selected' : '' }}>شراء</option>
                                    <option value="referral" {{ old('source', $loyaltyPoint->source) === 'referral' ? 'selected' : '' }}>إحالة</option>
                                    <option value="review" {{ old('source', $loyaltyPoint->source) === 'review' ? 'selected' : '' }}>تقييم</option>
                                    <option value="bonus" {{ old('source', $loyaltyPoint->source) === 'bonus' ? 'selected' : '' }}>مكافأة</option>
                                    <option value="manual" {{ old('source', $loyaltyPoint->source) === 'manual' ? 'selected' : '' }}>يدوي</option>
                                    <option value="other" {{ old('source', $loyaltyPoint->source) === 'other' ? 'selected' : '' }}>أخرى</option>
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
                                       id="source_id" name="source_id" value="{{ old('source_id', $loyaltyPoint->source_id) }}"
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
                                       id="expires_at" name="expires_at"
                                       value="{{ old('expires_at', $loyaltyPoint->expires_at?->format('Y-m-d')) }}">
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
                                  placeholder="وصف تفصيلي للنقاط">{{ old('description', $loyaltyPoint->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                   {{ old('is_active', $loyaltyPoint->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                النقاط نشطة
                            </label>
                        </div>
                        <div class="form-text">إلغاء تحديد هذا الخيار لإيقاف النقاط</div>
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
        <!-- معلومات النقاط الحالية -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    معلومات النقاط الحالية
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">عدد النقاط:</label>
                    <p class="mb-0 fs-5 {{ $loyaltyPoint->points > 0 ? 'text-success' : 'text-danger' }}">
                        {{ $loyaltyPoint->points > 0 ? '+' : '' }}{{ number_format($loyaltyPoint->points) }}
                    </p>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">النوع:</label>
                    <p class="mb-0">
                        @if($loyaltyPoint->type === 'earned')
                            <span class="badge badge-success">مكتسب</span>
                        @elseif($loyaltyPoint->type === 'redeemed')
                            <span class="badge badge-warning">مسترد</span>
                        @elseif($loyaltyPoint->type === 'expired')
                            <span class="badge badge-secondary">منتهي</span>
                        @elseif($loyaltyPoint->type === 'bonus')
                            <span class="badge badge-info">مكافأة</span>
                        @endif
                    </p>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">المصدر:</label>
                    <p class="mb-0">{{ $loyaltyPoint->source }}</p>
                    @if($loyaltyPoint->source_id)
                        <small class="text-muted">معرف المصدر: {{ $loyaltyPoint->source_id }}</small>
                    @endif
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">الحالة:</label>
                    <p class="mb-0">
                        @if($loyaltyPoint->isActive())
                            <span class="badge badge-success">نشط</span>
                        @elseif($loyaltyPoint->isExpired())
                            <span class="badge badge-warning">منتهي</span>
                        @else
                            <span class="badge badge-secondary">غير نشط</span>
                        @endif
                    </p>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">تاريخ الإنشاء:</label>
                    <p class="mb-0">{{ $loyaltyPoint->created_at->format('Y-m-d H:i:s') }}</p>
                    <small class="text-muted">{{ $loyaltyPoint->created_at->diffForHumans() }}</small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">آخر تحديث:</label>
                    <p class="mb-0">{{ $loyaltyPoint->updated_at->format('Y-m-d H:i:s') }}</p>
                    <small class="text-muted">{{ $loyaltyPoint->updated_at->diffForHumans() }}</small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">عدد المعاملات:</label>
                    <p class="mb-0">{{ $loyaltyPoint->transactions->count() }} معاملة</p>
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
                        <li>تغيير عدد النقاط قد يؤثر على رصيد المستخدم</li>
                        <li>تعديل النوع قد يؤثر على المعاملات</li>
                        <li>تغيير الحالة قد يؤثر على الاستخدام</li>
                        <li>لا يمكن حذف النقاط التي لها معاملات</li>
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
                    @if(!$loyaltyPoint->isExpired())
                        <form method="POST" action="{{ route('dashboard.loyalty-points.mark-expired', $loyaltyPoint) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-warning w-100">
                                <i class="bi bi-clock me-2"></i>
                                وضع علامة كمنتهي
                            </button>
                        </form>
                    @endif

                    <form method="POST" action="{{ route('dashboard.loyalty-points.toggle-status', $loyaltyPoint) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-info w-100">
                            <i class="bi bi-{{ $loyaltyPoint->is_active ? 'pause' : 'play' }} me-2"></i>
                            {{ $loyaltyPoint->is_active ? 'إلغاء تفعيل' : 'تفعيل' }}
                        </button>
                    </form>

                    @if($loyaltyPoint->transactions()->count() == 0)
                        <form method="POST" action="{{ route('dashboard.loyalty-points.destroy', $loyaltyPoint) }}" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه النقاط؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="bi bi-trash me-2"></i>
                                حذف النقاط
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
.badge {
    font-size: 0.75rem;
}
</style>
@endpush
@endsection
