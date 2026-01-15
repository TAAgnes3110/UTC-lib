<?php

namespace App\Services;

class FineService
{
  /**
   * Tạo phiếu phạt thủ công hoặc tự động
   */
  public function createFine($userId, $amount, $reason, $borrowId = null) {}

  /**
   * Tính toán tiền phạt dựa trên ngày quá hạn và quy tắc
   */
  public function calculateOverdueFine($borrowId) {}

  /**
   * Tạo URL thanh toán (VNPAY/Momo)
   */
  public function createPaymentUrl($fineId, $gateway) {}

  /**
   * Xử lý callback/IPN từ cổng thanh toán
   */
  public function handlePaymentCallback($gateway, $data) {}

  /**
   * Xác nhận thanh toán (tiền mặt/chuyển khoản)
   */
  public function confirmPayment($fineId, $amount, $method) {}

  /**
   * Lấy danh sách phạt chưa đóng của user
   */
  public function getUnpaidFines($userId) {}
}
