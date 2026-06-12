<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    protected $fillable = ['branch_id', 'row', 'seat_number', 'type'];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function bookings()
    {
        return $this->belongsToMany(Booking::class, 'booking_seat')->withPivot('price');
    }
}
