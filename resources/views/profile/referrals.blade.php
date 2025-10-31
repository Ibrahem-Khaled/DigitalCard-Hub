@extends('layouts.app')

@section('title', 'الإحالات')

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
                    <span class="bg-gradient-to-r from-purple-500 to-orange-500 bg-clip-text text-transparent">برنامج الإحالة</span>
                </h1>
                <p class="text-gray-400">شارك كود الإحالة واحصل على مكافآت</p>
            </div>

            <!-- Referral Code Card -->
            <div class="bg-gradient-to-br from-purple-500 to-orange-500 rounded-2xl p-8 mb-8">
                <div class="flex flex-col md:flex-row items-center gap-6">
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold text-white mb-2">كود الإحالة الخاص بك</h2>
                        <p class="text-white/80 mb-4">شارك هذا الكود مع أصدقائك واحصل على مكافآت عند تسجيلهم</p>
                        <div class="flex gap-3">
                            <input type="text"
                                   id="referral-code-input"
                                   value="{{ $referralCode }}"
                                   readonly
                                   class="flex-1 px-4 py-3 bg-white/20 backdrop-blur-lg border border-white/30 rounded-xl text-white font-bold text-lg focus:outline-none">
                            <button onclick="copyReferralCode()"
                                    class="px-6 py-3 bg-white/20 backdrop-blur-lg border border-white/30 rounded-xl text-white hover:bg-white/30 transition-all">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="hidden md:block">
                        <svg class="w-32 h-32 text-white/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-[#1A1A1A] rounded-2xl border border-purple-500/20 p-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-green-500/20 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-gray-400 text-sm">إجمالي الإحالات</p>
                            <p class="text-2xl font-black text-white">{{ $referrals->total() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-[#1A1A1A] rounded-2xl border border-orange-500/20 p-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-orange-500/20 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-gray-400 text-sm">المكافآت المكتسبة</p>
                            <p class="text-2xl font-black text-white">0</p>
                        </div>
                    </div>
                </div>

                <div class="bg-[#1A1A1A] rounded-2xl border border-purple-500/20 p-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-purple-500/20 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-gray-400 text-sm">إحالات نشطة</p>
                            <p class="text-2xl font-black text-white">{{ $referrals->where('status', 'active')->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Referrals List -->
            <div class="bg-[#1A1A1A] rounded-2xl border border-purple-500/20 p-6">
                <h3 class="text-2xl font-bold text-white mb-6">الإحالات</h3>

                @if($referrals->count() > 0)
                <div class="space-y-4">
                    @foreach($referrals as $referral)
                    <div class="flex items-center gap-4 p-4 bg-[#0F0F0F] rounded-xl border border-purple-500/10">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-r from-purple-500 to-orange-500 flex items-center justify-center text-white font-bold flex-shrink-0">
                            {{ mb_substr($referral->referred->first_name ?? 'م', 0, 1) }}{{ mb_substr($referral->referred->last_name ?? 'س', 0, 1) }}
                        </div>

                        <div class="flex-1 min-w-0">
                            <h4 class="font-bold text-white">{{ $referral->referred->full_name ?? 'مستخدم' }}</h4>
                            <p class="text-sm text-gray-400">{{ $referral->referred->email ?? '' }}</p>
                            <p class="text-xs text-gray-500 mt-1">انضم في: {{ $referral->created_at->format('d/m/Y') }}</p>
                        </div>

                        <div class="text-left">
                            <span class="px-3 py-1 rounded-full text-xs font-bold
                                @if($referral->status === 'active') bg-green-500/20 text-green-400
                                @elseif($referral->status === 'pending') bg-yellow-500/20 text-yellow-400
                                @else bg-gray-500/20 text-gray-400
                                @endif">
                                @if($referral->status === 'active') نشط
                                @elseif($referral->status === 'pending') قيد الانتظار
                                @else غير نشط
                                @endif
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $referrals->links() }}
                </div>
                @else
                <div class="text-center py-12 text-gray-400">
                    <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <p>لا توجد إحالات بعد</p>
                    <p class="text-sm mt-2">شارك كود الإحالة الخاص بك مع أصدقائك</p>
                </div>
                @endif
            </div>

            <!-- How it works -->
            <div class="bg-gradient-to-r from-purple-500/10 to-orange-500/10 rounded-2xl border border-purple-500/20 p-6 mt-8">
                <h3 class="text-xl font-bold text-white mb-4">كيف يعمل برنامج الإحالة؟</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto mb-4 bg-purple-500/20 rounded-full flex items-center justify-center">
                            <span class="text-2xl font-black text-purple-400">1</span>
                        </div>
                        <h4 class="font-bold text-white mb-2">شارك الكود</h4>
                        <p class="text-sm text-gray-400">شارك كود الإحالة الخاص بك مع أصدقائك</p>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto mb-4 bg-orange-500/20 rounded-full flex items-center justify-center">
                            <span class="text-2xl font-black text-orange-400">2</span>
                        </div>
                        <h4 class="font-bold text-white mb-2">التسجيل</h4>
                        <p class="text-sm text-gray-400">عند تسجيل أصدقائك باستخدام كودك</p>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto mb-4 bg-green-500/20 rounded-full flex items-center justify-center">
                            <span class="text-2xl font-black text-green-400">3</span>
                        </div>
                        <h4 class="font-bold text-white mb-2">احصل على المكافآت</h4>
                        <p class="text-sm text-gray-400">ستحصل على نقاط ومكافآت عند كل إحالة</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function copyReferralCode() {
    const codeInput = document.getElementById('referral-code-input');
    codeInput.select();
    document.execCommand('copy');

    // Show notification
    alert('تم نسخ كود الإحالة! شاركه مع أصدقائك');
}
</script>
@endpush
@endsection

