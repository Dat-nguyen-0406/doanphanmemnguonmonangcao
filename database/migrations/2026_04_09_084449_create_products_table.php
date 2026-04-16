<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            
            // Giá và số lượng
            $table->decimal('price', 15, 2); // Tối đa 999 tỷ, 2 số thập phân
            $table->integer('stock')->default(0); // Số lượng tồn kho
            
            // Hình ảnh
            $table->string('image')->nullable();
            
            // Khóa ngoại
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade'); // Thuộc AEON nào
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');     // Chủ shop (Role 4) là ai
            
            $table->boolean('is_active')->default(true); // Trạng thái hiển thị
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};