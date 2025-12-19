<?php
// 1. Start Session (Dùng lệnh kiểm tra để tránh lỗi nếu header đã start rồi)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'includes/db.php';

// --- PHẦN XỬ LÝ THÊM VÀO GIỎ HÀNG (LOGIC MỚI) ---
if (isset($_POST['add_to_cart'])) {
    $p_id = $_POST['product_id'];
    $qty = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

    // Lấy thông tin sản phẩm từ DB để đảm bảo chính xác
    $stmt = $conn->prepare("SELECT * FROM Products WHERE ProductID = ?");
    $stmt->execute([$p_id]);
    $prod = $stmt->fetch();

    if ($prod) {
        // Nếu giỏ hàng chưa tồn tại thì tạo mới
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // KIỂM TRA ĐỂ CỘNG DỒN SỐ LƯỢNG
        if (isset($_SESSION['cart'][$p_id])) {
            // Đây là chìa khóa: Dùng += để cộng thêm vào số cũ
            $_SESSION['cart'][$p_id]['quantity'] += $qty;
        } else {
            // Nếu chưa có thì thêm mới
            $_SESSION['cart'][$p_id] = [
                'name' => $prod['ProductName'],
                'price' => $prod['Price'],
                'image' => $prod['Image'],
                'quantity' => $qty
            ];
        }

        // Thông báo và load lại trang để tránh gửi lại form khi F5
        echo "<script>alert('Đã thêm sản phẩm vào giỏ!'); window.location.href='product_detail.php?id=$p_id';</script>";
        exit;
    }
}

// --- PHẦN LẤY THÔNG TIN SẢN PHẨM HIỂN THỊ ---
include 'includes/header.php'; // Header để sau khi xử lý logic (tránh lỗi header sent)

if (!isset($_GET['id'])) {
    echo "<script>alert('Không tìm thấy sản phẩm!'); window.location='index.php';</script>";
    exit;
}
$id = $_GET['id'];

// Lấy thông tin chi tiết
$sql = "SELECT p.*, c.CategoryName, b.BrandName 
        FROM Products p
        LEFT JOIN Categories c ON p.CategoryID = c.CategoryID
        LEFT JOIN Brands b ON p.BrandID = b.BrandID
        WHERE p.ProductID = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    echo "<script>alert('Sản phẩm không tồn tại!'); window.location='index.php';</script>";
    exit;
}
?>

<style>
    /* Tùy chỉnh input số lượng */
    .quantity-input {
        max-width: 80px;
        text-align: center;
        font-weight: bold;
        border: 2px solid #eee;
    }

    .quantity-input:focus {
        border-color: #000;
        box-shadow: none;
    }

    /* Nút mua hàng */
    .btn-buy {
        background-color: #0d6efd; /* Đổi sang màu xanh cho đồng bộ */
        color: #fff;
        border: none;
        transition: all 0.3s;
    }

    .btn-buy:hover {
        background-color: #0b5ed7;
        color: #fff;
        transform: translateY(-2px);
    }

    .service-item {
        font-size: 0.9rem;
        color: #666;
    }

    .service-item i {
        color: #28a745;
        margin-right: 8px;
        font-size: 1.1rem;
    }
</style>

<div class="container py-5">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small text-muted">
            <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-muted">Trang chủ</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo $product['ProductName']; ?></li>
        </ol>
    </nav>

    <div class="row g-5">
        <div class="col-lg-6">
            <div class="mb-3 text-center border rounded p-3">
                <?php if (!empty($product['Image'])): ?>
                    <img src="uploads/<?php echo $product['Image']; ?>" class="img-fluid" alt="<?php echo $product['ProductName']; ?>" style="max-height: 450px; object-fit: contain;">
                <?php else: ?>
                    <div class="d-flex justify-content-center align-items-center bg-light rounded" style="height: 400px;">
                        <i class="bi bi-image text-secondary fs-1"></i>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="text-muted small mb-2 fw-bold text-uppercase">
                <?php echo $product['BrandName'] ?? 'No Brand'; ?> / <?php echo $product['CategoryName'] ?? 'General'; ?>
            </div>

            <h1 class="fw-bold text-dark mb-3 display-6"><?php echo $product['ProductName']; ?></h1>

            <div class="fs-2 text-danger fw-bold mb-4">
                <?php echo number_format($product['Price'], 0, ',', '.'); ?>₫
            </div>

            <div class="text-secondary mb-5" style="white-space: pre-line; font-size: 1rem; line-height: 1.6;">
                <?php echo $product['Description']; ?>
            </div>

            <div class="mb-5 pb-5 border-bottom">
                <p class="mb-3 small">
                    Tình trạng:
                    <?php if ($product['Stock'] > 0): ?>
                        <span class="text-success fw-bold"><i class="bi bi-check-circle-fill"></i> Còn hàng</span>
                    <?php else: ?>
                        <span class="text-danger fw-bold"><i class="bi bi-x-circle-fill"></i> Hết hàng</span>
                    <?php endif; ?>
                </p>

                <form action="" method="POST" class="d-flex align-items-center gap-3">
                    
                    <input type="hidden" name="product_id" value="<?php echo $product['ProductID']; ?>">

                    <div class="d-flex align-items-center">
                        <label class="me-2 fw-bold small">Số lượng:</label>
                        <input type="number" name="quantity" class="form-control form-control-lg quantity-input" value="1" min="1" max="<?php echo $product['Stock']; ?>">
                    </div>

                    <button type="submit" name="add_to_cart" class="btn btn-primary btn-lg flex-grow-1 py-3 fw-bold rounded-pill btn-buy" <?php echo ($product['Stock'] <= 0) ? 'disabled' : ''; ?>>
                        <i class="bi bi-bag-plus-fill me-2"></i> THÊM VÀO GIỎ
                    </button>
                </form>
            </div>

            <div class="row row-cols-2 g-3">
                <div class="col service-item"><i class="bi bi-shield-check"></i> Hàng chính hãng 100%</div>
                <div class="col service-item"><i class="bi bi-arrow-counterclockwise"></i> Đổi trả trong 30 ngày</div>
                <div class="col service-item"><i class="bi bi-truck"></i> Miễn phí vận chuyển</div>
                <div class="col service-item"><i class="bi bi-patch-check"></i> Bảo hành 12 tháng</div>
            </div>

        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>