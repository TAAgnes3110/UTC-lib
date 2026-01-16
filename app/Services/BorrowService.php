<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Borrow;
use App\Models\BorrowItem;
use App\Models\BookCopy;
use App\Models\Customer;

class BorrowService
{
  /**
   * Xử lý mượn sách dựa trên Mã Sinh Viên và Danh sách Mã Vạch (dùng cho Scanner)
   *
   * @param string $studentCode Mã thẻ/Mã SV
   * @param array $barcodes Danh sách mã vạch các sách cần mượn
   * @param string|null $bookCode Mã sách (Optional - dùng để check khớp nếu cần)
   * @param int|null $staffId ID nhân viên thực hiện
   */
  public function borrowByCodes($studentCode, array $barcodes, $staffId = null) {}

  /**
   * Tạo phiếu mượn sách (Dựa trên ID đã resolve)
   */
  public function createBorrow($userId, array $bookCopyIds, $staffId = null) {}

  /**
   * Trả sách (xử lý tình trạng sách trả, tính phạt nếu có)
   */
  public function returnBorrow($borrowId, array $returnDetails) {}

  /**
   * Yêu cầu gia hạn sách
   */
  public function requestExtension($borrowId, $reason) {}

  /**
   * Phê duyệt yêu cầu gia hạn
   */
  public function approveExtension($extensionId, $approvedBy) {}

  /**
   * Lấy danh sách phiếu mượn quá hạn
   */
  public function getOverdueBorrows() {}

  /**
   * Lấy lịch sử mượn trả của user
   */
  public function getUserBorrowHistory($userId) {}
}
