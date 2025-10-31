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
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_session_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->text('message');
            $table->enum('type', ['text', 'ai_response', 'system', 'file'])->default('text');
            $table->enum('sender', ['user', 'ai', 'system'])->default('user');
            $table->json('metadata')->nullable();
            $table->json('ai_response')->nullable();
            $table->decimal('processing_time', 8, 3)->nullable();
            $table->integer('tokens_used')->nullable();
            $table->timestamp('sent_at');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['chat_session_id', 'sent_at']);
            $table->index(['user_id', 'sent_at']);
            $table->index(['sender', 'type']);
            $table->index('sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};