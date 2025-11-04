@extends('layouts.app')

@section('title', 'نسيت كلمة المرور')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-[#0F0F0F]">
    <div class="max-w-md w-full space-y-8">
        <!-- Header -->
        <div class="text-center">
            <h2 class="text-4xl font-black text-white mb-2">
                نسيت <span class="bg-gradient-to-r from-purple-500 to-orange-500 bg-clip-text text-transparent">كلمة المرور</span>
            </h2>
            <p class="text-gray-400">أدخل بريدك الإلكتروني وسنرسل لك رابط إعادة تعيين كلمة المرور</p>
        </div>

        <!-- Form -->
        <div class="bg-[#1A1A1A] rounded-2xl border border-purple-500/20 p-8">
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-500/10 border border-green-500/20 rounded-xl text-green-400 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400 text-sm">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-300 mb-2">البريد الإلكتروني</label>
                    <input id="email" name="email" type="email" required autofocus
                           value="{{ old('email') }}"
                           class="w-full px-4 py-3 bg-[#0F0F0F] border border-purple-500/20 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-purple-500 transition-colors"
                           placeholder="your@email.com">
                </div>

                <!-- Info -->
                <div class="p-4 bg-blue-500/10 border border-blue-500/20 rounded-xl text-blue-400 text-sm">
                    <p class="mb-2"><strong>ملاحظة:</strong></p>
                    <ul class="list-disc list-inside space-y-1">
                        <li>سيتم إرسال رابط إعادة تعيين كلمة المرور إلى بريدك الإلكتروني</li>
                        <li>الرابط صالح لمدة ساعة واحدة فقط</li>
                        <li>تحقق من صندوق الوارد والرسائل غير المرغوب فيها</li>
                    </ul>
                </div>

                <!-- Submit -->
                <button type="submit" class="w-full bg-gradient-to-r from-purple-500 to-orange-500 text-white py-4 rounded-xl font-bold text-lg hover:shadow-2xl hover:shadow-purple-500/50 transition-all duration-300 hover:scale-105">
                    إرسال رابط إعادة التعيين
                </button>
            </form>
        </div>

        <!-- Back to Login -->
        <p class="text-center text-gray-400">
            <a href="{{ route('login') }}" class="text-purple-400 hover:text-orange-400 font-semibold transition-colors">
                العودة إلى تسجيل الدخول
            </a>
        </p>
    </div>
</div>
@endsection


