<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once __DIR__ . '/../includes/db/functions.php';

if (!isset($_SESSION["admin_email"])) {
    header("Location: login.php");
    exit;
}

$functions = new Functions();
$conn = $functions->getDatabaseConnection();
$errors = [];
$success = "";

// Pagination
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Search
$search = isset($_GET['search']) ? trim($_GET['search']) : "";
$searchQuery = "";
if (!empty($search)) {
    $searchQuery = "WHERE p.name LIKE ? OR p.type LIKE ?";
}

// Count total
$countQuery = "SELECT COUNT(*) AS total FROM products p $searchQuery";
$stmt = $conn->prepare($countQuery);

if (!empty($search)) {
    $searchTerm = "%$search%";
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
}

$stmt->execute();
$countResult = $stmt->get_result();
$totalProducts = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalProducts / $limit);

// Fetch products
$query = "SELECT p.product_id, p.name, p.price, p.stock, p.type, p.image, c.name AS category_name 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.category_id 
          $searchQuery 
          ORDER BY p.product_id DESC 
          LIMIT ?, ?";
$stmt = $conn->prepare($query);

if (!empty($search)) {
    $stmt->bind_param("ssii", $searchTerm, $searchTerm, $offset, $limit);
} else {
    $stmt->bind_param("ii", $offset, $limit);
}

$stmt->execute();
$result = $stmt->get_result();
$products = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Products | Admin Panel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>

<body>
<?php include "sidebar.php"; ?>

<div class="content">
    <div class="dashboard-header">
        <h2>Manage Products</h2>
    </div>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>All Products</h4>
            <a href="add_product.php" class="btn btn-primary">➕ Add New Product</a>
        </div>

        <form method="GET" class="mb-3 d-flex">
            <input type="text" name="search" class="form-control me-2" placeholder="Search by Name or Type" value="<?php echo htmlspecialchars($search); ?>">
            <select name="limit" class="form-select me-2">
                <option value="10" <?php echo ($limit == 10) ? 'selected' : ''; ?>>Show 10</option>
                <option value="20" <?php echo ($limit == 20) ? 'selected' : ''; ?>>Show 20</option>
                <option value="50" <?php echo ($limit == 50) ? 'selected' : ''; ?>>Show 50</option>
            </select>
            <button type="submit" class="btn btn-success">Filter</button>
        </form>

        <div class="card">
            <div class="card-header bg-dark text-white">Product List</div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Main Image</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?php echo $product['product_id']; ?></td>
                                <td><?php echo htmlspecialchars($product['name'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($product['type'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($product['category_name'] ?? ''); ?></td>
                                <td>$<?php echo htmlspecialchars($product['price'] ?? '0.00'); ?></td>
                                <td><?php echo htmlspecialchars($product['stock'] ?? '0'); ?></td>
                                <td>
                                    <?php
                                    $imagePath = $product['image'] ?? '';
                                    $showPath = (strpos($imagePath, 'assets/') === 0) ? "../$imagePath" : "../uploads/products/$imagePath";
                                    ?>
                                    <img src="<?php echo $showPath; ?>" alt="Product Image" width="50">
                                </td>
                                <td>
                                    <a href="edit_product.php?id=<?php echo $product['product_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="delete_product.php?id=<?php echo $product['product_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($products)): ?>
                            <tr>
                                <td colspan="8" class="text-center">No products found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <!-- Pagination -->
                <nav>
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                <a class="page-link" href="?search=<?php echo urlencode($search); ?>&limit=<?php echo $limit; ?>&page=<?php echo $i; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>

            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>