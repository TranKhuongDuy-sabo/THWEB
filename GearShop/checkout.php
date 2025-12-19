<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'includes/db.php';

// 1. Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Vui lòng đăng nhập để thanh toán!'); window.location='login.php';</script>";
    exit;
}

// 2. Kiểm tra giỏ hàng
if (empty($_SESSION['cart'])) {
    echo "<script>alert('Giỏ hàng trống!'); window.location='index.php';</script>";
    exit;
}

// 3. Lấy thông tin người dùng để điền sẵn
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM Users WHERE UserID = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// --- XỬ LÝ LƯU ĐƠN HÀNG (KHI BẤM NÚT) ---
if (isset($_POST['btn_checkout'])) {
    $fullname = $_POST['fullname'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $note = $_POST['note'];
    $created_at = date('Y-m-d H:i:s');
    
    // Tính lại tổng tiền
    $total_money = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total_money += $item['price'] * $item['quantity'];
    }

    try {
        $conn->beginTransaction();

        // 1. Insert bảng Orders
        $sql_order = "INSERT INTO Orders (UserID, FullName, Phone, Address, Note, OrderDate, TotalMoney, Status) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending')";
        $stmt = $conn->prepare($sql_order);
        $stmt->execute([$user_id, $fullname, $phone, $address, $note, $created_at, $total_money]);
        $order_id = $conn->lastInsertId();

        // 2. Insert bảng OrderDetails
        $sql_detail = "INSERT INTO OrderDetails (OrderID, ProductID, Price, Quantity) VALUES (?, ?, ?, ?)";
        $stmt_detail = $conn->prepare($sql_detail);

        foreach ($_SESSION['cart'] as $p_id => $item) {
            $stmt_detail->execute([$order_id, $p_id, $item['price'], $item['quantity']]);
        }

        $conn->commit();
        unset($_SESSION['cart']); // Xóa giỏ sau khi mua
        
        // Chuyển sang trang cảm ơn
        echo "<script>window.location='thankyou.php';</script>";
        exit;

    } catch (Exception $e) {
        $conn->rollBack();
        echo "<script>alert('Lỗi: " . $e->getMessage() . "');</script>";
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="container py-5">
    <h2 class="text-uppercase fw-bold mb-4 text-center">Thanh Toán & Đặt Hàng</h2>

    <div class="row g-5">
        <div class="col-md-7">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-person-lines-fill text-primary"></i> Thông tin giao hàng</h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Họ tên người nhận</label>
                            <input type="text" name="fullname" class="form-control" required 
                                   value="<?php echo $user['FullName'] ?? ''; ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Số điện thoại</label>
                            <input type="text" name="phone" class="form-control" required 
                                   value="<?php echo $user['Phone'] ?? ''; ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Địa chỉ nhận hàng</label>
                            <textarea name="address" class="form-control" rows="2" required><?php echo $user['Address'] ?? ''; ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Ghi chú (nếu có)</label>
                            <textarea name="note" class="form-control" rows="2" placeholder="Ví dụ: Gọi trước khi giao..."></textarea>
                        </div>

                        <hr class="my-4">
                        
                        <button type="submit" name="btn_checkout" class="btn btn-primary w-100 py-3 fw-bold text-uppercase rounded-pill shadow">
                            Xác nhận đặt hàng
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card shadow-sm border-0 bg-light">
                <div class="card-header bg-light border-bottom py-3">
                    <h5 class="mb-0 fw-bold">Đơn hàng của bạn</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush bg-transparent">
                        <?php 
                        $total = 0;
                        foreach ($_SESSION['cart'] as $item): 
                            $subtotal = $item['price'] * $item['quantity'];
                            $total += $subtotal;
                        ?>
                        <li class="list-group-item d-flex align-items-center bg-transparent py-3">
                            <div class="position-relative me-3">
                                <?php if(!empty($item['image'])): ?>
                                    <img src="uploads/<?php echo $item['image']; ?>" class="rounded border" style="width: 60px; height: 60px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="bg-white rounded border d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                        <i class="bi bi-image text-muted"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-secondary">
                                    <?php echo $item['quantity']; ?>
                                </span>
                            </div>

                            <div class="flex-grow-1">
                                <h6 class="my-0 small fw-bold text-dark"><?php echo $item['name']; ?></h6>
                                <small class="text-muted"><?php echo number_format($item['price'], 0, ',', '.'); ?>đ</small>
                            </div>

                            <span class="text-dark fw-bold small"><?php echo number_format($subtotal, 0, ',', '.'); ?>đ</span>
                        </li>
                        <?php endforeach; ?>
                        
                        <li class="list-group-item d-flex justify-content-between bg-white py-4 border-top">
                            <span class="fw-bold">Tổng thanh toán:</span>
                            <strong class="text-danger fs-4"><?php echo number_format($total, 0, ',', '.'); ?>đ</strong>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="mt-3 text-center">
                <a href="cart.php" class="text-decoration-none text-muted small"><i class="bi bi-chevron-left"></i> Quay lại giỏ hàng</a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>