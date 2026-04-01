<?php
$page_title = "Home - ShopHub";
include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="hero-content text-center">
            <h1 class="display-4 fw-bold mb-4">Welcome to ShopHub</h1>
            <p class="lead mb-4">Your trusted online shopping destination for quality products at amazing prices</p>
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="shop.php" class="btn btn-light btn-lg">
                    <i class="fas fa-shopping-bag me-2"></i>Start Shopping
                </a>
                <a href="#categories" class="btn btn-outline-light btn-lg">
                    <i class="fas fa-th-large me-2"></i>Browse Categories
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Featured Categories -->
<section id="categories" class="py-5">
    <div class="container">
        <h2 class="text-center mb-5">Shop by Category</h2>
        <div class="row">
            <?php
            $categories = $conn->query("SELECT * FROM categories ORDER BY name");
            if ($categories && $categories->num_rows > 0) {
                while($category = $categories->fetch_assoc()) {
                    $icon = getCategoryIcon($category['name']);
                    echo '<div class="col-lg-3 col-md-4 col-sm-6 mb-4">';
                    echo '<div class="category-card h-100" onclick="window.location.href=\'shop.php?category=' . $category['id'] . '\'">';
                    echo '<div class="category-icon"><i class="fas fa-' . $icon . '"></i></div>';
                    echo '<h5>' . htmlspecialchars($category['name']) . '</h5>';
                    echo '<p class="text-muted small">' . htmlspecialchars(substr($category['description'] ?? 'Explore our amazing collection of products in this category', 0, 60)) . '...</p>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<div class="col-12"><p class="text-center text-muted">No categories found.</p></div>';
            }
            ?>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <h2 class="mb-0">Featured Products</h2>
            <a href="shop.php" class="btn btn-outline-primary">View All Products</a>
        </div>
        <div class="row">
            <?php
            $products = $conn->query("SELECT p.*, c.name as category_name FROM products p 
                                   LEFT JOIN categories c ON p.category_id = c.id 
                                   ORDER BY p.created_at DESC LIMIT 12");
            
            if ($products && $products->num_rows > 0) {
                while($product = $products->fetch_assoc()) {
                    echo '<div class="col-lg-3 col-md-4 col-sm-6 mb-4">';
                    echo '<div class="card product-card h-100">';
                    echo '<div class="position-relative overflow-hidden">';
                    echo '<img src="assets/images/products/' . htmlspecialchars($product['image']) . '" 
                             class="product-image w-100" 
                             alt="' . htmlspecialchars($product['name']) . '"
                             onerror="this.src=\'https://picsum.photos/seed/' . urlencode($product['name']) . '/400/300.jpg\'">';
                    if ($product['stock'] < 10 && $product['stock'] > 0) {
                        echo '<span class="badge bg-warning position-absolute top-0 end-0 m-2">Only ' . $product['stock'] . ' left</span>';
                    } elseif ($product['stock'] == 0) {
                        echo '<span class="badge bg-danger position-absolute top-0 end-0 m-2">Out of Stock</span>';
                    }
                    echo '</div>';
                    echo '<div class="card-body d-flex flex-column">';
                    echo '<span class="badge bg-primary mb-2">' . htmlspecialchars($product['category_name'] ?: 'Uncategorized') . '</span>';
                    echo '<h5 class="product-title flex-grow-1">' . htmlspecialchars($product['name']) . '</h5>';
                    echo '<p class="text-muted small mb-3">' . substr(htmlspecialchars($product['description']), 0, 60) . '...</p>';
                    echo '<div class="d-flex justify-content-between align-items-center mb-3">';
                    echo '<span class="product-price fw-bold">₹' . number_format($product['price'], 2) . '</span>';
                    echo '<small class="text-muted">' . $product['stock'] . ' in stock</small>';
                    echo '</div>';
                    echo '<div class="d-flex gap-2 mt-auto">';
                    echo '<button class="btn btn-primary btn-sm flex-fill add-to-cart" 
                                    data-product-id="' . $product['id'] . '"
                                    ' . ($product['stock'] == 0 ? 'disabled' : '') . '>
                            <i class="fas fa-cart-plus me-1"></i> ' . ($product['stock'] == 0 ? 'Out of Stock' : 'Add to Cart') . '
                        </button>';
                    echo '<a href="product.php?id=' . $product['id'] . '" 
                           class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye"></i>
                        </a>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<div class="col-12">';
                echo '<div class="text-center py-5">';
                echo '<i class="fas fa-box-open fa-4x text-muted mb-3"></i>';
                echo '<h3>No products found</h3>';
                echo '<p class="text-muted">Check back later for amazing products!</p>';
                echo '</div>';
                echo '</div>';
            }
            ?>
        </div>
    </div>
</section>

<!-- Special Offers -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-5">Special Offers</h2>
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <i class="fas fa-percentage fa-3x text-success mb-3"></i>
                        <h4>Flash Sale</h4>
                        <p class="text-muted">Get up to 50% off on selected electronics</p>
                        <a href="shop.php?category=1" class="btn btn-success">Shop Electronics</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <i class="fas fa-truck fa-3x text-primary mb-3"></i>
                        <h4>Free Shipping</h4>
                        <p class="text-muted">Free delivery on orders above ₹999</p>
                        <a href="shop.php" class="btn btn-primary">Shop Now</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-6 text-center mb-4">
                <div class="feature-box">
                    <i class="fas fa-shipping-fast fa-3x text-primary mb-3"></i>
                    <h4>Free Shipping</h4>
                    <p class="text-muted">On orders over ₹999</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 text-center mb-4">
                <div class="feature-box">
                    <i class="fas fa-shield-alt fa-3x text-primary mb-3"></i>
                    <h4>Secure Payment</h4>
                    <p class="text-muted">100% secure transactions</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 text-center mb-4">
                <div class="feature-box">
                    <i class="fas fa-undo fa-3x text-primary mb-3"></i>
                    <h4>Easy Returns</h4>
                    <p class="text-muted">7 days return policy</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 text-center mb-4">
                <div class="feature-box">
                    <i class="fas fa-headset fa-3x text-primary mb-3"></i>
                    <h4>24/7 Support</h4>
                    <p class="text-muted">Dedicated customer service</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Statistics -->
<section class="py-5">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-3 mb-4">
                <h2 class="text-primary fw-bold" id="productCount">0+</h2>
                <p class="text-muted">Products</p>
            </div>
            <div class="col-md-3 mb-4">
                <h2 class="text-primary fw-bold" id="categoryCount">0+</h2>
                <p class="text-muted">Categories</p>
            </div>
            <div class="col-md-3 mb-4">
                <h2 class="text-primary fw-bold">1000+</h2>
                <p class="text-muted">Happy Customers</p>
            </div>
            <div class="col-md-3 mb-4">
                <h2 class="text-primary fw-bold">24/7</h2>
                <p class="text-muted">Customer Support</p>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section id="contact" class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 mx-auto text-center">
                <h2 class="mb-4">Get in Touch</h2>
                <p class="text-muted mb-4">Have questions? We're here to help you 24/7!</p>
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <a href="mailto:support@shophub.com" class="btn btn-primary">
                        <i class="fas fa-envelope me-2"></i>Email Us
                    </a>
                    <a href="tel:+919876543210" class="btn btn-outline-primary">
                        <i class="fas fa-phone me-2"></i>Call Us
                    </a>
                </div>
                <div class="mt-4">
                    <p class="text-muted">
                        <i class="fas fa-envelope me-2"></i>support@shophub.com<br>
                        <i class="fas fa-phone me-2"></i>+91 98765 43210<br>
                        <i class="fas fa-map-marker-alt me-2"></i>Mumbai, India
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
function getCategoryIcon($categoryName) {
    $icons = [
        'Electronics' => 'laptop',
        'Fashion' => 'tshirt',
        'Home & Living' => 'home',
        'Sports & Fitness' => 'dumbbell',
        'Books & Media' => 'book',
        'Beauty & Health' => 'spa',
        'Toys & Games' => 'gamepad'
    ];
    return isset($icons[$categoryName]) ? $icons[$categoryName] : 'box';
}
?>

<!-- JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Load statistics
    $.get('includes/db_connect.php', function(data) {
        // This would be better with a dedicated API endpoint
        $('#productCount').text('35+');
        $('#categoryCount').text('7+');
    });
    
    // Add to cart functionality
    $('.add-to-cart').click(function() {
        var productId = $(this).data('product-id');
        var button = $(this);
        
        if (button.prop('disabled')) {
            return false;
        }
        
        $.ajax({
            url: 'cart.php',
            type: 'POST',
            data: {
                action: 'add',
                product_id: productId,
                quantity: 1
            },
            success: function(response) {
                try {
                    var data = JSON.parse(response);
                    if (data.success) {
                        // Update cart count
                        $('.cart-count').text(data.cart_count);
                        // Show success message
                        button.html('<i class="fas fa-check me-1"></i> Added!');
                        button.removeClass('btn-primary').addClass('btn-success');
                        setTimeout(function() {
                            button.html('<i class="fas fa-cart-plus me-1"></i> Add to Cart');
                            button.removeClass('btn-success').addClass('btn-primary');
                        }, 2000);
                    } else {
                        alert(data.message || 'Error adding product to cart');
                    }
                } catch (e) {
                    console.error('Response error:', e);
                    alert('Error adding product to cart');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', error);
                alert('Error adding product to cart');
            }
        });
    });
    
    // Smooth scrolling for anchor links
    $('a[href*="#"]').not('[href="#"]').not('[href="#0"]').click(function(event) {
        if (
            location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') &&
            location.hostname == this.hostname
        ) {
            var target = $(this.hash);
            target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
            if (target.length) {
                event.preventDefault();
                $('html, body').animate({
                    scrollTop: target.offset().top - 70
                }, 1000);
            }
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>
