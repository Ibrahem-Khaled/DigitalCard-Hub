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
                    <div class="flex gap-2">
                        <!-- Country Code -->
                        <div class="w-32">
                            <select id="country_code" name="country_code" 
                                    class="w-full px-4 py-3 bg-[#0F0F0F] border border-purple-500/20 rounded-xl text-white focus:outline-none focus:border-purple-500 transition-colors">
                                <option value="+966" {{ old('country_code', '+966') == '+966' ? 'selected' : '' }}>🇸🇦 +966</option>
                                <option value="+971" {{ old('country_code') == '+971' ? 'selected' : '' }}>🇦🇪 +971</option>
                                <option value="+965" {{ old('country_code') == '+965' ? 'selected' : '' }}>🇰🇼 +965</option>
                                <option value="+974" {{ old('country_code') == '+974' ? 'selected' : '' }}>🇶🇦 +974</option>
                                <option value="+968" {{ old('country_code') == '+968' ? 'selected' : '' }}>🇴🇲 +968</option>
                                <option value="+973" {{ old('country_code') == '+973' ? 'selected' : '' }}>🇧🇭 +973</option>
                                <option value="+961" {{ old('country_code') == '+961' ? 'selected' : '' }}>🇱🇧 +961</option>
                                <option value="+962" {{ old('country_code') == '+962' ? 'selected' : '' }}>🇯🇴 +962</option>
                                <option value="+20" {{ old('country_code') == '+20' ? 'selected' : '' }}>🇪🇬 +20</option>
                                <option value="+212" {{ old('country_code') == '+212' ? 'selected' : '' }}>🇲🇦 +212</option>
                                <option value="+1" {{ old('country_code') == '+1' ? 'selected' : '' }}>🇺🇸 +1</option>
                                <option value="+44" {{ old('country_code') == '+44' ? 'selected' : '' }}>🇬🇧 +44</option>
                                <option value="+33" {{ old('country_code') == '+33' ? 'selected' : '' }}>🇫🇷 +33</option>
                                <option value="+49" {{ old('country_code') == '+49' ? 'selected' : '' }}>🇩🇪 +49</option>
                                <option value="+39" {{ old('country_code') == '+39' ? 'selected' : '' }}>🇮🇹 +39</option>
                                <option value="+34" {{ old('country_code') == '+34' ? 'selected' : '' }}>🇪🇸 +34</option>
                                <option value="+90" {{ old('country_code') == '+90' ? 'selected' : '' }}>🇹🇷 +90</option>
                                <option value="+91" {{ old('country_code') == '+91' ? 'selected' : '' }}>🇮🇳 +91</option>
                                <option value="+86" {{ old('country_code') == '+86' ? 'selected' : '' }}>🇨🇳 +86</option>
                                <option value="+81" {{ old('country_code') == '+81' ? 'selected' : '' }}>🇯🇵 +81</option>
                                <option value="+82" {{ old('country_code') == '+82' ? 'selected' : '' }}>🇰🇷 +82</option>
                                <option value="+60" {{ old('country_code') == '+60' ? 'selected' : '' }}>🇲🇾 +60</option>
                                <option value="+65" {{ old('country_code') == '+65' ? 'selected' : '' }}>🇸🇬 +65</option>
                                <option value="+62" {{ old('country_code') == '+62' ? 'selected' : '' }}>🇮🇩 +62</option>
                                <option value="+66" {{ old('country_code') == '+66' ? 'selected' : '' }}>🇹🇭 +66</option>
                                <option value="+84" {{ old('country_code') == '+84' ? 'selected' : '' }}>🇻🇳 +84</option>
                                <option value="+63" {{ old('country_code') == '+63' ? 'selected' : '' }}>🇵🇭 +63</option>
                            </select>
                        </div>
                        <!-- Phone Number -->
                        <div class="flex-1">
                            <input id="phone_number" name="phone_number" type="tel"
                                   value="{{ old('phone_number') }}"
                                   class="w-full px-4 py-3 bg-[#0F0F0F] border border-purple-500/20 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-purple-500 transition-colors"
                                   placeholder="5xxxxxxxx">
                            <input type="hidden" id="phone" name="phone" value="{{ old('phone') }}">
                        </div>
                    </div>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const countryCodeSelect = document.getElementById('country_code');
    const phoneNumberInput = document.getElementById('phone_number');
    const phoneHiddenInput = document.getElementById('phone');
    
    // Function to combine country code and phone number
    function updatePhoneValue() {
        const countryCode = countryCodeSelect.value;
        const phoneNumber = phoneNumberInput.value.trim();
        
        if (phoneNumber) {
            // Remove leading zeros and spaces
            let cleanPhone = phoneNumber.replace(/^0+/, '').replace(/\s+/g, '');
            // Combine country code with phone number
            phoneHiddenInput.value = countryCode + cleanPhone;
        } else {
            phoneHiddenInput.value = '';
        }
    }
    
    // Update phone value on input change
    phoneNumberInput.addEventListener('input', function() {
        // Only allow numbers
        this.value = this.value.replace(/[^0-9]/g, '');
        updatePhoneValue();
    });
    
    // Update phone value when country code changes
    countryCodeSelect.addEventListener('change', updatePhoneValue);
    
    // Initialize on page load
    updatePhoneValue();
});
</script>
@endsection
