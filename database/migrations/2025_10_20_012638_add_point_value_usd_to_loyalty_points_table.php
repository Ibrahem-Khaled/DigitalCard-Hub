<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('loyalty_points', function (Blueprint $table) {
            $table->decimal('point_value_usd', 8, 4)->default(0.01)->after('points')->comment('قيمة كل نقطة بالدولار الأمريكي');
            $table->decimal('total_value_usd', 10, 2)->nullable()->after('point_value_usd')->comment('إجمالي القيمة بالدولار');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loyalty_points', function (Blueprint $table) {
            $table->dropColumn(['point_value_usd', 'total_value_usd']);
        });
    }
};
