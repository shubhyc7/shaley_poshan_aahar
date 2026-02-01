-- Shaley Poshan Aahar Backup
-- Generated: 2026-02-01 19:40:16 (IST)
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `daily_aahar_entries`;

CREATE TABLE `daily_aahar_entries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` enum('1-5','6-8') NOT NULL,
  `entry_date` date DEFAULT NULL,
  `total_students` int(11) DEFAULT NULL,
  `present_students` int(11) DEFAULT NULL,
  `is_disable` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- --------------------------------------------------------
DROP TABLE IF EXISTS `daily_aahar_entries_items`;

CREATE TABLE `daily_aahar_entries_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `daily_aahar_entries_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `item_rates` decimal(20,5) DEFAULT NULL,
  `qty` decimal(20,5) DEFAULT NULL,
  `is_disable` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `daily_aahar_entries_fk` (`daily_aahar_entries_id`),
  KEY `items_fk` (`item_id`),
  CONSTRAINT `daily_aahar_entries_fk` FOREIGN KEY (`daily_aahar_entries_id`) REFERENCES `daily_aahar_entries` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `items_fk` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- --------------------------------------------------------
DROP TABLE IF EXISTS `item_rates`;

CREATE TABLE `item_rates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` enum('1-5','6-8') NOT NULL,
  `item_id` int(11) DEFAULT NULL,
  `per_student_qty` decimal(20,5) DEFAULT NULL,
  `is_disable` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `item_rates_item_fk` (`item_id`),
  CONSTRAINT `item_rates_item_fk` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4;

-- Data for table `item_rates` --
INSERT INTO `item_rates` (`id`, `category`, `item_id`, `per_student_qty`, `is_disable`, `created_at`, `updated_at`) VALUES ('1', '1-5', '1', '0.10000', '0', '2026-02-01 07:56:41', '2026-02-01 07:56:41');
INSERT INTO `item_rates` (`id`, `category`, `item_id`, `per_student_qty`, `is_disable`, `created_at`, `updated_at`) VALUES ('2', '1-5', '2', '0.02000', '0', '2026-02-01 07:57:05', '2026-02-01 07:57:05');
INSERT INTO `item_rates` (`id`, `category`, `item_id`, `per_student_qty`, `is_disable`, `created_at`, `updated_at`) VALUES ('3', '1-5', '3', '0.01000', '0', '2026-02-01 07:57:37', '2026-02-01 07:57:37');
INSERT INTO `item_rates` (`id`, `category`, `item_id`, `per_student_qty`, `is_disable`, `created_at`, `updated_at`) VALUES ('4', '1-5', '4', '0.02000', '0', '2026-02-01 07:58:03', '2026-02-01 07:58:03');
INSERT INTO `item_rates` (`id`, `category`, `item_id`, `per_student_qty`, `is_disable`, `created_at`, `updated_at`) VALUES ('5', '1-5', '5', '0.02000', '0', '2026-02-01 07:58:13', '2026-02-01 07:58:13');
INSERT INTO `item_rates` (`id`, `category`, `item_id`, `per_student_qty`, `is_disable`, `created_at`, `updated_at`) VALUES ('6', '1-5', '6', '0.01000', '0', '2026-02-01 07:58:24', '2026-02-01 07:58:24');
INSERT INTO `item_rates` (`id`, `category`, `item_id`, `per_student_qty`, `is_disable`, `created_at`, `updated_at`) VALUES ('7', '1-5', '7', '0.02000', '0', '2026-02-01 07:58:41', '2026-02-01 07:58:41');
INSERT INTO `item_rates` (`id`, `category`, `item_id`, `per_student_qty`, `is_disable`, `created_at`, `updated_at`) VALUES ('8', '1-5', '8', '0.02000', '0', '2026-02-01 07:58:58', '2026-02-01 07:58:58');
INSERT INTO `item_rates` (`id`, `category`, `item_id`, `per_student_qty`, `is_disable`, `created_at`, `updated_at`) VALUES ('9', '1-5', '9', '0.02000', '0', '2026-02-01 07:59:53', '2026-02-01 07:59:53');
INSERT INTO `item_rates` (`id`, `category`, `item_id`, `per_student_qty`, `is_disable`, `created_at`, `updated_at`) VALUES ('10', '1-5', '10', '0.00020', '0', '2026-02-01 08:00:14', '2026-02-01 08:00:14');
INSERT INTO `item_rates` (`id`, `category`, `item_id`, `per_student_qty`, `is_disable`, `created_at`, `updated_at`) VALUES ('11', '1-5', '11', '0.00010', '0', '2026-02-01 08:00:23', '2026-02-01 08:00:23');
INSERT INTO `item_rates` (`id`, `category`, `item_id`, `per_student_qty`, `is_disable`, `created_at`, `updated_at`) VALUES ('12', '1-5', '12', '0.00020', '0', '2026-02-01 08:00:35', '2026-02-01 08:00:35');
INSERT INTO `item_rates` (`id`, `category`, `item_id`, `per_student_qty`, `is_disable`, `created_at`, `updated_at`) VALUES ('13', '1-5', '13', '0.00030', '0', '2026-02-01 08:01:59', '2026-02-01 08:01:59');
INSERT INTO `item_rates` (`id`, `category`, `item_id`, `per_student_qty`, `is_disable`, `created_at`, `updated_at`) VALUES ('14', '1-5', '14', '0.00500', '0', '2026-02-01 08:02:24', '2026-02-01 08:02:24');
INSERT INTO `item_rates` (`id`, `category`, `item_id`, `per_student_qty`, `is_disable`, `created_at`, `updated_at`) VALUES ('15', '1-5', '15', '0.00200', '0', '2026-02-01 08:02:42', '2026-02-01 08:02:42');
INSERT INTO `item_rates` (`id`, `category`, `item_id`, `per_student_qty`, `is_disable`, `created_at`, `updated_at`) VALUES ('16', '1-5', '16', '0.00020', '0', '2026-02-01 08:02:57', '2026-02-01 08:02:57');
INSERT INTO `item_rates` (`id`, `category`, `item_id`, `per_student_qty`, `is_disable`, `created_at`, `updated_at`) VALUES ('17', '1-5', '17', '0.02000', '0', '2026-02-01 08:03:52', '2026-02-01 08:03:52');

-- --------------------------------------------------------
DROP TABLE IF EXISTS `items`;

CREATE TABLE `items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_name` varchar(50) DEFAULT NULL,
  `item_type` enum('MAIN','SUPPORT') DEFAULT NULL,
  `unit` varchar(20) DEFAULT NULL,
  `is_disable` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4;

-- Data for table `items` --
INSERT INTO `items` (`id`, `item_name`, `item_type`, `unit`, `is_disable`, `created_at`, `updated_at`) VALUES ('1', 'तांदूळ', 'MAIN', 'ग्रॅम', '0', '2026-02-01 07:44:22', '2026-02-01 07:44:22');
INSERT INTO `items` (`id`, `item_name`, `item_type`, `unit`, `is_disable`, `created_at`, `updated_at`) VALUES ('2', 'मूगडाळ', 'MAIN', 'ग्रॅम', '0', '2026-02-01 07:44:37', '2026-02-01 07:46:31');
INSERT INTO `items` (`id`, `item_name`, `item_type`, `unit`, `is_disable`, `created_at`, `updated_at`) VALUES ('3', 'तूरडाळ', 'MAIN', 'ग्रॅम', '0', '2026-02-01 07:44:51', '2026-02-01 07:44:51');
INSERT INTO `items` (`id`, `item_name`, `item_type`, `unit`, `is_disable`, `created_at`, `updated_at`) VALUES ('4', 'मसूरदाळ', 'MAIN', 'ग्रॅम', '0', '2026-02-01 07:45:06', '2026-02-01 07:45:21');
INSERT INTO `items` (`id`, `item_name`, `item_type`, `unit`, `is_disable`, `created_at`, `updated_at`) VALUES ('5', 'मटकी', 'MAIN', 'ग्रॅम', '0', '2026-02-01 07:47:12', '2026-02-01 07:47:12');
INSERT INTO `items` (`id`, `item_name`, `item_type`, `unit`, `is_disable`, `created_at`, `updated_at`) VALUES ('6', 'मूग', 'MAIN', 'ग्रॅम', '0', '2026-02-01 07:47:34', '2026-02-01 07:47:34');
INSERT INTO `items` (`id`, `item_name`, `item_type`, `unit`, `is_disable`, `created_at`, `updated_at`) VALUES ('7', 'चवली', 'MAIN', 'ग्रॅम', '0', '2026-02-01 07:47:54', '2026-02-01 07:47:54');
INSERT INTO `items` (`id`, `item_name`, `item_type`, `unit`, `is_disable`, `created_at`, `updated_at`) VALUES ('8', 'हरभरा', 'MAIN', 'ग्रॅम', '0', '2026-02-01 07:48:18', '2026-02-01 07:48:18');
INSERT INTO `items` (`id`, `item_name`, `item_type`, `unit`, `is_disable`, `created_at`, `updated_at`) VALUES ('9', 'वाटाणा', 'MAIN', 'ग्रॅम', '0', '2026-02-01 07:48:25', '2026-02-01 07:48:25');
INSERT INTO `items` (`id`, `item_name`, `item_type`, `unit`, `is_disable`, `created_at`, `updated_at`) VALUES ('10', 'जिरे', 'SUPPORT', 'ग्रॅम', '0', '2026-02-01 07:48:41', '2026-02-01 07:48:41');
INSERT INTO `items` (`id`, `item_name`, `item_type`, `unit`, `is_disable`, `created_at`, `updated_at`) VALUES ('11', 'मोहरी', 'SUPPORT', 'ग्रॅम', '0', '2026-02-01 07:48:51', '2026-02-01 07:48:51');
INSERT INTO `items` (`id`, `item_name`, `item_type`, `unit`, `is_disable`, `created_at`, `updated_at`) VALUES ('12', 'हळद', 'SUPPORT', 'ग्रॅम', '0', '2026-02-01 07:49:05', '2026-02-01 07:49:05');
INSERT INTO `items` (`id`, `item_name`, `item_type`, `unit`, `is_disable`, `created_at`, `updated_at`) VALUES ('13', 'मिरची पावडर', 'SUPPORT', 'ग्रॅम', '0', '2026-02-01 07:49:20', '2026-02-01 07:49:20');
INSERT INTO `items` (`id`, `item_name`, `item_type`, `unit`, `is_disable`, `created_at`, `updated_at`) VALUES ('14', 'सोयाबीन तेल', 'SUPPORT', 'ग्रॅम', '0', '2026-02-01 07:50:24', '2026-02-01 07:50:24');
INSERT INTO `items` (`id`, `item_name`, `item_type`, `unit`, `is_disable`, `created_at`, `updated_at`) VALUES ('15', 'मीठ', 'SUPPORT', 'ग्रॅम', '0', '2026-02-01 07:50:39', '2026-02-01 07:50:39');
INSERT INTO `items` (`id`, `item_name`, `item_type`, `unit`, `is_disable`, `created_at`, `updated_at`) VALUES ('16', 'मसाला', 'SUPPORT', 'ग्रॅम', '0', '2026-02-01 07:50:51', '2026-02-01 07:50:51');
INSERT INTO `items` (`id`, `item_name`, `item_type`, `unit`, `is_disable`, `created_at`, `updated_at`) VALUES ('17', 'सोया वाडी', 'SUPPORT', 'ग्रॅम', '0', '2026-02-01 07:51:47', '2026-02-01 07:51:47');

-- --------------------------------------------------------
DROP TABLE IF EXISTS `stock_transactions`;

CREATE TABLE `stock_transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `transaction_type` enum('OPENING','IN','OUT') NOT NULL,
  `category` enum('1-5','6-8') NOT NULL,
  `daily_aahar_entries_id` int(11) DEFAULT NULL,
  `quantity` decimal(20,5) NOT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

-- Data for table `stock_transactions` --
INSERT INTO `stock_transactions` (`id`, `item_id`, `transaction_type`, `category`, `daily_aahar_entries_id`, `quantity`, `transaction_date`, `remarks`, `is_disable`, `created_at`, `updated_at`) VALUES ('1', '1', 'OPENING', '1-5', NULL, '255200.00000', '2025-11-30', '', '0', '2026-02-01 19:38:47', '2026-02-01 19:38:47');

-- --------------------------------------------------------
DROP TABLE IF EXISTS `student_strength`;

CREATE TABLE `student_strength` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` enum('1-5','6-8') NOT NULL,
  `total_students` int(11) DEFAULT NULL,
  `is_disable` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

-- Data for table `student_strength` --
INSERT INTO `student_strength` (`id`, `category`, `total_students`, `is_disable`, `created_at`, `updated_at`) VALUES ('1', '1-5', '66', '0', '2026-02-01 08:04:42', '2026-02-01 08:04:42');

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