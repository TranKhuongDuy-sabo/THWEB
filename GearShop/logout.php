<?php
session_start();
session_unset();
session_destroy();

// Đăng xuất xong thì quay lại trang Login (ngang hàng)
header("Location: login.php");
exit();
?>