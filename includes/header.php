<!-- header-area-start -->
<header>
    <div class="header__top theme-bg-1 d-none d-md-block">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 col-md-12">
                    <div class="header__top-left d-flex align-items-center justify-content-center">
                        <span>Welcome offer on first purchase .Use code <strong>WELCOME21</strong> to avail grab this offer</span>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12">
                    <div class="header__top-right d-flex align-items-center">
                        <div class="header__top-link">
                            <a href="#">Store Location</a>
                            <a href="#">FAQs</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="header__main-area d-none d-xl-block">
        <div class="container">
            <div class="header__for-megamenu p-relative">
                <div class="row align-items-center">
                    <div class="col-xl-3">
                        <div class="header__logo">
                            <a href="index.php"><img src="assets/img/logo/logo.png" alt="logo"></a>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="header__menu main-menu text-center">
                            <nav id="mobile-menu">
                                <ul>
                                    <li class="has-dropdown has-homemenu">
                                        <a href="index.php">Home</a>
                                    </li>
                                    <li class="has-dropdown has-megamenu">
                                        <a href="#">Shop</a>

                                    </li>
                                    <li class="has-dropdown">
                                        <a href="#">Blog</a>
                                    </li>
                                    <li class="has-dropdown">
                                        <a href="#">Pages</a>
                                        <ul class="sub-menu">
                                            <li><a href="login.php">Sign In</a></li>
                                        </ul>
                                    </li>
                                    <li class="has-dropdown">
                                        <a href="#">Shop</a>
                                    </li>
                                    <li><a href="#">About Us</a></li>
                                    <li><a href="#">Contact Us</a></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                    <div class="col-xl-3">
                        <div class="header__info d-flex align-items-center">
                            <div class="header__info-search tpcolor__purple ml-10">
                                <button class="tp-search-toggle"><i class="icon-search"></i></button>
                            </div>
                            <div class="header__info-user tpcolor__yellow ml-10">
                                <a href="profile.php"><i class="icon-user"></i></a>
                            </div>
                            <div class="header__info-wishlist tpcolor__greenish ml-10">
                                <a href="#"><i class="icon-heart icons"></i></a>
                            </div>

                            <div class="header__info-cart tpcolor__oasis ml-10 tp-cart-toggle">
                                <button><i><img src="assets/img/icon/cart-1.svg" alt=""></i>
                                    <span>5</span>
                                </button>
                            </div>
                            <div class="header__info-wishlist header__info-signout tpcolor__greenish ml-10">
                                <?php

                                // Check if user is logged in
                                if (isset($_SESSION["user_id"])) {
                                ?>
                                    <a href="includes/logout.php">
                                        <i class="signout-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                                <path d="M502.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-128-128c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L402.7 224 192 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l210.7 0-73.4 73.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l128-128zM160 96c17.7 0 32-14.3 32-32s-14.3-32-32-32L96 32C43 32 0 75 0 128L0 384c0 53 43 96 96 96l64 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-64 0c-17.7 0-32-14.3-32-32l0-256c0-17.7 14.3-32 32-32l64 0z" />
                                            </svg>
                                        </i>
                                    </a>
                                <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- header-search -->
    <div class="tpsearchbar tp-sidebar-area">
        <button class="tpsearchbar__close"><i class="icon-x"></i></button>
        <div class="search-wrap text-center">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-6 pt-100 pb-100">
                        <h2 class="tpsearchbar__title">What Are You Looking For?</h2>
                        <div class="tpsearchbar__form">
                            <form action="#">
                                <input type="text" name="search" placeholder="Search Product...">
                                <button class="tpsearchbar__search-btn"><i class="icon-search"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="search-body-overlay"></div>
    <!-- header-search-end -->

    <!-- header-cart-start -->
    <div class="tpcartinfo tp-cart-info-area p-relative">
        <button class="tpcart__close"><i class="icon-x"></i></button>
        <div class="tpcart">
            <h4 class="tpcart__title">Your Cart</h4>
            <div class="tpcart__product">
                <div class="tpcart__product-list">
                    <ul>
                        <li>
                            <div class="tpcart__item">
                                <div class="tpcart__img">
                                    <img src="assets/img/product/products1-min.jpg" alt="">
                                    <div class="tpcart__del">
                                        <a href="#"><i class="icon-x-circle"></i></a>
                                    </div>
                                </div>
                                <div class="tpcart__content">
                                    <span class="tpcart__content-title"><a href="#">Stacy's Pita Chips Parmesan Garlic & Herb From Nature</a>
                                    </span>
                                    <div class="tpcart__cart-price">
                                        <span class="quantity">1 x</span>
                                        <span class="new-price">$162.80</span>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="tpcart__item">
                                <div class="tpcart__img">
                                    <img src="assets/img/product/products12-min.jpg" alt="">
                                    <div class="tpcart__del">
                                        <a href="#"><i class="icon-x-circle"></i></a>
                                    </div>
                                </div>
                                <div class="tpcart__content">
                                    <span class="tpcart__content-title"><a href="#">Banana, Beautiful Skin, Good For Health 1Kg</a>
                                    </span>
                                    <div class="tpcart__cart-price">
                                        <span class="quantity">1 x</span>
                                        <span class="new-price">$138.00</span>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="tpcart__item">
                                <div class="tpcart__img">
                                    <img src="assets/img/product/products3-min.jpg" alt="">
                                    <div class="tpcart__del">
                                        <a href="#"><i class="icon-x-circle"></i></a>
                                    </div>
                                </div>
                                <div class="tpcart__content">
                                    <span class="tpcart__content-title"><a href="#">Quaker Popped Rice Crisps Snacks Chocolate</a>
                                    </span>
                                    <div class="tpcart__cart-price">
                                        <span class="quantity">1 x</span>
                                        <span class="new-price">$162.8</span>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="tpcart__checkout">
                    <div class="tpcart__total-price d-flex justify-content-between align-items-center">
                        <span> Subtotal:</span>
                        <span class="heilight-price"> $300.00</span>
                    </div>
                    <div class="tpcart__checkout-btn">
                        <a class="tpcart-btn mb-10" href="#">View Cart</a>
                        <a class="tpcheck-btn" href="#">Checkout</a>
                    </div>
                </div>
            </div>
            <div class="tpcart__free-shipping text-center">
                <span>Free shipping for orders <b>under 10km</b></span>
            </div>
        </div>
    </div>
    <div class="cartbody-overlay"></div>
    <!-- header-cart-end -->

    <!-- mobile-menu-area -->
    <div id="header-sticky-2" class="tpmobile-menu d-xl-none">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-4 col-md-4 col-3 col-sm-3">
                    <div class="mobile-menu-icon">
                        <button class="tp-menu-toggle"><i class="icon-menu1"></i></button>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-6 col-sm-4">
                    <div class="header__logo text-center">
                        <a href="index.php"><img src="assets/img/logo/logo.png" alt="logo"></a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-3 col-sm-5">
                    <div class="header__info d-flex align-items-center">
                        <div class="header__info-search tpcolor__purple ml-10 d-none d-sm-block">
                            <button class="tp-search-toggle"><i class="icon-search"></i></button>
                        </div>
                        <div class="header__info-user tpcolor__yellow ml-10 d-none d-sm-block">
                            <a href="#"><i class="icon-user"></i></a>
                        </div>
                        <div class="header__info-wishlist tpcolor__greenish ml-10 d-none d-sm-block">
                            <a href="#"><i class="icon-heart icons"></i></a>
                        </div>
                        <div class="header__info-cart tpcolor__oasis ml-10 tp-cart-toggle">
                            <button><i><img src="assets/img/icon/cart-1.svg" alt=""></i>
                                <span>5</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="body-overlay"></div>
    <!-- mobile-menu-area-end -->

    <!-- sidebar-menu-area -->
    <div class="tpsideinfo">
        <button class="tpsideinfo__close">Close<i class="fal fa-times ml-10"></i></button>
        <div class="tpsideinfo__search text-center pt-35">
            <span class="tpsideinfo__search-title mb-20">What Are You Looking For?</span>
            <form action="#">
                <input type="text" placeholder="Search Products...">
                <button><i class="icon-search"></i></button>
            </form>
        </div>
        <div class="tpsideinfo__nabtab">
            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">Menu</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Categories</button>
                </li>
            </ul>
            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab" tabindex="0">
                    <div class="mobile-menu"></div>
                </div>
                <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab" tabindex="0">
                    <div class="tpsidebar-categories">
                        <ul>
                            <li><a href="#">Dairy Farm</a></li>
                            <li><a href="#">Healthy Foods</a></li>
                            <li><a href="#">Lifestyle</a></li>
                            <li><a href="#">Organics</a></li>
                            <li><a href="#">Photography</a></li>
                            <li><a href="#">Shopping</a></li>
                            <li><a href="#">Tips & Tricks</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="tpsideinfo__account-link">
            <a href="login.php"><i class="icon-user icons"></i> Login / Register</a>
        </div>
        <div class="tpsideinfo__wishlist-link">
            <a href="#" target="_parent"><i class="icon-heart"></i> Wishlist</a>
        </div>
    </div>
    <!-- sidebar-menu-area-end -->
</header>
<!-- header-area-end -->