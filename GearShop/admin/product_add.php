<?php
include '../includes/db.php';
include 'includes/header.php';

// 1. LẤY DỮ LIỆU DANH MỤC & THƯƠNG HIỆU (Để hiện vào ô chọn)
$categories = $conn->query("SELECT * FROM Categories")->fetchAll();
$brands = $conn->query("SELECT * FROM Brands")->fetchAll();

// 2. XỬ LÝ LƯU SẢN PHẨM
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['ProductName']);
    $price = $_POST['Price'];
    $stock = $_POST['Stock'];
    $catId = $_POST['CategoryID'];
    $brandId = $_POST['BrandID'];
    $desc = $_POST['Description'];
    $image = ""; // Mặc định không có ảnh

    // Validate cơ bản
    if (empty($name) || empty($price)) {
        $error = "Vui lòng nhập Tên và Giá sản phẩm!";
    } else {
        // --- XỬ LÝ UPLOAD ẢNH ---
        if (!empty($_FILES['Image']['name'])) {
            $targetDir = "../uploads/";
            // Tạo tên file mới: Thêm thời gian vào trước tên file để tránh trùng (VD: 169999_anh.jpg)
            $fileName = time() . "_" . basename($_FILES['Image']['name']);
            $targetFilePath = $targetDir . $fileName;
            
            // Kiểm tra định dạng ảnh
            $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
            $allowTypes = array('jpg', 'png', 'jpeg', 'gif', 'webp');
            
            if (in_array(strtolower($fileType), $allowTypes)) {
                // Upload file
                if (move_uploaded_file($_FILES['Image']['tmp_name'], $targetFilePath)) {
                    $image = $fileName; // Lưu tên file vào biến để lát insert vào DB
                } else {
                    $error = "Lỗi: Không thể tải ảnh lên thư mục uploads.";
                }
            } else {
                $error = "Chỉ chấp nhận file ảnh (JPG, JPEG, PNG, GIF, WEBP).";
            }
        }

        // Nếu không có lỗi thì Insert vào DB
        if (!isset($error)) {
            try {
                $sql = "INSERT INTO Products (ProductName, CategoryID, BrandID, Price, Stock, Description, Image) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$name, $catId, $brandId, $price, $stock, $desc, $image]);
                
                echo "<script>alert('Thêm sản phẩm thành công!'); window.location='product.php';</script>";
            } catch (Exception $e) {
                $error = "Lỗi hệ thống: " . $e->getMessage();
            }
        }
    }
}
?>

<div class="container-fluid mt-4 px-4">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-header bg-primary text-white py-4">
                    <h4 class="mb-0 fw-bold text-uppercase text-center"><i class="bi bi-box-seam me-2"></i> Thêm Sản Phẩm Mới</h4>
                </div>
                
                <div class="card-body p-5">
                    <?php if(isset($error)): ?>
                        <div class="alert alert-danger shadow-sm border-0 mb-4"><i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo $error; ?></div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data">
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary text-uppercase">Tên Sản Phẩm <span class="text-danger">*</span></label>
                            <input type="text" name="ProductName" class="form-control form-control-lg" required 
                                   placeholder="VD: Laptop Gaming Asus ROG...">
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary text-uppercase">Giá Bán (VNĐ) <span class="text-danger">*</span></label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light fw-bold text-success">₫</span>
                                    <input type="number" name="Price" class="form-control" required placeholder="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary text-uppercase">Số Lượng Kho</label>
                                <input type="number" name="Stock" class="form-control form-control-lg" value="0">
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary text-uppercase">Danh Mục</label>
                                <select name="CategoryID" class="form-select form-select-lg">
                                    <option value="">-- Chọn Danh Mục --</option>
                                    <?php foreach($categories as $cat): ?>
                                        <option value="<?php echo $cat['CategoryID']; ?>"><?php echo $cat['CategoryName']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary text-uppercase">Thương Hiệu</label>
                                <select name="BrandID" class="form-select form-select-lg">
                                    <option value="">-- Chọn Hãng --</option>
                                    <?php foreach($brands as $brand): ?>
                                        <option value="<?php echo $brand['BrandID']; ?>"><?php echo $brand['BrandName']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary text-uppercase">Hình Ảnh Sản Phẩm</label>
                            <input type="file" name="Image" class="form-control form-control-lg">
                            <div class="form-text">Hỗ trợ ảnh jpg, png, gif, webp.</div>
                        </div>

                        <div class="mb-5">
                            <label class="form-label fw-bold text-secondary text-uppercase">Mô Tả / Cấu Hình</label>
                            <textarea name="Description" class="form-control" rows="5" placeholder="Nhập chi tiết cấu hình, thông số kỹ thuật..."></textarea>
                        </div>
                        
                        <div class="d-flex gap-3">
                            <a href="products.php" class="btn btn-light btn-lg w-50 border shadow-sm">
                                <i class="bi bi-arrow-left me-2"></i> Quay lại
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg px-4 shadow w-50 fw-bold">
                                <i class="bi bi-save me-2"></i> LƯU SẢN PHẨM
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>