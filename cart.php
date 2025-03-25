<?php
session_start(); // Start the session

// Include the functions.php file
require_once __DIR__ . "/includes/db/functions.php";

// Create an instance of the Functions class
$functions = new Functions();

// Get cart items
$cartItems = $functions->getCartItems();

// Calculate subtotal
$subtotal = 0;
foreach ($cartItems as $item) {
   $subtotal += $item['price'] * $item['quantity'];
}
?>

<!doctype html>
<html class="no-js" lang="zxx">

<head>
   <meta charset="utf-8">
   <meta http-equiv="x-ua-compatible" content="ie=edge">
   <title>Indianbasket | Cart</title>
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
                        <span>Cart</span>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- breadcrumb-area-end -->

      <!-- cart area -->
      <section class="cart-area pb-80">
         <div class="container">
            <div class="row">
               <div class="col-12">
                  <!-- Cart Table -->
                  <div class="table-content table-responsive">
                     <table class="table">
                        <thead>
                           <tr>
                              <th class="product-thumbnail">Images</th>
                              <th class="cart-product-name">Product</th>
                              <th class="product-price">Unit Price</th>
                              <th class="product-quantity">Quantity</th>
                              <th class="product-subtotal">Total</th>
                              <th class="product-remove">Remove</th>
                           </tr>
                        </thead>
                        <tbody>
                           <?php if (!empty($cartItems)): ?>
                              <?php foreach ($cartItems as $item): ?>
                                 <tr>
                                    <td class="product-thumbnail">
                                       <a href="shop-details.php?product_id=<?php echo $item['product_id']; ?>">
                                          <img src="./uploads/products/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                       </a>
                                    </td>
                                    <td class="product-name">
                                       <a href="shop-details.php?product_id=<?php echo $item['product_id']; ?>"><?php echo htmlspecialchars($item['name']); ?></a>
                                    </td>
                                    <td class="product-price">
                                       <span class="amount">$<?php echo htmlspecialchars($item['price']); ?></span>
                                    </td>
                                    <td class="product-quantity">
                                       <form action="update_cart.php" method="POST" class="d-flex align-items-center">
                                          <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                          <span class="cart-minus">-</span>
                                          <input class="cart-input" type="text" name="quantity" value="<?php echo $item['quantity']; ?>">
                                          <span class="cart-plus">+</span>
                                          <button type="submit" class="tp-btn tp-color-btn banner-animation">Update</button>
                                       </form>
                                    </td>
                                    <td class="product-subtotal">
                                       <span class="amount">$<?php echo htmlspecialchars($item['price'] * $item['quantity']); ?></span>
                                    </td>
                                    <td class="product-remove">
                                       <a href="remove_from_cart.php?product_id=<?php echo $item['product_id']; ?>"><i class="fa fa-times"></i></a>
                                    </td>
                                 </tr>
                              <?php endforeach; ?>
                           <?php else: ?>
                              <tr>
                                 <td colspan="6" class="text-center">Your cart is empty.</td>
                              </tr>
                           <?php endif; ?>
                        </tbody>
                     </table>
                  </div>
                  <div class="row">
                     <div class="col-12">
                        <div class="coupon-all">
                           <div class="coupon">
                              <input id="coupon_code" class="input-text" name="coupon_code" value="" placeholder="Coupon code" type="text">
                              <button class="tp-btn tp-color-btn banner-animation" name="apply_coupon" type="submit">Apply Coupon</button>
                           </div>
                           <div class="coupon2">
                              <button class="tp-btn tp-color-btn banner-animation" name="update_cart" type="submit">Update cart</button>
                           </div>
                        </div>
                     </div>
                  </div>
                  <!-- Cart Totals -->
                  <div class="row justify-content-end">
                     <div class="col-md-5">
                        <div class="cart-page-total">
                           <h2>Cart totals</h2>
                           <ul class="mb-20">
                              <li>Subtotal <span>$<?php echo isset($subtotal) ? $subtotal : 0; ?></span></li>
                              <li>Total <span>$<?php echo isset($subtotal) ? $subtotal : 0; ?></span></li>
                           </ul>
                           <a href="checkout.php" class="tp-btn tp-color-btn banner-animation">Proceed to Checkout</a>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </section>
      <!-- cart area end-->
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
      //shop quantity
      document.addEventListener('DOMContentLoaded', function () {
         // Handle minus button click
         document.querySelectorAll('.cart-minus').forEach(function (minus) {
            minus.addEventListener('click', function (event) {
               event.preventDefault(); // Prevent default form submission
               let input = this.closest('form').querySelector('.cart-input');
               let value = parseInt(input.value);
               if (value > 1) {
                  input.value = value - 1;
               }
            });
         });

         // Handle plus button click
         document.querySelectorAll('.cart-plus').forEach(function (plus) {
            plus.addEventListener('click', function (event) {
               event.preventDefault(); // Prevent default form submission
               let input = this.closest('form').querySelector('.cart-input');
               let value = parseInt(input.value);
               input.value = value + 1;
            });
         });
      });
   </script>
</body>

</html>