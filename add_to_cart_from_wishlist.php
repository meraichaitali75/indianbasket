<?php
session_start();
require_once __DIR__ . "/includes/db/functions.php";

$functions = new Functions();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
        $product_id = intval($_POST['product_id']);
        $quantity = intval($_POST['quantity']);
        
        // Add to cart
        $result = $functions->addToCart($product_id, $quantity);
        
        // Optionally remove from wishlist
        if (isset($_POST['from_wishlist']) && $_POST['from_wishlist'] == 1 && isset($_SESSION['user_id'])) {
            $functions->removeFromWishlist($_SESSION['user_id'], $product_id);
        }
        
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Missing parameters']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>