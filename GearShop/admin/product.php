<?php
// 1. Kết nối Database
include '../includes/db.php';

// --- XỬ LÝ XÓA SẢN PHẨM (LOGIC CHUẨN) ---
// Đặt lên ĐẦU TRANG để xử lý trước khi hiện HTML
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];

    try {
        // Bước 1: Xóa ràng buộc trong OrderDetails trước
        $stmtDetail = $conn->prepare("DELETE FROM OrderDetails WHERE ProductID = ?");
        $stmtDetail->execute([$id]);

        // Bước 2: Xóa file ảnh vật lý (nếu có)
        $stmtImg = $conn->prepare("SELECT Image FROM Products WHERE ProductID = ?");
        $stmtImg->execute([$id]);
        $product = $stmtImg->fetch();

        if ($product && !empty($product['Image'])) {
            $imgPath = "../uploads/" . $product['Image'];
            if (file_exists($imgPath)) {
                @unlink($imgPath); 
            }
        }

        // Bước 3: Xóa Sản phẩm trong Database
        $deleteStmt = $conn->prepare("DELETE FROM Products WHERE ProductID = ?");
        $deleteStmt->execute([$id]);

        // Bước 4: Load lại trang này để cập nhật danh sách
        header("Location: product.php");
        exit();

    } catch (Exception $e) {
        $error_message = "Lỗi: " . $e->getMessage();
    }
}

// 2. Gọi giao diện Header
include 'includes/header.php';
?>

<?php if (isset($error_message)): ?>
    <script>alert('<?php echo $error_message; ?>'); window.location='product.php';</script>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-primary fw-bold text-uppercase m-0">Quản Lý Sản Phẩm</h2>
    <a href="product_add.php" class="btn btn-success px-4 py-2 fw-bold shadow-sm">
        <i class="bi bi-plus-lg me-1"></i> Thêm Sản Phẩm
    </a>
</div>

<div class="card shadow-lg border-0 rounded-4 w-100">
    <div class="card-header bg-white py-3">
        <h6 class="m-0 fw-bold text-secondary"><i class="bi bi-box-seam me-2"></i>Danh Sách Sản Phẩm Hiện Có</h6>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0" style="width: 100%;">
                <thead class="table-dark">
                    <tr>
                        <th class="ps-4 py-3">ID</th>
                        <th class="py-3">Hình Ảnh</th>
                        <th class="py-3">Tên Sản Phẩm</th>
                        <th class="py-3">Danh Mục</th>
                        <th class="py-3">Thương Hiệu</th>
                        <th class="py-3">Giá Tiền</th>
                        <th class="py-3">Kho</th>
                        <th class="py-3 text-end pe-4">Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT p.*, c.CategoryName, b.BrandName 
                            FROM Products p 
                            LEFT JOIN Categories c ON p.CategoryID = c.CategoryID 
                            LEFT JOIN Brands b ON p.BrandID = b.BrandID 
                            ORDER BY p.ProductID DESC";
                    $stmt = $conn->query($sql);

                    if ($stmt->rowCount() == 0) {
                        echo "<tr><td colspan='8' class='text-center py-5 text-muted'>Chưa có sản phẩm nào.</td></tr>";
                    }

                    while ($row = $stmt->fetch()) {
                    ?>
                        <tr class="border-bottom">
                            <td class="ps-4 fw-bold text-muted">#<?php echo $row['ProductID']; ?></td>
                            <td>
                                <?php if (!empty($row['Image'])): ?>
                                    <img src="../uploads/<?php echo $row['Image']; ?>"
                                         alt="Img" class="rounded shadow-sm border"
                                         style="width: 60px; height: 60px; object-fit: cover;">
                                <?php else: ?>
                                    <span class="text-muted small"><i class="bi bi-image-alt"></i> No img</span>
                                <?php endif; ?>
                            </td>
                            <td class="fw-bold text-dark fs-5"><?php echo $row['ProductName']; ?></td>
                            <td>
                                <span class="badge bg-light text-secondary border px-2 py-1">
                                    <?php echo $row['CategoryName'] ?? 'Chưa phân loại'; ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-light text-primary border px-2 py-1">
                                    <?php echo $row['BrandName'] ?? 'Chưa rõ'; ?>
                                </span>
                            </td>
                            <td class="fw-bold text-danger">
                                <?php echo number_format($row['Price'], 0, ',', '.'); ?>đ
                            </td>
                            <td>
                                <?php if ($row['Stock'] > 0): ?>
                                    <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">
                                        Còn <?php echo $row['Stock']; ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill">
                                        Hết hàng
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end pe-4">
                                <a href="product_edit.php?id=<?php echo $row['ProductID']; ?>"
                                   class="btn btn-warning btn-sm me-2" title="Sửa">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <a href="product.php?delete_id=<?php echo $row['ProductID']; ?>"
                                   class="btn btn-danger btn-sm"
                                   title="Xóa"
                                   onclick="return confirm('Bạn chắc chắn muốn xóa không?');">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>