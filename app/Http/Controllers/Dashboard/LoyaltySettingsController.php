<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\LoyaltySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LoyaltySettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = [
            'values' => 'إعدادات القيم',
            'expiry' => 'إعدادات انتهاء الصلاحية',
            'bonuses' => 'إعدادات المكافآت',
            'system' => 'إعدادات النظام',
            'general' => 'إعدادات عامة',
        ];

        $settingsByCategory = [];
        foreach ($categories as $category => $categoryName) {
            $settingsByCategory[$category] = [
                'name' => $categoryName,
                'settings' => LoyaltySetting::getByCategory($category)
            ];
        }

        return view('dashboard.loyalty-settings.index', compact('settingsByCategory'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = [
            'values' => 'إعدادات القيم',
            'expiry' => 'إعدادات انتهاء الصلاحية',
            'bonuses' => 'إعدادات المكافآت',
            'system' => 'إعدادات النظام',
            'general' => 'إعدادات عامة',
        ];

        $types = [
            'string' => 'نص',
            'integer' => 'رقم صحيح',
            'decimal' => 'رقم عشري',
            'boolean' => 'نعم/لا',
            'json' => 'JSON',
            'array' => 'مصفوفة',
        ];

        return view('dashboard.loyalty-settings.create', compact('categories', 'types'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'setting_key' => 'required|string|max:255|unique:loyalty_settings,setting_key',
            'setting_value' => 'required',
            'setting_type' => 'required|in:string,integer,decimal,boolean,json,array',
            'description' => 'nullable|string',
            'category' => 'required|in:values,expiry,bonuses,system,general',
            'sort_order' => 'nullable|integer|min:0',
        ], [
            'setting_key.required' => 'يجب إدخال مفتاح الإعداد',
            'setting_key.unique' => 'مفتاح الإعداد موجود بالفعل',
            'setting_value.required' => 'يجب إدخال قيمة الإعداد',
            'setting_type.required' => 'يجب اختيار نوع الإعداد',
            'category.required' => 'يجب اختيار فئة الإعداد',
        ]);

        LoyaltySetting::create([
            'setting_key' => $request->setting_key,
            'setting_value' => $request->setting_value,
            'setting_type' => $request->setting_type,
            'description' => $request->description,
            'category' => $request->category,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => true,
            'is_editable' => true,
        ]);

        // Clear cache
        LoyaltySetting::clearCache();

        return redirect()->route('dashboard.loyalty-settings.index')
            ->with('success', 'تم إنشاء الإعداد بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(LoyaltySetting $loyaltySetting)
    {
        return view('dashboard.loyalty-settings.show', compact('loyaltySetting'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LoyaltySetting $loyaltySetting)
    {
        $categories = [
            'values' => 'إعدادات القيم',
            'expiry' => 'إعدادات انتهاء الصلاحية',
            'bonuses' => 'إعدادات المكافآت',
            'system' => 'إعدادات النظام',
            'general' => 'إعدادات عامة',
        ];

        $types = [
            'string' => 'نص',
            'integer' => 'رقم صحيح',
            'decimal' => 'رقم عشري',
            'boolean' => 'نعم/لا',
            'json' => 'JSON',
            'array' => 'مصفوفة',
        ];

        return view('dashboard.loyalty-settings.edit', compact('loyaltySetting', 'categories', 'types'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LoyaltySetting $loyaltySetting)
    {
        $request->validate([
            'setting_key' => 'required|string|max:255|unique:loyalty_settings,setting_key,' . $loyaltySetting->id,
            'setting_value' => 'required',
            'setting_type' => 'required|in:string,integer,decimal,boolean,json,array',
            'description' => 'nullable|string',
            'category' => 'required|in:values,expiry,bonuses,system,general',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'is_editable' => 'boolean',
        ], [
            'setting_key.required' => 'يجب إدخال مفتاح الإعداد',
            'setting_key.unique' => 'مفتاح الإعداد موجود بالفعل',
            'setting_value.required' => 'يجب إدخال قيمة الإعداد',
            'setting_type.required' => 'يجب اختيار نوع الإعداد',
            'category.required' => 'يجب اختيار فئة الإعداد',
        ]);

        $loyaltySetting->update([
            'setting_key' => $request->setting_key,
            'setting_value' => $request->setting_value,
            'setting_type' => $request->setting_type,
            'description' => $request->description,
            'category' => $request->category,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->boolean('is_active'),
            'is_editable' => $request->boolean('is_editable'),
        ]);

        // Clear cache
        LoyaltySetting::clearCache();

        return redirect()->route('dashboard.loyalty-settings.index')
            ->with('success', 'تم تحديث الإعداد بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LoyaltySetting $loyaltySetting)
    {
        if (!$loyaltySetting->is_editable) {
            return redirect()->back()
                ->with('error', 'لا يمكن حذف هذا الإعداد');
        }

        $loyaltySetting->delete();

        // Clear cache
        LoyaltySetting::clearCache();

        return redirect()->route('dashboard.loyalty-settings.index')
            ->with('success', 'تم حذف الإعداد بنجاح');
    }

    /**
     * Bulk update settings.
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
            'settings.*.id' => 'required|exists:loyalty_settings,id',
            'settings.*.setting_value' => 'required',
        ]);

        foreach ($request->settings as $settingData) {
            $setting = LoyaltySetting::find($settingData['id']);
            if ($setting && $setting->is_editable) {
                $setting->update(['setting_value' => $settingData['setting_value']]);
            }
        }

        // Clear cache
        LoyaltySetting::clearCache();

        return redirect()->back()
            ->with('success', 'تم تحديث الإعدادات بنجاح');
    }

    /**
     * Reset settings to defaults.
     */
    public function resetToDefaults()
    {
        LoyaltySetting::truncate();
        LoyaltySetting::initializeDefaults();

        return redirect()->back()
            ->with('success', 'تم إعادة تعيين الإعدادات إلى القيم الافتراضية');
    }

    /**
     * Toggle setting status.
     */
    public function toggleStatus(LoyaltySetting $loyaltySetting)
    {
        if (!$loyaltySetting->is_editable) {
            return redirect()->back()
                ->with('error', 'لا يمكن تعديل هذا الإعداد');
        }

        $loyaltySetting->update(['is_active' => !$loyaltySetting->is_active]);

        // Clear cache
        LoyaltySetting::clearCache();

        $status = $loyaltySetting->is_active ? 'تفعيل' : 'إلغاء تفعيل';

        return redirect()->back()
            ->with('success', "تم {$status} الإعداد بنجاح");
    }

    /**
     * Export settings.
     */
    public function export()
    {
        $settings = LoyaltySetting::all();

        $filename = 'loyalty_settings_' . now()->format('Y-m-d_H-i-s') . '.json';

        $headers = [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        return response()->json($settings, 200, $headers);
    }

    /**
     * Import settings.
     */
    public function import(Request $request)
    {
        $request->validate([
            'settings_file' => 'required|file|mimes:json',
        ]);

        $file = $request->file('settings_file');
        $settings = json_decode(file_get_contents($file->getPathname()), true);

        if (!$settings) {
            return redirect()->back()
                ->with('error', 'ملف الإعدادات غير صحيح');
        }

        foreach ($settings as $settingData) {
            LoyaltySetting::updateOrCreate(
                ['setting_key' => $settingData['setting_key']],
                $settingData
            );
        }

        // Clear cache
        LoyaltySetting::clearCache();

        return redirect()->back()
            ->with('success', 'تم استيراد الإعدادات بنجاح');
    }
}
