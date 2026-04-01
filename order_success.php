<?php
$page_title = "Order Success - ShopHub";
include 'includes/header.php';

// Get order ID from URL
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if ($order_id === 0) {
    redirect('index.php');
}

// Get order details
$sql = "SELECT o.*, u.name as customer_name, u.email as customer_email 
        FROM orders o 
        JOIN users u ON o.user_id = u.id 
        WHERE o.id = ? AND o.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $order_id, $_SESSION['user_id']);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    redirect('index.php');
}

// Get order items
$items_sql = "SELECT oi.*, p.name as product_name, p.image as product_image 
              FROM order_items oi 
              JOIN products p ON oi.product_id = p.id 
              WHERE oi.order_id = ?";
$stmt = $conn->prepare($items_sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_items = $stmt->get_result();
?>

<!-- Order Success Page -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <div class="success-icon mb-4">
                <i class="fas fa-check-circle fa-5x text-success"></i>
            </div>
            <h1 class="mb-3" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Order Successful!</h1>
            <p class="lead text-muted">Thank you for your purchase. Your order has been received.</p>
        </div>
        
        <div class="row">
            <!-- Order Details -->
            <div class="col-lg-8 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Order Details</h5>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <p class="mb-2"><strong>Order Number:</strong> #<?php echo str_pad($order['id'], 8, '0', STR_PAD_LEFT); ?></p>
                                <p class="mb-2"><strong>Date:</strong> <?php echo date('F j, Y, g:i a', strtotime($order['created_at'])); ?></p>
                                <p class="mb-2"><strong>Status:</strong> 
                                    <span class="badge bg-warning text-dark"><?php echo ucfirst($order['status']); ?></span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2"><strong>Customer:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                                <p class="mb-2"><strong>Email:</strong> <?php echo htmlspecialchars($order['customer_email']); ?></p>
                                <p class="mb-2"><strong>Payment:</strong> Cash on Delivery</p>
                            </div>
                        </div>
                        
                        <h6 class="mb-3">Order Items</h6>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($item = $order_items->fetch_assoc()): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="assets/images/products/<?php echo htmlspecialchars($item['product_image']); ?>" 
                                                         alt="<?php echo htmlspecialchars($item['product_name']); ?>" 
                                                         class="me-3" 
                                                         style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                                                    <div>
                                                        <h6 class="mb-0"><?php echo htmlspecialchars($item['product_name']); ?></h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?php echo $item['quantity']; ?></td>
                                            <td>$<?php echo number_format($item['price'], 2); ?></td>
                                            <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <h6 class="mb-3">Shipping Address</h6>
                                <p class="text-muted"><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="mb-3">Order Summary</h6>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal:</span>
                                    <span>$<?php echo number_format($order['total_amount'], 2); ?></span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Shipping:</span>
                                    <span>Included</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Tax:</span>
                                    <span>Included</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <strong>Total:</strong>
                                    <strong>$<?php echo number_format($order['total_amount'], 2); ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Next Steps -->
            <div class="col-lg-4 mb-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-4">What's Next?</h5>
                        
                        <div class="d-flex mb-3">
                            <div class="me-3">
                                <i class="fas fa-envelope fa-2x text-primary"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Order Confirmation</h6>
                                <p class="text-muted small mb-0">We've sent a confirmation email with your order details.</p>
                            </div>
                        </div>
                        
                        <div class="d-flex mb-3">
                            <div class="me-3">
                                <i class="fas fa-box fa-2x text-primary"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Order Processing</h6>
                                <p class="text-muted small mb-0">We'll process your order within 1-2 business days.</p>
                            </div>
                        </div>
                        
                        <div class="d-flex mb-3">
                            <div class="me-3">
                                <i class="fas fa-truck fa-2x text-primary"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Shipping</h6>
                                <p class="text-muted small mb-0">Your order will be delivered within 5-7 business days.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Need Help?</h5>
                        
                        <div class="d-grid gap-2">
                            <a href="contact.php" class="btn btn-outline-primary">
                                <i class="fas fa-headset me-2"></i>Contact Support
                            </a>
                            <a href="user/orders.php" class="btn btn-outline-primary">
                                <i class="fas fa-list me-2"></i>View My Orders
                            </a>
                            <a href="index.php" class="btn btn-primary">
                                <i class="fas fa-shopping-bag me-2"></i>Continue Shopping
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recommended Products -->
        <div class="row mt-5">
            <div class="col-12">
                <h3 class="text-center mb-4">You Might Also Like</h3>
                <div class="row">
                    <?php
                    // Get recommended products
                    $recommended_sql = "SELECT * FROM products ORDER BY RAND() LIMIT 4";
                    $recommended = $conn->query($recommended_sql);
                    
                    while($product = $recommended->fetch_assoc()):
                    ?>
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="product-card">
                                <img src="assets/images/products/<?php echo htmlspecialchars($product['image']); ?>" 
                                     class="product-image w-100" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>">
                                <div class="card-body p-3">
                                    <h6 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h6>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="product-price">$<?php echo number_format($product['price'], 2); ?></span>
                                        <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-primary btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.success-icon {
    animation: scaleIn 0.5s ease-out;
}

@keyframes scaleIn {
    from {
        transform: scale(0);
        opacity: 0;
    }
    to {
        transform: scale(1);
        opacity: 1;
    }
}
</style>

<?php include 'includes/footer.php'; ?>
