<?php
// Check if admin is logged in
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

// Include database connection
require_once '../includes/db_connect.php';

// Handle product actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? sanitize($_POST['action']) : '';
    
    switch ($action) {
        case 'add':
            $name = sanitize($_POST['name']);
            $description = sanitize($_POST['description']);
            $price = floatval($_POST['price']);
            $category_id = intval($_POST['category_id']);
            $stock = intval($_POST['stock']);
            $image = $_FILES['image']['name'];
            
            // Validate inputs
            $errors = [];
            if (empty($name)) $errors[] = "Product name is required";
            if (empty($description)) $errors[] = "Description is required";
            if ($price <= 0) $errors[] = "Price must be greater than 0";
            if ($category_id <= 0) $errors[] = "Please select a category";
            if ($stock < 0) $errors[] = "Stock cannot be negative";
            
            // Handle image upload
            if (!empty($image)) {
                $target_dir = "../assets/images/products/";
                $target_file = $target_dir . basename($image);
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                
                // Check if image file is actual image
                $check = getimagesize($_FILES["image"]["tmp_name"]);
                if ($check === false) {
                    $errors[] = "File is not an image.";
                }
                
                // Check file size (5MB limit)
                if ($_FILES["image"]["size"] > 5000000) {
                    $errors[] = "Sorry, your file is too large.";
                }
                
                // Allow certain file formats
                if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                    $errors[] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                }
            }
            
            if (empty($errors)) {
                // Upload image if provided
                if (!empty($image) && move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    $image = basename($image);
                } else {
                    $image = 'default.jpg'; // Default image
                }
                
                $sql = "INSERT INTO products (name, description, price, category_id, stock, image) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssdiis", $name, $description, $price, $category_id, $stock, $image);
                
                if ($stmt->execute()) {
                    $_SESSION['success_message'] = "Product added successfully!";
                    header('Location: products.php');
                    exit();
                } else {
                    $errors[] = "Error adding product. Please try again.";
                }
            }
            break;
            
        case 'edit':
            $product_id = intval($_POST['product_id']);
            $name = sanitize($_POST['name']);
            $description = sanitize($_POST['description']);
            $price = floatval($_POST['price']);
            $category_id = intval($_POST['category_id']);
            $stock = intval($_POST['stock']);
            
            // Validate inputs
            $errors = [];
            if (empty($name)) $errors[] = "Product name is required";
            if (empty($description)) $errors[] = "Description is required";
            if ($price <= 0) $errors[] = "Price must be greater than 0";
            if ($category_id <= 0) $errors[] = "Please select a category";
            if ($stock < 0) $errors[] = "Stock cannot be negative";
            
            // Handle image upload
            $image = null;
            if (!empty($_FILES['image']['name'])) {
                $target_dir = "../assets/images/products/";
                $image = basename($_FILES['image']['name']);
                $target_file = $target_dir . $image;
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                
                // Validate image
                $check = getimagesize($_FILES["image"]["tmp_name"]);
                if ($check === false) {
                    $errors[] = "File is not an image.";
                } elseif ($_FILES["image"]["size"] > 5000000) {
                    $errors[] = "File is too large.";
                } elseif (!in_array($imageFileType, ["jpg", "png", "jpeg", "gif"])) {
                    $errors[] = "Invalid image format.";
                } elseif (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    $errors[] = "Error uploading image.";
                }
            }
            
            if (empty($errors)) {
                if ($image) {
                    $sql = "UPDATE products SET name = ?, description = ?, price = ?, category_id = ?, stock = ?, image = ? WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ssdiisi", $name, $description, $price, $category_id, $stock, $image, $product_id);
                } else {
                    $sql = "UPDATE products SET name = ?, description = ?, price = ?, category_id = ?, stock = ? WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ssdiii", $name, $description, $price, $category_id, $stock, $product_id);
                }
                
                if ($stmt->execute()) {
                    $_SESSION['success_message'] = "Product updated successfully!";
                    header('Location: products.php');
                    exit();
                } else {
                    $errors[] = "Error updating product. Please try again.";
                }
            }
            break;
            
        case 'delete':
            $product_id = intval($_POST['product_id']);
            
            // Check if product is in any orders
            $check_sql = "SELECT COUNT(*) as count FROM order_items WHERE product_id = ?";
            $stmt = $conn->prepare($check_sql);
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $count = $stmt->get_result()->fetch_assoc()['count'];
            
            if ($count > 0) {
                $_SESSION['error_message'] = "Cannot delete product. It is associated with existing orders.";
            } else {
                $sql = "DELETE FROM products WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $product_id);
                
                if ($stmt->execute()) {
                    $_SESSION['success_message'] = "Product deleted successfully!";
                } else {
                    $_SESSION['error_message'] = "Error deleting product. Please try again.";
                }
            }
            header('Location: products.php');
            exit();
            break;
    }
}

// Get products for display
$products = $conn->query("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC");

// Get categories for dropdown
$categories = $conn->query("SELECT * FROM categories ORDER BY name");

$page_title = "Manage Products - Admin";
include '../includes/header.php';
?>

<!-- Admin Products Page -->
<div class="admin-layout">
    <!-- Sidebar -->
    <div class="admin-sidebar">
        <div class="sidebar-header">
            <h4>Admin Panel</h4>
        </div>
        <nav class="sidebar-nav">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="products.php">
                        <i class="fas fa-box me-2"></i>Products
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="categories.php">
                        <i class="fas fa-tags me-2"></i>Categories
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="orders.php">
                        <i class="fas fa-shopping-cart me-2"></i>Orders
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="users.php">
                        <i class="fas fa-users me-2"></i>Users
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="reviews.php">
                        <i class="fas fa-star me-2"></i>Reviews
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="settings.php">
                        <i class="fas fa-cog me-2"></i>Settings
                    </a>
                </li>
                <li class="nav-item mt-3">
                    <a class="nav-link text-danger" href="logout.php">
                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="admin-main">
        <div class="admin-header">
            <h2>Manage Products</h2>
            <div class="admin-user">
                <span><?php echo $_SESSION['admin_name']; ?></span>
                <img src="https://picsum.photos/seed/admin/40/40.jpg" alt="Admin" class="rounded-circle">
            </div>
        </div>

        <div class="admin-content">
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success">
                    <?php 
                    echo $_SESSION['success_message']; 
                    unset($_SESSION['success_message']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger">
                    <?php 
                    echo $_SESSION['error_message']; 
                    unset($_SESSION['error_message']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Products</h5>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                        <i class="fas fa-plus me-2"></i>Add Product
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($product = $products->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <img src="../assets/images/products/<?php echo htmlspecialchars($product['image']); ?>" 
                                                 alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                                 style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                                        </td>
                                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                                        <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                                        <td>$<?php echo number_format($product['price'], 2); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $product['stock'] > 10 ? 'success' : 'warning'; ?>">
                                                <?php echo $product['stock']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo $product['stock'] > 0 ? 'success' : 'danger'; ?>">
                                                <?php echo $product['stock'] > 0 ? 'In Stock' : 'Out of Stock'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" onclick="editProduct(<?php echo $product['id']; ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger" onclick="deleteProduct(<?php echo $product['id']; ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Product Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Category</label>
                            <select class="form-select" name="category_id" required>
                                <option value="">Select Category</option>
                                <?php while($category = $categories->fetch_assoc()): ?>
                                    <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Price</label>
                            <input type="number" class="form-control" name="price" step="0.01" min="0" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Stock</label>
                            <input type="number" class="form-control" name="stock" min="0" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Product Image</label>
                            <input type="file" class="form-control" name="image" accept="image/*">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.admin-layout {
    display: flex;
    min-height: 100vh;
}

.admin-sidebar {
    width: 250px;
    background: #2c3e50;
    color: white;
    position: fixed;
    height: 100vh;
    overflow-y: auto;
}

.sidebar-header {
    padding: 20px;
    border-bottom: 1px solid #34495e;
}

.sidebar-nav {
    padding: 20px 0;
}

.sidebar-nav .nav-link {
    color: #bdc3c7;
    padding: 12px 20px;
    border-radius: 0;
    transition: all 0.3s;
}

.sidebar-nav .nav-link:hover,
.sidebar-nav .nav-link.active {
    background: #34495e;
    color: white;
}

.admin-main {
    flex: 1;
    margin-left: 250px;
    background: #f8f9fa;
}

.admin-header {
    background: white;
    padding: 20px;
    border-bottom: 1px solid #dee2e6;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.admin-user {
    display: flex;
    align-items: center;
    gap: 10px;
}

.admin-content {
    padding: 20px;
}
</style>

<script>
function editProduct(productId) {
    // Load product data and show edit modal
    window.location.href = 'edit_product.php?id=' + productId;
}

function deleteProduct(productId) {
    if (confirm('Are you sure you want to delete this product?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="product_id" value="${productId}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php include '../includes/footer.php'; ?>
