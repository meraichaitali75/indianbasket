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
    // First get the image path to delete the file
    $getStmt = $conn->prepare("SELECT image_path FROM product_images WHERE image_id = ?");
    $getStmt->bind_param("i", $image_id);
    $getStmt->execute();
    $imgResult = $getStmt->get_result();
    $imgData = $imgResult->fetch_assoc();

    if ($imgData) {
        $file_path = "../uploads/products/additional/" . $imgData['image_path'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }

    $delStmt = $conn->prepare("DELETE FROM product_images WHERE image_id = ? AND product_id = ?");
    $delStmt->bind_param("ii", $image_id, $product_id);
    $delStmt->execute();
    header("Location: edit_product.php?id=$product_id");
    exit;
}

// Delete main image
if (isset($_GET['delete_main_image'])) {
    $file_path = "../uploads/products/main/" . $product['image'];
    if (file_exists($file_path)) {
        unlink($file_path);
    }

    $stmt = $conn->prepare("UPDATE products SET image = NULL WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    header("Location: edit_product.php?id=$product_id");
    exit;
}

// Update product
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_product"])) {
    // Basic validation
    if (empty($_POST["name"]) || empty($_POST["category_id"]) || empty($_POST["price"]) || empty($_POST["stock"])) {
        $errors[] = "Please fill all required fields";
    }

    // Define allowed file types and size
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];
    $max_file_size = 5 * 1024 * 1024; // 5MB

    // Process main image upload if new one is provided
    $main_image = $product['image']; // Keep existing by default

    if (!empty($_FILES['main_image']['name'])) {
        $main_img_tmp = $_FILES['main_image']['tmp_name'];
        $main_img_ext = strtolower(pathinfo($_FILES['main_image']['name'], PATHINFO_EXTENSION));

        if (!in_array($main_img_ext, $allowed_extensions)) {
            $errors[] = "Main image: Only JPG, JPEG, PNG, and WEBP files are allowed.";
        } elseif ($_FILES['main_image']['size'] > $max_file_size) {
            $errors[] = "Main image: File size must be less than 5MB";
        } else {
            // Delete old main image if exists
            if (!empty($product['image'])) {
                $old_file = "../uploads/products/" . $product['image'];
                if (file_exists($old_file)) {
                    unlink($old_file);
                }
            }

            $main_image = uniqid('main_', true) . '.' . $main_img_ext;
            $main_img_path = "../uploads/products/" . $main_image;

            if (!move_uploaded_file($main_img_tmp, $main_img_path)) {
                $errors[] = "Failed to upload main image";
            }
        }
    }

    // Process additional images if no errors so far
    $new_additional_images = [];
    if (empty($errors) && !empty($_FILES['product_images']['name'][0])) {
        $additional_img_dir = "../uploads/products/product-images/";

        foreach ($_FILES['product_images']['tmp_name'] as $key => $tmp_name) {
            $img_name = $_FILES['product_images']['name'][$key];
            $img_size = $_FILES['product_images']['size'][$key];
            $img_ext = strtolower(pathinfo($img_name, PATHINFO_EXTENSION));

            if (!in_array($img_ext, $allowed_extensions)) {
                $errors[] = "Additional image $img_name: Invalid file type";
                continue;
            }

            if ($img_size > $max_file_size) {
                $errors[] = "Additional image $img_name: File too large";
                continue;
            }

            $unique_name = uniqid('additional_', true) . '.' . $img_ext;
            $target_path = $additional_img_dir . $unique_name;

            if (move_uploaded_file($tmp_name, $target_path)) {
                $new_additional_images[] = $unique_name;
            } else {
                $errors[] = "Failed to upload additional image $img_name";
            }
        }
    }

    // Only proceed with database operations if no errors
    if (empty($errors)) {
        // Prepare product data
        
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

        // Update product
        $query = "UPDATE products SET 
                 name=?, category_id=?, price=?, stock=?, description=?, image=?, 
                 material=?, legs=?, dimensions=?, length=?, depth=?, 
                 additional_details=?, video_url=?, type=?, mfg_date=?, life_days=? 
                 WHERE product_id=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param(
            "sidissssssssssssi", // Fixed type string (17 characters)
            $name,
            $category_id,
            $price,
            $stock,
            $description,
            $main_image,
            $material,
            $legs,
            $dimensions,
            $length,
            $depth,
            $additional_details,
            $video_url,
            $type,
            $mfg_date,
            $life_days,
            $product_id
        );

        if ($stmt->execute()) {
            // Insert new additional images
            if (!empty($new_additional_images)) {
                $imgQuery = "INSERT INTO product_images (product_id, image_path) VALUES (?, ?)";
                $imgStmt = $conn->prepare($imgQuery);

                foreach ($new_additional_images as $image) {
                    $imgStmt->bind_param("is", $product_id, $image);
                    $imgStmt->execute();
                }
            }

            $success = "Product updated successfully with " . count($new_additional_images) . " new additional images!";

            // Refresh product data
            $stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $product = $result->fetch_assoc();

            // Refresh additional images
            $imageStmt = $conn->prepare("SELECT * FROM product_images WHERE product_id = ?");
            $imageStmt->bind_param("i", $product_id);
            $imageStmt->execute();
            $imageResult = $imageStmt->get_result();
            $additional_images = $imageResult->fetch_all(MYSQLI_ASSOC);
        } else {
            $errors[] = "Error updating product: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Product | Admin Panel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        .preview-box {
            position: relative;
            width: 120px;
            height: 120px;
            margin: 5px;
        }

        .preview-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .remove-img-btn {
            position: absolute;
            top: 5px;
            right: 5px;
            width: 25px;
            height: 25px;
            border-radius: 50%;
            background: #dc3545;
            color: white;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }

        .image-upload-container {
            border: 2px dashed #dee2e6;
            border-radius: 5px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }

        .image-upload-container:hover {
            border-color: #0d6efd;
            background-color: #f8f9fa;
        }

        .main-image-preview {
            max-width: 300px;
            max-height: 300px;
            margin-top: 15px;
        }

        .required-field::after {
            content: " *";
            color: red;
        }

        .current-image {
            position: relative;
            display: inline-block;
            margin-right: 15px;
            margin-bottom: 15px;
        }

        .current-image img {
            max-width: 150px;
            max-height: 150px;
        }
    </style>
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
                    <ul><?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                <div class="row">
                    <!-- Basic Information -->
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header bg-dark text-white">
                                <i class="bi bi-info-circle"></i> Basic Information
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label required-field">Product Name</label>
                                    <input type="text" class="form-control" name="name"
                                        value="<?php echo htmlspecialchars($product['name']); ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label required-field">Category</label>
                                    <select name="category_id" class="form-select" required>
                                        <option value="">Select Category</option>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?php echo $cat['category_id']; ?>"
                                                <?php echo ($product['category_id'] == $cat['category_id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($cat['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required-field">Price</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" step="0.01" class="form-control" name="price"
                                                value="<?php echo $product['price']; ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required-field">Stock</label>
                                        <input type="number" class="form-control" name="stock"
                                            value="<?php echo $product['stock']; ?>" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-control" rows="3"><?php
                                                                                                echo htmlspecialchars($product['description']);
                                                                                                ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product Specifications -->
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header bg-dark text-white">
                                <i class="bi bi-gear"></i> Specifications
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Material</label>
                                        <input type="text" class="form-control" name="material"
                                            value="<?php echo htmlspecialchars($product['material']); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Legs</label>
                                        <input type="text" class="form-control" name="legs"
                                            value="<?php echo htmlspecialchars($product['legs']); ?>">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Dimensions</label>
                                    <input type="text" class="form-control" name="dimensions"
                                        value="<?php echo htmlspecialchars($product['dimensions']); ?>">
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Length</label>
                                        <input type="text" class="form-control" name="length"
                                            value="<?php echo htmlspecialchars($product['length']); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Depth</label>
                                        <input type="text" class="form-control" name="depth"
                                            value="<?php echo htmlspecialchars($product['depth']); ?>">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Type</label>
                                        <input type="text" class="form-control" name="type"
                                            value="<?php echo htmlspecialchars($product['type']); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Video URL</label>
                                        <input type="url" class="form-control" name="video_url"
                                            value="<?php echo htmlspecialchars($product['video_url']); ?>">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Manufacturing Date</label>
                                        <input type="date" class="form-control" name="mfg_date"
                                            value="<?php echo $product['mfg_date']; ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Life (in Days)</label>
                                        <input type="number" class="form-control" name="life_days"
                                            value="<?php echo $product['life_days']; ?>">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Additional Details</label>
                                    <textarea name="additional_details" class="form-control" rows="3"><?php
                                                                                                        echo htmlspecialchars($product['additional_details']);
                                                                                                        ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Image Upload Sections -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header bg-dark text-white">
                                <i class="bi bi-image"></i> Main Product Image
                            </div>
                            <div class="card-body">
                                <?php if (!empty($product['image'])): ?>
                                    <div class="current-image">
                                        <img src="../uploads/products/<?php echo $product['image']; ?>"
                                            class="img-thumbnail main-image-preview">
                                        <a href="edit_product.php?id=<?php echo $product_id; ?>&delete_main_image=1"
                                            class="btn btn-sm btn-danger d-block mt-2"
                                            onclick="return confirm('Are you sure you want to delete the main image?')">
                                            <i class="bi bi-trash"></i> Delete
                                        </a>
                                    </div>
                                <?php endif; ?>

                                <div class="image-upload-container" onclick="document.getElementById('main_image').click()">
                                    <i class="bi bi-cloud-arrow-up" style="font-size: 2rem;"></i>
                                    <p class="mt-2">Click to <?php echo empty($product['image']) ? 'upload' : 'change'; ?> main product image</p>
                                    <small class="text-muted">(Max 5MB, JPG/PNG/WEBP)</small>
                                    <input type="file" class="form-control d-none" id="main_image" name="main_image" accept="image/*">
                                </div>
                                <div id="newMainImagePreview" class="mt-3"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header bg-dark text-white">
                                <i class="bi bi-images"></i> Additional Images
                            </div>
                            <div class="card-body">
                                <?php if (!empty($additional_images)): ?>
                                    <div class="mb-3">
                                        <label>Current Additional Images</label>
                                        <div class="d-flex flex-wrap">
                                            <?php foreach ($additional_images as $img): ?>
                                                <div class="current-image">
                                                    <img src="../uploads/products/product-images/<?php echo $img['image_path']; ?>"
                                                        class="img-thumbnail">
                                                    <a href="edit_product.php?id=<?php echo $product_id; ?>&delete_image=<?php echo $img['image_id']; ?>"
                                                        class="btn btn-sm btn-danger d-block mt-2"
                                                        onclick="return confirm('Are you sure you want to delete this image?')">
                                                        <i class="bi bi-trash"></i> Delete
                                                    </a>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div class="image-upload-container" onclick="document.getElementById('product_images').click()">
                                    <i class="bi bi-cloud-arrow-up" style="font-size: 2rem;"></i>
                                    <p class="mt-2">Click to upload additional images</p>
                                    <small class="text-muted">(Optional, max 5MB each, JPG/PNG/WEBP)</small>
                                    <input type="file" class="form-control d-none" id="product_images" name="product_images[]" accept="image/*" multiple>
                                </div>
                                <div id="previewContainer" class="mt-3 d-flex flex-wrap"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mb-4">
                    <a href="products.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Products
                    </a>
                    <button type="submit" name="update_product" class="btn btn-primary">
                        <i class="bi bi-save"></i> Update Product
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Main image preview
        const mainImageInput = document.getElementById('main_image');
        const newMainImagePreview = document.getElementById('newMainImagePreview');

        mainImageInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                newMainImagePreview.innerHTML = '';

                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.classList.add('img-thumbnail', 'main-image-preview');

                    const box = document.createElement('div');
                    box.classList.add('position-relative');

                    const btn = document.createElement('button');
                    btn.innerHTML = '&times;';
                    btn.classList.add('remove-img-btn');
                    btn.title = "Remove this image";
                    btn.onclick = function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        mainImageInput.value = '';
                        newMainImagePreview.innerHTML = '';
                    };

                    box.appendChild(img);
                    box.appendChild(btn);
                    newMainImagePreview.appendChild(box);
                };
                reader.readAsDataURL(file);
            }
        });

        // Additional images preview
        const imageInput = document.getElementById('product_images');
        const previewContainer = document.getElementById('previewContainer');

        imageInput.addEventListener('change', function() {
            previewContainer.innerHTML = '';

            Array.from(this.files).forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const box = document.createElement('div');
                    box.classList.add('preview-box');

                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.classList.add('preview-img');

                    const btn = document.createElement('button');
                    btn.innerHTML = '&times;';
                    btn.classList.add('remove-img-btn');
                    btn.title = "Remove this image";
                    btn.onclick = function(e) {
                        e.preventDefault();
                        e.stopPropagation();

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

        // Form validation
        (function() {
            'use strict';

            const forms = document.querySelectorAll('.needs-validation');

            Array.from(forms).forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }

                    form.classList.add('was-validated');
                }, false);
            });
        })();
    </script>
</body>

</html>