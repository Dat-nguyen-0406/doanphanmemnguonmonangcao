<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\Branch;
use App\Models\RestaurantBooking;
use App\Models\RestaurantBookingItem;
use App\Models\RestaurantMenuItem;
use App\Models\RestaurantTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RestaurantController extends Controller
{
    // DANH SÁCH NHÀ HÀNG
    public function index(Request $request)
    {
        $branches = Branch::all();
        $query = Restaurant::with('branch')->where('is_active', true)->withCount(['tables', 'bookings']);
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }
        if ($request->filled('cuisine_type')) {
            $query->where('cuisine_type', $request->cuisine_type);
        }
        $restaurants = $query->get();
        $cuisineTypes = Restaurant::where('is_active', true)->distinct()->pluck('cuisine_type')->filter()->values();
        return view('restaurants.index', compact('restaurants', 'branches', 'cuisineTypes'));
    }

    // FORM ĐẶT BÀN
    public function showBookForm(Request $request, $id)
    {
        $restaurant = Restaurant::with(['branch', 'tables' => function ($q) {
            $q->where('is_active', true)->orderBy('floor')->orderBy('table_number');
        }])->findOrFail($id);

        $date = $request->get('date', date('Y-m-d'));
        $time = $request->get('time', '12:00');

        $tables = $restaurant->tables->map(function ($table) use ($date, $time) {
            $table->is_booked = $table->isBookedAt($date, $time);
            return $table;
        });

        $floors = $tables->pluck('floor')->unique()->sort()->values();

        $menuItems = $restaurant->menuItems()
            ->where('is_available', true)
            ->get()
            ->groupBy('category');

        return view('restaurants.book', compact('restaurant', 'tables', 'floors', 'menuItems', 'date', 'time'));
    }

    // AJAX CHECK AVAILABILITY
    public function checkAvailability(Request $request, $id)
    {
        $restaurant = Restaurant::findOrFail($id);
        $date = $request->get('date', date('Y-m-d'));
        $time = $request->get('time', '12:00');

        $tables = $restaurant->tables()
            ->where('is_active', true)
            ->get()
            ->map(function ($table) use ($date, $time) {
                $table->is_booked = $table->isBookedAt($date, $time);
                return $table;
            });

        return response()->json($tables);
    }

    // XỬ LÝ ĐẶT BÀN
    public function submitBooking(Request $request, $id)
    {
        $request->validate([
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'required',
            'guests_count' => 'required|integer|min:1',
            'table_id'     => 'required|integer|exists:restaurant_tables,id',
        ]);

        $bookingDateTime = \Carbon\Carbon::parse(
            $request->booking_date . ' ' . $request->booking_time, 'Asia/Ho_Chi_Minh'
        );
        $minAllowedTime = \Carbon\Carbon::now('Asia/Ho_Chi_Minh')->addHour();

        if ($bookingDateTime->lt($minAllowedTime)) {
            return back()->with('error', 'Thời gian đặt bàn phải cách hiện tại ít nhất 1 tiếng. Vui lòng chọn giờ sau ' . $minAllowedTime->format('H:i d/m/Y'));
        }

        if (!Auth::check()) {
            session(['url.intended' => url()->current()]);
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để thực hiện đặt bàn.');
        }

        try {
            DB::beginTransaction();

            $restaurant = Restaurant::where('id', $id)->lockForUpdate()->firstOrFail();

            $table = RestaurantTable::where('id', $request->table_id)
                ->where('restaurant_id', $id)
                ->where('is_active', true)
                ->lockForUpdate()
                ->firstOrFail();

            if ($request->guests_count > $table->capacity) {
                DB::rollBack();
                return back()->with('error', "Bàn này chỉ phục vụ {$table->capacity} người. Vui lòng chọn bàn có sức chứa lớn hơn!");
            }

            $isBooked = RestaurantBooking::where('table_id', $table->id)
                ->where('booking_date', $request->booking_date)
                ->where('booking_time', $request->booking_time)
                ->whereIn('status', ['pending', 'confirmed'])
                ->exists();

            if ($isBooked) {
                DB::rollBack();
                return back()->with('error', 'Bàn này đã được đặt trong khung giờ bạn chọn. Vui lòng chọn bàn hoặc giờ khác!');
            }

            $preOrderAmount = 0;
            $preOrderData = [];

            if ($request->has('pre_order')) {
                foreach ($request->pre_order as $item) {
                    $qty = (int)($item['qty'] ?? 0);
                    if ($qty <= 0) continue;
                    $menuItem = RestaurantMenuItem::where('id', $item['id'])
                        ->where('restaurant_id', $id)
                        ->where('is_available', true)
                        ->first();
                    if ($menuItem) {
                        $preOrderAmount += $menuItem->price * $qty;
                        $preOrderData[] = [
                            'menu_item_id' => $menuItem->id,
                            'quantity'     => $qty,
                            'unit_price'   => $menuItem->price,
                        ];
                    }
                }
            }

            $booking = RestaurantBooking::create([
                'user_id'          => Auth::id(),
                'restaurant_id'    => $restaurant->id,
                'table_id'         => $table->id,
                'booking_date'     => $request->booking_date,
                'booking_time'     => $request->booking_time,
                'guests_count'     => $request->guests_count,
                'note'             => $request->note,
                'status'           => 'pending',
                'deposit_amount'   => 100000,
                'pre_order_amount' => $preOrderAmount,
                'transaction_id'   => 'TXN-' . Str::uuid(),
            ]);

            foreach ($preOrderData as $item) {
                RestaurantBookingItem::create(array_merge($item, ['booking_id' => $booking->id]));
            }

            DB::commit();
            return redirect()->route('booking.payment', $booking->id);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi hệ thống xảy ra, vui lòng thử lại sau!');
        }
    }

    // TRANG THANH TOÁN
    public function showPayment($id)
    {
        $booking = RestaurantBooking::with(['restaurant', 'table', 'items.menuItem'])->findOrFail($id);
        return view('restaurants.payment', compact('booking'));
    }

    // VNPAY PROCESS
    public function processVnPay(Request $request, $id)
    {
        $booking = RestaurantBooking::findOrFail($id);

        $vnp_Url        = env('VNPAY_URL');
        $vnp_Returnurl  = route('booking.vnpay.return');
        $vnp_TmnCode    = env('VNPAY_TMN_CODE');
        $vnp_HashSecret = env('VNPAY_HASH_SECRET');

        $totalAmount = ($booking->deposit_amount + ($booking->pre_order_amount ?? 0)) * 100;

        $inputData = [
            "vnp_Version"   => "2.1.0",
            "vnp_TmnCode"   => $vnp_TmnCode,
            "vnp_Amount"    => $totalAmount,
            "vnp_Command"   => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode"  => "VND",
            "vnp_IpAddr"    => $request->ip(),
            "vnp_Locale"    => 'vn',
            "vnp_OrderInfo" => "Thanh toan coc dat ban: " . $booking->transaction_id,
            "vnp_OrderType" => 'billpayment',
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef"   => $booking->transaction_id,
            "vnp_BankCode"  => 'NCB',
        ];

        ksort($inputData);
        $hashdata = '';
        $query = '';
        $i = 0;
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
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        return redirect($vnp_Url);
    }

    // VNPAY RETURN
    public function vnpayReturn(Request $request)
    {
        $vnp_HashSecret = env('VNPAY_HASH_SECRET');
        $inputData = [];

        foreach ($request->all() as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }

        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? '';
        unset($inputData['vnp_SecureHash'], $inputData['vnp_SecureHashType']);
        ksort($inputData);

        $i = 0;
        $hashData = '';
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        if ($secureHash == $vnp_SecureHash) {
            if ($request->vnp_ResponseCode == '00') {
                $booking = RestaurantBooking::where('transaction_id', $request->vnp_TxnRef)->first();
                if ($booking && $booking->status === 'pending') {
                    $booking->update(['status' => 'confirmed']);
                }
                return redirect()->route('booking.success', $booking->id);
            } else {
                return redirect()->route('restaurants.index')->with('error', 'Giao dịch thanh toán đã bị hủy.');
            }
        } else {
            return redirect()->route('restaurants.index')->with('error', 'Chữ ký bảo mật không hợp lệ.');
        }
    }

    // TRANG THÀNH CÔNG
    public function showSuccess($id)
    {
        $booking = RestaurantBooking::with(['restaurant', 'table', 'items.menuItem'])->findOrFail($id);
        return view('restaurants.success', compact('booking'));
    }
}