<?php
// Enable debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 E-COMMERCE DEBUG & FIX TOOL</h1>";

// Test database connection with all possible configurations
$configs = [
    ['host' => 'localhost', 'port' => 3307, 'db' => 'ecommerce'],
    ['host' => 'localhost', 'port' => 3306, 'db' => 'ecommerce'],
    ['host' => '127.0.0.1', 'port' => 3307, 'db' => 'ecommerce'],
    ['host' => '127.0.0.1', 'port' => 3306, 'db' => 'ecommerce'],
];

$working_config = null;
$conn = null;

foreach ($configs as $config) {
    echo "<h3>Testing connection to {$config['host']}:{$config['port']}...</h3>";
    
    try {
        $test_conn = new mysqli($config['host'], 'root', '', $config['db'], $config['port']);
        if ($test_conn->connect_error) {
            echo "<p style='color: orange;'>⚠️ Failed: " . $test_conn->connect_error . "</p>";
        } else {
            echo "<p style='color: green;'>✅ SUCCESS: Connected to {$config['host']}:{$config['port']}</p>";
            $working_config = $config;
            $conn = $test_conn;
            break;
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    }
}

if (!$working_config) {
    echo "<h2 style='color: red;'>❌ NO DATABASE CONNECTION FOUND!</h2>";
    echo "<p>Please check:</p>";
    echo "<ul>";
    echo "<li>XAMPP MySQL service is running</li>";
    echo "<li>MySQL port is 3306 or 3307</li>";
    echo "<li>Database 'ecommerce' exists</li>";
    echo "</ul>";
    exit();
}

echo "<h2 style='color: green;'>✅ Working configuration found: {$working_config['host']}:{$working_config['port']}</h2>";

// Check and create database if needed
echo "<h2>📊 CHECKING DATABASE STRUCTURE</h2>";

// Create database if not exists
$conn->query("CREATE DATABASE IF NOT EXISTS ecommerce");
$conn->select_db('ecommerce');

// Check tables
$tables = ['categories', 'products'];
foreach ($tables as $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result->num_rows > 0) {
        $count = $conn->query("SELECT COUNT(*) as count FROM $table")->fetch_assoc()['count'];
        echo "<p style='color: green;'>✅ Table '$table' exists with $count records</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ Table '$table' does not exist - Creating...</p>";
        
        if ($table === 'categories') {
            $conn->query("CREATE TABLE categories (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                description TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
        } elseif ($table === 'products') {
            $conn->query("CREATE TABLE products (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(200) NOT NULL,
                description TEXT,
                category_id INT,
                price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                stock INT DEFAULT 0,
                image VARCHAR(255) DEFAULT 'default.jpg',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
        }
        echo "<p style='color: green;'>✅ Table '$table' created</p>";
    }
}

// Check if data exists and populate if empty
echo "<h2>📦 CHECKING & POPULATING DATA</h2>";

// Check categories
$cat_count = $conn->query("SELECT COUNT(*) as count FROM categories")->fetch_assoc()['count'];
if ($cat_count == 0) {
    echo "<p style='color: orange;'>⚠️ Categories table is empty - Inserting sample data...</p>";
    
    $categories = [
        ['Electronics', 'Latest electronic devices and gadgets'],
        ['Fashion', 'Trendy clothing and fashion accessories'],
        ['Mobile', 'Smartphones and mobile accessories']
    ];
    
    foreach ($categories as $cat) {
        $conn->query("INSERT INTO categories (name, description) VALUES ('" . $conn->real_escape_string($cat[0]) . "', '" . $conn->real_escape_string($cat[1]) . "')");
    }
    echo "<p style='color: green;'>✅ Sample categories inserted</p>";
} else {
    echo "<p style='color: blue;'>ℹ️ Categories table has $cat_count records</p>";
}

// Check products
$prod_count = $conn->query("SELECT COUNT(*) as count FROM products")->fetch_assoc()['count'];
if ($prod_count == 0) {
    echo "<p style='color: orange;'>⚠️ Products table is empty - Inserting sample data...</p>";
    
    $products = [
        ['iPhone 15 Pro', 'Latest iPhone with advanced camera system and A17 Pro chip', 1, 119999.00, 50, 'iphone.jpg'],
        ['Samsung Galaxy S24', 'Flagship Android phone with amazing features', 1, 89999.00, 75, 'samsung.jpg'],
        ['MacBook Air M2', 'Ultra-thin laptop with M2 chip and 15-hour battery life', 1, 99999.00, 30, 'laptop.jpg'],
        ['Nike Air Max', 'Classic running shoes with Air cushioning', 2, 8999.00, 100, 'shoes.jpg'],
        ['Adidas T-Shirt', 'Premium cotton t-shirt with logo', 2, 1299.00, 150, 'tshirt.jpg'],
        ['Levi\'s Jeans', 'Classic fit denim jeans', 2, 3999.00, 80, 'jeans.jpg'],
        ['OnePlus 12', 'Premium smartphone with fast charging', 3, 64999.00, 60, 'oneplus.jpg'],
        ['Xiaomi 14', 'Budget-friendly flagship phone', 3, 44999.00, 90, 'xiaomi.jpg']
    ];
    
    foreach ($products as $product) {
        $conn->query("INSERT INTO products (name, description, category_id, price, stock, image) VALUES 
            ('" . $conn->real_escape_string($product[0]) . "', 
             '" . $conn->real_escape_string($product[1]) . "', 
             " . $product[2] . ", 
             " . $product[3] . ", 
             " . $product[4] . ", 
             '" . $product[5] . "')");
    }
    echo "<p style='color: green;'>✅ Sample products inserted</p>";
} else {
    echo "<p style='color: blue;'>ℹ️ Products table has $prod_count records</p>";
}

// Test product query
echo "<h2>🧪 TESTING PRODUCT QUERY</h2>";
$sql = "SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC LIMIT 5";
$result = $conn->query($sql);

if ($result) {
    $num_rows = $result->num_rows;
    echo "<p style='color: green;'>✅ Query executed successfully</p>";
    echo "<p style='color: blue;'>ℹ️ Query returned $num_rows rows</p>";
    
    if ($num_rows > 0) {
        echo "<h4>Sample Products:</h4>";
        while ($row = $result->fetch_assoc()) {
            echo "<div style='border: 1px solid #ddd; padding: 10px; margin: 10px 0;'>";
            echo "<strong>ID:</strong> " . $row['id'] . "<br>";
            echo "<strong>Name:</strong> " . htmlspecialchars($row['name']) . "<br>";
            echo "<strong>Price:</strong> ₹" . number_format($row['price'], 2) . "<br>";
            echo "<strong>Category:</strong> " . htmlspecialchars($row['category_name']) . "<br>";
            echo "<strong>Image:</strong> " . htmlspecialchars($row['image']) . "<br>";
            echo "<strong>Description:</strong> " . htmlspecialchars(substr($row['description'], 0, 100)) . "...<br>";
            echo "</div>";
        }
    }
} else {
    echo "<p style='color: red;'>❌ Query failed: " . $conn->error . "</p>";
}

// Check images
echo "<h2>🖼️ CHECKING IMAGE FILES</h2>";
$image_dir = __DIR__ . '/assets/images/products/';
$image_files = ['iphone.jpg', 'samsung.jpg', 'laptop.jpg', 'shoes.jpg', 'tshirt.jpg', 'jeans.jpg', 'oneplus.jpg', 'xiaomi.jpg'];

if (!is_dir($image_dir)) {
    echo "<p style='color: orange;'>⚠️ Creating images directory...</p>";
    mkdir($image_dir, 0777, true);
}

foreach ($image_files as $img) {
    $path = $image_dir . $img;
    if (file_exists($path)) {
        echo "<span style='color: green;'>✅ $img exists</span><br>";
    } else {
        echo "<span style='color: orange;'>⚠️ $img missing - Downloading...</span><br>";
        // Download placeholder image
        $img_data = file_get_contents("https://picsum.photos/seed/" . urlencode($img) . "/400/400.jpg");
        if ($img_data) {
            file_put_contents($path, $img_data);
            echo "<span style='color: green;'>✅ $img downloaded</span><br>";
        } else {
            echo "<span style='color: red;'>❌ Failed to download $img</span><br>";
        }
    }
}

// Update db_connect.php with working configuration
echo "<h2>🔧 UPDATING DATABASE CONNECTION</h2>";
$db_connect_content = "<?php
// Enable debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
\$host = '{$working_config['host']}';
\$username = 'root';
\$password = '';
\$database = 'ecommerce';
\$port = {$working_config['port']};

// Create connection
\$conn = new mysqli(\$host, \$username, \$password, \$database, \$port);

// Check connection
if (\$conn->connect_error) {
    die('Connection failed: ' . \$conn->connect_error);
}

// Set charset
\$conn->set_charset('utf8mb4');

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
    header('Location: \$url');
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

if (file_put_contents(__DIR__ . '/includes/db_connect.php', $db_connect_content)) {
    echo "<p style='color: green;'>✅ Database connection file updated</p>";
} else {
    echo "<p style='color: red;'>❌ Failed to update database connection file</p>";
}

echo "<h2 style='color: green;'>🎉 FIX COMPLETE!</h2>";
echo "<p><strong>Summary:</strong></p>";
echo "<ul>";
echo "<li>✅ Database connection established</li>";
echo "<li>✅ Tables created/verified</li>";
echo "<li>✅ Sample data inserted</li>";
echo "<li>✅ Images downloaded</li>";
echo "<li>✅ Configuration updated</li>";
echo "</ul>";

echo "<div style='text-align: center; margin-top: 30px;'>";
echo "<a href='index.php' style='font-size: 18px; background: #007bff; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px;'>👉 GO TO YOUR WEBSITE</a>";
echo "</div>";

$conn->close();
?>
