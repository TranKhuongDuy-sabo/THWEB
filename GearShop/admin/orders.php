<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../login.php"); // Sửa đường dẫn login cho đúng
    exit();
}

include '../includes/db.php';
include 'includes/header.php';

// --- XỬ LÝ XÓA ĐƠN HÀNG ---
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    try {
        $conn->prepare("DELETE FROM Orders WHERE OrderID = ?")->execute([$id]);
        echo "<script>alert('Đã xóa đơn hàng thành công!'); window.location='orders.php';</script>";
    } catch (Exception $e) {
        echo "<script>alert('Lỗi: " . $e->getMessage() . "');</script>";
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-primary fw-bold text-uppercase m-0">Quản Lý Đơn Hàng</h2>
</div>

<div class="card shadow-lg border-0 rounded-4 w-100">
    <div class="card-header bg-white py-3">
        <h6 class="m-0 fw-bold text-secondary"><i class="bi bi-cart-check-fill me-2"></i>Danh Sách Đơn Hàng Mới Nhất</h6>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0" style="width: 100%;">
                <thead class="table-dark">
                    <tr>
                        <th class="ps-4 py-3">Mã Đơn</th>
                        <th class="py-3">Ngày Đặt</th>
                        <th class="py-3">Khách Hàng</th>
                        <th class="py-3">Địa Chỉ Giao</th>
                        <th class="py-3">Tổng Tiền</th>
                        <th class="py-3">Trạng Thái</th>
                        <th class="py-3 text-end pe-4">Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Lấy danh sách đơn hàng mới nhất lên đầu
                    $sql = "SELECT * FROM Orders ORDER BY OrderID DESC";
                    $stmt = $conn->query($sql);

                    while ($row = $stmt->fetch()) {
                        // Xử lý màu sắc trạng thái
                        $statusColor = 'bg-warning text-dark'; // Mặc định Pending là màu vàng
                        if ($row['Status'] == 'Completed') $statusColor = 'bg-success';
                        if ($row['Status'] == 'Cancelled') $statusColor = 'bg-danger';
                    ?>
                        <tr>
                            <td class="ps-4 fw-bold text-primary">#<?php echo $row['OrderID']; ?></td>
                            
                            <td>
                                <div class="small fw-bold"><?php echo date('d/m/Y', strtotime($row['OrderDate'])); ?></div>
                                <div class="small text-muted"><?php echo date('H:i', strtotime($row['OrderDate'])); ?></div>
                            </td>

                            <td>
                                <div class="fw-bold"><?php echo $row['FullName']; ?></div>
                                <div class="small text-muted"><i class="bi bi-telephone-fill me-1"></i><?php echo $row['Phone']; ?></div>
                            </td>

                            <td>
                                <div class="text-truncate" style="max-width: 200px;" title="<?php echo $row['Address']; ?>">
                                    <i class="bi bi-geo-alt-fill text-danger me-1"></i><?php echo $row['Address']; ?>
                                </div>
                            </td>

                            <td class="fw-bold text-danger fs-6">
                                <?php echo number_format($row['TotalMoney'], 0, ',', '.'); ?>đ
                            </td>

                            <td>
                                <span class="badge <?php echo $statusColor; ?> rounded-pill px-3 py-2">
                                    <?php echo $row['Status']; ?>
                                </span>
                            </td>

                            <td class="text-end pe-4">
                                <a href="order_details.php?id=<?php echo $row['OrderID']; ?>" class="btn btn-info btn-sm text-white shadow-sm me-1" title="Xem chi tiết">
                                    <i class="bi bi-eye-fill"></i> Xem
                                </a>
                                
                                <a href="orders.php?delete_id=<?php echo $row['OrderID']; ?>" 
                                   class="btn btn-danger btn-sm shadow-sm"
                                   onclick="return confirm('Bạn có chắc chắn muốn xóa đơn hàng này không?');" title="Xóa đơn hàng">
                                    <i class="bi bi-trash-fill"></i>
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