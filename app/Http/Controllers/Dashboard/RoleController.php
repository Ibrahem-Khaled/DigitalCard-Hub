<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Role::with('permissions');

        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('display_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // فلترة حسب النوع
        if ($request->filled('type')) {
            if ($request->type === 'system') {
                $query->system();
            } elseif ($request->type === 'custom') {
                $query->nonSystem();
            }
        }

        // ترتيب
        $sortBy = $request->get('sort_by', 'sort_order');
        $sortOrder = $request->get('sort_order', 'asc');

        // التحقق من صحة معاملات الترتيب
        $allowedSortFields = ['sort_order', 'name', 'display_name', 'created_at', 'updated_at'];
        $allowedSortOrders = ['asc', 'desc'];

        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'sort_order';
        }

        if (!in_array($sortOrder, $allowedSortOrders)) {
            $sortOrder = 'asc';
        }

        $query->orderBy($sortBy, $sortOrder);

        $roles = $query->paginate(15)->withQueryString();

        // إحصائيات
        $stats = [
            'total_roles' => Role::count(),
            'active_roles' => Role::active()->count(),
            'system_roles' => Role::system()->count(),
            'custom_roles' => Role::nonSystem()->count(),
            'roles_with_permissions' => Role::whereHas('permissions')->count(),
            'roles_with_users' => Role::whereHas('users')->count(),
        ];

        return view('dashboard.roles.index', compact('roles', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = Permission::active()->ordered()->get()->groupBy('module');
        return view('dashboard.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ], [
            'name.required' => 'اسم الدور مطلوب',
            'name.unique' => 'اسم الدور موجود بالفعل',
            'display_name.required' => 'اسم العرض مطلوب',
            'color.regex' => 'لون غير صحيح',
            'permissions.*.exists' => 'الصلاحية المحددة غير موجودة',
        ]);

        DB::beginTransaction();
        try {
            $roleData = $request->except(['permissions']);
            $roleData['slug'] = Str::slug($request->name);
            $roleData['is_system'] = false; // Custom roles are not system roles

            $role = Role::create($roleData);

            // إضافة الصلاحيات
            if ($request->has('permissions')) {
                $role->permissions()->attach($request->permissions);
            }

            DB::commit();

            return redirect()->route('dashboard.roles.index')
                ->with('success', 'تم إنشاء الدور بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء الدور: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        $role->load(['permissions', 'users']);

        // إحصائيات الدور
        $roleStats = [
            'permissions_count' => $role->permissions()->count(),
            'users_count' => $role->users()->count(),
            'permissions_by_module' => $role->getPermissionsByModule(),
        ];

        return view('dashboard.roles.show', compact('role', 'roleStats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        // منع تعديل الأدوار النظامية
        if ($role->is_system) {
            return redirect()->route('dashboard.roles.index')
                ->with('error', 'لا يمكن تعديل الأدوار النظامية');
        }

        $permissions = Permission::active()->ordered()->get()->groupBy('module');
        $role->load('permissions');

        return view('dashboard.roles.edit', compact('role', 'permissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        // منع تعديل الأدوار النظامية
        if ($role->is_system) {
            return redirect()->route('dashboard.roles.index')
                ->with('error', 'لا يمكن تعديل الأدوار النظامية');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles')->ignore($role->id)],
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ], [
            'name.required' => 'اسم الدور مطلوب',
            'name.unique' => 'اسم الدور موجود بالفعل',
            'display_name.required' => 'اسم العرض مطلوب',
            'color.regex' => 'لون غير صحيح',
            'permissions.*.exists' => 'الصلاحية المحددة غير موجودة',
        ]);

        DB::beginTransaction();
        try {
            $roleData = $request->except(['permissions']);
            $roleData['slug'] = Str::slug($request->name);

            $role->update($roleData);

            // تحديث الصلاحيات
            $role->permissions()->sync($request->permissions ?? []);

            DB::commit();

            return redirect()->route('dashboard.roles.index')
                ->with('success', 'تم تحديث الدور بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث الدور: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        // منع حذف الأدوار النظامية
        if ($role->is_system) {
            return redirect()->back()
                ->with('error', 'لا يمكن حذف الأدوار النظامية');
        }

        // التحقق من إمكانية الحذف
        if (!$role->canBeDeleted()) {
            return redirect()->back()
                ->with('error', 'لا يمكن حذف هذا الدور لأنه مرتبط بمستخدمين');
        }

        DB::beginTransaction();
        try {
            // حذف الصلاحيات المرتبطة
            $role->permissions()->detach();

            // حذف الدور
            $role->delete();

            DB::commit();

            return redirect()->route('dashboard.roles.index')
                ->with('success', 'تم حذف الدور بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء حذف الدور: ' . $e->getMessage());
        }
    }

    /**
     * Toggle role active status.
     */
    public function toggleStatus(Role $role)
    {
        // منع تعطيل الأدوار النظامية
        if ($role->is_system) {
            return redirect()->back()
                ->with('error', 'لا يمكن تعطيل الأدوار النظامية');
        }

        $role->update(['is_active' => !$role->is_active]);

        $status = $role->is_active ? 'تفعيل' : 'تعطيل';

        return redirect()->back()
            ->with('success', "تم {$status} الدور بنجاح");
    }

    /**
     * Duplicate role.
     */
    public function duplicate(Role $role)
    {
        DB::beginTransaction();
        try {
            $newRole = $role->replicate();
            $newRole->name = $role->name . ' (نسخة)';
            $newRole->display_name = $role->display_name . ' (نسخة)';
            $newRole->slug = Str::slug($newRole->name);
            $newRole->is_system = false;
            $newRole->save();

            // نسخ الصلاحيات
            $newRole->permissions()->attach($role->permissions()->pluck('permissions.id'));

            DB::commit();

            return redirect()->route('dashboard.roles.index')
                ->with('success', 'تم نسخ الدور بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء نسخ الدور: ' . $e->getMessage());
        }
    }

    /**
     * Export roles to CSV.
     */
    public function export()
    {
        $roles = Role::with('permissions')->get();

        $filename = 'roles_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($roles) {
            $file = fopen('php://output', 'w');

            // إضافة BOM للدعم العربي
            fwrite($file, "\xEF\xBB\xBF");

            // رؤوس الأعمدة
            fputcsv($file, [
                'اسم الدور',
                'اسم العرض',
                'الوصف',
                'اللون',
                'الحالة',
                'نوع الدور',
                'الصلاحيات',
                'عدد المستخدمين',
                'تاريخ الإنشاء'
            ]);

            foreach ($roles as $role) {
                fputcsv($file, [
                    $role->name,
                    $role->display_name,
                    $role->description,
                    $role->color,
                    $role->is_active ? 'نشط' : 'معطل',
                    $role->is_system ? 'نظام' : 'مخصص',
                    $role->permissions->pluck('display_name')->join(', '),
                    $role->users()->count(),
                    $role->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
