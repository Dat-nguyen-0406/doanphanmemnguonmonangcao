<?php

namespace App\Observers;

use App\Models\Showtime;
use App\Models\Booking;

class ShowtimeObserver
{
    /**
     * Handle the Showtime "deleting" event.
     * Xóa cascading: suất chiếu -> booking
     */
    public function deleting(Showtime $showtime): void
    {
        // Hủy tất cả booking pending liên quan đến suất chiếu này
        Booking::where('showtime_id', $showtime->id)
            ->where('status', '!=', 'confirmed')
            ->update(['status' => 'cancelled']);

        // Xóa booking seats relationship
        $bookingIds = Booking::where('showtime_id', $showtime->id)->pluck('id');
        if ($bookingIds->isNotEmpty()) {
            \DB::table('booking_seat')
                ->whereIn('booking_id', $bookingIds)
                ->delete();
        }
    }
}
