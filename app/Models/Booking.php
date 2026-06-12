<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = ['user_id', 'showtime_id', 'total_price', 'status', 'booking_date'];
    protected $casts = [
        'booking_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function showtime()
    {
        return $this->belongsTo(Showtime::class);
    }

    public function seats()
    {
        return $this->belongsToMany(Seat::class, 'booking_seat')->withPivot('price');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
