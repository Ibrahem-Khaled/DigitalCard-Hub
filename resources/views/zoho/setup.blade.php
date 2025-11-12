@extends('layouts.app')

@section('title', 'إعداد Zoho OAuth')

@section('content')
<div class="min-h-screen bg-[#0F0F0F] py-12">
    <div class="container mx-auto px-4 max-w-3xl">
        <div class="bg-[#1A1A1A] rounded-2xl border border-purple-500/20 p-8">
            <div class="text-center mb-8">
                <div class="w-20 h-20 bg-purple-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <h1 class="text-3xl font-black text-white mb-2">إعداد Zoho OAuth</h1>
                <p class="text-gray-400">احصل على Refresh Token تلقائياً</p>
            </div>

            @if($error)
                <div class="bg-red-500/10 border border-red-500/20 rounded-xl p-6 mb-6">
                    <div class="flex items-center gap-3 mb-2">
                        <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h3 class="text-lg font-bold text-red-400">خطأ في الإعدادات</h3>
                    </div>
                    <p class="text-gray-300">{{ $error }}</p>
                    <div class="mt-4 bg-[#0F0F0F] rounded-lg p-4">
                        <p class="text-gray-400 text-sm mb-2">تأكد من إضافة هذه المتغيرات في ملف <code class="text-purple-400">.env</code>:</p>
                        <code class="text-green-400 text-sm block">
                            ZOHO_CLIENT_ID=your_client_id<br>
                            ZOHO_CLIENT_SECRET=your_client_secret
                        </code>
                    </div>
                </div>
            @else
                <div class="bg-[#0F0F0F] rounded-xl border border-purple-500/20 p-6 mb-6">
                    <h2 class="text-xl font-bold text-white mb-4">معلومات الإعداد</h2>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-400">Client ID:</span>
                            <code class="text-purple-400">{{ $clientId }}</code>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Redirect URI:</span>
                            <code class="text-purple-400 text-xs break-all">{{ $redirectUri }}</code>
                        </div>
                    </div>
                </div>

                <div class="bg-yellow-500/10 border border-yellow-500/20 rounded-xl p-6 mb-6">
                    <h3 class="text-lg font-bold text-yellow-400 mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        خطوات مهمة قبل البدء
                    </h3>
                    <ol class="list-decimal list-inside space-y-2 text-gray-300 text-sm">
                        <li>تأكد من إضافة <code class="bg-[#0F0F0F] px-2 py-1 rounded text-purple-400">{{ $redirectUri }}</code> في <strong>Authorized Redirect URIs</strong> في Zoho Developer Console</li>
                        <li>تأكد من أن Client ID و Client Secret صحيحين في ملف <code class="bg-[#0F0F0F] px-2 py-1 rounded text-purple-400">.env</code></li>
                        <li>بعد الضغط على الزر أدناه، ستحتاج إلى تسجيل الدخول والموافقة على الصلاحيات</li>
                        <li><strong class="text-white">سيتم حفظ Refresh Token تلقائياً في ملف .env</strong></li>
                    </ol>
                </div>

                <a 
                    href="{{ $authUrl }}" 
                    target="_blank"
                    class="block w-full bg-gradient-to-r from-purple-500 to-orange-500 text-white py-4 rounded-xl font-bold text-center hover:shadow-2xl hover:shadow-purple-500/50 transition-all mb-4"
                >
                    <svg class="w-6 h-6 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    اذهب إلى Zoho للموافقة
                </a>

                <div class="bg-blue-500/10 border border-blue-500/20 rounded-xl p-6">
                    <h3 class="text-lg font-bold text-blue-400 mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        ماذا سيحدث بعد الموافقة؟
                    </h3>
                    <p class="text-gray-300 text-sm">
                        بعد الموافقة على الصلاحيات في Zoho، سيتم توجيهك تلقائياً إلى صفحة Callback التي ستعرض لك Refresh Token. 
                        <strong class="text-white">سيتم حفظ Refresh Token تلقائياً في ملف .env</strong> - لا حاجة للنسخ واللصق!
                    </p>
                </div>
            @endif

            <div class="flex gap-4 mt-8">
                <a href="{{ route('home') }}" class="flex-1 bg-[#0F0F0F] border border-purple-500/20 text-white py-3 rounded-xl font-bold text-center hover:border-purple-500 transition-all">
                    العودة للرئيسية
                </a>
            </div>
        </div>
    </div>
</div>
@endsection


