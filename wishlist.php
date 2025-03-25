<?php
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
   session_start();
}

require_once "./includes/db/functions.php";

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
   header("Location: login.php");
   exit();
}

$functions = new Functions();
$wishlistItems = $functions->getWishlistItems($_SESSION["user_id"]);
?>

<!doctype html>
<html class="no-js" lang="zxx">

<head>
   <meta charset="utf-8">
   <meta http-equiv="x-ua-compatible" content="ie=edge">
   <title>Indianbasket | Wishlist</title>
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
      <div class="breadcrumb__area pt-5 pb-5">
         <div class="container">
            <div class="row">
               <div class="col-lg-12">
                  <div class="tp-breadcrumb__content">
                     <div class="tp-breadcrumb__list">
                        <span class="tp-breadcrumb__active"><a href="index.php">Home</a></span>
                        <span class="dvdr">/</span>
                        <span>Wishlist</span>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- breadcrumb-area-end -->

      <!-- wishlist-area-start -->
      <div class="cart-area pb-80">
         <div class="container">
            <div class="row">
               <div class="col-12">
                  <?php if (count($wishlistItems) > 0): ?>
                     <form action="#">
                        <div class="table-content table-responsive">
                           <table class="table">
                              <thead>
                                 <tr>
                                    <th class="product-thumbnail">Images</th>
                                    <th class="cart-product-name">Product</th>
                                    <th class="product-price">Unit Price</th>
                                    <th class="product-quantity">Quantity</th>
                                    <th class="product-subtotal">Total</th>
                                    <th class="product-add-to-cart">Add To Cart</th>
                                    <th class="product-remove">Remove</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 <?php foreach ($wishlistItems as $item): ?>
                                    <tr>
                                       <td class="product-thumbnail">
                                          <a href="shopdetails.php?product_id=<?php echo $item['product_id']; ?>">
                                             <img src="./uploads/products/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>">
                                          </a>
                                       </td>
                                       <td class="product-name">
                                          <a href="shopdetails.php?product_id=<?php echo $item['product_id']; ?>"><?php echo $item['name']; ?></a>
                                       </td>
                                       <td class="product-price">
                                          <span class="amount">$<?php echo $item['price']; ?></span>
                                       </td>
                                       <td class="product-quantity">
                                          <span class="cart-minus">-</span>
                                          <input class="cart-input" type="text" value="1">
                                          <span class="cart-plus">+</span>
                                       </td>
                                       <td class="product-subtotal">
                                          <span class="amount">$<?php echo $item['price']; ?></span>
                                       </td>
                                       <td class="product-add-to-cart">
                                          <a href="add_to_cart.php?product_id=<?php echo $item['product_id']; ?>&quantity=1" class="tp-btn tp-color-btn tp-wish-cart banner-animation">Add To Cart</a>
                                       </td>
                                       <td class="product-remove">
                                          <a href="remove_from_wishlist.php?product_id=<?php echo $item['product_id']; ?>"><i class="fa fa-times"></i></a>
                                       </td>
                                    </tr>
                                 <?php endforeach; ?>
                              </tbody>
                           </table>
                        </div>
                     </form>
                  <?php else: ?>
                     <div class="text-center py-5">
                        <h4>Your wishlist is empty</h4>
                        <p>You don't have any products in your wishlist yet.</p>
                        <a href="shop.php" class="tp-btn tp-color-btn">Continue Shopping</a>
                     </div>
                  <?php endif; ?>
               </div>
            </div>
         </div>
      </div>
      <!-- wishlist-area-end-->

     
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
</body>

</html>