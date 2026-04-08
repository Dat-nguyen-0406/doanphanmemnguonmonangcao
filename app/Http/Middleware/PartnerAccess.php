<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PartnerAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles (Danh sách các role được phép vào)
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Kiểm tra đã đăng nhập chưa
        if (!Auth::check()) {
            return redirect()->route('admin.login')->with('error', 'Vui lòng đăng nhập.');
        }

        $userRole = Auth::user()->role;

        // 2. Nếu là Admin tổng (role = 1) thì cho qua hết mọi cửa
        if ($userRole == 1) {
            return $next($request);
        }

        // 3. Kiểm tra xem role của user có nằm trong danh sách được phép không
        // Ví dụ: Check:2,3 thì user có role 2 hoặc 3 sẽ được vào
        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        // 4. Nếu không khớp quyền, đá về trang chủ hoặc dashboard kèm thông báo
        return redirect()->route('admin.dashboard')->with('error', 'Bạn không có quyền truy cập khu vực này.');
    }
}