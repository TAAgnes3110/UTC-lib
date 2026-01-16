<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::prefix('v1')->group(function () {
    Route::middleware('throttle:5,1')->group(function () {
        Route::post('/auth/login', [AuthController::class, 'login'])
            ->name('api.auth.login');

        Route::post('/auth/register', [AuthController::class, 'register'])
            ->name('api.auth.register');
    });

    Route::middleware('throttle:10,1')->group(function () {
        Route::post('/auth/verify-register-otp', [AuthController::class, 'verifyRegisterOtp'])
            ->name('api.auth.verify-register-otp');

        Route::post('/auth/resend-register-otp', [AuthController::class, 'resendRegisterOtp'])
            ->name('api.auth.resend-register-otp');
    });

    Route::middleware('throttle:5,1')->group(function () {
        Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword'])
            ->name('api.auth.forgot-password');

        Route::post('/auth/verify-reset-password-otp', [AuthController::class, 'verifyResetPasswordOtp'])
            ->name('api.auth.verify-reset-password-otp');

        Route::post('/auth/reset-password', [AuthController::class, 'resetPassword'])
            ->name('api.auth.reset-password');
    });
});

Route::middleware(['auth:api'])->prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])
            ->name('api.auth.logout');

        Route::post('/refresh', [AuthController::class, 'refreshToken'])
            ->name('api.auth.refresh');

        Route::get('/profile', [AuthController::class, 'getProfile'])
            ->name('api.auth.profile');

        Route::middleware('throttle:5,1')
            ->post('/change-password', [AuthController::class, 'changePassword'])
            ->name('api.auth.change-password');
    });

    Route::get('/user', function (Request $request) {
        return response()->json([
            'status' => true,
            'data' => [
                'user' => $request->user(),
            ],
        ]);
    })->name('api.user.info');
});
