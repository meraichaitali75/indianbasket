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

// Handle Banner Upload with SEO properties
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['upload_banner'])) {
    $title = trim($_POST['title']);
    $meta_description = trim($_POST['meta_description']);
    $keywords = trim($_POST['keywords']);
    $target_url = trim($_POST['target_url']);
    $status = $_POST['status'];

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
                $bannerPath = "uploads/banners/" . $bannerName;

                // Insert banner with SEO properties into database
                $insertQuery = "INSERT INTO banners (image, title, meta_description, keywords, target_url, status) 
                                VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($insertQuery);
                $stmt->bind_param("ssssss", $bannerPath, $title, $meta_description, $keywords, $target_url, $status);

                if ($stmt->execute()) {
                    $success = "Banner uploaded successfully with SEO properties!";
                } else {
                    $errors[] = "Error saving banner to database.";
                }
            } else {
                $errors[] = "Error uploading banner.";
            }
        } else {
            $errors[] = "Only JPG, JPEG, and PNG formats are allowed.";
        }
    } else {
        $errors[] = "No file selected.";
    }
}

// Fetch all banners
$banners = $functions->getAllBanners();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Banners | Admin Panel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

    <?php include "sidebar.php"; ?>

    <div class="content">
        <div class="dashboard-header">
            <h2>Manage Banners</h2>
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

            <!-- Upload New Banner Form -->
            <div class="card">
                <div class="card-header bg-primary text-white">Upload New Banner with SEO</div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Banner Image</label>
                                <input type="file" name="banner" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label>Title (Alt Text)</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label>Meta Description</label>
                                <textarea name="meta_description" class="form-control"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label>Keywords (Comma-separated)</label>
                                <input type="text" name="keywords" class="form-control">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label>Target URL (Optional)</label>
                                <input type="text" name="target_url" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" name="upload_banner" class="btn btn-success mt-3">Upload Banner</button>
                    </form>
                </div>
            </div>

            <!-- Banners Table View -->
            <div class="card mt-4">
                <div class="card-header bg-primary text-white">Current Banners</div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Title</th>
                                <th>Meta Description</th>
                                <th>Keywords</th>
                                <th>Target URL</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($banners)): ?>
                                <?php foreach ($banners as $banner): ?>
                                    <tr>
                                        <td><?php echo $banner['id']; ?></td>
                                        <td><img src="../<?php echo $banner['image']; ?>" width="80"></td>
                                        <td><?php echo htmlspecialchars($banner['title']); ?></td>
                                        <td><?php echo htmlspecialchars($banner['meta_description']); ?></td>
                                        <td><?php echo htmlspecialchars($banner['keywords']); ?></td>
                                        <td><a href="<?php echo $banner['target_url']; ?>" target="_blank">Visit</a></td>
                                        <td>
                                            <span class="badge bg-<?php echo $banner['status'] == 'active' ? 'success' : 'danger'; ?>">
                                                <?php echo ucfirst($banner['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="edit_banner.php?id=<?php echo $banner['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                            <a href="delete_banner.php?id=<?php echo $banner['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center">No banners found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
