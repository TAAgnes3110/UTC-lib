<?php

namespace App\Services;

class LibraryRuleService
{
  /**
   * Lấy quy tắc hiện hành cho loại user (số sách tối đa, ngày mượn, tiền phạt)
   */
  public function getRuleForUserType($userType) {}

  /**
   * Kiểm tra user có được phép mượn sách không (dựa trên max_books, quá hạn, nợ phạt)
   */
  public function validateBorrowEligibility($userId) {}

  /**
   * Tính toán ngày hết hạn dựa trên ngày mượn và loại user
   */
  public function calculateDueDate($userId, $borrowDate) {}

  /**
   * Tính toán phí phạt quá hạn
   */
  public function calculateFine($daysOverdue, $userType) {}

  /**
   * Cập nhật cấu hình quy tắc (Dynamic Config)
   */
  public function updateRules($data) {}
}
