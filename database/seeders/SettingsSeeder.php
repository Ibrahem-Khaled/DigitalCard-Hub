<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General Settings
            [
                'key' => 'site_name',
                'group' => 'general',
                'name' => 'اسم الموقع',
                'description' => 'اسم الموقع الذي يظهر في العنوان والشعار',
                'value' => 'متجر البطاقات الرقمية',
                'type' => 'text',
                'is_public' => true,
                'is_required' => true,
                'sort_order' => 1,
            ],
            [
                'key' => 'site_description',
                'group' => 'general',
                'name' => 'وصف الموقع',
                'description' => 'وصف مختصر للموقع',
                'value' => 'متجر متخصص في بيع البطاقات الرقمية والهدايا الإلكترونية',
                'type' => 'textarea',
                'is_public' => true,
                'is_required' => true,
                'sort_order' => 2,
            ],
            [
                'key' => 'site_keywords',
                'group' => 'general',
                'name' => 'كلمات مفتاحية',
                'description' => 'كلمات مفتاحية للموقع مفصولة بفواصل',
                'value' => 'بطاقات رقمية,هدايا إلكترونية,متجر أونلاين,بطاقات شحن',
                'type' => 'text',
                'is_public' => true,
                'is_required' => false,
                'sort_order' => 3,
            ],
            [
                'key' => 'site_logo',
                'group' => 'general',
                'name' => 'شعار الموقع',
                'description' => 'شعار الموقع الرئيسي',
                'value' => null,
                'type' => 'file',
                'is_public' => true,
                'is_required' => false,
                'sort_order' => 4,
            ],
            [
                'key' => 'site_favicon',
                'group' => 'general',
                'name' => 'أيقونة الموقع',
                'description' => 'أيقونة الموقع التي تظهر في المتصفح',
                'value' => null,
                'type' => 'file',
                'is_public' => true,
                'is_required' => false,
                'sort_order' => 5,
            ],

            // Site Settings
            [
                'key' => 'default_language',
                'group' => 'site',
                'name' => 'اللغة الافتراضية',
                'description' => 'اللغة الافتراضية للموقع',
                'value' => 'ar',
                'type' => 'select',
                'options' => ['ar' => 'العربية', 'en' => 'English'],
                'is_public' => true,
                'is_required' => true,
                'sort_order' => 1,
            ],
            [
                'key' => 'timezone',
                'group' => 'site',
                'name' => 'المنطقة الزمنية',
                'description' => 'المنطقة الزمنية للموقع',
                'value' => 'Asia/Riyadh',
                'type' => 'select',
                'options' => [
                    'Asia/Riyadh' => 'الرياض',
                    'Asia/Dubai' => 'دبي',
                    'Asia/Kuwait' => 'الكويت',
                    'Asia/Qatar' => 'قطر',
                    'Asia/Bahrain' => 'البحرين',
                    'Asia/Muscat' => 'مسقط',
                ],
                'is_public' => false,
                'is_required' => true,
                'sort_order' => 2,
            ],
            [
                'key' => 'maintenance_mode',
                'group' => 'site',
                'name' => 'وضع الصيانة',
                'description' => 'تفعيل وضع الصيانة للموقع',
                'value' => '0',
                'type' => 'boolean',
                'is_public' => false,
                'is_required' => false,
                'sort_order' => 3,
            ],
            [
                'key' => 'maintenance_message',
                'group' => 'site',
                'name' => 'رسالة الصيانة',
                'description' => 'الرسالة التي تظهر في وضع الصيانة',
                'value' => 'الموقع قيد الصيانة حالياً. سنعود قريباً!',
                'type' => 'textarea',
                'is_public' => false,
                'is_required' => false,
                'sort_order' => 4,
            ],

            // Contact Settings
            [
                'key' => 'contact_email',
                'group' => 'contact',
                'name' => 'البريد الإلكتروني للتواصل',
                'description' => 'البريد الإلكتروني الرئيسي للتواصل',
                'value' => 'info@example.com',
                'type' => 'email',
                'is_public' => true,
                'is_required' => true,
                'sort_order' => 1,
            ],
            [
                'key' => 'contact_phone',
                'group' => 'contact',
                'name' => 'رقم الهاتف',
                'description' => 'رقم الهاتف الرئيسي للتواصل',
                'value' => '+966501234567',
                'type' => 'text',
                'is_public' => true,
                'is_required' => false,
                'sort_order' => 2,
            ],
            [
                'key' => 'contact_address',
                'group' => 'contact',
                'name' => 'العنوان',
                'description' => 'العنوان الرئيسي للشركة',
                'value' => 'الرياض، المملكة العربية السعودية',
                'type' => 'textarea',
                'is_public' => true,
                'is_required' => false,
                'sort_order' => 3,
            ],
            [
                'key' => 'working_hours',
                'group' => 'contact',
                'name' => 'ساعات العمل',
                'description' => 'ساعات العمل الرسمية',
                'value' => '24/7',
                'type' => 'text',
                'is_public' => true,
                'is_required' => false,
                'sort_order' => 4,
            ],

            // Social Media Settings
            [
                'key' => 'facebook_url',
                'group' => 'social',
                'name' => 'رابط فيسبوك',
                'description' => 'رابط الصفحة الرسمية على فيسبوك',
                'value' => null,
                'type' => 'url',
                'is_public' => true,
                'is_required' => false,
                'sort_order' => 1,
            ],
            [
                'key' => 'twitter_url',
                'group' => 'social',
                'name' => 'رابط تويتر',
                'description' => 'رابط الحساب الرسمي على تويتر',
                'value' => null,
                'type' => 'url',
                'is_public' => true,
                'is_required' => false,
                'sort_order' => 2,
            ],
            [
                'key' => 'instagram_url',
                'group' => 'social',
                'name' => 'رابط إنستغرام',
                'description' => 'رابط الحساب الرسمي على إنستغرام',
                'value' => null,
                'type' => 'url',
                'is_public' => true,
                'is_required' => false,
                'sort_order' => 3,
            ],
            [
                'key' => 'youtube_url',
                'group' => 'social',
                'name' => 'رابط يوتيوب',
                'description' => 'رابط القناة الرسمية على يوتيوب',
                'value' => null,
                'type' => 'url',
                'is_public' => true,
                'is_required' => false,
                'sort_order' => 4,
            ],
            [
                'key' => 'linkedin_url',
                'group' => 'social',
                'name' => 'رابط لينكد إن',
                'description' => 'رابط الشركة على لينكد إن',
                'value' => null,
                'type' => 'url',
                'is_public' => true,
                'is_required' => false,
                'sort_order' => 5,
            ],

            // SEO Settings
            [
                'key' => 'meta_title',
                'group' => 'seo',
                'name' => 'عنوان Meta',
                'description' => 'العنوان الذي يظهر في نتائج البحث',
                'value' => 'متجر البطاقات الرقمية - أفضل متجر للهدايا الإلكترونية',
                'type' => 'text',
                'is_public' => true,
                'is_required' => true,
                'sort_order' => 1,
            ],
            [
                'key' => 'meta_description',
                'group' => 'seo',
                'name' => 'وصف Meta',
                'description' => 'الوصف الذي يظهر في نتائج البحث',
                'value' => 'متجر متخصص في بيع البطاقات الرقمية والهدايا الإلكترونية بأفضل الأسعار وأسرع التسليم',
                'type' => 'textarea',
                'is_public' => true,
                'is_required' => true,
                'sort_order' => 2,
            ],
            [
                'key' => 'google_analytics',
                'group' => 'seo',
                'name' => 'كود Google Analytics',
                'description' => 'كود تتبع Google Analytics',
                'value' => null,
                'type' => 'textarea',
                'is_public' => false,
                'is_required' => false,
                'sort_order' => 3,
            ],
            [
                'key' => 'google_search_console',
                'group' => 'seo',
                'name' => 'كود Google Search Console',
                'description' => 'كود التحقق من Google Search Console',
                'value' => null,
                'type' => 'text',
                'is_public' => false,
                'is_required' => false,
                'sort_order' => 4,
            ],

            // Legal Settings
            [
                'key' => 'privacy_policy',
                'group' => 'legal',
                'name' => 'سياسة الخصوصية',
                'description' => 'نص سياسة الخصوصية',
                'value' => $this->getDefaultPrivacyPolicy(),
                'type' => 'textarea',
                'is_public' => true,
                'is_required' => true,
                'sort_order' => 1,
            ],
            [
                'key' => 'terms_of_service',
                'group' => 'legal',
                'name' => 'شروط الاستخدام',
                'description' => 'نص شروط الاستخدام',
                'value' => $this->getDefaultTermsOfService(),
                'type' => 'textarea',
                'is_public' => true,
                'is_required' => true,
                'sort_order' => 2,
            ],
            [
                'key' => 'refund_policy',
                'group' => 'legal',
                'name' => 'سياسة الاسترداد',
                'description' => 'نص سياسة الاسترداد',
                'value' => $this->getDefaultRefundPolicy(),
                'type' => 'textarea',
                'is_public' => true,
                'is_required' => true,
                'sort_order' => 3,
            ],
            [
                'key' => 'shipping_policy',
                'group' => 'legal',
                'name' => 'سياسة الشحن',
                'description' => 'نص سياسة الشحن والتوصيل',
                'value' => $this->getDefaultShippingPolicy(),
                'type' => 'textarea',
                'is_public' => true,
                'is_required' => true,
                'sort_order' => 4,
            ],
            [
                'key' => 'copyright_text',
                'group' => 'legal',
                'name' => 'نص حقوق النشر',
                'description' => 'نص حقوق النشر في أسفل الموقع',
                'value' => 'جميع الحقوق محفوظة © ' . date('Y') . ' متجر البطاقات الرقمية',
                'type' => 'text',
                'is_public' => true,
                'is_required' => true,
                'sort_order' => 5,
            ],

            // Payment Settings
            [
                'key' => 'currency',
                'group' => 'payment',
                'name' => 'العملة الافتراضية',
                'description' => 'العملة الافتراضية للموقع',
                'value' => 'USD',
                'type' => 'select',
                'options' => [
                    'SAR' => 'ريال سعودي',
                    'AED' => 'درهم إماراتي',
                    'KWD' => 'دينار كويتي',
                    'QAR' => 'ريال قطري',
                    'BHD' => 'دينار بحريني',
                    'OMR' => 'ريال عماني',
                    'USD' => 'دولار أمريكي',
                ],
                'is_public' => true,
                'is_required' => true,
                'sort_order' => 1,
            ],
            [
                'key' => 'payment_methods',
                'group' => 'payment',
                'name' => 'طرق الدفع المتاحة',
                'description' => 'طرق الدفع المتاحة في الموقع',
                'value' => json_encode(['credit_card', 'bank_transfer', 'paypal']),
                'type' => 'json',
                'is_public' => true,
                'is_required' => false,
                'sort_order' => 2,
            ],
            [
                'key' => 'min_order_amount',
                'group' => 'payment',
                'name' => 'الحد الأدنى للطلب',
                'description' => 'الحد الأدنى لقيمة الطلب',
                'value' => '10',
                'type' => 'number',
                'is_public' => true,
                'is_required' => false,
                'sort_order' => 3,
            ],

            // Email Settings
            [
                'key' => 'smtp_host',
                'group' => 'email',
                'name' => 'SMTP Host',
                'description' => 'خادم SMTP لإرسال البريد الإلكتروني',
                'value' => null,
                'type' => 'text',
                'is_public' => false,
                'is_required' => false,
                'sort_order' => 1,
            ],
            [
                'key' => 'smtp_port',
                'group' => 'email',
                'name' => 'SMTP Port',
                'description' => 'منفذ SMTP',
                'value' => '587',
                'type' => 'number',
                'is_public' => false,
                'is_required' => false,
                'sort_order' => 2,
            ],
            [
                'key' => 'smtp_username',
                'group' => 'email',
                'name' => 'اسم مستخدم SMTP',
                'description' => 'اسم المستخدم لخادم SMTP',
                'value' => null,
                'type' => 'text',
                'is_public' => false,
                'is_required' => false,
                'sort_order' => 3,
            ],
            [
                'key' => 'smtp_password',
                'group' => 'email',
                'name' => 'كلمة مرور SMTP',
                'description' => 'كلمة المرور لخادم SMTP',
                'value' => null,
                'type' => 'text',
                'is_public' => false,
                'is_required' => false,
                'sort_order' => 4,
            ],

            // Security Settings
            [
                'key' => 'max_login_attempts',
                'group' => 'security',
                'name' => 'الحد الأقصى لمحاولات تسجيل الدخول',
                'description' => 'عدد المحاولات المسموحة قبل حظر الحساب',
                'value' => '5',
                'type' => 'number',
                'is_public' => false,
                'is_required' => true,
                'sort_order' => 1,
            ],
            [
                'key' => 'session_timeout',
                'group' => 'security',
                'name' => 'مهلة انتهاء الجلسة (بالدقائق)',
                'description' => 'مدة انتهاء الجلسة بالدقائق',
                'value' => '120',
                'type' => 'number',
                'is_public' => false,
                'is_required' => true,
                'sort_order' => 2,
            ],
            [
                'key' => 'password_min_length',
                'group' => 'security',
                'name' => 'الحد الأدنى لطول كلمة المرور',
                'description' => 'الحد الأدنى لعدد أحرف كلمة المرور',
                'value' => '8',
                'type' => 'number',
                'is_public' => false,
                'is_required' => true,
                'sort_order' => 3,
            ],

            // Appearance Settings
            [
                'key' => 'primary_color',
                'group' => 'appearance',
                'name' => 'اللون الأساسي',
                'description' => 'اللون الأساسي للموقع',
                'value' => '#6f42c1',
                'type' => 'text',
                'is_public' => true,
                'is_required' => false,
                'sort_order' => 1,
            ],
            [
                'key' => 'secondary_color',
                'group' => 'appearance',
                'name' => 'اللون الثانوي',
                'description' => 'اللون الثانوي للموقع',
                'value' => '#6c757d',
                'type' => 'text',
                'is_public' => true,
                'is_required' => false,
                'sort_order' => 2,
            ],
            [
                'key' => 'font_family',
                'group' => 'appearance',
                'name' => 'عائلة الخط',
                'description' => 'عائلة الخط المستخدمة في الموقع',
                'value' => 'Cairo',
                'type' => 'select',
                'options' => [
                    'Cairo' => 'Cairo',
                    'Tajawal' => 'Tajawal',
                    'Amiri' => 'Amiri',
                    'Arial' => 'Arial',
                    'Helvetica' => 'Helvetica',
                ],
                'is_public' => true,
                'is_required' => false,
                'sort_order' => 3,
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }

    private function getDefaultPrivacyPolicy(): string
    {
        return "نحن في متجر البطاقات الرقمية نحترم خصوصيتك ونلتزم بحماية معلوماتك الشخصية.

1. جمع المعلومات:
- نجمع المعلومات التي تقدمها لنا عند التسجيل أو الشراء
- نستخدم ملفات تعريف الارتباط لتحسين تجربتك
- نجمع معلومات تقنية عن جهازك ومتصفحك

2. استخدام المعلومات:
- نستخدم معلوماتك لتقديم الخدمات المطلوبة
- نرسل لك تحديثات حول طلباتك
- نحسن خدماتنا بناءً على ملاحظاتك

3. حماية المعلومات:
- نستخدم تقنيات تشفير متقدمة لحماية بياناتك
- لا نشارك معلوماتك مع أطراف ثالثة دون موافقتك
- نحتفظ ببياناتك فقط للمدة المطلوبة

4. حقوقك:
- يمكنك طلب حذف حسابك في أي وقت
- يمكنك تحديث معلوماتك الشخصية
- يمكنك إلغاء الاشتراك في الرسائل التسويقية

للاستفسارات حول سياسة الخصوصية، يرجى التواصل معنا على: info@example.com";
    }

    private function getDefaultTermsOfService(): string
    {
        return "شروط الاستخدام - متجر البطاقات الرقمية

1. قبول الشروط:
باستخدامك لهذا الموقع، فإنك توافق على الالتزام بشروط الاستخدام هذه.

2. الخدمات المقدمة:
- نقدم بطاقات رقمية وهدايا إلكترونية
- جميع المنتجات رقمية وتسلم فورياً
- نحتفظ بالحق في تعديل أو إيقاف الخدمات

3. حسابات المستخدمين:
- يجب أن تكون المعلومات المقدمة صحيحة ومحدثة
- أنت مسؤول عن الحفاظ على سرية حسابك
- يجب أن تكون عمرك 18 عاماً أو أكثر

4. المدفوعات:
- جميع المدفوعات تتم عبر بوابات دفع آمنة
- الأسعار قابلة للتغيير دون إشعار مسبق
- لا نتحمل مسؤولية أخطاء البنوك أو أنظمة الدفع

5. الاسترداد والإلغاء:
- يمكن إلغاء الطلبات خلال 30 دقيقة من الشراء
- البطاقات المستخدمة لا يمكن استردادها
- الاسترداد يتم خلال 5-7 أيام عمل

6. المسؤولية:
- نقدم المنتجات كما هي دون ضمانات إضافية
- لا نتحمل مسؤولية الاستخدام غير القانوني للمنتجات
- الحد الأقصى للمسؤولية هو قيمة الطلب

7. تعديل الشروط:
نحتفظ بالحق في تعديل هذه الشروط في أي وقت.

للاستفسارات، يرجى التواصل معنا على: info@example.com";
    }

    private function getDefaultRefundPolicy(): string
    {
        return "سياسة الاسترداد - متجر البطاقات الرقمية

1. شروط الاسترداد:
- يمكن طلب الاسترداد خلال 30 دقيقة من الشراء
- يجب أن تكون البطاقة غير مستخدمة
- يجب تقديم رقم الطلب والمعلومات المطلوبة

2. حالات الاسترداد:
- خطأ في معلومات البطاقة المقدمة
- مشكلة تقنية في التسليم
- عدم توافق البطاقة مع الجهاز المطلوب

3. حالات عدم الاسترداد:
- البطاقة مستخدمة أو مفعلة
- انتهاء صلاحية البطاقة
- الاستخدام غير القانوني للمنتج

4. إجراءات الاسترداد:
- تقديم طلب الاسترداد عبر نموذج التواصل
- مراجعة الطلب خلال 24 ساعة
- معالجة الاسترداد خلال 5-7 أيام عمل

5. طريقة الاسترداد:
- يتم الاسترداد بنفس طريقة الدفع الأصلية
- قد تستغرق عملية الاسترداد 5-10 أيام عمل
- نتحمل رسوم الاسترداد

6. الاستثناءات:
- البطاقات المجانية أو المقدمة كهدايا
- الطلبات المخفضة بأكثر من 50%
- المنتجات المخصصة أو المصنوعة حسب الطلب

للاستفسارات حول الاسترداد، يرجى التواصل معنا على: info@example.com";
    }

    private function getDefaultShippingPolicy(): string
    {
        return "سياسة الشحن والتوصيل - متجر البطاقات الرقمية

1. طبيعة المنتجات:
- جميع منتجاتنا رقمية ولا تحتاج شحناً مادياً
- التسليم يتم فورياً عبر البريد الإلكتروني أو الرسائل النصية

2. أوقات التسليم:
- التسليم الفوري: خلال دقائق من تأكيد الدفع
- التسليم السريع: خلال ساعة واحدة
- التسليم العادي: خلال 24 ساعة

3. طرق التسليم:
- البريد الإلكتروني: للمنتجات الرقمية
- الرسائل النصية: لبطاقات الشحن
- رابط مباشر: للتحميلات الرقمية

4. متطلبات التسليم:
- عنوان بريد إلكتروني صحيح
- رقم هاتف صحيح للرسائل النصية
- اتصال بالإنترنت للوصول للمنتجات

5. مشاكل التسليم:
- في حالة عدم وصول المنتج، يرجى التواصل خلال 24 ساعة
- نتحمل مسؤولية إعادة الإرسال في حالة الخطأ من جانبنا
- لا نتحمل مسؤولية مشاكل البريد الإلكتروني أو الرسائل النصية

6. الدعم الفني:
- متاح على مدار الساعة
- رد سريع خلال ساعة واحدة
- حلول فورية للمشاكل التقنية

7. الضمان:
- ضمان وصول المنتج خلال 24 ساعة
- ضمان صحة المعلومات المقدمة
- ضمان الدعم الفني المستمر

للاستفسارات حول التسليم، يرجى التواصل معنا على: info@example.com";
    }
}
