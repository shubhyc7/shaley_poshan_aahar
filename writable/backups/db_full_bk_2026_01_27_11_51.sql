-- Shaley Poshan Aahar Backup
-- Generated: 2026-01-27 11:51:54 (IST)
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `daily_aahar_entries`;

CREATE TABLE `daily_aahar_entries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` enum('6-8','9-10') NOT NULL,
  `entry_date` date DEFAULT NULL,
  `total_students` int(11) DEFAULT NULL,
  `present_students` int(11) DEFAULT NULL,
  `is_disable` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

-- Data for table `daily_aahar_entries` --
INSERT INTO `daily_aahar_entries` (`id`, `category`, `entry_date`, `total_students`, `present_students`, `is_disable`, `created_at`, `updated_at`) VALUES ('1', '6-8', '2026-01-01', '20', '5', '0', '2026-01-25 23:58:52', '2026-01-25 23:58:52');
INSERT INTO `daily_aahar_entries` (`id`, `category`, `entry_date`, `total_students`, `present_students`, `is_disable`, `created_at`, `updated_at`) VALUES ('2', '6-8', '2026-01-26', '20', '2', '0', '2026-01-26 00:14:13', '2026-01-26 00:14:13');

-- --------------------------------------------------------
DROP TABLE IF EXISTS `daily_aahar_entries_items`;

CREATE TABLE `daily_aahar_entries_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `daily_aahar_entries_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `qty` decimal(10,3) DEFAULT NULL,
  `is_disable` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `daily_aahar_entries_fk` (`daily_aahar_entries_id`),
  KEY `items_fk` (`item_id`),
  CONSTRAINT `daily_aahar_entries_fk` FOREIGN KEY (`daily_aahar_entries_id`) REFERENCES `daily_aahar_entries` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `items_fk` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4;

-- Data for table `daily_aahar_entries_items` --
INSERT INTO `daily_aahar_entries_items` (`id`, `daily_aahar_entries_id`, `item_id`, `qty`, `is_disable`, `created_at`, `updated_at`) VALUES ('1', '1', '1', '0.500', '0', '2026-01-26 11:28:52', '2026-01-26 11:28:52');
INSERT INTO `daily_aahar_entries_items` (`id`, `daily_aahar_entries_id`, `item_id`, `qty`, `is_disable`, `created_at`, `updated_at`) VALUES ('2', '1', '2', '0.500', '0', '2026-01-26 11:28:52', '2026-01-26 11:28:52');
INSERT INTO `daily_aahar_entries_items` (`id`, `daily_aahar_entries_id`, `item_id`, `qty`, `is_disable`, `created_at`, `updated_at`) VALUES ('3', '1', '3', '0.500', '0', '2026-01-26 11:28:52', '2026-01-26 11:28:52');
INSERT INTO `daily_aahar_entries_items` (`id`, `daily_aahar_entries_id`, `item_id`, `qty`, `is_disable`, `created_at`, `updated_at`) VALUES ('4', '1', '4', '2.500', '0', '2026-01-26 11:28:52', '2026-01-26 11:28:52');
INSERT INTO `daily_aahar_entries_items` (`id`, `daily_aahar_entries_id`, `item_id`, `qty`, `is_disable`, `created_at`, `updated_at`) VALUES ('5', '1', '5', '1.000', '0', '2026-01-26 11:28:52', '2026-01-26 11:28:52');
INSERT INTO `daily_aahar_entries_items` (`id`, `daily_aahar_entries_id`, `item_id`, `qty`, `is_disable`, `created_at`, `updated_at`) VALUES ('6', '2', '1', '0.200', '0', '2026-01-26 11:44:13', '2026-01-26 11:44:13');
INSERT INTO `daily_aahar_entries_items` (`id`, `daily_aahar_entries_id`, `item_id`, `qty`, `is_disable`, `created_at`, `updated_at`) VALUES ('7', '2', '3', '0.200', '0', '2026-01-26 11:44:13', '2026-01-26 11:44:13');
INSERT INTO `daily_aahar_entries_items` (`id`, `daily_aahar_entries_id`, `item_id`, `qty`, `is_disable`, `created_at`, `updated_at`) VALUES ('8', '2', '4', '1.000', '0', '2026-01-26 11:44:13', '2026-01-26 11:44:13');
INSERT INTO `daily_aahar_entries_items` (`id`, `daily_aahar_entries_id`, `item_id`, `qty`, `is_disable`, `created_at`, `updated_at`) VALUES ('9', '2', '5', '0.400', '0', '2026-01-26 11:44:13', '2026-01-26 11:44:13');

-- --------------------------------------------------------
DROP TABLE IF EXISTS `item_rates`;

CREATE TABLE `item_rates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` enum('6-8','9-10') NOT NULL,
  `item_id` int(11) DEFAULT NULL,
  `per_student_qty` decimal(10,3) DEFAULT NULL,
  `month` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `is_disable` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `item_rates_item_fk` (`item_id`),
  CONSTRAINT `item_rates_item_fk` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;

-- Data for table `item_rates` --
INSERT INTO `item_rates` (`id`, `category`, `item_id`, `per_student_qty`, `month`, `year`, `is_disable`, `created_at`, `updated_at`) VALUES ('1', '6-8', '1', '0.100', '1', '2026', '0', '2026-01-25 23:53:32', '2026-01-25 23:53:32');
INSERT INTO `item_rates` (`id`, `category`, `item_id`, `per_student_qty`, `month`, `year`, `is_disable`, `created_at`, `updated_at`) VALUES ('2', '6-8', '2', '0.100', '1', '2026', '0', '2026-01-25 23:53:42', '2026-01-25 23:53:42');
INSERT INTO `item_rates` (`id`, `category`, `item_id`, `per_student_qty`, `month`, `year`, `is_disable`, `created_at`, `updated_at`) VALUES ('3', '6-8', '3', '0.100', '1', '2026', '0', '2026-01-25 23:53:51', '2026-01-25 23:53:51');
INSERT INTO `item_rates` (`id`, `category`, `item_id`, `per_student_qty`, `month`, `year`, `is_disable`, `created_at`, `updated_at`) VALUES ('4', '6-8', '4', '0.500', '1', '2026', '0', '2026-01-25 23:53:59', '2026-01-25 23:53:59');
INSERT INTO `item_rates` (`id`, `category`, `item_id`, `per_student_qty`, `month`, `year`, `is_disable`, `created_at`, `updated_at`) VALUES ('5', '6-8', '5', '0.200', '1', '2026', '0', '2026-01-25 23:54:08', '2026-01-25 23:54:08');

-- --------------------------------------------------------
DROP TABLE IF EXISTS `items`;

CREATE TABLE `items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_name` varchar(50) DEFAULT NULL,
  `item_type` enum('MAIN','SUPPORT') DEFAULT NULL,
  `unit` varchar(10) DEFAULT NULL,
  `is_disable` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;

-- Data for table `items` --
INSERT INTO `items` (`id`, `item_name`, `item_type`, `unit`, `is_disable`, `created_at`, `updated_at`) VALUES ('1', 'तांदूळ', 'MAIN', 'ग्रॅम', '0', '2026-01-25 23:52:33', '2026-01-25 23:52:33');
INSERT INTO `items` (`id`, `item_name`, `item_type`, `unit`, `is_disable`, `created_at`, `updated_at`) VALUES ('2', 'मूगडाळ', 'MAIN', 'ग्रॅम', '0', '2026-01-25 23:52:51', '2026-01-25 23:52:51');
INSERT INTO `items` (`id`, `item_name`, `item_type`, `unit`, `is_disable`, `created_at`, `updated_at`) VALUES ('3', 'तेल', 'SUPPORT', 'ग्रॅम', '0', '2026-01-25 23:53:02', '2026-01-25 23:53:02');
INSERT INTO `items` (`id`, `item_name`, `item_type`, `unit`, `is_disable`, `created_at`, `updated_at`) VALUES ('4', 'मीठ', 'SUPPORT', 'ग्रॅम', '0', '2026-01-25 23:53:08', '2026-01-25 23:53:08');
INSERT INTO `items` (`id`, `item_name`, `item_type`, `unit`, `is_disable`, `created_at`, `updated_at`) VALUES ('5', 'वाटाणा', 'SUPPORT', 'ग्रॅम', '0', '2026-01-25 23:53:16', '2026-01-25 23:53:16');

-- --------------------------------------------------------
DROP TABLE IF EXISTS `stock_transactions`;

CREATE TABLE `stock_transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `transaction_type` enum('OPENING','IN','OUT') NOT NULL,
  `daily_aahar_entries_id` int(11) DEFAULT NULL,
  `quantity` decimal(10,3) NOT NULL,
  `transaction_date` date NOT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `is_disable` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`),
  KEY `transaction_date` (`transaction_date`),
  KEY `daily_aahar_entries_id_fk` (`daily_aahar_entries_id`),
  CONSTRAINT `daily_aahar_entries_id_fk` FOREIGN KEY (`daily_aahar_entries_id`) REFERENCES `daily_aahar_entries` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `stock_transactions_item_fk` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4;

-- Data for table `stock_transactions` --
INSERT INTO `stock_transactions` (`id`, `item_id`, `transaction_type`, `daily_aahar_entries_id`, `quantity`, `transaction_date`, `remarks`, `is_disable`, `created_at`, `updated_at`) VALUES ('1', '1', 'OPENING', NULL, '2000.000', '2025-12-31', 'OPENING', '0', '2026-01-26 11:24:58', '2026-01-26 11:30:14');
INSERT INTO `stock_transactions` (`id`, `item_id`, `transaction_type`, `daily_aahar_entries_id`, `quantity`, `transaction_date`, `remarks`, `is_disable`, `created_at`, `updated_at`) VALUES ('2', '2', 'OPENING', NULL, '1000.000', '2025-12-31', 'OPENING', '0', '2026-01-26 11:25:12', '2026-01-26 11:30:46');
INSERT INTO `stock_transactions` (`id`, `item_id`, `transaction_type`, `daily_aahar_entries_id`, `quantity`, `transaction_date`, `remarks`, `is_disable`, `created_at`, `updated_at`) VALUES ('3', '3', 'OPENING', NULL, '1000.000', '2025-12-31', 'OPENING', '0', '2026-01-26 11:25:52', '2026-01-26 11:30:53');
INSERT INTO `stock_transactions` (`id`, `item_id`, `transaction_type`, `daily_aahar_entries_id`, `quantity`, `transaction_date`, `remarks`, `is_disable`, `created_at`, `updated_at`) VALUES ('4', '4', 'OPENING', NULL, '1000.000', '2025-12-31', 'OPENING', '0', '2026-01-26 11:26:03', '2026-01-26 11:30:40');
INSERT INTO `stock_transactions` (`id`, `item_id`, `transaction_type`, `daily_aahar_entries_id`, `quantity`, `transaction_date`, `remarks`, `is_disable`, `created_at`, `updated_at`) VALUES ('5', '5', 'OPENING', NULL, '1000.000', '2025-12-31', 'OPENING', '0', '2026-01-26 11:26:34', '2026-01-26 11:30:24');
INSERT INTO `stock_transactions` (`id`, `item_id`, `transaction_type`, `daily_aahar_entries_id`, `quantity`, `transaction_date`, `remarks`, `is_disable`, `created_at`, `updated_at`) VALUES ('6', '1', 'OUT', '1', '0.500', '2026-01-01', NULL, '0', '2026-01-26 11:28:52', '2026-01-26 11:28:52');
INSERT INTO `stock_transactions` (`id`, `item_id`, `transaction_type`, `daily_aahar_entries_id`, `quantity`, `transaction_date`, `remarks`, `is_disable`, `created_at`, `updated_at`) VALUES ('7', '2', 'OUT', '1', '0.500', '2026-01-01', NULL, '0', '2026-01-26 11:28:52', '2026-01-26 11:28:52');
INSERT INTO `stock_transactions` (`id`, `item_id`, `transaction_type`, `daily_aahar_entries_id`, `quantity`, `transaction_date`, `remarks`, `is_disable`, `created_at`, `updated_at`) VALUES ('8', '3', 'OUT', '1', '0.500', '2026-01-01', NULL, '0', '2026-01-26 11:28:52', '2026-01-26 11:28:52');
INSERT INTO `stock_transactions` (`id`, `item_id`, `transaction_type`, `daily_aahar_entries_id`, `quantity`, `transaction_date`, `remarks`, `is_disable`, `created_at`, `updated_at`) VALUES ('9', '4', 'OUT', '1', '2.500', '2026-01-01', NULL, '0', '2026-01-26 11:28:52', '2026-01-26 11:28:52');
INSERT INTO `stock_transactions` (`id`, `item_id`, `transaction_type`, `daily_aahar_entries_id`, `quantity`, `transaction_date`, `remarks`, `is_disable`, `created_at`, `updated_at`) VALUES ('10', '5', 'OUT', '1', '1.000', '2026-01-01', NULL, '0', '2026-01-26 11:28:52', '2026-01-26 11:28:52');
INSERT INTO `stock_transactions` (`id`, `item_id`, `transaction_type`, `daily_aahar_entries_id`, `quantity`, `transaction_date`, `remarks`, `is_disable`, `created_at`, `updated_at`) VALUES ('11', '1', 'OUT', '2', '0.200', '2026-01-26', NULL, '0', '2026-01-26 11:44:13', '2026-01-26 11:44:13');
INSERT INTO `stock_transactions` (`id`, `item_id`, `transaction_type`, `daily_aahar_entries_id`, `quantity`, `transaction_date`, `remarks`, `is_disable`, `created_at`, `updated_at`) VALUES ('12', '3', 'OUT', '2', '0.200', '2026-01-26', NULL, '0', '2026-01-26 11:44:13', '2026-01-26 11:44:13');
INSERT INTO `stock_transactions` (`id`, `item_id`, `transaction_type`, `daily_aahar_entries_id`, `quantity`, `transaction_date`, `remarks`, `is_disable`, `created_at`, `updated_at`) VALUES ('13', '4', 'OUT', '2', '1.000', '2026-01-26', NULL, '0', '2026-01-26 11:44:13', '2026-01-26 11:44:13');
INSERT INTO `stock_transactions` (`id`, `item_id`, `transaction_type`, `daily_aahar_entries_id`, `quantity`, `transaction_date`, `remarks`, `is_disable`, `created_at`, `updated_at`) VALUES ('14', '5', 'OUT', '2', '0.400', '2026-01-26', NULL, '0', '2026-01-26 11:44:13', '2026-01-26 11:44:13');

-- --------------------------------------------------------
DROP TABLE IF EXISTS `student_strength`;

CREATE TABLE `student_strength` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` enum('6-8','9-10') NOT NULL,
  `total_students` int(11) DEFAULT NULL,
  `month` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `is_disable` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

-- Data for table `student_strength` --
INSERT INTO `student_strength` (`id`, `category`, `total_students`, `month`, `year`, `is_disable`, `created_at`, `updated_at`) VALUES ('1', '6-8', '20', '1', '2026', '0', '2026-01-25 23:54:21', '2026-01-26 02:56:48');

-- --------------------------------------------------------
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `role` enum('ADMIN','TEACHER') DEFAULT 'TEACHER',
  `is_disable` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

-- Data for table `users` --
INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `role`, `is_disable`, `created_at`) VALUES ('1', 'admin', '$2y$10$iORWvI3L3Sb6PqfkCi8ypO/SF5aIq2C95TpQGHLvqxW3pxfO5oANK', 'System Admin', 'ADMIN', '0', '2026-01-26 12:29:03');

-- --------------------------------------------------------

SET FOREIGN_KEY_CHECKS=1;