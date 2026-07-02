<?php

namespace App\Http\Controllers\User\Shop;

use App\Http\Controllers\Controller;

use App\Models\Product;
use App\Models\Order;
use App\Models\Category;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PartnerShopController extends Controller
{
    /**
     * Hiển thị danh sách sản phẩm của Shop
     */
    public function index(Request $request)
{
    // Eager loading để tối ưu query, tránh N+1
    $query = Product::with(['category', 'branch', 'user']);
    
    // 1. Phân quyền: Role 1 (Admin) xem hết, còn lại chỉ xem hàng của mình
    if (Auth::user()->role != 1) {
        $query->where('user_id', Auth::id());
    }

    // 2. Lọc theo Danh mục (nếu có request category_id)
    $query->when($request->category_id, function($q) use ($request) {
        return $q->where('category_id', $request->category_id);
    });

    // 3. Lọc theo Chi nhánh (nếu có request branch_id)
    $query->when($request->branch_id, function($q) use ($request) {
        return $q->where('branch_id', $request->branch_id);
    });

    // 4. Tìm kiếm theo tên sản phẩm (Search bar đã thêm ở giao diện)
    $query->when($request->search, function($q) use ($request) {
        return $q->where('name', 'LIKE', '%' . $request->search . '%');
    });

    // Sắp xếp mới nhất lên đầu và lấy kết quả
    $products = $query->latest()->get();

    // Lấy dữ liệu bổ trợ cho các dropdown filter trong view
    $categories = Category::all();
    $branches = Branch::all();

    return view('admin.shop.index', compact('products', 'categories', 'branches'));
}

    /**
     * Form thêm sản phẩm mới
     */
    public function create()
    {
        $categories = Category::all();
        $branches = Branch::all();
        return view('admin.shop.create', compact('categories', 'branches'));
    }

    /**
     * Lưu sản phẩm vào Database
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'branch_id' => 'required|exists:branches,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $data = $request->all();
        $data['user_id'] = Auth::id();
        $data['slug'] = Str::slug($request->name) . '-' . time(); // Tránh trùng slug
        $data['is_active'] = $request->has('is_active') ? true : true;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($data);

    return redirect()->route('admin.shop.index')->with('success', 'Đã thêm sản phẩm mới thành công!');
    }

    /**
     * Form chỉnh sửa sản phẩm
     */
    public function edit($id)
{
    // Logic: Admin tổng thì tìm mọi sản phẩm, Partner thì chỉ tìm sản phẩm của mình
    $query = (Auth::user()->role == 1) ? Product::query() : Product::where('user_id', Auth::id());
    $product = $query->findOrFail($id);
    
    // Bảo vệ thêm: Admin tổng lỡ có vào được trang edit cũng không cho thấy form sửa
    if (Auth::user()->role == 1) {
        return redirect()->route('admin.shop.index')->with('error', 'Chế độ xem: Admin không thể sửa sản phẩm.');
    }

    $categories = Category::all();
    $branches = Branch::all();
    return view('admin.shop.edit', compact('product', 'categories', 'branches'));
}

    /**
     * Cập nhật thông tin sản phẩm
     */
    public function update(Request $request, $id)
    {
        // Chỉ cho phép sửa sản phẩm của chính mình (Auth::id())
        $product = Product::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'name' => 'required|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'branch_id' => 'required|exists:branches,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:5120', // Tăng lên 5MB cho đồng bộ
        ]);

        $data = $request->all();
        
        // Cập nhật Slug dựa trên tên mới và ID sản phẩm
        $data['slug'] = Str::slug($request->name) . '-' . $product->id;
        
        // Xử lý checkbox is_active
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            // Xóa ảnh cũ vật lý để tránh rác server
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('admin.shop.index')->with('success', 'Cập nhật sản phẩm thành công!');
    }

    // Thêm vào trong PartnerShopController.php

    public function categoryIndex() {
        $categories = Category::all();
        return view('admin.shop.category', compact('categories'));
    }

    public function categoryStore(Request $request) {
        $request->validate([
            'name' => 'required|unique:categories,name|max:255',
        ]);

        Category::create([
            'name' => $request->name,
            'slug' => \Str::slug($request->name)
        ]);

        return back()->with('success', 'Đã thêm danh mục mới!');
    }

    public function categoryDestroy($id) {
        Category::findOrFail($id)->delete();
        return back()->with('success', 'Đã xóa danh mục!');
    }

    /**
     * Xóa sản phẩm
     */
    public function destroy($id)
    {
        $product = Product::where('user_id', Auth::id())->findOrFail($id);

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return back()->with('success', 'Đã xóa sản phẩm khỏi kho hàng!');
    }

   public function orders(Request $request)
{
    $user = Auth::user();

    // 1. Khởi tạo Query lấy Đơn hàng kèm thông tin khách đặt đơn
    $query = Order::with(['user']);

    // 2. PHÂN QUYỀN: Nếu KHÔNG PHẢI Admin tối cao (role != 1), tức là Shop Partner (role == 4)
    // Chỉ lấy các đơn hàng chứa sản phẩm thuộc quyền sở hữu của chính Shop đó
    if ($user->role != 1) {
        $query->whereHas('orderDetails.product', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->with(['orderDetails' => function ($q) use ($user) {
            // Bảo mật: Chỉ load chân rết sản phẩm thuộc quyền sở hữu của Shop này
            $q->whereHas('product', function ($pQ) use ($user) {
                $pQ->where('user_id', $user->id);
            })->with('product');
        }]);
    } else {
        // Nếu là Admin: Load toàn bộ chi tiết sản phẩm không giới hạn
        $query->with('orderDetails.product');
    }

    // 3. BỘ LỌC ĐỒNG BỘ: Lọc theo Danh mục sản phẩm nằm trong đơn
    if ($request->filled('category_id')) {
        $query->whereHas('orderDetails.product', function ($q) use ($request) {
            $q->where('category_id', $request->category_id);
        });
    }

    // 4. BỘ LỌC ĐỒNG BỘ: Lọc theo Chi nhánh AEON phát sinh đơn
    if ($request->filled('branch_id')) {
        $query->whereHas('orderDetails.product', function ($q) use ($request) {
            $q->where('branch_id', $request->branch_id);
        });
    }

    // 5. BỘ LỌC ĐỒNG BỘ: Tìm kiếm theo Mã Đơn hàng hoặc Tên Khách hàng đặt
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('id', $search)
              ->orWhereHas('user', function ($uQ) use ($search) {
                  $uQ->where('name', 'LIKE', '%' . $search . '%');
              });
        });
    }

    // Lấy dữ liệu phân trang
    $orders = $query->latest()->paginate(15);

    // Lấy danh sách danh mục và chi nhánh để nạp vào Select Box ở Giao diện
    $categories = Category::all();
    $branches = Branch::all();

    return view('admin.shop.orders', compact('orders', 'categories', 'branches'));
}

/**
 * Xử lý cập nhật trạng thái đơn hàng cho từng Shop
 */
public function updateStatus(Request $request, $id)
{
    $request->validate([
        'status' => 'required|in:pending,paid,shipping,completed,cancelled'
    ]);

    $order = Order::findOrFail($id);
    $user = Auth::user();

    // Bảo mật: Nếu là Shop Partner (role = 4), check xem đơn này có thực sự chứa sản phẩm của mình không
    if ($user->role != 1) {
        $hasProduct = DB::table('order_details')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->where('order_details.order_id', $id)
            ->where('products.user_id', $user->id)
            ->exists();

        if (!$hasProduct) {
            return back()->with('error', 'Bạn không có quyền quản lý đơn hàng này!');
        }
    }

    $order->status = $request->status;
    $order->save();

    return back()->with('success', 'Cập nhật trạng thái đơn hàng #' . $id . ' thành công!');
}
}