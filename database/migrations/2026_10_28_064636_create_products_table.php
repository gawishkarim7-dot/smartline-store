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
            $table->string('sku')->unique();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['ready_made', 'custom_made', 'hybrid'])->default('ready_made');
            $table->decimal('base_price', 10, 2);
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->string('currency', 3)->default('SAR');
            $table->decimal('weight', 8, 2)->nullable()->comment('Weight in kg');
            $table->decimal('length', 8, 2)->nullable()->comment('Length in cm');
            $table->decimal('width', 8, 2)->nullable()->comment('Width in cm');
            $table->decimal('height', 8, 2)->nullable()->comment('Height in cm');
            $table->integer('lead_time_days')->default(0)->comment('Estimated days for production/delivery');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamp('sale_from')->nullable();
            $table->timestamp('sale_until')->nullable();
            $table->timestamps();
        });

        Schema::create('product_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('name');
            $table->text('description');
            $table->text('specifications')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->unique(['product_id', 'locale']);
            $table->timestamps();
        });

        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->string('alt_text')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });

        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('sku')->unique();
            $table->string('name');
            $table->json('attributes')->comment('JSON object with variant attributes like size, color');
            $table->decimal('price_adjustment', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
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
