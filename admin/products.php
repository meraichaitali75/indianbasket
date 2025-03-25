<?php
session_start();
require_once __DIR__ . '/../includes/db/functions.php';

// Redirect to login if not an admin
if (!isset($_SESSION["admin_email"])) {
    header("Location: login.php");
    exit;
}

$productsObj = new Functions();
$conn = $productsObj->getDatabaseConnection();

$errors = [];
$success = "";

// Create Product
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["create_product"])) {
    $name = trim($_POST["name"]);
    $category_id = intval($_POST["category_id"]);
    $price = floatval($_POST["price"]);
    $stock = intval($_POST["stock"]);
    $description = trim($_POST["description"]);
    $image = $_FILES["image"]["name"];
    $target_dir = "../uploads/products/";
    $target_file = $target_dir . basename($image);

    if (empty($name) || empty($category_id) || empty($price) || empty($stock)) {
        $errors[] = "All fields are required except description.";
    } else {
        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $query = "INSERT INTO products (name, category_id, price, stock, description, image) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("sidiss", $name, $category_id, $price, $stock, $description, $image);

                if ($stmt->execute()) {
                    $success = "Product created successfully!";
                    header("Location: products.php");
                    exit();
                } else {
                    $errors[] = "Error creating product.";
                }
            } else {
                $errors[] = "Sorry, there was an error uploading your file.";
            }
        } else {
            $errors[] = "File is not an image.";
        }
    }
}

// Update Product
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_product"])) {
    $product_id = intval($_POST["product_id"]);
    $name = trim($_POST["name"]);
    $category_id = intval($_POST["category_id"]);
    $price = floatval($_POST["price"]);
    $stock = intval($_POST["stock"]);
    $description = trim($_POST["description"]);
    $image = $_FILES["image"]["name"];
    $target_dir = "../uploads/products/";
    $target_file = $target_dir . basename($image);

    if (empty($name) || empty($category_id) || empty($price) || empty($stock)) {
        $errors[] = "All fields are required except description.";
    } else {
        if (!empty($image)) {
            // Check if image file is a actual image or fake image
            $check = getimagesize($_FILES["image"]["tmp_name"]);
            if ($check !== false) {
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    $query = "UPDATE products SET name = ?, category_id = ?, price = ?, stock = ?, description = ?, image = ? WHERE product_id = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("sidissi", $name, $category_id, $price, $stock, $description, $image, $product_id);
                } else {
                    $errors[] = "Sorry, there was an error uploading your file.";
                }
            } else {
                $errors[] = "File is not an image.";
            }
        } else {
            $query = "UPDATE products SET name = ?, category_id = ?, price = ?, stock = ?, description = ? WHERE product_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sidisi", $name, $category_id, $price, $stock, $description, $product_id);
        }

        if ($stmt->execute()) {
            $success = "Product updated successfully!";
            header("Location: products.php");
            exit();
        } else {
            $errors[] = "Error updating product.";
        }
    }
}

// Delete Product
if (isset($_GET["delete"])) {
    $product_id = intval($_GET["delete"]);

    $query = "DELETE FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $product_id);

    if ($stmt->execute()) {
        $success = "Product deleted successfully!";
        header("Location: products.php");
        exit();
    } else {
        $errors[] = "Error deleting product.";
    }
}

// Fetch All Products
$query = "SELECT p.product_id, p.name, p.category_id, p.price, p.stock, p.description, p.image, c.name AS category_name 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.category_id";
$result = $conn->query($query);
$products = $result->fetch_all(MYSQLI_ASSOC);

// Fetch All Categories for Dropdown
$query = "SELECT * FROM categories";
$categoriesResult = $conn->query($query);
$categories = $categoriesResult->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products | Admin Panel</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>

<body>
    <!-- Include Sidebar -->
    <?php include "sidebar.php"; ?>

    <!-- Main Content -->
    <div class="content">
        <div class="dashboard-header">
            <h2>Manage Products</h2>
        </div>

        <div class="container mt-4">
            <!-- Display Messages -->
            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Create Product Form -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">Add New Product</div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="name" class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="category_id" class="form-label">Category</label>
                            <select class="form-select" id="category_id" name="category_id" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category["category_id"]; ?>"><?php echo $category["name"]; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="price" class="form-label">Price</label>
                            <input type="number" step="0.01" class="form-control" id="price" name="price" required>
                        </div>
                        <div class="mb-3">
                            <label for="stock" class="form-label">Stock</label>
                            <input type="number" class="form-control" id="stock" name="stock" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">Product Image</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        </div>
                        <button type="submit" name="create_product" class="btn btn-primary">Add Product</button>
                    </form>
                </div>
            </div>

            <!-- Products Table -->
            <div class="card">
                <div class="card-header bg-dark text-white">Product List</div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Description</th>
                                <th>Image</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td><?php echo $product["product_id"]; ?></td>
                                    <td><?php echo $product["name"]; ?></td>
                                    <td><?php echo $product["category_name"]; ?></td>
                                    <td><?php echo $product["price"]; ?></td>
                                    <td><?php echo $product["stock"]; ?></td>
                                    <td><?php echo $product["description"]; ?></td>
                                    <td>
                                        <?php if (!empty($product["image"])): ?>
                                            <img src="../uploads/products/<?php echo $product["image"]; ?>" alt="Product Image" width="50">
                                        <?php else: ?>
                                            No Image
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <!-- Edit Button -->
                                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editProductModal<?php echo $product["product_id"]; ?>">
                                            Edit
                                        </button>
                                        <!-- Delete Button -->
                                        <a href="products.php?delete=<?php echo $product["product_id"]; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Product Modals -->
    <?php foreach ($products as $product): ?>
        <div class="modal fade" id="editProductModal<?php echo $product["product_id"]; ?>" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editProductModalLabel">Edit Product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="product_id" value="<?php echo $product["product_id"]; ?>">
                            <div class="mb-3">
                                <label for="name" class="form-label">Product Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo $product["name"]; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Category</label>
                                <select class="form-select" id="category_id" name="category_id" required>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category["category_id"]; ?>" <?php echo ($category["category_id"] == $product["category_id"]) ? 'selected' : ''; ?>>
                                            <?php echo $category["name"]; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="price" class="form-label">Price</label>
                                <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?php echo $product["price"]; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="stock" class="form-label">Stock</label>
                                <input type="number" class="form-control" id="stock" name="stock" value="<?php echo $product["stock"]; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description"><?php echo $product["description"]; ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="image" class="form-label">Product Image</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                <?php if (!empty($product["image"])): ?>
                                    <img src="../uploads/products/<?php echo $product["image"]; ?>" alt="Product Image" width="100" class="mt-2">
                                <?php endif; ?>
                            </div>
                            <button type="submit" name="update_product" class="btn btn-primary">Update Product</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>