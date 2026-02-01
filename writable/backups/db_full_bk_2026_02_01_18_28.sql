-- Shaley Poshan Aahar Backup
-- Generated: 2026-02-01 18:28:43 (IST)
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
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4;

-- Data for table `item_rates` --
INSERT INTO `item_rates` (`id`, `category`, `item_id`, `per_student_qty`, `is_disable`, `created_at`, `updated_at`) VALUES ('1', '1-5', '1', '0.10000', '0', '2026-01-27 08:09:43', '2026-01-27 08:51:28');
INSERT INTO `item_rates` (`id`, `category`, `item_id`, `per_student_qty`, `is_disable`, `created_at`, `updated_at`) VALUES ('2', '1-5', '1', '0.10000', '0', '2026-01-27 08:46:28', '2026-01-27 08:46:28');
INSERT INTO `item_rates` (`id`, `category`, `item_id`, `per_student_qty`, `is_disable`, `created_at`, `updated_at`) VALUES ('3', '1-5', '2', '0.02000', '0', '2026-01-27 08:46:56', '2026-01-27 08:46:56');
INSERT INTO `item_rates` (`id`, `category`, `item_id`, `per_student_qty`, `is_disable`, `created_at`, `updated_at`) VALUES ('4', '1-5', '3', '0.01000', '0', '2026-01-27 08:47:08', '2026-01-27 08:47:08');
INSERT INTO `item_rates` (`id`, `category`, `item_id`, `per_student_qty`, `is_disable`, `created_at`, `updated_at`) VALUES ('5', '1-5', '4', '0.02000', '0', '2026-01-27 08:47:29', '2026-01-27 08:47:29');
INSERT INTO `item_rates` (`id`, `category`, `item_id`, `per_student_qty`, `is_disable`, `created_at`, `updated_at`) VALUES ('6', '1-5', '5', '0.02000', '0', '2026-01-27 08:48:00', '2026-01-27 08:48:00');
INSERT INTO `item_rates` (`id`, `category`, `item_id`, `per_student_qty`, `is_disable`, `created_at`, `updated_at`) VALUES ('7', '1-5', '6', '0.01000', '0', '2026-01-27 08:48:24', '2026-01-27 08:48:24');
INSERT INTO `item_rates` (`id`, `category`, `item_id`, `per_student_qty`, `is_disable`, `created_at`, `updated_at`) VALUES ('8', '1-5', '7', '0.02000', '0', '2026-01-27 08:49:03', '2026-01-27 08:49:03');
INSERT INTO `item_rates` (`id`, `category`, `item_id`, `per_student_qty`, `is_disable`, `created_at`, `updated_at`) VALUES ('9', '1-5', '9', '0.02000', '0', '2026-01-27 08:49:14', '2026-01-27 08:49:14');
INSERT INTO `item_rates` (`id`, `category`, `item_id`, `per_student_qty`, `is_disable`, `created_at`, `updated_at`) VALUES ('10', '1-5', '17', '0.02000', '0', '2026-01-27 08:49:29', '2026-01-27 08:49:29');
INSERT INTO `item_rates` (`id`, `category`, `item_id`, `per_student_qty`, `is_disable`, `created_at`, `updated_at`) VALUES ('11', '1-5', '10', '0.00200', '0', '2026-01-27 08:50:13', '2026-01-27 08:50:13');

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
INSERT INTO `items` (`id`, `item_name`, `item_type`, `unit`, `is_disable`, `created_at`, `updated_at`) VALUES ('1', 'तांदूळ', 'MAIN', 'ग्रॅम', '0', '2026-01-27 08:09:09', '2026-01-27 08:09:09');
INSERT INTO `items` (`id`, `item_name`, `item_type`, `unit`, `is_disable`, `created_at`, `updated_at`) VALUES ('2', 'मुंगडाळ', 'MAIN', 'ग्रॅम', '0', '2026-01-27 08:39:19', '2026-01-27 08:59:13');
INSERT INTO `items` (`id`, `item_name`, `item_type`, `unit`, `is_disable`, `created_at`, `updated_at`) VALUES ('3', 'तूर डाळ', 'MAIN', 'ग्रॅम', '0', '2026-01-27 08:41:08', '2026-01-27 08:59:27');
INSERT INTO `items` (`id`, `item_name`, `item_type`, `unit`, `is_disable`, `created_at`, `updated_at`) VALUES ('4', 'मसूर', 'SUPPORT', 'ग्रॅम', '0', '2026-01-27 08:41:23', '2026-01-27 08:41:23');
INSERT INTO `items` (`id`, `item_name`, `item_type`, `unit`, `is_disable`, `created_at`, `updated_at`) VALUES ('5', 'मटकी', 'SUPPORT', 'ग्रॅम', '0', '2026-01-27 08:41:35', '2026-01-27 08:41:35');
INSERT INTO `items` (`id`, `item_name`, `item_type`, `unit`, `is_disable`, `created_at`, `updated_at`) VALUES ('6', 'मूळ', 'SUPPORT', 'ग्रॅम', '0', '2026-01-27 08:41:45', '2026-01-27 08:41:45');
INSERT INTO `items` (`id`, `item_name`, `item_type`, `unit`, `is_disable`, `created_at`, `updated_at`) VALUES ('7', 'चवली', 'SUPPORT', 'ग्रॅम', '0', '2026-01-27 08:41:59', '2026-01-27 08:41:59');
INSERT INTO `items` (`id`, `item_name`, `item_type`, `unit`, `is_disable`, `created_at`, `updated_at`) VALUES ('8', 'हरभरा', 'SUPPORT', 'ग्रॅम', '0', '2026-01-27 08:42:10', '2026-01-27 08:42:10');
INSERT INTO `items` (`id`, `item_name`, `item_type`, `unit`, `is_disable`, `created_at`, `updated_at`) VALUES ('9', 'वाटाणा', 'SUPPORT', 'ग्रॅम', '0', '2026-01-27 08:42:18', '2026-01-27 08:42:18');
INSERT INTO `items` (`id`, `item_name`, `item_type`, `unit`, `is_disable`, `created_at`, `updated_at`) VALUES ('10', 'जिरा', 'SUPPORT', 'ग्रॅम', '0', '2026-01-27 08:42:33', '2026-01-27 08:42:33');
INSERT INTO `items` (`id`, `item_name`, `item_type`, `unit`, `is_disable`, `created_at`, `updated_at`) VALUES ('11', 'मोहरी', 'SUPPORT', 'ग्रॅम', '0', '2026-01-27 08:42:42', '2026-01-27 08:42:42');
INSERT INTO `items` (`id`, `item_name`, `item_type`, `unit`, `is_disable`, `created_at`, `updated_at`) VALUES ('12', 'हळद', 'SUPPORT', 'ग्रॅम', '0', '2026-01-27 08:43:09', '2026-01-27 08:43:09');
INSERT INTO `items` (`id`, `item_name`, `item_type`, `unit`, `is_disable`, `created_at`, `updated_at`) VALUES ('13', 'मिरची पोउदर', 'SUPPORT', 'ग्रॅम', '0', '2026-01-27 08:43:38', '2026-01-27 08:43:38');
INSERT INTO `items` (`id`, `item_name`, `item_type`, `unit`, `is_disable`, `created_at`, `updated_at`) VALUES ('14', 'सोयाबीन तेल', 'SUPPORT', 'ग्रॅम', '0', '2026-01-27 08:43:48', '2026-01-27 08:43:48');
INSERT INTO `items` (`id`, `item_name`, `item_type`, `unit`, `is_disable`, `created_at`, `updated_at`) VALUES ('15', 'मीठ', 'SUPPORT', 'ग्रॅम', '0', '2026-01-27 08:44:00', '2026-01-27 08:44:00');
INSERT INTO `items` (`id`, `item_name`, `item_type`, `unit`, `is_disable`, `created_at`, `updated_at`) VALUES ('16', 'मसाला', 'SUPPORT', 'ग्रॅम', '0', '2026-01-27 08:44:07', '2026-01-27 08:44:07');
INSERT INTO `items` (`id`, `item_name`, `item_type`, `unit`, `is_disable`, `created_at`, `updated_at`) VALUES ('17', 'सोयावदी', 'SUPPORT', 'ग्रॅम', '0', '2026-01-27 08:44:27', '2026-01-27 08:44:27');

-- --------------------------------------------------------
DROP TABLE IF EXISTS `stock_transactions`;

CREATE TABLE `stock_transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `transaction_type` enum('OPENING','IN','OUT') NOT NULL,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


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