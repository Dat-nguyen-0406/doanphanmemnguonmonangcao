<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles)
{
    // Kiểm tra nếu role của user nằm trong danh sách được phép
    if (Auth::check() && in_array(Auth::user()->role, $roles)) {
        return $next($request);
    }

    return redirect('/')->with('error', 'Bạn không có quyền truy cập khu vực này.');
}
}
