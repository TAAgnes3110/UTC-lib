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
  public function uploadFile($file, $directory, $disk = 'public')
  {
    $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
    return $file->storeAs($directory, $filename, $disk);
  }

  /**
   * Xóa file
   */
  public function deleteFile($filePath, $disk = 'public')
  {
    if (Storage::disk($disk)->exists($filePath)) {
      return Storage::disk($disk)->delete($filePath);
    }
    return false;
  }

  /**
   * Tạo ghi chú trên file PDF (PDF Note)
   */
  public function addPdfNote($fileId, $userId, $page, $position, $content)
  {
    return PdfNote::create([
      'file_id' => $fileId,
      'user_id' => $userId,
      'page_number' => $page,
      'x_position' => $position['x'],
      'y_position' => $position['y'],
      'content' => $content
    ]);
  }

  /**
   * Lấy danh sách ghi chú của file PDF
   */
  public function getPdfNotes($fileId)
  {
    return PdfNote::where('file_id', $fileId)->with('user')->get();
  }

  /**
   * Tạo link download tạm thời
   */
  public function generateTempUrl($filePath)
  {
    return Storage::temporaryUrl($filePath, now()->addMinutes(60));
  }
}
