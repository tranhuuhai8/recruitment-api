<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Tuyển dụng mới - {{ $companyData['name'] }}</title>
    <style>
        .container {
            max-width: 680px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
            font-family: Arial, sans-serif;
        }

        .header {
            background: linear-gradient(120deg, #0f766e 0%, #14b8a6 45%, #0ea5e9 100%);
            color: #fff;
            padding: 16px 20px;
            font-size: 18px;
            font-weight: bold;
        }

        .content {
            padding: 18px 20px;
            color: #0f172a;
            line-height: 1.6;
        }

        .badge {
            display: inline-block;
            background: rgba(13, 148, 136, 0.12);
            color: #0f766e;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
        }

        .title {
            font-size: 18px;
            font-weight: 700;
            margin: 10px 0 6px;
        }

        .meta {
            color: #475569;
            font-size: 13px;
        }

        .btn {
            display: inline-block;
            margin-top: 14px;
            background: #0d9488;
            color: #fff !important;
            text-decoration: none;
            padding: 10px 14px;
            border-radius: 10px;
            font-weight: 700;
        }

        .footer {
            background: #f8fafc;
            padding: 14px 18px;
            font-size: 12px;
            color: #64748b;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">📣 Tuyển dụng mới từ công ty bạn theo dõi</div>
        <div class="content">
            <div class="badge">{{ $companyData['name'] }}</div>
            <div class="title">{{ $jobData['title'] }}</div>
            <div class="meta">
                Hạn nộp: <b>{{ $jobData['end_date'] }}</b>
            </div>
            <a class="btn" href="{{ $jobUrl }}" target="_blank">Xem chi tiết công việc</a>
            <p style="margin-top: 14px;">Bạn nhận email này vì đã theo dõi công ty <b>{{ $companyData['name'] }}</b>.</p>
        </div>
        <div class="footer">
            Đây là email tự động, vui lòng không phản hồi. © {{ date('Y') }} {{ config('app.name') }}.
        </div>
    </div>
</body>

</html>

