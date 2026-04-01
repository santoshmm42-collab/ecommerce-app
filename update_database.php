<?php
require_once 'includes/db_connect.php';

// Enable debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔧 Updating E-Commerce Database</h1>";

// Clear existing products and reinsert with proper data
$conn->query("DELETE FROM products");
$conn->query("DELETE FROM categories");

// Insert categories
$categories = [
    ['Electronics', 'Latest electronic devices and gadgets'],
    ['Fashion', 'Trendy clothing and fashion accessories'],
    ['Mobiles', 'Smartphones and mobile accessories']
];

foreach ($categories as $cat) {
    $stmt = $conn->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
    $stmt->bind_param("ss", $cat[0], $cat[1]);
    $stmt->execute();
    echo "<p>✅ Category added: {$cat[0]}</p>";
}

// Insert comprehensive products
$products = [
    // Electronics (5 products)
    ['Laptop Pro 15', 'High-performance laptop with Intel i7 processor, 16GB RAM, 512GB SSD', 1, 89999.00, 25, 'laptop.jpg'],
    ['Smartwatch Ultra', 'Advanced fitness tracking, GPS, heart rate monitor, water resistant', 1, 24999.00, 50, 'smartwatch.jpg'],
    ['Wireless Headphones', 'Premium noise-canceling headphones with 30-hour battery life', 1, 12999.00, 75, 'headphones.jpg'],
    ['Tablet Pro 12', '12.9-inch display, powerful processor, perfect for work and entertainment', 1, 54999.00, 30, 'tablet.jpg'],
    ['4K Webcam', 'Ultra HD webcam with auto-focus and built-in microphone', 1, 7999.00, 100, 'webcam.jpg'],
    
    // Fashion (5 products)
    ['Premium T-Shirt', '100% cotton comfort t-shirt, available in multiple colors', 2, 1299.00, 200, 'tshirt.jpg'],
    ['Classic Jeans', 'Comfortable denim jeans with modern fit and style', 2, 2999.00, 150, 'jeans.jpg'],
    ['Sports Jacket', 'Lightweight and breathable, perfect for outdoor activities', 2, 4999.00, 80, 'jacket.jpg'],
    ['Running Shoes', 'Professional running shoes with advanced cushioning technology', 2, 6999.00, 120, 'shoes.jpg'],
    ['Leather Wallet', 'Genuine leather wallet with multiple card slots', 2, 1999.00, 90, 'wallet.jpg'],
    
    // Mobiles (5 products)
    ['iPhone 13 Pro', 'A15 Bionic chip, Pro camera system, 5G capable', 3, 99999.00, 40, 'iphone13.jpg'],
    ['Samsung Galaxy S23', 'Flagship Android phone with amazing camera and display', 3, 79999.00, 60, 'samsung.jpg'],
    ['OnePlus 11', 'Fast charging, flagship performance, Hasselblad camera', 3, 64999.00, 70, 'oneplus.jpg'],
    ['Realme GT 2', '5G smartphone with premium features at great price', 3, 39999.00, 100, 'realme.jpg'],
    ['Xiaomi 13', 'Leica camera, Snapdragon 8 Gen 2, elegant design', 3, 54999.00, 80, 'xiaomi.jpg']
];

foreach ($products as $product) {
    $stmt = $conn->prepare("INSERT INTO products (name, description, category_id, price, stock, image) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sidids", $product[0], $product[1], $product[2], $product[3], $product[4], $product[5]);
    $stmt->execute();
    echo "<p>✅ Product added: {$product[0]} - ₹{$product[3]}</p>";
}

// Create admin user if not exists
$admin_check = $conn->query("SELECT * FROM admin_users WHERE username = 'admin'");
if ($admin_check->num_rows == 0) {
    $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO admin_users (username, password, name) VALUES (?, ?, ?)");
    $username = 'admin';
    $name = 'Administrator';
    $stmt->bind_param("sss", $username, $hashed_password, $name);
    $stmt->execute();
    echo "<p>✅ Admin user created: admin / admin123</p>";
}

echo "<h2>✅ Database update complete!</h2>";
echo "<p><strong>Total Categories:</strong> 3</p>";
echo "<p><strong>Total Products:</strong> 15</p>";
echo "<p><a href='index.php'>👉 Go to Homepage</a></p>";
?>
