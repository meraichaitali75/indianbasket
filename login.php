<?php
session_start();
require_once "./includes/db/functions.php";

$user = new Functions();
$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
   $email = $_POST["userEmail"];
   $password = $_POST["userPassword"];

   $result = $user->login($email, $password);

   if ($result === "success") {
      $success = "Login successful! Redirecting Home...";
      echo "<script>setTimeout(() => { window.location.href = 'index.php'; }, 2000);</script>";
   } else {
      // Ensure $errors is always an array
      $errors = is_array($result) ? $result : [$result];
   }
}
?>
<!doctype html>
<html class="no-js" lang="zxx">

<head>
   <meta charset="utf-8">
   <meta http-equiv="x-ua-compatible" content="ie=edge">
   <title>Indian Basket | Login</title>
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
   <?php
   include "includes/header.php";
   ?>
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
                        <span>Sign in</span>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- breadcrumb-area-end -->

      <!-- track-area-start -->
      <section class="track-area pb-40">
         <div class="container">
            <div class="row justify-content-center">
               <div class="col-lg-6 col-sm-12 d-flex">
                  <div class="tptrack__product mb-40 d-flex">
                     <div class="tptrack__content grey-bg">
                        <div class="tptrack__item d-flex mb-20">
                           <div class="tptrack__item-icon">
                              <i class="fal fa-user-unlock"></i>
                           </div>
                           <div class="tptrack__item-content">
                              <h4 class="tptrack__item-title">Login Here</h4>
                              <p>Your personal data will be used to support your experience throughout this website, to manage access to your account.</p>
                           </div>
                        </div>
                        <!-- Display Success Message and Redirect After 5 Seconds -->
                        <!-- Success Message -->
                        <?php if (!empty($success)): ?>
                           <div class='alert alert-success'><?php echo $success; ?></div>
                        <?php endif; ?>

                        <!-- Error Messages -->
                        <?php if (!empty($errors)): ?>
                           <?php foreach ($errors as $error): ?>
                              <div class='alert alert-danger'><?php echo $error; ?></div>
                           <?php endforeach; ?>
                        <?php endif; ?>
                        <form action="#" method="POST">
                           <div class="tptrack__id mb-10">
                              <span><i class="fal fa-user"></i></span>
                              <input type="email" name="userEmail" placeholder="Username / email address">
                           </div>
                           <div class="tptrack__email mb-10">
                              <span><i class="fal fa-key"></i></span>
                              <input type="password" name="userPassword" placeholder="Password">
                           </div>
                           <div class="tpsign__remember d-flex align-items-center justify-content-between mb-15">
                              <div class="form-check">
                                 <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault2">
                                 <label class="form-check-label" for="flexCheckDefault2">Remember me</label>
                              </div>
                              <div class="tpsign__pass">
                                 <a href="forgotpassword.php">Forget Password</a>
                              </div>
                           </div>
                           <div class="tpsign__account mb-15 d-flex justify-content-center">
                              <a href="register.php">Create a new Account</a>
                           </div>
                           <div class="tptrack__btn">
                              <button class="tptrack__submition active">Login Now<i class="fal fa-long-arrow-right"></i></button>
                           </div>
                        </form>
                     </div>
                  </div>
               </div>
               <div class="col-lg-6 col-sm-12 d-flex">
                  <div class="tptrack__product mb-40 d-flex">
                     <div class="tptrack__content grey-bg login-member-box">
                        <h5>Benefits of becoming a member</h5>
                        <div class="login-member-row">
                           <img src="./assets/img/icon/history.png" alt="history icon">
                           <p>Easy access to order history, saved items and more.</p>
                        </div>
                        <div class="login-member-row">
                           <img src="./assets/img/icon/cart.png" alt="cart icon">
                           <p>Faster checkout with stored shipping and billing information.</p>
                        </div>
                        <div class="login-member-row">
                           <img src="./assets/img/icon/gift.png" alt="history icon">
                           <p>Exclusive offers, discounts, and shipping upgrades.</p>
                        </div>
                        <div class="login-member-row">
                           <img src="./assets/img/icon/star.png" alt="star icon">
                           <p>With a Indian Basket account, you can save time during checkout,
                              access your shopping bag from any device and view your order history.</p>
                        </div>
                        <div class="tptrack__btn">
                           <a href="register.php" class="tptrack__submition tpsign__reg">Sign Up<i class="fal fa-long-arrow-right"></i></a>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </section>
      <!-- track-area-end -->

      <!-- footer-area-start -->
      <?php
      include "includes/footer.php";
      ?>
      <!-- footer-area-end -->