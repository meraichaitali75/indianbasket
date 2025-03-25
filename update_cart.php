<?php
session_start(); // Start the session

// Include the functions.php file
require_once __DIR__ . "/includes/db/functions.php";

// Create an instance of the Functions class
$functions = new Functions();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);

    // Update cart quantity
    $functions->updateCartQuantity($product_id, $quantity);

    // Redirect back to the cart page
    header("Location: cart.php");
    exit();
}
?>