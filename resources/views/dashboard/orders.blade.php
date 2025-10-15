@extends('layouts.app')

@section('title', 'الطلبات - لوحة التحكم')

@section('page-title', 'إدارة الطلبات')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3 class="mb-1">إدارة الطلبات</h3>
                <p class="text-muted mb-0">متابعة وإدارة جميع طلبات العملاء</p>
            </div>
            <div>
                <button class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>
                    طلب جديد
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Order Statistics -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stats-number">156</div>
                        <div class="stats-label">طلبات جديدة</div>
                    </div>
                    <div class="text-primary">
                        <i class="bi bi-cart-plus fs-1"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="badge badge-info">
                        <i class="bi bi-clock me-1"></i>
                        في الانتظار
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
                        <div class="stats-label">طلبات مكتملة</div>
                    </div>
                    <div class="text-primary">
                        <i class="bi bi-check-circle fs-1"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="badge badge-success">
                        <i class="bi bi-check me-1"></i>
                        مكتملة
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
                        <div class="stats-number">23</div>
                        <div class="stats-label">طلبات ملغية</div>
                    </div>
                    <div class="text-primary">
                        <i class="bi bi-x-circle fs-1"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="badge badge-danger">
                        <i class="bi bi-x me-1"></i>
                        ملغية
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
                        <div class="stats-number">12,450</div>
                        <div class="stats-label">إجمالي المبيعات</div>
                    </div>
                    <div class="text-primary">
                        <i class="bi bi-currency-dollar fs-1"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="badge badge-success">
                        <i class="bi bi-arrow-up me-1"></i>
                        +15.2%
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
                        <label class="form-label">رقم الطلب</label>
                        <input type="text" class="form-control" placeholder="ابحث برقم الطلب...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">اسم العميل</label>
                        <input type="text" class="form-control" placeholder="ابحث بالعميل...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">حالة الطلب</label>
                        <select class="form-select">
                            <option>جميع الحالات</option>
                            <option>جديد</option>
                            <option>قيد المراجعة</option>
                            <option>مكتمل</option>
                            <option>ملغي</option>
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

<!-- Orders Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-cart-check me-2"></i>
                    قائمة الطلبات
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>رقم الطلب</th>
                                <th>العميل</th>
                                <th>المنتجات</th>
                                <th>المبلغ الإجمالي</th>
                                <th>طريقة الدفع</th>
                                <th>الحالة</th>
                                <th>تاريخ الطلب</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <strong>#1234</strong>
                                </td>
                                <td>
                                    <div>
                                        <h6 class="mb-1">أحمد محمد</h6>
                                        <small class="text-muted">ahmed@example.com</small>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <span class="badge badge-primary">بطاقة أمازون</span>
                                        <span class="badge badge-info">بطاقة ستيم</span>
                                    </div>
                                </td>
                                <td>
                                    <strong>150.00 ر.س</strong>
                                </td>
                                <td>
                                    <span class="badge badge-success">فيزا</span>
                                </td>
                                <td>
                                    <span class="badge badge-success">مكتمل</span>
                                </td>
                                <td>2024-01-15 14:30</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-primary" title="عرض التفاصيل">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-warning" title="تعديل">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-success" title="طباعة">
                                            <i class="bi bi-printer"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>#1233</strong>
                                </td>
                                <td>
                                    <div>
                                        <h6 class="mb-1">فاطمة علي</h6>
                                        <small class="text-muted">fatima@example.com</small>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <span class="badge badge-primary">بطاقة آيتونز</span>
                                    </div>
                                </td>
                                <td>
                                    <strong>75.50 ر.س</strong>
                                </td>
                                <td>
                                    <span class="badge badge-warning">ماستركارد</span>
                                </td>
                                <td>
                                    <span class="badge badge-warning">قيد المراجعة</span>
                                </td>
                                <td>2024-01-15 12:15</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-primary" title="عرض التفاصيل">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-warning" title="تعديل">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-success" title="طباعة">
                                            <i class="bi bi-printer"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>#1232</strong>
                                </td>
                                <td>
                                    <div>
                                        <h6 class="mb-1">محمد السعد</h6>
                                        <small class="text-muted">mohammed@example.com</small>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <span class="badge badge-primary">بطاقة نتفليكس</span>
                                        <span class="badge badge-info">بطاقة سبوتيفاي</span>
                                    </div>
                                </td>
                                <td>
                                    <strong>200.00 ر.س</strong>
                                </td>
                                <td>
                                    <span class="badge badge-danger">رفض الدفع</span>
                                </td>
                                <td>
                                    <span class="badge badge-danger">ملغي</span>
                                </td>
                                <td>2024-01-14 16:45</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-primary" title="عرض التفاصيل">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-warning" title="تعديل">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-success" title="طباعة">
                                            <i class="bi bi-printer"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <nav aria-label="صفحات الطلبات">
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
