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
        if (Auth::attempt($request->only('email', 'password'))) {
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

        if (Auth::attempt($credentials, $request->remember)) {
            $user = Auth::user();

            // Chỉ cho phép Role từ 1 đến 4 vào trang Admin
            if ($user->role >= 1 && $user->role <= 4) {
                $request->session()->regenerate();

                return redirect()->intended(route('admin.dashboard'))
                                 ->with('success', 'Chào mừng Quản trị viên ' . $user->name . ' trở lại!');
            }

            // Role 0 (Khách) cố vào Admin -> Đăng xuất ngay
            Auth::logout();
            return back()->with('error', 'Tài khoản của bạn không có quyền truy cập khu vực quản trị.');
        }

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

        // Tìm kiếm theo email (từ P1)
        if ($request->filled('search_email')) {
            $query->where('email', 'LIKE', '%' . $request->search_email . '%');
        }

        // Tìm kiếm theo ID (từ P1)
        if ($request->filled('search_id')) {
            $query->where('id', $request->search_id);
        }

        // Tìm kiếm theo Tên (từ P1)
        if ($request->filled('search_name')) {
            $query->where('name', 'LIKE', '%' . $request->search_name . '%');
        }

        $users = $query->orderBy('role', 'desc')->get();
        $branches = \App\Models\Branch::with('city')->orderBy('name')->get();

        return view('admin.users.index', compact('users', 'branches'));
    }

    // 8. Cấp quyền (P2: kèm branch_id cho role 2)
    public function changeRole(Request $request, $id) {
        $user = User::findOrFail($id);

        $rules = [
            'role' => 'required|in:0,1,2,3,4'
        ];

        // Nếu cấp role 2 (Cinema Partner), bắt buộc chọn chi nhánh AEON
        if ($request->role == 2) {
            $rules['branch_id'] = 'required|exists:branches,id';
        } else {
            $rules['branch_id'] = 'nullable';
        }

        $validated = $request->validate($rules, [
            'branch_id.required' => 'Vui lòng chọn chi nhánh AEON cho Cinema Partner này.',
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
