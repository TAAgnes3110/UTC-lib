<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .container {
            background-color: #f9f9f9;
            border-radius: 8px;
            padding: 30px;
            border: 1px solid #e0e0e0;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #2c3e50;
            margin: 0;
        }
        .button {
            display: inline-block;
            background-color: #3498db;
            color: #ffffff;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .otp-box {
            background-color: #ffffff;
            border: 2px dashed #3498db;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 30px 0;
        }
        .otp-code {
            font-size: 32px;
            font-weight: bold;
            color: #3498db;
            letter-spacing: 8px;
            margin: 10px 0;
        }
        .warning {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Đặt lại mật khẩu</h1>
        </div>

        <p>Xin chào <strong>{{ $name }}</strong>,</p>

        <p>Chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn.</p>

        @if($resetUrl)
            <p>Vui lòng nhấp vào nút bên dưới để đặt lại mật khẩu:</p>
            <div style="text-align: center;">
                <a href="{{ $resetUrl }}" class="button">Đặt lại mật khẩu</a>
            </div>
            <p>Hoặc copy và dán link sau vào trình duyệt:</p>
            <p style="word-break: break-all; color: #3498db;">{{ $resetUrl }}</p>
        @else
            <p>Vui lòng sử dụng mã OTP sau để đặt lại mật khẩu:</p>
            <div class="otp-box">
                <p style="margin: 0 0 10px 0; color: #666; font-size: 14px;">Mã OTP của bạn:</p>
                <div class="otp-code">{{ $token }}</div>
            </div>
        @endif

        <div class="warning">
            <strong>⚠️ Lưu ý quan trọng:</strong>
            <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                <li>Mã/Token có hiệu lực trong <strong>{{ $expiresIn }} phút</strong></li>
                <li>Không chia sẻ mã này với bất kỳ ai</li>
                <li>Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này</li>
            </ul>
        </div>

        <p>Nếu bạn gặp vấn đề, vui lòng liên hệ bộ phận hỗ trợ.</p>

        <div class="footer">
            <p>Email này được gửi tự động, vui lòng không trả lời email này.</p>
            <p>&copy; {{ date('Y') }} Hệ thống Thư viện. Tất cả quyền được bảo lưu.</p>
        </div>
    </div>
</body>
</html>
