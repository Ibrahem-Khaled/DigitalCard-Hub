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
        Schema::create('sliders', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // عنوان السلايدر
            $table->text('description')->nullable(); // وصف السلايدر
            $table->string('image'); // صورة السلايدر
            $table->string('button_text')->nullable(); // نص الزر
            $table->string('button_url')->nullable(); // رابط الزر
            $table->integer('sort_order')->default(0); // ترتيب السلايدر
            $table->boolean('is_active')->default(true); // حالة السلايدر
            $table->string('position')->default('homepage'); // موقع السلايدر (homepage, category, etc)
            $table->json('settings')->nullable(); // إعدادات إضافية
            $table->timestamp('starts_at')->nullable(); // تاريخ بداية العرض
            $table->timestamp('ends_at')->nullable(); // تاريخ نهاية العرض
            $table->timestamps();

            // فهارس
            $table->index(['is_active', 'position']);
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sliders');
    }
};
