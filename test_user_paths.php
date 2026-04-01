<?php
echo "<h1>🔧 User Authentication Paths</h1>";

echo "<h2>✅ Correct URLs:</h2>";
echo "<p><a href='user/login.php' target='_blank'>🔐 Login: user/login.php</a></p>";
echo "<p><a href='user/signup.php' target='_blank'>👤 Sign Up: user/signup.php</a></p>";
echo "<p><a href='user/forgot_password.php' target='_blank'>🔑 Forgot Password: user/forgot_password.php</a></p>";

echo "<h2>❌ Incorrect URL (what you used):</h2>";
echo "<p><code>user/user/signup.php</code> - This has an extra 'user/' folder</p>";

echo "<h2>📁 File Structure:</h2>";
echo "<pre>
ecommerce-App/
├── user/
│   ├── login.php ✅
│   ├── signup.php ✅
│   ├── forgot_password.php ✅
│   └── logout.php ✅
└── index.php
</pre>";

echo "<h2>🔗 Working Links:</h2>";
echo "<div class='text-center'>";
echo "<a href='user/signup.php' class='btn btn-primary btn-lg me-2'>👤 Sign Up (Correct)</a>";
echo "<a href='user/login.php' class='btn btn-success btn-lg me-2'>🔐 Login (Correct)</a>";
echo "<a href='index.php' class='btn btn-info btn-lg'>🏠 Home</a>";
echo "</div>";

echo "<h2>✅ Solution:</h2>";
echo "<p>Use the correct URL: <code>http://localhost/ecommerce-App/user/signup.php</code></p>";
echo "<p>Remove the extra 'user/' from your URL.</p>";
?>
