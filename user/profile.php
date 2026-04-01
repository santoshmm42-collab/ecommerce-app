<?php
// Redirect if not logged in
if (!isLoggedIn()) {
    redirect('user/login.php');
}

$page_title = "My Profile - ShopHub";
include '../includes/header.php';

// Get user information
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $phone = sanitize($_POST['phone']);
    $address = sanitize($_POST['address']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    $errors = [];
    
    // Validate basic fields
    if (empty($name)) {
        $errors[] = "Name is required";
    }
    
    // Validate password change if provided
    if (!empty($current_password) || !empty($new_password) || !empty($confirm_password)) {
        if (empty($current_password)) {
            $errors[] = "Current password is required to change password";
        } elseif (!password_verify($current_password, $user['password'])) {
            $errors[] = "Current password is incorrect";
        }
        
        if (empty($new_password)) {
            $errors[] = "New password is required";
        } elseif (strlen($new_password) < 6) {
            $errors[] = "New password must be at least 6 characters";
        }
        
        if ($new_password !== $confirm_password) {
            $errors[] = "New passwords do not match";
        }
    }
    
    if (empty($errors)) {
        // Update user information
        $update_sql = "UPDATE users SET name = ?, phone = ?, address = ?";
        $params = [$name, $phone, $address];
        $types = "sss";
        
        // Add password update if provided
        if (!empty($new_password)) {
            $update_sql .= ", password = ?";
            $params[] = password_hash($new_password, PASSWORD_DEFAULT);
            $types .= "s";
        }
        
        $update_sql .= " WHERE id = ?";
        $params[] = $_SESSION['user_id'];
        $types .= "i";
        
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param($types, ...$params);
        
        if ($stmt->execute()) {
            $_SESSION['user_name'] = $name;
            $_SESSION['success_message'] = "Profile updated successfully!";
            
            // Refresh user data
            $sql = "SELECT * FROM users WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $_SESSION['user_id']);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
        } else {
            $errors[] = "Error updating profile. Please try again.";
        }
    }
}
?>

<!-- User Profile Page -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <img src="https://picsum.photos/seed/user<?php echo $_SESSION['user_id']; ?>/150/150.jpg" 
                                 alt="Profile" 
                                 class="rounded-circle" 
                                 style="width: 100px; height: 100px; object-fit: cover;">
                        </div>
                        <h5><?php echo htmlspecialchars($user['name']); ?></h5>
                        <p class="text-muted"><?php echo htmlspecialchars($user['email']); ?></p>
                        <hr>
                        <div class="list-group list-group-flush">
                            <a href="profile.php" class="list-group-item list-group-item-action active">
                                <i class="fas fa-user me-2"></i>Profile
                            </a>
                            <a href="orders.php" class="list-group-item list-group-item-action">
                                <i class="fas fa-shopping-bag me-2"></i>My Orders
                            </a>
                            <a href="wishlist.php" class="list-group-item list-group-item-action">
                                <i class="fas fa-heart me-2"></i>Wishlist
                            </a>
                            <a href="settings.php" class="list-group-item list-group-item-action">
                                <i class="fas fa-cog me-2"></i>Settings
                            </a>
                            <a href="logout.php" class="list-group-item list-group-item-action text-danger">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-lg-9">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Profile Information</h4>
                        
                        <?php if (isset($_SESSION['success_message'])): ?>
                            <div class="alert alert-success">
                                <?php 
                                echo $_SESSION['success_message']; 
                                unset($_SESSION['success_message']);
                                ?>
                            </div>
                        <?php endif; ?>
                        
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
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" class="form-control" name="name" 
                                           value="<?php echo htmlspecialchars($user['name']); ?>" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email Address</label>
                                    <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                                    <small class="text-muted">Email cannot be changed</small>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" name="phone" 
                                           value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" 
                                           placeholder="Enter phone number">
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Address</label>
                                    <input type="text" class="form-control" name="address" 
                                           value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>" 
                                           placeholder="Enter address">
                                </div>
                            </div>
                            
                            <hr>
                            
                            <h5 class="mb-3">Change Password</h5>
                            <p class="text-muted mb-3">Leave blank if you don't want to change your password</p>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Current Password</label>
                                    <input type="password" class="form-control" name="current_password" 
                                           placeholder="Enter current password">
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">New Password</label>
                                    <input type="password" class="form-control" name="new_password" 
                                           placeholder="Enter new password">
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" name="confirm_password" 
                                           placeholder="Confirm new password">
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Account Statistics -->
                <div class="row mt-4">
                    <div class="col-md-4 mb-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fas fa-shopping-bag fa-2x text-primary mb-2"></i>
                                <h5>Total Orders</h5>
                                <?php
                                $order_count_sql = "SELECT COUNT(*) as count FROM orders WHERE user_id = ?";
                                $stmt = $conn->prepare($order_count_sql);
                                $stmt->bind_param("i", $_SESSION['user_id']);
                                $stmt->execute();
                                $order_count = $stmt->get_result()->fetch_assoc()['count'];
                                ?>
                                <h3><?php echo $order_count; ?></h3>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fas fa-dollar-sign fa-2x text-success mb-2"></i>
                                <h5>Total Spent</h5>
                                <?php
                                $total_spent_sql = "SELECT SUM(total_amount) as total FROM orders WHERE user_id = ? AND status != 'cancelled'";
                                $stmt = $conn->prepare($total_spent_sql);
                                $stmt->bind_param("i", $_SESSION['user_id']);
                                $stmt->execute();
                                $total_spent = $stmt->get_result()->fetch_assoc()['total'];
                                ?>
                                <h3>$<?php echo number_format($total_spent ?? 0, 2); ?></h3>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fas fa-calendar fa-2x text-info mb-2"></i>
                                <h5>Member Since</h5>
                                <h3><?php echo date('M Y', strtotime($user['created_at'])); ?></h3>
                            </div>
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
        var hasPasswordFields = false;
        
        // Check required fields
        $(this).find('input[required]').each(function() {
            if (!$(this).val()) {
                isValid = false;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        // Check password fields
        var currentPassword = $('input[name="current_password"]').val();
        var newPassword = $('input[name="new_password"]').val();
        var confirmPassword = $('input[name="confirm_password"]').val();
        
        if (currentPassword || newPassword || confirmPassword) {
            hasPasswordFields = true;
            
            if (!currentPassword) {
                isValid = false;
                $('input[name="current_password"]').addClass('is-invalid');
            }
            
            if (!newPassword) {
                isValid = false;
                $('input[name="new_password"]').addClass('is-invalid');
            }
            
            if (newPassword !== confirmPassword) {
                isValid = false;
                $('input[name="confirm_password"]').addClass('is-invalid');
            }
        }
        
        if (!isValid) {
            e.preventDefault();
            return false;
        }
        
        // Show loading state
        $(this).find('button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Saving...');
    });
    
    // Clear validation on input
    $('input').on('input', function() {
        $(this).removeClass('is-invalid');
    });
});
</script>

<?php include '../includes/footer.php'; ?>
