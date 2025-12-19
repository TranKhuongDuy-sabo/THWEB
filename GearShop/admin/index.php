<?php
session_start();

// Kiểm tra: Chưa đăng nhập hoặc Không phải Admin
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../login.php");
    exit();
}
// 1. Kết nối Database
include '../includes/db.php';
include 'includes/header.php';

// --- XỬ LÝ SỐ LIỆU THỐNG KÊ (ĐÃ SỬA CỘT TOTALMONEY) ---
$totalProduct = $conn->query("SELECT COUNT(*) FROM Products")->fetchColumn();
$totalOrder = $conn->query("SELECT COUNT(*) FROM Orders")->fetchColumn();
$totalUser = $conn->query("SELECT COUNT(*) FROM Users WHERE Role = 'CUSTOMER'")->fetchColumn();

// ĐÃ SỬA: Dùng TotalMoney thay vì TotalAmount
$revenue = $conn->query("SELECT SUM(TotalMoney) FROM Orders")->fetchColumn();
if (!$revenue) $revenue = 0;
?>

<style>
    /* Card thống kê hiện đại */
    .card-box {
        position: relative;
        color: #fff;
        padding: 20px 10px 40px;
        margin: 20px 0px;
        border-radius: 15px;
        /* Bo tròn nhiều hơn */
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        /* Đổ bóng mềm */
        transition: transform 0.3s ease;
    }

    .card-box:hover {
        transform: translateY(-5px);
        /* Bay lên nhẹ khi hover */
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
    }

    .card-box .inner {
        padding: 5px 10px 0 10px;
        z-index: 2;
        position: relative;
    }

    .card-box h3 {
        font-size: 2.5rem;
        font-weight: 700;
        margin: 0 0 10px 0;
        white-space: nowrap;
        padding: 0;
    }

    .card-box p {
        font-size: 1rem;
        text-transform: uppercase;
        font-weight: 600;
        opacity: 0.8;
    }

    /* Icon chìm phía sau */
    .card-box .icon {
        position: absolute;
        top: auto;
        bottom: 5px;
        right: 5px;
        z-index: 0;
        font-size: 70px;
        color: rgba(0, 0, 0, 0.15);
        transition: all 0.3s;
    }

    .card-box:hover .icon {
        font-size: 80px;
        /* Phóng to icon khi hover */
        transform: rotate(-10deg);
    }

    .card-box .card-box-footer {
        position: absolute;
        left: 0px;
        bottom: 0px;
        text-align: center;
        padding: 3px 0;
        color: rgba(255, 255, 255, 0.8);
        background: rgba(0, 0, 0, 0.1);
        width: 100%;
        text-decoration: none;
        border-radius: 0 0 15px 15px;
    }

    .card-box .card-box-footer:hover {
        background: rgba(0, 0, 0, 0.3);
        color: #fff;
    }

    /* Màu Gradient đẹp mắt */
    .bg-blue {
        background: linear-gradient(45deg, #4099ff, #73b4ff);
    }

    .bg-green {
        background: linear-gradient(45deg, #2ed8b6, #59e0c5);
    }

    .bg-orange {
        background: linear-gradient(45deg, #FFB64D, #ffcb80);
    }

    .bg-red {
        background: linear-gradient(45deg, #FF5370, #ff869a);
    }

    /* Style cho bảng đơn hàng */
    .table-card {
        border-radius: 15px;
        overflow: hidden;
        border: none;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
    }

    .table-head-custom {
        background-color: #f8f9fa;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        color: #6c757d;
    }
</style>

<div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-4">
    <div>
        <h2 class="fw-bold text-dark">Dashboard</h2>
        <p class="text-muted">Tổng quan tình hình kinh doanh GearShop</p>
    </div>
</div>

<div class="row">
    <div class="col-lg-3 col-md-6">
        <div class="card-box bg-blue">
            <div class="inner">
                <h3><?php echo $totalProduct; ?></h3>
                <p>Sản Phẩm</p>
            </div>
            <div class="icon"><i class="bi bi-joystick"></i></div>
            <a href="product.php" class="card-box-footer">Xem chi tiết <i class="bi bi-arrow-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card-box bg-orange">
            <div class="inner">
                <h3><?php echo $totalOrder; ?></h3>
                <p>Đơn Hàng Mới</p>
            </div>
            <div class="icon"><i class="bi bi-cart3"></i></div>
            <a href="orders.php" class="card-box-footer">Quản lý đơn <i class="bi bi-arrow-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card-box bg-green">
            <div class="inner">
                <h3><?php echo $totalUser; ?></h3>
                <p>Khách Hàng</p>
            </div>
            <div class="icon"><i class="bi bi-people-fill"></i></div>
            <a href="users.php" class="card-box-footer">Xem danh sách <i class="bi bi-arrow-right"></i></a>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="card-box bg-red">
            <div class="inner">
                <h3><?php
                    // Format số gọn gàng
                    if ($revenue > 1000000000) echo round($revenue / 1000000000, 1) . 'B';
                    else if ($revenue > 1000000) echo round($revenue / 1000000, 1) . 'M';
                    else echo number_format($revenue);
                    ?></h3>
                <p>Doanh Thu (VNĐ)</p>
            </div>
            <div class="icon"><i class="bi bi-wallet2"></i></div>
            <a href="#" class="card-box-footer">Thực tế đã thu <i class="bi bi-arrow-right"></i></a>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card table-card">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-clock-history"></i> Đơn Hàng Vừa Đặt</h5>
                <a href="orders.php" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-head-custom">
                            <tr>
                                <th class="ps-4">Mã Đơn</th>
                                <th>Ngày Đặt</th>
                                <th>Khách Hàng</th>
                                <th>Tổng Tiền</th>
                                <th>Trạng Thái</th>
                                <th class="text-end pe-4">Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sqlRecent = "SELECT o.*, u.FullName 
                                          FROM Orders o 
                                          LEFT JOIN Users u ON o.UserID = u.UserID 
                                          ORDER BY o.OrderDate DESC LIMIT 5";
                            $stmtRecent = $conn->query($sqlRecent);

                            if ($stmtRecent->rowCount() == 0) {
                                echo "<tr><td colspan='6' class='text-center py-5 text-muted'><i class='bi bi-inbox fs-1'></i><br>Chưa có đơn hàng nào.</td></tr>";
                            }

                            while ($row = $stmtRecent->fetch()) {
                                $statusClass = match ($row['Status']) {
                                    'Pending', 'Mới', 'Đang xử lý' => 'bg-info text-dark',
                                    'Completed', 'Đã giao' => 'bg-success',
                                    'Cancelled', 'Hủy' => 'bg-danger',
                                    default => 'bg-secondary'
                                };
                            ?>
                                <tr>
                                    <td class="ps-4 fw-bold text-primary">#<?php echo $row['OrderID']; ?></td>
                                    <td class="text-muted small">
                                        <i class="bi bi-calendar3"></i> <?php echo date('d/m/Y', strtotime($row['OrderDate'])); ?>
                                        <br>
                                        <i class="bi bi-clock"></i> <?php echo date('H:i', strtotime($row['OrderDate'])); ?>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar bg-light rounded-circle text-center d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px;">
                                                <i class="bi bi-person text-secondary"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold"><?php echo $row['FullName'] ?? 'Khách vãng lai'; ?></div>
                                                <div class="small text-muted">ID: <?php echo $row['UserID']; ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td class="fw-bold text-danger"><?php echo number_format($row['TotalMoney'], 0, ',', '.'); ?>đ</td>
                                    
                                    <td><span class="badge rounded-pill <?php echo $statusClass; ?> px-3 py-2"><?php echo $row['Status']; ?></span></td>
                                    <td class="text-end pe-4">
                                        <a href="order_details.php?id=<?php echo $row['OrderID']; ?>" class="btn btn-sm btn-outline-secondary" title="Xem chi tiết">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>