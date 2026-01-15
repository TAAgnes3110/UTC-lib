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
  public function borrowByCodes($studentCode, array $barcodes, $staffId = null)
  {
    return DB::transaction(function () use ($studentCode, $barcodes, $staffId) {
      // 1. Tìm User qua Mã sinh viên (card_number)
      $customer = \App\Models\Customer::where('card_number', $studentCode)->first();
      if (!$customer) {
        throw new \Exception("Không tìm thấy độc giả với mã thẻ: {$studentCode}");
      }

      // TODO: Validate độc giả (thẻ hết hạn, đang bị khóa, v.v...)
      // if ($customer->card_expiry_date < now()) ...

      $userId = $customer->user_id;
      $bookCopyIds = [];

      // 2. Tìm BookCopy qua Barcode
      foreach ($barcodes as $barcode) {
        $copy = \App\Models\BookCopy::where('barcode', $barcode)->first();
        if (!$copy) {
          throw new \Exception("Không tìm thấy sách với mã vạch: {$barcode}");
        }

        if ($copy->status !== 'available') {
          throw new \Exception("Cuốn sách {$copy->book->title} (Mã: {$barcode}) hiện không khả dụng (Status: {$copy->status})");
        }

        $bookCopyIds[] = $copy->id;
      }

      // 3. Tạo phiếu mượn
      return $this->createBorrow($userId, $bookCopyIds, $staffId);
    });
  }

  /**
   * Tạo phiếu mượn sách (Dựa trên ID đã resolve)
   */
  public function createBorrow($userId, array $bookCopyIds, $staffId = null)
  {
    return DB::transaction(function () use ($userId, $bookCopyIds, $staffId) {
      // Lấy Config luật mượn (Ví dụ: số ngày mượn tối đa)
      // $rule = LibraryRule::where('user_type', ...)->first();
      $borrowDays = 14;

      // 1. Tạo Master Record
      $borrow = \App\Models\Borrow::create([
        'user_id' => $userId,
        'staff_id' => $staffId,
        'borrow_date' => now(),
        'due_date' => now()->addDays($borrowDays),
        'status' => 'borrowed',
        'note' => 'Mượn tại quầy',
      ]);

      // 2. Tạo Detail Items và Cập nhật trạng thái sách
      foreach ($bookCopyIds as $copyId) {
        $copy = \App\Models\BookCopy::lockForUpdate()->find($copyId);

        // Double check status trong transaction
        if ($copy->status !== 'available') {
          throw new \Exception("Sách mã {$copy->barcode} vừa bị mượn bởi người khác.");
        }

        // Cập nhật trạng thái bản copy
        $copy->update(['status' => 'borrowed']);

        // Cập nhật số lượng khả dụng của đầu sách
        $copy->book->decrement('available_copies');

        // Tạo Borrow Item
        \App\Models\BorrowItem::create([
          'borrow_id' => $borrow->id,
          'book_copy_id' => $copyId,
          'status' => 'borrowed',
        ]);
      }

      return $borrow;
    });
  }

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
