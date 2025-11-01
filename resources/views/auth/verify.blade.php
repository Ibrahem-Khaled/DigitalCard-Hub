@extends('layouts.app')

@section('title', 'كود التحقق')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-[#0F0F0F]">
    <div class="max-w-md w-full space-y-8">
        <!-- Header -->
        <div class="text-center">
            <h2 class="text-4xl font-black text-white mb-2">
                كود <span class="bg-gradient-to-r from-purple-500 to-orange-500 bg-clip-text text-transparent">التحقق</span>
            </h2>
            <p class="text-gray-400">
                @if($type === 'registration')
                    تم إرسال كود التحقق إلى بريدك الإلكتروني لإكمال عملية التسجيل
                @else
                    تم إرسال كود التحقق إلى بريدك الإلكتروني لتسجيل الدخول
                @endif
            </p>
            <p class="text-gray-500 text-sm mt-2">تم الإرسال إلى: {{ $user->email }}</p>
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

            <form method="POST" action="{{ route('verification.verify') }}" class="space-y-6">
                @csrf

                <!-- Verification Code -->
                <div>
                    <label for="code" class="block text-sm font-semibold text-gray-300 mb-2">كود التحقق</label>
                    <input id="code" name="code" type="text" required autofocus maxlength="6" 
                           pattern="[0-9]{6}" inputmode="numeric"
                           value="{{ old('code') }}"
                           class="w-full px-4 py-3 bg-[#0F0F0F] border border-purple-500/20 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-purple-500 transition-colors text-center text-2xl tracking-widest"
                           placeholder="000000">
                    <p class="mt-2 text-xs text-gray-500">أدخل الكود المكون من 6 أرقام</p>
                </div>

                <!-- Info -->
                <div class="p-4 bg-blue-500/10 border border-blue-500/20 rounded-xl text-blue-400 text-sm">
                    <p class="mb-2"><strong>ملاحظة:</strong></p>
                    <ul class="list-disc list-inside space-y-1">
                        <li>كود التحقق صالح لمدة 10 دقائق فقط</li>
                        <li>تحقق من صندوق الوارد والرسائل غير المرغوب فيها</li>
                        <li>إذا لم تصلك الرسالة، اضغط على "إعادة الإرسال"</li>
                    </ul>
                </div>

                <!-- Submit -->
                <button type="submit" class="w-full bg-gradient-to-r from-purple-500 to-orange-500 text-white py-4 rounded-xl font-bold text-lg hover:shadow-2xl hover:shadow-purple-500/50 transition-all duration-300 hover:scale-105">
                    التحقق من الكود
                </button>
            </form>

            <!-- Resend Code -->
            <div class="mt-6 pt-6 border-t border-purple-500/20">
                <form method="POST" action="{{ route('verification.resend') }}">
                    @csrf
                    <button type="submit" class="w-full text-purple-400 hover:text-orange-400 font-semibold transition-colors text-sm">
                        إعادة إرسال كود التحقق
                    </button>
                </form>
            </div>
        </div>

        <!-- Back to Login -->
        <p class="text-center text-gray-400">
            <a href="{{ route('login') }}" class="text-purple-400 hover:text-orange-400 font-semibold transition-colors">
                العودة إلى تسجيل الدخول
            </a>
        </p>
    </div>
</div>

<script>
    // Auto-focus and format code input
    document.addEventListener('DOMContentLoaded', function() {
        const codeInput = document.getElementById('code');
        
        if (codeInput) {
            // Only allow numbers
            codeInput.addEventListener('input', function(e) {
                e.target.value = e.target.value.replace(/[^0-9]/g, '');
            });

            // Auto-submit when 6 digits entered
            codeInput.addEventListener('input', function(e) {
                if (e.target.value.length === 6) {
                    // Optional: auto-submit
                    // e.target.form.submit();
                }
            });
        }
    });
</script>
@endsection

