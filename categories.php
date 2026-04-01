<?php
$page_title = "Categories - ShopHub";
include 'includes/header.php';
?>

<!-- Categories Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h1 class="display-4 fw-bold mb-4" style="font-family: 'Poppins', sans-serif;">Shop by Category</h1>
            <p class="lead text-muted">Browse our wide range of categories to find exactly what you're looking for</p>
        </div>
        
        <div class="row">
            <?php
            try {
                $sql = "SELECT * FROM categories ORDER BY name";
                $result = $conn->query($sql);
                
                if ($result && $result->num_rows > 0) {
                    while($category = $result->fetch_assoc()) {
                        // Get product count for this category (simplified query)
                        $count_sql = "SELECT COUNT(*) as product_count FROM products WHERE category_id = ?";
                        $count_stmt = $conn->prepare($count_sql);
                        
                        if ($count_stmt) {
                            $count_stmt->bind_param("i", $category['id']);
                            $count_stmt->execute();
                            $count_result = $count_stmt->get_result();
                            $product_count = $count_result ? $count_result->fetch_assoc()['product_count'] : 0;
                            $count_stmt->close();
                        } else {
                            $product_count = 0;
                        }
                    
                    echo '<div class="col-lg-3 col-md-4 col-sm-6 mb-4">';
                    echo '<div class="card h-100 category-card" onclick="window.location.href=\'shop.php?category=' . $category['id'] . '\'" style="cursor: pointer; transition: transform 0.3s, box-shadow 0.3s;">';
                    echo '<div class="card-body text-center p-4">';
                    echo '<div class="category-icon mb-3">';
                    echo '<i class="fas fa-' . getCategoryIcon($category['name']) . ' fa-3x text-primary"></i>';
                    echo '</div>';
                    echo '<h5 class="card-title">' . htmlspecialchars($category['name']) . '</h5>';
                    echo '<p class="card-text text-muted">' . htmlspecialchars(substr($category['description'] ?? 'Explore our amazing collection', 0, 80)) . '...</p>';
                    echo '<div class="mt-auto">';
                    echo '<span class="badge bg-primary">' . $product_count . ' Products</span>';
                    echo '<br><br>';
                    echo '<button class="btn btn-primary btn-sm">Shop Now</button>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<div class="col-12">';
                echo '<div class="text-center py-5">';
                echo '<i class="fas fa-folder-open fa-3x text-muted mb-3"></i>';
                echo '<h4 class="text-muted">No Categories Found</h4>';
                echo '<p class="text-muted">Please check back later or contact support.</p>';
                echo '<a href="index.php" class="btn btn-primary mt-3">Return to Home</a>';
                echo '</div>';
                echo '</div>';
            }
            } catch (Exception $e) {
                echo '<div class="col-12">';
                echo '<div class="alert alert-warning">';
                echo '<h4>Database Connection Issue</h4>';
                echo '<p>We are experiencing technical difficulties. Please try again later.</p>';
                echo '<a href="index.php" class="btn btn-primary">Return to Home</a>';
                echo '</div>';
                echo '</div>';
            }
            ?>
        </div>
        
        <!-- Quick Links -->
        <div class="row mt-5">
            <div class="col-lg-12">
                <div class="card bg-light">
                    <div class="card-body p-4">
                        <h4 class="mb-4 text-center" style="font-family: 'Montserrat', sans-serif; font-weight: 600;">Quick Links</h4>
                        <div class="row">
                            <div class="col-md-3 text-center mb-3">
                                <a href="shop.php" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-shopping-bag me-2"></i>All Products
                                </a>
                            </div>
                            <div class="col-md-3 text-center mb-3">
                                <a href="index.php" class="btn btn-outline-success w-100">
                                    <i class="fas fa-home me-2"></i>Home
                                </a>
                            </div>
                            <div class="col-md-3 text-center mb-3">
                                <a href="contact.php" class="btn btn-outline-info w-100">
                                    <i class="fas fa-phone me-2"></i>Contact Us
                                </a>
                            </div>
                            <div class="col-md-3 text-center mb-3">
                                <a href="about.php" class="btn btn-outline-warning w-100">
                                    <i class="fas fa-info-circle me-2"></i>About Us
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.category-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.category-icon {
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>

<?php
function getCategoryIcon($categoryName) {
    $icons = [
        'Electronics' => 'laptop',
        'Fashion' => 'tshirt',
        'Mobiles' => 'mobile-alt',
        'Home & Living' => 'home',
        'Sports & Fitness' => 'dumbbell',
        'Books & Media' => 'book',
        'Beauty & Health' => 'heart',
        'Toys & Games' => 'gamepad'
    ];
    return $icons[$categoryName] ?? 'box';
}
?>

<?php include 'includes/footer.php'; ?>
