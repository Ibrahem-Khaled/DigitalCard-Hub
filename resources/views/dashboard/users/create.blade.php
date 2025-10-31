@extends('layouts.dashboard-new')

@section('title', 'إضافة مستخدم جديد - متجر البطاقات الرقمية')

@section('page-title', 'إضافة مستخدم جديد')
@section('page-subtitle', 'إنشاء مستخدم جديد في النظام')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="page-title">إضافة مستخدم جديد</h3>
            <p class="page-subtitle">إنشاء مستخدم جديد في النظام</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('dashboard.users.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-right me-2"></i>
                العودة للقائمة
            </a>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('dashboard.users.store') }}" enctype="multipart/form-data">
    @csrf

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-person-plus me-2"></i>
                        بيانات المستخدم
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- الاسم الأول -->
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label">الاسم الأول <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                                   id="first_name" name="first_name" value="{{ old('first_name') }}" required>
                            @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- الاسم الأخير -->
                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label">الاسم الأخير <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                                   id="last_name" name="last_name" value="{{ old('last_name') }}" required>
                            @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- البريد الإلكتروني -->
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- رقم الهاتف -->
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">رقم الهاتف</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                   id="phone" name="phone" value="{{ old('phone') }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- كلمة المرور -->
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">كلمة المرور <span class="text-danger">*</span></label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                   id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- تأكيد كلمة المرور -->
                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label">تأكيد كلمة المرور <span class="text-danger">*</span></label>
                            <input type="password" class="form-control"
                                   id="password_confirmation" name="password_confirmation" required>
                        </div>

                        <!-- تاريخ الميلاد -->
                        <div class="col-md-6 mb-3">
                            <label for="birth_date" class="form-label">تاريخ الميلاد</label>
                            <input type="date" class="form-control @error('birth_date') is-invalid @enderror"
                                   id="birth_date" name="birth_date" value="{{ old('birth_date') }}">
                            @error('birth_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- الجنس -->
                        <div class="col-md-6 mb-3">
                            <label for="gender" class="form-label">الجنس</label>
                            <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender">
                                <option value="">اختر الجنس</option>
                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>ذكر</option>
                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>أنثى</option>
                            </select>
                            @error('gender')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- العنوان -->
                        <div class="col-12 mb-3">
                            <label for="address" class="form-label">العنوان</label>
                            <textarea class="form-control @error('address') is-invalid @enderror"
                                      id="address" name="address" rows="3">{{ old('address') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- المدينة -->
                        <div class="col-md-4 mb-3">
                            <label for="city" class="form-label">المدينة</label>
                            <input type="text" class="form-control @error('city') is-invalid @enderror"
                                   id="city" name="city" value="{{ old('city') }}">
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- الدولة -->
                        <div class="col-md-4 mb-3">
                            <label for="country" class="form-label">الدولة</label>
                            <input type="text" class="form-control @error('country') is-invalid @enderror"
                                   id="country" name="country" value="{{ old('country') }}">
                            @error('country')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- الرمز البريدي -->
                        <div class="col-md-4 mb-3">
                            <label for="postal_code" class="form-label">الرمز البريدي</label>
                            <input type="text" class="form-control @error('postal_code') is-invalid @enderror"
                                   id="postal_code" name="postal_code" value="{{ old('postal_code') }}">
                            @error('postal_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- الصورة الشخصية -->
                        <div class="col-12 mb-3">
                            <label for="avatar" class="form-label">الصورة الشخصية</label>
                            <input type="file" class="form-control @error('avatar') is-invalid @enderror"
                                   id="avatar" name="avatar" accept="image/*">
                            <div class="form-text">الحد الأقصى للحجم: 2MB. الأنواع المسموحة: JPG, PNG, GIF</div>
                            @error('avatar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <a href="{{ route('dashboard.users.index') }}" class="btn btn-outline-secondary me-2">إلغاء</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-2"></i>
                            إنشاء المستخدم
                        </button>
                    </div>
                </div>
            </div>
        </div>
</n    </div>

    <div class="row mt-3">
        <div class="col-lg-4">
            <!-- الأدوار -->
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-person-gear me-2"></i>
                        الأدوار
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">اختر الأدوار <span class="text-danger">*</span></label>
                        @if(isset($roles) && $roles->count() > 0)
                            <div class="roles-container">
                                @foreach($roles as $role)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="roles[]"
                                           value="{{ $role->id }}" id="role_{{ $role->id }}"
                                           {{ in_array($role->id, old('roles', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="role_{{ $role->id }}">
                                        {{ $role->name }}
                                        @if($role->description)
                                            <small class="text-muted d-block">{{ $role->description }}</small>
                                        @endif
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                لا توجد أدوار متاحة. يرجى إنشاء أدوار أولاً.
                            </div>
                        @endif
                        @error('roles')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <!-- نصائح -->
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-lightbulb me-2"></i>
                        نصائح
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            تأكد من صحة البيانات المدخلة
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            كلمة المرور يجب أن تكون 8 أحرف على الأقل
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            يمكن للمستخدم تغيير كلمة المرور لاحقاً
                        </li>
                        <li class="mb-0">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            يمكن تعديل جميع البيانات بعد الإنشاء
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</form>

@push('styles')
<style>
.form-check {
    margin-bottom: 0.75rem;
}

.form-check-input:checked {
    background-color: var(--primary-purple);
    border-color: var(--primary-purple);
}

.form-check-input:focus {
    box-shadow: 0 0 0 0.25rem rgba(139, 92, 246, 0.25);
}

.whatsapp-btn {
    background-color: #25D366 !important;
    border-color: #25D366 !important;
    color: white !important;
    padding: 4px 8px;
    font-size: 12px;
}

.whatsapp-btn:hover {
    background-color: #128C7E !important;
    border-color: #128C7E !important;
    color: white !important;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const roleCheckboxes = document.querySelectorAll('input[name="roles[]"]');

    if (!form || !roleCheckboxes.length) {
        return;
    }

    form.addEventListener('submit', function(e) {
        const hasSelection = Array.from(roleCheckboxes).some(cb => cb.checked);

        if (!hasSelection) {
            e.preventDefault();
            alert('يرجى اختيار دور واحد على الأقل للمستخدم');
        }
    });
});
</script>
@endpush
@endsection
