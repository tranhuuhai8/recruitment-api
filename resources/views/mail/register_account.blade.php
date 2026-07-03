<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác Thực Tài Khoản - VietJob</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 0;
            color: #333333;
            -webkit-font-smoothing: antialiased;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }
        .header {
            background-color: #28a745;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .content {
            padding: 40px 30px;
            text-align: left;
        }
        .content p {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 20px;
            color: #555555;
        }
        .content .btn-container {
            text-align: center;
            margin: 35px 0;
        }
        .btn {
            background-color: #28a745;
            color: #ffffff !important;
            text-decoration: none;
            padding: 14px 28px;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            display: inline-block;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #218838;
        }
        .footer {
            background-color: #f8fafc;
            padding: 20px;
            text-align: center;
            font-size: 13px;
            color: #888888;
            border-top: 1px solid #eeeeee;
        }
        .note {
            font-size: 14px;
            color: #777777;
            background-color: #f9f9f9;
            padding: 15px;
            border-left: 4px solid #dddddd;
            margin-top: 30px;
        }
        .link-fallback {
            word-break: break-all;
            color: #28a745;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Xác Thực Tài Khoản</h1>
        </div>
        
        <div class="content">
            <p>Xin chào,</p>
            
            <p>Cảm ơn bạn đã đăng ký tài khoản trên hệ thống tuyển dụng <strong>VietJob</strong>. Địa chỉ email đăng ký của bạn là: <strong>{{ $data['mail_address'] }}</strong>.</p>
            
            <p>Để hoàn tất quá trình đăng ký, vui lòng kích hoạt tài khoản của bạn bằng cách nhấn vào nút bên dưới:</p>
            
            <div class="btn-container">
                <a href="{{ $data['verify_url'] }}" class="btn">
                    Xác Thực Email
                </a>
            </div>

            <p>Lưu ý: Liên kết kích hoạt này có hiệu lực trong 24 giờ. Nếu bạn không yêu cầu đăng ký tài khoản, vui lòng bỏ qua email này.</p>

            <div class="note">
                <p style="margin: 0; font-size: 14px;">Nếu nút trên không hoạt động, vui lòng copy và dán đường dẫn sau vào trình duyệt của bạn:</p>
                <a href="{{ $data['verify_url'] }}" class="link-fallback">
                    {{ $data['verify_url'] }}
                </a>
            </div>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} VietJob. All rights reserved.</p>
            <p>Đây là email tự động, vui lòng không phản hồi.</p>
        </div>
    </div>
</body>
</html>
