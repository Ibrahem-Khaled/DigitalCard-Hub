<?php

namespace Database\Seeders;

use App\Models\Slider;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class SliderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء مجلد السلايدرات إذا لم يكن موجوداً
        if (!Storage::disk('public')->exists('sliders')) {
            Storage::disk('public')->makeDirectory('sliders');
        }

        $sliders = [
            [
                'title' => 'عروض رائعة على البطاقات الرقمية',
                'description' => 'احصل على أفضل العروض والخصومات على جميع البطاقات الرقمية',
                'image' => 'sliders/slider-1.jpg', // سيتم إنشاء صورة وهمية
                'button_text' => 'تسوق الآن',
                'button_url' => route('products.index'),
                'sort_order' => 1,
                'is_active' => true,
                'position' => 'homepage',
                'settings' => [
                    'animation_type' => 'fade',
                    'animation_duration' => 3,
                ],
                'starts_at' => now(),
                'ends_at' => now()->addMonths(3),
            ],
            [
                'title' => 'بطاقات جيم جديدة',
                'description' => 'اكتشف أحدث البطاقات للألعاب المفضلة لديك',
                'image' => 'sliders/slider-2.jpg',
                'button_text' => 'استكشف المجموعة',
                'button_url' => route('products.index', ['category' => 'gaming']),
                'sort_order' => 2,
                'is_active' => true,
                'position' => 'homepage',
                'settings' => [
                    'animation_type' => 'slide',
                    'animation_duration' => 4,
                ],
                'starts_at' => now(),
                'ends_at' => now()->addMonths(2),
            ],
            [
                'title' => 'خصم 50% على جميع البطاقات',
                'description' => 'عرض محدود لفترة قصيرة - لا تفوت الفرصة!',
                'image' => 'sliders/slider-3.jpg',
                'button_text' => 'احصل على الخصم',
                'button_url' => route('products.index', ['sale' => true]),
                'sort_order' => 3,
                'is_active' => true,
                'position' => 'homepage',
                'settings' => [
                    'animation_type' => 'zoom',
                    'animation_duration' => 2.5,
                ],
                'starts_at' => now(),
                'ends_at' => now()->addWeeks(2),
            ],
            [
                'title' => 'بطاقات التطبيقات الرقمية',
                'description' => 'احصل على بطاقات iTunes، Google Play، Netflix والمزيد',
                'image' => 'sliders/slider-4.jpg',
                'button_text' => 'تصفح البطاقات',
                'button_url' => route('products.index', ['category' => 'apps']),
                'sort_order' => 4,
                'is_active' => true,
                'position' => 'homepage',
                'settings' => [
                    'animation_type' => 'fade',
                    'animation_duration' => 3.5,
                ],
                'starts_at' => now(),
                'ends_at' => now()->addMonths(6),
            ],
            [
                'title' => 'بطاقات الهدايا المثالية',
                'description' => 'اختر الهدية المثالية لأحبائك',
                'image' => 'sliders/slider-5.jpg',
                'button_text' => 'اختر هدية',
                'button_url' => route('products.index', ['category' => 'gift-cards']),
                'sort_order' => 5,
                'is_active' => true,
                'position' => 'homepage',
                'settings' => [
                    'animation_type' => 'slide',
                    'animation_duration' => 3,
                ],
                'starts_at' => now(),
                'ends_at' => now()->addMonths(12),
            ],
        ];

        foreach ($sliders as $sliderData) {
            // إنشاء صورة وهمية بسيطة
            $this->createPlaceholderImage($sliderData['image']);

            Slider::create($sliderData);
        }
    }

    /**
     * إنشاء صورة وهمية للسلايدر
     */
    private function createPlaceholderImage(string $imagePath): void
    {
        // إنشاء صورة بسيطة باستخدام GD
        $width = 1200;
        $height = 600;

        $image = imagecreatetruecolor($width, $height);

        // ألوان متدرجة
        $colors = [
            [102, 126, 234], // أزرق
            [118, 75, 162],  // بنفسجي
            [255, 107, 107], // أحمر
            [78, 205, 196],  // تركوازي
            [255, 193, 7],   // أصفر
        ];

        $colorIndex = array_rand($colors);
        $color1 = $colors[$colorIndex];
        $color2 = $colors[($colorIndex + 1) % count($colors)];

        // إنشاء تدرج لوني
        for ($i = 0; $i < $height; $i++) {
            $ratio = $i / $height;
            $r = $color1[0] + ($color2[0] - $color1[0]) * $ratio;
            $g = $color1[1] + ($color2[1] - $color1[1]) * $ratio;
            $b = $color1[2] + ($color2[2] - $color1[2]) * $ratio;

            $color = imagecolorallocate($image, $r, $g, $b);
            imageline($image, 0, $i, $width, $i, $color);
        }

        // حفظ الصورة
        $fullPath = storage_path('app/public/' . $imagePath);
        $directory = dirname($fullPath);

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        imagejpeg($image, $fullPath, 80);
        imagedestroy($image);
    }
}
