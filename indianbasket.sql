-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Feb 20, 2025 at 03:07 PM
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
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `reset_token_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `firstname`, `lastname`, `email`, `password`, `gender`, `google_id`, `profile_pic`, `provider`, `reset_token`, `reset_token_expiry`) VALUES
(1, 'Chaitali', 'Merai', 'chaitalimerai3694@gmail.com', '$2y$10$X2fiRPBIDD2WPvn/NNYp1uFII5io9WbfgpimCAxB0EtqSVr/Pto2.', 'female', NULL, NULL, 'manual', NULL, NULL),
(3, 'Kshitij', 'Sharma', 'kshitijsharma@gmail.com', '$2y$10$a1XCj1pTl4r6Tob0.kgM5Ol9Bw9tVYMUWEAE.yIpmsEXx/eaSa.1u', 'male', NULL, NULL, 'manual', '9ab643c224931744fbde755abe5f241eaf2e8841e2e3160731bc96e61ab5164b', '2025-02-14 05:28:37'),
(10, 'john', 'doe', 'johndoe@gmail.com', '$2y$10$5aGfNibFfr1UHEskRdYPZuyAq43akVnohiJJnm6tBBvNPMCEk1hYG', 'other', NULL, NULL, 'manual', NULL, NULL),
(26, 'Sapana', 'Merai', 'sapana@gmail.com', '$2y$10$EuKkwWDbSETiZd0901CuWOk7cMgQ5kv1ysZ5Ti0ndDv2CCpltdb1O', 'Male', NULL, NULL, 'manual', NULL, NULL),
(27, 'Sweta', 'Merai', 'swetamerai@gmail.com', '$2y$10$qlpvZxFitwY0NQStXvPB.O62vHnLPw2s5A3uyHoVnlR4AAsYBXvvS', 'Male', NULL, NULL, 'manual', NULL, NULL),
(28, 'sachi', 'Merai', 'sachimerai@gmail.com', '$2y$10$WbS9a/6kJO4.xZnhQSq2pepdGNwP0yBWRLLrV61De/p0YBOBizIam', 'Female', NULL, NULL, 'manual', NULL, NULL),
(29, 'Nisha', 'Patel', 'nishapatel@gmail.com', '$2y$10$qEohUzX8BC33hSIKyT9VDekdJ5F12XmEUKRc8fRy7riCGy7P/d0G.', 'Other', NULL, NULL, 'manual', NULL, NULL),
(33, 'KSHITIJ', 'SHARMA', 'kshitijsharma94@gmail.com', NULL, NULL, '116540217674636277101', 'https://lh3.googleusercontent.com/a/ACg8ocJTh58Te70CBs9ulWwkOLBC2dbmZws50PTGuzDl3CQX09B10fVS=s96-c', 'google', NULL, NULL);

--
-- Indexes for dumped tables
--

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
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
