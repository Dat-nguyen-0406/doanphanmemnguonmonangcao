<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Restaurant;
use App\Models\RestaurantBooking;
use App\Models\RestaurantMenuItem;
use App\Models\RestaurantTable;
use Illuminate\Http\Request;

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
            'image_url'    => 'nullable|url|max:500',
        ]);

        Restaurant::create([
            'branch_id'    => $request->branch_id,
            'name'         => $request->name,
            'cuisine_type' => $request->cuisine_type,
            'description'  => $request->description,
            'image_url'    => $request->image_url,
            'is_active'    => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.restaurant.index')
            ->with('success', 'Nhà hàng "' . $request->name . '" đã được tạo thành công!');
    }

    public function edit($id)
    {
        $restaurant = Restaurant::findOrFail($id);
        $branches = Branch::all();
        return view('admin.restaurant.item.edit', compact('restaurant', 'branches'));
    }

    public function update(Request $request, $id)
    {
        $restaurant = Restaurant::findOrFail($id);

        $request->validate([
            'branch_id'    => 'required|exists:branches,id',
            'name'         => 'required|string|max:255',
            'cuisine_type' => 'nullable|string|max:100',
            'description'  => 'nullable|string',
            'image_url'    => 'nullable|url|max:500',
        ]);

        $restaurant->update([
            'branch_id'    => $request->branch_id,
            'name'         => $request->name,
            'cuisine_type' => $request->cuisine_type,
            'description'  => $request->description,
            'image_url'    => $request->image_url,
            'is_active'    => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.restaurant.index')
            ->with('success', 'Cập nhật nhà hàng thành công!');
    }

    public function destroy($id)
    {
        $restaurant = Restaurant::findOrFail($id);
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
        $tables = $restaurant->tables()->orderBy('floor')->orderBy('table_number')->get();
        return view('admin.restaurant.table.index', compact('restaurant', 'tables'));
    }

    public function tableCreate($restaurantId)
    {
        $restaurant = Restaurant::findOrFail($restaurantId);
        return view('admin.restaurant.table.creat', compact('restaurant'));
    }

    public function tableStore(Request $request, $restaurantId)
    {
        $restaurant = Restaurant::findOrFail($restaurantId);

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
        $table = RestaurantTable::where('restaurant_id', $restaurantId)->findOrFail($tableId);
        return view('admin.restaurant.table.edit', compact('restaurant', 'table'));
    }

    public function tableUpdate(Request $request, $restaurantId, $tableId)
    {
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
        $menuItems = $restaurant->menuItems()->orderBy('category')->orderBy('name')->get();
        return view('admin.restaurant.menu.index', compact('restaurant', 'menuItems'));
    }

    public function menuStore(Request $request, $restaurantId)
    {
        $restaurant = Restaurant::findOrFail($restaurantId);

        $request->validate([
            'name'     => 'required|string|max:255',
            'category' => 'required|in:main,appetizer,dessert,drink',
            'price'    => 'required|numeric|min:0',
        ]);

        $restaurant->menuItems()->create([
            'name'         => $request->name,
            'category'     => $request->category,
            'price'        => $request->price,
            'description'  => $request->description,
            'image_url'    => $request->image_url,
            'is_available' => $request->boolean('is_available', true),
        ]);

        return redirect()->route('admin.restaurant.menu', $restaurantId)
            ->with('success', 'Thêm món ăn thành công!');
    }

    public function menuDestroy($restaurantId, $itemId)
    {
        $item = RestaurantMenuItem::where('restaurant_id', $restaurantId)->findOrFail($itemId);
        $item->delete();
        return redirect()->route('admin.restaurant.menu', $restaurantId)
            ->with('success', 'Đã xoá món ăn!');
    }

    public function menuEdit($restaurantId, $itemId)
    {
        $restaurant = Restaurant::findOrFail($restaurantId);
        $item = RestaurantMenuItem::where('restaurant_id', $restaurantId)->findOrFail($itemId);
        return view('admin.restaurant.menu.edit', compact('restaurant', 'item'));
    }

    public function menuUpdate(Request $request, $restaurantId, $itemId)
    {
        $item = RestaurantMenuItem::where('restaurant_id', $restaurantId)->findOrFail($itemId);

        $request->validate([
            'name'     => 'required|string|max:255',
            'category' => 'required|in:main,appetizer,dessert,drink',
            'price'    => 'required|numeric|min:0',
        ]);

        $item->update([
            'name'         => $request->name,
            'category'     => $request->category,
            'price'        => $request->price,
            'description'  => $request->description,
            'image_url'    => $request->image_url,
            'is_available' => $request->boolean('is_available'),
        ]);

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
        $restaurants = Restaurant::all();

        return view('admin.restaurant.bookings.index', compact('bookings', 'restaurants'));
    }

    public function bookingUpdateStatus(Request $request, $id)
    {
        $booking = RestaurantBooking::findOrFail($id);
        $request->validate(['status' => 'required|in:pending,confirmed,cancelled,completed']);
        $booking->update(['status' => $request->status]);
        return redirect()->back()->with('success', 'Cập nhật trạng thái đặt bàn thành công!');
    }
}