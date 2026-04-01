<?php
$page_title = "Product Details - ShopHub";
include 'includes/header.php';

// Get product ID
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($product_id === 0) {
    redirect('shop.php');
}

// Get product details
$sql = "SELECT p.*, c.name as category_name FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
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
?>

<!-- Breadcrumb -->
<section class="py-3 bg-light">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="shop.php">Shop</a></li>
                <li class="breadcrumb-item active"><?php echo htmlspecialchars($product['name']); ?></li>
            </ol>
        </nav>
    </div>
</section>

<!-- Product Details -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Product Image -->
            <div class="col-lg-6 mb-4">
                <div class="product-image-container">
                    <img src="assets/images/products/<?php echo htmlspecialchars($product['image']); ?>" 
                         class="img-fluid rounded shadow" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>"
                         onerror="this.src='https://picsum.photos/seed/<?php echo urlencode($product['name']); ?>/600/600.jpg'">
                </div>
            </div>

            <!-- Product Info -->
            <div class="col-lg-6 mb-4">
                <span class="badge bg-primary mb-2"><?php echo htmlspecialchars($product['category_name']); ?></span>
                <h1 class="mb-3"><?php echo htmlspecialchars($product['name']); ?></h1>
                
                <div class="mb-4">
                    <h2 class="text-primary">₹<?php echo number_format($product['price'], 2); ?></h2>
                    <p class="text-muted">
                        <i class="fas fa-box me-2"></i><?php echo $product['stock']; ?> units available
                        <?php if ($product['stock'] < 10): ?>
                            <span class="badge bg-warning ms-2">Low Stock</span>
                        <?php endif; ?>
                    </p>
                </div>

                <div class="mb-4">
                    <h5>Description</h5>
                    <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                </div>

                <div class="mb-4">
                    <h5>Quantity</h5>
                    <div class="d-flex align-items-center">
                        <div class="input-group" style="width: 150px;">
                            <button class="btn btn-outline-secondary" type="button" onclick="decreaseQty()">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" class="form-control text-center" id="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>">
                            <button class="btn btn-outline-secondary" type="button" onclick="increaseQty()">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-3 mb-4">
                    <button class="btn btn-primary btn-lg flex-fill add-to-cart-btn" 
                            data-product-id="<?php echo $product['id']; ?>">
                        <i class="fas fa-cart-plus me-2"></i>Add to Cart
                    </button>
                    <button class="btn btn-outline-primary btn-lg" onclick="buyNow()">
                        <i class="fas fa-bolt me-2"></i>Buy Now
                    </button>
                </div>

                <!-- Product Features -->
                <div class="border-top pt-4">
                    <h5>Product Features</h5>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check text-success me-2"></i>High Quality Product</li>
                        <li><i class="fas fa-check text-success me-2"></i>Fast Delivery</li>
                        <li><i class="fas fa-check text-success me-2"></i>Secure Packaging</li>
                        <li><i class="fas fa-check text-success me-2"></i>Easy Returns</li>
                    </ul>
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
            <?php if ($related_products && $related_products->num_rows > 0): ?>
                <?php while($related = $related_products->fetch_assoc()): ?>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="card product-card">
                            <img src="assets/images/products/<?php echo htmlspecialchars($related['image']); ?>" 
                                 class="product-image" 
                                 alt="<?php echo htmlspecialchars($related['name']); ?>"
                                 onerror="this.src='https://picsum.photos/seed/<?php echo urlencode($related['name']); ?>/400/300.jpg'">
                            <div class="card-body">
                                <h5 class="product-title"><?php echo htmlspecialchars($related['name']); ?></h5>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="product-price">₹<?php echo number_format($related['price'], 2); ?></span>
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

<!-- JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function increaseQty() {
    var input = document.getElementById('quantity');
    var max = parseInt(input.getAttribute('max'));
    if (parseInt(input.value) < max) {
        input.value = parseInt(input.value) + 1;
    }
}

function decreaseQty() {
    var input = document.getElementById('quantity');
    if (parseInt(input.value) > 1) {
        input.value = parseInt(input.value) - 1;
    }
}

function buyNow() {
    // Add to cart and redirect to checkout
    addToCartAndRedirect();
}

function addToCartAndRedirect() {
    var productId = <?php echo $product_id; ?>;
    var quantity = document.getElementById('quantity').value;
    
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
                window.location.href = 'checkout.php';
            } else {
                alert(data.message);
            }
        },
        error: function() {
            alert('Error adding product to cart');
        }
    });
}

$(document).ready(function() {
    // Add to cart functionality
    $('.add-to-cart-btn').click(function() {
        var productId = $(this).data('product-id');
        var quantity = $('#quantity').val();
        var button = $(this);
        
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
                    // Update cart count
                    $('.cart-count').text(data.cart_count);
                    // Show success message
                    button.html('<i class="fas fa-check me-2"></i>Added to Cart!');
                    button.removeClass('btn-primary').addClass('btn-success');
                    setTimeout(function() {
                        button.html('<i class="fas fa-cart-plus me-2"></i>Add to Cart');
                        button.removeClass('btn-success').addClass('btn-primary');
                    }, 2000);
                } else {
                    alert(data.message);
                }
            },
            error: function() {
                alert('Error adding product to cart');
            }
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>
