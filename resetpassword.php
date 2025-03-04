<?php
session_start();
require_once "./includes/db/functions.php";
$functions = new Functions();

// Check if email exists in session
if (!isset($_SESSION['reset_email'])) {
   header("Location: forgotpassword.php");
   exit();
}

$errors = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   $email = $_SESSION['reset_email'];
   $new_password = $_POST['new_password'];
   $confirm_password = $_POST['confirm_password'];

   if ($new_password !== $confirm_password) {
      $errors[] = "Passwords do not match.";
   } elseif (strlen($new_password) < 8) {
      $errors[] = "Password must be at least 8 characters.";
   } else {
      // ✅ Get the database connection properly
      $conn = $functions->getDatabaseConnection();

      // ✅ Reset the password
      $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
      $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
      $stmt->bind_param("ss", $hashed_password, $email);

      if ($stmt->execute()) {
         unset($_SESSION['reset_email']); // Remove session data
         $success = "Password successfully reset. <a href='login.php'>Login now</a>";
      } else {
         $errors[] = "Failed to reset password.";
      }
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
                              <h4 class="tptrack__item-title">Reset Password</h4>
                              <p>Your personal data will be used to support your experience throughout this website, to manage access to your account.</p>
                           </div>
                        </div>


                        <?php if (!empty($errors)): ?>
                           <?php foreach ($errors as $error): ?>
                              <p style="color: red;"><?php echo $error; ?></p>
                           <?php endforeach; ?>
                        <?php endif; ?>

                        <?php if (!empty($success)): ?>
                           <p style="color: green;"><?php echo $success; ?></p>

                        <?php else: ?>
                           <form action="#" method="POST">
                              <div class="tptrack__id mb-10">
                                 <span><i class="fal fa-user"></i></span>
                                 <input type="password" name="new_password" placeholder="New Password" required><br>
                              </div>
                              <div class="tptrack__email mb-10">
                                 <span><i class="fal fa-key"></i></span>
                                 <input type="password" name="confirm_password" placeholder="Confirm Password" required><br>
                              </div>

                              <div class="tpsign__account mb-15 d-flex justify-content-center">
                                 <a href="register.php">Create a new Account</a>
                              </div>
                              <div class="tptrack__btn">
                                 <button class="tptrack__submition active">Submit<i class="fal fa-long-arrow-right"></i></button>
                              </div>
                           </form>
                        <?php endif; ?>
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