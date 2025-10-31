@extends('layouts.app')

@section('title', 'سياسة الاسترجاع والاستبدال')

@section('content')
<div class="min-h-screen bg-[#0F0F0F] py-12">
    <div class="container mx-auto px-4 max-w-4xl">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-5xl font-black text-white mb-4">
                سياسة <span class="bg-gradient-to-r from-purple-500 to-orange-500 bg-clip-text text-transparent">الاسترجاع والاستبدال</span>
            </h1>
            <p class="text-gray-400">آخر تحديث: {{ date('Y/m/d') }}</p>
        </div>

        <div class="bg-[#1A1A1A] rounded-2xl border border-purple-500/20 p-8 lg:p-12 space-y-8">
            <!-- Introduction -->
            <section>
                <h2 class="text-2xl font-bold text-white mb-4 flex items-center gap-2">
                    <span class="w-2 h-8 bg-gradient-to-b from-purple-500 to-orange-500 rounded-full"></span>
                    مقدمة
                </h2>
                <p class="text-gray-300 leading-relaxed">
                    في {{ $settings['site_name'] ?? 'متجر البطاقات الرقمية' }}، نلتزم بتوفير تجربة شراء ممتازة. نحن نتفهم أنه قد تحدث مشاكل أحياناً، لذلك وضعنا سياسة واضحة للاسترجاع والاستبدال.
                </p>
            </section>

            <!-- Digital Products -->
            <section>
                <h2 class="text-2xl font-bold text-white mb-4 flex items-center gap-2">
                    <span class="w-2 h-8 bg-gradient-to-b from-purple-500 to-orange-500 rounded-full"></span>
                    المنتجات الرقمية
                </h2>
                <div class="bg-[#0F0F0F] rounded-xl p-6 border border-purple-500/10 mb-4">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-r from-purple-500 to-orange-500 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-white mb-2">بطاقات الشحن الرقمية</h3>
                            <p class="text-gray-300 mb-3">
                                بسبب طبيعة المنتجات الرقمية، لا يمكن استرجاعها بعد التسليم إلا في الحالات التالية:
                            </p>
                            <ul class="space-y-2 text-gray-400">
                                <li class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-orange-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>البطاقة غير صالحة أو مستخدمة مسبقاً</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-orange-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>استلمت منتج مختلف عما طلبته</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-orange-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>خطأ تقني منع استخدام البطاقة</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Refund Conditions -->
            <section>
                <h2 class="text-2xl font-bold text-white mb-4 flex items-center gap-2">
                    <span class="w-2 h-8 bg-gradient-to-b from-purple-500 to-orange-500 rounded-full"></span>
                    شروط الاسترجاع
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-[#0F0F0F] rounded-xl p-4 border border-green-500/20">
                        <div class="flex items-center gap-2 mb-3">
                            <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <h3 class="font-bold text-white">يمكن الاسترجاع</h3>
                        </div>
                        <ul class="space-y-2 text-gray-400 text-sm">
                            <li class="flex gap-2">
                                <span>•</span>
                                <span>خلال 24 ساعة من الشراء</span>
                            </li>
                            <li class="flex gap-2">
                                <span>•</span>
                                <span>البطاقة لم تُستخدم</span>
                            </li>
                            <li class="flex gap-2">
                                <span>•</span>
                                <span>وجود إثبات للمشكلة</span>
                            </li>
                            <li class="flex gap-2">
                                <span>•</span>
                                <span>خطأ من البائع</span>
                            </li>
                        </ul>
                    </div>

                    <div class="bg-[#0F0F0F] rounded-xl p-4 border border-red-500/20">
                        <div class="flex items-center gap-2 mb-3">
                            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            <h3 class="font-bold text-white">لا يمكن الاسترجاع</h3>
                        </div>
                        <ul class="space-y-2 text-gray-400 text-sm">
                            <li class="flex gap-2">
                                <span>•</span>
                                <span>بعد استخدام البطاقة</span>
                            </li>
                            <li class="flex gap-2">
                                <span>•</span>
                                <span>بعد مرور 24 ساعة</span>
                            </li>
                            <li class="flex gap-2">
                                <span>•</span>
                                <span>تغيير الرأي</span>
                            </li>
                            <li class="flex gap-2">
                                <span>•</span>
                                <span>عدم وجود إثبات</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Refund Process -->
            <section>
                <h2 class="text-2xl font-bold text-white mb-4 flex items-center gap-2">
                    <span class="w-2 h-8 bg-gradient-to-b from-purple-500 to-orange-500 rounded-full"></span>
                    إجراءات الاسترجاع
                </h2>
                <div class="space-y-4">
                    <!-- Step 1 -->
                    <div class="flex gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-r from-purple-500 to-orange-500 flex items-center justify-center text-white font-bold">
                                1
                            </div>
                        </div>
                        <div class="flex-1 pt-1">
                            <h3 class="font-bold text-white mb-1">تواصل معنا</h3>
                            <p class="text-gray-400">
                                اتصل بفريق الدعم الفني عبر البريد الإلكتروني أو نموذج التواصل
                            </p>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="flex gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-r from-purple-500 to-orange-500 flex items-center justify-center text-white font-bold">
                                2
                            </div>
                        </div>
                        <div class="flex-1 pt-1">
                            <h3 class="font-bold text-white mb-1">قدّم المعلومات المطلوبة</h3>
                            <p class="text-gray-400">
                                رقم الطلب، تفاصيل المشكلة، وأي صور أو أدلة داعمة
                            </p>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div class="flex gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-r from-purple-500 to-orange-500 flex items-center justify-center text-white font-bold">
                                3
                            </div>
                        </div>
                        <div class="flex-1 pt-1">
                            <h3 class="font-bold text-white mb-1">انتظر المراجعة</h3>
                            <p class="text-gray-400">
                                سيقوم فريقنا بمراجعة طلبك خلال 24-48 ساعة عمل
                            </p>
                        </div>
                    </div>

                    <!-- Step 4 -->
                    <div class="flex gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-r from-purple-500 to-orange-500 flex items-center justify-center text-white font-bold">
                                4
                            </div>
                        </div>
                        <div class="flex-1 pt-1">
                            <h3 class="font-bold text-white mb-1">الحصول على الاسترجاع</h3>
                            <p class="text-gray-400">
                                في حالة الموافقة، سيتم إرجاع المبلغ خلال 5-7 أيام عمل
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Refund Methods -->
            <section>
                <h2 class="text-2xl font-bold text-white mb-4 flex items-center gap-2">
                    <span class="w-2 h-8 bg-gradient-to-b from-purple-500 to-orange-500 rounded-full"></span>
                    طرق الاسترجاع
                </h2>
                <p class="text-gray-300 mb-4">سيتم إرجاع المبلغ بنفس طريقة الدفع المستخدمة:</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-[#0F0F0F] rounded-xl p-4 border border-purple-500/10 text-center">
                        <div class="w-12 h-12 rounded-xl bg-purple-500/10 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                        </div>
                        <h3 class="font-bold text-white mb-1">البطاقة البنكية</h3>
                        <p class="text-gray-400 text-sm">5-7 أيام عمل</p>
                    </div>

                    <div class="bg-[#0F0F0F] rounded-xl p-4 border border-purple-500/10 text-center">
                        <div class="w-12 h-12 rounded-xl bg-orange-500/10 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <h3 class="font-bold text-white mb-1">المحفظة الإلكترونية</h3>
                        <p class="text-gray-400 text-sm">3-5 أيام عمل</p>
                    </div>

                    <div class="bg-[#0F0F0F] rounded-xl p-4 border border-purple-500/10 text-center">
                        <div class="w-12 h-12 rounded-xl bg-purple-500/10 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="font-bold text-white mb-1">رصيد المتجر</h3>
                        <p class="text-gray-400 text-sm">فوري</p>
                    </div>
                </div>
            </section>

            <!-- Important Notes -->
            <section class="bg-gradient-to-r from-orange-500/10 to-red-500/10 rounded-xl p-6 border border-orange-500/20">
                <h2 class="text-2xl font-bold text-white mb-4 flex items-center gap-2">
                    <svg class="w-7 h-7 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    ملاحظات مهمة
                </h2>
                <ul class="space-y-2 text-gray-300">
                    <li class="flex items-start gap-2">
                        <span class="text-orange-500 font-bold mt-1">!</span>
                        <span>يرجى التأكد من صحة المنتج قبل استخدامه</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-orange-500 font-bold mt-1">!</span>
                        <span>احتفظ بجميع تفاصيل الطلب والإيصالات</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-orange-500 font-bold mt-1">!</span>
                        <span>لا نقبل طلبات الاسترجاع بعد استخدام المنتج</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-orange-500 font-bold mt-1">!</span>
                        <span>نحتفظ بالحق في رفض الطلبات المشبوهة أو الاحتيالية</span>
                    </li>
                </ul>
            </section>

            <!-- Contact -->
            <section class="bg-gradient-to-r from-purple-500/10 to-orange-500/10 rounded-xl p-6 border border-purple-500/20">
                <h2 class="text-2xl font-bold text-white mb-4">تحتاج مساعدة؟</h2>
                <p class="text-gray-300 mb-4">
                    فريق الدعم الفني متاح لمساعدتك في أي استفسار حول سياسة الاسترجاع
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="mailto:{{ $settings['contact_email'] ?? 'info@example.com' }}" class="flex items-center justify-center gap-2 bg-[#0F0F0F] px-6 py-3 rounded-lg text-purple-400 hover:text-orange-400 transition-colors border border-purple-500/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        {{ $settings['contact_email'] ?? 'info@example.com' }}
                    </a>
                    <a href="{{ route('contact') }}" class="bg-gradient-to-r from-purple-500 to-orange-500 px-6 py-3 rounded-lg text-white font-semibold hover:shadow-lg hover:shadow-purple-500/50 transition-all text-center">
                        تواصل معنا الآن
                    </a>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection

