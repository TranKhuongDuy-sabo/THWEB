<?php
include '../includes/db.php';
include 'includes/header.php';

// --- XỬ LÝ XÓA ---
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    try {
        $conn->prepare("DELETE FROM Categories WHERE CategoryID = ?")->execute([$id]);
        echo "<script>alert('Đã xóa thành công!'); window.location='categories.php';</script>";
    } catch (Exception $e) {
        echo "<script>alert('Lỗi: Không thể xóa danh mục đang có sản phẩm!');</script>";
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-primary fw-bold text-uppercase m-0">Quản Lý Danh Mục</h2>
    <a href="categories_add.php" class="btn btn-success px-4 py-2 fw-bold shadow-sm">
        <i class="bi bi-plus-lg me-1"></i> Thêm Mới
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <h6 class="m-0 fw-bold text-secondary"><i class="bi bi-list-task me-2"></i>Danh Sách Loại Hàng</h6>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0" style="width: 100%;">
                <thead class="table-dark">
                    <tr>
                        <th class="ps-4 py-3" style="width: 10%;">ID</th>
                        <th class="py-3">Tên Danh Mục</th>
                        <th class="py-3 text-end pe-4" style="width: 20%;">Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $conn->query("SELECT * FROM Categories ORDER BY CategoryID DESC");

                    // NẾU CHƯA CÓ DỮ LIỆU
                    if ($stmt->rowCount() == 0) {
                        echo "<tr>
                                <td colspan='3' class='text-center py-5'>
                                    <div class='text-muted mb-2'><i class='bi bi-inbox fs-1'></i></div>
                                    <span class='fw-bold text-secondary'>Chưa có danh mục nào. Hãy bấm 'Thêm Mới'!</span>
                                </td>
                              </tr>";
                    } else {
                        // NẾU CÓ DỮ LIỆU THÌ HIỆN RA
                        while ($row = $stmt->fetch()) {
                    ?>
                            <tr>
                                <td class="ps-4 fw-bold">#<?php echo $row['CategoryID']; ?></td>
                                <td class="fs-5 text-primary"><?php echo $row['CategoryName']; ?></td>
                                <td class="text-end pe-4">
                                    <a href="categories_edit.php?id=<?php echo $row['CategoryID']; ?>" class="btn btn-warning btn-sm me-2">
                                        <i class="bi bi-pencil-square"></i> Sửa
                                    </a>
                                    <a href="categories.php?delete_id=<?php echo $row['CategoryID']; ?>"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('Bạn chắc chắn muốn xóa?');">
                                        <i class="bi bi-trash"></i> Xóa
                                    </a>
                                </td>
                            </tr>
                    <?php
                        } // End while
                    } // End else
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>