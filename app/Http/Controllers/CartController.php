<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Notifications\OrderProcessed;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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

    public function vnpay_payment(Request $request) 
    {
        $cart = session()->get('cart');
        if (!$cart) return redirect()->back()->with('error', 'Giỏ hàng đang trống!');

        \DB::beginTransaction();
        try {
            $order = Order::create([
                'user_id' => Auth::id(),
                'total_amount' => $request->total_amount,
                'status' => 'pending',
            ]);

            foreach ($cart as $id => $details) {
                \App\Models\OrderDetail::create([
                    'order_id'   => $order->id,
                    'product_id' => $id,
                    'quantity'   => $details['quantity'],
                    'price'      => $details['price'],
                ]);
            }
            \DB::commit();

            // Cấu hình (Lấy từ .env)
            // Sửa lại đoạn cấu hình trong hàm vnpay_payment
            $vnp_TmnCode = "2X606HJ6"; 
            $vnp_HashSecret = "RCA5D1VHPLTY3X6CXS5GMXCE3MZYW9OW"; // Viết thẳng vào đây
            $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
            $vnp_Returnurl = route('vnpay.return');

            $inputData = array(
                "vnp_Version" => "2.1.0",
                "vnp_TmnCode" => $vnp_TmnCode,
                "vnp_Amount" => intval($request->total_amount * 100), // Đảm bảo là số nguyên
                "vnp_Command" => "pay",
                "vnp_CreateDate" => date('YmdHis'),
                "vnp_CurrCode" => "VND",
                "vnp_IpAddr" => $request->ip(),
                "vnp_Locale" => 'vn',
                
                "vnp_OrderInfo" => "Thanh toan don hang " . $order->id,
                "vnp_OrderType" => "billpayment",
                "vnp_ReturnUrl" => $vnp_Returnurl,
                "vnp_TxnRef" => (string)$order->id // Chuyển về string
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

            // Xử lý dấu & thừa ở cuối $query trước khi nối SecureHash
            $vnp_Url = $vnp_Url . "?" . $query;
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;

            return redirect()->away($vnp_Url);

        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }
    public function vnpay_return(Request $request) 
    {
        // 1. Tìm đơn hàng dựa trên vnp_TxnRef trả về
        $orderId = $request->vnp_TxnRef;
        $order = Order::findOrFail($orderId);

        // 2. Kiểm tra mã phản hồi (00 là thành công)
        if ($request->vnp_ResponseCode == '00') {
            // Cập nhật trạng thái đơn hàng
            $order->update(['status' => 'paid']);

            // Gửi thông báo Email qua Notification
            Auth::user()->notify(new OrderProcessed($order));

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
}