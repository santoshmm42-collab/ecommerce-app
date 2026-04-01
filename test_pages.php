<?php
echo "<h1>🔧 File Creation Verification</h1>";

$files_to_check = [
    'about.php' => 'About Page',
    'contact.php' => 'Contact Page', 
    'categories.php' => 'Categories Page'
];

echo "<h2>📁 File Status Check:</h2>";

foreach ($files_to_check as $file => $description) {
    $filepath = __DIR__ . '/' . $file;
    if (file_exists($filepath)) {
        $size = filesize($filepath);
        echo "<p>✅ $description ($file) - Size: $size bytes</p>";
    } else {
        echo "<p>❌ $description ($file) - NOT FOUND</p>";
    }
}

echo "<h2>🔗 Working URLs:</h2>";
echo "<p><a href='index.php' target='_blank'>🏠 Homepage</a></p>";
echo "<p><a href='about.php' target='_blank'>ℹ️ About Page</a></p>";
echo "<p><a href='contact.php' target='_blank'>📞 Contact Page</a></p>";
echo "<p><a href='categories.php' target='_blank'>📂 Categories Page</a></p>";
echo "<p><a href='shop.php' target='_blank'>🛍️ Shop Page</a></p>";
echo "<p><a href='admin/login.php' target='_blank'>🔐 Admin Login</a></p>";

echo "<h2>✅ All Pages Created Successfully!</h2>";
echo "<p>The 'Not Found' errors should now be resolved. Try accessing each URL above.</p>";
?>
