<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Showtime extends Model
{
    protected $fillable = ['movie_id', 'branch_id', 'start_time', 'price', 'price_normal', 'price_vip'];
    protected $casts = [
        'start_time' => 'datetime',
    ];

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function availableSeatsCount()
    {
        $totalSeats = \App\Models\Seat::where('branch_id', $this->branch_id)->count();
        $bookedSeats = \DB::table('booking_seat')
            ->join('bookings', 'booking_seat.booking_id', '=', 'bookings.id')
            ->where('bookings.showtime_id', $this->id)
            ->where('bookings.status', '!=', 'cancelled')
            ->count();
        
        return $totalSeats - $bookedSeats;
    }
}
