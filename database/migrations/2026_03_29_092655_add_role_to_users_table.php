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
        Schema::table('users', function (Blueprint $table) {
            // Thay đổi hoặc cập nhật chú thích cho cột role
            // 0: Khách hàng, 1: Admin tổng, 2: QL Phim, 3: QL Quán ăn, 4: QL Bán hàng Online
            $table->integer('role')->default(0)->change(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
