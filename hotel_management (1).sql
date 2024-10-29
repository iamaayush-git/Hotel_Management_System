-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 29, 2024 at 04:30 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hotel_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `food_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `delivery_location` varchar(255) NOT NULL,
  `location_number` varchar(50) NOT NULL,
  `order_time` datetime DEFAULT current_timestamp(),
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `image_url` varchar(255) DEFAULT NULL,
  `food_name` varchar(255) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `order_status` enum('Pending','Completed','Cancelled') NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `food_items`
--

CREATE TABLE `food_items` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `food_items`
--

INSERT INTO `food_items` (`id`, `name`, `description`, `image_url`, `price`) VALUES
(1, 'Margherita Pizza', 'Classic cheese and tomato pizza with fresh basil.', './assets/foodImg/margherita_pizza.jpg', 8.99),
(2, 'Caesar Salad', 'Crisp romaine lettuce with parmesan cheese and Caesar dressing.', './assets/foodImg/caesar_salad.jpg', 6.50),
(3, 'Spaghetti Bolognese', 'Pasta with a rich and hearty meat sauce.', './assets/foodImg/spaghetti_bolognese.jpg', 10.99),
(4, 'Grilled Chicken Sandwich', 'Grilled chicken with lettuce and tomato on a toasted bun.', './assets/foodImg/grilled_chicken_sandwich.jpg', 7.50),
(5, 'Cheeseburger', 'Juicy beef patty with cheese, lettuce, and tomato on a brioche bun.', './assets/foodImg/cheeseburger.jpg', 9.00),
(6, 'Vegetable Stir Fry', 'Mixed vegetables stir-fried with soy sauce and ginger.', './assets/foodImg/vegetable_stir_fry.jpg', 8.00),
(7, 'Fish Tacos', 'Soft tacos filled with grilled fish, slaw, and spicy mayo.', './assets/foodImg/fish_tacos.jpg', 9.50),
(8, 'Chicken Wings', 'Crispy wings tossed in a spicy buffalo sauce.', './assets/foodImg/chicken_wings.jpg', 6.99),
(9, 'Chocolate Brownie', 'Rich chocolate brownie served with vanilla ice cream.', './assets/foodImg/chocolate_brownie.jpg', 4.99);

-- --------------------------------------------------------

--
-- Table structure for table `food_order`
--

CREATE TABLE `food_order` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `food_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `delivery_location` varchar(255) NOT NULL,
  `location_number` varchar(50) NOT NULL,
  `order_time` datetime NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `food_name` varchar(100) DEFAULT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `order_status` enum('Pending','Confirmed','Cancelled') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `food_order`
--

INSERT INTO `food_order` (`id`, `user_id`, `food_id`, `quantity`, `name`, `email`, `delivery_location`, `location_number`, `order_time`, `price`, `image_url`, `food_name`, `total_price`, `order_status`) VALUES
(1, 11, 2, 2, 'aayush', 'aayush@gmail.com', 'Room', '105', '0000-00-00 00:00:00', 0.00, NULL, NULL, 0.00, 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `guest_name` varchar(100) NOT NULL,
  `room_id` int(11) NOT NULL,
  `check_in_date` date NOT NULL,
  `check_out_date` date NOT NULL,
  `booking_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Pending','Confirmed') DEFAULT 'Pending',
  `room_number` varchar(50) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`id`, `guest_name`, `room_id`, `check_in_date`, `check_out_date`, `booking_date`, `status`, `room_number`, `user_id`) VALUES
(58, 'Aayush', 13, '2024-10-28', '2024-10-29', '2024-10-28 11:49:07', 'Pending', '104', 11);

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `room_number` varchar(10) NOT NULL,
  `room_type` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `availability` enum('available','not available') NOT NULL,
  `image_url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `room_number`, `room_type`, `price`, `availability`, `image_url`) VALUES
(12, '102', 'single', 10.00, 'available', './assets/demo-6.jpg'),
(13, '104', 'deluxe', 5000.00, 'available', './assets/demo-4.jpg'),
(14, '106', 'suite', 5000.00, 'available', './assets/demo-5.jpg'),
(15, '103', 'deluxe', 5000.00, 'available', './assets/demo-3.jpg'),
(16, '105', 'suite', 10.00, 'available', './assets/demo-1.jpg'),
(17, '101', 'double', 10.00, 'available', './assets/demo-2.jpg'),
(18, '107', 'single', 200.00, 'available', './assets/demo-6.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL,
  `is_banned` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `is_banned`) VALUES
(11, 'user', 'user@gmail.com', '1', 'user', 0),
(13, 'admin', 'admin@gmail.com', '1', 'admin', 0),
(14, 'userb', 'userb@gmail.com', '1', 'user', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `food_items`
--
ALTER TABLE `food_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `food_order`
--
ALTER TABLE `food_order`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `food_items`
--
ALTER TABLE `food_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `food_order`
--
ALTER TABLE `food_order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
