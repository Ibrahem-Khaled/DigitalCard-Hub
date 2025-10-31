@extends('layouts.dashboard-new')

@section('title', 'تعديل المنتج - ' . $product->name . ' - متجر البطاقات الرقمية')

@section('page-title', 'تعديل المنتج')
@section('page-subtitle', 'تعديل بيانات المنتج: ' . $product->name)

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="page-title">تعديل المنتج</h3>
            <p class="page-subtitle">تعديل بيانات المنتج: {{ $product->name }}</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('dashboard.products.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-right me-2"></i>
                العودة للقائمة
            </a>
            <a href="{{ route('dashboard.products.show', $product) }}" class="btn btn-outline-primary">
                <i class="bi bi-eye me-2"></i>
                عرض المنتج
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-pencil-square me-2"></i>
                    بيانات المنتج
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('dashboard.products.update', $product) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <!-- اسم المنتج -->
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">اسم المنتج <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name', $product->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- SKU -->
                        <div class="col-md-6 mb-3">
                            <label for="sku" class="form-label">SKU <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('sku') is-invalid @enderror"
                                   id="sku" name="sku" value="{{ old('sku', $product->sku) }}" required>
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
                                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
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
                                   id="brand" name="brand" value="{{ old('brand', $product->brand) }}">
                            @error('brand')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- السعر -->
                        <div class="col-md-4 mb-3">
                            <label for="price" class="form-label">السعر <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror"
                                   id="price" name="price" value="{{ old('price', $product->price) }}" required>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- سعر البيع -->
                        <div class="col-md-4 mb-3">
                            <label for="sale_price" class="form-label">سعر البيع</label>
                            <input type="number" step="0.01" class="form-control @error('sale_price') is-invalid @enderror"
                                   id="sale_price" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}">
                            <div class="form-text">سعر مخفض (اختياري)</div>
                            @error('sale_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- سعر التكلفة -->
                        <div class="col-md-4 mb-3">
                            <label for="cost_price" class="form-label">سعر التكلفة</label>
                            <input type="number" step="0.01" class="form-control @error('cost_price') is-invalid @enderror"
                                   id="cost_price" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}">
                            @error('cost_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- الوصف المختصر -->
                        <div class="col-12 mb-3">
                            <label for="short_description" class="form-label">الوصف المختصر</label>
                            <textarea class="form-control @error('short_description') is-invalid @enderror"
                                      id="short_description" name="short_description" rows="3">{{ old('short_description', $product->short_description) }}</textarea>
                            @error('short_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- الوصف الكامل -->
                        <div class="col-12 mb-3">
                            <label for="description" class="form-label">الوصف الكامل</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="5">{{ old('description', $product->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- نقاط الولاء المكتسبة -->
                        <div class="col-md-6 mb-3">
                            <label for="loyalty_points_earn" class="form-label">نقاط الولاء المكتسبة</label>
                            <input type="number" class="form-control @error('loyalty_points_earn') is-invalid @enderror"
                                   id="loyalty_points_earn" name="loyalty_points_earn" value="{{ old('loyalty_points_earn', $product->loyalty_points_earn ?? 0) }}" min="0">
                            <div class="form-text">عدد النقاط التي يحصل عليها العميل عند الشراء</div>
                            @error('loyalty_points_earn')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- نقاط الولاء المطلوبة -->
                        <div class="col-md-6 mb-3">
                            <label for="loyalty_points_cost" class="form-label">نقاط الولاء المطلوبة</label>
                            <input type="number" class="form-control @error('loyalty_points_cost') is-invalid @enderror"
                                   id="loyalty_points_cost" name="loyalty_points_cost" value="{{ old('loyalty_points_cost', $product->loyalty_points_cost ?? 0) }}" min="0">
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
                                <option value="gift_card" {{ old('card_type', $product->card_type ?? '') == 'gift_card' ? 'selected' : '' }}>بطاقة هدايا</option>
                                <option value="gaming" {{ old('card_type', $product->card_type ?? '') == 'gaming' ? 'selected' : '' }}>بطاقة ألعاب</option>
                                <option value="subscription" {{ old('card_type', $product->card_type ?? '') == 'subscription' ? 'selected' : '' }}>اشتراك</option>
                                <option value="entertainment" {{ old('card_type', $product->card_type ?? '') == 'entertainment' ? 'selected' : '' }}>ترفيه</option>
                                <option value="mobile" {{ old('card_type', $product->card_type ?? '') == 'mobile' ? 'selected' : '' }}>شحن محمول</option>
                            </select>
                            @error('card_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- مزود البطاقة -->
                        <div class="col-md-6 mb-3">
                            <label for="card_provider" class="form-label">مزود البطاقة</label>
                            <input type="text" class="form-control @error('card_provider') is-invalid @enderror"
                                   id="card_provider" name="card_provider" value="{{ old('card_provider', $product->card_provider ?? '') }}" placeholder="مثال: أمازون، ستيم، نتفليكس">
                            @error('card_provider')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- المنطقة الجغرافية -->
                        <div class="col-md-6 mb-3">
                            <label for="card_region" class="form-label">المنطقة الجغرافية</label>
                            <select class="form-select @error('card_region') is-invalid @enderror" id="card_region" name="card_region">
                                <option value="">اختر المنطقة</option>
                                <option value="Global" {{ old('card_region', $product->card_region ?? '') == 'Global' ? 'selected' : '' }}>عالمي</option>
                                <option value="US" {{ old('card_region', $product->card_region ?? '') == 'US' ? 'selected' : '' }}>الولايات المتحدة</option>
                                <option value="EU" {{ old('card_region', $product->card_region ?? '') == 'EU' ? 'selected' : '' }}>أوروبا</option>
                                <option value="Egypt" {{ old('card_region', $product->card_region ?? '') == 'Egypt' ? 'selected' : '' }}>مصر</option>
                                <option value="Saudi" {{ old('card_region', $product->card_region ?? '') == 'Saudi' ? 'selected' : '' }}>السعودية</option>
                                <option value="UAE" {{ old('card_region', $product->card_region ?? '') == 'UAE' ? 'selected' : '' }}>الإمارات</option>
                            </select>
                            @error('card_region')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- ترتيب العرض -->
                        <div class="col-md-6 mb-3">
                            <label for="sort_order" class="form-label">ترتيب العرض</label>
                            <input type="number" class="form-control @error('sort_order') is-invalid @enderror"
                                   id="sort_order" name="sort_order" value="{{ old('sort_order', $product->sort_order) }}" min="0">
                            @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- الفئات المتاحة -->
                        <div class="col-12 mb-3">
                            <label for="card_denominations" class="form-label">الفئات المتاحة</label>
                            <input type="text" class="form-control @error('card_denominations') is-invalid @enderror"
                                   id="card_denominations" name="card_denominations" value="{{ old('card_denominations', is_array($product->card_denominations ?? null) ? implode(',', $product->card_denominations) : ($product->card_denominations ?? '')) }}" placeholder="مثال: 25,50,100,200">
                            <div class="form-text">أدخل الفئات مفصولة بفاصلة (مثال: 25,50,100,200)</div>
                            @error('card_denominations')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- تعليمات التسليم -->
                        <div class="col-12 mb-3">
                            <label for="delivery_instructions" class="form-label">تعليمات التسليم</label>
                            <textarea class="form-control @error('delivery_instructions') is-invalid @enderror"
                                      id="delivery_instructions" name="delivery_instructions" rows="3" placeholder="مثال: سيتم إرسال الكود عبر البريد الإلكتروني فوراً">{{ old('delivery_instructions', $product->delivery_instructions ?? '') }}</textarea>
                            @error('delivery_instructions')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- الصورة الرئيسية -->
                        <div class="col-12 mb-3">
                            <label for="image" class="form-label">الصورة الرئيسية</label>

                            @if($product->image)
                                <div class="mb-2">
                                    <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}"
                                         class="rounded" width="100" height="100">
                                    <div class="form-text">الصورة الحالية</div>
                                </div>
                            @endif

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

                            @if($product->gallery && count($product->gallery) > 0)
                                <div class="mb-2">
                                    <div class="row">
                                        @foreach($product->gallery as $image)
                                        <div class="col-md-3 mb-2">
                                            <img src="{{ Storage::url($image) }}" alt="{{ $product->name }}"
                                                 class="img-fluid rounded">
                                        </div>
                                        @endforeach
                                    </div>
                                    <div class="form-text">الصور الحالية</div>
                                </div>
                            @endif

                            <input type="file" class="form-control @error('gallery') is-invalid @enderror"
                                   id="gallery" name="gallery[]" accept="image/*" multiple>
                            <div class="form-text">يمكن رفع عدة صور. الحد الأقصى للحجم: 2MB لكل صورة</div>
                            @error('gallery')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <a href="{{ route('dashboard.products.show', $product) }}" class="btn btn-outline-secondary me-2">إلغاء</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-2"></i>
                            حفظ التغييرات
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
                           value="1" {{ old('is_digital', $product->is_digital) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_digital">
                        بطاقة رقمية
                    </label>
                </div>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                           value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">
                        نشط
                    </label>
                </div>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured"
                           value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_featured">
                        بطاقة مميزة
                    </label>
                </div>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="is_instant_delivery" name="is_instant_delivery"
                           value="1" {{ old('is_instant_delivery', $product->is_instant_delivery ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_instant_delivery">
                        تسليم فوري
                    </label>
                </div>
            </div>
        </div>

        <!-- معلومات المنتج -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    معلومات المنتج
                </h5>
            </div>
            <div class="card-body">
                <div class="info-item">
                    <label>الرابط الحالي:</label>
                    <span>{{ $product->slug }}</span>
                </div>
                <div class="info-item">
                    <label>تاريخ الإنشاء:</label>
                    <span>{{ $product->created_at->format('Y-m-d H:i:s') }}</span>
                </div>
                <div class="info-item">
                    <label>آخر تحديث:</label>
                    <span>{{ $product->updated_at->format('Y-m-d H:i:s') }}</span>
                </div>
                <div class="info-item">
                    <label>عدد التقييمات:</label>
                    <span>{{ $product->reviews->count() }}</span>
                </div>
                <div class="info-item">
                    <label>عدد الطلبات:</label>
                    <span>{{ $product->orderItems->count() }}</span>
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
                           id="meta_title" name="meta_title" value="{{ old('meta_title', $product->meta_title) }}">
                    @error('meta_title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="meta_description" class="form-label">وصف SEO</label>
                    <textarea class="form-control @error('meta_description') is-invalid @enderror"
                              id="meta_description" name="meta_description" rows="3">{{ old('meta_description', $product->meta_description) }}</textarea>
                    @error('meta_description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="tags" class="form-label">العلامات</label>
                    <input type="text" class="form-control @error('tags') is-invalid @enderror"
                           id="tags" name="tags" value="{{ old('tags', $product->tags ? implode(', ', $product->tags) : '') }}" placeholder="علامة1, علامة2, علامة3">
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
                        تأكد من صحة البيانات المدخلة
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        الرابط يتم تحديثه تلقائياً عند تغيير الاسم
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        يمكن تغيير ترتيب العرض في أي وقت
                    </li>
                    <li class="mb-0">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        جميع التغييرات محفوظة تلقائياً
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
.info-item {
    margin-bottom: 1rem;
}

.info-item label {
    font-weight: 600;
    color: var(--text-dark);
    display: block;
    margin-bottom: 0.25rem;
}

.info-item span {
    color: var(--text-muted);
}

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
