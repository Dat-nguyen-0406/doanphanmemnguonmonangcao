<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
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

        // Tạo đối tượng thời gian từ input khách chọn, ép về múi giờ VN
        $bookingDateTime = \Carbon\Carbon::parse($request->booking_date . ' ' . $request->booking_time, 'Asia/Ho_Chi_Minh');

        // Lấy thời gian hiện tại của VN và cộng thêm 1 tiếng
        $minAllowedTime = \Carbon\Carbon::now('Asia/Ho_Chi_Minh')->addHour();

        if ($bookingDateTime->lt($minAllowedTime)) {
            return back()->with('error', 'Thời gian đặt bàn phải cách hiện tại ít nhất 1 tiếng. Vui lòng chọn giờ sau ' . $minAllowedTime->format('H:i d/m/Y'));
        }

        // 3. Kiểm tra xem người dùng đã đăng nhập chưa
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để thực hiện đặt bàn.');
        }
        // 4. Lấy ID của người dùng đang đăng nhập thực tế
        $userId = Auth::id();

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
                'transaction_id' => 'TXN-' . Str::uuid() // Tạo mã giao dịch duy nhất
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
    // public function processPayment($id)
    // {
    //     $bookingId = DB::transaction(function () use ($id) {
    //         $booking = RestaurantBooking::findOrFail($id);

    //         if (!Auth::check() || (int) $booking->user_id !== (int) Auth::id()) {
    //             abort(403, 'Bạn không có quyền xác nhận đặt bàn này.');
    //         }

    //         // Đổi trạng thái từ pending -> confirmed
    //         if ($booking->status === 'pending') {
    //             $booking->update(['status' => 'confirmed']);
    //         }

    //         return $booking->id;
    //     });

    //     // Tích hợp Gửi Email (Module 4.2) sẽ viết ở đây sau

    //     return redirect()->route('booking.success', $bookingId);
    // }

    // Hàm 1: Tạo URL và chuyển hướng sang VNPAY
    public function processVnPay(Request $request, $id)
    {
        $booking = RestaurantBooking::findOrFail($id);

        // Lấy config từ .env
        $vnp_Url = env('VNPAY_URL');
        $vnp_Returnurl = env('VNPAY_RETURN_URL');
        $vnp_TmnCode = env('VNPAY_TMN_CODE');
        $vnp_HashSecret = env('VNPAY_HASH_SECRET');

        $vnp_TxnRef = $booking->transaction_id; // Mã tham chiếu (mã đơn)
        $vnp_OrderInfo = "Thanh toan coc dat ban don: " . $vnp_TxnRef;
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = $booking->deposit_amount * 100; // VNPAY yêu cầu số tiền nhân 100
        $vnp_Locale = 'vn';
        $vnp_BankCode = 'NCB'; // Dùng mặc định NCB để test Sandbox
        $vnp_IpAddr = $request->ip();

        // Cấu trúc mảng dữ liệu gửi đi
        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
        );

        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }

        // Sinh chữ ký bảo mật (Checksum/Hash)
        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash =   hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        // Redirect khách sang trang thanh toán của VNPAY
        return redirect($vnp_Url);
    }

    // Hàm 2: Hứng kết quả VNPAY trả về
    public function vnpayReturn(Request $request)
    {
        $vnp_HashSecret = env('VNPAY_HASH_SECRET');
        $inputData = array();
        
        foreach ($request->all() as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }

        $vnp_SecureHash = $inputData['vnp_SecureHash'];
        unset($inputData['vnp_SecureHash']);
        unset($inputData['vnp_SecureHashType']);
        ksort($inputData);
        
        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        // Kiểm tra chữ ký xem có bị giả mạo không
        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
        if ($secureHash == $vnp_SecureHash) {
            // Kiểm tra mã phản hồi (00 là thành công)
            if ($request->vnp_ResponseCode == '00') {
                $booking = RestaurantBooking::where('transaction_id', $request->vnp_TxnRef)->first();
                
                if ($booking && $booking->status === 'pending') {
                    $booking->update(['status' => 'confirmed']);
                }
                
                // Trả về trang Hóa đơn xanh lá thành công
                return redirect()->route('booking.success', $booking->id);
            } else {
                return redirect()->route('restaurants.index')->with('error', 'Giao dịch thanh toán đã bị hủy.');
            }
        } else {
            return redirect()->route('restaurants.index')->with('error', 'Chữ ký bảo mật không hợp lệ.');
        }
    }

    // 3. Hiển thị trang Hóa đơn thành công
    public function showSuccess($id)
    {
        $booking = RestaurantBooking::with('restaurant')->findOrFail($id);
        return view('restaurants.success', compact('booking'));
    }
}
