-- ============================================================
-- DevicesVN E-Commerce Database
-- Simple but Complete - 8 Essential Tables
-- ============================================================

CREATE DATABASE IF NOT EXISTS devicesvn CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE devicesvn;

-- ============================================================
-- 1. users - Customer and admin accounts
-- ============================================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(60) NOT NULL,
    phone VARCHAR(20) NULL,
    address TEXT NULL,
    role ENUM('customer', 'admin') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- 2. categories - Product categories
-- ============================================================
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT NULL,
    parent_id INT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- 3. products - Product catalog
-- ============================================================
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT NULL,
    price DECIMAL(10, 2) NOT NULL,
    sale_price DECIMAL(10, 2) NULL,
    sku VARCHAR(50) NULL,
    stock_quantity INT DEFAULT 0,
    brand VARCHAR(100) NULL,
    specifications JSON NULL,
    image_url VARCHAR(255) NULL,
    is_featured BOOLEAN DEFAULT FALSE,
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- 4. stores - Physical store locations
-- ============================================================
CREATE TABLE stores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    address TEXT NOT NULL,
    city VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NULL,
    latitude DECIMAL(10, 8) NULL,
    longitude DECIMAL(11, 8) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- 5. orders - Customer orders
-- ============================================================
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    order_number VARCHAR(50) NOT NULL UNIQUE,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'confirmed', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    shipping_name VARCHAR(100) NOT NULL,
    shipping_address TEXT NOT NULL,
    shipping_phone VARCHAR(20) NOT NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- 6. order_items - Items in each order
-- ============================================================
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    product_price DECIMAL(10, 2) NOT NULL,
    quantity INT NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- 7. reviews - Product reviews
-- ============================================================
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    rating TINYINT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- 8. cart - Shopping cart
-- ============================================================
CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    session_id VARCHAR(100) NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- SAMPLE DATA
-- ============================================================

-- Categories
INSERT INTO categories (name, slug, description, parent_id, is_active, sort_order) VALUES
('Laptops', 'laptops', 'Browse our selection of laptops', NULL, TRUE, 1),
('Gaming Laptops', 'gaming-laptops', 'High-performance gaming laptops', NULL, TRUE, 2),
('Phones', 'phones', 'Latest smartphones', NULL, TRUE, 3),
('Tablets', 'tablets', 'Tablets and iPads', NULL, TRUE, 4),
('Accessories', 'accessories', 'Computer and phone accessories', NULL, TRUE, 5),
('Mice', 'mice', 'Gaming and office mice', 5, TRUE, 6),
('Keyboards', 'keyboards', 'Mechanical and wireless keyboards', 5, TRUE, 7),
('Headphones', 'headphones', 'Gaming and music headphones', 5, TRUE, 8);

-- Admin User (password: admin123)
-- Password security: HMAC-SHA256(password, pepper) + BCrypt(cost=12)
INSERT INTO users (full_name, email, password, role) VALUES
('DevicesVN Admin', 'admin@devicesvn.com', '$2y$12$aGAdLHr01usCVh.Cwl4ZnOq7UKhf0t6XvRKUvb8x9S9.jyma6Ja.C', 'admin');

-- Stores
INSERT INTO stores (name, slug, address, city, phone, latitude, longitude) VALUES
('DevicesVN Hanoi', 'hanoi-store', '123 Nguyen Trai, Thanh Xuan', 'Hanoi', '024-1234-5678', 21.0285, 105.8542),
('DevicesVN Ho Chi Minh', 'hcm-store', '456 Le Loi, District 1', 'Ho Chi Minh', '028-9876-5432', 10.7769, 106.7009),
('DevicesVN Da Nang', 'danang-store', '789 Bach Dang, Hai Chau', 'Da Nang', '0236-111-2222', 16.0544, 108.2022);

-- Products
INSERT INTO products (category_id, name, slug, description, price, sale_price, sku, stock_quantity, brand, specifications, image_url, is_featured) VALUES
-- Laptops
(1, 'Dell XPS 13 (2024)', 'dell-xps-13-2024', 
'Ultra-portable 13-inch laptop with stunning InfinityEdge display and powerful Intel Core i7 processor', 
32990000, NULL, 'LAP-DELL-XPS13', 15, 'Dell',
'{"CPU": "Intel Core i7-1355U", "RAM": "16GB", "Storage": "512GB SSD", "Display": "13.4-inch FHD+", "Weight": "1.19kg"}',
'/images/products/dell-xps-13.jpg', TRUE),

(1, 'MacBook Air M2', 'macbook-air-m2',
'Supercharged by M2 chip with incredible battery life and stunning Retina display',
29990000, 27990000, 'LAP-APPLE-MBA-M2', 20, 'Apple',
'{"CPU": "Apple M2", "RAM": "8GB", "Storage": "256GB SSD", "Display": "13.6-inch Retina", "Weight": "1.24kg"}',
'/images/products/macbook-air-m2.jpg', TRUE),

(1, 'HP Pavilion 15', 'hp-pavilion-15',
'Reliable everyday laptop with good performance and affordable price',
18990000, 16990000, 'LAP-HP-PAV15', 25, 'HP',
'{"CPU": "Intel Core i5-1235U", "RAM": "8GB", "Storage": "512GB SSD", "Display": "15.6-inch FHD", "Weight": "1.75kg"}',
'/images/products/hp-pavilion-15.jpg', FALSE),

-- Gaming Laptops
(2, 'ASUS ROG Strix G16', 'asus-rog-strix-g16',
'Dominate the competition with RTX 4060 graphics and 165Hz display',
42990000, 39990000, 'GAM-ASUS-ROG-G16', 10, 'ASUS',
'{"CPU": "Intel Core i7-13650HX", "RAM": "16GB DDR5", "Storage": "512GB SSD", "GPU": "RTX 4060 8GB", "Display": "16-inch FHD 165Hz"}',
'/images/products/asus-rog-g16.jpg', TRUE),

(2, 'Acer Predator Helios 300', 'acer-predator-helios-300',
'Excellent gaming performance with advanced cooling technology',
35990000, NULL, 'GAM-ACER-PH300', 8, 'Acer',
'{"CPU": "Intel Core i7-12700H", "RAM": "16GB", "Storage": "1TB SSD", "GPU": "RTX 3060 6GB", "Display": "15.6-inch FHD 144Hz"}',
'/images/products/acer-predator.jpg', TRUE),

(2, 'MSI Katana 15', 'msi-katana-15',
'Budget gaming laptop with solid performance for 1080p gaming',
28990000, 26990000, 'GAM-MSI-KAT15', 12, 'MSI',
'{"CPU": "Intel Core i5-12450H", "RAM": "16GB", "Storage": "512GB SSD", "GPU": "RTX 3050 4GB", "Display": "15.6-inch FHD 144Hz"}',
'/images/products/msi-katana.jpg', FALSE),

-- Phones
(3, 'iPhone 15 Pro Max', 'iphone-15-pro-max',
'Titanium design, A17 Pro chip, and advanced camera system',
34990000, NULL, 'PHN-APPLE-15PM', 25, 'Apple',
'{"Display": "6.7-inch Super Retina XDR", "Chip": "A17 Pro", "Camera": "48MP", "Storage": "256GB", "Battery": "Up to 29 hours"}',
'/images/products/iphone-15-pro.jpg', TRUE),

(3, 'Samsung Galaxy S24 Ultra', 'samsung-galaxy-s24-ultra',
'Galaxy AI features with 200MP camera and S Pen built-in',
31990000, 29990000, 'PHN-SAMSUNG-S24U', 30, 'Samsung',
'{"Display": "6.8-inch AMOLED 120Hz", "Processor": "Snapdragon 8 Gen 3", "Camera": "200MP", "RAM": "12GB", "Battery": "5000mAh"}',
'/images/products/samsung-s24.jpg', TRUE),

(3, 'Xiaomi 14 Pro', 'xiaomi-14-pro',
'Flagship specs at competitive price with Leica camera',
22990000, 20990000, 'PHN-XIAOMI-14PRO', 35, 'Xiaomi',
'{"Display": "6.73-inch AMOLED 120Hz", "Processor": "Snapdragon 8 Gen 3", "Camera": "50MP Leica", "RAM": "12GB", "Battery": "4880mAh"}',
'/images/products/xiaomi-14-pro.jpg', FALSE),

-- Tablets
(4, 'iPad Pro 11-inch M2', 'ipad-pro-11-m2',
'Powerful M2 chip with Apple Pencil support for creative work',
21990000, NULL, 'TAB-APPLE-IPADPRO11', 12, 'Apple',
'{"Display": "11-inch Liquid Retina", "Chip": "M2", "Storage": "128GB", "Camera": "12MP", "Battery": "Up to 10 hours"}',
'/images/products/ipad-pro-11.jpg', TRUE),

(4, 'Samsung Galaxy Tab S9', 'samsung-galaxy-tab-s9',
'Premium Android tablet with S Pen included and IP68 rating',
18990000, 17490000, 'TAB-SAMSUNG-S9', 15, 'Samsung',
'{"Display": "11-inch AMOLED 120Hz", "Processor": "Snapdragon 8 Gen 2", "Storage": "128GB", "RAM": "8GB", "Battery": "8400mAh"}',
'/images/products/samsung-tab-s9.jpg', FALSE),

-- Accessories - Mice
(6, 'Logitech G502 HERO', 'logitech-g502-hero',
'Gaming mouse with HERO 25K sensor and 11 programmable buttons',
1290000, 990000, 'ACC-MOUSE-G502', 50, 'Logitech',
'{"Sensor": "HERO 25K", "DPI": "25,600", "Buttons": "11", "Weight": "121g", "RGB": "Yes"}',
'/images/products/logitech-g502.jpg', FALSE),

(6, 'Razer DeathAdder V3', 'razer-deathadder-v3',
'Ergonomic gaming mouse with Focus Pro 30K sensor',
1790000, NULL, 'ACC-MOUSE-RAZ-DA3', 40, 'Razer',
'{"Sensor": "Focus Pro 30K", "DPI": "30,000", "Buttons": "8", "Weight": "59g", "RGB": "Yes"}',
'/images/products/razer-deathadder.jpg', FALSE),

-- Accessories - Keyboards
(7, 'Razer BlackWidow V3', 'razer-blackwidow-v3',
'Mechanical gaming keyboard with Green switches and RGB',
2990000, NULL, 'ACC-KB-RBW3', 35, 'Razer',
'{"Switch": "Razer Green", "Layout": "Full-size", "RGB": "Chroma", "Wrist Rest": "Yes"}',
'/images/products/razer-blackwidow.jpg', FALSE),

(7, 'Logitech G Pro X', 'logitech-g-pro-x',
'Tenkeyless mechanical keyboard with hot-swappable switches',
3290000, 2990000, 'ACC-KB-GPRO-X', 30, 'Logitech',
'{"Switch": "GX Swappable", "Layout": "TKL", "RGB": "LIGHTSYNC", "Cable": "Detachable"}',
'/images/products/logitech-gpro-x.jpg', FALSE),

(7, 'Keychron K8 Pro', 'keychron-k8-pro',
'Wireless mechanical keyboard with hot-swappable switches',
2490000, 2190000, 'ACC-KB-KEY-K8', 25, 'Keychron',
'{"Switch": "Gateron/Keychron", "Layout": "TKL", "Wireless": "Bluetooth 5.1", "Battery": "4000mAh"}',
'/images/products/keychron-k8.jpg', FALSE),

-- Accessories - Headphones
(8, 'Sony WH-1000XM5', 'sony-wh-1000xm5',
'Industry-leading noise cancelling with 30-hour battery life',
8990000, 7990000, 'ACC-HP-SONY-XM5', 25, 'Sony',
'{"Type": "Over-ear wireless", "ANC": "Yes", "Battery": "30 hours", "Driver": "30mm"}',
'/images/products/sony-xm5.jpg', TRUE),

(8, 'HyperX Cloud II', 'hyperx-cloud-ii',
'Gaming headset with 7.1 surround sound and memory foam',
2290000, NULL, 'ACC-HP-HYP-C2', 45, 'HyperX',
'{"Type": "Over-ear wired", "Surround": "7.1 Virtual", "Driver": "53mm", "Microphone": "Detachable"}',
'/images/products/hyperx-cloud2.jpg', FALSE),

(8, 'SteelSeries Arctis Nova Pro', 'steelseries-arctis-nova-pro',
'Premium gaming headset with active noise cancellation',
7990000, 7490000, 'ACC-HP-SS-NOVA', 20, 'SteelSeries',
'{"Type": "Over-ear wireless", "ANC": "Yes", "Battery": "44 hours", "Driver": "40mm"}',
'/images/products/steelseries-arctis.jpg', TRUE);

-- ============================================================
SELECT '✅ Database created successfully!' AS Status;
SELECT '📊 8 Tables | 19 Products | 3 Stores | 8 Categories' AS Summary;
SELECT '🔐 Login: admin@devicesvn.com / admin123' AS Credentials;
