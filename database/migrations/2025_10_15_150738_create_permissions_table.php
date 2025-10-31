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
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('display_name');
            $table->text('description')->nullable();
            $table->string('module')->nullable(); // e.g., 'dashboard', 'products', 'orders'
            $table->string('action')->nullable(); // e.g., 'create', 'read', 'update', 'delete'
            $table->boolean('is_active')->default(true);
            $table->boolean('is_system')->default(false); // System permissions cannot be deleted
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            // Indexes
            $table->index(['slug', 'is_active']);
            $table->index(['module', 'action']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
