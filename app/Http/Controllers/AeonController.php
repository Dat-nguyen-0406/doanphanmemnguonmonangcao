<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Branch;
use Illuminate\Http\Request;

class AeonController extends Controller
{
    /**
     * Hiển thị trang chủ (home.blade.php)
     * Lấy danh sách thành phố và các chi nhánh để người dùng chọn
     */
    public function index()
    {
        // Eager loading 'branches' để tối ưu truy vấn (tránh lỗi N+1)
        $cities = City::with('branches')->get();
        
        return view('home', compact('cities'));
    }

    /**
     * Hiển thị trang chi tiết một chi nhánh (aeon_detail.blade.php)
     * @param int $id ID của chi nhánh AEON
     */
    public function show($id)
    {
        // Tìm chi nhánh theo ID, nếu không thấy sẽ trả về lỗi 404
        $branch = Branch::with('city')->findOrFail($id);
        
        return view('aeon_detail', compact('branch'));
    }

    /**
     * Chức năng Mua sắm trực tuyến (Online Shopping)
     */
    public function shop()
    {
        // Sau này bạn sẽ lấy dữ liệu từ bảng Products ở đây
        return view('shop_index');
    }
}