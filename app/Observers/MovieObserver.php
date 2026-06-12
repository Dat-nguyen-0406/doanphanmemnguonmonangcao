<?php

namespace App\Observers;

use App\Models\Movie;
use App\Models\Showtime;
use App\Models\Booking;

class MovieObserver
{
    /**
     * Handle the Movie "deleting" event.
     * Xóa cascading: phim -> suất chiếu -> booking
     */
    public function deleting(Movie $movie): void
    {
        // Lấy tất cả suất chiếu của phim này
        $showtimes = $movie->showtimes()->pluck('id');

        // Hủy tất cả booking liên quan đến suất chiếu này
        if ($showtimes->isNotEmpty()) {
            Booking::whereIn('showtime_id', $showtimes)
                ->where('status', '!=', 'confirmed')
                ->update(['status' => 'cancelled']);

            // Xóa suất chiếu (cascade sẽ xử lý booking details)
            Showtime::whereIn('id', $showtimes)->delete();
        }
    }
}
