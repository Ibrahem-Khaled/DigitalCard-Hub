@extends('layouts.dashboard-new')

@section('title', 'تعديل رسالة التواصل')

@section('content')
<div class="page-header">
    <div class="page-header-content">
        <div class="page-title">
            <h4>
                <i class="bi bi-pencil me-2"></i>
                تعديل رسالة التواصل
            </h4>
            <p class="text-muted mb-0">تعديل تفاصيل رسالة التواصل رقم #{{ $contact->id }}</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('dashboard.contacts.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-right me-1"></i>
                العودة للقائمة
            </a>
            <a href="{{ route('dashboard.contacts.show', $contact) }}" class="btn btn-outline-primary">
                <i class="bi bi-eye me-1"></i>
                عرض الرسالة
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-gear me-2"></i>
                    تحديث معلومات الرسالة
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('dashboard.contacts.update', $contact) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">الحالة <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                @foreach($statuses as $value => $label)
                                    <option value="{{ $value }}" {{ old('status', $contact->status) == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="type" class="form-label">النوع <span class="text-danger">*</span></label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                @foreach($types as $value => $label)
                                    <option value="{{ $value }}" {{ old('type', $contact->type) == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="priority" class="form-label">الأولوية <span class="text-danger">*</span></label>
                            <select class="form-select @error('priority') is-invalid @enderror" id="priority" name="priority" required>
                                @foreach($priorities as $value => $label)
                                    <option value="{{ $value }}" {{ old('priority', $contact->priority) == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="assigned_to" class="form-label">تعيين إلى</label>
                            <select class="form-select @error('assigned_to') is-invalid @enderror" id="assigned_to" name="assigned_to">
                                <option value="">اختر مستخدم</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('assigned_to', $contact->assigned_to) == $user->id ? 'selected' : '' }}>
                                        {{ $user->display_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('assigned_to')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="admin_response" class="form-label">رد الإدارة</label>
                        <textarea class="form-control @error('admin_response') is-invalid @enderror"
                                  id="admin_response"
                                  name="admin_response"
                                  rows="6"
                                  placeholder="اكتب رد الإدارة هنا...">{{ old('admin_response', $contact->admin_response) }}</textarea>
                        @error('admin_response')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('dashboard.contacts.show', $contact) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x me-1"></i>
                            إلغاء
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check me-1"></i>
                            حفظ التغييرات
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    معلومات الرسالة الأصلية
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6 class="text-muted mb-1">المرسل:</h6>
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                            <i class="bi bi-person"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">{{ $contact->name }}</h6>
                            <small class="text-muted">{{ $contact->email }}</small>
                            @if($contact->phone)
                                <br><small class="text-muted">{{ $contact->phone }}</small>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <h6 class="text-muted mb-1">الموضوع:</h6>
                    <p class="mb-0">{{ $contact->subject }}</p>
                </div>

                <div class="mb-3">
                    <h6 class="text-muted mb-1">الرسالة:</h6>
                    <div class="bg-light p-2 rounded">
                        <p class="mb-0 small" style="white-space: pre-wrap;">{{ Str::limit($contact->message, 150) }}</p>
                    </div>
                </div>

                <div class="mb-3">
                    <h6 class="text-muted mb-1">تاريخ الإرسال:</h6>
                    <small>{{ $contact->formatted_created_at }}</small>
                </div>

                @if($contact->is_registered_user)
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">المستخدم المسجل:</h6>
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                <i class="bi bi-person-check"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">{{ $contact->user->display_name }}</h6>
                                <small class="text-muted">{{ $contact->user->email }}</small>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-lightning me-2"></i>
                    إجراءات سريعة
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-warning btn-sm" onclick="quickAction('in_progress')">
                        <i class="bi bi-clock me-1"></i>
                        قيد المعالجة
                    </button>
                    <button type="button" class="btn btn-success btn-sm" onclick="quickAction('resolved')">
                        <i class="bi bi-check-circle me-1"></i>
                        تم الحل
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="quickAction('closed')">
                        <i class="bi bi-x-circle me-1"></i>
                        إغلاق
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" onclick="quickAction('spam')">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        مزعج
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function quickAction(status) {
    const statusTexts = {
        'in_progress': 'قيد المعالجة',
        'resolved': 'تم الحل',
        'closed': 'مغلق',
        'spam': 'مزعج'
    };

    if (confirm(`هل تريد تحديث الحالة إلى "${statusTexts[status]}"؟`)) {
        document.getElementById('status').value = status;
        document.querySelector('form').submit();
    }
}
</script>
@endpush
@endsection
