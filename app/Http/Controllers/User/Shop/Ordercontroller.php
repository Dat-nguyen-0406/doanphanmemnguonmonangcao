<?php

namespace App\Http\Controllers\User\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    // Danh sách đơn hàng của user đang đăng nhập.
    // Trang hồ sơ (profile.index) đã hiển thị sẵn danh sách đơn hàng,
    // nên route này chỉ cần điều hướng về đó để tránh trùng lặp view.
    public function index()
    {
        return redirect()->route('profile.index');
    }

    // Chi tiết 1 đơn hàng.
    public function show($id)
    {
        $order = Order::with(['orderDetails.product'])->findOrFail($id);

        // FIX: chặn IDOR — không cho user A xem đơn hàng của user B
        // chỉ bằng cách đổi {id} trên URL.
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền xem đơn hàng này.');
        }

        return view('user.profile.order_detail', compact('order'));
    }
}