<?php
include '../includes/db.php';
include 'includes/header.php';

// 1. Lấy thông tin cũ để hiển thị
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM Brands WHERE BrandID = ?");
    $stmt->execute([$id]);
    $brand = $stmt->fetch();

    if (!$brand) {
        echo "<script>alert('Không tìm thấy thương hiệu!'); window.location='brands.php';</script>";
        exit;
    }
}

// 2. Xử lý Cập nhật (POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['BrandName']);
    $origin = trim($_POST['Origin']);
    $id = $_POST['BrandID'];

    if (!empty($name)) {
        try {
            $sql = "UPDATE Brands SET BrandName = ?, Origin = ? WHERE BrandID = ?";
            $stmt = $conn->prepare($sql);
            if ($stmt->execute([$name, $origin, $id])) {
                echo "<script>alert('Cập nhật thành công!'); window.location='brands.php';</script>";
            } else {
                $error = "Lỗi khi cập nhật!";
            }
        } catch (Exception $e) {
            $error = "Lỗi hệ thống: Không thể cập nhật.";
        }
    } else {
        $error = "Tên không được để trống!";
    }

    // Nếu có lỗi, load lại trang với dữ liệu mới nhất (để hiển thị lỗi)
    if (isset($error)) {
        $stmt = $conn->prepare("SELECT * FROM Brands WHERE BrandID = ?");
        $stmt->execute([$id]);
        $brand = $stmt->fetch();
    }
}
?>

<div class="container" style="max-width: 600px;">
    <div class="card shadow">
        <div class="card-header bg-warning text-dark">
            <h4 class="mb-0"><i class="bi bi-pencil-square"></i> Chỉnh Sửa Thương Hiệu</h4>
        </div>

        <div class="card-body p-4">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="BrandID" value="<?php echo $brand['BrandID']; ?>">

                <div class="mb-4">
                    <label class="form-label fw-bold">Tên Thương Hiệu</label>
                    <div class="input-group input-group-lg shadow-sm">
                        <input type="text" name="BrandName" class="form-control" required
                            value="<?php echo htmlspecialchars($brand['BrandName']); ?>">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Xuất Xứ / Quốc Gia</label>
                    <div class="input-group input-group-lg shadow-sm">
                        <input type="text" name="Origin" class="form-control"
                            value="<?php echo htmlspecialchars($brand['Origin']); ?>">
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="brands.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Quay lại
                    </a>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-check-circle"></i> CẬP NHẬT
                    </button>
                </div>
            </form>
        </div>
    </div>



</div>

<?php include 'includes/footer.php'; ?>