<?php
include '../includes/db.php';
include 'includes/header.php';

// 1. LẤY ID SẢN PHẨM CẦN SỬA
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM Products WHERE ProductID = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();

    if (!$product) {
        echo "<script>alert('Sản phẩm không tồn tại!'); window.location='products.php';</script>";
        exit;
    }
} else {
    header("Location: product.php");
    exit;
}

// 2. LẤY DANH MỤC & THƯƠNG HIỆU (Để đổ vào Dropdown)
$categories = $conn->query("SELECT * FROM Categories")->fetchAll();
$brands = $conn->query("SELECT * FROM Brands")->fetchAll();

// 3. XỬ LÝ CẬP NHẬT
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['ProductName']);
    $price = $_POST['Price'];
    $stock = $_POST['Stock'];
    $catId = $_POST['CategoryID'];
    $brandId = $_POST['BrandID'];
    $desc = $_POST['Description'];
    $id = $_POST['ProductID'];
    
    // Giữ lại tên ảnh cũ mặc định
    $image = $_POST['OldImage']; 

    // --- XỬ LÝ ẢNH MỚI (NẾU CÓ CHỌN) ---
    if (!empty($_FILES['Image']['name'])) {
        $targetDir = "../uploads/";
        $fileName = time() . "_" . basename($_FILES['Image']['name']);
        $targetFilePath = $targetDir . $fileName;
        
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
        $allowTypes = array('jpg', 'png', 'jpeg', 'gif', 'webp');

        if (in_array(strtolower($fileType), $allowTypes)) {
            if (move_uploaded_file($_FILES['Image']['tmp_name'], $targetFilePath)) {
                // Upload thành công -> Cập nhật tên ảnh mới
                $image = $fileName;
                
                // (Tùy chọn) Xóa ảnh cũ cho đỡ rác server
                if (!empty($_POST['OldImage']) && file_exists("../uploads/" . $_POST['OldImage'])) {
                    @unlink("../uploads/" . $_POST['OldImage']);
                }
            } else {
                $error = "Lỗi upload ảnh mới.";
            }
        } else {
            $error = "Chỉ chấp nhận file ảnh (JPG, PNG, GIF, WEBP).";
        }
    }

    if (!isset($error)) {
        try {
            $sql = "UPDATE Products SET 
                    ProductName = ?, CategoryID = ?, BrandID = ?, 
                    Price = ?, Stock = ?, Description = ?, Image = ? 
                    WHERE ProductID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$name, $catId, $brandId, $price, $stock, $desc, $image, $id]);
            
            echo "<script>alert('Cập nhật thành công!'); window.location='product.php';</script>";
        } catch (Exception $e) {
            $error = "Lỗi hệ thống: " . $e->getMessage();
        }
    }
}
?>

<div class="container-fluid mt-4 px-4">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-header bg-warning text-dark py-4 text-center">
                    <h4 class="mb-0 fw-bold text-uppercase"><i class="bi bi-pencil-square me-2"></i> Chỉnh Sửa Sản Phẩm</h4>
                </div>
                
                <div class="card-body p-5">
                    <?php if(isset($error)): ?>
                        <div class="alert alert-danger mb-4"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="ProductID" value="<?php echo $product['ProductID']; ?>">
                        <input type="hidden" name="OldImage" value="<?php echo $product['Image']; ?>">

                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary text-uppercase">Tên Sản Phẩm</label>
                            <input type="text" name="ProductName" class="form-control form-control-lg" required 
                                   value="<?php echo htmlspecialchars($product['ProductName']); ?>">
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary text-uppercase">Giá Bán (VNĐ)</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light fw-bold text-success">₫</span>
                                    <input type="number" name="Price" class="form-control" required 
                                           value="<?php echo $product['Price']; ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary text-uppercase">Số Lượng Kho</label>
                                <input type="number" name="Stock" class="form-control form-control-lg" 
                                       value="<?php echo $product['Stock']; ?>">
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary text-uppercase">Danh Mục</label>
                                <select name="CategoryID" class="form-select form-select-lg">
                                    <option value="">-- Chọn Danh Mục --</option>
                                    <?php foreach($categories as $cat): ?>
                                        <option value="<?php echo $cat['CategoryID']; ?>" 
                                            <?php echo ($cat['CategoryID'] == $product['CategoryID']) ? 'selected' : ''; ?>>
                                            <?php echo $cat['CategoryName']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary text-uppercase">Thương Hiệu</label>
                                <select name="BrandID" class="form-select form-select-lg">
                                    <option value="">-- Chọn Hãng --</option>
                                    <?php foreach($brands as $brand): ?>
                                        <option value="<?php echo $brand['BrandID']; ?>"
                                            <?php echo ($brand['BrandID'] == $product['BrandID']) ? 'selected' : ''; ?>>
                                            <?php echo $brand['BrandName']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary text-uppercase">Hình Ảnh</label>
                            <div class="d-flex align-items-center gap-4">
                                <?php if(!empty($product['Image'])): ?>
                                    <div class="text-center">
                                        <img src="../uploads/<?php echo $product['Image']; ?>" class="rounded shadow border" style="width: 100px; height: 100px; object-fit: cover;">
                                        <div class="small text-muted mt-1">Ảnh hiện tại</div>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="flex-grow-1">
                                    <input type="file" name="Image" class="form-control form-control-lg">
                                    <div class="form-text">Để trống nếu không muốn thay đổi ảnh.</div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-5">
                            <label class="form-label fw-bold text-secondary text-uppercase">Mô Tả / Cấu Hình</label>
                            <textarea name="Description" class="form-control" rows="5"><?php echo htmlspecialchars($product['Description']); ?></textarea>
                        </div>
                        
                        <div class="d-flex gap-3">
                            <a href="products.php" class="btn btn-light btn-lg w-50 border shadow-sm">
                                <i class="bi bi-arrow-left me-2"></i> Quay lại
                            </a>
                            <button type="submit" class="btn btn-warning btn-lg w-50 shadow fw-bold">
                                <i class="bi bi-check-lg me-2"></i> CẬP NHẬT
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>