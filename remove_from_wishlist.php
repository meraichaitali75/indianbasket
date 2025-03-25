<?php
session_start();
require_once __DIR__ . "/includes/db/functions.php";

$functions = new Functions();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['product_id']) && isset($_SESSION['user_id'])) {
        $product_id = intval($_POST['product_id']);
        $user_id = $_SESSION['user_id'];

        $result = $functions->removeFromWishlist($user_id, $product_id);
        echo json_encode($result);
    } else {
        echo json_encode(['success' => false, 'error' => 'Missing parameters']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
