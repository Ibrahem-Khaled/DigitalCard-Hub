<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Referral;
use App\Models\ReferralReward;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ReferralSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // مسح البيانات الموجودة
        DB::table('referral_rewards')->delete();
        DB::table('referrals')->delete();

        // الحصول على المستخدمين
        $users = User::all();

        if ($users->count() < 2) {
            $this->command->warn('يجب أن يكون هناك مستخدمين على الأقل لإنشاء الإحالات. يرجى تشغيل UserSeeder أولاً.');
            return;
        }

        // إنشاء إحالات متنوعة
        $this->createActiveReferrals($users);
        $this->createCompletedReferrals($users);
        $this->createExpiredReferrals($users);
        $this->createCancelledReferrals($users);

        // إنشاء مكافآت للإحالات
        $this->createReferralRewards();

        $this->command->info('تم إنشاء بيانات الإحالات بنجاح!');
    }

    /**
     * إنشاء إحالات نشطة
     */
    private function createActiveReferrals($users)
    {
        for ($i = 0; $i < 15; $i++) {
            $referrer = $users->random();
            $referred = $users->where('id', '!=', $referrer->id)->random();

            Referral::create([
                'referrer_id' => $referrer->id,
                'referred_id' => $referred->id,
                'referral_code' => Referral::generateReferralCode(),
                'status' => 'active',
                'commission_amount' => rand(5, 50),
                'commission_percentage' => rand(5, 20),
                'reward_amount' => rand(10, 100),
                'reward_percentage' => rand(10, 30),
                'expires_at' => now()->addDays(rand(30, 365)),
                'notes' => rand(0, 1) ? 'إحالة نشطة جديدة' : null,
            ]);
        }
    }

    /**
     * إنشاء إحالات مكتملة
     */
    private function createCompletedReferrals($users)
    {
        for ($i = 0; $i < 20; $i++) {
            $referrer = $users->random();
            $referred = $users->where('id', '!=', $referrer->id)->random();
            $completedAt = now()->subDays(rand(1, 90));

            Referral::create([
                'referrer_id' => $referrer->id,
                'referred_id' => $referred->id,
                'referral_code' => Referral::generateReferralCode(),
                'status' => 'completed',
                'commission_amount' => rand(10, 100),
                'commission_percentage' => rand(10, 25),
                'reward_amount' => rand(20, 200),
                'reward_percentage' => rand(15, 40),
                'completed_at' => $completedAt,
                'expires_at' => $completedAt->addDays(rand(30, 180)),
                'notes' => rand(0, 1) ? 'إحالة مكتملة بنجاح' : null,
            ]);
        }
    }

    /**
     * إنشاء إحالات منتهية الصلاحية
     */
    private function createExpiredReferrals($users)
    {
        for ($i = 0; $i < 8; $i++) {
            $referrer = $users->random();
            $referred = $users->where('id', '!=', $referrer->id)->random();
            $expiredAt = now()->subDays(rand(1, 30));

            Referral::create([
                'referrer_id' => $referrer->id,
                'referred_id' => $referred->id,
                'referral_code' => Referral::generateReferralCode(),
                'status' => 'expired',
                'commission_amount' => rand(5, 30),
                'commission_percentage' => rand(5, 15),
                'reward_amount' => rand(10, 50),
                'reward_percentage' => rand(10, 25),
                'expires_at' => $expiredAt,
                'notes' => rand(0, 1) ? 'إحالة منتهية الصلاحية' : null,
            ]);
        }
    }

    /**
     * إنشاء إحالات ملغية
     */
    private function createCancelledReferrals($users)
    {
        for ($i = 0; $i < 5; $i++) {
            $referrer = $users->random();
            $referred = $users->where('id', '!=', $referrer->id)->random();

            Referral::create([
                'referrer_id' => $referrer->id,
                'referred_id' => $referred->id,
                'referral_code' => Referral::generateReferralCode(),
                'status' => 'cancelled',
                'commission_amount' => rand(5, 25),
                'commission_percentage' => rand(5, 10),
                'reward_amount' => rand(5, 30),
                'reward_percentage' => rand(5, 15),
                'expires_at' => now()->addDays(rand(30, 180)),
                'notes' => rand(0, 1) ? 'إحالة ملغية من قبل المستخدم' : null,
            ]);
        }
    }

    /**
     * إنشاء مكافآت الإحالات
     */
    private function createReferralRewards()
    {
        $referrals = Referral::whereIn('status', ['completed', 'active'])->get();

        foreach ($referrals as $referral) {
            // إنشاء مكافآت للمحيل
            if ($referral->commission_amount > 0) {
                ReferralReward::create([
                    'referral_id' => $referral->id,
                    'user_id' => $referral->referrer_id,
                    'type' => 'commission',
                    'amount' => $referral->commission_amount,
                    'points' => rand(100, 1000),
                    'status' => $referral->status === 'completed' ? 'processed' : 'pending',
                    'processed_at' => $referral->status === 'completed' ? $referral->completed_at : null,
                    'expires_at' => now()->addDays(rand(30, 365)),
                    'description' => 'عمولة إحالة للمستخدم المحيل',
                ]);
            }

            // إنشاء مكافآت للمحال إليه
            if ($referral->reward_amount > 0) {
                ReferralReward::create([
                    'referral_id' => $referral->id,
                    'user_id' => $referral->referred_id,
                    'type' => 'reward',
                    'amount' => $referral->reward_amount,
                    'points' => rand(50, 500),
                    'status' => $referral->status === 'completed' ? 'processed' : 'pending',
                    'processed_at' => $referral->status === 'completed' ? $referral->completed_at : null,
                    'expires_at' => now()->addDays(rand(30, 365)),
                    'description' => 'مكافأة إحالة للمستخدم المحال إليه',
                ]);
            }

            // إنشاء مكافآت إضافية للإحالات المكتملة
            if ($referral->status === 'completed' && rand(0, 1)) {
                ReferralReward::create([
                    'referral_id' => $referral->id,
                    'user_id' => $referral->referrer_id,
                    'type' => 'bonus',
                    'amount' => rand(5, 25),
                    'points' => rand(50, 200),
                    'status' => 'processed',
                    'processed_at' => $referral->completed_at,
                    'expires_at' => now()->addDays(rand(30, 180)),
                    'description' => 'مكافأة إضافية لإكمال الإحالة',
                ]);
            }
        }
    }
}
