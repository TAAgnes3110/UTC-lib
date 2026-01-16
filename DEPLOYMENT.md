# 🚀 Hướng dẫn Deploy UTC Library System

## 📋 Mục lục
1. [Chuẩn bị Domain](#chuẩn-bị-domain)
2. [Cấu hình Server](#cấu-hình-server)
3. [Cấu hình Environment](#cấu-hình-environment)
4. [Deploy Code](#deploy-code)
5. [Cấu hình CORS và API](#cấu-hình-cors-và-api)
6. [Test API và Frontend](#test-api-và-frontend)
7. [Chạy Queue và Schedule](#chạy-queue-và-schedule)

---

## 🌐 Chuẩn bị Domain

### 1. Cấu hình DNS
- Trỏ domain về IP server của bạn
- Nếu có subdomain cho API: `api.yourdomain.com`
- Nếu có subdomain cho Frontend: `app.yourdomain.com` hoặc `www.yourdomain.com`

### 2. Cấu hình SSL Certificate
- Cài đặt SSL certificate (Let's Encrypt hoặc từ nhà cung cấp)
- Đảm bảo HTTPS hoạt động đúng

---

## ⚙️ Cấu hình Server

### 1. Upload code lên server
```bash
# Upload toàn bộ code lên server (trừ node_modules, vendor)
# Hoặc clone từ Git repository
git clone <your-repo-url>
cd UTC-lib
```

### 2. Cài đặt dependencies
```bash
composer install --optimize-autoloader --no-dev
npm install
npm run build
```

### 3. Cấu hình quyền thư mục
```bash
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 4. Tạo symbolic link cho storage
```bash
php artisan storage:link
```

---

## 🔧 Cấu hình Environment

### 1. Tạo file `.env` từ `.env.example`
```bash
cp .env.example .env
php artisan key:generate
```

### 2. Cấu hình `.env` cho Production

```env
# Application
APP_NAME="UTC Library System"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
APP_TIMEZONE=Asia/Ho_Chi_Minh
APP_LOCALE=vi

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=utc_library
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

# Cache & Session
CACHE_STORE=database
SESSION_DRIVER=database
QUEUE_CONNECTION=database

# CORS Configuration (nếu có Frontend riêng)
CORS_ALLOWED_ORIGINS=https://app.yourdomain.com,https://www.yourdomain.com

# Sanctum Configuration (cho SPA authentication)
SANCTUM_STATEFUL_DOMAINS=yourdomain.com,app.yourdomain.com,www.yourdomain.com

# JWT Configuration (nếu dùng JWT)
JWT_SECRET=your_jwt_secret_here
JWT_TTL=60

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"

# File Upload
FILESYSTEM_DISK=local
```

### 3. Cập nhật file `.user.ini` (nếu cần)
```ini
open_basedir=/www/wwwroot/your-project-path/:/tmp/
upload_max_filesize=50M
post_max_size=50M
memory_limit=256M
```

---

## 📦 Deploy Code

### 1. Chạy migrations
```bash
php artisan migrate --force
```

### 2. Chạy seeders (nếu cần dữ liệu mẫu)
```bash
php artisan db:seed --force
```

### 3. Cache các config
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

---

## 🌍 Cấu hình CORS và API

### 1. File `config/cors.php` đã được tạo
- Cấu hình `CORS_ALLOWED_ORIGINS` trong `.env`
- Ví dụ: `CORS_ALLOWED_ORIGINS=https://app.yourdomain.com,https://www.yourdomain.com`

### 2. File `config/sanctum.php`
- Cấu hình `SANCTUM_STATEFUL_DOMAINS` trong `.env`
- Thêm domain của bạn vào danh sách

### 3. Kiểm tra API routes
- API routes nằm trong `routes/api.php`
- Prefix: `/api/v1/`
- Ví dụ: `https://yourdomain.com/api/v1/auth/login`

---

## 🧪 Test API và Frontend

### 1. Test API với Postman/cURL

#### Test Health Check
```bash
curl https://yourdomain.com/up
```

#### Test Login API
```bash
curl -X POST https://yourdomain.com/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@utc.edu.vn",
    "password": "123456"
  }'
```

#### Test API với Token
```bash
curl https://yourdomain.com/api/v1/auth/profile \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### 2. Test Frontend

#### Nếu Frontend riêng (React/Vue/Angular)
- Cấu hình API base URL: `https://yourdomain.com/api`
- Đảm bảo CORS đã được cấu hình đúng
- Test các chức năng: Login, Register, API calls

#### Nếu Frontend tích hợp trong Laravel
- Truy cập: `https://yourdomain.com`
- Test các trang và chức năng

### 3. Test File Upload
```bash
curl -X POST https://yourdomain.com/api/v1/files/upload \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -F "file=@/path/to/file.jpg"
```

---

## 🔄 Chạy Queue và Schedule

### 1. Queue Worker
```bash
# Chạy queue worker (chạy trong background hoặc dùng supervisor)
php artisan queue:work --tries=3

# Hoặc dùng file batch
./queue.bat
```

### 2. Schedule (Cron Job)
Thêm vào crontab:
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

Hoặc dùng file batch:
```bash
./schedule.bat
```

### 3. Supervisor Configuration (khuyến nghị)
Tạo file `/etc/supervisor/conf.d/utc-lib-queue.conf`:
```ini
[program:utc-lib-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /path-to-your-project/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path-to-your-project/storage/logs/queue.log
stopwaitsecs=3600
```

---

## 📝 Checklist Deploy

- [ ] Domain đã được cấu hình và trỏ về server
- [ ] SSL certificate đã được cài đặt
- [ ] Code đã được upload lên server
- [ ] Dependencies đã được cài đặt (`composer install`, `npm install`, `npm run build`)
- [ ] File `.env` đã được cấu hình đúng
- [ ] Database đã được migrate
- [ ] Storage link đã được tạo (`php artisan storage:link`)
- [ ] Config đã được cache (`php artisan config:cache`)
- [ ] CORS đã được cấu hình đúng
- [ ] Queue worker đã được chạy
- [ ] Cron job cho schedule đã được cấu hình
- [ ] API đã được test thành công
- [ ] Frontend đã được test thành công

---

## 🐛 Troubleshooting

### Lỗi CORS
- Kiểm tra `CORS_ALLOWED_ORIGINS` trong `.env`
- Clear config cache: `php artisan config:clear`

### Lỗi 500 Internal Server Error
- Kiểm tra log: `storage/logs/laravel.log`
- Kiểm tra quyền thư mục: `storage`, `bootstrap/cache`
- Kiểm tra `.env` có đúng không

### Lỗi Database Connection
- Kiểm tra thông tin database trong `.env`
- Kiểm tra database đã được tạo chưa
- Kiểm tra user database có quyền truy cập

### API không hoạt động
- Kiểm tra routes: `php artisan route:list`
- Kiểm tra middleware authentication
- Kiểm tra CORS configuration

---

## 📞 Support

Nếu gặp vấn đề, kiểm tra:
1. Log files: `storage/logs/laravel.log`
2. Server logs
3. Browser console (cho Frontend)
4. Network tab trong DevTools

---

**Chúc bạn deploy thành công! 🎉**
