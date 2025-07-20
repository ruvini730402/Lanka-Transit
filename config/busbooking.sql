-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 20, 2025 at 05:36 PM
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
-- Database: `busbooking`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
CREATE TABLE IF NOT EXISTS `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

DROP TABLE IF EXISTS `bookings`;
CREATE TABLE IF NOT EXISTS `bookings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_id` varchar(20) DEFAULT NULL,
  `seat` int(11) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `nic` varchar(50) DEFAULT NULL,
  `gender` enum('female','male','undisclosed') DEFAULT NULL,
  `booked_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `booking_id`, `seat`, `phone`, `name`, `nic`, `gender`, `booked_at`) VALUES
(1, 'BID6851b58465838', 0, '0764974805', 'ruvini', '200062300283', 'female', '2025-06-17 18:35:48'),
(2, 'BID6851b83745bc1', 4, '0764974805', 'ruvini', '200062300283', 'female', '2025-06-17 18:47:19'),
(3, 'BID6851babf039d4', 11, '071490', 'kavi', '200067300200', 'undisclosed', '2025-06-17 18:58:07'),
(4, 'BID68641f506c431', 23, '0764974805', 'ruvini', '200067300200', 'male', '2025-07-01 17:48:00'),
(5, 'BID68667c3c72276', 13, '0764974805', 'ruvini', '200067300200', 'male', '2025-07-03 12:49:00'),
(6, 'BID6866c0da5cca9', 32, '0764974505', 'rtt', '200067300200', 'undisclosed', '2025-07-03 17:41:46'),
(7, 'BID6866c2ea8b121', 20, '0764974505', 'ruvini', '200067300200', 'female', '2025-07-03 17:50:34'),
(8, 'BID6870017f3aa76', 28, '0764974505', 'ruvini', '200067300267', 'male', '2025-07-10 18:07:59'),
(9, 'BID687001d9d899e', 8, '0764974505', 'ruvini', '200067300267', 'female', '2025-07-10 18:09:29'),
(10, 'BID68700475b4728', 12, '0752336899', 'kav', '200068700287', 'male', '2025-07-10 18:20:37'),
(11, 'BID687007f08a05a', 24, '0752336899', 'kav', '200068700287', 'male', '2025-07-10 18:35:28'),
(12, 'BID68749d78aee05', 9, '0713725639', 'Kavishka', '200067300V', 'female', '2025-07-14 06:02:32'),
(13, 'BID6874d3f0b5304', 21, '0714902001', 'sadun', '200062300678', 'female', '2025-07-14 09:54:56'),
(14, 'BID6874d7890e247', 22, '0714902001', 'Nilmini', '200062300678', 'female', '2025-07-14 10:10:17'),
(15, 'BID6874d7c2908f4', 10, '0714902001', 'Nilmini', '200062300678', 'undisclosed', '2025-07-14 10:11:14'),
(16, 'BID6874da4591a34', 26, '0714902001', 'Nilmini', '200062300678', 'male', '2025-07-14 10:21:57'),
(17, 'BID6874da59d0b9a', 18, '0714902001', 'Nilmini', '200067300200', 'male', '2025-07-14 10:22:17'),
(18, 'BID6874da6fba88e', 19, '0714902001', 'Nilmini', '200067300200', 'undisclosed', '2025-07-14 10:22:39'),
(19, 'BID6874da7fbdf8f', 15, '0714902001', 'Nilmini', '200067300200', 'female', '2025-07-14 10:22:55'),
(20, 'BID6874da8c0e9f1', 16, '0714902001', 'Nilmini', '200067300200', 'female', '2025-07-14 10:23:08'),
(21, 'BID6874da9d69fc7', 31, '0714902001', 'Nilmini', '200067300200', 'male', '2025-07-14 10:23:25'),
(22, 'BID6874dab2e5287', 14, '0714902001', 'Nilmini', '200067300200', 'male', '2025-07-14 10:23:46'),
(23, 'BID6874daf9f24f2', 5, '0714902677', 'Chalani', '200067300300', 'female', '2025-07-14 10:24:57'),
(24, 'BID687743dbbb00d', 35, '0713725284', 'Lahiru Chandika', '199403400456', 'male', '2025-07-16 06:16:59'),
(25, 'BID687ce90c16f82', 25, '0764974805', 'Kavishka', '200062300283', 'female', '2025-07-20 13:03:08');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('registered','admin') DEFAULT 'registered',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reset_token` varchar(255) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`, `reset_token`, `token_expiry`) VALUES
(1, 'ruvini ', 'ruvini@gmail.com', '$2y$10$i80i0pbq4xbl76prAUvkDOXDuumoWSwwsDXu1HuRpEKxfo73KFvI.', 'registered', '2025-07-11 04:49:11', NULL, NULL),
(2, 'Kavishka', 'kavishka@gmail.com', '$2y$10$CYccqKUVpX/mDP8K7pTaYOfvXfNtD0m0xNKhM6/P2DkQFYb8ckqPa', 'registered', '2025-07-11 05:31:13', NULL, NULL),
(3, 'Kavishka', 'ruvinikanchana2021al@gmail.com', '$2y$10$wdZw503cIYxWNj.k/V/nHeQuMOpi5z3ZyqoGuZsvdj7wHI.aROO.6', 'registered', '2025-07-12 02:31:27', '7f91b4bde79ecdbf490c6180032488da1fef925665b6749d5ebf672acad799fc', '2025-07-18 05:32:10'),
(4, 'devindi', 'devindi8@gmail.com', '$2y$10$ulWu/hFyJdrBsw9NFU0nyuc2YVx6Xr4z93Ohm490CyzpnM91FfScu', 'registered', '2025-07-12 14:33:46', NULL, NULL),
(5, 'Test', 'test1@gmail.com', '$2y$10$.GZiTo4FQPlNut2vaAfhe.oWIuv/26dDfMcdZf8q86GioHpNgIYRW', 'registered', '2025-07-14 06:12:57', NULL, NULL),
(7, 'Nilmini', 'nilmini8@gmail.com', '$2y$10$Mo9IR6QzzSj./0we4/bDE.k8ixtlsDPaEBnq361X5Tgq.VRL8WJ7C', 'registered', '2025-07-14 13:16:26', NULL, NULL),
(9, 'ishara', 'ishara4@gmail.com', '$2y$10$6kLZYZY1nhaOXQiWIrvgOOELFpfjYvZ2XV5uFJFqvxeJQgb4nDnWa', 'registered', '2025-07-14 13:19:42', NULL, NULL),
(12, 'Sadun Pushpika', 'sadunpu8@gmail.com', '$2y$10$ZoCXsUZ0LHjzX5i/9I5WoeRdtpSIcnZiu4SPYDADPCA4b/pWUXxQu', 'registered', '2025-07-14 16:25:07', NULL, NULL),
(14, 'kasundi', 'kasundi8@gmail.com', '$2y$10$NFlvSB/9dSSpn7RPHIA3YO.n8x.4fKq/tiNLTosDfEDwyVOa8QGsq', 'registered', '2025-07-14 18:12:09', NULL, NULL),
(15, 'lahiru', 'lahiru4@gmail.com', '$2y$10$A2zAARWOVW5nBIbvp4tct.bn49f3v5z8g4XEwX4iq3I04PrQ2eP1S', 'registered', '2025-07-14 18:41:20', NULL, NULL),
(16, 'Lahiru Chandika', 'lahiruni8@gmail.com', '$2y$10$3PiNlRu6oOe9ASv.Wvn4c.9/tIRAZUmblXAnwwbLedwMhlPBlG9Ue', 'registered', '2025-07-15 02:36:58', NULL, NULL),
(17, 'Kavishka Navod', 'kavishkanavodr@gmail.com', '$2y$10$GjKA1ovQ2eFar.VBcCZehucbK9QOsntiYc5Dv6FLWFqSYX1DfMMDi', 'registered', '2025-07-15 02:44:52', '9b738d956dc8578ea497c6b675965ec98d776aad1ea5ce28f7212a0613a3789d', '2025-07-16 20:13:17'),
(18, 'lanka Transit', 'lankatransitinfo@gmail.com', '$2y$10$gtDqdaUm9dsfUhF2sY8sfOh6wC.PfysQFLnc1rjbHZct821AmsbKu', 'registered', '2025-07-16 16:17:55', NULL, NULL),
(19, 'lanka taransit', 'lankatrasitinfo@gmail.com', '$2y$10$xMthNkBwQpyfdCtVNrK7YeqT6fAiE98XqVtvBUSkxPvbnz73oLTIK', 'registered', '2025-07-16 16:22:52', '63a88914172fd9681a1d88cfee984b635fd46941452a162c108ce321dbf33444', '2025-07-16 20:48:45');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
