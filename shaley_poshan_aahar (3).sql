-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Feb 02, 2026 at 07:01 AM
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

--
-- Dumping data for table `daily_aahar_entries`
--

INSERT INTO `daily_aahar_entries` (`id`, `category`, `entry_date`, `total_students`, `present_students`, `is_disable`, `created_at`, `updated_at`) VALUES
(1, '1-5', '2026-02-01', 66, 56, 1, '2026-02-01 08:31:23', '2026-02-01 20:01:50'),
(2, '1-5', '2025-12-01', 66, 56, 0, '2026-02-01 08:32:16', '2026-02-01 08:32:16'),
(3, '1-5', '2025-12-02', 66, 56, 0, '2026-02-01 08:36:39', '2026-02-01 08:36:39');

-- --------------------------------------------------------

--
-- Table structure for table `daily_aahar_entries_items`
--

CREATE TABLE `daily_aahar_entries_items` (
  `id` int(11) NOT NULL,
  `daily_aahar_entries_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `item_rates` decimal(20,4) DEFAULT NULL,
  `qty` decimal(20,4) DEFAULT NULL,
  `is_disable` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `daily_aahar_entries_items`
--

INSERT INTO `daily_aahar_entries_items` (`id`, `daily_aahar_entries_id`, `item_id`, `item_rates`, `qty`, `is_disable`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '0.1000', '5.6000', 1, '2026-02-01 20:01:23', '2026-02-01 20:01:50'),
(2, 1, 9, '0.0200', '1.1200', 1, '2026-02-01 20:01:23', '2026-02-01 20:01:50'),
(3, 1, 10, '0.0002', '0.0112', 1, '2026-02-01 20:01:23', '2026-02-01 20:01:50'),
(4, 1, 11, '0.0001', '0.0056', 1, '2026-02-01 20:01:23', '2026-02-01 20:01:50'),
(5, 1, 12, '0.0002', '0.0112', 1, '2026-02-01 20:01:23', '2026-02-01 20:01:50'),
(6, 1, 13, '0.0003', '0.0168', 1, '2026-02-01 20:01:23', '2026-02-01 20:01:50'),
(7, 1, 14, '0.0050', '0.2800', 1, '2026-02-01 20:01:23', '2026-02-01 20:01:50'),
(8, 1, 15, '0.0020', '0.1120', 1, '2026-02-01 20:01:23', '2026-02-01 20:01:50'),
(9, 1, 16, '0.0002', '0.0112', 1, '2026-02-01 20:01:23', '2026-02-01 20:01:50'),
(10, 2, 1, '0.1000', '5.6000', 0, '2026-02-01 20:02:16', '2026-02-01 20:02:16'),
(11, 2, 9, '0.0200', '1.1200', 0, '2026-02-01 20:02:16', '2026-02-01 20:02:16'),
(12, 2, 10, '0.0002', '0.0112', 0, '2026-02-01 20:02:16', '2026-02-01 20:02:16'),
(13, 2, 11, '0.0001', '0.0056', 0, '2026-02-01 20:02:16', '2026-02-01 20:02:16'),
(14, 2, 12, '0.0002', '0.0112', 0, '2026-02-01 20:02:16', '2026-02-01 20:02:16'),
(15, 2, 13, '0.0003', '0.0168', 0, '2026-02-01 20:02:16', '2026-02-01 20:02:16'),
(16, 2, 14, '0.0050', '0.2800', 0, '2026-02-01 20:02:16', '2026-02-01 20:02:16'),
(17, 2, 15, '0.0020', '0.1120', 0, '2026-02-01 20:02:16', '2026-02-01 20:02:16'),
(18, 2, 16, '0.0002', '0.0112', 0, '2026-02-01 20:02:16', '2026-02-01 20:02:16'),
(19, 3, 1, '0.1000', '5.6000', 0, '2026-02-01 20:06:39', '2026-02-01 20:06:39'),
(20, 3, 4, '0.0200', '1.1200', 0, '2026-02-01 20:06:39', '2026-02-01 20:06:39'),
(21, 3, 10, '0.0002', '0.0112', 0, '2026-02-01 20:06:39', '2026-02-01 20:06:39'),
(22, 3, 11, '0.0001', '0.0056', 0, '2026-02-01 20:06:39', '2026-02-01 20:06:39'),
(23, 3, 12, '0.0002', '0.0112', 0, '2026-02-01 20:06:39', '2026-02-01 20:06:39'),
(24, 3, 13, '0.0003', '0.0168', 0, '2026-02-01 20:06:39', '2026-02-01 20:06:39'),
(25, 3, 14, '0.0050', '0.2800', 0, '2026-02-01 20:06:39', '2026-02-01 20:06:39'),
(26, 3, 15, '0.0020', '0.1120', 0, '2026-02-01 20:06:39', '2026-02-01 20:06:39'),
(27, 3, 16, '0.0002', '0.0112', 0, '2026-02-01 20:06:39', '2026-02-01 20:06:39'),
(28, 3, 17, '0.0200', '1.1200', 0, '2026-02-01 20:06:39', '2026-02-01 20:06:39');

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
(1, 'तांदूळ', 'MAIN', 'किलोग्रॅम', 0, '2026-02-01 07:44:22', '2026-02-01 20:16:09'),
(2, 'मूगडाळ', 'MAIN', 'किलोग्रॅम', 0, '2026-02-01 07:44:37', '2026-02-01 20:16:17'),
(3, 'तूरडाळ', 'MAIN', 'किलोग्रॅम', 0, '2026-02-01 07:44:51', '2026-02-01 20:16:17'),
(4, 'मसूरदाळ', 'MAIN', 'किलोग्रॅम', 0, '2026-02-01 07:45:06', '2026-02-01 20:16:17'),
(5, 'मटकी', 'MAIN', 'किलोग्रॅम', 0, '2026-02-01 07:47:12', '2026-02-01 20:16:17'),
(6, 'मूग', 'MAIN', 'किलोग्रॅम', 0, '2026-02-01 07:47:34', '2026-02-01 20:16:17'),
(7, 'चवली', 'MAIN', 'किलोग्रॅम', 0, '2026-02-01 07:47:54', '2026-02-01 20:16:17'),
(8, 'हरभरा', 'MAIN', 'किलोग्रॅम', 0, '2026-02-01 07:48:18', '2026-02-01 20:16:17'),
(9, 'वाटाणा', 'MAIN', 'किलोग्रॅम', 0, '2026-02-01 07:48:25', '2026-02-01 20:16:17'),
(10, 'जिरे', 'SUPPORT', 'किलोग्रॅम', 0, '2026-02-01 07:48:41', '2026-02-01 20:16:17'),
(11, 'मोहरी', 'SUPPORT', 'किलोग्रॅम', 0, '2026-02-01 07:48:51', '2026-02-01 20:16:17'),
(12, 'हळद', 'SUPPORT', 'किलोग्रॅम', 0, '2026-02-01 07:49:05', '2026-02-01 20:16:17'),
(13, 'मिरची पावडर', 'SUPPORT', 'किलोग्रॅम', 0, '2026-02-01 07:49:20', '2026-02-01 20:16:17'),
(14, 'सोयाबीन तेल', 'SUPPORT', 'किलोग्रॅम', 0, '2026-02-01 07:50:24', '2026-02-01 20:16:17'),
(15, 'मीठ', 'SUPPORT', 'किलोग्रॅम', 0, '2026-02-01 07:50:39', '2026-02-01 20:16:17'),
(16, 'मसाला', 'SUPPORT', 'किलोग्रॅम', 0, '2026-02-01 07:50:51', '2026-02-01 20:16:17'),
(17, 'सोया वाडी', 'MAIN', 'किलोग्रॅम', 0, '2026-02-01 07:51:47', '2026-02-01 20:16:17');

-- --------------------------------------------------------

--
-- Table structure for table `item_rates`
--

CREATE TABLE `item_rates` (
  `id` int(11) NOT NULL,
  `category` enum('1-5','6-8') NOT NULL,
  `item_id` int(11) DEFAULT NULL,
  `per_student_qty` decimal(20,4) DEFAULT NULL,
  `is_disable` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `item_rates`
--

INSERT INTO `item_rates` (`id`, `category`, `item_id`, `per_student_qty`, `is_disable`, `created_at`, `updated_at`) VALUES
(1, '1-5', 1, '0.1000', 0, '2026-02-01 07:56:41', '2026-02-01 07:56:41'),
(2, '1-5', 2, '0.0200', 0, '2026-02-01 07:57:05', '2026-02-01 07:57:05'),
(3, '1-5', 3, '0.0100', 0, '2026-02-01 07:57:37', '2026-02-01 07:57:37'),
(4, '1-5', 4, '0.0200', 0, '2026-02-01 07:58:03', '2026-02-01 07:58:03'),
(5, '1-5', 5, '0.0200', 0, '2026-02-01 07:58:13', '2026-02-01 07:58:13'),
(6, '1-5', 6, '0.0100', 0, '2026-02-01 07:58:24', '2026-02-01 07:58:24'),
(7, '1-5', 7, '0.0200', 0, '2026-02-01 07:58:41', '2026-02-01 07:58:41'),
(8, '1-5', 8, '0.0200', 0, '2026-02-01 07:58:58', '2026-02-01 07:58:58'),
(9, '1-5', 9, '0.0200', 0, '2026-02-01 07:59:53', '2026-02-01 07:59:53'),
(10, '1-5', 10, '0.0002', 0, '2026-02-01 08:00:14', '2026-02-01 08:00:14'),
(11, '1-5', 11, '0.0001', 0, '2026-02-01 08:00:23', '2026-02-01 08:00:23'),
(12, '1-5', 12, '0.0002', 0, '2026-02-01 08:00:35', '2026-02-01 08:00:35'),
(13, '1-5', 13, '0.0003', 0, '2026-02-01 08:01:59', '2026-02-01 08:01:59'),
(14, '1-5', 14, '0.0050', 0, '2026-02-01 08:02:24', '2026-02-01 08:02:24'),
(15, '1-5', 15, '0.0020', 0, '2026-02-01 08:02:42', '2026-02-01 08:02:42'),
(16, '1-5', 16, '0.0002', 0, '2026-02-01 08:02:57', '2026-02-01 08:02:57'),
(17, '1-5', 17, '0.0200', 0, '2026-02-01 08:03:52', '2026-02-01 08:03:52');

-- --------------------------------------------------------

--
-- Table structure for table `stock_transactions`
--

CREATE TABLE `stock_transactions` (
  `id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `transaction_type` enum('OPENING','IN','OUT') NOT NULL,
  `category` enum('1-5','6-8') NOT NULL,
  `daily_aahar_entries_id` int(11) DEFAULT NULL,
  `quantity` decimal(20,4) NOT NULL,
  `transaction_date` date NOT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `is_disable` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `stock_transactions`
--

INSERT INTO `stock_transactions` (`id`, `item_id`, `transaction_type`, `category`, `daily_aahar_entries_id`, `quantity`, `transaction_date`, `remarks`, `is_disable`, `created_at`, `updated_at`) VALUES
(1, 1, 'OPENING', '1-5', NULL, '255.2000', '2025-11-30', '', 0, '2026-02-01 19:38:47', '2026-02-01 20:28:17'),
(2, 2, 'OPENING', '1-5', NULL, '4.1300', '2025-11-30', '', 0, '2026-02-01 19:42:18', '2026-02-01 20:28:50'),
(3, 3, 'OPENING', '1-5', NULL, '3.4500', '2025-11-30', '', 0, '2026-02-01 19:44:34', '2026-02-01 20:29:07'),
(4, 4, 'OPENING', '1-5', NULL, '24.4800', '2025-11-30', '', 0, '2026-02-01 19:44:43', '2026-02-01 20:29:15'),
(5, 5, 'OPENING', '1-5', NULL, '13.1900', '2025-11-30', '', 0, '2026-02-01 19:45:07', '2026-02-01 20:29:26'),
(6, 6, 'OPENING', '1-5', NULL, '3.2400', '2025-11-30', '', 0, '2026-02-01 19:45:59', '2026-02-01 20:29:35'),
(7, 7, 'OPENING', '1-5', NULL, '3.7500', '2025-11-30', '', 0, '2026-02-01 19:47:03', '2026-02-01 20:29:43'),
(8, 8, 'OPENING', '1-5', NULL, '5.6300', '2025-11-30', '', 0, '2026-02-01 19:47:28', '2026-02-01 20:30:07'),
(9, 9, 'OPENING', '1-5', NULL, '14.3500', '2025-11-30', '', 0, '2026-02-01 19:47:39', '2026-02-01 20:30:33'),
(10, 10, 'OPENING', '1-5', NULL, '1.2230', '2025-11-30', '', 0, '2026-02-01 19:48:01', '2026-02-01 20:30:41'),
(11, 11, 'OPENING', '1-5', NULL, '1.1625', '2025-11-30', '', 0, '2026-02-01 19:48:21', '2026-02-01 20:30:59'),
(12, 12, 'OPENING', '1-5', NULL, '1.1617', '2025-11-30', '', 0, '2026-02-01 19:49:07', '2026-02-01 20:31:12'),
(13, 13, 'OPENING', '1-5', NULL, '2.1363', '2025-11-30', '', 0, '2026-02-01 19:49:33', '2026-02-01 20:31:45'),
(14, 14, 'OPENING', '1-5', NULL, '27.7100', '2025-11-30', '', 0, '2026-02-01 19:49:43', '2026-02-01 20:31:53'),
(15, 15, 'OPENING', '1-5', NULL, '12.5280', '2025-11-30', '', 0, '2026-02-01 19:50:00', '2026-02-01 20:32:08'),
(16, 16, 'OPENING', '1-5', NULL, '4.0219', '2025-11-30', '', 0, '2026-02-01 19:50:13', '2026-02-01 20:32:19'),
(17, 17, 'OPENING', '1-5', NULL, '3.0200', '2025-11-30', '', 0, '2026-02-01 19:50:47', '2026-02-01 20:32:32'),
(18, 1, 'OUT', '1-5', 1, '5.6000', '2026-02-01', NULL, 1, '2026-02-01 20:01:23', '2026-02-01 20:01:50'),
(19, 9, 'OUT', '1-5', 1, '1.1200', '2026-02-01', NULL, 1, '2026-02-01 20:01:23', '2026-02-01 20:01:50'),
(20, 10, 'OUT', '1-5', 1, '0.0112', '2026-02-01', NULL, 1, '2026-02-01 20:01:23', '2026-02-01 20:01:50'),
(21, 11, 'OUT', '1-5', 1, '0.0056', '2026-02-01', NULL, 1, '2026-02-01 20:01:23', '2026-02-01 20:01:50'),
(22, 12, 'OUT', '1-5', 1, '0.0112', '2026-02-01', NULL, 1, '2026-02-01 20:01:23', '2026-02-01 20:01:50'),
(23, 13, 'OUT', '1-5', 1, '0.0168', '2026-02-01', NULL, 1, '2026-02-01 20:01:23', '2026-02-01 20:01:50'),
(24, 14, 'OUT', '1-5', 1, '0.2800', '2026-02-01', NULL, 1, '2026-02-01 20:01:23', '2026-02-01 20:01:50'),
(25, 15, 'OUT', '1-5', 1, '0.1120', '2026-02-01', NULL, 1, '2026-02-01 20:01:23', '2026-02-01 20:01:50'),
(26, 16, 'OUT', '1-5', 1, '0.0112', '2026-02-01', NULL, 1, '2026-02-01 20:01:23', '2026-02-01 20:01:50'),
(27, 1, 'OUT', '1-5', 2, '5.6000', '2025-12-01', NULL, 0, '2026-02-01 20:02:16', '2026-02-01 20:02:16'),
(28, 9, 'OUT', '1-5', 2, '1.1200', '2025-12-01', NULL, 0, '2026-02-01 20:02:16', '2026-02-01 20:02:16'),
(29, 10, 'OUT', '1-5', 2, '0.0112', '2025-12-01', NULL, 0, '2026-02-01 20:02:16', '2026-02-01 20:02:16'),
(30, 11, 'OUT', '1-5', 2, '0.0056', '2025-12-01', NULL, 0, '2026-02-01 20:02:16', '2026-02-01 20:02:16'),
(31, 12, 'OUT', '1-5', 2, '0.0112', '2025-12-01', NULL, 0, '2026-02-01 20:02:16', '2026-02-01 20:02:16'),
(32, 13, 'OUT', '1-5', 2, '0.0168', '2025-12-01', NULL, 0, '2026-02-01 20:02:16', '2026-02-01 20:02:16'),
(33, 14, 'OUT', '1-5', 2, '0.2800', '2025-12-01', NULL, 0, '2026-02-01 20:02:16', '2026-02-01 20:02:16'),
(34, 15, 'OUT', '1-5', 2, '0.1120', '2025-12-01', NULL, 0, '2026-02-01 20:02:16', '2026-02-01 20:02:16'),
(35, 16, 'OUT', '1-5', 2, '0.0112', '2025-12-01', NULL, 0, '2026-02-01 20:02:16', '2026-02-01 20:02:16'),
(36, 1, 'OUT', '1-5', 3, '5.6000', '2025-12-02', NULL, 0, '2026-02-01 20:06:39', '2026-02-01 20:06:39'),
(37, 4, 'OUT', '1-5', 3, '1.1200', '2025-12-02', NULL, 0, '2026-02-01 20:06:39', '2026-02-01 20:06:39'),
(38, 10, 'OUT', '1-5', 3, '0.0112', '2025-12-02', NULL, 0, '2026-02-01 20:06:39', '2026-02-01 20:06:39'),
(39, 11, 'OUT', '1-5', 3, '0.0056', '2025-12-02', NULL, 0, '2026-02-01 20:06:39', '2026-02-01 20:06:39'),
(40, 12, 'OUT', '1-5', 3, '0.0112', '2025-12-02', NULL, 0, '2026-02-01 20:06:39', '2026-02-01 20:06:39'),
(41, 13, 'OUT', '1-5', 3, '0.0168', '2025-12-02', NULL, 0, '2026-02-01 20:06:39', '2026-02-01 20:06:39'),
(42, 14, 'OUT', '1-5', 3, '0.2800', '2025-12-02', NULL, 0, '2026-02-01 20:06:39', '2026-02-01 20:06:39'),
(43, 15, 'OUT', '1-5', 3, '0.1120', '2025-12-02', NULL, 0, '2026-02-01 20:06:39', '2026-02-01 20:06:39'),
(44, 16, 'OUT', '1-5', 3, '0.0112', '2025-12-02', NULL, 0, '2026-02-01 20:06:39', '2026-02-01 20:06:39'),
(45, 17, 'OUT', '1-5', 3, '1.1200', '2025-12-02', NULL, 0, '2026-02-01 20:06:39', '2026-02-01 20:06:39');

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

--
-- Dumping data for table `student_strength`
--

INSERT INTO `student_strength` (`id`, `category`, `total_students`, `is_disable`, `created_at`, `updated_at`) VALUES
(1, '1-5', 66, 0, '2026-02-01 08:04:42', '2026-02-01 08:04:42');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `daily_aahar_entries_items`
--
ALTER TABLE `daily_aahar_entries_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `item_rates`
--
ALTER TABLE `item_rates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `stock_transactions`
--
ALTER TABLE `stock_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `student_strength`
--
ALTER TABLE `student_strength`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
