@extends('layouts.app')

@section('title', 'Zoho OAuth Callback')

@section('content')
<div class="min-h-screen bg-[#0F0F0F] py-12">
    <div class="container mx-auto px-4 max-w-3xl">
        <div class="bg-[#1A1A1A] rounded-2xl border border-purple-500/20 p-8">
            @if($success)
                <!-- Success State -->
                <div class="text-center mb-8">
                    <div class="w-20 h-20 bg-green-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <h1 class="text-3xl font-black text-white mb-2">تم بنجاح!</h1>
                    <p class="text-gray-400">
                        @if($saved)
                            تم الحصول على Refresh Token وحفظه تلقائياً في ملف .env
                        @else
                            تم الحصول على Refresh Token
                        @endif
                    </p>
                </div>

                @if($saved)
                <div class="bg-green-500/10 border border-green-500/20 rounded-xl p-6 mb-6">
                    <div class="flex items-center gap-3 mb-2">
                        <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h3 class="text-lg font-bold text-green-400">تم الحفظ تلقائياً!</h3>
                    </div>
                    <p class="text-gray-300 text-sm">
                        تم حفظ Refresh Token في ملف <code class="bg-[#0F0F0F] px-2 py-1 rounded text-green-400">.env</code> تلقائياً.
                        يمكنك الآن استخدام Zoho Books API مباشرة!
                    </p>
                </div>
                @endif

                @if($refresh_token)
                <div class="bg-[#0F0F0F] rounded-xl border border-green-500/20 p-6 mb-6">
                    <h2 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                        Refresh Token
                    </h2>
                    <div class="bg-[#1A1A1A] rounded-lg p-4 mb-4">
                        <code class="text-green-400 text-sm break-all select-all">{{ $refresh_token }}</code>
                    </div>
                    @if(!$saved)
                    <button 
                        onclick="copyToClipboard('{{ $refresh_token }}')" 
                        class="w-full bg-gradient-to-r from-purple-500 to-orange-500 text-white py-3 rounded-xl font-bold hover:shadow-2xl hover:shadow-purple-500/50 transition-all"
                    >
                        <svg class="w-5 h-5 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        نسخ Refresh Token
                    </button>
                    @endif
                </div>
                @endif

                @if(!$saved && $refresh_token)
                <div class="bg-yellow-500/10 border border-yellow-500/20 rounded-xl p-6 mb-6">
                    <h3 class="text-lg font-bold text-yellow-400 mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        خطوات يدوية (إذا فشل الحفظ التلقائي)
                    </h3>
                    <ol class="list-decimal list-inside space-y-2 text-gray-300 text-sm">
                        <li>انسخ <strong class="text-white">Refresh Token</strong> أعلاه</li>
                        <li>افتح ملف <code class="bg-[#0F0F0F] px-2 py-1 rounded text-purple-400">.env</code></li>
                        <li>أضف أو حدث السطر: <code class="bg-[#0F0F0F] px-2 py-1 rounded text-purple-400">ZOHO_REFRESH_TOKEN=your_refresh_token_here</code></li>
                        <li>احفظ الملف وامسح الـ cache: <code class="bg-[#0F0F0F] px-2 py-1 rounded text-purple-400">php artisan config:clear</code></li>
                    </ol>
                </div>
                @endif

            @else
                <!-- Error State -->
                <div class="text-center mb-8">
                    <div class="w-20 h-20 bg-red-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <h1 class="text-3xl font-black text-white mb-2">حدث خطأ</h1>
                    <p class="text-gray-400">فشل الحصول على Refresh Token من Zoho</p>
                </div>

                <div class="bg-[#0F0F0F] rounded-xl border border-red-500/20 p-6 mb-6">
                    <h2 class="text-xl font-bold text-red-400 mb-4">تفاصيل الخطأ</h2>
                    <div class="space-y-3">
                        <div>
                            <p class="text-gray-400 text-sm mb-1">نوع الخطأ:</p>
                            <p class="text-white font-semibold">{{ $error ?? 'Unknown' }}</p>
                        </div>
                        @if($error_description)
                        <div>
                            <p class="text-gray-400 text-sm mb-1">الوصف:</p>
                            <p class="text-white">{{ $error_description }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="bg-yellow-500/10 border border-yellow-500/20 rounded-xl p-6 mb-6">
                    <h3 class="text-lg font-bold text-yellow-400 mb-3">حلول محتملة</h3>
                    <ul class="list-disc list-inside space-y-2 text-gray-300 text-sm">
                        @if($error === 'invalid_code')
                            <li>Authorization Code تم استخدامه مسبقاً - احصل على code جديد</li>
                            <li>تأكد من أن Redirect URI في Zoho App مطابق للـ URI المستخدم</li>
                        @elseif($error === 'invalid_client')
                            <li>تأكد من صحة ZOHO_CLIENT_ID و ZOHO_CLIENT_SECRET في ملف .env</li>
                        @elseif($error === 'missing_config')
                            <li>أضف ZOHO_CLIENT_ID و ZOHO_CLIENT_SECRET في ملف .env</li>
                        @elseif($error === 'no_refresh_token')
                            <li>تأكد من استخدام <code class="bg-[#0F0F0F] px-2 py-1 rounded text-purple-400">access_type=offline</code> و <code class="bg-[#0F0F0F] px-2 py-1 rounded text-purple-400">prompt=consent</code></li>
                        @else
                            <li>تحقق من إعدادات Zoho App في Developer Console</li>
                            <li>تأكد من أن Redirect URI صحيح</li>
                        @endif
                    </ul>
                </div>
            @endif

            <div class="flex gap-4 mt-8">
                <a href="{{ route('zoho.setup') }}" class="flex-1 bg-[#0F0F0F] border border-purple-500/20 text-white py-3 rounded-xl font-bold text-center hover:border-purple-500 transition-all">
                    المحاولة مرة أخرى
                </a>
                <a href="{{ route('home') }}" class="flex-1 bg-gradient-to-r from-purple-500 to-orange-500 text-white py-3 rounded-xl font-bold text-center hover:shadow-2xl hover:shadow-purple-500/50 transition-all">
                    العودة للرئيسية
                </a>
            </div>
        </div>
    </div>
</div>

@if($refresh_token && !$saved)
<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        const button = event.target.closest('button');
        const originalText = button.innerHTML;
        button.innerHTML = '<svg class="w-5 h-5 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> تم النسخ!';
        button.classList.add('bg-green-500');
        button.classList.remove('bg-gradient-to-r', 'from-purple-500', 'to-orange-500');
        
        setTimeout(() => {
            button.innerHTML = originalText;
            button.classList.remove('bg-green-500');
            button.classList.add('bg-gradient-to-r', 'from-purple-500', 'to-orange-500');
        }, 2000);
    }).catch(function(err) {
        alert('فشل النسخ: ' + err);
    });
}
</script>
@endif
@endsection


