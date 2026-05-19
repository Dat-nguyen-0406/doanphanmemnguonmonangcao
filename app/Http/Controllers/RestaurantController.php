<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Restaurant;
use App\Models\RestaurantBooking;
use App\Services\VnpayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RestaurantController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'nullable|integer|exists:branches,id',
        ]);

        $branches = Branch::all();

        $query = Restaurant::with('branch')->where('is_active', true);

        if (!empty($validated['branch_id'])) {
            $query->where('branch_id', $validated['branch_id']);
        }

        $restaurants = $query->get();

        return view('restaurants.index', compact('restaurants', 'branches'));
    }

    public function showBookForm($id)
    {
        $restaurant = Restaurant::where('is_active', true)->findOrFail($id);

        return view('restaurants.book', compact('restaurant'));
    }

    public function submitBooking(Request $request, $id)
    {
        $validated = $request->validate([
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'required|date_format:H:i',
            'guests_count' => 'required|integer|min:1|max:50',
            'note' => 'nullable|string|max:500',
        ]);

        $bookingDateTime = \Carbon\Carbon::parse($validated['booking_date'] . ' ' . $validated['booking_time'], 'Asia/Ho_Chi_Minh');
        $minAllowedTime = \Carbon\Carbon::now('Asia/Ho_Chi_Minh')->addHour();

        if ($bookingDateTime->lt($minAllowedTime)) {
            return back()->with('error', 'Thời gian đặt bàn phải cách hiện tại ít nhất 1 tiếng. Vui lòng chọn giờ sau ' . $minAllowedTime->format('H:i d/m/Y'));
        }

        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để thực hiện đặt bàn.');
        }

        try {
            DB::beginTransaction();

            $restaurant = Restaurant::where('id', $id)
                ->where('is_active', true)
                ->lockForUpdate()
                ->firstOrFail();

            $totalCapacity = $restaurant->tables()
                ->where('is_active', true)
                ->sum('capacity');

            if ($totalCapacity <= 0) {
                DB::rollBack();
                return back()->with('error', 'Nhà hàng hiện chưa có bàn khả dụng. Vui lòng chọn nhà hàng khác!');
            }

            $bookedGuests = RestaurantBooking::where('restaurant_id', $restaurant->id)
                ->where('booking_date', $validated['booking_date'])
                ->where('booking_time', $validated['booking_time'])
                ->whereIn('status', ['pending', 'confirmed'])
                ->sum('guests_count');

            if (($bookedGuests + $validated['guests_count']) > $totalCapacity) {
                DB::rollBack();
                return back()->with('error', 'Xin lỗi, khung giờ này nhà hàng đã hết bàn trống. Vui lòng chọn giờ khác!');
            }

            $booking = RestaurantBooking::create([
                'user_id' => Auth::id(),
                'restaurant_id' => $restaurant->id,
                'booking_date' => $validated['booking_date'],
                'booking_time' => $validated['booking_time'],
                'guests_count' => $validated['guests_count'],
                'note' => $validated['note'] ?? null,
                'status' => 'pending',
                'deposit_amount' => 100000,
                'transaction_id' => 'TXN-' . Str::uuid(),
            ]);

            DB::commit();

            return redirect()->route('booking.payment', $booking->id);
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', 'Có lỗi hệ thống xảy ra, vui lòng thử lại sau!');
        }
    }

    public function showPayment($id)
    {
        $booking = $this->findUserBooking($id);

        if ($booking->status === 'confirmed') {
            return redirect()->route('booking.success', $booking->id);
        }

        if ($booking->status !== 'pending') {
            return redirect()->route('restaurants.index')->with('error', 'Đơn đặt bàn này không thể thanh toán.');
        }

        return view('restaurants.payment', compact('booking'));
    }

    public function processVnPay(Request $request, $id)
    {
        $booking = $this->findUserBooking($id);

        if ($booking->status === 'confirmed') {
            return redirect()->route('booking.success', $booking->id);
        }

        if ($booking->status !== 'pending') {
            return redirect()->route('restaurants.index')->with('error', 'Đơn đặt bàn này không thể thanh toán.');
        }

        try {
            $vnpayUrl = app(VnpayService::class)->createPaymentUrl($booking, $request);
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', 'Cấu hình VNPAY chưa đầy đủ. Vui lòng thử lại sau!');
        }

        return redirect($vnpayUrl);
    }

    public function vnpayReturn(Request $request)
    {
        $vnpay = app(VnpayService::class);

        if (!$vnpay->hasValidSignature($request->all())) {
            return redirect()->route('restaurants.index')->with('error', 'Chữ ký bảo mật không hợp lệ.');
        }

        if ($request->vnp_ResponseCode !== '00') {
            return redirect()->route('restaurants.index')->with('error', 'Giao dịch thanh toán đã bị hủy.');
        }

        $booking = DB::transaction(function () use ($request) {
            $booking = RestaurantBooking::where('transaction_id', $request->vnp_TxnRef)
                ->lockForUpdate()
                ->first();

            if (!$booking) {
                return null;
            }

            $expectedAmount = (int) round(((float) $booking->deposit_amount) * 100);
            if ((int) $request->vnp_Amount !== $expectedAmount) {
                return false;
            }

            if ($booking->status === 'pending') {
                $booking->update(['status' => 'confirmed']);
            }

            return $booking;
        });

        if ($booking === null) {
            return redirect()->route('restaurants.index')->with('error', 'Không tìm thấy giao dịch đặt bàn.');
        }

        if ($booking === false) {
            return redirect()->route('restaurants.index')->with('error', 'Số tiền thanh toán không khớp với đơn đặt bàn.');
        }

        return redirect()->route('booking.success', $booking->id);
    }

    public function showSuccess($id)
    {
        $booking = $this->findUserBooking($id, ['restaurant']);

        if ($booking->status === 'pending') {
            return redirect()->route('booking.payment', $booking->id);
        }

        if (!in_array($booking->status, ['confirmed', 'completed'], true)) {
            return redirect()->route('restaurants.index')->with('error', 'Đơn đặt bàn này chưa được xác nhận.');
        }

        return view('restaurants.success', compact('booking'));
    }

    private function findUserBooking($id, array $with = []): RestaurantBooking
    {
        return RestaurantBooking::with($with)
            ->where('user_id', Auth::id())
            ->findOrFail($id);
    }
}
