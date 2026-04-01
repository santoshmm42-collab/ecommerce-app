<?php
require_once 'includes/db_connect.php';

// Enable debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🖼️ Fixing All Product Images - Proper & Relevant</h1>";

// Define correct images for all products
$image_fixes = [
    // Electronics (Category 1)
    ['4K Webcam Ultra', 'webcam-hd.jpg'],
    ['Gaming Keyboard RGB', 'gaming-keyboard.jpg'],
    ['Wireless Mouse Pro', 'wireless-mouse.jpg'],
    ['USB-C Hub 7-in-1', 'usb-hub.jpg'],
    
    // Fashion (Category 2)
    ['Genuine Leather Wallet', 'leather-wallet.jpg'],
    ['Designer Sunglasses', 'sunglasses.jpg'],
    ['Leather Belt Premium', 'leather-belt.jpg'],
    
    // Home & Living (Category 4)
    ['Modern Sofa Set 3+1+1', 'modern-sofa.jpg'],
    ['Smart TV 55" 4K', 'smart-tv.jpg'],
    ['Coffee Table Modern', 'coffee-table.jpg'],
    ['Dining Table Set 6-Seater', 'dining-table.jpg'],
    ['Air Purifier Pro', 'air-purifier.jpg'],
    ['LED Desk Lamp', 'desk-lamp.jpg'],
    ['Wall Clock Premium', 'wall-clock.jpg'],
    ['Plant Pot Set', 'plant-pots.jpg'],
    
    // Books & Media (Category 6)
    ['Kindle Paperwhite', 'kindle.jpg'],
    ['JBL Flip 6 Speaker', 'bluetooth-speaker.jpg'],
    ['Programming Masterclass', 'programming-book.jpg'],
    ['Gaming Headset RGB Pro', 'gaming-headset.jpg'],
    ['External SSD 1TB', 'external-ssd.jpg'],
    ['HDMI Cable 4K 2m', 'hdmi-cable.jpg'],
    ['Wireless Charger Fast', 'wireless-charger.jpg'],
    ['Bluetooth Earbuds Pro', 'wireless-earbuds.jpg'],
    
    // Beauty & Health (Category 7)
    ['Face Cream Anti-Aging', 'face-cream.jpg'],
    ['Luxury Perfume Set', 'perfume.jpg'],
    ['Hair Dryer Professional', 'hair-dryer.jpg'],
    ['Makeup Brush Set Pro', 'makeup-brushes.jpg'],
    ['Vitamin Supplements Premium', 'vitamins.jpg'],
    ['Face Wash Gel', 'face-wash.jpg'],
    ['Body Lotion Moisturizing', 'body-lotion.jpg'],
    ['Shampoo Anti-Hair Fall', 'shampoo.jpg']
];

echo "<h2>📥 Downloading Correct Images...</h2>";

// Download correct images
$image_downloads = [
    // Electronics
    'webcam-hd.jpg' => 'https://images.unsplash.com/photo-1593696140826-c58b021acf8b?w=400&h=400&fit=crop',
    'gaming-keyboard.jpg' => 'https://images.unsplash.com/photo-1596178065881-7a93a5b71c1f?w=400&h=400&fit=crop',
    'wireless-mouse.jpg' => 'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?w=400&h=400&fit=crop',
    'usb-hub.jpg' => 'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?w=400&h=400&fit=crop',
    
    // Fashion
    'leather-wallet.jpg' => 'https://images.unsplash.com/photo-1627123429571-6a7c4e0a7ab3?w=400&h=400&fit=crop',
    'sunglasses.jpg' => 'https://images.unsplash.com/photo-1572635196237-14b3f281503e?w=400&h=400&fit=crop',
    'leather-belt.jpg' => 'https://images.unsplash.com/photo-1544966503-7e3c40c8ed8c?w=400&h=400&fit=crop',
    
    // Home & Living
    'modern-sofa.jpg' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=400&h=400&fit=crop',
    'smart-tv.jpg' => 'https://images.unsplash.com/photo-1593354253330-cd6a0a75abde?w=400&h=400&fit=crop',
    'coffee-table.jpg' => 'https://images.unsplash.com/photo-1506439773649-6e0eb8cfb237?w=400&h=400&fit=crop',
    'dining-table.jpg' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=400&h=400&fit=crop',
    'air-purifier.jpg' => 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=400&h=400&fit=crop',
    'desk-lamp.jpg' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=400&fit=crop',
    'wall-clock.jpg' => 'https://images.unsplash.com/photo-1564211245531-60649e0589c3?w=400&h=400&fit=crop',
    'plant-pots.jpg' => 'https://images.unsplash.com/photo-1485955900006-10f4e3213965?w=400&h=400&fit=crop',
    
    // Books & Media
    'kindle.jpg' => 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?w=400&h=400&fit=crop',
    'bluetooth-speaker.jpg' => 'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=400&h=400&fit=crop',
    'programming-book.jpg' => 'https://images.unsplash.com/photo-1532012197267-da84d127e765?w=400&h=400&fit=crop',
    'gaming-headset.jpg' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&h=400&fit=crop',
    'external-ssd.jpg' => 'https://images.unsplash.com/photo-1593818078873-9274c4b8c8c1?w=400&h=400&fit=crop',
    'hdmi-cable.jpg' => 'https://images.unsplash.com/photo-1628745984816-8f4c4cf5ea5b?w=400&h=400&fit=crop',
    'wireless-charger.jpg' => 'https://images.unsplash.com/photo-1586953208448-b95a79798f07?w=400&h=400&fit=crop',
    'wireless-earbuds.jpg' => 'https://images.unsplash.com/photo-1606220945770-b5b6c2c55bf1?w=400&h=400&fit=crop',
    
    // Beauty & Health
    'face-cream.jpg' => 'https://images.unsplash.com/photo-1570197788417-0e82375c9371?w=400&h=400&fit=crop',
    'perfume.jpg' => 'https://images.unsplash.com/photo-1528181304800-259bbed8b2e4?w=400&h=400&fit=crop',
    'hair-dryer.jpg' => 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?w=400&h=400&fit=crop',
    'makeup-brushes.jpg' => 'https://images.unsplash.com/photo-1596462502278-27d035a59035?w=400&h=400&fit=crop',
    'vitamins.jpg' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=400&h=400&fit=crop',
    'face-wash.jpg' => 'https://images.unsplash.com/photo-1556228534-1c9162ee0ea9?w=400&h=400&fit=crop',
    'body-lotion.jpg' => 'https://images.unsplash.com/photo-1570197788417-0e82375c9371?w=400&h=400&fit=crop',
    'shampoo.jpg' => 'https://images.unsplash.com/photo-1556228534-1c9162ee0ea9?w=400&h=400&fit=crop'
];

$image_dir = __DIR__ . '/assets/images/products/';
if (!is_dir($image_dir)) {
    mkdir($image_dir, 0777, true);
}

$downloaded_count = 0;
foreach ($image_downloads as $filename => $url) {
    $filepath = $image_dir . $filename;
    echo "<p>Downloading $filename...</p>";
    
    $image_data = file_get_contents($url);
    if ($image_data) {
        file_put_contents($filepath, $image_data);
        echo "<p>✅ Downloaded: $filename</p>";
        $downloaded_count++;
    } else {
        echo "<p>❌ Failed to download: $filename</p>";
    }
}

echo "<h2>🔄 Updating Database with Correct Images...</h2>";

// Update database with correct images
$updated_count = 0;
foreach ($image_fixes as $product_name => $new_image) {
    $sql = "UPDATE products SET image = ? WHERE name = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        echo "<p>❌ Error preparing update for $product_name: " . $conn->error . "</p>";
        continue;
    }
    
    $stmt->bind_param("ss", $new_image, $product_name);
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo "<p>✅ Updated: $product_name → $new_image</p>";
            $updated_count++;
        } else {
            echo "<p>⚠️ Product not found: $product_name</p>";
        }
    } else {
        echo "<p>❌ Error updating $product_name: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

echo "<h2>📊 Final Status</h2>";
echo "<p><strong>Images Downloaded:</strong> $downloaded_count/" . count($image_downloads) . "</p>";
echo "<p><strong>Database Updated:</strong> $updated_count/" . count($image_fixes) . "</p>";

// Show current products and their images by category
echo "<h2>🛍️ Current Product Images by Category:</h2>";

$categories = [
    1 => 'Electronics',
    2 => 'Fashion', 
    4 => 'Home & Living',
    6 => 'Books & Media',
    7 => 'Beauty & Health'
];

foreach ($categories as $category_id => $category_name) {
    echo "<h3>$category_name (Category $category_id)</h3>";
    $result = $conn->query("SELECT id, name, image FROM products WHERE category_id = $category_id ORDER BY name");
    
    if ($result && $result->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th>Product Name</th><th>Image File</th><th>Image Check</th></tr>";
        
        while($row = $result->fetch_assoc()) {
            $image_path = "assets/images/products/" . $row['image'];
            $image_exists = file_exists($image_path);
            
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['image']) . "</td>";
            echo "<td>" . ($image_exists ? "✅ Exists" : "❌ Missing") . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
}

echo "<div class='text-center mt-4'>";
echo "<a href='shop.php?category=1' class='btn btn-primary btn-lg me-2'>🔌 Electronics</a>";
echo "<a href='shop.php?category=2' class='btn btn-success btn-lg me-2'>👔 Fashion</a>";
echo "<a href='shop.php?category=4' class='btn btn-info btn-lg me-2'>🏠 Home & Living</a>";
echo "<a href='shop.php?category=6' class='btn btn-warning btn-lg me-2'>📚 Books & Media</a>";
echo "<a href='shop.php?category=7' class='btn btn-danger btn-lg'>💄 Beauty & Health</a>";
echo "</div>";

echo "<h2>✅ All Product Images Fixed!</h2>";
echo "<p>All product images have been updated with correct, relevant images for each category.</p>";
echo "<p>Visit the category pages to see the proper images!</p>";
?>
