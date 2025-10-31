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
        Schema::table('digital_cards', function (Blueprint $table) {
            // إضافة foreign key لـ order_item_id بعد إنشاء جدول order_items
            $table->foreign('order_item_id')->references('id')->on('order_items')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('digital_cards', function (Blueprint $table) {
            // إزالة foreign key
            $table->dropForeign(['order_item_id']);
        });
    }
};
