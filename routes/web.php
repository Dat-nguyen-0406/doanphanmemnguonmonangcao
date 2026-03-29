<?php
use App\Http\Controllers\AeonController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

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
    
    Route::get('/dashboard', function () {
        return "Chào mừng Admin Đạt đến với hệ thống AEON!";
    })->name('admin.dashboard');

    // Thêm các route quản lý sản phẩm, đơn hàng... ở đây
});