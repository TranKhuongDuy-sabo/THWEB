<?php
ob_start(); // QUAN TRỌNG: Giúp tránh lỗi Header trên Localhost/WAMP
session_start();

// Gọi file kết nối database
// Đảm bảo bạn đã có file includes/db.php với cấu hình: localhost, root, (rỗng)
include 'includes/db.php'; 

// --- 1. KIỂM TRA NẾU ĐÃ ĐĂNG NHẬP RỒI ---
if (isset($_SESSION['user_id'])) {
    if (isset($_SESSION['role']) && $_SESSION['role'] == 'ADMIN') {
        header("Location: admin/index.php");
    } else {
        header("Location: index.php");
    }
    exit();
}

// --- 2. XỬ LÝ KHI BẤM NÚT ĐĂNG NHẬP ---
if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    try {
        // Lệnh SQL chuẩn
        // LƯU Ý: Nếu chạy không được, hãy kiểm tra tên bảng trong Database là 'users' hay 'Users'
        $sql = "SELECT * FROM users WHERE Username = ? AND Password = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$username, $password]);
        $user = $stmt->fetch();

        if ($user) {
            // -- Đăng nhập thành công --
            $_SESSION['user_id'] = $user['UserID'];
            $_SESSION['username'] = $user['Username'];
            $_SESSION['fullname'] = $user['FullName'];
            $_SESSION['role']     = $user['Role'];

            if ($user['Role'] == 'ADMIN') {
                $_SESSION['admin_logged_in'] = true;
                header("Location: admin/index.php"); 
            } else {
                header("Location: index.php"); 
            }
            exit();
        } else {
            $error = "Tài khoản hoặc mật khẩu không chính xác!";
        }
    } catch (PDOException $e) {
        // Hiện lỗi rõ ràng để dễ sửa trên Localhost
        $error = "Lỗi hệ thống: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập - GearShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* GIỮ NGUYÊN CSS GỐC */
        body {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            height: 100vh;
        }
        .login-card {
            width: 400px;
            border-radius: 15px;
            border: none;
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center">
    
    <div class="card login-card shadow p-4 bg-white">
        <h3 class="text-center text-primary fw-bold mb-4">ĐĂNG NHẬP</h3>
        
        <?php if(isset($error)): ?>
            <div class='alert alert-danger text-center'><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="mb-3">
                <label class="form-label fw-bold">Tên đăng nhập</label>
                <input type="text" name="username" class="form-control" placeholder="Nhập username..." required>
            </div>
            
            <div class="mb-3">
                <label class="form-label fw-bold">Mật khẩu</label>
                <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu..." required>
            </div>
            
            <button type="submit" name="login" class="btn btn-primary w-100 py-2 fw-bold rounded-pill">
                Đăng Nhập
            </button>
        </form>
        
        <div class="text-center mt-4">
            <span class="text-muted">Chưa có tài khoản?</span>
            <a href="register.php" class="text-decoration-none fw-bold">Đăng ký ngay</a>
            <br>
            <a href="index.php" class="text-decoration-none text-secondary small mt-2 d-inline-block">
                <i class="bi bi-arrow-left"></i> Quay về trang chủ
            </a>
        </div>
    </div>

</body>
</html>