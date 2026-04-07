<?php

namespace App\Http\Controllers;


use App\Models\City;
use App\Models\Branch;
use Illuminate\Http\Request;



class AeonController extends Controller
{

public function storeBranch(Request $request) {
    // 1. Kiểm tra dữ liệu đầu vào (Validation)
    $request->validate([
        'name' => 'required|max:255',
        'city_id' => 'required|exists:cities,id',
        'address' => 'required',
    ]);

    // 2. Lưu vào Database
    Branch::create([
        'name' => $request->name,
        'city_id' => $request->city_id,
        'address' => $request->address,
        'map_link' => $request->map_link,
    ]);

    // 3. Quay lại trang Dashboard với thông báo thành công
    return redirect()->route('admin.dashboard')->with('success', 'Thêm chi nhánh mới thành công!');
}
    public function createBranch() {
    $cities = \App\Models\City::all(); // Lấy danh sách thành phố để chọn trong dropdown
    return view('admin.branches.create', compact('cities'));
}

    public function editBranch($id)
    {
        $branch = Branch::findOrFail($id);
        $cities = City::all(); // Để chọn lại thành phố nếu cần
        return view('admin.branches.edit', compact('branch', 'cities'));
    }

        public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'city_id' => 'required|exists:cities,id',
            'address' => 'required', // Đảm bảo không để trống địa chỉ khi sửa
        ]);

        $branch = Branch::findOrFail($id);
        
        // Cập nhật tất cả các trường cần thiết
        $branch->update([
            'name' => $request->name,
            'city_id' => $request->city_id,
            'address' => $request->address,
            'map_link' => $request->map_link, // Nếu không nhập thì nó sẽ giữ nguyên hoặc null tùy DB
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Cập nhật chi nhánh thành công!');
}

    public function destroyBranch($id)
  {
    $branch = Branch::findOrFail($id);
    $branch->delete();
    return back()->with('success', 'Đã xóa chi nhánh thành công!');
  }
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


// Hiển thị danh sách
    public function listCities() {
        $cities = City::withCount('branches')->get(); // Lấy kèm số lượng chi nhánh mỗi thành phố
        return view('admin.cities.index', compact('cities'));
    }

    // Lưu thành phố mới
    public function storeCity(Request $request) {
        $request->validate([
            'name' => 'required|unique:cities,name|max:100',
        ], [
            'name.unique' => 'Thành phố này đã tồn tại trong hệ thống!'
        ]);

        City::create(['name' => $request->name]);

        return redirect()->route('admin.cities.index')->with('success', 'Thêm thành phố thành công!');
    }

    // Xóa thành phố
    public function destroyCity($id) {
        $city = City::findOrFail($id);
        $city->delete();
        return back()->with('success', 'Đã xóa thành phố!');
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