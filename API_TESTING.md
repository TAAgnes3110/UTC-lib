# 🧪 Hướng dẫn Test API

## 📋 Danh sách API Endpoints

### Base URL
- **Development:** `http://localhost:8000/api/v1`
- **Production:** `https://yourdomain.com/api/v1`

---

## 🔐 Authentication APIs

### 1. Đăng nhập (Login)
```bash
POST /api/v1/auth/login
Content-Type: application/json

{
  "email": "admin@utc.edu.vn",
  "password": "123456"
}
```

**Response:**
```json
{
  "status": true,
  "data": {
    "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "token_type": "bearer",
    "expires_in": 3600,
    "user": {
      "id": 1,
      "email": "admin@utc.edu.vn",
      "name": "Admin UTC"
    }
  }
}
```

### 2. Đăng ký (Register)
```bash
POST /api/v1/auth/register
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "name": "User Name"
}
```

### 3. Xác thực OTP đăng ký
```bash
POST /api/v1/auth/verify-register-otp
Content-Type: application/json

{
  "email": "user@example.com",
  "otp": "123456"
}
```

### 4. Quên mật khẩu (Forgot Password)
```bash
POST /api/v1/auth/forgot-password
Content-Type: application/json

{
  "email": "user@example.com"
}
```

### 5. Reset mật khẩu
```bash
POST /api/v1/auth/reset-password
Content-Type: application/json

{
  "email": "user@example.com",
  "otp": "123456",
  "password": "newpassword123",
  "password_confirmation": "newpassword123"
}
```

### 6. Lấy thông tin profile (Cần Authentication)
```bash
GET /api/v1/auth/profile
Authorization: Bearer YOUR_TOKEN_HERE
```

### 7. Đổi mật khẩu (Cần Authentication)
```bash
POST /api/v1/auth/change-password
Authorization: Bearer YOUR_TOKEN_HERE
Content-Type: application/json

{
  "current_password": "oldpassword",
  "password": "newpassword123",
  "password_confirmation": "newpassword123"
}
```

### 8. Refresh Token
```bash
POST /api/v1/auth/refresh
Authorization: Bearer YOUR_TOKEN_HERE
```

### 9. Đăng xuất (Logout)
```bash
POST /api/v1/auth/logout
Authorization: Bearer YOUR_TOKEN_HERE
```

---

## 📝 Test với cURL

### Test Login
```bash
curl -X POST https://yourdomain.com/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@utc.edu.vn",
    "password": "123456"
  }'
```

### Test Profile (với token)
```bash
curl -X GET https://yourdomain.com/api/v1/auth/profile \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

---

## 🌐 Test với Postman

### 1. Tạo Collection mới
- Tên: "UTC Library API"
- Base URL: `https://yourdomain.com/api/v1`

### 2. Tạo Environment
- Variable: `base_url` = `https://yourdomain.com/api/v1`
- Variable: `token` = (sẽ được set sau khi login)

### 3. Test Flow

#### Step 1: Login
- Method: POST
- URL: `{{base_url}}/auth/login`
- Body (raw JSON):
```json
{
  "email": "admin@utc.edu.vn",
  "password": "123456"
}
```
- Tests Script (để lưu token):
```javascript
if (pm.response.code === 200) {
    var jsonData = pm.response.json();
    pm.environment.set("token", jsonData.data.token);
}
```

#### Step 2: Get Profile
- Method: GET
- URL: `{{base_url}}/auth/profile`
- Headers:
  - `Authorization: Bearer {{token}}`

---

## 🧪 Test với JavaScript (Fetch API)

```javascript
// Login
const login = async () => {
  const response = await fetch('https://yourdomain.com/api/v1/auth/login', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      email: 'admin@utc.edu.vn',
      password: '123456'
    })
  });
  
  const data = await response.json();
  localStorage.setItem('token', data.data.token);
  return data;
};

// Get Profile
const getProfile = async () => {
  const token = localStorage.getItem('token');
  const response = await fetch('https://yourdomain.com/api/v1/auth/profile', {
    method: 'GET',
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json',
    }
  });
  
  return await response.json();
};
```

---

## 🧪 Test với Axios

```javascript
import axios from 'axios';

// Tạo axios instance
const api = axios.create({
  baseURL: 'https://yourdomain.com/api/v1',
  headers: {
    'Content-Type': 'application/json',
  }
});

// Thêm interceptor để tự động thêm token
api.interceptors.request.use(config => {
  const token = localStorage.getItem('token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Login
const login = async (email, password) => {
  const response = await api.post('/auth/login', { email, password });
  localStorage.setItem('token', response.data.data.token);
  return response.data;
};

// Get Profile
const getProfile = async () => {
  const response = await api.get('/auth/profile');
  return response.data;
};
```

---

## ✅ Checklist Test API

- [ ] Health check endpoint (`/up`) hoạt động
- [ ] Login API trả về token
- [ ] Register API gửi OTP
- [ ] Verify OTP hoạt động
- [ ] Forgot password gửi OTP
- [ ] Reset password hoạt động
- [ ] Get profile với token hoạt động
- [ ] Change password hoạt động
- [ ] Refresh token hoạt động
- [ ] Logout hoạt động
- [ ] CORS headers được trả về đúng
- [ ] Rate limiting hoạt động (throttle)

---

## 🐛 Troubleshooting

### Lỗi 401 Unauthorized
- Kiểm tra token có đúng không
- Kiểm tra token đã hết hạn chưa
- Kiểm tra header `Authorization: Bearer TOKEN`

### Lỗi 422 Validation Error
- Kiểm tra format dữ liệu gửi lên
- Kiểm tra các trường bắt buộc
- Kiểm tra validation rules

### Lỗi CORS
- Kiểm tra `CORS_ALLOWED_ORIGINS` trong `.env`
- Clear config cache: `php artisan config:clear`
- Kiểm tra domain frontend có trong danh sách allowed origins

### Lỗi 500 Internal Server Error
- Kiểm tra log: `storage/logs/laravel.log`
- Kiểm tra database connection
- Kiểm tra `.env` configuration

---

## 📞 Support

Nếu gặp vấn đề khi test API:
1. Kiểm tra log: `storage/logs/laravel.log`
2. Kiểm tra Network tab trong browser DevTools
3. Kiểm tra response headers và status code
4. Verify API routes: `php artisan route:list`
