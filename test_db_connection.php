<?php
echo "<h1>🔧 Database Connection Test</h1>";

// Test database connection
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'ecommerce';
$port = 3307;

echo "<h2>📊 Connection Details:</h2>";
echo "<p><strong>Host:</strong> $host</p>";
echo "<p><strong>Port:</strong> $port</p>";
echo "<p><strong>Username:</strong> $username</p>";
echo "<p><strong>Database:</strong> $database</p>";

echo "<h2>🔌 Testing Connection...</h2>";

try {
    $conn = new mysqli($host, $username, $password, $database, $port);
    
    if ($conn->connect_error) {
        echo "<p>❌ Connection failed: " . $conn->connect_error . "</p>";
        echo "<h3>🛠️ Possible Solutions:</h3>";
        echo "<ul>";
        echo "<li>Check if XAMPP MySQL is running</li>";
        echo "<li>Verify MySQL port is 3307 (not 3306)</li>";
        echo "<li>Check if 'ecommerce' database exists</li>";
        echo "<li>Make sure no other MySQL service is using port 3307</li>";
        echo "</ul>";
    } else {
        echo "<p>✅ Database connection successful!</p>";
        
        // Test if database exists
        $result = $conn->query("SHOW DATABASES LIKE 'ecommerce'");
        if ($result->num_rows > 0) {
            echo "<p>✅ Database 'ecommerce' exists</p>";
            
            // Check tables
            $tables = $conn->query("SHOW TABLES");
            echo "<h3>📋 Tables in database:</h3>";
            echo "<ul>";
            while($table = $tables->fetch_array()) {
                echo "<li>" . $table[0] . "</li>";
            }
            echo "</ul>";
            
            // Check products count
            $products = $conn->query("SELECT COUNT(*) as count FROM products");
            $product_count = $products->fetch_assoc()['count'];
            echo "<p><strong>Products:</strong> $product_count</p>";
            
            // Check categories count
            $categories = $conn->query("SELECT COUNT(*) as count FROM categories");
            $category_count = $categories->fetch_assoc()['count'];
            echo "<p><strong>Categories:</strong> $category_count</p>";
            
        } else {
            echo "<p>❌ Database 'ecommerce' does not exist</p>";
            echo "<p><a href='import_database.php'>📥 Import Database</a></p>";
        }
        
        $conn->close();
    }
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<h2>🚀 Quick Actions:</h2>";
echo "<div class='text-center'>";
echo "<a href='index.php' class='btn btn-primary btn-lg me-2'>🏠 Homepage</a>";
echo "<a href='import_database.php' class='btn btn-success btn-lg me-2'>📥 Import Database</a>";
echo "<a href='test_pages.php' class='btn btn-info btn-lg'>🧪 Test Pages</a>";
echo "</div>";

echo "<h2>📝 XAMPP Checklist:</h2>";
echo "<ul>";
echo "<li>☐ XAMPP Control Panel is open</li>";
echo "<li>☐ Apache service is running</li>";
echo "<li>☐ MySQL service is running</li>";
echo "<li>☐ MySQL port is set to 3307</li>";
echo "<li>☐ 'ecommerce' database exists</li>";
echo "</ul>";
?>
