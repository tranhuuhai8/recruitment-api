<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Recruitment ITH18</title>
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
            background-color: #0b5cff;
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
            background-color: #0b5cff;
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
            background-color: #0046d5;
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
            color: #0b5cff;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Khôi Phục Mật Khẩu</h1>
        </div>
        
        <div class="content">
            <p>Chào bạn,</p>
            
            <p>Chúng tôi nhận được yêu cầu khôi phục mật khẩu cho tài khoản liên kết với địa chỉ email: <strong>{{ $data['mail_address'] }}</strong>.</p>
            
            <p>{{ __('auth.mail.forgot_password.text') }} Bằng cách nhấn vào nút dưới đây:</p>
            
            <div class="btn-container">
                <a href="{{ config('app.url_home') }}/auth/reset-password?token=<?= $data['token'] ?>&email=<?= $data['mail_address'] ?>" class="btn">
                    Đặt Lại Mật Khẩu
                </a>
            </div>
            
            <p>Nếu bạn không thực hiện yêu cầu này, bạn có thể bỏ qua email này một cách an toàn. Mật khẩu của bạn sẽ không bị thay đổi cho đến khi bạn truy cập vào liên kết trên và cập nhật mật khẩu mới.</p>
            
            <div class="note">
                <p style="margin: 0; font-size: 14px;">Nếu nút trên không hoạt động, vui lòng copy và dán đường dẫn sau vào trình duyệt của bạn:</p>
                <a href="{{ config('app.url_home') }}/auth/reset-password?token=<?= $data['token'] ?>&email=<?= $data['mail_address'] ?>" class="link-fallback">
                    {{ config('app.url_home') }}/auth/reset-password?token=<?= $data['token'] ?>&email=<?= $data['mail_address'] ?>
                </a>
            </div>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} Recruitment ITH18. All rights reserved.</p>
            <p>Đây là email tự động, vui lòng không phản hồi.</p>
        </div>
    </div>
</body>
</html>
