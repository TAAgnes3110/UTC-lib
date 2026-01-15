<?php

namespace App\Services;

class ReportService
{
  /**
   * Lấy thống kê tổng quan (sách, user, mượn trả)
   */
  public function getDashboardStats() {}

  /**
   * Thống kê lượt mượn theo thời gian
   */
  public function getBorrowStatistics($startDate, $endDate) {}

  /**
   * Thống kê sách nhập kho
   */
  public function getBookImportStatistics($year) {}

  /**
   * Tạo báo cáo định kỳ (lưu vào bảng period_reports)
   */
  public function generatePeriodReport($period, $type) {}
}
