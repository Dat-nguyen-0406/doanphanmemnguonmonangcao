<?php

namespace App\Http\Controllers\User\Payment;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * Tạo giao dịch VNPay cho vé xem phim (Booking).
     * POST /payment/create  (form gửi lên: booking_id)
     */
    public function createPayment(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
        ]);

        $booking = Booking::with('showtime.movie')
            ->where('user_id', Auth::id())
            ->findOrFail($request->booking_id);

        if ($booking->status === 'confirmed') {
            return redirect()->route('booking.ticket', $booking->id)
                ->with('success', 'Vé này đã được thanh toán trước đó.');
        }

        $amount = $booking->total_price;
        if (!$amount || $amount <= 0) {
            return redirect()->back()->with('error', 'Số tiền thanh toán không hợp lệ!');
        }

        // Mỗi lần tạo giao dịch dùng 1 mã TxnRef mới (unique) để tránh trùng lặp
        $txnRef = 'BOOKING_' . $booking->id . '_' . time();

        DB::beginTransaction();
        try {
            Payment::create([
                'booking_id'        => $booking->id,
                'vnp_txn_ref'       => $txnRef,
                'amount'            => $amount,
                'order_info'        => 'Thanh toan ve xem phim: ' . ($booking->showtime->movie->title ?? $booking->id),
                'vnp_response_code' => null,
                'status'            => 'pending',
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi tạo Payment vé xem phim: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra, vui lòng thử lại sau.');
        }

        $vnp_Url        = env('VNPAY_URL');
        $vnp_TmnCode    = env('VNPAY_TMN_CODE');
        $vnp_HashSecret = env('VNPAY_HASH_SECRET');
        $vnp_Returnurl  = route('payment.return');

        $inputData = [
            "vnp_Version"    => "2.1.0",
            "vnp_TmnCode"    => $vnp_TmnCode,
            "vnp_Amount"     => intval($amount * 100),
            "vnp_Command"    => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode"   => "VND",
            "vnp_IpAddr"     => $request->ip(),
            "vnp_Locale"     => 'vn',
            "vnp_OrderInfo"  => "Thanh toan ve xem phim booking " . $booking->id,
            "vnp_OrderType"  => "billpayment",
            "vnp_ReturnUrl"  => $vnp_Returnurl,
            "vnp_TxnRef"     => $txnRef,
        ];

        ksort($inputData);
        $hashdata = "";
        $query = "";
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
        $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;

        return redirect()->away($vnp_Url);
    }

    /**
     * Xử lý callback trả về từ VNPay sau khi thanh toán vé xem phim.
     * GET /payment/return  và  GET /cinema/vnpay-return
     */
    public function paymentReturn(Request $request)
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
            Log::warning('VNPay Cinema: chữ ký không hợp lệ. TxnRef: ' . ($request->vnp_TxnRef ?? 'N/A'));
            return redirect()->route('my.bookings')->with('error', 'Chữ ký giao dịch không hợp lệ!');
        }

        $payment = Payment::where('vnp_txn_ref', $request->vnp_TxnRef)->first();

        if (!$payment) {
            return redirect()->route('my.bookings')->with('error', 'Không tìm thấy giao dịch thanh toán!');
        }

        $booking = Booking::find($payment->booking_id);

        if ($request->vnp_ResponseCode == '00') {
            if ($payment->status !== 'success') {
                $payment->update([
                    'bank_code'          => $request->vnp_BankCode,
                    'card_type'          => $request->vnp_CardType,
                    'vnp_response_code'  => $request->vnp_ResponseCode,
                    'vnp_transaction_no' => $request->vnp_TransactionNo,
                    'pay_date'           => now(),
                    'status'             => 'success',
                    'vnp_data'           => $inputData,
                ]);

                if ($booking && $booking->status !== 'confirmed') {
                    $booking->update(['status' => 'confirmed']);
                }

                Log::info("SMS_AEON: Booking #{$payment->booking_id} đã thanh toán vé xem phim thành công.");
            }

            return redirect()->route('booking.ticket', $payment->booking_id)
                ->with('success', 'Thanh toán vé xem phim thành công!');
        }

        $payment->update([
            'vnp_response_code' => $request->vnp_ResponseCode,
            'status'            => 'failed',
            'vnp_data'          => $inputData,
        ]);

        if ($booking && $booking->status === 'pending') {
            $booking->update(['status' => 'cancelled']);
        }

        return redirect()->route('my.bookings')->with('error', 'Giao dịch thanh toán thất bại hoặc đã bị hủy.');
    }
}  