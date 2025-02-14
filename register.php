<?php
session_start();
require_once "./includes/db/functions.php";

$user = new Functions();
$errors = [];
$success = ""; // Initialize success message variable

if ($_SERVER["REQUEST_METHOD"] == "POST") {
   $firstname = $_POST["userFirstName"];
   $lastname = $_POST["userLastName"];
   $email = $_POST["userEmail"];
   $password = $_POST["userPassword"];
   $confirm_password = $_POST["userConfrmPassword"];
   $gender = $_POST["userGender"];

   $existing_user = $user->getUserByEmail($email);

   if ($existing_user) {
      // If user already registered with social media, show message
      if ($existing_user['provider'] !== 'manual') {
         $errors[] = "You have already registered with " . ucfirst($existing_user['provider']) . ". Please log in using " . ucfirst($existing_user['provider']) . ".";
      } else {
         $errors[] = "This email is already registered. Please log in instead.";
      }
   } else {
      $result = $user->register($firstname, $lastname, $email, $password, $confirm_password, $gender);

      if ($result === "success") {
         $success = "Registration successful! You can now log in.";
      } else {
         $errors = is_array($result) ? $result : [$result];
      }
   }
}
?>

<!doctype html>
<html class="no-js" lang="zxx">

<head>
   <meta charset="utf-8">
   <meta http-equiv="x-ua-compatible" content="ie=edge">
   <title>Indian Basket | Sign Up</title>
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
                        <span>Sign up</span>
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
                              <i class="fal fa-lock"></i>
                           </div>
                           <div class="tptrack__item-content">
                              <h4 class="tptrack__item-title">Sign Up</h4>
                              <p>Your personal data will be used to support your experience throughout this website, to manage access to your account.</p>
                           </div>
                        </div>

                        <?php if (!empty($errors)): ?>
                           <?php foreach ($errors as $error): ?>
                              <div class='alert alert-danger'><?php echo $error; ?></div>
                           <?php endforeach; ?>
                        <?php endif; ?>

                        <!-- Display Success Message and Redirect After 5 Seconds -->
                        <?php if (!empty($success)): ?>
                           <div class='alert alert-success'><?php echo $success; ?></div>
                           <script>
                              setTimeout(function() {
                                 window.location.href = 'login.php'; // Redirect after 3 seconds
                              }, 3000);
                           </script>
                        <?php endif; ?>

                        <form method="POST">
                           <div class="row">
                              <div class="col-lg-6 col-sm-12">
                                 <div class="tptrack__id mb-10">
                                    <span><i class="fal fa-user"></i></span>
                                    <input type="text" name="userFirstName" placeholder="First Name" required>
                                 </div>
                              </div>
                              <div class="col-lg-6 col-sm-12">
                                 <div class="tptrack__email mb-10">
                                    <span><i class="fal fa-key"></i></span>
                                    <input type="text" name="userLastName" placeholder="Last Name" required>
                                 </div>
                              </div>
                              <div class="col-lg-6 col-sm-12">
                                 <div class="tptrack__id mb-10">
                                    <span><i class="fal fa-envelope"></i></span>
                                    <input type="email" name="userEmail" placeholder="Email address" required>
                                 </div>
                              </div>
                              <div class="col-lg-6 col-sm-12">
                                 <div class="tptrack__email mb-10">
                                    <span><i class="fal fa-key"></i></span>
                                    <input type="password" name="userPassword" placeholder="Password" required>
                                 </div>
                              </div>
                           </div>

                           <div class="tptrack__id mb-10 country-select">
                              <!-- <span><i class="fal fa-envelope"></i></span> -->
                              <select name="userGender" required>
                                 <option value="" selected="selected">Select your Gender</option>
                                 <option value="Male">Male</option>
                                 <option value="Female">Female</option>
                                 <option value="Other">Other</option>
                              </select>
                           </div>

                           <div class="tpsign__account mb-15 text-center">
                              <a href="login.php">Already Have Account?</a>
                           </div>
                           <div class="tptrack__btn">
                              <button type="submit" class="tptrack__submition tpsign__reg">Register Now<i class="fal fa-long-arrow-right"></i></button>
                           </div>
                        </form>

                         <!-- Social Media Login Buttons -->
                         <div class="social-login mt-3 text-center">
                           <p>Or login with:</p>
                           <a href="google_oauth.php" class="btn btn-danger">Google</a>
                           <a href="facebook_oauth.php" class="btn btn-primary">Facebook</a>
                           <a href="x_oauth.php" class="btn btn-dark">X (Twitter)</a>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="col-lg-6 col-sm-12  d-flex">
                  <div class="tptrack__product mb-40 d-flex">
                     <div class="tptrack__content grey-bg login-member-box ">
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
                           <a href="login.php" class="tptrack__submition tpsign__reg">Sign In<i class="fal fa-long-arrow-right"></i></a>
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