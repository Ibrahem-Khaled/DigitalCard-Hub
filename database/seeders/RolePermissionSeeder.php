<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Permissions
        $permissions = [
            // Dashboard permissions
            ['name' => 'dashboard.view', 'slug' => 'dashboard.view', 'display_name' => 'عرض لوحة التحكم', 'module' => 'dashboard', 'action' => 'view', 'is_system' => true],
            ['name' => 'dashboard.stats', 'slug' => 'dashboard.stats', 'display_name' => 'عرض الإحصائيات', 'module' => 'dashboard', 'action' => 'stats', 'is_system' => true],

            // User management permissions
            ['name' => 'users.view', 'slug' => 'users.view', 'display_name' => 'عرض المستخدمين', 'module' => 'users', 'action' => 'view', 'is_system' => true],
            ['name' => 'users.create', 'slug' => 'users.create', 'display_name' => 'إنشاء مستخدم', 'module' => 'users', 'action' => 'create', 'is_system' => true],
            ['name' => 'users.update', 'slug' => 'users.update', 'display_name' => 'تعديل مستخدم', 'module' => 'users', 'action' => 'update', 'is_system' => true],
            ['name' => 'users.delete', 'slug' => 'users.delete', 'display_name' => 'حذف مستخدم', 'module' => 'users', 'action' => 'delete', 'is_system' => true],

            // Role management permissions
            ['name' => 'roles.view', 'slug' => 'roles.view', 'display_name' => 'عرض الأدوار', 'module' => 'roles', 'action' => 'view', 'is_system' => true],
            ['name' => 'roles.create', 'slug' => 'roles.create', 'display_name' => 'إنشاء دور', 'module' => 'roles', 'action' => 'create', 'is_system' => true],
            ['name' => 'roles.update', 'slug' => 'roles.update', 'display_name' => 'تعديل دور', 'module' => 'roles', 'action' => 'update', 'is_system' => true],
            ['name' => 'roles.delete', 'slug' => 'roles.delete', 'display_name' => 'حذف دور', 'module' => 'roles', 'action' => 'delete', 'is_system' => true],

            // Product management permissions
            ['name' => 'products.view', 'slug' => 'products.view', 'display_name' => 'عرض المنتجات', 'module' => 'products', 'action' => 'view', 'is_system' => true],
            ['name' => 'products.create', 'slug' => 'products.create', 'display_name' => 'إنشاء منتج', 'module' => 'products', 'action' => 'create', 'is_system' => true],
            ['name' => 'products.update', 'slug' => 'products.update', 'display_name' => 'تعديل منتج', 'module' => 'products', 'action' => 'update', 'is_system' => true],
            ['name' => 'products.delete', 'slug' => 'products.delete', 'display_name' => 'حذف منتج', 'module' => 'products', 'action' => 'delete', 'is_system' => true],

            // Order management permissions
            ['name' => 'orders.view', 'slug' => 'orders.view', 'display_name' => 'عرض الطلبات', 'module' => 'orders', 'action' => 'view', 'is_system' => true],
            ['name' => 'orders.create', 'slug' => 'orders.create', 'display_name' => 'إنشاء طلب', 'module' => 'orders', 'action' => 'create', 'is_system' => true],
            ['name' => 'orders.update', 'slug' => 'orders.update', 'display_name' => 'تعديل طلب', 'module' => 'orders', 'action' => 'update', 'is_system' => true],
            ['name' => 'orders.delete', 'slug' => 'orders.delete', 'display_name' => 'حذف طلب', 'module' => 'orders', 'action' => 'delete', 'is_system' => true],

            // Customer management permissions
            ['name' => 'customers.view', 'slug' => 'customers.view', 'display_name' => 'عرض العملاء', 'module' => 'customers', 'action' => 'view', 'is_system' => true],
            ['name' => 'customers.create', 'slug' => 'customers.create', 'display_name' => 'إنشاء عميل', 'module' => 'customers', 'action' => 'create', 'is_system' => true],
            ['name' => 'customers.update', 'slug' => 'customers.update', 'display_name' => 'تعديل عميل', 'module' => 'customers', 'action' => 'update', 'is_system' => true],
            ['name' => 'customers.delete', 'slug' => 'customers.delete', 'display_name' => 'حذف عميل', 'module' => 'customers', 'action' => 'delete', 'is_system' => true],

            // Reports permissions
            ['name' => 'reports.view', 'slug' => 'reports.view', 'display_name' => 'عرض التقارير', 'module' => 'reports', 'action' => 'view', 'is_system' => true],
            ['name' => 'reports.export', 'slug' => 'reports.export', 'display_name' => 'تصدير التقارير', 'module' => 'reports', 'action' => 'export', 'is_system' => true],

            // Settings permissions
            ['name' => 'settings.view', 'slug' => 'settings.view', 'display_name' => 'عرض الإعدادات', 'module' => 'settings', 'action' => 'view', 'is_system' => true],
            ['name' => 'settings.update', 'slug' => 'settings.update', 'display_name' => 'تعديل الإعدادات', 'module' => 'settings', 'action' => 'update', 'is_system' => true],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }

        // Create Roles
        $roles = [
            [
                'name' => 'مدير النظام',
                'slug' => 'admin',
                'display_name' => 'مدير النظام',
                'description' => 'مدير النظام مع جميع الصلاحيات',
                'color' => '#DC2626',
                'is_system' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'مدير',
                'slug' => 'manager',
                'display_name' => 'مدير',
                'description' => 'مدير مع صلاحيات إدارية محدودة',
                'color' => '#7C3AED',
                'is_system' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'موظف',
                'slug' => 'employee',
                'display_name' => 'موظف',
                'description' => 'موظف مع صلاحيات محدودة',
                'color' => '#059669',
                'is_system' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'عميل',
                'slug' => 'customer',
                'display_name' => 'عميل',
                'description' => 'عميل عادي',
                'color' => '#0891B2',
                'is_system' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($roles as $roleData) {
            $role = Role::create($roleData);

            // Assign permissions based on role
            switch ($role->slug) {
                case 'admin':
                    // Admin gets all permissions
                    $role->permissions()->attach(Permission::all()->pluck('id'));
                    break;

                case 'manager':
                    // Manager gets most permissions except user/role management
                    $managerPermissions = Permission::whereNotIn('module', ['users', 'roles'])->pluck('id');
                    $role->permissions()->attach($managerPermissions);
                    break;

                case 'employee':
                    // Employee gets view permissions for most modules
                    $employeePermissions = Permission::whereIn('action', ['view'])
                        ->whereNotIn('module', ['users', 'roles', 'settings'])
                        ->pluck('id');
                    $role->permissions()->attach($employeePermissions);
                    break;

                case 'customer':
                    // Customer gets minimal permissions
                    $customerPermissions = Permission::whereIn('slug', [
                        'dashboard.view',
                        'products.view',
                        'orders.view',
                        'orders.create',
                    ])->pluck('id');
                    $role->permissions()->attach($customerPermissions);
                    break;
            }
        }

        // Create Admin User
        $adminUser = User::create([
            'first_name' => 'مدير',
            'last_name' => 'النظام',
            'email' => 'admin@admin.com',
            'phone' => '+966501234567',
            'password' => Hash::make('123456'),
            'is_active' => true,
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
        ]);

        // Assign admin role to admin user
        $adminRole = Role::where('slug', 'admin')->first();
        $adminUser->roles()->attach($adminRole->id, [
            'assigned_at' => now(),
            'assigned_by' => $adminUser->id,
        ]);

        // Create Manager User
        $managerUser = User::create([
            'first_name' => 'أحمد',
            'last_name' => 'المدير',
            'email' => 'manager@example.com',
            'phone' => '+966501234568',
            'password' => Hash::make('password'),
            'is_active' => true,
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
        ]);

        // Assign manager role to manager user
        $managerRole = Role::where('slug', 'manager')->first();
        $managerUser->roles()->attach($managerRole->id, [
            'assigned_at' => now(),
            'assigned_by' => $adminUser->id,
        ]);

        // Create Employee User
        $employeeUser = User::create([
            'first_name' => 'فاطمة',
            'last_name' => 'الموظفة',
            'email' => 'employee@example.com',
            'phone' => '+966501234569',
            'password' => Hash::make('password'),
            'is_active' => true,
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
        ]);

        // Assign employee role to employee user
        $employeeRole = Role::where('slug', 'employee')->first();
        $employeeUser->roles()->attach($employeeRole->id, [
            'assigned_at' => now(),
            'assigned_by' => $adminUser->id,
        ]);

        // Create Customer User
        $customerUser = User::create([
            'first_name' => 'محمد',
            'last_name' => 'العميل',
            'email' => 'customer@example.com',
            'phone' => '+966501234570',
            'password' => Hash::make('password'),
            'is_active' => true,
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
        ]);

        // Assign customer role to customer user
        $customerRole = Role::where('slug', 'customer')->first();
        $customerUser->roles()->attach($customerRole->id, [
            'assigned_at' => now(),
            'assigned_by' => $adminUser->id,
        ]);
    }
}
