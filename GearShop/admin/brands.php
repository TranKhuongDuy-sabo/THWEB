<?php
include '../includes/db.php';
include 'includes/header.php';

// --- XỬ LÝ XÓA THƯƠNG HIỆU ---
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    try {
        $stmt = $conn->prepare("DELETE FROM Brands WHERE BrandID = ?");
        $stmt->execute([$id]);
        echo "<script>alert('Đã xóa thương hiệu thành công!'); window.location='brands.php';</script>";
    } catch (Exception $e) {
        echo "<script>alert('Lỗi: Không thể xóa thương hiệu này vì đang có sản phẩm liên kết!');</script>";
    }
}
?>


<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-primary fw-bold text-uppercase m-0">Quản Lý Thương Hiệu</h2>
    <a href="brands_add.php" class="btn btn-success px-4 py-2 fw-bold shadow-sm">
        <i class="bi bi-plus-lg me-1"></i> Thêm Mới
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <h6 class="m-0 fw-bold text-secondary"><i class="bi bi-list-task me-2"></i>Danh Sách Thương Hiệu</h6>
    </div>
    
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0" style="width: 100%;">
                <thead class="table-dark">
                    <tr>
                        <th class="ps-4 py-3" style="width: 10%;">ID</th>
                        <th class="py-3">Tên Thương Hiệu</th>
                        <th class="py-3">Xuất Xứ</th>
                        <th class="py-3 text-end pe-4" style="width: 20%;">Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $conn->query("SELECT * FROM Brands ORDER BY BrandID DESC");
                    
                    // Xử lý không có dữ liệu
                    if($stmt->rowCount() == 0) {
                        echo "<tr>
                                <td colspan='3' class='text-center py-5'>
                                    <div class='text-muted mb-2'><i class='bi bi-inbox fs-1'></i></div>
                                    <span class='fw-bold text-secondary'>Chưa có thương hiệu nào. Hãy thêm mới!</span>
                                </td>
                              </tr>";
                    }

                    while ($row = $stmt->fetch()) {
                    ?>
                        <tr>
                            <td class="ps-4 fw-bold">#<?php echo $row['BrandID']; ?></td>
                            <td class="fs-5 text-primary"><?php echo $row['BrandName']; ?></td>
                            <td>
                                <span class="badge bg-info text-dark fs-6 px-3 py-2 rounded-pill">
                                    <i class="bi bi-geo-alt-fill me-1"></i> <?php echo $row['Origin']; ?>
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <a href="brands_edit.php?id=<?php echo $row['BrandID']; ?>" 
                                   class="btn btn-warning btn-sm me-2" title="Chỉnh sửa">
                                    <i class="bi bi-pencil-square"></i> Sửa
                                </a>
                                <a href="brands.php?delete_id=<?php echo $row['BrandID']; ?>" 
                                   class="btn btn-danger btn-sm"
                                   title="Xóa bỏ"
                                   onclick="return confirm('Bạn chắc chắn muốn xóa hãng <?php echo $row['BrandName']; ?>?');">
                                    <i class="bi bi-trash"></i> Xóa
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