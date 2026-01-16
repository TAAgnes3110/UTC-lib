<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\PdfNote;

class FileService
{
  /**
   * Upload file (ảnh bìa, ebook, tài liệu minh chứng)
   */
  public function uploadFile($file, $directory, $disk = 'public') {}

  /**
   * Xóa file
   */
  public function deleteFile($filePath, $disk = 'public') {}

  /**
   * Tạo ghi chú trên file PDF (PDF Note)
   */
  public function addPdfNote($fileId, $userId, $page, $position, $content) {}

  /**
   * Lấy danh sách ghi chú của file PDF
   */
  public function getPdfNotes($fileId) {}

  /**
   * Tạo link download tạm thời
   */
  public function generateTempUrl($filePath) {}
}
