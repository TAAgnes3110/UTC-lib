# Cấu trúc Database - Hệ thống Quản lý Thư viện UTC

## Tổng quan

Database được tổ chức theo nhóm chức năng, dễ quản lý và mở rộng.

## Danh sách Migration

### 1. Auth & Users (2025_01_01_000001)
- **roles**: Vai trò (ADMIN, LIBRARIAN, STUDENT, LECTURER, etc.)
- **permissions**: Quyền chi tiết (VIEW_BOOKS, EDIT_BOOKS, etc.)
- **user_roles**: Quan hệ nhiều-nhiều User ↔ Role
- **role_permissions**: Quan hệ nhiều-nhiều Role ↔ Permission
- **customers**: Thông tin khách hàng (sinh viên, giảng viên)

### 2. Library Core (2025_01_01_000002)
- **categories**: Danh mục sách
- **suppliers**: Nhà cung cấp
- **authors**: Tác giả
- **publishers**: Nhà xuất bản
- **library_rules**: Quy định thư viện (số sách mượn, số ngày, phí phạt)

### 3. Books (2025_01_01_000003)
- **books**: Thông tin sách
- **book_author**: Quan hệ nhiều-nhiều Book ↔ Author
- **book_copies**: Bản copy của sách (barcode, tình trạng)

### 4. Borrowing (2025_01_01_000004)
- **borrows**: Phiếu mượn
- **borrow_items**: Chi tiết sách trong phiếu mượn
- **borrow_extensions**: Gia hạn mượn sách

### 5. Fines & Payments (2025_01_01_000005)
- **fines**: Phí phạt
- **payments**: Thanh toán phí phạt

### 6. Reservations (2025_01_01_000006)
- **reservations**: Đặt chỗ sách

### 7. Files (2025_01_01_000007)
- **files**: File dùng morphs (polymorphic)
- **file_uploads**: File upload theo taxonomy
- **pdf_notes**: Ghi chú trên PDF
- **digital_signatures**: Chữ ký số

### 8. Tags (2025_01_01_000008)
- **tags**: Thẻ tag
- **taggables**: Quan hệ polymorphic Tag ↔ Model

### 9. Reports (2025_01_01_000009)
- **period_reports**: Báo cáo theo kỳ
- **period_statistics**: Thống kê theo kỳ

### 10. Logs (2025_01_01_000010)
- **audit_logs**: Nhật ký hoạt động
- **excel_import_logs**: Log import Excel

## Đặc điểm tối ưu

### Soft Deletes
Các bảng có soft deletes (deleted_at):
- roles, permissions
- categories, suppliers, authors, publishers
- books, book_copies
- borrows, customers
- library_rules

### Indexes
- **Single indexes**: Cho các field thường query (status, created_at, user_id, etc.)
- **Composite indexes**: Cho queries phức tạp:
  - `[user_id, status]` - Lọc theo user và trạng thái
  - `[status, due_date]` - Tìm sách quá hạn
  - `[taxonomy, related_id]` - Tìm file theo loại và ID liên quan

### Foreign Keys
- Sử dụng `onDelete('cascade')` cho quan hệ chặt chẽ
- Sử dụng `onDelete('set null')` cho quan hệ lỏng lẻo
- Sử dụng `onDelete('restrict')` cho dữ liệu quan trọng (fines)

### Data Types
- `unsignedInteger` cho IDs và số đếm
- `decimal(10, 2)` cho tiền tệ
- `string(63)` cho taxonomy, status
- `json` cho params (dễ mở rộng)

### Mở rộng
- Tất cả bảng đều có `params` JSON để lưu thông tin bổ sung
- Dễ thêm field mới mà không cần migration lớn

## Quan hệ chính

```
Users → Customers (1:1)
Users → Borrows (1:N)
Users → Fines (1:N)
Users → Reservations (1:N)

Books → BookCopies (1:N)
Books → Authors (N:M qua book_author)
Books → Categories (N:1)
Books → Publishers (N:1)

Borrows → BorrowItems (1:N)
BorrowItems → BookCopies (N:1)

Fines → Payments (1:N)
```

## Lưu ý

1. **customer_id** trong `file_uploads` dùng để kiểm tra quyền truy cập file, không phải multi-tenant
2. **params** JSON field cho phép mở rộng mà không cần migration
3. **Soft deletes** giúp giữ lại dữ liệu lịch sử
4. **Indexes** được tối ưu cho các query thường dùng
