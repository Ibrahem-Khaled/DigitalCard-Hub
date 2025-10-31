<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء الصلاحيات الأساسية
        $permissions = $this->createPermissions();

        // إنشاء الأدوار الأساسية
        $this->createRoles($permissions);
    }

    /**
     * إنشاء الصلاحيات الأساسية
     */
    private function createPermissions(): array
    {
        $permissions = [
            // صلاحيات لوحة التحكم
            [
                'name' => 'dashboard.access',
                'slug' => 'dashboard-access',
                'display_name' => 'الوصول للوحة التحكم',
                'description' => 'السماح بالوصول إلى لوحة التحكم',
                'module' => 'dashboard',
                'action' => 'access',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 1,
            ],

            // صلاحيات إدارة المستخدمين
            [
                'name' => 'users.create',
                'slug' => 'users-create',
                'display_name' => 'إنشاء مستخدم',
                'description' => 'السماح بإنشاء مستخدم جديد',
                'module' => 'users',
                'action' => 'create',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 10,
            ],
            [
                'name' => 'users.read',
                'slug' => 'users-read',
                'display_name' => 'عرض المستخدمين',
                'description' => 'السماح بعرض قائمة المستخدمين',
                'module' => 'users',
                'action' => 'read',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 11,
            ],
            [
                'name' => 'users.update',
                'slug' => 'users-update',
                'display_name' => 'تعديل مستخدم',
                'description' => 'السماح بتعديل بيانات المستخدم',
                'module' => 'users',
                'action' => 'update',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 12,
            ],
            [
                'name' => 'users.delete',
                'slug' => 'users-delete',
                'display_name' => 'حذف مستخدم',
                'description' => 'السماح بحذف المستخدم',
                'module' => 'users',
                'action' => 'delete',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 13,
            ],
            [
                'name' => 'users.export',
                'slug' => 'users-export',
                'display_name' => 'تصدير المستخدمين',
                'description' => 'السماح بتصدير بيانات المستخدمين',
                'module' => 'users',
                'action' => 'export',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 14,
            ],

            // صلاحيات إدارة الأدوار
            [
                'name' => 'roles.create',
                'slug' => 'roles-create',
                'display_name' => 'إنشاء دور',
                'description' => 'السماح بإنشاء دور جديد',
                'module' => 'roles',
                'action' => 'create',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 20,
            ],
            [
                'name' => 'roles.read',
                'slug' => 'roles-read',
                'display_name' => 'عرض الأدوار',
                'description' => 'السماح بعرض قائمة الأدوار',
                'module' => 'roles',
                'action' => 'read',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 21,
            ],
            [
                'name' => 'roles.update',
                'slug' => 'roles-update',
                'display_name' => 'تعديل دور',
                'description' => 'السماح بتعديل بيانات الدور',
                'module' => 'roles',
                'action' => 'update',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 22,
            ],
            [
                'name' => 'roles.delete',
                'slug' => 'roles-delete',
                'display_name' => 'حذف دور',
                'description' => 'السماح بحذف الدور',
                'module' => 'roles',
                'action' => 'delete',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 23,
            ],

            // صلاحيات إدارة الصلاحيات
            [
                'name' => 'permissions.create',
                'slug' => 'permissions-create',
                'display_name' => 'إنشاء صلاحية',
                'description' => 'السماح بإنشاء صلاحية جديدة',
                'module' => 'permissions',
                'action' => 'create',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 30,
            ],
            [
                'name' => 'permissions.read',
                'slug' => 'permissions-read',
                'display_name' => 'عرض الصلاحيات',
                'description' => 'السماح بعرض قائمة الصلاحيات',
                'module' => 'permissions',
                'action' => 'read',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 31,
            ],
            [
                'name' => 'permissions.update',
                'slug' => 'permissions-update',
                'display_name' => 'تعديل صلاحية',
                'description' => 'السماح بتعديل بيانات الصلاحية',
                'module' => 'permissions',
                'action' => 'update',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 32,
            ],
            [
                'name' => 'permissions.delete',
                'slug' => 'permissions-delete',
                'display_name' => 'حذف صلاحية',
                'description' => 'السماح بحذف الصلاحية',
                'module' => 'permissions',
                'action' => 'delete',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 33,
            ],

            // صلاحيات إدارة المنتجات
            [
                'name' => 'products.create',
                'slug' => 'products-create',
                'display_name' => 'إنشاء منتج',
                'description' => 'السماح بإنشاء منتج جديد',
                'module' => 'products',
                'action' => 'create',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 40,
            ],
            [
                'name' => 'products.read',
                'slug' => 'products-read',
                'display_name' => 'عرض المنتجات',
                'description' => 'السماح بعرض قائمة المنتجات',
                'module' => 'products',
                'action' => 'read',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 41,
            ],
            [
                'name' => 'products.update',
                'slug' => 'products-update',
                'display_name' => 'تعديل منتج',
                'description' => 'السماح بتعديل بيانات المنتج',
                'module' => 'products',
                'action' => 'update',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 42,
            ],
            [
                'name' => 'products.delete',
                'slug' => 'products-delete',
                'display_name' => 'حذف منتج',
                'description' => 'السماح بحذف المنتج',
                'module' => 'products',
                'action' => 'delete',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 43,
            ],
            [
                'name' => 'products.export',
                'slug' => 'products-export',
                'display_name' => 'تصدير المنتجات',
                'description' => 'السماح بتصدير بيانات المنتجات',
                'module' => 'products',
                'action' => 'export',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 44,
            ],

            // صلاحيات إدارة الفئات
            [
                'name' => 'categories.create',
                'slug' => 'categories-create',
                'display_name' => 'إنشاء فئة',
                'description' => 'السماح بإنشاء فئة جديدة',
                'module' => 'categories',
                'action' => 'create',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 50,
            ],
            [
                'name' => 'categories.read',
                'slug' => 'categories-read',
                'display_name' => 'عرض الفئات',
                'description' => 'السماح بعرض قائمة الفئات',
                'module' => 'categories',
                'action' => 'read',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 51,
            ],
            [
                'name' => 'categories.update',
                'slug' => 'categories-update',
                'display_name' => 'تعديل فئة',
                'description' => 'السماح بتعديل بيانات الفئة',
                'module' => 'categories',
                'action' => 'update',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 52,
            ],
            [
                'name' => 'categories.delete',
                'slug' => 'categories-delete',
                'display_name' => 'حذف فئة',
                'description' => 'السماح بحذف الفئة',
                'module' => 'categories',
                'action' => 'delete',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 53,
            ],

            // صلاحيات إدارة البطاقات الرقمية
            [
                'name' => 'digital-cards.create',
                'slug' => 'digital-cards-create',
                'display_name' => 'إنشاء بطاقة رقمية',
                'description' => 'السماح بإنشاء بطاقة رقمية جديدة',
                'module' => 'digital-cards',
                'action' => 'create',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 60,
            ],
            [
                'name' => 'digital-cards.read',
                'slug' => 'digital-cards-read',
                'display_name' => 'عرض البطاقات الرقمية',
                'description' => 'السماح بعرض قائمة البطاقات الرقمية',
                'module' => 'digital-cards',
                'action' => 'read',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 61,
            ],
            [
                'name' => 'digital-cards.update',
                'slug' => 'digital-cards-update',
                'display_name' => 'تعديل بطاقة رقمية',
                'description' => 'السماح بتعديل بيانات البطاقة الرقمية',
                'module' => 'digital-cards',
                'action' => 'update',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 62,
            ],
            [
                'name' => 'digital-cards.delete',
                'slug' => 'digital-cards-delete',
                'display_name' => 'حذف بطاقة رقمية',
                'description' => 'السماح بحذف البطاقة الرقمية',
                'module' => 'digital-cards',
                'action' => 'delete',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 63,
            ],

            // صلاحيات إدارة السلات
            [
                'name' => 'carts.read',
                'slug' => 'carts-read',
                'display_name' => 'عرض السلات',
                'description' => 'السماح بعرض قائمة السلات',
                'module' => 'carts',
                'action' => 'read',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 70,
            ],
            [
                'name' => 'carts.manage',
                'slug' => 'carts-manage',
                'display_name' => 'إدارة السلات',
                'description' => 'السماح بإدارة السلات المتروكة',
                'module' => 'carts',
                'action' => 'manage',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 71,
            ],

            // صلاحيات إدارة الكوبونات
            [
                'name' => 'coupons.create',
                'slug' => 'coupons-create',
                'display_name' => 'إنشاء كوبون',
                'description' => 'السماح بإنشاء كوبون جديد',
                'module' => 'coupons',
                'action' => 'create',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 80,
            ],
            [
                'name' => 'coupons.read',
                'slug' => 'coupons-read',
                'display_name' => 'عرض الكوبونات',
                'description' => 'السماح بعرض قائمة الكوبونات',
                'module' => 'coupons',
                'action' => 'read',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 81,
            ],
            [
                'name' => 'coupons.update',
                'slug' => 'coupons-update',
                'display_name' => 'تعديل كوبون',
                'description' => 'السماح بتعديل بيانات الكوبون',
                'module' => 'coupons',
                'action' => 'update',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 82,
            ],
            [
                'name' => 'coupons.delete',
                'slug' => 'coupons-delete',
                'display_name' => 'حذف كوبون',
                'description' => 'السماح بحذف الكوبون',
                'module' => 'coupons',
                'action' => 'delete',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 83,
            ],

            // صلاحيات إدارة الإشعارات
            [
                'name' => 'notifications.create',
                'slug' => 'notifications-create',
                'display_name' => 'إنشاء إشعار',
                'description' => 'السماح بإنشاء إشعار جديد',
                'module' => 'notifications',
                'action' => 'create',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 90,
            ],
            [
                'name' => 'notifications.read',
                'slug' => 'notifications-read',
                'display_name' => 'عرض الإشعارات',
                'description' => 'السماح بعرض قائمة الإشعارات',
                'module' => 'notifications',
                'action' => 'read',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 91,
            ],
            [
                'name' => 'notifications.update',
                'slug' => 'notifications-update',
                'display_name' => 'تعديل إشعار',
                'description' => 'السماح بتعديل بيانات الإشعار',
                'module' => 'notifications',
                'action' => 'update',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 92,
            ],
            [
                'name' => 'notifications.delete',
                'slug' => 'notifications-delete',
                'display_name' => 'حذف إشعار',
                'description' => 'السماح بحذف الإشعار',
                'module' => 'notifications',
                'action' => 'delete',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 93,
            ],

            // صلاحيات إدارة التواصل
            [
                'name' => 'contacts.read',
                'slug' => 'contacts-read',
                'display_name' => 'عرض رسائل التواصل',
                'description' => 'السماح بعرض رسائل التواصل',
                'module' => 'contacts',
                'action' => 'read',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 100,
            ],
            [
                'name' => 'contacts.update',
                'slug' => 'contacts-update',
                'display_name' => 'تعديل رسالة تواصل',
                'description' => 'السماح بتعديل حالة رسالة التواصل',
                'module' => 'contacts',
                'action' => 'update',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 101,
            ],
            [
                'name' => 'contacts.delete',
                'slug' => 'contacts-delete',
                'display_name' => 'حذف رسالة تواصل',
                'description' => 'السماح بحذف رسالة التواصل',
                'module' => 'contacts',
                'action' => 'delete',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 102,
            ],

            // صلاحيات إدارة الإعدادات
            [
                'name' => 'settings.read',
                'slug' => 'settings-read',
                'display_name' => 'عرض الإعدادات',
                'description' => 'السماح بعرض إعدادات الموقع',
                'module' => 'settings',
                'action' => 'read',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 110,
            ],
            [
                'name' => 'settings.update',
                'slug' => 'settings-update',
                'display_name' => 'تعديل الإعدادات',
                'description' => 'السماح بتعديل إعدادات الموقع',
                'module' => 'settings',
                'action' => 'update',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 111,
            ],
        ];

        $createdPermissions = [];
        foreach ($permissions as $permissionData) {
            $permission = Permission::firstOrCreate(
                ['name' => $permissionData['name']],
                $permissionData
            );
            $createdPermissions[] = $permission;
        }

        return $createdPermissions;
    }

    /**
     * إنشاء الأدوار الأساسية
     */
    private function createRoles(array $permissions): void
    {
        // دور المدير العام
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            [
                'slug' => 'admin',
                'display_name' => 'مدير عام',
                'description' => 'مدير عام للنظام مع جميع الصلاحيات',
                'color' => '#DC2626',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 1,
            ]
        );

        // إعطاء جميع الصلاحيات للمدير العام
        $adminRole->permissions()->sync($permissions);

        // دور مدير المحتوى
        $contentManagerRole = Role::firstOrCreate(
            ['name' => 'content-manager'],
            [
                'slug' => 'content-manager',
                'display_name' => 'مدير المحتوى',
                'description' => 'مدير المحتوى والمنتجات',
                'color' => '#059669',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 2,
            ]
        );

        // صلاحيات مدير المحتوى
        $contentManagerPermissions = collect($permissions)->filter(function ($permission) {
            return in_array($permission->module, ['dashboard', 'products', 'categories', 'digital-cards']) ||
                   in_array($permission->name, ['dashboard.access', 'notifications.read', 'contacts.read']);
        })->pluck('id')->toArray();

        $contentManagerRole->permissions()->sync($contentManagerPermissions);

        // دور مدير المبيعات
        $salesManagerRole = Role::firstOrCreate(
            ['name' => 'sales-manager'],
            [
                'slug' => 'sales-manager',
                'display_name' => 'مدير المبيعات',
                'description' => 'مدير المبيعات والعملاء',
                'color' => '#7C3AED',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 3,
            ]
        );

        // صلاحيات مدير المبيعات
        $salesManagerPermissions = collect($permissions)->filter(function ($permission) {
            return in_array($permission->module, ['dashboard', 'users', 'carts', 'coupons', 'notifications', 'contacts']) ||
                   in_array($permission->name, ['dashboard.access', 'products.read', 'categories.read', 'digital-cards.read']);
        })->pluck('id')->toArray();

        $salesManagerRole->permissions()->sync($salesManagerPermissions);

        // دور موظف الدعم
        $supportRole = Role::firstOrCreate(
            ['name' => 'support'],
            [
                'slug' => 'support',
                'display_name' => 'موظف الدعم',
                'description' => 'موظف دعم العملاء',
                'color' => '#EA580C',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 4,
            ]
        );

        // صلاحيات موظف الدعم
        $supportPermissions = collect($permissions)->filter(function ($permission) {
            return in_array($permission->name, [
                'dashboard.access',
                'users.read',
                'contacts.read',
                'contacts.update',
                'notifications.read',
                'notifications.create',
                'carts.read',
                'products.read',
                'categories.read',
                'digital-cards.read'
            ]);
        })->pluck('id')->toArray();

        $supportRole->permissions()->sync($supportPermissions);

        // دور المستخدم العادي
        $userRole = Role::firstOrCreate(
            ['name' => 'user'],
            [
                'slug' => 'user',
                'display_name' => 'مستخدم عادي',
                'description' => 'مستخدم عادي بدون صلاحيات إدارية',
                'color' => '#6B7280',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 5,
            ]
        );

        // المستخدم العادي لا يحتاج صلاحيات إدارية
        $userRole->permissions()->sync([]);
    }
}
