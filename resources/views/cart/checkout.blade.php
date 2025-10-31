@extends('layouts.app')

@section('title', 'إتمام الشراء')

@section('content')
<div class="min-h-screen bg-[#0F0F0F] py-12">
    <div class="container mx-auto px-4 max-w-6xl">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-black text-white mb-2">
                <span class="bg-gradient-to-r from-purple-500 to-orange-500 bg-clip-text text-transparent">إتمام الشراء</span>
            </h1>
            <p class="text-gray-400">أدخل بياناتك لإتمام عملية الشراء</p>
        </div>

        <form action="{{ route('checkout.process') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Checkout Form -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Customer Information -->
                    <div class="bg-[#1A1A1A] rounded-2xl border border-purple-500/20 p-6">
                        <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-2">
                            <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            معلومات العميل
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-300 mb-2">الاسم الأول *</label>
                                <input type="text" name="first_name" required
                                       value="{{ old('first_name', Auth::user()->first_name ?? '') }}"
                                       class="w-full px-4 py-3 bg-[#0F0F0F] border border-purple-500/20 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-purple-500 transition-colors">
                                @error('first_name')
                                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-300 mb-2">الاسم الأخير *</label>
                                <input type="text" name="last_name" required
                                       value="{{ old('last_name', Auth::user()->last_name ?? '') }}"
                                       class="w-full px-4 py-3 bg-[#0F0F0F] border border-purple-500/20 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-purple-500 transition-colors">
                                @error('last_name')
                                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-300 mb-2">البريد الإلكتروني *</label>
                                <input type="email" name="email" required
                                       value="{{ old('email', Auth::user()->email ?? '') }}"
                                       class="w-full px-4 py-3 bg-[#0F0F0F] border border-purple-500/20 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-purple-500 transition-colors">
                                @error('email')
                                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-300 mb-2">رقم الهاتف *</label>
                                <input type="tel" name="phone" required
                                       value="{{ old('phone', Auth::user()->phone ?? '') }}"
                                       class="w-full px-4 py-3 bg-[#0F0F0F] border border-purple-500/20 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-purple-500 transition-colors">
                                @error('phone')
                                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Billing Address (Optional) -->
                    <div class="bg-[#1A1A1A] rounded-2xl border border-purple-500/20 p-6">
                        <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-2">
                            <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            عنوان الفوترة (اختياري)
                        </h2>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-300 mb-2">العنوان</label>
                                <input type="text" name="address"
                                       value="{{ old('address', Auth::user()->address ?? '') }}"
                                       class="w-full px-4 py-3 bg-[#0F0F0F] border border-purple-500/20 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-purple-500 transition-colors">
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-300 mb-2">المدينة</label>
                                    <input type="text" name="city"
                                           value="{{ old('city', Auth::user()->city ?? '') }}"
                                           class="w-full px-4 py-3 bg-[#0F0F0F] border border-purple-500/20 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-purple-500 transition-colors">
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-300 mb-2">الرمز البريدي</label>
                                    <input type="text" name="postal_code"
                                           value="{{ old('postal_code', Auth::user()->postal_code ?? '') }}"
                                           class="w-full px-4 py-3 bg-[#0F0F0F] border border-purple-500/20 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-purple-500 transition-colors">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="bg-[#1A1A1A] rounded-2xl border border-purple-500/20 p-6">
                        <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-2">
                            <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                            طريقة الدفع *
                        </h2>

                        <div class="space-y-3">
                            <label class="flex items-center p-4 bg-[#0F0F0F] border-2 border-purple-500/20 rounded-xl cursor-pointer hover:border-purple-500 transition-all">
                                <input type="radio" name="payment_method" value="amwalpay" checked class="w-5 h-5 text-purple-500">
                                <div class="mr-3 flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-white">AmwalPay - بوابة الدفع</span>
                                        <div class="flex gap-1">
                                            <div class="w-8 h-5 bg-blue-600 rounded text-white text-[8px] flex items-center justify-center font-bold">VISA</div>
                                            <div class="w-8 h-5 bg-red-600 rounded text-white text-[8px] flex items-center justify-center font-bold">MC</div>
                                            <div class="w-8 h-5 bg-orange-600 rounded text-white text-[8px] flex items-center justify-center font-bold">AM</div>
                                        </div>
                                    </div>
                                    <p class="text-sm text-gray-400">الدفع الآمن عبر AmwalPay (بطاقة ائتمان / مدى / ميزة)</p>
                                </div>
                            </label>

                            {{-- <label class="flex items-center p-4 bg-[#0F0F0F] border-2 border-purple-500/20 rounded-xl cursor-pointer hover:border-purple-500 transition-all">
                                <input type="radio" name="payment_method" value="credit_card" class="w-5 h-5 text-purple-500">
                                <div class="mr-3 flex-1">
                                    <span class="font-bold text-white">بطاقة ائتمان (يدوي)</span>
                                    <p class="text-sm text-gray-400">دفع يدوي - سيتم إرسال الأكواد مباشرة</p>
                                </div>
                            </label>

                            <label class="flex items-center p-4 bg-[#0F0F0F] border-2 border-purple-500/20 rounded-xl cursor-pointer hover:border-purple-500 transition-all">
                                <input type="radio" name="payment_method" value="paypal" class="w-5 h-5 text-purple-500">
                                <div class="mr-3 flex-1">
                                    <span class="font-bold text-white">PayPal (يدوي)</span>
                                    <p class="text-sm text-gray-400">دفع يدوي عبر PayPal</p>
                                </div>
                            </label>

                            <label class="flex items-center p-4 bg-[#0F0F0F] border-2 border-purple-500/20 rounded-xl cursor-pointer hover:border-purple-500 transition-all">
                                <input type="radio" name="payment_method" value="bank_transfer" class="w-5 h-5 text-purple-500">
                                <div class="mr-3 flex-1">
                                    <span class="font-bold text-white">تحويل بنكي (يدوي)</span>
                                    <p class="text-sm text-gray-400">الدفع عبر التحويل البنكي</p>
                                </div>
                            </label> --}}
                        </div>
                        @error('payment_method')
                            <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div class="bg-[#1A1A1A] rounded-2xl border border-purple-500/20 p-6">
                        <h2 class="text-2xl font-bold text-white mb-4">ملاحظات إضافية</h2>
                        <textarea name="notes" rows="4" placeholder="أي ملاحظات أو طلبات خاصة..."
                                  class="w-full px-4 py-3 bg-[#0F0F0F] border border-purple-500/20 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-purple-500 transition-colors resize-none">{{ old('notes') }}</textarea>
                    </div>

                    <!-- Terms & Conditions -->
                    <div class="bg-[#1A1A1A] rounded-2xl border border-purple-500/20 p-6">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" name="terms" required class="w-5 h-5 mt-1 text-purple-500 bg-[#0F0F0F] border-purple-500/20 rounded focus:ring-purple-500">
                            <div class="flex-1">
                                <span class="text-white">
                                    أوافق على
                                    <a href="{{ route('terms') }}" target="_blank" class="text-purple-400 hover:text-orange-400">الشروط والأحكام</a>
                                    و
                                    <a href="{{ route('privacy') }}" target="_blank" class="text-purple-400 hover:text-orange-400">سياسة الخصوصية</a>
                                    و
                                    <a href="{{ route('refund') }}" target="_blank" class="text-purple-400 hover:text-orange-400">سياسة الاسترجاع</a>
                                </span>
                                @error('terms')
                                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Order Summary (Sticky) -->
                <div class="lg:col-span-1">
                    <div class="bg-[#1A1A1A] rounded-2xl border border-purple-500/20 p-6 sticky top-24">
                        <h3 class="text-2xl font-bold text-white mb-6">ملخص الطلب</h3>

                        <!-- Products -->
                        <div class="space-y-3 mb-6 max-h-60 overflow-y-auto">
                            @foreach($cart->items as $item)
                            <div class="flex gap-3 pb-3 border-b border-purple-500/10">
                                <div class="w-16 h-16 rounded-lg overflow-hidden bg-[#0F0F0F] flex-shrink-0">
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
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-semibold text-white truncate">{{ $item->product->name }}</h4>
                                    <p class="text-xs text-gray-400">الكمية: {{ $item->quantity }}</p>
                                    <p class="text-sm font-bold text-purple-400">{{ number_format($item->total_price, 2) }} $</p>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Summary -->
                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between text-gray-300">
                                <span>المجموع الفرعي</span>
                                <span class="font-semibold">{{ number_format($subtotal, 2) }} $</span>
                            </div>
                            <div class="flex justify-between text-gray-300">
                                <span>الضريبة (14%)</span>
                                <span class="font-semibold">{{ number_format($tax, 2) }} $</span>
                            </div>
                            @if($discount > 0)
                            <div class="flex justify-between text-green-400">
                                <span>الخصم</span>
                                <span class="font-semibold">- {{ number_format($discount, 2) }} $</span>
                            </div>
                            @endif
                            <div class="flex justify-between text-gray-300">
                                <span>الشحن</span>
                                <span class="font-semibold text-green-400">مجاني</span>
                            </div>
                        </div>

                        <div class="border-t border-purple-500/20 pt-4 mb-6">
                            <div class="flex justify-between items-center">
                                <span class="text-xl font-bold text-white">الإجمالي</span>
                                <span class="text-3xl font-black bg-gradient-to-r from-purple-500 to-orange-500 bg-clip-text text-transparent">
                                    {{ number_format($total, 2) }} $
                                </span>
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-gradient-to-r from-purple-500 to-orange-500 text-white py-4 rounded-xl font-bold hover:shadow-2xl hover:shadow-purple-500/50 transition-all duration-300 hover:scale-105 mb-3">
                            تأكيد الطلب
                        </button>

                        <a href="{{ route('cart.index') }}" class="block w-full text-center bg-[#0F0F0F] border border-purple-500/20 text-white py-3 rounded-xl font-semibold hover:border-purple-500 transition-all">
                            العودة للسلة
                        </a>

                        <!-- Security Badge -->
                        <div class="mt-6 pt-6 border-t border-purple-500/20 text-center">
                            <div class="flex items-center justify-center gap-2 text-gray-400 text-sm">
                                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                                <span>دفع آمن ومشفر 256-bit SSL</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

