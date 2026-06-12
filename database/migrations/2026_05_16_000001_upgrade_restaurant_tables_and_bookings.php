<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Thêm các cột còn thiếu cho restaurant_tables
        Schema::table('restaurant_tables', function (Blueprint $table) {
            if (!Schema::hasColumn('restaurant_tables', 'label')) {
                $table->string('label')->nullable()->after('table_number');
            }
            if (!Schema::hasColumn('restaurant_tables', 'floor')) {
                $table->integer('floor')->default(1)->after('capacity');
            }
            if (!Schema::hasColumn('restaurant_tables', 'shape')) {
                $table->enum('shape', ['square', 'round', 'long'])->default('square')->after('floor');
            }
            if (!Schema::hasColumn('restaurant_tables', 'position_x')) {
                $table->integer('position_x')->default(0)->after('shape');
            }
            if (!Schema::hasColumn('restaurant_tables', 'position_y')) {
                $table->integer('position_y')->default(0)->after('position_x');
            }
        });

        // Thêm table_id vào restaurant_bookings
        Schema::table('restaurant_bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('restaurant_bookings', 'table_id')) {
                $table->foreignId('table_id')->nullable()->constrained('restaurant_tables')->onDelete('set null')->after('restaurant_id');
            }
            if (!Schema::hasColumn('restaurant_bookings', 'pre_order_amount')) {
                $table->decimal('pre_order_amount', 12, 2)->default(0)->after('deposit_amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('restaurant_tables', function (Blueprint $table) {
            $table->dropColumnIfExists(['label', 'floor', 'shape', 'position_x', 'position_y']);
        });
        Schema::table('restaurant_bookings', function (Blueprint $table) {
            $table->dropForeignIfExists(['table_id']);
            $table->dropColumnIfExists(['table_id', 'pre_order_amount']);
        });
    }
};
