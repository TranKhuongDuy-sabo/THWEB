<?php
// Kiểm tra session (nếu chưa có thì mở)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'includes/db.php';
include 'includes/header.php';

// 1. XỬ LÝ XÓA SẢN PHẨM
if (isset($_GET['delete_id'])) {
    $del_id = $_GET['delete_id'];
    unset($_SESSION['cart'][$del_id]);
    echo "<script>window.location='cart.php';</script>";
}

// 2. XỬ LÝ CẬP NHẬT SỐ LƯỢNG (Khi bấm nút Cập nhật)
if (isset($_POST['update_cart'])) {
    foreach ($_POST['qty'] as $id => $new_qty) {
        if ($new_qty <= 0) {
            unset($_SESSION['cart'][$id]); // Nếu nhập <= 0 thì xóa luôn
        } else {
            $_SESSION['cart'][$id]['quantity'] = $new_qty;
        }
    }
    echo "<script>window.location='cart.php';</script>";
}

// 3. XỬ LÝ THANH TOÁN (Chuyển hướng sang checkout)
if (isset($_POST['checkout'])) {
    if (!empty($_SESSION['cart'])) {
        header("Location: checkout.php");
        exit();
    } else {
        echo "<script>alert('Giỏ hàng trống!');</script>";
    }
}
?>

<div class="container py-5">
    <h2 class="text-uppercase fw-bold mb-4">Giỏ hàng của bạn</h2>

    <?php if (empty($_SESSION['cart'])): ?>
        <div class="alert alert-warning text-center">
            <i class="bi bi-cart-x fs-1"></i>
            <p class="mt-3">Giỏ hàng đang trống!</p>
            <a href="index.php" class="btn btn-primary rounded-pill">Mua sắm ngay</a>
        </div>
    <?php else: ?>

        <form method="POST" action="">
            <div class="table-responsive bg-white shadow-sm rounded p-3">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Đơn giá</th>
                            <th style="width: 150px;">Số lượng</th>
                            <th>Thành tiền</th>
                            <th>Xóa</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total_money = 0;
                        foreach ($_SESSION['cart'] as $id => $item): 
                            // TÍNH TOÁN TIỀN (Đảm bảo kiểu dữ liệu là số)
                            $price = (float)$item['price']; 
                            $qty = (int)$item['quantity'];
                            $subtotal = $price * $qty;
                            $total_money += $subtotal;
                        ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <?php if (!empty($item['image'])): ?>
                                        <img src="uploads/<?php echo $item['image']; ?>" style="width: 60px; height: 60px; object-fit: cover;" class="rounded me-3">
                                    <?php else: ?>
                                        <div class="bg-light d-flex align-items-center justify-content-center rounded me-3" style="width: 60px; height: 60px;">
                                            <i class="bi bi-image text-secondary"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <h6 class="m-0 fw-bold"><?php echo $item['name']; ?></h6>
                                    </div>
                                </div>
                            </td>
                            <td><?php echo number_format($price, 0, ',', '.'); ?>đ</td>
                            <td>
                                <input type="number" name="qty[<?php echo $id; ?>]" value="<?php echo $qty; ?>" min="1" class="form-control form-control-sm text-center">
                            </td>
                            <td class="fw-bold text-primary"><?php echo number_format($subtotal, 0, ',', '.'); ?>đ</td>
                            <td>
                                <a href="cart.php?delete_id=<?php echo $id; ?>" class="btn btn-sm btn-outline-danger border-0" onclick="return confirm('Bạn chắc chắn muốn xóa?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="row mt-4">
                <div class="col-md-6">
                    <a href="./listProducts.php" class="btn btn-outline-secondary rounded-pill"><i class="bi bi-arrow-left"></i> Tiếp tục mua sắm</a>
                </div>
                <div class="col-md-6 text-end">
                    <h4 class="fw-bold">Tổng tiền: <span class="text-danger"><?php echo number_format($total_money, 0, ',', '.'); ?>đ</span></h4>
                    
                    <button type="submit" name="update_cart" class="btn btn-warning rounded-pill text-white fw-bold me-2">
                        <i class="bi bi-arrow-clockwise"></i> Cập nhật giỏ
                    </button>
                    
                    <a href="checkout.php" class="btn btn-success rounded-pill fw-bold px-4 py-2">
                        Tiến hành thanh toán <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </form>

    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>