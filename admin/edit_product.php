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
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($product_id <= 0) {
    header("Location: products.php");
    exit;
}

$errors = [];
$success = "";

// Fetch product
$stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    header("Location: products.php");
    exit;
}

// Fetch categories
$categories = [];
$catResult = $conn->query("SELECT category_id, name FROM categories");
if ($catResult) {
    $categories = $catResult->fetch_all(MYSQLI_ASSOC);
}

// Fetch additional images
$additional_images = [];
$imageStmt = $conn->prepare("SELECT * FROM product_images WHERE product_id = ?");
$imageStmt->bind_param("i", $product_id);
$imageStmt->execute();
$imageResult = $imageStmt->get_result();
$additional_images = $imageResult->fetch_all(MYSQLI_ASSOC);

// Delete additional image
if (isset($_GET['delete_image'])) {
    $image_id = intval($_GET['delete_image']);
    $delStmt = $conn->prepare("DELETE FROM product_images WHERE image_id = ? AND product_id = ?");
    $delStmt->bind_param("ii", $image_id, $product_id);
    $delStmt->execute();
    header("Location: edit_product.php?id=$product_id");
    exit;
}

// Delete main image
if (isset($_GET['delete_main_image'])) {
    $stmt = $conn->prepare("UPDATE products SET image = '' WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    header("Location: edit_product.php?id=$product_id");
    exit;
}

// Update product
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_product"])) {
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
    $mfg_date = !empty($_POST["mfg_date"]) ? $_POST["mfg_date"] : null;
    $life_days = intval($_POST["life_days"]);

    $target_dir = "../uploads/products/";
    $image = $product['image']; // Keep existing

    // Upload new main image
    if (!empty($_FILES["image"]["name"])) {
        $img_tmp = $_FILES["image"]["tmp_name"];
        $ext = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $image = uniqid('main_', true) . '.' . $ext;
        move_uploaded_file($img_tmp, $target_dir . $image);
    }

    // Update query
    $query = "UPDATE products SET name=?, category_id=?, price=?, stock=?, description=?, image=?, material=?, legs=?, dimensions=?, length=?, depth=?, additional_details=?, video_url=?, type=?, mfg_date=?, life_days=? WHERE product_id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sidissssssssssssi", $name, $category_id, $price, $stock, $description, $image, $material, $legs, $dimensions, $length, $depth, $additional_details, $video_url, $type, $mfg_date, $life_days, $product_id);

    if ($stmt->execute()) {
        // Handle additional images
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

        $success = "Product updated successfully!";
        // Refresh main product data
$stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

// 🔁 REFRESH additional_images here
$imageStmt = $conn->prepare("SELECT * FROM product_images WHERE product_id = ?");
$imageStmt->bind_param("i", $product_id);
$imageStmt->execute();
$imageResult = $imageStmt->get_result();
$additional_images = $imageResult->fetch_all(MYSQLI_ASSOC);

        $stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();
    } else {
        $errors[] = "Error updating product.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product | Admin Panel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
<?php include "sidebar.php"; ?>

<div class="content">
    <div class="container mt-4">
        <h2>Edit Product</h2>

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
                    <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                </div>
                <div class="mb-3 col-md-6">
                    <label>Category *</label>
                    <select name="category_id" class="form-select" required>
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['category_id']; ?>" <?php echo ($product['category_id'] == $cat['category_id']) ? 'selected' : ''; ?>><?php echo $cat['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3 col-md-4">
                    <label>Price *</label>
                    <input type="number" step="0.01" class="form-control" name="price" value="<?php echo $product['price']; ?>" required>
                </div>
                <div class="mb-3 col-md-4">
                    <label>Stock *</label>
                    <input type="number" class="form-control" name="stock" value="<?php echo $product['stock']; ?>" required>
                </div>
                <div class="mb-3 col-md-4">
                    <label>Type</label>
                    <input type="text" class="form-control" name="type" value="<?php echo htmlspecialchars($product['type']); ?>">
                </div>
                <div class="mb-3 col-md-6">
                    <label>Material</label>
                    <input type="text" class="form-control" name="material" value="<?php echo htmlspecialchars($product['material']); ?>">
                </div>
                <div class="mb-3 col-md-6">
                    <label>Legs</label>
                    <input type="text" class="form-control" name="legs" value="<?php echo htmlspecialchars($product['legs']); ?>">
                </div>
                <div class="mb-3 col-md-6">
                    <label>Dimensions</label>
                    <input type="text" class="form-control" name="dimensions" value="<?php echo htmlspecialchars($product['dimensions']); ?>">
                </div>
                <div class="mb-3 col-md-3">
                    <label>Length</label>
                    <input type="text" class="form-control" name="length" value="<?php echo htmlspecialchars($product['length']); ?>">
                </div>
                <div class="mb-3 col-md-3">
                    <label>Depth</label>
                    <input type="text" class="form-control" name="depth" value="<?php echo htmlspecialchars($product['depth']); ?>">
                </div>
                <div class="mb-3 col-md-6">
                    <label>Manufacturing Date</label>
                    <input type="date" class="form-control" name="mfg_date" value="<?php echo $product['mfg_date']; ?>">
                </div>
                <div class="mb-3 col-md-6">
                    <label>Life (in Days)</label>
                    <input type="number" class="form-control" name="life_days" value="<?php echo $product['life_days']; ?>">
                </div>
                <div class="mb-3">
                    <label>Video URL</label>
                    <input type="text" class="form-control" name="video_url" value="<?php echo htmlspecialchars($product['video_url']); ?>">
                </div>
                <div class="mb-3">
                    <label>Description</label>
                    <textarea name="description" class="form-control"><?php echo htmlspecialchars($product['description']); ?></textarea>
                </div>
                <div class="mb-3">
                    <label>Additional Details</label>
                    <textarea name="additional_details" class="form-control"><?php echo htmlspecialchars($product['additional_details']); ?></textarea>
                </div>
            </div>

            <div class="mb-3">
                <label>Main Product Image</label>
                <input type="file" class="form-control" name="image" accept="image/*">
                <?php if (!empty($product['image'])): ?>
                    <div class="mt-2">
                        <img src="../uploads/products/<?php echo $product['image']; ?>" width="100" class="img-thumbnail">
                        <a href="edit_product.php?id=<?php echo $product_id; ?>&delete_main_image=1" class="btn btn-sm btn-danger mt-1" onclick="return confirm('Delete main image?')">Delete</a>
                    </div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
    <label>Upload Additional Product Images</label>
    <input type="file" class="form-control" id="product_images" name="product_images[]" accept="image/*" multiple hidden>
    <button type="button" class="btn btn-outline-primary mt-2" onclick="document.getElementById('product_images').click()">Select Images</button>
    <div id="previewContainer" class="mt-3 d-flex flex-wrap gap-2"></div>
</div>


            <div class="mb-3">
                <label>Current Additional Images</label>
                <div class="d-flex flex-wrap gap-3">
                    <?php foreach ($additional_images as $img): ?>
                        <div>
                            <img src="../uploads/products/<?php echo $img['image_path']; ?>" width="100" class="img-thumbnail">
                            <a href="edit_product.php?id=<?php echo $product_id; ?>&delete_image=<?php echo $img['image_id']; ?>" class="btn btn-sm btn-danger d-block mt-1" onclick="return confirm('Delete this image?')">Delete</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <button type="submit" name="update_product" class="btn btn-primary">Update Product</button>
            <a href="products.php" class="btn btn-secondary">Back to Products</a>
        </form>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const imageInput = document.getElementById('product_images');
const previewContainer = document.getElementById('previewContainer');

imageInput.addEventListener('change', function () {
    previewContainer.innerHTML = '';
    Array.from(this.files).forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function (e) {
            const box = document.createElement('div');
            box.classList.add('position-relative');

            const img = document.createElement('img');
            img.src = e.target.result;
            img.classList.add('img-thumbnail');
            img.style.maxWidth = '100px';
            img.style.marginRight = '10px';

            const btn = document.createElement('button');
            btn.textContent = '×';
            btn.type = 'button';
            btn.classList.add('btn', 'btn-sm', 'btn-danger');
            btn.style.position = 'absolute';
            btn.style.top = '-5px';
            btn.style.right = '-5px';

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
