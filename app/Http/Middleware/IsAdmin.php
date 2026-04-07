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
   public function handle(Request $request, Closure $next)
{
    // 1. Kiểm tra đã đăng nhập chưa
    if (!Auth::check()) {
        return redirect()->route('admin.login')->with('error', 'Bạn cần đăng nhập quyền Admin.');
    }

    // 2. Kiểm tra xem có đúng là Admin (role = 1) không
    if (Auth::check() && Auth::user()->role >= 1) {
        return $next($request);
    }

    return redirect()->route('admin.login')->with('error', 'Bạn phải là quản trị viên để vào trang này.');

    
}
}