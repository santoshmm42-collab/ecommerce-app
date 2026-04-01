<?php
require_once 'includes/db_connect.php';

// Enable debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔧 Fixing Images and Signup Issues</h1>";

// Update the shoes image to use the correct Nike shoes image
$stmt = $conn->prepare("UPDATE products SET image = 'nike-shoes.jpg' WHERE name LIKE '%shoes%' OR image = 'shoes.jpg'");
$stmt->execute();
echo "<p>✅ Updated Nike shoes image</p>";

// Check if signup.php exists and is readable
$signup_file = __DIR__ . '/user/signup.php';
if (file_exists($signup_file)) {
    echo "<p>✅ signup.php exists at: user/signup.php</p>";
    
    // Check if the file has proper PHP opening tag
    $content = file_get_contents($signup_file);
    if (strpos($content, '<?php') === 0) {
        echo "<p>✅ signup.php has proper PHP opening</p>";
    } else {
        echo "<p>❌ signup.php missing PHP opening tag</p>";
    }
} else {
    echo "<p>❌ signup.php not found</p>";
}

// Create a simple test for the signup route
echo "<h2>Testing Routes:</h2>";
echo "<p><a href='user/signup.php' target='_blank'>👉 Test Signup Page</a></p>";
echo "<p><a href='index.php' target='_blank'>👉 Test Homepage</a></p>";
echo "<p><a href='shop.php' target='_blank'>👉 Test Shop Page</a></p>";

// Show current products and their images
echo "<h2>Current Products:</h2>";
$result = $conn->query("SELECT id, name, image, category_id FROM products ORDER BY category_id, name");
if ($result && $result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Name</th><th>Image</th><th>Category</th><th>Image Check</th></tr>";
    while($row = $result->fetch_assoc()) {
        $image_path = "assets/images/products/" . $row['image'];
        $image_exists = file_exists($image_path);
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['image']) . "</td>";
        echo "<td>" . $row['category_id'] . "</td>";
        echo "<td>" . ($image_exists ? "✅ Exists" : "❌ Missing") . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No products found</p>";
}

// Download missing images
echo "<h2>Downloading Missing Images:</h2>";
$required_images = [
    'laptop.jpg' => 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=400&h=400&fit=crop',
    'smartwatch.jpg' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400&h=400&fit=crop',
    'headphones.jpg' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&h=400&fit=crop',
    'tablet.jpg' => 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=400&h=400&fit=crop',
    'webcam.jpg' => 'https://images.unsplash.com/photo-1593696140826-c58b021acf8b?w=400&h=400&fit=crop',
    'tshirt.jpg' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=400&h=400&fit=crop',
    'jeans.jpg' => 'https://images.unsplash.com/photo-1542272604-787c3835535d?w=400&h=400&fit=crop',
    'jacket.jpg' => 'https://images.unsplash.com/photo-1551698618-1dfe5d97d256?w=400&h=400&fit=crop',
    'nike-shoes.jpg' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&h=400&fit=crop',
    'wallet.jpg' => 'https://images.unsplash.com/photo-1565193566173-7a0ee3dbe261?w=400&h=400&fit=crop',
    'iphone13.jpg' => 'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=400&h=400&fit=crop',
    'samsung.jpg' => 'https://images.unsplash.com/photo-1598327105666-5b893dc97320?w=400&h=400&fit=crop',
    'oneplus.jpg' => 'https://images.unsplash.com/photo-1605236453806-b25ea718bb5b?w=400&h=400&fit=crop',
    'realme.jpg' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=400&h=400&fit=crop',
    'xiaomi.jpg' => 'https://images.unsplash.com/photo-1605236453806-b25ea718bb5b?w=400&h=400&fit=crop'
];

$image_dir = __DIR__ . '/assets/images/products/';
if (!is_dir($image_dir)) {
    mkdir($image_dir, 0777, true);
}

foreach ($required_images as $filename => $url) {
    $filepath = $image_dir . $filename;
    if (!file_exists($filepath)) {
        $image_data = file_get_contents($url);
        if ($image_data) {
            file_put_contents($filepath, $image_data);
            echo "<p>✅ Downloaded: $filename</p>";
        } else {
            echo "<p>❌ Failed to download: $filename</p>";
        }
    } else {
        echo "<p>✅ Already exists: $filename</p>";
    }
}

echo "<h2>✅ All fixes completed!</h2>";
echo "<p><a href='index.php'>👉 Go to Homepage</a></p>";
?>
