@extends('layouts.app')

@section('title', 'تسجيل الدخول')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-[#0F0F0F]">
    <div class="max-w-md w-full space-y-8">
        <!-- Header -->
        <div class="text-center">
            <h2 class="text-4xl font-black text-white mb-2">
                مرحباً <span class="bg-gradient-to-r from-purple-500 to-orange-500 bg-clip-text text-transparent">بعودتك</span>
            </h2>
            <p class="text-gray-400">سجّل الدخول للوصول إلى حسابك</p>
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

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <!-- Email -->
                <div>
                    <label for="login" class="block text-sm font-semibold text-gray-300 mb-2">البريد الإلكتروني أو رقم الهاتف</label>
                    <input id="login" name="login" type="text" required autofocus
                           value="{{ old('login') }}"
                           class="w-full px-4 py-3 bg-[#0F0F0F] border border-purple-500/20 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-purple-500 transition-colors"
                           placeholder="your@email.com أو 05xxxxxxxx">
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-300 mb-2">كلمة المرور</label>
                    <input id="password" name="password" type="password" required
                           class="w-full px-4 py-3 bg-[#0F0F0F] border border-purple-500/20 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-purple-500 transition-colors"
                           placeholder="••••••••">
                </div>

                <!-- Remember & Forgot -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-purple-500 bg-[#0F0F0F] border-purple-500/20 rounded focus:ring-purple-500">
                        <span class="mr-2 text-sm text-gray-300">تذكرني</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="text-sm text-purple-400 hover:text-orange-400 transition-colors">
                        نسيت كلمة المرور؟
                    </a>
                </div>

                <!-- Submit -->
                <button type="submit" class="w-full bg-gradient-to-r from-purple-500 to-orange-500 text-white py-4 rounded-xl font-bold text-lg hover:shadow-2xl hover:shadow-purple-500/50 transition-all duration-300 hover:scale-105">
                    تسجيل الدخول
                </button>
            </form>
        </div>

        <!-- Register Link -->
        <p class="text-center text-gray-400">
            ليس لديك حساب؟
            <a href="{{ route('register') }}" class="text-purple-400 hover:text-orange-400 font-semibold transition-colors">
                إنشاء حساب جديد
            </a>
        </p>
    </div>
</div>
@endsection
