<?php
session_start();
require_once __DIR__ . '/../includes/db/functions.php'; // Correct path

$admin = new Functions();
$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
   $email = trim($_POST["adminEmail"]);
   $password = trim($_POST["adminPassword"]);

   $result = $admin->login($email, $password);

   if ($result === "admin") {  // ✅ Admin login successful
      $_SESSION["admin_email"] = $email; // Store admin session
      $success = "Admin login successful! Redirecting to Dashboard...";
      echo "<script>setTimeout(() => { window.location.href = 'dashboard.php'; }, 2000);</script>";
   } else {
      // ✅ Store errors in an array
      $errors = is_array($result) ? $result : [$result];
   }
}
?>

<!doctype html>
<html lang="zxx">
<head>
   <meta charset="utf-8">
   <meta http-equiv="x-ua-compatible" content="ie=edge">
   <title>Admin Login | Indian Basket</title>
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <link rel="shortcut icon" type="image/x-icon" href="../assets/img/logo/favicon.png">
   <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
   <link rel="stylesheet" href="../assets/css/main.css">
</head>

<body>

   <main>
      <div class="breadcrumb__area pt-5 pb-5">
         <div class="container">
            <div class="row">
               <div class="col-lg-12">
                  <div class="tp-breadcrumb__content">
                     <div class="tp-breadcrumb__list">
                        <span class="tp-breadcrumb__active"><a href="../index.php">Home</a></span>
                        <span class="dvdr">/</span>
                        <span>Admin Login</span>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>

      <section class="track-area pb-40">
         <div class="container">
            <div class="row justify-content-center">

               <div class="col-lg-6 col-sm-12 d-flex">
                  <div class="tptrack__product mb-40 d-flex">
                     <div class="tptrack__content grey-bg">
                        <h4 class="tptrack__item-title">Admin Login</h4>
                        <p>Only authorized administrators are allowed to log in.</p>

                        <!-- ✅ Display Errors Here -->
                        <?php if (!empty($errors)): ?>
                           <div class="alert alert-danger">
                              <ul>
                                 <?php foreach ($errors as $error): ?>
                                    <li><?php echo $error; ?></li>
                                 <?php endforeach; ?>
                              </ul>
                           </div>
                        <?php endif; ?>

                        <!-- ✅ Display Success Message -->
                        <?php if (!empty($success)): ?>
                           <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>

                        <form method="POST">
                           <div class="tptrack__id mb-10">
                              <span><i class="fal fa-user"></i></span>
                              <input type="email" name="adminEmail" placeholder="Admin Email" required>
                           </div>
                           <div class="tptrack__email mb-10">
                              <span><i class="fal fa-key"></i></span>
                              <input type="password" name="adminPassword" placeholder="Password" required>
                           </div>
                           <div class="tpsign__remember d-flex align-items-center justify-content-between mb-15">
                              <div class="form-check">
                                 <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault2">
                                 <label class="form-check-label" for="flexCheckDefault2">Remember me</label>
                              </div>
                           </div>
                           <div class="tptrack__btn">
                              <button type="submit" class="tptrack__submition active">Login<i class="fal fa-long-arrow-right"></i></button>
                           </div>
                        </form>

                        <div class="mt-3 text-center">
                           <a href="../index.php" class="btn btn-secondary">Go to Home</a>
                        </div>
                     </div>
                  </div>
               </div>

            </div>
         </div>
      </section>
   </main>

</body>
</html>
