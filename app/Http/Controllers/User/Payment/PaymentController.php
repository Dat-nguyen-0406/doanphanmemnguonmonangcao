<?php

namespace App\Http\Controllers\User\Payment;

use App\Http\Controllers\Controller;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Order; // Model của Shop
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PaymentController extends Controller
{
    private $vnp_TmnCode;
    private $vnp_HashSecret;
    private $vnp_Url;
    private $vnp_Returnurl;

    public function __construct()
    {
        $this->vnp_TmnCode = config('vnpay.vnp_TmnCode');
        $this->vnp_HashSecret = config('vnpay.vnp_HashSecret');
        $this->vnp_Url = config('vnpay.vnp_Url');
        
        // QUAN TRỌNG: Lấy đúng URL return của Cinema từ config
        $this->vnp_Returnurl = config('vnpay.vnp_ReturnUrl_Cinema');
    }

    /**
     * Tạo link thanh toán VNPay cho ĐẶT VÉ (CINEMA)
     */
    public function createPayment(Request $request)
    {
        try {
            $request->validate([
                'booking_id' => 'required|exists:bookings,id',
            ]);

            $booking = Booking::with('showtime.movie')->findOrFail($request->booking_id);

            // Kiểm tra quyền và trạng thái
            if ($booking->user_id !== auth()->id() || $booking->status !== 'pending') {
                return back()->with('error', 'Giao dịch không hợp lệ.');
            }

            // Tạo/Cập nhật bản ghi thanh toán
            $payment = Payment::updateOrCreate(
                ['booking_id' => $booking->id],
                [
                    // Dùng tiền tố TICKET_ để nhận diện luồng Cinema
                    'vnp_txn_ref' => 'TICKET_' . time() . '_' . $booking->id, 
                    'amount' => $booking->total_price,
                    'order_info' => 'Thanh toan ve phim: ' . $booking->showtime->movie->title,
                    'status' => 'pending',
                ]
            );

            $vnp_Url = $this->buildVnpayUrl($payment, $request->ip());

            return redirect()->away($vnp_Url);

        } catch (\Exception $e) {
            Log::error('Cinema Payment Error: ' . $e->getMessage());
            return back()->with('error', 'Lỗi kết nối thanh toán.');
        }
    }

    /**
     * Callback VNPay dành riêng cho Cinema
     * URL: /booking/vnpay-return
     */
    public function paymentReturn(Request $request)
    {
        $vnp_SecureHash = $request->vnp_SecureHash;
        $inputData = $request->except(['vnp_SecureHash', 'vnp_SecureHashType']);
        ksort($inputData);
        
        if ($this->verifySecureHash($inputData, $vnp_SecureHash)) {
            $vnp_TxnRef = $request->vnp_TxnRef;

            // Kiểm tra tính hợp lệ của tiền tố
            if (str_contains($vnp_TxnRef, 'TICKET_')) {
                $payment = Payment::where('vnp_txn_ref', $vnp_TxnRef)->first();
                
                if ($payment) {
                    if ($request->vnp_ResponseCode == '00') {
                        // 1. Cập nhật Payment thành công
                        $payment->update([
                            'status' => 'success',
                            'vnp_response_code' => $request->vnp_ResponseCode,
                            'vnp_transaction_no' => $request->vnp_TransactionNo
                        ]);
                        
                        // 2. Xác nhận Booking
                        $payment->booking->update(['status' => 'confirmed']);
                        
                        return redirect()->route('my.bookings')->with('success', 'Thanh toán vé thành công!');
                    } else {
                        $payment->update(['status' => 'failed']);
                        return redirect()->route('my.bookings')->with('error', 'Thanh toán không thành công.');
                    }
                }
            }
        }

        return redirect()->route('home')->with('error', 'Chữ ký không hợp lệ!');
    }

    /**
     * Logic tạo URL (Hỗ trợ Cinema)
     */
    private function buildVnpayUrl(Payment $payment, $ipAddress): string
    {
        $inputData = [
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $this->vnp_TmnCode,
            "vnp_Amount" => (int)($payment->amount * 100),
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $ipAddress,
            "vnp_Locale" => 'vn',
            "vnp_OrderInfo" => $payment->order_info,
            "vnp_OrderType" => 'billpayment',
            "vnp_ReturnUrl" => $this->vnp_Returnurl,
            "vnp_TxnRef" => $payment->vnp_txn_ref,
        ];

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

        $vnp_Url = $this->vnp_Url . "?" . rtrim($query, '&');
        if (isset($this->vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $this->vnp_HashSecret);
            $vnp_Url .= '&vnp_SecureHash=' . $vnpSecureHash;
        }

        return $vnp_Url;
    }

    private function verifySecureHash(array $inputData, string $vnp_SecureHash): bool
    {
        ksort($inputData);
        $hashdata = "";
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }
        $secureHash = hash_hmac('sha512', $hashdata, $this->vnp_HashSecret);
        return hash_equals($secureHash, $vnp_SecureHash);
    }
}