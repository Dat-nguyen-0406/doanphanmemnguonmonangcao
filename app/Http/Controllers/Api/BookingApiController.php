<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Seat;
use App\Models\Showtime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * API Controller cho Booking (seats, availability)
 * Phục vụ các request từ client-side
 */
class BookingApiController extends Controller
{
    /**
     * Lấy danh sách ghế có sẵn cho suất chiếu
     * GET /api/showtimes/{showtime_id}/available-seats
     */
    public function availableSeats($showtimeId)
    {
        try {
            $showtime = Showtime::with('branch', 'movie')->findOrFail($showtimeId);

            // Lấy danh sách ghế đã đặt (không cancelled)
            $bookedSeatIds = DB::table('booking_seat')
                ->join('bookings', 'booking_seat.booking_id', '=', 'bookings.id')
                ->where('bookings.showtime_id', $showtimeId)
                ->where('bookings.status', '!=', 'cancelled')
                ->pluck('booking_seat.seat_id')
                ->toArray();

            // Lấy tất cả ghế của chi nhánh, group by row
            $allSeats = Seat::where('branch_id', $showtime->branch_id)
                ->orderBy('row')
                ->orderBy('seat_number')
                ->get()
                ->groupBy('row');

            // Tính toán ghế có sẵn
            $totalSeats = Seat::where('branch_id', $showtime->branch_id)->count();
            $availableCount = $totalSeats - count($bookedSeatIds);

            // Format seat data
            $seatsByRow = $allSeats->map(function ($rowSeats) use ($bookedSeatIds) {
                return $rowSeats->map(function ($seat) use ($bookedSeatIds) {
                    return [
                        'id' => $seat->id,
                        'row' => $seat->row,
                        'seat_number' => $seat->seat_number,
                        'type' => $seat->type,
                        'is_booked' => in_array($seat->id, $bookedSeatIds),
                        'price' => $seat->type === 'vip' 
                            ? $showtime->price_vip ?? ($showtime->price * 1.5)
                            : $showtime->price_normal ?? $showtime->price,
                    ];
                });
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'showtime' => [
                        'id' => $showtime->id,
                        'movie_title' => $showtime->movie->title,
                        'branch_name' => $showtime->branch->name,
                        'start_time' => $showtime->start_time,
                        'price_normal' => $showtime->price_normal ?? $showtime->price,
                        'price_vip' => $showtime->price_vip ?? ($showtime->price * 1.5),
                    ],
                    'seats_by_row' => $seatsByRow,
                    'booked_seat_ids' => $bookedSeatIds,
                    'available_count' => $availableCount,
                    'total_count' => $totalSeats,
                ],
                'message' => 'Lấy danh sách ghế có sẵn thành công.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy suất chiếu.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy thông tin booking của user hiện tại
     * GET /api/my-bookings
     */
    public function myBookings(Request $request)
    {
        try {
            $bookings = Booking::where('user_id', auth()->id())
                ->with('showtime.movie', 'showtime.branch', 'seats', 'payment')
                ->orderBy('booking_date', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $bookings,
                'message' => 'Lấy danh sách booking của bạn thành công.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy chi tiết booking
     * GET /api/bookings/{id}
     */
    public function show($bookingId)
    {
        try {
            $booking = Booking::with('user', 'showtime.movie', 'showtime.branch', 'seats', 'payment')
                ->findOrFail($bookingId);

            // Kiểm tra quyền: user chỉ xem được booking của mình
            if ($booking->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền xem booking này.'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'data' => $booking,
                'message' => 'Lấy chi tiết booking thành công.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy booking.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hủy booking (chỉ pending bookings)
     * POST /api/bookings/{id}/cancel
     */
    public function cancel($bookingId)
    {
        try {
            $booking = Booking::findOrFail($bookingId);

            // Kiểm tra quyền
            if ($booking->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền hủy booking này.'
                ], 403);
            }

            // Chỉ có thể hủy pending bookings
            if ($booking->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ có thể hủy booking đang xử lý.'
                ], 400);
            }

            $booking->update(['status' => 'cancelled']);

            return response()->json([
                'success' => true,
                'data' => $booking,
                'message' => 'Hủy booking thành công.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy booking.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
}
