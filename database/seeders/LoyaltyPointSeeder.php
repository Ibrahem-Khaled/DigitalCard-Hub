<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LoyaltyPoint;
use App\Models\LoyaltyPointTransaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LoyaltyPointSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // مسح البيانات الموجودة
        DB::table('loyalty_point_transactions')->delete();
        DB::table('loyalty_points')->delete();

        // الحصول على المستخدمين
        $users = User::all();

        if ($users->count() < 1) {
            $this->command->warn('يجب أن يكون هناك مستخدمين على الأقل لإنشاء نقاط الولاء. يرجى تشغيل UserSeeder أولاً.');
            return;
        }

        // إنشاء نقاط ولاء متنوعة
        $this->createEarnedPoints($users);
        $this->createRedeemedPoints($users);
        $this->createExpiredPoints($users);
        $this->createBonusPoints($users);

        // إنشاء معاملات للنقاط
        $this->createLoyaltyPointTransactions();

        $this->command->info('تم إنشاء بيانات نقاط الولاء بنجاح!');
    }

    /**
     * إنشاء نقاط مكتسبة
     */
    private function createEarnedPoints($users)
    {
        for ($i = 0; $i < 30; $i++) {
            $user = $users->random();
            $points = rand(10, 500);
            $sources = ['purchase', 'referral', 'review', 'manual'];
            $source = $sources[array_rand($sources)];

            $loyaltyPoint = LoyaltyPoint::create([
                'user_id' => $user->id,
                'points' => $points,
                'type' => 'earned',
                'source' => $source,
                'source_id' => $source === 'purchase' ? rand(1, 100) : null,
                'description' => $this->getDescriptionForSource($source),
                'expires_at' => rand(0, 1) ? now()->addDays(rand(30, 365)) : null,
                'is_active' => true,
            ]);
        }
    }

    /**
     * إنشاء نقاط مستردة
     */
    private function createRedeemedPoints($users)
    {
        for ($i = 0; $i < 15; $i++) {
            $user = $users->random();
            $points = rand(50, 300);

            $loyaltyPoint = LoyaltyPoint::create([
                'user_id' => $user->id,
                'points' => -$points,
                'type' => 'redeemed',
                'source' => 'purchase',
                'source_id' => rand(1, 100),
                'description' => 'استرداد نقاط للشراء',
                'expires_at' => null,
                'is_active' => true,
            ]);
        }
    }

    /**
     * إنشاء نقاط منتهية الصلاحية
     */
    private function createExpiredPoints($users)
    {
        for ($i = 0; $i < 10; $i++) {
            $user = $users->random();
            $points = rand(20, 200);
            $expiredAt = now()->subDays(rand(1, 30));

            $loyaltyPoint = LoyaltyPoint::create([
                'user_id' => $user->id,
                'points' => -$points,
                'type' => 'expired',
                'source' => 'system',
                'source_id' => null,
                'description' => 'نقاط منتهية الصلاحية',
                'expires_at' => $expiredAt,
                'is_active' => false,
            ]);
        }
    }

    /**
     * إنشاء نقاط مكافآت
     */
    private function createBonusPoints($users)
    {
        for ($i = 0; $i < 20; $i++) {
            $user = $users->random();
            $points = rand(25, 150);
            $sources = ['bonus', 'referral', 'manual'];
            $source = $sources[array_rand($sources)];

            $loyaltyPoint = LoyaltyPoint::create([
                'user_id' => $user->id,
                'points' => $points,
                'type' => 'bonus',
                'source' => $source,
                'source_id' => $source === 'referral' ? rand(1, 50) : null,
                'description' => $this->getDescriptionForSource($source),
                'expires_at' => rand(0, 1) ? now()->addDays(rand(60, 180)) : null,
                'is_active' => true,
            ]);
        }
    }

    /**
     * إنشاء معاملات نقاط الولاء
     */
    private function createLoyaltyPointTransactions()
    {
        $loyaltyPoints = LoyaltyPoint::all();

        foreach ($loyaltyPoints as $point) {
            // حساب الرصيد قبل وبعد
            $balanceBefore = LoyaltyPoint::getTotalPointsForUser($point->user_id) - $point->points;
            $balanceAfter = LoyaltyPoint::getTotalPointsForUser($point->user_id);

            LoyaltyPointTransaction::create([
                'loyalty_point_id' => $point->id,
                'user_id' => $point->user_id,
                'points' => $point->points,
                'type' => $point->type,
                'source' => $point->source,
                'source_id' => $point->source_id,
                'description' => $point->description,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'processed_at' => $point->created_at,
            ]);
        }
    }

    /**
     * الحصول على وصف للمصدر
     */
    private function getDescriptionForSource($source)
    {
        $descriptions = [
            'purchase' => 'نقاط مكتسبة من الشراء',
            'referral' => 'نقاط مكتسبة من الإحالة',
            'review' => 'نقاط مكتسبة من التقييم',
            'manual' => 'نقاط مضافة يدوياً',
            'bonus' => 'نقاط مكافأة إضافية',
            'system' => 'نقاط من النظام',
        ];

        return $descriptions[$source] ?? 'نقاط ولاء';
    }
}
