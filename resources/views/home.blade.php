@extends('layouts.app')

@section('title', 'الرئيسية - متجر البطاقات الرقمية')

@section('content')
<!-- Sliders Section -->
@if($sliders->count() > 0)
<section class="relative overflow-hidden">
    <div class="slider-container">
        <div class="slider-wrapper">
            @foreach($sliders as $index => $slider)
            <div class="slider-slide {{ $index === 0 ? 'active' : '' }}" data-slide="{{ $index }}">
                <div class="slider-image">
                    <img src="{{ $slider->image_url }}" alt="{{ $slider->title }}" class="w-full h-full object-cover">
                    <div class="slider-overlay"></div>
                </div>

                <div class="slider-content">
                    <div class="container mx-auto px-4">
                        <div class="max-w-4xl mx-auto">
                            <div class="slider-text-content">
                                @if($slider->title)
                                <h1 class="slider-title">{{ $slider->title }}</h1>
                                @endif

                                @if($slider->description)
                                <p class="slider-description">{{ $slider->description }}</p>
                                @endif

                                @if($slider->button_text && $slider->button_url)
                                <div class="slider-actions">
                                    <a href="{{ $slider->button_url }}" class="slider-btn-primary">
                                        {{ $slider->button_text }}
                                    </a>
                                    <a href="{{ route('products.index') }}" class="slider-btn-secondary">
                                        تصفح المنتجات
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Navigation Arrows -->
        @if($sliders->count() > 1)
        <button class="slider-nav slider-nav-prev" id="sliderPrev">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>

        <button class="slider-nav slider-nav-next" id="sliderNext">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
        @endif

        <!-- Indicators -->
        @if($sliders->count() > 1)
        <div class="slider-indicators">
            @foreach($sliders as $index => $slider)
            <button class="slider-indicator {{ $index === 0 ? 'active' : '' }}" data-slide="{{ $index }}"></button>
            @endforeach
        </div>
        @endif
    </div>
</section>
@else
<!-- Fallback Hero Section -->
<section class="relative overflow-hidden py-20 bg-gradient-to-b from-[#1A1A1A] to-[#0F0F0F]">
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute w-96 h-96 bg-purple-500/20 rounded-full blur-3xl -top-48 -right-48"></div>
        <div class="absolute w-96 h-96 bg-orange-500/20 rounded-full blur-3xl -bottom-48 -left-48"></div>
    </div>

    <div class="container mx-auto px-4 relative z-10">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div>
                <div class="inline-block px-4 py-2 bg-purple-500/10 border border-purple-500/30 rounded-full mb-6">
                    <span class="text-purple-400 text-sm font-semibold">🔥 عروض حصرية لفترة محدودة</span>
                </div>

                <h1 class="text-5xl lg:text-7xl font-black mb-6 leading-tight">
                    <span class="text-white">أفضل</span>
                    <span class="block bg-gradient-to-r from-purple-500 to-orange-500 bg-clip-text text-transparent">البطاقات الرقمية</span>
                    <span class="text-white">في مصر</span>
                </h1>

                <p class="text-xl text-gray-400 mb-8 leading-relaxed">
                    اشترِ بطاقات الألعاب والتطبيقات الرقمية بأفضل الأسعار مع توصيل فوري وآمن.
                    نوفر لك أكثر من <span class="text-purple-400 font-semibold">{{ $stats['total_products'] }}</span> منتج.
                </p>

                <div class="flex flex-wrap gap-4 mb-8">
                    <a href="{{ route('products.index') }}" class="px-8 py-4 bg-gradient-to-r from-purple-500 to-orange-500 text-white rounded-full font-bold text-lg hover:shadow-2xl hover:shadow-purple-500/50 transition-all duration-300 hover:scale-105">
                        تصفح المنتجات
                    </a>
                    <a href="#featured" class="px-8 py-4 bg-white/10 backdrop-blur-lg border border-white/20 text-white rounded-full font-bold text-lg hover:bg-white/20 transition-all duration-300">
                        المنتجات المميزة
                    </a>
                </div>

                <div class="grid grid-cols-3 gap-6">
                    <div class="text-center">
                        <div class="text-3xl font-black text-white mb-2">{{ $stats['total_products'] }}+</div>
                        <div class="text-sm text-gray-400">منتج متاح</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-black text-white mb-2">{{ $stats['total_sales'] }}+</div>
                        <div class="text-sm text-gray-400">عملية بيع</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-black text-white mb-2">24/7</div>
                        <div class="text-sm text-gray-400">دعم فني</div>
                    </div>
                </div>
            </div>

            <div class="relative">
                <div class="relative z-10">
                    <img src="https://via.placeholder.com/600x700/8B5CF6/FFFFFF?text=Digital+Cards" alt="Hero Image" class="rounded-3xl shadow-2xl shadow-purple-500/30 w-full">
                </div>
                <div class="absolute inset-0 bg-gradient-to-r from-purple-500/30 to-orange-500/30 rounded-3xl blur-2xl transform scale-95"></div>
            </div>
        </div>
    </div>
</section>
@endif

<!-- Categories Section -->
<section class="py-20 bg-[#0F0F0F]">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-4xl lg:text-5xl font-black text-white mb-4">
                تصفح حسب <span class="bg-gradient-to-r from-purple-500 to-orange-500 bg-clip-text text-transparent">الفئات</span>
            </h2>
            <p class="text-gray-400 text-lg">اختر الفئة المناسبة لك واستكشف مجموعتنا الواسعة</p>
        </div>

        <!-- Categories Carousel -->
        <div class="relative">
            <!-- Navigation Buttons -->
            <button id="categories-prev" class="absolute right-0 top-1/2 -translate-y-1/2 -translate-x-1/2 z-10 w-14 h-14 bg-gradient-to-r from-purple-500 to-orange-500 rounded-full flex items-center justify-center text-white shadow-2xl shadow-purple-500/50 hover:scale-110 transition-all duration-300 group">
                <svg class="w-6 h-6 group-hover:scale-125 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>

            <button id="categories-next" class="absolute left-0 top-1/2 -translate-y-1/2 translate-x-1/2 z-10 w-14 h-14 bg-gradient-to-r from-purple-500 to-orange-500 rounded-full flex items-center justify-center text-white shadow-2xl shadow-purple-500/50 hover:scale-110 transition-all duration-300 group">
                <svg class="w-6 h-6 group-hover:scale-125 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>

            <!-- Carousel Container -->
            <div class="overflow-hidden px-12">
                <div id="categories-carousel" class="flex gap-8 transition-transform duration-500 ease-in-out">
                    @foreach($categories as $category)
                    <div class="flex-shrink-0" style="width: calc(25% - 24px);">
                        <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="group block text-center">
                            <!-- صورة دائرية -->
                            <div class="relative mx-auto mb-4 w-40 h-40">
                                <div class="absolute inset-0 bg-gradient-to-r from-purple-500 to-orange-500 rounded-full blur-xl opacity-0 group-hover:opacity-50 transition-opacity duration-300"></div>
                                <div class="relative w-full h-full rounded-full overflow-hidden border-4 border-purple-500/20 group-hover:border-purple-500 transition-all duration-300 bg-[#1A1A1A]">
                                    @if($category->image)
                                        <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                    @else
                                        <div class="w-full h-full bg-gradient-to-br from-purple-500/20 to-orange-500/20 flex items-center justify-center">
                                            <span class="text-5xl font-black text-white/30">{{ mb_substr($category->name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                </div>
                                <!-- Badge -->
                                <div class="absolute -top-2 -right-2 bg-gradient-to-r from-purple-500 to-orange-500 w-10 h-10 rounded-full flex items-center justify-center text-white text-xs font-bold shadow-lg">
                                    🔥
                                </div>
                            </div>
                            <!-- اسم الفئة -->
                            <h3 class="text-lg font-bold text-white mb-1 group-hover:text-purple-400 transition-colors">{{ $category->name }}</h3>
                            <p class="text-sm text-gray-400">{{ $category->products->count() }} منتج</p>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Indicators -->
            <div class="flex justify-center gap-2 mt-8">
                @php
                    $totalCategories = $categories->count();
                    $itemsPerView = 4; // Desktop
                    $totalPages = ceil($totalCategories / $itemsPerView);
                @endphp
                @for($i = 0; $i < $totalPages; $i++)
                    <button class="categories-indicator w-3 h-3 rounded-full transition-all duration-300 {{ $i === 0 ? 'bg-gradient-to-r from-purple-500 to-orange-500 w-8' : 'bg-white/20 hover:bg-white/40' }}" data-index="{{ $i }}"></button>
                @endfor
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const carousel = document.getElementById('categories-carousel');
    const prevBtn = document.getElementById('categories-prev');
    const nextBtn = document.getElementById('categories-next');
    const indicators = document.querySelectorAll('.categories-indicator');

    let currentIndex = 0;
    const totalItems = {{ $categories->count() }};

    // Get items per view based on screen size
    function getItemsPerView() {
        if (window.innerWidth >= 1024) return 4; // lg
        if (window.innerWidth >= 768) return 3;  // md
        return 2; // mobile
    }

    let itemsPerView = getItemsPerView();
    const totalPages = Math.ceil(totalItems / itemsPerView);

    function updateCarousel() {
        const itemWidth = carousel.querySelector('div').offsetWidth;
        const gap = 24; // 6 * 4px (gap-6)
        const offset = currentIndex * itemsPerView * (itemWidth + gap);

        carousel.style.transform = `translateX(${offset}px)`;

        // Update indicators
        indicators.forEach((indicator, index) => {
            if (index === currentIndex) {
                indicator.classList.remove('bg-white/20', 'w-3');
                indicator.classList.add('bg-gradient-to-r', 'from-purple-500', 'to-orange-500', 'w-8');
            } else {
                indicator.classList.remove('bg-gradient-to-r', 'from-purple-500', 'to-orange-500', 'w-8');
                indicator.classList.add('bg-white/20', 'w-3');
            }
        });

        // Disable buttons at ends
        prevBtn.style.opacity = currentIndex === 0 ? '0.5' : '1';
        prevBtn.style.pointerEvents = currentIndex === 0 ? 'none' : 'auto';

        nextBtn.style.opacity = currentIndex >= totalPages - 1 ? '0.5' : '1';
        nextBtn.style.pointerEvents = currentIndex >= totalPages - 1 ? 'none' : 'auto';
    }

    prevBtn.addEventListener('click', () => {
        if (currentIndex > 0) {
            currentIndex--;
            updateCarousel();
        }
    });

    nextBtn.addEventListener('click', () => {
        if (currentIndex < totalPages - 1) {
            currentIndex++;
            updateCarousel();
        }
    });

    // Indicator click
    indicators.forEach((indicator, index) => {
        indicator.addEventListener('click', () => {
            currentIndex = index;
            updateCarousel();
        });
    });

    // Touch/Swipe support
    let touchStartX = 0;
    let touchEndX = 0;

    carousel.addEventListener('touchstart', (e) => {
        touchStartX = e.changedTouches[0].screenX;
    });

    carousel.addEventListener('touchend', (e) => {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    });

    function handleSwipe() {
        if (touchEndX < touchStartX - 50 && currentIndex < totalPages - 1) {
            // Swipe left (next)
            currentIndex++;
            updateCarousel();
        }
        if (touchEndX > touchStartX + 50 && currentIndex > 0) {
            // Swipe right (prev)
            currentIndex--;
            updateCarousel();
        }
    }

    // Auto-play (optional)
    let autoPlayInterval;
    function startAutoPlay() {
        autoPlayInterval = setInterval(() => {
            if (currentIndex < totalPages - 1) {
                currentIndex++;
            } else {
                currentIndex = 0;
            }
            updateCarousel();
        }, 5000); // 5 seconds
    }

    function stopAutoPlay() {
        clearInterval(autoPlayInterval);
    }

    // Start auto-play
    startAutoPlay();

    // Stop auto-play on hover
    carousel.addEventListener('mouseenter', stopAutoPlay);
    carousel.addEventListener('mouseleave', startAutoPlay);

    // Update on window resize
    let resizeTimeout;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
            itemsPerView = getItemsPerView();
            updateCarousel();
        }, 250);
    });

    // Initialize
    updateCarousel();
});
</script>
@endpush

@push('styles')
<style>
/* Slider Styles */
.slider-container {
    position: relative;
    height: 80vh;
    min-height: 600px;
    overflow: hidden;
}

.slider-wrapper {
    position: relative;
    width: 100%;
    height: 100%;
}

.slider-slide {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    transition: opacity 1s ease-in-out;
}

.slider-slide.active {
    opacity: 1;
}

.slider-image {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1;
}

.slider-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.slider-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(0,0,0,0.6) 0%, rgba(0,0,0,0.3) 50%, rgba(0,0,0,0.6) 100%);
    z-index: 2;
}

.slider-content {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    z-index: 3;
}

.slider-text-content {
    max-width: 600px;
    animation: slideInUp 1s ease-out;
}

.slider-title {
    font-size: 3.5rem;
    font-weight: 900;
    color: white;
    margin-bottom: 1.5rem;
    line-height: 1.1;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
}

.slider-description {
    font-size: 1.25rem;
    color: rgba(255,255,255,0.9);
    margin-bottom: 2rem;
    line-height: 1.6;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
}

.slider-actions {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.slider-btn-primary {
    display: inline-block;
    padding: 1rem 2rem;
    background: linear-gradient(135deg, #8B5CF6 0%, #F97316 100%);
    color: white;
    text-decoration: none;
    border-radius: 50px;
    font-weight: 700;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    box-shadow: 0 10px 30px rgba(139, 92, 246, 0.3);
}

.slider-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 15px 40px rgba(139, 92, 246, 0.4);
    color: white;
}

.slider-btn-secondary {
    display: inline-block;
    padding: 1rem 2rem;
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(10px);
    border: 2px solid rgba(255,255,255,0.2);
    color: white;
    text-decoration: none;
    border-radius: 50px;
    font-weight: 600;
    font-size: 1.1rem;
    transition: all 0.3s ease;
}

.slider-btn-secondary:hover {
    background: rgba(255,255,255,0.2);
    border-color: rgba(255,255,255,0.3);
    color: white;
    transform: translateY(-2px);
}

/* Navigation Arrows */
.slider-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 60px;
    height: 60px;
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(10px);
    border: 2px solid rgba(255,255,255,0.2);
    border-radius: 50%;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    z-index: 4;
}

.slider-nav:hover {
    background: rgba(255,255,255,0.2);
    border-color: rgba(255,255,255,0.3);
    transform: translateY(-50%) scale(1.1);
}

.slider-nav-prev {
    left: 2rem;
}

.slider-nav-next {
    right: 2rem;
}

/* Indicators */
.slider-indicators {
    position: absolute;
    bottom: 2rem;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 0.75rem;
    z-index: 4;
}

.slider-indicator {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: rgba(255,255,255,0.3);
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
}

.slider-indicator.active {
    background: linear-gradient(135deg, #8B5CF6 0%, #F97316 100%);
    transform: scale(1.2);
}

.slider-indicator:hover {
    background: rgba(255,255,255,0.6);
}

/* Animations */
@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(50px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive */
@media (max-width: 768px) {
    .slider-container {
        height: 60vh;
        min-height: 400px;
    }

    .slider-title {
        font-size: 2.5rem;
    }

    .slider-description {
        font-size: 1.1rem;
    }

    .slider-nav {
        width: 50px;
        height: 50px;
    }

    .slider-nav-prev {
        left: 1rem;
    }

    .slider-nav-next {
        right: 1rem;
    }

    .slider-indicators {
        bottom: 1rem;
    }

    .slider-actions {
        flex-direction: column;
        align-items: flex-start;
    }

    .slider-btn-primary,
    .slider-btn-secondary {
        width: 100%;
        text-align: center;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const slides = document.querySelectorAll('.slider-slide');
    const indicators = document.querySelectorAll('.slider-indicator');
    const prevBtn = document.getElementById('sliderPrev');
    const nextBtn = document.getElementById('sliderNext');

    if (slides.length === 0) return;

    let currentSlide = 0;
    const totalSlides = slides.length;

    function showSlide(index) {
        // Hide all slides
        slides.forEach(slide => slide.classList.remove('active'));
        indicators.forEach(indicator => indicator.classList.remove('active'));

        // Show current slide
        slides[index].classList.add('active');
        indicators[index].classList.add('active');

        currentSlide = index;
    }

    function nextSlide() {
        const next = (currentSlide + 1) % totalSlides;
        showSlide(next);
    }

    function prevSlide() {
        const prev = (currentSlide - 1 + totalSlides) % totalSlides;
        showSlide(prev);
    }

    // Event listeners
    if (nextBtn) nextBtn.addEventListener('click', nextSlide);
    if (prevBtn) prevBtn.addEventListener('click', prevSlide);

    indicators.forEach((indicator, index) => {
        indicator.addEventListener('click', () => showSlide(index));
    });

    // Auto-play
    let autoPlayInterval = setInterval(nextSlide, 5000);

    // Pause on hover
    const sliderContainer = document.querySelector('.slider-container');
    if (sliderContainer) {
        sliderContainer.addEventListener('mouseenter', () => {
            clearInterval(autoPlayInterval);
        });

        sliderContainer.addEventListener('mouseleave', () => {
            autoPlayInterval = setInterval(nextSlide, 5000);
        });
    }

    // Touch support
    let touchStartX = 0;
    let touchEndX = 0;

    sliderContainer.addEventListener('touchstart', (e) => {
        touchStartX = e.changedTouches[0].screenX;
    });

    sliderContainer.addEventListener('touchend', (e) => {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    });

    function handleSwipe() {
        const swipeThreshold = 50;
        if (touchEndX < touchStartX - swipeThreshold) {
            nextSlide();
        } else if (touchEndX > touchStartX + swipeThreshold) {
            prevSlide();
        }
    }
});
</script>
@endpush

<!-- Best Sellers Section -->
<section class="py-20 bg-[#1A1A1A]" id="best-sellers">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-12">
            <div>
                <h2 class="text-4xl lg:text-5xl font-black text-white mb-2">
                    الأكثر <span class="bg-gradient-to-r from-purple-500 to-orange-500 bg-clip-text text-transparent">مبيعاً</span>
                </h2>
                <p class="text-gray-400">المنتجات الأكثر طلباً من عملائنا</p>
            </div>
            <a href="{{ route('products.index', ['sort' => 'popular']) }}" class="hidden md:block px-6 py-3 bg-white/5 border border-white/10 rounded-full text-white hover:bg-white/10 transition-all">
                عرض الكل →
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($bestSellers->take(8) as $product)
                @include('components.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section class="py-20 bg-[#0F0F0F]" id="featured">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-12">
            <div>
                <h2 class="text-4xl lg:text-5xl font-black text-white mb-2">
                    المنتجات <span class="bg-gradient-to-r from-purple-500 to-orange-500 bg-clip-text text-transparent">المميزة</span>
                </h2>
                <p class="text-gray-400">مختارات خاصة من أفضل منتجاتنا</p>
            </div>
            <a href="{{ route('products.index') }}" class="hidden md:block px-6 py-3 bg-white/5 border border-white/10 rounded-full text-white hover:bg-white/10 transition-all">
                عرض الكل →
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($featuredProducts->take(8) as $product)
                @include('components.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>

<!-- New Products Section -->
<section class="py-20 bg-[#1A1A1A]">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-12">
            <div>
                <h2 class="text-4xl lg:text-5xl font-black text-white mb-2">
                    أحدث <span class="bg-gradient-to-r from-purple-500 to-orange-500 bg-clip-text text-transparent">المنتجات</span>
                </h2>
                <p class="text-gray-400">آخر الإضافات لمتجرنا</p>
            </div>
            <a href="{{ route('products.index', ['sort' => 'latest']) }}" class="hidden md:block px-6 py-3 bg-white/5 border border-white/10 rounded-full text-white hover:bg-white/10 transition-all">
                عرض الكل →
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($newProducts->take(8) as $product)
                @include('components.product-card', ['product' => $product, 'badge' => 'جديد'])
            @endforeach
        </div>
    </div>
</section>

<!-- Sale Products Section -->
@if($saleProducts->count() > 0)
<section class="py-20 bg-[#0F0F0F]">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-12">
            <div>
                <h2 class="text-4xl lg:text-5xl font-black text-white mb-2">
                    عروض <span class="bg-gradient-to-r from-purple-500 to-orange-500 bg-clip-text text-transparent">التخفيضات</span>
                </h2>
                <p class="text-gray-400">لا تفوت هذه العروض المحدودة</p>
            </div>
            <a href="{{ route('products.index', ['sort' => 'sale']) }}" class="hidden md:block px-6 py-3 bg-gradient-to-r from-purple-500 to-orange-500 text-white rounded-full font-bold hover:shadow-xl hover:shadow-purple-500/50 transition-all">
                عرض الكل →
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($saleProducts->take(8) as $product)
                @include('components.product-card', ['product' => $product, 'badge' => 'خصم'])
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Features Section -->
<section class="py-20 bg-[#1A1A1A]">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="text-center p-8 rounded-2xl bg-[#0F0F0F] border border-purple-500/20">
                <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-orange-500 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">توصيل فوري</h3>
                <p class="text-gray-400">احصل على بطاقتك فوراً بعد الدفع</p>
            </div>

            <div class="text-center p-8 rounded-2xl bg-[#0F0F0F] border border-purple-500/20">
                <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-orange-500 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">دفع آمن</h3>
                <p class="text-gray-400">معاملات آمنة ومشفرة 100%</p>
            </div>

            <div class="text-center p-8 rounded-2xl bg-[#0F0F0F] border border-purple-500/20">
                <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-orange-500 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">جودة مضمونة</h3>
                <p class="text-gray-400">بطاقات أصلية ومضمونة 100%</p>
            </div>

            <div class="text-center p-8 rounded-2xl bg-[#0F0F0F] border border-purple-500/20">
                <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-orange-500 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">دعم 24/7</h3>
                <p class="text-gray-400">فريق الدعم جاهز لخدمتك دائماً</p>
            </div>
        </div>
    </div>
</section>
@endsection
