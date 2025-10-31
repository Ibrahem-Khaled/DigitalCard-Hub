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
        Schema::create('loyalty_point_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loyalty_point_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('points');
            $table->enum('type', ['earned', 'redeemed', 'expired', 'bonus']);
            $table->string('source');
            $table->unsignedBigInteger('source_id')->nullable();
            $table->text('description')->nullable();
            $table->integer('balance_before');
            $table->integer('balance_after');
            $table->timestamp('processed_at');
            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'processed_at']);
            $table->index(['type', 'source']);
            $table->index('processed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalty_point_transactions');
    }
};