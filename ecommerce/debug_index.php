<?php
require_once 'includes/db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopHub - Debug Version</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .debug-section { margin: 30px 0; padding: 20px; border: 1px solid #ddd; border-radius: 10px; }
        .product-card { border: 1px solid #ddd; border-radius: 10px; padding: 15px; margin: 10px 0; }
        .product-image { width: 100%; height: 200px; object-fit: cover; border-radius: 5px; }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .warning { color: #ffc107; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-5">🛍️ ShopHub - Debug Version</h1>
        
        <!-- Database Connection Status -->
        <div class="debug-section">
            <h3>🔗 Database Connection Status</h3>
            <?php if ($conn->connect_error): ?>
                <p class="error">❌ Connection failed: <?php echo $conn->connect_error; ?></p>
            <?php else: ?>
                <p class="success">✅ Database connected successfully!</p>
                <p class="success">✅ Character set: <?php echo $conn->character_set_name(); ?></p>
            <?php endif; ?>
        </div>
        
        <!-- Categories Check -->
        <div class="debug-section">
            <h3>🏷️ Categories Data</h3>
            <?php
            $categories_query = "SELECT * FROM categories ORDER BY name";
            $categories_result = $conn->query($categories_query);
            
            if ($categories_result) {
                $cat_count = $categories_result->num_rows;
                echo "<p class='success'>✅ Categories query executed successfully</p>";
                echo "<p class='success'>✅ Found $cat_count categories</p>";
                
                if ($cat_count > 0) {
                    echo "<div class='row'>";
                    while($cat = $categories_result->fetch_assoc()) {
                        echo "<div class='col-md-4 mb-3'>";
                        echo "<div class='alert alert-info'>";
                        echo "<strong>ID:</strong> " . $cat['id'] . "<br>";
                        echo "<strong>Name:</strong> " . htmlspecialchars($cat['name']) . "<br>";
                        echo "<strong>Description:</strong> " . htmlspecialchars($cat['description']) . "<br>";
                        echo "</div>";
                        echo "</div>";
                    }
                    echo "</div>";
                }
            } else {
                echo "<p class='error'>❌ Categories query failed: " . $conn->error . "</p>";
            }
            ?>
        </div>
        
        <!-- Products Check -->
        <div class="debug-section">
            <h3>📦 Products Data</h3>
            <?php
            $products_query = "SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC";
            $products_result = $conn->query($products_query);
            
            if ($products_result) {
                $prod_count = $products_result->num_rows;
                echo "<p class='success'>✅ Products query executed successfully</p>";
                echo "<p class='success'>✅ Found $prod_count products</p>";
                
                if ($prod_count > 0) {
                    echo "<div class='row'>";
                    while($product = $products_result->fetch_assoc()) {
                        echo "<div class='col-md-6 mb-4'>";
                        echo "<div class='product-card'>";
                        echo "<h5>" . htmlspecialchars($product['name']) . "</h5>";
                        echo "<p><strong>Category:</strong> " . htmlspecialchars($product['category_name']) . "</p>";
                        echo "<p><strong>Price:</strong> ₹" . number_format($product['price'], 2) . "</p>";
                        echo "<p><strong>Stock:</strong> " . $product['stock'] . "</p>";
                        echo "<p><strong>Image:</strong> " . htmlspecialchars($product['image']) . "</p>";
                        echo "<p><strong>Description:</strong> " . htmlspecialchars(substr($product['description'], 0, 100)) . "...</p>";
                        
                        // Test image path
                        $image_path = "assets/images/products/" . $product['image'];
                        echo "<p><strong>Image Path:</strong> $image_path</p>";
                        
                        if (file_exists($image_path)) {
                            echo "<img src='$image_path' class='product-image' alt='" . htmlspecialchars($product['name']) . "' onerror=\"this.src='https://picsum.photos/seed/" . urlencode($product['name']) . "/400/300.jpg'\">";
                            echo "<p class='success'>✅ Image file exists</p>";
                        } else {
                            echo "<img src='https://picsum.photos/seed/" . urlencode($product['name']) . "/400/300.jpg' class='product-image' alt='" . htmlspecialchars($product['name']) . "'>";
                            echo "<p class='warning'>⚠️ Image file missing, using placeholder</p>";
                        }
                        
                        echo "</div>";
                        echo "</div>";
                    }
                    echo "</div>";
                } else {
                    echo "<p class='warning'>⚠️ No products found in database</p>";
                }
            } else {
                echo "<p class='error'>❌ Products query failed: " . $conn->error . "</p>";
            }
            ?>
        </div>
        
        <!-- Image Directory Check -->
        <div class="debug-section">
            <h3>🖼️ Image Directory Check</h3>
            <?php
            $image_dir = __DIR__ . '/assets/images/products/';
            if (is_dir($image_dir)) {
                echo "<p class='success'>✅ Image directory exists: $image_dir</p>";
                $files = scandir($image_dir);
                $image_files = array_diff($files, array('.', '..'));
                echo "<p class='success'>✅ Found " . count($image_files) . " files in directory</p>";
                if (!empty($image_files)) {
                    echo "<p><strong>Files:</strong> " . implode(', ', $image_files) . "</p>";
                }
            } else {
                echo "<p class='error'>❌ Image directory does not exist: $image_dir</p>";
            }
            ?>
        </div>
        
        <!-- Navigation Links -->
        <div class="debug-section text-center">
            <h3>🔗 Navigation Links</h3>
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="index.php" class="btn btn-primary">🏠 Normal Homepage</a>
                <a href="shop.php" class="btn btn-success">🛍️ Shop Page</a>
                <a href="product.php?id=1" class="btn btn-info">📱 Product Details</a>
                <a href="cart.php" class="btn btn-warning">🛒 Shopping Cart</a>
                <a href="user/login.php" class="btn btn-secondary">👤 User Login</a>
                <a href="admin/login.php" class="btn btn-dark">🔐 Admin Login</a>
            </div>
        </div>
        
        <!-- Debug Summary -->
        <div class="debug-section">
            <h3>📋 Debug Summary</h3>
            <div class="row">
                <div class="col-md-6">
                    <h5>✅ Working Components:</h5>
                    <ul>
                        <li>Database Connection</li>
                        <li>Categories Table</li>
                        <li>Products Table</li>
                        <li>Image Fallbacks</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h5>🔧 Next Steps:</h5>
                    <ul>
                        <li>Visit normal homepage</li>
                        <li>Test product display</li>
                        <li>Check image loading</li>
                        <li>Test cart functionality</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
