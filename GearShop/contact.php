<?php
// Kiểm tra session trước khi include header (để tránh lỗi nếu header có session_start)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// include 'includes/db.php'; // Nếu muốn lưu tin nhắn vào DB thì mở dòng này
include 'includes/header.php';

// --- XỬ LÝ GỬI LIÊN HỆ ---
$msg_success = "";
if (isset($_POST['btn_send'])) {
    // Ở đây bạn có thể code thêm phần Lưu vào Database (Bảng Feedbacks) nếu muốn
    // Hiện tại mình làm giả lập là gửi thành công nhé
    $name = $_POST['name'];
    $msg_success = "Cảm ơn <strong>$name</strong> đã liên hệ! Chúng tôi sẽ phản hồi sớm nhất.";
}
?>

<div class="container py-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">Trang chủ</a></li>
            <li class="breadcrumb-item active" aria-current="page">Liên hệ</li>
        </ol>
    </nav>

    <div class="row g-5">
        <div class="col-lg-6">
            <h2 class="fw-bold text-uppercase mb-4">Thông Tin Liên Hệ</h2>
            <p class="text-muted mb-4">
                Nếu bạn có thắc mắc về sản phẩm hoặc cần hỗ trợ kỹ thuật, hãy liên hệ với GearShop qua các kênh dưới đây.
            </p>

            <div class="d-flex mb-3">
                <div class="flex-shrink-0 btn-square bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="bi bi-geo-alt-fill fs-4"></i>
                </div>
                <div class="ms-3">
                    <h5 class="fw-bold mb-1">Địa chỉ cửa hàng</h5>
                    <p class="text-muted mb-0">123 Đường Số 1, Quận 1, TP. Hồ Chí Minh</p>
                </div>
            </div>

            <div class="d-flex mb-3">
                <div class="flex-shrink-0 btn-square bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="bi bi-telephone-fill fs-4"></i>
                </div>
                <div class="ms-3">
                    <h5 class="fw-bold mb-1">Hotline hỗ trợ</h5>
                    <p class="text-muted mb-0">0909.123.456 (8:00 - 21:00)</p>
                </div>
            </div>

            <div class="d-flex mb-4">
                <div class="flex-shrink-0 btn-square bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="bi bi-envelope-fill fs-4"></i>
                </div>
                <div class="ms-3">
                    <h5 class="fw-bold mb-1">Email</h5>
                    <p class="text-muted mb-0">hotro@gearshop.vn</p>
                </div>
            </div>

            <div class="rounded overflow-hidden shadow-sm">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.424668102372!2d106.6876672748825!3d10.778747489370258!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752f2fa9165b4b%3A0x6730722340356555!2zRGluaCDEkOG7mWMgTOG6rXA!5e0!3m2!1svi!2s!4v1700000000000!5m2!1svi!2s" 
                    width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy">
                </iframe>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-lg p-4">
                <h3 class="fw-bold text-primary mb-4">Gửi Tin Nhắn</h3>
                
                <?php if($msg_success): ?>
                    <div class="alert alert-success d-flex align-items-center">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <div><?php echo $msg_success; ?></div>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Họ và tên</label>
                            <input type="text" name="name" class="form-control" placeholder="Nhập tên của bạn" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="example@email.com" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small">Tiêu đề</label>
                            <input type="text" name="subject" class="form-control" placeholder="Vấn đề cần hỗ trợ..." required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small">Nội dung tin nhắn</label>
                            <textarea name="message" class="form-control" rows="5" placeholder="Viết nội dung tại đây..." required></textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" name="btn_send" class="btn btn-primary w-100 py-3 rounded-pill fw-bold">
                                <i class="bi bi-send-fill me-2"></i> Gửi Ngay
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>