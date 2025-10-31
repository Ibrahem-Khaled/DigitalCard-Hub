@extends('layouts.app')

@section('title', 'الشروط والأحكام')

@section('content')
<div class="min-h-screen bg-[#0F0F0F] py-12">
    <div class="container mx-auto px-4 max-w-4xl">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-5xl font-black text-white mb-4">
                <span class="bg-gradient-to-r from-purple-500 to-orange-500 bg-clip-text text-transparent">الشروط والأحكام</span>
            </h1>
            <p class="text-gray-400">آخر تحديث: {{ date('Y/m/d') }}</p>
        </div>

        <div class="bg-[#1A1A1A] rounded-2xl border border-purple-500/20 p-8 lg:p-12 space-y-8">
            <!-- Introduction -->
            <section>
                <h2 class="text-2xl font-bold text-white mb-4 flex items-center gap-2">
                    <span class="w-2 h-8 bg-gradient-to-b from-purple-500 to-orange-500 rounded-full"></span>
                    مرحباً بك
                </h2>
                <p class="text-gray-300 leading-relaxed">
                    مرحباً بك في {{ $settings['site_name'] ?? 'متجر البطاقات الرقمية' }}. باستخدامك لهذا الموقع، فإنك توافق على الالتزام بهذه الشروط والأحكام. يرجى قراءتها بعناية قبل استخدام خدماتنا.
                </p>
            </section>

            <!-- Account Terms -->
            <section>
                <h2 class="text-2xl font-bold text-white mb-4 flex items-center gap-2">
                    <span class="w-2 h-8 bg-gradient-to-b from-purple-500 to-orange-500 rounded-full"></span>
                    شروط استخدام الحساب
                </h2>
                <div class="space-y-4 text-gray-300">
                    <div class="bg-[#0F0F0F] rounded-xl p-4 border border-purple-500/10">
                        <ul class="space-y-2">
                            <li class="flex items-start gap-2">
                                <span class="text-purple-500 font-bold mt-1">•</span>
                                <span>يجب أن تكون 18 عاماً أو أكثر لإنشاء حساب</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-purple-500 font-bold mt-1">•</span>
                                <span>يجب تقديم معلومات دقيقة وكاملة عند التسجيل</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-purple-500 font-bold mt-1">•</span>
                                <span>أنت مسؤول عن الحفاظ على سرية حسابك وكلمة المرور</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-purple-500 font-bold mt-1">•</span>
                                <span>أنت مسؤول عن جميع الأنشطة التي تحدث تحت حسابك</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-purple-500 font-bold mt-1">•</span>
                                <span>يجب إخطارنا فوراً بأي استخدام غير مصرح به لحسابك</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Purchase Terms -->
            <section>
                <h2 class="text-2xl font-bold text-white mb-4 flex items-center gap-2">
                    <span class="w-2 h-8 bg-gradient-to-b from-purple-500 to-orange-500 rounded-full"></span>
                    شروط الشراء
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div class="bg-[#0F0F0F] rounded-xl p-4 border border-purple-500/10">
                        <div class="flex items-center gap-2 mb-3">
                            <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <h3 class="font-bold text-white">الأسعار</h3>
                        </div>
                        <p class="text-gray-400 text-sm">
                            جميع الأسعار معروضة بالعملة المحلية وتشمل الضرائب المطبقة. نحتفظ بالحق في تعديل الأسعار دون إشعار مسبق.
                        </p>
                    </div>

                    <div class="bg-[#0F0F0F] rounded-xl p-4 border border-purple-500/10">
                        <div class="flex items-center gap-2 mb-3">
                            <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                            <h3 class="font-bold text-white">الدفع</h3>
                        </div>
                        <p class="text-gray-400 text-sm">
                            يجب إتمام عملية الدفع بالكامل قبل تسليم المنتج. نقبل جميع طرق الدفع المعروضة على الموقع.
                        </p>
                    </div>

                    <div class="bg-[#0F0F0F] rounded-xl p-4 border border-purple-500/10">
                        <div class="flex items-center gap-2 mb-3">
                            <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <h3 class="font-bold text-white">التسليم</h3>
                        </div>
                        <p class="text-gray-400 text-sm">
                            يتم تسليم المنتجات الرقمية فوراً بعد تأكيد الدفع عبر البريد الإلكتروني أو لوحة التحكم.
                        </p>
                    </div>

                    <div class="bg-[#0F0F0F] rounded-xl p-4 border border-purple-500/10">
                        <div class="flex items-center gap-2 mb-3">
                            <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <h3 class="font-bold text-white">الضمان</h3>
                        </div>
                        <p class="text-gray-400 text-sm">
                            نضمن صلاحية جميع البطاقات الرقمية. في حالة وجود مشكلة، يرجى التواصل معنا خلال 24 ساعة.
                        </p>
                    </div>
                </div>
            </section>

            <!-- Prohibited Uses -->
            <section>
                <h2 class="text-2xl font-bold text-white mb-4 flex items-center gap-2">
                    <span class="w-2 h-8 bg-gradient-to-b from-purple-500 to-orange-500 rounded-full"></span>
                    الاستخدامات المحظورة
                </h2>
                <div class="bg-red-500/10 rounded-xl p-6 border border-red-500/20">
                    <p class="text-gray-300 mb-4">يُحظر استخدام موقعنا للأغراض التالية:</p>
                    <ul class="grid grid-cols-1 md:grid-cols-2 gap-3 text-gray-300">
                        <li class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            <span>أي نشاط غير قانوني أو احتيالي</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            <span>إساءة استخدام البطاقات المشتراة</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            <span>إعادة بيع المنتجات دون إذن</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            <span>اختراق أو محاولة اختراق الموقع</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            <span>نشر فيروسات أو برامج ضارة</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            <span>انتحال شخصية الآخرين</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            <span>جمع معلومات المستخدمين الآخرين</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            <span>التحايل على أنظمة الأمان</span>
                        </li>
                    </ul>
                </div>
            </section>

            <!-- Intellectual Property -->
            <section>
                <h2 class="text-2xl font-bold text-white mb-4 flex items-center gap-2">
                    <span class="w-2 h-8 bg-gradient-to-b from-purple-500 to-orange-500 rounded-full"></span>
                    الملكية الفكرية
                </h2>
                <div class="bg-[#0F0F0F] rounded-xl p-6 border border-purple-500/10">
                    <p class="text-gray-300 leading-relaxed mb-4">
                        جميع المحتويات والمواد المعروضة على هذا الموقع، بما في ذلك النصوص والصور والشعارات والتصاميم، محمية بموجب قوانين حقوق النشر والملكية الفكرية.
                    </p>
                    <ul class="space-y-2 text-gray-400">
                        <li class="flex items-start gap-2">
                            <span class="text-orange-500">•</span>
                            <span>لا يجوز نسخ أو تعديل أو توزيع أي محتوى دون إذن كتابي</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-orange-500">•</span>
                            <span>الشعارات والعلامات التجارية مملوكة لأصحابها</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-orange-500">•</span>
                            <span>نحتفظ بجميع الحقوق غير الممنوحة صراحة</span>
                        </li>
                    </ul>
                </div>
            </section>

            <!-- Liability -->
            <section>
                <h2 class="text-2xl font-bold text-white mb-4 flex items-center gap-2">
                    <span class="w-2 h-8 bg-gradient-to-b from-purple-500 to-orange-500 rounded-full"></span>
                    حدود المسؤولية
                </h2>
                <div class="space-y-4 text-gray-300">
                    <p class="bg-[#0F0F0F] rounded-xl p-4 border border-purple-500/10">
                        نقدم خدماتنا "كما هي" دون أي ضمانات صريحة أو ضمنية. لن نكون مسؤولين عن:
                    </p>
                    <ul class="space-y-2">
                        <li class="flex items-start gap-2 bg-[#0F0F0F] rounded-xl p-3 border border-purple-500/10">
                            <span class="text-purple-500 font-bold">1.</span>
                            <span>أي أضرار مباشرة أو غير مباشرة ناتجة عن استخدام الموقع</span>
                        </li>
                        <li class="flex items-start gap-2 bg-[#0F0F0F] rounded-xl p-3 border border-purple-500/10">
                            <span class="text-purple-500 font-bold">2.</span>
                            <span>انقطاع الخدمة أو الأخطاء التقنية</span>
                        </li>
                        <li class="flex items-start gap-2 bg-[#0F0F0F] rounded-xl p-3 border border-purple-500/10">
                            <span class="text-purple-500 font-bold">3.</span>
                            <span>فقدان البيانات أو الأرباح</span>
                        </li>
                        <li class="flex items-start gap-2 bg-[#0F0F0F] rounded-xl p-3 border border-purple-500/10">
                            <span class="text-purple-500 font-bold">4.</span>
                            <span>إساءة استخدام المنتجات من قبل المستخدم</span>
                        </li>
                    </ul>
                </div>
            </section>

            <!-- Modifications -->
            <section>
                <h2 class="text-2xl font-bold text-white mb-4 flex items-center gap-2">
                    <span class="w-2 h-8 bg-gradient-to-b from-purple-500 to-orange-500 rounded-full"></span>
                    تعديل الشروط
                </h2>
                <div class="bg-gradient-to-r from-purple-500/10 to-orange-500/10 rounded-xl p-6 border border-purple-500/20">
                    <p class="text-gray-300 leading-relaxed">
                        نحتفظ بالحق في تعديل هذه الشروط والأحكام في أي وقت. ستصبح التعديلات سارية المفعول فور نشرها على الموقع. استمرارك في استخدام الموقع بعد التعديلات يعني موافقتك على الشروط الجديدة.
                    </p>
                </div>
            </section>

            <!-- Termination -->
            <section>
                <h2 class="text-2xl font-bold text-white mb-4 flex items-center gap-2">
                    <span class="w-2 h-8 bg-gradient-to-b from-purple-500 to-orange-500 rounded-full"></span>
                    إنهاء الحساب
                </h2>
                <div class="bg-[#0F0F0F] rounded-xl p-6 border border-purple-500/10">
                    <p class="text-gray-300 mb-4">نحتفظ بالحق في إيقاف أو إنهاء حسابك في الحالات التالية:</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div class="flex items-start gap-2 text-gray-400">
                            <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <span>انتهاك الشروط والأحكام</span>
                        </div>
                        <div class="flex items-start gap-2 text-gray-400">
                            <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <span>نشاط احتيالي أو مشبوه</span>
                        </div>
                        <div class="flex items-start gap-2 text-gray-400">
                            <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <span>طلبك الشخصي لحذف الحساب</span>
                        </div>
                        <div class="flex items-start gap-2 text-gray-400">
                            <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <span>عدم النشاط لمدة طويلة</span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Governing Law -->
            <section>
                <h2 class="text-2xl font-bold text-white mb-4 flex items-center gap-2">
                    <span class="w-2 h-8 bg-gradient-to-b from-purple-500 to-orange-500 rounded-full"></span>
                    القانون الواجب التطبيق
                </h2>
                <p class="text-gray-300 bg-[#0F0F0F] rounded-xl p-4 border border-purple-500/10">
                    تخضع هذه الشروط والأحكام وتفسيرها لقوانين المملكة العربية السعودية. أي نزاع ينشأ عن هذه الشروط سيتم حله وفقاً للقوانين المحلية.
                </p>
            </section>

            <!-- Contact -->
            <section class="bg-gradient-to-r from-purple-500/10 to-orange-500/10 rounded-xl p-6 border border-purple-500/20">
                <h2 class="text-2xl font-bold text-white mb-4">تواصل معنا</h2>
                <p class="text-gray-300 mb-4">
                    إذا كان لديك أي استفسارات حول الشروط والأحكام، يمكنك التواصل معنا عبر:
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="mailto:{{ $settings['contact_email'] ?? 'info@example.com' }}" class="flex items-center gap-2 text-purple-400 hover:text-orange-400 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        {{ $settings['contact_email'] ?? 'info@example.com' }}
                    </a>
                    <a href="{{ route('contact') }}" class="bg-gradient-to-r from-purple-500 to-orange-500 px-6 py-2 rounded-lg text-white font-semibold hover:shadow-lg hover:shadow-purple-500/50 transition-all text-center">
                        نموذج التواصل
                    </a>
                </div>
            </section>

            <!-- Acceptance -->
            <section class="bg-gradient-to-r from-green-500/10 to-blue-500/10 rounded-xl p-6 border border-green-500/20">
                <div class="flex items-start gap-4">
                    <svg class="w-8 h-8 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <h3 class="text-xl font-bold text-white mb-2">الموافقة على الشروط</h3>
                        <p class="text-gray-300">
                            باستخدامك لهذا الموقع وخدماته، فإنك تقر بأنك قرأت وفهمت ووافقت على جميع الشروط والأحكام المذكورة أعلاه.
                        </p>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection

