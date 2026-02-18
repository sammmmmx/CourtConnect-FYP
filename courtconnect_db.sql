-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 18, 2026 at 06:48 PM
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
-- Database: `courtconnect_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `court_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `booking_date` date NOT NULL,
  `start_time` time NOT NULL,
  `duration_hours` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `account_number` varchar(50) DEFAULT NULL,
  `contact_number` varchar(15) DEFAULT NULL,
  `special_requests` text DEFAULT NULL,
  `status` enum('Pending','Confirmed','Cancelled') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expiry_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `court_id`, `user_id`, `booking_date`, `start_time`, `duration_hours`, `total_price`, `bank_name`, `account_number`, `contact_number`, `special_requests`, `status`, `created_at`, `expiry_time`) VALUES
(1, 2, 2, '2026-02-19', '12:00:00', 1, 22.00, NULL, NULL, NULL, NULL, 'Confirmed', '2026-02-18 15:19:17', '2026-02-18 23:26:17'),
(2, 6, 2, '2026-02-19', '12:00:00', 1, 22.00, NULL, NULL, NULL, NULL, 'Cancelled', '2026-02-18 15:22:01', '2026-02-18 23:29:01'),
(3, 2, 2, '2026-02-19', '15:00:00', 1, 22.00, NULL, NULL, NULL, NULL, 'Confirmed', '2026-02-18 15:55:18', '2026-02-19 00:02:18'),
(4, 3, 2, '2026-02-20', '15:00:00', 2, 44.00, NULL, NULL, NULL, NULL, 'Confirmed', '2026-02-18 16:05:25', '2026-02-19 00:12:25'),
(5, 10, 2, '2026-02-23', '19:00:00', 2, 36.00, NULL, NULL, NULL, NULL, 'Pending', '2026-02-18 16:07:05', '2026-02-19 00:14:05');

-- --------------------------------------------------------

--
-- Table structure for table `courts`
--

CREATE TABLE `courts` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` enum('VVIP','VIP','Normal') NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('Available','Not Available') DEFAULT 'Available',
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courts`
--

INSERT INTO `courts` (`id`, `name`, `type`, `price`, `description`, `status`, `image`) VALUES
(1, 'Court 1', 'VIP', 22.00, 'Red Court', 'Available', NULL),
(2, 'Court 2', 'VIP', 22.00, 'Blue Court', 'Available', NULL),
(3, 'Court 3', 'VIP', 22.00, 'Purple Court', 'Available', NULL),
(4, 'Court 4', 'VIP', 22.00, 'Orange Court', 'Available', NULL),
(5, 'Court 5', 'Normal', 18.00, 'Green Court', 'Available', NULL),
(6, 'Court 6', 'VIP', 22.00, 'Red Court', 'Available', NULL),
(7, 'Court 7', 'VIP', 22.00, 'Blue Court', 'Available', NULL),
(8, 'Court 8', 'VIP', 22.00, 'Purple', 'Available', NULL),
(9, 'Court 9', 'VIP', 22.00, 'Orange Court', 'Available', NULL),
(10, 'Court 10', 'Normal', 18.00, 'Green Court', 'Available', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_admin` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password`, `created_at`, `is_admin`) VALUES
(1, 'Admin SuperSports', 'admin.ss@gmail.com', '01789456123', '$2a$12$dPhqFpdKyHhOYeYdMJg3eOo4ApkAhkFCJFz0N.5vl3wGYfmYnPPuu', '2025-08-29 16:00:00', 1),
(2, 'User1', 'user1@gmail.com', '0123456789', '$2y$10$ddfaeUXyfRgv2dEvkEFieOZNIYhe23dePTLqns2T.jDI6vHu7pBIS', '2026-02-18 14:52:42', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `court_id` (`court_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `courts`
--
ALTER TABLE `courts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `courts`
--
ALTER TABLE `courts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`court_id`) REFERENCES `courts` (`id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
