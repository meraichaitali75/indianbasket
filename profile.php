<?php
session_start(); // Start the session

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
   // Redirect to login page if not logged in
   header("Location: login.php");
   exit();
}

// Include the database and functions
require_once "./includes/db/functions.php";

// Create an instance of the Functions class
$functions = new Functions();

// Fetch user details from the database
$user_id = $_SESSION["user_id"];
$user = $functions->getUserById($user_id);

// Fetch orders for the logged-in user
$orders = $functions->getUserOrders($user_id);

// Fetch existing billing addresses
$addresses = $functions->getBillingAddresses($user_id);

// Handle form submission for updating profile
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_profile"])) {
   $firstname = $_POST["firstname"];
   $lastname = $_POST["lastname"];
   $email = $_POST["email"];
   $gender = $_POST["gender"];

   // Handle profile picture upload
   if (isset($_FILES["profile_picture"]) && $_FILES["profile_picture"]["error"] == 0) {
      $target_dir = "assets/img/uploads/";
      if (!is_dir($target_dir)) {
         mkdir($target_dir, 0777, true); // Create the directory if it doesn't exist
      }

      $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);
      $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

      // Check if the file is an actual image
      $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
      if ($check === false) {
         $error_message = "File is not an image.";
      } elseif ($_FILES["profile_picture"]["size"] > 2000000) { // 2MB limit
         $error_message = "Sorry, your file is too large. Max size is 2MB.";
      } elseif ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
         $error_message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
      } else {
         // Upload the file
         if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
            // Update the profile picture path in the database
            $profile_pic = $target_file;
         } else {
            $error_message = "Sorry, there was an error uploading your file.";
         }
      }
   } else {
      // Keep the existing profile picture if no new file is uploaded
      $profile_pic = $user["profile_pic"];
   }

   // Update user profile
   if (!isset($error_message)) {
      $result = $functions->updateProfile($user_id, $firstname, $lastname, $email, $gender, $profile_pic);

      if ($result === "success") {
         // Refresh the page to show updated details
         header("Location: profile.php");
         exit();
      } else {
         $error_message = $result;
      }
   }
}

// Handle form submission for adding a new billing address
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_address"])) {
   $firstname = $_POST["firstname"];
   $lastname = $_POST["lastname"];
   $email = $_POST["email"];
   $phone = $_POST["phone"];
   $country = $_POST["country"];
   $province = $_POST["province"];
   $city = $_POST["city"];
   $street_address = $_POST["street_address"];
   $zip_code = $_POST["zip_code"];
   $landmark = $_POST["landmark"];
   $address_type = $_POST["addressType"];

   // Call the addBillingAddress method
   $result = $functions->addBillingAddress($user_id, $firstname, $lastname, $email, $phone, $country, $province, $city, $street_address, $zip_code, $landmark, $address_type);

   if ($result === "success") {
      // Redirect to refresh the page and show the new address
      header("Location: profile.php");
      exit();
   } else {
      // Display an error message
      $error_message = $result;
   }
}

// Handle Edit Address Request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["edit_address"])) {
   $address_id = $_POST["address_id"];
   $firstname = $_POST["firstname"];
   $lastname = $_POST["lastname"];
   $email = $_POST["email"];
   $phone = $_POST["phone"];
   $country = $_POST["country"];
   $province = $_POST["province"];
   $city = $_POST["city"];
   $street_address = $_POST["street_address"];
   $zip_code = $_POST["zip_code"];
   $landmark = $_POST["landmark"];
   $address_type = $_POST["addressType"];

   $result = $functions->editBillingAddress($address_id, $firstname, $lastname, $email, $phone, $country, $province, $city, $street_address, $zip_code, $landmark, $address_type);

   if ($result === "success") {
      header("Location: profile.php");
      exit();
   } else {
      $error_message = $result;
   }
}

// Handle Delete Address Request
if (isset($_GET["delete_address"])) {
   $address_id = $_GET["delete_address"];
   $result = $functions->deleteBillingAddress($address_id);

   if ($result === "success") {
      header("Location: profile.php");
      exit();
   } else {
      $error_message = $result;
   }
}

// Handle form submission for changing password
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["change_password"])) {
   $currentPassword = $_POST["currentPassword"];
   $newPassword = $_POST["newPassword"];
   $confirmPassword = $_POST["confirmPassword"];

   // Call the changePassword method
   $result = $functions->changePassword($user_id, $currentPassword, $newPassword, $confirmPassword);

   if ($result === "success") {
      // Redirect to the change password tab with a success message
      header("Location: profile.php?password_change=success#change-password");
      exit();
   } else {
      // Redirect to the change password tab with an error message
      header("Location: profile.php?password_change=error&message=" . urlencode($result) . "#change-password");
      exit();
   }
}
?>
<!doctype html>
<html class="no-js" lang="zxx">

<head>
   <meta charset="utf-8">
   <meta http-equiv="x-ua-compatible" content="ie=edge">
   <title>Indian Basket | Profile</title>
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
      <div class="container profile-container">
         <div class="row">
            <div class="col-md-3">
               <div class="card profile-sidebar">
                  <div class="card-body">
                     <div>
                        <h5 class="card-title">My Account</h5>
                        <!-- Tab Navigation -->
                        <ul class="nav flex-column nav-pills" id="profileTabs" role="tablist">
                           <li class="nav-item">
                              <a class="nav-link active" id="account-details-tab" data-bs-toggle="pill" href="#account-details" role="tab" aria-controls="account-details" aria-selected="true">Account Details</a>
                           </li>
                           <li class="nav-item">
                              <a class="nav-link" id="orders-tab" data-bs-toggle="pill" href="#orders" role="tab" aria-controls="orders" aria-selected="false">My Orders</a>
                           </li>
                           <li class="nav-item">
                              <a class="nav-link" id="address-tab" data-bs-toggle="pill" href="#address" role="tab" aria-controls="address" aria-selected="false">Address</a>
                           </li>
                           <li class="nav-item">
                              <a class="nav-link" id="change-password-tab" data-bs-toggle="pill" href="#change-password" role="tab" aria-controls="change-password" aria-selected="false">Change Password</a>
                           </li>
                        </ul>
                        <button type="button" class="btn btn-dark logout-btn mt-3">Log out</button>
                     </div>
                     <div class="contact-address">
                        <h6>Need Help?</h6>
                        <p class="mb-2">For any help, please call us:</p>
                        <p class="mb-0"><strong>1800-5556-69635</strong></p>
                     </div>
                  </div>
               </div>
            </div>
            <div class="col-md-9">
               <!-- Tab Content -->
               <div class="tab-content" id="profileTabsContent">
                  <!-- Account Details Tab -->
                  <div class="tab-pane account-tab-pane fade show active" id="account-details" role="tabpanel" aria-labelledby="account-details-tab">
                     <div class="card">
                        <div class="card-body card-main-body">
                           <div class="account-top-row">
                              <h5 class="card-account-title">Account Details</h5>
                           </div>
                           <form method="POST" enctype="multipart/form-data" class="account-form">
                              <!-- Profile Picture Section -->
                              <div class="text-center mb-4">
                                 <?php
                                 $profile_picture = isset($user["profile_pic"]) ? $user["profile_pic"] : "assets/img/default-profile.png";
                                 ?>
                                 <img src="<?php echo $profile_picture; ?>" alt="Profile Picture" class="profile-picture">
                                 <div class="upload-btn-wrapper">
                                    <input type="file" id="profile_picture" name="profile_picture" accept="image/*" style="display: none;" />
                                    <label for="profile_picture" type="file" class="upload-btn-icon" name="profile_picture" accept="image/*">
                                       <svg width="30" height="31" viewBox="0 0 30 31" fill="none" xmlns="http://www.w3.org/2000/svg">
                                          <path d="M20.9691 5.03101L25.6668 10.4917C25.7143 10.5464 25.752 10.6116 25.7777 10.6833C25.8034 10.755 25.8166 10.832 25.8166 10.9097C25.8166 10.9874 25.8034 11.0644 25.7777 11.1361C25.752 11.2079 25.7143 11.273 25.6668 11.3277L14.2914 24.5541L9.45785 25.1782C9.30669 25.1979 9.15366 25.1777 9.01012 25.1192C8.86658 25.0607 8.73621 24.9654 8.6287 24.8403C8.52118 24.7152 8.43929 24.5636 8.38908 24.3967C8.33888 24.2297 8.32166 24.0518 8.3387 23.8761L8.87549 18.2564L20.25 5.03101C20.2971 4.97582 20.3531 4.93202 20.4148 4.90213C20.4766 4.87224 20.5427 4.85685 20.6096 4.85685C20.6764 4.85685 20.7426 4.87224 20.8043 4.90213C20.8661 4.93202 20.9221 4.97582 20.9691 5.03101ZM29.4091 3.64449L26.8645 0.68992C26.4827 0.248062 25.9659 0 25.4271 0C24.8884 0 24.3716 0.248062 23.9898 0.68992L22.1457 2.83789C22.0982 2.89263 22.0605 2.95774 22.0348 3.02949C22.0091 3.10123 21.9959 3.17819 21.9959 3.25591C21.9959 3.33363 22.0091 3.41059 22.0348 3.48233C22.0605 3.55408 22.0982 3.61919 22.1457 3.67393L26.8434 9.13561C26.8904 9.19079 26.9465 9.23459 27.0082 9.26448C27.0699 9.29437 27.1361 9.30976 27.2029 9.30976C27.2698 9.30976 27.3359 9.29437 27.3977 9.26448C27.4594 9.23459 27.5154 9.19079 27.5625 9.13561L29.4066 6.99156C29.7866 6.54765 30 5.94681 30 5.32048C30 4.69414 29.7866 4.0933 29.4066 3.64939L29.4091 3.64449ZM20.0028 20.9568V27.1211H3.33295V7.74418H15.3025C15.4683 7.74283 15.6271 7.66686 15.7456 7.53223L17.8286 5.11049C17.9164 5.00885 17.9762 4.8792 18.0005 4.73798C18.0248 4.59676 18.0125 4.45032 17.9651 4.31725C17.9178 4.18418 17.8375 4.07046 17.7344 3.99052C17.6314 3.91057 17.5102 3.86801 17.3864 3.86822H2.49992C1.83697 3.86848 1.20124 4.17478 0.732458 4.71979C0.26368 5.26481 0.000224276 6.00393 5.6984e-07 6.7747V28.0906C-0.000221168 28.4725 0.0642715 28.8507 0.189795 29.2036C0.315318 29.5565 0.499412 29.8772 0.731563 30.1474C0.963713 30.4176 1.23937 30.6319 1.54279 30.7782C1.84621 30.9245 2.17145 30.9999 2.49992 31H20.8333C21.4962 30.9997 22.1319 30.6934 22.6007 30.1484C23.0695 29.6034 23.333 28.8643 23.3332 28.0935V18.538C23.333 18.3943 23.2962 18.2539 23.2274 18.1345C23.1587 18.0151 23.0611 17.9219 22.947 17.8669C22.8328 17.8118 22.7072 17.7972 22.5859 17.825C22.4647 17.8527 22.3532 17.9216 22.2655 18.0229L20.1825 20.4446C20.0683 20.5822 20.0039 20.7656 20.0028 20.9568Z" fill="#CB9274" />
                                       </svg>
                                    </label>
                                 </div>
                              </div>
                              <div class="row">
                                 <div class="col-md-12 mb-3">
                                    <input type="text" class="form-control" id="firstname" name="firstname" placeholder="First Name" value="<?php echo htmlspecialchars($user["firstname"]); ?>">
                                 </div>
                                 <div class="col-md-12 mb-3">
                                    <input type="text" class="form-control" id="lastname" name="lastname" placeholder="Last Name" value="<?php echo htmlspecialchars($user["lastname"]); ?>">
                                 </div>
                                 <div class="col-md-12 mb-3">
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($user["email"]); ?>">
                                 </div>
                                 <div class="col-md-12 mb-3">
                                    <select class="form-select w-100" id="gender" name="gender">
                                       <option value="Male" <?php echo ($user["gender"] == "Male") ? "selected" : ""; ?>>Male</option>
                                       <option value="Female" <?php echo ($user["gender"] == "Female") ? "selected" : ""; ?>>Female</option>
                                       <option value="Other" <?php echo ($user["gender"] == "Other") ? "selected" : ""; ?>>Other</option>
                                    </select>
                                 </div>
                              </div>
                              <button type="submit" name="update_profile" class="btn btn-dark btn-sm">Save</button>
                           </form>
                        </div>
                     </div>
                  </div>
                  <!-- Orders Tab -->
                  <div class="tab-pane order-tab-pane fade" id="orders" role="tabpanel" aria-labelledby="orders-tab">
                     <div class="card">
                        <div class="card-body card-main-bod">
                           <div class="account-top-row">
                              <h5 class="card-account-title">My Orders</h5>
                           </div>
                           <div class="order-card-body-content">
                              <?php if (!empty($orders)): ?>
                                 <?php
                                 $grouped_orders = [];
                                 foreach ($orders as $order) {
                                    $order_id = $order["order_id"];
                                    if (!isset($grouped_orders[$order_id])) {
                                       $grouped_orders[$order_id] = [
                                          "order_id" => $order["order_id"],
                                          "order_date" => $order["order_date"],
                                          "total_amount" => $order["total_amount"],
                                          "status" => $order["status"],
                                          "items" => [],
                                       ];
                                    }
                                    $grouped_orders[$order_id]["items"][] = [
                                       "product_name" => $order["product_name"],
                                       "quantity" => $order["quantity"],
                                       "price" => $order["price"],
                                       "product_image" => $order["product_image"], // Add product image
                                    ];
                                 }
                                 ?>

                                 <?php foreach ($grouped_orders as $order): ?>
                                    <div class="order-card">
                                       <div class="order-header">
                                          <div class="order-info">
                                             <p class="order-number">Order number - #<?php echo $order["order_id"]; ?></p>
                                             <p class="order-number">Order Date - <?php echo date("d M Y", strtotime($order["order_date"])); ?></p>
                                             <p class="delivered">Status: <?php echo $order["status"]; ?></p>
                                          </div>
                                       </div>
                                       <div class="order-items">
                                          <?php foreach ($order["items"] as $item): ?>
                                             <div class="order-item">
                                                <img src="<?php echo $item["product_image"]; ?>" class="order-img" alt="Product image">
                                                <div class="ps-3">
                                                   <p class="item-name"><?php echo $item["product_name"]; ?></p>
                                                   <p class="item-quantity"><?php echo $item["quantity"]; ?> x $<?php echo number_format($item["price"], 2); ?></p>
                                                </div>
                                             </div>
                                          <?php endforeach; ?>
                                       </div>
                                    </div>
                                 <?php endforeach; ?>
                              <?php else: ?>
                                 <p>No orders found.</p>
                              <?php endif; ?>
                           </div>
                        </div>
                     </div>
                  </div>
                  <!-- Address Tab -->
                  <div class="tab-pane address-tab-pane fade" id="address" role="tabpanel" aria-labelledby="address-tab">
                     <div class="billing-card">
                        <div class="card-body">
                           <h5 class="card-account-title">Billing Address</h5>
                           <div class="row">
                              <?php if (!empty($addresses)): ?>
                                 <?php foreach ($addresses as $address): ?>
                                    <div class="col-md-6">
                                       <div class="billing-card">
                                          <div class="billing-address-details">
                                             <div class="billing-address-details-row">
                                                <p class="name"><?php echo htmlspecialchars($address['firstname'] . ' ' . htmlspecialchars($address['lastname'])); ?></p>
                                                <div>
                                                   <!-- Edit Button -->
                                                   <a href="#" data-bs-toggle="modal" data-bs-target="#editAddressModal<?php echo $address['address_id']; ?>">
                                                      <img src="./assets/img/icon/edit.svg" class="icons" alt="Edit icon">
                                                   </a>
                                                   <!-- Delete Button -->
                                                   <a href="profile.php?delete_address=<?php echo $address['address_id']; ?>" onclick="return confirm('Are you sure you want to delete this address?');">
                                                      <img src="./assets/img/icon/delete.svg" class="icons" alt="Delete icon">
                                                   </a>
                                                </div>
                                             </div>
                                             <div class="billing-address-details-row">
                                                <div>
                                                   <p class="street-address"><?php echo htmlspecialchars($address['street_address']); ?></p>
                                                   <p class="email"><?php echo htmlspecialchars($address['email']); ?></p>
                                                </div>
                                                <div>
                                                   <p class="city-state-zip"><?php echo htmlspecialchars($address['city']) . ', ' . htmlspecialchars($address['province']) . ' ' . htmlspecialchars($address['zip_code']); ?></p>
                                                   <p class="phone"><?php echo htmlspecialchars($address['phone']); ?></p>
                                                </div>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                 <?php endforeach; ?>
                              <?php else: ?>
                                 <p>No billing addresses found.</p>
                              <?php endif; ?>
                           </div>
                           <div class="row justify-content-center">
                              <button type="button" class="btn btn-dark btn-sm" data-bs-toggle="modal" data-bs-target="#addAddressModal">Add New</button>
                           </div>
                        </div>
                     </div>
                  </div>
                  <!-- Change Password Tab -->
                  <div class="tab-pane change-password-tab-pane fade" id="change-password" role="tabpanel" aria-labelledby="change-password">
                     <div class="card change-password-card">
                        <div class="card-body card-main-body change-password-body">
                           <h5 class="card-account-title">Change Password</h5>

                           <!-- Display Success or Error Messages -->
                           <?php if (isset($_GET['password_change'])): ?>
                              <?php if ($_GET['password_change'] === 'success'): ?>
                                 <div class="alert alert-success" id="passwordChangeAlert" role="alert">
                                    Password changed successfully!
                                 </div>
                              <?php elseif ($_GET['password_change'] === 'error' && isset($_GET['message'])): ?>
                                 <div class="alert alert-danger" role="alert">
                                    <?php echo htmlspecialchars(urldecode($_GET['message'])); ?>
                                 </div>
                              <?php endif; ?>
                           <?php endif; ?>

                           <form method="POST" enctype="multipart/form-data">
                              <div class="row">
                                 <div class="col-md-12 mb-3">
                                    <input type="password" class="form-control" id="currentPassword" name="currentPassword" placeholder="Current Password" required>
                                 </div>
                                 <div class="col-md-12 mb-3">
                                    <input type="password" class="form-control" id="newPassword" name="newPassword" placeholder="New Password" required>
                                 </div>
                                 <div class="col-md-12 mb-3">
                                    <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="Confirm New Password" required>
                                 </div>
                              </div>
                              <button type="submit" name="change_password" class="btn btn-dark btn-sm">Submit</button>
                           </form>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </main>


   <!-- footer-area-start -->
   <?php include "includes/footer.php"; ?>
   <!-- footer-area-end -->


   <!-- Add Address Modal -->
   <div class="modal fade" id="addAddressModal" tabindex="-1" aria-labelledby="addAddressModalLabel" aria-hidden="true">
      <div class="modal-dialog">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="addAddressModalLabel">Add New Billing Address</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
               <form method="POST" action="profile.php">
                  <div class="row">
                     <div class="col-md-6 mb-3">
                        <input type="text" class="form-control" id="firstName" name="firstname" placeholder="First Name" required>
                     </div>
                     <div class="col-md-6 mb-3">
                        <input type="text" class="form-control" id="lastName" name="lastname" placeholder="Last Name" required>
                     </div>
                     <div class="col-md-6 mb-3">
                        <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                     </div>
                     <div class="col-md-6 mb-3">
                        <input type="text" class="form-control" id="phone" name="phone" placeholder="Phone Number" required>
                     </div>
                     <div class="col-md-6 mb-3">
                        <input type="text" class="form-control" id="country" name="country" placeholder="Country" required>
                     </div>
                     <div class="col-md-6 mb-3">
                        <input type="text" class="form-control" id="province" name="province" placeholder="Province" required>
                     </div>
                     <div class="col-md-6 mb-3">
                        <input type="text" class="form-control" id="city" name="city" placeholder="City" required>
                     </div>
                     <div class="col-md-6 mb-3">
                        <input type="text" class="form-control" id="zipCode" name="zip_code" placeholder="Zip Code" required>
                     </div>
                     <div class="col-md-12 mb-3">
                        <input type="text" class="form-control" id="streetAddress" name="street_address" placeholder="Street Address" required>
                     </div>
                     <div class="col-md-6 mb-3">
                        <input type="text" class="form-control" id="landmark" name="landmark" placeholder="Landmark">
                     </div>
                     <div class="col-md-6 custom-radio">
                        <label class="form-label">Address Type</label>
                        <div class="form-check">
                           <input class="form-check-input" type="radio" name="addressType" id="home" value="Home" checked>
                           <label class="form-check-label" for="home">Home</label>
                        </div>
                        <div class="form-check">
                           <input class="form-check-input" type="radio" name="addressType" id="office" value="Office">
                           <label class="form-check-label" for="office">Office</label>
                        </div>
                     </div>
                  </div>
                  <div class="modal-footer">
                     <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                     <button type="submit" name="add_address" class="btn btn-dark">Save</button>
                  </div>
               </form>
            </div>
         </div>
      </div>
   </div>

   <!-- Edit Address Modals -->
   <?php if (!empty($addresses)): ?>
      <?php foreach ($addresses as $address): ?>
         <div class="modal fade" id="editAddressModal<?php echo $address['address_id']; ?>" tabindex="-1" aria-labelledby="editAddressModalLabel<?php echo $address['address_id']; ?>" aria-hidden="true">
            <div class="modal-dialog">
               <div class="modal-content">
                  <div class="modal-header">
                     <h5 class="modal-title" id="editAddressModalLabel<?php echo $address['address_id']; ?>">Edit Billing Address</h5>
                     <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                     <form method="POST" action="profile.php">
                        <input type="hidden" name="address_id" value="<?php echo $address['address_id']; ?>">
                        <div class="row">
                           <div class="col-md-6 mb-3">
                              <input type="text" class="form-control" name="firstname" placeholder="First Name" value="<?php echo htmlspecialchars($address['firstname']); ?>" required>
                           </div>
                           <div class="col-md-6 mb-3">
                              <input type="text" class="form-control" name="lastname" placeholder="Last Name" value="<?php echo htmlspecialchars($address['lastname']); ?>" required>
                           </div>
                           <div class="col-md-6 mb-3">
                              <input type="email" class="form-control" name="email" placeholder="Email" value="<?php echo htmlspecialchars($address['email']); ?>" required>
                           </div>
                           <div class="col-md-6 mb-3">
                              <input type="text" class="form-control" name="phone" placeholder="Phone Number" value="<?php echo htmlspecialchars($address['phone']); ?>" required>
                           </div>
                           <div class="col-md-6 mb-3">
                              <input type="text" class="form-control" name="country" placeholder="Country" value="<?php echo htmlspecialchars($address['country']); ?>" required>
                           </div>
                           <div class="col-md-6 mb-3">
                              <input type="text" class="form-control" name="province" placeholder="Province" value="<?php echo htmlspecialchars($address['province']); ?>" required>
                           </div>
                           <div class="col-md-6 mb-3">
                              <input type="text" class="form-control" name="city" placeholder="City" value="<?php echo htmlspecialchars($address['city']); ?>" required>
                           </div>
                           <div class="col-md-6 mb-3">
                              <input type="text" class="form-control" name="zip_code" placeholder="Zip Code" value="<?php echo htmlspecialchars($address['zip_code']); ?>" required>
                           </div>
                           <div class="col-md-12 mb-3">
                              <input type="text" class="form-control" name="street_address" placeholder="Street Address" value="<?php echo htmlspecialchars($address['street_address']); ?>" required>
                           </div>
                           <div class="col-md-6 mb-3">
                              <input type="text" class="form-control" name="landmark" placeholder="Landmark" value="<?php echo htmlspecialchars($address['landmark']); ?>">
                           </div>
                           <div class="col-md-6 custom-radio">
                              <label class="form-label">Address Type</label>
                              <div class="form-check">
                                 <input class="form-check-input" type="radio" name="addressType" id="home<?php echo $address['address_id']; ?>" value="Home" <?php echo ($address['address_type'] == 'Home') ? 'checked' : ''; ?>>
                                 <label class="form-check-label" for="home<?php echo $address['address_id']; ?>">Home</label>
                              </div>
                              <div class="form-check">
                                 <input class="form-check-input" type="radio" name="addressType" id="office<?php echo $address['address_id']; ?>" value="Office" <?php echo ($address['address_type'] == 'Office') ? 'checked' : ''; ?>>
                                 <label class="form-check-label" for="office<?php echo $address['address_id']; ?>">Office</label>
                              </div>
                           </div>
                        </div>
                        <div class="modal-footer">
                           <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                           <button type="submit" name="edit_address" class="btn btn-dark">Save Changes</button>
                        </div>
                     </form>
                  </div>
               </div>
            </div>
         </div>
      <?php endforeach; ?>
   <?php endif; ?>

   <!-- JavaScript -->
   <script src="assets/js/bootstrap.bundle.min.js"></script>
   <script src="assets/js/main.js"></script>

   <script>
      // Activate the Change Password tab if the URL contains #change-password
      document.addEventListener("DOMContentLoaded", function() {
         if (window.location.hash === "#change-password") {
            // Trigger the tab click
            const changePasswordTab = document.querySelector('#change-password-tab');
            if (changePasswordTab) {
               const tab = new bootstrap.Tab(changePasswordTab);
               tab.show();
            }
         }
      });

      // Function to hide the alert message after 3 seconds
      function hideAlert() {
         const alertElement = document.getElementById('passwordChangeAlert');
         if (alertElement) {
            setTimeout(() => {
               alertElement.classList.add('hide'); // Add the 'hide' class for fade-out
            }, 3000); // 3000 milliseconds = 3 seconds
         }
      }

      // Call the function when the page loads
      document.addEventListener("DOMContentLoaded", hideAlert);
   </script>
</body>

</html>