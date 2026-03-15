# Recruitment API

Hệ thống API backend mạnh mẽ phục vụ cho nền tảng Tuyển dụng, kết nối giữa Ứng viên (Applicant), Công ty tuyển dụng (Company) và Quản trị viên (Admin).

Dự án được xây dựng dựa trên kiến trúc Service-Repository-Controller Pattern để đảm bảo tính mở rộng, bảo trì dễ dàng và khả năng đáp ứng cao.

---

## 🌟 Chức Năng Chính

Hệ thống được thiết kế với việc phân quyền rõ ràng qua 3 vai trò (Roles) chính:

### 1. Quản Trị Viên (Admin)

- Quản lý toàn bộ danh mục hệ thống (Master Data): Thành phố (Cities), Ngành nghề (Job Categories).
- Quản lý và kiểm duyệt tài khoản Công ty (Company) và Ứng viên (Applicant).
- Theo dõi và thống kê tổng quan hệ thống qua Dashboard.
- Quản trị tất cả tin tuyển dụng (Jobs).

### 2. Công Ty Tuyển Dụng (Company)

- Đăng nhập, đăng ký và quản lý hồ sơ nhà tuyển dụng.
- Đăng tải, cập nhật và quản lý tin tuyển dụng (Jobs).
- Tiếp nhận, theo dõi và xử lý hồ sơ (Job Applications) từ ứng viên.

### 3. Ứng Viên (Applicant)

- Đăng nhập, đăng ký và quản lý hồ sơ cá nhân.
- Quản lý CV và các tài liệu đính kèm (File Upload).
- Tìm kiếm việc làm, xem chi tiết tin tuyển dụng.
- Lưu việc làm yêu thích và nộp hồ sơ ứng tuyển.

---

## 🚀 Công Nghệ Sử Dụng

Dự án sử dụng các công nghệ và thư viện hiện đại nhất trong hệ sinh thái PHP:

- **Framework Core**: Laravel 11.9
- **Ngôn ngữ**: PHP 8.2+
- **Authentication**: JWT Auth (`tymon/jwt-auth`) - Quản lý token xác thực cho các Roles độc lập.
- **Tài liệu API**: L5-Swagger (`darkaonline/l5-swagger`) - Tự động sinh tài liệu chuẩn OpenAPI.
- **Lưu trữ tệp (Storage)**: AWS S3 (`league/flysystem-aws-s3-v3`) - Upload CV và hình ảnh an toàn.
- **Code Format/Linting**: `friendsofphp/php-cs-fixer` & `squizlabs/php_codesniffer` - Đảm bảo tuân thủ tiêu chuẩn PSR-12.
- **Testing**: PHPUnit (`phpunit/phpunit`) & Mockery.

---

## 📂 Kiến Trúc Thư Mục Nổi Bật

Dự án tuân theo chuẩn **Service Pattern**, giúp Controller luôn mỏng và dễ test:

- `app/Http/Controllers`: Chỉ nhận Request, gọi Service và trả về Response (thông qua `ResponseHelper`).
- `app/Http/Requests`: Nơi chứa toàn bộ FormRequests, phân cấp rõ ràng theo từng Role.
- `app/Services`: Nơi xử lý toàn bộ logic nghiệp vụ (Business Logic). Kế thừa `BaseService` để tận dụng tính năng build Query động (filter/search/sort/paginate).
- `app/Models`: Chứa định nghĩa DB, Schema relations và các Constants.
- `routes/api.php`: Cấu trúc routes phân tách rõ ràng theo các middleware auth guard.

---

## 🛠 Hướng Dẫn Cài Đặt (Setup Môi Trường)

Làm theo các bước sau để khởi chạy dự án trên máy tính cá nhân (Local):

### Bước 1: Clone dự án và cài đặt thư viện

```bash
git clone <repository_url>
cd recruitment-api
composer install
```

### Bước 2: Cấu hình môi trường (Environment)

Sao chép file `.env.example` thành `.env` và cập nhật các thông số kết nối Database, cấu hình JWT, S3:

```bash
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
```

### Bước 3: migrate database & seed (Nếu có)

```bash
php artisan migrate
```

### Bước 4: Chạy server ảo của Laravel

```bash
php artisan serve
```

---

## 📚 Xem Tài Liệu API (Swagger Docs)

Sau khi chạy server, bạn có thể thiết lập tài liệu API.
Trước tiên, hãy chắc chắn render file JSON/YAML của Swagger bằng lệnh:

```bash
php artisan l5-swagger:generate
```

Theo mặc định, tài liệu sẽ có thể truy cập qua trình duyệt ở đường dẫn:
👉 `http://localhost:8000/api/documentation`

---
