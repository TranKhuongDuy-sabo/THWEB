<?php
include 'includes/db.php';
// --- XỬ LÝ THÊM VÀO GIỎ HÀNG (Mới thêm) ---
session_start();
if (isset($_POST['add_to_cart_quick'])) {
    $id = $_POST['product_id'];
    $quantity = 1; // Mặc định mua 1 cái

    // Lấy thông tin sản phẩm từ DB để cho an toàn (không lấy từ form HTML)
    $stmt = $conn->prepare("SELECT * FROM Products WHERE ProductID = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();

    if ($product) {
        // Nếu giỏ hàng chưa có gì thì tạo mới
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Nếu sản phẩm đã có trong giỏ -> Tăng số lượng
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity'] += $quantity;
        } else {
            // Nếu chưa có -> Thêm mới vào
            $_SESSION['cart'][$id] = [
                'name' => $product['ProductName'],
                'price' => $product['Price'],
                'image' => $product['Image'],
                'quantity' => $quantity
            ];
        }

        // Thông báo nhẹ (hoặc có thể bỏ qua để trải nghiệm mượt hơn)
        echo "<script>alert('Đã thêm " . $product['ProductName'] . " vào giỏ!');</script>";
    }
}
include 'includes/header.php';

// LẤY 8 SẢN PHẨM MỚI NHẤT
$sql = "SELECT * FROM Products ORDER BY ProductID DESC LIMIT 8";
$stmt = $conn->query($sql);
$products = $stmt->fetchAll();
?>

<div id="bannerCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="https://images.unsplash.com/photo-1593640408182-31c70c8268f5?q=80&w=1200&h=400&fit=crop" class="d-block w-100" style="height: 400px; object-fit: cover;" alt="Banner">
            <div class="carousel-caption d-none d-md-block">
                <h2 class="fw-bold display-4 shadow-sm">Siêu Sale Cuối Năm</h2>
                <p class="fs-4">Giảm giá lên đến 50% cho Laptop Gaming</p>
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-uppercase border-start border-5 border-primary ps-3 text-dark">Sản Phẩm Mới</h3>
        <a href="./listProducts.php" class="btn btn-outline-primary rounded-pill">Xem tất cả <i class="bi bi-arrow-right"></i></a>
    </div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
        <?php foreach ($products as $p): ?>
            <div class="col">
                <div class="card product-card h-100 bg-white position-relative">

                    <div class="position-relative">
                        <?php if (!empty($p['Image'])): ?>
                            <img src="uploads/<?php echo $p['Image']; ?>" class="card-img-top p-3" alt="<?php echo $p['ProductName']; ?>" style="height: 250px; object-fit: contain;">
                        <?php else: ?>
                            <div class="d-flex justify-content-center align-items-center bg-light" style="height: 250px;">
                                <i class="bi bi-image text-secondary fs-1"></i>
                            </div>
                        <?php endif; ?>
                        <span class="position-absolute top-0 start-0 bg-danger text-white badge m-3 py-2 px-3 rounded-pill shadow-sm">-10%</span>
                    </div>

                    <div class="card-body d-flex flex-column">
                        <div class="small text-muted mb-1">
                            <?php
                            $brand = $conn->query("SELECT BrandName FROM Brands WHERE BrandID=" . $p['BrandID'])->fetchColumn();
                            echo $brand ?? 'Chính hãng';
                            ?>
                        </div>

                        <h5 class="card-title fw-bold text-dark text-truncate" title="<?php echo $p['ProductName']; ?>">
                            <a href="product_detail.php?id=<?php echo $p['ProductID']; ?>" class="text-decoration-none text-dark stretched-link">
                                <?php echo $p['ProductName']; ?>
                            </a>
                        </h5>

                        <div class="mt-auto pt-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="price-tag"><?php echo number_format($p['Price'], 0, ',', '.'); ?>đ</span>
                                <span class="text-decoration-line-through text-muted small">
                                    <?php echo number_format($p['Price'] * 1.1, 0, ',', '.'); ?>đ
                                </span>
                            </div>

                            <form method="POST" action="" style="z-index: 2; position: relative;">
                                <input type="hidden" name="product_id" value="<?php echo $p['ProductID']; ?>">

                                <button type="submit" name="add_to_cart_quick" class="btn btn-primary w-100 mt-3 rounded-pill fw-bold shadow-sm">
                                    <i class="bi bi-cart-plus me-1"></i> Thêm vào giỏ
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="bg-white py-5 mt-5">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-3 mb-3"><i class="bi bi-truck text-primary fs-1"></i>
                <h6 class="fw-bold mt-2">Giao Hàng Toàn Quốc</h6>
            </div>
            <div class="col-md-3 mb-3"><i class="bi bi-shield-check text-success fs-1"></i>
                <h6 class="fw-bold mt-2">Bảo Hành Chính Hãng</h6>
            </div>
            <div class="col-md-3 mb-3"><i class="bi bi-arrow-counterclockwise text-warning fs-1"></i>
                <h6 class="fw-bold mt-2">Đổi Trả 30 Ngày</h6>
            </div>
            <div class="col-md-3 mb-3"><i class="bi bi-headset text-danger fs-1"></i>
                <h6 class="fw-bold mt-2">Hỗ Trợ 24/7</h6>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>