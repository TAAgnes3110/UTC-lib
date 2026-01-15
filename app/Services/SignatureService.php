<?php

namespace App\Services;

class SignatureService
{
  /**
   * Tạo hash cho dữ liệu (phiếu phạt/biên bản)
   */
  public function generateHash($data) {}

  /**
   * Ký số vào tài liệu (lưu signature, IP, user agent)
   */
  public function signDocument($userId, $documentType, $documentId, $data) {}

  /**
   * Xác minh chữ ký (Verify Hash & Signature)
   */
  public function verifySignature($documentType, $documentId) {}

  /**
   * Lấy danh sách chữ ký của tài liệu
   */
  public function getSignatures($documentType, $documentId) {}
}
