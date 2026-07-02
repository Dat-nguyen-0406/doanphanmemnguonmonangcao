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
        Schema::table('showtimes', function (Blueprint $table) {
            $table->decimal('price_normal', 8, 2)->default(120000)->after('price');
            $table->decimal('price_vip', 8, 2)->default(150000)->after('price_normal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('showtimes', function (Blueprint $table) {
            $table->dropColumn(['price_normal', 'price_vip']);
        });
    }
};
