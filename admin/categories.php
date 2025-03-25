<?php
session_start();
require_once __DIR__ . '/../includes/db/functions.php';

// Redirect to login if not an admin
if (!isset($_SESSION["admin_email"])) {
    header("Location: login.php");
    exit;
}

$categoriesObj = new Functions();
$conn = $categoriesObj->getDatabaseConnection();

$errors = [];
$success = "";

// Create Category
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["create_category"])) {
    $name = trim($_POST["name"]);
    $image = $_FILES["image"]["name"];
    $item_count = isset($_POST["item_count"]) ? intval($_POST["item_count"]) : 0;
    $target_dir = "../uploads/categories/";
    $target_file = $target_dir . basename($image);

    if (empty($name)) {
        $errors[] = "Category name is required.";
    } else {
        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $query = "INSERT INTO categories (name, image, item_count) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ssi", $name, $image, $item_count);

                if ($stmt->execute()) {
                    $success = "Category created successfully!";
                    header("Location: categories.php");
                    exit();
                } else {
                    $errors[] = "Error creating category: " . $conn->error;
                }
            } else {
                $errors[] = "Sorry, there was an error uploading your file.";
            }
        } else {
            $errors[] = "File is not an image.";
        }
    }
}

// Update Category
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_category"])) {
    $category_id = intval($_POST["category_id"]);
    $name = trim($_POST["name"]);
    $image = $_FILES["image"]["name"];
    $item_count = isset($_POST["item_count"]) ? intval($_POST["item_count"]) : 0;
    $target_dir = "../uploads/categories/";
    $target_file = $target_dir . basename($image);

    if (empty($name)) {
        $errors[] = "Category name is required.";
    } else {
        if (!empty($image)) {
            // Check if image file is a actual image or fake image
            $check = getimagesize($_FILES["image"]["tmp_name"]);
            if ($check !== false) {
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    $query = "UPDATE categories SET name = ?, image = ?, item_count = ? WHERE category_id = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("ssii", $name, $image, $item_count, $category_id);
                } else {
                    $errors[] = "Sorry, there was an error uploading your file.";
                }
            } else {
                $errors[] = "File is not an image.";
            }
        } else {
            $query = "UPDATE categories SET name = ?, item_count = ? WHERE category_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sii", $name, $item_count, $category_id);
        }

        if (empty($errors)) {
            if ($stmt->execute()) {
                $success = "Category updated successfully!";
                header("Location: categories.php");
                exit();
            } else {
                $errors[] = "Error updating category: " . $conn->error;
            }
        }
    }
}

// Delete Category
if (isset($_GET["delete"])) {
    $category_id = intval($_GET["delete"]);

    $query = "DELETE FROM categories WHERE category_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $category_id);

    if ($stmt->execute()) {
        $success = "Category deleted successfully!";
        header("Location: categories.php");
        exit();
    } else {
        $errors[] = "Error deleting category: " . $conn->error;
    }
}

// Fetch All Categories with product counts
$query = "SELECT c.*, 
          (SELECT COUNT(*) FROM products WHERE category_id = c.category_id) as actual_product_count
          FROM categories c";
$result = $conn->query($query);
$categories = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories | Admin Panel</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        .img-thumbnail {
            max-width: 100px;
            height: auto;
        }

        .count-badge {
            font-size: 0.9rem;
            min-width: 50px;
        }

        .count-discrepancy {
            color: #dc3545;
            font-size: 0.8rem;
        }
    </style>
</head>

<body>
    <!-- Include Sidebar -->
    <?php include "sidebar.php"; ?>

    <!-- Main Content -->
    <div class="content">
        <div class="dashboard-header">
            <h2>Manage Categories</h2>
            <p class="text-muted">Manage product categories and item counts</p>
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

            <!-- Create Category Form -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <i class="bi bi-plus-circle"></i> Add New Category
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="name" class="form-label">Category Name *</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="item_count" class="form-label">Category Qty</label>
                                <input type="number" class="form-control" id="item_count" name="item_count" min="0" value="0">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="image" class="form-label">Category Image *</label>
                                <input type="file" class="form-control" id="image" name="image" required>
                                <small class="text-muted">Recommended size: 300x300 pixels</small>
                            </div>
                        </div>
                        <button type="submit" name="create_category" class="btn btn-primary">
                            <i class="bi bi-save"></i> Add Category
                        </button>
                    </form>
                </div>
            </div>

            <!-- Categories Table -->
            <div class="card">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-list-ul"></i> Category List</span>
                    <span class="badge bg-light text-dark">
                        Total: <?php echo count($categories); ?>
                    </span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Image</th>
                                    <th class="text-center">Category Qty</th>
                                    <!-- <th class="text-center">Actual Products</th> -->
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categories as $category): ?>
                                    <tr>
                                        <td><?php echo $category["category_id"]; ?></td>
                                        <td><?php echo htmlspecialchars($category["name"]); ?></td>
                                        <td>
                                            <?php if (!empty($category["image"])): ?>
                                                <img src="../uploads/categories/<?php echo htmlspecialchars($category["image"]); ?>"
                                                    alt="<?php echo htmlspecialchars($category["name"]); ?>"
                                                    class="img-thumbnail">
                                            <?php else: ?>
                                                <span class="text-muted">No Image</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-primary count-badge">
                                                <?php echo $category["item_count"]; ?>
                                            </span>
                                        </td>
                                       
                                        <td>
                                            <!-- Edit Button -->
                                            <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#editCategoryModal<?php echo $category["category_id"]; ?>">
                                                <i class="bi bi-pencil"></i> Edit
                                            </button>
                                            <!-- Delete Button -->
                                            <a href="categories.php?delete=<?php echo $category["category_id"]; ?>"
                                                class="btn btn-danger btn-sm"
                                                onclick="return confirm('Are you sure you want to delete this category?')">
                                                <i class="bi bi-trash"></i> Delete
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Category Modals -->
    <?php foreach ($categories as $category): ?>
        <div class="modal fade" id="editCategoryModal<?php echo $category["category_id"]; ?>" tabindex="-1"
            aria-labelledby="editCategoryModalLabel<?php echo $category["category_id"]; ?>" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editCategoryModalLabel<?php echo $category["category_id"]; ?>">
                            <i class="bi bi-pencil"></i> Edit Category: <?php echo htmlspecialchars($category["name"]); ?>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="category_id" value="<?php echo $category["category_id"]; ?>">
                            <div class="mb-3">
                                <label for="name<?php echo $category["category_id"]; ?>" class="form-label">Category Name *</label>
                                <input type="text" class="form-control" id="name<?php echo $category["category_id"]; ?>"
                                    name="name" value="<?php echo htmlspecialchars($category["name"]); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="item_count<?php echo $category["category_id"]; ?>" class="form-label">Category Qty</label>
                                <input type="number" class="form-control" id="item_count<?php echo $category["category_id"]; ?>"
                                    name="item_count" min="0" value="<?php echo $category["item_count"]; ?>">
                                <small class="text-muted">Category Qty: <?php echo $category["actual_product_count"]; ?></small>
                            </div>
                            <div class="mb-3">
                                <label for="image<?php echo $category["category_id"]; ?>" class="form-label">Category Image</label>
                                <input type="file" class="form-control" id="image<?php echo $category["category_id"]; ?>" name="image">
                                <?php if (!empty($category["image"])): ?>
                                    <div class="mt-2">
                                        <p>Current Image:</p>
                                        <img src="../uploads/categories/<?php echo htmlspecialchars($category["image"]); ?>"
                                            alt="<?php echo htmlspecialchars($category["name"]); ?>"
                                            class="img-thumbnail">
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    <i class="bi bi-x-circle"></i> Close
                                </button>
                                <button type="submit" name="update_category" class="btn btn-primary">
                                    <i class="bi bi-save"></i> Save Changes
                                </button>
                            </div>
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