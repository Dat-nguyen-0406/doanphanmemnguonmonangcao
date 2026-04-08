<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\RestaurantBooking;

class RestaurantController extends Controller
{
    public function index(Request $request)
    {
        // 1. Lấy tất cả chi nhánh để hiện thị vào thẻ <select> ở Frontend
        $branches = Branch::all();

        // 2. Logic lọc: Nếu có branch_id thì lọc, không thì lấy tất cả
        $query = Restaurant::with('branch')->where('is_active', true);

        if ($request->has('branch_id') && $request->branch_id != '') {
            $query->where('branch_id', $request->branch_id);
        }

        $restaurants = $query->get();

        // 3. Trả về view chúng ta đã làm ở bước trước
        return view('restaurants.index', compact('restaurants', 'branches'));
    }
    // Hiển thị form đặt bàn cho một nhà hàng cụ thể (2.2.2.2)
    public function showBookForm($id)
    {
        // Lấy thông tin nhà hàng dựa vào ID, nếu không thấy sẽ báo lỗi 404
        $restaurant = Restaurant::findOrFail($id);

        return view('restaurants.book', compact('restaurant'));
    }

    // Xử lý logic đặt bàn và cọc tiền (2.2.2.3)
    public function submitBooking(Request $request, $id)
    {
        // 1. Validate dữ liệu đầu vào
        $request->validate([
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'required',
            'guests_count' => 'required|integer|min:1',
        ]);

        // Giả lập User ID (Vì dự án bạn có thể chưa login, tạm thời lấy user ID = 1)
        // Trong thực tế sẽ là: $userId = Auth::id();
        $userId = Auth::id() ?? 1;

        try {
            // BẮT ĐẦU TRANSACTION: Chống trùng lặp dữ liệu
            DB::beginTransaction();

            // Lấy thông tin nhà hàng và KHÓA DÒNG (lockForUpdate)
            // Lệnh này ép các truy vấn khác phải xếp hàng chờ đợi cho đến khi Transaction này xong
            $restaurant = Restaurant::where('id', $id)->lockForUpdate()->firstOrFail();

            // Tính tổng sức chứa của nhà hàng (Tổng capacity của tất cả các bàn)
            $totalCapacity = $restaurant->tables()->sum('capacity');

            // Tính tổng số khách đã đặt trùng ngày, trùng giờ và trạng thái đơn đang Pending hoặc Confirmed
            $bookedGuests = RestaurantBooking::where('restaurant_id', $id)
                ->where('booking_date', $request->booking_date)
                ->where('booking_time', $request->booking_time)
                ->whereIn('status', ['pending', 'confirmed'])
                ->sum('guests_count');

            // Kiểm tra xem nhà hàng còn đủ chỗ cho số người này không?
            if (($bookedGuests + $request->guests_count) > $totalCapacity) {
                // Nếu không đủ chỗ -> Hủy Transaction
                DB::rollBack();
                return back()->with('error', 'Xin lỗi, khung giờ này nhà hàng đã hết bàn trống. Vui lòng chọn giờ khác!');
            }

            // Nếu đủ chỗ -> Tạo đơn đặt bàn với trạng thái 'Pending' (Chờ cọc)
            // Giả sử phí cọc mặc định là 100,000 VNĐ / đơn
            $booking = RestaurantBooking::create([
                'user_id' => $userId,
                'restaurant_id' => $restaurant->id,
                'booking_date' => $request->booking_date,
                'booking_time' => $request->booking_time,
                'guests_count' => $request->guests_count,
                'note' => $request->note,
                'status' => 'pending',
                'deposit_amount' => 100000,
                'transaction_id' => 'TXN' . time() // Tạo mã giao dịch ngẫu nhiên
            ]);

            // LƯU VÀO DATABASE
            DB::commit();

            // Thành công -> Chuyển hướng sang trang thanh toán cọc
            return redirect()->route('booking.payment', $booking->id);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi hệ thống xảy ra, vui lòng thử lại sau!');
        }
    }
    // 1. Hiển thị trang giả lập thanh toán
    public function showPayment($id)
    {
        $booking = RestaurantBooking::findOrFail($id);
        return view('restaurants.payment', compact('booking'));
    }

    // 2. Xử lý khi bấm nút "Đã thanh toán"
    public function processPayment($id)
    {
        $booking = RestaurantBooking::findOrFail($id);

        // Đổi trạng thái từ pending -> confirmed
        if ($booking->status === 'pending') {
            $booking->update(['status' => 'confirmed']);
        }

        // Tích hợp Gửi Email (Module 4.2) sẽ viết ở đây sau

        return redirect()->route('booking.success', $booking->id);
    }

    // 3. Hiển thị trang Hóa đơn thành công
    public function showSuccess($id)
    {
        $booking = RestaurantBooking::with('restaurant')->findOrFail($id);
        return view('restaurants.success', compact('booking'));
    }
}
