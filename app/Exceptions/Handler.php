<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenBlacklistedException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Throwable $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Throwable $e): JsonResponse|Response
    {
        // JWT Token Exceptions
        if ($e instanceof TokenExpiredException) {
            return response()->json([
                'status' => false,
                'status_code' => 401,
                'message' => 'Token đã hết hạn. Vui lòng đăng nhập lại hoặc refresh token.',
                'data' => []
            ], 401);
        }

        if ($e instanceof TokenInvalidException) {
            return response()->json([
                'status' => false,
                'status_code' => 401,
                'message' => 'Token không hợp lệ. Vui lòng đăng nhập lại.',
                'data' => []
            ], 401);
        }

        if ($e instanceof TokenBlacklistedException) {
            return response()->json([
                'status' => false,
                'status_code' => 401,
                'message' => 'Token đã bị vô hiệu hóa. Vui lòng đăng nhập lại.',
                'data' => []
            ], 401);
        }

        if ($e instanceof JWTException) {
            return response()->json([
                'status' => false,
                'status_code' => 401,
                'message' => 'Lỗi xác thực: ' . $e->getMessage(),
                'data' => []
            ], 401);
        }

        // Validation Exception
        if ($e instanceof ValidationException) {
            return response()->json([
                'status' => false,
                'status_code' => 422,
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $e->errors(),
                'data' => []
            ], 422);
        }

        // Permission Exceptions (nếu có package Spatie Permission)
        // if ($e instanceof \Spatie\Permission\Exceptions\UnauthorizedException) {
        //     return response()->json([
        //         'status' => false,
        //         'status_code' => 403,
        //         'message' => __('messages.error_403', [], 'vi'),
        //         'data' => []
        //     ], 403);
        // }

        // Authorization Exception
        if ($e instanceof AuthorizationException) {
            return response()->json([
                'status' => false,
                'status_code' => 401,
                'message' => __('messages.error_401', [], 'vi'),
                'data' => []
            ], 401);
        }

        // Not Found Exception
        if ($e instanceof NotFoundHttpException) {
            return response()->json([
                'status' => false,
                'status_code' => 404,
                'message' => __('messages.error_404', [], 'vi'),
                'data' => []
            ], 404);
        }

        // Firebase JWT Expired Exception (nếu có package Firebase JWT)
        // if ($e instanceof \Firebase\JWT\ExpiredException) {
        //     return response()->json([
        //         'status' => false,
        //         'status_code' => 401,
        //         'message' => 'Token đã hết hạn.',
        //         'data' => []
        //     ], 401);
        // }

        // Default exception handler
        return parent::render($request, $e);
    }
}
