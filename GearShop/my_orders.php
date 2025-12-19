<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'includes/db.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location='login.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];

// Lấy danh sách đơn hàng của user này (Sắp xếp mới nhất lên đầu)
$stmt = $conn->prepare("SELECT * FROM Orders WHERE UserID = ? ORDER BY OrderID DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();
?>

<div class="container py-5" style="min-height: 50vh;">
    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="list-group shadow-sm">
                <a href="profile.php" class="list-group-item list-group-item-action"><i class="bi bi-person-circle me-2"></i> Hồ sơ cá nhân</a>
                <a href="my_orders.php" class="list-group-item list-group-item-action active fw-bold"><i class="bi bi-bag-check me-2"></i> Đơn mua của tôi</a>
                <a href="logout.php" class="list-group-item list-group-item-action text-danger"><i class="bi bi-box-arrow-right me-2"></i> Đăng xuất</a>
            </div>
        </div>

        <div class="col-md-9">
            <h3 class="fw-bold mb-4">Lịch sử đơn hàng</h3>
            
            <?php if (count($orders) > 0): ?>
                <div class="card border-0 shadow-sm">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="py-3 ps-4">Mã đơn</th>
                                    <th>Ngày đặt</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
                                    <th class="text-end pe-4">Chi tiết</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): 
                                    $statusClass = match ($order['Status']) {
                                        'Pending', 'Mới' => 'bg-warning text-dark',
                                        'Completed', 'Đã giao' => 'bg-success text-white',
                                        'Cancelled', 'Hủy' => 'bg-danger text-white',
                                        default => 'bg-secondary text-white'
                                    };
                                ?>
                                <tr>
                                    <td class="ps-4 fw-bold text-primary">#<?php echo $order['OrderID']; ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($order['OrderDate'])); ?></td>
                                    <td class="fw-bold text-danger"><?php echo number_format($order['TotalMoney'], 0, ',', '.'); ?>đ</td>
                                    <td><span class="badge rounded-pill <?php echo $statusClass; ?> px-3"><?php echo $order['Status']; ?></span></td>
                                    <td class="text-end pe-4">
                                        <a href="my_order_details.php?id=<?php echo $order['OrderID']; ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">Xem</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center py-5 bg-white rounded shadow-sm">
                    <i class="bi bi-cart-x display-1 text-muted"></i>
                    <p class="mt-3 lead">Bạn chưa có đơn hàng nào.</p>
                    <a href="index.php" class="btn btn-primary rounded-pill mt-2">Mua sắm ngay</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>