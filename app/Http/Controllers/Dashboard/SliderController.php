<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SliderController extends Controller
{
    /**
     * عرض قائمة السلايدرات
     */
    public function index(Request $request)
    {
        $query = Slider::query();

        // البحث
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // فلتر حسب الحالة
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // فلتر حسب الموقع
        if ($request->filled('position')) {
            $query->where('position', $request->position);
        }

        $sliders = $query->ordered()->paginate(15);

        return view('dashboard.sliders.index', compact('sliders'));
    }

    /**
     * عرض نموذج إنشاء سلايدر جديد
     */
    public function create()
    {
        $positions = [
            'homepage' => 'الصفحة الرئيسية',
            'category' => 'صفحات الفئات',
            'product' => 'صفحات المنتجات',
            'footer' => 'الفوتر',
        ];

        return view('dashboard.sliders.create', compact('positions'));
    }

    /**
     * حفظ السلايدر الجديد
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'button_text' => 'nullable|string|max:100',
            'button_url' => 'nullable|url|max:500',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'position' => 'required|string|in:homepage,category,product,footer',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
        ]);

        // رفع الصورة
        $imagePath = $request->file('image')->store('sliders', 'public');

        // إعدادات إضافية
        $settings = [];
        if ($request->filled('animation_type')) {
            $settings['animation_type'] = $request->animation_type;
        }
        if ($request->filled('animation_duration')) {
            $settings['animation_duration'] = $request->animation_duration;
        }

        Slider::create([
            'title' => $request->title,
            'description' => $request->description,
            'image' => $imagePath,
            'button_text' => $request->button_text,
            'button_url' => $request->button_url,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->boolean('is_active'),
            'position' => $request->position,
            'settings' => $settings,
            'starts_at' => $request->starts_at,
            'ends_at' => $request->ends_at,
        ]);

        return redirect()->route('dashboard.sliders.index')
            ->with('success', 'تم إنشاء السلايدر بنجاح');
    }

    /**
     * عرض تفاصيل السلايدر
     */
    public function show(Slider $slider)
    {
        return view('dashboard.sliders.show', compact('slider'));
    }

    /**
     * عرض نموذج تعديل السلايدر
     */
    public function edit(Slider $slider)
    {
        $positions = [
            'homepage' => 'الصفحة الرئيسية',
            'category' => 'صفحات الفئات',
            'product' => 'صفحات المنتجات',
            'footer' => 'الفوتر',
        ];

        return view('dashboard.sliders.edit', compact('slider', 'positions'));
    }

    /**
     * تحديث السلايدر
     */
    public function update(Request $request, Slider $slider)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'button_text' => 'nullable|string|max:100',
            'button_url' => 'nullable|url|max:500',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'position' => 'required|string|in:homepage,category,product,footer',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
        ]);

        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'button_text' => $request->button_text,
            'button_url' => $request->button_url,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->boolean('is_active'),
            'position' => $request->position,
            'starts_at' => $request->starts_at,
            'ends_at' => $request->ends_at,
        ];

        // تحديث الصورة إذا تم رفع صورة جديدة
        if ($request->hasFile('image')) {
            // حذف الصورة القديمة
            if ($slider->image) {
                Storage::disk('public')->delete($slider->image);
            }
            $data['image'] = $request->file('image')->store('sliders', 'public');
        }

        // تحديث الإعدادات
        $settings = $slider->settings ?? [];
        if ($request->filled('animation_type')) {
            $settings['animation_type'] = $request->animation_type;
        }
        if ($request->filled('animation_duration')) {
            $settings['animation_duration'] = $request->animation_duration;
        }
        $data['settings'] = $settings;

        $slider->update($data);

        return redirect()->route('dashboard.sliders.index')
            ->with('success', 'تم تحديث السلايدر بنجاح');
    }

    /**
     * حذف السلايدر
     */
    public function destroy(Slider $slider)
    {
        // حذف الصورة
        if ($slider->image) {
            Storage::disk('public')->delete($slider->image);
        }

        $slider->delete();

        return redirect()->route('dashboard.sliders.index')
            ->with('success', 'تم حذف السلايدر بنجاح');
    }

    /**
     * تغيير حالة السلايدر
     */
    public function toggleStatus(Slider $slider)
    {
        $slider->update(['is_active' => !$slider->is_active]);

        $status = $slider->is_active ? 'تفعيل' : 'إلغاء تفعيل';

        return redirect()->back()
            ->with('success', "تم {$status} السلايدر بنجاح");
    }

    /**
     * تحديث ترتيب السلايدرات
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'sliders' => 'required|array',
            'sliders.*.id' => 'required|exists:sliders,id',
            'sliders.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($request->sliders as $sliderData) {
            Slider::where('id', $sliderData['id'])
                ->update(['sort_order' => $sliderData['sort_order']]);
        }

        return response()->json(['success' => true]);
    }
}
