@extends('layouts.app')

@section('title', $product->name . ' - متجر البطاقات الرقمية')

@section('content')
<div class="min-h-screen bg-[#0F0F0F] py-12">
    <div class="container mx-auto px-4">
        <!-- Breadcrumb -->
        <nav class="mb-8 flex items-center gap-2 text-sm">
            <a href="{{ route('home') }}" class="text-gray-400 hover:text-purple-500 transition-colors">الرئيسية</a>
            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            <a href="{{ route('products.index') }}" class="text-gray-400 hover:text-purple-500 transition-colors">المنتجات</a>
            @if($product->category)
            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            <a href="{{ route('products.index', ['category' => $product->category->slug]) }}" class="text-gray-400 hover:text-purple-500 transition-colors">{{ $product->category->name }}</a>
            @endif
            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            <span class="text-white font-medium">{{ $product->name }}</span>
        </nav>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-500/10 border border-green-500/20 rounded-xl text-green-400">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 bg-red-500/10 border border-red-500/20 rounded-xl">
                <ul class="text-red-400 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Product Details -->
        <div class="grid lg:grid-cols-2 gap-12 mb-20">
            <!-- Product Images -->
            <div>
                <div class="relative rounded-3xl overflow-hidden bg-[#1A1A1A] border border-purple-500/20 mb-4">
                    <div class="aspect-square">
                        @if($product->image)
                            <img id="mainImage" src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-purple-500/20 to-orange-500/20 flex items-center justify-center">
                                <svg class="w-32 h-32 text-white/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                    </div>

                    <!-- Badges -->
                    <div class="absolute top-6 right-6 flex flex-col gap-2">
                        @if($product->isOnSale())
                            <span class="bg-gradient-to-r from-red-500 to-pink-500 text-white px-4 py-2 rounded-full text-sm font-bold shadow-xl">
                                خصم {{ round((($product->price - $product->sale_price) / $product->price) * 100) }}%
                            </span>
                        @endif

                        @if($product->is_featured)
                            <span class="bg-gradient-to-r from-purple-500 to-orange-500 text-white px-4 py-2 rounded-full text-sm font-bold shadow-xl">
                                ⭐ مميز
                            </span>
                        @endif

                        @if($product->is_instant_delivery)
                            <span class="bg-gradient-to-r from-blue-500 to-cyan-500 text-white px-4 py-2 rounded-full text-sm font-bold shadow-xl">
                                ⚡ توصيل فوري
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Gallery Thumbnails -->
                @if($product->gallery && count($product->gallery) > 0)
                <div class="grid grid-cols-4 gap-4">
                    <button onclick="changeImage('{{ asset('storage/' . $product->image) }}')" class="rounded-xl overflow-hidden border-2 border-purple-500 opacity-100">
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full aspect-square object-cover">
                    </button>
                    @foreach($product->gallery as $image)
                    <button onclick="changeImage('{{ asset('storage/' . $image) }}')" class="rounded-xl overflow-hidden border-2 border-transparent hover:border-purple-500 opacity-70 hover:opacity-100 transition-all">
                        <img src="{{ asset('storage/' . $image) }}" alt="{{ $product->name }}" class="w-full aspect-square object-cover">
                    </button>
                    @endforeach
                </div>
                @endif
            </div>

            <!-- Product Info -->
            <div>
                <!-- Category -->
                @if($product->category)
                <a href="{{ route('products.index', ['category' => $product->category->slug]) }}"
                   class="inline-block px-4 py-2 bg-purple-500/10 border border-purple-500/30 rounded-full text-purple-400 text-sm font-semibold mb-4 hover:bg-purple-500/20 transition-colors">
                    {{ $product->category->name }}
                </a>
                @endif

                <!-- Product Name -->
                <h1 class="text-4xl lg:text-5xl font-black text-white mb-4 leading-tight">{{ $product->name }}</h1>

                <!-- Rating -->
                <div class="flex items-center gap-4 mb-6">
                    <div class="flex items-center gap-2">
                        <div class="flex items-center">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= floor($averageRating))
                                    <svg class="w-5 h-5 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endif
                            @endfor
                        </div>
                        <span class="text-white font-semibold">{{ number_format($averageRating, 1) }}</span>
                        <span class="text-gray-400">({{ $totalReviews }} تقييم)</span>
                    </div>
                </div>

                <!-- Price -->
                <div class="mb-8">
                    @if($product->isOnSale())
                        <div class="flex items-baseline gap-4 mb-2">
                            <span class="text-5xl font-black bg-gradient-to-r from-purple-500 to-orange-500 bg-clip-text text-transparent">
                                {{ number_format($product->sale_price, 0) }} $
                            </span>
                            <span class="text-2xl text-gray-500 line-through">{{ number_format($product->price, 0) }} $</span>
                        </div>
                        <span class="text-green-400 font-semibold">وفّر {{ number_format($product->price - $product->sale_price, 0) }} $</span>
                    @else
                        <span class="text-5xl font-black bg-gradient-to-r from-purple-500 to-orange-500 bg-clip-text text-transparent">
                            {{ number_format($product->price, 0) }} $
                        </span>
                    @endif
                </div>

                <!-- Short Description -->
                @if($product->short_description)
                <p class="text-gray-300 text-lg mb-8 leading-relaxed">{{ $product->short_description }}</p>
                @endif

                <!-- Features -->
                <div class="grid grid-cols-2 gap-4 mb-8">
                    @if($product->is_instant_delivery)
                    <div class="flex items-center gap-3 p-4 bg-[#1A1A1A] rounded-xl border border-purple-500/20">
                        <div class="w-10 h-10 bg-purple-500/20 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-white font-semibold text-sm">توصيل فوري</div>
                            <div class="text-gray-400 text-xs">استلم فوراً</div>
                        </div>
                    </div>
                    @endif

                    @if($product->loyalty_points_earn > 0)
                    <div class="flex items-center gap-3 p-4 bg-[#1A1A1A] rounded-xl border border-orange-500/20">
                        <div class="w-10 h-10 bg-orange-500/20 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-white font-semibold text-sm">نقاط ولاء</div>
                            <div class="text-gray-400 text-xs">+{{ $product->loyalty_points_earn }} نقطة</div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Quantity & Add to Cart -->
                <div class="flex gap-4 mb-8">
                    <div class="flex items-center gap-3 bg-[#1A1A1A] rounded-xl border border-purple-500/20 px-6">
                        <button onclick="decreaseQuantity()" class="text-gray-400 hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                            </svg>
                        </button>
                        <input type="number" id="quantity" value="1" min="1" class="w-16 bg-transparent text-center text-white font-bold text-lg focus:outline-none">
                        <button onclick="increaseQuantity()" class="text-gray-400 hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </button>
                    </div>

                    <button onclick="addToCart()" class="flex-1 bg-gradient-to-r from-purple-500 to-orange-500 text-white py-4 px-8 rounded-xl font-bold text-lg hover:shadow-2xl hover:shadow-purple-500/50 transition-all duration-300 flex items-center justify-center gap-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        أضف للسلة
                    </button>
                </div>

                <!-- Additional Info -->
                <div class="space-y-4 p-6 bg-[#1A1A1A] rounded-xl border border-purple-500/20">
                    @if($product->sku)
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-400">رمز المنتج:</span>
                        <span class="text-white font-semibold">{{ $product->sku }}</span>
                    </div>
                    @endif

                    @if($product->card_provider)
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-400">المزود:</span>
                        <span class="text-white font-semibold">{{ $product->card_provider }}</span>
                    </div>
                    @endif

                    @if($product->card_region)
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-400">المنطقة:</span>
                        <span class="text-white font-semibold">{{ $product->card_region }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tabs: Description, Reviews -->
        <div class="mb-20">
            <div class="flex gap-4 mb-8 border-b border-purple-500/20">
                <button onclick="showTab('description')" id="tab-description" class="px-6 py-4 font-bold text-white border-b-2 border-purple-500 transition-colors">
                    الوصف
                </button>
                <button onclick="showTab('reviews')" id="tab-reviews" class="px-6 py-4 font-bold text-gray-400 hover:text-white border-b-2 border-transparent transition-colors">
                    التقييمات ({{ $totalReviews }})
                </button>
            </div>

            <!-- Description Tab -->
            <div id="content-description" class="tab-content">
                <div class="prose prose-invert max-w-none">
                    <div class="text-gray-300 text-lg leading-relaxed">
                        {!! nl2br(e($product->description)) !!}
                    </div>

                    @if($product->delivery_instructions)
                    <div class="mt-8 p-6 bg-[#1A1A1A] rounded-xl border border-purple-500/20">
                        <h3 class="text-xl font-bold text-white mb-4">تعليمات التسليم</h3>
                        <p class="text-gray-300">{!! nl2br(e($product->delivery_instructions)) !!}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Reviews Tab -->
            <div id="content-reviews" class="tab-content hidden">
                <!-- Rating Summary -->
                <div class="grid md:grid-cols-3 gap-8 mb-12">
                    <div class="text-center p-8 bg-[#1A1A1A] rounded-2xl border border-purple-500/20">
                        <div class="text-6xl font-black bg-gradient-to-r from-purple-500 to-orange-500 bg-clip-text text-transparent mb-2">
                            {{ number_format($averageRating, 1) }}
                        </div>
                        <div class="flex items-center justify-center mb-2">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-6 h-6 {{ $i <= floor($averageRating) ? 'text-orange-500' : 'text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                        <div class="text-gray-400">{{ $totalReviews }} تقييم</div>
                    </div>

                    <div class="md:col-span-2 space-y-3">
                        @foreach([5,4,3,2,1] as $stars)
                        @php
                            $count = $ratingDistribution[$stars] ?? 0;
                            $percentage = $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0;
                        @endphp
                        <div class="flex items-center gap-4">
                            <span class="text-white font-semibold w-16">{{ $stars }} نجوم</span>
                            <div class="flex-1 h-3 bg-[#1A1A1A] rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-purple-500 to-orange-500 rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                            <span class="text-gray-400 w-12 text-left">{{ $count }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Add Review Form -->
                @auth
                    @if($hasPurchased && !$existingReview)
                    <div class="mb-8 p-6 bg-gradient-to-r from-purple-500/10 to-orange-500/10 rounded-xl border border-purple-500/20">
                        <h3 class="text-xl font-bold text-white mb-4">أضف تقييمك</h3>
                        <form action="{{ route('reviews.store', $product) }}" method="POST" id="reviewForm">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-sm font-semibold text-gray-300 mb-2">التقييم</label>
                                <div class="flex items-center gap-2" id="ratingStars">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-8 h-8 cursor-pointer star-rating text-gray-600 hover:text-orange-500 transition-colors" 
                                             data-rating="{{ $i }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                </div>
                                <input type="hidden" name="rating" id="ratingValue" value="0" required>
                                @error('rating')
                                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="review_title" class="block text-sm font-semibold text-gray-300 mb-2">عنوان التقييم (اختياري)</label>
                                <input type="text" name="title" id="review_title" 
                                       class="w-full px-4 py-3 bg-[#0F0F0F] border border-purple-500/20 rounded-xl text-white focus:outline-none focus:border-purple-500" 
                                       placeholder="اكتب عنواناً للتقييم">
                                @error('title')
                                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="review_comment" class="block text-sm font-semibold text-gray-300 mb-2">التعليق</label>
                                <textarea name="comment" id="review_comment" rows="4" 
                                          class="w-full px-4 py-3 bg-[#0F0F0F] border border-purple-500/20 rounded-xl text-white focus:outline-none focus:border-purple-500 resize-none" 
                                          placeholder="شارك تجربتك مع هذا المنتج..."></textarea>
                                @error('comment')
                                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <button type="submit" 
                                    class="px-6 py-3 bg-gradient-to-r from-purple-500 to-orange-500 text-white rounded-xl font-bold hover:shadow-lg hover:shadow-purple-500/50 transition-all duration-300"
                                    onclick="if(document.getElementById('ratingValue').value == 0) { alert('يرجى اختيار تقييم'); return false; }">
                                إرسال التقييم
                            </button>
                        </form>
                    </div>
                    @elseif($hasPurchased && $existingReview)
                    <div class="mb-8 p-6 bg-[#1A1A1A] rounded-xl border border-purple-500/20">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-bold text-white">تقييمك</h3>
                            <form action="{{ route('reviews.destroy', $existingReview) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا التقييم؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-300 text-sm">حذف التقييم</button>
                            </form>
                        </div>
                        <div class="flex items-center gap-2 mb-2">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-5 h-5 {{ $i <= $existingReview->rating ? 'text-orange-500' : 'text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                        @if($existingReview->title)
                            <h4 class="text-white font-semibold mb-2">{{ $existingReview->title }}</h4>
                        @endif
                        @if($existingReview->comment)
                            <p class="text-gray-300">{{ $existingReview->comment }}</p>
                        @endif
                    </div>
                    @endif
                @endauth

                <!-- Reviews List -->
                <div class="space-y-6">
                    @forelse($product->reviews->where('is_approved', true) as $review)
                    <div class="p-6 bg-[#1A1A1A] rounded-xl border border-purple-500/20">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-full bg-gradient-to-r from-purple-500 to-orange-500 flex items-center justify-center text-white font-bold text-lg flex-shrink-0">
                                {{ mb_substr($review->user->first_name, 0, 1) }}
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-2">
                                    <div>
                                        <h4 class="text-white font-bold">{{ $review->user->full_name }}</h4>
                                        <div class="flex items-center gap-2">
                                            <div class="flex">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-orange-500' : 'text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                @endfor
                                            </div>
                                            <span class="text-gray-400 text-sm">{{ $review->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-gray-300">{{ $review->comment }}</p>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-12 text-gray-400">
                        لا توجد تقييمات بعد. كن أول من يقيّم هذا المنتج!
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Similar Products -->
        @if($similarProducts->count() > 0)
        <section class="mb-20">
            <h2 class="text-3xl lg:text-4xl font-black text-white mb-8">
                منتجات <span class="bg-gradient-to-r from-purple-500 to-orange-500 bg-clip-text text-transparent">مشابهة</span>
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($similarProducts as $similarProduct)
                    @include('components.product-card', ['product' => $similarProduct])
                @endforeach
            </div>
        </section>
        @endif
    </div>
</div>

@push('scripts')
<script>
function changeImage(src) {
    document.getElementById('mainImage').src = src;
}

// Rating Stars Interaction
document.addEventListener('DOMContentLoaded', function() {
    const stars = document.querySelectorAll('.star-rating');
    const ratingValue = document.getElementById('ratingValue');
    
    if (stars.length > 0 && ratingValue) {
        let currentRating = 0;
        
        stars.forEach(star => {
            star.addEventListener('click', function() {
                currentRating = parseInt(this.dataset.rating);
                ratingValue.value = currentRating;
                updateStars(currentRating);
            });
            
            star.addEventListener('mouseenter', function() {
                const hoverRating = parseInt(this.dataset.rating);
                updateStars(hoverRating);
            });
        });
        
        document.getElementById('ratingStars').addEventListener('mouseleave', function() {
            updateStars(currentRating);
        });
        
        function updateStars(rating) {
            stars.forEach((star, index) => {
                if (index < rating) {
                    star.classList.remove('text-gray-600');
                    star.classList.add('text-orange-500');
                } else {
                    star.classList.remove('text-orange-500');
                    star.classList.add('text-gray-600');
                }
            });
        }
    }
});

function showTab(tab) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('[id^="tab-"]').forEach(el => {
        el.classList.remove('border-purple-500', 'text-white');
        el.classList.add('border-transparent', 'text-gray-400');
    });

    // Show selected tab
    document.getElementById('content-' + tab).classList.remove('hidden');
    document.getElementById('tab-' + tab).classList.add('border-purple-500', 'text-white');
    document.getElementById('tab-' + tab).classList.remove('border-transparent', 'text-gray-400');
}

function increaseQuantity() {
    let input = document.getElementById('quantity');
    input.value = parseInt(input.value) + 1;
}

function decreaseQuantity() {
    let input = document.getElementById('quantity');
    if (parseInt(input.value) > 1) {
        input.value = parseInt(input.value) - 1;
    }
}

function addToCart() {
    const quantity = parseInt(document.getElementById('quantity').value);

    fetch('{{ route("cart.add") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            product_id: {{ $product->id }},
            quantity: quantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            document.getElementById('cart-count').textContent = data.cart_count;
            alert(data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>
@endpush
@endsection

