<?php
session_start();
require_once "./includes/db/functions.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['product_id'])) {
    $functions = new Functions();
    $product_id = $_GET['product_id'];
    $user_id = $_SESSION["user_id"];
    
    $functions->removeFromWishlist($user_id, $product_id);
    
    // Redirect back to wishlist page
    header("Location: wishlist.php");
    exit();
}
?>