# Hệ Thống Đặt Vé Xem Phim, Đặt Bàn Nhà Hàng và Mua Sắm Trực Tuyến

[![Laravel](https://img.shields.io/badge/Laravel-12.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://www.php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-orange.svg)](https://www.mysql.com)
[![GitHub Actions](https://img.shields.io/badge/CI-GitHub_Actions-success.svg)](https://github.com/features/actions)

## Giới Thiệu

Đây là đồ án môn **Phần Mềm Nguồn Mở Nâng Cao**, được phát triển bằng **Laravel Framework**.

Hệ thống cung cấp một nền tảng tích hợp cho phép người dùng:

- Đặt vé xem phim trực tuyến
- Đặt bàn nhà hàng
- Mua sắm sản phẩm
- Thanh toán trực tuyến qua VNPay
- Quản lý hồ sơ và lịch sử giao dịch

---

## Tính Năng Chính

### Người dùng

- Đăng ký / Đăng nhập
- Quản lý thông tin cá nhân
- Xem lịch sử đơn hàng
- Thanh toán trực tuyến

### Đặt Vé Xem Phim

- Xem danh sách phim
- Xem lịch chiếu
- Chọn ghế ngồi
- Đặt vé
- Thanh toán vé

### Đặt Bàn Nhà Hàng

- Xem danh sách nhà hàng
- Đặt bàn trực tuyến
- Chọn thời gian đặt bàn
- Thanh toán đặt chỗ

### Mua Sắm

- Xem sản phẩm
- Thêm vào giỏ hàng
- Quản lý giỏ hàng
- Thanh toán đơn hàng

### Quản Trị Hệ Thống

- Quản lý người dùng
- Quản lý phim
- Quản lý suất chiếu
- Quản lý ghế
- Quản lý nhà hàng
- Quản lý đơn hàng

---


## Công Nghệ Sử Dụng

| Thành phần | Công nghệ |
|------------|------------|
| Backend | Laravel 12 |
| Ngôn ngữ | PHP 8.2 |
| Database | MySQL |
| ORM | Eloquent ORM |
| Frontend | Blade, Bootstrap, JavaScript |
| Thanh toán | VNPay |
| Version Control | Git, GitHub |
| CI | GitHub Actions |

---

## 🚀 Cài Đặt Dự Án

### 1. Clone Repository

```bash
git clone https://github.com/<your-repository>.git
cd doanphanmemnguonmonangcao
```

### 2. Cài Đặt Thư Viện

```bash
composer install
```

### 3. Tạo File Môi Trường

```bash
cp .env.example .env
```

### 4. Sinh APP_KEY

```bash
php artisan key:generate
```

### 5. Cấu Hình Database

Mở file `.env`

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=doanpmnm
DB_USERNAME=root
DB_PASSWORD=
```

### 6. Chạy Migration & Seeder

```bash
php artisan migrate:fresh --seed
```

### 7. Khởi Động Dự Án

```bash
php artisan serve
```

Truy cập:

```text
http://127.0.0.1:8000
```

---

## CI - Continuous Integration

Dự án sử dụng **GitHub Actions** để tự động kiểm tra mã nguồn.

Workflow được kích hoạt khi:

- Push code lên repository
- Tạo Pull Request vào nhánh chính

Các bước kiểm tra:

- Composer Install
- Generate Application Key
- Laravel Bootstrap Check
- Route Validation

Workflow:

```text
.github/workflows/laravel-ci.yml
```

---

## Quy Trình Làm Việc Nhóm

### Tạo Branch Mới

```bash
git checkout main
git pull origin main
git checkout -b feature/ten-chuc-nang
```

### Commit Code

```bash
git add .
git commit -m "feat: thêm chức năng ..."
```

### Push Code

```bash
git push origin feature/ten-chuc-nang
```

### Tạo Pull Request

```text
feature/ten-chuc-nang → main
```

CI phải pass trước khi merge.

---

## Database

Một số bảng chính trong hệ thống:

- users
- movies
- showtimes
- seats
- bookings
- payments
- restaurants
- restaurant_bookings
- orders
- order_items

---

## Hướng Phát Triển

- [ ] Hoàn thiện bảo mật thanh toán VNPay
- [ ] Tích hợp gửi email xác nhận
- [ ] Tích hợp thông báo thời gian thực
- [ ] Viết Unit Test
- [ ] Viết API Documentation
- [ ] Docker hóa hệ thống

