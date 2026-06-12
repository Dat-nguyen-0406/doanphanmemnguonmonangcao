<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestaurantBooking extends Model
{
    protected $fillable = [
        'user_id',
        'restaurant_id',
        'table_id',
        'booking_date',
        'booking_time',
        'guests_count',
        'note',
        'status',
        'deposit_amount',
        'pre_order_amount',
        'transaction_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function table()
    {
        return $this->belongsTo(RestaurantTable::class, 'table_id');
    }

    public function items()
    {
        return $this->hasMany(RestaurantBookingItem::class, 'booking_id');
    }

    public function getTotalAmountAttribute(): float
    {
        return $this->deposit_amount + $this->pre_order_amount;
    }
}
