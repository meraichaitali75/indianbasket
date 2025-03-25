<?php
session_start(); // Start the session

// Include the functions.php file
require_once __DIR__ . "/includes/db/functions.php";

// Create an instance of the Functions class
$functions = new Functions();

// Get filter parameters from the request
$categories = isset($_POST['categories']) ? $_POST['categories'] : [];
$min_price = isset($_POST['min_price']) ? floatval($_POST['min_price']) : 0;
$max_price = isset($_POST['max_price']) ? floatval($_POST['max_price']) : 1000;

// Fetch filtered products
$filtered_products = $functions->getFilteredProducts($categories, $min_price, $max_price);

// Render the filtered products
foreach ($filtered_products as $product) {
    echo '
    <div class="col">
        <div class="tpproduct p-relative mb-20">
            <div class="tpproduct__thumb p-relative text-center">
                <a href="#"><img src="./uploads/products/' . $product['image'] . '" alt="' . $product['name'] . '"></a>
                <div class="tpproduct__info bage">
                    <span class="tpproduct__info-hot bage__hot">HOT</span>
                </div>
            </div>
            <div class="tpproduct__content">
                <h4 class="tpproduct__title">
                    <a href="shop-details.php?product_id=' . $product['product_id'] . '">' . $product['name'] . '</a>
                </h4>
                <div class="tpproduct__price">
                    <span>$' . $product['price'] . '</span>
                </div>
            </div>
        </div>
    </div>';
}
?>