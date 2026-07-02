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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->string('vnp_txn_ref'); // Mã giao dịch VNPay
            $table->decimal('amount', 15, 2);
            $table->string('bank_code')->nullable();
            $table->string('card_type')->nullable();
            $table->string('order_info');
            $table->string('vnp_response_code');
            $table->string('vnp_transaction_no')->nullable();
            $table->timestamp('pay_date')->nullable();
            $table->enum('status', ['pending', 'success', 'completed', 'failed'])->default('pending');
            $table->json('vnp_data')->nullable(); // Lưu toàn bộ data từ VNPay
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
