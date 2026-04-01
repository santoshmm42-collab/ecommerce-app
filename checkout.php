<?php
// Redirect if not logged in
if (!isLoggedIn()) {
    redirect('user/login.php?redirect=checkout.php');
}

// Redirect if cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    redirect('cart.php');
}

$page_title = "Checkout - ShopHub";
include 'includes/header.php';

// Process checkout form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shipping_address = sanitize($_POST['shipping_address']);
    $billing_address = sanitize($_POST['billing_address']);
    $payment_method = sanitize($_POST['payment_method']);
    $phone = sanitize($_POST['phone']);
    
    // Validate form
    $errors = [];
    if (empty($shipping_address)) $errors[] = "Shipping address is required";
    if (empty($billing_address)) $errors[] = "Billing address is required";
    if (empty($payment_method)) $errors[] = "Payment method is required";
    if (empty($phone)) $errors[] = "Phone number is required";
    
    if (empty($errors)) {
        // Calculate total
        $subtotal = 0;
        $order_items = [];
        
        foreach ($_SESSION['cart'] as $product_id => $quantity) {
            $sql = "SELECT * FROM products WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $product = $stmt->get_result()->fetch_assoc();
            
            if ($product && $quantity <= $product['stock']) {
                $item_total = $product['price'] * $quantity;
                $subtotal += $item_total;
                $order_items[] = [
                    'product_id' => $product_id,
                    'quantity' => $quantity,
                    'price' => $product['price']
                ];
            }
        }
        
        $shipping = $subtotal > 50 ? 0 : 10;
        $tax = $subtotal * 0.1;
        $total = $subtotal + $shipping + $tax;
        
        // Create order
        $conn->begin_transaction();
        
        try {
            // Insert order
            $order_sql = "INSERT INTO orders (user_id, total_amount, status, shipping_address) VALUES (?, ?, 'pending', ?)";
            $stmt = $conn->prepare($order_sql);
            $stmt->bind_param("ids", $_SESSION['user_id'], $total, $shipping_address);
            $stmt->execute();
            $order_id = $conn->insert_id;
            
            // Insert order items
            foreach ($order_items as $item) {
                $item_sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($item_sql);
                $stmt->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
                $stmt->execute();
                
                // Update product stock
                $update_sql = "UPDATE products SET stock = stock - ? WHERE id = ?";
                $stmt = $conn->prepare($update_sql);
                $stmt->bind_param("ii", $item['quantity'], $item['product_id']);
                $stmt->execute();
            }
            
            $conn->commit();
            
            // Clear cart
            unset($_SESSION['cart']);
            
            // Redirect to success page
            redirect('order_success.php?order_id=' . $order_id);
            
        } catch (Exception $e) {
            $conn->rollback();
            $error = "Error processing order. Please try again.";
        }
    }
}

// Get cart items and calculate totals
$cart_items = [];
$subtotal = 0;

foreach ($_SESSION['cart'] as $product_id => $quantity) {
    $sql = "SELECT * FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
    
    if ($product) {
        $item_total = $product['price'] * $quantity;
        $subtotal += $item_total;
        $cart_items[] = [
            'product' => $product,
            'quantity' => $quantity,
            'total' => $item_total
        ];
    }
}

$shipping = $subtotal > 50 ? 0 : 10;
$tax = $subtotal * 0.1;
$total = $subtotal + $shipping + $tax;

// Get user information
$user_sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($user_sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>

<!-- Checkout Page -->
<section class="py-5">
    <div class="container">
        <h1 class="mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Checkout</h1>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
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
        
        <!-- Checkout Steps -->
        <div class="checkout-steps mb-5">
            <div class="step active">
                <div class="step-circle">1</div>
                <div>Shipping</div>
            </div>
            <div class="step active">
                <div class="step-circle">2</div>
                <div>Payment</div>
            </div>
            <div class="step">
                <div class="step-circle">3</div>
                <div>Review</div>
            </div>
        </div>
        
        <form method="POST" id="checkoutForm">
            <div class="row">
                <!-- Billing Information -->
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title mb-4">
                                <i class="fas fa-user me-2"></i>Billing Information
                            </h5>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">First Name</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars(explode(' ', $user['name'])[0]); ?>" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars(explode(' ', $user['name'])[1] ?? ''); ?>" readonly>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Phone *</label>
                                <input type="tel" class="form-control" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Billing Address *</label>
                                <textarea class="form-control" name="billing_address" rows="3" required><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Shipping Information -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-truck me-2"></i>Shipping Information
                                </h5>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="sameAsBilling" checked>
                                    <label class="form-check-label" for="sameAsBilling">
                                        Same as billing address
                                    </label>
                                </div>
                            </div>
                            
                            <div id="shippingAddress">
                                <div class="mb-3">
                                    <label class="form-label">Shipping Address *</label>
                                    <textarea class="form-control" name="shipping_address" rows="3" required><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Payment Method -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title mb-4">
                                <i class="fas fa-credit-card me-2"></i>Payment Method
                            </h5>
                            
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod" checked>
                                    <label class="form-check-label" for="cod">
                                        <strong>Cash on Delivery</strong>
                                        <small class="d-block text-muted">Pay when you receive your order</small>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" id="card" value="card">
                                    <label class="form-check-label" for="card">
                                        <strong>Credit/Debit Card</strong>
                                        <small class="d-block text-muted">Visa, Mastercard, AMEX</small>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" id="paypal" value="paypal">
                                    <label class="form-check-label" for="paypal">
                                        <strong>PayPal</strong>
                                        <small class="d-block text-muted">Fast and secure payment</small>
                                    </label>
                                </div>
                            </div>
                            
                            <!-- Card Details (shown when card is selected) -->
                            <div id="cardDetails" style="display: none;">
                                <hr>
                                <div class="mb-3">
                                    <label class="form-label">Card Number</label>
                                    <input type="text" class="form-control" placeholder="1234 5678 9012 3456" maxlength="19">
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Expiry Date</label>
                                        <input type="text" class="form-control" placeholder="MM/YY" maxlength="5">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">CVV</label>
                                        <input type="text" class="form-control" placeholder="123" maxlength="3">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Order Summary -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Order Summary</h5>
                            
                            <!-- Order Items -->
                            <div class="mb-4">
                                <?php foreach ($cart_items as $item): ?>
                                    <div class="d-flex justify-content-between mb-2">
                                        <div>
                                            <h6 class="mb-0"><?php echo htmlspecialchars($item['product']['name']); ?></h6>
                                            <small class="text-muted">Qty: <?php echo $item['quantity']; ?></small>
                                        </div>
                                        <span>$<?php echo number_format($item['total'], 2); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <hr>
                            
                            <!-- Price Breakdown -->
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span>$<?php echo number_format($subtotal, 2); ?></span>
                            </div>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span>Shipping:</span>
                                <span>$<?php echo number_format($shipping, 2); ?></span>
                            </div>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tax (10%):</span>
                                <span>$<?php echo number_format($tax, 2); ?></span>
                            </div>
                            
                            <hr>
                            
                            <div class="d-flex justify-content-between mb-4">
                                <h5>Total:</h5>
                                <h5>$<?php echo number_format($total, 2); ?></h5>
                            </div>
                            
                            <!-- Security Badge -->
                            <div class="text-center mb-3">
                                <small class="text-muted">
                                    <i class="fas fa-lock me-1"></i>Secure Checkout
                                </small>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-check me-2"></i>Place Order
                            </button>
                            
                            <div class="text-center mt-3">
                                <small class="text-muted">
                                    By placing this order, you agree to our 
                                    <a href="#">Terms of Service</a> and 
                                    <a href="#">Privacy Policy</a>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<script>
$(document).ready(function() {
    // Same as billing address checkbox
    $('#sameAsBilling').change(function() {
        if ($(this).is(':checked')) {
            var billingAddress = $('textarea[name="billing_address"]').val();
            $('textarea[name="shipping_address"]').val(billingAddress).prop('readonly', true);
        } else {
            $('textarea[name="shipping_address"]').prop('readonly', false);
        }
    });
    
    // Payment method selection
    $('input[name="payment_method"]').change(function() {
        if ($(this).val() === 'card') {
            $('#cardDetails').slideDown();
        } else {
            $('#cardDetails').slideUp();
        }
    });
    
    // Form validation
    $('#checkoutForm').submit(function(e) {
        var isValid = true;
        
        // Check required fields
        $(this).find('input[required], textarea[required]').each(function() {
            if (!$(this).val()) {
                isValid = false;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: $('.is-invalid').first().offset().top - 100
            }, 500);
            return false;
        }
        
        // Show loading state
        $(this).find('button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Processing...');
    });
    
    // Card number formatting
    $('input[placeholder*="1234"]').on('input', function() {
        var value = $(this).val().replace(/\s/g, '');
        var formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
        $(this).val(formattedValue);
    });
    
    // Expiry date formatting
    $('input[placeholder="MM/YY"]').on('input', function() {
        var value = $(this).val().replace(/\D/g, '');
        if (value.length >= 2) {
            value = value.slice(0, 2) + '/' + value.slice(2, 4);
        }
        $(this).val(value);
    });
    
    // CVV validation
    $('input[placeholder="123"]').on('input', function() {
        $(this).val($(this).val().replace(/\D/g, ''));
    });
});
</script>

<?php include 'includes/footer.php'; ?>
