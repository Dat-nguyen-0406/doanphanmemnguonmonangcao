<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Movie;
use App\Models\Showtime;
use App\Models\Seat;
use App\Observers\MovieObserver;
use App\Observers\ShowtimeObserver;
use App\Observers\SeatObserver;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Register Model Observers (từ doanphanmem)
        Movie::observe(MovieObserver::class);
        Showtime::observe(ShowtimeObserver::class);
        Seat::observe(SeatObserver::class);
    }
}
