<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('showtimes', function (Blueprint $table) {
            $table->id();
            // Đã điền 'movies' và 'branches' vào để tránh lỗi Incorrect table name ''
            $table->foreignId('movie_id')->constrained('movies')->onDelete('cascade');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->datetime('start_time');
            $table->decimal('price', 15, 2); // Để 15, 2 cho giá tiền Việt Nam
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('showtimes');
    }
};