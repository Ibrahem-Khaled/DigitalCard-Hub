<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Permission::with('roles');

        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('display_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('module', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%");
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

        // فلترة حسب الوحدة
        if ($request->filled('module')) {
            $query->byModule($request->module);
        }

        // فلترة حسب الإجراء
        if ($request->filled('action')) {
            $query->byAction($request->action);
        }

        // ترتيب
        $sortBy = $request->get('sort_by', 'sort_order');
        $sortOrder = $request->get('sort_order', 'asc');

        // التحقق من صحة معاملات الترتيب
        $allowedSortFields = ['sort_order', 'name', 'display_name', 'module', 'action', 'created_at', 'updated_at'];
        $allowedSortOrders = ['asc', 'desc'];

        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'sort_order';
        }

        if (!in_array($sortOrder, $allowedSortOrders)) {
            $sortOrder = 'asc';
        }

        $query->orderBy($sortBy, $sortOrder);

        $permissions = $query->paginate(15)->withQueryString();

        // إحصائيات
        $stats = [
            'total_permissions' => Permission::count(),
            'active_permissions' => Permission::active()->count(),
            'system_permissions' => Permission::system()->count(),
            'custom_permissions' => Permission::nonSystem()->count(),
            'permissions_by_module' => Permission::getGroupedByModule(),
            'permissions_by_action' => Permission::getGroupedByAction(),
        ];

        // خيارات الفلترة
        $filterOptions = [
            'modules' => Permission::getModules(),
            'actions' => Permission::getActions(),
        ];

        return view('dashboard.permissions.index', compact('permissions', 'stats', 'filterOptions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $modules = Permission::getModules();
        $actions = Permission::getActions();

        return view('dashboard.permissions.create', compact('modules', 'actions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'module' => 'nullable|string|max:255',
            'action' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ], [
            'name.required' => 'اسم الصلاحية مطلوب',
            'name.unique' => 'اسم الصلاحية موجود بالفعل',
            'display_name.required' => 'اسم العرض مطلوب',
        ]);

        DB::beginTransaction();
        try {
            $permissionData = $request->all();
            $permissionData['slug'] = Str::slug($request->name);
            $permissionData['is_system'] = false; // Custom permissions are not system permissions

            $permission = Permission::create($permissionData);

            DB::commit();

            return redirect()->route('dashboard.permissions.index')
                ->with('success', 'تم إنشاء الصلاحية بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء الصلاحية: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Permission $permission)
    {
        $permission->load('roles');

        // إحصائيات الصلاحية
        $permissionStats = [
            'roles_count' => $permission->roles()->count(),
            'roles_by_module' => $permission->roles()
                                          ->select('roles.*')
                                          ->get()
                                          ->groupBy(function($role) {
                                              return $role->is_system ? 'نظام' : 'مخصص';
                                          }),
        ];

        return view('dashboard.permissions.show', compact('permission', 'permissionStats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Permission $permission)
    {
        // منع تعديل الصلاحيات النظامية
        if ($permission->is_system) {
            return redirect()->route('dashboard.permissions.index')
                ->with('error', 'لا يمكن تعديل الصلاحيات النظامية');
        }

        $modules = Permission::getModules();
        $actions = Permission::getActions();

        return view('dashboard.permissions.edit', compact('permission', 'modules', 'actions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Permission $permission)
    {
        // منع تعديل الصلاحيات النظامية
        if ($permission->is_system) {
            return redirect()->route('dashboard.permissions.index')
                ->with('error', 'لا يمكن تعديل الصلاحيات النظامية');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('permissions')->ignore($permission->id)],
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'module' => 'nullable|string|max:255',
            'action' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ], [
            'name.required' => 'اسم الصلاحية مطلوب',
            'name.unique' => 'اسم الصلاحية موجود بالفعل',
            'display_name.required' => 'اسم العرض مطلوب',
        ]);

        DB::beginTransaction();
        try {
            $permissionData = $request->all();
            $permissionData['slug'] = Str::slug($request->name);

            $permission->update($permissionData);

            DB::commit();

            return redirect()->route('dashboard.permissions.index')
                ->with('success', 'تم تحديث الصلاحية بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث الصلاحية: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission)
    {
        // منع حذف الصلاحيات النظامية
        if ($permission->is_system) {
            return redirect()->back()
                ->with('error', 'لا يمكن حذف الصلاحيات النظامية');
        }

        // التحقق من إمكانية الحذف
        if (!$permission->canBeDeleted()) {
            return redirect()->back()
                ->with('error', 'لا يمكن حذف هذه الصلاحية لأنها مرتبطة بأدوار');
        }

        DB::beginTransaction();
        try {
            $permission->delete();

            DB::commit();

            return redirect()->route('dashboard.permissions.index')
                ->with('success', 'تم حذف الصلاحية بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء حذف الصلاحية: ' . $e->getMessage());
        }
    }

    /**
     * Toggle permission active status.
     */
    public function toggleStatus(Permission $permission)
    {
        // منع تعطيل الصلاحيات النظامية
        if ($permission->is_system) {
            return redirect()->back()
                ->with('error', 'لا يمكن تعطيل الصلاحيات النظامية');
        }

        $permission->update(['is_active' => !$permission->is_active]);

        $status = $permission->is_active ? 'تفعيل' : 'تعطيل';

        return redirect()->back()
            ->with('success', "تم {$status} الصلاحية بنجاح");
    }

    /**
     * Duplicate permission.
     */
    public function duplicate(Permission $permission)
    {
        DB::beginTransaction();
        try {
            $newPermission = $permission->replicate();
            $newPermission->name = $permission->name . ' (نسخة)';
            $newPermission->display_name = $permission->display_name . ' (نسخة)';
            $newPermission->slug = Str::slug($newPermission->name);
            $newPermission->is_system = false;
            $newPermission->save();

            DB::commit();

            return redirect()->route('dashboard.permissions.index')
                ->with('success', 'تم نسخ الصلاحية بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء نسخ الصلاحية: ' . $e->getMessage());
        }
    }

    /**
     * Bulk create permissions for a module.
     */
    public function bulkCreate(Request $request)
    {
        $request->validate([
            'module' => 'required|string|max:255',
            'actions' => 'required|array|min:1',
            'actions.*' => 'string|max:255',
            'prefix' => 'nullable|string|max:255',
        ], [
            'module.required' => 'اسم الوحدة مطلوب',
            'actions.required' => 'يجب تحديد إجراء واحد على الأقل',
            'actions.min' => 'يجب تحديد إجراء واحد على الأقل',
        ]);

        DB::beginTransaction();
        try {
            $createdPermissions = [];
            $prefix = $request->prefix ?: $request->module;

            foreach ($request->actions as $action) {
                $name = $prefix . '.' . $action;
                $displayName = ucfirst($request->module) . ' - ' . ucfirst($action);

                // التحقق من عدم وجود الصلاحية
                if (!Permission::where('name', $name)->exists()) {
                    $permission = Permission::create([
                        'name' => $name,
                        'slug' => Str::slug($name),
                        'display_name' => $displayName,
                        'description' => "صلاحية {$displayName}",
                        'module' => $request->module,
                        'action' => $action,
                        'is_active' => true,
                        'is_system' => false,
                        'sort_order' => 0,
                    ]);

                    $createdPermissions[] = $permission;
                }
            }

            DB::commit();

            $count = count($createdPermissions);
            return redirect()->route('dashboard.permissions.index')
                ->with('success', "تم إنشاء {$count} صلاحية بنجاح");
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء الصلاحيات: ' . $e->getMessage());
        }
    }

    /**
     * Export permissions to CSV.
     */
    public function export()
    {
        $permissions = Permission::with('roles')->get();

        $filename = 'permissions_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($permissions) {
            $file = fopen('php://output', 'w');

            // إضافة BOM للدعم العربي
            fwrite($file, "\xEF\xBB\xBF");

            // رؤوس الأعمدة
            fputcsv($file, [
                'اسم الصلاحية',
                'اسم العرض',
                'الوصف',
                'الوحدة',
                'الإجراء',
                'الحالة',
                'نوع الصلاحية',
                'الأدوار',
                'تاريخ الإنشاء'
            ]);

            foreach ($permissions as $permission) {
                fputcsv($file, [
                    $permission->name,
                    $permission->display_name,
                    $permission->description,
                    $permission->module,
                    $permission->action,
                    $permission->is_active ? 'نشط' : 'معطل',
                    $permission->is_system ? 'نظام' : 'مخصص',
                    $permission->roles->pluck('display_name')->join(', '),
                    $permission->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
