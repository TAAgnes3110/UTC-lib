<?php

namespace App\Services;

class CustomerService
{
  /**
   * Tạo hồ sơ bạn đọc (khi user đăng ký hoặc admin tạo)
   */
  public function createCustomerProfile($userId, array $data) {}

  /**
   * Cập nhật thông tin bạn đọc
   */
  public function updateCustomerProfile($customerId, array $data) {}

  /**
   * Tìm bạn đọc theo số thẻ thư viện
   */
  public function getByCardNumber($cardNumber) {}

  /**
   * Kiểm tra giới hạn mượn (số lượng, phạt quá hạn...)
   */
  public function checkBorrowLimit($customerId) {}

  /**
   * Gia hạn thẻ thư viện
   */
  public function renewCard($customerId) {}
}
