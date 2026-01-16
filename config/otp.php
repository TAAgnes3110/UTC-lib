<?php

return [
  /*
    |--------------------------------------------------------------------------
    | OTP Configuration
    |--------------------------------------------------------------------------
    |
    | Cấu hình thời gian hết hạn cho các loại OTP trong hệ thống.
    | Tất cả thời gian được tính bằng phút.
    |
    */

  /**
   * Thời gian hết hạn mã OTP đăng ký (phút)
   * Mặc định: 10 phút
   */
  'register_expiry' => (int) env('OTP_REGISTER_EXPIRY', 10),

  /**
   * Thời gian hết hạn mã OTP reset password (phút)
   * Mặc định: 10 phút
   */
  'reset_password_expiry' => (int) env('OTP_RESET_PASSWORD_EXPIRY', 10),

  /**
   * Độ dài mã OTP (số ký tự)
   * Mặc định: 6 số
   */
  'length' => (int) env('OTP_LENGTH', 6),

  /**
   * Số lần tối đa được nhập sai OTP trước khi khóa tạm thời
   * Mặc định: 5 lần
   */
  'max_attempts' => (int) env('OTP_MAX_ATTEMPTS', 5),

  /**
   * Thời gian khóa sau khi nhập sai quá nhiều lần (phút)
   * Mặc định: 15 phút
   */
  'lockout_duration' => (int) env('OTP_LOCKOUT_DURATION', 15),
];
