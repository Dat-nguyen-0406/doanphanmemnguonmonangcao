<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     */
   /**
    * Handle an incoming request.
    *
    * LƯU Ý: Middleware này KHÔNG chỉ dành riêng cho Admin (role 1) — tên gọi
    * "IsAdmin" dễ gây hiểu lầm. Nó là cổng chung "đã đăng nhập + có quyền vào
    * khu /admin" cho TẤT CẢ role quản trị (1=Admin, 2=Cinema Partner,
    * 3=Restaurant Partner, 4=Shop Partner). Phân quyền CHI TIẾT theo từng
    * route con luôn được áp dụng tiếp bằng middleware `partner:1`, `partner:1,2`
    * v.v ở routes/web.php — không được xóa các middleware partner: đó.
    */
   public function handle(Request $request, Closure $next)
{
    // 1. Kiểm tra đã đăng nhập chưa
    if (!Auth::check()) {
        return redirect()->route('admin.login')->with('error', 'Bạn cần đăng nhập quyền Admin.');
    }

    // 2. Cho phép mọi role quản trị (1-4) vào khu /admin nói chung.
    //    Phân quyền chi tiết (ai vào được route nào) do middleware `partner:`
    //    ở từng nhóm route đảm nhiệm — xem routes/web.php.
    if (Auth::user()->role >= 1 && Auth::user()->role <= 4) {
        return $next($request);
    }

    return redirect()->route('admin.login')->with('error', 'Bạn phải là quản trị viên để vào trang này.');
}
}