<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/includes/db/functions.php";
$functions = new Functions();

// Initialize messages
$_SESSION['cart_message'] = '';
$_SESSION['cart_error'] = '';

if (isset($_GET['product_id']) && is_numeric($_GET['product_id'])) {
    $product_id = (int)$_GET['product_id'];
    
    if ($product_id <= 0) {
        $_SESSION['cart_error'] = "Invalid product ID";
        header("Location: cart.php");
        exit();
    }
    
    $product = $functions->getProductById($product_id);
    
    if ($product) {
        if (isset($_SESSION['cart'][$product_id])) {
            // Remove product from cart
            unset($_SESSION['cart'][$product_id]);
            
            // Force immediate session update
            session_write_close();
            session_start();
            
            // Clear cart completely if it's now empty
            if (empty($_SESSION['cart'])) {
                unset($_SESSION['cart']);
            }
            
            $_SESSION['cart_message'] = "{$product['name']} removed from cart successfully";
            
            // Force redirect with cache prevention
            header("Cache-Control: no-cache, must-revalidate");
            header("Location: cart.php");
            exit();
        } else {
            $_SESSION['cart_error'] = "{$product['name']} not found in your cart";
        }
    } else {
        $_SESSION['cart_error'] = "Product not found in our system";
    }
} else {
    $_SESSION['cart_error'] = "No product specified";
}

header("Location: cart.php");
exit();
?>