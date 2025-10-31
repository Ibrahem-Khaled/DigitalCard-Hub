<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class LoyaltySetting extends Model
{
    protected $fillable = [
        'setting_key',
        'setting_value',
        'setting_type',
        'description',
        'category',
        'is_active',
        'is_editable',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_editable' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Get setting value by key with caching.
     */
    public static function getValue(string $key, $default = null)
    {
        $cacheKey = "loyalty_setting_{$key}";

        return Cache::remember($cacheKey, 3600, function () use ($key, $default) {
            $setting = self::where('setting_key', $key)
                          ->where('is_active', true)
                          ->first();

            if (!$setting) {
                return $default;
            }

            return self::castValue($setting->setting_value, $setting->setting_type);
        });
    }

    /**
     * Set setting value and clear cache.
     */
    public static function setValue(string $key, $value, string $type = 'string', string $description = null, string $category = 'general'): self
    {
        $setting = self::updateOrCreate(
            ['setting_key' => $key],
            [
                'setting_value' => $value,
                'setting_type' => $type,
                'description' => $description,
                'category' => $category,
                'is_active' => true,
                'is_editable' => true,
            ]
        );

        // Clear cache
        Cache::forget("loyalty_setting_{$key}");

        return $setting;
    }

    /**
     * Get all settings by category.
     */
    public static function getByCategory(string $category)
    {
        return self::where('category', $category)
                   ->where('is_active', true)
                   ->orderBy('sort_order')
                   ->get();
    }

    /**
     * Get all settings as key-value array.
     */
    public static function getAllAsArray(): array
    {
        $cacheKey = 'loyalty_settings_all';

        return Cache::remember($cacheKey, 3600, function () {
            return self::where('is_active', true)
                       ->pluck('setting_value', 'setting_key')
                       ->map(function ($value, $key) {
                           $setting = self::where('setting_key', $key)->first();
                           return self::castValue($value, $setting->setting_type);
                       })
                       ->toArray();
        });
    }

    /**
     * Cast value based on type.
     */
    private static function castValue($value, string $type)
    {
        switch ($type) {
            case 'number':
            case 'decimal':
                return (float) $value;
            case 'integer':
                return (int) $value;
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'json':
                return json_decode($value, true);
            case 'array':
                return is_array($value) ? $value : explode(',', $value);
            default:
                return $value;
        }
    }

    /**
     * Clear all settings cache.
     */
    public static function clearCache(): void
    {
        $settings = self::where('is_active', true)->get();

        foreach ($settings as $setting) {
            Cache::forget("loyalty_setting_{$setting->setting_key}");
        }

        Cache::forget('loyalty_settings_all');
    }

    /**
     * Initialize default settings.
     */
    public static function initializeDefaults(): void
    {
        $defaultSettings = [
            // إعدادات القيم الأساسية
            [
                'key' => 'default_point_value_usd',
                'value' => '0.01',
                'type' => 'decimal',
                'description' => 'القيمة الافتراضية لكل نقطة بالدولار الأمريكي',
                'category' => 'values',
                'sort_order' => 1,
            ],
            [
                'key' => 'points_per_dollar',
                'value' => '100',
                'type' => 'integer',
                'description' => 'عدد النقاط المكتسبة لكل دولار من الشراء',
                'category' => 'values',
                'sort_order' => 2,
            ],
            [
                'key' => 'min_point_value_usd',
                'value' => '0.001',
                'type' => 'decimal',
                'description' => 'الحد الأدنى لقيمة النقطة بالدولار',
                'category' => 'values',
                'sort_order' => 3,
            ],
            [
                'key' => 'max_point_value_usd',
                'value' => '100',
                'type' => 'decimal',
                'description' => 'الحد الأقصى لقيمة النقطة بالدولار',
                'category' => 'values',
                'sort_order' => 4,
            ],

            // إعدادات انتهاء الصلاحية
            [
                'key' => 'default_expiry_days',
                'value' => '365',
                'type' => 'integer',
                'description' => 'عدد الأيام الافتراضي لانتهاء صلاحية النقاط',
                'category' => 'expiry',
                'sort_order' => 1,
            ],
            [
                'key' => 'max_expiry_days',
                'value' => '1095',
                'type' => 'integer',
                'description' => 'الحد الأقصى لأيام انتهاء الصلاحية (3 سنوات)',
                'category' => 'expiry',
                'sort_order' => 2,
            ],

            // إعدادات المكافآت
            [
                'key' => 'referral_bonus_points',
                'value' => '500',
                'type' => 'integer',
                'description' => 'نقاط المكافأة للإحالة',
                'category' => 'bonuses',
                'sort_order' => 1,
            ],
            [
                'key' => 'review_bonus_points',
                'value' => '50',
                'type' => 'integer',
                'description' => 'نقاط المكافأة لتقييم المنتج',
                'category' => 'bonuses',
                'sort_order' => 2,
            ],
            [
                'key' => 'birthday_bonus_points',
                'value' => '1000',
                'type' => 'integer',
                'description' => 'نقاط المكافأة لعيد الميلاد',
                'category' => 'bonuses',
                'sort_order' => 3,
            ],

            // إعدادات النظام
            [
                'key' => 'loyalty_system_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'تفعيل نظام نقاط الولاء',
                'category' => 'system',
                'sort_order' => 1,
            ],
            [
                'key' => 'auto_expire_points',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'انتهاء تلقائي للنقاط المنتهية الصلاحية',
                'category' => 'system',
                'sort_order' => 2,
            ],
            [
                'key' => 'allow_negative_points',
                'value' => 'false',
                'type' => 'boolean',
                'description' => 'السماح بالنقاط السالبة',
                'category' => 'system',
                'sort_order' => 3,
            ],
        ];

        foreach ($defaultSettings as $setting) {
            self::updateOrCreate(
                ['setting_key' => $setting['key']],
                [
                    'setting_value' => $setting['value'],
                    'setting_type' => $setting['type'],
                    'description' => $setting['description'],
                    'category' => $setting['category'],
                    'sort_order' => $setting['sort_order'],
                    'is_active' => true,
                    'is_editable' => true,
                ]
            );
        }
    }
}
