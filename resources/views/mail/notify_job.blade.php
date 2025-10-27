<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Thông báo ứng tuyển - {{ $jobData->title }}</title>
    <style>
        .container {
            max-width: 90%;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        }

        .header {
            background: #004aad;
            color: #fff;
            padding: 10px 20px;
            font-size: 20px;
            font-weight: bold;
        }

        .content {
            padding: 10px 20px;
            color: #333;
        }

        .job-title {
            font-size: 18px;
            font-weight: 600;
            color: #004aad;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 10px 8px;
            text-align: center;
            border-bottom: 1px solid #e9e9e9;
        }

        th {
            background-color: #f0f4ff;
            font-weight: 600;
            color: #333;
        }

        a {
            color: #004aad;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .footer {
            background: #f7f7f7;
            padding: 15px 30px;
            font-size: 13px;
            color: #666;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">📨 Thông báo ứng tuyển mới</div>
        <div class="content">
            <p>Kính gửi <strong>{{ $jobData->company?->name }}</strong>,</p>
            <p>Trong thời gian vừa qua, quý công ty đã nhận được các hồ sơ ứng tuyển cho vị trí:</p>
            <div class="job-title">{{ $jobData->title }}</div>
            <table>
                <thead>
                    <tr>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>Số điện thoại</th>
                        <th>Thời gian ứng tuyển</th>
                        <th>File đính kèm</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($jobApplications as $item)
                        <tr>
                            <td>{{ $item['name'] }}</td>
                            <td>{{ $item['email'] }}</td>
                            <td>{{ $item['telephone'] }}</td>
                            <td>{{ $item['created_at'] }}</td>
                            <td><a href="{{ $item['file_path'] }}" target="_blank">Xem</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <p style="margin-top: 20px;">Xin vui lòng đăng nhập hệ thống để xem chi tiết và phản hồi ứng viên.</p>
        </div>
        <div class="footer">
            Đây là email tự động, vui lòng không phản hồi.
            © {{ date('Y') }} {{ config('app.name') }}.
        </div>
    </div>
</body>

</html>
