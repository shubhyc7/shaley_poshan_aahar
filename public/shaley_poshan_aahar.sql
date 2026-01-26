-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jan 26, 2026 at 07:53 AM
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
  `category` enum('6-8','9-10') NOT NULL,
  `entry_date` date DEFAULT NULL,
  `total_students` int(11) DEFAULT NULL,
  `present_students` int(11) DEFAULT NULL,
  `is_disable` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `daily_aahar_entries`
--

INSERT INTO `daily_aahar_entries` (`id`, `category`, `entry_date`, `total_students`, `present_students`, `is_disable`, `created_at`, `updated_at`) VALUES
(1, '6-8', '2026-01-01', 20, 5, 0, '2026-01-25 23:58:52', '2026-01-25 23:58:52'),
(2, '6-8', '2026-01-26', 20, 2, 0, '2026-01-26 00:14:13', '2026-01-26 00:14:13'),
(3, '6-8', '2026-01-02', 20, 10, 0, '2026-01-26 00:42:50', '2026-01-26 00:42:50');

-- --------------------------------------------------------

--
-- Table structure for table `daily_aahar_entries_items`
--

CREATE TABLE `daily_aahar_entries_items` (
  `id` int(11) NOT NULL,
  `daily_aahar_entries_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `qty` decimal(10,3) DEFAULT NULL,
  `is_disable` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `daily_aahar_entries_items`
--

INSERT INTO `daily_aahar_entries_items` (`id`, `daily_aahar_entries_id`, `item_id`, `qty`, `is_disable`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '0.500', 0, '2026-01-26 11:28:52', '2026-01-26 11:28:52'),
(2, 1, 2, '0.500', 0, '2026-01-26 11:28:52', '2026-01-26 11:28:52'),
(3, 1, 3, '0.500', 0, '2026-01-26 11:28:52', '2026-01-26 11:28:52'),
(4, 1, 4, '2.500', 0, '2026-01-26 11:28:52', '2026-01-26 11:28:52'),
(5, 1, 5, '1.000', 0, '2026-01-26 11:28:52', '2026-01-26 11:28:52'),
(6, 2, 1, '0.200', 0, '2026-01-26 11:44:13', '2026-01-26 11:44:13'),
(7, 2, 3, '0.200', 0, '2026-01-26 11:44:13', '2026-01-26 11:44:13'),
(8, 2, 4, '1.000', 0, '2026-01-26 11:44:13', '2026-01-26 11:44:13'),
(9, 2, 5, '0.400', 0, '2026-01-26 11:44:13', '2026-01-26 11:44:13'),
(10, 3, 1, '1.000', 0, '2026-01-26 12:12:50', '2026-01-26 12:12:50'),
(11, 3, 2, '1.000', 0, '2026-01-26 12:12:50', '2026-01-26 12:12:50'),
(12, 3, 3, '1.000', 0, '2026-01-26 12:12:50', '2026-01-26 12:12:50'),
(13, 3, 4, '5.000', 0, '2026-01-26 12:12:50', '2026-01-26 12:12:50'),
(14, 3, 5, '2.000', 0, '2026-01-26 12:12:50', '2026-01-26 12:12:50');

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` int(11) NOT NULL,
  `item_name` varchar(50) DEFAULT NULL,
  `item_type` enum('MAIN','SUPPORT') DEFAULT NULL,
  `unit` varchar(10) DEFAULT NULL,
  `is_disable` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `item_name`, `item_type`, `unit`, `is_disable`, `created_at`, `updated_at`) VALUES
(1, 'तांदूळ', 'MAIN', 'ग्रॅम', 0, '2026-01-25 23:52:33', '2026-01-25 23:52:33'),
(2, 'मूगडाळ', 'MAIN', 'ग्रॅम', 0, '2026-01-25 23:52:51', '2026-01-25 23:52:51'),
(3, 'तेल', 'SUPPORT', 'ग्रॅम', 0, '2026-01-25 23:53:02', '2026-01-25 23:53:02'),
(4, 'मीठ', 'SUPPORT', 'ग्रॅम', 0, '2026-01-25 23:53:08', '2026-01-25 23:53:08'),
(5, 'वाटाणा', 'SUPPORT', 'ग्रॅम', 0, '2026-01-25 23:53:16', '2026-01-25 23:53:16');

-- --------------------------------------------------------

--
-- Table structure for table `item_rates`
--

CREATE TABLE `item_rates` (
  `id` int(11) NOT NULL,
  `category` enum('6-8','9-10') NOT NULL,
  `item_id` int(11) DEFAULT NULL,
  `per_student_qty` decimal(10,3) DEFAULT NULL,
  `month` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `is_disable` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `item_rates`
--

INSERT INTO `item_rates` (`id`, `category`, `item_id`, `per_student_qty`, `month`, `year`, `is_disable`, `created_at`, `updated_at`) VALUES
(1, '6-8', 1, '0.100', 1, 2026, 0, '2026-01-25 23:53:32', '2026-01-25 23:53:32'),
(2, '6-8', 2, '0.100', 1, 2026, 0, '2026-01-25 23:53:42', '2026-01-25 23:53:42'),
(3, '6-8', 3, '0.100', 1, 2026, 0, '2026-01-25 23:53:51', '2026-01-25 23:53:51'),
(4, '6-8', 4, '0.500', 1, 2026, 0, '2026-01-25 23:53:59', '2026-01-25 23:53:59'),
(5, '6-8', 5, '0.200', 1, 2026, 0, '2026-01-25 23:54:08', '2026-01-25 23:54:08');

-- --------------------------------------------------------

--
-- Table structure for table `stock_transactions`
--

CREATE TABLE `stock_transactions` (
  `id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `transaction_type` enum('OPENING','IN','OUT') NOT NULL,
  `daily_aahar_entries_id` int(11) DEFAULT NULL,
  `quantity` decimal(10,3) NOT NULL,
  `transaction_date` date NOT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `is_disable` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `stock_transactions`
--

INSERT INTO `stock_transactions` (`id`, `item_id`, `transaction_type`, `daily_aahar_entries_id`, `quantity`, `transaction_date`, `remarks`, `is_disable`, `created_at`, `updated_at`) VALUES
(1, 1, 'OPENING', NULL, '2000.000', '2025-12-31', 'OPENING', 0, '2026-01-26 11:24:58', '2026-01-26 11:30:14'),
(2, 2, 'OPENING', NULL, '1000.000', '2025-12-31', 'OPENING', 0, '2026-01-26 11:25:12', '2026-01-26 11:30:46'),
(3, 3, 'OPENING', NULL, '1000.000', '2025-12-31', 'OPENING', 0, '2026-01-26 11:25:52', '2026-01-26 11:30:53'),
(4, 4, 'OPENING', NULL, '1000.000', '2025-12-31', 'OPENING', 0, '2026-01-26 11:26:03', '2026-01-26 11:30:40'),
(5, 5, 'OPENING', NULL, '1000.000', '2025-12-31', 'OPENING', 0, '2026-01-26 11:26:34', '2026-01-26 11:30:24'),
(6, 1, 'OUT', 1, '0.500', '2026-01-01', NULL, 0, '2026-01-26 11:28:52', '2026-01-26 11:28:52'),
(7, 2, 'OUT', 1, '0.500', '2026-01-01', NULL, 0, '2026-01-26 11:28:52', '2026-01-26 11:28:52'),
(8, 3, 'OUT', 1, '0.500', '2026-01-01', NULL, 0, '2026-01-26 11:28:52', '2026-01-26 11:28:52'),
(9, 4, 'OUT', 1, '2.500', '2026-01-01', NULL, 0, '2026-01-26 11:28:52', '2026-01-26 11:28:52'),
(10, 5, 'OUT', 1, '1.000', '2026-01-01', NULL, 0, '2026-01-26 11:28:52', '2026-01-26 11:28:52'),
(11, 1, 'OUT', 2, '0.200', '2026-01-26', NULL, 0, '2026-01-26 11:44:13', '2026-01-26 11:44:13'),
(12, 3, 'OUT', 2, '0.200', '2026-01-26', NULL, 0, '2026-01-26 11:44:13', '2026-01-26 11:44:13'),
(13, 4, 'OUT', 2, '1.000', '2026-01-26', NULL, 0, '2026-01-26 11:44:13', '2026-01-26 11:44:13'),
(14, 5, 'OUT', 2, '0.400', '2026-01-26', NULL, 0, '2026-01-26 11:44:13', '2026-01-26 11:44:13'),
(15, 1, 'OUT', 3, '1.000', '2026-01-02', NULL, 0, '2026-01-26 12:12:50', '2026-01-26 12:12:50'),
(16, 2, 'OUT', 3, '1.000', '2026-01-02', NULL, 0, '2026-01-26 12:12:50', '2026-01-26 12:12:50'),
(17, 3, 'OUT', 3, '1.000', '2026-01-02', NULL, 0, '2026-01-26 12:12:50', '2026-01-26 12:12:50'),
(18, 4, 'OUT', 3, '5.000', '2026-01-02', NULL, 0, '2026-01-26 12:12:50', '2026-01-26 12:12:50'),
(19, 5, 'OUT', 3, '2.000', '2026-01-02', NULL, 0, '2026-01-26 12:12:50', '2026-01-26 12:12:50'),
(20, 1, 'IN', NULL, '10.000', '2026-01-26', 'test', 0, '2026-01-26 12:23:31', '2026-01-26 12:23:31');

-- --------------------------------------------------------

--
-- Table structure for table `student_strength`
--

CREATE TABLE `student_strength` (
  `id` int(11) NOT NULL,
  `category` enum('6-8','9-10') NOT NULL,
  `total_students` int(11) DEFAULT NULL,
  `month` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `is_disable` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `student_strength`
--

INSERT INTO `student_strength` (`id`, `category`, `total_students`, `month`, `year`, `is_disable`, `created_at`, `updated_at`) VALUES
(1, '6-8', 20, 1, 2026, 0, '2026-01-25 23:54:21', '2026-01-25 23:54:21');

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
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `daily_aahar_entries`
--
ALTER TABLE `daily_aahar_entries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `daily_aahar_entries_items`
--
ALTER TABLE `daily_aahar_entries_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `item_rates`
--
ALTER TABLE `item_rates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `stock_transactions`
--
ALTER TABLE `stock_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `student_strength`
--
ALTER TABLE `student_strength`
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
