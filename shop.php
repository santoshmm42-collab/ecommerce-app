<?php
$page_title = "Shop - ShopHub";
include 'includes/header.php';

// Get filter parameters
$category = isset($_GET['category']) ? intval($_GET['category']) : 0;
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$sort = isset($_GET['sort']) ? sanitize($_GET['sort']) : 'created_at';
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

$limit = 12;
$offset = ($page - 1) * $limit;

// Build WHERE
$where = [];
$params = [];
$types = "";

if ($category > 0) {
    $where[] = "p.category_id = ?";
    $params[] = $category;
    $types .= "i";
}

if (!empty($search)) {
    $where[] = "(p.name LIKE ? OR p.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= "ss";
}

$whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

// Sorting
$sortOptions = [
    'price_low' => 'p.price ASC',
    'price_high' => 'p.price DESC',
    'name' => 'p.name ASC',
    'created_at' => 'p.created_at DESC',
    'popularity' => 'p.id DESC'
];

$orderBy = $sortOptions[$sort] ?? $sortOptions['created_at'];


// COUNT QUERY
$countSql = "SELECT COUNT(*) as total FROM products p $whereClause";
$stmt = $conn->prepare($countSql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$totalProducts = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
$totalPages = ceil($totalProducts / $limit);


// PRODUCT QUERY
$sql = "SELECT p.*, c.name as category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        $whereClause 
        ORDER BY $orderBy 
        LIMIT $limit OFFSET $offset";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$products = $stmt->get_result();


// CATEGORIES
$categories = $conn->query("SELECT * FROM categories ORDER BY name");
?>

<!-- SHOP HEADER -->
<section class="py-4 bg-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1>Shop</h1>
                <p class="text-muted">
                    Showing <?php echo min($totalProducts, $limit); ?> of <?php echo $totalProducts; ?> products
                </p>
            </div>

            <div class="col-md-6 text-end">
                <select id="categoryFilter">
                    <option value="">All Categories</option>
                    <?php while($cat = $categories->fetch_assoc()): ?>
                        <option value="<?php echo $cat['id']; ?>"
                            <?php echo ($category == $cat['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <select id="sortFilter">
                    <option value="created_at">Newest</option>
                    <option value="price_low">Price Low</option>
                    <option value="price_high">Price High</option>
                    <option value="name">Name</option>
                    <option value="popularity">Popular</option>
                </select>
            </div>
        </div>
    </div>
</section>

<!-- PRODUCTS -->
<section class="py-5">
    <div class="container">
        <div class="row">

        <?php if ($products && $products->num_rows > 0): ?>

            <?php while($product = $products->fetch_assoc()): ?>

                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">

                    <div class="product-card">

                        <img src="assets/images/products/<?php echo htmlspecialchars($product['image'] ?? 'default.jpg'); ?>"
                             class="img-fluid"
                             alt="<?php echo htmlspecialchars($product['name']); ?>"
                             onerror="this.src='https://picsum.photos/seed/<?php echo urlencode($product['name']); ?>/400/300.jpg'">

                        <div class="p-2">

                            <span class="badge bg-primary">
                                <?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?>
                            </span>

                            <h5><?php echo htmlspecialchars($product['name']); ?></h5>

                            <p>₹<?php echo number_format($product['price'], 2); ?></p>

                            <small><?php echo $product['stock']; ?> in stock</small>

                        </div>
                    </div>
                </div>

            <?php endwhile; ?>

        <?php else: ?>

            <div class="col-12 text-center">
                <h3>No products found</h3>
                <p>Check database or categories/products table</p>
            </div>

        <?php endif; ?>

        </div>
    </div>
</section>

<script>
document.getElementById('categoryFilter').addEventListener('change', function () {
    let url = new URL(window.location.href);
    url.searchParams.set('category', this.value);
    window.location.href = url;
});

document.getElementById('sortFilter').addEventListener('change', function () {
    let url = new URL(window.location.href);
    url.searchParams.set('sort', this.value);
    window.location.href = url;
});
</script>

<?php include 'includes/footer.php'; ?>
