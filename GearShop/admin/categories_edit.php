<?php
include '../includes/db.php';
include 'includes/header.php';

// 1. Lấy ID từ URL để biết đang sửa cái nào
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM Categories WHERE CategoryID = ?");
    $stmt->execute([$id]);
    $category = $stmt->fetch();

    if (!$category) {
        echo "<script>alert('Không tìm thấy danh mục!'); window.location='categories.php';</script>";
        exit;
    }
}

// 2. Xử lý khi bấm Cập Nhật
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['CategoryName'];
    $id = $_POST['CategoryID']; // Lấy ID từ input ẩn

    if (!empty($name)) {
        $sql = "UPDATE Categories SET CategoryName = ? WHERE CategoryID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$name, $id]);
        echo "<script>alert('Cập nhật thành công!'); window.location='categories.php';</script>";
    } else {
        $error = "Tên không được để trống!";
    }
}
?>

<div class="container" style="max-width: 600px;">
    <div class="card shadow">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0"><i class="bi bi-pencil-square"></i> Chỉnh Sửa Danh Mục</h5>
        </div>
        <div class="card-body p-4">
            
            <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

            <form method="POST">
                <input type="hidden" name="CategoryID" value="<?php echo $category['CategoryID']; ?>">

                <div class="mb-4">
                    <label class="form-label fw-bold">Tên Danh Mục:</label>
                    <input type="text" name="CategoryName" class="form-control" 
                           value="<?php echo $category['CategoryName']; ?>" required>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="categories.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Quay lại</a>
                    <button type="submit" class="btn btn-warning"><i class="bi bi-check-circle"></i> Cập Nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>