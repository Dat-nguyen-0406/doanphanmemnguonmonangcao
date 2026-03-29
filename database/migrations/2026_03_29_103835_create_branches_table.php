<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained('cities')->onDelete('cascade'); // Khóa ngoại kết nối với bảng cities
            $table->string('name'); // Tên chi nhánh (Ví dụ: AEON Mall Long Biên)
            $table->string('address'); // Địa chỉ chi tiết
            $table->string('image_url')->nullable(); // Đường dẫn ảnh tòa nhà
            $table->string('map_link')->nullable(); // Link Google Maps
            $table->text('description')->nullable(); // Giới thiệu ngắn
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};