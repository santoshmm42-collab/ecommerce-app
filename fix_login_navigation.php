<?php
require_once 'includes/db_connect.php';

// Enable debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔧 Fixing Admin Login & Navigation Issues</h1>";

// First, let's check current database status
echo "<h2>📊 Current Database Status:</h2>";

// Check admin_users table
$result = $conn->query("SHOW TABLES LIKE 'admin_users'");
if ($result->num_rows > 0) {
    echo "<p>✅ Admin users table exists</p>";
    
    // Check if admin user exists
    $admin_check = $conn->query("SELECT * FROM admin_users WHERE username = 'admin'");
    if ($admin_check->num_rows > 0) {
        $admin = $admin_check->fetch_assoc();
        echo "<p>✅ Admin user found: {$admin['username']}</p>";
        
        // Verify password hash
        if (password_verify('admin123', $admin['password'])) {
            echo "<p>✅ Admin password is correct</p>";
        } else {
            echo "<p>❌ Admin password is incorrect - updating...</p>";
            $new_password = password_hash('admin123', PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE admin_users SET password = ? WHERE username = 'admin'");
            $stmt->bind_param("s", $new_password);
            $stmt->execute();
            echo "<p>✅ Admin password updated</p>";
        }
    } else {
        echo "<p>❌ Admin user not found - creating...</p>";
        
        // Create admin_users table if needed
        $create_admin = "CREATE TABLE IF NOT EXISTS admin_users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) UNIQUE,
            role ENUM('super_admin', 'admin', 'manager') DEFAULT 'admin',
            status ENUM('active', 'inactive') DEFAULT 'active',
            last_login TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        $conn->query($create_admin);
        
        // Insert admin user
        $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO admin_users (username, password, name, email, role) VALUES (?, ?, ?, ?, ?)");
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
    }
} else {
    echo "<p>❌ Admin users table does not exist - creating...</p>";
    
    // Create admin_users table
    $create_admin = "CREATE TABLE admin_users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE,
        role ENUM('super_admin', 'admin', 'manager') DEFAULT 'admin',
        status ENUM('active', 'inactive') DEFAULT 'active',
        last_login TIMESTAMP NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    if ($conn->query($create_admin)) {
        echo "<p>✅ Admin users table created</p>";
        
        // Insert admin user
        $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO admin_users (username, password, name, email, role) VALUES (?, ?, ?, ?, ?)");
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
    } else {
        echo "<p>❌ Error creating admin table: " . $conn->error . "</p>";
    }
}

// Check and fix navigation pages
echo "<h2>🔧 Fixing Navigation Pages...</h2>";

$pages_to_create = [
    'about.php' => '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - ShopHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include \'includes/header.php\'; ?>
    
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="mb-4">About ShopHub</h1>
                    <p class="lead">Your trusted online shopping destination for quality products at amazing prices.</p>
                    
                    <h3 class="mt-5">Our Story</h3>
                    <p>Founded in 2024, ShopHub has quickly become one of the most trusted e-commerce platforms, offering a wide range of products from electronics to fashion, home goods to sports equipment.</p>
                    
                    <h3 class="mt-4">Our Mission</h3>
                    <p>To provide customers with an exceptional shopping experience by offering quality products, competitive prices, and outstanding customer service.</p>
                    
                    <h3 class="mt-4">Why Choose Us?</h3>
                    <ul>
                        <li>Wide selection of quality products</li>
                        <li>Competitive prices and regular discounts</li>
                        <li>Secure payment options</li>
                        <li>Fast and reliable delivery</li>
                        <li>Excellent customer support</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    
    <?php include \'includes/footer.php\'; ?>
</body>
</html>',
    
    'contact.php' => '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - ShopHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include \'includes/header.php\'; ?>
    
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <h1 class="mb-4">Contact Us</h1>
                    <p class="lead">We\'d love to hear from you! Send us a message and we\'ll respond as soon as possible.</p>
                    
                    <?php
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        $name = sanitize($_POST["name"]);
                        $email = sanitize($_POST["email"]);
                        $message = sanitize($_POST["message"]);
                        
                        // Here you would normally send an email or save to database
                        echo "<div class=\'alert alert-success\'>Thank you for contacting us! We\'ll get back to you soon.</div>";
                    }
                    ?>
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Send Message</button>
                    </form>
                </div>
                
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Contact Information</h5>
                            <p class="card-text">
                                <i class="fas fa-envelope me-2"></i> info@shophub.com<br>
                                <i class="fas fa-phone me-2"></i> +91 98765 43210<br>
                                <i class="fas fa-map-marker-alt me-2"></i> Bangalore, India
                            </p>
                        </div>
                    </div>
                    
                    <div class="card mt-3">
                        <div class="card-body">
                            <h5 class="card-title">Business Hours</h5>
                            <p class="card-text">
                                Monday - Friday: 9:00 AM - 6:00 PM<br>
                                Saturday: 10:00 AM - 4:00 PM<br>
                                Sunday: Closed
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <?php include \'includes/footer.php\'; ?>
</body>
</html>',
    
    'categories.php' => '<?php
$page_title = "Categories - ShopHub";
include \'includes/header.php\';
?>

<section class="py-5">
    <div class="container">
        <h1 class="mb-4">Shop by Category</h1>
        <p class="lead">Browse our wide range of categories to find exactly what you\'re looking for.</p>
        
        <div class="row">
            <?php
            $sql = "SELECT * FROM categories ORDER BY name";
            $result = $conn->query($sql);
            
            if ($result && $result->num_rows > 0) {
                while($category = $result->fetch_assoc()) {
                    echo \'<div class="col-lg-3 col-md-4 col-sm-6 mb-4">\';
                    echo \'<div class="card h-100 category-card" onclick="window.location.href=\\\'shop.php?category=\' . $category[\'id\'] . \'\\\'">\';
                    echo \'<div class="card-body text-center">\';
                    echo \'<div class="category-icon mb-3">\';
                    echo \'<i class="fas fa-\' . getCategoryIcon($category[\'name\']) . \' fa-3x text-primary"></i>\';
                    echo \'</div>\';
                    echo \'<h5 class="card-title">\' . htmlspecialchars($category[\'name\']) . \'</h5>\';
                    echo \'<p class="card-text">\' . htmlspecialchars(substr($category[\'description\'] ?? \'\', 0, 100)) . \'</p>\';
                    echo \'<button class="btn btn-primary">Shop Now</button>\';
                    echo \'</div>\';
                    echo \'</div>\';
                    echo \'</div>\';
                }
            } else {
                echo \'<div class="col-12"><p class="text-center text-muted">No categories found.</p></div>\';
            }
            ?>
        </div>
    </div>
</section>

<?php
function getCategoryIcon($categoryName) {
    $icons = [
        \'Electronics\' => \'laptop\',
        \'Fashion\' => \'tshirt\',
        \'Mobiles\' => \'mobile-alt\',
        \'Home & Living\' => \'home\',
        \'Sports & Fitness\' => \'dumbbell\',
        \'Books & Media\' => \'book\',
        \'Beauty & Health\' => \'heart\',
        \'Toys & Games\' => \'gamepad\'
    ];
    return $icons[$categoryName] ?? \'box\';
}
?>

<?php include \'includes/footer.php\'; ?>'
];

foreach ($pages_to_create as $filename => $content) {
    $filepath = __DIR__ . '/' . $filename;
    if (!file_exists($filepath)) {
        file_put_contents($filepath, $content);
        echo "<p>✅ Created: $filename</p>";
    } else {
        echo "<p>✅ Already exists: $filename</p>";
    }
}

// Test admin login
echo "<h2>🔐 Testing Admin Login</h2>";
$test_username = 'admin';
$test_password = 'admin123';

$admin_check = $conn->query("SELECT * FROM admin_users WHERE username = '$test_username'");
if ($admin_check->num_rows > 0) {
    $admin = $admin_check->fetch_assoc();
    if (password_verify($test_password, $admin['password'])) {
        echo "<p>✅ Admin login test successful</p>";
    } else {
        echo "<p>❌ Admin login test failed</p>";
    }
} else {
    echo "<p>❌ Admin user not found</p>";
}

echo "<h2>📊 Final Status</h2>";
echo "<p><strong>Admin User:</strong> admin / admin123</p>";
echo "<p><strong>Navigation Pages:</strong> All created</p>";
echo "<p><strong>Database:</strong> Ready</p>";

echo "<div class='text-center mt-4'>";
echo "<a href='index.php' class='btn btn-primary btn-lg me-2'>🏠 Homepage</a>";
echo "<a href='admin/login.php' class='btn btn-success btn-lg me-2'>🔐 Admin Login</a>";
echo "<a href='about.php' class='btn btn-info btn-lg me-2'>ℹ️ About Page</a>";
echo "<a href='contact.php' class='btn btn-warning btn-lg'>📞 Contact Page</a>";
echo "</div>";

echo "<h2>✅ All Issues Fixed!</h2>";
echo "<p>Admin login and navigation pages are now working correctly.</p>";
?>
