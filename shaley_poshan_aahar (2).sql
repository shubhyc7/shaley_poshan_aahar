-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jan 30, 2026 at 09:14 AM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 7.4.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `shaley_poshan_aahar`
--

-- --------------------------------------------------------

--
-- Table structure for table `daily_aahar_entries`
--

CREATE TABLE `daily_aahar_entries` (
  `id` int(11) NOT NULL,
  `category` enum('1-5','6-8') NOT NULL,
  `entry_date` date DEFAULT NULL,
  `total_students` int(11) DEFAULT NULL,
  `present_students` int(11) DEFAULT NULL,
  `is_disable` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `daily_aahar_entries_items`
--

CREATE TABLE `daily_aahar_entries_items` (
  `id` int(11) NOT NULL,
  `daily_aahar_entries_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `item_rates` decimal(20,5) DEFAULT NULL,
  `qty` decimal(20,5) DEFAULT NULL,
  `is_disable` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` int(11) NOT NULL,
  `item_name` varchar(50) DEFAULT NULL,
  `item_type` enum('MAIN','SUPPORT') DEFAULT NULL,
  `unit` varchar(20) DEFAULT NULL,
  `is_disable` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `item_name`, `item_type`, `unit`, `is_disable`, `created_at`, `updated_at`) VALUES
(1, 'तांदूळ', 'MAIN', 'ग्रॅम', 0, '2026-01-27 08:09:09', '2026-01-27 08:09:09'),
(2, 'मुंगडाळ', 'MAIN', 'ग्रॅम', 0, '2026-01-27 08:39:19', '2026-01-27 08:59:13'),
(3, 'तूर डाळ', 'MAIN', 'ग्रॅम', 0, '2026-01-27 08:41:08', '2026-01-27 08:59:27'),
(4, 'मसूर', 'SUPPORT', 'ग्रॅम', 0, '2026-01-27 08:41:23', '2026-01-27 08:41:23'),
(5, 'मटकी', 'SUPPORT', 'ग्रॅम', 0, '2026-01-27 08:41:35', '2026-01-27 08:41:35'),
(6, 'मूळ', 'SUPPORT', 'ग्रॅम', 0, '2026-01-27 08:41:45', '2026-01-27 08:41:45'),
(7, 'चवली', 'SUPPORT', 'ग्रॅम', 0, '2026-01-27 08:41:59', '2026-01-27 08:41:59'),
(8, 'हरभरा', 'SUPPORT', 'ग्रॅम', 0, '2026-01-27 08:42:10', '2026-01-27 08:42:10'),
(9, 'वाटाणा', 'SUPPORT', 'ग्रॅम', 0, '2026-01-27 08:42:18', '2026-01-27 08:42:18'),
(10, 'जिरा', 'SUPPORT', 'ग्रॅम', 0, '2026-01-27 08:42:33', '2026-01-27 08:42:33'),
(11, 'मोहरी', 'SUPPORT', 'ग्रॅम', 0, '2026-01-27 08:42:42', '2026-01-27 08:42:42'),
(12, 'हळद', 'SUPPORT', 'ग्रॅम', 0, '2026-01-27 08:43:09', '2026-01-27 08:43:09'),
(13, 'मिरची पोउदर', 'SUPPORT', 'ग्रॅम', 0, '2026-01-27 08:43:38', '2026-01-27 08:43:38'),
(14, 'सोयाबीन तेल', 'SUPPORT', 'ग्रॅम', 0, '2026-01-27 08:43:48', '2026-01-27 08:43:48'),
(15, 'मीठ', 'SUPPORT', 'ग्रॅम', 0, '2026-01-27 08:44:00', '2026-01-27 08:44:00'),
(16, 'मसाला', 'SUPPORT', 'ग्रॅम', 0, '2026-01-27 08:44:07', '2026-01-27 08:44:07'),
(17, 'सोयावदी', 'SUPPORT', 'ग्रॅम', 0, '2026-01-27 08:44:27', '2026-01-27 08:44:27');

-- --------------------------------------------------------

--
-- Table structure for table `item_rates`
--

CREATE TABLE `item_rates` (
  `id` int(11) NOT NULL,
  `category` enum('1-5','6-8') NOT NULL,
  `item_id` int(11) DEFAULT NULL,
  `per_student_qty` decimal(20,5) DEFAULT NULL,
  `is_disable` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `item_rates`
--

INSERT INTO `item_rates` (`id`, `category`, `item_id`, `per_student_qty`, `is_disable`, `created_at`, `updated_at`) VALUES
(1, '1-5', 1, '0.10000', 0, '2026-01-27 08:09:43', '2026-01-27 08:51:28'),
(2, '1-5', 1, '0.10000', 0, '2026-01-27 08:46:28', '2026-01-27 08:46:28'),
(3, '1-5', 2, '0.02000', 0, '2026-01-27 08:46:56', '2026-01-27 08:46:56'),
(4, '1-5', 3, '0.01000', 0, '2026-01-27 08:47:08', '2026-01-27 08:47:08'),
(5, '1-5', 4, '0.02000', 0, '2026-01-27 08:47:29', '2026-01-27 08:47:29'),
(6, '1-5', 5, '0.02000', 0, '2026-01-27 08:48:00', '2026-01-27 08:48:00'),
(7, '1-5', 6, '0.01000', 0, '2026-01-27 08:48:24', '2026-01-27 08:48:24'),
(8, '1-5', 7, '0.02000', 0, '2026-01-27 08:49:03', '2026-01-27 08:49:03'),
(9, '1-5', 9, '0.02000', 0, '2026-01-27 08:49:14', '2026-01-27 08:49:14'),
(10, '1-5', 17, '0.02000', 0, '2026-01-27 08:49:29', '2026-01-27 08:49:29'),
(11, '1-5', 10, '0.00200', 0, '2026-01-27 08:50:13', '2026-01-27 08:50:13');

-- --------------------------------------------------------

--
-- Table structure for table `stock_transactions`
--

CREATE TABLE `stock_transactions` (
  `id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `transaction_type` enum('OPENING','IN','OUT') NOT NULL,
  `daily_aahar_entries_id` int(11) DEFAULT NULL,
  `quantity` decimal(20,5) NOT NULL,
  `transaction_date` date NOT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `is_disable` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `student_strength`
--

CREATE TABLE `student_strength` (
  `id` int(11) NOT NULL,
  `category` enum('1-5','6-8') NOT NULL,
  `total_students` int(11) DEFAULT NULL,
  `is_disable` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `role` enum('ADMIN','TEACHER') DEFAULT 'TEACHER',
  `is_disable` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `role`, `is_disable`, `created_at`) VALUES
(1, 'admin', '$2y$10$iORWvI3L3Sb6PqfkCi8ypO/SF5aIq2C95TpQGHLvqxW3pxfO5oANK', 'System Admin', 'ADMIN', 0, '2026-01-26 12:29:03');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `daily_aahar_entries`
--
ALTER TABLE `daily_aahar_entries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `daily_aahar_entries_items`
--
ALTER TABLE `daily_aahar_entries_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `daily_aahar_entries_fk` (`daily_aahar_entries_id`),
  ADD KEY `items_fk` (`item_id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `item_rates`
--
ALTER TABLE `item_rates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_rates_item_fk` (`item_id`);

--
-- Indexes for table `stock_transactions`
--
ALTER TABLE `stock_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `transaction_date` (`transaction_date`),
  ADD KEY `daily_aahar_entries_id_fk` (`daily_aahar_entries_id`);

--
-- Indexes for table `student_strength`
--
ALTER TABLE `student_strength`
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
-- AUTO_INCREMENT for table `daily_aahar_entries`
--
ALTER TABLE `daily_aahar_entries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `daily_aahar_entries_items`
--
ALTER TABLE `daily_aahar_entries_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `item_rates`
--
ALTER TABLE `item_rates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `stock_transactions`
--
ALTER TABLE `stock_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_strength`
--
ALTER TABLE `student_strength`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `daily_aahar_entries_items`
--
ALTER TABLE `daily_aahar_entries_items`
  ADD CONSTRAINT `daily_aahar_entries_fk` FOREIGN KEY (`daily_aahar_entries_id`) REFERENCES `daily_aahar_entries` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `items_fk` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `item_rates`
--
ALTER TABLE `item_rates`
  ADD CONSTRAINT `item_rates_item_fk` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `stock_transactions`
--
ALTER TABLE `stock_transactions`
  ADD CONSTRAINT `daily_aahar_entries_id_fk` FOREIGN KEY (`daily_aahar_entries_id`) REFERENCES `daily_aahar_entries` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `stock_transactions_item_fk` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
