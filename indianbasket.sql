-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 25, 2025 at 04:33 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `indianbasket`
--

-- --------------------------------------------------------

--
-- Table structure for table `banners`
--

CREATE TABLE `banners` (
  `id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `title` varchar(255) NOT NULL,
  `meta_description` text DEFAULT NULL,
  `keywords` text DEFAULT NULL,
  `target_url` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `banners`
--

INSERT INTO `banners` (`id`, `image`, `created_at`, `title`, `meta_description`, `keywords`, `target_url`, `status`) VALUES
(3, 'uploads/banners/1742501079_slider-bg-1.png', '2025-03-20 20:04:39', 'Alt', 'Meta Test', 'test, test1', '', 'inactive'),
(4, 'uploads/banners/1742618876_slider-bg-1.png', '2025-03-22 04:47:56', 'Banner image', 'Meta Meta', 'test, test1', '', 'inactive');

-- --------------------------------------------------------

--
-- Table structure for table `billing_addresses`
--

CREATE TABLE `billing_addresses` (
  `address_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `country` varchar(100) NOT NULL,
  `province` varchar(100) NOT NULL,
  `city` varchar(100) NOT NULL,
  `street_address` varchar(255) NOT NULL,
  `zip_code` varchar(20) NOT NULL,
  `landmark` varchar(255) DEFAULT NULL,
  `address_type` enum('Home','Office') NOT NULL DEFAULT 'Home'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `billing_addresses`
--

INSERT INTO `billing_addresses` (`address_id`, `user_id`, `firstname`, `lastname`, `email`, `phone`, `country`, `province`, `city`, `street_address`, `zip_code`, `landmark`, `address_type`) VALUES
(2, 33, 'Jane', 'Smith', 'janesmith@example.com', '0987654321', 'Canada', 'Ontario', 'Toronto', '456 Elm St', 'M5H 2N2', 'Opposite City Hall', 'Office'),
(3, 33, 'Alice', 'Johnson', 'alicej@example.com', '5551234567', 'USA', 'New York', 'New York', '789 Broadway', '10001', 'Next to Times Square', 'Home'),
(4, 33, 'Bob', 'Brown', 'bobbrown@example.com', '4449876543', 'UK', 'England', 'London', '321 Oxford St', 'W1D 1AB', 'Near Piccadilly Circus', 'Office'),
(6, 33, 'Chaitali', 'Merai', 'meraichaitali75@gmail.com', '4379719758', 'Canada', 'ON', 'Kitchener', '5 john Russell lane', 'N2H0B4', 'test', 'Home'),
(8, 33, 'Aasif', 'Malik', 'aasifmalik@gmail.com', '4379711234', 'Canada', 'ON', 'Kitchener', '5 john Russell lane', '123456', 'tets', 'Office'),
(10, 35, 'Kshitij', 'Sharma', 'kshitijinfobyte@gmail.com', '+919888886539', 'Canada', 'Ontario', 'Waterlo', '82 Weber Street South', 'N2J 1Z9', 'Landmark', 'Home'),
(12, 37, 'Chaitalis', 'Merais', 'meraichaitalis75@gmail.com', '123456789', 'Canada', 'ONs', 'Kitcheners', '5 john Russell lanes', 'N2H0B5', '', 'Home');

-- --------------------------------------------------------

--
-- Table structure for table `blogs`
--

CREATE TABLE `blogs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `content` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `meta_description` text DEFAULT NULL,
  `meta_keywords` varchar(255) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blogs`
--

INSERT INTO `blogs` (`id`, `user_id`, `title`, `meta_title`, `content`, `image`, `status`, `created_at`, `meta_description`, `meta_keywords`, `slug`) VALUES
(2, 34, 'Title2', 'Meta Title1', 'Content2', 'uploads/blogs/blog-bg-1.jpg', 'inactive', '2025-03-23 17:09:26', 'Meta2', 'keyword2', 'Slug2'),
(3, 34, '10 Essential Indian Spices Every Kitchen Should Have', 'Essential Indian Spices', 'Indian cuisine is renowned for its rich flavors and aromatic dishes, all thanks to the wonderful spices used. Here are 10 essential Indian spices you should always have in your kitchen:\r\n1. Turmeric (Haldi) - The golden spice with anti-inflammatory properties\r\n2. Cumin (Jeera) - Earthy flavor, great for tempering\r\n3. Coriander (Dhania) - Fresh citrusy notes\r\n4. Cardamom (Elaichi) - Sweet and floral\r\n5. Cinnamon (Dalchini) - Warm and sweet\r\n6. Cloves (Laung) - Intense and pungent\r\n7. Mustard Seeds (Rai) - For that perfect tadka\r\n8. Fenugreek (Methi) - Distinct bitter-sweet flavor\r\n9. Asafoetida (Hing) - Adds umami flavor\r\n10. Red Chili Powder - For that essential heat\r\n\r\nStock up on these spices at Indian Basket today!', 'uploads/blogs/blog-bg-2.jpg', 'active', '2025-03-20 14:00:00', 'Discover the 10 essential Indian spices that will transform your cooking and bring authentic flavors to your kitchen.', 'indian spices, cooking essentials, indian cuisine, kitchen must-haves', 'essential-indian-spices'),
(4, 34, 'How to Store Fresh Vegetables for Maximum Shelf Life', 'Vegetable Storage Tips', 'Proper storage can significantly extend the life of your fresh vegetables. Here are our top tips:\r\n1. Leafy Greens: Wrap in paper towels and store in airtight containers\r\n2. Root Vegetables: Store in cool, dark places (not necessarily the fridge)\r\n3. Tomatoes: Keep at room temperature until ripe\r\n4. Onions & Garlic: Need air circulation - store in mesh bags\r\n5. Potatoes: Keep away from onions to prevent sprouting\r\n6. Cucumbers: Store at room temperature for short term, fridge for longer storage\r\n7. Eggplants: Prefer slightly cooler temperatures but not cold\r\n8. Peppers: Can be stored in fridge crisper drawer\r\n\r\nFollow these tips to reduce waste and enjoy fresh produce longer from your Indian Basket purchases!', 'uploads/blogs/blog-bg-3.jpg', 'active', '2025-03-18 18:30:00', 'Learn professional techniques to store fresh vegetables properly and make them last longer in your kitchen.', 'vegetable storage, fresh produce, food preservation, reduce waste', 'vegetable-storage-tips'),
(5, 37, 'The Health Benefits of Traditional Indian Superfoods', 'Indian Superfoods Benefits', 'India has been using superfoods for centuries before they became trendy worldwide. Here are some powerful ones:\r\n1. Moringa (Drumstick leaves) - Packed with nutrients\r\n2. Turmeric - Powerful anti-inflammatory\r\n3. Ghee - Healthy fats for brain function\r\n4. Amla (Indian Gooseberry) - Vitamin C powerhouse\r\n5. Jaggery - Mineral-rich natural sweetener\r\n6. Bajra (Pearl Millet) - Gluten-free grain\r\n7. Ragi (Finger Millet) - High in calcium\r\n8. Sabja Seeds (Basil Seeds) - Great for digestion\r\n\r\nIncorporate these into your diet for natural health benefits. All available at Indian Basket!', 'uploads/blogs/blog-bg-4.jpg', 'active', '2025-03-15 13:15:00', 'Discover the amazing health benefits of traditional Indian superfoods that have been used for centuries.', 'indian superfoods, health benefits, traditional foods, nutrition', 'indian-superfoods-benefits'),
(6, 34, '5 Easy Indian Snacks You Can Make in Under 15 Minutes', 'Quick Indian Snack Recipes', 'Craving something delicious but short on time? Try these quick Indian snacks:\r\n1. Masala Peanuts - Toss roasted peanuts with chaat masala\r\n2. Besan Chilla - Savory gram flour pancakes\r\n3. Sprout Chaat - Healthy and protein-packed\r\n4. Microwave Poha - Flattened rice with spices\r\n5. Fruit Chaat - Seasonal fruits with chaat masala\r\n\r\nAll ingredients available at Indian Basket. Perfect for unexpected guests or sudden cravings!', 'uploads/blogs/blog-bg-5.jpg', 'active', '2025-03-10 20:45:00', 'These 5 easy Indian snack recipes can be prepared in under 15 minutes using ingredients from Indian Basket.', 'quick snacks, indian recipes, easy cooking, instant snacks', 'quick-indian-snacks'),
(7, 37, 'Festival Special: How to Make Perfect Diwali Sweets at Home', 'Diwali Sweet Recipes', 'Diwali is incomplete without sweets! Here are some traditional recipes:\r\n1. Besan Ladoo - Roasted gram flour sweets\r\n2. Kaju Katli - Cashew fudge slices\r\n3. Gulab Jamun - Soft milk dumplings in syrup\r\n4. Coconut Barfi - Sweet coconut squares\r\n5. Soan Papdi - Flaky layered sweet\r\n\r\nWe have all the ingredients you need - premium quality ghee, fresh nuts, pure khoya, and more at Indian Basket!', 'uploads/blogs/blog-bg-6.jpg', 'active', '2025-03-05 16:20:00', 'Learn to make perfect traditional Diwali sweets at home with ingredients from Indian Basket.', 'diwali sweets, festival recipes, indian desserts, homemade mithai', 'diwali-sweet-recipes');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `item_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `name`, `image`, `item_count`) VALUES
(1, 'Vegetables', 'category-1.jpg', 0),
(3, 'Fresh Fruits', 'category-2.jpg', 0),
(4, 'Fruit Drink', 'category-3.jpg', 0),
(5, 'Fresh Bakery', 'category-4.jpg', 0),
(6, 'Biscuits Snack', 'category-5.jpg', 0),
(7, 'Fresh Meat', 'category-6.jpg', 0),
(8, 'Fresh Milk', 'category-7.jpg', 0),
(9, 'Sea Foods', 'category-8 (1).jpg', 0);

-- --------------------------------------------------------

--
-- Table structure for table `orderdetails`
--

CREATE TABLE `orderdetails` (
  `order_detail_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orderdetails`
--

INSERT INTO `orderdetails` (`order_detail_id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 1, 2, 162.80),
(2, 1, 2, 1, 162.80),
(3, 2, 1, 1, 162.80),
(4, 3, 3, 1, 50.00),
(5, 4, 4, 2, 30.00),
(6, 4, 5, 1, 20.00),
(7, 5, 1, 1, 162.80),
(8, 5, 3, 2, 50.00),
(9, 6, 1, 1, 162.80),
(10, 6, 2, 1, 162.80),
(11, 7, 3, 3, 50.00),
(12, 8, 4, 5, 30.00),
(13, 8, 5, 2, 20.00),
(14, 9, 1, 1, 162.80),
(15, 10, 2, 2, 162.80);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('Pending','Shipped','Delivered','Cancelled') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `order_date`, `total_amount`, `status`) VALUES
(1, 34, '2025-03-08 15:00:00', 325.60, 'Delivered'),
(2, 34, '2025-03-10 16:00:00', 162.80, 'Pending'),
(3, 34, '2025-03-12 18:00:00', 50.00, 'Cancelled'),
(4, 34, '2025-03-15 20:00:00', 80.00, 'Shipped'),
(5, 34, '2025-03-18 22:00:00', 100.00, 'Delivered'),
(6, 37, '2025-03-20 14:00:00', 200.00, 'Pending'),
(7, 37, '2025-03-21 15:00:00', 150.00, 'Shipped'),
(8, 37, '2025-03-22 16:00:00', 300.00, 'Delivered'),
(9, 37, '2025-03-23 17:00:00', 100.00, 'Cancelled'),
(10, 37, '2025-03-24 18:00:00', 250.00, 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT 'assets/img/product/default-product.png',
  `material` varchar(255) DEFAULT NULL,
  `legs` varchar(255) DEFAULT NULL,
  `dimensions` varchar(255) DEFAULT NULL,
  `length` varchar(50) DEFAULT NULL,
  `depth` varchar(50) DEFAULT NULL,
  `additional_details` text DEFAULT NULL,
  `video_url` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `mfg_date` date DEFAULT NULL,
  `life_days` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `name`, `category_id`, `price`, `stock`, `description`, `image`, `material`, `legs`, `dimensions`, `length`, `depth`, `additional_details`, `video_url`, `type`, `mfg_date`, `life_days`) VALUES
(1, 'Mangosteen Organic From Vietnamese', 3, 162.80, 100, 'Designed by Puik in 1949 as one of the first models created especially for Carl Hansen & Son, and produced since 1950. The last of a series of chairs wegner designed based on inspiration from antique chinese armchairs. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia eserunt mollit anim id est laborum. Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, aque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.', 'product1.png', 'Plastic, Wood', 'Lacquered oak and black painted oak', 'Height: 80 cm, Weight: 5.3 kg', '48cm', '52 cm', 'Form is an armless modern chair with a minimalistic expression. With a simple and contemporary design Form Chair has a soft and welcoming ilhouette and a distinctly residential look. The legs appear almost as if they are growing out of the shell. This gives the design flexibility and makes it possible to vary the frame. Unika is a mouth blown series of small, glass pendant lamps, originally designed for the Restaurant Gronbech. Est eum itaque maiores qui blanditiis architecto. Eligendi saepe rem ut. Cumque quia earum eligendi.', 'https://www.youtube.com/watch?v=rLrV5Tel7zw', 'Organic', '2021-08-04', 60),
(2, 'Quaker Popped Rice Crisps Snacks', 1, 162.80, 200, 'Delicious chocolate-flavored rice crisps.', 'product2.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'HOT - Lettuce Fresh Produce', 1, 50.00, 150, 'Fresh lettuce for salads and sandwiches.', 'product3.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 'Organic Apples', 1, 30.00, 300, 'Fresh organic apples.', 'product4.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, 'Bananas test', 1, 200.00, 50, 'Ripe and sweet bananas. test', 'product5.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(12, 'test2', 1, 501.00, 20, 'test2', 'product6.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(13, 'test3', 1, 2000.00, 10, 'test3', 'product7.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(14, 'Fresh Beef', 9, 99.98, 9, 'These are the people who make your life easier. Large tiles were arranged on the counter top plate near the window of the living room, they were connected to the kitchen.', 'product8.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `image_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_images`
--

INSERT INTO `product_images` (`image_id`, `product_id`, `image_path`) VALUES
(1, 1, 'product1-image1.png'),
(2, 1, 'product1-image2.png'),
(3, 1, 'product1-image3.png'),
(4, 2, 'product2-image1.png'),
(5, 2, 'product2-image2.png');

-- --------------------------------------------------------

--
-- Table structure for table `recentlyviewed`
--

CREATE TABLE `recentlyviewed` (
  `viewed_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `viewed_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `gender` varchar(255) DEFAULT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `profile_pic` text DEFAULT NULL,
  `provider` enum('manual','google','facebook','twitter') NOT NULL DEFAULT 'manual',
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expiry` datetime DEFAULT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `firstname`, `lastname`, `email`, `password`, `gender`, `google_id`, `profile_pic`, `provider`, `reset_token`, `reset_token_expiry`, `role`) VALUES
(2, 'Chaitali', 'Merai', 'chaitalimerai3694@gmail.com', '$2y$10$X2fiRPBIDD2WPvn/NNYp1uFII5io9WbfgpimCAxB0EtqSVr/Pto2.', 'female', NULL, NULL, 'manual', NULL, NULL, 'user'),
(3, 'Kshitij', 'Sharma', 'kshitijsharma@gmail.com', '$2y$10$hP9bjWHuCLV3ryjJfZG6C.DsvO9NH1p0EzaW14yJA9.NL0pkxRRp6', 'male', NULL, NULL, 'manual', '9ab643c224931744fbde755abe5f241eaf2e8841e2e3160731bc96e61ab5164b', '2025-02-14 05:28:37', 'user'),
(10, 'john', 'doe', 'johndoe@gmail.com', '$2y$10$5aGfNibFfr1UHEskRdYPZuyAq43akVnohiJJnm6tBBvNPMCEk1hYG', 'other', NULL, NULL, 'manual', NULL, NULL, 'user'),
(26, 'Sapana', 'Merai', 'sapana@gmail.com', '$2y$10$EuKkwWDbSETiZd0901CuWOk7cMgQ5kv1ysZ5Ti0ndDv2CCpltdb1O', 'Male', NULL, NULL, 'manual', NULL, NULL, 'user'),
(27, 'Sweta', 'Merai', 'swetamerai@gmail.com', '$2y$10$qlpvZxFitwY0NQStXvPB.O62vHnLPw2s5A3uyHoVnlR4AAsYBXvvS', 'Male', NULL, NULL, 'manual', NULL, NULL, 'user'),
(29, 'Nisha', 'Patel', 'nishapatel@gmail.com', '$2y$10$qEohUzX8BC33hSIKyT9VDekdJ5F12XmEUKRc8fRy7riCGy7P/d0G.', 'Other', NULL, NULL, 'manual', NULL, NULL, 'user'),
(33, 'KSHITIJ', 'SHARMA', 'kshitijsharma94@gmail.com', NULL, NULL, '116540217674636277101', 'https://lh3.googleusercontent.com/a/ACg8ocJTh58Te70CBs9ulWwkOLBC2dbmZws50PTGuzDl3CQX09B10fVS=s96-c', 'google', NULL, NULL, 'user'),
(34, 'Admin', 'User', 'admin@bigbasket.com', '$2y$10$sGM9julK0MqW16Lt3cg3ve19De7G2Ny3qHAU98czkI8lsON1vgcd6', NULL, NULL, NULL, 'manual', NULL, NULL, 'admin'),
(35, 'kshitij', 'sharmaa', 'sf11@grr.la', '$2y$10$uD2SxMqJEgbWATDvx4wZHuRrLOzqeurYMu8mxwYKMQmnjZ/GANbrS', 'Male', NULL, NULL, 'manual', NULL, NULL, 'user'),
(36, 'kshitij', 'sharma', 'sf12@grr.la', '$2y$10$B3SsYW/7jrXcXOnpQxFAeOocs4F5kiGcQKbKMp2TPEKfINPbBjqGy', 'Male', NULL, NULL, 'manual', NULL, NULL, 'user'),
(37, 'Chaitalis', 'Merais', 'meraichaitali75@gmail.com', '$2y$10$sGM9julK0MqW16Lt3cg3ve19De7G2Ny3qHAU98czkI8lsON1vgcd6', 'Female', NULL, NULL, 'manual', NULL, NULL, 'user'),
(38, 'test2', 'test2', 'test@gmail.com', '$2y$10$FHTVBzxi0jWBzFYwYwwLoucadrB2k3ufkbdwiODDG/ZIxEtdhdSyi', NULL, NULL, 'assets/img/uploads/1741266995_banner-9.jpg', 'manual', NULL, NULL, 'user'),
(39, 'Disha', 'Does', 'john1@gmail.com', '$2y$10$.AweZ0iY5.eyR2nL.4kxMed73c5gOvLUk65Y/Rjef9ZN9ruG79YUu', NULL, NULL, '../assets/img/uploads/1741270785_banner-8.jpg', 'manual', NULL, NULL, 'user'),
(40, 'test', 'test', 'test1@gmail.com', '$2y$10$ZJKpFJLqoEiDCCt2XecJyOl8FvkiaK5rMsVhvajIOPtY7Oq.1/D7G', NULL, NULL, 'assets/img/uploads/1742414689_category-8 (1).jpg', 'manual', NULL, NULL, 'user'),
(41, 'Pooja', 'Merai', 'meraichaitali1912@yahoo.com', '$2y$10$o77JQ4Y5YC.Y71T/TEqS8OhLQkpNARLr3IUuzQG/i/ZtO1c0ng2jG', 'Female', NULL, NULL, 'manual', NULL, NULL, 'user');

-- --------------------------------------------------------

--
-- Table structure for table `user_carts`
--

CREATE TABLE `user_carts` (
  `user_id` int(11) NOT NULL,
  `cart_data` text NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_carts`
--

INSERT INTO `user_carts` (`user_id`, `cart_data`, `updated_at`) VALUES
(37, '[1]', '2025-03-25 00:15:44'),
(41, '[1]', '2025-03-25 00:36:20');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `wishlist_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`wishlist_id`, `user_id`, `product_id`, `created_at`) VALUES
(1, 41, 2, '2025-03-25 02:04:59'),
(2, 41, 1, '2025-03-25 03:00:30'),
(4, 37, 14, '2025-03-25 15:31:36');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `banners`
--
ALTER TABLE `banners`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `billing_addresses`
--
ALTER TABLE `billing_addresses`
  ADD PRIMARY KEY (`address_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `blogs`
--
ALTER TABLE `blogs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_carts`
--
ALTER TABLE `user_carts`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`wishlist_id`),
  ADD UNIQUE KEY `user_product_unique` (`user_id`,`product_id`),
  ADD KEY `wishlist_ibfk_2` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `banners`
--
ALTER TABLE `banners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `billing_addresses`
--
ALTER TABLE `billing_addresses`
  MODIFY `address_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `blogs`
--
ALTER TABLE `blogs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `wishlist_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `billing_addresses`
--
ALTER TABLE `billing_addresses`
  ADD CONSTRAINT `billing_addresses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `blogs`
--
ALTER TABLE `blogs`
  ADD CONSTRAINT `blogs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_carts`
--
ALTER TABLE `user_carts`
  ADD CONSTRAINT `fk_user_carts_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
