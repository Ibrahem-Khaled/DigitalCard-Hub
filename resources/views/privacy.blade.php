@extends('layouts.app')

@section('title', 'سياسة الخصوصية')

@section('content')
<div class="min-h-screen bg-[#0F0F0F] py-12">
    <div class="container mx-auto px-4 max-w-4xl">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-5xl font-black text-white mb-4">
                سياسة <span class="bg-gradient-to-r from-purple-500 to-orange-500 bg-clip-text text-transparent">الخصوصية</span>
            </h1>
            <p class="text-gray-400">آخر تحديث: {{ date('Y/m/d') }}</p>
        </div>

        <div class="bg-[#1A1A1A] rounded-2xl border border-purple-500/20 p-8 lg:p-12 space-y-8">
            <!-- Introduction -->
            <section>
                <h2 class="text-2xl font-bold text-white mb-4 flex items-center gap-2">
                    <span class="w-2 h-8 bg-gradient-to-b from-purple-500 to-orange-500 rounded-full"></span>
                    المقدمة
                </h2>
                <p class="text-gray-300 leading-relaxed">
                    نحن في {{ $settings['site_name'] ?? 'متجر البطاقات الرقمية' }} نلتزم بحماية خصوصيتك وبياناتك الشخصية. توضح هذه السياسة كيفية جمع واستخدام وحماية معلوماتك الشخصية عند استخدامك لموقعنا وخدماتنا.
                </p>
            </section>

            <!-- Data Collection -->
            <section>
                <h2 class="text-2xl font-bold text-white mb-4 flex items-center gap-2">
                    <span class="w-2 h-8 bg-gradient-to-b from-purple-500 to-orange-500 rounded-full"></span>
                    البيانات التي نجمعها
                </h2>
                <div class="space-y-4 text-gray-300">
                    <div class="bg-[#0F0F0F] rounded-xl p-4 border border-purple-500/10">
                        <h3 class="font-semibold text-white mb-2">معلومات الحساب</h3>
                        <ul class="list-disc list-inside space-y-1 text-gray-400">
                            <li>الاسم الكامل</li>
                            <li>البريد الإلكتروني</li>
                            <li>رقم الهاتف</li>
                            <li>كلمة المرور (مشفرة)</li>
                        </ul>
                    </div>

                    <div class="bg-[#0F0F0F] rounded-xl p-4 border border-purple-500/10">
                        <h3 class="font-semibold text-white mb-2">معلومات الطلبات</h3>
                        <ul class="list-disc list-inside space-y-1 text-gray-400">
                            <li>تاريخ الطلبات والمشتريات</li>
                            <li>تفاصيل الدفع (بشكل مشفر)</li>
                            <li>عنوان التسليم (إن وجد)</li>
                        </ul>
                    </div>

                    <div class="bg-[#0F0F0F] rounded-xl p-4 border border-purple-500/10">
                        <h3 class="font-semibold text-white mb-2">معلومات التصفح</h3>
                        <ul class="list-disc list-inside space-y-1 text-gray-400">
                            <li>عنوان IP</li>
                            <li>نوع المتصفح والجهاز</li>
                            <li>صفحات الموقع التي تمت زيارتها</li>
                            <li>ملفات تعريف الارتباط (Cookies)</li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Data Usage -->
            <section>
                <h2 class="text-2xl font-bold text-white mb-4 flex items-center gap-2">
                    <span class="w-2 h-8 bg-gradient-to-b from-purple-500 to-orange-500 rounded-full"></span>
                    كيف نستخدم بياناتك
                </h2>
                <ul class="space-y-3 text-gray-300">
                    <li class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-purple-500 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>معالجة وتنفيذ طلباتك ومشترياتك</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-purple-500 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>تحسين تجربة المستخدم وتخصيص المحتوى</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-purple-500 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>إرسال إشعارات حول طلباتك وحسابك</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-purple-500 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>تحليل وتحسين أداء الموقع</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-purple-500 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>الامتثال للمتطلبات القانونية والتنظيمية</span>
                    </li>
                </ul>
            </section>

            <!-- Data Protection -->
            <section>
                <h2 class="text-2xl font-bold text-white mb-4 flex items-center gap-2">
                    <span class="w-2 h-8 bg-gradient-to-b from-purple-500 to-orange-500 rounded-full"></span>
                    حماية البيانات
                </h2>
                <p class="text-gray-300 leading-relaxed mb-4">
                    نتخذ إجراءات أمنية صارمة لحماية معلوماتك الشخصية، بما في ذلك:
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-[#0F0F0F] rounded-xl p-4 border border-purple-500/10">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 rounded-lg bg-purple-500/10 flex items-center justify-center">
                                <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <h3 class="font-semibold text-white">التشفير SSL</h3>
                        </div>
                        <p class="text-gray-400 text-sm">جميع البيانات المنقولة مشفرة</p>
                    </div>

                    <div class="bg-[#0F0F0F] rounded-xl p-4 border border-purple-500/10">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 rounded-lg bg-orange-500/10 flex items-center justify-center">
                                <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                            <h3 class="font-semibold text-white">حماية متقدمة</h3>
                        </div>
                        <p class="text-gray-400 text-sm">جدران حماية وأنظمة كشف التهديدات</p>
                    </div>

                    <div class="bg-[#0F0F0F] rounded-xl p-4 border border-purple-500/10">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 rounded-lg bg-purple-500/10 flex items-center justify-center">
                                <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                </svg>
                            </div>
                            <h3 class="font-semibold text-white">كلمات مرور قوية</h3>
                        </div>
                        <p class="text-gray-400 text-sm">تشفير متقدم لكلمات المرور</p>
                    </div>

                    <div class="bg-[#0F0F0F] rounded-xl p-4 border border-purple-500/10">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 rounded-lg bg-orange-500/10 flex items-center justify-center">
                                <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                                </svg>
                            </div>
                            <h3 class="font-semibold text-white">التحكم بالوصول</h3>
                        </div>
                        <p class="text-gray-400 text-sm">صلاحيات محددة للموظفين</p>
                    </div>
                </div>
            </section>

            <!-- User Rights -->
            <section>
                <h2 class="text-2xl font-bold text-white mb-4 flex items-center gap-2">
                    <span class="w-2 h-8 bg-gradient-to-b from-purple-500 to-orange-500 rounded-full"></span>
                    حقوقك
                </h2>
                <div class="space-y-3 text-gray-300">
                    <p>لك الحق في:</p>
                    <ul class="space-y-2">
                        <li class="flex items-start gap-2">
                            <span class="text-orange-500 font-bold">•</span>
                            <span>الوصول إلى بياناتك الشخصية ومراجعتها</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-orange-500 font-bold">•</span>
                            <span>تحديث أو تعديل معلوماتك</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-orange-500 font-bold">•</span>
                            <span>طلب حذف حسابك وبياناتك</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-orange-500 font-bold">•</span>
                            <span>الاعتراض على معالجة بياناتك</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-orange-500 font-bold">•</span>
                            <span>سحب الموافقة في أي وقت</span>
                        </li>
                    </ul>
                </div>
            </section>

            <!-- Cookies -->
            <section>
                <h2 class="text-2xl font-bold text-white mb-4 flex items-center gap-2">
                    <span class="w-2 h-8 bg-gradient-to-b from-purple-500 to-orange-500 rounded-full"></span>
                    ملفات تعريف الارتباط (Cookies)
                </h2>
                <p class="text-gray-300 leading-relaxed mb-4">
                    نستخدم ملفات تعريف الارتباط لتحسين تجربتك على موقعنا. يمكنك التحكم في إعدادات ملفات تعريف الارتباط من خلال متصفحك.
                </p>
            </section>

            <!-- Contact -->
            <section class="bg-gradient-to-r from-purple-500/10 to-orange-500/10 rounded-xl p-6 border border-purple-500/20">
                <h2 class="text-2xl font-bold text-white mb-4">تواصل معنا</h2>
                <p class="text-gray-300 mb-4">
                    إذا كان لديك أي استفسارات حول سياسة الخصوصية أو ممارساتنا، يرجى التواصل معنا:
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="mailto:{{ $settings['contact_email'] ?? 'info@example.com' }}" class="flex items-center gap-2 text-purple-400 hover:text-orange-400 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        {{ $settings['contact_email'] ?? 'info@example.com' }}
                    </a>
                    <a href="{{ route('contact') }}" class="bg-gradient-to-r from-purple-500 to-orange-500 px-6 py-2 rounded-lg text-white font-semibold hover:shadow-lg hover:shadow-purple-500/50 transition-all">
                        نموذج التواصل
                    </a>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection

