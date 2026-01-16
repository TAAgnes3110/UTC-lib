<?php

namespace App\Services;

use App\Models\User;
use App\Mail\RegisterOtpMail;
use App\Mail\ResetPasswordMail;
use App\Mail\ChangePasswordMail;
use App\Helpers\Helpers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

/**
 * @uses \PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth
 * @uses \PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException
 */
// JWT Package: php-open-source-saver/jwt-auth
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;

class AuthService
{
  protected UserService $userService;

  public function __construct(UserService $userService)
  {
    $this->userService = $userService;
  }

  /**
   * Xử lý đăng nhập, trả về token và thông tin user
   *
   * @param array $credentials ['email' hoặc 'user_code', 'password']
   * @return array ['token', 'user', 'expires_in']
   * @throws ValidationException
   * @throws \Exception
   */
  public function login(array $credentials): array
  {
    $user = null;
    if (!empty($credentials['email'])) {
      $user = $this->userService->getUserByEmail($credentials['email']);
    } elseif (!empty($credentials['user_code'])) {
      $user = $this->userService->getUserByCode($credentials['user_code']);
    }

    if (!$user) {
      throw ValidationException::withMessages([
        'credentials' => 'Thông tin đăng nhập không chính xác.'
      ]);
    }

    if ($user->status !== 1) {
      throw ValidationException::withMessages([
        'status' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.'
      ]);
    }

    if (!Hash::check($credentials['password'], $user->password)) {
      throw ValidationException::withMessages([
        'credentials' => 'Mật khẩu không chính xác.'
      ]);
    }

    try {
      $token = JWTAuth::fromUser($user);

      $user->load(['roles', 'customer']);

      return [
        'token' => $token,
        'token_type' => 'bearer',
        'expires_in' => config('jwt.ttl') * 60,
        'user' => $user,
      ];
    } catch (JWTException $e) {
      throw new \Exception('Không thể tạo token: ' . $e->getMessage());
    }
  }

  /**
   * Đăng ký tài khoản mới (khách vãng lai/sinh viên tự đăng ký)
   *
   * @param array $data ['name', 'email', 'password', 'user_code', 'role'?]
   * @param bool $sendOtp Có gửi OTP email không (mặc định: true)
   * @return array ['token', 'user', 'expires_in', 'otp'?]
   * @throws \Exception
   */
  public function register(array $data, bool $sendOtp = true): array
  {
    DB::beginTransaction();
    try {
      if (empty($data['role'])) {
        $data['role'] = 'student';
      }

      $user = $this->userService->createUser([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => $data['password'],
        'user_code' => $data['user_code'] ?? null,
        'status' => 1,
        'role' => $data['role'],
      ]);

      $token = JWTAuth::fromUser($user);

      $user->load(['roles', 'customer']);

      $otp = null;
      if ($sendOtp) {
        $otp = $this->generateOtp();
        $this->sendRegisterOtp($user->email, $user->name, $otp);
      }

      DB::commit();

      $result = [
        'token' => $token,
        'token_type' => 'bearer',
        'expires_in' => config('jwt.ttl') * 60,
        'user' => $user,
      ];

      if ($sendOtp && $otp) {
        $result['otp'] = $otp;
      }

      return $result;
    } catch (\Exception $e) {
      DB::rollBack();
      throw $e;
    }
  }

  /**
   * Đăng xuất, vô hiệu hóa token
   *
   * @return bool
   */
  public function logout(): bool
  {
    try {
      JWTAuth::invalidate(JWTAuth::getToken());
      return true;
    } catch (JWTException $e) {
      return false;
    }
  }

  /**
   * Lấy thông tin user hiện tại từ token
   *
   * @return User|null
   */
  public function getProfile(): ?User
  {
    try {
      $user = JWTAuth::parseToken()->authenticate();
      if ($user) {
        $user->load(['roles', 'customer']);
      }
      return $user;
    } catch (JWTException $e) {
      return null;
    }
  }

  /**
   * Làm mới token (Refresh Token)
   *
   * @return array ['token', 'expires_in']
   * @throws \Exception
   */
  public function refreshToken(): array
  {
    try {
      $token = JWTAuth::parseToken()->refresh();

      return [
        'token' => $token,
        'token_type' => 'bearer',
        'expires_in' => config('jwt.ttl') * 60,
      ];
    } catch (JWTException $e) {
      throw new \Exception('Không thể làm mới token: ' . $e->getMessage());
    }
  }

  /**
   * Gửi email yêu cầu đặt lại mật khẩu
   *
   * @param string $email
   * @param bool $useOtp Sử dụng OTP thay vì link reset (mặc định: true)
   * @return string
   * @throws ValidationException
   */
  public function forgotPassword(string $email, bool $useOtp = true): string
  {
    $user = $this->userService->getUserByEmail($email);
    if (!$user) {
      return Password::RESET_LINK_SENT;
    }

    if ($useOtp) {
      $otp = $this->generateOtp();
      $this->sendResetPasswordOtp($user->email, $user->name, $otp);
      return Password::RESET_LINK_SENT;
    } else {
      $token = Password::createToken($user);
      $resetUrl = url("/password/reset/{$token}?email=" . urlencode($email));
      $this->sendResetPasswordLink($user->email, $user->name, $token, $resetUrl);
      return Password::RESET_LINK_SENT;
    }
  }

  /**
   * Xác thực token và đặt lại mật khẩu mới
   *
   * @param array $data ['token', 'email', 'password']
   * @return string
   * @throws ValidationException
   */
  public function resetPassword(array $data): string
  {
    $status = Password::reset(
      $data,
      function (User $user, string $password) {
        $this->userService->changePassword($user->id, $password);
      }
    );

    if ($status === Password::PASSWORD_RESET) {
      return Password::PASSWORD_RESET;
    }

    throw ValidationException::withMessages([
      'email' => [__($status)]
    ]);
  }

  /**
   * Đổi mật khẩu (khi đang đăng nhập)
   *
   * @param User $user
   * @param string $currentPassword
   * @param string $newPassword
   * @param string|null $ipAddress Địa chỉ IP (tùy chọn)
   * @return User
   * @throws ValidationException
   */
  public function changePassword(User $user, string $currentPassword, string $newPassword, ?string $ipAddress = null): User
  {
    if (!Hash::check($currentPassword, $user->password)) {
      throw ValidationException::withMessages([
        'current_password' => 'Mật khẩu hiện tại không chính xác.'
      ]);
    }

    $updatedUser = $this->userService->changePassword($user->id, $newPassword);
    try {
      $this->sendChangePasswordNotification($updatedUser->email, $updatedUser->name, $ipAddress ?? request()->ip());
    } catch (\Exception $e) {
      Log::error('Failed to send change password email: ' . $e->getMessage());
    }

    return $updatedUser;
  }

  /**
   * Verify token và lấy user
   *
   * @param string|null $token
   * @return User|null
   */
  public function verifyToken(?string $token = null): ?User
  {
    try {
      if ($token) {
        $user = JWTAuth::setToken($token)->authenticate();
      } else {
        $user = JWTAuth::parseToken()->authenticate();
      }

      if ($user) {
        $user->load(['roles', 'customer']);
      }

      return $user;
    } catch (JWTException $e) {
      return null;
    }
  }

  /**
   * Tạo mã OTP 6 số
   *
   * @param int $length Độ dài OTP (mặc định: 6)
   * @return string
   */
  public function generateOtp(?int $length = null): string
  {
    $length = $length ?? config('otp.length', 6);
    return Helpers::generateRandomNumber($length);
  }

  /**
   * Gửi email OTP đăng ký
   *
   * @param string $email
   * @param string $name
   * @param string $otp
   * @param int $expiresIn Thời gian hết hạn (phút)
   * @return void
   */
  public function sendRegisterOtp(string $email, string $name, string $otp, ?int $expiresIn = null): void
  {
    $expiresIn = $expiresIn ?? config('otp.register_expiry', 10);
    Cache::put("register_otp:{$email}", $otp, now()->addMinutes($expiresIn));
    Mail::to($email)->send(new RegisterOtpMail($otp, $name, $expiresIn));
  }

  /**
   * Gửi email reset password với OTP
   *
   * @param string $email
   * @param string $name
   * @param string $otp
   * @param int $expiresIn Thời gian hết hạn (phút)
   * @return void
   */
  public function sendResetPasswordOtp(string $email, string $name, string $otp, ?int $expiresIn = null): void
  {
    $expiresIn = $expiresIn ?? config('otp.reset_password_expiry', 10);
    Cache::put("reset_password_otp:{$email}", $otp, now()->addMinutes($expiresIn));
    Mail::to($email)->send(new ResetPasswordMail($otp, $email, $name, null, $expiresIn));
  }

  /**
   * Gửi email reset password với link
   *
   * @param string $email
   * @param string $name
   * @param string $token
   * @param string $resetUrl
   * @param int $expiresIn Thời gian hết hạn (phút)
   * @return void
   */
  public function sendResetPasswordLink(string $email, string $name, string $token, string $resetUrl, int $expiresIn = 60): void
  {
    Mail::to($email)->send(new ResetPasswordMail($token, $email, $name, $resetUrl, $expiresIn));
  }

  /**
   * Gửi email thông báo đổi mật khẩu thành công
   *
   * @param string $email
   * @param string $name
   * @param string|null $ipAddress
   * @return void
   */
  public function sendChangePasswordNotification(string $email, string $name, ?string $ipAddress = null): void
  {
    Mail::to($email)->send(new ChangePasswordMail(
      $name,
      now()->format('d/m/Y H:i:s'),
      $ipAddress
    ));
  }

  /**
   * Xác thực OTP đăng ký
   *
   * @param string $email
   * @param string $otp
   * @return bool
   */
  public function verifyRegisterOtp(string $email, string $otp): bool
  {
    $cachedOtp = Cache::get("register_otp:{$email}");
    if ($cachedOtp && $cachedOtp === $otp) {
      Cache::forget("register_otp:{$email}");
      return true;
    }
    return false;
  }

  /**
   * Xác thực OTP reset password
   *
   * @param string $email
   * @param string $otp
   * @return bool
   */
  public function verifyResetPasswordOtp(string $email, string $otp): bool
  {
    $cachedOtp = Cache::get("reset_password_otp:{$email}");
    if ($cachedOtp && $cachedOtp === $otp) {
      Cache::forget("reset_password_otp:{$email}");
      return true;
    }
    return false;
  }
}
