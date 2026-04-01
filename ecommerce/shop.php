<?php
$page_title = "Shop - ShopHub";
include 'includes/header.php';

// Get filter parameters with proper sanitization
$category = isset($_GET['category']) ? intval($_GET['category']) : 0;
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$sort = isset($_GET['sort']) ? sanitize($_GET['sort']) : 'created_at';
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 12;
$offset = ($page - 1) * $limit;

// Build WHERE clause
$where = [];
$params = [];
$types = '';

if ($category > 0) {
    $where[] = "p.category_id = ?";
    $params[] = $category;
    $types .= 'i';
}

if (!empty($search)) {
    $where[] = "(p.name LIKE ? OR p.description LIKE ?)";
    $searchParam = "%$search%";
    $params[] = $searchParam;
    $params[] = $searchParam;
    $types .= 'ss';
}

$whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

// Sorting options
$sortOptions = [
    'price_low' => 'p.price ASC',
    'price_high' => 'p.price DESC',
    'name' => 'p.name ASC',
    'created_at' => 'p.created_at DESC',
    'name_desc' => 'p.name DESC'
];

$orderBy = isset($sortOptions[$sort]) ? $sortOptions[$sort] : $sortOptions['created_at'];

// Get total products count
$countSql = "SELECT COUNT(*) as total FROM products p $whereClause";
$stmt = $conn->prepare($countSql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$totalProducts = $stmt->get_result()->fetch_assoc()['total'];
$totalPages = ceil($totalProducts / $limit);

// Get products with proper JOIN
$sql = "SELECT p.*, c.name as category_name FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        $whereClause 
        ORDER BY $orderBy 
        LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$bindParams = array_merge($params, [$limit, $offset]);
$bindTypes = $types . 'ii';
$stmt->bind_param($bindTypes, ...$bindParams);
$stmt->execute();
$products = $stmt->get_result();

// Get all categories for filter
$categories = $conn->query("SELECT * FROM categories ORDER BY name");
?>

<!-- Shop Header -->
<section class="py-4 bg-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="mb-0" style="font-family: 'Montserrat', sans-serif; font-weight: 600;">Shop</h1>
                <p class="text-muted mb-0">
                    Showing <?php echo min($totalProducts, $limit); ?> of <?php echo $totalProducts; ?> products
                    <?php if (!empty($search)): ?>
                        for "<strong><?php echo htmlspecialchars($search); ?></strong>"
                    <?php endif; ?>
                </p>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-md-end align-items-center flex-wrap gap-2">
                    <div class="me-3">
                        <select class="form-select category-filter" id="categoryFilter">
                            <option value="">All Categories</option>
                            <?php 
                            $categories->data_seek(0); // Reset pointer
                            while($cat = $categories->fetch_assoc()) { 
                            ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo $category == $cat['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="me-3">
                        <select class="form-select" id="sortFilter">
                            <option value="created_at" <?php echo $sort == 'created_at' ? 'selected' : ''; ?>>Latest First</option>
                            <option value="price_low" <?php echo $sort == 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
                            <option value="price_high" <?php echo $sort == 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
                            <option value="name" <?php echo $sort == 'name' ? 'selected' : ''; ?>>Name: A-Z</option>
                            <option value="name_desc" <?php echo $sort == 'name_desc' ? 'selected' : ''; ?>>Name: Z-A</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Search Bar -->
<section class="py-3 bg-light">
    <div class="container">
        <form method="GET" class="d-flex gap-2">
            <input type="hidden" name="category" value="<?php echo $category; ?>">
            <input type="hidden" name="sort" value="<?php echo $sort; ?>">
            <input type="text" name="search" class="form-control" placeholder="Search products..." 
                   value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search me-2"></i>Search
            </button>
            <?php if (!empty($search) || $category > 0): ?>
                <a href="shop.php" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-2"></i>Clear
                </a>
            <?php endif; ?>
        </form>
    </div>
</section>

<!-- Active Filters -->
<?php if ($category > 0): ?>
    <?php 
    $categories->data_seek(0);
    while($cat = $categories->fetch_assoc()) {
        if ($cat['id'] == $category) {
            $activeCategory = $cat;
            break;
        }
    }
    ?>
    <section class="py-2 bg-info bg-opacity-10">
        <div class="container">
            <span class="badge bg-info me-2">
                Category: <?php echo htmlspecialchars($activeCategory['name']); ?>
                <a href="shop.php?search=<?php echo urlencode($search); ?>&sort=<?php echo $sort; ?>" class="text-white text-decoration-none ms-1">
                    <i class="fas fa-times"></i>
                </a>
            </span>
        </div>
    </section>
<?php endif; ?>

<!-- Products Grid -->
<section class="py-5">
    <div class="container">
        <?php if ($products && $products->num_rows > 0): ?>
            <div class="row">
                <?php while($product = $products->fetch_assoc()): ?>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="card product-card h-100">
                            <div class="position-relative overflow-hidden">
                                <img src="assets/images/products/<?php echo htmlspecialchars($product['image']); ?>" 
                                     class="product-image w-100" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>"
                                     onerror="this.src='https://picsum.photos/seed/<?php echo urlencode($product['name']); ?>/400/300.jpg'">
                                <?php if ($product['stock'] < 10 && $product['stock'] > 0): ?>
                                    <span class="badge bg-warning position-absolute top-0 end-0 m-2">Only <?php echo $product['stock']; ?> left</span>
                                <?php elseif ($product['stock'] == 0): ?>
                                    <span class="badge bg-danger position-absolute top-0 end-0 m-2">Out of Stock</span>
                                <?php endif; ?>
                                <?php if ($product['price'] > 50000): ?>
                                    <span class="badge bg-success position-absolute top-0 start-0 m-2">Premium</span>
                                <?php endif; ?>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <div class="mb-2">
                                    <span class="badge bg-primary mb-2"><?php echo htmlspecialchars($product['category_name'] ?: 'Uncategorized'); ?></span>
                                </div>
                                <h5 class="product-title flex-grow-1"><?php echo htmlspecialchars($product['name']); ?></h5>
                                <p class="text-muted small mb-3"><?php echo substr(htmlspecialchars($product['description']), 0, 80); ?>...</p>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="product-price fw-bold">₹<?php echo number_format($product['price'], 2); ?></span>
                                    <small class="text-muted">
                                        <?php if ($product['stock'] > 0): ?>
                                            <?php echo $product['stock']; ?> in stock
                                        <?php else: ?>
                                            Out of stock
                                        <?php endif; ?>
                                    </small>
                                </div>
                                <div class="d-flex gap-2 mt-auto">
                                    <button class="btn btn-primary btn-sm flex-fill add-to-cart" 
                                            data-product-id="<?php echo $product['id']; ?>"
                                            <?php echo $product['stock'] == 0 ? 'disabled' : ''; ?>>
                                        <i class="fas fa-cart-plus me-1"></i> 
                                        <?php echo $product['stock'] == 0 ? 'Out of Stock' : 'Add to Cart'; ?>
                                    </button>
                                    <a href="product.php?id=<?php echo $product['id']; ?>" 
                                       class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <nav aria-label="Page navigation" class="mt-5">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?category=<?php echo $category; ?>&search=<?php echo urlencode($search); ?>&sort=<?php echo $sort; ?>&page=<?php echo $page - 1; ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <?php 
                        $startPage = max(1, $page - 2);
                        $endPage = min($totalPages, $page + 2);
                        
                        for($i = $startPage; $i <= $endPage; $i++): 
                        ?>
                            <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                                <a class="page-link" href="?category=<?php echo $category; ?>&search=<?php echo urlencode($search); ?>&sort=<?php echo $sort; ?>&page=<?php echo $i; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?category=<?php echo $category; ?>&search=<?php echo urlencode($search); ?>&sort=<?php echo $sort; ?>&page=<?php echo $page + 1; ?>">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
            
        <?php else: ?>
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-search fa-4x text-muted mb-3"></i>
                    <h3>No products found</h3>
                    <p class="text-muted">
                        <?php if (!empty($search) || $category > 0): ?>
                            Try adjusting your filters or search terms
                        <?php else: ?>
                            No products available at the moment
                        <?php endif; ?>
                    </p>
                    <a href="shop.php" class="btn btn-primary">Clear Filters</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Category filter
    $('#categoryFilter').change(function() {
        var category = $(this).val();
        var url = new URL(window.location);
        if (category) {
            url.searchParams.set('category', category);
        } else {
            url.searchParams.delete('category');
        }
        url.searchParams.delete('page');
        window.location = url;
    });
    
    // Sort filter
    $('#sortFilter').change(function() {
        var sort = $(this).val();
        var url = new URL(window.location);
        url.searchParams.set('sort', sort);
        url.searchParams.delete('page');
        window.location = url;
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
});
</script>

<?php include 'includes/footer.php'; ?>
