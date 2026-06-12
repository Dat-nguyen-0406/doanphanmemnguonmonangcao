<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestaurantBookingItem extends Model
{
    protected $table = 'restaurant_booking_items';

    protected $fillable = [
        'booking_id',
        'menu_item_id',
        'quantity',
        'unit_price',
    ];

    protected $casts = [
        'unit_price' => 'float',
    ];

    public function booking()
    {
        return $this->belongsTo(RestaurantBooking::class, 'booking_id');
    }

    public function menuItem()
    {
        return $this->belongsTo(RestaurantMenuItem::class, 'menu_item_id');
    }

    public function getSubtotalAttribute(): float
    {
        return $this->unit_price * $this->quantity;
    }
}
