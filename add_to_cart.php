<?php
session_start(); // Start the session

// Include the functions.php file
require_once __DIR__ . "/includes/db/functions.php";

// Create an instance of the Functions class
$functions = new Functions();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Debug: Print POST data
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";

    // Validate product_id and quantity
    if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
        $product_id = intval($_POST['product_id']);
        $quantity = intval($_POST['quantity']);

        // Debug: Print product_id and quantity
        echo "Product ID: $product_id, Quantity: $quantity<br>";

        // Add product to cart
        $result = $functions->addToCart($product_id, $quantity);

        // Debug: Print result of addToCart
        echo "Result of addToCart: ";
        var_dump($result);

        // Debug: Print session data
        echo "<pre>";
        print_r($_SESSION);
        echo "</pre>";

        // Redirect to the cart page
        header("Location: cart.php");
        exit();
    } else {
        echo "Error: product_id or quantity not set in POST data.";
    }
} else {
    echo "Error: Invalid request method.";
}
?>