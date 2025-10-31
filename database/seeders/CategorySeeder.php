<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'بطاقات شحن الألعاب',
                'slug' => 'gaming-cards',
                'description' => 'بطاقات شحن للألعاب المختلفة مثل PUBG Mobile, Free Fire, Call of Duty, Fortnite وغيرها',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'بطاقات الشحن المحمول',
                'slug' => 'mobile-cards',
                'description' => 'بطاقات شحن للهواتف المحمولة لجميع الشبكات المحلية',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'بطاقات الإنترنت',
                'slug' => 'internet-cards',
                'description' => 'بطاقات الإنترنت والواي فاي للاستخدام المنزلي والمكتبي',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'بطاقات التطبيقات',
                'slug' => 'app-cards',
                'description' => 'بطاقات شحن للتطبيقات المختلفة مثل Netflix, Spotify, Apple Music',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'بطاقات الهدايا',
                'slug' => 'gift-cards',
                'description' => 'بطاقات هدايا للمتاجر الإلكترونية مثل Amazon, Google Play, App Store',
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'بطاقات الدفع',
                'slug' => 'payment-cards',
                'description' => 'بطاقات دفع رقمية للخدمات المالية والتحويلات',
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'name' => 'بطاقات التعليم',
                'slug' => 'education-cards',
                'description' => 'بطاقات للخدمات التعليمية والتدريبية عبر الإنترنت',
                'is_active' => true,
                'sort_order' => 7,
            ],
            [
                'name' => 'بطاقات الترفيه',
                'slug' => 'entertainment-cards',
                'description' => 'بطاقات للخدمات الترفيهية مثل الألعاب والسينما والرياضة',
                'is_active' => false, // غير نشطة حالياً
                'sort_order' => 8,
            ],
        ];

        foreach ($categories as $categoryData) {
            // إنشاء slug تلقائياً إذا لم يكن موجوداً
            if (!isset($categoryData['slug']) || empty($categoryData['slug'])) {
                $categoryData['slug'] = Str::slug($categoryData['name']);
            }

            Category::updateOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );
        }

        $this->command->info('تم إنشاء الفئات بنجاح!');
    }
}
