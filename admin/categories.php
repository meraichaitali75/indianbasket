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
    $target_dir = "../uploads/categories/";
    $target_file = $target_dir . basename($image);

    if (empty($name)) {
        $errors[] = "Category name is required.";
    } else {
        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $query = "INSERT INTO categories (name, image) VALUES (?, ?)";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ss", $name, $image);

                if ($stmt->execute()) {
                    $success = "Category created successfully!";
                    header("Location: categories.php");
                    exit();
                } else {
                    $errors[] = "Error creating category.";
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
                    $query = "UPDATE categories SET name = ?, image = ? WHERE category_id = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("ssi", $name, $image, $category_id);
                } else {
                    $errors[] = "Sorry, there was an error uploading your file.";
                }
            } else {
                $errors[] = "File is not an image.";
            }
        } else {
            $query = "UPDATE categories SET name = ? WHERE category_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("si", $name, $category_id);
        }

        if ($stmt->execute()) {
            $success = "Category updated successfully!";
            header("Location: categories.php");
            exit();
        } else {
            $errors[] = "Error updating category.";
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
        $errors[] = "Error deleting category.";
    }
}

// Fetch All Categories
$query = "SELECT * FROM categories";
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
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>

<body>
    <!-- Include Sidebar -->
    <?php include "sidebar.php"; ?>

    <!-- Main Content -->
    <div class="content">
        <div class="dashboard-header">
            <h2>Manage Categories</h2>
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
                <div class="card-header bg-dark text-white">Add New Category</div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="name" class="form-label">Category Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">Category Image</label>
                            <input type="file" class="form-control" id="image" name="image" required>
                        </div>
                        <button type="submit" name="create_category" class="btn btn-primary">Add Category</button>
                    </form>
                </div>
            </div>

            <!-- Categories Table -->
            <div class="card">
                <div class="card-header bg-dark text-white">Category List</div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Image</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td><?php echo $category["category_id"]; ?></td>
                                    <td><?php echo $category["name"]; ?></td>
                                    <td>
                                        <?php if (!empty($category["image"])): ?>
                                            <img src="../uploads/categories/<?php echo $category["image"]; ?>" alt="<?php echo $category["name"]; ?>" width="50">
                                        <?php else: ?>
                                            No Image
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <!-- Edit Button -->
                                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editCategoryModal<?php echo $category["category_id"]; ?>">
                                            Edit
                                        </button>
                                        <!-- Delete Button -->
                                        <a href="categories.php?delete=<?php echo $category["category_id"]; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Category Modals -->
    <?php foreach ($categories as $category): ?>
        <div class="modal fade" id="editCategoryModal<?php echo $category["category_id"]; ?>" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editCategoryModalLabel">Edit Category</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="category_id" value="<?php echo $category["category_id"]; ?>">
                            <div class="mb-3">
                                <label for="name" class="form-label">Category Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo $category["name"]; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="image" class="form-label">Category Image</label>
                                <input type="file" class="form-control" id="image" name="image">
                                <?php if (!empty($category["image"])): ?>
                                    <img src="../uploads/categories/<?php echo $category["image"]; ?>" alt="<?php echo $category["name"]; ?>" width="50">
                                <?php endif; ?>
                            </div>
                            <button type="submit" name="update_category" class="btn btn-primary">Update Category</button>
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