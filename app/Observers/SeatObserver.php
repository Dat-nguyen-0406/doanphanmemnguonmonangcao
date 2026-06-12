<?php

namespace App\Observers;

use App\Models\Seat;

class SeatObserver
{
    /**
     * Handle the Seat "deleting" event.
     * Xóa cascading: ghế -> booking_seat
     */
    public function deleting(Seat $seat): void
    {
        // Xóa tất cả booking_seat liên quan đến ghế này
        \DB::table('booking_seat')
            ->where('seat_id', $seat->id)
            ->delete();
    }
}
