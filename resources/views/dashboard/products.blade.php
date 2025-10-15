@extends('layouts.app')

@section('title', 'المنتجات - لوحة التحكم')

@section('page-title', 'إدارة المنتجات')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3 class="mb-1">إدارة المنتجات</h3>
                <p class="text-muted mb-0">إدارة جميع منتجات متجر البطاقات الرقمية</p>
            </div>
            <div>
                <button class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>
                    إضافة منتج جديد
                </button>
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
                        <input type="text" class="form-control" placeholder="ابحث عن منتج...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">الفئة</label>
                        <select class="form-select">
                            <option>جميع الفئات</option>
                            <option>بطاقات الألعاب</option>
                            <option>بطاقات التسوق</option>
                            <option>بطاقات الترفيه</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">الحالة</label>
                        <select class="form-select">
                            <option>جميع الحالات</option>
                            <option>نشط</option>
                            <option>غير نشط</option>
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

<!-- Products Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-box-seam me-2"></i>
                    قائمة المنتجات
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>الصورة</th>
                                <th>اسم المنتج</th>
                                <th>الفئة</th>
                                <th>السعر</th>
                                <th>المخزون</th>
                                <th>الحالة</th>
                                <th>تاريخ الإنشاء</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="product-image" style="width: 50px; height: 50px; background: linear-gradient(135deg, var(--primary-purple), var(--secondary-purple)); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                        A
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <h6 class="mb-1">بطاقة هدايا أمازون</h6>
                                        <small class="text-muted">بطاقة هدايا أمازون بقيمة 50 ريال</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-info">بطاقات التسوق</span>
                                </td>
                                <td>
                                    <strong>50.00 ر.س</strong>
                                </td>
                                <td>
                                    <span class="badge badge-success">متوفر (45)</span>
                                </td>
                                <td>
                                    <span class="badge badge-success">نشط</span>
                                </td>
                                <td>2024-01-10</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-primary" title="عرض">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-warning" title="تعديل">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" title="حذف">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="product-image" style="width: 50px; height: 50px; background: linear-gradient(135deg, #10B981, #34D399); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                        S
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <h6 class="mb-1">بطاقة شحن ستيم</h6>
                                        <small class="text-muted">بطاقة شحن ستيم بقيمة 100 ريال</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-warning">بطاقات الألعاب</span>
                                </td>
                                <td>
                                    <strong>100.00 ر.س</strong>
                                </td>
                                <td>
                                    <span class="badge badge-success">متوفر (32)</span>
                                </td>
                                <td>
                                    <span class="badge badge-success">نشط</span>
                                </td>
                                <td>2024-01-08</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-primary" title="عرض">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-warning" title="تعديل">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" title="حذف">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="product-image" style="width: 50px; height: 50px; background: linear-gradient(135deg, #F59E0B, #FBBF24); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                        I
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <h6 class="mb-1">بطاقة آيتونز</h6>
                                        <small class="text-muted">بطاقة آيتونز بقيمة 75 ريال</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-danger">بطاقات الترفيه</span>
                                </td>
                                <td>
                                    <strong>75.00 ر.س</strong>
                                </td>
                                <td>
                                    <span class="badge badge-warning">محدود (8)</span>
                                </td>
                                <td>
                                    <span class="badge badge-success">نشط</span>
                                </td>
                                <td>2024-01-05</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-primary" title="عرض">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-warning" title="تعديل">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" title="حذف">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <nav aria-label="صفحات المنتجات">
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
