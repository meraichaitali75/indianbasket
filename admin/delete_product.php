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

$upload_dir = "../uploads/products/";

// Fetch main image to delete
$stmt = $conn->prepare("SELECT image FROM products WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$stmt->bind_result($main_image);
$stmt->fetch();
$stmt->close();

if (!empty($main_image) && file_exists($upload_dir . $main_image)) {
    unlink($upload_dir . $main_image);
}

// Delete additional images
$imageStmt = $conn->prepare("SELECT image_path FROM product_images WHERE product_id = ?");
$imageStmt->bind_param("i", $product_id);
$imageStmt->execute();
$result = $imageStmt->get_result();

while ($row = $result->fetch_assoc()) {
    $path = $row['image_path'];
    if (!empty($path) && file_exists($upload_dir . $path)) {
        unlink($upload_dir . $path);
    }
}
$imageStmt->close();

// Delete image records from DB
$delImageRecords = $conn->prepare("DELETE FROM product_images WHERE product_id = ?");
$delImageRecords->bind_param("i", $product_id);
$delImageRecords->execute();
$delImageRecords->close();

// Delete product
$deleteProduct = $conn->prepare("DELETE FROM products WHERE product_id = ?");
$deleteProduct->bind_param("i", $product_id);

if ($deleteProduct->execute()) {
    $_SESSION['success'] = "Product deleted successfully!";
} else {
    $_SESSION['error'] = "Failed to delete product.";
}

header("Location: products.php");
exit;
?>
