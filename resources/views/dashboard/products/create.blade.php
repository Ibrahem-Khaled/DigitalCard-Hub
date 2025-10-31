@extends('layouts.dashboard-new')

@section('title', 'إضافة منتج جديد - متجر البطاقات الرقمية')

@section('page-title', 'إضافة منتج جديد')
@section('page-subtitle', 'إنشاء منتج جديد في النظام')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="page-title">إضافة منتج جديد</h3>
            <p class="page-subtitle">إنشاء منتج جديد في النظام</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('dashboard.products.index') }}" class="btn btn-outline-secondary">
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
                    بيانات المنتج
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('dashboard.products.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <!-- اسم المنتج -->
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">اسم المنتج <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- SKU -->
                        <div class="col-md-6 mb-3">
                            <label for="sku" class="form-label">SKU <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('sku') is-invalid @enderror"
                                   id="sku" name="sku" value="{{ old('sku') }}" required>
                            <div class="form-text">رمز المنتج الفريد</div>
                            @error('sku')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- الفئة -->
                        <div class="col-md-6 mb-3">
                            <label for="category_id" class="form-label">الفئة <span class="text-danger">*</span></label>
                            <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                <option value="">اختر الفئة</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- العلامة التجارية -->
                        <div class="col-md-6 mb-3">
                            <label for="brand" class="form-label">العلامة التجارية</label>
                            <input type="text" class="form-control @error('brand') is-invalid @enderror"
                                   id="brand" name="brand" value="{{ old('brand') }}">
                            @error('brand')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- السعر -->
                        <div class="col-md-4 mb-3">
                            <label for="price" class="form-label">السعر <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror"
                                   id="price" name="price" value="{{ old('price') }}" required>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- سعر البيع -->
                        <div class="col-md-4 mb-3">
                            <label for="sale_price" class="form-label">سعر البيع</label>
                            <input type="number" step="0.01" class="form-control @error('sale_price') is-invalid @enderror"
                                   id="sale_price" name="sale_price" value="{{ old('sale_price') }}">
                            <div class="form-text">سعر مخفض (اختياري)</div>
                            @error('sale_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- سعر التكلفة -->
                        <div class="col-md-4 mb-3">
                            <label for="cost_price" class="form-label">سعر التكلفة</label>
                            <input type="number" step="0.01" class="form-control @error('cost_price') is-invalid @enderror"
                                   id="cost_price" name="cost_price" value="{{ old('cost_price') }}">
                            @error('cost_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- الوصف المختصر -->
                        <div class="col-12 mb-3">
                            <label for="short_description" class="form-label">الوصف المختصر</label>
                            <textarea class="form-control @error('short_description') is-invalid @enderror"
                                      id="short_description" name="short_description" rows="3">{{ old('short_description') }}</textarea>
                            @error('short_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- الوصف الكامل -->
                        <div class="col-12 mb-3">
                            <label for="description" class="form-label">الوصف الكامل</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="5">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- نقاط الولاء المكتسبة -->
                        <div class="col-md-6 mb-3">
                            <label for="loyalty_points_earn" class="form-label">نقاط الولاء المكتسبة</label>
                            <input type="number" class="form-control @error('loyalty_points_earn') is-invalid @enderror"
                                   id="loyalty_points_earn" name="loyalty_points_earn" value="{{ old('loyalty_points_earn', 0) }}" min="0">
                            <div class="form-text">عدد النقاط التي يحصل عليها العميل عند الشراء</div>
                            @error('loyalty_points_earn')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- نقاط الولاء المطلوبة -->
                        <div class="col-md-6 mb-3">
                            <label for="loyalty_points_cost" class="form-label">نقاط الولاء المطلوبة</label>
                            <input type="number" class="form-control @error('loyalty_points_cost') is-invalid @enderror"
                                   id="loyalty_points_cost" name="loyalty_points_cost" value="{{ old('loyalty_points_cost', 0) }}" min="0">
                            <div class="form-text">عدد النقاط المطلوبة لشراء المنتج (0 = لا يمكن شراؤه بنقاط)</div>
                            @error('loyalty_points_cost')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- نوع البطاقة -->
                        <div class="col-md-6 mb-3">
                            <label for="card_type" class="form-label">نوع البطاقة</label>
                            <select class="form-select @error('card_type') is-invalid @enderror" id="card_type" name="card_type">
                                <option value="">اختر نوع البطاقة</option>
                                <option value="gift_card" {{ old('card_type') == 'gift_card' ? 'selected' : '' }}>بطاقة هدايا</option>
                                <option value="gaming" {{ old('card_type') == 'gaming' ? 'selected' : '' }}>بطاقة ألعاب</option>
                                <option value="subscription" {{ old('card_type') == 'subscription' ? 'selected' : '' }}>اشتراك</option>
                                <option value="entertainment" {{ old('card_type') == 'entertainment' ? 'selected' : '' }}>ترفيه</option>
                                <option value="mobile" {{ old('card_type') == 'mobile' ? 'selected' : '' }}>شحن محمول</option>
                            </select>
                            @error('card_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- مزود البطاقة -->
                        <div class="col-md-6 mb-3">
                            <label for="card_provider" class="form-label">مزود البطاقة</label>
                            <input type="text" class="form-control @error('card_provider') is-invalid @enderror"
                                   id="card_provider" name="card_provider" value="{{ old('card_provider') }}" placeholder="مثال: أمازون، ستيم، نتفليكس">
                            @error('card_provider')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- المنطقة الجغرافية -->
                        <div class="col-md-6 mb-3">
                            <label for="card_region" class="form-label">المنطقة الجغرافية</label>
                            <select class="form-select @error('card_region') is-invalid @enderror" id="card_region" name="card_region">
                                <option value="">اختر المنطقة</option>
                                <option value="Global" {{ old('card_region') == 'Global' ? 'selected' : '' }}>عالمي</option>
                                <option value="US" {{ old('card_region') == 'US' ? 'selected' : '' }}>الولايات المتحدة</option>
                                <option value="EU" {{ old('card_region') == 'EU' ? 'selected' : '' }}>أوروبا</option>
                                <option value="Egypt" {{ old('card_region') == 'Egypt' ? 'selected' : '' }}>مصر</option>
                                <option value="Saudi" {{ old('card_region') == 'Saudi' ? 'selected' : '' }}>السعودية</option>
                                <option value="UAE" {{ old('card_region') == 'UAE' ? 'selected' : '' }}>الإمارات</option>
                            </select>
                            @error('card_region')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- ترتيب العرض -->
                        <div class="col-md-6 mb-3">
                            <label for="sort_order" class="form-label">ترتيب العرض</label>
                            <input type="number" class="form-control @error('sort_order') is-invalid @enderror"
                                   id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0">
                            @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- الفئات المتاحة -->
                        <div class="col-12 mb-3">
                            <label for="card_denominations" class="form-label">الفئات المتاحة</label>
                            <input type="text" class="form-control @error('card_denominations') is-invalid @enderror"
                                   id="card_denominations" name="card_denominations" value="{{ old('card_denominations') }}" placeholder="مثال: 25,50,100,200">
                            <div class="form-text">أدخل الفئات مفصولة بفاصلة (مثال: 25,50,100,200)</div>
                            @error('card_denominations')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- تعليمات التسليم -->
                        <div class="col-12 mb-3">
                            <label for="delivery_instructions" class="form-label">تعليمات التسليم</label>
                            <textarea class="form-control @error('delivery_instructions') is-invalid @enderror"
                                      id="delivery_instructions" name="delivery_instructions" rows="3" placeholder="مثال: سيتم إرسال الكود عبر البريد الإلكتروني فوراً">{{ old('delivery_instructions') }}</textarea>
                            @error('delivery_instructions')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- الصورة الرئيسية -->
                        <div class="col-12 mb-3">
                            <label for="image" class="form-label">الصورة الرئيسية</label>
                            <input type="file" class="form-control @error('image') is-invalid @enderror"
                                   id="image" name="image" accept="image/*">
                            <div class="form-text">الحد الأقصى للحجم: 2MB. الأنواع المسموحة: JPG, PNG, GIF</div>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- معرض الصور -->
                        <div class="col-12 mb-3">
                            <label for="gallery" class="form-label">معرض الصور</label>
                            <input type="file" class="form-control @error('gallery') is-invalid @enderror"
                                   id="gallery" name="gallery[]" accept="image/*" multiple>
                            <div class="form-text">يمكن رفع عدة صور. الحد الأقصى للحجم: 2MB لكل صورة</div>
                            @error('gallery')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <a href="{{ route('dashboard.products.index') }}" class="btn btn-outline-secondary me-2">إلغاء</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-2"></i>
                            إنشاء المنتج
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- خيارات المنتج -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-gear me-2"></i>
                    خيارات المنتج
                </h5>
            </div>
            <div class="card-body">
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="is_digital" name="is_digital"
                           value="1" {{ old('is_digital', true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_digital">
                        بطاقة رقمية
                    </label>
                </div>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                           value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">
                        نشط
                    </label>
                </div>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured"
                           value="1" {{ old('is_featured') ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_featured">
                        بطاقة مميزة
                    </label>
                </div>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="is_instant_delivery" name="is_instant_delivery"
                           value="1" {{ old('is_instant_delivery', true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_instant_delivery">
                        تسليم فوري
                    </label>
                </div>
            </div>
        </div>

        <!-- SEO -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-search me-2"></i>
                    SEO
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="meta_title" class="form-label">عنوان SEO</label>
                    <input type="text" class="form-control @error('meta_title') is-invalid @enderror"
                           id="meta_title" name="meta_title" value="{{ old('meta_title') }}">
                    @error('meta_title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="meta_description" class="form-label">وصف SEO</label>
                    <textarea class="form-control @error('meta_description') is-invalid @enderror"
                              id="meta_description" name="meta_description" rows="3">{{ old('meta_description') }}</textarea>
                    @error('meta_description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="tags" class="form-label">العلامات</label>
                    <input type="text" class="form-control @error('tags') is-invalid @enderror"
                           id="tags" name="tags" value="{{ old('tags') }}" placeholder="علامة1, علامة2, علامة3">
                    <div class="form-text">افصل بين العلامات بفاصلة</div>
                    @error('tags')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- نصائح -->
        <div class="card mt-3">
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
                        اسم المنتج يجب أن يكون واضحاً ومميزاً
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        SKU يجب أن يكون فريداً ومميزاً
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        الصور تحسن من مظهر المنتج
                    </li>
                    <li class="mb-0">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        الوصف الكامل يساعد في SEO
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // معالجة العلامات
    const tagsInput = document.getElementById('tags');
    if (tagsInput) {
        tagsInput.addEventListener('blur', function() {
            const tags = this.value.split(',').map(tag => tag.trim()).filter(tag => tag);
            this.value = tags.join(', ');
        });
    }
});
</script>
@endpush

@push('styles')
<style>
.form-check-input:checked {
    background-color: var(--primary-purple);
    border-color: var(--primary-purple);
}

.form-check-input:focus {
    box-shadow: 0 0 0 0.25rem rgba(139, 92, 246, 0.25);
}
</style>
@endpush
@endsection
