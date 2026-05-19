<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestaurantBooking extends Model
{
    protected $fillable = [
        'user_id',
        'restaurant_id',
        'booking_date',
        'booking_time',
        'guests_count',
        'note',
        'status',
        'deposit_amount',
        'transaction_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}
