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
        Schema::table('restaurant_bookings', function (Blueprint $table) {
            $table->unique('transaction_id', 'restaurant_bookings_transaction_id_unique');
            $table->index(
                ['restaurant_id', 'booking_date', 'booking_time', 'status'],
                'restaurant_booking_slot_status_index'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurant_bookings', function (Blueprint $table) {
            $table->dropUnique('restaurant_bookings_transaction_id_unique');
            $table->dropIndex('restaurant_booking_slot_status_index');
        });
    }
};
