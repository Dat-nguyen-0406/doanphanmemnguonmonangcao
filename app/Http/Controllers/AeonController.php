<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\City;
use App\Models\Branch;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AeonController extends Controller
{
    // =====================================================
    // QUẢN LÝ CHI NHÁNH (BRANCH MANAGEMENT)
    // =====================================================

    public function storeBranch(Request $request) {
        $request->validate([
            'name' => 'required|max:255',
            'city_id' => 'required|exists:cities,id',
            'address' => 'required',
        ]);

        Branch::create([
            'name' => $request->name,
            'city_id' => $request->city_id,
            'address' => $request->address,
            'map_link' => $request->map_link,
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Thêm chi nhánh mới thành công!');
    }

    public function createBranch() {
        $cities = City::all();
        return view('admin.branches.create', compact('cities'));
    }

    public function editBranch($id)
    {
        $branch = Branch::findOrFail($id);
        $cities = City::all();
        return view('admin.branches.edit', compact('branch', 'cities'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'city_id' => 'required|exists:cities,id',
            'address' => 'required',
        ]);

        $branch = Branch::findOrFail($id);
        $branch->update([
            'name' => $request->name,
            'city_id' => $request->city_id,
            'address' => $request->address,
            'map_link' => $request->map_link,
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Cập nhật chi nhánh thành công!');
    }

    public function destroyBranch($id)
    {
        $branch = Branch::findOrFail($id);
        $branch->delete();
        return back()->with('success', 'Đã xóa chi nhánh thành công!');
    }

    // =====================================================
    // TRANG CHỦ & CHI TIẾT CHI NHÁNH
    // =====================================================

    /**
     * Hiển thị trang chủ - lấy danh sách thành phố và chi nhánh
     */
    public function index()
    {
        $cities = City::with('branches')->get();
        return view('home', compact('cities'));
    }

    /**
     * Hiển thị trang chi tiết một chi nhánh AEON
     */
    public function show($id)
    {
        $branch = Branch::with('city')->findOrFail($id);
        return view('aeon_detail', compact('branch'));
    }

    // =====================================================
    // API ENDPOINTS CHO CINEMA
    // =====================================================

    /**
     * API: Lấy danh sách chi nhánh
     */
    public function apiBranches()
    {
        return response()->json(Branch::select('id', 'name', 'address')->get());
    }

    /**
     * API: Lấy danh sách lịch chiếu theo phim/chi nhánh
     */
    public function apiShowtimes(Request $request)
    {
        $movieId = $request->input('movie_id');
        $branchId = $request->input('branch_id');

        $query = \App\Models\Showtime::with('movie');

        if ($movieId) {
            $query->where('movie_id', $movieId);
        }
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $showtimes = $query->get()->map(function ($st) {
            return [
                'id' => $st->id,
                'time' => $st->start_time->format('H:i'),
                'price' => $st->price,
                'available_seats' => $st->availableSeatsCount(),
            ];
        });

        return response()->json($showtimes);
    }

    // =====================================================
    // QUẢN LÝ THÀNH PHỐ
    // =====================================================

    public function listCities() {
        $cities = City::withCount('branches')->get();
        return view('admin.cities.index', compact('cities'));
    }

    public function storeCity(Request $request) {
        $request->validate([
            'name' => 'required|unique:cities,name|max:100',
        ], [
            'name.unique' => 'Thành phố này đã tồn tại trong hệ thống!'
        ]);

        City::create(['name' => $request->name]);

        return redirect()->route('admin.cities.index')->with('success', 'Thêm thành phố thành công!');
    }

    public function destroyCity($id) {
        $city = City::findOrFail($id);
        $city->delete();
        return back()->with('success', 'Đã xóa thành phố!');
    }

    // =====================================================
    // MUA SẮM TRỰC TUYẾN (SHOP - từ kethop)
    // =====================================================

    public function shop(Request $request) {
        $query = Product::with(['category', 'branch', 'user']);

        // Lọc theo thương hiệu (User - Shop đối tác)
        if ($request->has('partner_id')) {
            $query->where('user_id', $request->partner_id);
        }

        // Lọc theo chi nhánh
        $query->when($request->branch_id, function($q) use ($request) {
            return $q->where('branch_id', $request->branch_id);
        });

        // Lọc theo danh mục
        $query->when($request->category_id, function($q) use ($request) {
            return $q->where('category_id', $request->category_id);
        });

        // Tìm kiếm sản phẩm
        $query->when($request->search, function($q) use ($request) {
            return $q->where('name', 'LIKE', '%' . $request->search . '%');
        });

        $products = $query->latest()->get();
        $categories = Category::all();
        $partners = User::where('role', 4)->get();

        return view('user.shop.index', compact('products', 'categories', 'partners'));
    }

    /**
     * Báo cáo doanh thu Shop Partner
     */
    public function shopReport()
    {
        $shopId = auth()->id();

        // Tổng doanh thu
        $totalRevenue = DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->where('products.user_id', $shopId)
            ->where('orders.status', 'paid')
            ->sum(DB::raw('order_details.quantity * order_details.price'));

        // Thống kê sản phẩm bán chạy
        $bestSellers = OrderDetail::with('product')
            ->whereHas('product', function($query) use ($shopId) {
                $query->where('user_id', $shopId);
            })
            ->whereHas('order', function($query) {
                $query->where('status', 'paid');
            })
            ->select('product_id', DB::raw('SUM(quantity) as sold_count'))
            ->groupBy('product_id')
            ->orderBy('sold_count', 'desc')
            ->limit(5)
            ->get();

        // Dữ liệu biểu đồ 7 ngày
        $revenueLast7Days = DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->where('products.user_id', $shopId)
            ->where('orders.status', 'paid')
            ->where('orders.created_at', '>=', now()->subDays(7))
            ->select(
                DB::raw('DATE(orders.created_at) as date'),
                DB::raw('SUM(order_details.quantity * order_details.price) as daily_total')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        return view('admin.shop.report', compact('totalRevenue', 'bestSellers', 'revenueLast7Days'));
    }

    // =====================================================
    // ĐẶT VÉ RẠP CHIẾU PHIM (CINEMA - từ doanphanmem)
    // =====================================================

    /**
     * Hiển thị lịch chiếu theo chi nhánh
     */
    public function showtimes($branchId)
    {
        $branch = Branch::findOrFail($branchId);
        $showtimesGrouped = \App\Models\Showtime::with('movie')
            ->where('branch_id', $branchId)
            ->get()
            ->groupBy('movie_id');
        return view('user.cinema.showtimes', compact('branch', 'showtimesGrouped'));
    }

    /**
     * Form đặt vé
     */
    public function bookingForm($showtimeId)
    {
        $showtime = \App\Models\Showtime::with('movie', 'branch')->findOrFail($showtimeId);
        $seats = \App\Models\Seat::where('branch_id', $showtime->branch_id)->get()->groupBy('row');

        // Lấy danh sách ghế đã đặt
        $bookedSeats = \DB::table('booking_seat')
            ->join('bookings', 'booking_seat.booking_id', '=', 'bookings.id')
            ->where('bookings.showtime_id', $showtimeId)
            ->where('bookings.status', '!=', 'cancelled')
            ->pluck('booking_seat.seat_id')->toArray();

        return view('user.cinema.booking', compact('showtime', 'seats', 'bookedSeats'));
    }

    /**
     * Xử lý đặt vé
     */
    public function bookTicket(Request $request)
    {
        Log::info('bookTicket called', $request->all());

        $request->validate([
            'showtime_id' => 'required|exists:showtimes,id',
            'seats' => 'required',
        ]);

        $showtimeId = $request->showtime_id;

        // Xử lý seats - có thể là JSON string hoặc array
        $seatsInput = $request->seats;
        if (is_string($seatsInput)) {
            $seats_array = json_decode($seatsInput, true);
        } else {
            $seats_array = (array)$seatsInput;
        }

        if (empty($seats_array)) {
            return back()->withErrors(['seats' => 'Vui lòng chọn ít nhất một ghế']);
        }

        $showtime = \App\Models\Showtime::findOrFail($showtimeId);
        $seats = \App\Models\Seat::whereIn('id', $seats_array)
            ->where('branch_id', $showtime->branch_id)
            ->get();

        if ($seats->count() != count($seats_array)) {
            return back()->withErrors(['seats' => 'Một số ghế không tìm thấy']);
        }

        // Kiểm tra ghế còn trống
        $booked = \DB::table('booking_seat')
            ->join('bookings', 'booking_seat.booking_id', '=', 'bookings.id')
            ->where('bookings.showtime_id', $showtime->id)
            ->whereIn('booking_seat.seat_id', $seats_array)
            ->where('bookings.status', '!=', 'cancelled')
            ->exists();

        if ($booked) {
            return back()->withErrors(['seats' => 'Một số ghế đã được đặt']);
        }

        $totalPrice = $seats->sum(function ($seat) use ($showtime) {
            return $seat->type === 'vip' ? $showtime->price * 1.5 : $showtime->price;
        });

        $booking = \App\Models\Booking::create([
            'user_id' => auth()->id(),
            'showtime_id' => $showtime->id,
            'total_price' => $totalPrice,
            'status' => 'pending',
            'booking_date' => now(),
        ]);

        foreach ($seats as $seat) {
            $price = $seat->type === 'vip' ? $showtime->price * 1.5 : $showtime->price;
            $booking->seats()->attach($seat->id, ['price' => $price]);
        }

        Log::info('Booking created', ['booking_id' => $booking->id]);

        return redirect()->route('payment.page', $booking->id);
    }

    /**
     * Lịch sử đặt vé của tôi
     */
    public function myBookings()
    {
        $bookings = \App\Models\Booking::with('showtime.movie', 'showtime.branch', 'seats', 'payment')
            ->where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->get();
        return view('user.cinema.my_bookings', compact('bookings'));
    }

    /**
     * Trang thanh toán vé
     */
    public function paymentPage($bookingId)
    {
        $booking = \App\Models\Booking::with('showtime.movie', 'showtime.branch', 'seats')
            ->where('user_id', auth()->id())
            ->findOrFail($bookingId);

        return view('user.cinema.payment', compact('booking'));
    }

    /**
     * Vé điện tử
     */
    public function eTicket($bookingId)
    {
        $booking = \App\Models\Booking::with('showtime.movie', 'showtime.branch', 'seats', 'payment')
            ->where('user_id', auth()->id())
            ->findOrFail($bookingId);

        if ($booking->status !== 'confirmed') {
            return redirect()->route('my.bookings')
                ->with('error', 'Vé điện tử chỉ có sau khi thanh toán thành công.');
        }

        return view('user.cinema.ticket', compact('booking'));
    }
}
