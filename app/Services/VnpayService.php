<?php

namespace App\Services;

use App\Models\RestaurantBooking;
use Illuminate\Http\Request;
use InvalidArgumentException;

class VnpayService
{
    public function createPaymentUrl(RestaurantBooking $booking, Request $request): string
    {
        $paymentUrl = env('VNPAY_URL');
        $returnUrl = env('VNPAY_RETURN_URL');
        $tmnCode = env('VNPAY_TMN_CODE');
        $hashSecret = env('VNPAY_HASH_SECRET');

        if (empty($paymentUrl) || empty($returnUrl) || empty($tmnCode) || empty($hashSecret)) {
            throw new InvalidArgumentException('VNPAY configuration is incomplete.');
        }

        $inputData = [
            'vnp_Version' => '2.1.0',
            'vnp_TmnCode' => $tmnCode,
            'vnp_Amount' => (int) ($booking->deposit_amount * 100),
            'vnp_Command' => 'pay',
            'vnp_CreateDate' => now()->format('YmdHis'),
            'vnp_CurrCode' => 'VND',
            'vnp_IpAddr' => $request->ip(),
            'vnp_Locale' => 'vn',
            'vnp_OrderInfo' => 'Thanh toan coc dat ban don: ' . $booking->transaction_id,
            'vnp_OrderType' => 'billpayment',
            'vnp_ReturnUrl' => $returnUrl,
            'vnp_TxnRef' => $booking->transaction_id,
            'vnp_BankCode' => 'NCB',
        ];

        $hashData = $this->buildHashData($inputData);
        $secureHash = hash_hmac('sha512', $hashData, $hashSecret);

        return $paymentUrl . '?' . $hashData . '&vnp_SecureHash=' . $secureHash;
    }

    public function hasValidSignature(array $payload): bool
    {
        $hashSecret = env('VNPAY_HASH_SECRET');
        $receivedHash = $payload['vnp_SecureHash'] ?? null;

        if (empty($hashSecret) || empty($receivedHash)) {
            return false;
        }

        $inputData = $this->onlyVnpayData($payload);
        unset($inputData['vnp_SecureHash'], $inputData['vnp_SecureHashType']);

        $secureHash = hash_hmac('sha512', $this->buildHashData($inputData), $hashSecret);

        return hash_equals($secureHash, $receivedHash);
    }

    public function onlyVnpayData(array $payload): array
    {
        return array_filter(
            $payload,
            fn ($value, $key) => str_starts_with($key, 'vnp_'),
            ARRAY_FILTER_USE_BOTH
        );
    }

    private function buildHashData(array $data): string
    {
        ksort($data);

        $pairs = [];
        foreach ($data as $key => $value) {
            $pairs[] = urlencode($key) . '=' . urlencode($value);
        }

        return implode('&', $pairs);
    }
}
