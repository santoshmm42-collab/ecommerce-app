<?php
// Enable debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection - FIXED for port 3307
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'ecommerce';
$port = 3307;

// Create connection
$conn = new mysqli($host, $username, $password, $database, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset
$conn->set_charset("utf8mb4");

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Helper functions
function sanitize($data) {
    global $conn;
    return htmlspecialchars(strip_tags($conn->real_escape_string($data)));
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['admin_id']);
}

function displayAlert($type, $message) {
    return "<div class='alert alert-$type alert-dismissible fade show' role='alert'>
                $message
                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
            </div>";
}

// Initialize database with complete structure and data
function initializeDatabase() {
    global $conn;
    
    // Create categories table
    $conn->query("CREATE TABLE IF NOT EXISTS categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Create products table with proper foreign key
    $conn->query("CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(200) NOT NULL,
        description TEXT,
        category_id INT,
        price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        stock INT DEFAULT 0,
        image VARCHAR(255) DEFAULT 'default.jpg',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
    )");
    
    // Create users table
    $conn->query("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        phone VARCHAR(20),
        address TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Create orders table
    $conn->query("CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        total_amount DECIMAL(10,2) NOT NULL,
        status VARCHAR(50) DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");
    
    // Create admin_users table
    $conn->query("CREATE TABLE IF NOT EXISTS admin_users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        name VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Insert categories if empty
    $cat_count = $conn->query("SELECT COUNT(*) as count FROM categories")->fetch_assoc()['count'];
    if ($cat_count == 0) {
        $categories = [
            ['Electronics', 'Latest electronic devices and gadgets including smartphones, laptops, and accessories'],
            ['Fashion', 'Trendy clothing, shoes, and fashion accessories for men and women'],
            ['Home & Living', 'Home decor, furniture, kitchen appliances, and lifestyle products'],
            ['Sports & Fitness', 'Sports equipment, fitness gear, and athletic accessories'],
            ['Books & Media', 'Books, e-books, and digital media products'],
            ['Beauty & Health', 'Cosmetics, skincare, and health supplements'],
            ['Toys & Games', 'Toys, games, and entertainment products for all ages']
        ];
        
        foreach ($categories as $cat) {
            $stmt = $conn->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
            $stmt->bind_param("ss", $cat[0], $cat[1]);
            $stmt->execute();
        }
    }
    
    // Insert products if empty
    $prod_count = $conn->query("SELECT COUNT(*) as count FROM products")->fetch_assoc()['count'];
    if ($prod_count == 0) {
        $products = [
            // Electronics
            ['iPhone 15 Pro', 'Latest iPhone with advanced camera system and A17 Pro chip. Features titanium design, USB-C, and pro-grade camera.', 1, 119999.00, 50, 'iphone.jpg'],
            ['Samsung Galaxy S24 Ultra', 'Premium Android smartphone with S Pen, 200MP camera, and Galaxy AI features.', 1, 109999.00, 40, 'samsung.jpg'],
            ['MacBook Air M2', 'Ultra-thin laptop with M2 chip, 15-hour battery life, and stunning Liquid Retina display.', 1, 99999.00, 25, 'laptop.jpg'],
            ['iPad Pro 12.9', 'Professional tablet with M2 chip, ProMotion display, and Apple Pencil support.', 1, 89999.00, 30, 'tablet.jpg'],
            ['Sony WH-1000XM5', 'Industry-leading noise-canceling headphones with exceptional sound quality.', 1, 29999.00, 60, 'headphones.jpg'],
            ['Apple Watch Series 9', 'Advanced smartwatch with health monitoring, fitness tracking, and cellular connectivity.', 1, 44999.00, 45, 'watch.jpg'],
            
            // Fashion
            ['Nike Air Max 270', 'Popular running shoes with Max Air unit for exceptional cushioning and style.', 2, 12999.00, 80, 'shoes.jpg'],
            ['Adidas Ultraboost 22', 'Premium running shoes with responsive Boost midsole and adaptive fit.', 2, 15999.00, 70, 'running.jpg'],
            ['Levi\'s 501 Original Jeans', 'Classic straight-fit denim jeans with iconic button-fly design.', 2, 4999.00, 120, 'jeans.jpg'],
            ['Nike Dri-FIT T-Shirt', 'Moisture-wicking performance t-shirt for sports and casual wear.', 2, 1999.00, 200, 'tshirt.jpg'],
            ['Ray-Ban Aviator', 'Classic pilot-style sunglasses with premium lenses and timeless design.', 2, 8999.00, 60, 'sunglasses.jpg'],
            ['Coach Leather Wallet', 'Genuine leather bifold wallet with multiple card slots and RFID protection.', 2, 7999.00, 50, 'wallet.jpg'],
            
            // Home & Living
            ['IKEA SOFAROG Sofa', 'Modern 3-seater sofa with removable covers and storage compartment.', 3, 24999.00, 15, 'sofa.jpg'],
            ['Philips 55" 4K Smart TV', 'Ultra HD LED TV with Android TV, Dolby Vision, and Ambilight.', 3, 44999.00, 25, 'tv.jpg'],
            ['Dyson V15 Vacuum', 'Cordless vacuum cleaner with laser detection and advanced filtration.', 3, 54999.00, 20, 'vacuum.jpg'],
            ['Nespresso Coffee Machine', 'Automatic espresso machine with one-touch brewing and milk frother.', 3, 19999.00, 40, 'coffee.jpg'],
            ['Xiaomi Air Purifier', 'HEPA air purifier with smart controls and real-time air quality monitoring.', 3, 12999.00, 55, 'purifier.jpg'],
            
            // Sports & Fitness
            ['Yoga Mat Premium', 'Extra-thick non-slip yoga mat with alignment markers and carrying strap.', 4, 2999.00, 150, 'yoga.jpg'],
            ['Adjustable Dumbbell Set', 'Space-saving dumbbell set with weight range from 5kg to 25kg.', 4, 14999.00, 30, 'dumbbell.jpg'],
            ['Treadmill Electric', 'Folding treadmill with heart rate monitor and pre-set workout programs.', 4, 34999.00, 18, 'treadmill.jpg'],
            ['Cricket Bat Professional', 'Grade A English willow cricket bat for professional players.', 4, 7999.00, 40, 'cricket.jpg'],
            ['Football Premium', 'Professional match football with official size and weight specifications.', 4, 1999.00, 80, 'football.jpg'],
            
            // Books & Media
            ['Kindle Paperwhite', 'Waterproof e-reader with adjustable warm light and 8GB storage.', 5, 12999.00, 60, 'kindle.jpg'],
            ['JBL Flip 6 Speaker', 'Portable Bluetooth speaker with powerful 360-degree sound.', 5, 6999.00, 90, 'speaker.jpg'],
            ['Programming Masterclass', 'Complete guide to modern programming languages and best practices.', 5, 1499.00, 100, 'book.jpg'],
            ['Gaming Headset RGB', 'Professional gaming headset with 7.1 surround sound and RGB lighting.', 5, 3999.00, 70, 'gaming.jpg'],
            ['External SSD 1TB', 'High-speed portable SSD with USB 3.2 Gen 2 connectivity.', 5, 8999.00, 85, 'ssd.jpg'],
            
            // Beauty & Health
            ['Face Cream Premium', 'Anti-aging face cream with hyaluronic acid and vitamin C.', 6, 2999.00, 120, 'cream.jpg'],
            ['Perfume Luxury', 'Premium fragrance with long-lasting scent and elegant packaging.', 6, 6999.00, 60, 'perfume.jpg'],
            ['Hair Dryer Professional', 'Ionic hair dryer with multiple heat and speed settings.', 6, 4999.00, 90, 'dryer.jpg'],
            ['Makeup Brush Set', 'Complete set of professional makeup brushes for all applications.', 6, 3499.00, 75, 'makeup.jpg'],
            ['Vitamin Supplements', 'Daily multivitamin tablets with essential nutrients and minerals.', 6, 1499.00, 200, 'vitamins.jpg']
        ];
        
        foreach ($products as $product) {
            $stmt = $conn->prepare("INSERT INTO products (name, description, category_id, price, stock, image) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sidids", $product[0], $product[1], $product[2], $product[3], $product[4], $product[5]);
            $stmt->execute();
        }
    }
    
    // Insert admin user if not exists
    $admin_count = $conn->query("SELECT COUNT(*) as count FROM admin_users")->fetch_assoc()['count'];
    if ($admin_count == 0) {
        $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO admin_users (username, password, name) VALUES (?, ?, ?)");
        $username = 'admin';
        $name = 'Administrator';
        $stmt->bind_param("sss", $username, $hashed_password, $name);
        $stmt->execute();
    }
}

// Auto-initialize database on every connection
initializeDatabase();
?>
