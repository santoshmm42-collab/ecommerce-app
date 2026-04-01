<?php
require_once 'includes/db_connect.php';

// Handle cart AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    $action = sanitize($_POST['action']);
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    
    if ($product_id === 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid product']);
        exit;
    }
    
    // Verify product exists
    $sql = "SELECT * FROM products WHERE id = ? AND stock > 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
    
    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Product not available']);
        exit;
    }
    
    switch ($action) {
        case 'add':
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }
            
            if (isset($_SESSION['cart'][$product_id])) {
                $new_quantity = $_SESSION['cart'][$product_id] + $quantity;
                if ($new_quantity > $product['stock']) {
                    echo json_encode(['success' => false, 'message' => 'Not enough stock available']);
                    exit;
                }
                $_SESSION['cart'][$product_id] = $new_quantity;
            } else {
                if ($quantity > $product['stock']) {
                    echo json_encode(['success' => false, 'message' => 'Not enough stock available']);
                    exit;
                }
                $_SESSION['cart'][$product_id] = $quantity;
            }
            
            echo json_encode([
                'success' => true, 
                'message' => 'Product added to cart',
                'cart_count' => array_sum($_SESSION['cart'])
            ]);
            break;
            
        case 'remove':
            if (isset($_SESSION['cart'][$product_id])) {
                unset($_SESSION['cart'][$product_id]);
                echo json_encode([
                    'success' => true, 
                    'message' => 'Product removed from cart',
                    'cart_count' => array_sum($_SESSION['cart'])
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Product not in cart']);
            }
            break;
            
        case 'update':
            if (isset($_SESSION['cart'][$product_id])) {
                if ($quantity > $product['stock']) {
                    echo json_encode(['success' => false, 'message' => 'Not enough stock available']);
                    exit;
                }
                
                if ($quantity > 0) {
                    $_SESSION['cart'][$product_id] = $quantity;
                } else {
                    unset($_SESSION['cart'][$product_id]);
                }
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'Cart updated',
                    'cart_count' => array_sum($_SESSION['cart'])
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Product not in cart']);
            }
            break;
            
        case 'clear':
            $_SESSION['cart'] = [];
            echo json_encode(['success' => true, 'message' => 'Cart cleared']);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    exit;
}

$page_title = "Shopping Cart - ShopHub";
include 'includes/header.php';
?>

<!-- Cart Page -->
<section class="py-5">
    <div class="container">
        <h1 class="mb-4">Shopping Cart</h1>
        
        <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
            <div class="row">
                <div class="col-lg-8">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
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
                                ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="assets/images/products/<?php echo htmlspecialchars($product['image']); ?>" 
                                                         alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                                         class="me-3" 
                                                         style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                                                    <div>
                                                        <h6 class="mb-0"><?php echo htmlspecialchars($product['name']); ?></h6>
                                                        <small class="text-muted"><?php echo htmlspecialchars($product['description']); ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>$<?php echo number_format($product['price'], 2); ?></td>
                                            <td>
                                                <div class="input-group" style="width: 120px;">
                                                    <button class="btn btn-outline-secondary quantity-selector" type="button" data-action="decrease" data-target="#qty-<?php echo $product_id; ?>">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                    <input type="number" class="form-control text-center update-quantity" 
                                                           id="qty-<?php echo $product_id; ?>" 
                                                           value="<?php echo $quantity; ?>" 
                                                           min="1" 
                                                           max="<?php echo $product['stock']; ?>"
                                                           data-product-id="<?php echo $product_id; ?>">
                                                    <button class="btn btn-outline-secondary quantity-selector" type="button" data-action="increase" data-target="#qty-<?php echo $product_id; ?>">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                            </td>
                                            <td>$<?php echo number_format($item_total, 2); ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-danger remove-from-cart" data-product-id="<?php echo $product_id; ?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                <?php
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-between mt-4">
                        <a href="shop.php" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-2"></i>Continue Shopping
                        </a>
                        <button class="btn btn-outline-danger" onclick="clearCart()">
                            <i class="fas fa-trash me-2"></i>Clear Cart
                        </button>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Order Summary</h5>
                            
                            <div class="d-flex justify-content-between mb-3">
                                <span>Subtotal:</span>
                                <span>$<?php echo number_format($subtotal, 2); ?></span>
                            </div>
                            
                            <div class="d-flex justify-content-between mb-3">
                                <span>Shipping:</span>
                                <span id="shipping-cost">$<?php echo $subtotal > 50 ? '0.00' : '10.00'; ?></span>
                            </div>
                            
                            <div class="d-flex justify-content-between mb-3">
                                <span>Tax (10%):</span>
                                <span>$<?php echo number_format($subtotal * 0.1, 2); ?></span>
                            </div>
                            
                            <hr>
                            
                            <div class="d-flex justify-content-between mb-4">
                                <h5>Total:</h5>
                                <h5 id="total-cost">$<?php 
                                    $shipping = $subtotal > 50 ? 0 : 10;
                                    $tax = $subtotal * 0.1;
                                    echo number_format($subtotal + $shipping + $tax, 2); 
                                ?></h5>
                            </div>
                            
                            <?php if ($subtotal > 50): ?>
                                <div class="alert alert-success mb-3">
                                    <i class="fas fa-truck me-2"></i>You qualify for free shipping!
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info mb-3">
                                    <i class="fas fa-truck me-2"></i>Add $<?php echo number_format(50 - $subtotal, 2); ?> more for free shipping!
                                </div>
                            <?php endif; ?>
                            
                            <a href="checkout.php" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-lock me-2"></i>Proceed to Checkout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-shopping-cart fa-4x text-muted mb-4"></i>
                <h3>Your cart is empty</h3>
                <p class="text-muted mb-4">Looks like you haven't added anything to your cart yet.</p>
                <a href="shop.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-shopping-bag me-2"></i>Start Shopping
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
function clearCart() {
    if (confirm('Are you sure you want to clear your cart?')) {
        $.ajax({
            url: 'cart.php',
            type: 'POST',
            data: { action: 'clear' },
            success: function(response) {
                var data = JSON.parse(response);
                if (data.success) {
                    location.reload();
                }
            }
        });
    }
}

$(document).ready(function() {
    $('.quantity-selector').click(function() {
        var action = $(this).data('action');
        var target = $(this).data('target');
        var input = $(target);
        var currentValue = parseInt(input.val());
        var maxValue = parseInt(input.attr('max'));
        
        if (action === 'increase' && currentValue < maxValue) {
            input.val(currentValue + 1);
            input.trigger('change');
        } else if (action === 'decrease' && currentValue > 1) {
            input.val(currentValue - 1);
            input.trigger('change');
        }
    });
    
    $('.update-quantity').change(function() {
        var productId = $(this).data('product-id');
        var quantity = parseInt($(this).val());
        
        $.ajax({
            url: 'cart.php',
            type: 'POST',
            data: {
                action: 'update',
                product_id: productId,
                quantity: quantity
            },
            success: function(response) {
                var data = JSON.parse(response);
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message);
                }
            }
        });
    });
    
    $('.remove-from-cart').click(function() {
        var productId = $(this).data('product-id');
        
        if (confirm('Remove this item from cart?')) {
            $.ajax({
                url: 'cart.php',
                type: 'POST',
                data: {
                    action: 'remove',
                    product_id: productId
                },
                success: function(response) {
                    var data = JSON.parse(response);
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                }
            });
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>
