<?php

// Test hash VNPay
$vnp_TmnCode = "2X606HJ6";
$vnp_HashSecret = "RCA5D1VHPLTY3X6CXS5GMXCE3MZYW9OW";

// Sample data - thay bằng data từ payment_debug
$inputData = array(
    "vnp_Amount" => "5000000",
    "vnp_Command" => "pay",
    "vnp_CreateDate" => "20260408120000", // 5 minutes ago from now
    "vnp_CurrCode" => "VND",
    "vnp_IpAddr" => "127.0.0.1",
    "vnp_Locale" => "vn",
    "vnp_OrderInfo" => "Thanh toan dat ve phim: Spider-Man: No Way Home",
    "vnp_OrderType" => "billpayment",
    "vnp_ReturnUrl" => "http://localhost:8000/payment/return",
    "vnp_TmnCode" => $vnp_TmnCode,
    "vnp_TxnRef" => "1775639340_5",
    "vnp_Version" => "2.1.0",
);

ksort($inputData);

// Build query string
$queryParts = [];
foreach ($inputData as $key => $value) {
    $queryParts[] = rawurlencode($key) . "=" . rawurlencode($value);
}

$query = implode('&', $queryParts);

// Calculate hash
$hash = hash_hmac('sha512', $query, $vnp_HashSecret);

echo "=== VNPay Hash Test ===\n\n";
echo "TmnCode: " . $vnp_TmnCode . "\n";
echo "HashSecret: " . $vnp_HashSecret . "\n\n";

echo "Input Data (sorted):\n";
echo json_encode($inputData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

echo "Query String:\n";
echo $query . "\n\n";

echo "Secure Hash (SHA512):\n";
echo $hash . "\n\n";

echo "Full URL:\n";
echo "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html?" . $query . "&vnp_SecureHash=" . $hash . "\n";
