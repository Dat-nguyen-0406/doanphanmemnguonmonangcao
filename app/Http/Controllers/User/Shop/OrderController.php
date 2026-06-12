<?php

namespace App\Http\Controllers\User\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // 1. Hiển thị danh sách lịch sử mua hàng trong Profile
        public function index()
        {
            $user = auth()->user(); 
            $orders = Order::with('orderDetails.product') // Load sẵn chi tiết và sản phẩm
                ->where('user_id', $user->id)
                ->latest()
                ->paginate(10);

            return view('user.profile.index', compact('orders', 'user'));
        }
    // 2. Hiển thị chi tiết của một đơn hàng
   // File: app/Http/Controllers/OrderController.php
public function show($id)
{
    $order = Order::with(['orderDetails.product']) // Đổi details thành orderDetails
        ->where('user_id', auth()->id())
        ->findOrFail($id);

    return view('user.profile.order_detail', compact('order'));
}
}