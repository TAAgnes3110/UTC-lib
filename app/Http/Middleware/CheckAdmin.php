<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAdmin
{
  /**
   * Handle an incoming request.
   *
   * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
   */
  public function handle(Request $request, Closure $next): Response
  {
    if (!Auth::check()) {
      return response()->json([
        'status' => false,
        'status_code' => 401,
        'message' => 'Bạn cần đăng nhập để sử dụng chức năng này.',
        'data' => []
      ], 401);
    }

    $user = Auth::user();

    if (!$user->hasAnyRole(['ADMIN', 'SUPER_ADMIN'])) {
      return response()->json([
        'status' => false,
        'status_code' => 403,
        'message' => 'Bạn không có quyền truy cập. Chỉ quản trị viên mới được sử dụng chức năng này.',
        'data' => []
      ], 403);
    }

    return $next($request);
  }
}
