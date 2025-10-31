@extends('layouts.dashboard-new')

@section('title', 'إعدادات النظام')

@section('content')
<div class="page-header">
    <div class="page-header-content">
        <div class="page-title">
            <h4>
                <i class="bi bi-gear me-2"></i>
                إعدادات النظام
            </h4>
            <p class="text-muted mb-0">إدارة جميع إعدادات الموقع والنظام</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('dashboard.settings.create') }}" class="btn btn-primary">
                <i class="bi bi-plus me-1"></i>
                إضافة إعداد
            </a>
            <a href="{{ route('dashboard.settings.export') }}" class="btn btn-outline-success">
                <i class="bi bi-download me-1"></i>
                تصدير
            </a>
            <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="bi bi-upload me-1"></i>
                استيراد
            </button>
            <form action="{{ route('dashboard.settings.clear-cache') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-info" onclick="return confirm('هل أنت متأكد من مسح ذاكرة التخزين المؤقت؟')">
                    <i class="bi bi-arrow-clockwise me-1"></i>
                    مسح الكاش
                </button>
            </form>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <x-dashboard.stats-card
            title="إجمالي الإعدادات"
            :value="$stats['total_settings']"
            icon="bi-gear"
            color="primary" />
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <x-dashboard.stats-card
            title="إعدادات عامة"
            :value="$stats['public_settings']"
            icon="bi-globe"
            color="success" />
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <x-dashboard.stats-card
            title="إعدادات مطلوبة"
            :value="$stats['required_settings']"
            icon="bi-exclamation-triangle"
            color="warning" />
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <x-dashboard.stats-card
            title="المجموعات"
            :value="$stats['groups_count']"
            icon="bi-folder"
            color="info" />
    </div>
</div>

<div class="row">
    <div class="col-lg-3">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-list me-2"></i>
                    مجموعات الإعدادات
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @foreach($groups as $groupName)
                        <a href="{{ route('dashboard.settings.index', ['group' => $groupName]) }}"
                           class="list-group-item list-group-item-action {{ $group === $groupName ? 'active' : '' }}">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-{{ match($groupName) {
                                    'general' => 'house',
                                    'site' => 'globe',
                                    'contact' => 'telephone',
                                    'social' => 'share',
                                    'seo' => 'search',
                                    'legal' => 'shield-check',
                                    'payment' => 'credit-card',
                                    'email' => 'envelope',
                                    'security' => 'lock',
                                    'appearance' => 'palette',
                                    default => 'gear'
                                } }} me-2"></i>
                                <span>{{ match($groupName) {
                                    'general' => 'عام',
                                    'site' => 'الموقع',
                                    'contact' => 'التواصل',
                                    'social' => 'وسائل التواصل',
                                    'seo' => 'SEO',
                                    'legal' => 'قانوني',
                                    'payment' => 'الدفع',
                                    'email' => 'البريد الإلكتروني',
                                    'security' => 'الأمان',
                                    'appearance' => 'المظهر',
                                    default => 'عام'
                                } }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-9">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-{{ match($group) {
                            'general' => 'house',
                            'site' => 'globe',
                            'contact' => 'telephone',
                            'social' => 'share',
                            'seo' => 'search',
                            'legal' => 'shield-check',
                            'payment' => 'credit-card',
                            'email' => 'envelope',
                            'security' => 'lock',
                            'appearance' => 'palette',
                            default => 'gear'
                        } }} me-2"></i>
                        إعدادات {{ match($group) {
                            'general' => 'عام',
                            'site' => 'الموقع',
                            'contact' => 'التواصل',
                            'social' => 'وسائل التواصل',
                            'seo' => 'SEO',
                            'legal' => 'قانوني',
                            'payment' => 'الدفع',
                            'email' => 'البريد الإلكتروني',
                            'security' => 'الأمان',
                            'appearance' => 'المظهر',
                            default => 'عام'
                        } }}
                    </h5>
                    <div class="card-actions">
                        <form action="{{ route('dashboard.settings.reset-group') }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="group" value="{{ $group }}">
                            <button type="submit" class="btn btn-outline-danger btn-sm"
                                    onclick="return confirm('هل أنت متأكد من إعادة تعيين جميع إعدادات هذه المجموعة؟')">
                                <i class="bi bi-arrow-clockwise me-1"></i>
                                إعادة تعيين
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($settings->count() > 0)
                    <form action="{{ route('dashboard.settings.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            @foreach($settings as $setting)
                                <div class="col-md-6 mb-4">
                                    <div class="setting-item">
                                        <label for="{{ $setting->key }}" class="form-label">
                                            {{ $setting->name }}
                                            @if($setting->is_required)
                                                <span class="text-danger">*</span>
                                            @endif
                                        </label>

                                        @if($setting->description)
                                            <small class="text-muted d-block mb-2">{{ $setting->description }}</small>
                                        @endif

                                        @if($setting->type === 'text')
                                            <input type="text"
                                                   class="form-control @error($setting->key) is-invalid @enderror"
                                                   id="{{ $setting->key }}"
                                                   name="{{ $setting->key }}"
                                                   value="{{ old($setting->key, $setting->value) }}">
                                        @elseif($setting->type === 'textarea')
                                            <textarea class="form-control @error($setting->key) is-invalid @enderror"
                                                      id="{{ $setting->key }}"
                                                      name="{{ $setting->key }}"
                                                      rows="4">{{ old($setting->key, $setting->value) }}</textarea>
                                        @elseif($setting->type === 'number')
                                            <input type="number"
                                                   class="form-control @error($setting->key) is-invalid @enderror"
                                                   id="{{ $setting->key }}"
                                                   name="{{ $setting->key }}"
                                                   value="{{ old($setting->key, $setting->value) }}">
                                        @elseif($setting->type === 'boolean')
                                            <div class="form-check form-switch">
                                                <input class="form-check-input"
                                                       type="checkbox"
                                                       id="{{ $setting->key }}"
                                                       name="{{ $setting->key }}"
                                                       value="1"
                                                       {{ old($setting->key, $setting->value) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="{{ $setting->key }}">
                                                    {{ $setting->value ? 'مفعل' : 'معطل' }}
                                                </label>
                                            </div>
                                        @elseif($setting->type === 'select')
                                            <select class="form-select @error($setting->key) is-invalid @enderror"
                                                    id="{{ $setting->key }}"
                                                    name="{{ $setting->key }}">
                                                <option value="">اختر...</option>
                                                @foreach($setting->getSelectOptions() as $value => $text)
                                                    <option value="{{ $value }}" {{ old($setting->key, $setting->value) == $value ? 'selected' : '' }}>
                                                        {{ $text }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        @elseif($setting->type === 'file')
                                            <div class="mb-2">
                                                @if($setting->value)
                                                    <div class="current-file mb-2">
                                                        <small class="text-muted">الملف الحالي:</small>
                                                        <a href="{{ Storage::url($setting->value) }}" target="_blank" class="text-primary">
                                                            {{ basename($setting->value) }}
                                                        </a>
                                                    </div>
                                                @endif
                                                <input type="file"
                                                       class="form-control @error($setting->key) is-invalid @enderror"
                                                       id="{{ $setting->key }}"
                                                       name="{{ $setting->key }}">
                                            </div>
                                        @elseif($setting->type === 'email')
                                            <input type="email"
                                                   class="form-control @error($setting->key) is-invalid @enderror"
                                                   id="{{ $setting->key }}"
                                                   name="{{ $setting->key }}"
                                                   value="{{ old($setting->key, $setting->value) }}">
                                        @elseif($setting->type === 'url')
                                            <input type="url"
                                                   class="form-control @error($setting->key) is-invalid @enderror"
                                                   id="{{ $setting->key }}"
                                                   name="{{ $setting->key }}"
                                                   value="{{ old($setting->key, $setting->value) }}">
                                        @elseif($setting->type === 'json')
                                            <textarea class="form-control @error($setting->key) is-invalid @enderror"
                                                      id="{{ $setting->key }}"
                                                      name="{{ $setting->key }}"
                                                      rows="4"
                                                      placeholder="JSON format">{{ old($setting->key, $setting->value) }}</textarea>
                                        @endif

                                        @error($setting->key)
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror

                                        <div class="setting-meta mt-2">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="setting-badges">
                                                    @if($setting->is_public)
                                                        <span class="badge badge-success badge-sm">عام</span>
                                                    @endif
                                                    @if($setting->is_required)
                                                        <span class="badge badge-warning badge-sm">مطلوب</span>
                                                    @endif
                                                    <span class="badge badge-info badge-sm">{{ $setting->type_text }}</span>
                                                </div>
                                                <div class="setting-actions">
                                                    <a href="{{ route('dashboard.settings.edit', $setting) }}"
                                                       class="btn btn-outline-primary btn-sm"
                                                       title="تعديل">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    @if(!$setting->is_required)
                                                        <button type="button"
                                                                class="btn btn-outline-danger btn-sm"
                                                                onclick="deleteSetting({{ $setting->id }})"
                                                                title="حذف">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check me-1"></i>
                                حفظ التغييرات
                            </button>
                        </div>
                    </form>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-gear fs-1 text-muted mb-3"></i>
                        <h5 class="text-muted">لا توجد إعدادات في هذه المجموعة</h5>
                        <p class="text-muted">يمكنك إضافة إعدادات جديدة لهذه المجموعة</p>
                        <a href="{{ route('dashboard.settings.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus me-1"></i>
                            إضافة إعداد
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">استيراد الإعدادات</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('dashboard.settings.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="settings_file" class="form-label">ملف الإعدادات (JSON)</label>
                        <input type="file" class="form-control" id="settings_file" name="settings_file" accept=".json" required>
                        <div class="form-text">يجب أن يكون الملف بصيغة JSON</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">استيراد</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function deleteSetting(id) {
    if (confirm('هل أنت متأكد من حذف هذا الإعداد؟')) {
        fetch(`/dashboard/settings/${id}`, {
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
                alert('حدث خطأ أثناء حذف الإعداد');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ أثناء حذف الإعداد');
        });
    }
}
</script>
@endpush
@endsection
