<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'includes/db.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    echo "<script>window.location='index.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];
$order_id = $_GET['id'];

// 1. Lấy thông tin đơn hàng (BẮT BUỘC CHECK UserID để bảo mật)
$stmt = $conn->prepare("SELECT * FROM Orders WHERE OrderID = ? AND UserID = ?");
$stmt->execute([$order_id, $user_id]);
$order = $stmt->fetch();

if (!$order) {
    echo "<script>alert('Không tìm thấy đơn hàng!'); window.location='my_orders.php';</script>";
    exit;
}

// 2. Lấy chi tiết sản phẩm
$stmtDetail = $conn->prepare("SELECT d.*, p.ProductName, p.Image 
                              FROM OrderDetails d 
                              JOIN Products p ON d.ProductID = p.ProductID 
                              WHERE d.OrderID = ?");
$stmtDetail->execute([$order_id]);
$details = $stmtDetail->fetchAll();
?>

<div class="container py-5" style="min-height: 50vh;">
    <div class="d-flex align-items-center mb-4 gap-3">
        <a href="my_orders.php" class="btn btn-outline-secondary rounded-circle"><i class="bi bi-arrow-left"></i></a>
        <h3 class="fw-bold m-0">Chi tiết đơn hàng #<?php echo $order['OrderID']; ?></h3>
        <span class="badge bg-warning text-dark ms-auto fs-6"><?php echo $order['Status']; ?></span>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-bold py-3">Địa chỉ nhận hàng</div>
                <div class="card-body">
                    <p class="mb-1 fw-bold"><?php echo $order['FullName']; ?></p>
                    <p class="mb-1 text-muted small"><?php echo $order['Phone']; ?></p>
                    <p class="mb-2 text-muted small"><?php echo $order['Address']; ?></p>
                    <?php if(!empty($order['Note'])): ?>
                        <div class="alert alert-light border small mb-0">
                            <strong>Ghi chú:</strong> <?php echo $order['Note']; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-bold py-3">Sản phẩm đã mua</div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <?php foreach ($details as $item): ?>
                        <li class="list-group-item d-flex align-items-center py-3">
                            <img src="uploads/<?php echo $item['Image']; ?>" class="rounded border me-3" style="width: 60px; height: 60px; object-fit: cover;">
                            <div class="flex-grow-1">
                                <h6 class="mb-0 fw-bold"><?php echo $item['ProductName']; ?></h6>
                                <small class="text-muted">x<?php echo $item['Quantity']; ?></small>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold"><?php echo number_format($item['Price'], 0, ',', '.'); ?>đ</div>
                            </div>
                        </li>
                        <?php endforeach; ?>
                        <li class="list-group-item py-3 bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold">Tổng tiền thanh toán</span>
                                <span class="fw-bold text-danger fs-5"><?php echo number_format($order['TotalMoney'], 0, ',', '.'); ?>đ</span>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>