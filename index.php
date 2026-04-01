<?php
$page_title = "Home - ShopHub";
include 'includes/header.php';
?>

<!-- Hero Section with Slider -->
<section class="hero-section">
    <div id="heroSlider" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#heroSlider" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#heroSlider" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#heroSlider" data-bs-slide-to="2"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <div class="hero-content text-center">
                    <h1 class="display-4 fw-bold mb-4">Summer Sale 2024</h1>
                    <p class="lead mb-4">Get up to 50% off on selected items</p>
                    <a href="shop.php" class="btn btn-primary btn-lg">Shop Now</a>
                </div>
            </div>
            <div class="carousel-item">
                <div class="hero-content text-center">
                    <h1 class="display-4 fw-bold mb-4">New Arrivals</h1>
                    <p class="lead mb-4">Discover the latest trends</p>
                    <a href="shop.php?category=new" class="btn btn-primary btn-lg">Explore</a>
                </div>
            </div>
            <div class="carousel-item">
                <div class="hero-content text-center">
                    <h1 class="display-4 fw-bold mb-4">Free Shipping</h1>
                    <p class="lead mb-4">On orders over ₹999</p>
                    <a href="shop.php" class="btn btn-primary btn-lg">Start Shopping</a>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#heroSlider" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroSlider" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>
</section>

<!-- Featured Categories -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-5" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Featured Categories</h2>
        <div class="row">
            <?php
            $sql = "SELECT * FROM categories ORDER BY name LIMIT 6";
            $result = $conn->query($sql);
            
            if ($result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo '<div class="col-lg-4 col-md-4 col-sm-6 mb-4">';
                    echo '<div class="category-card" onclick="window.location.href=\'shop.php?category=' . $row['id'] . '\'">';
                    echo '<div class="category-icon">';
                    echo '<i class="fas fa-' . getCategoryIcon($row['name']) . '"></i>';
                    echo '</div>';
                    echo '<h5>' . htmlspecialchars($row['name']) . '</h5>';
                    echo '<p class="text-muted small">' . htmlspecialchars(substr($row['description'] ?? 'Explore our collection', 0, 60)) . '...</p>';
                    echo '</div>';
                    echo '</div>';
                }
            }
            ?>
        </div>
    </div>
</section>

<!-- Trending Products -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-5" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Trending Products</h2>
        <div class="row">
            <?php
            $sql = "SELECT p.*, c.name as category_name FROM products p 
                    JOIN categories c ON p.category_id = c.id 
                    ORDER BY p.created_at DESC LIMIT 12";
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo '<div class="col-lg-3 col-md-4 col-sm-6 mb-4">';
                    echo '<div class="product-card card h-100">';
                    echo '<img src="assets/images/products/' . htmlspecialchars($row['image'] ?? 'default.jpg') . '" 
                             class="product-image w-100" 
                             alt="' . htmlspecialchars($row['name']) . '"
                             onerror="this.src=\'https://picsum.photos/seed/' . urlencode($row['name']) . '/400/300.jpg\'">';
                    echo '<div class="card-body p-3 d-flex flex-column">';
                    echo '<span class="badge bg-primary mb-2">' . htmlspecialchars($row['category_name'] ?? 'Uncategorized') . '</span>';
                    echo '<h5 class="product-title">' . htmlspecialchars($row['name']) . '</h5>';
                    echo '<p class="text-muted small">' . htmlspecialchars(substr($row['description'] ?? 'Great product', 0, 60)) . '...</p>';
                    echo '<div class="d-flex justify-content-between align-items-center mt-auto">';
                    echo '<span class="product-price fw-bold">₹' . number_format($row['price'] ?? 0, 2) . '</span>';
                    echo '<button class="btn btn-primary btn-sm add-to-cart" data-product-id="' . $row['id'] . '">';
                    echo '<i class="fas fa-cart-plus me-1"></i>Add';
                    echo '</button>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<div class="col-12 text-center">';
                echo '<p class="text-muted">No products found.</p>';
                echo '</div>';
            }
            ?>
        </div>
        <div class="text-center mt-4">
            <a href="shop.php" class="btn btn-outline-primary btn-lg">View All Products</a>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-5">
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

<?php
function getCategoryIcon($categoryName) {
    $icons = [
        'Electronics' => 'laptop',
        'Fashion' => 'tshirt',
        'Mobiles' => 'mobile-alt',
        'Home' => 'home',
        'Sports' => 'football-ball',
        'Books' => 'book'
    ];
    return $icons[$categoryName] ?? 'box';
}
?>

<!-- JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Add to cart functionality
    $('.add-to-cart').click(function() {
        var productId = $(this).data('product-id');
        var button = $(this);
        
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
                        // Update cart count if exists
                        if ($('.cart-count').length) {
                            $('.cart-count').text(data.cart_count || 1);
                        }
                        // Show success feedback
                        button.html('<i class="fas fa-check me-1"></i>Added!');
                        button.removeClass('btn-primary').addClass('btn-success');
                        setTimeout(function() {
                            button.html('<i class="fas fa-cart-plus me-1"></i>Add');
                            button.removeClass('btn-success').addClass('btn-primary');
                        }, 2000);
                    } else {
                        alert(data.message || 'Error adding to cart');
                    }
                } catch (e) {
                    console.error('Response error:', e);
                    alert('Error adding to cart');
                }
            },
            error: function() {
                alert('Error adding to cart');
            }
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>
