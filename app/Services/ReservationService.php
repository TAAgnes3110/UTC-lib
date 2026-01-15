<?php

namespace App\Services;

class ReservationService
{
  /**
   * Đặt chỗ sách (khi sách hết bản cứng)
   */
  public function placeReservation($userId, $bookId) {}

  /**
   * Hủy đặt chỗ
   */
  public function cancelReservation($reservationId) {}

  /**
   * Kiểm tra sách có sẵn để thông báo người đặt
   */
  public function processAvailableBooks() {}

  /**
   * Lấy danh sách đặt chỗ đang chờ của user
   */
  public function getUserReservations($userId) {}
}
