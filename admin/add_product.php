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
    // Basic validation
    if (empty($_POST["name"]) || empty($_POST["category_id"]) || empty($_POST["price"]) || empty($_POST["stock"])) {
        $errors[] = "Please fill all required fields";
    }

    // Process main image upload
    $main_image = null;
    if (!empty($_FILES['main_image']['name'])) {
        $main_img_tmp = $_FILES['main_image']['tmp_name'];
        $main_img_ext = strtolower(pathinfo($_FILES['main_image']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];
        
        if (!in_array($main_img_ext, $allowed_extensions)) {
            $errors[] = "Main image: Only JPG, JPEG, PNG, and WEBP files are allowed.";
        } elseif ($_FILES['main_image']['size'] > 5 * 1024 * 1024) { // 5MB limit
            $errors[] = "Main image: File size must be less than 5MB";
        } else {
            $main_image = uniqid('main_', true) . '.' . $main_img_ext;
            $main_img_path = "../uploads/products/" . $main_image;
            
            if (!move_uploaded_file($main_img_tmp, $main_img_path)) {
                $errors[] = "Failed to upload main image";
            }
        }
    } else {
        $errors[] = "Main product image is required";
    }

    // Process additional images if no errors so far
    $additional_images = [];
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
            
            if ($img_size > 5 * 1024 * 1024) { // 5MB limit
                $errors[] = "Additional image $img_name: File too large";
                continue;
            }
            
            $unique_name = uniqid('additional_', true) . '.' . $img_ext;
            $target_path = $additional_img_dir . $unique_name;
            
            if (move_uploaded_file($tmp_name, $target_path)) {
                $additional_images[] = $unique_name;
            } else {
                $errors[] = "Failed to upload product images $img_name";
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
        $mfg_date = $_POST["mfg_date"];
        $life_days = intval($_POST["life_days"]);

        // Insert product
        $query = "INSERT INTO products 
                 (name, category_id, price, stock, description, image, material, legs, dimensions, length, depth, 
                  additional_details, video_url, type, mfg_date, life_days)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($query);
        $stmt->bind_param(
            "sidisssssssssssi",
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
            $life_days
        );

        if ($stmt->execute()) {
            $product_id = $stmt->insert_id;
            
            // Insert additional images
            if (!empty($additional_images)) {
                $imgQuery = "INSERT INTO product_images (product_id, image_path) VALUES (?, ?)";
                $imgStmt = $conn->prepare($imgQuery);
                
                foreach ($additional_images as $image) {
                    $imgStmt->bind_param("is", $product_id, $image);
                    $imgStmt->execute();
                }
            }

            $success = "Product added successfully with " . count($additional_images) . " additional images!";
        } else {
            $errors[] = "Error inserting product: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
    </style>
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
                    <ul><?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?></ul>
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
                                    <input type="text" class="form-control" name="name" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label required-field">Category</label>
                                    <select name="category_id" class="form-select" required>
                                        <option value="">Select Category</option>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?php echo $cat['category_id']; ?>">
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
                                            <input type="number" step="0.01" class="form-control" name="price" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required-field">Stock</label>
                                        <input type="number" class="form-control" name="stock" required>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-control" rows="3"></textarea>
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
                                        <input type="text" class="form-control" name="material">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Legs</label>
                                        <input type="text" class="form-control" name="legs">
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Dimensions</label>
                                    <input type="text" class="form-control" name="dimensions">
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Length</label>
                                        <input type="text" class="form-control" name="length">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Depth</label>
                                        <input type="text" class="form-control" name="depth">
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Type</label>
                                        <input type="text" class="form-control" name="type">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Video URL</label>
                                        <input type="url" class="form-control" name="video_url">
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Manufacturing Date</label>
                                        <input type="date" class="form-control" name="mfg_date">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Life (in Days)</label>
                                        <input type="number" class="form-control" name="life_days">
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Additional Details</label>
                                    <textarea name="additional_details" class="form-control" rows="3"></textarea>
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
                                <div class="image-upload-container" onclick="document.getElementById('main_image').click()">
                                    <i class="bi bi-cloud-arrow-up" style="font-size: 2rem;"></i>
                                    <p class="mt-2">Click to upload main product image</p>
                                    <small class="text-muted">(Required, max 5MB, JPG/PNG/WEBP)</small>
                                    <input type="file" class="form-control d-none" id="main_image" name="main_image" accept="image/*" required>
                                </div>
                                <img id="mainImagePreview" class="main-image-preview d-none img-thumbnail">
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header bg-dark text-white">
                                <i class="bi bi-images"></i> Additional Images
                            </div>
                            <div class="card-body">
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
                    <button type="submit" name="create_product" class="btn btn-primary">
                        <i class="bi bi-save"></i> Add Product
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Main image preview
        const mainImageInput = document.getElementById('main_image');
        const mainImagePreview = document.getElementById('mainImagePreview');
        
        mainImageInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    mainImagePreview.src = e.target.result;
                    mainImagePreview.classList.remove('d-none');
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