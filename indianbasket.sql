-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 06, 2025 at 01:50 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

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
(11, 35, 'Kshitij', 'Sharma', 'kshitijinfobyte@gmail.com', '647-529-9059', 'Canada', 'Ontario', 'Waterloo', '82 Weber Street South', 'N2J 1Z9', 'Landmark', 'Home');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(8, 5, 3, 2, 50.00);

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
(5, 34, '2025-03-18 22:00:00', 100.00, 'Delivered');

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
  `image` varchar(255) DEFAULT 'assets/img/product/default-product.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `name`, `category_id`, `price`, `stock`, `description`, `image`) VALUES
(1, 'Mangosteen Organic From Vietnamese', NULL, 162.80, 100, 'Fresh organic mangosteen from Vietnam.', 'assets/img/product/product1.png'),
(2, 'Quaker Popped Rice Crisps Snacks', NULL, 162.80, 200, 'Delicious chocolate-flavored rice crisps.', 'assets/img/product/product2.png'),
(3, 'HOT - Lettuce Fresh Produce', NULL, 50.00, 150, 'Fresh lettuce for salads and sandwiches.', 'assets/img/product/product3.png'),
(4, 'Organic Apples', NULL, 30.00, 300, 'Fresh organic apples.', 'assets/img/product/product4.png'),
(5, 'Bananas', NULL, 20.00, 500, 'Ripe and sweet bananas.', 'assets/img/product/product5.png');

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
(34, 'Admin', 'User', 'admin@bigbasket.com', '$2y$10$qVOL7N3vFzdSNDCI.bIiE.WtuhWv53MUDeiWG82TPrWi1cvVZIM8q', NULL, NULL, NULL, 'manual', NULL, NULL, 'admin'),
(35, 'kshitij', 'sharmaa', 'sf11@grr.la', '$2y$10$uD2SxMqJEgbWATDvx4wZHuRrLOzqeurYMu8mxwYKMQmnjZ/GANbrS', 'Male', NULL, NULL, 'manual', NULL, NULL, 'user'),
(36, 'kshitij', 'sharma', 'sf12@grr.la', '$2y$10$B3SsYW/7jrXcXOnpQxFAeOocs4F5kiGcQKbKMp2TPEKfINPbBjqGy', 'Male', NULL, NULL, 'manual', NULL, NULL, 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `billing_addresses`
--
ALTER TABLE `billing_addresses`
  ADD PRIMARY KEY (`address_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `billing_addresses`
--
ALTER TABLE `billing_addresses`
  MODIFY `address_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `billing_addresses`
--
ALTER TABLE `billing_addresses`
  ADD CONSTRAINT `billing_addresses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
