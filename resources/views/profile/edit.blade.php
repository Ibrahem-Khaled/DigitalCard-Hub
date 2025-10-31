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
                        <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}"
                               class="w-full px-4 py-3 bg-[#0F0F0F] border border-purple-500/20 rounded-xl text-white focus:outline-none focus:border-purple-500 transition-colors">
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
@endsection

