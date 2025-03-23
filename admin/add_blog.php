<?php
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $slug = trim($_POST['slug']);
    $status = $_POST['status'];
    $meta_title = trim($_POST['meta_title']);
    $meta_description = trim($_POST['meta_description']);
    $meta_keywords = trim($_POST['meta_keywords']);
    $image = "";

    if (empty($title) || empty($content) || empty($slug)) {
        $errors[] = "Title, Content, and Slug are required.";
    }

    if (empty($errors)) {
        // Image Upload Handling
        $uploadDir = __DIR__ . '/../uploads/blogs/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (!empty($_FILES['image']['name'])) {
            $imageName = time() . '_' . basename($_FILES['image']['name']);
            $targetFilePath = $uploadDir . $imageName;
            $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

            if (in_array($fileType, ['jpg', 'jpeg', 'png'])) {
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
                    $image = 'uploads/blogs/' . $imageName;
                } else {
                    $errors[] = "Failed to upload image.";
                }
            } else {
                $errors[] = "Only JPG, JPEG, and PNG formats are allowed.";
            }
        }

        if (empty($errors)) {
            $stmt = $conn->prepare("INSERT INTO blogs (user_id, title, content, image, status, slug, meta_title, meta_description, meta_keywords) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $adminId = $_SESSION['admin_id'];
            $stmt->bind_param("issssssss", $adminId, $title, $content, $image, $status, $slug, $meta_title, $meta_description, $meta_keywords);

            if ($stmt->execute()) {
                $success = "Blog added successfully!";
            } else {
                $errors[] = "Error saving blog: " . $stmt->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Blog | Admin Panel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

<?php include "sidebar.php"; ?>

<div class="content">
    <div class="dashboard-header">
        <h2>Add Blog</h2>
    </div>

    <div class="container mt-4">
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

        <div class="card">
            <div class="card-header bg-primary text-white">Create New Blog</div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label>Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Slug</label>
                        <input type="text" name="slug" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Content</label>
                        <textarea name="content" class="form-control" rows="6" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label>Image</label>
                        <input type="file" name="image" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Status</label>
                        <select name="status" class="form-control" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <hr>
                    <h5>SEO Settings</h5>
                    <div class="mb-3">
                        <label>Meta Title</label>
                        <input type="text" name="meta_title" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Meta Description</label>
                        <textarea name="meta_description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label>Meta Keywords</label>
                        <input type="text" name="meta_keywords" class="form-control">
                    </div>

                    <button type="submit" class="btn btn-success">Publish Blog</button>
                    <a href="blogs.php" class="btn btn-secondary">Back to Blogs</a>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>