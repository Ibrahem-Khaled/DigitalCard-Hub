<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // مسح البيانات الموجودة
        DB::table('notifications')->delete();

        // الحصول على المستخدمين
        $users = User::all();

        if ($users->count() < 1) {
            $this->command->warn('يجب أن يكون هناك مستخدمين على الأقل لإنشاء الإشعارات. يرجى تشغيل UserSeeder أولاً.');
            return;
        }

        // إنشاء إشعارات متنوعة
        $this->createOrderNotifications($users);
        $this->createPaymentNotifications($users);
        $this->createShippingNotifications($users);
        $this->createPromotionNotifications($users);
        $this->createSystemNotifications($users);

        $this->command->info('تم إنشاء بيانات الإشعارات بنجاح!');
    }

    /**
     * إنشاء إشعارات الطلبات
     */
    private function createOrderNotifications($users)
    {
        for ($i = 0; $i < 20; $i++) {
            $user = $users->random();
            $titles = [
                'تم تأكيد طلبك',
                'تم تحديث حالة طلبك',
                'طلبك جاهز للشحن',
                'تم إلغاء طلبك',
                'طلبك قيد المراجعة'
            ];
            $messages = [
                'تم تأكيد طلبك رقم #' . rand(1000, 9999) . ' بنجاح',
                'تم تحديث حالة طلبك إلى: ' . $this->getRandomOrderStatus(),
                'طلبك جاهز للشحن وسيتم إرساله قريباً',
                'تم إلغاء طلبك بسبب عدم توفر المنتج',
                'طلبك قيد المراجعة من قبل فريقنا'
            ];

            Notification::create([
                'user_id' => $user->id,
                'type' => 'order',
                'title' => $titles[array_rand($titles)],
                'message' => $messages[array_rand($messages)],
                'data' => ['order_id' => rand(1000, 9999), 'status' => $this->getRandomOrderStatus()],
                'channel' => $this->getRandomChannel(),
                'priority' => $this->getRandomPriority(),
                'read_at' => rand(0, 1) ? now()->subDays(rand(1, 30)) : null,
                'sent_at' => now()->subDays(rand(1, 30)),
            ]);
        }
    }

    /**
     * إنشاء إشعارات الدفع
     */
    private function createPaymentNotifications($users)
    {
        for ($i = 0; $i < 15; $i++) {
            $user = $users->random();
            $titles = [
                'تم استلام دفعتك',
                'فشل في معالجة الدفع',
                'تم استرداد مبلغك',
                'دفعتك معلقة',
                'تم تأكيد الدفع'
            ];
            $messages = [
                'تم استلام دفعتك بقيمة ' . rand(50, 500) . ' دولار',
                'فشل في معالجة دفعتك، يرجى المحاولة مرة أخرى',
                'تم استرداد مبلغ ' . rand(50, 500) . ' دولار إلى حسابك',
                'دفعتك معلقة قيد المراجعة',
                'تم تأكيد دفعتك بنجاح'
            ];

            Notification::create([
                'user_id' => $user->id,
                'type' => 'payment',
                'title' => $titles[array_rand($titles)],
                'message' => $messages[array_rand($messages)],
                'data' => ['amount' => rand(50, 500), 'payment_method' => $this->getRandomPaymentMethod()],
                'channel' => $this->getRandomChannel(),
                'priority' => $this->getRandomPriority(),
                'read_at' => rand(0, 1) ? now()->subDays(rand(1, 30)) : null,
                'sent_at' => now()->subDays(rand(1, 30)),
            ]);
        }
    }

    /**
     * إنشاء إشعارات الشحن
     */
    private function createShippingNotifications($users)
    {
        for ($i = 0; $i < 12; $i++) {
            $user = $users->random();
            $titles = [
                'تم شحن طلبك',
                'طلبك في الطريق',
                'تم تسليم طلبك',
                'تأخير في الشحن',
                'طلبك وصل إلى وجهته'
            ];
            $messages = [
                'تم شحن طلبك رقم #' . rand(1000, 9999) . ' بنجاح',
                'طلبك في الطريق وسيصل خلال ' . rand(1, 5) . ' أيام',
                'تم تسليم طلبك بنجاح',
                'هناك تأخير في شحن طلبك، نعتذر عن الإزعاج',
                'طلبك وصل إلى وجهته وجاهز للتسليم'
            ];

            Notification::create([
                'user_id' => $user->id,
                'type' => 'shipping',
                'title' => $titles[array_rand($titles)],
                'message' => $messages[array_rand($messages)],
                'data' => ['tracking_number' => 'TRK' . rand(100000, 999999), 'carrier' => $this->getRandomCarrier()],
                'channel' => $this->getRandomChannel(),
                'priority' => $this->getRandomPriority(),
                'read_at' => rand(0, 1) ? now()->subDays(rand(1, 30)) : null,
                'sent_at' => now()->subDays(rand(1, 30)),
            ]);
        }
    }

    /**
     * إنشاء إشعارات ترويجية
     */
    private function createPromotionNotifications($users)
    {
        for ($i = 0; $i < 18; $i++) {
            $user = $users->random();
            $titles = [
                'عرض خاص محدود',
                'خصم 50% على جميع المنتجات',
                'عرض نهاية الأسبوع',
                'كوبون خصم جديد',
                'عرض الصيف الكبير'
            ];
            $messages = [
                'استمتع بعرض خاص محدود على منتجاتنا المختارة',
                'خصم 50% على جميع المنتجات لفترة محدودة',
                'عرض نهاية الأسبوع - خصومات رائعة',
                'كوبون خصم جديد بقيمة ' . rand(10, 100) . ' دولار',
                'عرض الصيف الكبير - خصومات تصل إلى 70%'
            ];

            Notification::create([
                'user_id' => $user->id,
                'type' => 'promotion',
                'title' => $titles[array_rand($titles)],
                'message' => $messages[array_rand($messages)],
                'data' => ['discount_percentage' => rand(10, 70), 'coupon_code' => 'SAVE' . rand(10, 99)],
                'channel' => $this->getRandomChannel(),
                'priority' => 'normal',
                'read_at' => rand(0, 1) ? now()->subDays(rand(1, 30)) : null,
                'sent_at' => now()->subDays(rand(1, 30)),
            ]);
        }
    }

    /**
     * إنشاء إشعارات النظام
     */
    private function createSystemNotifications($users)
    {
        for ($i = 0; $i < 10; $i++) {
            $user = $users->random();
            $titles = [
                'تحديث النظام',
                'صيانة مجدولة',
                'تحديث سياسة الخصوصية',
                'تحديث شروط الاستخدام',
                'تحديث أمني مهم'
            ];
            $messages = [
                'تم تحديث النظام بنجاح، استمتع بالميزات الجديدة',
                'سيتم إجراء صيانة مجدولة غداً من الساعة 2-4 صباحاً',
                'تم تحديث سياسة الخصوصية، يرجى مراجعتها',
                'تم تحديث شروط الاستخدام، يرجى الموافقة عليها',
                'تحديث أمني مهم لحماية حسابك'
            ];

            Notification::create([
                'user_id' => $user->id,
                'type' => 'system',
                'title' => $titles[array_rand($titles)],
                'message' => $messages[array_rand($messages)],
                'data' => ['version' => 'v' . rand(1, 5) . '.' . rand(0, 9)],
                'channel' => 'database',
                'priority' => $this->getRandomPriority(),
                'read_at' => rand(0, 1) ? now()->subDays(rand(1, 30)) : null,
                'sent_at' => now()->subDays(rand(1, 30)),
            ]);
        }
    }

    /**
     * الحصول على قناة عشوائية
     */
    private function getRandomChannel()
    {
        $channels = ['database', 'email', 'sms', 'push'];
        return $channels[array_rand($channels)];
    }

    /**
     * الحصول على أولوية عشوائية
     */
    private function getRandomPriority()
    {
        $priorities = ['low', 'normal', 'high', 'urgent'];
        return $priorities[array_rand($priorities)];
    }

    /**
     * الحصول على حالة طلب عشوائية
     */
    private function getRandomOrderStatus()
    {
        $statuses = ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'];
        return $statuses[array_rand($statuses)];
    }

    /**
     * الحصول على طريقة دفع عشوائية
     */
    private function getRandomPaymentMethod()
    {
        $methods = ['credit_card', 'bank_transfer', 'paypal', 'apple_pay', 'google_pay'];
        return $methods[array_rand($methods)];
    }

    /**
     * الحصول على شركة شحن عشوائية
     */
    private function getRandomCarrier()
    {
        $carriers = ['DHL', 'FedEx', 'UPS', 'Aramex', 'SMSA'];
        return $carriers[array_rand($carriers)];
    }
}
