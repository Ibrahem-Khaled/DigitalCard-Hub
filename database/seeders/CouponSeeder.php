<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // مسح البيانات الموجودة
        DB::table('coupon_usages')->delete();
        DB::table('coupons')->delete();

        // الحصول على المستخدمين والمنتجات والفئات
        $users = User::all();
        $products = Product::all();
        $categories = Category::all();

        if ($users->isEmpty()) {
            $this->command->warn('لا توجد مستخدمين. يرجى تشغيل UserSeeder أولاً.');
            return;
        }

        // إنشاء كوبونات متنوعة
        $this->createPercentageCoupons($users, $products, $categories);
        $this->createFixedCoupons($users, $products, $categories);
        $this->createSpecialCoupons($users, $products, $categories);
        $this->createExpiredCoupons($users, $products, $categories);

        // إنشاء استخدامات للكوبونات
        $this->createCouponUsages();

        $this->command->info('تم إنشاء بيانات الكوبونات بنجاح!');
    }

    /**
     * إنشاء كوبونات نسبة مئوية
     */
    private function createPercentageCoupons($users, $products, $categories)
    {
        $percentageCoupons = [
            [
                'code' => 'WELCOME20',
                'name' => 'خصم ترحيبي 20%',
                'description' => 'خصم ترحيبي للعملاء الجدد',
                'value' => 20,
                'minimum_amount' => 50,
                'maximum_discount' => 100,
                'usage_limit' => 1000,
                'user_limit' => 1,
                'first_time_only' => true,
                'starts_at' => now(),
                'expires_at' => now()->addMonths(3),
            ],
            [
                'code' => 'SUMMER15',
                'name' => 'خصم صيفي 15%',
                'description' => 'خصم خاص على جميع المنتجات',
                'value' => 15,
                'minimum_amount' => 100,
                'maximum_discount' => 200,
                'usage_limit' => 500,
                'user_limit' => 2,
                'starts_at' => now(),
                'expires_at' => now()->addMonths(2),
            ],
            [
                'code' => 'VIP10',
                'name' => 'خصم VIP 10%',
                'description' => 'خصم حصري للعملاء المميزين',
                'value' => 10,
                'minimum_amount' => 200,
                'maximum_discount' => 500,
                'usage_limit' => 100,
                'user_limit' => 5,
                'applicable_users' => $users->take(3)->pluck('id')->toArray(),
                'starts_at' => now(),
                'expires_at' => now()->addYear(),
            ],
        ];

        foreach ($percentageCoupons as $couponData) {
            Coupon::create($couponData);
        }
    }

    /**
     * إنشاء كوبونات مبلغ ثابت
     */
    private function createFixedCoupons($users, $products, $categories)
    {
        $fixedCoupons = [
            [
                'code' => 'SAVE50',
                'name' => 'وفر 50 دولار',
                'description' => 'خصم ثابت 50 دولار على الطلبات الكبيرة',
                'type' => 'fixed',
                'value' => 50,
                'minimum_amount' => 300,
                'usage_limit' => 200,
                'user_limit' => 1,
                'starts_at' => now(),
                'expires_at' => now()->addMonths(6),
            ],
            [
                'code' => 'NEWUSER25',
                'name' => 'خصم 25 دولار للعملاء الجدد',
                'description' => 'خصم ترحيبي للعملاء الجدد',
                'type' => 'fixed',
                'value' => 25,
                'minimum_amount' => 100,
                'usage_limit' => 500,
                'user_limit' => 1,
                'first_time_only' => true,
                'starts_at' => now(),
                'expires_at' => now()->addMonths(3),
            ],
            [
                'code' => 'BULK100',
                'name' => 'خصم 100 دولار للطلبات الكبيرة',
                'description' => 'خصم خاص للطلبات التي تزيد عن 500 دولار',
                'type' => 'fixed',
                'value' => 100,
                'minimum_amount' => 500,
                'usage_limit' => 50,
                'user_limit' => 2,
                'starts_at' => now(),
                'expires_at' => now()->addYear(),
            ],
        ];

        foreach ($fixedCoupons as $couponData) {
            Coupon::create($couponData);
        }
    }

    /**
     * إنشاء كوبونات خاصة
     */
    private function createSpecialCoupons($users, $products, $categories)
    {
        $specialCoupons = [
            [
                'code' => 'PRODUCTS20',
                'name' => 'خصم على منتجات محددة',
                'description' => 'خصم 20% على منتجات محددة',
                'value' => 20,
                'minimum_amount' => 75,
                'usage_limit' => 300,
                'user_limit' => 3,
                'applicable_products' => $products->take(2)->pluck('id')->toArray(),
                'starts_at' => now(),
                'expires_at' => now()->addMonths(4),
            ],
            [
                'code' => 'CATEGORY15',
                'name' => 'خصم على فئة محددة',
                'description' => 'خصم 15% على فئة محددة',
                'value' => 15,
                'minimum_amount' => 50,
                'usage_limit' => 200,
                'user_limit' => 2,
                'applicable_categories' => $categories->take(1)->pluck('id')->toArray(),
                'starts_at' => now(),
                'expires_at' => now()->addMonths(3),
            ],
            [
                'code' => 'STACKABLE10',
                'name' => 'كوبون قابل للتجميع',
                'description' => 'كوبون قابل للتجميع مع كوبونات أخرى',
                'value' => 10,
                'minimum_amount' => 25,
                'usage_limit' => 1000,
                'user_limit' => 10,
                'stackable' => true,
                'starts_at' => now(),
                'expires_at' => now()->addYear(),
            ],
        ];

        foreach ($specialCoupons as $couponData) {
            Coupon::create($couponData);
        }
    }

    /**
     * إنشاء كوبونات منتهية الصلاحية
     */
    private function createExpiredCoupons($users, $products, $categories)
    {
        $expiredCoupons = [
            [
                'code' => 'EXPIRED30',
                'name' => 'كوبون منتهي الصلاحية',
                'description' => 'كوبون انتهت صلاحيته',
                'value' => 30,
                'minimum_amount' => 100,
                'usage_limit' => 100,
                'user_limit' => 1,
                'starts_at' => now()->subMonths(2),
                'expires_at' => now()->subMonth(),
                'is_active' => true,
            ],
            [
                'code' => 'INACTIVE25',
                'name' => 'كوبون معطل',
                'description' => 'كوبون معطل مؤقتاً',
                'value' => 25,
                'minimum_amount' => 50,
                'usage_limit' => 200,
                'user_limit' => 1,
                'starts_at' => now(),
                'expires_at' => now()->addMonths(6),
                'is_active' => false,
            ],
        ];

        foreach ($expiredCoupons as $couponData) {
            Coupon::create($couponData);
        }
    }

    /**
     * إنشاء استخدامات الكوبونات
     */
    private function createCouponUsages()
    {
        $coupons = Coupon::where('is_active', true)->get();
        $users = User::all();

        if ($coupons->isEmpty() || $users->isEmpty()) {
            return;
        }

        // إنشاء استخدامات عشوائية
        for ($i = 0; $i < 50; $i++) {
            $coupon = $coupons->random();
            $user = $users->random();

            // حساب مبلغ الخصم
            $cartTotal = rand(50, 500);
            $discountAmount = $coupon->calculateDiscount($cartTotal);

            if ($discountAmount > 0) {
                CouponUsage::create([
                    'coupon_id' => $coupon->id,
                    'user_id' => $user->id,
                    'discount_amount' => $discountAmount,
                    'used_at' => now()->subDays(rand(1, 90)),
                    'ip_address' => '192.168.1.' . rand(1, 255),
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                ]);

                // تحديث عدد الاستخدامات
                $coupon->increment('used_count');
            }
        }
    }
}
