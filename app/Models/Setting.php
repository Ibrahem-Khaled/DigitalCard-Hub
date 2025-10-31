<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'group',
        'name',
        'description',
        'value',
        'type',
        'options',
        'validation_rules',
        'is_public',
        'is_required',
        'sort_order',
    ];

    protected $casts = [
        'options' => 'array',
        'is_public' => 'boolean',
        'is_required' => 'boolean',
    ];

    // Scopes
    public function scopeByGroup($query, $group)
    {
        return $query->where('group', $group);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // Static methods for easy access
    public static function get($key, $default = null)
    {
        return Cache::remember("setting.{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    public static function set($key, $value)
    {
        $setting = static::where('key', $key)->first();

        if ($setting) {
            $setting->update(['value' => $value]);
        } else {
            $setting = static::create([
                'key' => $key,
                'name' => ucfirst(str_replace('_', ' ', $key)),
                'value' => $value,
                'group' => 'general',
                'type' => 'text',
            ]);
        }

        Cache::forget("setting.{$key}");
        return $setting;
    }

    public static function getGroup($group)
    {
        return Cache::remember("settings.group.{$group}", 3600, function () use ($group) {
            return static::byGroup($group)->ordered()->get();
        });
    }

    public static function getAllPublic()
    {
        return Cache::remember('settings.public', 3600, function () {
            return static::public()->ordered()->get();
        });
    }

    public static function getAllAsArray()
    {
        return Cache::remember('settings.all', 3600, function () {
            return static::all()->pluck('value', 'key')->toArray();
        });
    }

    public static function bulkUpdate(array $settings)
    {
        foreach ($settings as $key => $value) {
            static::set($key, $value);
        }

        Cache::flush();
    }

    // Accessors
    public function getFormattedValueAttribute()
    {
        switch ($this->type) {
            case 'boolean':
                return (bool) $this->value;
            case 'number':
                return is_numeric($this->value) ? (float) $this->value : 0;
            case 'json':
                return json_decode($this->value, true);
            default:
                return $this->value;
        }
    }

    public function getTypeTextAttribute()
    {
        return match($this->type) {
            'text' => 'نص',
            'textarea' => 'نص طويل',
            'number' => 'رقم',
            'boolean' => 'نعم/لا',
            'select' => 'قائمة منسدلة',
            'file' => 'ملف',
            'email' => 'بريد إلكتروني',
            'url' => 'رابط',
            'json' => 'JSON',
            default => 'نص'
        };
    }

    public function getGroupTextAttribute()
    {
        return match($this->group) {
            'general' => 'عام',
            'site' => 'الموقع',
            'contact' => 'التواصل',
            'social' => 'وسائل التواصل',
            'seo' => 'SEO',
            'legal' => 'قانوني',
            'payment' => 'الدفع',
            'email' => 'البريد الإلكتروني',
            'security' => 'الأمان',
            'appearance' => 'المظهر',
            default => 'عام'
        };
    }

    // Methods
    public function getValidationRules()
    {
        if ($this->validation_rules) {
            return explode('|', $this->validation_rules);
        }

        $rules = [];

        if ($this->is_required) {
            $rules[] = 'required';
        }

        switch ($this->type) {
            case 'email':
                $rules[] = 'email';
                break;
            case 'url':
                $rules[] = 'url';
                break;
            case 'number':
                $rules[] = 'numeric';
                break;
            case 'boolean':
                $rules[] = 'boolean';
                break;
        }

        return $rules;
    }

    public function getFormattedValidationRules()
    {
        return implode('|', $this->getValidationRules());
    }

    public function isSelectType()
    {
        return $this->type === 'select';
    }

    public function isFileType()
    {
        return $this->type === 'file';
    }

    public function isBooleanType()
    {
        return $this->type === 'boolean';
    }

    public function isTextareaType()
    {
        return $this->type === 'textarea';
    }

    public function isJsonType()
    {
        return $this->type === 'json';
    }

    public function getSelectOptions()
    {
        if ($this->isSelectType() && $this->options) {
            return $this->options;
        }

        return [];
    }

    public function updateValue($value)
    {
        $this->update(['value' => $value]);
        Cache::forget("setting.{$this->key}");
        Cache::forget("settings.group.{$this->group}");
        Cache::forget('settings.public');
        Cache::forget('settings.all');
    }
}
