<?php
$host = 'localhost';
$dbname = 'gearshop'; // Đảm bảo đúng tên database trong phpMyAdmin
$username = 'root';
$password = '';

try {
    // Biến $conn được tạo ở đây
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Lỗi kết nối: " . $e->getMessage());
}
?>