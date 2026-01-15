<?php

namespace App\Services;


use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Tag;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BookService
{
  protected $model;
  protected $fileService;

  public function __construct(Book $model, FileService $fileService)
  {
    $this->model = $model;
    $this->fileService = $fileService;
  }

  /**
   * Lấy danh sách sách có lọc và phân trang (Keyword, Author, Category...)
   */
  public function getBooks(array $filters = [], $limit = 10)
  {
    $query = $this->model()->query();
    if (!empty($filters['keyword'])) {
      $query->where('title', 'like', '%' . $filters['keyword'] . '%');
    }
    return $query->paginate($limit);
  }

  /**
   * Lấy chi tiết sách kèm thông tin tác giả, nxb, vị trí
   */
  public function getBookById($id) {}

  /**
   * Tạo mới sách
   */
  public function storeBook(array $data) {}

  /**
   * Upload ảnh bìa sách
   */
  public function uploadCoverImage($bookId, $file) {}

  /**
   * Upload tài liệu điện tử (ebook)
   */
  public function uploadEbook($bookId, $file) {}

  /**
   * Cập nhật thông tin sách
   */
  public function updateBook($id, array $data) {}

  /**
   * Xóa sách (soft delete hoặc check ràng buộc)
   */
  public function deleteBook($id) {}

  /**
   * Thêm bản sao (book copy) - Tạo Barcode tự động nếu không nhập
   */
  public function addCopy($bookId, array $data) {}

  /**
   * Cập nhật bản sao (tình trạng, giá)
   */
  public function updateCopy($copyId, array $data) {}

  /**
   * Xóa bản sao
   */
  public function deleteCopy($copyId) {}

  /**
   * Tra cứu theo ISBN hoặc Barcode (hỗ trợ Scanner)
   */
  public function searchByCode($code) {}


  /**
   * Xử lý tags (thêm mới nếu không tồn tại)
   */
  protected function syncTags($book, array $tags) {}

  protected function generateBarcode($bookId) {}

  protected function updateBookCounts($bookId) {}
}
