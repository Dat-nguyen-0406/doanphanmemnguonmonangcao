<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('restaurant_menu_items')) {
            Schema::create('restaurant_menu_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('restaurant_id')->constrained('restaurants')->onDelete('cascade');
                $table->string('name');
                $table->enum('category', ['main', 'appetizer', 'dessert', 'drink'])->default('main');
                $table->decimal('price', 12, 2);
                $table->text('description')->nullable();
                $table->string('image_url')->nullable();
                $table->boolean('is_available')->default(true);
                $table->timestamps();
            });
        }

        // Tạo bảng lưu pre-order items
        if (!Schema::hasTable('restaurant_booking_items')) {
            Schema::create('restaurant_booking_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('booking_id')->constrained('restaurant_bookings')->onDelete('cascade');
                $table->foreignId('menu_item_id')->constrained('restaurant_menu_items')->onDelete('cascade');
                $table->integer('quantity');
                $table->decimal('unit_price', 12, 2);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_booking_items');
        Schema::dropIfExists('restaurant_menu_items');
    }
};
