<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class CheckRoleOrPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $rolesOrPermission): Response
    {
        global $currentUser, $role_prefix, $domain, $bearer_token;
        try {
            if ($bearer_token) {
                $reponse = Http::withOptions(['verify' => false])
                    ->withHeaders([
                        'Authorization' => 'Bearer '.$bearer_token,
                        'domain'=>$domain
                    ])
                    ->get(env('BASE_URL', '').'auth/init');
                if ($reponse->ok()) {
                    $data = $reponse->json();
                    if (!empty($data['current_user'])) {
                        if($data['current_user']['id'] == $currentUser->id){
                            $rolesOrPermission = str_replace("role_prefix_", $role_prefix, $rolesOrPermission);
                            $rolesOrPermission = explode("|", $rolesOrPermission);
                            if (!empty($currentUser->roles)) {
                                foreach ($currentUser->roles as $role) {
                                    if (in_array($role, $rolesOrPermission)) {
                                        return $next($request);
                                    }
                                }
                            }
                            if (!empty($currentUser->permissions)) {
                                foreach ($currentUser->permissions as $permission) {
                                    if (in_array($permission, $rolesOrPermission)) {
                                        return $next($request);
                                    }
                                }
                            }
                        }
                    }
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Bạn chưa đươợc cấp quyền để sử dụng chức năng này.'
                    ], 401);
                } else {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Phần mềm bạn đang sử dụng không tồn tại hoặc đã hết hạn. Vui lòng liên hệ với Admin để được hỗ trợ 3',
                    ], 404);
                }
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Bạn cần đăng nhập để sử dụng chức năng này.'
                ],401);
            }
        } catch ( \Exception $e ) {
            return \response()->json([
                'status' => 'error',
                'message' => 'Bạn cần đăng nhập để sử dụng chức năng này.',
            ], 401);
        }
    }
}
