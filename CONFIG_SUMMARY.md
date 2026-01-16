# 📋 Tóm tắt các File Config quan trọng cho Deploy

## 🎯 Các file config liên quan đến Deploy, Test API, Frontend

### 1. **`.htaccess`** (Root)
- **Mục đích:** Redirect tất cả request về `/public/`
- **Vị trí:** `d:\UTC-lib\.htaccess`
- **Nội dung:** Đã được cấu hình để redirect về public folder

### 2. **`.user.ini`**
- **Mục đích:** Cấu hình PHP (open_basedir, upload_max_filesize, etc.)
- **Vị trí:** `d:\UTC-lib\.user.ini`
- **Lưu ý:** Cần chỉnh `open_basedir` theo đường dẫn thực tế trên server

### 3. **`config/cors.php`** ⭐ NEW
- **Mục đích:** Cấu hình CORS cho API
- **Vị trí:** `d:\UTC-lib\config\cors.php`
- **Biến môi trường:** `CORS_ALLOWED_ORIGINS`
- **Ví dụ:** `CORS_ALLOWED_ORIGINS=https://app.yourdomain.com,https://www.yourdomain.com`

### 4. **`config/sanctum.php`** ⭐ UPDATED
- **Mục đích:** Cấu hình Sanctum cho SPA authentication
- **Vị trí:** `d:\UTC-lib\config\sanctum.php`
- **Biến môi trường:** `SANCTUM_STATEFUL_DOMAINS`
- **Ví dụ:** `SANCTUM_STATEFUL_DOMAINS=yourdomain.com,app.yourdomain.com`

### 5. **`config/app.php`**
- **Mục đích:** Cấu hình ứng dụng chính
- **Vị trí:** `d:\UTC-lib\config\app.php`
- **Biến quan trọng:**
  - `APP_URL` - URL của ứng dụng
  - `APP_ENV` - Môi trường (production/development)
  - `APP_DEBUG` - Debug mode (false cho production)
  - `APP_TIMEZONE` - Múi giờ (Asia/Ho_Chi_Minh)
  - `APP_LOCALE` - Ngôn ngữ (vi)

### 6. **`config/database.php`**
- **Mục đích:** Cấu hình database
- **Vị trí:** `d:\UTC-lib\config\database.php`
- **Biến quan trọng:**
  - `DB_CONNECTION` - Loại database (mysql/pgsql/sqlite)
  - `DB_HOST` - Host database
  - `DB_DATABASE` - Tên database
  - `DB_USERNAME` - Username database
  - `DB_PASSWORD` - Password database

### 7. **`config/cache.php`**
- **Mục đích:** Cấu hình cache
- **Vị trí:** `d:\UTC-lib\config\cache.php`
- **Biến quan trọng:** `CACHE_STORE` (database/file/redis)

### 8. **`config/session.php`**
- **Mục đích:** Cấu hình session
- **Vị trí:** `d:\UTC-lib\config\session.php`
- **Biến quan trọng:** `SESSION_DRIVER` (database/file/redis)

### 9. **`config/queue.php`**
- **Mục đích:** Cấu hình queue
- **Vị trí:** `d:\UTC-lib\config\queue.php`
- **Biến quan trọng:** `QUEUE_CONNECTION` (database/redis/sync)

### 10. **`config/filesystems.php`**
- **Mục đích:** Cấu hình filesystem và storage
- **Vị trí:** `d:\UTC-lib\config\filesystems.php`
- **Biến quan trọng:** `FILESYSTEM_DISK` (local/public/s3)

### 11. **`config/jwt.php`**
- **Mục đích:** Cấu hình JWT authentication
- **Vị trí:** `d:\UTC-lib\config\jwt.php`
- **Biến quan trọng:** `JWT_SECRET`, `JWT_TTL`

### 12. **`config/mail.php`**
- **Mục đích:** Cấu hình email
- **Vị trí:** `d:\UTC-lib\config\mail.php`
- **Biến quan trọng:**
  - `MAIL_MAILER` - Loại mailer (smtp/mailgun/postmark)
  - `MAIL_HOST` - SMTP host
  - `MAIL_PORT` - SMTP port
  - `MAIL_USERNAME` - SMTP username
  - `MAIL_PASSWORD` - SMTP password

### 13. **`bootstrap/app.php`** ⭐ UPDATED
- **Mục đích:** Bootstrap ứng dụng, xử lý middleware và exceptions
- **Vị trí:** `d:\UTC-lib\bootstrap\app.php`
- **Thay đổi:** Đã thêm xử lý exceptions cho API (401, 422)

### 14. **`routes/api.php`**
- **Mục đích:** Định nghĩa API routes
- **Vị trí:** `d:\UTC-lib\routes\api.php`
- **Prefix:** `/api/v1`

### 15. **`vite.config.js`**
- **Mục đích:** Cấu hình Vite cho frontend assets
- **Vị trí:** `d:\UTC-lib\vite.config.js`
- **Lưu ý:** Đã có cấu hình watch ignored cho storage

---

## 🔧 Các biến môi trường quan trọng (.env)

### Application
```env
APP_NAME="UTC Library System"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
APP_TIMEZONE=Asia/Ho_Chi_Minh
APP_LOCALE=vi
```

### Database
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=utc_library
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password
```

### Cache & Session
```env
CACHE_STORE=database
SESSION_DRIVER=database
QUEUE_CONNECTION=database
```

### CORS & Sanctum (Cho Frontend)
```env
CORS_ALLOWED_ORIGINS=https://app.yourdomain.com,https://www.yourdomain.com
SANCTUM_STATEFUL_DOMAINS=yourdomain.com,app.yourdomain.com,www.yourdomain.com
```

### JWT
```env
JWT_SECRET=your_jwt_secret_here
JWT_TTL=60
```

### Mail
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

---

## 📁 Các file batch để chạy services

### 1. **`start.bat`**
- **Mục đích:** Chạy Laravel development server
- **Nội dung:** `php artisan serve --port 8501`

### 2. **`queue.bat`**
- **Mục đích:** Chạy queue worker
- **Nội dung:** `php artisan queue:work`

### 3. **`schedule.bat`**
- **Mục đích:** Chạy Laravel scheduler
- **Nội dung:** `php artisan schedule:run`

---

## 🚀 Checklist khi deploy

### Trước khi deploy
- [ ] Cấu hình `.env` với domain mới
- [ ] Cấu hình `CORS_ALLOWED_ORIGINS` với domain frontend
- [ ] Cấu hình `SANCTUM_STATEFUL_DOMAINS` với domain
- [ ] Cấu hình database connection
- [ ] Cấu hình mail settings

### Sau khi deploy
- [ ] Chạy `php artisan config:cache`
- [ ] Chạy `php artisan route:cache`
- [ ] Chạy `php artisan view:cache`
- [ ] Chạy `php artisan storage:link`
- [ ] Test API endpoints
- [ ] Test CORS headers
- [ ] Test Frontend connection

---

## 📚 Tài liệu tham khảo

- **DEPLOYMENT.md** - Hướng dẫn deploy chi tiết
- **API_TESTING.md** - Hướng dẫn test API
- **README.md** - Tài liệu tổng quan dự án

---

**Lưu ý:** Tất cả các file config đã được cập nhật và sẵn sàng cho deployment! 🎉
