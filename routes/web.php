<?php



use App\Http\Controllers\AeonController;
use App\Http\Controllers\AuthController;

use App\Http\Controllers\User\ProfileController;

use App\Http\Controllers\User\Payment\PaymentController;
use App\Http\Controllers\User\Payment\CartController;

use App\Http\Controllers\User\Shop\OrderController;
use App\Http\Controllers\User\Shop\PartnerShopController;

use App\Http\Controllers\Admin\MovieController;
use App\Http\Controllers\Admin\ShowtimeController;
use App\Http\Controllers\Admin\SeatController;
use App\Http\Controllers\Admin\DashboardController;

use App\Http\Controllers\RestaurantController;
use Illuminate\Support\Facades\Route;




// =====================================================
// TRANG CHỦ & THÔNG TIN CHI NHÁNH
// =====================================================
Route::get('/', [AeonController::class, 'index'])->name('home');
Route::get('/aeon-detail/{id}', [AeonController::class, 'show'])->name('aeon.detail');
Route::get('/shop', [AeonController::class, 'shop'])->name('shop.index');

// API endpoints for cinema
Route::get('/api/branches', [AeonController::class, 'apiBranches']);
Route::get('/api/showtimes', [AeonController::class, 'apiShowtimes']);

// =====================================================
// LUỒNG NGƯỜI DÙNG - AUTH
// =====================================================
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/restaurants', [RestaurantController::class, 'index'])->name('restaurants.index');
Route::get('/restaurants/{id}/book', [RestaurantController::class, 'showBookForm'])->name('restaurants.book');
Route::post('/restaurants/{id}/book', [RestaurantController::class, 'submitBooking'])->name('restaurants.book.submit');
Route::get('/restaurants/{id}/availability', [RestaurantController::class, 'checkAvailability'])->name('restaurants.availability');

// =====================================================
// LUỒNG NGƯỜI DÙNG - CÓ AUTH
// =====================================================
Route::middleware(['auth'])->group(function () {

    // --- GIỎ HÀNG & MUA SẮM (từ kethop) ---
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::get('/add-to-cart/{id}', [CartController::class, 'add'])->name('cart.add');
    Route::delete('/remove-from-cart', [CartController::class, 'remove'])->name('cart.remove');

    // Thanh toán Shop (VNPay + COD)
    Route::post('/vnpay-payment', [CartController::class, 'vnpay_payment'])->name('vnpay.payment');
    Route::get('/booking/vnpay-return', [CartController::class, 'vnpay_return'])->name('vnpay.return');
    Route::post('/cod-payment', [CartController::class, 'cod_payment'])->name('cod.payment');

    // --- HỒ SƠ & ĐƠN HÀNG SHOP (từ kethop) ---
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/orders', [OrderController::class, 'index'])->name('profile.orders.index');
    Route::get('/profile/orders/{id}', [OrderController::class, 'show'])->name('profile.orders.show');

    // --- ĐẶT VÉ RẠP CHIẾU PHIM (từ doanphanmem) ---
    Route::get('/showtimes/{branch}', [AeonController::class, 'showtimes'])->name('showtimes');
    Route::get('/booking/{showtime}', [AeonController::class, 'bookingForm'])->name('booking.form');
    // Xóa các route vnpay-return cũ, chỉ để lại 1 cái duy nhất
    Route::get('/cinema/vnpay-return', [PaymentController::class, 'paymentReturn'])->name('vnpay.return');
    Route::post('/booking', [AeonController::class, 'bookTicket'])->name('booking.store');
    Route::get('/my-bookings', [AeonController::class, 'myBookings'])->name('my.bookings');

    // Thanh toán Cinema (VNPay)
    Route::post('/payment/create', [PaymentController::class, 'createPayment'])->name('payment.create');
    Route::get('/payment/{booking}', [AeonController::class, 'paymentPage'])->name('payment.page');
    Route::get('/ticket/{booking}', [AeonController::class, 'eTicket'])->name('booking.ticket');
    Route::get('/payment/return', [PaymentController::class, 'paymentReturn'])->name('payment.return');

    // --- ĐẶT BÀN NHÀ HÀNG (từ doanphanmem) ---
    Route::get('/booking/payment/{id}', [RestaurantController::class, 'showPayment'])->name('booking.payment');
    Route::post('/booking/payment/{id}/vnpay', [RestaurantController::class, 'processVnPay'])->name('booking.vnpay.process');
    Route::get('/booking/vnpay-restaurant-return', [RestaurantController::class, 'vnpayReturn'])->name('booking.vnpay.return');
    Route::get('/booking/success/{id}', [RestaurantController::class, 'showSuccess'])->name('booking.success');
});

// VNPay return callback (không cần auth)
Route::get('/payment/return', [PaymentController::class, 'paymentReturn'])->name('payment.return');

// =====================================================
// LUỒNG QUẢN TRỊ (ADMIN)
// =====================================================
Route::get('/admin/login', [AuthController::class, 'showAdminLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'adminLogin']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {

    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('admin.dashboard');

    // --- PHÂN KHU ROLE 1: SUPER ADMIN ---
    Route::middleware(['partner:1'])->group(function () {

        // Quản lý Chi nhánh
        Route::get('/branches/create', [AeonController::class, 'createBranch'])->name('admin.branches.create');
        Route::post('/branches/store', [AeonController::class, 'storeBranch'])->name('admin.branches.store');
        Route::get('/branches/{id}/edit', [AeonController::class, 'editBranch'])->name('admin.branches.edit');
        Route::put('/branches/{id}', [AeonController::class, 'update'])->name('admin.branches.update');
        Route::delete('/branches/{id}', [AeonController::class, 'destroyBranch'])->name('admin.branches.destroy');

        // Quản lý Thành phố
        Route::get('/cities', [AeonController::class, 'listCities'])->name('admin.cities.index');
        Route::post('/cities/store', [AeonController::class, 'storeCity'])->name('admin.cities.store');
        Route::delete('/cities/{id}', [AeonController::class, 'destroyCity'])->name('admin.cities.destroy');

        // Quản lý Người dùng
        Route::get('/users', [AuthController::class, 'listUsers'])->name('admin.users.index');
        Route::post('/users/change-role/{id}', [AuthController::class, 'changeRole'])->name('admin.users.changeRole');
    });

    // --- PHÂN KHU ROLE 1,2: CINEMA PARTNER ---
    Route::middleware(['partner:1,2'])->prefix('cinema')->group(function () {

        // Dashboard doanh thu cinema
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.cinema.dashboard');
        Route::get('/export-revenue', [DashboardController::class, 'exportRevenue'])->name('admin.cinema.export-revenue');

        // Quản lý Phim
        Route::get('/movies', [MovieController::class, 'index'])->name('admin.movies.index');
        Route::get('/movies/create', [MovieController::class, 'create'])->name('admin.movies.create');
        Route::post('/movies', [MovieController::class, 'store'])->name('admin.movies.store');
        Route::get('/movies/{movie}/edit', [MovieController::class, 'edit'])->name('admin.movies.edit');
        Route::put('/movies/{movie}', [MovieController::class, 'update'])->name('admin.movies.update');
        Route::delete('/movies/{movie}', [MovieController::class, 'destroy'])->name('admin.movies.destroy');

        // Quản lý Lịch chiếu
        Route::get('/showtimes', [ShowtimeController::class, 'index'])->name('admin.showtimes.index');
        Route::get('/showtimes/create', [ShowtimeController::class, 'create'])->name('admin.showtimes.create');
        Route::post('/showtimes', [ShowtimeController::class, 'store'])->name('admin.showtimes.store');
        Route::get('/showtimes/{showtime}/edit', [ShowtimeController::class, 'edit'])->name('admin.showtimes.edit');
        Route::put('/showtimes/{showtime}', [ShowtimeController::class, 'update'])->name('admin.showtimes.update');
        Route::delete('/showtimes/{showtime}', [ShowtimeController::class, 'destroy'])->name('admin.showtimes.destroy');

        // Quản lý Ghế
        Route::get('/seats', [SeatController::class, 'index'])->name('admin.seats.index');
        Route::get('/seats/create', [SeatController::class, 'create'])->name('admin.seats.create');
        Route::post('/seats', [SeatController::class, 'store'])->name('admin.seats.store');
        Route::get('/seats/{seat}/edit', [SeatController::class, 'edit'])->name('admin.seats.edit');
        Route::put('/seats/{seat}', [SeatController::class, 'update'])->name('admin.seats.update');
        Route::delete('/seats/{seat}', [SeatController::class, 'destroy'])->name('admin.seats.destroy');
        Route::post('/seats/bulk-create', [SeatController::class, 'bulkCreate'])->name('admin.seats.bulk-create');
    });

    // --- PHÂN KHU ROLE 1,3: QUẢN LÝ NHÀ HÀNG ---
    Route::middleware(['partner:1,3'])->prefix('restaurant')->group(function () {
        // Nhà hàng CRUD
        Route::get('/', [\App\Http\Controllers\Admin\RestaurantAdminController::class, 'index'])->name('admin.restaurant.index');
        Route::get('/create', [\App\Http\Controllers\Admin\RestaurantAdminController::class, 'create'])->name('admin.restaurant.create');
        Route::post('/store', [\App\Http\Controllers\Admin\RestaurantAdminController::class, 'store'])->name('admin.restaurant.store');
        Route::get('/{id}/edit', [\App\Http\Controllers\Admin\RestaurantAdminController::class, 'edit'])->name('admin.restaurant.edit');
        Route::put('/{id}', [\App\Http\Controllers\Admin\RestaurantAdminController::class, 'update'])->name('admin.restaurant.update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\RestaurantAdminController::class, 'destroy'])->name('admin.restaurant.destroy');

        // Quản lý bàn
        Route::get('/{restaurantId}/tables', [\App\Http\Controllers\Admin\RestaurantAdminController::class, 'tables'])->name('admin.restaurant.tables');
        Route::get('/{restaurantId}/tables/create', [\App\Http\Controllers\Admin\RestaurantAdminController::class, 'tableCreate'])->name('admin.restaurant.tables.create');
        Route::post('/{restaurantId}/tables', [\App\Http\Controllers\Admin\RestaurantAdminController::class, 'tableStore'])->name('admin.restaurant.tables.store');
        Route::get('/{restaurantId}/tables/{tableId}/edit', [\App\Http\Controllers\Admin\RestaurantAdminController::class, 'tableEdit'])->name('admin.restaurant.tables.edit');
        Route::put('/{restaurantId}/tables/{tableId}', [\App\Http\Controllers\Admin\RestaurantAdminController::class, 'tableUpdate'])->name('admin.restaurant.tables.update');
        Route::delete('/{restaurantId}/tables/{tableId}', [\App\Http\Controllers\Admin\RestaurantAdminController::class, 'tableDestroy'])->name('admin.restaurant.tables.destroy');

        // Quản lý menu
        Route::get('/{restaurantId}/menu', [\App\Http\Controllers\Admin\RestaurantAdminController::class, 'menu'])->name('admin.restaurant.menu');
        Route::post('/{restaurantId}/menu', [\App\Http\Controllers\Admin\RestaurantAdminController::class, 'menuStore'])->name('admin.restaurant.menu.store');
        Route::get('/{restaurantId}/menu/{itemId}/edit', [\App\Http\Controllers\Admin\RestaurantAdminController::class, 'menuEdit'])->name('admin.restaurant.menu.edit');
        Route::put('/{restaurantId}/menu/{itemId}', [\App\Http\Controllers\Admin\RestaurantAdminController::class, 'menuUpdate'])->name('admin.restaurant.menu.update');
        Route::delete('/{restaurantId}/menu/{itemId}', [\App\Http\Controllers\Admin\RestaurantAdminController::class, 'menuDestroy'])->name('admin.restaurant.menu.destroy');

        // Quản lý đặt bàn
        Route::get('/bookings', [\App\Http\Controllers\Admin\RestaurantAdminController::class, 'bookings'])->name('admin.restaurant.bookings');
        Route::patch('/bookings/{id}/status', [\App\Http\Controllers\Admin\RestaurantAdminController::class, 'bookingUpdateStatus'])->name('admin.restaurant.bookings.status');
    });

    // --- PHÂN KHU ROLE 1,4: BÁN HÀNG ONLINE (SHOP PARTNER) ---
    Route::middleware(['partner:1,4'])->prefix('shop-partner')->group(function() {
    // Quản lý sản phẩm
        Route::get('/products', [PartnerShopController::class, 'index'])->name('admin.shop.index');
        Route::get('/products/create', [PartnerShopController::class, 'create'])->name('admin.shop.create');
        Route::post('/products/store', [PartnerShopController::class, 'store'])->name('admin.shop.store');
    
        Route::delete('/products/{id}', [PartnerShopController::class, 'destroy'])->name('admin.shop.destroy');
        Route::get('/products/{id}/edit', [PartnerShopController::class, 'edit'])->name('admin.shop.edit');
        Route::put('/products/{id}', [PartnerShopController::class, 'update'])->name('admin.shop.update');

        // Quản lý Danh mục
        Route::get('/categories', [PartnerShopController::class, 'categoryIndex'])->name('admin.category.index');
        Route::post('/categories/store', [PartnerShopController::class, 'categoryStore'])->name('admin.category.store');
        Route::delete('/categories/{id}', [PartnerShopController::class, 'categoryDestroy'])->name('admin.category.destroy');

        Route::get('/admin/shop/report', [AeonController::class, 'shopReport'])->name('admin.shop.report');

        // Quản lý đơn hàng (Mở rộng sau)
        Route::get('/orders', [PartnerShopController::class, 'orders'])->name('admin.shop.orders');
});
});