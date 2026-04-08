<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\Branch;
use Illuminate\Http\Request;

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
}
