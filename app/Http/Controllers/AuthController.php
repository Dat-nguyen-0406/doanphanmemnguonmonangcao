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
    public function adminLogin(Request $request)
{
    // 1. Validate dữ liệu đầu vào
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ], [
        'email.required' => 'Vui lòng nhập Email.',
        'email.email' => 'Email không đúng định dạng.',
        'password.required' => 'Vui lòng nhập mật khẩu.',
    ]);

    // 2. Thử đăng nhập với thông tin cung cấp
    if (Auth::attempt($credentials, $request->remember)) {
        $user = Auth::user();

        // 3. KIỂM TRA QUYỀN: Chỉ cho phép Role từ 1 đến 4 vào trang Admin
        // Role 0 là khách hàng bình thường - không được vào Dashboard
        if ($user->role >= 1 && $user->role <= 4) {
            $request->session()->regenerate(); // Bảo mật session chống tấn công Fixation

            return redirect()->intended(route('admin.dashboard'))
                             ->with('success', 'Chào mừng Quản trị viên ' . $user->name . ' trở lại!');
        }

        // 4. Nếu là Role 0 (Khách) nhưng cố tình vào Admin -> Đăng xuất ngay
        Auth::logout();
        return back()->with('error', 'Tài khoản của bạn không có quyền truy cập khu vực quản trị.');
    }

    // 5. Sai thông tin đăng nhập
    return back()->with('error', 'Email hoặc mật khẩu không chính xác.');
}
    // Thêm hàm này vào cuối class AuthController
      
        public function dashboard() {

        
    // 1. Lấy danh sách chi nhánh kèm tên thành phố
    $branches = \App\Models\Branch::with('city')->get();

    // 2. Lấy các con số thống kê thật
    $totalBranches = $branches->count();
    $totalUsers = \App\Models\User::where('role', 0)->count(); // Đếm khách hàng

    // 3. Truyền dữ liệu sang view
    return view('admin.dashboard', compact('branches', 'totalBranches', 'totalUsers'));
}

    public function listUsers() {
        // Lấy tất cả user, có thể phân trang nếu data lớn
        $users = User::orderBy('role', 'desc')->get(); 
        return view('admin.users.index', compact('users'));
    }

    // Cấp quyền quản trị/đối tác
    public function changeRole(Request $request, $id) {
        $user = User::findOrFail($id);
        
        // Validate role để đảm bảo không truyền số lạ vào DB
        $request->validate([
            'role' => 'required|in:0,1,2,3,4'
        ]);

        $user->role = $request->role;
        $user->save();

        return back()->with('success', 'Đã cập nhật quyền hạn cho ' . $user->name);
    }

    // 5. Đăng xuất
    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}