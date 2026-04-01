<?php
require_once 'includes/db_connect.php';

// Enable debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>📥 Complete Database Import</h1>";

// Drop all existing tables to start fresh
echo "<h2>🗑️ Cleaning existing database...</h2>";

$conn->query("SET FOREIGN_KEY_CHECKS = 0");
$tables = ['products', 'categories', 'admin_users', 'users', 'orders'];
foreach ($tables as $table) {
    $conn->query("DROP TABLE IF EXISTS $table");
    echo "<p>✅ Dropped table: $table</p>";
}
$conn->query("SET FOREIGN_KEY_CHECKS = 1");

echo "<h2>🏗️ Creating database structure...</h2>";

// Create categories table
$sql = "CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($conn->query($sql)) {
    echo "<p>✅ Created categories table</p>";
} else {
    echo "<p>❌ Error creating categories: " . $conn->error . "</p>";
}

// Create products table
$sql = "CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    category_id INT,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    stock INT NOT NULL DEFAULT 0,
    image VARCHAR(255) DEFAULT 'default.jpg',
    sku VARCHAR(100) UNIQUE,
    status ENUM('active', 'inactive') DEFAULT 'active',
    featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_category (category_id),
    INDEX idx_status (status),
    INDEX idx_featured (featured)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($conn->query($sql)) {
    echo "<p>✅ Created products table</p>";
} else {
    echo "<p>❌ Error creating products: " . $conn->error . "</p>";
}

// Create users table
$sql = "CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    city VARCHAR(50),
    state VARCHAR(50),
    pincode VARCHAR(10),
    email_verified BOOLEAN DEFAULT FALSE,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($conn->query($sql)) {
    echo "<p>✅ Created users table</p>";
} else {
    echo "<p>❌ Error creating users: " . $conn->error . "</p>";
}

// Create orders table
$sql = "CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    payment_status ENUM('pending', 'paid', 'failed') DEFAULT 'pending',
    payment_method VARCHAR(50),
    shipping_address TEXT,
    billing_address TEXT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_status (status),
    INDEX idx_order_number (order_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($conn->query($sql)) {
    echo "<p>✅ Created orders table</p>";
} else {
    echo "<p>❌ Error creating orders: " . $conn->error . "</p>";
}

// Create order_items table
$sql = "CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_order (order_id),
    INDEX idx_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($conn->query($sql)) {
    echo "<p>✅ Created order_items table</p>";
} else {
    echo "<p>❌ Error creating order_items: " . $conn->error . "</p>";
}

// Create admin_users table
$sql = "CREATE TABLE admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE,
    role ENUM('super_admin', 'admin', 'manager') DEFAULT 'admin',
    status ENUM('active', 'inactive') DEFAULT 'active',
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($conn->query($sql)) {
    echo "<p>✅ Created admin_users table</p>";
} else {
    echo "<p>❌ Error creating admin_users: " . $conn->error . "</p>";
}

echo "<h2>📂 Importing Categories...</h2>";

// Import categories
$categories = [
    ['Electronics', 'Latest electronic devices, gadgets, and technology products including smartphones, laptops, and accessories'],
    ['Fashion', 'Trendy clothing, footwear, and fashion accessories for men, women, and kids'],
    ['Mobiles', 'Smartphones, tablets, mobile accessories, and communication devices'],
    ['Home & Living', 'Furniture, home decor, kitchen appliances, and home improvement products'],
    ['Sports & Fitness', 'Sports equipment, fitness gear, athletic accessories, and outdoor products'],
    ['Books & Media', 'Books, e-books, movies, music, digital media, and entertainment products'],
    ['Beauty & Health', 'Skincare, makeup, health supplements, personal care, and wellness products'],
    ['Toys & Games', 'Toys, games, puzzles, educational products, and entertainment for all ages']
];

foreach ($categories as $category) {
    $sql = "INSERT INTO categories (name, description) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $category[0], $category[1]);
    if ($stmt->execute()) {
        echo "<p>✅ Category imported: {$category[0]}</p>";
    } else {
        echo "<p>❌ Error importing {$category[0]}: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

echo "<h2>📦 Importing Products...</h2>";

// Import comprehensive products
$products = [
    // Electronics
    ['Laptop Pro 15"', 'High-performance laptop with Intel i7, 16GB RAM, 512GB SSD, 15.6" 4K display', 1, 89999.00, 25, 'laptop.jpg', 'LAPTOP-001', true],
    ['Smartwatch Ultra', 'Advanced fitness tracking, GPS, heart rate, water resistant, 7-day battery life', 1, 24999.00, 50, 'smartwatch.jpg', 'WATCH-001', true],
    ['Wireless Headphones Pro', 'Premium noise-canceling headphones with 30-hour battery, superior sound quality', 1, 12999.00, 75, 'headphones.jpg', 'HEADPHONE-001', false],
    ['Tablet Pro 12.9"', '12.9" Liquid Retina display, M2 chip, perfect for work and entertainment', 1, 54999.00, 30, 'tablet.jpg', 'TABLET-001', true],
    ['4K Webcam Ultra', 'Ultra HD webcam with auto-focus, noise cancellation, built-in dual microphones', 1, 7999.00, 100, 'webcam.jpg', 'WEBCAM-001', false],
    ['Gaming Keyboard RGB', 'Mechanical gaming keyboard with RGB backlighting, programmable keys', 1, 5999.00, 60, 'gaming-keyboard.jpg', 'KEYBOARD-001', false],
    ['Wireless Mouse Pro', 'Ergonomic wireless mouse with precision tracking, long battery life', 1, 2999.00, 120, 'mouse.jpg', 'MOUSE-001', false],
    ['USB-C Hub 7-in-1', 'Multi-port USB-C hub with HDMI, USB 3.0, SD card reader, PD charging', 1, 1999.00, 150, 'usb-hub.jpg', 'USBHUB-001', false],
    
    // Fashion
    ['Premium Cotton T-Shirt', '100% organic cotton t-shirt, comfortable fit, available in 6 colors', 2, 1299.00, 200, 'tshirt.jpg', 'TSHIRT-001', false],
    ['Classic Slim Fit Jeans', 'Premium denim jeans with modern slim fit and stretch comfort', 2, 2999.00, 150, 'jeans.jpg', 'JEANS-001', true],
    ['Sports Jacket Pro', 'Lightweight, water-resistant jacket perfect for outdoor activities', 2, 4999.00, 80, 'jacket.jpg', 'JACKET-001', true],
    ['Nike Running Shoes Air Max', 'Professional running shoes with advanced cushioning technology', 2, 6999.00, 120, 'nike-shoes.jpg', 'SHOES-001', true],
    ['Genuine Leather Wallet', 'Handcrafted leather wallet with RFID protection, multiple card slots', 2, 1999.00, 90, 'wallet.jpg', 'WALLET-001', false],
    ['Designer Sunglasses', 'UV protection sunglasses with premium frames and polarized lenses', 2, 3499.00, 70, 'sunglasses.jpg', 'SUNGLASS-001', false],
    ['Cotton Polo Shirt', 'Classic polo shirt with breathable fabric, perfect for casual wear', 2, 1799.00, 110, 'polo.jpg', 'POLO-001', false],
    ['Leather Belt Premium', 'Genuine leather belt with classic buckle, adjustable sizing', 2, 2499.00, 85, 'belt.jpg', 'BELT-001', false],
    
    // Mobiles
    ['iPhone 13 Pro Max', 'A15 Bionic chip, Pro camera system, 5G, 6.7" Super Retina XDR display', 3, 109999.00, 40, 'iphone13.jpg', 'IPHONE-001', true],
    ['Samsung Galaxy S23 Ultra', 'Flagship Android with S Pen, 200MP camera, 6.8" Dynamic AMOLED', 3, 99999.00, 45, 'samsung.jpg', 'SAMSUNG-001', true],
    ['OnePlus 11 Pro', 'Fast charging, Hasselblad camera, Snapdragon 8 Gen 2 processor', 3, 69999.00, 60, 'oneplus.jpg', 'ONEPLUS-001', true],
    ['Realme GT 2 Pro', '5G smartphone with premium features, great camera, fast performance', 3, 44999.00, 80, 'realme.jpg', 'REALME-001', false],
    ['Xiaomi 13 Pro', 'Leica camera system, Snapdragon 8 Gen 2, elegant ceramic design', 3, 59999.00, 70, 'xiaomi.jpg', 'XIAOMI-001', false],
    ['iPad Air 5', '10.9" Liquid Retina display, M1 chip, perfect for creativity and productivity', 3, 49999.00, 50, 'ipad.jpg', 'IPAD-001', true],
    ['Samsung Galaxy Tab S8', '11" AMOLED display, S Pen included, premium tablet experience', 3, 44999.00, 55, 'tablet2.jpg', 'GALAXYTAB-001', false],
    ['Power Bank 20000mAh', 'Fast charging power bank with multiple ports, LED display, compact design', 3, 1999.00, 140, 'powerbank.jpg', 'POWERBANK-001', false],
    
    // Home & Living
    ['Modern Sofa Set 3+1+1', 'Contemporary design sofa set with premium fabric and solid wooden frame', 4, 34999.00, 15, 'modern-sofa.jpg', 'SOFA-001', true],
    ['Smart TV 55" 4K', 'Ultra HD Smart TV with Android TV, voice control, streaming apps', 4, 39999.00, 25, 'smart-tv.jpg', 'TV-001', true],
    ['Coffee Table Modern', 'Stylish coffee table with storage, tempered glass top, modern design', 4, 8999.00, 40, 'coffee-table.jpg', 'COFFEE-001', false],
    ['Dining Table Set 6-Seater', 'Solid wood dining table with 6 comfortable chairs, elegant design', 4, 24999.00, 20, 'dining-table.jpg', 'DINING-001', false],
    ['Air Purifier Pro', 'HEPA air purifier with smart controls, covers large area, quiet operation', 4, 12999.00, 55, 'air-purifier.jpg', 'PURIFIER-001', false],
    ['LED Desk Lamp', 'Modern LED desk lamp with adjustable brightness, USB charging port', 4, 1999.00, 90, 'desk-lamp.jpg', 'LAMP-001', false],
    ['Wall Clock Premium', 'Silent movement wall clock with elegant design, accurate timekeeping', 4, 1499.00, 100, 'wall-clock.jpg', 'CLOCK-001', false],
    ['Plant Pot Set', 'Set of 3 decorative plant pots with drainage system, modern design', 4, 2499.00, 75, 'plant-pots.jpg', 'PLANTS-001', false],
    
    // Sports & Fitness
    ['Yoga Mat Premium Extra Thick', 'Non-slip yoga mat with alignment markers, carrying strap', 5, 2499.00, 150, 'yoga.jpg', 'YOGA-001', false],
    ['Adjustable Dumbbell Set', 'Space-saving dumbbell set 5-25kg with sturdy stand', 5, 14999.00, 35, 'dumbbell.jpg', 'DUMBBELL-001', true],
    ['Treadmill Electric Folding', 'Electric treadmill with heart rate monitor, incline settings', 5, 29999.00, 20, 'treadmill.jpg', 'TREADMILL-001', true],
    ['Cricket Bat Professional', 'Grade A English willow cricket bat for professional play', 5, 7999.00, 45, 'cricket.jpg', 'CRICKET-001', false],
    ['Football Professional Match', 'Official size and weight football with premium materials', 5, 1999.00, 85, 'football.jpg', 'FOOTBALL-001', false],
    ['Badminton Racket Pro', 'Professional badminton racket with carbon fiber frame', 5, 3499.00, 60, 'badminton.jpg', 'BADMINTON-001', false],
    ['Skipping Rope Digital', 'Digital skipping rope with calorie counter and timer display', 5, 799.00, 120, 'rope.jpg', 'ROPE-001', false],
    ['Gym Gloves Premium', 'Professional gym gloves with wrist support, breathable material', 5, 999.00, 180, 'gloves.jpg', 'GLOVES-001', false],
    
    // Books & Media
    ['Kindle Paperwhite', 'Waterproof e-reader with adjustable warm light, 8GB storage', 6, 12999.00, 65, 'kindle-reader.jpg', 'KINDLE-001', true],
    ['JBL Flip 6 Speaker', 'Portable Bluetooth speaker with 360° sound, waterproof design', 6, 6999.00, 95, 'bluetooth-speaker.jpg', 'SPEAKER-001', true],
    ['Programming Masterclass', 'Complete guide to modern programming languages and best practices', 6, 1499.00, 110, 'programming-book.jpg', 'BOOK-001', false],
    ['Gaming Headset RGB Pro', '7.1 surround sound gaming headset with RGB lighting', 6, 3999.00, 75, 'gaming-headset.jpg', 'HEADSET-001', false],
    ['External SSD 1TB', 'High-speed portable SSD with USB 3.2 Gen 2 connectivity', 6, 8999.00, 85, 'external-ssd.jpg', 'SSD-001', false],
    ['HDMI Cable 4K 2m', 'Premium HDMI cable supporting 4K@60Hz, gold plated connectors', 6, 599.00, 200, 'hdmi-cable.jpg', 'HDMI-001', false],
    ['Wireless Charger Fast', 'Fast wireless charging pad for all Qi-enabled devices', 6, 1499.00, 130, 'wireless-charger.jpg', 'CHARGER-001', false],
    ['Bluetooth Earbuds Pro', 'True wireless earbuds with noise cancellation, 24hr battery', 6, 4999.00, 100, 'wireless-earbuds.jpg', 'EARBUDS-001', true],
    
    // Beauty & Health
    ['Face Cream Anti-Aging', 'Premium anti-aging face cream with hyaluronic acid and vitamin C', 7, 2999.00, 125, 'face-cream.jpg', 'CREAM-001', false],
    ['Luxury Perfume Set', 'Premium fragrance set with 3 different scents, long-lasting', 7, 6999.00, 65, 'perfume-bottle.jpg', 'PERFUME-001', true],
    ['Hair Dryer Professional', 'Ionic hair dryer with multiple heat and speed settings', 7, 4999.00, 90, 'hair-dryer.jpg', 'DRYER-001', false],
    ['Makeup Brush Set Pro', 'Complete set of professional makeup brushes for all applications', 7, 3499.00, 80, 'makeup-brushes.jpg', 'BRUSHES-001', false],
    ['Vitamin Supplements Premium', 'Daily multivitamin tablets with essential nutrients', 7, 1499.00, 200, 'vitamins-supplements.jpg', 'VITAMINS-001', false],
    ['Face Wash Gel', 'Gentle face wash gel for all skin types, deep cleansing formula', 7, 799.00, 150, 'face-wash.jpg', 'FACEWASH-001', false],
    ['Body Lotion Moisturizing', 'Deep moisturizing body lotion with natural ingredients', 7, 999.00, 140, 'body-lotion.jpg', 'LOTION-001', false],
    ['Shampoo Anti-Hair Fall', 'Professional shampoo to reduce hair fall, strengthen roots', 7, 899.00, 160, 'shampoo-bottle.jpg', 'SHAMPOO-001', false],
    
    // Toys & Games
    ['LEGO Building Set', 'Creative LEGO building set with 500+ pieces, multiple models', 8, 2999.00, 115, 'lego.jpg', 'LEGO-001', true],
    ['RC Car High Speed', 'Remote control car with 2.4GHz, high speed, rechargeable battery', 8, 4999.00, 70, 'rccar.jpg', 'RCCAR-001', false],
    ['Board Game Strategy', 'Strategic board game for family and friends, ages 8+', 8, 1999.00, 130, 'boardgame.jpg', 'BOARDGAME-001', false],
    ['Drone Camera 4K', 'HD camera drone with GPS, altitude hold, 30min flight time', 8, 19999.00, 35, 'drone.jpg', 'DRONE-001', true],
    ['Puzzle Set 1000 Pieces', 'High-quality jigsaw puzzle with beautiful scenery', 8, 999.00, 180, 'puzzle.jpg', 'PUZZLE-001', false],
    ['Action Figure Collection', 'Set of 5 detailed action figures with accessories', 8, 2499.00, 90, 'actionfigure.jpg', 'ACTION-001', false],
    ['Teddy Bear Large', 'Soft and cuddly teddy bear, 24 inches, premium quality', 8, 1499.00, 105, 'teddy.jpg', 'TEDDY-001', false],
    ['Art Kit Complete', 'Complete art kit with colors, brushes, canvas, easel', 8, 3499.00, 60, 'artkit.jpg', 'ARTKIT-001', false]
];

$product_count = 0;
foreach ($products as $product) {
    $sql = "INSERT INTO products (name, description, category_id, price, stock, image, sku, featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sididssi", $product[0], $product[1], $product[2], $product[3], $product[4], $product[5], $product[6], $product[7]);
    if ($stmt->execute()) {
        $product_count++;
        echo "<p>✅ Product imported: {$product[0]} (SKU: {$product[6]})</p>";
    } else {
        echo "<p>❌ Error importing {$product[0]}: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

echo "<h2>👤 Creating Admin User...</h2>";

// Create admin user
$hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
$sql = "INSERT INTO admin_users (username, password, name, email, role) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$username = 'admin';
$name = 'Administrator';
$email = 'admin@shophub.com';
$role = 'super_admin';
$stmt->bind_param("sssss", $username, $hashed_password, $name, $email, $role);
if ($stmt->execute()) {
    echo "<p>✅ Admin user created: admin / admin123</p>";
} else {
    echo "<p>❌ Error creating admin: " . $stmt->error . "</p>";
}
$stmt->close();

echo "<h2>📊 Import Summary</h2>";

// Get final counts
$cat_count = $conn->query("SELECT COUNT(*) as count FROM categories")->fetch_assoc()['count'];
$prod_count = $conn->query("SELECT COUNT(*) as count FROM products")->fetch_assoc()['count'];
$admin_count = $conn->query("SELECT COUNT(*) as count FROM admin_users")->fetch_assoc()['count'];
$featured_count = $conn->query("SELECT COUNT(*) as count FROM products WHERE featured = 1")->fetch_assoc()['count'];

echo "<div style='background-color: #f0f8ff; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h3>🎉 Database Import Complete!</h3>";
echo "<p><strong>Categories:</strong> {$cat_count}/8 ✅</p>";
echo "<p><strong>Products:</strong> {$prod_count}/64 ✅</p>";
echo "<p><strong>Featured Products:</strong> {$featured_count} ✅</p>";
echo "<p><strong>Admin Users:</strong> {$admin_count} ✅</p>";
echo "</div>";

echo "<h3>🔐 Login Credentials:</h3>";
echo "<p><strong>Admin Username:</strong> admin</p>";
echo "<p><strong>Admin Password:</strong> admin123</p>";

echo "<div class='text-center mt-4'>";
echo "<a href='index.php' class='btn btn-primary btn-lg me-2'>🏠 View Homepage</a>";
echo "<a href='shop.php' class='btn btn-success btn-lg me-2'>🛍️ Browse Shop</a>";
echo "<a href='admin/login.php' class='btn btn-dark btn-lg'>🔐 Admin Panel</a>";
echo "</div>";

echo "<h2>✅ Database Successfully Imported!</h2>";
echo "<p>Your e-commerce database is now fully populated with 64 products across 8 categories.</p>";
?>
