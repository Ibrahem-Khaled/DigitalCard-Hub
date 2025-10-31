@extends('layouts.dashboard-new')

@section('title', 'عرض رسالة التواصل')

@section('content')
<div class="page-header">
    <div class="page-header-content">
        <div class="page-title">
            <h4>
                <i class="bi bi-chat-dots me-2"></i>
                عرض رسالة التواصل
            </h4>
            <p class="text-muted mb-0">تفاصيل رسالة التواصل رقم #{{ $contact->id }}</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('dashboard.contacts.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-right me-1"></i>
                العودة للقائمة
            </a>
            <a href="{{ route('dashboard.contacts.edit', $contact) }}" class="btn btn-outline-warning">
                <i class="bi bi-pencil me-1"></i>
                تعديل
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-chat-text me-2"></i>
                    محتوى الرسالة
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <h6 class="text-muted mb-2">الموضوع:</h6>
                    <h4 class="mb-0">{{ $contact->subject }}</h4>
                </div>

                <div class="mb-4">
                    <h6 class="text-muted mb-2">الرسالة:</h6>
                    <div class="bg-light p-3 rounded">
                        <p class="mb-0" style="white-space: pre-wrap;">{{ $contact->message }}</p>
                    </div>
                </div>

                @if($contact->admin_response)
                    <div class="mb-4">
                        <h6 class="text-muted mb-2">رد الإدارة:</h6>
                        <div class="bg-primary text-white p-3 rounded">
                            <p class="mb-0" style="white-space: pre-wrap;">{{ $contact->admin_response }}</p>
                        </div>
                        <small class="text-muted">
                            <i class="bi bi-clock me-1"></i>
                            تم الرد في: {{ $contact->formatted_responded_at }}
                        </small>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    معلومات الرسالة
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
                    <h6 class="text-muted mb-1">النوع:</h6>
                    <span class="badge badge-info">{{ $contact->type_text }}</span>
                </div>

                <div class="mb-3">
                    <h6 class="text-muted mb-1">الأولوية:</h6>
                    <span class="badge badge-{{ $contact->priority_color }}">{{ $contact->priority_text }}</span>
                </div>

                <div class="mb-3">
                    <h6 class="text-muted mb-1">الحالة:</h6>
                    <span class="badge badge-{{ $contact->status_color }}">{{ $contact->status_text }}</span>
                </div>

                <div class="mb-3">
                    <h6 class="text-muted mb-1">المعين إلى:</h6>
                    @if($contact->assignedTo)
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                <i class="bi bi-person-check"></i>
                            </div>
                            <span>{{ $contact->assignedTo->display_name }}</span>
                        </div>
                    @else
                        <span class="text-muted">غير معين</span>
                    @endif
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
                    <i class="bi bi-gear me-2"></i>
                    إجراءات سريعة
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if($contact->status === 'new')
                        <button type="button" class="btn btn-warning" onclick="updateStatus('in_progress')">
                            <i class="bi bi-clock me-1"></i>
                            وضع قيد المعالجة
                        </button>
                    @endif

                    @if($contact->status === 'in_progress')
                        <button type="button" class="btn btn-success" onclick="updateStatus('resolved')">
                            <i class="bi bi-check-circle me-1"></i>
                            تم الحل
                        </button>
                    @endif

                    @if($contact->status !== 'closed')
                        <button type="button" class="btn btn-secondary" onclick="updateStatus('closed')">
                            <i class="bi bi-x-circle me-1"></i>
                            إغلاق
                        </button>
                    @endif

                    @if($contact->status !== 'spam')
                        <button type="button" class="btn btn-danger" onclick="updateStatus('spam')">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            وضع كمزعج
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function updateStatus(status) {
    const statusTexts = {
        'in_progress': 'قيد المعالجة',
        'resolved': 'تم الحل',
        'closed': 'مغلق',
        'spam': 'مزعج'
    };

    if (confirm(`هل أنت متأكد من تحديث الحالة إلى "${statusTexts[status]}"؟`)) {
        fetch(`/dashboard/contacts/{{ $contact->id }}/mark-${status.replace('_', '-')}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('حدث خطأ أثناء تحديث الحالة');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ أثناء تحديث الحالة');
        });
    }
}
</script>
@endpush
@endsection
