<?php
// Quick Database Setup Script
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🚀 QUICK DATABASE SETUP</h1>";

// Try to connect to MySQL first (without specifying database)
$host = 'localhost';
$username = 'root';
$password = '';

// Try port 3307 first
try {
    $conn = new mysqli($host, $username, $password, '', 3307);
    if ($conn->connect_error) {
        throw new Exception("Port 3307 failed");
    }
    echo "<p style='color: green;'>✅ Connected to MySQL on port 3307</p>";
    $port = 3307;
} catch (Exception $e) {
    // Try port 3306
    try {
        $conn = new mysqli($host, $username, $password, '', 3306);
        if ($conn->connect_error) {
            throw new Exception("Port 3306 failed");
        }
        echo "<p style='color: green;'>✅ Connected to MySQL on port 3306</p>";
        $port = 3306;
    } catch (Exception $e2) {
        die("<p style='color: red;'>❌ Cannot connect to MySQL on either port 3306 or 3307</p>");
    }
}

// Create database if not exists
$conn->query("CREATE DATABASE IF NOT EXISTS ecommerce");
echo "<p style='color: green;'>✅ Database 'ecommerce' created/verified</p>";

// Select the database
$conn->select_db('ecommerce');

// Create tables
$conn->query("CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$conn->query("CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    category_id INT,
    price DECIMAL(10,2) NOT NULL,
    stock INT DEFAULT 0,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$conn->query("CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$conn->query("CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

echo "<p style='color: green;'>✅ All tables created/verified</p>";

// Insert sample data
$cat_count = $conn->query("SELECT COUNT(*) as count FROM categories")->fetch_assoc()['count'];
if ($cat_count == 0) {
    $conn->query("INSERT INTO categories (name, description) VALUES 
        ('Electronics', 'Electronic devices and gadgets'),
        ('Fashion', 'Clothing and accessories'),
        ('Home & Garden', 'Home decoration and garden supplies'),
        ('Sports', 'Sports equipment and accessories'),
        ('Books', 'Books and educational materials')");
    echo "<p style='color: green;'>✅ Sample categories inserted</p>";
} else {
    echo "<p style='color: blue;'>ℹ️ Categories already exist ($cat_count records)</p>";
}

$prod_count = $conn->query("SELECT COUNT(*) as count FROM products")->fetch_assoc()['count'];
if ($prod_count == 0) {
    $conn->query("INSERT INTO products (name, description, category_id, price, stock, image) VALUES 
        ('Laptop Pro', 'High-performance laptop for professionals', 1, 999.99, 50, 'laptop.jpg'),
        ('T-Shirt Premium', 'Comfortable cotton t-shirt', 2, 29.99, 100, 'tshirt.jpg'),
        ('Smart Home Kit', 'Complete smart home starter kit', 3, 199.99, 30, 'home.jpg'),
        ('Running Shoes', 'Professional running shoes', 4, 89.99, 75, 'sports.jpg'),
        ('Programming Book', 'Learn programming from scratch', 5, 49.99, 200, 'book.jpg')");
    echo "<p style='color: green;'>✅ Sample products inserted</p>";
} else {
    echo "<p style='color: blue;'>ℹ️ Products already exist ($prod_count records)</p>";
}

// Insert admin user
$admin_count = $conn->query("SELECT COUNT(*) as count FROM admin_users")->fetch_assoc()['count'];
if ($admin_count == 0) {
    $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
    $conn->query("INSERT INTO admin_users (username, password, name) VALUES 
        ('admin', '$hashed_password', 'Administrator')");
    echo "<p style='color: green;'>✅ Admin user created (username: admin, password: admin123)</p>";
} else {
    echo "<p style='color: blue;'>ℹ️ Admin user already exists</p>";
}

// Update db_connect.php with correct port
$db_connect_content = "<?php
// Enable debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
\$host = 'localhost';
\$username = 'root';
\$password = '';
\$database = 'ecommerce';
\$port = $port;

// Create connection
\$conn = new mysqli(\$host, \$username, \$password, \$database, \$port);

// Check connection
if (\$conn->connect_error) {
    die(\"Connection failed: \" . \$conn->connect_error);
}

// Set charset
\$conn->set_charset(\"utf8mb4\");

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Helper functions
function sanitize(\$data) {
    global \$conn;
    return htmlspecialchars(strip_tags(\$conn->real_escape_string(\$data)));
}

function redirect(\$url) {
    header(\"Location: \$url\");
    exit();
}

function isLoggedIn() {
    return isset(\$_SESSION['user_id']);
}

function isAdmin() {
    return isset(\$_SESSION['admin_id']);
}

function displayAlert(\$type, \$message) {
    return \"<div class='alert alert-\$type alert-dismissible fade show' role='alert'>
                \$message
                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
            </div>\";
}
?>";

file_put_contents(__DIR__ . '/includes/db_connect.php', $db_connect_content);
echo "<p style='color: green;'>✅ Database connection updated with port $port</p>";

echo "<h2 style='color: green;'>🎉 SETUP COMPLETE!</h2>";
echo "<p><strong>Database Details:</strong></p>";
echo "<ul>";
echo "<li>Host: localhost</li>";
echo "<li>Port: $port</li>";
echo "<li>Database: ecommerce</li>";
echo "<li>Categories: " . $conn->query("SELECT COUNT(*) as count FROM categories")->fetch_assoc()['count'] . "</li>";
echo "<li>Products: " . $conn->query("SELECT COUNT(*) as count FROM products")->fetch_assoc()['count'] . "</li>";
echo "</ul>";

echo "<p style='font-size: 18px;'><a href='index.php' style='color: blue; font-weight: bold;'>👉 GO TO HOMEPAGE</a></p>";
echo "<p style='font-size: 18px;'><a href='shop.php' style='color: blue; font-weight: bold;'>👉 GO TO SHOP PAGE</a></p>";

$conn->close();
?>
