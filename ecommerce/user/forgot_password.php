<?php
$page_title = "Forgot Password - ShopHub";
include '../includes/header.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('index.php');
}

// Handle password reset request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    
    $errors = [];
    
    // Validate email
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    } else {
        // Check if email exists
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        
        if (!$user) {
            $errors[] = "Email not found in our system";
        }
    }
    
    if (empty($errors)) {
        // Generate reset token
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        // Store token in database (you would add a password_reset_tokens table)
        // For now, we'll just show success message
        
        $_SESSION['success_message'] = "Password reset link has been sent to your email. Please check your inbox.";
        
        // In a real application, you would send an email with the reset link
        // mail($email, "Password Reset", "Click here to reset your password: reset_password.php?token=$token");
        
        redirect('login.php');
    }
}
?>

<!-- Forgot Password Page -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="card shadow-lg">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <div class="mb-3">
                                <i class="fas fa-lock fa-3x text-primary"></i>
                            </div>
                            <h2 class="mb-3" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Forgot Password?</h2>
                            <p class="text-muted">Enter your email address and we'll send you a link to reset your password.</p>
                        </div>
                        
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo $error; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-4">
                                <label class="form-label">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input type="email" class="form-control" name="email" 
                                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                                           placeholder="Enter your email address" required>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                                <i class="fas fa-paper-plane me-2"></i>Send Reset Link
                            </button>
                        </form>
                        
                        <div class="text-center">
                            <p class="mb-0">
                                <a href="login.php" class="text-primary text-decoration-none">
                                    <i class="fas fa-arrow-left me-2"></i>Back to Login
                                </a>
                            </p>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="text-center">
                            <p class="text-muted mb-2">Remember your password?</p>
                            <a href="signup.php" class="text-primary text-decoration-none">Create an account</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
$(document).ready(function() {
    // Form validation
    $('form').submit(function(e) {
        var isValid = true;
        
        $(this).find('input[required]').each(function() {
            if (!$(this).val()) {
                isValid = false;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        // Email validation
        var email = $(this).find('input[name="email"]').val();
        if (email && !isValidEmail(email)) {
            isValid = false;
            $(this).find('input[name="email"]').addClass('is-invalid');
        }
        
        if (!isValid) {
            e.preventDefault();
            return false;
        }
        
        // Show loading state
        $(this).find('button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Sending...');
    });
    
    function isValidEmail(email) {
        var regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    }
});
</script>

<?php include '../includes/footer.php'; ?>
