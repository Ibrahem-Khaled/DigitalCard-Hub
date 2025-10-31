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
        Schema::table('orders', function (Blueprint $table) {
            // Make user_id nullable for guest checkout
            $table->foreignId('user_id')->nullable()->change();

            // Make shipping_address nullable (digital products don't need shipping)
            $table->json('shipping_address')->nullable()->change();

            // billing_address is already set to required, which is correct
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Revert changes
            $table->foreignId('user_id')->nullable(false)->change();
            $table->json('shipping_address')->nullable(false)->change();
        });
    }
};
