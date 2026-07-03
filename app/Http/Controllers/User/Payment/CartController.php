<?php

namespace App\Http\Controllers\User\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str; 
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Notifications\OrderProcessed;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function index()
    {
        return view('user.cart.index');
    }

    public function add($id)
    {
        $product = Product::with('branch')->findOrFail($id);
        $cart = session()->get('cart', []);

        if(isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $cart[$id] = [
                "name" => $product->name,
                "quantity" => 1,
                "price" => $product->price,
                "image" => $product->image,
                "branch" => $product->branch->name ?? 'AEON Mall'
            ];
        }

        session()->put('cart', $cart);
        return redirect()->back()->with('success', 'Đã thêm ' . $product->name . ' vào giỏ hàng!');
    }

    public function remove(Request $request)
    {
        if($request->id) {
            $cart = session()->get('cart');
            if(isset($cart[$request->id])) {
                unset($cart[$request->id]);
                session()->put('cart', $cart);
            }
        }
        return redirect()->back()->with('success', 'Đã xóa sản phẩm khỏi giỏ!');
    }

    // CartController_2.php

public function vnpay_payment(Request $request) 
{
    $cart = session()->get('cart');
    
    foreach ($cart as $id => $details) {
        $product = Product::find($id);
        if (!$product || $product->stock < $details['quantity']) {
            return redirect()->back()->with('error', 'Sản phẩm "' . $details['name'] . '" không đủ số lượng trong kho (Hiện còn: ' . ($product->stock ?? 0) . ')');
        }
    }

    // 1. Kiểm tra giỏ hàng tồn tại
    if (!$cart || count($cart) == 0) {
        return redirect()->back()->with('error', 'Giỏ hàng đang trống!');
    }

    // 2. TỰ TÍNH LẠI TỔNG TIỀN TỪ SESSION (Đảm bảo an toàn tuyệt đối)
    $realTotalAmount = 0;
    foreach ($cart as $item) {
        $realTotalAmount += $item['price'] * $item['quantity'];
    }

    // Kiểm tra nếu vì lý do nào đó (giá sản phẩm lỗi) mà tổng tiền vẫn = 0
    if ($realTotalAmount <= 0) {
        return redirect()->back()->with('error', 'Số tiền thanh toán không hợp lệ!');
    }

    \DB::beginTransaction();
    try {
        // Sử dụng $realTotalAmount thay vì $request->total_amount
        $order = Order::create([
            'user_id' => Auth::id(),
            'total_amount' => $realTotalAmount, 
            'status' => 'pending',
        ]);

        foreach ($cart as $id => $details) {
            OrderDetail::create([
                'order_id'   => $order->id,
                'product_id' => $id,
                'quantity'   => $details['quantity'],
                'price'      => $details['price'],
            ]);
        }
        \DB::commit();
        // --- CẤU HÌNH VNPAY ---[cite: 9]
        $vnp_TmnCode = env('VNPAY_TMN_CODE');
        $vnp_HashSecret = env('VNPAY_HASH_SECRET');
        $vnp_Url = env('VNPAY_URL');
        $vnp_Returnurl = env('VNPAY_RETURN_URL');

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            // FIX: dùng $realTotalAmount đã tính lại từ session (an toàn),
            // KHÔNG dùng $request->total_amount vì client có thể sửa giá trị này
            // trước khi gửi request để trả ít tiền hơn thực tế.
            "vnp_Amount" => intval($realTotalAmount * 100),
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $request->ip(),
            "vnp_Locale" => 'vn',
            "vnp_OrderInfo" => "Thanh toan don hang " . $order->id,
            "vnp_OrderType" => "billpayment",
            "vnp_ReturnUrl" => $vnp_Returnurl,
            // FIX: TxnRef phải unique mỗi giao dịch (không chỉ là order->id thuần)
            // để tránh nhầm lẫn/replay nếu order được thanh toán lại nhiều lần.
            "vnp_TxnRef" => 'SHOP_' . $order->id . '_' . time()
        );

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
        $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;

        return redirect()->away($vnp_Url);

    } catch (\Exception $e) {
        \DB::rollBack();
        Log::error('Lỗi VNPay Shop: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Có lỗi xảy ra, vui lòng thử lại sau.');
    }
}
    public function vnpay_return(Request $request) 
    {
        // BẢO MẬT: Verify chữ ký HMAC trước khi tin bất kỳ tham số nào từ URL.
        // Thiếu bước này, kẻ tấn công có thể tự build URL callback giả
        // (vnp_ResponseCode=00) để đánh dấu đơn hàng "đã thanh toán" mà
        // không hề trả tiền qua VNPay thật.
        $vnp_HashSecret = env('VNPAY_HASH_SECRET');
        $vnp_SecureHash = $request->vnp_SecureHash;
        $inputData = $request->except(['vnp_SecureHash', 'vnp_SecureHashType']);
        ksort($inputData);

        $hashData = '';
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }
        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        if (!hash_equals($secureHash, (string) $vnp_SecureHash)) {
            Log::warning('VNPay Shop: chữ ký không hợp lệ — nghi ngờ giả mạo callback. TxnRef: ' . $request->vnp_TxnRef);
            return redirect()->route('cart.index')->with('error', 'Chữ ký giao dịch không hợp lệ!');
        }

        // 1. Tìm đơn hàng dựa trên vnp_TxnRef trả về.
        // TxnRef có dạng SHOP_{order_id}_{timestamp} — tách lấy order_id ở giữa.
        $txnRef = $request->vnp_TxnRef;
        $parts  = explode('_', $txnRef);
        $orderId = $parts[1] ?? null;

        if (!$orderId || !is_numeric($orderId)) {
            return redirect()->route('cart.index')->with('error', 'Mã giao dịch không hợp lệ!');
        }

        // Nạp sẵn orderDetails và product để đảm bảo dữ liệu không bị null
        $order = Order::with('orderDetails.product')->findOrFail($orderId);

        // 2. Kiểm tra mã phản hồi (00 là thành công)
        if ($request->vnp_ResponseCode == '00') {
            if ($order->status !== 'paid') {
            
            // LOGIC TRỪ KHO BẮT ĐẦU TẠI ĐÂY
            foreach ($order->orderDetails as $detail) { 
                $product = $detail->product;
                if ($product) {
                    $product->decrement('stock', $detail->quantity);
                }
            }


            // Cập nhật trạng thái đơn hàng
            $order->update(['status' => 'paid']);
            Log::info("SMS_AEON: Đơn hàng #$orderId đã trừ kho và thanh toán thành công.");
        }

            // Gửi thông báo Email qua Notification
            // Auth::user()->notify(new OrderProcessed($order));

            // Giả lập ghi Log gửi SMS (Phục vụ báo cáo đồ án)
            Log::info("SMS_AEON: Đơn hàng #$orderId đã được thanh toán thành công bởi khách hàng.");

            // Xóa giỏ hàng sau khi thanh toán xong
            session()->forget('cart');

            return redirect()->route('shop.index')->with('success', 'Thanh toán thành công qua VNPay!');
        } else {
            // Nếu thất bại, có thể xóa đơn hàng tạm hoặc cập nhật status = failed
            $order->update(['status' => 'failed']);
            return redirect()->route('cart.index')->with('error', 'Giao dịch thất bại hoặc đã bị hủy.');
        }
    }
    public function cod_payment(Request $request)
    {
        $cart = session()->get('cart');

        if (!$cart || count($cart) == 0) {
            return redirect()->back()->with('error', 'Giỏ hàng đang trống!');
        }
        foreach ($cart as $id => $details) {
        $product = Product::find($id);
        if (!$product || $product->stock < $details['quantity']) {
            return redirect()->back()->with('error', 'Sản phẩm "' . $details['name'] . '" không đủ số lượng trong kho (Hiện còn: ' . ($product->stock ?? 0) . ')');
        }
    }

        $totalAmount = 0;
        foreach ($cart as $item) {
            $totalAmount += $item['price'] * $item['quantity'];
        }

        DB::beginTransaction();
        try {
            // 1. Tạo đơn hàng
            $order = Order::create([
                'user_id' => Auth::id(),
                'total_amount' => $totalAmount,
                'status' => 'pending', // Trạng thái Chờ xử lý
                //'note' => 'Thanh toán khi nhận hàng (COD)',
            ]);

            // 2. Lưu chi tiết và TRỪ KHO NGAY
            foreach ($cart as $id => $details) {
                OrderDetail::create([
                    'order_id'   => $order->id,
                    'product_id' => $id,
                    'quantity'   => $details['quantity'],
                    'price'      => $details['price'],
                ]);

                // Trừ số lượng sản phẩm trong kho
                $product = Product::find($id);
                if ($product) {
                    $product->decrement('stock', $details['quantity']);
                }
            }

            DB::commit();
            session()->forget('cart');
            
            return redirect()->route('shop.index')->with('success', 'Đơn hàng #' . $order->id . ' đã đặt thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Lỗi COD: " . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra, vui lòng thử lại sau.');
        }
    }

}