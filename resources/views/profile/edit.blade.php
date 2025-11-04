@extends('layouts.app')

@section('title', 'تعديل الملف الشخصي')

@section('content')
<div class="min-h-screen bg-[#0F0F0F] py-12">
    <div class="container mx-auto px-4 max-w-3xl">
        <h1 class="text-4xl font-black text-white mb-8">
            <span class="bg-gradient-to-r from-purple-500 to-orange-500 bg-clip-text text-transparent">تعديل الملف الشخصي</span>
        </h1>

        <div class="bg-[#1A1A1A] rounded-2xl border border-purple-500/20 p-8">
            @if($errors->any())
                <div class="mb-6 p-4 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">الاسم الأول</label>
                        <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" required
                               class="w-full px-4 py-3 bg-[#0F0F0F] border border-purple-500/20 rounded-xl text-white focus:outline-none focus:border-purple-500 transition-colors">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">الاسم الأخير</label>
                        <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" required
                               class="w-full px-4 py-3 bg-[#0F0F0F] border border-purple-500/20 rounded-xl text-white focus:outline-none focus:border-purple-500 transition-colors">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">البريد الإلكتروني</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                               class="w-full px-4 py-3 bg-[#0F0F0F] border border-purple-500/20 rounded-xl text-white focus:outline-none focus:border-purple-500 transition-colors">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">رقم الهاتف</label>
                        <div class="flex gap-2">
                            <!-- Country Code -->
                            <div class="w-32">
                                @php
                                    $currentPhone = old('phone', $user->phone);
                                    $countryCode = '+966';
                                    $phoneNumber = '';
                                    
                                    if ($currentPhone) {
                                        // Extract country code from phone (common patterns)
                                        if (preg_match('/^(\+\d{1,3})(.+)$/', $currentPhone, $matches)) {
                                            $countryCode = $matches[1];
                                            $phoneNumber = $matches[2];
                                        } else {
                                            // Default to Saudi Arabia if no country code
                                            $phoneNumber = $currentPhone;
                                        }
                                    }
                                @endphp
                                <select id="country_code" name="country_code" 
                                        class="w-full px-4 py-3 bg-[#0F0F0F] border border-purple-500/20 rounded-xl text-white focus:outline-none focus:border-purple-500 transition-colors">
                                    <option value="+966" {{ old('country_code', $countryCode) == '+966' ? 'selected' : '' }}>🇸🇦 +966</option>
                                    <option value="+971" {{ old('country_code', $countryCode) == '+971' ? 'selected' : '' }}>🇦🇪 +971</option>
                                    <option value="+965" {{ old('country_code', $countryCode) == '+965' ? 'selected' : '' }}>🇰🇼 +965</option>
                                    <option value="+974" {{ old('country_code', $countryCode) == '+974' ? 'selected' : '' }}>🇶🇦 +974</option>
                                    <option value="+968" {{ old('country_code', $countryCode) == '+968' ? 'selected' : '' }}>🇴🇲 +968</option>
                                    <option value="+973" {{ old('country_code', $countryCode) == '+973' ? 'selected' : '' }}>🇧🇭 +973</option>
                                    <option value="+961" {{ old('country_code', $countryCode) == '+961' ? 'selected' : '' }}>🇱🇧 +961</option>
                                    <option value="+962" {{ old('country_code', $countryCode) == '+962' ? 'selected' : '' }}>🇯🇴 +962</option>
                                    <option value="+20" {{ old('country_code', $countryCode) == '+20' ? 'selected' : '' }}>🇪🇬 +20</option>
                                    <option value="+212" {{ old('country_code', $countryCode) == '+212' ? 'selected' : '' }}>🇲🇦 +212</option>
                                    <option value="+1" {{ old('country_code', $countryCode) == '+1' ? 'selected' : '' }}>🇺🇸 +1</option>
                                    <option value="+44" {{ old('country_code', $countryCode) == '+44' ? 'selected' : '' }}>🇬🇧 +44</option>
                                    <option value="+33" {{ old('country_code', $countryCode) == '+33' ? 'selected' : '' }}>🇫🇷 +33</option>
                                    <option value="+49" {{ old('country_code', $countryCode) == '+49' ? 'selected' : '' }}>🇩🇪 +49</option>
                                    <option value="+39" {{ old('country_code', $countryCode) == '+39' ? 'selected' : '' }}>🇮🇹 +39</option>
                                    <option value="+34" {{ old('country_code', $countryCode) == '+34' ? 'selected' : '' }}>🇪🇸 +34</option>
                                    <option value="+90" {{ old('country_code', $countryCode) == '+90' ? 'selected' : '' }}>🇹🇷 +90</option>
                                    <option value="+91" {{ old('country_code', $countryCode) == '+91' ? 'selected' : '' }}>🇮🇳 +91</option>
                                    <option value="+86" {{ old('country_code', $countryCode) == '+86' ? 'selected' : '' }}>🇨🇳 +86</option>
                                    <option value="+81" {{ old('country_code', $countryCode) == '+81' ? 'selected' : '' }}>🇯🇵 +81</option>
                                    <option value="+82" {{ old('country_code', $countryCode) == '+82' ? 'selected' : '' }}>🇰🇷 +82</option>
                                    <option value="+60" {{ old('country_code', $countryCode) == '+60' ? 'selected' : '' }}>🇲🇾 +60</option>
                                    <option value="+65" {{ old('country_code', $countryCode) == '+65' ? 'selected' : '' }}>🇸🇬 +65</option>
                                    <option value="+62" {{ old('country_code', $countryCode) == '+62' ? 'selected' : '' }}>🇮🇩 +62</option>
                                    <option value="+66" {{ old('country_code', $countryCode) == '+66' ? 'selected' : '' }}>🇹🇭 +66</option>
                                    <option value="+84" {{ old('country_code', $countryCode) == '+84' ? 'selected' : '' }}>🇻🇳 +84</option>
                                    <option value="+63" {{ old('country_code', $countryCode) == '+63' ? 'selected' : '' }}>🇵🇭 +63</option>
                                </select>
                            </div>
                            <!-- Phone Number -->
                            <div class="flex-1">
                                <input id="phone_number" name="phone_number" type="tel"
                                       value="{{ old('phone_number', $phoneNumber) }}"
                                       class="w-full px-4 py-3 bg-[#0F0F0F] border border-purple-500/20 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-purple-500 transition-colors"
                                       placeholder="5xxxxxxxx">
                                <input type="hidden" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">تاريخ الميلاد</label>
                        <input type="date" name="birth_date" value="{{ old('birth_date', $user->birth_date) }}"
                               class="w-full px-4 py-3 bg-[#0F0F0F] border border-purple-500/20 rounded-xl text-white focus:outline-none focus:border-purple-500 transition-colors">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">الجنس</label>
                        <select name="gender"
                                class="w-full px-4 py-3 bg-[#0F0F0F] border border-purple-500/20 rounded-xl text-white focus:outline-none focus:border-purple-500 transition-colors">
                            <option value="">اختر</option>
                            <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>ذكر</option>
                            <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>أنثى</option>
                            <option value="other" {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>آخر</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-300 mb-2">العنوان</label>
                        <input type="text" name="address" value="{{ old('address', $user->address) }}"
                               class="w-full px-4 py-3 bg-[#0F0F0F] border border-purple-500/20 rounded-xl text-white focus:outline-none focus:border-purple-500 transition-colors">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">المدينة</label>
                        <input type="text" name="city" value="{{ old('city', $user->city) }}"
                               class="w-full px-4 py-3 bg-[#0F0F0F] border border-purple-500/20 rounded-xl text-white focus:outline-none focus:border-purple-500 transition-colors">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">الدولة</label>
                        <input type="text" name="country" value="{{ old('country', $user->country) }}"
                               class="w-full px-4 py-3 bg-[#0F0F0F] border border-purple-500/20 rounded-xl text-white focus:outline-none focus:border-purple-500 transition-colors">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-300 mb-2">الرمز البريدي</label>
                        <input type="text" name="postal_code" value="{{ old('postal_code', $user->postal_code) }}"
                               class="w-full px-4 py-3 bg-[#0F0F0F] border border-purple-500/20 rounded-xl text-white focus:outline-none focus:border-purple-500 transition-colors">
                    </div>
                </div>

                <div class="flex gap-4">
                    <button type="submit"
                            class="flex-1 bg-gradient-to-r from-purple-500 to-orange-500 text-white py-4 rounded-xl font-bold hover:shadow-2xl hover:shadow-purple-500/50 transition-all duration-300">
                        حفظ التغييرات
                    </button>
                    <a href="{{ route('profile.index') }}"
                       class="flex-1 bg-[#0F0F0F] border border-purple-500/20 text-white py-4 rounded-xl font-bold text-center hover:border-purple-500 transition-all">
                        إلغاء
                    </a>
                </div>
            </form>
        </div>
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

