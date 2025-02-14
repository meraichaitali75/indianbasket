<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);


require_once "./includes/db/functions.php";
session_start();

$functions = new Functions();
$errors = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = trim($_POST['email']);
    $result = $functions->forgotPassword($email);

    if ($result === "success") {
        $_SESSION['reset_email'] = $email; // Store email in session
        header("Location: resetpassword.php"); // Redirect to reset password page
        exit();
    } else {
        $errors[] = $result;
    }
}

?>

<!doctype html>
<html class="no-js" lang="zxx">

<head>
   <meta charset="utf-8">
   <meta http-equiv="x-ua-compatible" content="ie=edge">
   <title>Indian Basket | Forgot Password</title>
   <meta name="viewport" content="width=device-width, initial-scale=1">

   <link rel="shortcut icon" type="image/x-icon" href="assets/img/logo/favicon.png">
   <link rel="stylesheet" href="assets/css/bootstrap.min.css">
   <link rel="stylesheet" href="assets/css/main.css">
</head>

<body>

   <!-- Scroll-top -->
   <button class="scroll-top scroll-to-target" data-target="html">
      <i class="icon-chevrons-up"></i>
   </button>

   <!-- Header -->
   <?php include "includes/header.php"; ?>

   <main>

      <!-- Breadcrumb -->
      <div class="breadcrumb__area pt-5 pb-5">
         <div class="container">
            <div class="row">
               <div class="col-lg-12">
                  <div class="tp-breadcrumb__content">
                     <div class="tp-breadcrumb__list">
                        <span class="tp-breadcrumb__active"><a href="index.php">Home</a></span>
                        <span class="dvdr">/</span>
                        <span>Forgot Password</span>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>

      <!-- Forgot Password Form -->
      <section class="track-area pb-40">
         <div class="container">
            <div class="row justify-content-center">
               <div class="col-lg-6 col-sm-12">
                  <div class="tptrack__product mb-40">
                     <div class="tptrack__content grey-bg">
                        <div class="tptrack__item d-flex mb-20">
                           <div class="tptrack__item-icon">
                              <i class="fal fa-user-unlock"></i>
                           </div>
                           <div class="tptrack__item-content">
                              <h4 class="tptrack__item-title">Forgot Password</h4>
                              <p>Enter your registered email to receive a password reset link.</p>
                           </div>
                        </div>

                        <!-- Display Messages -->
                        <?php if (!empty($errors)): ?>
                           <?php foreach ($errors as $error): ?>
                              <div class='alert alert-danger'><?php echo $error; ?></div>
                           <?php endforeach; ?>
                        <?php endif; ?>

                        <?php if (!empty($success)): ?>
                           <div class='alert alert-success'><?php echo $success; ?></div>
                        <?php endif; ?>

                        <!-- Reset Password Form -->
                        <form method="POST">
                           <div class="tptrack__id mb-10">
                              <span><i class="fal fa-envelope"></i></span>
                              <input type="email" name="email" placeholder="Enter Your Email" required>
                           </div>
                           <div class="tptrack__btn">
                              <button type="submit" class="tptrack__submition">Send Reset Link<i class="fal fa-long-arrow-right"></i></button>
                           </div>
                        </form>

                        <div class="tpsign__pass text-center mt-3">
                           <a href="login.php">Back to Login</a>
                        </div>
                     </div>
                  </div>
               </div>

            </div>
         </div>
      </section>

   </main>

   <!-- Footer -->
   <?php include "includes/footer.php"; ?>

</body>
</html>
