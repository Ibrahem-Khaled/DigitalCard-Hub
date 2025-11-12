@extends('layouts.app')

@section('title', 'تفاصيل الطلب')

@section('content')
<div class="min-h-screen bg-[#0F0F0F] py-12">
    <div class="container mx-auto px-4 max-w-5xl">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('profile.orders') }}" class="inline-flex items-center gap-2 text-purple-400 hover:text-orange-400 transition-colors mb-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                العودة لقائمة الطلبات
            </a>
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-4xl font-black text-white mb-2">
                        طلب <span class="bg-gradient-to-r from-purple-500 to-orange-500 bg-clip-text text-transparent">#{{ $order->order_number }}</span>
                    </h1>
                    <p class="text-gray-400">{{ $order->created_at->format('d/m/Y - h:i A') }}</p>
                </div>
                <span class="px-4 py-2 bg-{{ $order->getStatusColor() }}-500/20 border border-{{ $order->getStatusColor() }}-500/30 rounded-full text-{{ $order->getStatusColor() }}-400 font-semibold">
                    {{ $order->getStatusInArabic() }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Order Items -->
                <div class="bg-[#1A1A1A] rounded-2xl border border-purple-500/20 p-6">
                    <h2 class="text-2xl font-bold text-white mb-6">المنتجات</h2>
                    <div class="space-y-4">
                        @foreach($order->orderItems as $item)
                        <div class="flex gap-4 p-4 bg-[#0F0F0F] rounded-xl border border-purple-500/10">
                            <div class="w-20 h-20 rounded-lg overflow-hidden bg-[#1A1A1A] flex-shrink-0">
                                @if($item->product && $item->product->images && count($item->product->images) > 0)
                                    <img src="{{ asset('storage/' . $item->product->images[0]) }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-10 h-10 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <h3 class="font-bold text-white mb-1">{{ $item->product->name ?? 'منتج' }}</h3>
                                <p class="text-sm text-gray-400 mb-2">الكمية: {{ $item->quantity }}</p>
                                <p class="text-sm text-gray-400">السعر: {{ number_format($item->price, 2) }} $</p>
                            </div>
                            <div class="text-left">
                                <p class="text-xl font-bold text-white">{{ number_format($item->total, 2) }} $</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Payment Info -->
                @if($order->payments->count() > 0)
                <div class="bg-[#1A1A1A] rounded-2xl border border-purple-500/20 p-6">
                    <h2 class="text-2xl font-bold text-white mb-6">معلومات الدفع</h2>
                    @foreach($order->payments as $payment)
                    <div class="flex items-center justify-between p-4 bg-[#0F0F0F] rounded-xl border border-purple-500/10">
                        <div>
                            <p class="font-bold text-white mb-1">طريقة الدفع</p>
                            <p class="text-sm text-gray-400">
                                @if($payment->payment_method === 'credit_card') بطاقة ائتمان
                                @elseif($payment->payment_method === 'paypal') PayPal
                                @elseif($payment->payment_method === 'bank_transfer') تحويل بنكي
                                @else {{ $payment->payment_method }}
                                @endif
                            </p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold
                            @if($payment->status === 'completed') bg-green-500/20 text-green-400
                            @elseif($payment->status === 'pending') bg-yellow-500/20 text-yellow-400
                            @else bg-red-500/20 text-red-400
                            @endif">
                            @if($payment->status === 'completed') مكتمل
                            @elseif($payment->status === 'pending') قيد الانتظار
                            @else فشل
                            @endif
                        </span>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Order Summary -->
                <div class="bg-[#1A1A1A] rounded-2xl border border-purple-500/20 p-6">
                    <h3 class="text-xl font-bold text-white mb-6">ملخص الطلب</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between text-gray-300">
                            <span>المجموع الفرعي</span>
                            <span class="font-semibold">{{ number_format($order->subtotal, 2) }} $</span>
                        </div>
                        <div class="flex justify-between text-gray-300">
                            <span>الضريبة</span>
                            <span class="font-semibold">{{ number_format($order->tax, 2) }} $</span>
                        </div>
                        @if($order->discount_amount > 0)
                        <div class="flex justify-between text-green-400">
                            <span>الخصم</span>
                            <span class="font-semibold">- {{ number_format($order->discount_amount, 2) }} $</span>
                        </div>
                        @endif
                        <div class="flex justify-between text-gray-300">
                            <span>الشحن</span>
                            <span class="font-semibold text-green-400">مجاني</span>
                        </div>
                        <div class="border-t border-purple-500/20 pt-3 flex justify-between items-center">
                            <span class="text-xl font-bold text-white">الإجمالي</span>
                            <span class="text-2xl font-black bg-gradient-to-r from-purple-500 to-orange-500 bg-clip-text text-transparent">
                                {{ number_format($order->total, 2) }} $
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Customer Info -->
                <div class="bg-[#1A1A1A] rounded-2xl border border-purple-500/20 p-6">
                    <h3 class="text-xl font-bold text-white mb-4">معلومات العميل</h3>
                    <div class="space-y-3 text-sm">
                        <div>
                            <p class="text-gray-400 mb-1">الاسم</p>
                            <p class="text-white font-semibold">{{ $order->customer_first_name }} {{ $order->customer_last_name }}</p>
                        </div>
                        <div>
                            <p class="text-gray-400 mb-1">البريد الإلكتروني</p>
                            <p class="text-white">{{ $order->customer_email }}</p>
                        </div>
                        <div>
                            <p class="text-gray-400 mb-1">رقم الهاتف</p>
                            <p class="text-white">{{ $order->customer_phone }}</p>
                        </div>
                        @if($order->customer_address)
                        <div>
                            <p class="text-gray-400 mb-1">العنوان</p>
                            <p class="text-white">{{ $order->customer_address }}</p>
                            @if($order->customer_city)
                                <p class="text-gray-400">{{ $order->customer_city }}</p>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-[#1A1A1A] rounded-2xl border border-purple-500/20 p-6">
                    <h3 class="text-xl font-bold text-white mb-4">إجراءات</h3>
                    <div class="space-y-3">
                        @if($order->payment_status === 'paid')
                        <a href="{{ route('profile.zoho-invoice', $order->id) }}" class="block w-full bg-gradient-to-r from-purple-500 to-orange-500 text-white py-3 rounded-xl font-bold text-center hover:shadow-2xl hover:shadow-purple-500/50 transition-all">
                            <svg class="w-5 h-5 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            تحميل الفاتورة الضريبية
                        </a>
                        @else
                        <button disabled class="w-full bg-gray-600 text-gray-400 py-3 rounded-xl font-bold cursor-not-allowed">
                            الفاتورة متاحة فقط للطلبات المدفوعة
                        </button>
                        @endif
                        <a href="{{ route('contact') }}" class="block w-full bg-[#0F0F0F] border border-purple-500/20 text-white py-3 rounded-xl font-bold text-center hover:border-purple-500 transition-all">
                            تواصل معنا
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

