<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('receipt_path')->nullable()->after('notes');
            $table->string('payment_reference')->nullable()->after('transaction_id');
            $table->timestamp('receipt_uploaded_at')->nullable()->after('receipt_path');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['receipt_path', 'payment_reference', 'receipt_uploaded_at']);
        });
    }
};
