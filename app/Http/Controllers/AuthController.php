<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * Authentication Controller
 *
 * Controller xử lý tất cả các chức năng liên quan đến authentication:
 * - Đăng nhập/Đăng xuất
 * - Đăng ký tài khoản với OTP
 * - Quên mật khẩu/Reset mật khẩu với OTP hoặc link
 * - Đổi mật khẩu
 * - Quản lý JWT token (refresh, verify)
 *
 * @package App\Http\Controllers
 * @author UTC Library System
 */
class AuthController extends Controller
{
    /**
     * AuthService instance
     *
     * @var AuthService
     */
    protected AuthService $authService;

    /**
     * Constructor
     *
     * Inject AuthService để xử lý business logic
     *
     * @param AuthService $authService
     */
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Đăng nhập
     *
     * Xử lý đăng nhập với email/user_code và password.
     * Trả về JWT token và thông tin user nếu thành công.
     *
     * Rate limiting: 5 requests/minute (được cấu hình trong routes)
     *
     * @param LoginRequest $request Request đã được validate
     * @return JsonResponse
     *
     * @response 200 {
     *   "status": true,
     *   "message": "Đăng nhập thành công.",
     *   "data": {
     *     "token": "jwt_token_here",
     *     "token_type": "bearer",
     *     "expires_in": 3600,
     *     "user": {...}
     *   }
     * }
     *
     * @response 400 {
     *   "status": false,
     *   "message": "Thông tin đăng nhập không chính xác."
     * }
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->login($request->validated());

            return $this->jsonResponse([
                'status' => true,
                'message' => 'Đăng nhập thành công.',
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return $this->jsonResponse([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Đăng ký tài khoản mới
     * Tạo tài khoản mới và tự động gửi OTP qua email để xác thực.
     * Trả về JWT token ngay sau khi đăng ký thành công.
     *
     * Rate limiting: 5 requests/minute (được cấu hình trong routes)
     *
     * @param RegisterRequest $request Request đã được validate
     * @return JsonResponse
     *
     * @response 201 {
     *   "status": true,
     *   "message": "Đăng ký thành công. Vui lòng kiểm tra email để lấy mã OTP.",
     *   "data": {
     *     "token": "jwt_token_here",
     *     "token_type": "bearer",
     *     "expires_in": 3600,
     *     "user": {...}
     *   }
     * }
     *
     * @response 400 {
     *   "status": false,
     *   "message": "Email đã tồn tại trong hệ thống."
     * }
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            // Mặc định gửi OTP khi đăng ký (có thể tắt bằng send_otp=false)
            $sendOtp = $request->input('send_otp', true);

            $result = $this->authService->register($data, $sendOtp);

            $response = [
                'status' => true,
                'message' => 'Đăng ký thành công. ' . ($sendOtp ? 'Vui lòng kiểm tra email để lấy mã OTP.' : ''),
                'data' => [
                    'token' => $result['token'],
                    'token_type' => $result['token_type'],
                    'expires_in' => $result['expires_in'],
                    'user' => $result['user'],
                ],
            ];

            if (config('app.debug') && isset($result['otp'])) {
                $response['data']['otp'] = $result['otp'];
                $response['message'] .= ' (Development: OTP = ' . $result['otp'] . ')';
            }

            return $this->jsonResponse($response, 201);
        } catch (\Exception $e) {
            return $this->jsonResponse([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Xác thực OTP đăng ký
     *
     * Xác thực mã OTP đã được gửi khi đăng ký.
     * OTP có thời gian hết hạn (mặc định: 10 phút).
     *
     * Rate limiting: 10 requests/minute (được cấu hình trong routes)
     *
     * @param VerifyOtpRequest $request Request đã được validate
     * @return JsonResponse
     *
     * @response 200 {
     *   "status": true,
     *   "message": "Xác thực OTP thành công."
     * }
     *
     * @response 400 {
     *   "status": false,
     *   "message": "Mã OTP không chính xác hoặc đã hết hạn."
     * }
     */
    public function verifyRegisterOtp(VerifyOtpRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $isValid = $this->authService->verifyRegisterOtp($validated['email'], $validated['otp']);

            if (!$isValid) {
                return $this->jsonResponse([
                    'status' => false,
                    'message' => 'Mã OTP không chính xác hoặc đã hết hạn.',
                ], 400);
            }

            return $this->jsonResponse([
                'status' => true,
                'message' => 'Xác thực OTP thành công.',
            ]);
        } catch (\Exception $e) {
            return $this->jsonResponse([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Gửi lại OTP đăng ký
     *
     * Gửi lại mã OTP mới cho email đã đăng ký.
     * OTP cũ sẽ bị vô hiệu hóa khi gửi OTP mới.
     *
     * Rate limiting: 10 requests/minute (được cấu hình trong routes)
     *
     * @param Request $request
     * @return JsonResponse
     *
     * @response 200 {
     *   "status": true,
     *   "message": "Đã gửi lại mã OTP. Vui lòng kiểm tra email."
     * }
     *
     * @response 404 {
     *   "status": false,
     *   "message": "Không tìm thấy tài khoản với email này."
     * }
     */
    public function resendRegisterOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return $this->jsonResponse([
                'status' => false,
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $validator->errors(),
            ], 400);
        }

        try {
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return $this->jsonResponse([
                    'status' => false,
                    'message' => 'Nếu email tồn tại, mã OTP đã được gửi.',
                ], 200);
            }

            $otp = $this->authService->generateOtp();
            $this->authService->sendRegisterOtp($user->email, $user->name, $otp);

            $response = [
                'status' => true,
                'message' => 'Đã gửi lại mã OTP. Vui lòng kiểm tra email.',
            ];

            // Chỉ trả về OTP trong development mode
            if (config('app.debug')) {
                $response['data'] = ['otp' => $otp];
            }

            return $this->jsonResponse($response);
        } catch (\Exception $e) {
            return $this->jsonResponse([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Quên mật khẩu - Gửi OTP hoặc link reset
     *
     * Gửi mã OTP hoặc link reset password qua email.
     * Hỗ trợ 2 chế độ:
     * - use_otp=true: Gửi OTP (mặc định)
     * - use_otp=false: Gửi link reset password (dùng Laravel Password Reset)
     *
     * Rate limiting: 5 requests/minute (được cấu hình trong routes)
     *
     * @param ForgotPasswordRequest $request Request đã được validate
     * @return JsonResponse
     *
     * @response 200 {
     *   "status": true,
     *   "message": "Đã gửi mã OTP. Vui lòng kiểm tra email."
     * }
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        try {
            // Mặc định sử dụng OTP (use_otp=true)
            $useOtp = $request->input('use_otp', true);
            $this->authService->forgotPassword($request->email, $useOtp);

            return $this->jsonResponse([
                'status' => true,
                'message' => $useOtp
                    ? 'Đã gửi mã OTP. Vui lòng kiểm tra email.'
                    : 'Đã gửi link đặt lại mật khẩu. Vui lòng kiểm tra email.',
            ]);
        } catch (\Exception $e) {
            return $this->jsonResponse([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Xác thực OTP reset password
     *
     * Xác thực mã OTP đã được gửi khi quên mật khẩu.
     * OTP có thời gian hết hạn (mặc định: 10 phút).
     *
     * Rate limiting: 10 requests/minute (được cấu hình trong routes)
     *
     * @param VerifyOtpRequest $request Request đã được validate
     * @return JsonResponse
     *
     * @response 200 {
     *   "status": true,
     *   "message": "Xác thực OTP thành công. Bạn có thể đặt lại mật khẩu."
     * }
     *
     * @response 400 {
     *   "status": false,
     *   "message": "Mã OTP không chính xác hoặc đã hết hạn."
     * }
     */
    public function verifyResetPasswordOtp(VerifyOtpRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $isValid = $this->authService->verifyResetPasswordOtp($validated['email'], $validated['otp']);

            if (!$isValid) {
                return $this->jsonResponse([
                    'status' => false,
                    'message' => 'Mã OTP không chính xác hoặc đã hết hạn.',
                ], 400);
            }

            return $this->jsonResponse([
                'status' => true,
                'message' => 'Xác thực OTP thành công. Bạn có thể đặt lại mật khẩu.',
            ]);
        } catch (\Exception $e) {
            return $this->jsonResponse([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Đặt lại mật khẩu
     *
     * Đặt lại mật khẩu mới sau khi xác thực OTP hoặc token.
     * Hỗ trợ 2 chế độ:
     * - Dùng OTP: Gửi email + otp + password mới
     * - Dùng Token: Gửi email + token + password mới (Laravel Password Reset)
     *
     * Rate limiting: 5 requests/minute (được cấu hình trong routes)
     *
     * @param ResetPasswordRequest $request Request đã được validate
     * @return JsonResponse
     *
     * @response 200 {
     *   "status": true,
     *   "message": "Đặt lại mật khẩu thành công. Vui lòng đăng nhập với mật khẩu mới."
     * }
     *
     * @response 400 {
     *   "status": false,
     *   "message": "Mã OTP không chính xác hoặc đã hết hạn."
     * }
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            if (isset($data['otp']) && !empty($data['otp'])) {
                $isValid = $this->authService->verifyResetPasswordOtp($data['email'], $data['otp']);
                if (!$isValid) {
                    return $this->jsonResponse([
                        'status' => false,
                        'message' => 'Mã OTP không chính xác hoặc đã hết hạn.',
                    ], 400);
                }

                $user = User::where('email', $data['email'])->first();
                if (!$user) {
                    return $this->jsonResponse([
                        'status' => false,
                        'message' => 'Không tìm thấy tài khoản với email này.',
                    ], 404);
                }


                $user->password = Hash::make($data['password']);
                $user->save();

                return $this->jsonResponse([
                    'status' => true,
                    'message' => 'Đặt lại mật khẩu thành công. Vui lòng đăng nhập với mật khẩu mới.',
                ]);
            } else {
                $status = $this->authService->resetPassword($data);

                if ($status === \Illuminate\Support\Facades\Password::PASSWORD_RESET) {
                    return $this->jsonResponse([
                        'status' => true,
                        'message' => 'Đặt lại mật khẩu thành công. Vui lòng đăng nhập với mật khẩu mới.',
                    ]);
                }

                return $this->jsonResponse([
                    'status' => false,
                    'message' => 'Không thể đặt lại mật khẩu. Token không hợp lệ hoặc đã hết hạn.',
                ], 400);
            }
        } catch (\Exception $e) {
            return $this->jsonResponse([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Đổi mật khẩu (khi đang đăng nhập)
     *
     * Đổi mật khẩu cho user hiện tại (đã đăng nhập).
     * Yêu cầu nhập mật khẩu hiện tại để xác thực.
     * Tự động gửi email thông báo khi đổi mật khẩu thành công.
     *
     * Rate limiting: 5 requests/minute (được cấu hình trong routes)
     *
     * @param ChangePasswordRequest $request Request đã được validate
     * @return JsonResponse
     *
     * @response 200 {
     *   "status": true,
     *   "message": "Đổi mật khẩu thành công.",
     *   "data": {
     *     "user": {...}
     *   }
     * }
     *
     * @response 400 {
     *   "status": false,
     *   "message": "Mật khẩu hiện tại không chính xác."
     * }
     *
     * @response 401 {
     *   "status": false,
     *   "message": "Không tìm thấy thông tin người dùng."
     * }
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        try {
            $user = $this->authService->getProfile();
            if (!$user) {
                return $this->jsonResponse([
                    'status' => false,
                    'message' => 'Không tìm thấy thông tin người dùng. Vui lòng đăng nhập lại.',
                ], 401);
            }

            $user = $this->authService->changePassword(
                $user,
                $request->current_password,
                $request->password,
                $request->ip()
            );

            return $this->jsonResponse([
                'status' => true,
                'message' => 'Đổi mật khẩu thành công. Vui lòng kiểm tra email để xác nhận.',
                'data' => [
                    'user' => $user->fresh(['roles', 'customer']),
                ],
            ]);
        } catch (\Exception $e) {
            return $this->jsonResponse([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Đăng xuất
     *
     * Vô hiệu hóa JWT token hiện tại (blacklist token).
     * User sẽ không thể sử dụng token này để truy cập API nữa.
     *
     * @return JsonResponse
     *
     * @response 200 {
     *   "status": true,
     *   "message": "Đăng xuất thành công."
     * }
     */
    public function logout(): JsonResponse
    {
        try {
            $this->authService->logout();

            return $this->jsonResponse([
                'status' => true,
                'message' => 'Đăng xuất thành công.',
            ]);
        } catch (\Exception $e) {
            return $this->jsonResponse([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Lấy thông tin user hiện tại
     *
     * Lấy thông tin user đang đăng nhập từ JWT token.
     * Bao gồm thông tin user, roles, và customer profile.
     *
     * @return JsonResponse
     *
     * @response 200 {
     *   "status": true,
     *   "data": {
     *     "user": {
     *       "id": 1,
     *       "name": "Nguyen Van A",
     *       "email": "user@example.com",
     *       "roles": [...],
     *       "customer": {...}
     *     }
     *   }
     * }
     *
     * @response 401 {
     *   "status": false,
     *   "message": "Không tìm thấy thông tin người dùng."
     * }
     */
    public function getProfile(): JsonResponse
    {
        try {
            $user = $this->authService->getProfile();

            if (!$user) {
                return $this->jsonResponse([
                    'status' => false,
                    'message' => 'Không tìm thấy thông tin người dùng. Token không hợp lệ hoặc đã hết hạn.',
                ], 401);
            }

            return $this->jsonResponse([
                'status' => true,
                'data' => [
                    'user' => $user,
                ],
            ]);
        } catch (\Exception $e) {
            return $this->jsonResponse([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Làm mới JWT token
     *
     * Tạo JWT token mới từ token hiện tại (refresh token).
     * Token cũ sẽ bị blacklist sau khi refresh thành công.
     *
     * @return JsonResponse
     *
     * @response 200 {
     *   "status": true,
     *   "message": "Làm mới token thành công.",
     *   "data": {
     *     "token": "new_jwt_token_here",
     *     "token_type": "bearer",
     *     "expires_in": 3600
     *   }
     * }
     *
     * @response 400 {
     *   "status": false,
     *   "message": "Không thể làm mới token."
     * }
     */
    public function refreshToken(): JsonResponse
    {
        try {
            $result = $this->authService->refreshToken();

            return $this->jsonResponse([
                'status' => true,
                'message' => 'Làm mới token thành công.',
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return $this->jsonResponse([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
