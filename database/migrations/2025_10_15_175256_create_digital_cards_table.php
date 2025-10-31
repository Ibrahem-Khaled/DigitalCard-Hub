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
        Schema::create('digital_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('card_code')->nullable();
            $table->string('card_pin')->nullable();
            $table->string('card_number')->nullable();
            $table->string('serial_number')->nullable();
            $table->decimal('value', 10, 2)->nullable();
            $table->string('currency', 3)->default('USD');
            $table->date('expiry_date')->nullable();
            $table->boolean('is_used')->default(false);
            $table->timestamp('used_at')->nullable();
            $table->foreignId('used_by')->nullable()->constrained('users')->onDelete('set null');
            $table->unsignedBigInteger('order_item_id')->nullable();
            $table->enum('status', ['active', 'used', 'expired', 'inactive'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();

            // Foreign Keys - سيتم إضافة order_item_id foreign key في migration منفصل بعد إنشاء order_items
            // $table->foreign('order_item_id')->references('id')->on('order_items')->onDelete('set null');

            // Indexes
            $table->index(['product_id', 'status']);
            $table->index(['is_used', 'status']);
            $table->index('card_code');
            $table->index('card_pin');
            $table->index('serial_number');
            $table->index('expiry_date');
            $table->index('used_by');
            $table->index('order_item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('digital_cards');
    }
};
