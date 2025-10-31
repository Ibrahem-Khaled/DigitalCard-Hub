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
        Schema::table('payments', function (Blueprint $table) {
            // Make user_id nullable for guest checkout
            $table->foreignId('user_id')->nullable()->change();

            // Make payment_gateway nullable (not all payments go through gateways)
            $table->string('payment_gateway')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Revert changes
            $table->foreignId('user_id')->nullable(false)->change();
            $table->string('payment_gateway')->nullable(false)->change();
        });
    }
};
