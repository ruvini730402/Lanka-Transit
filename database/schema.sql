-- Lanka Transit Database Schema
-- Bus Seat Booking System

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- Create database
CREATE DATABASE IF NOT EXISTS `lanka_transit` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `lanka_transit`;

-- --------------------------------------------------------

--
-- Table structure for table `operators`
--

CREATE TABLE `operators` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `license_number` varchar(100) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `license_number` (`license_number`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `routes`
--

CREATE TABLE `routes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `origin` varchar(255) NOT NULL,
  `destination` varchar(255) NOT NULL,
  `distance` decimal(8,2) NOT NULL COMMENT 'Distance in kilometers',
  `estimated_duration` varchar(50) NOT NULL COMMENT 'Estimated travel time',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `origin` (`origin`),
  KEY `destination` (`destination`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `buses`
--

CREATE TABLE `buses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bus_number` varchar(50) NOT NULL,
  `bus_type` enum('AC','Non-AC','Semi-Luxury','Luxury') NOT NULL,
  `total_seats` int(11) NOT NULL,
  `amenities` text DEFAULT NULL COMMENT 'JSON or comma-separated list of amenities',
  `operator_id` int(11) NOT NULL,
  `status` enum('active','inactive','maintenance') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `bus_number` (`bus_number`),
  KEY `operator_id` (`operator_id`),
  KEY `status` (`status`),
  CONSTRAINT `buses_ibfk_1` FOREIGN KEY (`operator_id`) REFERENCES `operators` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bus_schedules`
--

CREATE TABLE `bus_schedules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bus_id` int(11) NOT NULL,
  `route_id` int(11) NOT NULL,
  `departure_time` time NOT NULL,
  `arrival_time` time NOT NULL,
  `fare` decimal(10,2) NOT NULL,
  `days_of_week` varchar(20) NOT NULL DEFAULT '1,2,3,4,5,6,7' COMMENT 'Comma-separated day numbers (1=Monday, 7=Sunday)',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `bus_id` (`bus_id`),
  KEY `route_id` (`route_id`),
  KEY `departure_time` (`departure_time`),
  KEY `status` (`status`),
  CONSTRAINT `bus_schedules_ibfk_1` FOREIGN KEY (`bus_id`) REFERENCES `buses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `bus_schedules_ibfk_2` FOREIGN KEY (`route_id`) REFERENCES `routes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `address` text DEFAULT NULL,
  `email_verified` tinyint(1) DEFAULT 0,
  `phone_verified` tinyint(1) DEFAULT 0,
  `status` enum('active','inactive','suspended') DEFAULT 'active',
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `phone` (`phone`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_reference` varchar(20) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `schedule_id` int(11) NOT NULL,
  `travel_date` date NOT NULL,
  `passenger_name` varchar(255) NOT NULL,
  `passenger_phone` varchar(20) NOT NULL,
  `passenger_email` varchar(255) DEFAULT NULL,
  `seat_numbers` text NOT NULL COMMENT 'JSON array of booked seat numbers',
  `total_fare` decimal(10,2) NOT NULL,
  `booking_fee` decimal(10,2) DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_status` enum('pending','paid','failed','refunded') DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_reference` varchar(100) DEFAULT NULL,
  `status` enum('pending','confirmed','cancelled','completed') DEFAULT 'pending',
  `booking_source` enum('web','mobile','api') DEFAULT 'web',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `booking_reference` (`booking_reference`),
  KEY `user_id` (`user_id`),
  KEY `schedule_id` (`schedule_id`),
  KEY `travel_date` (`travel_date`),
  KEY `status` (`status`),
  KEY `payment_status` (`payment_status`),
  CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`schedule_id`) REFERENCES `bus_schedules` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `seats`
--

CREATE TABLE `seats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bus_id` int(11) NOT NULL,
  `seat_number` varchar(10) NOT NULL,
  `seat_type` enum('regular','premium','disabled_accessible') DEFAULT 'regular',
  `position` varchar(20) NOT NULL COMMENT 'Position like window, aisle, etc.',
  `status` enum('active','blocked','maintenance') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `bus_seat_unique` (`bus_id`,`seat_number`),
  KEY `bus_id` (`bus_id`),
  KEY `status` (`status`),
  CONSTRAINT `seats_ibfk_1` FOREIGN KEY (`bus_id`) REFERENCES `buses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `role` enum('super_admin','admin','operator') DEFAULT 'admin',
  `permissions` text DEFAULT NULL COMMENT 'JSON array of specific permissions',
  `status` enum('active','inactive') DEFAULT 'active',
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `role` (`role`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Insert sample data
--

-- Sample operators
INSERT INTO `operators` (`name`, `contact_number`, `email`, `address`, `license_number`) VALUES
('Lanka Express Transport', '+94112345678', 'info@lankaexpress.lk', 'No. 123, Galle Road, Colombo 03', 'LET001'),
('Island Wide Travels', '+94112345679', 'contact@islandwide.lk', 'No. 456, Kandy Road, Colombo 07', 'IWT002'),
('Comfort Line Buses', '+94112345680', 'booking@comfortline.lk', 'No. 789, Negombo Road, Colombo 13', 'CLB003');

-- Sample routes
INSERT INTO `routes` (`origin`, `destination`, `distance`, `estimated_duration`) VALUES
('Colombo', 'Kandy', 115.00, '3h 30m'),
('Colombo', 'Galle', 119.00, '2h 45m'),
('Colombo', 'Matara', 160.00, '4h 00m'),
('Kandy', 'Nuwara Eliya', 77.00, '2h 30m'),
('Colombo', 'Anuradhapura', 205.00, '4h 30m'),
('Galle', 'Matara', 41.00, '1h 15m'),
('Kandy', 'Batticaloa', 150.00, '4h 00m'),
('Colombo', 'Jaffna', 396.00, '8h 00m');

-- Sample buses
INSERT INTO `buses` (`bus_number`, `bus_type`, `total_seats`, `amenities`, `operator_id`) VALUES
('WP-CAB-1234', 'AC', 45, 'Air Conditioning, WiFi, USB Charging, Reclining Seats', 1),
('WP-CAB-5678', 'Non-AC', 52, 'Comfortable Seats, Music System', 1),
('CP-EF-9012', 'Luxury', 32, 'Air Conditioning, WiFi, USB Charging, Entertainment System, Snacks', 2),
('WP-GH-3456', 'Semi-Luxury', 40, 'Air Conditioning, Comfortable Seats, Music System', 2),
('CP-IJ-7890', 'AC', 48, 'Air Conditioning, WiFi, USB Charging', 3),
('WP-KL-2345', 'Non-AC', 55, 'Comfortable Seats, Music System', 3);

-- Sample bus schedules
INSERT INTO `bus_schedules` (`bus_id`, `route_id`, `departure_time`, `arrival_time`, `fare`) VALUES
-- Colombo to Kandy
(1, 1, '06:00:00', '09:30:00', 250.00),
(1, 1, '10:00:00', '13:30:00', 250.00),
(1, 1, '14:00:00', '17:30:00', 250.00),
(1, 1, '18:00:00', '21:30:00', 280.00),
(2, 1, '07:00:00', '10:30:00', 200.00),
(2, 1, '15:00:00', '18:30:00', 200.00),

-- Colombo to Galle
(3, 2, '06:30:00', '09:15:00', 300.00),
(3, 2, '11:00:00', '13:45:00', 300.00),
(3, 2, '16:00:00', '18:45:00', 320.00),
(4, 2, '08:00:00', '10:45:00', 180.00),
(4, 2, '13:00:00', '15:45:00', 180.00),

-- Colombo to Matara
(5, 3, '05:30:00', '09:30:00', 350.00),
(5, 3, '12:00:00', '16:00:00', 350.00),
(6, 3, '07:30:00', '11:30:00', 250.00),
(6, 3, '14:30:00', '18:30:00', 250.00),

-- Kandy to Nuwara Eliya
(1, 4, '08:00:00', '10:30:00', 150.00),
(1, 4, '14:00:00', '16:30:00', 150.00),
(2, 4, '09:00:00', '11:30:00', 120.00),
(2, 4, '15:00:00', '17:30:00', 120.00);

-- Sample seats for buses
INSERT INTO `seats` (`bus_id`, `seat_number`, `seat_type`, `position`) VALUES
-- Bus 1 (45 seats)
(1, '1A', 'regular', 'window'),
(1, '1B', 'regular', 'aisle'),
(1, '1C', 'regular', 'aisle'),
(1, '1D', 'regular', 'window'),
(1, '2A', 'regular', 'window'),
(1, '2B', 'regular', 'aisle'),
(1, '2C', 'regular', 'aisle'),
(1, '2D', 'regular', 'window'),
-- Add more seats as needed...

-- Sample admin user (password: admin123)
INSERT INTO `admin_users` (`username`, `email`, `password_hash`, `full_name`, `role`) VALUES
('admin', 'admin@lankatransit.lk', '$argon2id$v=19$m=65536,t=4,p=3$example$hash', 'System Administrator', 'super_admin');

COMMIT;
