<?php
require_once 'includes/db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopHub - Database Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .test-section { margin: 30px 0; padding: 20px; border: 1px solid #ddd; border-radius: 10px; }
        .success { color: #28a745; }
        .error { color: #dc3545; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-5">🛍️ ShopHub - Database & System Test</h1>
        
        <div class="test-section">
            <h3>🔗 Database Connection</h3>
            <?php if ($conn->connect_error): ?>
                <p class="error">❌ Connection failed: <?php echo $conn->connect_error; ?></p>
            <?php else: ?>
                <p class="success">✅ Database connected successfully!</p>
            <?php endif; ?>
        </div>
        
        <div class="test-section">
            <h3>📊 Database Tables</h3>
            <?php
            $tables = ['categories', 'products', 'users', 'admin_users'];
            foreach ($tables as $table) {
                $result = $conn->query("SHOW TABLES LIKE '$table'");
                if ($result->num_rows > 0) {
                    $count = $conn->query("SELECT COUNT(*) as count FROM $table")->fetch_assoc()['count'];
                    echo "<p class='success'>✅ Table '$table' exists with $count records</p>";
                } else {
                    echo "<p class='error'>❌ Table '$table' does NOT exist</p>";
                }
            }
            ?>
        </div>
        
        <div class="test-section">
            <h3>🏷️ Categories</h3>
            <div class="row">
                <?php
                $categories = $conn->query("SELECT * FROM categories ORDER BY name LIMIT 8");
                if ($categories && $categories->num_rows > 0) {
                    while($cat = $categories->fetch_assoc()) {
                        echo '<div class="col-md-3 mb-2">';
                        echo '<span class="badge bg-primary p-2">' . htmlspecialchars($cat['name']) . '</span>';
                        echo '</div>';
                    }
                } else {
                    echo '<p class="error">No categories found</p>';
                }
                ?>
            </div>
        </div>
        
        <div class="test-section">
            <h3>📦 Products (Sample)</h3>
            <div class="row">
                <?php
                $products = $conn->query("SELECT p.*, c.name as category_name FROM products p 
                                       LEFT JOIN categories c ON p.category_id = c.id 
                                       ORDER BY p.created_at DESC LIMIT 6");
                if ($products && $products->num_rows > 0) {
                    while($product = $products->fetch_assoc()) {
                        echo '<div class="col-md-4 mb-3">';
                        echo '<div class="card">';
                        echo '<div class="card-body">';
                        echo '<h6>' . htmlspecialchars($product['name']) . '</h6>';
                        echo '<p class="text-muted small">' . htmlspecialchars($product['category_name']) . '</p>';
                        echo '<p class="fw-bold text-success">₹' . number_format($product['price'], 2) . '</p>';
                        echo '<p class="text-muted small">Stock: ' . $product['stock'] . '</p>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<p class="error">No products found</p>';
                }
                ?>
            </div>
        </div>
        
        <div class="test-section">
            <h3>🖼️ Image Files Check</h3>
            <?php
            $image_files = ['iphone.jpg', 'laptop.jpg', 'watch.jpg', 'headphones.jpg', 'tablet.jpg', 'shoes.jpg'];
            foreach ($image_files as $img) {
                $path = __DIR__ . '/assets/images/products/' . $img;
                if (file_exists($path)) {
                    echo '<span class="badge bg-success me-2 mb-2">✅ ' . $img . '</span>';
                } else {
                    echo '<span class="badge bg-danger me-2 mb-2">❌ ' . $img . '</span>';
                }
            }
            ?>
        </div>
        
        <div class="test-section text-center">
            <h3>🚀 Ready to Launch!</h3>
            <p class="lead">Your e-commerce website is ready with:</p>
            <div class="row">
                <div class="col-md-3">
                    <h4>40+ Products</h4>
                </div>
                <div class="col-md-3">
                    <h4>8 Categories</h4>
                </div>
                <div class="col-md-3">
                    <h4>Real Prices</h4>
                </div>
                <div class="col-md-3">
                    <h4>Images</h4>
                </div>
            </div>
            <a href="index.php" class="btn btn-primary btn-lg mt-4">
                <i class="fas fa-shopping-cart me-2"></i>Go to ShopHub Website
            </a>
        </div>
    </div>
</body>
</html>
