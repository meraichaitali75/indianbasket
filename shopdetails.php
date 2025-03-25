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

// Check if product ID is set in the URL
if (isset($_GET['product_id'])) {
    $product_id = intval($_GET['product_id']);
    $product = $functions->getProductById($product_id);

    if (!$product) {
        // Redirect to a 404 page or show an error message
        header("Location: 404.php");
        exit();
    }
} else {
    // Redirect to the shop page if no product ID is provided
    header("Location: shop.php");
    exit();
}

// Check if wishlist action is requested
if (isset($_GET['wishlist_action'])) {
    $action = $_GET['wishlist_action'];
    if ($action === 'add') {
        $result = $functions->addToWishlist($_SESSION['user_id'], $product_id);
        $_SESSION['wishlist_message'] = $result['message'];
    } elseif ($action === 'remove') {
        $result = $functions->removeFromWishlist($_SESSION['user_id'], $product_id);
        $_SESSION['wishlist_message'] = $result['message'];
    }
    header("Location: shopdetails.php?product_id=" . $product_id);
    exit();
}

// Check if product is in wishlist
$in_wishlist = false;
if (isset($_SESSION['user_id'])) {
    $in_wishlist = $functions->isInWishlist($_SESSION['user_id'], $product_id);
}

// Fetch recent products
$recent_products = $functions->getRecentProducts(3); // Fetch the latest 3 products

// Fetch product images
$product_images = $functions->getProductImages($product_id);
?>

<!doctype html>
<html class="no-js" lang="zxx">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Indianbasket | <?php echo htmlspecialchars($product['name']); ?></title>
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

    <style>
        .tpdescription__video-wrapper {
            position: relative;
            height: 300px;
            overflow: hidden;
        }

        .tpdescription__video-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .tpvideo__video-btn {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1;
        }

        .wishlist-btn {
            color: #333;
            text-decoration: none;
        }
        
        .wishlist-btn:hover {
            color: #e74c3c;
        }
        
        .wishlist-btn.active {
            color: #e74c3c;
        }
        
        .wishlist-btn i {
            margin-right: 5px;
        }

        @media (max-width: 768px) {
            .tpdescription__video-wrapper {
                height: 200px;
            }
        }

        @media (max-width: 480px) {
            .tpdescription__video-wrapper {
                height: 150px;
            }
        }
    </style>
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
                                <span class="tp-breadcrumb__active"><a href="shop.php">Shop</a></span>
                                <span class="dvdr">/</span>
                                <span><?php echo htmlspecialchars($product['name']); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- breadcrumb-area-end -->

        <!-- shop-details-area-start -->
        <section class="shopdetails-area grey-bg pb-50">
            <div class="container">
                <?php if (isset($_SESSION['wishlist_message'])): ?>
                    <div class="alert alert-info mb-4">
                        <?php echo $_SESSION['wishlist_message']; ?>
                        <?php unset($_SESSION['wishlist_message']); ?>
                    </div>
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-lg-10 col-md-12">
                        <div class="tpdetails__area mr-60 pb-30">
                            <div class="tpdetails__product mb-30">
                                <div class="tpdetails__title-box">
                                    <h3 class="tpdetails__title"><?php echo htmlspecialchars($product['name']); ?></h3>
                                    <ul class="tpdetails__brand">
                                        <li> Brands: <a href="#">INDIAN BASKET</a> </li>
                                        <li>
                                            <i class="icon-star_outline1"></i>
                                            <i class="icon-star_outline1"></i>
                                            <i class="icon-star_outline1"></i>
                                            <i class="icon-star_outline1"></i>
                                            <i class="icon-star_outline1"></i>
                                            <b>02 Reviews</b>
                                        </li>
                                        <li>
                                            SKU: <span><?php echo htmlspecialchars($product['product_id']); ?></span>
                                        </li>
                                    </ul>
                                </div>
                                <div class="tpdetails__box">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="tpproduct-details__nab">
                                                <div class="tab-content" id="nav-tabContents">
                                                    <?php foreach ($product_images as $index => $image) { ?>
                                                        <div class="tab-pane fade <?php echo $index === 0 ? 'show active' : ''; ?> w-img" id="nav-image-<?php echo $index; ?>" role="tabpanel" aria-labelledby="nav-image-<?php echo $index; ?>-tab" tabindex="0">
                                                            <img src="./uploads/products/product-images/<?php echo htmlspecialchars($image); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                                            <div class="tpproduct__info bage">
                                                                <span class="tpproduct__info-hot bage__hot">HOT</span>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                                <nav>
                                                    <div class="nav nav-tabs justify-content-center" id="nav-tab" role="tablist">
                                                        <?php foreach ($product_images as $index => $image) { ?>
                                                            <button class="nav-link <?php echo $index === 0 ? 'active' : ''; ?>" id="nav-image-<?php echo $index; ?>-tab" data-bs-toggle="tab" data-bs-target="#nav-image-<?php echo $index; ?>" type="button" role="tab" aria-controls="nav-image-<?php echo $index; ?>" aria-selected="<?php echo $index === 0 ? 'true' : 'false'; ?>">
                                                                <img src="./uploads/products/product-images/<?php echo htmlspecialchars($image); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                                            </button>
                                                        <?php } ?>
                                                    </div>
                                                </nav>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="product__details">
                                                <div class="product__details-price-box">
                                                    <h5 class="product__details-price">$<?php echo htmlspecialchars($product['price']); ?></h5>
                                                    <ul class="product__details-info-list">
                                                        <li><?php echo htmlspecialchars($product['description']); ?></li>
                                                    </ul>
                                                </div>
                                                <div class="product__details-cart">
                                                    <div class="product__details-quantity d-flex align-items-center mb-15">
                                                        <b>Qty:</b>
                                                        <div class="product__details-count mr-10">
                                                            <span class="cart-minus"><i class="fas fa-minus"></i></span>
                                                            <input class="tp-cart-input" type="text" name="quantity" value="1">
                                                            <span class="cart-plus"><i class="fas fa-plus"></i></span>
                                                        </div>
                                                        <div class="product__details-btn">
                                                            <form action="add_to_cart.php" method="POST" class="add-to-cart-form">
                                                                <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                                                <input type="hidden" name="quantity" value="1" class="quantity-input">
                                                                <button type="submit" class="tp-btn-2">Add to cart</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                    <ul class="product__details-check">
                                                        <li>
                                                            <a href="?product_id=<?php echo $product_id; ?>&wishlist_action=<?php echo $in_wishlist ? 'remove' : 'add'; ?>" 
                                                               class="wishlist-btn <?php echo $in_wishlist ? 'active' : ''; ?>">
                                                                <i class="icon-heart icons"></i> 
                                                                <?php echo $in_wishlist ? 'Remove from wishlist' : 'Add to wishlist'; ?>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div class="product__details-stock mb-25">
                                                    <ul>
                                                        <li>Availability: <i><?php echo htmlspecialchars($product['stock']); ?> Instock</i></li>
                                                        <li>Categories: <span><?php echo htmlspecialchars($product['category_name']); ?></span></li>
                                                        <li>Tags: <span>Chicken, Natural, Organic</span></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tpdescription__box">
                                <div class="tpdescription__box-center d-flex align-items-center justify-content-center">
                                    <nav>
                                        <div class="nav nav-tabs" role="tablist">
                                            <button class="nav-link active" id="nav-description-tab" data-bs-toggle="tab"
                                                data-bs-target="#nav-description" type="button" role="tab"
                                                aria-controls="nav-description" aria-selected="true">Product Description</button>

                                            <button class="nav-link" id="nav-review-tab" data-bs-toggle="tab"
                                                data-bs-target="#nav-review" type="button" role="tab" aria-controls="nav-review"
                                                aria-selected="false">Reviews (1)</button>
                                        </div>
                                    </nav>
                                </div>
                                <div class="tab-content" id="nav-tabContent">
                                    <div class="tab-pane fade show active" id="nav-description" role="tabpanel" aria-labelledby="nav-description-tab" tabindex="0">
                                        <div class="tpdescription__content">
                                            <p><?php echo htmlspecialchars($product['description']); ?></p>
                                        </div>
                                        <div class="row tpdescription__product-wrapper mt-30 mb-30 d-flex justify-content-between align-items-center">
                                            <div class="col-lg-8 col-md-12">
                                                <div class="tpdescription__product-info">
                                                    <h5 class="tpdescription__product-title">PRODUCT DETAILS</h5>
                                                    <ul class="tpdescription__product-info">
                                                        <li>Material: <?php echo htmlspecialchars($product['material']); ?></li>
                                                        <li>Dimensions and Weight: <?php echo htmlspecialchars($product['dimensions']); ?></li>
                                                        <li>Length: <?php echo htmlspecialchars($product['length']); ?></li>
                                                        <li>Depth: <?php echo htmlspecialchars($product['depth']); ?></li>
                                                    </ul>
                                                    <p><?php echo htmlspecialchars($product['additional_details']); ?></p>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-12">
                                                <div class="tpdescription__product-thumb">
                                                    <a href="#"><img src="./uploads/products/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>"></a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tpdescription__video">
                                            <h5 class="tpdescription__product-title">PRODUCT DETAILS</h5>
                                            <p>Additional details about the product.</p>
                                            <div class="tpdescription__video-wrapper p-relative mt-30 mb-35 w-img">
                                                <img src="assets/img/product/product-video1.jpg" alt="Product Video">
                                                <div class="tpvideo__video-btn">
                                                    <a class="tpvideo__video-icon popup-video" href="https://www.youtube.com/watch?v=rLrV5Tel7zw">
                                                        <i>
                                                            <svg width="20" height="22" viewBox="0 0 20 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M15.6499 6.58886L15.651 6.58953C17.8499 7.85553 18.7829 9.42511 18.7829 10.8432C18.7829 12.2613 17.8499 13.8308 15.651 15.0968L15.6499 15.0975L12.0218 17.195L8.3948 19.2919C8.3946 19.292 8.3944 19.2921 8.3942 19.2922C6.19546 20.558 4.36817 20.5794 3.13833 19.8697C1.9087 19.1602 1.01562 17.5694 1.01562 15.0382V10.8432V6.64818C1.01562 4.10132 1.90954 2.51221 3.13721 1.80666C4.36609 1.1004 6.1936 1.12735 8.3942 2.39416C8.3944 2.39428 8.3946 2.3944 8.3948 2.39451L12.0218 4.49135L15.6499 6.58886Z" stroke="white" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path>
                                                            </svg>
                                                        </i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="nav-review" role="tabpanel" aria-labelledby="nav-review-tab"
                                        tabindex="0">
                                        <div class="tpreview__wrapper">
                                            <h4 class="tpreview__wrapper-title">1 review for Cheap and delicious fresh chicken</h4>
                                            <div class="tpreview__comment">
                                                <div class="tpreview__comment-img mr-20">
                                                    <img src="assets/img/testimonial/test-avata-1.png" alt="">
                                                </div>
                                                <div class="tpreview__comment-text">
                                                    <div
                                                        class="tpreview__comment-autor-info d-flex align-items-center justify-content-between">
                                                        <div class="tpreview__comment-author">
                                                            <span>admin</span>
                                                        </div>
                                                        <div class="tpreview__comment-star">
                                                            <i class="icon-star_outline1"></i>
                                                            <i class="icon-star_outline1"></i>
                                                            <i class="icon-star_outline1"></i>
                                                            <i class="icon-star_outline1"></i>
                                                            <i class="icon-star_outline1"></i>
                                                        </div>
                                                    </div>
                                                    <span class="date mb-20">--April 9, 2022: </span>
                                                    <p>very good</p>
                                                </div>
                                            </div>
                                            <div class="tpreview__form">
                                                <h4 class="tpreview__form-title mb-25">Add a review </h4>
                                                <form action="#">
                                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            <div class="tpreview__input mb-30">
                                                                <input type="text" placeholder="Name">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div class="tpreview__input mb-30">
                                                                <input type="email" placeholder="Email">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12">
                                                            <div class="tpreview__star mb-20">
                                                                <h4 class="title">Your Rating</h4>
                                                                <div class="tpreview__star-icon">
                                                                    <a href="#"><i class="icon-star_outline1"></i></a>
                                                                    <a href="#"><i class="icon-star_outline1"></i></a>
                                                                    <a href="#"><i class="icon-star_outline1"></i></a>
                                                                    <a href="#"><i class="icon-star_outline1"></i></a>
                                                                    <a href="#"><i class="icon-star_outline1"></i></a>
                                                                </div>
                                                            </div>
                                                            <div class="tpreview__input mb-30">
                                                                <textarea name="text" placeholder="Message"></textarea>
                                                                <div class="tpreview__submit mt-30">
                                                                    <button class="tp-btn">Submit</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-12">
                        <div class="tpsidebar pb-30">
                            <div class="tpsidebar__warning mb-30">
                                <ul>
                                    <li>
                                        <div class="tpsidebar__warning-item">
                                            <div class="tpsidebar__warning-icon">
                                                <i class="icon-package"></i>
                                            </div>
                                            <div class="tpsidebar__warning-text">
                                                <p>Free shipping apply to all <br> orders over $90</p>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="tpsidebar__warning-item">
                                            <div class="tpsidebar__warning-icon">
                                                <i class="icon-shield"></i>
                                            </div>
                                            <div class="tpsidebar__warning-text">
                                                <p>Guaranteed 100% Organic <br> from nature farms</p>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="tpsidebar__warning-item">
                                            <div class="tpsidebar__warning-icon">
                                                <i class="icon-package"></i>
                                            </div>
                                            <div class="tpsidebar__warning-text">
                                                <p>60 days returns if you change <br> your mind</p>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div class="tpsidebar__banner mb-30">
                                <img src="assets/img/shape/sidebar-product-1.png" alt="">
                            </div>

                            <div class="tpsidebar__product">
                                <h4 class="tpsidebar__title mb-15">Recent Products</h4>
                                <?php foreach ($recent_products as $recent_product) { 
                                    $recent_in_wishlist = $functions->isInWishlist($_SESSION['user_id'], $recent_product['product_id']);
                                ?>
                                    <div class="tpsidebar__product-item">
                                        <div class="tpsidebar__product-thumb p-relative">
                                            <img src="assets/img/product/<?php echo htmlspecialchars($recent_product['image']); ?>" alt="<?php echo htmlspecialchars($recent_product['name']); ?>">
                                            <div class="tpsidebar__info bage">
                                                <span class="tpproduct__info-hot bage__hot">HOT</span>
                                            </div>
                                        </div>
                                        <div class="tpsidebar__product-content">
                                            <span class="tpproduct__product-category">
                                                <a href="shopdetails.php?product_id=<?php echo htmlspecialchars($recent_product['product_id']); ?>"><?php echo htmlspecialchars($recent_product['category_name']); ?></a>
                                            </span>
                                            <h4 class="tpsidebar__product-title">
                                                <a href="shopdetails.php?product_id=<?php echo htmlspecialchars($recent_product['product_id']); ?>"><?php echo htmlspecialchars($recent_product['name']); ?></a>
                                            </h4>
                                            <div class="tpproduct__rating mb-5">
                                                <a href="#"><i class="icon-star_outline1"></i></a>
                                                <a href="#"><i class="icon-star_outline1"></i></a>
                                                <a href="#"><i class="icon-star_outline1"></i></a>
                                                <a href="#"><i class="icon-star_outline1"></i></a>
                                                <a href="#"><i class="icon-star_outline1"></i></a>
                                            </div>
                                            <div class="tpproduct__price">
                                                <span>$<?php echo htmlspecialchars($recent_product['price']); ?></span>
                                                <del>$19.00</del>
                                            </div>
                                            <div class="tpsidebar__product-wishlist">
                                                <a href="?product_id=<?php echo $recent_product['product_id']; ?>&wishlist_action=<?php echo $recent_in_wishlist ? 'remove' : 'add'; ?>" 
                                                   class="wishlist-btn <?php echo $recent_in_wishlist ? 'active' : ''; ?>">
                                                    <i class="icon-heart icons"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- shop-details-area-end -->

        <!-- product-area-start -->
        <section class="product-area whight-product pt-75 pb-80">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <h5 class="tpdescription__product-title mb-20">Related Products</h5>
                    </div>
                </div>
                <div class="tpproduct__arrow double-product p-relative">
                    <div class="swiper-container tpproduct-active tpslider-bottom p-relative">
                        <div class="swiper-wrapper">
                            <?php
                            $related_products = $functions->getRelatedProducts($product['category_id'], $product['product_id']);
                            foreach ($related_products as $related_product) {
                                $related_in_wishlist = $functions->isInWishlist($_SESSION['user_id'], $related_product['product_id']);
                            ?>
                                <div class="swiper-slide">
                                    <div class="tpproduct p-relative">
                                        <div class="tpproduct__thumb p-relative text-center">
                                            <a href="#"><img src="./uploads/products/<?php echo htmlspecialchars($related_product['image']); ?>" alt="<?php echo htmlspecialchars($related_product['name']); ?>"></a>
                                            <a class="tpproduct__thumb-img" href="shopdetails.php?product_id=<?php echo htmlspecialchars($related_product['product_id']); ?>"><img src="./uploads/products/<?php echo htmlspecialchars($related_product['image']); ?>" alt="<?php echo htmlspecialchars($related_product['name']); ?>"></a>
                                            <div class="tpproduct__info bage">
                                                <span class="tpproduct__info-discount bage__discount">-50%</span>
                                                <span class="tpproduct__info-hot bage__hot">HOT</span>
                                            </div>
                                            <div class="tpproduct__shopping">
                                                <a href="?product_id=<?php echo $related_product['product_id']; ?>&wishlist_action=<?php echo $related_in_wishlist ? 'remove' : 'add'; ?>" 
                                                   class="tpproduct__shopping-wishlist <?php echo $related_in_wishlist ? 'active' : ''; ?>">
                                                    <i class="icon-heart icons"></i>
                                                </a>
                                                <a class="tpproduct__shopping-wishlist" href="#"><i class="icon-layers"></i></a>
                                                <a class="tpproduct__shopping-cart" href="#"><i class="icon-eye"></i></a>
                                            </div>
                                        </div>
                                        <div class="tpproduct__content">
                                            <span class="tpproduct__content-weight">
                                                <a href="shopdetails.php?product_id=<?php echo htmlspecialchars($related_product['product_id']); ?>"><?php echo htmlspecialchars($related_product['category_name']); ?></a>
                                            </span>
                                            <h4 class="tpproduct__title">
                                                <a href="shopdetails.php?product_id=<?php echo htmlspecialchars($related_product['product_id']); ?>"><?php echo htmlspecialchars($related_product['name']); ?></a>
                                            </h4>
                                            <div class="tpproduct__rating mb-5">
                                                <a href="#"><i class="icon-star_outline1"></i></a>
                                                <a href="#"><i class="icon-star_outline1"></i></a>
                                                <a href="#"><i class="icon-star_outline1"></i></a>
                                                <a href="#"><i class="icon-star_outline1"></i></a>
                                                <a href="#"><i class="icon-star_outline1"></i></a>
                                            </div>
                                            <div class="tpproduct__price">
                                                <span>$<?php echo htmlspecialchars($related_product['price']); ?></span>
                                                <del>$19.00</del>
                                            </div>
                                        </div>
                                        <div class="tpproduct__hover-text">
                                            <div class="tpproduct__hover-btn d-flex justify-content-center mb-10">
                                                <form action="add_to_cart.php" method="POST" class="add-to-cart-form">
                                                    <input type="hidden" name="product_id" value="<?php echo $related_product['product_id']; ?>">
                                                    <div class="product__details-quantity d-flex align-items-center">
                                                        <span class="cart-minus"><i class="far fa-minus"></i></span>
                                                        <input class="tp-cart-input" type="text" name="quantity" value="1">
                                                        <span class="cart-plus"><i class="far fa-plus"></i></span>
                                                    </div>
                                                    <button type="submit" class="tp-btn-2">Add to cart</button>
                                                </form>
                                            </div>
                                            <div class="tpproduct__descrip">
                                                <?php
                                                $mfg_date = !empty($related_product['mfg_date']) ? new DateTime($related_product['mfg_date']) : null;
                                                ?>
                                                <ul>
                                                    <li>Type: <?php echo !empty($related_product['type']) ? htmlspecialchars($related_product['type']) : 'Not specified'; ?></li>
                                                    <li>MFG: <?php echo $mfg_date ? $mfg_date->format('F j, Y') : 'Not specified'; ?></li>
                                                    <li>LIFE: <?php echo !empty($related_product['life_days']) ? htmlspecialchars($related_product['life_days']) . ' days' : 'Not specified'; ?></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- product-area-end -->

    </main>

    <!-- footer-area-start -->
    <?php include "includes/footer.php"; ?>
    <!-- footer-area-end -->

    <!-- JS here -->
    <script src="assets/js/jquery.js"></script>
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
        document.addEventListener('DOMContentLoaded', function() {
            // Handle minus button click
            document.querySelectorAll('.cart-minus').forEach(function(minus) {
                minus.addEventListener('click', function(event) {
                    event.preventDefault(); // Prevent default form submission
                    let input = this.closest('.product__details-count').querySelector('.tp-cart-input');
                    let value = parseInt(input.value);
                    if (value > 1) {
                        input.value = value - 1;
                    }
                    // Update the hidden quantity input in the form
                    let form = this.closest('.product__details-quantity').querySelector('.add-to-cart-form');
                    form.querySelector('.quantity-input').value = input.value;
                });
            });

            // Handle plus button click
            document.querySelectorAll('.cart-plus').forEach(function(plus) {
                plus.addEventListener('click', function(event) {
                    event.preventDefault(); // Prevent default form submission
                    let input = this.closest('.product__details-count').querySelector('.tp-cart-input');
                    let value = parseInt(input.value);
                    input.value = value + 1;
                    // Update the hidden quantity input in the form
                    let form = this.closest('.product__details-quantity').querySelector('.add-to-cart-form');
                    form.querySelector('.quantity-input').value = input.value;
                });
            });
        });
    </script>
</body>

</html>