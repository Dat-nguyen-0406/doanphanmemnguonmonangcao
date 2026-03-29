<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    // 1. Hiện Form Đăng ký
    public function showRegister() {
        return view('auth.register');
    }
    // 3. Hiện Form Đăng nhập
    public function showLogin() {
        return view('auth.login');
    }

    // 2. Xử lý Đăng ký
    public function register(RegisterRequest $request) {
        try {
            // Dữ liệu đã được Validate tự động bởi RegisterRequest
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'password' => Hash::make($request->password), // Mã hóa mật khẩu
            ]);

            Auth::login($user); // Đăng nhập ngay sau khi đăng ký xong
            
            return redirect('/')->with('success', 'Đăng ký tài khoản AEON thành công!');
        } catch (\Exception $e) {
            // Xử lý ngoại lệ nếu có lỗi Database (yêu cầu trong ảnh image_e0c406)
            Log::error("Lỗi đăng ký: " . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra, vui lòng thử lại sau.');
        }
    }

     public function showAdminLogin() {
        return view('auth.admin-login');
    }


        // 4. Xử lý Đăng nhập
        // Xử lý Login cho Khách hàng (User)
    public function login(LoginRequest $request) {
        if (Auth::attempt($request->only('email', 'password'))) {
            // Nếu là Admin mà lại đăng nhập ở trang User -> Đá sang trang Admin
            if (Auth::user()->role == 1) {
                return redirect()->route('admin.dashboard');
            }
            return redirect('/')->with('success', 'Đăng nhập thành công!');
        }
        return back()->withErrors(['email' => 'Thông tin không chính xác.']);
    }

    // Xử lý Login cho Admin
    public function adminLogin(LoginRequest $request) {
        if (Auth::attempt($request->only('email', 'password'))) {
            // Kiểm tra xem có đúng role Admin không
            if (Auth::user()->role == 1) {
                return redirect()->route('admin.dashboard');
            }
            
            // Nếu là User thường mà định vào trang Admin -> Logout và báo lỗi
            Auth::logout();
            return redirect()->route('admin.login')->with('error', 'Bạn không có quyền truy cập.');
        }
        return back()->withErrors(['email' => 'Tài khoản Admin không đúng.']);
    }

    // 5. Đăng xuất
    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}