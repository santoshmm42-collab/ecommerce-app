<?php
require_once 'includes/db_connect.php';

// Enable debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🛍️ Adding Comprehensive Products to All Categories</h1>";

// First, let's check if tables exist and create them if needed
echo "<h2>🔧 Setting up database structure...</h2>";

// Create categories table if it doesn't exist
$create_categories = "CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($create_categories)) {
    echo "<p>✅ Categories table ready</p>";
} else {
    echo "<p>❌ Error creating categories table: " . $conn->error . "</p>";
}

// Create products table if it doesn't exist
$create_products = "CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    category_id INT,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    stock INT DEFAULT 0,
    image VARCHAR(255) DEFAULT 'default.jpg',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
)";

if ($conn->query($create_products)) {
    echo "<p>✅ Products table ready</p>";
} else {
    echo "<p>❌ Error creating products table: " . $conn->error . "</p>";
}

// Clear existing data
$conn->query("DELETE FROM products");
$conn->query("DELETE FROM categories");
echo "<p>🗑️ Cleared existing data</p>";

// Insert comprehensive categories
echo "<h2>📂 Adding categories...</h2>";
$categories = [
    ['Electronics', 'Latest electronic devices, gadgets, and technology products'],
    ['Fashion', 'Trendy clothing, footwear, and fashion accessories for all styles'],
    ['Mobiles', 'Smartphones, tablets, and mobile accessories'],
    ['Home & Living', 'Furniture, decor, kitchen appliances, and home improvement'],
    ['Sports & Fitness', 'Sports equipment, fitness gear, and athletic accessories'],
    ['Books & Media', 'Books, e-books, movies, music, and digital media'],
    ['Beauty & Health', 'Skincare, makeup, health supplements, and personal care'],
    ['Toys & Games', 'Toys, games, puzzles, and entertainment for all ages']
];

foreach ($categories as $cat) {
    $sql = "INSERT INTO categories (name, description) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        echo "<p>❌ Error preparing category statement: " . $conn->error . "</p>";
        continue;
    }
    
    $stmt->bind_param("ss", $cat[0], $cat[1]);
    if ($stmt->execute()) {
        echo "<p>✅ Category added: {$cat[0]}</p>";
    } else {
        echo "<p>❌ Error adding category {$cat[0]}: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

// Comprehensive products array
echo "<h2>📦 Adding products...</h2>";
$products = [
    // Electronics (8 products)
    ['Laptop Pro 15"', 'High-performance laptop with Intel i7, 16GB RAM, 512GB SSD, 15.6" display', 1, 89999.00, 25, 'laptop.jpg'],
    ['Smartwatch Ultra', 'Advanced fitness tracking, GPS, heart rate, water resistant, 7-day battery', 1, 24999.00, 50, 'smartwatch.jpg'],
    ['Wireless Headphones Pro', 'Premium noise-canceling with 30-hour battery, premium sound quality', 1, 12999.00, 75, 'headphones.jpg'],
    ['Tablet Pro 12.9"', '12.9" Liquid Retina display, M2 chip, perfect for work and entertainment', 1, 54999.00, 30, 'tablet.jpg'],
    ['4K Webcam Ultra', 'Ultra HD webcam with auto-focus, noise cancellation, built-in microphone', 1, 7999.00, 100, 'webcam.jpg'],
    ['Gaming Keyboard RGB', 'Mechanical gaming keyboard with RGB backlighting, programmable keys', 1, 5999.00, 60, 'keyboard.jpg'],
    ['Wireless Mouse Pro', 'Ergonomic wireless mouse with precision tracking, long battery life', 1, 2999.00, 120, 'mouse.jpg'],
    ['USB-C Hub 7-in-1', 'Multi-port USB-C hub with HDMI, USB 3.0, SD card reader', 1, 1999.00, 150, 'usbhub.jpg'],
    
    // Fashion (8 products)
    ['Premium Cotton T-Shirt', '100% organic cotton, comfortable fit, available in 6 colors', 2, 1299.00, 200, 'tshirt.jpg'],
    ['Classic Slim Fit Jeans', 'Premium denim jeans with modern slim fit and stretch comfort', 2, 2999.00, 150, 'jeans.jpg'],
    ['Sports Jacket Pro', 'Lightweight, water-resistant jacket perfect for outdoor activities', 2, 4999.00, 80, 'jacket.jpg'],
    ['Nike Running Shoes Air Max', 'Professional running shoes with advanced cushioning technology', 2, 6999.00, 120, 'nike-shoes.jpg'],
    ['Genuine Leather Wallet', 'Handcrafted leather wallet with RFID protection, multiple card slots', 2, 1999.00, 90, 'wallet.jpg'],
    ['Designer Sunglasses', 'UV protection sunglasses with premium frames and lenses', 2, 3499.00, 70, 'sunglasses.jpg'],
    ['Cotton Polo Shirt', 'Classic polo shirt with breathable fabric, perfect for casual wear', 2, 1799.00, 110, 'polo.jpg'],
    ['Leather Belt Premium', 'Genuine leather belt with classic buckle, adjustable sizing', 2, 2499.00, 85, 'belt.jpg'],
    
    // Mobiles (8 products)
    ['iPhone 13 Pro Max', 'A15 Bionic chip, Pro camera system, 5G, 6.7" display', 3, 109999.00, 40, 'iphone13.jpg'],
    ['Samsung Galaxy S23 Ultra', 'Flagship Android with S Pen, 200MP camera, 6.8" display', 3, 99999.00, 45, 'samsung.jpg'],
    ['OnePlus 11 Pro', 'Fast charging, Hasselblad camera, Snapdragon 8 Gen 2', 3, 69999.00, 60, 'oneplus.jpg'],
    ['Realme GT 2 Pro', '5G smartphone with premium features, great camera, fast performance', 3, 44999.00, 80, 'realme.jpg'],
    ['Xiaomi 13 Pro', 'Leica camera system, Snapdragon 8 Gen 2, elegant design', 3, 59999.00, 70, 'xiaomi.jpg'],
    ['iPad Air 5', '10.9" Liquid Retina display, M1 chip, perfect for creativity', 3, 49999.00, 50, 'ipad.jpg'],
    ['Samsung Galaxy Tab S8', '11" AMOLED display, S Pen included, premium tablet', 3, 44999.00, 55, 'tablet2.jpg'],
    ['Power Bank 20000mAh', 'Fast charging power bank with multiple ports, LED display', 3, 1999.00, 140, 'powerbank.jpg'],
    
    // Home & Living (8 products)
    ['Modern Sofa Set 3+1+1', 'Contemporary design sofa set with premium fabric and wooden frame', 4, 34999.00, 15, 'sofa.jpg'],
    ['Smart TV 55" 4K', 'Ultra HD Smart TV with Android TV, voice control, apps', 4, 39999.00, 25, 'tv.jpg'],
    ['Coffee Table Modern', 'Stylish coffee table with storage, tempered glass top', 4, 8999.00, 40, 'coffee-table.jpg'],
    ['Dining Table Set 6-Seater', 'Solid wood dining table with 6 comfortable chairs', 4, 24999.00, 20, 'dining.jpg'],
    ['Air Purifier Pro', 'HEPA air purifier with smart controls, covers large area', 4, 12999.00, 55, 'purifier.jpg'],
    ['LED Desk Lamp', 'Modern LED desk lamp with adjustable brightness, USB charging', 4, 1999.00, 90, 'lamp.jpg'],
    ['Wall Clock Premium', 'Silent movement wall clock with elegant design', 4, 1499.00, 100, 'clock.jpg'],
    ['Plant Pot Set', 'Set of 3 decorative plant pots with drainage system', 4, 2499.00, 75, 'plants.jpg'],
    
    // Sports & Fitness (8 products)
    ['Yoga Mat Premium Extra Thick', 'Non-slip yoga mat with alignment markers, carrying strap', 5, 2499.00, 150, 'yoga.jpg'],
    ['Adjustable Dumbbell Set', 'Space-saving dumbbell set 5-25kg with stand', 5, 14999.00, 35, 'dumbbell.jpg'],
    ['Treadmill Electric Folding', 'Electric treadmill with heart rate monitor, incline settings', 5, 29999.00, 20, 'treadmill.jpg'],
    ['Cricket Bat Professional', 'Grade A English willow cricket bat for professional play', 5, 7999.00, 45, 'cricket.jpg'],
    ['Football Professional Match', 'Official size and weight football with premium materials', 5, 1999.00, 85, 'football.jpg'],
    ['Badminton Racket Pro', 'Professional badminton racket with carbon fiber frame', 5, 3499.00, 60, 'badminton.jpg'],
    ['Skipping Rope Digital', 'Digital skipping rope with calorie counter and timer', 5, 799.00, 120, 'rope.jpg'],
    ['Gym Gloves Premium', 'Professional gym gloves with wrist support, breathable', 5, 999.00, 180, 'gloves.jpg'],
    
    // Books & Media (8 products)
    ['Kindle Paperwhite', 'Waterproof e-reader with adjustable warm light, 8GB storage', 6, 12999.00, 65, 'kindle.jpg'],
    ['JBL Flip 6 Speaker', 'Portable Bluetooth speaker with 360° sound, waterproof', 6, 6999.00, 95, 'speaker.jpg'],
    ['Programming Masterclass', 'Complete guide to modern programming languages and best practices', 6, 1499.00, 110, 'book.jpg'],
    ['Gaming Headset RGB Pro', '7.1 surround sound gaming headset with RGB lighting', 6, 3999.00, 75, 'gaming.jpg'],
    ['External SSD 1TB', 'High-speed portable SSD with USB 3.2 Gen 2 connectivity', 6, 8999.00, 85, 'ssd.jpg'],
    ['HDMI Cable 4K 2m', 'Premium HDMI cable supporting 4K@60Hz, gold plated', 6, 599.00, 200, 'hdmi.jpg'],
    ['Wireless Charger Fast', 'Fast wireless charging pad for all Qi-enabled devices', 6, 1499.00, 130, 'charger.jpg'],
    ['Bluetooth Earbuds Pro', 'True wireless earbuds with noise cancellation, 24hr battery', 6, 4999.00, 100, 'earbuds.jpg'],
    
    // Beauty & Health (8 products)
    ['Face Cream Anti-Aging', 'Premium anti-aging face cream with hyaluronic acid and vitamin C', 7, 2999.00, 125, 'cream.jpg'],
    ['Luxury Perfume Set', 'Premium fragrance set with 3 different scents, long-lasting', 7, 6999.00, 65, 'perfume.jpg'],
    ['Hair Dryer Professional', 'Ionic hair dryer with multiple heat/speed settings', 7, 4999.00, 90, 'dryer.jpg'],
    ['Makeup Brush Set Pro', 'Complete set of professional makeup brushes for all applications', 7, 3499.00, 80, 'makeup.jpg'],
    ['Vitamin Supplements Premium', 'Daily multivitamin tablets with essential nutrients', 7, 1499.00, 200, 'vitamins.jpg'],
    ['Face Wash Gel', 'Gentle face wash gel for all skin types, deep cleansing', 7, 799.00, 150, 'facewash.jpg'],
    ['Body Lotion Moisturizing', 'Deep moisturizing body lotion with natural ingredients', 7, 999.00, 140, 'lotion.jpg'],
    ['Shampoo Anti-Hair Fall', 'Professional shampoo to reduce hair fall, strengthen roots', 7, 899.00, 160, 'shampoo.jpg'],
    
    // Toys & Games (8 products)
    ['LEGO Building Set', 'Creative LEGO building set with 500+ pieces, multiple models', 8, 2999.00, 115, 'lego.jpg'],
    ['RC Car High Speed', 'Remote control car with 2.4GHz, high speed, rechargeable', 8, 4999.00, 70, 'rccar.jpg'],
    ['Board Game Strategy', 'Strategic board game for family and friends, ages 8+', 8, 1999.00, 130, 'boardgame.jpg'],
    ['Drone Camera 4K', 'HD camera drone with GPS, altitude hold, 30min flight', 8, 19999.00, 35, 'drone.jpg'],
    ['Puzzle Set 1000 Pieces', 'High-quality jigsaw puzzle with beautiful scenery', 8, 999.00, 180, 'puzzle.jpg'],
    ['Action Figure Collection', 'Set of 5 detailed action figures with accessories', 8, 2499.00, 90, 'actionfigure.jpg'],
    ['Teddy Bear Large', 'Soft and cuddly teddy bear, 24 inches, premium quality', 8, 1499.00, 105, 'teddy.jpg'],
    ['Art Kit Complete', 'Complete art kit with colors, brushes, canvas, easel', 8, 3499.00, 60, 'artkit.jpg']
];

$success_count = 0;
foreach ($products as $product) {
    $sql = "INSERT INTO products (name, description, category_id, price, stock, image) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        echo "<p>❌ Error preparing product statement: " . $conn->error . "</p>";
        continue;
    }
    
    $stmt->bind_param("sidids", $product[0], $product[1], $product[2], $product[3], $product[4], $product[5]);
    if ($stmt->execute()) {
        echo "<p>✅ Product added: {$product[0]} - ₹{$product[3]}</p>";
        $success_count++;
    } else {
        echo "<p>❌ Error adding product {$product[0]}: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

// Create admin user if not exists
echo "<h2>👤 Setting up admin access...</h2>";
$admin_check = $conn->query("SELECT * FROM admin_users WHERE username = 'admin'");
if ($admin_check->num_rows == 0) {
    // Create admin_users table if needed
    $create_admin = "CREATE TABLE IF NOT EXISTS admin_users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        name VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->query($create_admin);
    
    $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
    $sql = "INSERT INTO admin_users (username, password, name) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $hashed_password, $name);
    $username = 'admin';
    $name = 'Administrator';
    if ($stmt->execute()) {
        echo "<p>✅ Admin user created: admin / admin123</p>";
    } else {
        echo "<p>❌ Error creating admin user: " . $stmt->error . "</p>";
    }
    $stmt->close();
} else {
    echo "<p>✅ Admin user already exists</p>";
}

// Final summary
echo "<h2>🎉 Database Setup Complete!</h2>";
echo "<p><strong>Total Categories:</strong> 8</p>";
echo "<p><strong>Total Products Added:</strong> {$success_count}/64</p>";
echo "<p><strong>Products per Category:</strong> 8 each</p>";

// Display current database status
echo "<h2>📊 Current Database Status:</h2>";
$cat_count = $conn->query("SELECT COUNT(*) as count FROM categories")->fetch_assoc()['count'];
$prod_count = $conn->query("SELECT COUNT(*) as count FROM products")->fetch_assoc()['count'];
echo "<p>Categories in database: {$cat_count}</p>";
echo "<p>Products in database: {$prod_count}</p>";

echo "<div class='text-center mt-4'>";
echo "<a href='index.php' class='btn btn-primary btn-lg me-2'>🏠 View Homepage</a>";
echo "<a href='shop.php' class='btn btn-success btn-lg me-2'>🛍️ Browse Shop</a>";
echo "<a href='admin/login.php' class='btn btn-dark btn-lg'>🔐 Admin Panel</a>";
echo "</div>";
?>
