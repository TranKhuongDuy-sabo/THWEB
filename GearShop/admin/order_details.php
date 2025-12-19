<?php
ob_start(); // Tránh lỗi header chuyển hướng
session_start();

// Kiểm tra quyền Admin
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../login.php");
    exit();
}

include '../includes/db.php';
include 'includes/header.php';

// 1. Kiểm tra ID đơn hàng
if (!isset($_GET['id'])) {
    echo "<script>alert('Không tìm thấy ID đơn hàng!'); window.location='orders.php';</script>";
    exit();
}
$order_id = $_GET['id'];

// --- XỬ LÝ 1: XÓA SẢN PHẨM KHỎI ĐƠN HÀNG ---
if (isset($_GET['remove_item'])) {
    $product_id_to_remove = $_GET['remove_item'];
    
    // Tự động nhận diện tên bảng (order_details hay orderdetails)
    $table_details = 'order_details'; // Mặc định
    
    try {
        // Thử xóa ở bảng order_details
        $stmt = $conn->prepare("DELETE FROM order_details WHERE OrderID = ? AND ProductID = ?");
        $stmt->execute([$order_id, $product_id_to_remove]);
    } catch (Exception $e) {
        // Nếu lỗi, thử xóa ở bảng orderdetails
        $table_details = 'orderdetails';
        $stmt = $conn->prepare("DELETE FROM orderdetails WHERE OrderID = ? AND ProductID = ?");
        $stmt->execute([$order_id, $product_id_to_remove]);
    }
    
    // Cập nhật lại Tổng tiền (TotalMoney) trong bảng orders
    // Tính tổng tiền còn lại
    $sqlSum = "SELECT SUM(Price * Quantity) FROM $table_details WHERE OrderID = ?";
    $stmtSum = $conn->prepare($sqlSum);
    $stmtSum->execute([$order_id]);
    $new_total = $stmtSum->fetchColumn() ?: 0; // Nếu không còn sp nào thì bằng 0

    // Update vào bảng orders
    $conn->prepare("UPDATE orders SET TotalMoney = ? WHERE OrderID = ?")
         ->execute([$new_total, $order_id]);

    echo "<script>alert('Đã xóa sản phẩm và cập nhật lại tổng tiền!'); window.location.href='order_details.php?id=$order_id';</script>";
    exit();
}

// --- XỬ LÝ 2: CẬP NHẬT TRẠNG THÁI ---
if (isset($_POST['btn_update_status'])) {
    $new_status = $_POST['status'];
    $conn->prepare("UPDATE orders SET Status = ? WHERE OrderID = ?")->execute([$new_status, $order_id]);
    
    echo "<script>alert('Đã cập nhật trạng thái đơn hàng thành công!'); window.location.href='order_details.php?id=$order_id';</script>";
    exit();
}

// 2. Lấy thông tin Đơn hàng
try {
    $sqlOrder = "SELECT o.*, u.FullName as UserFullName, u.Phone as UserPhone, u.Email 
                 FROM orders o 
                 LEFT JOIN users u ON o.UserID = u.UserID 
                 WHERE o.OrderID = ?";
    $stmtOrder = $conn->prepare($sqlOrder);
    $stmtOrder->execute([$order_id]);
    $order = $stmtOrder->fetch();

    if (!$order) {
        die("<div class='alert alert-danger m-4'>Đơn hàng không tồn tại.</div>");
    }
} catch (Exception $e) {
    die("Lỗi: " . $e->getMessage());
}

// 3. Lấy chi tiết sản phẩm (Tự động thử tên bảng)
$details = [];
try {
    // Thử bảng chuẩn
    $sql = "SELECT d.*, p.ProductName, p.Image 
            FROM order_details d 
            LEFT JOIN products p ON d.ProductID = p.ProductID 
            WHERE d.OrderID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$order_id]);
    $details = $stmt->fetchAll();
} catch (Exception $e) {
    // Nếu lỗi thử bảng viết liền
    $sql = "SELECT d.*, p.ProductName, p.Image 
            FROM orderdetails d 
            LEFT JOIN products p ON d.ProductID = p.ProductID 
            WHERE d.OrderID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$order_id]);
    $details = $stmt->fetchAll();
}
?>

<div class="container-fluid px-4 py-4">
    <div class="d-flex align-items-center mb-4 gap-3">
        <a href="orders.php" class="btn btn-secondary shadow-sm">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
        <h2 class="text-primary fw-bold text-uppercase m-0">CHI TIẾT ĐƠN #<?php echo $order['OrderID']; ?></h2>
        
        <?php 
            $statusColor = 'secondary';
            if($order['Status'] == 'Pending') $statusColor = 'warning';
            if($order['Status'] == 'Shipping') $statusColor = 'info';
            if($order['Status'] == 'Completed') $statusColor = 'success';
            if($order['Status'] == 'Cancelled') $statusColor = 'danger';
        ?>
        <span class="badge bg-<?php echo $statusColor; ?> fs-6 ms-auto px-3 py-2 rounded-pill">
            <?php echo $order['Status']; ?>
        </span>
    </div>

    <div class="row">
        <div class="col-lg-4 mb-4">
            
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <h6 class="m-0 fw-bold"><i class="bi bi-pencil-square me-2"></i>CẬP NHẬT TRẠNG THÁI</h6>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <label class="form-label small fw-bold text-secondary">Trạng thái đơn hàng:</label>
                        <div class="input-group">
                            <select name="status" class="form-select fw-bold">
                                <option value="Pending" <?php echo ($order['Status'] == 'Pending') ? 'selected' : ''; ?>>Pending (Chờ xử lý)</option>
                                <option value="Shipping" <?php echo ($order['Status'] == 'Shipping') ? 'selected' : ''; ?>>Shipping (Đang giao)</option>
                                <option value="Completed" <?php echo ($order['Status'] == 'Completed') ? 'selected' : ''; ?>>Completed (Hoàn thành)</option>
                                <option value="Cancelled" <?php echo ($order['Status'] == 'Cancelled') ? 'selected' : ''; ?>>Cancelled (Đã Hủy)</option>
                            </select>
                            <button type="submit" name="btn_update_status" class="btn btn-success fw-bold">
                                <i class="bi bi-save"></i> Lưu
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-3 h-100">
                <div class="card-header bg-info text-white py-3">
                    <h6 class="m-0 fw-bold"><i class="bi bi-person-vcard me-2"></i>THÔNG TIN KHÁCH HÀNG</h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0">
                            <small class="text-muted d-block">Họ và tên</small>
                            <strong><?php echo $order['FullName']; ?></strong>
                        </li>
                        <li class="list-group-item px-0">
                            <small class="text-muted d-block">Số điện thoại</small>
                            <strong><?php echo $order['Phone']; ?></strong>
                        </li>
                        <li class="list-group-item px-0">
                            <small class="text-muted d-block">Địa chỉ giao hàng</small>
                            <span><?php echo $order['Address']; ?></span>
                        </li>
                        <li class="list-group-item px-0">
                            <small class="text-muted d-block">Ngày đặt hàng</small>
                            <span><?php echo date('d/m/Y H:i', strtotime($order['OrderDate'])); ?></span>
                        </li>
                        <li class="list-group-item px-0">
                            <small class="text-muted d-block">Ghi chú</small>
                            <em class="text-secondary"><?php echo !empty($order['Note']) ? $order['Note'] : 'Không có'; ?></em>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="m-0 fw-bold text-dark"><i class="bi bi-cart3 me-2"></i>DANH SÁCH SẢN PHẨM</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-secondary small">
                                <tr>
                                    <th class="ps-4 py-3">SẢN PHẨM</th>
                                    <th class="text-center">GIÁ</th>
                                    <th class="text-center">SỐ LƯỢNG</th>
                                    <th class="text-end pe-3">THÀNH TIỀN</th>
                                    <th class="text-center" style="width: 50px;">XÓA</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($details as $item): ?>
                                <tr class="border-bottom">
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <?php if(!empty($item['Image'])): ?>
                                                <img src="../uploads/<?php echo $item['Image']; ?>" class="rounded border me-3" style="width: 48px; height: 48px; object-fit: contain;">
                                            <?php else: ?>
                                                <div class="bg-light rounded border me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                                    <i class="bi bi-image text-secondary"></i>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <div>
                                                <div class="fw-bold text-dark"><?php echo $item['ProductName']; ?></div>
                                                <small class="text-muted">ID: #<?php echo $item['ProductID']; ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td class="text-center">
                                        <?php echo number_format($item['Price'], 0, ',', '.'); ?>đ
                                    </td>
                                    
                                    <td class="text-center fw-bold">
                                        x<?php echo $item['Quantity']; ?>
                                    </td>
                                    
                                    <td class="text-end pe-3 fw-bold text-primary">
                                        <?php echo number_format($item['Price'] * $item['Quantity'], 0, ',', '.'); ?>đ
                                    </td>
                                    
                                    <td class="text-center">
                                        <a href="order_details.php?id=<?php echo $order_id; ?>&remove_item=<?php echo $item['ProductID']; ?>" 
                                           class="btn btn-sm btn-outline-danger border-0"
                                           onclick="return confirm('Bạn chắc chắn muốn xóa sản phẩm này khỏi đơn hàng? Tổng tiền sẽ được tính lại.');"
                                           title="Xóa món này">
                                            <i class="bi bi-x-lg"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td colspan="3" class="text-end py-3 pe-3 text-secondary fs-6">TỔNG CỘNG THANH TOÁN:</td>
                                    <td class="text-end pe-3 py-3 text-danger fw-bold fs-4">
                                        <?php echo number_format($order['TotalMoney'], 0, ',', '.'); ?>đ
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="text-end mt-4">
                <button onclick="window.print()" class="btn btn-outline-dark fw-bold">
                    <i class="bi bi-printer me-2"></i> IN HÓA ĐƠN
                </button>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>