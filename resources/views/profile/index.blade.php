@extends('layouts.app')

@section('title', 'الملف الشخصي - متجر البطاقات الرقمية')

@section('content')
<div class="min-h-screen bg-[#0F0F0F] py-12">
    <div class="container mx-auto px-4">
        <!-- Profile Header -->
        <div class="bg-gradient-to-r from-purple-500 to-orange-500 rounded-3xl p-8 mb-8">
            <div class="flex flex-col md:flex-row items-center gap-6">
                <!-- Avatar -->
                <div class="w-32 h-32 rounded-full bg-white flex items-center justify-center text-6xl font-black text-purple-500 shadow-2xl">
                    {{ $user->display_name }}
                </div>

                <!-- User Info -->
                <div class="flex-1 text-center md:text-right">
                    <h1 class="text-4xl font-black text-white mb-2">{{ $user->full_name }}</h1>
                    <p class="text-white/80 text-lg mb-4">{{ $user->email }}</p>
                    <div class="flex flex-wrap gap-3 justify-center md:justify-start">
                        <a href="{{ route('profile.edit') }}"
                           class="px-6 py-2 bg-white/20 backdrop-blur-lg border border-white/30 text-white rounded-full font-semibold hover:bg-white/30 transition-all duration-300">
                            تعديل الملف الشخصي
                        </a>
                        <a href="{{ route('password.change') }}"
                           class="px-6 py-2 bg-white/20 backdrop-blur-lg border border-white/30 text-white rounded-full font-semibold hover:bg-white/30 transition-all duration-300">
                            تغيير كلمة المرور
                        </a>
                    </div>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-3 gap-6 text-center">
                    <div>
                        <div class="text-3xl font-black text-white mb-1">{{ $ordersStats['total'] }}</div>
                        <div class="text-white/80 text-sm">طلب</div>
                    </div>
                    <div>
                        <div class="text-3xl font-black text-white mb-1">{{ $totalPoints }}</div>
                        <div class="text-white/80 text-sm">نقطة</div>
                    </div>
                    <div>
                        <div class="text-3xl font-black text-white mb-1">{{ $user->referrals->count() }}</div>
                        <div class="text-white/80 text-sm">إحالة</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-[#1A1A1A] rounded-2xl border border-purple-500/20 p-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-purple-500/20 rounded-xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-gray-400 text-sm">الطلبات قيد التنفيذ</div>
                        <div class="text-2xl font-black text-white">{{ $ordersStats['processing'] }}</div>
                    </div>
                </div>
            </div>

            <div class="bg-[#1A1A1A] rounded-2xl border border-orange-500/20 p-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-orange-500/20 rounded-xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-gray-400 text-sm">طلبات مكتملة</div>
                        <div class="text-2xl font-black text-white">{{ $ordersStats['delivered'] }}</div>
                    </div>
                </div>
            </div>

            <div class="bg-[#1A1A1A] rounded-2xl border border-purple-500/20 p-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-gradient-to-r from-purple-500 to-orange-500 rounded-xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-gray-400 text-sm">نقاط الولاء</div>
                        <div class="text-2xl font-black bg-gradient-to-r from-purple-500 to-orange-500 bg-clip-text text-transparent">
                            {{ $totalPoints }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-[#1A1A1A] rounded-2xl border border-green-500/20 p-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-green-500/20 rounded-xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-gray-400 text-sm">الإحالات</div>
                        <div class="text-2xl font-black text-white">{{ $user->referrals->count() }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Recent Orders -->
            <div class="lg:col-span-2">
                <div class="bg-[#1A1A1A] rounded-2xl border border-purple-500/20 p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-white">أحدث الطلبات</h2>
                        <a href="{{ route('profile.orders') }}"
                           class="text-purple-400 hover:text-orange-400 transition-colors font-semibold">
                            عرض الكل →
                        </a>
                    </div>

                    @if($user->orders->count() > 0)
                    <div class="space-y-4">
                        @foreach($user->orders as $order)
                        <a href="{{ route('profile.order-details', $order->id) }}"
                           class="block p-6 bg-[#0F0F0F] rounded-xl border border-purple-500/20 hover:border-purple-500 transition-all duration-300">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <div class="text-white font-bold text-lg mb-1">طلب #{{ $order->order_number }}</div>
                                    <div class="text-gray-400 text-sm">{{ $order->created_at->format('d/m/Y - h:i A') }}</div>
                                </div>
                                <span class="px-4 py-2 bg-{{ $order->getStatusColor() }}-500/20 border border-{{ $order->getStatusColor() }}-500/30 rounded-full text-{{ $order->getStatusColor() }}-400 text-sm font-semibold">
                                    {{ $order->getStatusInArabic() }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                    </svg>
                                    <span class="text-gray-400 text-sm">{{ $order->getTotalItems() }} منتج</span>
                                </div>
                                <div class="text-2xl font-black text-white">{{ number_format($order->total, 2) }} $</div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-12 text-gray-400">
                        <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p>لا توجد طلبات بعد</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Loyalty Points -->
                <div class="bg-gradient-to-br from-purple-500 to-orange-500 rounded-2xl p-6 text-white">
                    <div class="flex items-center gap-3 mb-4">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        <h3 class="text-xl font-bold">نقاط الولاء</h3>
                    </div>
                    <div class="text-5xl font-black mb-4">{{ $totalPoints }}</div>
                    <p class="text-white/80 mb-4">استخدم نقاطك للحصول على خصومات حصرية</p>
                    <a href="{{ route('profile.loyalty-points') }}"
                       class="block w-full bg-white/20 backdrop-blur-lg border border-white/30 text-center py-3 rounded-xl font-semibold hover:bg-white/30 transition-all duration-300">
                        عرض النقاط
                    </a>
                </div>

                <!-- Referral Code -->
                <div class="bg-[#1A1A1A] rounded-2xl border border-purple-500/20 p-6">
                    <h3 class="text-xl font-bold text-white mb-4">كود الإحالة</h3>
                    <p class="text-gray-400 text-sm mb-4">شارك كود الإحالة مع أصدقائك واحصل على مكافآت</p>
                    <div class="flex gap-2 mb-4">
                        <input type="text"
                               id="referral-code"
                               value="{{ $user->referral_code }}"
                               readonly
                               class="flex-1 bg-[#0F0F0F] border border-purple-500/20 rounded-xl px-4 py-3 text-white focus:outline-none">
                        <button onclick="copyReferralCode()"
                                class="px-4 py-3 bg-purple-500/20 border border-purple-500/30 rounded-xl text-purple-400 hover:bg-purple-500 hover:text-white transition-all duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                        </button>
                    </div>
                    <a href="{{ route('profile.referrals') }}"
                       class="block text-center text-purple-400 hover:text-orange-400 transition-colors font-semibold">
                        عرض الإحالات →
                    </a>
                </div>

                <!-- Account Actions -->
                <div class="bg-[#1A1A1A] rounded-2xl border border-purple-500/20 p-6">
                    <h3 class="text-xl font-bold text-white mb-4">إعدادات الحساب</h3>
                    <div class="space-y-3">
                        <a href="{{ route('profile.edit') }}"
                           class="flex items-center gap-3 text-gray-300 hover:text-purple-400 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            <span>تعديل الملف الشخصي</span>
                        </a>
                        <a href="{{ route('password.change') }}"
                           class="flex items-center gap-3 text-gray-300 hover:text-purple-400 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <span>تغيير كلمة المرور</span>
                        </a>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="flex items-center gap-3 text-red-400 hover:text-red-300 transition-colors w-full">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                <span>تسجيل الخروج</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function copyReferralCode() {
    const codeInput = document.getElementById('referral-code');
    codeInput.select();
    document.execCommand('copy');
    alert('تم نسخ كود الإحالة!');
}
</script>
@endpush
@endsection

