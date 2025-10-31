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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();
            $table->string('sku')->unique();
            $table->decimal('price', 10, 2);
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->decimal('cost_price', 10, 2)->nullable();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('brand')->nullable();
            $table->string('image')->nullable();
            $table->json('gallery')->nullable();
            $table->boolean('is_digital')->default(true);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('loyalty_points_earn')->default(0); // نقاط الولاء المكتسبة عند الشراء
            $table->integer('loyalty_points_cost')->default(0); // نقاط الولاء المطلوبة للشراء
            $table->string('card_type')->nullable(); // نوع البطاقة (هدايا، شحن، اشتراك، إلخ)
            $table->string('card_provider')->nullable(); // مزود البطاقة (أمازون، ستيم، نتفليكس، إلخ)
            $table->string('card_region')->nullable(); // المنطقة الجغرافية للبطاقة
            $table->json('card_denominations')->nullable(); // الفئات المتاحة للبطاقة
            $table->boolean('is_instant_delivery')->default(true); // التسليم الفوري للبطاقات الرقمية
            $table->text('delivery_instructions')->nullable(); // تعليمات التسليم
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->json('tags')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            // Indexes
            $table->index(['slug', 'is_active']);
            $table->index(['category_id', 'is_active']);
            $table->index(['is_digital', 'is_active']);
            $table->index(['is_featured', 'is_active']);
            $table->index(['card_type', 'is_active']);
            $table->index(['card_provider', 'is_active']);
            $table->index('sku');
            $table->index('sort_order');
            $table->index('loyalty_points_earn');
            $table->index('loyalty_points_cost');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
