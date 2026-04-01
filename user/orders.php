<?php
// Redirect if not logged in
if (!isLoggedIn()) {
    redirect('user/login.php');
}

$page_title = "My Orders - ShopHub";
include '../includes/header.php';

// Get user orders
$sql = "SELECT o.*, COUNT(oi.id) as item_count 
        FROM orders o 
        LEFT JOIN order_items oi ON o.id = oi.order_id 
        WHERE o.user_id = ? 
        GROUP BY o.id 
        ORDER BY o.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$orders = $stmt->get_result();
?>

<!-- User Orders Page -->
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
                        <h5><?php echo htmlspecialchars($_SESSION['user_name']); ?></h5>
                        <p class="text-muted"><?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
                        <hr>
                        <div class="list-group list-group-flush">
                            <a href="profile.php" class="list-group-item list-group-item-action">
                                <i class="fas fa-user me-2"></i>Profile
                            </a>
                            <a href="orders.php" class="list-group-item list-group-item-action active">
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
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4>My Orders</h4>
                    <div>
                        <select class="form-select" id="orderFilter">
                            <option value="">All Orders</option>
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="shipped">Shipped</option>
                            <option value="delivered">Delivered</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
                
                <?php if ($orders->num_rows > 0): ?>
                    <?php while($order = $orders->fetch_assoc()): ?>
                        <div class="card mb-3 order-card" data-status="<?php echo $order['status']; ?>">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <h6 class="mb-1">Order #<?php echo str_pad($order['id'], 8, '0', STR_PAD_LEFT); ?></h6>
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar me-1"></i>
                                                    <?php echo date('F j, Y', strtotime($order['created_at'])); ?>
                                                </small>
                                            </div>
                                            <span class="badge bg-<?php echo getStatusColor($order['status']); ?>">
                                                <?php echo ucfirst($order['status']); ?>
                                            </span>
                                        </div>
                                        
                                        <div class="mb-2">
                                            <small class="text-muted">
                                                <i class="fas fa-box me-1"></i>
                                                <?php echo $order['item_count']; ?> items
                                            </small>
                                        </div>
                                        
                                        <div>
                                            <small class="text-muted">
                                                <i class="fas fa-truck me-1"></i>
                                                <?php echo htmlspecialchars(substr($order['shipping_address'], 0, 50)) . '...'; ?>
                                            </small>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4 text-md-end">
                                        <h5 class="mb-2">$<?php echo number_format($order['total_amount'], 2); ?></h5>
                                        <div class="btn-group">
                                            <button class="btn btn-outline-primary btn-sm" onclick="viewOrderDetails(<?php echo $order['id']; ?>)">
                                                <i class="fas fa-eye me-1"></i>View
                                            </button>
                                            <?php if ($order['status'] === 'delivered'): ?>
                                                <button class="btn btn-outline-success btn-sm" onclick="reorderItems(<?php echo $order['id']; ?>)">
                                                    <i class="fas fa-redo me-1"></i>Reorder
                                                </button>
                                            <?php endif; ?>
                                            <?php if (in_array($order['status'], ['pending', 'processing'])): ?>
                                                <button class="btn btn-outline-danger btn-sm" onclick="cancelOrder(<?php echo $order['id']; ?>)">
                                                    <i class="fas fa-times me-1"></i>Cancel
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-shopping-bag fa-4x text-muted mb-4"></i>
                        <h4>No Orders Yet</h4>
                        <p class="text-muted mb-4">You haven't placed any orders yet. Start shopping to see your orders here.</p>
                        <a href="../shop.php" class="btn btn-primary btn-lg">
                            <i class="fas fa-shopping-bag me-2"></i>Start Shopping
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Order Details Modal -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Order Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="orderDetailsContent">
                <!-- Content will be loaded via AJAX -->
            </div>
        </div>
    </div>
</div>

<?php
function getStatusColor($status) {
    $colors = [
        'pending' => 'warning',
        'processing' => 'info',
        'shipped' => 'primary',
        'delivered' => 'success',
        'cancelled' => 'danger'
    ];
    
    return isset($colors[$status]) ? $colors[$status] : 'secondary';
}
?>

<script>
$(document).ready(function() {
    // Order filter
    $('#orderFilter').change(function() {
        var status = $(this).val();
        
        if (status === '') {
            $('.order-card').show();
        } else {
            $('.order-card').hide();
            $('.order-card[data-status="' + status + '"]').show();
        }
    });
    
    // View order details
    window.viewOrderDetails = function(orderId) {
        $.ajax({
            url: 'api/order_details.php',
            type: 'GET',
            data: { order_id: orderId },
            success: function(response) {
                $('#orderDetailsContent').html(response);
                $('#orderDetailsModal').modal('show');
            },
            error: function() {
                alert('Error loading order details');
            }
        });
    };
    
    // Cancel order
    window.cancelOrder = function(orderId) {
        if (confirm('Are you sure you want to cancel this order?')) {
            $.ajax({
                url: 'api/cancel_order.php',
                type: 'POST',
                data: { order_id: orderId },
                success: function(response) {
                    var data = JSON.parse(response);
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                },
                error: function() {
                    alert('Error cancelling order');
                }
            });
        }
    };
    
    // Reorder items
    window.reorderItems = function(orderId) {
        if (confirm('Add all items from this order to your cart?')) {
            $.ajax({
                url: 'api/reorder_items.php',
                type: 'POST',
                data: { order_id: orderId },
                success: function(response) {
                    var data = JSON.parse(response);
                    if (data.success) {
                        alert('Items added to cart successfully!');
                        window.location.href = '../cart.php';
                    } else {
                        alert(data.message);
                    }
                },
                error: function() {
                    alert('Error reordering items');
                }
            });
        }
    }
});
</script>

<?php include '../includes/footer.php'; ?>
