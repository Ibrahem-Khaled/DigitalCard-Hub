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

