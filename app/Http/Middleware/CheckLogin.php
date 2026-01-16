<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class CheckLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        global $currentUser, $domain, $bearer_token;
        try {
            $response = Http::withOptions(['verify' => false])
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $bearer_token,
                    'domain' => $domain
                ])
                ->get(env('BASE_URL') . '/api/init');
            if ($response->ok()) {
                $data = $response->json();
                if (!empty($data['current_user'])) {
                    if ($currentUser->id == $data['current_user']['id']) {
                        return $next($request);
                    }
                }
            }
            return response()->json([
                'status' => 'error',
                'message' => 'Bạn cần đăng nhập để sử dụng chức năng này.',
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Bạn cần đăng nhập để sử dụng chức năng này.',
            ], 401);
        }
    }
}
