<?php
require_once 'includes/db_connect.php';

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit_review') {
    header('Content-Type: application/json');
    
    if (!isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Please login to submit a review']);
        exit;
    }
    
    $product_id = intval($_POST['product_id']);
    $rating = intval($_POST['rating']);
    $comment = sanitize($_POST['comment']);
    $user_id = $_SESSION['user_id'];
    
    // Validate inputs
    if ($product_id <= 0 || $rating < 1 || $rating > 5 || empty($comment)) {
        echo json_encode(['success' => false, 'message' => 'Invalid review data']);
        exit;
    }
    
    // Check if user already reviewed this product
    $check_sql = "SELECT id FROM reviews WHERE product_id = ? AND user_id = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("ii", $product_id, $user_id);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'You have already reviewed this product']);
        exit;
    }
    
    // Insert review
    $insert_sql = "INSERT INTO reviews (product_id, user_id, rating, comment) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_sql);
    $stmt->bind_param("iiis", $product_id, $user_id, $rating, $comment);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Review submitted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error submitting review']);
    }
    exit;
}

// Get product ID
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($product_id === 0) {
    redirect('shop.php');
}

// Get product details
$sql = "SELECT p.*, c.name as category_name FROM products p 
        JOIN categories c ON p.category_id = c.id 
        WHERE p.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    redirect('shop.php');
}

// Get related products
$related_sql = "SELECT * FROM products WHERE category_id = ? AND id != ? ORDER BY created_at DESC LIMIT 4";
$stmt = $conn->prepare($related_sql);
$stmt->bind_param("ii", $product['category_id'], $product_id);
$stmt->execute();
$related_products = $stmt->get_result();

// Get reviews
$reviews_sql = "SELECT r.*, u.name as user_name FROM reviews r 
                JOIN users u ON r.user_id = u.id 
                WHERE r.product_id = ? 
                ORDER BY r.created_at DESC";
$stmt = $conn->prepare($reviews_sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$reviews = $stmt->get_result();

// Calculate average rating
$avg_rating_sql = "SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews FROM reviews WHERE product_id = ?";
$stmt = $conn->prepare($avg_rating_sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$rating_data = $stmt->get_result()->fetch_assoc();
$avg_rating = $rating_data['avg_rating'] ? round($rating_data['avg_rating'], 1) : 0;
$total_reviews = $rating_data['total_reviews'];

$page_title = "Product Details - ShopHub";
include 'includes/header.php';
?>

<!-- Product Details -->
<section class="py-5">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="shop.php">Shop</a></li>
                <li class="breadcrumb-item"><a href="shop.php?category=<?php echo $product['category_id']; ?>"><?php echo htmlspecialchars($product['category_name']); ?></a></li>
                <li class="breadcrumb-item active"><?php echo htmlspecialchars($product['name']); ?></li>
            </ol>
        </nav>

        <div class="row">
            <!-- Product Images -->
            <div class="col-lg-6 mb-4">
                <div class="product-detail-image">
                    <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <img src="assets/images/products/<?php echo htmlspecialchars($product['image']); ?>" 
                                     class="d-block w-100" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Info -->
            <div class="col-lg-6 mb-4">
                <h1 class="mb-3"><?php echo htmlspecialchars($product['name']); ?></h1>
                
                <div class="mb-3">
                    <span class="badge bg-primary"><?php echo htmlspecialchars($product['category_name']); ?></span>
                    <?php if ($total_reviews > 0): ?>
                        <div class="d-inline-block ms-3">
                            <div class="rating">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star <?php echo $i <= $avg_rating ? '' : 'text-muted'; ?>"></i>
                                <?php endfor; ?>
                            </div>
                            <small class="text-muted">(<?php echo $total_reviews; ?> reviews)</small>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mb-4">
                    <h2 class="product-price">$<?php echo number_format($product['price'], 2); ?></h2>
                    <p class="text-muted">Stock: <?php echo $product['stock']; ?> units available</p>
                </div>

                <div class="mb-4">
                    <h5>Description</h5>
                    <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                </div>

                <div class="mb-4">
                    <h5>Quantity</h5>
                    <div class="d-flex align-items-center">
                        <div class="input-group" style="width: 150px;">
                            <button class="btn btn-outline-secondary quantity-selector" type="button" data-action="decrease">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" class="form-control text-center" id="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>">
                            <button class="btn btn-outline-secondary quantity-selector" type="button" data-action="increase">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-3 mb-4">
                    <button class="btn btn-primary btn-lg add-to-cart" 
                            data-product-id="<?php echo $product['id']; ?>" 
                            data-quantity="1">
                        <i class="fas fa-cart-plus me-2"></i>Add to Cart
                    </button>
                    <button class="btn btn-outline-primary btn-lg add-to-wishlist" 
                            data-product-id="<?php echo $product['id']; ?>">
                        <i class="far fa-heart"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Product Tabs -->
        <div class="row mt-5">
            <div class="col-12">
                <ul class="nav nav-tabs" id="productTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" type="button">
                            Product Details
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button">
                            Reviews (<?php echo $total_reviews; ?>)
                        </button>
                    </li>
                </ul>
                <div class="tab-content mt-3" id="productTabsContent">
                    <div class="tab-pane fade show active" id="details" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Product Information</h5>
                                <table class="table">
                                    <tr>
                                        <td><strong>Category:</strong></td>
                                        <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>SKU:</strong></td>
                                        <td>PRD-<?php echo str_pad($product['id'], 6, '0', STR_PAD_LEFT); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Availability:</strong></td>
                                        <td><?php echo $product['stock'] > 0 ? 'In Stock' : 'Out of Stock'; ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="reviews" role="tabpanel">
                        <?php if (isLoggedIn()): ?>
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h5>Write a Review</h5>
                                    <form id="reviewForm">
                                        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                                        <div class="mb-3">
                                            <label class="form-label">Rating</label>
                                            <div class="rating-input">
                                                <?php for($i = 1; $i <= 5; $i++): ?>
                                                    <input type="radio" id="star<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>" required>
                                                    <label for="star<?php echo $i; ?>" class="fas fa-star"></label>
                                                <?php endfor; ?>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Your Review</label>
                                            <textarea class="form-control" name="comment" rows="3" required></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Submit Review</button>
                                    </form>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <a href="user/login.php">Login</a> to write a review.
                            </div>
                        <?php endif; ?>

                        <h5>Customer Reviews</h5>
                        <?php if ($reviews->num_rows > 0): ?>
                            <?php while($review = $reviews->fetch_assoc()): ?>
                                <div class="review-card">
                                    <div class="d-flex justify-content-between mb-2">
                                        <h6 class="mb-0"><?php echo htmlspecialchars($review['user_name']); ?></h6>
                                        <small class="text-muted"><?php echo date('M j, Y', strtotime($review['created_at'])); ?></small>
                                    </div>
                                    <div class="rating mb-2">
                                        <?php for($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star <?php echo $i <= $review['rating'] ? '' : 'text-muted'; ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <p class="mb-0"><?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p class="text-muted">No reviews yet. Be the first to review this product!</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Related Products -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-5">Related Products</h2>
        <div class="row">
            <?php if ($related_products->num_rows > 0): ?>
                <?php while($related = $related_products->fetch_assoc()): ?>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="product-card">
                            <img src="assets/images/products/<?php echo htmlspecialchars($related['image']); ?>" 
                                 class="product-image w-100" 
                                 alt="<?php echo htmlspecialchars($related['name']); ?>">
                            <div class="card-body p-3">
                                <h6 class="product-title"><?php echo htmlspecialchars($related['name']); ?></h6>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="product-price">$<?php echo number_format($related['price'], 2); ?></span>
                                    <a href="product.php?id=<?php echo $related['id']; ?>" class="btn btn-primary btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <p class="text-muted">No related products found.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<style>
.rating-input {
    display: flex;
    flex-direction: row-reverse;
    gap: 5px;
}

.rating-input input[type="radio"] {
    display: none;
}

.rating-input label {
    cursor: pointer;
    color: #ddd;
    font-size: 20px;
    transition: color 0.2s;
}

.rating-input input[type="radio"]:checked ~ label {
    color: #ffc107;
}

.rating-input label:hover,
.rating-input label:hover ~ label {
    color: #ffc107;
}
</style>

<script>
$(document).ready(function() {
    // Quantity selector
    $('.quantity-selector').click(function() {
        var action = $(this).data('action');
        var input = $('#quantity');
        var currentValue = parseInt(input.val());
        var maxValue = parseInt(input.attr('max'));
        
        if (action === 'increase' && currentValue < maxValue) {
            input.val(currentValue + 1);
        } else if (action === 'decrease' && currentValue > 1) {
            input.val(currentValue - 1);
        }
    });
    
    // Update add to cart button
    $('.add-to-cart').click(function() {
        var productId = $(this).data('product-id');
        var quantity = $('#quantity').val();
        
        $.ajax({
            url: 'cart.php',
            type: 'POST',
            data: {
                action: 'add',
                product_id: productId,
                quantity: quantity
            },
            success: function(response) {
                var data = JSON.parse(response);
                if (data.success) {
                    showNotification('Product added to cart!', 'success');
                    updateCartCount(data.cart_count);
                } else {
                    showNotification(data.message, 'error');
                }
            },
            error: function() {
                showNotification('Error adding product to cart', 'error');
            }
        });
    });
    
    // Review form submission
    $('#reviewForm').submit(function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        formData += '&action=submit_review';
        
        $.ajax({
            url: 'product.php?id=<?php echo $product_id; ?>',
            type: 'POST',
            data: formData,
            success: function(response) {
                var data = JSON.parse(response);
                if (data.success) {
                    showNotification('Review submitted successfully!', 'success');
                    $('#reviewForm')[0].reset();
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    showNotification(data.message, 'error');
                }
            },
            error: function() {
                showNotification('Error submitting review', 'error');
            }
        });
    });
});

function showNotification(message, type) {
    var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    var notification = $('<div class="alert ' + alertClass + ' alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999;">' +
        message +
        '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
        '</div>');
    
    $('body').append(notification);
    
    setTimeout(function() {
        notification.alert('close');
    }, 3000);
}

function updateCartCount(count) {
    $('.cart-count').text(count);
}
</script>

<?php include 'includes/footer.php'; ?>
