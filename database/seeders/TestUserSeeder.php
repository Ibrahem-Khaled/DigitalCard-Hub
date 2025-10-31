<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    public function run(): void
    {
        // إنشاء مستخدم تجريبي
        $user = User::updateOrCreate(
            ['email' => 'test@test.com'],
            [
                'first_name' => 'مستخدم',
                'last_name' => 'تجريبي',
                'phone' => '0500000000',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );

        // إضافة دور العميل
        $customerRole = Role::where('slug', 'customer')->first();
        if ($customerRole && !$user->roles()->where('role_id', $customerRole->id)->exists()) {
            $user->roles()->attach($customerRole->id, [
                'assigned_at' => now(),
                'assigned_by' => null,
            ]);
        }

        $this->command->info('تم إنشاء المستخدم التجريبي:');
        $this->command->info('البريد: test@test.com');
        $this->command->info('كلمة المرور: password');
    }
}

