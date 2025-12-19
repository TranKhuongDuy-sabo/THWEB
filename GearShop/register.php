<?php
ob_start(); // Tránh lỗi header
session_start();
include 'includes/db.php'; // Đảm bảo file kết nối db đúng đường dẫn

if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']); 
    $email    = trim($_POST['email']);
    $fullname = trim($_POST['fullname']);
    $phone    = trim($_POST['phone']);
    $address  = trim($_POST['address']);

    try {
        // 1. Kiểm tra tài khoản đã tồn tại chưa
        // Lưu ý: Nếu tên bảng của bạn là 'Users' (viết hoa), hãy sửa lại dòng dưới
        $stmt = $conn->prepare("SELECT UserID FROM users WHERE Username = ?");
        $stmt->execute([$username]);
        
        if ($stmt->rowCount() > 0) {
            echo "<script>alert('Tên đăng nhập này đã có người sử dụng! Vui lòng chọn tên khác.');</script>";
        } else {
            // 2. Thêm mới user vào Database
            // Mình giữ nguyên logic set Role là 'CUSTOMER' để đảm bảo phân quyền đúng
            $sql = "INSERT INTO users (Username, Password, Email, FullName, Phone, Address, Role) 
                    VALUES (?, ?, ?, ?, ?, ?, 'CUSTOMER')";
            $stmt = $conn->prepare($sql);
            
            if ($stmt->execute([$username, $password, $email, $fullname, $phone, $address])) {
                echo "<script>alert('Đăng ký thành công! Mời bạn đăng nhập.'); window.location='login.php';</script>";
            } else {
                echo "<script>alert('Có lỗi xảy ra, vui lòng thử lại');</script>";
            }
        }
    } catch (PDOException $e) {
        // Hiện lỗi chi tiết vì đang chạy Localhost
        echo "<script>alert('Lỗi hệ thống: " . $e->getMessage() . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký - GearShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        /* CSS Y HỆT MẪU BẠN GỬI */
        body {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 0;
            font-family: Arial, sans-serif;
        }
        .register-card {
            width: 100%;
            max-width: 500px;
            border-radius: 15px;
            border: none;
        }
        .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
            border-color: #0d6efd;
        }
    </style>
</head>
<body>

    <div class="card register-card shadow p-4 bg-white">
        <h3 class="text-center text-primary fw-bold mb-4">ĐĂNG KÝ TÀI KHOẢN</h3>
        
        <form method="POST">
            <div class="mb-3">
                <label class="form-label small fw-bold text-secondary">Tên đăng nhập (*)</label>
                <input type="text" name="username" class="form-control" placeholder="Ví dụ: nguyenvanan" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label small fw-bold text-secondary">Mật khẩu (*)</label>
                <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu..." required>
            </div>
            
            <div class="mb-3">
                <label class="form-label small fw-bold text-secondary">Họ và tên (*)</label>
                <input type="text" name="fullname" class="form-control" placeholder="Nhập họ tên đầy đủ" required>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-secondary">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="abc@gmail.com">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-secondary">Số điện thoại</label>
                    <input type="text" name="phone" class="form-control" placeholder="09xxxxxxx">
                </div>
            </div>
            
            <div class="mb-4">
                <label class="form-label small fw-bold text-secondary">Địa chỉ giao hàng</label>
                <textarea name="address" class="form-control" rows="2" placeholder="Số nhà, tên đường, phường/xã..."></textarea>
            </div>
            
            <button type="submit" name="register" class="btn btn-primary w-100 py-2 fw-bold rounded-pill shadow-sm">
                ĐĂNG KÝ NGAY
            </button>
        </form>
        
        <div class="text-center mt-4">
            <span class="text-muted small">Đã có tài khoản?</span>
            <a href="login.php" class="text-decoration-none fw-bold">Đăng nhập ngay</a>
            <br>
            <hr class="my-3 text-muted">
            <a href="index.php" class="text-decoration-none text-secondary small d-inline-flex align-items-center">
                <i class="bi bi-arrow-left me-1"></i> Quay về trang chủ
            </a>
        </div>
    </div>

</body>
</html>