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

// Fetch Categories
$categories = [];
$result = $conn->query("SELECT category_id, name FROM categories");
if ($result) {
    $categories = $result->fetch_all(MYSQLI_ASSOC);
}

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["create_product"])) {
    $name = trim($_POST["name"]);
    $category_id = intval($_POST["category_id"]);
    $price = floatval($_POST["price"]);
    $stock = intval($_POST["stock"]);
    $description = trim($_POST["description"]);
    $material = trim($_POST["material"]);
    $legs = trim($_POST["legs"]);
    $dimensions = trim($_POST["dimensions"]);
    $length = trim($_POST["length"]);
    $depth = trim($_POST["depth"]);
    $additional_details = trim($_POST["additional_details"]);
    $video_url = trim($_POST["video_url"]);
    $type = trim($_POST["type"]);
    $mfg_date = $_POST["mfg_date"];
    $life_days = intval($_POST["life_days"]);

    $image = null;
    if (!empty($_FILES['main_image']['name'])) {
        $main_img_tmp = $_FILES['main_image']['tmp_name'];
        $main_img_ext = pathinfo($_FILES['main_image']['name'], PATHINFO_EXTENSION);
        $image = uniqid('main_', true) . '.' . $main_img_ext;
        move_uploaded_file($main_img_tmp, "../uploads/products/" . $image);
    }
    
    // Add image to the query
    $query = "INSERT INTO products 
    (name, category_id, price, stock, description, image, material, legs, dimensions, length, depth, additional_details, video_url, type, mfg_date, life_days)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param(
        "sidisssssssssssi", // 16 values now match
        $name, $category_id, $price, $stock, $description,
        $image, $material, $legs, $dimensions, $length, $depth,
        $additional_details, $video_url, $type, $mfg_date, $life_days
    );
    
    if ($stmt->execute()) {
        $product_id = $stmt->insert_id;
        $target_dir = "../uploads/products/";

        // Handle multiple images with unique names
        if (!empty($_FILES['product_images']['name'][0])) {
            foreach ($_FILES['product_images']['name'] as $key => $img_name) {
                $img_tmp = $_FILES['product_images']['tmp_name'][$key];
                $ext = pathinfo($img_name, PATHINFO_EXTENSION);
                $unique_name = uniqid('product_', true) . '.' . $ext;
                $img_target = $target_dir . $unique_name;

                if (move_uploaded_file($img_tmp, $img_target)) {
                    $imgQuery = "INSERT INTO product_images (product_id, image_path) VALUES (?, ?)";
                    $imgStmt = $conn->prepare($imgQuery);
                    $imgStmt->bind_param("is", $product_id, $unique_name);
                    $imgStmt->execute();
                }
            }
        }

        $success = "Product added successfully!";
    } else {
        $errors[] = "Error inserting product.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

<?php include "sidebar.php"; ?>
<div class="content">
<div class="container mt-4">
    <h2>Add Product</h2>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul><?php foreach ($errors as $error): ?><li><?php echo $error; ?></li><?php endforeach; ?></ul>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="row">
            <div class="mb-3 col-md-6">
                <label>Product Name *</label>
                <input type="text" class="form-control" name="name" required>
            </div>
            <div class="mb-3 col-md-6">
                <label>Category *</label>
                <select name="category_id" class="form-select" required>
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['category_id']; ?>"><?php echo $cat['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3 col-md-4">
                <label>Price *</label>
                <input type="number" step="0.01" class="form-control" name="price" required>
            </div>
            <div class="mb-3 col-md-4">
                <label>Stock *</label>
                <input type="number" class="form-control" name="stock" required>
            </div>
            <div class="mb-3 col-md-4">
                <label>Type</label>
                <input type="text" class="form-control" name="type">
            </div>
            <div class="mb-3 col-md-6">
                <label>Material</label>
                <input type="text" class="form-control" name="material">
            </div>
            <div class="mb-3 col-md-6">
                <label>Legs</label>
                <input type="text" class="form-control" name="legs">
            </div>
            <div class="mb-3 col-md-6">
                <label>Dimensions</label>
                <input type="text" class="form-control" name="dimensions">
            </div>
            <div class="mb-3 col-md-3">
                <label>Length</label>
                <input type="text" class="form-control" name="length">
            </div>
            <div class="mb-3 col-md-3">
                <label>Depth</label>
                <input type="text" class="form-control" name="depth">
            </div>
            <div class="mb-3 col-md-6">
                <label>Manufacturing Date</label>
                <input type="date" class="form-control" name="mfg_date">
            </div>
            <div class="mb-3 col-md-6">
                <label>Life (in Days)</label>
                <input type="number" class="form-control" name="life_days">
            </div>
            <div class="mb-3">
                <label>Video URL</label>
                <input type="text" class="form-control" name="video_url">
            </div>
            <div class="mb-3">
                <label>Description</label>
                <textarea name="description" class="form-control"></textarea>
            </div>
            <div class="mb-3">
                <label>Additional Details</label>
                <textarea name="additional_details" class="form-control"></textarea>
            </div>
            <div class="mb-3">
        <label for="main_image" class="form-label">Main Product Image</label>
        <input type="file" class="form-control" id="main_image" name="main_image" accept="image/*">
    </div>

    <div class="mb-3">
        <label for="product_images" class="form-label">Upload Additional Images</label>
        <input type="file" class="form-control d-none" id="product_images" name="product_images[]" accept="image/*" multiple>
        <button type="button" class="btn btn-outline-primary mt-2" onclick="document.getElementById('product_images').click()">Select Images</button>
        <div id="previewContainer" class="mt-3 d-flex flex-wrap gap-2"></div>
    </div>
</div>
        <button type="submit" name="create_product" class="btn btn-primary">Add Product</button>
    </form>
</div>
                    </div>

                    <script>
const imageInput = document.getElementById('product_images');
const previewContainer = document.getElementById('previewContainer');

imageInput.addEventListener('change', function () {
    previewContainer.innerHTML = '';
    Array.from(this.files).forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function (e) {
            const box = document.createElement('div');
            box.classList.add('preview-box', 'position-relative');

            const img = document.createElement('img');
            img.src = e.target.result;
            img.classList.add('preview-img', 'img-thumbnail');

            const btn = document.createElement('button');
            btn.innerHTML = '&times;';
            btn.classList.add('remove-img-btn');
            btn.onclick = function () {
                const dt = new DataTransfer();
                const files = Array.from(imageInput.files);
                files.splice(index, 1);
                files.forEach(f => dt.items.add(f));
                imageInput.files = dt.files;
                imageInput.dispatchEvent(new Event('change'));
            };

            box.appendChild(img);
            box.appendChild(btn);
            previewContainer.appendChild(box);
        };
        reader.readAsDataURL(file);
    });
});
</script>
</body>
</html>