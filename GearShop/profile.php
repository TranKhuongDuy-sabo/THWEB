<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'includes/db.php';
include 'includes/header.php';

// 1. Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Vui lòng đăng nhập!'); window.location='login.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];
$msg = "";

// 2. Xử lý Cập nhật thông tin
if (isset($_POST['update_profile'])) {
    $fullname = $_POST['fullname'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $email = $_POST['email'];

    $sql = "UPDATE Users SET FullName=?, Phone=?, Address=?, Email=? WHERE UserID=?";
    $stmt = $conn->prepare($sql);
    if ($stmt->execute([$fullname, $phone, $address, $email, $user_id])) {
        // Cập nhật lại session tên
        $_SESSION['fullname'] = $fullname;
        $msg = "<div class='alert alert-success'>Cập nhật thông tin thành công!</div>";
    } else {
        $msg = "<div class='alert alert-danger'>Có lỗi xảy ra!</div>";
    }
}

// 3. Xử lý Đổi mật khẩu
if (isset($_POST['change_pass'])) {
    $old_pass = $_POST['old_pass'];
    $new_pass = $_POST['new_pass'];
    $confirm_pass = $_POST['confirm_pass'];

    // Kiểm tra mật khẩu cũ
    $stmt = $conn->prepare("SELECT Password FROM Users WHERE UserID = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    // Lưu ý: Nếu bạn dùng md5 hoặc password_verify thì sửa dòng này cho khớp logic cũ
    if ($user['Password'] == $old_pass) { 
        if ($new_pass == $confirm_pass) {
            $conn->prepare("UPDATE Users SET Password=? WHERE UserID=?")->execute([$new_pass, $user_id]);
            $msg = "<div class='alert alert-success'>Đổi mật khẩu thành công!</div>";
        } else {
            $msg = "<div class='alert alert-danger'>Mật khẩu xác nhận không khớp!</div>";
        }
    } else {
        $msg = "<div class='alert alert-danger'>Mật khẩu cũ không đúng!</div>";
    }
}

// 4. Lấy thông tin user hiện tại để điền vào form
$stmt = $conn->prepare("SELECT * FROM Users WHERE UserID = ?");
$stmt->execute([$user_id]);
$currentUser = $stmt->fetch();
?>

<div class="container py-5">
    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="list-group shadow-sm">
                <a href="profile.php" class="list-group-item list-group-item-action active fw-bold"><i class="bi bi-person-circle me-2"></i> Hồ sơ cá nhân</a>
                <a href="my_orders.php" class="list-group-item list-group-item-action"><i class="bi bi-bag-check me-2"></i> Đơn mua của tôi</a>
                <a href="logout.php" class="list-group-item list-group-item-action text-danger"><i class="bi bi-box-arrow-right me-2"></i> Đăng xuất</a>
            </div>
        </div>

        <div class="col-md-9">
            <h3 class="fw-bold mb-4">Hồ sơ của tôi</h3>
            <?php echo $msg; ?>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white fw-bold py-3">Thông tin tài khoản</div>
                <div class="card-body">
                    <form method="POST">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Tên đăng nhập</label>
                                <input type="text" class="form-control bg-light" value="<?php echo $currentUser['Username']; ?>" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Họ và tên</label>
                                <input type="text" name="fullname" class="form-control" value="<?php echo $currentUser['FullName']; ?>" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Email</label>
                            <input type="email" name="email" class="form-control" value="<?php echo $currentUser['Email']; ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Số điện thoại</label>
                            <input type="text" name="phone" class="form-control" value="<?php echo $currentUser['Phone']; ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Địa chỉ (Mặc định)</label>
                            <textarea name="address" class="form-control" rows="2"><?php echo $currentUser['Address']; ?></textarea>
                        </div>
                        <button type="submit" name="update_profile" class="btn btn-primary rounded-pill px-4">Lưu thay đổi</button>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-bold py-3 text-danger">Đổi mật khẩu</div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Mật khẩu cũ</label>
                            <input type="password" name="old_pass" class="form-control" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Mật khẩu mới</label>
                                <input type="password" name="new_pass" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Xác nhận mật khẩu mới</label>
                                <input type="password" name="confirm_pass" class="form-control" required>
                            </div>
                        </div>
                        <button type="submit" name="change_pass" class="btn btn-outline-danger rounded-pill px-4">Đổi mật khẩu</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>