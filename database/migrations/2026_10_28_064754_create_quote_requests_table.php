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
        Schema::create('quote_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone')->nullable();
            $table->string('locale', 5)->default('ar');
            $table->enum('status', [
                'draft',
                'under_review',
                'technical_review',
                'commercial_review',
                'sent',
                'accepted',
                'rejected',
                'expired',
                'cancelled'
            ])->default('draft');
            $table->text('requirements');
            $table->json('specifications')->nullable()->comment('JSON object with custom specifications');
            $table->text('delivery_location')->nullable();
            $table->integer('estimated_quantity')->nullable();
            $table->text('internal_notes')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('quote_request_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_request_id')->constrained()->cascadeOnDelete();
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type');
            $table->bigInteger('file_size');
            $table->timestamps();
        });

        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->string('quote_number')->unique();
            $table->foreignId('quote_request_id')->constrained()->cascadeOnDelete();
            $table->enum('status', [
                'draft',
                'sent',
                'accepted',
                'rejected',
                'expired',
                'cancelled'
            ])->default('draft');
            $table->string('currency', 3)->default('SAR');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('shipping_estimate', 10, 2)->nullable();
            $table->decimal('total', 10, 2);
            $table->integer('validity_days')->default(30);
            $table->text('terms_and_conditions')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('quote_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('description');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->text('specifications')->nullable();
            $table->timestamps();
        });

        Schema::create('quote_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('approval_token')->unique();
            $table->boolean('approved')->default(false);
            $table->text('notes')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quote_requests');
    }
};
