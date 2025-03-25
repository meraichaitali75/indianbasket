<?php
session_start();
require_once __DIR__ . "/includes/db/functions.php";

$functions = new Functions();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['items']) && is_array($_POST['items'])) {
        $successCount = 0;

        foreach ($_POST['items'] as $item) {
            $product_id = intval($item['product_id']);
            $quantity = intval($item['quantity']);

            // Add to cart
            if ($functions->addToCart($product_id, $quantity)) {
                $successCount++;

                // Optionally remove from wishlist
                if (isset($_POST['from_wishlist']) && $_POST['from_wishlist'] == 1 && isset($_SESSION['user_id'])) {
                    $functions->removeFromWishlist($_SESSION['user_id'], $product_id);
                }
            }
        }

        echo json_encode(['success' => true, 'count' => $successCount]);
    } else {
        echo json_encode(['success' => false, 'error' => 'No items provided']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
