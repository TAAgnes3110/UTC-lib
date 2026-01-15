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
# Chạy migrations và seeders (tạo database + dữ liệu mẫu)
php artisan migrate:fresh --seed

# Hoặc nếu database đã có, chỉ chạy seeders
php artisan db:seed
```

**Lưu ý:** `migrate:fresh` sẽ xóa toàn bộ dữ liệu hiện có và tạo lại từ đầu. Chỉ sử dụng trong môi trường development!

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

---

## 🚀 Hướng dẫn nhanh

### Bước 1: Setup dự án
```bash
# Clone và cài đặt
git clone <repository-url>
cd UTC-lib
composer install
npm install

# Cấu hình môi trường
cp .env.example .env
php artisan key:generate

# Cấu hình database trong .env
# Sau đó chạy migrations và seeders
php artisan migrate:fresh --seed
```

### Bước 2: Chạy ứng dụng
```bash
# Terminal 1: Chạy Laravel server
php artisan serve

# Terminal 2: Chạy Vite (development)
npm run dev

# Hoặc build production
npm run build
```

### Bước 3: Đăng nhập và khám phá
1. Truy cập `http://localhost:8000`
2. Đăng nhập với một trong các tài khoản mặc định (xem bên dưới)
3. Khám phá các chức năng theo quyền của từng role

### Tài khoản mặc định

Sau khi chạy seeders, bạn có thể đăng nhập với các tài khoản sau:

#### 🔑 Super Admin (Quản trị viên hệ thống)
- **Email:** admin@utc.edu.vn
- **Mật khẩu:** 123456
- **Mã người dùng:** ADMIN001
- **Quyền hạn:** Toàn quyền hệ thống (quản lý người dùng, sách, mượn trả, phạt, báo cáo, cấu hình)
- **Thông tin:** Admin UTC - Phòng Công nghệ thông tin

#### 📚 Librarian (Thủ thư)
- **Email:** librarian@utc.edu.vn
- **Mật khẩu:** 123456
- **Mã người dùng:** LIB001
- **Quyền hạn:** Quản lý sách, mượn trả, phạt, thanh toán, báo cáo, import/export
- **Thông tin:** Nguyễn Thị Lan - Thư viện

#### 👨‍🎓 Student (Sinh viên)
- **Email:** student@utc.edu.vn
- **Mật khẩu:** 123456
- **Mã người dùng:** SV20250001
- **Mã sinh viên:** SV20250001
- **Lớp:** IT-K64
- **Chuyên ngành:** Kỹ thuật phần mềm
- **Quyền hạn:** Mượn sách, xem báo cáo cá nhân
- **Thông tin:** Nguyễn Văn Sinh Viên - Khoa Công nghệ thông tin

#### 👨‍🏫 Lecturer (Giảng viên)
- **Email:** lecturer@utc.edu.vn
- **Mật khẩu:** 123456
- **Mã người dùng:** GV20250001
- **Mã nhân viên:** GV20250001
- **Chức vụ:** Giảng viên
- **Học hàm:** Phó Giáo sư, Tiến sĩ
- **Quyền hạn:** Mượn sách (ưu tiên), xem báo cáo
- **Thông tin:** PGS.TS Trần Văn Giảng Viên - Khoa Công nghệ thông tin

### 📊 Dữ liệu mẫu

Sau khi chạy seeders, hệ thống sẽ có:

#### 📚 Sách và Danh mục
- **5 danh mục:** Công nghệ thông tin, Kinh tế vận tải, Toán học, Xây dựng, Cơ khí
- **4 đầu sách mẫu:**
  - Nhập môn Lập trình C++ (10 bản sao, 1 đang mượn)
  - Logistics và Quản lý chuỗi cung ứng (5 bản sao)
  - Lập trình Laravel Framework (15 bản sao, 3 đang mượn, có ebook)
  - Toán cao cấp A1 (20 bản sao, 2 đang mượn)
- **3 nhà xuất bản:** NXB Giao thông Vận tải, NXB Giáo dục Việt Nam, NXB Tài Chính
- **3 tác giả:** Phạm Văn Ất, Đoàn Thị Hồng Vân, Nguyễn Đình Trí
- **3 nhà cung cấp:** NXB Giao thông Vận tải, Fahasa, NXB Giáo dục Việt Nam

#### 🔄 Mượn/Trả sách
- **5 phiếu mượn mẫu:**
  - 3 phiếu đang mượn (1 của sinh viên, 1 của giảng viên, 1 quá hạn)
  - 1 phiếu đã trả (có phạt quá hạn đã thanh toán)
  - 1 phiếu quá hạn chưa trả (có phạt chưa thanh toán)

#### 💸 Phạt và Thanh toán
- **3 phiếu phạt mẫu:**
  - 1 phiếu đã thanh toán (quá hạn trả sách)
  - 1 phiếu chưa thanh toán (quá hạn trả sách)
  - 1 phiếu chưa thanh toán (làm mất thẻ thư viện)

#### ⚙️ Quy tắc thư viện
- **Sinh viên:** Tối đa 5 sách, 14 ngày, phạt 2,000 VNĐ/ngày
- **Giảng viên:** Tối đa 10 sách, 30 ngày, phạt 1,000 VNĐ/ngày
- **Thủ thư:** Tối đa 20 sách, 60 ngày, không phạt

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

Hệ thống sử dụng **30+ bảng** bao gồm:

### Authentication & Authorization
- `users` - Tài khoản người dùng
- `roles` - Vai trò (SUPER_ADMIN, ADMIN, LIBRARIAN, LECTURER, STUDENT)
- `permissions` - Quyền hạn
- `user_roles` - Phân quyền người dùng
- `role_permissions` - Phân quyền theo vai trò
- `sessions` - Phiên đăng nhập
- `personal_access_tokens` - API tokens (Sanctum)

### Quản lý người dùng
- `customers` - Thông tin khách hàng (sinh viên, giảng viên, nhân viên)
- `student_profiles` - Thông tin chi tiết sinh viên (mã SV, lớp, chuyên ngành, GPA)
- `staff_profiles` - Thông tin chi tiết nhân viên/giảng viên (mã NV, chức vụ, học hàm)

### Quản lý sách
- `categories` - Danh mục sách
- `authors` - Tác giả
- `publishers` - Nhà xuất bản
- `suppliers` - Nhà cung cấp
- `books` - Đầu sách
- `book_author` - Quan hệ sách - tác giả (many-to-many)
- `book_copies` - Bản sao sách (với barcode)

### Mượn/Trả sách
- `library_rules` - Quy tắc thư viện (theo user_type)
- `borrows` - Phiếu mượn
- `borrow_items` - Chi tiết sách mượn
- `borrow_extensions` - Gia hạn mượn sách
- `reservations` - Đặt chỗ sách

### Phạt & Thanh toán
- `fines` - Phiếu phạt
- `payments` - Thanh toán phạt

### File & Tài liệu
- `files` - File đính kèm (polymorphic)
- `file_uploads` - File upload với phân quyền
- `pdf_notes` - Ghi chú trên PDF
- `digital_signatures` - Chữ ký số (polymorphic)

### Tags
- `tags` - Thẻ
- `taggables` - Quan hệ tag (polymorphic)

### Báo cáo & Thống kê
- `period_reports` - Báo cáo theo kỳ
- `period_statistics` - Thống kê theo kỳ

### Logging & Audit
- `audit_logs` - Nhật ký hoạt động
- `excel_import_logs` - Log import Excel

### Queue & Jobs
- `jobs` - Hàng đợi công việc
- `job_batches` - Batch jobs
- `failed_jobs` - Jobs thất bại

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

# Tạo dữ liệu mẫu mới (nếu cần)
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=LibrarySeeder
php artisan db:seed --class=BorrowSeeder
```

## 🔍 Kiểm tra và Debug

### Kiểm tra database
```bash
# Xem danh sách migrations
php artisan migrate:status

# Reset database (xóa tất cả và chạy lại)
php artisan migrate:fresh --seed

# Xem logs
php artisan pail
# hoặc
tail -f storage/logs/laravel.log
```

### Kiểm tra tài khoản
```bash
# Tạo tài khoản mới qua tinker
php artisan tinker
>>> $user = \App\Models\User::create(['name' => 'Test', 'email' => 'test@test.com', 'password' => bcrypt('123456')]);
>>> $user->roles()->attach(5); // Gán role STUDENT
```

### Test API (nếu có)
```bash
# Test với Sanctum token
curl -X GET http://localhost:8000/api/books \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

---

## 📄 License

Dự án này được phát hành dưới giấy phép [MIT License](https://opensource.org/licenses/MIT).

---

## 👥 Tác giả
**Tác giả:** TAAgnes
**Đồ án:** Thiết kế và Xây dựng Hệ thống Quản lý Thư viện
**Trường:** Đại học Giao thông Vận tải
**Năm:** 2025

---

## 📞 Liên hệ

Nếu có câu hỏi hoặc đề xuất, vui lòng tạo issue trên GitHub hoặc liên hệ qua email taagnes3110@gmail.com
---

**⭐ Nếu dự án này hữu ích, hãy cho một star!**
