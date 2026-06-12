<?php

use App\Http\Controllers\Api\MovieApiController;
use App\Http\Controllers\Api\ShowtimeApiController;
use App\Http\Controllers\Api\BookingApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// ============== PUBLIC API ENDPOINTS ==============

// Movies - Lấy danh sách, tìm kiếm phim
Route::prefix('movies')->group(function () {
    Route::get('/', [MovieApiController::class, 'index'])->name('api.movies.index');
    Route::get('/{id}', [MovieApiController::class, 'show'])->name('api.movies.show');
    Route::get('/search', [MovieApiController::class, 'search'])->name('api.movies.search');
    Route::get('/latest', [MovieApiController::class, 'latest'])->name('api.movies.latest');
    Route::get('/{movie_id}/showtimes', [ShowtimeApiController::class, 'byMovie'])->name('api.movies.showtimes');
});

// Showtimes - Lấy danh sách, filter lịch chiếu
Route::prefix('showtimes')->group(function () {
    Route::get('/', [ShowtimeApiController::class, 'index'])->name('api.showtimes.index');
    Route::get('/{id}', [ShowtimeApiController::class, 'show'])->name('api.showtimes.show');
    Route::get('/{showtime_id}/available-seats', [BookingApiController::class, 'availableSeats'])
        ->name('api.showtimes.available-seats');
});

// Branches - Suất chiếu theo chi nhánh
Route::prefix('branches')->group(function () {
    Route::get('/{branch_id}/showtimes', [ShowtimeApiController::class, 'byBranch'])
        ->name('api.branches.showtimes');
});

// ============== AUTHENTICATED API ENDPOINTS ==============

Route::middleware('auth:sanctum')->group(function () {
    // Bookings - Xem, hủy booking
    Route::prefix('bookings')->group(function () {
        Route::get('/my-bookings', [BookingApiController::class, 'myBookings'])
            ->name('api.bookings.my-bookings');
        Route::get('/{id}', [BookingApiController::class, 'show'])
            ->name('api.bookings.show');
        Route::post('/{id}/cancel', [BookingApiController::class, 'cancel'])
            ->name('api.bookings.cancel');
    });
});
