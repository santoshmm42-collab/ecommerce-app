<?php
// Enable debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 DATABASE & PROJECT DIAGNOSTIC</h1>";

// Test database connection with different configurations
$configs = [
    ['host' => 'localhost', 'port' => 3306, 'db' => 'ecommerce'],
    ['host' => 'localhost', 'port' => 3307, 'db' => 'ecommerce'],
    ['host' => '127.0.0.1', 'port' => 3306, 'db' => 'ecommerce'],
    ['host' => '127.0.0.1', 'port' => 3307, 'db' => 'ecommerce'],
];

$working_config = null;
foreach ($configs as $config) {
    try {
        $conn = new mysqli($config['host'], 'root', '', $config['db'], $config['port']);
        if ($conn->connect_error) {
            echo "<p style='color: orange;'>⚠️ Trying {$config['host']}:{$config['port']} - Failed: " . $conn->connect_error . "</p>";
        } else {
            echo "<p style='color: green;'>✅ SUCCESS: Connected to {$config['host']}:{$config['port']}</p>";
            $working_config = $config;
            break;
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error with {$config['host']}:{$config['port']} - " . $e->getMessage() . "</p>";
    }
}

if ($working_config) {
    $conn = new mysqli($working_config['host'], 'root', '', $working_config['db'], $working_config['port']);
    
    // Check if tables exist
    $tables = ['categories', 'products', 'users', 'admin_users'];
    foreach ($tables as $table) {
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        if ($result->num_rows > 0) {
            $count = $conn->query("SELECT COUNT(*) as count FROM $table")->fetch_assoc()['count'];
            echo "<p style='color: green;'>✅ Table '$table' exists with $count records</p>";
        } else {
            echo "<p style='color: red;'>❌ Table '$table' does NOT exist</p>";
        }
    }
    
    // If tables don't exist, create them
    $categories_exist = $conn->query("SHOW TABLES LIKE 'categories'")->num_rows > 0;
    $products_exist = $conn->query("SHOW TABLES LIKE 'products'")->num_rows > 0;
    
    if (!$categories_exist || !$products_exist) {
        echo "<h2 style='color: orange;'>🔧 CREATING MISSING TABLES...</h2>";
        
        // Create categories table
        $conn->query("CREATE TABLE IF NOT EXISTS categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        
        // Create products table
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
        
        // Insert sample data if empty
        $cat_count = $conn->query("SELECT COUNT(*) as count FROM categories")->fetch_assoc()['count'];
        if ($cat_count == 0) {
            $conn->query("INSERT INTO categories (name, description) VALUES 
                ('Electronics', 'Electronic devices and gadgets'),
                ('Fashion', 'Clothing and accessories'),
                ('Home & Garden', 'Home decoration and garden supplies'),
                ('Sports', 'Sports equipment and accessories'),
                ('Books', 'Books and educational materials')");
            echo "<p style='color: green;'>✅ Sample categories inserted</p>";
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
        }
    }
    
    // Show current data
    echo "<h2>📊 CURRENT DATA:</h2>";
    
    $categories = $conn->query("SELECT * FROM categories");
    echo "<h3>Categories:</h3>";
    while($cat = $categories->fetch_assoc()) {
        echo "<p>- {$cat['name']}: {$cat['description']}</p>";
    }
    
    $products = $conn->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id");
    echo "<h3>Products:</h3>";
    while($prod = $products->fetch_assoc()) {
        echo "<p>- <strong>{$prod['name']}</strong> ({$prod['category_name']}) - ₹{$prod['price']} - Image: {$prod['image']}</p>";
    }
    
    // Check images
    echo "<h2>🖼️ IMAGE CHECK:</h2>";
    $image_files = ['laptop.jpg', 'tshirt.jpg', 'home.jpg', 'sports.jpg', 'book.jpg'];
    foreach ($image_files as $img) {
        $path = __DIR__ . '/assets/images/products/' . $img;
        if (file_exists($path)) {
            echo "<p style='color: green;'>✅ $img exists</p>";
        } else {
            echo "<p style='color: red;'>❌ $img missing</p>";
        }
    }
    
    $conn->close();
    
    echo "<h2 style='color: green;'>🎉 DIAGNOSTIC COMPLETE!</h2>";
    echo "<p><a href='index.php' style='font-size: 18px; color: blue;'>👉 Go to Homepage</a></p>";
    
} else {
    echo "<h2 style='color: red;'>❌ NO WORKING DATABASE CONNECTION FOUND!</h2>";
    echo "<p>Please check:</p>";
    echo "<ul>";
    echo "<li>XAMPP MySQL service is running</li>";
    echo "<li>MySQL port is 3306 or 3307</li>";
    echo "<li>Database 'ecommerce' exists</li>";
    echo "</ul>";
}
?>
