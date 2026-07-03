<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // 1. Hiện Form Đăng ký
    public function showRegister() {
        return view('auth.register');
    }

    // 2. Hiện Form Đăng nhập
    public function showLogin() {
        return view('auth.login');
    }

    // 3. Xử lý Đăng ký
    public function register(RegisterRequest $request) {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'password' => Hash::make($request->password),
            ]);

            Auth::login($user);

            return redirect('/')->with('success', 'Đăng ký tài khoản AEON thành công!');
        } catch (\Exception $e) {
            Log::error("Lỗi đăng ký: " . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra, vui lòng thử lại sau.');
        }
    }

    public function showAdminLogin() {
        return view('auth.admin-login');
    }

    // 4. Xử lý Đăng nhập Khách hàng (User)
    // Chỉ cho phép role 0 (khách hàng thường) đăng nhập tại đây
    public function login(LoginRequest $request) {
        // FIX: chống brute-force / spam đăng nhập để dò tài khoản.
        // Khoá theo (email + ip) chứ không chỉ theo IP, để kẻ tấn công
        // không thể né bằng cách đổi IP nhưng vẫn nhắm vào 1 email cụ thể,
        // đồng thời không khoá nhầm người dùng khác cùng IP (mạng công ty/wifi chung).
        $throttleKey = Str::lower($request->input('email')) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'email' => "Bạn đã đăng nhập sai quá nhiều lần. Vui lòng thử lại sau {$seconds} giây.",
            ]);
        }

        if (Auth::attempt($request->only('email', 'password'))) {
            RateLimiter::clear($throttleKey);

            // FIX: regenerate session ID ngay sau khi xác thực thành công
            // để chống session fixation attack.
            $request->session()->regenerate();
            $user = Auth::user();

            // CHẶN ADMIN & PARTNER ĐĂNG NHẬP TẠI TRANG USER
            if ($user->role == 1) {
                Auth::logout();
                return back()->withErrors(['email' => 'Thông tin không chính xác.']);
            }
            if ($user->role == 2) {
                Auth::logout();
                return back()->withErrors(['email' => 'Thông tin không chính xác.']);
            }
            if ($user->role == 3) {
                Auth::logout();
                return back()->withErrors(['email' => 'Thông tin không chính xác.']);
            }
            if ($user->role == 4) {
                Auth::logout();
                return back()->withErrors(['email' => 'Thông tin không chính xác.']);
            }

            return redirect('/')->with('success', 'Đăng nhập thành công!');
        }

        // FIX: chỉ tăng bộ đếm khi đăng nhập SAI, lưu trong 60 giây.
        RateLimiter::hit($throttleKey, 60);

        return back()->withErrors(['email' => 'Thông tin không chính xác.']);
    }

    // 5. Xử lý Đăng nhập Admin / Partner
    public function adminLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'Vui lòng nhập Email.',
            'email.email' => 'Email không đúng định dạng.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        // FIX: chống brute-force / spam đăng nhập để dò tài khoản admin/partner.
        // Khoá theo (email + ip), không khoá nhầm người khác cùng IP.
        $throttleKey = Str::lower($credentials['email']) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->with('error', "Bạn đã đăng nhập sai quá nhiều lần. Vui lòng thử lại sau {$seconds} giây.");
        }

        if (Auth::attempt($credentials, $request->remember)) {
            $user = Auth::user();

            // Chỉ cho phép Role từ 1 đến 4 vào trang Admin
            if ($user->role >= 1 && $user->role <= 4) {
                RateLimiter::clear($throttleKey);
                $request->session()->regenerate();

                return redirect()->intended(route('admin.dashboard'))
                                 ->with('success', 'Chào mừng Quản trị viên ' . $user->name . ' trở lại!');
            }

            // Role 0 (Khách) cố vào Admin -> Đăng xuất ngay
            Auth::logout();
            RateLimiter::hit($throttleKey, 60);
            return back()->with('error', 'Tài khoản của bạn không có quyền truy cập khu vực quản trị.');
        }

        // FIX: chỉ tăng bộ đếm khi đăng nhập SAI, lưu trong 60 giây.
        RateLimiter::hit($throttleKey, 60);

        return back()->with('error', 'Email hoặc mật khẩu không chính xác.');
    }

    // 6. Dashboard Admin
    public function dashboard() {
        $branches = \App\Models\Branch::with('city')->get();
        $totalBranches = $branches->count();
        $totalUsers = User::where('role', 0)->count();

        return view('admin.dashboard', compact('branches', 'totalBranches', 'totalUsers'));
    }

    // 7. Danh sách Users (P2: kèm branch + tìm kiếm từ P1)
    public function listUsers(Request $request) {
        $query = User::with('branch');



        if ($request->filled('search')) {
            $searchTerm = $request->search;
            
            $query->where(function($q) use ($searchTerm) {
                $q->where('id', $searchTerm) // Tìm chính xác theo ID
                ->orWhere('name', 'LIKE', '%' . $searchTerm . '%')   // Tìm gần đúng theo Tên
                ->orWhere('email', 'LIKE', '%' . $searchTerm . '%')  // Tìm gần đúng theo Email
                ->orWhere('phone', 'LIKE', '%' . $searchTerm . '%');  // Tìm gần đúng theo Số điện thoại
            });
            }

        $users = $query->orderBy('role', 'desc')->get();
        $branches = \App\Models\Branch::with('city')->orderBy('name')->get();

        return view('admin.users.index', compact('users', 'branches'));
    }

    // 8. Cấp quyền (P2: kèm branch_id cho role 2)
    public function changeRole(Request $request, $id) {
        $user = User::findOrFail($id);

        // FIX: chặn Admin tự đổi quyền của chính mình — tránh tự khóa
        // (vô tình hạ role) hoặc bị lừa qua CSRF để tự hạ cấp.
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Không thể thay đổi quyền hạn của chính mình!');
        }

        $rules = [
            'role' => 'required|in:0,1,2,3,4'
        ];

        // Bắt buộc chọn chi nhánh AEON cho Cinema Partner (role 2)
        // và Restaurant Partner (role 3) — cả 2 role này đều quản lý
        // dữ liệu theo branch_id, thiếu nó sẽ không thao tác được gì.
        if ($request->role == 2 || $request->role == 3) {
            $rules['branch_id'] = 'required|exists:branches,id';
        } else {
            $rules['branch_id'] = 'nullable';
        }

        $validated = $request->validate($rules, [
            'branch_id.required' => 'Vui lòng chọn chi nhánh AEON cho đối tác này.',
            'branch_id.exists' => 'Chi nhánh không tồn tại.',
        ]);

        $user->role = $validated['role'];
        $user->branch_id = $request->branch_id ?? null;
        $user->save();

        return back()->with('success', 'Đã cập nhật quyền hạn cho ' . $user->name);
    }

    // 9. Đăng xuất
    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}