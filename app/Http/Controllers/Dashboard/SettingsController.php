<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        $group = $request->get('group', 'general');

        $settings = Setting::byGroup($group)->ordered()->get();
        $groups = Setting::select('group')->distinct()->orderBy('group')->pluck('group');

        // Group statistics
        $stats = [
            'total_settings' => Setting::count(),
            'public_settings' => Setting::public()->count(),
            'required_settings' => Setting::required()->count(),
            'groups_count' => $groups->count(),
        ];

        return view('dashboard.settings.index', compact('settings', 'groups', 'group', 'stats'));
    }

    public function update(Request $request)
    {
        $settings = $request->except(['_token', '_method']);

        $validationRules = [];
        $customMessages = [];

        // Build validation rules for all settings
        foreach ($settings as $key => $value) {
            $setting = Setting::where('key', $key)->first();

            if ($setting) {
                $rules = $setting->getValidationRules();
                if (!empty($rules)) {
                    $validationRules[$key] = implode('|', $rules);
                }

                // Custom messages
                if ($setting->is_required) {
                    $customMessages["{$key}.required"] = "حقل {$setting->name} مطلوب";
                }
            }
        }

        $validator = Validator::make($settings, $validationRules, $customMessages);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Update settings
        foreach ($settings as $key => $value) {
            $setting = Setting::where('key', $key)->first();

            if ($setting) {
                // Handle file uploads
                if ($setting->isFileType() && $request->hasFile($key)) {
                    $file = $request->file($key);
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('settings', $filename, 'public');

                    // Delete old file if exists
                    if ($setting->value) {
                        Storage::disk('public')->delete($setting->value);
                    }

                    $setting->updateValue($path);
                } else {
                    $setting->updateValue($value);
                }
            }
        }

        return redirect()->back()->with('success', 'تم تحديث الإعدادات بنجاح');
    }

    public function create()
    {
        $groups = [
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
        ];

        $types = [
            'text' => 'نص',
            'textarea' => 'نص طويل',
            'number' => 'رقم',
            'boolean' => 'نعم/لا',
            'select' => 'قائمة منسدلة',
            'file' => 'ملف',
            'email' => 'بريد إلكتروني',
            'url' => 'رابط',
            'json' => 'JSON',
        ];

        return view('dashboard.settings.create', compact('groups', 'types'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'key' => 'required|string|max:255|unique:settings,key',
            'group' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'value' => 'nullable|string',
            'type' => 'required|in:text,textarea,number,boolean,select,file,email,url,json',
            'options' => 'nullable|array',
            'validation_rules' => 'nullable|string',
            'is_public' => 'boolean',
            'is_required' => 'boolean',
            'sort_order' => 'integer|min:0',
        ], [
            'key.required' => 'مفتاح الإعداد مطلوب',
            'key.unique' => 'مفتاح الإعداد موجود مسبقاً',
            'name.required' => 'اسم الإعداد مطلوب',
            'type.required' => 'نوع الإعداد مطلوب',
        ]);

        $setting = Setting::create($request->all());

        Cache::flush();

        return redirect()->route('dashboard.settings.index', ['group' => $setting->group])
            ->with('success', 'تم إنشاء الإعداد بنجاح');
    }

    public function edit(Setting $setting)
    {
        $groups = [
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
        ];

        $types = [
            'text' => 'نص',
            'textarea' => 'نص طويل',
            'number' => 'رقم',
            'boolean' => 'نعم/لا',
            'select' => 'قائمة منسدلة',
            'file' => 'ملف',
            'email' => 'بريد إلكتروني',
            'url' => 'رابط',
            'json' => 'JSON',
        ];

        return view('dashboard.settings.edit', compact('setting', 'groups', 'types'));
    }

    public function updateSetting(Request $request, Setting $setting)
    {
        $request->validate([
            'key' => 'required|string|max:255|unique:settings,key,' . $setting->id,
            'group' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'value' => 'nullable|string',
            'type' => 'required|in:text,textarea,number,boolean,select,file,email,url,json',
            'options' => 'nullable|array',
            'validation_rules' => 'nullable|string',
            'is_public' => 'boolean',
            'is_required' => 'boolean',
            'sort_order' => 'integer|min:0',
        ], [
            'key.required' => 'مفتاح الإعداد مطلوب',
            'key.unique' => 'مفتاح الإعداد موجود مسبقاً',
            'name.required' => 'اسم الإعداد مطلوب',
            'type.required' => 'نوع الإعداد مطلوب',
        ]);

        $setting->update($request->all());

        Cache::flush();

        return redirect()->route('dashboard.settings.index', ['group' => $setting->group])
            ->with('success', 'تم تحديث الإعداد بنجاح');
    }

    public function destroy(Setting $setting)
    {
        // Don't allow deletion of required settings
        if ($setting->is_required) {
            return redirect()->back()->with('error', 'لا يمكن حذف الإعدادات المطلوبة');
        }

        // Delete file if exists
        if ($setting->isFileType() && $setting->value) {
            Storage::disk('public')->delete($setting->value);
        }

        $setting->delete();

        Cache::flush();

        return redirect()->back()->with('success', 'تم حذف الإعداد بنجاح');
    }

    public function resetGroup(Request $request)
    {
        $group = $request->get('group');

        if (!$group) {
            return redirect()->back()->with('error', 'المجموعة غير محددة');
        }

        $settings = Setting::byGroup($group)->where('is_required', false)->get();

        foreach ($settings as $setting) {
            $setting->update(['value' => null]);
        }

        Cache::flush();

        return redirect()->back()->with('success', "تم إعادة تعيين إعدادات مجموعة {$group}");
    }

    public function export()
    {
        $settings = Setting::all();

        $filename = 'settings_export_' . now()->format('Y_m_d_H_i_s') . '.json';

        $headers = [
            'Content-Type' => 'application/json',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $data = $settings->map(function ($setting) {
            return [
                'key' => $setting->key,
                'group' => $setting->group,
                'name' => $setting->name,
                'description' => $setting->description,
                'value' => $setting->value,
                'type' => $setting->type,
                'options' => $setting->options,
                'validation_rules' => $setting->validation_rules,
                'is_public' => $setting->is_public,
                'is_required' => $setting->is_required,
                'sort_order' => $setting->sort_order,
            ];
        });

        return response()->json($data, 200, $headers);
    }

    public function import(Request $request)
    {
        $request->validate([
            'settings_file' => 'required|file|mimes:json',
        ], [
            'settings_file.required' => 'ملف الإعدادات مطلوب',
            'settings_file.mimes' => 'يجب أن يكون الملف من نوع JSON',
        ]);

        $file = $request->file('settings_file');
        $content = file_get_contents($file->getPathname());
        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return redirect()->back()->with('error', 'ملف JSON غير صحيح');
        }

        $imported = 0;
        $updated = 0;

        foreach ($data as $settingData) {
            $existing = Setting::where('key', $settingData['key'])->first();

            if ($existing) {
                $existing->update($settingData);
                $updated++;
            } else {
                Setting::create($settingData);
                $imported++;
            }
        }

        Cache::flush();

        return redirect()->back()->with('success', "تم استيراد {$imported} إعداد جديد وتحديث {$updated} إعداد موجود");
    }

    public function clearCache()
    {
        Cache::flush();

        return redirect()->back()->with('success', 'تم مسح ذاكرة التخزين المؤقت بنجاح');
    }

    public function getPublicSettings()
    {
        $settings = Setting::getAllPublic();

        return response()->json([
            'settings' => $settings->pluck('value', 'key')
        ]);
    }
}
