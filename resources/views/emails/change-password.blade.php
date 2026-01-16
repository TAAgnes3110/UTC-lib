<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông báo đổi mật khẩu thành công</title>
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
            color: #27ae60;
            margin: 0;
        }
        .success-box {
            background-color: #d4edda;
            border: 2px solid #27ae60;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 30px 0;
        }
        .info-box {
            background-color: #ffffff;
            border-left: 4px solid #3498db;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
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
            <h1>✓ Đổi mật khẩu thành công</h1>
        </div>

        <p>Xin chào <strong>{{ $name }}</strong>,</p>

        <div class="success-box">
            <p style="margin: 0; font-size: 18px; color: #27ae60; font-weight: bold;">
                Mật khẩu của bạn đã được thay đổi thành công!
            </p>
        </div>

        <div class="info-box">
            <p style="margin: 0 0 10px 0;"><strong>Thông tin:</strong></p>
            <ul style="margin: 0; padding-left: 20px;">
                <li>Thời gian: <strong>{{ $changedAt }}</strong></li>
                @if($ipAddress)
                <li>Địa chỉ IP: <strong>{{ $ipAddress }}</strong></li>
                @endif
            </ul>
        </div>

        <div class="warning">
            <strong>⚠️ Nếu bạn không thực hiện thay đổi này:</strong>
            <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                <li>Vui lòng đổi mật khẩu ngay lập tức</li>
                <li>Kiểm tra các hoạt động đáng ngờ trên tài khoản của bạn</li>
                <li>Liên hệ bộ phận hỗ trợ nếu bạn nghi ngờ tài khoản bị xâm nhập</li>
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
