@extends('layouts.app')

@section('title', 'إنشاء حساب جديد')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-[#0F0F0F]">
    <div class="max-w-md w-full space-y-8">
        <!-- Header -->
        <div class="text-center">
            <h2 class="text-4xl font-black text-white mb-2">
                انضم <span class="bg-gradient-to-r from-purple-500 to-orange-500 bg-clip-text text-transparent">إلينا</span>
            </h2>
            <p class="text-gray-400">أنشئ حساباً جديداً واستمتع بمميزات حصرية</p>
        </div>

        <!-- Form -->
        <div class="bg-[#1A1A1A] rounded-2xl border border-purple-500/20 p-8">
            @if($errors->any())
                <div class="mb-6 p-4 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400 text-sm">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-6">
                @csrf

                <!-- Name Fields -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="first_name" class="block text-sm font-semibold text-gray-300 mb-2">الاسم الأول</label>
                        <input id="first_name" name="first_name" type="text" required autofocus
                               value="{{ old('first_name') }}"
                               class="w-full px-4 py-3 bg-[#0F0F0F] border border-purple-500/20 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-purple-500 transition-colors"
                               placeholder="أحمد">
                    </div>
                    <div>
                        <label for="last_name" class="block text-sm font-semibold text-gray-300 mb-2">الاسم الأخير</label>
                        <input id="last_name" name="last_name" type="text" required
                               value="{{ old('last_name') }}"
                               class="w-full px-4 py-3 bg-[#0F0F0F] border border-purple-500/20 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-purple-500 transition-colors"
                               placeholder="محمد">
                    </div>
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-300 mb-2">البريد الإلكتروني</label>
                    <input id="email" name="email" type="email" required
                           value="{{ old('email') }}"
                           class="w-full px-4 py-3 bg-[#0F0F0F] border border-purple-500/20 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-purple-500 transition-colors"
                           placeholder="your@email.com">
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-semibold text-gray-300 mb-2">رقم الهاتف (اختياري)</label>
                    <input id="phone" name="phone" type="tel"
                           value="{{ old('phone') }}"
                           class="w-full px-4 py-3 bg-[#0F0F0F] border border-purple-500/20 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-purple-500 transition-colors"
                           placeholder="05xxxxxxxx">
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-300 mb-2">كلمة المرور</label>
                    <input id="password" name="password" type="password" required
                           class="w-full px-4 py-3 bg-[#0F0F0F] border border-purple-500/20 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-purple-500 transition-colors"
                           placeholder="••••••••">
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-300 mb-2">تأكيد كلمة المرور</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required
                           class="w-full px-4 py-3 bg-[#0F0F0F] border border-purple-500/20 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-purple-500 transition-colors"
                           placeholder="••••••••">
                </div>

                <!-- Terms -->
                <div class="flex items-start">
                    <input type="checkbox" name="terms" required class="w-4 h-4 mt-1 text-purple-500 bg-[#0F0F0F] border-purple-500/20 rounded focus:ring-purple-500">
                    <label class="mr-2 text-sm text-gray-300">
                        أوافق على <a href="{{ route('terms') }}" class="text-purple-400 hover:text-orange-400">الشروط والأحكام</a> و
                        <a href="{{ route('privacy') }}" class="text-purple-400 hover:text-orange-400">سياسة الخصوصية</a>
                    </label>
                </div>

                <!-- Submit -->
                <button type="submit" class="w-full bg-gradient-to-r from-purple-500 to-orange-500 text-white py-4 rounded-xl font-bold text-lg hover:shadow-2xl hover:shadow-purple-500/50 transition-all duration-300 hover:scale-105">
                    إنشاء حساب
                </button>
            </form>
        </div>

        <!-- Login Link -->
        <p class="text-center text-gray-400">
            لديك حساب بالفعل؟
            <a href="{{ route('login') }}" class="text-purple-400 hover:text-orange-400 font-semibold transition-colors">
                تسجيل الدخول
            </a>
        </p>
    </div>
</div>
@endsection
