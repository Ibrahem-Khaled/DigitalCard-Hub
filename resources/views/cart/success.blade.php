@extends('layouts.app')

@section('title', 'تم إنشاء طلبك بنجاح')

@section('content')
<div class="min-h-screen bg-[#0F0F0F] py-12">
    <div class="container mx-auto px-4 max-w-4xl">
        <!-- Success Message -->
        <div class="text-center mb-12">
            <div class="w-24 h-24 mx-auto mb-6 rounded-full bg-gradient-to-r from-green-500/20 to-green-600/20 flex items-center justify-center">
                <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h1 class="text-4xl font-black text-white mb-4">
                <span class="bg-gradient-to-r from-purple-500 to-orange-500 bg-clip-text text-transparent">تم إنشاء طلبك بنجاح!</span>
            </h1>
            <p class="text-gray-400 text-lg">رقم الطلب: <span class="text-white font-bold">{{ $order->order_number }}</span></p>
        </div>

        <!-- Order Details -->
        <div class="bg-[#1A1A1A] rounded-2xl border border-purple-500/20 p-8 mb-6">
            <h2 class="text-2xl font-bold text-white mb-6">تفاصيل الطلب</h2>

            <!-- Customer Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 pb-8 border-b border-purple-500/10">
                <div>
                    <h3 class="text-sm font-semibold text-gray-400 mb-2">معلومات العميل</h3>
                    @if($order->user)
                        <p class="text-white font-semibold">{{ $order->user->first_name }} {{ $order->user->last_name }}</p>
                        <p class="text-gray-400 text-sm">{{ $order->user->email }}</p>
                        @if($order->user->phone)
                            <p class="text-gray-400 text-sm">{{ $order->user->phone }}</p>
                        @endif
                    @else
                        <p class="text-white font-semibold">{{ $order->billing_address['first_name'] ?? '' }} {{ $order->billing_address['last_name'] ?? '' }}</p>
                        <p class="text-gray-400 text-sm">{{ $order->billing_address['email'] ?? '' }}</p>
                        @if(isset($order->billing_address['phone']))
                            <p class="text-gray-400 text-sm">{{ $order->billing_address['phone'] }}</p>
                        @endif
                        <span class="badge bg-secondary text-xs">عميل ضيف</span>
                    @endif
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-gray-400 mb-2">حالة الطلب</h3>
                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1 rounded-full text-xs font-bold
                            @if($order->status === 'pending') bg-yellow-500/20 text-yellow-400
                            @elseif($order->status === 'processing') bg-blue-500/20 text-blue-400
                            @elseif($order->status === 'completed') bg-green-500/20 text-green-400
                            @else bg-gray-500/20 text-gray-400
                            @endif">
                            @if($order->status === 'pending') قيد الانتظار
                            @elseif($order->status === 'processing') قيد المعالجة
                            @elseif($order->status === 'completed') مكتمل
                            @elseif($order->status === 'cancelled') ملغي
                            @else {{ $order->status }}
                            @endif
                        </span>
                    </div>
                    <p class="text-gray-400 text-sm mt-2">تاريخ الطلب: {{ $order->created_at->format('Y/m/d - h:i A') }}</p>
                </div>
            </div>

            <!-- Order Items -->
            <div class="mb-6">
                <h3 class="text-lg font-bold text-white mb-4">المنتجات</h3>
                <div class="space-y-3">
                    @foreach($order->orderItems as $item)
                    <div class="flex gap-4 p-4 bg-[#0F0F0F] rounded-xl border border-purple-500/10">
                        <div class="w-16 h-16 rounded-lg overflow-hidden bg-[#1A1A1A] flex-shrink-0">
                            @if($item->product->images && count($item->product->images) > 0)
                                <img src="{{ asset('storage/' . $item->product->images[0]) }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <h4 class="font-semibold text-white mb-1">{{ $item->product->name }}</h4>
                            <p class="text-sm text-gray-400">الكمية: {{ $item->quantity }} × {{ number_format($item->price, 2) }} $</p>
                        </div>
                        <div class="text-left">
                            <p class="text-lg font-bold text-white">{{ number_format($item->total_price, 2) }} $</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Order Summary -->
            <div class="border-t border-purple-500/10 pt-6">
                <div class="space-y-3 max-w-sm mr-auto">
                    <div class="flex justify-between text-gray-300">
                        <span>المجموع الفرعي</span>
                        <span class="font-semibold">{{ formatPrice($order->subtotal) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-300">
                        <span>الضريبة</span>
                        <span class="font-semibold">{{ formatPrice($order->tax_amount) }}</span>
                    </div>
                    @if($order->discount_amount > 0)
                    <div class="flex justify-between text-green-400">
                        <span>الخصم</span>
                        <span class="font-semibold">- {{ formatPrice($order->discount_amount) }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between text-gray-300">
                        <span>الشحن</span>
                        <span class="font-semibold text-green-400">مجاني</span>
                    </div>
                    <div class="border-t border-purple-500/10 pt-3 flex justify-between items-center">
                        <span class="text-xl font-bold text-white">الإجمالي</span>
                        <span class="text-2xl font-black bg-gradient-to-r from-purple-500 to-orange-500 bg-clip-text text-transparent">
                            {{ formatPrice($order->total_amount) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Next Steps -->
        <div class="bg-gradient-to-r from-purple-500/10 to-orange-500/10 rounded-2xl border border-purple-500/20 p-8 mb-6">
            <h3 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                ماذا بعد؟
            </h3>
            <ul class="space-y-3 text-gray-300">
                <li class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>تم إرسال تأكيد الطلب إلى بريدك الإلكتروني <strong class="text-white">{{ $order->user ? $order->user->email : ($order->billing_address['email'] ?? '') }}</strong></span>
                </li>
                <li class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>تم إرسال البطاقات الرقمية إلى بريدك الإلكتروني فوراً! تحقق من صندوق الوارد</span>
                </li>
                <li class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>يمكنك متابعة حالة طلبك من خلال <a href="{{ route('profile.orders') }}" class="text-purple-400 hover:text-orange-400 underline">صفحة طلباتي</a></span>
                </li>
                <li class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>في حالة وجود أي استفسار، يمكنك <a href="{{ route('contact') }}" class="text-purple-400 hover:text-orange-400 underline">التواصل معنا</a></span>
                </li>
            </ul>
        </div>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row gap-4">
            @auth
            <a href="{{ route('profile.orders') }}" class="flex-1 bg-gradient-to-r from-purple-500 to-orange-500 text-white py-4 rounded-xl font-bold text-center hover:shadow-2xl hover:shadow-purple-500/50 transition-all duration-300 hover:scale-105">
                عرض طلباتي
            </a>
            @endauth
            <a href="{{ route('products.index') }}" class="flex-1 bg-[#1A1A1A] border border-purple-500/20 text-white py-4 rounded-xl font-bold text-center hover:border-purple-500 transition-all">
                متابعة التسوق
            </a>
            <a href="{{ route('home') }}" class="flex-1 bg-[#1A1A1A] border border-purple-500/20 text-white py-4 rounded-xl font-bold text-center hover:border-purple-500 transition-all">
                العودة للرئيسية
            </a>
        </div>
    </div>
</div>
@endsection

