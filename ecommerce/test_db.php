<?php
// Test database connection
require_once 'includes/db_connect.php';

echo "<h1>Database Connection Test</h1>";

if ($conn->connect_error) {
    echo "<p style='color: red;'>Connection failed: " . $conn->connect_error . "</p>";
} else {
    echo "<p style='color: green;'>✅ Database connected successfully!</p>";
    
    // Test categories table
    $result = $conn->query("SELECT COUNT(*) as count FROM categories");
    $count = $result->fetch_assoc()['count'];
    echo "<p>Categories found: " . $count . "</p>";
    
    // Test products table
    $result = $conn->query("SELECT COUNT(*) as count FROM products");
    $count = $result->fetch_assoc()['count'];
    echo "<p>Products found: " . $count . "</p>";
    
    // Show sample products
    $result = $conn->query("SELECT * FROM products LIMIT 3");
    echo "<h3>Sample Products:</h3>";
    while($row = $result->fetch_assoc()) {
        echo "<p><strong>" . $row['name'] . "</strong> - ₹" . $row['price'] . " - Image: " . $row['image'] . "</p>";
    }
}
?>
