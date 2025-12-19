-- 1. TẠO DATABASE
CREATE DATABASE IF NOT EXISTS gearshop_pro CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gearshop_pro;

-- 2. BẢNG THƯƠNG HIỆU (Brands)
CREATE TABLE Brands (
    BrandID INT AUTO_INCREMENT PRIMARY KEY,
    BrandName VARCHAR(100) NOT NULL,
    Origin VARCHAR(100)
);

-- 3. BẢNG DANH MỤC (Categories)
CREATE TABLE Categories (
    CategoryID INT AUTO_INCREMENT PRIMARY KEY,
    CategoryName VARCHAR(100) NOT NULL
);

-- 4. BẢNG NGƯỜI DÙNG (Users) - Đã bỏ ngày đăng ký
CREATE TABLE Users (
    UserID INT AUTO_INCREMENT PRIMARY KEY,
    Username VARCHAR(50) NOT NULL UNIQUE,
    Password VARCHAR(255) NOT NULL,
    Role VARCHAR(20) DEFAULT 'CUSTOMER', 
    FullName VARCHAR(100),
    Email VARCHAR(100),
    Phone VARCHAR(20),
    Address VARCHAR(255)
);

-- 5. BẢNG SẢN PHẨM (Products) - Chỉ chứa thông tin chung
CREATE TABLE Products (
    ProductID INT AUTO_INCREMENT PRIMARY KEY,
    ProductName VARCHAR(255) NOT NULL,
    CategoryID INT,
    BrandID INT,
    Price DECIMAL(18, 0) NOT NULL,
    Stock INT DEFAULT 0,
    Image TEXT,
    Description TEXT, -- Bài viết giới thiệu chung
    
    FOREIGN KEY (CategoryID) REFERENCES Categories(CategoryID) ON DELETE SET NULL,
    FOREIGN KEY (BrandID) REFERENCES Brands(BrandID) ON DELETE SET NULL
);

-- 6. BẢNG THÔNG SỐ KỸ THUẬT (ProductSpecs) - BẢNG MỚI BẠN CẦN
-- Bảng này lưu chi tiết: 1 Sản phẩm có thể có nhiều dòng thông số
CREATE TABLE ProductSpecs (
    SpecID INT AUTO_INCREMENT PRIMARY KEY,
    ProductID INT,              -- Thuộc về sản phẩm nào
    SpecName VARCHAR(100),      -- Tên thông số (VD: CPU, RAM, DPI)
    SpecValue VARCHAR(255),     -- Giá trị (VD: Core i7, 16GB, 8000 DPI)
    
    FOREIGN KEY (ProductID) REFERENCES Products(ProductID) ON DELETE CASCADE
);

-- 7. BẢNG ĐƠN HÀNG (Orders)
CREATE TABLE Orders (
    OrderID INT AUTO_INCREMENT PRIMARY KEY,
    UserID INT,
    OrderDate DATETIME DEFAULT CURRENT_TIMESTAMP,
    TotalAmount DECIMAL(18, 0),
    Status VARCHAR(50) DEFAULT 'Đang xử lý',
    ShippingAddress VARCHAR(255),
    
    FOREIGN KEY (UserID) REFERENCES Users(UserID) ON DELETE CASCADE
);

-- 8. BẢNG CHI TIẾT ĐƠN (OrderDetails)
CREATE TABLE OrderDetails (
    DetailID INT AUTO_INCREMENT PRIMARY KEY,
    OrderID INT,
    ProductID INT,
    Quantity INT NOT NULL,
    Price DECIMAL(18, 0),
    
    FOREIGN KEY (OrderID) REFERENCES Orders(OrderID) ON DELETE CASCADE,
    FOREIGN KEY (ProductID) REFERENCES Products(ProductID)
);