<?php
session_start();
require_once __DIR__ . '/../includes/db/functions.php';

// Check if admin is logged in
if (!isset($_SESSION["admin_email"])) {
    header("Location: login.php");
    exit;
}

$functions = new Functions();
$conn = $functions->getDatabaseConnection();
$errors = [];
$success = "";

// Get Banner ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: banners.php");
    exit();
}

$banner_id = intval($_GET['id']);

// Fetch Banner Data
$query = "SELECT * FROM banners WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $banner_id);
$stmt->execute();
$result = $stmt->get_result();
$banner = $result->fetch_assoc();

if (!$banner) {
    header("Location: banners.php");
    exit();
}

// Handle Banner Update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $meta_description = trim($_POST['meta_description']);
    $keywords = trim($_POST['keywords']);
    $target_url = trim($_POST['target_url']);
    $status = $_POST['status'];
    $updated_image = $banner['image']; // Keep old image if not changed

    // Handle New Image Upload
    if (!empty($_FILES["banner"]["name"])) {
        $uploadDir = __DIR__ . "/../uploads/banners/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $bannerName = time() . "_" . basename($_FILES["banner"]["name"]);
        $targetFilePath = $uploadDir . $bannerName;
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

        if (in_array($fileType, ["jpg", "jpeg", "png"])) {
            if (move_uploaded_file($_FILES["banner"]["tmp_name"], $targetFilePath)) {
                $updated_image = "uploads/banners/" . $bannerName;

                // Remove old image
                if (file_exists(__DIR__ . "/../" . $banner['image'])) {
                    unlink(__DIR__ . "/../" . $banner['image']);
                }
            } else {
                $errors[] = "Error uploading banner.";
            }
        } else {
            $errors[] = "Only JPG, JPEG, and PNG formats are allowed.";
        }
    }

    // Update Banner Data in DB
    if (empty($errors)) {
        $updateQuery = "UPDATE banners SET image=?, title=?, meta_description=?, keywords=?, target_url=?, status=? WHERE id=?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("ssssssi", $updated_image, $title, $meta_description, $keywords, $target_url, $status, $banner_id);

        if ($stmt->execute()) {
            $success = "Banner updated successfully!";
            header("Location: banners.php");
            exit();
        } else {
            $errors[] = "Error updating banner.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Banner | Admin Panel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

    <?php include "sidebar.php"; ?>

    <div class="content">
        <div class="dashboard-header">
            <h2>Edit Banner</h2>
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
                <div class="card-header bg-primary text-white">Edit Banner</div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3 text-center">
                            <img src="../<?php echo $banner['image']; ?>" width="150">
                        </div>
                        <div class="mb-3">
                            <label>Change Image (Optional)</label>
                            <input type="file" name="banner" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Title (Alt Text)</label>
                            <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($banner['title']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label>Meta Description</label>
                            <textarea name="meta_description" class="form-control"><?php echo htmlspecialchars($banner['meta_description']); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label>Keywords</label>
                            <input type="text" name="keywords" class="form-control" value="<?php echo htmlspecialchars($banner['keywords']); ?>">
                        </div>
                        <div class="mb-3">
                            <label>Target URL</label>
                            <input type="text" name="target_url" class="form-control" value="<?php echo htmlspecialchars($banner['target_url']); ?>">
                        </div>
                        <div class="mb-3">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="active" <?php echo $banner['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?php echo $banner['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success">Update Banner</button>
                        <a href="banners.php" class="btn btn-secondary">Back to Banners</a>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
