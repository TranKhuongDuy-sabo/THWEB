<?php
include '../includes/db.php';
include 'includes/header.php';

// --- XỬ LÝ LƯU ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['BrandName']);
    $origin = trim($_POST['Origin']);

    if (!empty($name)) {
        // Kiểm tra trùng tên
        $check = $conn->prepare("SELECT * FROM Brands WHERE BrandName = ?");
        $check->execute([$name]);

        if ($check->rowCount() > 0) {
            $error = "Tên thương hiệu '$name' đã tồn tại!";
        } else {
            // Thêm mới
            $sql = "INSERT INTO Brands (BrandName, Origin) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            if ($stmt->execute([$name, $origin])) {
                echo "<script>alert('Thêm thành công!'); window.location='brands.php';</script>";
            } else {
                $error = "Lỗi hệ thống, vui lòng thử lại!";
            }
        }
    } else {
        $error = "Vui lòng nhập tên thương hiệu!";
    }
}
?>

<div class="container-fluid mt-4 px-4">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">

            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-header bg-primary text-white py-4">
                    <h4 class="mb-0 fw-bold text-uppercase text-center"><i class="bi bi-folder-plus me-2"></i> Thêm Thương Hiệu Mới</h4>
                </div>

                <div class="card-body p-5">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger shadow-sm border-0 mb-4"><i class="bi bi-exclamation-octagon-fill me-2"></i> <?php echo $error; ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-5">
                            <label class="form-label fw-bold text-secondary text-uppercase mb-3">Tên Thương Hiệu <span class="text-danger">*</span></label>
                            <div class="input-group input-group-lg shadow-sm">
                                <span class="input-group-text bg-light"><i class="bi bi-tag-fill text-primary"></i></span>
                                <input type="text" name="BrandName" class="form-control bg-white" required
                                    placeholder="Ví dụ: Apple, Samsung, Logitech..." style="height: 60px; font-size: 1.2rem;">
                            </div>
                        </div>

                        <div class="mb-5">
                            <label class="form-label fw-bold text-secondary text-uppercase mb-2">Xuất Xứ / Quốc Gia</label>
                            <div class="input-group input-group-lg shadow-sm">
                                <span class="input-group-text bg-light"><i class="bi bi-tag-fill text-primary"></i></span>
                                <input type="text" name="Origin" class="form-control bg-white"
                                    placeholder="Ví dụ: Mỹ, Trung Quốc, Đài Loan..." style="height: 60px; font-size: 1.2rem;">
                            </div>
                        </div>

                        <div class="d-flex gap-3">
                            <a href="brands.php" class="btn btn-light btn-lg w-50 border shadow-sm">
                                <i class="bi bi-arrow-left me-2"></i> Quay lại
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg px-4 shadow w-50 fw-bold">
                                <i class="bi bi-save me-2"></i> LƯU THƯƠNG HIỆU
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>