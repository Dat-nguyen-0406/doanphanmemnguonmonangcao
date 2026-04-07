<?php
use App\Http\Controllers\AeonController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Models\City;

// Trang chủ
Route::get('/', [AeonController::class, 'index'])->name('home');
Route::get('/aeon-detail/{id}', [AeonController::class, 'show'])->name('aeon.detail');
Route::get('/shop', [AeonController::class, 'shop'])->name('shop.index');
// --- LUỒNG NGƯỜI DÙNG (USER) ---
// Đăng ký
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Đăng nhập User
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);








// --- LUỒNG QUẢN TRỊ (ADMIN) ---
// Đăng nhập Admin
Route::get('/admin/login', [AuthController::class, 'showAdminLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'adminLogin']);

// Đăng xuất chung
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Nhóm các Route dành riêng cho Admin
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])
          ->name('admin.dashboard');
    
   Route::middleware(['partner:1'])->group(function () {    

        
        Route::get('/branches/create', [AeonController::class, 'createBranch'])->name('admin.branches.create');  
        Route::post('/branches/store', [AeonController::class, 'storeBranch'])->name('admin.branches.store');
        Route::delete('/branches/{id}', [AeonController::class, 'destroyBranch'])->name('admin.branches.destroy');
        Route::get('/branches/{id}/edit', [AeonController::class, 'editBranch'])->name('admin.branches.edit');
        Route::delete('/branches/{id}', [AeonController::class, 'destroyBranch'])->name('admin.branches.destroy');
    
    // Bổ sung Route Update (vì trong Dashboard Đạt dùng nút Sửa, cần có chỗ lưu)
    Route::put('/branches/{id}', [AeonController::class, 'update'])->name('admin.branches.update');
    // Thêm các route quản lý sản phẩm, đơn hàng... ở đây
        // Quản lý Thành phố & Chi nhánh

        Route::get('/cities', [AeonController::class, 'listCities'])->name('admin.cities.index');
        Route::post('/cities/store', [AeonController::class, 'storeCity'])->name('admin.cities.store');
        Route::delete('/cities/{id}', [AeonController::class, 'destroyCity'])->name('admin.cities.destroy');
        
        // Quản lý Đối tác (User Management)
        Route::get('/users', [AuthController::class, 'listUsers'])->name('admin.users.index');
        Route::post('/users/change-role/{id}', [AuthController::class, 'changeRole'])->name('admin.users.changeRole');
    });

    Route::middleware(['partner:1,2'])->prefix('cinema')->group(function() {
        // Route::get('/movies', [CinemaController::class, 'index'])->name('admin.cinema.index');
        // Thêm các route quản lý phim tại đây
    });

    // 6. Phân khu dành cho Quản lý QUÁN ĂN (Food - Role 3)
    Route::middleware(['partner:1,3'])->prefix('food')->group(function() {
        // Route::get('/menus', [FoodController::class, 'index'])->name('admin.food.index');
        // Thêm các route quản lý món ăn tại đây
    });

    // 7. Phân khu dành cho BÁN HÀNG ONLINE (Shop - Role 4)
    Route::middleware(['partner:1,4'])->prefix('shop-partner')->group(function() {
        // Route::get('/products', [PartnerShopController::class, 'index'])->name('admin.shop_partner.index');
        // Thêm các route quản lý sản phẩm đối tác tại đây
    });
});