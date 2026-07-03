<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Restaurant;
use App\Models\RestaurantBooking;
use App\Models\RestaurantMenuItem;
use App\Models\RestaurantTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RestaurantAdminController extends Controller
{
    // =====================================================
    // QUẢN LÝ NHÀ HÀNG
    // =====================================================
    public function index()
    {
        $query = Restaurant::with('branch')
            ->withCount(['tables', 'bookings'])
            ->latest();

        // Role 3 (đối tác nhà hàng) chỉ thấy nhà hàng thuộc chi nhánh của mình
        if (auth()->user()->role == 3 && auth()->user()->branch_id) {
            $query->where('branch_id', auth()->user()->branch_id);
        }

        $restaurants = $query->paginate(15);
        return view('admin.restaurant.item.index', compact('restaurants'));
    }

    /**
     * Kiểm tra quyền sở hữu: Partner (role 3) chỉ được quản lý nhà hàng
     * thuộc chi nhánh của mình. Admin (role 1) được quản lý tất cả.
     * Chặn IDOR — không cho Partner A truy cập nhà hàng của Partner B
     * chỉ bằng cách đổi {id} trên URL.
     */
    private function authorizeRestaurant(Restaurant $restaurant)
    {
        $user = auth()->user();
        if ($user->role == 3) {
            if (!$user->branch_id) {
                abort(403, 'Tài khoản của bạn chưa được gán chi nhánh. Vui lòng liên hệ Admin để được cấu hình branch_id trước khi quản lý nhà hàng.');
            }
            if ($restaurant->branch_id != $user->branch_id) {
                abort(403, 'Bạn không có quyền quản lý nhà hàng này.');
            }
        }
    }

    public function create()
    {
        $branches = Branch::all();
        return view('admin.restaurant.item.creat', compact('branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'branch_id'    => 'required|exists:branches,id',
            'name'         => 'required|string|max:255',
            'cuisine_type' => 'nullable|string|max:100',
            'description'  => 'nullable|string',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $data = [
            'branch_id'    => $request->branch_id,
            'name'         => $request->name,
            'cuisine_type' => $request->cuisine_type,
            'description'  => $request->description,
            'is_active'    => $request->boolean('is_active', true),
        ];

        if ($request->hasFile('image')) {
            $file     = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path     = $file->storeAs('restaurants', $filename, 'public');
            $data['image_url'] = $path;
        }

        Restaurant::create($data);

        return redirect()->route('admin.restaurant.index')
            ->with('success', 'Nhà hàng "' . $request->name . '" đã được tạo thành công!');
    }

    public function edit($id)
    {
        $restaurant = Restaurant::findOrFail($id);
        $this->authorizeRestaurant($restaurant);
        $branches = Branch::all();
        return view('admin.restaurant.item.edit', compact('restaurant', 'branches'));
    }

    public function update(Request $request, $id)
    {
        $restaurant = Restaurant::findOrFail($id);
        $this->authorizeRestaurant($restaurant);

        $request->validate([
            'branch_id'    => 'required|exists:branches,id',
            'name'         => 'required|string|max:255',
            'cuisine_type' => 'nullable|string|max:100',
            'description'  => 'nullable|string',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        // Partner (role 3) không được phép đổi branch_id sang chi nhánh khác
        if (auth()->user()->role == 3 && $request->branch_id != $restaurant->branch_id) {
            abort(403, 'Bạn không có quyền chuyển nhà hàng sang chi nhánh khác.');
        }

        $data = [
            'branch_id'    => $request->branch_id,
            'name'         => $request->name,
            'cuisine_type' => $request->cuisine_type,
            'description'  => $request->description,
            'is_active'    => $request->boolean('is_active'),
        ];

        // Xử lý upload ảnh mới
        if ($request->hasFile('image')) {
            // Xóa ảnh cũ trong storage nếu là đường dẫn tương đối (không phải URL ngoài)
            if ($restaurant->image_url && !\Illuminate\Support\Str::startsWith($restaurant->image_url, ['http://', 'https://'])) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($restaurant->image_url);
            }
            $file     = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path     = $file->storeAs('restaurants', $filename, 'public');
            $data['image_url'] = $path; // lưu đường dẫn tương đối, hiển thị qua asset('storage/...')
        } else {
            // Không upload ảnh mới → giữ nguyên ảnh cũ
            $data['image_url'] = $restaurant->image_url;
        }

        $restaurant->update($data);

        return redirect()->route('admin.restaurant.index')
            ->with('success', 'Cập nhật nhà hàng thành công!');
    }

    public function destroy($id)
    {
        $restaurant = Restaurant::findOrFail($id);
        $this->authorizeRestaurant($restaurant);
        $restaurant->delete();
        return redirect()->route('admin.restaurant.index')
            ->with('success', 'Đã xoá nhà hàng thành công!');
    }

    // =====================================================
    // QUẢN LÝ BÀN
    // =====================================================
    public function tables($restaurantId)
    {
        $restaurant = Restaurant::findOrFail($restaurantId);
        $this->authorizeRestaurant($restaurant);
        $tables = $restaurant->tables()->orderBy('floor')->orderBy('table_number')->get();
        return view('admin.restaurant.table.index', compact('restaurant', 'tables'));
    }

    public function tableCreate($restaurantId)
    {
        $restaurant = Restaurant::findOrFail($restaurantId);
        $this->authorizeRestaurant($restaurant);
        return view('admin.restaurant.table.creat', compact('restaurant'));
    }

    public function tableStore(Request $request, $restaurantId)
    {
        $restaurant = Restaurant::findOrFail($restaurantId);
        $this->authorizeRestaurant($restaurant);

        $request->validate([
            'table_number' => 'required|string|max:50',
            'capacity'     => 'required|integer|min:1|max:50',
            'floor'        => 'required|integer|min:1|max:10',
            'shape'        => 'required|in:square,round,long',
        ]);

        $restaurant->tables()->create([
            'table_number' => $request->table_number,
            'label'        => $request->label,
            'capacity'     => $request->capacity,
            'floor'        => $request->floor,
            'shape'        => $request->shape,
            'position_x'   => $request->position_x ?? 0,
            'position_y'   => $request->position_y ?? 0,
            'is_active'    => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.restaurant.tables', $restaurantId)
            ->with('success', 'Thêm bàn thành công!');
    }

    public function tableEdit($restaurantId, $tableId)
    {
        $restaurant = Restaurant::findOrFail($restaurantId);
        $this->authorizeRestaurant($restaurant);
        $table = RestaurantTable::where('restaurant_id', $restaurantId)->findOrFail($tableId);
        return view('admin.restaurant.table.edit', compact('restaurant', 'table'));
    }

    public function tableUpdate(Request $request, $restaurantId, $tableId)
    {
        $restaurant = Restaurant::findOrFail($restaurantId);
        $this->authorizeRestaurant($restaurant);
        $table = RestaurantTable::where('restaurant_id', $restaurantId)->findOrFail($tableId);

        $request->validate([
            'table_number' => 'required|string|max:50',
            'capacity'     => 'required|integer|min:1|max:50',
            'floor'        => 'required|integer|min:1|max:10',
            'shape'        => 'required|in:square,round,long',
        ]);

        $table->update([
            'table_number' => $request->table_number,
            'label'        => $request->label,
            'capacity'     => $request->capacity,
            'floor'        => $request->floor,
            'shape'        => $request->shape,
            'position_x'   => $request->position_x ?? 0,
            'position_y'   => $request->position_y ?? 0,
            'is_active'    => $request->has('is_active'),
        ]);

        return redirect()->route('admin.restaurant.tables', $restaurantId)
            ->with('success', 'Cập nhật bàn thành công!');
    }

    public function tableDestroy($restaurantId, $tableId)
    {
        $restaurant = Restaurant::findOrFail($restaurantId);
        $this->authorizeRestaurant($restaurant);
        $table = RestaurantTable::where('restaurant_id', $restaurantId)->findOrFail($tableId);
        $table->delete();
        return redirect()->route('admin.restaurant.tables', $restaurantId)
            ->with('success', 'Đã xoá bàn!');
    }

    // =====================================================
    // QUẢN LÝ MENU
    // =====================================================
    public function menu($restaurantId)
    {
        $restaurant = Restaurant::findOrFail($restaurantId);
        $this->authorizeRestaurant($restaurant);
        $menuItems = $restaurant->menuItems()->orderBy('category')->orderBy('name')->get();
        return view('admin.restaurant.menu.index', compact('restaurant', 'menuItems'));
    }

    public function menuStore(Request $request, $restaurantId)
    {
        $restaurant = Restaurant::findOrFail($restaurantId);
        $this->authorizeRestaurant($restaurant);

        $request->validate([
            'name'     => 'required|string|max:255',
            'category' => 'required|in:main,appetizer,dessert,drink',
            'price'    => 'required|numeric|min:0',
            'image'    => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $data = [
            'name'         => $request->name,
            'category'     => $request->category,
            'price'        => $request->price,
            'description'  => $request->description,
            'is_available' => $request->boolean('is_available', true),
        ];

        if ($request->hasFile('image')) {
            $file     = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path     = $file->storeAs('menu_items', $filename, 'public');
            $data['image_url'] = $path;
        }

        $restaurant->menuItems()->create($data);

        return redirect()->route('admin.restaurant.menu', $restaurantId)
            ->with('success', 'Thêm món ăn thành công!');
    }

    public function menuDestroy($restaurantId, $itemId)
    {
        $restaurant = Restaurant::findOrFail($restaurantId);
        $this->authorizeRestaurant($restaurant);
        $item = RestaurantMenuItem::where('restaurant_id', $restaurantId)->findOrFail($itemId);
        $item->delete();
        return redirect()->route('admin.restaurant.menu', $restaurantId)
            ->with('success', 'Đã xoá món ăn!');
    }

    public function menuEdit($restaurantId, $itemId)
    {
        $restaurant = Restaurant::findOrFail($restaurantId);
        $this->authorizeRestaurant($restaurant);
        $item = RestaurantMenuItem::where('restaurant_id', $restaurantId)->findOrFail($itemId);
        return view('admin.restaurant.menu.edit', compact('restaurant', 'item'));
    }

    public function menuUpdate(Request $request, $restaurantId, $itemId)
    {
        $restaurant = Restaurant::findOrFail($restaurantId);
        $this->authorizeRestaurant($restaurant);
        $item = RestaurantMenuItem::where('restaurant_id', $restaurantId)->findOrFail($itemId);

        $request->validate([
            'name'     => 'required|string|max:255',
            'category' => 'required|in:main,appetizer,dessert,drink',
            'price'    => 'required|numeric|min:0',
            'image'    => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $data = [
            'name'         => $request->name,
            'category'     => $request->category,
            'price'        => $request->price,
            'description'  => $request->description,
            'is_available' => $request->boolean('is_available'),
        ];

        if ($request->hasFile('image')) {
            // Xóa ảnh cũ trong storage nếu là đường dẫn tương đối (không phải URL ngoài)
            if ($item->image_url && !\Illuminate\Support\Str::startsWith($item->image_url, ['http://', 'https://'])) {
                Storage::disk('public')->delete($item->image_url);
            }
            $file     = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $data['image_url'] = $file->storeAs('menu_items', $filename, 'public');
        }

        $item->update($data);

        return redirect()->route('admin.restaurant.menu', $restaurantId)
            ->with('success', 'Cập nhật món ăn "' . $item->name . '" thành công!');
    }

    // =====================================================
    // QUẢN LÝ ĐẶT BÀN
    // =====================================================
    public function bookings(Request $request)
    {
        $query = RestaurantBooking::with(['restaurant', 'user', 'table'])
            ->latest();

        $user = auth()->user();
        // Partner (role 3) chỉ thấy booking của nhà hàng thuộc chi nhánh mình
        if ($user->role == 3 && $user->branch_id) {
            $query->whereHas('restaurant', function ($q) use ($user) {
                $q->where('branch_id', $user->branch_id);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('restaurant_id')) {
            $query->where('restaurant_id', $request->restaurant_id);
        }
        if ($request->filled('date')) {
            $query->where('booking_date', $request->date);
        }

        $bookings = $query->paginate(20);

        // Dropdown lọc nhà hàng: Partner chỉ thấy nhà hàng của chính mình
        $restaurants = ($user->role == 3 && $user->branch_id)
            ? Restaurant::where('branch_id', $user->branch_id)->get()
            : Restaurant::all();

        return view('admin.restaurant.bookings.index', compact('bookings', 'restaurants'));
    }

    public function bookingUpdateStatus(Request $request, $id)
    {
        $booking = RestaurantBooking::with('restaurant')->findOrFail($id);
        $this->authorizeRestaurant($booking->restaurant);
        $request->validate(['status' => 'required|in:pending,confirmed,cancelled,completed']);
        $booking->update(['status' => $request->status]);
        return redirect()->back()->with('success', 'Cập nhật trạng thái đặt bàn thành công!');
    }
}