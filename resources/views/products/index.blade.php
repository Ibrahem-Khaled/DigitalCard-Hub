@extends('layouts.app')

@section('title', 'المنتجات - متجر البطاقات الرقمية')

@section('content')
<div class="min-h-screen bg-[#0F0F0F] py-12">
    <div class="container mx-auto px-4">
        <!-- Page Header -->
        <div class="mb-12">
            <h1 class="text-4xl lg:text-5xl font-black text-white mb-4">
                جميع <span class="bg-gradient-to-r from-purple-500 to-orange-500 bg-clip-text text-transparent">المنتجات</span>
            </h1>
            <p class="text-gray-400 text-lg">استكشف مجموعتنا الواسعة من البطاقات الرقمية</p>
        </div>

        <div class="grid lg:grid-cols-4 gap-8">
            <!-- Filters Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-[#1A1A1A] rounded-2xl border border-purple-500/20 p-6 sticky top-24">
                    <h3 class="text-xl font-bold text-white mb-6">تصفية النتائج</h3>

                    <form id="filterForm" method="GET" action="{{ route('products.index') }}">
                        <!-- Search -->
                        <div class="mb-6">
                            <label class="block text-gray-400 text-sm mb-2">البحث</label>
                            <input type="text"
                                   name="search"
                                   value="{{ request('search') }}"
                                   placeholder="ابحث عن منتج..."
                                   class="w-full bg-[#0F0F0F] border border-purple-500/20 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-purple-500 transition-colors">
                        </div>

                        <!-- Categories -->
                        <div class="mb-6">
                            <label class="block text-gray-400 text-sm mb-3">الفئات</label>
                            <div class="space-y-2 max-h-64 overflow-y-auto">
                                <label class="flex items-center gap-3 text-gray-300 hover:text-white cursor-pointer transition-colors">
                                    <input type="radio" name="category" value="" {{ !request('category') ? 'checked' : '' }} class="w-4 h-4 text-purple-500 bg-[#0F0F0F] border-purple-500/20">
                                    <span>الكل</span>
                                </label>
                                @foreach($categories as $category)
                                <label class="flex items-center gap-3 text-gray-300 hover:text-white cursor-pointer transition-colors">
                                    <input type="radio" name="category" value="{{ $category->slug }}" {{ request('category') == $category->slug ? 'checked' : '' }} class="w-4 h-4 text-purple-500 bg-[#0F0F0F] border-purple-500/20">
                                    <span>{{ $category->name }}</span>
                                    <span class="mr-auto text-sm text-gray-500">({{ $category->products->count() }})</span>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Price Range -->
                        <div class="mb-6">
                            <label class="block text-gray-400 text-sm mb-3">نطاق السعر</label>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <input type="number"
                                           name="min_price"
                                           value="{{ request('min_price') }}"
                                           placeholder="من"
                                           class="w-full bg-[#0F0F0F] border border-purple-500/20 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-purple-500 transition-colors">
                                </div>
                                <div>
                                    <input type="number"
                                           name="max_price"
                                           value="{{ request('max_price') }}"
                                           placeholder="إلى"
                                           class="w-full bg-[#0F0F0F] border border-purple-500/20 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-purple-500 transition-colors">
                                </div>
                            </div>
                        </div>

                        <!-- Sort By -->
                        <div class="mb-6">
                            <label class="block text-gray-400 text-sm mb-3">الترتيب حسب</label>
                            <select name="sort"
                                    class="w-full bg-[#0F0F0F] border border-purple-500/20 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-purple-500 transition-colors">
                                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>الأحدث</option>
                                <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>الأكثر شعبية</option>
                                <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>السعر: من الأقل للأعلى</option>
                                <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>السعر: من الأعلى للأقل</option>
                                <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>الاسم</option>
                            </select>
                        </div>

                        <!-- Filter Buttons -->
                        <div class="flex gap-3">
                            <button type="submit"
                                    class="flex-1 bg-gradient-to-r from-purple-500 to-orange-500 text-white py-3 rounded-xl font-bold hover:shadow-lg hover:shadow-purple-500/50 transition-all duration-300">
                                تطبيق
                            </button>
                            <a href="{{ route('products.index') }}"
                               class="px-6 py-3 bg-[#0F0F0F] border border-purple-500/20 rounded-xl text-gray-300 hover:text-white hover:border-purple-500 transition-all duration-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="lg:col-span-3">
                <!-- Results Header -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
                    <div class="text-gray-400">
                        عرض <span class="text-white font-semibold">{{ $products->count() }}</span> من
                        <span class="text-white font-semibold">{{ $products->total() }}</span> منتج
                    </div>

                    <!-- View Toggle -->
                    <div class="flex gap-2">
                        <button onclick="setView('grid')" id="view-grid"
                                class="p-3 bg-purple-500/20 border border-purple-500 rounded-xl text-purple-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                            </svg>
                        </button>
                        <button onclick="setView('list')" id="view-list"
                                class="p-3 bg-[#0F0F0F] border border-purple-500/20 rounded-xl text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Products -->
                @if($products->count() > 0)
                <div id="products-container" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-12">
                    @foreach($products as $product)
                        @include('components.product-card', ['product' => $product])
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="flex justify-center">
                    {{ $products->links() }}
                </div>
                @else
                <!-- No Results -->
                <div class="text-center py-20">
                    <div class="w-32 h-32 bg-[#1A1A1A] rounded-full flex items-center justify-center mx-auto mb-8">
                        <svg class="w-16 h-16 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <h2 class="text-3xl font-bold text-white mb-4">لم يتم العثور على نتائج</h2>
                    <p class="text-gray-400 text-lg mb-8">جرب تغيير معايير البحث أو التصفية</p>
                    <a href="{{ route('products.index') }}"
                       class="inline-flex items-center gap-2 px-8 py-4 bg-gradient-to-r from-purple-500 to-orange-500 text-white rounded-full font-bold text-lg hover:shadow-2xl hover:shadow-purple-500/50 transition-all duration-300">
                        عرض جميع المنتجات
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function setView(view) {
    const container = document.getElementById('products-container');
    const gridBtn = document.getElementById('view-grid');
    const listBtn = document.getElementById('view-list');

    if (view === 'grid') {
        container.className = 'grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-12';
        gridBtn.className = 'p-3 bg-purple-500/20 border border-purple-500 rounded-xl text-purple-400';
        listBtn.className = 'p-3 bg-[#0F0F0F] border border-purple-500/20 rounded-xl text-gray-400';
    } else {
        container.className = 'grid grid-cols-1 gap-6 mb-12';
        gridBtn.className = 'p-3 bg-[#0F0F0F] border border-purple-500/20 rounded-xl text-gray-400';
        listBtn.className = 'p-3 bg-purple-500/20 border border-purple-500 rounded-xl text-purple-400';
    }
}

// Auto submit form on change
document.querySelectorAll('#filterForm input[type="radio"], #filterForm select').forEach(element => {
    element.addEventListener('change', function() {
        document.getElementById('filterForm').submit();
    });
});
</script>

@endpush
@endsection

