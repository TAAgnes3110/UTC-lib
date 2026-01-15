<?php

namespace App\Services;

class AuthService
{
  /**
   * Xử lý đăng nhập, trả về token và thông tin user
   * @param array $credentials ['email', 'password']
   */
  public function login(array $credentials) {}

  /**
   * Đăng ký tài khoản mới (khách vãng lai/sinh viên tự đăng ký)
   * @param array $data
   */
  public function register(array $data) {}

  /**
   * Đăng xuất, vô hiệu hóa token
   */
  public function logout() {}

  /**
   * Lấy thông tin user hiện tại từ token
   */
  public function getProfile() {}

  /**
   * Làm mới token (Refresh Token)
   */
  public function refreshToken() {}

  /**
   * Gửi email yêu cầu đặt lại mật khẩu
   */
  public function forgotPassword($email) {}

  /**
   * Xác thực token và đặt lại mật khẩu mới
   */
  public function resetPassword($token, $email, $newPassword) {}

  /**
   * Đổi mật khẩu (khi đang đăng nhập)
   */
  public function changePassword($user, $currentPassword, $newPassword) {}
}
