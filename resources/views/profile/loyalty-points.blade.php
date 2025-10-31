@extends('layouts.app')

@section('title', 'نقاط الولاء')

@section('content')
<div class="min-h-screen bg-[#0F0F0F] py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <a href="{{ route('profile.index') }}" class="inline-flex items-center gap-2 text-purple-400 hover:text-orange-400 transition-colors mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    العودة للملف الشخصي
                </a>
                <h1 class="text-4xl font-black text-white mb-2">
                    <span class="bg-gradient-to-r from-purple-500 to-orange-500 bg-clip-text text-transparent">نقاط الولاء</span>
                </h1>
                <p class="text-gray-400">تتبع نقاطك واستخدمها للحصول على خصومات</p>
            </div>

            <!-- Total Points Card -->
            <div class="bg-gradient-to-br from-purple-500 to-orange-500 rounded-2xl p-8 mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/80 mb-2">إجمالي النقاط المتاحة</p>
                        <h2 class="text-6xl font-black text-white mb-4">{{ $totalPoints }}</h2>
                        <p class="text-white/80">يمكنك استخدام نقاطك للحصول على خصومات على مشترياتك</p>
                    </div>
                    <div class="hidden md:block">
                        <svg class="w-32 h-32 text-white/20" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Points History -->
            <div class="bg-[#1A1A1A] rounded-2xl border border-purple-500/20 p-6">
                <h3 class="text-2xl font-bold text-white mb-6">سجل النقاط</h3>

                @if($loyaltyPoints->count() > 0)
                <div class="space-y-4">
                    @foreach($loyaltyPoints as $point)
                    <div class="flex items-center gap-4 p-4 bg-[#0F0F0F] rounded-xl border border-purple-500/10">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-r
                            @if($point->type === 'earned') from-green-500 to-green-600
                            @elseif($point->type === 'redeemed') from-red-500 to-red-600
                            @elseif($point->type === 'expired') from-gray-500 to-gray-600
                            @else from-purple-500 to-orange-500
                            @endif
                            flex items-center justify-center flex-shrink-0">
                            @if($point->type === 'earned')
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            @elseif($point->type === 'redeemed')
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                </svg>
                            @else
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <h4 class="font-bold text-white">{{ $point->source }}</h4>
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                                    @if($point->type === 'earned') bg-green-500/20 text-green-400
                                    @elseif($point->type === 'redeemed') bg-red-500/20 text-red-400
                                    @elseif($point->type === 'expired') bg-gray-500/20 text-gray-400
                                    @else bg-purple-500/20 text-purple-400
                                    @endif">
                                    @if($point->type === 'earned') مكتسب
                                    @elseif($point->type === 'redeemed') مستخدم
                                    @elseif($point->type === 'expired') منتهي
                                    @else مكافأة
                                    @endif
                                </span>
                            </div>
                            @if($point->description)
                                <p class="text-sm text-gray-400">{{ $point->description }}</p>
                            @endif
                            <p class="text-xs text-gray-500 mt-1">{{ $point->created_at->format('d/m/Y - h:i A') }}</p>
                            @if($point->expires_at)
                                <p class="text-xs text-orange-400 mt-1">
                                    <svg class="w-3 h-3 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    ينتهي في: {{ $point->expires_at->format('d/m/Y') }}
                                </p>
                            @endif
                        </div>

                        <div class="text-left">
                            <p class="text-2xl font-black
                                @if($point->type === 'earned' || $point->type === 'bonus') text-green-400
                                @else text-red-400
                                @endif">
                                {{ $point->type === 'redeemed' ? '-' : '+' }}{{ $point->points }}
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $loyaltyPoints->links() }}
                </div>
                @else
                <div class="text-center py-12 text-gray-400">
                    <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    <p>لا توجد نقاط بعد</p>
                    <p class="text-sm mt-2">ابدأ بالتسوق لتكسب نقاط الولاء</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

