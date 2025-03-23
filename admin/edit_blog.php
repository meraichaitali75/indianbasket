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

// Get blog ID from URL
$blogId = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($blogId <= 0) {
    header("Location: blogs.php");
    exit;
}

// Fetch blog from DB
$stmt = $conn->prepare("SELECT * FROM blogs WHERE id = ?");
$stmt->bind_param("i", $blogId);
$stmt->execute();
$result = $stmt->get_result();
$blog = $result->fetch_assoc();

if (!$blog) {
    header("Location: blogs.php");
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $slug = trim($_POST['slug']);
    $content = trim($_POST['content']);
    $meta_description = trim($_POST['meta_description']);
    $meta_keywords = trim($_POST['meta_keywords']);
    $status = $_POST['status'];

    // Validation
    if (empty($title) || empty($slug) || empty($content)) {
        $errors[] = "Title, Slug, and Content are required.";
    }

    // Handle image upload (optional)
    $imagePath = $blog['image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../uploads/blogs/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $imageName = time() . '_' . basename($_FILES['image']['name']);
        $targetFilePath = $uploadDir . $imageName;
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

        if (in_array($fileType, ['jpg', 'jpeg', 'png'])) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
                $imagePath = 'uploads/blogs/' . $imageName;
            } else {
                $errors[] = "Failed to upload image.";
            }
        } else {
            $errors[] = "Only JPG, JPEG, and PNG files are allowed.";
        }
    }

    // Update DB if no errors
    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE blogs SET title=?, slug=?, content=?, image=?, meta_description=?, meta_keywords=?, status=? WHERE id=?");
        $stmt->bind_param("sssssssi", $title, $slug, $content, $imagePath, $meta_description, $meta_keywords, $status, $blogId);

        if ($stmt->execute()) {
            $success = "Blog updated successfully!";
            header("Location: blogs.php");
            exit();
        } else {
            $errors[] = "Error updating blog.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Blog</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
<?php include "sidebar.php"; ?>
<div class="content">
    <div class="dashboard-header">
        <h2>Edit Blog</h2>
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
            <div class="card-header bg-primary text-white">Update Blog</div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label>Title</label>
                        <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($blog['title']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Slug</label>
                        <input type="text" name="slug" class="form-control" value="<?php echo htmlspecialchars($blog['slug']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Content</label>
                        <textarea name="content" class="form-control" rows="6" required><?php echo htmlspecialchars($blog['content']); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label>Meta Description</label>
                        <textarea name="meta_description" class="form-control"><?php echo htmlspecialchars($blog['meta_description']); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label>Meta Keywords</label>
                        <input type="text" name="meta_keywords" class="form-control" value="<?php echo htmlspecialchars($blog['meta_keywords']); ?>">
                    </div>
                    <div class="mb-3">
                        <label>Blog Image (Optional)</label><br>
                        <input type="file" name="image" class="form-control">
                        <?php if (!empty($blog['image'])): ?>
                            <img src="../<?php echo $blog['image']; ?>" alt="Current Image" width="150" class="mt-2">
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="active" <?php echo ($blog['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo ($blog['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success">Update Blog</button>
                    <a href="blogs.php" class="btn btn-secondary">Back</a>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>