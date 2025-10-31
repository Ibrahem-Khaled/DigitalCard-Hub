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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('group')->default('general');
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('value')->nullable();
            $table->enum('type', ['text', 'textarea', 'number', 'boolean', 'select', 'file', 'email', 'url', 'json'])->default('text');
            $table->json('options')->nullable(); // For select options
            $table->string('validation_rules')->nullable();
            $table->boolean('is_public')->default(false); // Can be accessed publicly
            $table->boolean('is_required')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            // Indexes
            $table->index(['group', 'sort_order']);
            $table->index('is_public');
            $table->index('is_required');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
