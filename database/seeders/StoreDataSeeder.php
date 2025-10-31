<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use App\Models\DigitalCard;
use App\Models\Coupon;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class StoreDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create categories
        $categories = [
            [
                'name' => 'بطاقات شحن الألعاب',
                'slug' => 'gaming-cards',
                'description' => 'بطاقات شحن للألعاب المختلفة مثل PUBG Mobile, Free Fire, Call of Duty',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'بطاقات الشحن المحمول',
                'slug' => 'mobile-cards',
                'description' => 'بطاقات شحن للهواتف المحمولة',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'بطاقات الإنترنت',
                'slug' => 'internet-cards',
                'description' => 'بطاقات الإنترنت والواي فاي',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'بطاقات التطبيقات',
                'slug' => 'app-cards',
                'description' => 'بطاقات شحن للتطبيقات المختلفة',
                'is_active' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($categories as $categoryData) {
            Category::updateOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );
        }

        // Create products
        $gamingCategory = Category::where('slug', 'gaming-cards')->first();
        $mobileCategory = Category::where('slug', 'mobile-cards')->first();

        $products = [
            [
                'name' => 'بطاقة شحن PUBG Mobile - 60 UC',
                'slug' => 'pubg-mobile-60-uc',
                'description' => 'بطاقة شحن PUBG Mobile بقيمة 60 UC للاستخدام في اللعبة',
                'short_description' => '60 UC لـ PUBG Mobile',
                'sku' => 'PUBG-60-UC',
                'price' => 15.00,
                'category_id' => $gamingCategory->id,
                'brand' => 'PUBG Mobile',
                'is_digital' => true,
                'is_active' => true,
                'is_featured' => true,
                'loyalty_points_earn' => 15, // 15 نقطة لكل دولار
                'loyalty_points_cost' => 0, // لا يمكن شراؤها بنقاط
                'card_type' => 'gaming',
                'card_provider' => 'pubg',
                'card_region' => 'Global',
                'card_denominations' => ['60'],
                'is_instant_delivery' => true,
                'delivery_instructions' => 'سيتم إرسال الكود عبر البريد الإلكتروني فوراً',
                'tags' => ['pubg', 'gaming', 'mobile', 'uc'],
            ],
            [
                'name' => 'بطاقة شحن Free Fire - 100 Diamonds',
                'slug' => 'free-fire-100-diamonds',
                'description' => 'بطاقة شحن Free Fire بقيمة 100 Diamonds للاستخدام في اللعبة',
                'short_description' => '100 Diamonds لـ Free Fire',
                'sku' => 'FF-100-DIA',
                'price' => 12.00,
                'category_id' => $gamingCategory->id,
                'brand' => 'Free Fire',
                'is_digital' => true,
                'is_active' => true,
                'is_featured' => true,
                'loyalty_points_earn' => 12, // 12 نقطة لكل دولار
                'loyalty_points_cost' => 0, // لا يمكن شراؤها بنقاط
                'card_type' => 'gaming',
                'card_provider' => 'free-fire',
                'card_region' => 'Global',
                'card_denominations' => ['100'],
                'is_instant_delivery' => true,
                'delivery_instructions' => 'سيتم إرسال الكود عبر البريد الإلكتروني فوراً',
                'tags' => ['free-fire', 'gaming', 'mobile', 'diamonds'],
            ],
            [
                'name' => 'بطاقة شحن موبينيل - 50 جنيه',
                'slug' => 'mobinil-50-egp',
                'description' => 'بطاقة شحن موبينيل بقيمة 50 جنيه مصري',
                'short_description' => '50 جنيه موبينيل',
                'sku' => 'MOB-50-EGP',
                'price' => 50.00,
                'category_id' => $mobileCategory->id,
                'brand' => 'Mobinil',
                'is_digital' => true,
                'is_active' => true,
                'is_featured' => false,
                'loyalty_points_earn' => 50, // 50 نقطة لكل جنيه
                'loyalty_points_cost' => 0, // لا يمكن شراؤها بنقاط
                'card_type' => 'mobile',
                'card_provider' => 'mobinil',
                'card_region' => 'Egypt',
                'card_denominations' => ['50'],
                'is_instant_delivery' => true,
                'delivery_instructions' => 'سيتم إرسال الكود عبر البريد الإلكتروني فوراً',
                'tags' => ['mobinil', 'mobile', 'egypt', 'recharge'],
            ],
            [
                'name' => 'بطاقة هدايا أمازون - 25 دولار',
                'slug' => 'amazon-gift-card-25-usd',
                'description' => 'بطاقة هدايا أمازون بقيمة 25 دولار أمريكي',
                'short_description' => '25 دولار أمازون',
                'sku' => 'AMZ-25-USD',
                'price' => 25.00,
                'category_id' => $gamingCategory->id, // يمكن تغييرها لتصنيف منفصل
                'brand' => 'Amazon',
                'is_digital' => true,
                'is_active' => true,
                'is_featured' => true,
                'loyalty_points_earn' => 25, // 25 نقطة لكل دولار
                'loyalty_points_cost' => 2500, // يمكن شراؤها بـ 2500 نقطة
                'card_type' => 'gift_card',
                'card_provider' => 'amazon',
                'card_region' => 'US',
                'card_denominations' => ['25', '50', '100', '200'],
                'is_instant_delivery' => true,
                'delivery_instructions' => 'سيتم إرسال الكود عبر البريد الإلكتروني فوراً',
                'tags' => ['amazon', 'gift-card', 'shopping', 'usd'],
            ],
        ];

        foreach ($products as $productData) {
            Product::updateOrCreate(
                ['sku' => $productData['sku']],
                $productData
            );
        }

        // Create digital cards for products
        $pubgProduct = Product::where('sku', 'PUBG-60-UC')->first();
        $freeFireProduct = Product::where('sku', 'FF-100-DIA')->first();

        // Create PUBG cards
        for ($i = 1; $i <= 50; $i++) {
            $cardCode = 'PUBG' . str_pad($i, 6, '0', STR_PAD_LEFT);
            DigitalCard::updateOrCreate(
                [
                    'product_id' => $pubgProduct->id,
                    'card_code' => $cardCode
                ],
                [
                    'card_pin' => str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT),
                    'value' => 60,
                    'currency' => 'USD',
                    'status' => 'active',
                ]
            );
        }

        // Create Free Fire cards
        for ($i = 1; $i <= 75; $i++) {
            $cardCode = 'FF' . str_pad($i, 6, '0', STR_PAD_LEFT);
            DigitalCard::updateOrCreate(
                [
                    'product_id' => $freeFireProduct->id,
                    'card_code' => $cardCode
                ],
                [
                    'card_pin' => str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT),
                    'value' => 100,
                    'currency' => 'USD',
                    'status' => 'active',
                ]
            );
        }

        // Create coupons
        $coupons = [
            [
                'code' => 'WELCOME10',
                'name' => 'كوبون ترحيب',
                'description' => 'خصم 10% على أول طلب',
                'type' => 'percentage',
                'value' => 10,
                'minimum_amount' => 20,
                'maximum_discount' => 50,
                'usage_limit' => 1000,
                'is_active' => true,
                'starts_at' => now(),
                'expires_at' => now()->addMonths(3),
                'first_time_only' => true,
            ],
            [
                'code' => 'SAVE20',
                'name' => 'خصم 20 جنيه',
                'description' => 'خصم ثابت 20 جنيه على الطلبات',
                'type' => 'fixed',
                'value' => 20,
                'minimum_amount' => 100,
                'usage_limit' => 500,
                'is_active' => true,
                'starts_at' => now(),
                'expires_at' => now()->addMonths(6),
            ],
        ];

        foreach ($coupons as $couponData) {
            Coupon::updateOrCreate(
                ['code' => $couponData['code']],
                $couponData
            );
        }

        // Create test users
        $users = [
            [
                'first_name' => 'أحمد',
                'last_name' => 'محمد',
                'email' => 'ahmed@example.com',
                'phone' => '01234567890',
                'password' => Hash::make('password'),
                'is_active' => true,
            ],
            [
                'first_name' => 'فاطمة',
                'last_name' => 'علي',
                'email' => 'fatima@example.com',
                'phone' => '01234567891',
                'password' => Hash::make('password'),
                'is_active' => true,
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        // Create AI knowledge base
        $knowledgeBase = [
            [
                'title' => 'كيفية استخدام بطاقات الشحن',
                'content' => 'بعد شراء البطاقة، ستحصل على كود الشحن الذي يمكنك استخدامه مباشرة في التطبيق أو اللعبة المطلوبة.',
                'category' => 'general',
                'tags' => ['شحن', 'استخدام', 'بطاقات'],
                'priority' => 10,
                'is_active' => true,
            ],
            [
                'title' => 'مشاكل في تسليم البطاقات',
                'content' => 'إذا لم تستلم البطاقة خلال 5 دقائق، يرجى التواصل مع خدمة العملاء مع رقم الطلب.',
                'category' => 'support',
                'tags' => ['تسليم', 'مشاكل', 'دعم'],
                'priority' => 8,
                'is_active' => true,
            ],
            [
                'title' => 'سياسة الاسترداد',
                'content' => 'يمكن استرداد المبلغ خلال 24 ساعة من الشراء إذا لم يتم استخدام البطاقة.',
                'category' => 'refund',
                'tags' => ['استرداد', 'سياسة', 'استرجاع'],
                'priority' => 7,
                'is_active' => true,
            ],
        ];

        foreach ($knowledgeBase as $kbData) {
            \App\Models\AIKnowledgeBase::updateOrCreate(
                ['title' => $kbData['title']],
                $kbData
            );
        }

        $this->command->info('تم إنشاء البيانات التجريبية بنجاح!');
    }
}

