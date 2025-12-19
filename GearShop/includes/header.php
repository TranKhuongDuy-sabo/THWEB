<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GearShop - Thế Giới Đồ Công Nghệ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .navbar-custom {
            background-color: #1e293b;
        }

        /* --- 1. HIỆU ỨNG MENU CHÍNH (Gạch chân từ giữa) --- */
        .navbar-nav .nav-link {
            position: relative;
            transition: color 0.3s ease;
            padding-bottom: 5px;
            /* Tạo khoảng trống cho gạch chân */
        }

        .navbar-nav .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 50%;
            background-color: #0d6efd;
            /* Màu xanh (Primary) hoặc màu bạn thích */
            transition: all 0.3s ease-in-out;
            transform: translateX(-50%);
            /* Căn giữa */
        }

        .navbar-nav .nav-link:hover::after {
            width: 100%;
            /* Khi hover thì gạch chân dài ra 100% */
        }

        .navbar-nav .nav-link:hover {
            color: #fff !important;
            /* Làm sáng chữ khi hover */
            text-shadow: 0 0 5px rgba(255, 255, 255, 0.5);
        }

        /* --- 2. HIỆU ỨNG NÚT BẤM (Phóng to nhẹ) --- */
        .btn {
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            /* Nhấc nút lên 1 chút */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            /* Thêm bóng đổ */
        }

        /* --- 3. HIỆU ỨNG DROPDOWN MENU (Trượt sang phải) --- */
        .dropdown-item {
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
            /* Chuẩn bị viền trái */
        }

        .dropdown-item:hover {
            background-color: #f1f5f9;
            /* Màu nền nhẹ khi hover */
            padding-left: 1.5rem;
            /* Đẩy chữ sang phải */
            border-left: 3px solid #0d6efd;
            /* Hiện viền trái màu xanh */
            color: #0d6efd;
            /* Đổi màu chữ */
        }

        /* --- GIỮ NGUYÊN CSS CŨ CỦA BẠN --- */
        .product-card {
            transition: transform 0.2s;
            border-radius: 15px;
            overflow: hidden;
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .price-tag {
            color: #dc3545;
            font-weight: bold;
            font-size: 1.2rem;
        }
    </style>
    </styl>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top py-3">
        <div class="container">
            <a class="navbar-brand fw-bold text-uppercase" href="index.php">
                <i class="bi bi-gear-wide-connected text-primary"></i> GearShop
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link active" href="index.php">Trang Chủ</a></li>
                    <li class="nav-item"><a class="nav-link active" href="../listProducts.php">Sản Phẩm</a></li>
                    <li class="nav-item"><a class="nav-link active" href="../contact.php">Liên Hệ</a></li>
                </ul>

                <div class="d-flex align-items-center gap-3">
                    <a href="cart.php" class="btn btn-outline-light position-relative rounded-pill border-0">
                        <i class="bi bi-cart3 fs-5"></i>
                    </a>

                    <?php if (isset($_SESSION['user_id'])): ?>

                        <div class="dropdown">
                            <button class="btn btn-outline-light dropdown-toggle rounded-pill btn-sm fw-bold px-3" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle me-1"></i> Xin chào, <?php echo isset($_SESSION['fullname']) ? $_SESSION['fullname'] : $_SESSION['username']; ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow animate slideIn">
                                <li><a class="dropdown-item" href="../profile.php"><i class="bi bi-person me-2"></i>Hồ sơ cá nhân</a></li>
                                <li><a class="dropdown-item" href="../my_orders.php"><i class="bi bi-bag-check me-2"></i>Đơn mua của tôi</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item text-danger fw-bold" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Đăng xuất</a></li>
                            </ul>
                        </div>

                    <?php else: ?>

                        <a href="login.php" class="btn btn-outline-light rounded-pill btn-sm fw-bold px-3">Đăng nhập</a>
                        <a href="register.php" class="btn btn-light rounded-pill btn-sm fw-bold px-3 text-primary ms-2">Đăng ký</a>

                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>