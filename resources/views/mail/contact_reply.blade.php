<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $mailLog->subject }}</title>
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
            background: linear-gradient(135deg, #0b5cff 0%, #0040cc 100%);
            padding: 36px 20px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 22px;
            font-weight: 600;
        }
        .header p {
            color: rgba(255,255,255,0.85);
            margin: 8px 0 0;
            font-size: 14px;
        }
        .content {
            padding: 40px 36px;
        }
        .reply-body {
            font-size: 15px;
            line-height: 1.8;
            color: #444;
        }
        .divider {
            height: 1px;
            background-color: #eeeeee;
            margin: 28px 0;
        }
        .footer {
            background-color: #f8fafc;
            padding: 20px 36px;
            text-align: center;
            font-size: 13px;
            color: #888888;
            border-top: 1px solid #eeeeee;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📩 Phản hồi từ {{ config('app.name') }}</h1>
            <p>{{ config('app.name') }} — Hệ thống tuyển dụng</p>
        </div>

        <div class="content">
            <div class="reply-body">
                {!! $mailLog->body !!}
            </div>

            <div class="divider"></div>

            <p style="font-size: 13px; color: #888;">
                Email này được gửi để phản hồi tin nhắn bạn đã gửi đến chúng tôi.
                Nếu bạn có thắc mắc thêm, vui lòng liên hệ lại qua website của chúng tôi.
            </p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
