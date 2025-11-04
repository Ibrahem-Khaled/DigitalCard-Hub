@extends('layouts.dashboard-new')

@section('title', 'إدارة التواصل')

@section('content')
<div class="page-header">
    <div class="page-header-content">
        <div class="page-title">
            <h4>
                <i class="bi bi-chat-dots me-2"></i>
                إدارة التواصل
            </h4>
            <p class="text-muted mb-0">إدارة رسائل التواصل والاستفسارات</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('dashboard.contacts.export') }}" class="btn btn-outline-primary">
                <i class="bi bi-download me-1"></i>
                تصدير
            </a>
            {{-- <a href="{{ route('dashboard.contacts.stats') }}" class="btn btn-outline-info">
                <i class="bi bi-graph-up me-1"></i>
                الإحصائيات
            </a> --}}
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <x-dashboard.stats-card
            title="إجمالي الرسائل"
            :value="$stats['total']"
            icon="bi-chat-dots"
            color="primary" />
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <x-dashboard.stats-card
            title="رسائل جديدة"
            :value="$stats['new']"
            icon="bi-chat-square"
            color="info" />
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <x-dashboard.stats-card
            title="قيد المعالجة"
            :value="$stats['in_progress']"
            icon="bi-clock"
            color="warning" />
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <x-dashboard.stats-card
            title="تم الحل"
            :value="$stats['resolved']"
            icon="bi-check-circle"
            color="success" />
    </div>
</div>

<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <x-dashboard.stats-card
            title="أولوية عالية"
            :value="$stats['high_priority']"
            icon="bi-exclamation-triangle"
            color="danger" />
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <x-dashboard.stats-card
            title="غير معين"
            :value="$stats['unassigned']"
            icon="bi-person-x"
            color="secondary" />
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <x-dashboard.stats-card
            title="اليوم"
            :value="$stats['today']"
            icon="bi-calendar-day"
            color="primary" />
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <x-dashboard.stats-card
            title="هذا الأسبوع"
            :value="$stats['this_week']"
            icon="bi-calendar-week"
            color="info" />
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="bi bi-list-ul me-2"></i>
                قائمة رسائل التواصل
            </h5>
            <div class="card-actions">
                <button type="button" class="btn btn-outline-danger btn-sm" id="bulkDeleteBtn" disabled>
                    <i class="bi bi-trash me-1"></i>
                    حذف المحدد
                </button>
                <button type="button" class="btn btn-outline-warning btn-sm" id="bulkAssignBtn" disabled>
                    <i class="bi bi-person-plus me-1"></i>
                    تعيين
                </button>
            </div>
        </div>
    </div>

    <div class="card-body">
        <x-dashboard.filters
            :filters="$filterOptions"
            search-placeholder="البحث في رسائل التواصل..."
            :search-value="request('search')"
            :action-url="route('dashboard.contacts.index')" />

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="50">
                            <input type="checkbox" id="selectAll" class="form-check-input">
                        </th>
                        <th>المرسل</th>
                        <th>النوع</th>
                        <th>الموضوع</th>
                        <th>الأولوية</th>
                        <th>الحالة</th>
                        <th>المعين إلى</th>
                        <th>التاريخ</th>
                        <th width="120">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contacts as $contact)
                        <tr>
                            <td>
                                <input type="checkbox" class="form-check-input contact-checkbox" value="{{ $contact->id }}">
                            </td>
                            <td>
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
                                        @if($contact->is_registered_user)
                                            <span class="badge badge-success badge-sm">مسجل</span>
                                        @else
                                            <span class="badge badge-secondary badge-sm">زائر</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-info">{{ $contact->type_text }}</span>
                            </td>
                            <td>
                                <div class="text-truncate" style="max-width: 200px;" title="{{ $contact->subject }}">
                                    {{ $contact->subject }}
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-{{ $contact->priority_color }}">{{ $contact->priority_text }}</span>
                            </td>
                            <td>
                                <span class="badge badge-{{ $contact->status_color }}">{{ $contact->status_text }}</span>
                            </td>
                            <td>
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
                            </td>
                            <td>
                                <div>
                                    <small class="text-muted">{{ $contact->formatted_created_at }}</small>
                                    @if($contact->is_responded)
                                        <br><small class="text-success">تم الرد</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('dashboard.contacts.show', $contact) }}"
                                       class="btn btn-outline-primary"
                                       title="عرض">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('dashboard.contacts.edit', $contact) }}"
                                       class="btn btn-outline-warning"
                                       title="تعديل">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button"
                                            class="btn btn-outline-danger"
                                            onclick="deleteContact({{ $contact->id }})"
                                            title="حذف">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="bi bi-chat-dots fs-1 d-block mb-3"></i>
                                    <h5>لا توجد رسائل تواصل</h5>
                                    <p>لم يتم العثور على أي رسائل تواصل بعد.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($contacts->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $contacts->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
// Select all functionality
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.contact-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    updateBulkActions();
});

// Individual checkbox change
document.querySelectorAll('.contact-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', updateBulkActions);
});

function updateBulkActions() {
    const checkedBoxes = document.querySelectorAll('.contact-checkbox:checked');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    const bulkAssignBtn = document.getElementById('bulkAssignBtn');

    if (checkedBoxes.length > 0) {
        bulkDeleteBtn.disabled = false;
        bulkAssignBtn.disabled = false;
    } else {
        bulkDeleteBtn.disabled = true;
        bulkAssignBtn.disabled = true;
    }
}

function deleteContact(id) {
    if (confirm('هل أنت متأكد من حذف هذه الرسالة؟')) {
        fetch(`/dashboard/contacts/${id}`, {
            method: 'DELETE',
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
                alert('حدث خطأ أثناء حذف الرسالة');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ أثناء حذف الرسالة');
        });
    }
}
</script>
@endpush
@endsection
