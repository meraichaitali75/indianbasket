<?php
session_start(); // Start the session

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
   // Redirect to login page if not logged in
   header("Location: login.php");
   exit();
}
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
   <?php
   include "includes/header.php";
   ?>
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
               <div class="swiper-slide">
                  <div class="tpslider pt-90 pb-0 grey-bg" data-background="assets/img/slider/shape-bg.jpg">
                     <div class="container">
                        <div class="row align-items-center">
                           <div class="col-xxl-5 col-lg-6 col-md-6 col-sm-6">
                              <div class="tpslider__content pt-20">
                                 <span class="tpslider__sub-title mb-35">Top Seller In The Week</span>
                                 <h2 class="tpslider__title mb-30">Fresh Bread <br> Oatmeal Crumble. </h2>
                                 <p>Presentation matters. Our fresh Vietnamese vegetable rolls <br> look good and taste even better</p>
                                 <div class="tpslider__btn">
                                    <a class="tp-btn" href="shop-2.html">Shop Now</a>
                                 </div>
                              </div>
                           </div>
                           <div class="col-xxl-7 col-lg-6  col-md-6 col-sm-6">
                              <div class="tpslider__thumb p-relative">
                                 <img class="tpslider__thumb-img" src="assets/img/slider/slider-bg-2.png" alt="slider-bg">
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
               <div class="swiper-slide">
                  <div class="tpslider pt-90 pb-0 grey-bg" data-background="assets/img/slider/shape-bg.jpg">
                     <div class="container">
                        <div class="row align-items-center">
                           <div class="col-xxl-5 col-xl-6 col-lg-6 col-md-6 col-sm-6">
                              <div class="tpslider__content pt-20">
                                 <span class="tpslider__sub-title mb-35">Top Seller In The Week</span>
                                 <h2 class="tpslider__title mb-30">The Best <br> Health Fresh.</h2>
                                 <p>Presentation matters. Our fresh Vietnamese vegetable rolls <br> look good and taste even better</p>
                                 <div class="tpslider__btn">
                                    <a class="tp-btn" href="shop-2.html">Shop Now</a>
                                 </div>
                              </div>
                           </div>
                           <div class="col-xxl-7 col-xl-6 col-lg-6 col-md-6 col-sm-6">
                              <div class="tpslider__thumb p-relative">
                                 <img class="tpslider__thumb-img" src="assets/img/slider/slider-bg-3.png" alt="slider-bg">
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
                  <div class="swiper-slide">
                     <div class="category__item mb-30">
                        <div class="category__thumb fix mb-15">
                           <a href="shop-details-3.html"><img src="assets/img/catagory/category-1.jpg" alt="category-thumb"></a>
                        </div>
                        <div class="category__content">
                           <h5 class="category__title"><a href="shop-details-4.html">Vegetables</a></h5>
                           <span class="category__count">05 items</span>
                        </div>
                     </div>
                  </div>
                  <div class="swiper-slide">
                     <div class="category__item mb-30">
                        <div class="category__thumb fix mb-15">
                           <a href="shop-details-3.html"><img src="assets/img/catagory/category-2.jpg" alt="category-thumb"></a>
                        </div>
                        <div class="category__content">
                           <h5 class="category__title"><a href="shop-details-3.html">Fresh Fruits</a></h5>
                           <span class="category__count">06 items</span>
                        </div>
                     </div>
                  </div>
                  <div class="swiper-slide">
                     <div class="category__item mb-30">
                        <div class="category__thumb fix mb-15">
                           <a href="shop-details-3.html"><img src="assets/img/catagory/category-3.jpg" alt="category-thumb"></a>
                        </div>
                        <div class="category__content">
                           <h5 class="category__title"><a href="shop-details-3.html">Fruit Drink</a></h5>
                           <span class="category__count">09 items</span>
                        </div>
                     </div>
                  </div>
                  <div class="swiper-slide">
                     <div class="category__item mb-30">
                        <div class="category__thumb fix mb-15">
                           <a href="shop-details-3.html"><img src="assets/img/catagory/category-4.jpg" alt="category-thumb"></a>
                        </div>
                        <div class="category__content">
                           <h5 class="category__title"><a href="shop-details-3.html">Fresh Bakery</a></h5>
                           <span class="category__count">11 items</span>
                        </div>
                     </div>
                  </div>
                  <div class="swiper-slide">
                     <div class="category__item mb-30">
                        <div class="category__thumb fix mb-15">
                           <a href="shop-details-3.html"><img src="assets/img/catagory/category-5.jpg" alt="category-thumb"></a>
                        </div>
                        <div class="category__content">
                           <h5 class="category__title"><a href="shop-details-3.html">Biscuits Snack</a></h5>
                           <span class="category__count">02 items</span>
                        </div>
                     </div>
                  </div>
                  <div class="swiper-slide">
                     <div class="category__item mb-30">
                        <div class="category__thumb fix mb-15">
                           <a href="shop-details-3.html"><img src="assets/img/catagory/category-6.jpg" alt="category-thumb"></a>
                        </div>
                        <div class="category__content">
                           <h5 class="category__title"><a href="shop-details-3.html">Fresh Meat</a></h5>
                           <span class="category__count">16 items</span>
                        </div>
                     </div>
                  </div>
                  <div class="swiper-slide">
                     <div class="category__item mb-30">
                        <div class="category__thumb fix mb-15">
                           <a href="shop-details-3.html"><img src="assets/img/catagory/category-7.jpg" alt="category-thumb"></a>
                        </div>
                        <div class="category__content">
                           <h5 class="category__title"><a href="shop-details-3.html">Fresh Milk</a></h5>
                           <span class="category__count">10 items</span>
                        </div>
                     </div>
                  </div>
                  <div class="swiper-slide">
                     <div class="category__item mb-30">
                        <div class="category__thumb fix mb-15">
                           <a href="shop-details-3.html"><img src="assets/img/catagory/category-8.jpg" alt="category-thumb"></a>
                        </div>
                        <div class="category__content">
                           <h5 class="category__title"><a href="shop-details-3.html">Sea Foods</a></h5>
                           <span class="category__count">11 items</span>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </section>
      <!-- category-area-end -->

      <!-- footer-area-start -->
      <?php
      include "includes/footer.php";
      ?>
      <!-- footer-area-end -->