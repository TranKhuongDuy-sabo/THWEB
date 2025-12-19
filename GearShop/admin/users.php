<?php
session_start();
// Kiểm tra session admin (tùy code login của bạn)
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../login.php");
    exit();
}

include '../includes/db.php';
include 'includes/header.php';

// --- 1. XỬ LÝ XÓA NGƯỜI DÙNG ---
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    if ($id == 1) {
        echo "<script>alert('Không thể xóa tài khoản Admin gốc!'); window.location='users.php';</script>";
        exit;
    }
    try {
        $conn->prepare("DELETE FROM Users WHERE UserID = ?")->execute([$id]);
        echo "<script>alert('Đã xóa người dùng thành công!'); window.location='users.php';</script>";
    } catch (Exception $e) {
        echo "<script>alert('Lỗi: " . $e->getMessage() . "');</script>";
    }
}

// --- 2. XỬ LÝ ĐỔI QUYỀN (MỚI THÊM) ---
if (isset($_POST['update_role'])) {
    $user_id = $_POST['user_id'];
    $new_role = $_POST['role_value']; // Giá trị nhận được: 'ADMIN' hoặc 'CUSTOMER'

    // Không cho phép đổi quyền của ông trùm ID = 1
    if ($user_id == 1) {
         echo "<script>alert('Không thể hạ quyền Admin gốc!');</script>";
    } else {
        try {
            $stmt = $conn->prepare("UPDATE Users SET Role = ? WHERE UserID = ?");
            $stmt->execute([$new_role, $user_id]);
            // Refresh lại trang để thấy thay đổi
            echo "<script>window.location='users.php';</script>";
        } catch (Exception $e) {
            echo "<script>alert('Lỗi cập nhật: " . $e->getMessage() . "');</script>";
        }
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-primary fw-bold text-uppercase m-0">Quản Lý Người Dùng</h2>
</div>

<div class="card shadow-lg border-0 rounded-4 w-100">
    <div class="card-header bg-white py-3">
        <h6 class="m-0 fw-bold text-secondary"><i class="bi bi-people-fill me-2"></i>Danh Sách Tài Khoản</h6>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0" style="width: 100%;">
                <thead class="table-dark">
                    <tr>
                        <th class="ps-4 py-3">ID</th>
                        <th class="py-3">Họ Tên</th>
                        <th class="py-3">Tài Khoản / Email</th>
                        <th class="py-3">Phân Quyền (Role)</th> <th class="py-3">Thông Tin Liên Hệ</th>
                        <th class="py-3 text-end pe-4">Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Lấy danh sách Users, sắp xếp Admin lên trước
                    $sql = "SELECT * FROM Users ORDER BY Role ASC, UserID DESC";
                    $stmt = $conn->query($sql);

                    while ($row = $stmt->fetch()) {
                        // Xác định màu Role
                        $is_admin = ($row['Role'] == 'ADMIN');
                    ?>
                        <tr class="border-bottom">
                            <td class="ps-4 fw-bold text-muted">#<?php echo $row['UserID']; ?></td>

                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-light d-flex justify-content-center align-items-center text-secondary border me-3"
                                         style="width: 45px; height: 45px; font-size: 20px;">
                                        <i class="bi bi-person-fill"></i>
                                    </div>
                                    <span class="fw-bold fs-5 text-dark"><?php echo $row['FullName']; ?></span>
                                </div>
                            </td>

                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-primary"><i class="bi bi-person-badge me-1"></i> <?php echo $row['Username']; ?></span>
                                    <span class="small text-muted"><i class="bi bi-envelope me-1"></i> <?php echo $row['Email']; ?></span>
                                </div>
                            </td>

                            <td>
                                <?php if ($row['UserID'] == 1): ?>
                                    <span class="badge bg-danger px-3 py-2 rounded-pill shadow-sm">ADMIN (Gốc)</span>
                                <?php else: ?>
                                    <form method="POST" class="d-flex align-items-center gap-2">
                                        <input type="hidden" name="user_id" value="<?php echo $row['UserID']; ?>">
                                        
                                        <select name="role_value" class="form-select form-select-sm fw-bold <?php echo $is_admin ? 'text-danger border-danger' : 'text-success border-success'; ?>" 
                                                style="width: 130px;" onchange="this.form.submit()"> <option value="CUSTOMER" <?php if(!$is_admin) echo 'selected'; ?>>Khách hàng</option>
                                            <option value="ADMIN" <?php if($is_admin) echo 'selected'; ?>>Quản trị viên</option>
                                        </select>
                                        <input type="hidden" name="update_role" value="1">
                                    </form>
                                <?php endif; ?>
                            </td>

                            <td>
                                <div class="text-secondary small">
                                    <div class="mb-1"><i class="bi bi-telephone-fill me-1"></i> <?php echo $row['Phone'] ?? '---'; ?></div>
                                    <div><i class="bi bi-geo-alt-fill me-1"></i> <?php echo $row['Address'] ?? 'Chưa cập nhật'; ?></div>
                                </div>
                            </td>

                            <td class="text-end pe-4">
                                <?php if ($row['UserID'] != 1): ?>
                                    <a href="users.php?delete_id=<?php echo $row['UserID']; ?>"
                                       class="btn btn-outline-danger btn-sm shadow-sm"
                                       onclick="return confirm('CẢNH BÁO: Xóa người dùng này sẽ xóa toàn bộ Đơn hàng của họ!\nBạn có chắc chắn không?');">
                                        <i class="bi bi-trash"></i> Xóa
                                    </a>
                                <?php else: ?>
                                    <button class="btn btn-light btn-sm text-muted border" disabled><i class="bi bi-lock-fill"></i></button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>