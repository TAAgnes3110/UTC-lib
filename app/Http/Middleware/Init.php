<?php

namespace App\Http\Middleware;

//use App\Helpers\CurrentUser;
use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class Init
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        global $currentSystem, $currentCustomer, $currentUser, $currentPerson, $role_prefix, $period, $domain, $bearer_token, $yaht, $__token, $apis;
        Log::info(json_encode($request));
        $bearer_token = '';
        $yaht = '';
        $__token = '';
        try {
            $domain = $request->headers->get('domain');
            $period = $request->headers->get('period', '2026-2027');
            if ($domain) {
                $bearer_token = $request->bearerToken();
                $yaht = $request->headers->get('yaht');
                if ($yaht) {
                    $payload = JWT::decode($yaht, new Key(config('jwt.secret'), 'HS256'));

                }
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token hết thời hạn',
            ], 408);
        }
    }
}
