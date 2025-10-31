<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cart;
use Carbon\Carbon;

class MarkAbandonedCarts extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'carts:mark-abandoned {--hours=24 : عدد الساعات بعدها تعتبر السلة متروكة}';

    /**
     * The console command description.
     */
    protected $description = 'وضع علامة على السلات المتروكة التي مر عليها أكثر من 24 ساعة بدون نشاط';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hours = $this->option('hours');
        $cutoffTime = Carbon::now()->subHours($hours);

        // البحث عن السلات النشطة التي لم يتم تحديثها منذ أكثر من 24 ساعة
        $abandonedCarts = Cart::where('is_abandoned', false)
            ->where(function ($query) use ($cutoffTime) {
                $query->where('last_activity_at', '<', $cutoffTime)
                      ->orWhere(function ($subQuery) use ($cutoffTime) {
                          $subQuery->whereNull('last_activity_at')
                                   ->where('created_at', '<', $cutoffTime);
                      });
            })
            ->get();

        $count = 0;

        foreach ($abandonedCarts as $cart) {
            $cart->markAsAbandoned();
            $count++;
        }

        if ($count > 0) {
            $this->info("تم وضع علامة على {$count} سلة كمتروكة.");
        } else {
            $this->info('لا توجد سلات متروكة جديدة.');
        }

        // عرض إحصائيات السلات المتروكة
        $this->displayAbandonedStats();

        return Command::SUCCESS;
    }

    /**
     * عرض إحصائيات السلات المتروكة
     */
    private function displayAbandonedStats()
    {
        $stats = [
            'total_abandoned' => Cart::abandoned()->count(),
            'abandoned_last_24h' => Cart::abandoned()->where('abandoned_at', '>=', now()->subDay())->count(),
            'abandoned_last_week' => Cart::abandoned()->where('abandoned_at', '>=', now()->subWeek())->count(),
            'abandoned_value' => Cart::abandoned()->sum('total_amount'),
        ];

        $this->newLine();
        $this->info('إحصائيات السلات المتروكة:');
        $this->table(
            ['المقياس', 'القيمة'],
            [
                ['إجمالي السلات المتروكة', number_format($stats['total_abandoned'])],
                ['متروكة في آخر 24 ساعة', number_format($stats['abandoned_last_24h'])],
                ['متروكة في آخر أسبوع', number_format($stats['abandoned_last_week'])],
                ['قيمة السلات المتروكة', '$' . number_format($stats['abandoned_value'], 2)],
            ]
        );
    }
}
