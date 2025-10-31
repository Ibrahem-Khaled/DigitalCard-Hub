<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Contact;
use App\Models\User;
use Carbon\Carbon;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // الحصول على مستخدمين للاختبار
        $users = User::take(2)->get();

        $contacts = [
            [
                'user_id' => $users->first()?->id,
                'name' => 'أحمد محمد علي',
                'email' => 'ahmed.mohamed@example.com',
                'phone' => '01234567890',
                'subject' => 'استفسار عن بطاقات PUBG Mobile',
                'message' => 'أريد معرفة المزيد عن بطاقات شحن PUBG Mobile المتوفرة وأسعارها',
                'type' => 'general',
                'priority' => 'medium',
                'status' => 'new',
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'created_at' => Carbon::now()->subDays(2),
            ],
            [
                'user_id' => null, // زائر غير مسجل
                'name' => 'فاطمة أحمد',
                'email' => 'fatima.ahmed@example.com',
                'phone' => '01234567891',
                'subject' => 'مشكلة في تسليم البطاقة',
                'message' => 'لم أستلم بطاقة الشحن التي اشتريتها منذ ساعتين، رقم الطلب #12345',
                'type' => 'support',
                'priority' => 'high',
                'status' => 'in_progress',
                'ip_address' => '192.168.1.101',
                'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_0 like Mac OS X)',
                'created_at' => Carbon::now()->subHours(3),
            ],
            [
                'user_id' => $users->last()?->id,
                'name' => 'محمد حسن',
                'email' => 'mohamed.hassan@example.com',
                'phone' => '01234567892',
                'subject' => 'اقتراح لتحسين الخدمة',
                'message' => 'أقترح إضافة بطاقات شحن لألعاب جديدة مثل Genshin Impact و Valorant',
                'type' => 'suggestion',
                'priority' => 'low',
                'status' => 'new',
                'ip_address' => '192.168.1.102',
                'user_agent' => 'Mozilla/5.0 (Android 11; Mobile; rv:68.0) Gecko/68.0 Firefox/88.0',
                'created_at' => Carbon::now()->subDays(1),
            ],
            [
                'user_id' => null,
                'name' => 'سارة محمود',
                'email' => 'sara.mahmoud@example.com',
                'phone' => '01234567893',
                'subject' => 'شكوى في جودة البطاقة',
                'message' => 'البطاقة التي اشتريتها لا تعمل، أريد استرداد المبلغ أو استبدالها',
                'type' => 'complaint',
                'priority' => 'urgent',
                'status' => 'new',
                'ip_address' => '192.168.1.103',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'created_at' => Carbon::now()->subHours(1),
            ],
            [
                'user_id' => null,
                'name' => 'علي إبراهيم',
                'email' => 'ali.ibrahim@example.com',
                'phone' => '01234567894',
                'subject' => 'طلب شراكة تجارية',
                'message' => 'أريد مناقشة إمكانية الشراكة في توزيع بطاقات الشحن في منطقتي',
                'type' => 'business',
                'priority' => 'medium',
                'status' => 'new',
                'ip_address' => '192.168.1.104',
                'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36',
                'created_at' => Carbon::now()->subDays(3),
            ],
            [
                'user_id' => null,
                'name' => 'نور الدين',
                'email' => 'nour.eldeen@example.com',
                'phone' => '01234567895',
                'subject' => 'مشكلة تقنية في الموقع',
                'message' => 'الموقع لا يعمل بشكل صحيح على متصفح Safari، يرجى إصلاح هذه المشكلة',
                'type' => 'technical',
                'priority' => 'high',
                'status' => 'resolved',
                'admin_response' => 'تم إصلاح المشكلة في الإصدار الجديد من الموقع',
                'responded_at' => Carbon::now()->subHours(2),
                'ip_address' => '192.168.1.105',
                'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15',
                'created_at' => Carbon::now()->subDays(5),
            ],
            [
                'user_id' => null,
                'name' => 'محمود عبد الرحمن',
                'email' => 'mahmoud.abdelrahman@example.com',
                'phone' => '01234567896',
                'subject' => 'طلب استرداد',
                'message' => 'أريد استرداد المبلغ لبطاقة لم أستخدمها بعد، رقم الطلب #12346',
                'type' => 'general',
                'priority' => 'medium',
                'status' => 'closed',
                'admin_response' => 'تم استرداد المبلغ بنجاح',
                'responded_at' => Carbon::now()->subDays(1),
                'ip_address' => '192.168.1.106',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'created_at' => Carbon::now()->subDays(7),
            ],
        ];

        foreach ($contacts as $contactData) {
            Contact::create($contactData);
        }

        $this->command->info('تم إنشاء بيانات الاتصالات التجريبية بنجاح!');
    }
}
