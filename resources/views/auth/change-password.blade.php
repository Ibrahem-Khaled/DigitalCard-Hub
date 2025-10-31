@extends('layouts.app')

@section('title', 'تغيير كلمة المرور')

@section('content')
<div class="min-h-screen bg-[#0F0F0F] py-12">
    <div class="container mx-auto px-4 max-w-2xl">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('profile.index') }}" class="inline-flex items-center gap-2 text-purple-400 hover:text-orange-400 transition-colors mb-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                العودة للملف الشخصي
            </a>
            <h1 class="text-4xl font-black text-white mb-2">
                <span class="bg-gradient-to-r from-purple-500 to-orange-500 bg-clip-text text-transparent">تغيير كلمة المرور</span>
            </h1>
            <p class="text-gray-400">قم بتحديث كلمة المرور الخاصة بك</p>
        </div>

        <div class="bg-[#1A1A1A] rounded-2xl border border-purple-500/20 p-8">
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-500/10 border border-green-500/20 rounded-xl text-green-400">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.change.update') }}">
                @csrf

                <!-- Current Password -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-300 mb-2">كلمة المرور الحالية</label>
                    <input type="password" name="current_password" required
                           class="w-full px-4 py-3 bg-[#0F0F0F] border border-purple-500/20 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-purple-500 transition-colors"
                           placeholder="أدخل كلمة المرور الحالية">
                    @error('current_password')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- New Password -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-300 mb-2">كلمة المرور الجديدة</label>
                    <input type="password" name="password" required
                           class="w-full px-4 py-3 bg-[#0F0F0F] border border-purple-500/20 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-purple-500 transition-colors"
                           placeholder="أدخل كلمة المرور الجديدة">
                    @error('password')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-gray-400 text-sm mt-2">يجب أن تحتوي كلمة المرور على 8 أحرف على الأقل</p>
                </div>

                <!-- Confirm Password -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-300 mb-2">تأكيد كلمة المرور الجديدة</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full px-4 py-3 bg-[#0F0F0F] border border-purple-500/20 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-purple-500 transition-colors"
                           placeholder="أعد إدخال كلمة المرور الجديدة">
                </div>

                <!-- Security Tips -->
                <div class="mb-6 p-4 bg-purple-500/10 border border-purple-500/20 rounded-xl">
                    <h3 class="text-sm font-bold text-white mb-2 flex items-center gap-2">
                        <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        نصائح لكلمة مرور قوية:
                    </h3>
                    <ul class="text-sm text-gray-400 space-y-1">
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-purple-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            استخدم 8 أحرف على الأقل
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-purple-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            استخدم مزيجاً من الأحرف الكبيرة والصغيرة
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-purple-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            أضف أرقاماً ورموزاً خاصة
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-purple-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            تجنب استخدام معلومات شخصية واضحة
                        </li>
                    </ul>
                </div>

                <!-- Buttons -->
                <div class="flex gap-4">
                    <button type="submit"
                            class="flex-1 bg-gradient-to-r from-purple-500 to-orange-500 text-white py-4 rounded-xl font-bold hover:shadow-2xl hover:shadow-purple-500/50 transition-all duration-300 hover:scale-105">
                        تغيير كلمة المرور
                    </button>
                    <a href="{{ route('profile.index') }}"
                       class="flex-1 bg-[#0F0F0F] border border-purple-500/20 text-white py-4 rounded-xl font-bold text-center hover:border-purple-500 transition-all">
                        إلغاء
                    </a>
                </div>
            </form>
        </div>

        <!-- Security Notice -->
        <div class="mt-8 p-4 bg-orange-500/10 border border-orange-500/20 rounded-xl">
            <div class="flex gap-3">
                <svg class="w-6 h-6 text-orange-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div class="flex-1">
                    <h3 class="text-sm font-bold text-white mb-1">ملاحظة أمنية</h3>
                    <p class="text-sm text-gray-400">بعد تغيير كلمة المرور، ستحتاج إلى تسجيل الدخول مرة أخرى على جميع أجهزتك.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

