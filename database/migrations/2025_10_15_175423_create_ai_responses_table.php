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
        Schema::create('ai_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_message_id')->constrained()->onDelete('cascade');
            $table->foreignId('knowledge_base_id')->nullable()->constrained('ai_knowledge_bases')->onDelete('set null');
            $table->text('prompt');
            $table->text('response');
            $table->string('model');
            $table->integer('tokens_used');
            $table->decimal('processing_time', 8, 3);
            $table->decimal('confidence_score', 3, 2);
            $table->json('sources')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('generated_at');
            $table->timestamps();

            // Indexes
            $table->index(['chat_message_id', 'generated_at']);
            $table->index(['knowledge_base_id', 'generated_at']);
            $table->index(['model', 'generated_at']);
            $table->index('confidence_score');
            $table->index('generated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_responses');
    }
};