@extends('layouts.app')

@section('title', 'طلباتي - متجر البطاقات الرقمية')

@section('content')
<div class="min-h-screen bg-[#0F0F0F] py-12">
    <div class="container mx-auto px-4">
        <!-- Page Header -->
        <div class="flex justify-between items-center mb-12">
            <div>
                <h1 class="text-4xl lg:text-5xl font-black text-white mb-4">
                    <span class="bg-gradient-to-r from-purple-500 to-orange-500 bg-clip-text text-transparent">طلباتي</span>
                </h1>
                <p class="text-gray-400 text-lg">تتبع جميع طلباتك في مكان واحد</p>
            </div>
            <a href="{{ route('profile.index') }}"
               class="px-6 py-3 bg-[#1A1A1A] border border-purple-500/20 rounded-xl text-gray-300 hover:text-white hover:border-purple-500 transition-all duration-300">
                العودة للملف الشخصي
            </a>
        </div>

        <!-- Filters -->
        <div class="bg-[#1A1A1A] rounded-2xl border border-purple-500/20 p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-white flex items-center gap-2">
                    <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    <span class="bg-gradient-to-r from-purple-400 to-orange-400 bg-clip-text text-transparent">فلترة الطلبات</span>
                </h3>
                @if(request()->hasAny(['status', 'payment_status', 'date_from', 'date_to', 'sort_by', 'sort_order']))
                <a href="{{ route('profile.orders') }}" 
                   class="px-4 py-2 bg-red-500/10 border border-red-500/20 text-red-400 rounded-xl text-sm font-semibold hover:bg-red-500/20 transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    إلغاء الفلترة
                </a>
                @endif
            </div>

            <form method="GET" action="{{ route('profile.orders') }}" class="space-y-6">
                <!-- First Row: Status and Payment Status -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Status Filter -->
                    <div class="relative">
                        <label class="block text-sm font-semibold text-gray-300 mb-2 flex items-center gap-2">
                            <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            حالة الطلب
                        </label>
                        <select name="status" 
                                class="w-full px-4 py-3 bg-[#0F0F0F] border border-purple-500/20 rounded-xl text-white focus:outline-none focus:border-purple-500 transition-colors appearance-none cursor-pointer">
                            <option value="">جميع الحالات</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>⏳ في الانتظار</option>
                            <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>⚙️ قيد المعالجة</option>
                            <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>📦 تم الشحن</option>
                            <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>✅ تم التسليم</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>❌ ملغي</option>
                            <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>🔄 مسترد</option>
                        </select>
                    </div>

                    <!-- Payment Status Filter -->
                    <div class="relative">
                        <label class="block text-sm font-semibold text-gray-300 mb-2 flex items-center gap-2">
                            <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            حالة الدفع
                        </label>
                        <select name="payment_status" 
                                class="w-full px-4 py-3 bg-[#0F0F0F] border border-purple-500/20 rounded-xl text-white focus:outline-none focus:border-purple-500 transition-colors appearance-none cursor-pointer">
                            <option value="">جميع حالات الدفع</option>
                            <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>⏳ في الانتظار</option>
                            <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>✅ مدفوع</option>
                            <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>❌ فشل</option>
                            <option value="refunded" {{ request('payment_status') == 'refunded' ? 'selected' : '' }}>🔄 مسترد</option>
                        </select>
                    </div>
                </div>

                <!-- Second Row: Date Range -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Date From -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2 flex items-center gap-2">
                            <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            من تاريخ
                        </label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}"
                               class="w-full px-4 py-3 bg-[#0F0F0F] border border-purple-500/20 rounded-xl text-white focus:outline-none focus:border-purple-500 transition-colors">
                    </div>

                    <!-- Date To -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2 flex items-center gap-2">
                            <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            إلى تاريخ
                        </label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}"
                               class="w-full px-4 py-3 bg-[#0F0F0F] border border-purple-500/20 rounded-xl text-white focus:outline-none focus:border-purple-500 transition-colors">
                    </div>
                </div>

                <!-- Third Row: Sort Options -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Sort By -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2 flex items-center gap-2">
                            <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path>
                            </svg>
                            ترتيب حسب
                        </label>
                        <select name="sort_by" 
                                class="w-full px-4 py-3 bg-[#0F0F0F] border border-purple-500/20 rounded-xl text-white focus:outline-none focus:border-purple-500 transition-colors appearance-none cursor-pointer">
                            <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>📅 تاريخ الطلب</option>
                            <option value="total_amount" {{ request('sort_by') == 'total_amount' ? 'selected' : '' }}>💰 المبلغ</option>
                            <option value="order_number" {{ request('sort_by') == 'order_number' ? 'selected' : '' }}>🔢 رقم الطلب</option>
                        </select>
                    </div>

                    <!-- Sort Order -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2 flex items-center gap-2">
                            <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                            </svg>
                            الاتجاه
                        </label>
                        <select name="sort_order" 
                                class="w-full px-4 py-3 bg-[#0F0F0F] border border-purple-500/20 rounded-xl text-white focus:outline-none focus:border-purple-500 transition-colors appearance-none cursor-pointer">
                            <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>⬇️ الأحدث أولاً</option>
                            <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>⬆️ الأقدم أولاً</option>
                        </select>
                    </div>
                </div>

                <!-- Filter Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 pt-4 border-t border-purple-500/20">
                    <button type="submit"
                            class="flex-1 px-6 py-3 bg-gradient-to-r from-purple-500 to-orange-500 text-white rounded-xl font-bold hover:shadow-2xl hover:shadow-purple-500/50 transition-all duration-300 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        تطبيق الفلترة
                    </button>
                    @if(request()->hasAny(['status', 'payment_status', 'date_from', 'date_to', 'sort_by', 'sort_order']))
                    <a href="{{ route('profile.orders') }}"
                       class="px-6 py-3 bg-[#0F0F0F] border border-purple-500/20 text-white rounded-xl font-bold hover:border-purple-500 transition-all flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        إعادة تعيين
                    </a>
                    @endif
                </div>
            </form>
        </div>

        @if($orders->count() > 0)
        <!-- Orders List -->
        <div class="space-y-6">
            @foreach($orders as $order)
            <div class="bg-[#1A1A1A] rounded-2xl border border-purple-500/20 overflow-hidden hover:border-purple-500 transition-all duration-300">
                <!-- Order Header -->
                <div class="p-6 border-b border-purple-500/20 bg-[#1F1F1F]">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <h3 class="text-xl font-bold text-white mb-2">طلب #{{ $order->order_number }}</h3>
                            <div class="flex items-center gap-4 text-sm text-gray-400">
                                <span>{{ $order->created_at->format('d/m/Y - h:i A') }}</span>
                                <span>•</span>
                                <span>{{ $order->getTotalItems() }} منتج</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="px-4 py-2 bg-{{ $order->getStatusColor() }}-500/20 border border-{{ $order->getStatusColor() }}-500/30 rounded-full text-{{ $order->getStatusColor() }}-400 font-semibold">
                                {{ $order->getStatusInArabic() }}
                            </span>
                            <a href="{{ route('profile.order-details', $order->id) }}"
                               class="px-6 py-2 bg-gradient-to-r from-purple-500 to-orange-500 text-white rounded-full font-semibold hover:shadow-lg hover:shadow-purple-500/50 transition-all duration-300">
                                عرض التفاصيل
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Order Items Preview -->
                <div class="p-6">
                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                        @foreach($order->orderItems->take(3) as $item)
                        <div class="flex items-center gap-3 p-3 bg-[#0F0F0F] rounded-xl">
                            <div class="w-16 h-16 rounded-lg overflow-hidden bg-[#1A1A1A] flex-shrink-0">
                                @if($item->product->image)
                                    <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-purple-500/20 to-orange-500/20"></div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-white font-semibold truncate">{{ $item->product->name }}</div>
                                <div class="text-sm text-gray-400">{{ $item->quantity }}x {{ number_format($item->price, 0) }} $</div>
                            </div>
                        </div>
                        @endforeach

                        @if($order->orderItems->count() > 3)
                        <div class="flex items-center justify-center p-3 bg-[#0F0F0F] rounded-xl text-gray-400">
                            +{{ $order->orderItems->count() - 3 }} منتج آخر
                        </div>
                        @endif
                    </div>

                    <div class="flex justify-between items-center pt-4 border-t border-purple-500/20">
                        <span class="text-gray-400">الإجمالي:</span>
                        <span class="text-2xl font-black text-white">{{ number_format($order->total_amount, 0) }} $</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8 flex justify-center">
            {{ $orders->links() }}
        </div>
        @else
        <!-- No Orders -->
        <div class="text-center py-20">
            <div class="w-32 h-32 bg-[#1A1A1A] rounded-full flex items-center justify-center mx-auto mb-8">
                <svg class="w-16 h-16 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-white mb-4">لا توجد طلبات بعد</h2>
            <p class="text-gray-400 text-lg mb-8">ابدأ التسوق الآن واستمتع بأفضل العروض</p>
            <a href="{{ route('products.index') }}"
               class="inline-flex items-center gap-2 px-8 py-4 bg-gradient-to-r from-purple-500 to-orange-500 text-white rounded-full font-bold text-lg hover:shadow-2xl hover:shadow-purple-500/50 transition-all duration-300">
                تصفح المنتجات
            </a>
        </div>
        @endif
    </div>
</div>
@endsection

