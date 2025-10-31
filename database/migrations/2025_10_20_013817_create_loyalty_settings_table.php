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
        Schema::create('loyalty_settings', function (Blueprint $table) {
            $table->id();

            // الإعدادات العامة
            $table->string('setting_key')->unique()->comment('مفتاح الإعداد');
            $table->text('setting_value')->nullable()->comment('قيمة الإعداد');
            $table->string('setting_type')->default('string')->comment('نوع الإعداد: string, number, boolean, json');
            $table->text('description')->nullable()->comment('وصف الإعداد');
            $table->string('category')->default('general')->comment('فئة الإعداد');
            $table->boolean('is_active')->default(true)->comment('هل الإعداد نشط');
            $table->boolean('is_editable')->default(true)->comment('هل يمكن تعديل الإعداد');
            $table->integer('sort_order')->default(0)->comment('ترتيب الإعداد');

            $table->timestamps();

            // فهارس
            $table->index(['category', 'is_active']);
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalty_settings');
    }
};
