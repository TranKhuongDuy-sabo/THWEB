<?php
session_start();
include 'includes/header.php'; 
?>

<div class="container py-5 text-center" style="min-height: 60vh; display: flex; flex-direction: column; justify-content: center; align-items: center;">
    
    <div class="mb-4">
        <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
    </div>

    <h1 class="fw-bold text-success mb-3">ĐẶT HÀNG THÀNH CÔNG!</h1>
    
    <p class="lead text-muted mb-4" style="max-width: 600px;">
        Cảm ơn bạn đã mua sắm tại GearShop. Đơn hàng của bạn đang được xử lý và sẽ sớm được giao đến địa chỉ của bạn.
    </p>

    <div class="d-flex gap-3 justify-content-center">
        <a href="index.php" class="btn btn-outline-secondary rounded-pill px-4 py-2 fw-bold">
            <i class="bi bi-house-door-fill me-2"></i> Về trang chủ
        </a>
        <a href="my_orders.php" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow">
            <i class="bi bi-box-seam me-2"></i> Xem đơn hàng của tôi
        </a>
    </div>

</div>

<?php include 'includes/footer.php'; ?>