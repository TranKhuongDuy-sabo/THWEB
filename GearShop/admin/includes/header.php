<?php
// 1. BẮT BUỘC PHẢI CÓ SESSION START Ở ĐẦU MỌI TRANG
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. CHECK BẢO MẬT: Chưa đăng nhập Admin thì đá về login.php ngay lập tức
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}
?>

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Lấy tên file hiện tại để Active Menu
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GearShop Administrator</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f3f4f6;
            overflow-x: hidden;
        }

        /* --- 1. CẤU TRÚC FLEXBOX (CHÌA KHÓA ĐỂ KHÔNG BỊ BỂ) --- */
        #wrapper {
            display: flex;
            width: 100%;
            align-items: stretch;
        }

        /* --- 2. SIDEBAR (THANH MENU TRÁI) --- */
        #sidebar {
            min-width: 260px;
            max-width: 260px;
            background: #111827;
            /* Màu đen sang trọng */
            color: #fff;
            transition: all 0.3s;
            min-height: 100vh;
        }

        #sidebar.active {
            margin-left: -260px;
            /* Ẩn đi khi toggle */
        }

        .sidebar-header {
            padding: 20px;
            background: #1f2937;
            border-bottom: 1px solid #374151;
        }

        .sidebar-brand {
            font-size: 1.5rem;
            font-weight: 800;
            color: #3b82f6;
            /* Màu xanh nổi bật */
            text-transform: uppercase;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        ul.components {
            padding: 20px 0;
            list-style: none;
            /* Bỏ dấu chấm tròn */
            margin: 0;
            /* Bỏ margin thừa */
        }

        ul.components li {
            padding: 0;
        }

        ul.components li a {
            padding: 15px 25px;
            font-size: 1.1em;
            display: block;
            color: #9ca3af;
            text-decoration: none;
            transition: 0.3s;
            display: flex;
            align-items: center;
            gap: 12px;
            border-left: 4px solid transparent;
        }

        ul.components li a:hover {
            color: #fff;
            background: #374151;
        }

        ul.components li a.active {
            color: #fff;
            background: #1f2937;
            border-left: 4px solid #3b82f6;
            /* Vạch xanh active */
        }

        /* --- 3. CONTENT (NỘI DUNG BÊN PHẢI) --- */
        #content {
            width: 100%;
            min-height: 100vh;
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
        }

        /* Navbar trên cùng */
        .navbar-custom {
            padding: 15px 30px;
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-toggle {
            background: #3b82f6;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        /* --- 4. AREA NỘI DUNG CHÍNH --- */
        .main-body {
            padding: 30px;
            /* Khoảng cách nội dung */
            flex: 1;
        }
    </style>
</head>

<body>

    <div id="wrapper">

        <nav id="sidebar">
            <div class="sidebar-header">
                <a href="index.php" class="sidebar-brand">
                    <i class="bi bi-cpu-fill"></i> GEAR ADMIN
                </a>
            </div>

            <ul class="components">
                <li>
                    <a href="index.php" class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
                        <i class="bi bi-grid-fill"></i> Dashboard
                    </a>
                </li>

                <li>
                    <a href="product.php" class="<?php echo ($current_page == 'product.php' || $current_page == 'product_add.php') ? 'active' : ''; ?>">
                        <i class="bi bi-box-seam-fill"></i> Sản Phẩm
                    </a>
                </li>

                <li>
                    <a href="categories.php" class="<?php echo ($current_page == 'categories.php' || $current_page == 'categories_add.php' ||  $current_page == 'categories_edit.php') ? 'active' : ''; ?>">
                        <i class="bi bi-tags-fill"></i> Danh Mục
                    </a>
                </li>

                <li>
                    <a href="brands.php" class="<?php echo ($current_page == 'brands.php' || $current_page == 'brands_add.php' ||  $current_page == 'brands_edit.php') ? 'active' : ''; ?>">
                        <i class="bi bi-award-fill"></i> Thương Hiệu
                    </a>
                </li>

                <li>
                    <a href="orders.php" class="<?php echo ($current_page == 'orders.php') ? 'active' : ''; ?>">
                        <i class="bi bi-cart-check-fill"></i> Đơn Hàng
                    </a>
                </li>

                <li>
                    <a href="users.php" class="<?php echo ($current_page == 'users.php') ? 'active' : ''; ?>">
                        <i class="bi bi-people-fill"></i> Tài Khoản
                    </a>
                </li>
            </ul>
        </nav>

        <div id="content">

            <div class="navbar-custom">
                <button type="button" id="sidebarCollapse" class="btn-toggle">
                    <i class="bi bi-list"></i>
                </button>

                <div class="d-flex align-items-center gap-3">
                    <a href="../index.php" target="_blank" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                        <i class="bi bi-eye"></i> Xem Website
                    </a>

                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">

                            <?php
                            // Lấy tên từ Session (Nếu không có thì để mặc định là Admin)
                            $display_name = isset($_SESSION['fullname']) ? $_SESSION['fullname'] : 'Administrator';
                            // Tạo link ảnh avatar theo tên (dùng urlencode để xử lý khoảng trắng)
                            $avatar_url = "https://ui-avatars.com/api/?name=" . urlencode($display_name) . "&background=0D8ABC&color=fff";
                            ?>
                            <img src="<?php echo $avatar_url; ?>" alt="" width="35" height="35" class="rounded-circle me-2">

                            <span class="fw-bold"><?php echo $display_name; ?></span>
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="dropdownUser1">
                            <li>
                                <div class="dropdown-header text-muted small">Xin chào, Sếp!</div>
                            </li>
                            <li><a class="dropdown-item" href="../profile"><i class="bi bi-person me-2"></i>Hồ sơ cá nhân</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item text-danger fw-bold" href="../logout.php"><i class="bi bi-box-arrow-right me-2"></i>Đăng xuất</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="main-body">