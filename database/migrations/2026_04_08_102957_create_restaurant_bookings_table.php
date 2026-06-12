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
        Schema::create('restaurant_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('restaurant_id')->constrained('restaurants')->onDelete('cascade');

            $table->date('booking_date'); // Ngày đặt
            $table->time('booking_time'); // Giờ đến
            $table->integer('guests_count'); // Số lượng khách
            $table->text('note')->nullable(); // Ghi chú

            // CÁC TRƯỜNG PHỤC VỤ THANH TOÁN & CỌC
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');
            // pending: Chờ thanh toán cọc
            // confirmed: Đã cọc, giữ bàn thành công
            // cancelled: Hủy
            // completed: Khách đã đến ăn xong

            $table->decimal('deposit_amount', 12, 2)->default(0); // Số tiền phải cọc
            $table->string('transaction_id')->nullable(); // Mã giao dịch trả về từ VNPAY/MoMo

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurant_bookings');
    }
};
