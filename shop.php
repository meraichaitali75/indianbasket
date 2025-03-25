<?php
session_start(); // Start the session

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

// Include the functions.php file
require_once __DIR__ . "/includes/db/functions.php";

// Create an instance of the Functions class
$functions = new Functions();

// Fetch products from the database
$products = $functions->getProducts();
?>

<!doctype html>
<html class="no-js" lang="zxx">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Indianbasket | Products</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Place favicon.ico in the root directory -->
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/logo/favicon.png">

    <!-- CSS here -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/animate.css">
    <link rel="stylesheet" href="assets/css/swiper-bundle.css">
    <link rel="stylesheet" href="assets/css/slick.css">
    <link rel="stylesheet" href="assets/css/magnific-popup.css">
    <link rel="stylesheet" href="assets/css/spacing.css">
    <link rel="stylesheet" href="assets/css/meanmenu.css">
    <link rel="stylesheet" href="assets/css/nice-select.css">
    <link rel="stylesheet" href="assets/css/fontawesome.min.css">
    <link rel="stylesheet" href="assets/css/icon-dukamarket.css">
    <link rel="stylesheet" href="assets/css/jquery-ui.css">
    <link rel="stylesheet" href="assets/css/main.css">

</head>

<body>
    <!-- Scroll-top -->
    <button class="scroll-top scroll-to-target" data-target="html">
        <i class="icon-chevrons-up"></i>
    </button>
    <!-- Scroll-top-end-->

    <!-- header-area-start -->
    <?php include "includes/header.php"; ?>
    <!-- header-area-end -->

    <main>
        <!-- breadcrumb-area-start -->
        <div class="breadcrumb__area grey-bg pt-5 pb-5">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="tp-breadcrumb__content">
                            <div class="tp-breadcrumb__list">
                                <span class="tp-breadcrumb__active"><a href="index.php">Home</a></span>
                                <span class="dvdr">/</span>
                                <span>Shop</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- breadcrumb-area-end -->

        <!-- shop-area-start -->
        <section class="shop-area-start grey-bg pb-200">
            <div class="container">
                <div class="row">
                    <div class="col-xl-2 col-lg-12 col-md-12">
                        <div class="tpshop__leftbar">
                            <div class="tpshop__widget mb-30 pb-25">
                                <h4 class="tpshop__widget-title">Product Categories</h4>
                                <?php
                                // Fetch categories from the database
                                $categories = $functions->getCategories(); // Ensure this method exists in your Functions class
                                foreach ($categories as $category) {
                                    echo '
                                    <div class="form-check">
                                        <input class="form-check-input category-filter" type="checkbox" value="' . $category['category_id'] . '" id="category-' . $category['category_id'] . '">
                                        <label class="form-check-label" for="category-' . $category['category_id'] . '">
                                            ' . $category['name'] . ' (' . $category['product_count'] . ')
                                        </label>
                                    </div>';
                                }
                                ?>
                            </div>
                            <div class="tpshop__widget mb-30 pb-25">
                                <h4 class="tpshop__widget-title mb-20">FILTER BY PRICE</h4>
                                <div class="productsidebar">
                                    <div class="productsidebar__head"></div>
                                    <div class="productsidebar__range">
                                        <div id="slider-range"></div>
                                        <div class="price-filter mt-10">
                                            <input type="text" id="amount" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="productsidebar__btn mt-15 mb-15">
                                    <button id="apply-filters" class="tp-btn tp-color-btn">FILTER</button>
                                </div>
                            </div>
                        </div>
                        <div class="tpshop__widget">
                            <div class="tpshop__sidbar-thumb mt-35">
                                <img src="assets/img/shape/sidebar-product-1.png" alt="">
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-10 col-lg-12 col-md-12">
                        <div class="tpshop__top ml-60">
                            <div class="tpshop__banner mb-30" data-background="assets/img/banner/shop-bg-1.jpg">
                                <div class="tpshop__content text-center">
                                    <span>The Salad</span>
                                    <h4 class="tpshop__content-title mb-20">Fresh & Natural <br>Healthy Food Special Offer</h4>
                                    <p>Do not miss the current offers of us!</p>
                                </div>
                            </div>
                            <div class="product__filter-content mb-40">
                                <div class="row align-items-center">
                                    <div class="col-sm-4">
                                        <div class="product__item-count">
                                            <span>Showing 1 - <?php echo count($products); ?> of <?php echo count($products); ?> Products</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="tpproductnav tpnavbar product-filter-nav d-flex align-items-center justify-content-center">
                                            <nav>
                                                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                                    <button class="nav-link" id="nav-all-tab" data-bs-toggle="tab" data-bs-target="#nav-all" type="button" role="tab" aria-controls="nav-all" aria-selected="false">
                                                        <i>
                                                            <svg width="22" height="16" viewBox="0 0 22 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M2 4C3.10457 4 4 3.10457 4 2C4 0.89543 3.10457 0 2 0C0.89543 0 0 0.89543 0 2C0 3.10457 0.89543 4 2 4Z" fill="currentColor" />
                                                                <path d="M2 10C3.10457 10 4 9.10457 4 8C4 6.89543 3.10457 6 2 6C0.89543 6 0 6.89543 0 8C0 9.10457 0.89543 10 2 10Z" fill="currentColor" />
                                                                <path d="M2 16C3.10457 16 4 15.1046 4 14C4 12.8954 3.10457 12 2 12C0.89543 12 0 12.8954 0 14C0 15.1046 0.89543 16 2 16Z" fill="currentColor" />
                                                                <path d="M8 4C9.10457 4 10 3.10457 10 2C10 0.89543 9.10457 0 8 0C6.89543 0 6 0.89543 6 2C6 3.10457 6.89543 4 8 4Z" fill="currentColor" />
                                                                <path d="M8 10C9.10457 10 10 9.10457 10 8C10 6.89543 9.10457 6 8 6C6.89543 6 6 6.89543 6 8C6 9.10457 6.89543 10 8 10Z" fill="currentColor" />
                                                                <path d="M8 16C9.10457 16 10 15.1046 10 14C10 12.8954 9.10457 12 8 12C6.89543 12 6 12.8954 6 14C6 15.1046 6.89543 16 8 16Z" fill="currentColor" />
                                                                <path d="M14 4C15.1046 4 16 3.10457 16 2C16 0.89543 15.1046 0 14 0C12.8954 0 12 0.89543 12 2C12 3.10457 12.8954 4 14 4Z" fill="currentColor" />
                                                                <path d="M14 10C15.1046 10 16 9.10457 16 8C16 6.89543 15.1046 6 14 6C12.8954 6 12 6.89543 12 8C12 9.10457 12.8954 10 14 10Z" fill="currentColor" />
                                                                <path d="M14 16C15.1046 16 16 15.1046 16 14C16 12.8954 15.1046 12 14 12C12.8954 12 12 12.8954 12 14C12 15.1046 12.8954 16 14 16Z" fill="currentColor" />
                                                                <path d="M20 4C21.1046 4 22 3.10457 22 2C22 0.89543 21.1046 0 20 0C18.8954 0 18 0.89543 18 2C18 3.10457 18.8954 4 20 4Z" fill="currentColor" />
                                                                <path d="M20 10C21.1046 10 22 9.10457 22 8C22 6.89543 21.1046 6 20 6C18.8954 6 18 6.89543 18 8C18 9.10457 18.8954 10 20 10Z" fill="currentColor" />
                                                                <path d="M20 16C21.1046 16 22 15.1046 22 14C22 12.8954 21.1046 12 20 12C18.8954 12 18 12.8954 18 14C18 15.1046 18.8954 16 20 16Z" fill="currentColor" />
                                                            </svg>
                                                        </i>
                                                    </button>
                                                    <button class="nav-link active" id="nav-popular-tab" data-bs-toggle="tab" data-bs-target="#nav-popular" type="button" role="tab" aria-controls="nav-popular" aria-selected="true">
                                                        <i>
                                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M2 4C3.10457 4 4 3.10457 4 2C4 0.89543 3.10457 0 2 0C0.89543 0 0 0.89543 0 2C0 3.10457 0.89543 4 2 4Z" fill="currentColor" />
                                                                <path d="M2 10C3.10457 10 4 9.10457 4 8C4 6.89543 3.10457 6 2 6C0.89543 6 0 6.89543 0 8C0 9.10457 0.89543 10 2 10Z" fill="currentColor" />
                                                                <path d="M2 16C3.10457 16 4 15.1046 4 14C4 12.8954 3.10457 12 2 12C0.89543 12 0 12.8954 0 14C0 15.1046 0.89543 16 2 16Z" fill="currentColor" />
                                                                <path d="M8 4C9.10457 4 10 3.10457 10 2C10 0.89543 9.10457 0 8 0C6.89543 0 6 0.89543 6 2C6 3.10457 6.89543 4 8 4Z" fill="currentColor" />
                                                                <path d="M8 10C9.10457 10 10 9.10457 10 8C10 6.89543 9.10457 6 8 6C6.89543 6 6 6.89543 6 8C6 9.10457 6.89543 10 8 10Z" fill="currentColor" />
                                                                <path d="M8 16C9.10457 16 10 15.1046 10 14C10 12.8954 9.10457 12 8 12C6.89543 12 6 12.8954 6 14C6 15.1046 6.89543 16 8 16Z" fill="currentColor" />
                                                                <path d="M14 4C15.1046 4 16 3.10457 16 2C16 0.89543 15.1046 0 14 0C12.8954 0 12 0.89543 12 2C12 3.10457 12.8954 4 14 4Z" fill="currentColor" />
                                                                <path d="M14 10C15.1046 10 16 9.10457 16 8C16 6.89543 15.1046 6 14 6C12.8954 6 12 6.89543 12 8C12 9.10457 12.8954 10 14 10Z" fill="currentColor" />
                                                                <path d="M14 16C15.1046 16 16 15.1046 16 14C16 12.8954 15.1046 12 14 12C12.8954 12 12 12.8954 12 14C12 15.1046 12.8954 16 14 16Z" fill="currentColor" />
                                                            </svg>
                                                        </i>
                                                    </button>
                                                    <button class="nav-link" id="nav-product-tab" data-bs-toggle="tab" data-bs-target="#nav-product" type="button" role="tab" aria-controls="nav-product" aria-selected="false">
                                                        <i>
                                                            <svg width="20" height="16" viewBox="0 0 20 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M2 4C3.10457 4 4 3.10457 4 2C4 0.89543 3.10457 0 2 0C0.89543 0 0 0.89543 0 2C0 3.10457 0.89543 4 2 4Z" fill="currentColor" />
                                                                <path d="M2 10C3.10457 10 4 9.10457 4 8C4 6.89543 3.10457 6 2 6C0.89543 6 0 6.89543 0 8C0 9.10457 0.89543 10 2 10Z" fill="currentColor" />
                                                                <path d="M2 16C3.10457 16 4 15.1046 4 14C4 12.8954 3.10457 12 2 12C0.89543 12 0 12.8954 0 14C0 15.1046 0.89543 16 2 16Z" fill="currentColor" />
                                                                <path d="M20 2C20 2.552 19.553 3 19 3H7C6.448 3 6 2.552 6 2C6 1.448 6.448 1 7 1H19C19.553 1 20 1.447 20 2Z" fill="currentColor" />
                                                                <path d="M20 8C20 8.552 19.553 9 19 9H7C6.448 9 6 8.552 6 8C6 7.448 6.448 7 7 7H19C19.553 7 20 7.447 20 8Z" fill="currentColor" />
                                                                <path d="M20 14C20 14.552 19.553 15 19 15H7C6.448 15 6 14.552 6 14C6 13.447 6.448 13 7 13H19C19.553 13 20 13.447 20 14Z" fill="currentColor" />
                                                            </svg>
                                                        </i>
                                                    </button>
                                                </div>
                                            </nav>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <!-- <div class="product__navtabs d-flex justify-content-end align-items-center">
                                            <div class="tp-shop-selector">
                                                <select style="display: none;">
                                                    <option>Default sorting</option>
                                                    <option>Show 14</option>
                                                    <option>Show 08</option>
                                                    <option>Show 20</option>
                                                </select>
                                                <div class="nice-select" tabindex="0">
                                                    <span class="current">Default sorting</span>
                                                    <ul class="list">
                                                        <li data-value="Show 12" class="option selected">Default sorting</li>
                                                        <li data-value="Show 14" class="option">Short popularity</li>
                                                        <li data-value="Show 08" class="option">Show 08</li>
                                                        <li data-value="Show 20" class="option">Show 20</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div> -->
                                    </div>
                                </div>
                            </div>
                            <div class="tab-content" id="nav-tabContent">
                                <div class="tab-pane fade" id="nav-all" role="tabpanel" aria-labelledby="nav-all-tab">
                                    <div class="row row-cols-xxl-4 row-cols-xl-4 row-cols-lg-4 row-cols-md-4 row-cols-sm-2 row-cols-1 tpproduct__shop-item">
                                        <?php foreach ($products as $product): ?>
                                            <div class="col">
                                                <div class="tpproduct p-relative mb-20">
                                                    <!-- Product Thumbnail -->
                                                    <div class="tpproduct__thumb p-relative text-center">
                                                        <a href="#"><img src="./uploads/products/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>"></a>
                                                        <a class="tpproduct__thumb-img" href="shopdetails.php?product_id=<?php echo $product['product_id']; ?>"><img src="./uploads/products/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>"></a>
                                                        <div class="tpproduct__info bage">
                                                            <span class="tpproduct__info-discount bage__discount">-50%</span>
                                                            <span class="tpproduct__info-hot bage__hot">HOT</span>
                                                        </div>

                                                        <div class="tpproduct__shopping">
                                                            <a href="add_to_wishlist.php?product_id=<?= $product['product_id'] ?>"
                                                                class="wishlist-btn <?= $functions->isInWishlist($_SESSION['user_id'], $product['product_id']) ? 'in-wishlist' : '' ?>">
                                                                <i class="icon-heart"></i>

                                                            </a>
                                                            <!-- <a class="tpproduct__shopping-wishlist" href="#"><i class="icon-layers"></i></a> -->
                                                            <!-- <a class="tpproduct__shopping-cart" href="#"><i class="icon-eye"></i></a> -->
                                                        </div>
                                                    </div>
                                                    <!-- Product Content -->
                                                    <div class="tpproduct__content">
                                                        <span class="tpproduct__content-weight">
                                                            <a href="shopdetails.php?product_id=<?php echo $product['product_id']; ?>"><?php echo $product['name']; ?></a>
                                                        </span>
                                                        <h4 class="tpproduct__title">
                                                            <a href="shopdetails.php?product_id=<?php echo $product['product_id']; ?>"><?php echo $product['name']; ?></a>
                                                        </h4>
                                                        <div class="tpproduct__rating mb-5">
                                                            <a href="#"><i class="icon-star_outline1"></i></a>
                                                            <a href="#"><i class="icon-star_outline1"></i></a>
                                                            <a href="#"><i class="icon-star_outline1"></i></a>
                                                            <a href="#"><i class="icon-star_outline1"></i></a>
                                                            <a href="#"><i class="icon-star_outline1"></i></a>
                                                        </div>
                                                        <div class="tpproduct__price">
                                                            <span>$<?php echo $product['price']; ?></span>
                                                            <del>$19.00</del>
                                                        </div>
                                                    </div>
                                                    <!-- Product Hover Text (Optional) -->
                                                    <div class="tpproduct__hover-text">
                                                        <div class="tpproduct__hover-btn d-flex justify-content-center mb-10">
                                                            <!-- <form action="add_to_cart.php" method="POST" class="add-to-cart-form">
                                                                <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                                                <div class="product__details-quantity d-flex align-items-center">
                                                                    <span class="cart-minus"><i class="far fa-minus"></i></span>
                                                                    <input class="tp-cart-input" type="text" name="quantity" value="1">
                                                                    <span class="cart-plus"><i class="far fa-plus"></i></span>
                                                                </div>
                                                                <button type="submit" class="tp-btn-2">Add to cart</button>
                                                            </form> -->
                                                            <form action="add_to_cart.php" method="POST" class="add-to-cart-form">
                                                                <div class="product__details-quantity quantity-ui">
                                                                    <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                                                    <div class="quantity-controls">
                                                                        <span class="cart-minus quantity-minus"><i class="fas fa-minus"></i></span>
                                                                        <input class="quantity-input" type="text" name="quantity" value="1">
                                                                        <span class="cart-plus quantity-plus"><i class="fas fa-plus"></i></span>
                                                                    </div>
                                                                    <button type="submit" class="tp-btn-2">Add to cart</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                        <div class="tpproduct__descrip">
                                                            <?php
                                                            $mfg_date = !empty($product['mfg_date']) ? new DateTime($product['mfg_date']) : null;
                                                            ?>
                                                            <ul>
                                                                <li>Type: <?php echo !empty($product['type']) ? htmlspecialchars($product['type']) : 'Not specified'; ?></li>
                                                                <li>MFG: <?php echo $mfg_date ? $mfg_date->format('F j, Y') : 'Not specified'; ?></li>
                                                                <li>LIFE: <?php echo !empty($product['life_days']) ? htmlspecialchars($product['life_days']) . ' days' : 'Not specified'; ?></li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <div class="tab-pane fade show active" id="nav-popular" role="tabpanel" aria-labelledby="nav-popular-tab">
                                    <div class="row row-cols-xxl-3 row-cols-xl-3 row-cols-lg-3 row-cols-md-3 row-cols-sm-2 row-cols-1 tpproduct__shop-item">
                                        <?php foreach ($products as $product): ?>
                                            <div class="col">
                                                <div class="tpproduct p-relative mb-20">
                                                    <div class="tpproduct__thumb p-relative text-center">
                                                        <a href="#"><img src="./uploads/products/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>"></a>
                                                        <a class="tpproduct__thumb-img" href="shopdetails.php?product_id=<?php echo $product['product_id']; ?>"><img src="./uploads/products/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>"></a>
                                                        <div class="tpproduct__info bage">
                                                            <span class="tpproduct__info-discount bage__discount">-50%</span>
                                                            <span class="tpproduct__info-hot bage__hot">HOT</span>
                                                        </div>
                                                        <div class="tpproduct__shopping">
                                                            <a href="add_to_wishlist.php?product_id=<?= $product['product_id'] ?>"
                                                                class="wishlist-btn <?= $functions->isInWishlist($_SESSION['user_id'], $product['product_id']) ? 'in-wishlist' : '' ?>">
                                                                <i class="icon-heart"></i>
                                                            </a>
                                                            <!-- <a class="tpproduct__shopping-wishlist" href="#"><i class="icon-layers"></i></a>
                                                            <a class="tpproduct__shopping-cart" href="#"><i class="icon-eye"></i></a> -->
                                                        </div>
                                                    </div>
                                                    <div class="tpproduct__content">
                                                        <span class="tpproduct__content-weight">
                                                            <a href="shopdetails.php?product_id=<?php echo $product['product_id']; ?>"><?php echo $product['category_name']; ?></a>
                                                        </span>
                                                        <h4 class="tpproduct__title">
                                                            <a href="shopdetails.php?product_id=<?php echo $product['product_id']; ?>"><?php echo $product['name']; ?></a>
                                                        </h4>
                                                        <div class="tpproduct__rating mb-5">
                                                            <a href="#"><i class="icon-star_outline1"></i></a>
                                                            <a href="#"><i class="icon-star_outline1"></i></a>
                                                            <a href="#"><i class="icon-star_outline1"></i></a>
                                                            <a href="#"><i class="icon-star_outline1"></i></a>
                                                            <a href="#"><i class="icon-star_outline1"></i></a>
                                                        </div>
                                                        <div class="tpproduct__price">
                                                            <span>$<?php echo $product['price']; ?></span>
                                                            <del>$19.00</del>
                                                        </div>
                                                    </div>
                                                    <div class="tpproduct__hover-text">
                                                        <div class="tpproduct__hover-btn d-flex justify-content-center mb-0">
                                                            <form action="add_to_cart.php" method="POST" class="add-to-cart-form">
                                                                <div class="product__details-quantity quantity-ui">
                                                                    <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                                                    <div class="quantity-controls">
                                                                        <span class="cart-minus quantity-minus"><i class="fas fa-minus"></i></span>
                                                                        <input class="quantity-input" type="text" name="quantity" value="1">
                                                                        <span class="cart-plus quantity-plus"><i class="fas fa-plus"></i></span>
                                                                    </div>
                                                                    <button type="submit" class="tp-btn-2">Add to cart</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                        <div class="tpproduct__descrip">
                                                            <?php
                                                            $mfg_date = !empty($product['mfg_date']) ? new DateTime($product['mfg_date']) : null;
                                                            ?>
                                                            <ul>
                                                                <li>Type: <?php echo !empty($product['type']) ? htmlspecialchars($product['type']) : 'Not specified'; ?></li>
                                                                <li>MFG: <?php echo $mfg_date ? $mfg_date->format('F j, Y') : 'Not specified'; ?></li>
                                                                <li>LIFE: <?php echo !empty($product['life_days']) ? htmlspecialchars($product['life_days']) . ' days' : 'Not specified'; ?></li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <div class="tab-pane fade whight-product" id="nav-product" role="tabpanel" aria-labelledby="nav-product-tab">
                                    <div class="row">
                                        <?php foreach ($products as $product): ?>
                                            <div class="col-lg-12">
                                                <div class="tplist__product d-flex align-items-center justify-content-between mb-20">
                                                    <div class="tplist__product-img">
                                                        <a href="#" class="tplist__product-img-one"><img src="./uploads/products/<?php echo $product['image']; ?>" alt=""></a>
                                                        <a class="tplist__product-img-two" href="shopdetails.php?product_id=<?php echo $product['product_id']; ?>"><img src="./uploads/products/<?php echo $product['image']; ?>" alt=""></a>
                                                        <div class="tpproduct__info bage">
                                                            <span class="tpproduct__info-discount bage__discount">-50%</span>
                                                            <span class="tpproduct__info-hot bage__hot">HOT</span>
                                                        </div>
                                                    </div>
                                                    <div class="tplist__content">
                                                        <span>500 gram</span>
                                                        <h4 class="tplist__content-title"><a href="#"><?php echo $product['name']; ?></a></h4>
                                                        <div class="tplist__rating mb-5">
                                                            <a href="#"><i class="icon-star_outline1"></i></a>
                                                            <a href="#"><i class="icon-star_outline1"></i></a>
                                                            <a href="#"><i class="icon-star_outline1"></i></a>
                                                            <a href="#"><i class="icon-star_outline1"></i></a>
                                                            <a href="#"><i class="icon-star_outline1"></i></a>
                                                        </div>
                                                        <!-- <ul class="tplist__content-info">
                                                            <li>Type: <?php //echo $product['type']; 
                                                                        ?></li>
                                                            <li>MFG: <?php //echo $product['mfg_date']; 
                                                                        ?></li>
                                                            <li>LIFE: <?php //echo $product['life']; 
                                                                        ?> days</li>
                                                        </ul> -->
                                                    </div>
                                                    <div class="tplist__price justify-content-end">
                                                        <!-- <h4 class="tplist__instock">Availability: <span>92 in stock</span> </h4> -->
                                                        <h3 class="tplist__count mb-15">$<?php echo $product['price']; ?></h3>
                                                        <!-- <form action="add_to_cart.php" method="POST" class="add-to-cart-form">
                                                            <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                                            <div class="product__details-quantity d-flex align-items-center">
                                                                <span class="cart-minus"><i class="far fa-minus"></i></span>
                                                                <input class="tp-cart-input" type="text" name="quantity" value="1">
                                                                <span class="cart-plus"><i class="far fa-plus"></i></span>
                                                            </div>
                                                            <button type="submit" class="tp-btn-2">Add to cart</button>
                                                        </form> -->
                                                        <form action="add_to_cart.php" method="POST" class="add-to-cart-form">
                                                            <div class="product__details-quantity quantity-ui">
                                                                <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                                                <div class="quantity-controls">
                                                                    <span class="cart-minus quantity-minus"><i class="fas fa-minus"></i></span>
                                                                    <input class="quantity-input" type="text" name="quantity" value="1">
                                                                    <span class="cart-plus quantity-plus"><i class="fas fa-plus"></i></span>
                                                                </div>
                                                                <button type="submit" class="tp-btn-2">Add to cart</button>
                                                            </div>
                                                        </form>
                                                        <!-- <a href="cart.php" class="tp-btn-2 mb-10">Add to cart</a> -->
                                                        <div class="tplist__shopping">
                                                            <!-- <a href="#"><i class="icon-heart icons"></i> wishlist</a> -->
                                                            <a href="add_to_wishlist.php?product_id=<?= $product['product_id'] ?>"
                                                                class="wishlist-btn <?= $functions->isInWishlist($_SESSION['user_id'], $product['product_id']) ? 'in-wishlist' : '' ?>">
                                                                <i class="icon-heart icons"></i> add to wishlist
                                                            </a>
                                                            <!-- <a href="#"><i class="icon-layers"></i>Compare</a> -->
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="basic-pagination text-center mt-35">
                                <nav>
                                    <ul>
                                        <li>
                                            <span class="current">1</span>
                                        </li>
                                        <li>
                                            <a href="blog.php">2</a>
                                        </li>
                                        <li>
                                            <a href="blog.php">3</a>
                                        </li>
                                        <li>
                                            <a href="blog.php">
                                                <i class="icon-chevrons-right"></i>
                                            </a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- shop-area-end -->
    </main>

    <!-- footer-area-start -->
    <?php include "includes/footer.php"; ?>
    <!-- footer-area-end -->

    <!-- JS here -->
    <!-- Include jQuery UI -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <!-- <script src="assets/js/jquery.js"></script> -->
    <script src="assets/js/waypoints.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/swiper-bundle.js"></script>
    <script src="assets/js/nice-select.js"></script>
    <script src="assets/js/slick.js"></script>
    <script src="assets/js/magnific-popup.js"></script>
    <script src="assets/js/counterup.js"></script>
    <script src="assets/js/wow.js"></script>
    <script src="assets/js/isotope-pkgd.js"></script>
    <script src="assets/js/imagesloaded-pkgd.js"></script>
    <script src="assets/js/countdown.js"></script>
    <script src="assets/js/ajax-form.js"></script>
    <script src="assets/js/jquery-ui.js"></script>
    <script src="assets/js/meanmenu.js"></script>
    <script src="assets/js/main.js"></script>
    <script>
        //shop quantity
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM fully loaded and parsed');
            document.querySelectorAll('.quantity-minus').forEach(function(minus) {
                minus.addEventListener('click', function(event) {
                    console.log('Minus button clicked');
                    event.preventDefault();
                    let input = this.closest('.quantity-controls').querySelector('.quantity-input');
                    let value = parseInt(input.value);
                    if (value > 1) {
                        input.value = value - 1;
                    }
                });
            });

            document.querySelectorAll('.quantity-plus').forEach(function(plus) {
                plus.addEventListener('click', function(event) {
                    console.log('Plus button clicked');
                    event.preventDefault();
                    let input = this.closest('.quantity-controls').querySelector('.quantity-input');
                    let value = parseInt(input.value);
                    input.value = value + 1;
                });
            });
        });

        ////////////////////////////////////////////////////
        // Price Filter Js

        $(document).ready(function() {
            // Initialize price range slider
            $("#slider-range").slider({
                range: true,
                min: 0,
                max: 1000,
                values: [0, 1000],
                slide: function(event, ui) {
                    $("#amount").val("$" + ui.values[0] + " - $" + ui.values[1]);
                }
            });
            $("#amount").val("$" + $("#slider-range").slider("values", 0) + " - $" + $("#slider-range").slider("values", 1));

            // Handle filter button click
            $("#apply-filters").on("click", function() {
                // Get selected categories
                let selectedCategories = [];
                $(".category-filter:checked").each(function() {
                    selectedCategories.push($(this).val());
                });

                // Get price range
                let minPrice = $("#slider-range").slider("values", 0);
                let maxPrice = $("#slider-range").slider("values", 1);

                // Send AJAX request to fetch filtered products
                $.ajax({
                    url: "filter_products.php",
                    type: "POST",
                    data: {
                        categories: selectedCategories,
                        min_price: minPrice,
                        max_price: maxPrice
                    },
                    success: function(response) {
                        // Update the product list
                        $(".tpproduct__shop-item").html(response);
                    }
                });
            });
        });
    </script>
</body>

</html>