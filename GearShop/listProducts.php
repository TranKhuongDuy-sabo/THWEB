<?php
// Thêm ob_start để tránh lỗi header chuyển hướng
ob_start(); 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'includes/db.php';

// --- BƯỚC 1: XỬ LÝ THÊM VÀO GIỎ HÀNG ---
if (isset($_POST['add_to_cart_quick'])) {
    $id = $_POST['product_id'];
    
    // Kiểm tra sản phẩm (Lưu ý: Tên bảng là 'products' hay 'Products' tùy database của bạn)
    // Ở đây mình để 'products' (chữ thường) cho chuẩn, nếu lỗi hãy sửa thành 'Products'
    $stmt = $conn->prepare("SELECT * FROM products WHERE ProductID = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();

    if ($product) {
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
        
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity']++;
        } else {
            $_SESSION['cart'][$id] = [
                'name' => $product['ProductName'],
                'price' => $product['Price'],
                'image' => $product['Image'],
                'quantity' => 1
            ];
        }
        
        // [MỚI THÊM] 1. Lưu câu thông báo vào Session
        $_SESSION['cart_success_msg'] = "Đã thêm '" . $product['ProductName'] . "' vào giỏ hàng thành công!";

        // Load lại đúng trang hiện tại (giữ nguyên bộ lọc)
        header("Location: " . $_SERVER['REQUEST_URI']); 
        exit();
    }
}

include 'includes/header.php';

// --- [MỚI THÊM] 2. HIỂN THỊ THÔNG BÁO NẾU CÓ ---
if (isset($_SESSION['cart_success_msg'])) {
    echo "
    <div class='alert alert-success alert-dismissible fade show fixed-top m-3 shadow-lg' role='alert' style='z-index: 9999; left: auto; max-width: 400px;'>
        <i class='bi bi-check-circle-fill me-2'></i> " . $_SESSION['cart_success_msg'] . "
        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
    </div>
    <script>
        // Tự động ẩn sau 3 giây
        setTimeout(function() {
            var alert = document.querySelector('.alert');
            if(alert) {
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 3000);
    </script>
    ";
    // Xóa thông báo sau khi đã hiện (để F5 không hiện lại)
    unset($_SESSION['cart_success_msg']);
}

/// --- XỬ LÝ BỘ LỌC (FILTER) ---
$whereClause = "WHERE 1=1"; 
$params = [];

// 1. Lọc theo Danh mục
if (isset($_GET['category']) && $_GET['category'] != '') {
    $whereClause .= " AND CategoryID = ?";
    $params[] = $_GET['category'];
}

// 2. Lọc theo Thương hiệu
if (isset($_GET['brand']) && $_GET['brand'] != '') {
    $whereClause .= " AND BrandID = ?";
    $params[] = $_GET['brand'];
}

// 3. Lọc theo Tìm kiếm tên
if (isset($_GET['q']) && $_GET['q'] != '') {
    $whereClause .= " AND ProductName LIKE ?";
    $params[] = "%" . $_GET['q'] . "%";
}

// 4. Lọc theo Giá
if (isset($_GET['price']) && $_GET['price'] != '') {
    switch ($_GET['price']) {
        case 'under10':
            $whereClause .= " AND Price < 10000000";
            break;
        case '10to20':
            $whereClause .= " AND Price BETWEEN 10000000 AND 20000000";
            break;
        case 'over20':
            $whereClause .= " AND Price > 20000000";
            break;
    }
}

// 5. Xử lý Sắp xếp (Sort)
$orderClause = "ORDER BY ProductID DESC"; 
if (isset($_GET['sort'])) {
    switch ($_GET['sort']) {
        case 'price_asc':
            $orderClause = "ORDER BY Price ASC";
            break;
        case 'price_desc':
            $orderClause = "ORDER BY Price DESC";
            break;
        case 'name_asc':
            $orderClause = "ORDER BY ProductName ASC";
            break;
        case 'newest':
             $orderClause = "ORDER BY ProductID DESC";
             break;
    }
}

// --- THỰC HIỆN TRUY VẤN ---
// (Lưu ý: Tên bảng users, products... nên viết thường để thống nhất)
$sql = "SELECT * FROM products $whereClause $orderClause";
$stmt = $conn->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$categories = $conn->query("SELECT * FROM categories")->fetchAll();
$brands = $conn->query("SELECT * FROM brands")->fetchAll();
?>

<div class="container py-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">Trang chủ</a></li>
            <li class="breadcrumb-item active" aria-current="page">Sản phẩm</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-3 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-bold py-3">
                    <i class="bi bi-funnel"></i> BỘ LỌC TÌM KIẾM
                </div>
                <div class="card-body p-0">
                    <form action="" method="GET">
                        
                        <div class="p-3 border-bottom">
                            <h6 class="fw-bold mb-3">Danh mục</h6>
                            <?php foreach($categories as $c): ?>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="category" value="<?php echo $c['CategoryID']; ?>" 
                                    id="cat_<?php echo $c['CategoryID']; ?>"
                                    <?php if(isset($_GET['category']) && $_GET['category'] == $c['CategoryID']) echo 'checked'; ?>>
                                <label class="form-check-label" for="cat_<?php echo $c['CategoryID']; ?>">
                                    <?php echo $c['CategoryName']; ?>
                                </label>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="p-3 border-bottom">
                            <h6 class="fw-bold mb-3">Thương hiệu</h6>
                            <select class="form-select" name="brand">
                                <option value="">-- Tất cả --</option>
                                <?php foreach($brands as $b): ?>
                                <option value="<?php echo $b['BrandID']; ?>" 
                                    <?php if(isset($_GET['brand']) && $_GET['brand'] == $b['BrandID']) echo 'selected'; ?>>
                                    <?php echo $b['BrandName']; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="p-3 border-bottom">
                            <h6 class="fw-bold mb-3">Mức giá</h6>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="price" value="under10" id="p1" <?php if(isset($_GET['price']) && $_GET['price'] == 'under10') echo 'checked'; ?>>
                                <label class="form-check-label" for="p1">Dưới 10 triệu</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="price" value="10to20" id="p2" <?php if(isset($_GET['price']) && $_GET['price'] == '10to20') echo 'checked'; ?>>
                                <label class="form-check-label" for="p2">Từ 10 - 20 triệu</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="price" value="over20" id="p3" <?php if(isset($_GET['price']) && $_GET['price'] == 'over20') echo 'checked'; ?>>
                                <label class="form-check-label" for="p3">Trên 20 triệu</label>
                            </div>
                        </div>

                        <div class="p-3">
                            <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold">
                                <i class="bi bi-search"></i> Áp dụng bộ lọc
                            </button>
                            <a href="<?php echo basename($_SERVER['PHP_SELF']); ?>" class="btn btn-outline-secondary w-100 rounded-pill mt-2">Xóa bộ lọc</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded shadow-sm">
                <span class="fw-bold text-secondary">Tìm thấy <?php echo count($products); ?> sản phẩm</span>
                
                <form action="" method="GET" id="sortForm" class="d-flex align-items-center">
                    <?php 
                    // Giữ lại các tham số filter khi sort
                    foreach($_GET as $key => $val) {
                        if($key != 'sort') echo "<input type='hidden' name='$key' value='$val'>";
                    }
                    ?>
                    <label class="me-2 text-nowrap">Sắp xếp theo:</label>
                    <select class="form-select form-select-sm" name="sort" onchange="this.form.submit()">
                        <option value="newest">Mới nhất</option>
                        <option value="price_asc" <?php if(isset($_GET['sort']) && $_GET['sort'] == 'price_asc') echo 'selected'; ?>>Giá tăng dần</option>
                        <option value="price_desc" <?php if(isset($_GET['sort']) && $_GET['sort'] == 'price_desc') echo 'selected'; ?>>Giá giảm dần</option>
                        <option value="name_asc" <?php if(isset($_GET['sort']) && $_GET['sort'] == 'name_asc') echo 'selected'; ?>>Tên A-Z</option>
                    </select>
                </form>
            </div>

            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php if(count($products) > 0): ?>
                    <?php foreach($products as $p): ?>
                    <div class="col">
                        <div class="card product-card h-100 border-0 shadow-sm">
                            <div class="position-relative">
                                <?php if(!empty($p['Image'])): ?>
                                    <img src="uploads/<?php echo $p['Image']; ?>" class="card-img-top p-3" alt="<?php echo $p['ProductName']; ?>" style="height: 220px; object-fit: contain;">
                                <?php else: ?>
                                    <div class="d-flex justify-content-center align-items-center bg-light" style="height: 220px;">
                                        <i class="bi bi-image text-secondary fs-1"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="card-body d-flex flex-column">
                                <h6 class="card-title fw-bold text-dark text-truncate">
                                    <a href="product_detail.php?id=<?php echo $p['ProductID']; ?>" class="text-decoration-none text-dark stretched-link">
                                        <?php echo $p['ProductName']; ?>
                                    </a>
                                </h6>
                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-danger fw-bold fs-5"><?php echo number_format($p['Price'], 0, ',', '.'); ?>đ</span>
                                    </div>
                                    <form method="POST" action="" style="z-index: 2; position: relative;">
                                        <input type="hidden" name="product_id" value="<?php echo $p['ProductID']; ?>">
                                        <button type="submit" name="add_to_cart_quick" class="btn btn-outline-primary w-100 mt-2 rounded-pill btn-sm fw-bold">
                                            <i class="bi bi-cart-plus"></i> Thêm vào giỏ
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <img src="https://cdni.iconscout.com/illustration/premium/thumb/search-not-found-illustration-download-in-svg-png-gif-file-formats--zoom-glass-magnifier-data-user-interface-pack-design-development-illustrations-6430781.png" style="width: 200px;">
                        <p class="mt-3 text-muted">Rất tiếc, không tìm thấy sản phẩm nào phù hợp tiêu chí lọc.</p>
                        <a href="<?php echo basename($_SERVER['PHP_SELF']); ?>" class="btn btn-primary rounded-pill">Xem tất cả sản phẩm</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>