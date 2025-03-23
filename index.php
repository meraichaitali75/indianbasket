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

// Fetch all categories from the database
$categories = $functions->getAllCategories();

// Fetch all products for the Weekly Food Offers section
$products = $functions->getAllProducts();


// Fetch latest 5 categories
$latestCategories = $functions->getLatestCategories(5);
?>

<!doctype html>
<html class="no-js" lang="zxx">

<head>
   <meta charset="utf-8">
   <meta http-equiv="x-ua-compatible" content="ie=edge">
   <title>Indian Basket | Home</title>
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
      <!-- slider-area-start -->
      <section class="slider-area tpslider-delay">
         <div class="swiper-container slider-active">
            <div class="swiper-wrapper">
               <div class="swiper-slide ">
                  <div class="tpslider pt-90 pb-0 grey-bg" data-background="assets/img/slider/shape-bg.jpg">
                     <div class="container">
                        <div class="row align-items-center">
                           <div class="col-xxl-5 col-lg-6 col-md-6 col-12 col-sm-6">
                              <div class="tpslider__content pt-20">
                                 <span class="tpslider__sub-title mb-35">Top Seller In The Week</span>
                                 <h2 class="tpslider__title mb-30">Choose Your Healthy Lifestyle.</h2>
                                 <p>Presentation matters. Our fresh Vietnamese vegetable rolls <br> look good and taste even better</p>
                                 <div class="tpslider__btn">
                                    <a class="tp-btn" href="shop-2.html">Shop Now</a>
                                 </div>
                              </div>
                           </div>
                           <div class="col-xxl-7 col-lg-6 col-md-6 col-12 col-sm-6">
                              <div class="tpslider__thumb p-relative pt-15">
                                 <img class="tpslider__thumb-img" src="assets/img/slider/slider-bg-1.png" alt="slider-bg">
                                 <div class="tpslider__shape d-none d-md-block">
                                    <img class="tpslider__shape-one" src="assets/img/slider/slider-shape-1.png" alt="shape">
                                    <img class="tpslider__shape-two" src="assets/img/slider/slider-shape-2.png" alt="shape">
                                    <img class="tpslider__shape-three" src="assets/img/slider/slider-shape-3.png" alt="shape">
                                    <img class="tpslider__shape-four" src="assets/img/slider/slider-shape-4.png" alt="shape">
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <!-- Add more slider slides here if needed -->
            </div>
            <div class="tpslider__arrow d-none  d-xxl-block">
               <button class="tpsliderarrow tpslider__arrow-prv"><i class="icon-chevron-left"></i></button>
               <button class="tpsliderarrow tpslider__arrow-nxt"><i class="icon-chevron-right"></i></button>
            </div>
            <div class="slider-pagination d-xxl-none"></div>
         </div>
      </section>
      <!-- slider-area-end -->

      <!-- category-area-start -->
      <section class="category-area grey-bg pb-40">
         <div class="container">
            <div class="swiper-container category-active">
               <div class="swiper-wrapper">
                  <?php if (!empty($categories)): ?>
                     <?php foreach ($categories as $category): ?>
                        <div class="swiper-slide">
                           <div class="category__item mb-30">
                              <div class="category__thumb fix mb-15">
                                 <a href="shop-details-3.html">
                                    <img src="uploads/categories/<?php echo $category['image']; ?>" alt="<?php echo $category['name']; ?>">
                                 </a>
                              </div>
                              <div class="category__content">
                                 <h5 class="category__title">
                                    <a href="shop-details-3.html"><?php echo $category['name']; ?></a>
                                 </h5>
                                 <span class="category__count"><?php echo $category['item_count']; ?> items</span>
                              </div>
                           </div>
                        </div>
                     <?php endforeach; ?>
                  <?php else: ?>
                     <div class="swiper-slide">
                        <div class="category__item mb-30">
                           <div class="category__content">
                              <h5 class="category__title">No categories found.</h5>
                           </div>
                        </div>
                     </div>
                  <?php endif; ?>
               </div>
            </div>
         </div>
      </section>
      <!-- category-area-end -->

      <!-- product-area-start -->
      <section class="product-area grey-bg pb-0">
         <div class="container">
            <div class="row">
               <div class="col-lg-12 text-center">
                  <div class="tpsection mb-35">
                     <h4 class="tpsection__sub-title">~ Special Products ~</h4>
                     <h4 class="tpsection__title">Weekly Food Offers</h4>
                     <p>The liber tempor cum soluta nobis eleifend option congue doming quod mazim.</p>
                  </div>
               </div>
            </div>
            <div class="tpproduct__arrow p-relative">
               <div class="swiper-container tpproduct-active tpslider-bottom p-relative">
                  <div class="swiper-wrapper">
                     <?php if (!empty($products)): ?>
                        <?php foreach ($products as $product): ?>
                           <div class="swiper-slide">
                              <div class="tpproduct p-relative">
                                 <div class="tpproduct__thumb p-relative text-center">
                                    <a href="shop-details-4.html">
                                       <img src="assets/img/product/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                                    </a>
                                    <div class="tpproduct__info bage">
                                       <span class="tpproduct__info-discount bage__discount">-<?php echo rand(10, 50); ?>%</span>
                                       <span class="tpproduct__info-hot bage__hot">HOT</span>
                                    </div>
                                    <div class="tpproduct__shopping">
                                       <a class="tpproduct__shopping-wishlist" href="wishlist.html"><i class="icon-heart icons"></i></a>
                                       <a class="tpproduct__shopping-wishlist" href="#"><i class="icon-layers"></i></a>
                                       <a class="tpproduct__shopping-cart" href="#"><i class="icon-eye"></i></a>
                                    </div>
                                 </div>
                                 <div class="tpproduct__content">
                                    <span class="tpproduct__content-weight">
                                       <a href="shop-details-4.html"><?php echo $product['category_name']; ?></a>
                                    </span>
                                    <h4 class="tpproduct__title">
                                       <a href="shop-details-4.html"><?php echo $product['name']; ?></a>
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
                                       <del>$<?php echo $product['price'] + rand(5, 20); ?></del>
                                    </div>
                                 </div>
                                 <div class="tpproduct__hover-text">
                                    <div class="tpproduct__hover-btn d-flex justify-content-center mb-10">
                                       <a class="tp-btn-2" href="shop-details-4.html">Add to cart</a>
                                    </div>
                                    <div class="tpproduct__descrip">
                                       <ul>
                                          <li>Type: Organic</li>
                                          <li>MFG: August 4.2021</li>
                                          <li>LIFE: 60 days</li>
                                       </ul>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        <?php endforeach; ?>
                     <?php else: ?>
                        <div class="swiper-slide">
                           <div class="tpproduct p-relative">
                              <div class="tpproduct__content">
                                 <h4 class="tpproduct__title">No products found.</h4>
                              </div>
                           </div>
                        </div>
                     <?php endif; ?>
                  </div>
               </div>
               <div class="tpproduct-btn">
                  <div class="tpprduct-arrow tpproduct-btn__prv"><a href="#"><i class="icon-chevron-left"></i></a></div>
                  <div class="tpprduct-arrow tpproduct-btn__nxt"><a href="#"><i class="icon-chevron-right"></i></a></div>
               </div>
            </div>
         </div>
      </section>
      <!-- product-area-end -->

      <!-- product-area-start -->
      <section class="weekly-product-area grey-bg pb-70">
         <div class="container">
            <div class="row">
               <div class="col-lg-12 text-center">
                  <div class="tpsection mb-20">
                     <h4 class="tpsection__sub-title">~ Special Products ~</h4>
                     <h4 class="tpsection__title">Weekly Food Offers2</h4>
                  </div>
               </div>
            </div>
            <div class="row">
               <div class="col-lg-12">
                  <div class="tpnavtab__area pb-40">
                     <!-- Dynamic Tabs for Latest 5 Categories -->
                     <nav>
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                           <button class="nav-link active" id="nav-all-tab" data-bs-toggle="tab" data-bs-target="#nav-all" type="button" role="tab" aria-controls="nav-all" aria-selected="true">All Products</button>
                           <?php
                           // Fetch latest 5 categories
                           $latestCategories = $functions->getLatestCategories(5);
                           if (!empty($latestCategories)):
                              foreach ($latestCategories as $category):
                           ?>
                                 <button class="nav-link" id="nav-<?php echo strtolower(str_replace(' ', '-', $category['name'])); ?>-tab" data-bs-toggle="tab" data-bs-target="#nav-<?php echo strtolower(str_replace(' ', '-', $category['name'])); ?>" type="button" role="tab" aria-controls="nav-<?php echo strtolower(str_replace(' ', '-', $category['name'])); ?>" aria-selected="false"><?php echo $category['name']; ?></button>
                           <?php
                              endforeach;
                           endif;
                           ?>
                        </div>
                     </nav>

                     <!-- Tab Content -->
                     <div class="tab-content" id="nav-tabContent">
                        <!-- All Products Tab -->
                        <div class="tab-pane fade show active" id="nav-all" role="tabpanel" aria-labelledby="nav-all-tab" tabindex="0">
                           <div class="tpproduct__arrow p-relative">
                              <div class="swiper-container tpproduct-active tpslider-bottom p-relative">
                                 <div class="swiper-wrapper">
                                    <?php if (!empty($products)): ?>
                                       <?php foreach ($products as $product): ?>
                                          <div class="swiper-slide">
                                             <div class="tpproduct p-relative">
                                                <div class="tpproduct__thumb p-relative text-center">
                                                   <a href="shop-details-4.html">
                                                      <img src="uploads/products/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                                                   </a>
                                                   <div class="tpproduct__info bage">
                                                      <span class="tpproduct__info-discount bage__discount">-<?php echo rand(10, 50); ?>%</span>
                                                      <span class="tpproduct__info-hot bage__hot">HOT</span>
                                                   </div>
                                                   <div class="tpproduct__shopping">
                                                      <a class="tpproduct__shopping-wishlist" href="wishlist.html"><i class="icon-heart icons"></i></a>
                                                      <a class="tpproduct__shopping-wishlist" href="#"><i class="icon-layers"></i></a>
                                                      <a class="tpproduct__shopping-cart" href="#"><i class="icon-eye"></i></a>
                                                   </div>
                                                </div>
                                                <div class="tpproduct__content">
                                                   <span class="tpproduct__content-weight">
                                                      <a href="shop-details-4.html"><?php echo $product['category_name']; ?></a>
                                                   </span>
                                                   <h4 class="tpproduct__title">
                                                      <a href="shop-details-4.html"><?php echo $product['name']; ?></a>
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
                                                      <del>$<?php echo $product['price'] + rand(5, 20); ?></del>
                                                   </div>
                                                </div>
                                                <div class="tpproduct__hover-text">
                                                   <div class="tpproduct__hover-btn d-flex justify-content-center mb-10">
                                                      <a class="tp-btn-2" href="shop-details-4.html">Add to cart</a>
                                                   </div>
                                                   <div class="tpproduct__descrip">
                                                      <ul>
                                                         <li>Type: Organic</li>
                                                         <li>MFG: August 4.2021</li>
                                                         <li>LIFE: 60 days</li>
                                                      </ul>
                                                   </div>
                                                </div>
                                             </div>
                                          </div>
                                       <?php endforeach; ?>
                                    <?php else: ?>
                                       <div class="swiper-slide">
                                          <div class="tpproduct p-relative">
                                             <div class="tpproduct__content">
                                                <h4 class="tpproduct__title">No products found.</h4>
                                             </div>
                                          </div>
                                       </div>
                                    <?php endif; ?>
                                 </div>
                              </div>
                              <div class="tpproduct-btn">
                                 <div class="tpprduct-arrow tpproduct-btn__prv"><a href="#"><i class="icon-chevron-left"></i></a></div>
                                 <div class="tpprduct-arrow tpproduct-btn__nxt"><a href="#"><i class="icon-chevron-right"></i></a></div>
                              </div>
                           </div>
                        </div>

                        <!-- Dynamic Tabs for Latest 5 Categories -->
                        <?php if (!empty($latestCategories)): ?>
                           <?php foreach ($latestCategories as $category): ?>
                              <div class="tab-pane fade" id="nav-<?php echo strtolower(str_replace(' ', '-', $category['name'])); ?>" role="tabpanel" aria-labelledby="nav-<?php echo strtolower(str_replace(' ', '-', $category['name'])); ?>-tab" tabindex="0">
                                 <div class="tpproduct__arrow p-relative">
                                    <div class="swiper-container tpproduct-active tpslider-bottom p-relative">
                                       <div class="swiper-wrapper">
                                          <?php
                                          // Filter products by category
                                          $categoryProducts = array_filter($products, function ($product) use ($category) {
                                             return $product['category_id'] == $category['category_id'];
                                          });
                                          ?>
                                          <?php if (!empty($categoryProducts)): ?>
                                             <?php foreach ($categoryProducts as $product): ?>
                                                <div class="swiper-slide">
                                                   <div class="tpproduct p-relative">
                                                      <div class="tpproduct__thumb p-relative text-center">
                                                         <a href="shop-details-4.html">
                                                            <img src="uploads/products/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                                                         </a>
                                                         <div class="tpproduct__info bage">
                                                            <span class="tpproduct__info-discount bage__discount">-<?php echo rand(10, 50); ?>%</span>
                                                            <span class="tpproduct__info-hot bage__hot">HOT</span>
                                                         </div>
                                                         <div class="tpproduct__shopping">
                                                            <a class="tpproduct__shopping-wishlist" href="wishlist.html"><i class="icon-heart icons"></i></a>
                                                            <a class="tpproduct__shopping-wishlist" href="#"><i class="icon-layers"></i></a>
                                                            <a class="tpproduct__shopping-cart" href="#"><i class="icon-eye"></i></a>
                                                         </div>
                                                      </div>
                                                      <div class="tpproduct__content">
                                                         <span class="tpproduct__content-weight">
                                                            <a href="shop-details-4.html"><?php echo $product['category_name']; ?></a>
                                                         </span>
                                                         <h4 class="tpproduct__title">
                                                            <a href="shop-details-4.html"><?php echo $product['name']; ?></a>
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
                                                            <del>$<?php echo $product['price'] + rand(5, 20); ?></del>
                                                         </div>
                                                      </div>
                                                      <div class="tpproduct__hover-text">
                                                         <div class="tpproduct__hover-btn d-flex justify-content-center mb-10">
                                                            <a class="tp-btn-2" href="shop-details-4.html">Add to cart</a>
                                                         </div>
                                                         <div class="tpproduct__descrip">
                                                            <ul>
                                                               <li>Type: Organic</li>
                                                               <li>MFG: August 4.2021</li>
                                                               <li>LIFE: 60 days</li>
                                                            </ul>
                                                         </div>
                                                      </div>
                                                   </div>
                                                </div>
                                             <?php endforeach; ?>
                                          <?php else: ?>
                                             <div class="swiper-slide">
                                                <div class="tpproduct p-relative">
                                                   <div class="tpproduct__content">
                                                      <h4 class="tpproduct__title">No products found in this category.</h4>
                                                   </div>
                                                </div>
                                             </div>
                                          <?php endif; ?>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           <?php endforeach; ?>
                        <?php endif; ?>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </section>
      <!-- product-area-end -->

      <!-- footer-area-start -->
      <?php include "includes/footer.php"; ?>
      <!-- footer-area-end -->
   </main>

   <!-- JS here -->
   <script src="assets/js/bootstrap.bundle.min.js"></script>
   <script src="assets/js/swiper-bundle.js"></script>
   <script src="assets/js/slick.js"></script>
   <script src="assets/js/magnific-popup.js"></script>
   <script src="assets/js/main.js"></script>
</body>

</html>