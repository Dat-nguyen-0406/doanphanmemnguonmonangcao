<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestaurantTable extends Model
{
    protected $fillable = [
        'restaurant_id',
        'table_number',
        'label',
        'capacity',
        'floor',
        'shape',
        'position_x',
        'position_y',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function bookings()
    {
        return $this->hasMany(RestaurantBooking::class, 'table_id');
    }

    /**
     * Kiểm tra bàn có bị đặt tại ngày & giờ cụ thể không
     */
    public function isBookedAt(string $date, string $time): bool
    {
        return $this->bookings()
            ->where('booking_date', $date)
            ->where('booking_time', $time)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();
    }
}
