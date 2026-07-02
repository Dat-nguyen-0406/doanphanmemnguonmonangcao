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
        Schema::create('restaurant_tables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained('restaurants')->onDelete('cascade');
            $table->string('table_number'); // Tên/Số bàn (VD: Bàn số 1, VIP 1)
            $table->integer('capacity'); // Sức chứa (VD: 4 người, 10 người)
            $table->boolean('is_active')->default(true); // Bàn có đang sử dụng được không
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurant_tables');
    }
};
