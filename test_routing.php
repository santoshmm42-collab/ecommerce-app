<?php
// Simple test file to verify routing
echo "<h1>🔍 Routing Test</h1>";
echo "<p>Current directory: " . __DIR__ . "</p>";
echo "<p>Document root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p>Request URI: " . $_SERVER['REQUEST_URI'] . "</p>";

// Test if user directory exists
$user_dir = __DIR__ . '/user';
if (is_dir($user_dir)) {
    echo "<p>✅ User directory exists</p>";
    
    // List files in user directory
    $files = scandir($user_dir);
    echo "<p>Files in user directory:</p>";
    echo "<ul>";
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            echo "<li>$file</li>";
        }
    }
    echo "</ul>";
} else {
    echo "<p>❌ User directory does not exist</p>";
}

// Test signup file specifically
$signup_file = __DIR__ . '/user/signup.php';
if (file_exists($signup_file)) {
    echo "<p>✅ signup.php exists</p>";
    echo "<p>File size: " . filesize($signup_file) . " bytes</p>";
} else {
    echo "<p>❌ signup.php does not exist</p>";
}

// Test database connection
try {
    require_once 'includes/db_connect.php';
    echo "<p>✅ Database connection working</p>";
} catch (Exception $e) {
    echo "<p>❌ Database connection failed: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h2>Test Links:</h2>";
echo "<p><a href='index.php'>👉 Homepage</a></p>";
echo "<p><a href='shop.php'>👉 Shop</a></p>";
echo "<p><a href='user/login.php'>👉 Login</a></p>";
echo "<p><a href='user/signup.php'>👉 Signup</a></p>";
echo "<p><a href='cart.php'>👉 Cart</a></p>";
?>
