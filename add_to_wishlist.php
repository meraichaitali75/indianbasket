<?php
session_start();
require_once "./includes/db/functions.php";

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verify user is logged in
if (!isset($_SESSION["user_id"])) {
    $_SESSION['error'] = "Please login to manage your wishlist";
    header("Location: login.php");
    exit();
}

// Verify product_id parameter
if (!isset($_GET['product_id']) || !is_numeric($_GET['product_id'])) {
    $_SESSION['error'] = "Invalid product";
    header("Location: shop.php");
    exit();
}

$functions = new Functions();
$product_id = (int)$_GET['product_id'];
$user_id = (int)$_SESSION["user_id"];

// Verify product exists
$product = $functions->getProductById($product_id);
if (!$product) {
    $_SESSION['error'] = "Product not found";
    header("Location: shop.php");
    exit();
}

// Process wishlist action
if ($functions->isInWishlist($user_id, $product_id)) {
    $result = $functions->removeFromWishlist($user_id, $product_id);
    if ($result['status'] === 'success') {
        $_SESSION['message'] = $result['message'];
    } else {
        $_SESSION['error'] = $result['message'];
    }
} else {
    $result = $functions->addToWishlist($user_id, $product_id);
    if ($result['status'] === 'success' || $result['status'] === 'exists') {
        $_SESSION['message'] = $result['message'];
    } else {
        $_SESSION['error'] = $result['message'];
    }
}

// Redirect back
header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'shop.php'));
exit();
