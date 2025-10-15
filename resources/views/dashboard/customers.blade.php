@extends('layouts.app')

@section('title', 'العملاء - لوحة التحكم')

@section('page-title', 'إدارة العملاء')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3 class="mb-1">إدارة العملاء</h3>
                <p class="text-muted mb-0">متابعة وإدارة قاعدة بيانات العملاء</p>
            </div>
            <div>
                <button class="btn btn-primary">
                    <i class="bi bi-person-plus me-2"></i>
                    إضافة عميل جديد
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Customer Statistics -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stats-number">2,456</div>
                        <div class="stats-label">إجمالي العملاء</div>
                    </div>
                    <div class="text-primary">
                        <i class="bi bi-people fs-1"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="badge badge-success">
                        <i class="bi bi-arrow-up me-1"></i>
                        +15.3%
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stats-number">89</div>
                        <div class="stats-label">عملاء جدد</div>
                    </div>
                    <div class="text-primary">
                        <i class="bi bi-person-plus fs-1"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="badge badge-info">
                        <i class="bi bi-calendar me-1"></i>
                        هذا الشهر
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stats-number">1,234</div>
                        <div class="stats-label">عملاء نشطين</div>
                    </div>
                    <div class="text-primary">
                        <i class="bi bi-person-check fs-1"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="badge badge-success">
                        <i class="bi bi-check-circle me-1"></i>
                        نشط
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stats-number">4.8</div>
                        <div class="stats-label">متوسط التقييم</div>
                    </div>
                    <div class="text-primary">
                        <i class="bi bi-star fs-1"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="badge badge-warning">
                        <i class="bi bi-star-fill me-1"></i>
                        ممتاز
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">البحث</label>
                        <input type="text" class="form-control" placeholder="ابحث بالاسم أو البريد الإلكتروني...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">نوع العميل</label>
                        <select class="form-select">
                            <option>جميع الأنواع</option>
                            <option>عملاء جدد</option>
                            <option>عملاء نشطين</option>
                            <option>عملاء VIP</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">تاريخ التسجيل</label>
                        <select class="form-select">
                            <option>جميع التواريخ</option>
                            <option>هذا الشهر</option>
                            <option>الشهر الماضي</option>
                            <option>هذا العام</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <button class="btn btn-outline-primary">
                                <i class="bi bi-search me-2"></i>
                                بحث
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Customers Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-people me-2"></i>
                    قائمة العملاء
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>العميل</th>
                                <th>البريد الإلكتروني</th>
                                <th>الهاتف</th>
                                <th>عدد الطلبات</th>
                                <th>إجمالي المشتريات</th>
                                <th>تاريخ التسجيل</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar me-3">
                                            أ
                                        </div>
                                        <div>
                                            <h6 class="mb-1">أحمد محمد</h6>
                                            <small class="text-muted">عميل VIP</small>
                                        </div>
                                    </div>
                                </td>
                                <td>ahmed@example.com</td>
                                <td>+966501234567</td>
                                <td>
                                    <span class="badge badge-primary">15 طلب</span>
                                </td>
                                <td>
                                    <strong>2,450.00 ر.س</strong>
                                </td>
                                <td>2023-12-15</td>
                                <td>
                                    <span class="badge badge-success">نشط</span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-primary" title="عرض الملف الشخصي">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-warning" title="تعديل">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-info" title="إرسال رسالة">
                                            <i class="bi bi-envelope"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar me-3">
                                            ف
                                        </div>
                                        <div>
                                            <h6 class="mb-1">فاطمة علي</h6>
                                            <small class="text-muted">عميل عادي</small>
                                        </div>
                                    </div>
                                </td>
                                <td>fatima@example.com</td>
                                <td>+966501234568</td>
                                <td>
                                    <span class="badge badge-info">8 طلبات</span>
                                </td>
                                <td>
                                    <strong>750.50 ر.س</strong>
                                </td>
                                <td>2024-01-10</td>
                                <td>
                                    <span class="badge badge-success">نشط</span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-primary" title="عرض الملف الشخصي">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-warning" title="تعديل">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-info" title="إرسال رسالة">
                                            <i class="bi bi-envelope"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar me-3">
                                            م
                                        </div>
                                        <div>
                                            <h6 class="mb-1">محمد السعد</h6>
                                            <small class="text-muted">عميل جديد</small>
                                        </div>
                                    </div>
                                </td>
                                <td>mohammed@example.com</td>
                                <td>+966501234569</td>
                                <td>
                                    <span class="badge badge-warning">3 طلبات</span>
                                </td>
                                <td>
                                    <strong>200.00 ر.س</strong>
                                </td>
                                <td>2024-01-14</td>
                                <td>
                                    <span class="badge badge-warning">غير نشط</span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-primary" title="عرض الملف الشخصي">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-warning" title="تعديل">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-info" title="إرسال رسالة">
                                            <i class="bi bi-envelope"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <nav aria-label="صفحات العملاء">
                    <ul class="pagination justify-content-center mt-4">
                        <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1">السابق</a>
                        </li>
                        <li class="page-item active">
                            <a class="page-link" href="#">1</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="#">2</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="#">3</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="#">التالي</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>
@endsection
