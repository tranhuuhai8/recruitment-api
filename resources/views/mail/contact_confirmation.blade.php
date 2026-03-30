<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận nhận tin nhắn — {{ config('app.name') }}</title>
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
            letter-spacing: 0.5px;
        }
        .header p {
            color: rgba(255,255,255,0.85);
            margin: 8px 0 0;
            font-size: 14px;
        }
        .content {
            padding: 40px 36px;
        }
        .content p {
            font-size: 15px;
            line-height: 1.7;
            margin-bottom: 16px;
            color: #555555;
        }
        .info-box {
            background-color: #f8faff;
            border: 1px solid #dde8ff;
            border-radius: 6px;
            padding: 20px 24px;
            margin: 24px 0;
        }
        .info-box .info-row {
            display: flex;
            margin-bottom: 10px;
            font-size: 14px;
        }
        .info-box .info-row:last-child {
            margin-bottom: 0;
        }
        .info-box .label {
            font-weight: 600;
            color: #333;
            min-width: 120px;
        }
        .info-box .value {
            color: #555;
            flex: 1;
        }
        .badge {
            display: inline-block;
            background-color: #e8f0fe;
            color: #0b5cff;
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
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
        .footer a {
            color: #0b5cff;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ Chúng tôi đã nhận được tin nhắn của bạn!</h1>
            <p>{{ config('app.name') }} — Hệ thống tuyển dụng</p>
        </div>

        <div class="content">
            <p>Chào <strong>{{ $contact->full_name }}</strong>,</p>

            <p>Cảm ơn bạn đã liên hệ với chúng tôi. Chúng tôi đã nhận được tin nhắn của bạn và sẽ phản hồi trong vòng <strong>2 giờ làm việc</strong>.</p>

            <div class="info-box">
                <div class="info-row">
                    <span class="label">Họ và tên:</span>
                    <span class="value">{{ $contact->full_name }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Email:</span>
                    <span class="value">{{ $contact->email }}</span>
                </div>
                @if($contact->phone)
                <div class="info-row">
                    <span class="label">Điện thoại:</span>
                    <span class="value">{{ $contact->phone }}</span>
                </div>
                @endif
                <div class="info-row">
                    <span class="label">Chủ đề:</span>
                    <span class="value">{{ $contact->title }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Thời gian:</span>
                    <span class="value">{{ $contact->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Trạng thái:</span>
                    <span class="value"><span class="badge">Đã tiếp nhận</span></span>
                </div>
            </div>

            <p>Trong thời gian chờ đợi, bạn có thể khám phá thêm các cơ hội việc làm trên nền tảng của chúng tôi.</p>

            <div class="divider"></div>

            <p style="font-size: 13px; color: #888;">Nếu bạn không thực hiện yêu cầu này, vui lòng bỏ qua email này.</p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <p>Đây là email tự động, vui lòng không phản hồi trực tiếp email này.</p>
        </div>
    </div>
</body>
</html>
