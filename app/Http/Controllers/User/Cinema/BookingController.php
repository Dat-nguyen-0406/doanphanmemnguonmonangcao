<?php

namespace App\Http\Controllers\User\Cinema;

use App\Http\Controllers\Controller;

use App\Models\Booking;
use App\Models\Seat;
use App\Models\Showtime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    /**
     * Lấy danh sách tất cả bookings (API)
     */
    public function index()
    {
        return Booking::with('user', 'showtime.movie', 'seats')->get();
    }

    /**
     * Tạo booking mới (Form-based)
     */
    public function store(Request $request)
    {
        $request->validate([
            'showtime_id' => 'required|exists:showtimes,id',
            'seat_ids' => 'required|array|min:1',
            'seat_ids.*' => 'exists:seats,id',
        ], [
            'showtime_id.required' => 'Vui lòng chọn suất chiếu.',
            'seat_ids.required' => 'Vui lòng chọn ít nhất một ghế.',
            'seat_ids.*.exists' => 'Ghế được chọn không hợp lệ.',
        ]);

        $showtime = Showtime::findOrFail($request->showtime_id);
        
        // Validate seats belong to same branch
        $seats = Seat::whereIn('id', $request->seat_ids)
            ->where('branch_id', $showtime->branch_id)
            ->get();

        if ($seats->count() != count($request->seat_ids)) {
            return back()->with('error', 'Một số ghế không thuộc chi nhánh này.');
        }

        // Check seat availability
        $bookedSeats = DB::table('booking_seat')
            ->join('bookings', 'booking_seat.booking_id', '=', 'bookings.id')
            ->where('bookings.showtime_id', $showtime->id)
            ->whereIn('booking_seat.seat_id', $request->seat_ids)
            ->where('bookings.status', '!=', 'cancelled')
            ->exists();

        if ($bookedSeats) {
            return back()->with('error', 'Một số ghế đã được đặt. Vui lòng chọn ghế khác.');
        }

        // Calculate total price
        $totalPrice = $seats->sum(function ($seat) use ($showtime) {
            return $seat->type === 'vip' ? $showtime->price_vip ?? ($showtime->price * 1.5) : $showtime->price_normal ?? $showtime->price;
        });

        // Create booking in transaction
        $booking = DB::transaction(function () use ($request, $showtime, $seats, $totalPrice) {
            $booking = Booking::create([
                'user_id' => auth()->id(),
                'showtime_id' => $showtime->id,
                'total_price' => $totalPrice,
                'status' => 'pending',
                'booking_date' => now(),
            ]);

            // Attach seats with prices
            foreach ($seats as $seat) {
                $price = $seat->type === 'vip' 
                    ? $showtime->price_vip ?? ($showtime->price * 1.5)
                    : $showtime->price_normal ?? $showtime->price;
                    
                $booking->seats()->attach($seat->id, ['price' => $price]);
            }

            return $booking;
        });

        return redirect()->route('payment.page', $booking->id)
            ->with('success', 'Đặt vé thành công! Vui lòng thanh toán để hoàn tất.');
    }

    /**
     * Lấy chi tiết booking
     */
    public function show(Booking $booking)
    {
        // Kiểm tra quyền: user chỉ xem được booking của mình, admin xem tất cả
        if (!auth()->user()->isAdmin() && $booking->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return $booking->load('user', 'showtime.movie', 'seats');
    }

    /**
     * Cập nhật booking status
     */
    public function update(Request $request, Booking $booking)
    {
        // Chỉ admin có thể cập nhật status
        if (!auth()->user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled',
        ]);

        $booking->update($request->only('status'));
        
        return response()->json([
            'message' => 'Cập nhật trạng thái thành công.',
            'booking' => $booking->load('user', 'showtime.movie', 'seats')
        ]);
    }

    /**
     * Xóa booking (hủy đặt vé)
     */
    public function destroy(Booking $booking)
    {
        // Kiểm tra quyền
        if (!auth()->user()->isAdmin() && $booking->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Chỉ có thể hủy booking ở trạng thái pending
        if ($booking->status !== 'pending') {
            return response()->json(['error' => 'Không thể hủy booking đã xác nhận.'], 400);
        }

        $booking->update(['status' => 'cancelled']);
        
        return response()->json(['message' => 'Hủy đặt vé thành công.']);
    }

    /**
     * Lấy danh sách ghế có sẵn cho suất chiếu
     * API endpoint: /api/showtimes/{showtime}/available-seats
     */
    public function availableSeats($showtimeId)
    {
        $showtime = Showtime::with('branch')->findOrFail($showtimeId);

        // Lấy danh sách ghế đã đặt
        $bookedSeatIds = DB::table('booking_seat')
            ->join('bookings', 'booking_seat.booking_id', '=', 'bookings.id')
            ->where('bookings.showtime_id', $showtimeId)
            ->where('bookings.status', '!=', 'cancelled')
            ->pluck('booking_seat.seat_id')
            ->toArray();

        // Lấy tất cả ghế của chi nhánh
        $allSeats = Seat::where('branch_id', $showtime->branch_id)
            ->orderBy('row')
            ->orderBy('seat_number')
            ->get();

        // Group by row for easier display
        $seatsByRow = $allSeats->groupBy('row');

        return response()->json([
            'showtime' => $showtime->load('movie'),
            'available_seat_count' => $allSeats->count() - count($bookedSeatIds),
            'booked_seat_ids' => $bookedSeatIds,
            'seats_by_row' => $seatsByRow->map(function ($rowSeats) {
                return $rowSeats->map(function ($seat) {
                    return [
                        'id' => $seat->id,
                        'row' => $seat->row,
                        'seat_number' => $seat->seat_number,
                        'type' => $seat->type,
                    ];
                });
            }),
        ]);
    }
}

