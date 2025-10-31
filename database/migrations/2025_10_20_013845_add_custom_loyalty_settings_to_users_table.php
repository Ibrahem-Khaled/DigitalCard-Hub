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
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('custom_point_value_usd', 8, 4)->nullable()->after('is_active')->comment('قيمة النقطة المخصصة للمستخدم');
            $table->decimal('custom_points_per_dollar', 8, 2)->nullable()->after('custom_point_value_usd')->comment('عدد النقاط لكل دولار');
            $table->boolean('use_custom_loyalty_settings')->default(false)->after('custom_points_per_dollar')->comment('استخدام إعدادات الولاء المخصصة');
            $table->json('loyalty_settings_override')->nullable()->after('use_custom_loyalty_settings')->comment('إعدادات الولاء المخصصة');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'custom_point_value_usd',
                'custom_points_per_dollar',
                'use_custom_loyalty_settings',
                'loyalty_settings_override'
            ]);
        });
    }
};
