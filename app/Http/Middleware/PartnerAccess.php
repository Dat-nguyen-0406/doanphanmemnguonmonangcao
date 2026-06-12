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
    public function handle($request, Closure $next, ...$roles)
{
    if (!Auth::check()) return redirect()->route('login');

    $user = Auth::user();

    // 1. Nếu là Admin tổng (Role 1)
    if ($user->role == 1) {
        
        return $next($request);
    }

    // 2. Nếu là Partner (Role 2, 3, 4)
    // Kiểm tra xem role của họ có nằm trong danh sách được phép của Route đó không
    if (in_array($user->role, $roles)) {
        return $next($request);
    }

    return redirect()->route('admin.dashboard')->with('error', 'Bạn không có quyền truy cập khu vực này.');
}
}