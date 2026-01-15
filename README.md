# 📚 HỆ THỐNG QUẢN LÝ THƯ VIỆN UTC

**Đồ án:** Thiết kế và Xây dựng Hệ thống Quản lý Thư viện
**Nền tảng:** Web Application (Laravel Framework)
**Đối tượng:** Trường Đại học Giao thông Vận tải

---

## 📖 Giới thiệu

Hệ thống Quản lý Thư viện UTC là một ứng dụng web hiện đại được xây dựng trên nền tảng Laravel, giúp tự động hóa và quản lý hiệu quả các hoạt động của thư viện trường đại học. Hệ thống hỗ trợ quản lý sách, mượn/trả sách, tính phạt tự động, chữ ký số và nhiều tính năng nâng cao khác.

---

## 🛠️ Công nghệ sử dụng

### Backend
- **Framework:** Laravel 12.0
- **Ngôn ngữ:** PHP 8.2+
- **Authentication:** Laravel Sanctum 4.2 (API Token)
- **Database:** MySQL/PostgreSQL/SQLite
- **ORM:** Eloquent ORM
- **Queue System:** Laravel Queue (Background Jobs)

### Frontend
- **CSS Framework:** Tailwind CSS 4.0
- **Build Tool:** Vite 7.0
- **Icons:** Font Awesome 6.4.0
- **Charts:** Chart.js (cho báo cáo thống kê)
- **JavaScript:** Vanilla JS / Axios

### Thư viện & Package
- **Excel Import/Export:** Maatwebsite Excel 3.1
- **Testing:** PHPUnit 11.5
- **Code Style:** Laravel Pint
- **Development:** Laravel Sail, Laravel Pail

### Kiến trúc
- **Pattern:** MVC (Model-View-Controller)
- **API:** RESTful API
- **Authentication:** Session-based & Token-based (Sanctum)
- **Authorization:** RBAC (Role-Based Access Control)

---

## ✨ Chức năng chính

### 🔐 Authentication & Authorization
- Đăng ký/Đăng nhập với xác minh email
- Phân quyền RBAC (Admin, Librarian, Student, Lecturer)
- Quản lý session và API tokens
- Khóa/Mở tài khoản

### 📚 Quản lý sách
- CRUD đầu sách với đầy đủ thông tin (ISBN, tác giả, NXB)
- Quản lý bản sao sách với barcode
- Theo dõi trạng thái: available, borrowed, lost, damaged
- Upload ảnh bìa và ebook
- Tìm kiếm và lọc sách nâng cao

### 🔄 Mượn - Trả sách
- Mượn sách với kiểm tra quy tắc tự động
- Trả sách với tính phạt quá hạn tự động
- Gia hạn mượn sách
- Đặt chỗ sách khi hết bản sao
- Quét barcode để mượn/trả

### 📜 Rule Engine (Quy tắc động)
- Cấu hình quy tắc thư viện động (không hard-code)
- Số sách tối đa, thời gian mượn, mức phạt theo user_type
- Chính sách gia hạn linh hoạt

### 💸 Phạt & Thanh toán
- Tính phạt tự động (quá hạn, mất sách, hư hỏng)
- Quản lý phiếu phạt
- Thanh toán đa phương thức: tiền mặt, chuyển khoản, QR code (VNPAY/Momo)

### ✍️ Chữ ký số
- Ký số xác nhận phiếu phạt
- Ký biên bản mất sách
- Lưu hash và xác minh chữ ký

### 📂 File & Tài liệu
- Upload và quản lý file với phân quyền
- Ghi chú trên file PDF (PDF Notes)

### 📊 Nhập/Xuất Excel
- Import sách, bản sao sách, người dùng
- Export sách, phiếu phạt, thống kê
- Validate dữ liệu và ghi log import

### ⚙️ Hệ thống & Nâng cao
- Queue & Jobs: Email nhắc trả sách, tính phạt định kỳ
- Logging & Audit: Ghi log đăng nhập và thao tác quan trọng
- Báo cáo thống kê với biểu đồ

---

## 🚀 Cài đặt

### Yêu cầu hệ thống
- PHP >= 8.2
- Composer
- Node.js >= 18.x và NPM
- MySQL/PostgreSQL hoặc SQLite
- Git

### Các bước cài đặt

1. **Clone repository**
```bash
git clone https://github.com/your-username/UTC-lib.git
cd UTC-lib
```

2. **Cài đặt dependencies**
```bash
composer install
npm install
```

3. **Cấu hình môi trường**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Cấu hình database trong `.env`**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=utc_library
DB_USERNAME=root
DB_PASSWORD=
```

5. **Chạy migrations và seeders**
```bash
php artisan migrate --seed
```

6. **Build assets**
```bash
npm run build
npm run dev
```

7. **Chạy server**
```bash
php artisan serve
```

Truy cập: `http://localhost:8000`

### Tài khoản mặc định

Sau khi chạy seeders, bạn có thể đăng nhập với:

- **Admin:** admin@utc.edu.vn / 123456
- **Librarian:** librarian@utc.edu.vn / 123456
- **Student:** student@utc.edu.vn / 123456
- **Lecturer:** lecturer@utc.edu.vn / 123456

---

## 📁 Cấu trúc dự án

```
UTC-lib/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   ├── Models/
│   └── Providers/
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
├── public/
├── resources/
│   ├── css/
│   ├── js/
│   └── views/
├── routes/
│   ├── api.php
│   └── web.php
├── storage/
├── tests/
└── ui-mockups/          # UI mockups cho tham khảo
```

---

## 🗄️ Database

Hệ thống sử dụng **28 bảng** bao gồm:

- **Authentication:** users, roles, permissions, sessions, personal_access_tokens
- **Quản lý sách:** categories, suppliers, books, book_copies
- **Mượn/Trả:** library_rules, borrows, borrow_items, reservations, borrow_extensions
- **Phạt & Thanh toán:** fines, payments
- **Chữ ký số & File:** digital_signatures, files, pdf_notes
- **Thông tin người dùng:** customers
- **Logging:** audit_logs, excel_import_logs
- **Queue:** jobs, job_batches, failed_jobs

---

## 🧪 Testing

```bash
# Chạy tests
php artisan test

# Với coverage
php artisan test --coverage
```

---

## 📝 Scripts hữu ích

```bash
# Development (chạy server, queue, logs, vite cùng lúc)
composer dev

# Setup project từ đầu
composer setup

# Code style fix
./vendor/bin/pint
```

---

## 📄 License

Dự án này được phát hành dưới giấy phép [MIT License](https://opensource.org/licenses/MIT).

---

## 👥 Tác giả

**Đồ án:** Thiết kế và Xây dựng Hệ thống Quản lý Thư viện
**Trường:** Đại học Giao thông Vận tải
**Năm:** 2025

---

## 📞 Liên hệ

Nếu có câu hỏi hoặc đề xuất, vui lòng tạo issue trên GitHub hoặc liên hệ qua email.

---

**⭐ Nếu dự án này hữu ích, hãy cho một star!**
