-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 24, 2025 at 02:11 PM
-- Server version: 10.11.14-MariaDB
-- PHP Version: 8.4.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `anagatha_transit`
--

-- --------------------------------------------------------

--
-- Table structure for table `Admin`
--

CREATE TABLE `Admin` (
  `ID` int(11) NOT NULL,
  `Email` varchar(60) NOT NULL,
  `PasswordHash` varchar(255) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `PhoneNumber` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `Admin`
--

INSERT INTO `Admin` (`ID`, `Email`, `PasswordHash`, `Name`, `PhoneNumber`) VALUES
(1, 'admin@lankatransit.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', '0771234567');

-- --------------------------------------------------------

--
-- Table structure for table `Admin_2`
--

CREATE TABLE `Admin_2` (
  `admin_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `Admin_2`
--

INSERT INTO `Admin_2` (`admin_id`, `created_at`, `updated_at`) VALUES
(1, '2025-07-29 22:00:45', '2025-07-29 22:00:45');

-- --------------------------------------------------------

--
-- Table structure for table `Announcements`
--

CREATE TABLE `Announcements` (
  `ID` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `Announcements`
--

INSERT INTO `Announcements` (`ID`, `title`, `message`, `created_at`, `updated_at`) VALUES
(1, 'Service Update', 'New bus route from Badulla to Matara now available with enhanced comfort features.', '2025-07-29 22:00:45', '2025-07-29 22:00:45'),
(2, 'Maintenance Notice', 'Scheduled maintenance on Route 1 buses every Sunday from 6 AM to 8 AM.', '2025-07-29 22:00:45', '2025-07-29 22:00:45'),
(3, 'Holiday Special', 'Special discount rates available for advance bookings during holiday season.', '2025-07-29 22:00:45', '2025-07-29 22:00:45'),
(7, 'Safety Protocol Update', 'New safety measures implemented across all routes. Face masks required.', '2025-07-29 22:01:02', '2025-07-29 22:01:02'),
(8, 'Route Expansion', 'We are pleased to announce new direct routes to Jaffna and Trincomalee starting next month.', '2025-07-29 22:01:02', '2025-07-29 22:01:02'),
(9, 'Customer Service', 'Our 24/7 customer service hotline is now available at 1234 for all inquiries and support.', '2025-07-29 22:01:02', '2025-07-29 22:01:02'),
(10, 'Database Initialization Complete - August 2025 Extended', 'Lanka Transit database successfully initialized on 2025-07-29 22:01:02 with complete schema and comprehensive sample data covering the entire month of August 2025. Includes full daily schedules for all buses (Aug 1-31), extensive booking records, incident reports, and extended user/admin functionality.', '2025-07-29 22:01:02', '2025-07-29 22:01:02'),
(16, 'test title', 'test message', '2025-08-20 17:52:41', '2025-08-20 17:52:41');

-- --------------------------------------------------------

--
-- Table structure for table `Booking`
--

CREATE TABLE `Booking` (
  `ID` int(11) NOT NULL,
  `UserId` int(11) DEFAULT NULL,
  `BusID` int(11) DEFAULT NULL,
  `SeatNumber` varchar(6) NOT NULL,
  `BookingTime` timestamp NULL DEFAULT current_timestamp(),
  `Status` enum('confirmed','cancelled','completed') NOT NULL DEFAULT 'confirmed',
  `PhoneNumber` varchar(10) NOT NULL,
  `Fare` decimal(10,2) NOT NULL,
  `TravelDate` date DEFAULT NULL,
  `Origin` varchar(50) DEFAULT NULL,
  `Destination` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `Booking`
--

INSERT INTO `Booking` (`ID`, `UserId`, `BusID`, `SeatNumber`, `BookingTime`, `Status`, `PhoneNumber`, `Fare`, `TravelDate`, `Origin`, `Destination`) VALUES
(312, NULL, 3, '40', '2025-09-24 08:39:49', 'confirmed', '0712345678', 580.00, '2025-09-22', NULL, NULL),
(313, NULL, 1, '28', '2025-09-24 09:04:36', 'confirmed', '0712345678', 580.00, '2025-09-24', 'Badulla', 'Devinuwara'),
(314, NULL, 3, '30', '2025-09-24 09:06:59', 'confirmed', '0712345678', 580.00, '2025-10-03', 'Wellawaya', 'Ella'),
(315, NULL, 4, '29', '2025-09-24 09:31:23', 'confirmed', '0712345678', 640.00, '2025-10-03', 'Devinuwara', 'Thanamalvila'),
(316, NULL, 2, '7', '2025-09-24 09:34:25', 'confirmed', '0712345678', 640.00, '2025-09-24', 'Badulla', 'Dickwella'),
(317, NULL, 1, '25', '2025-09-24 09:37:17', 'confirmed', '0712345678', 580.00, '2025-09-24', 'Badulla', 'Dickwella'),
(318, NULL, 1, '12', '2025-09-24 10:44:39', 'confirmed', '0712345678', 580.00, '2025-09-25', 'Badulla', 'Tangalle'),
(319, NULL, 3, '31', '2025-09-24 13:12:46', 'confirmed', '0712345678', 580.00, '2025-09-24', 'Wellawaya', 'Badulla'),
(320, NULL, 2, '1', '2025-09-24 13:15:43', 'confirmed', '0712345678', 640.00, '2025-10-01', 'Badulla', 'Lunugamvehera'),
(321, NULL, 3, '21', '2025-09-24 13:31:59', 'confirmed', '0712345678', 580.00, '2025-09-24', 'Wellawaya', 'Badulla');

-- --------------------------------------------------------

--
-- Table structure for table `BookingCancellation`
--

CREATE TABLE `BookingCancellation` (
  `ID` int(11) NOT NULL,
  `BookingID` int(11) NOT NULL,
  `UserID` int(11) DEFAULT NULL,
  `CancellationReason` text NOT NULL,
  `RequestedAt` timestamp NULL DEFAULT current_timestamp(),
  `Status` enum('pending','refunded','declined') NOT NULL DEFAULT 'pending',
  `ProcessedBy` int(11) DEFAULT NULL,
  `ProcessedAt` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `BookingCancellation`
--

INSERT INTO `BookingCancellation` (`ID`, `BookingID`, `UserID`, `CancellationReason`, `RequestedAt`, `Status`, `ProcessedBy`, `ProcessedAt`) VALUES
(1, 1, 1, 'Change of travel plans due to emergency', '2025-08-15 03:24:51', 'pending', NULL, NULL),
(2, 2, NULL, 'Medical emergency - unable to travel', '2025-08-15 03:24:51', 'refunded', 1, '2025-08-15 03:24:51'),
(3, 3, 1, 'Double booking mistake', '2025-08-15 03:24:51', 'declined', 1, '2025-08-15 03:24:51'),
(4, 194, 1, 'hhjl', '2025-09-13 13:15:15', 'pending', NULL, NULL),
(5, 194, 1, 'hhjl', '2025-09-13 13:16:39', 'pending', NULL, NULL),
(6, 194, 1, 'wew', '2025-09-13 13:54:42', 'pending', NULL, NULL),
(7, 206, 1, 'asdfasf', '2025-09-14 07:11:02', 'pending', NULL, NULL),
(8, 206, 1, 'asdfasf', '2025-09-14 08:31:56', 'pending', NULL, NULL),
(9, 200, 1, 'eftew', '2025-09-14 08:46:11', 'pending', NULL, NULL),
(10, 127, 1, 'hfdj fgjejh fhjpeti', '2025-09-17 09:56:59', 'pending', NULL, NULL),
(11, 127, 1, 'hfdj fgjejh fhjpeti', '2025-09-17 09:58:01', 'pending', NULL, NULL),
(12, 127, 1, 'po', '2025-09-17 10:38:08', 'pending', NULL, NULL),
(13, 127, 1, 'cancel', '2025-09-17 11:04:10', 'pending', NULL, NULL),
(14, 127, 1, 'cancel', '2025-09-17 11:04:30', 'pending', NULL, NULL),
(15, 127, 1, 'cancel', '2025-09-17 11:08:25', 'pending', NULL, NULL),
(16, 127, 1, 'cancel', '2025-09-17 11:12:37', 'pending', NULL, NULL),
(17, 127, 1, 'test', '2025-09-17 11:17:50', 'pending', NULL, NULL),
(18, 320, 1, 'test cancel', '2025-09-24 13:16:04', 'pending', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `Booking_2`
--

CREATE TABLE `Booking_2` (
  `booking_id` int(11) NOT NULL,
  `gender` enum('male','female','undisclosed') DEFAULT 'undisclosed',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `Booking_2`
--

INSERT INTO `Booking_2` (`booking_id`, `gender`, `created_at`, `updated_at`) VALUES
(312, 'male', '2025-09-24 08:39:49', '2025-09-24 08:39:49'),
(313, 'male', '2025-09-24 09:04:36', '2025-09-24 09:04:36'),
(314, 'male', '2025-09-24 09:06:59', '2025-09-24 09:06:59'),
(315, 'male', '2025-09-24 09:31:23', '2025-09-24 09:31:23'),
(316, 'female', '2025-09-24 09:34:25', '2025-09-24 09:34:25'),
(317, 'male', '2025-09-24 09:37:17', '2025-09-24 09:37:17'),
(318, 'male', '2025-09-24 10:44:39', '2025-09-24 10:44:39'),
(319, 'male', '2025-09-24 13:12:46', '2025-09-24 13:12:46'),
(320, 'female', '2025-09-24 13:15:43', '2025-09-24 13:15:43'),
(321, 'female', '2025-09-24 13:31:59', '2025-09-24 13:31:59');

-- --------------------------------------------------------

--
-- Table structure for table `Bus`
--

CREATE TABLE `Bus` (
  `ID` int(11) NOT NULL,
  `RouteId` int(11) DEFAULT NULL,
  `AdminId` int(11) DEFAULT NULL,
  `BusNumber` varchar(7) NOT NULL,
  `Capacity` int(11) NOT NULL,
  `LastUpdate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `Bus`
--

INSERT INTO `Bus` (`ID`, `RouteId`, `AdminId`, `BusNumber`, `Capacity`, `LastUpdate`) VALUES
(1, 1, NULL, 'NB-1237', 49, '2025-08-19'),
(2, 1, 1, 'NB-5678', 49, '2024-07-19'),
(3, 2, NULL, 'NB-9012', 49, '2025-08-19'),
(4, 2, NULL, 'NB-3456', 49, '2025-08-19'),
(6, 2, 1, 'NB-2468', 49, '2025-08-31'),
(15, 2, 1, 'NB-1234', 49, '2025-08-30'),
(16, 2, 1, 'NB-1233', 54, '2025-08-30'),
(17, 2, 1, 'NB-1223', 49, '2025-08-30'),
(18, 2, 1, 'NB-1534', 49, '2025-08-30'),
(19, 1, 1, 'NB-1565', 49, '2025-08-31'),
(20, 1, 1, 'NB-1001', 49, '2025-09-04'),
(21, 1, 1, 'NB-1002', 54, '2025-09-04'),
(22, 2, 1, 'NB-2001', 49, '2025-09-04'),
(23, 2, 1, 'NB-2002', 54, '2025-09-13');

-- --------------------------------------------------------

--
-- Table structure for table `Feedback`
--

CREATE TABLE `Feedback` (
  `ID` int(11) NOT NULL,
  `UserId` int(11) DEFAULT NULL,
  `BusId` int(11) DEFAULT NULL,
  `BookingId` int(11) DEFAULT NULL,
  `Comment` text DEFAULT NULL,
  `Rating` int(11) DEFAULT NULL,
  `CreatedDate` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `Feedback`
--

INSERT INTO `Feedback` (`ID`, `UserId`, `BusId`, `BookingId`, `Comment`, `Rating`, `CreatedDate`) VALUES
(3, NULL, 1, NULL, 'jjjjjjjjjjjjjjjjj', 5, '2025-07-30'),
(6, NULL, 6, NULL, '', 4, '2025-07-30'),
(9, NULL, 4, NULL, 'ydeuyu', 4, '2025-07-30'),
(10, NULL, 4, NULL, 'ppppppppppppppp', 3, '2025-07-30'),
(12, NULL, 6, NULL, 'test', 4, '2025-07-31'),
(17, 1, 1, NULL, 'Excellent service! The bus was on time and very comfortable.', 5, '2025-09-01'),
(18, 2, 1, NULL, 'Good journey overall. Driver was professional and courteous.', 4, '2025-09-01'),
(19, 3, 1, NULL, 'Clean bus with comfortable seats. Will book again.', 5, '2025-09-01'),
(20, 4, 2, NULL, 'Luxury bus experience was worth the extra cost. Highly recommended.', 5, '2025-09-01'),
(21, 5, 2, NULL, 'Air conditioning worked perfectly. Great service.', 5, '2025-09-01'),
(22, 1, 3, NULL, 'Smooth ride from Matara to Badulla. Very satisfied.', 4, '2025-09-02'),
(23, 2, 3, NULL, 'Punctual departure and arrival. Good value for money.', 4, '2025-09-02'),
(24, 6, 4, NULL, 'Premium service as expected. Comfortable journey.', 5, '2025-09-02'),
(25, 1, 1, NULL, 'Another great trip with Lanka Transit. Reliable service.', 5, '2025-10-01'),
(26, 2, 1, NULL, 'Consistent quality service. Keep up the good work!', 4, '2025-10-01'),
(27, 3, 2, NULL, 'Luxury bus was amazing. Perfect for long distance travel.', 5, '2025-10-01'),
(28, 4, 2, NULL, 'Excellent service and comfortable seating.', 5, '2025-10-01'),
(29, 5, 3, NULL, 'Good service from Matara route. Recommended.', 4, '2025-10-02'),
(30, 6, 3, NULL, 'Pleasant journey with professional crew.', 4, '2025-10-02'),
(31, 1, 4, NULL, 'Premium experience was outstanding.', 5, '2025-10-02'),
(32, 2, 4, NULL, 'Worth every rupee. Great service!', 5, '2025-10-02'),
(33, 1, 1, NULL, 'Excellent service! The bus was on time and very comfortable.', 5, '2025-09-01'),
(34, 2, 1, NULL, 'Good journey overall. Driver was professional and courteous.', 4, '2025-09-01'),
(35, 3, 1, NULL, 'Clean bus with comfortable seats. Will book again.', 5, '2025-09-01'),
(36, 4, 2, NULL, 'Luxury bus experience was worth the extra cost. Highly recommended.', 5, '2025-09-01'),
(37, 5, 2, NULL, 'Air conditioning worked perfectly. Great service.', 5, '2025-09-01'),
(38, 1, 3, NULL, 'Smooth ride from Matara to Badulla. Very satisfied.', 4, '2025-09-02'),
(39, 2, 3, NULL, 'Punctual departure and arrival. Good value for money.', 4, '2025-09-02'),
(40, 6, 4, NULL, 'Premium service as expected. Comfortable journey.', 5, '2025-09-02'),
(41, 1, 4, NULL, 'Outstanding luxury bus service. Professional crew.', 5, '2025-09-02'),
(42, 3, 1, NULL, 'Reliable service as always. Clean and comfortable bus.', 5, '2025-09-03'),
(43, 4, 1, NULL, 'Driver was very skilled and safety-conscious.', 4, '2025-09-03'),
(44, 5, 2, NULL, 'Premium amenities made the long journey enjoyable.', 5, '2025-09-03'),
(45, 6, 2, NULL, 'Excellent customer service from booking to arrival.', 5, '2025-09-03'),
(46, 2, 3, NULL, 'Good experience with Matara route. Recommended.', 4, '2025-09-04'),
(47, 3, 3, NULL, 'Bus was clean and departed exactly on time.', 4, '2025-09-04'),
(48, 1, 4, NULL, 'Luxury service exceeded expectations. Will book again.', 5, '2025-09-04'),
(49, 4, 4, NULL, 'Comfortable seats and smooth ride. Great value.', 5, '2025-09-04'),
(50, 5, 1, NULL, 'Consistent quality service throughout my monthly travels.', 5, '2025-09-08'),
(51, 6, 1, NULL, 'Always on time, clean buses, professional drivers.', 4, '2025-09-08'),
(52, 1, 2, NULL, 'Luxury bus amenities are top-notch. Highly satisfied.', 5, '2025-09-08'),
(53, 2, 2, NULL, 'Premium service justifies the slightly higher fare.', 5, '2025-09-08'),
(54, 3, 3, NULL, 'Regular commuter - Lanka Transit never disappoints.', 5, '2025-09-09'),
(55, 4, 3, NULL, 'Reliable schedule and comfortable seating.', 4, '2025-09-09'),
(56, 5, 4, NULL, 'Best bus service on this route. Excellent staff.', 5, '2025-09-09'),
(57, 6, 4, NULL, 'Luxury travel experience at reasonable prices.', 5, '2025-09-09'),
(58, 1, 1, NULL, 'Month-end travel was smooth and hassle-free.', 4, '2025-09-15'),
(59, 2, 1, NULL, 'Appreciate the consistent service quality.', 4, '2025-09-15'),
(60, 3, 2, NULL, 'Luxury bus comfort made the journey pleasant.', 5, '2025-09-15'),
(61, 4, 2, NULL, 'Professional service and timely arrival.', 5, '2025-09-15'),
(62, 5, 3, NULL, 'Regular user - service quality remains excellent.', 5, '2025-09-16'),
(63, 6, 3, NULL, 'Clean buses and courteous staff. Very satisfied.', 4, '2025-09-16'),
(64, 1, 4, NULL, 'Premium experience worth every rupee paid.', 5, '2025-09-16'),
(65, 2, 4, NULL, 'Luxury amenities and professional service.', 5, '2025-09-16'),
(66, 3, 1, NULL, 'End of month travel was as good as always.', 4, '2025-09-22'),
(67, 4, 1, NULL, 'Dependable service for regular commuters.', 4, '2025-09-22'),
(68, 5, 2, NULL, 'Luxury bus exceeded expectations once again.', 5, '2025-09-22'),
(69, 6, 2, NULL, 'Premium service with attention to detail.', 5, '2025-09-22'),
(70, 1, 3, NULL, 'Last September trip was excellent as usual.', 5, '2025-09-29'),
(71, 2, 3, NULL, 'Consistent quality throughout the month.', 4, '2025-09-29'),
(72, 3, 4, NULL, 'Luxury service maintained high standards.', 5, '2025-09-29'),
(73, 4, 4, NULL, 'Perfect end to a month of great travel experiences.', 5, '2025-09-29'),
(74, 1, 1, NULL, 'October started with excellent service as expected.', 5, '2025-10-01'),
(75, 2, 1, NULL, 'New month, same reliable Lanka Transit quality.', 4, '2025-10-01'),
(76, 3, 2, NULL, 'Luxury bus comfort perfect for October travels.', 5, '2025-10-01'),
(77, 4, 2, NULL, 'Premium service continues to impress.', 5, '2025-10-01'),
(78, 5, 3, NULL, 'October Matara route service off to great start.', 4, '2025-10-02'),
(79, 6, 3, NULL, 'Consistent service quality into new month.', 4, '2025-10-02'),
(80, 1, 4, NULL, 'Luxury experience remains outstanding in October.', 5, '2025-10-02'),
(81, 2, 4, NULL, 'Premium amenities and professional crew.', 5, '2025-10-02'),
(82, 3, 1, NULL, 'Early October travel was smooth and comfortable.', 4, '2025-10-03'),
(83, 4, 1, NULL, 'Reliable service continues in new month.', 4, '2025-10-03'),
(84, 5, 2, NULL, 'Luxury bus experience consistently excellent.', 5, '2025-10-03'),
(85, 6, 2, NULL, 'Premium service worth the investment.', 5, '2025-10-03'),
(86, 1, 3, NULL, 'Mid-October travel maintains high standards.', 5, '2025-10-08'),
(87, 2, 3, NULL, 'Service quality remains consistent throughout.', 4, '2025-10-08'),
(88, 3, 4, NULL, 'Luxury amenities perfect for longer journeys.', 5, '2025-10-08'),
(89, 4, 4, NULL, 'Professional service and comfortable travel.', 5, '2025-10-08'),
(90, 5, 1, NULL, 'October mid-month service as reliable as ever.', 4, '2025-10-09'),
(91, 6, 1, NULL, 'Consistent departure times and clean buses.', 4, '2025-10-09'),
(92, 1, 2, NULL, 'Luxury service maintains premium standards.', 5, '2025-10-09'),
(93, 2, 2, NULL, 'Excellent value for luxury bus travel.', 5, '2025-10-09'),
(94, 3, 3, NULL, 'Regular October travel - always satisfied.', 5, '2025-10-15'),
(95, 4, 3, NULL, 'Dependable service for monthly commuting.', 4, '2025-10-15'),
(96, 5, 4, NULL, 'Luxury experience consistently impressive.', 5, '2025-10-15'),
(97, 6, 4, NULL, 'Premium service justifies the fare difference.', 5, '2025-10-15'),
(98, 1, 1, NULL, 'Third week October travel was excellent.', 4, '2025-10-16'),
(99, 2, 1, NULL, 'Service quality never disappoints.', 4, '2025-10-16'),
(100, 3, 2, NULL, 'Luxury amenities make long journeys pleasant.', 5, '2025-10-16'),
(101, 4, 2, NULL, 'Professional crew and comfortable seating.', 5, '2025-10-16'),
(102, 5, 3, NULL, 'Late October service maintains excellence.', 5, '2025-10-22'),
(103, 6, 3, NULL, 'Reliable partner for regular travel needs.', 4, '2025-10-22'),
(104, 1, 4, NULL, 'Luxury experience worth every rupee.', 5, '2025-10-22'),
(105, 2, 4, NULL, 'Premium service with attention to details.', 5, '2025-10-22'),
(106, 3, 1, NULL, 'Month-end October travel as good as expected.', 4, '2025-10-29'),
(107, 4, 1, NULL, 'Consistent service throughout October.', 4, '2025-10-29'),
(108, 5, 2, NULL, 'Luxury service maintained high standards.', 5, '2025-10-29'),
(109, 6, 2, NULL, 'Premium experience exceeded expectations.', 5, '2025-10-29'),
(110, 1, 3, NULL, 'October finale with excellent service.', 5, '2025-10-31'),
(111, 2, 3, NULL, 'Perfect end to a month of great journeys.', 4, '2025-10-31'),
(112, 3, 4, NULL, 'Luxury service closed October on high note.', 5, '2025-10-31'),
(113, 4, 4, NULL, 'Outstanding premium experience to end October.', 5, '2025-10-31'),
(114, 1, 4, NULL, '', 3, '2025-09-12'),
(115, 1, 1, NULL, '', 2, '2025-09-14'),
(116, 1, 1, NULL, '', 3, '2025-09-14'),
(117, 1, 1, NULL, '', 3, '2025-09-14'),
(118, 1, 1, NULL, '', 3, '2025-09-14'),
(119, 1, 1, NULL, '', 2, '2025-09-14'),
(120, 1, 1, NULL, '', 2, '2025-09-14'),
(121, 1, 6, NULL, '', 2, '2025-09-14'),
(122, 1, 6, NULL, '', 2, '2025-09-14'),
(123, 1, 6, NULL, '', 2, '2025-09-14'),
(124, 1, 2, NULL, '', 4, '2025-09-14'),
(125, 1, 1, NULL, '', 2, '2025-09-14'),
(126, 1, 6, NULL, '', 4, '2025-09-14'),
(127, 1, 1, NULL, '', 5, '2025-09-14'),
(128, 1, 1, NULL, '', 2, '2025-09-15'),
(129, 1, 1, NULL, '', 4, '2025-09-17'),
(130, 1, 1, NULL, '', 4, '2025-09-17'),
(131, 1, 1, NULL, '', 4, '2025-09-17'),
(132, 1, 1, NULL, '', 4, '2025-09-17'),
(133, 1, 1, NULL, '', 4, '2025-09-17'),
(134, 1, 1, NULL, '', 4, '2025-09-17'),
(135, 1, 1, NULL, '', 4, '2025-09-17'),
(136, 1, 1, NULL, '', 4, '2025-09-17'),
(137, 1, 1, NULL, '', 4, '2025-09-17'),
(138, 1, 1, NULL, 'Excellent service! The bus was on time and very comfortable.', 5, '2025-09-01'),
(139, 2, 1, NULL, 'Good journey overall. Driver was professional and courteous.', 4, '2025-09-01'),
(140, 3, 1, NULL, 'Clean bus with comfortable seats. Will book again.', 5, '2025-09-01'),
(141, 4, 2, NULL, 'Luxury bus experience was worth the extra cost. Highly recommended.', 5, '2025-09-01'),
(142, 5, 2, NULL, 'Air conditioning worked perfectly. Great service.', 5, '2025-09-01'),
(143, 1, 3, NULL, 'Smooth ride from Matara to Badulla. Very satisfied.', 4, '2025-09-02'),
(144, 2, 3, NULL, 'Punctual departure and arrival. Good value for money.', 4, '2025-09-02'),
(145, 6, 4, NULL, 'Premium service as expected. Comfortable journey.', 5, '2025-09-02'),
(146, 1, 4, NULL, 'Outstanding luxury bus service. Professional crew.', 5, '2025-09-02'),
(147, 3, 1, NULL, 'Reliable service as always. Clean and comfortable bus.', 5, '2025-09-03'),
(148, 4, 1, NULL, 'Driver was very skilled and safety-conscious.', 4, '2025-09-03'),
(149, 5, 2, NULL, 'Premium amenities made the long journey enjoyable.', 5, '2025-09-03'),
(150, 6, 2, NULL, 'Excellent customer service from booking to arrival.', 5, '2025-09-03'),
(151, 2, 3, NULL, 'Good experience with Matara route. Recommended.', 4, '2025-09-04'),
(152, 3, 3, NULL, 'Bus was clean and departed exactly on time.', 4, '2025-09-04'),
(153, 1, 4, NULL, 'Luxury service exceeded expectations. Will book again.', 5, '2025-09-04'),
(154, 4, 4, NULL, 'Comfortable seats and smooth ride. Great value.', 5, '2025-09-04'),
(155, 5, 1, NULL, 'Consistent quality service throughout my monthly travels.', 5, '2025-09-08'),
(156, 6, 1, NULL, 'Always on time, clean buses, professional drivers.', 4, '2025-09-08'),
(157, 1, 2, NULL, 'Luxury bus amenities are top-notch. Highly satisfied.', 5, '2025-09-08'),
(158, 2, 2, NULL, 'Premium service justifies the slightly higher fare.', 5, '2025-09-08'),
(159, 3, 3, NULL, 'Regular commuter - Lanka Transit never disappoints.', 5, '2025-09-09'),
(160, 4, 3, NULL, 'Reliable schedule and comfortable seating.', 4, '2025-09-09'),
(161, 5, 4, NULL, 'Best bus service on this route. Excellent staff.', 5, '2025-09-09'),
(162, 6, 4, NULL, 'Luxury travel experience at reasonable prices.', 5, '2025-09-09'),
(163, 1, 1, NULL, 'Month-end travel was smooth and hassle-free.', 4, '2025-09-15'),
(164, 2, 1, NULL, 'Appreciate the consistent service quality.', 4, '2025-09-15'),
(165, 3, 2, NULL, 'Luxury bus comfort made the journey pleasant.', 5, '2025-09-15'),
(166, 4, 2, NULL, 'Professional service and timely arrival.', 5, '2025-09-15'),
(167, 5, 3, NULL, 'Regular user - service quality remains excellent.', 5, '2025-09-16'),
(168, 6, 3, NULL, 'Clean buses and courteous staff. Very satisfied.', 4, '2025-09-16'),
(169, 1, 4, NULL, 'Premium experience worth every rupee paid.', 5, '2025-09-16'),
(170, 2, 4, NULL, 'Luxury amenities and professional service.', 5, '2025-09-16'),
(171, 3, 1, NULL, 'End of month travel was as good as always.', 4, '2025-09-22'),
(172, 4, 1, NULL, 'Dependable service for regular commuters.', 4, '2025-09-22'),
(173, 5, 2, NULL, 'Luxury bus exceeded expectations once again.', 5, '2025-09-22'),
(174, 6, 2, NULL, 'Premium service with attention to detail.', 5, '2025-09-22'),
(175, 1, 3, NULL, 'Last September trip was excellent as usual.', 5, '2025-09-29'),
(176, 2, 3, NULL, 'Consistent quality throughout the month.', 4, '2025-09-29'),
(177, 3, 4, NULL, 'Luxury service maintained high standards.', 5, '2025-09-29'),
(178, 4, 4, NULL, 'Perfect end to a month of great travel experiences.', 5, '2025-09-29'),
(179, 1, 1, NULL, 'October started with excellent service as expected.', 5, '2025-10-01'),
(180, 2, 1, NULL, 'New month, same reliable Lanka Transit quality.', 4, '2025-10-01'),
(181, 3, 2, NULL, 'Luxury bus comfort perfect for October travels.', 5, '2025-10-01'),
(182, 4, 2, NULL, 'Premium service continues to impress.', 5, '2025-10-01'),
(183, 5, 3, NULL, 'October Matara route service off to great start.', 4, '2025-10-02'),
(184, 6, 3, NULL, 'Consistent service quality into new month.', 4, '2025-10-02'),
(185, 1, 4, NULL, 'Luxury experience remains outstanding in October.', 5, '2025-10-02'),
(186, 2, 4, NULL, 'Premium amenities and professional crew.', 5, '2025-10-02'),
(187, 3, 1, NULL, 'Early October travel was smooth and comfortable.', 4, '2025-10-03'),
(188, 4, 1, NULL, 'Reliable service continues in new month.', 4, '2025-10-03'),
(189, 5, 2, NULL, 'Luxury bus experience consistently excellent.', 5, '2025-10-03'),
(190, 6, 2, NULL, 'Premium service worth the investment.', 5, '2025-10-03'),
(191, 1, 3, NULL, 'Mid-October travel maintains high standards.', 5, '2025-10-08'),
(192, 2, 3, NULL, 'Service quality remains consistent throughout.', 4, '2025-10-08'),
(193, 3, 4, NULL, 'Luxury amenities perfect for longer journeys.', 5, '2025-10-08'),
(194, 4, 4, NULL, 'Professional service and comfortable travel.', 5, '2025-10-08'),
(195, 5, 1, NULL, 'October mid-month service as reliable as ever.', 4, '2025-10-09'),
(196, 6, 1, NULL, 'Consistent departure times and clean buses.', 4, '2025-10-09'),
(197, 1, 2, NULL, 'Luxury service maintains premium standards.', 5, '2025-10-09'),
(198, 2, 2, NULL, 'Excellent value for luxury bus travel.', 5, '2025-10-09'),
(199, 3, 3, NULL, 'Regular October travel - always satisfied.', 5, '2025-10-15'),
(200, 4, 3, NULL, 'Dependable service for monthly commuting.', 4, '2025-10-15'),
(201, 5, 4, NULL, 'Luxury experience consistently impressive.', 5, '2025-10-15'),
(202, 6, 4, NULL, 'Premium service justifies the fare difference.', 5, '2025-10-15'),
(203, 1, 1, NULL, 'Third week October travel was excellent.', 4, '2025-10-16'),
(204, 2, 1, NULL, 'Service quality never disappoints.', 4, '2025-10-16'),
(205, 3, 2, NULL, 'Luxury amenities make long journeys pleasant.', 5, '2025-10-16'),
(206, 4, 2, NULL, 'Professional crew and comfortable seating.', 5, '2025-10-16'),
(207, 5, 3, NULL, 'Late October service maintains excellence.', 5, '2025-10-22'),
(208, 6, 3, NULL, 'Reliable partner for regular travel needs.', 4, '2025-10-22'),
(209, 1, 4, NULL, 'Luxury experience worth every rupee.', 5, '2025-10-22'),
(210, 2, 4, NULL, 'Premium service with attention to details.', 5, '2025-10-22'),
(211, 3, 1, NULL, 'Month-end October travel as good as expected.', 4, '2025-10-29'),
(212, 4, 1, NULL, 'Consistent service throughout October.', 4, '2025-10-29'),
(213, 5, 2, NULL, 'Luxury service maintained high standards.', 5, '2025-10-29'),
(214, 6, 2, NULL, 'Premium experience exceeded expectations.', 5, '2025-10-29'),
(215, 1, 3, NULL, 'October finale with excellent service.', 5, '2025-10-31'),
(216, 2, 3, NULL, 'Perfect end to a month of great journeys.', 4, '2025-10-31'),
(217, 3, 4, NULL, 'Luxury service closed October on high note.', 5, '2025-10-31'),
(218, 4, 4, NULL, 'Outstanding premium experience to end October.', 5, '2025-10-31'),
(219, 1, 2, NULL, '', 4, '2025-09-24'),
(220, 1, 2, NULL, '', 4, '2025-09-24'),
(221, 1, 1, NULL, '', 4, '2025-09-24'),
(222, 1, 3, 319, '', 4, '2025-09-24');

-- --------------------------------------------------------

--
-- Table structure for table `Incident`
--

CREATE TABLE `Incident` (
  `ID` int(11) NOT NULL,
  `UserId` int(11) DEFAULT NULL,
  `AdminId` int(11) DEFAULT NULL,
  `BookingId` int(11) DEFAULT NULL,
  `Description` text DEFAULT NULL,
  `Status` enum('submitted','in progress','resolved') NOT NULL DEFAULT 'submitted',
  `ReportedDate` date DEFAULT curdate(),
  `ResolvedDate` date DEFAULT NULL,
  `RouteId` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `Incident`
--

INSERT INTO `Incident` (`ID`, `UserId`, `AdminId`, `BookingId`, `Description`, `Status`, `ReportedDate`, `ResolvedDate`, `RouteId`) VALUES
(1, 1, NULL, 1, 'Bus was 45 minutes late due to heavy traffic near Wellawaya. Passengers were not informed about the delay.', 'submitted', '2025-07-24', NULL, 1),
(2, 2, NULL, 2, 'Air conditioning was not working during the journey from Badulla to Matara. Very uncomfortable for passengers especially during the afternoon heat.', 'submitted', '2025-07-24', NULL, 1),
(3, 3, 1, 3, 'Driver was speeding dangerously on mountain roads between Badulla and Bandarawela. Multiple passengers complained about safety.', 'in progress', '2025-07-23', NULL, 1),
(4, 4, 1, 4, 'Seat was broken and uncomfortable throughout the 8-hour journey. No alternative seating was provided.', 'resolved', '2025-07-22', '2025-07-24', 2),
(5, 5, 1, 5, 'Bus broke down for 2 hours near Embilipitiya. No communication or refreshments provided to passengers during the wait.', 'in progress', '2025-07-21', NULL, 1),
(6, 1, 1, NULL, 'Overcharging incident - conductor tried to charge extra for luggage that was within allowed limits.', 'resolved', '2025-07-20', '2025-07-22', 2),
(7, 2, NULL, 7, 'Unhygienic restroom facilities at Wellawaya rest stop. Needs immediate attention for passenger comfort.', 'submitted', '2025-07-25', NULL, 2),
(8, 6, NULL, 8, 'Bus departed 10 minutes early without waiting for booked passengers. I missed my reserved seat.', 'submitted', '2025-07-25', NULL, 2),
(9, 1, NULL, 9, 'Excessive noise from engine throughout the journey from Badulla to Matara. Very disturbing for passengers trying to rest.', 'submitted', '2025-08-01', NULL, 1),
(10, 2, 1, 10, 'Driver was using mobile phone while driving which is extremely dangerous and against traffic rules.', 'in progress', '2025-08-02', NULL, 2),
(11, 3, NULL, NULL, 'Bus toilet facilities were out of order for entire journey. Very inconvenient for long distance travel.', 'submitted', '2025-08-03', NULL, 1),
(12, 4, 1, 12, 'Conductor was rude and unprofessional when passengers asked about arrival time and route information.', 'resolved', '2025-08-04', '2025-08-06', 2),
(13, 5, NULL, NULL, 'Bus seats were dirty and not properly cleaned. Found food crumbs and stains on multiple seats.', 'submitted', '2025-08-05', NULL, 1),
(14, 6, 1, NULL, 'Overbooked bus - more passengers than seats available. Some passengers had to stand for hours.', 'in progress', '2025-08-06', NULL, 2),
(15, 1, NULL, 15, 'No announcement about meal stops. Passengers missed breakfast opportunity at designated rest area.', 'submitted', '2025-08-07', NULL, 1),
(16, 2, 1, 16, 'Bus breakdown due to tire puncture. Delay of 3 hours with no alternative transport provided.', 'resolved', '2025-08-08', '2025-08-10', 1),
(17, 3, NULL, 17, 'WiFi service advertised but not working during entire journey. False advertising of amenities.', 'submitted', '2025-08-09', NULL, 1),
(18, 4, NULL, NULL, 'Driver took unauthorized detour adding 1 hour to journey time without passenger consent.', 'submitted', '2025-08-10', NULL, 2),
(19, 5, 1, NULL, 'Bus luggage compartment lock was broken. Passengers worried about security of belongings.', 'in progress', '2025-08-12', NULL, 1),
(20, 6, NULL, NULL, 'No working charging ports despite being advertised as available. Phone batteries died during journey.', 'submitted', '2025-08-13', NULL, 1),
(21, 1, 1, NULL, 'Driver exceeded speed limits on highway sections. Multiple passengers felt unsafe and requested slower driving.', 'resolved', '2025-08-14', '2025-08-16', 1),
(22, 2, NULL, 22, 'Bus heating system malfunctioned during mountain section. Passengers were very cold during night journey.', 'resolved', '2025-08-15', '2025-08-20', 2),
(23, 3, NULL, 23, 'Conductor lost passenger ticket and demanded second payment. Very unprofessional behavior.', 'submitted', '2025-08-18', NULL, 1),
(24, 4, 1, 24, 'Bus arrived 2 hours late causing passengers to miss connecting transport and appointments.', 'in progress', '2025-08-20', NULL, 1),
(25, 5, NULL, NULL, 'Food service on bus was poor quality and overpriced. Passengers got stomach upset after meal.', 'in progress', '2025-08-22', NULL, 1),
(26, 6, 1, NULL, 'Driver refused to stop at scheduled rest area despite passenger requests for restroom break.', 'resolved', '2025-08-25', '2025-08-27', 2),
(27, 1, NULL, NULL, 'Bus ventilation system not working. Very stuffy and uncomfortable during hot afternoon journey.', 'resolved', '2025-08-28', '2025-07-31', 1),
(28, 2, NULL, NULL, 'Seat reclining mechanism broken. Unable to adjust seat for comfortable sleeping during night travel.', 'resolved', '2025-08-30', '2025-07-31', 1),
(29, 3, 1, 29, 'Bus music/entertainment system too loud and could not be adjusted. Gave passengers headaches.', 'resolved', '2025-08-31', '2025-09-02', 1),
(30, NULL, NULL, NULL, 'Phone: 0785515165\n\nspeed high', 'submitted', '2025-07-30', NULL, NULL),
(31, NULL, NULL, NULL, 'Phone: 1234567890\n\nbjlkkkkkkkkkk', 'submitted', '2025-07-30', NULL, NULL),
(32, NULL, NULL, NULL, 'Phone: 1234567890\n\nppppppppppppppppppppppppp', 'submitted', '2025-07-30', NULL, NULL),
(33, NULL, NULL, NULL, 'Phone: 1234567890\n\nppppppppppppppppppppppppp', 'submitted', '2025-07-30', NULL, NULL),
(34, NULL, NULL, NULL, 'Phone: 1234567890\n\nppppppppppppppppppppppppp', 'submitted', '2025-07-30', NULL, NULL),
(35, NULL, NULL, NULL, 'Phone: 1234567890\n\nnnnnnnnnnnnnnnnnnnnnnnnnnnnn', 'submitted', '2025-07-30', NULL, NULL),
(36, NULL, NULL, NULL, 'Phone: 1234567890\n\nnnnnnnnnnnnnnnnnnnnnnnnnnnnn', 'submitted', '2025-07-30', NULL, NULL),
(37, NULL, NULL, NULL, 'Phone: 1234567890\n\nfmkdngvpkwegv', 'submitted', '2025-07-30', NULL, NULL),
(38, NULL, NULL, NULL, 'Phone: 1234567890\n\noooooooooooooooooooooo', 'submitted', '2025-07-30', NULL, NULL),
(39, NULL, NULL, NULL, 'Phone: 1234567890\n\nhhhhhhhhhhhhhhhhhhhhhhhhh', 'submitted', '2025-07-30', NULL, NULL),
(40, NULL, NULL, NULL, 'Phone: 1234567890\n\nhhhhhhhhhhhhhhhhhhhhhhhhh', 'submitted', '2025-07-30', NULL, NULL),
(41, NULL, NULL, NULL, 'Phone: 1234567890\n\nhhhhhhhhhhhhhhhhhhhhhhhhh', 'submitted', '2025-07-30', NULL, NULL),
(42, NULL, NULL, NULL, 'Phone: 1234567980\n\nqqqqqqqqqqqqqqqqqqqq', 'submitted', '2025-07-30', NULL, NULL),
(43, NULL, NULL, NULL, 'Phone: 1234567890\n\noooooooooooo', 'submitted', '2025-07-30', NULL, NULL),
(44, NULL, NULL, NULL, 'cddddddddddddddc', 'in progress', '2025-07-30', NULL, NULL),
(45, NULL, NULL, NULL, 'gfcjgvjvyhvgiy', 'submitted', '2025-07-31', NULL, NULL),
(46, NULL, NULL, NULL, 'test incident', 'submitted', '2025-07-31', NULL, NULL),
(47, NULL, NULL, 188, 'pppppppppppppp', 'submitted', '2025-09-15', NULL, NULL),
(48, NULL, NULL, 126, 'qqqqqqqqqqqqqqqqqqqqq', 'submitted', '2025-09-17', NULL, NULL),
(49, NULL, NULL, 126, 'ooooooooooooooo', 'submitted', '2025-09-17', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `Location`
--

CREATE TABLE `Location` (
  `UniqueID` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Type` enum('terminal','stop') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `Location`
--

INSERT INTO `Location` (`UniqueID`, `Name`, `Type`) VALUES
(1, 'Badulla', 'terminal'),
(2, 'Ella', 'stop'),
(3, 'Wellawaya', 'stop'),
(4, 'Thanamalvila', 'stop'),
(5, 'Lunugamvehera', 'stop'),
(6, 'Tangalle', 'stop'),
(7, 'Dickwella', 'stop'),
(8, 'Devinuwara', 'stop'),
(9, 'Matara', 'terminal'),
(10, 'Colombo Fort Terminal', 'terminal'),
(11, 'Panadura', 'stop'),
(12, 'Kalutara', 'stop'),
(13, 'Aluthgama', 'stop'),
(14, 'Bentota', 'stop'),
(15, 'Galle Terminal', 'terminal');

-- --------------------------------------------------------

--
-- Table structure for table `Payment`
--

CREATE TABLE `Payment` (
  `ID` int(11) NOT NULL,
  `BookingId` int(11) NOT NULL,
  `PaymentMethod` varchar(50) DEFAULT NULL,
  `Status` enum('success','failed') NOT NULL,
  `Amount` decimal(10,2) NOT NULL,
  `PaymentDate` timestamp NULL DEFAULT current_timestamp(),
  `TransactionId` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `Payment`
--

INSERT INTO `Payment` (`ID`, `BookingId`, `PaymentMethod`, `Status`, `Amount`, `PaymentDate`, `TransactionId`) VALUES
(1, 75, 'Demo Payment', 'success', 450.00, '2025-07-31 02:04:33', 'TXN-1753927473-9486'),
(2, 76, 'Demo Payment', 'success', 450.00, '2025-07-31 05:24:01', 'TXN-1753939440-9063'),
(5, 79, 'Demo Payment', 'success', 500.00, '2025-07-31 06:50:49', 'TXN-1753944648-5013'),
(6, 80, 'Demo Payment', 'success', 500.00, '2025-07-31 06:50:53', 'TXN-1753944652-9951'),
(10, 84, 'Demo Payment', 'success', 450.00, '2025-08-13 07:01:31', 'TXN-1755068489-3775'),
(12, 86, 'Demo Payment', 'success', 450.00, '2025-08-17 02:59:21', 'TXN-1755399561-6115'),
(13, 87, 'Demo Payment', 'success', 450.00, '2025-08-17 03:00:15', 'TXN-1755399615-2604'),
(14, 88, 'Demo Payment', 'success', 450.00, '2025-08-19 14:18:56', 'TXN-1755613136-6573'),
(15, 17, 'PayHere', 'success', 580.00, '2025-09-01 05:30:00', 'TXN_SEP_001'),
(16, 22, 'PayHere', 'success', 580.00, '2025-09-02 05:00:00', 'TXN_SEP_006'),
(17, 23, 'PayHere', 'success', 580.00, '2025-09-02 05:05:00', 'TXN_SEP_007'),
(18, 24, 'PayHere', 'success', 640.00, '2025-09-02 06:00:00', 'TXN_SEP_008'),
(19, 29, 'PayHere', 'success', 640.00, '2025-09-03 06:20:00', 'TXN_SEP_013'),
(20, 30, 'PayHere', 'success', 580.00, '2025-09-04 05:10:00', 'TXN_SEP_014'),
(21, 31, 'PayHere', 'success', 580.00, '2025-09-04 05:15:00', 'TXN_SEP_015'),
(22, 36, 'PayHere', 'success', 640.00, '2025-10-01 06:00:00', 'TXN_OCT_003'),
(23, 37, 'PayHere', 'success', 640.00, '2025-10-01 06:05:00', 'TXN_OCT_004'),
(24, 38, 'PayHere', 'success', 580.00, '2025-10-02 05:00:00', 'TXN_OCT_005'),
(25, 39, 'PayHere', 'success', 580.00, '2025-10-02 05:05:00', 'TXN_OCT_006'),
(26, 43, 'PayHere', 'success', 580.00, '2025-10-03 05:15:00', 'TXN_OCT_010'),
(27, 44, 'PayHere', 'success', 640.00, '2025-10-03 06:10:00', 'TXN_OCT_011'),
(44, 118, 'PayHere', 'success', 580.00, '2025-09-04 09:13:19', 'LT-1756977163-4218'),
(45, 119, 'PayHere', 'success', 500.00, '2025-09-04 09:22:41', 'TEST-1756977760'),
(46, 120, 'PayHere', 'success', 500.00, '2025-09-04 09:23:41', 'LT-TEST-1756977816'),
(47, 121, 'PayHere', 'success', 580.00, '2025-09-04 09:27:53', 'LT-1756978031-7045'),
(48, 122, 'PayHere Gateway', 'success', 750.00, '2025-09-04 09:35:22', 'LT-OOP-TEST-1756978516'),
(49, 125, 'PayHere Gateway', 'success', 920.00, '2025-09-04 09:55:48', 'LT-PAYMENT-TEST-1756979741'),
(50, 126, 'PayHere', 'success', 580.00, '2025-09-04 09:59:00', 'LT-1756979902-6502'),
(51, 127, 'PayHere', 'success', 640.00, '2025-09-04 10:10:49', 'LT-1756980594-6108'),
(52, 128, 'PayHere', 'success', 580.00, '2025-09-04 10:42:53', 'LT-1756982031-7266'),
(53, 129, 'PayHere', 'success', 580.00, '2025-09-05 08:02:41', 'LT-1757059308-2340'),
(54, 130, 'PayHere', 'success', 580.00, '2025-09-05 08:04:20', 'LT-1757059422-2485'),
(55, 131, 'PayHere', 'success', 580.00, '2025-09-05 08:08:56', 'LT-1757059708-9493'),
(56, 132, 'PayHere', 'success', 580.00, '2025-09-05 08:10:06', 'LT-1757059780-8617'),
(57, 133, 'PayHere', 'success', 580.00, '2025-09-05 10:11:42', 'LT-1757067074-6389'),
(58, 134, 'PayHere', 'success', 580.00, '2025-09-05 10:16:32', 'LT-1757067367-1391'),
(59, 17, 'PayHere', 'success', 580.00, '2025-09-01 05:30:00', 'TXN_SEP_001'),
(60, 22, 'PayHere', 'success', 580.00, '2025-09-02 05:00:00', 'TXN_SEP_006'),
(61, 23, 'PayHere', 'success', 580.00, '2025-09-02 05:05:00', 'TXN_SEP_007'),
(62, 24, 'PayHere', 'success', 640.00, '2025-09-02 06:00:00', 'TXN_SEP_008'),
(63, 29, 'PayHere', 'success', 640.00, '2025-09-03 06:20:00', 'TXN_SEP_013'),
(64, 30, 'PayHere', 'success', 580.00, '2025-09-04 05:10:00', 'TXN_SEP_014'),
(65, 31, 'PayHere', 'success', 580.00, '2025-09-04 05:15:00', 'TXN_SEP_015'),
(66, 36, 'PayHere', 'success', 640.00, '2025-09-08 06:25:00', 'TXN_SEP_020'),
(67, 37, 'PayHere', 'success', 640.00, '2025-09-08 06:30:00', 'TXN_SEP_021'),
(68, 38, 'PayHere', 'success', 580.00, '2025-09-09 05:20:00', 'TXN_SEP_022'),
(69, 39, 'PayHere', 'success', 580.00, '2025-09-09 05:25:00', 'TXN_SEP_023'),
(70, 43, 'PayHere', 'success', 580.00, '2025-09-15 05:20:00', 'TXN_SEP_027'),
(71, 44, 'PayHere', 'success', 640.00, '2025-09-15 06:15:00', 'TXN_SEP_028'),
(72, 47, 'PayHere', 'success', 580.00, '2025-09-16 05:15:00', 'TXN_SEP_031'),
(73, 49, 'PayHere', 'success', 640.00, '2025-09-16 06:15:00', 'TXN_SEP_033'),
(74, 50, 'PayHere', 'success', 580.00, '2025-09-22 05:25:00', 'TXN_SEP_034'),
(75, 54, 'PayHere', 'success', 580.00, '2025-09-29 05:00:00', 'TXN_SEP_038'),
(76, 55, 'PayHere', 'success', 580.00, '2025-09-29 05:05:00', 'TXN_SEP_039'),
(77, 56, 'PayHere', 'success', 640.00, '2025-09-29 06:00:00', 'TXN_SEP_040'),
(78, 57, 'PayHere', 'success', 640.00, '2025-09-29 06:05:00', 'TXN_SEP_041'),
(79, 58, 'PayHere', 'success', 580.00, '2025-10-01 05:00:00', 'TXN_OCT_001'),
(80, 60, 'PayHere', 'success', 640.00, '2025-10-01 06:00:00', 'TXN_OCT_003'),
(81, 64, 'PayHere', 'success', 640.00, '2025-10-02 06:00:00', 'TXN_OCT_007'),
(82, 67, 'PayHere', 'success', 580.00, '2025-10-03 05:15:00', 'TXN_OCT_010'),
(83, 68, 'PayHere', 'success', 640.00, '2025-10-03 06:10:00', 'TXN_OCT_011'),
(84, 71, 'PayHere', 'success', 580.00, '2025-10-08 05:25:00', 'TXN_OCT_014'),
(85, 72, 'PayHere', 'success', 640.00, '2025-10-08 06:20:00', 'TXN_OCT_015'),
(86, 74, 'PayHere', 'success', 580.00, '2025-10-09 05:15:00', 'TXN_OCT_017'),
(87, 75, 'PayHere', 'success', 580.00, '2025-10-09 05:20:00', 'TXN_OCT_018'),
(88, 76, 'PayHere', 'success', 640.00, '2025-10-09 06:15:00', 'TXN_OCT_019'),
(89, 79, 'PayHere', 'success', 580.00, '2025-10-15 05:05:00', 'TXN_OCT_022'),
(90, 80, 'PayHere', 'success', 640.00, '2025-10-15 06:00:00', 'TXN_OCT_023'),
(91, 84, 'PayHere', 'success', 640.00, '2025-10-16 06:25:00', 'TXN_OCT_027'),
(92, 86, 'PayHere', 'success', 580.00, '2025-10-22 05:10:00', 'TXN_OCT_029'),
(93, 87, 'PayHere', 'success', 580.00, '2025-10-22 05:15:00', 'TXN_OCT_030'),
(94, 88, 'PayHere', 'success', 640.00, '2025-10-22 06:10:00', 'TXN_OCT_031'),
(95, 89, 'PayHere', 'success', 640.00, '2025-10-22 06:15:00', 'TXN_OCT_032'),
(96, 90, 'PayHere', 'success', 580.00, '2025-10-29 05:00:00', 'TXN_OCT_033'),
(97, 91, 'PayHere', 'success', 580.00, '2025-10-29 05:05:00', 'TXN_OCT_034'),
(98, 92, 'PayHere', 'success', 640.00, '2025-10-29 06:00:00', 'TXN_OCT_035'),
(99, 93, 'PayHere', 'success', 640.00, '2025-10-29 06:05:00', 'TXN_OCT_036'),
(100, 94, 'PayHere', 'success', 580.00, '2025-10-31 05:30:00', 'TXN_OCT_037'),
(101, 95, 'PayHere', 'success', 580.00, '2025-10-31 05:35:00', 'TXN_OCT_038'),
(102, 96, 'PayHere', 'success', 640.00, '2025-10-31 06:30:00', 'TXN_OCT_039'),
(103, 97, 'PayHere', 'success', 640.00, '2025-10-31 06:35:00', 'TXN_OCT_040'),
(104, 216, 'Demo Payment', 'success', 580.00, '2025-09-15 17:47:49', 'TXN-1757958469-6933'),
(105, 217, 'Demo Payment', 'success', 580.00, '2025-09-15 17:54:22', 'TXN-1757958862-5468'),
(106, 218, 'Demo Payment', 'success', 580.00, '2025-09-15 17:55:34', 'TXN-1757958934-8653'),
(107, 219, 'Demo Payment', 'success', 580.00, '2025-09-15 17:58:07', 'TXN-1757959087-5188'),
(108, 221, 'Demo Payment', 'success', 580.00, '2025-09-15 18:32:30', 'TXN-1757961149-2623'),
(109, 17, 'PayHere', 'success', 580.00, '2025-09-01 05:30:00', 'TXN_SEP_001'),
(110, 22, 'PayHere', 'success', 580.00, '2025-09-02 05:00:00', 'TXN_SEP_006'),
(111, 23, 'PayHere', 'success', 580.00, '2025-09-02 05:05:00', 'TXN_SEP_007'),
(112, 24, 'PayHere', 'success', 640.00, '2025-09-02 06:00:00', 'TXN_SEP_008'),
(113, 29, 'PayHere', 'success', 640.00, '2025-09-03 06:20:00', 'TXN_SEP_013'),
(114, 30, 'PayHere', 'success', 580.00, '2025-09-04 05:10:00', 'TXN_SEP_014'),
(115, 31, 'PayHere', 'success', 580.00, '2025-09-04 05:15:00', 'TXN_SEP_015'),
(116, 36, 'PayHere', 'success', 640.00, '2025-09-08 06:25:00', 'TXN_SEP_020'),
(117, 37, 'PayHere', 'success', 640.00, '2025-09-08 06:30:00', 'TXN_SEP_021'),
(118, 38, 'PayHere', 'success', 580.00, '2025-09-09 05:20:00', 'TXN_SEP_022'),
(119, 39, 'PayHere', 'success', 580.00, '2025-09-09 05:25:00', 'TXN_SEP_023'),
(120, 43, 'PayHere', 'success', 580.00, '2025-09-15 05:20:00', 'TXN_SEP_027'),
(121, 44, 'PayHere', 'success', 640.00, '2025-09-15 06:15:00', 'TXN_SEP_028'),
(122, 47, 'PayHere', 'success', 580.00, '2025-09-16 05:15:00', 'TXN_SEP_031'),
(123, 49, 'PayHere', 'success', 640.00, '2025-09-16 06:15:00', 'TXN_SEP_033'),
(124, 50, 'PayHere', 'success', 580.00, '2025-09-22 05:25:00', 'TXN_SEP_034'),
(125, 54, 'PayHere', 'success', 580.00, '2025-09-29 05:00:00', 'TXN_SEP_038'),
(126, 55, 'PayHere', 'success', 580.00, '2025-09-29 05:05:00', 'TXN_SEP_039'),
(127, 56, 'PayHere', 'success', 640.00, '2025-09-29 06:00:00', 'TXN_SEP_040'),
(128, 57, 'PayHere', 'success', 640.00, '2025-09-29 06:05:00', 'TXN_SEP_041'),
(129, 58, 'PayHere', 'success', 580.00, '2025-10-01 05:00:00', 'TXN_OCT_001'),
(130, 60, 'PayHere', 'success', 640.00, '2025-10-01 06:00:00', 'TXN_OCT_003'),
(131, 64, 'PayHere', 'success', 640.00, '2025-10-02 06:00:00', 'TXN_OCT_007'),
(132, 67, 'PayHere', 'success', 580.00, '2025-10-03 05:15:00', 'TXN_OCT_010'),
(133, 68, 'PayHere', 'success', 640.00, '2025-10-03 06:10:00', 'TXN_OCT_011'),
(134, 71, 'PayHere', 'success', 580.00, '2025-10-08 05:25:00', 'TXN_OCT_014'),
(135, 72, 'PayHere', 'success', 640.00, '2025-10-08 06:20:00', 'TXN_OCT_015'),
(136, 74, 'PayHere', 'success', 580.00, '2025-10-09 05:15:00', 'TXN_OCT_017'),
(137, 75, 'PayHere', 'success', 580.00, '2025-10-09 05:20:00', 'TXN_OCT_018'),
(138, 76, 'PayHere', 'success', 640.00, '2025-10-09 06:15:00', 'TXN_OCT_019'),
(139, 79, 'PayHere', 'success', 580.00, '2025-10-15 05:05:00', 'TXN_OCT_022'),
(140, 80, 'PayHere', 'success', 640.00, '2025-10-15 06:00:00', 'TXN_OCT_023'),
(141, 84, 'PayHere', 'success', 640.00, '2025-10-16 06:25:00', 'TXN_OCT_027'),
(142, 86, 'PayHere', 'success', 580.00, '2025-10-22 05:10:00', 'TXN_OCT_029'),
(143, 87, 'PayHere', 'success', 580.00, '2025-10-22 05:15:00', 'TXN_OCT_030'),
(144, 88, 'PayHere', 'success', 640.00, '2025-10-22 06:10:00', 'TXN_OCT_031'),
(145, 89, 'PayHere', 'success', 640.00, '2025-10-22 06:15:00', 'TXN_OCT_032'),
(146, 90, 'PayHere', 'success', 580.00, '2025-10-29 05:00:00', 'TXN_OCT_033'),
(147, 91, 'PayHere', 'success', 580.00, '2025-10-29 05:05:00', 'TXN_OCT_034'),
(148, 92, 'PayHere', 'success', 640.00, '2025-10-29 06:00:00', 'TXN_OCT_035'),
(149, 93, 'PayHere', 'success', 640.00, '2025-10-29 06:05:00', 'TXN_OCT_036'),
(150, 94, 'PayHere', 'success', 580.00, '2025-10-31 05:30:00', 'TXN_OCT_037'),
(151, 95, 'PayHere', 'success', 580.00, '2025-10-31 05:35:00', 'TXN_OCT_038'),
(152, 96, 'PayHere', 'success', 640.00, '2025-10-31 06:30:00', 'TXN_OCT_039'),
(153, 97, 'PayHere', 'success', 640.00, '2025-10-31 06:35:00', 'TXN_OCT_040'),
(190, 303, 'PayHere', 'success', 640.00, '2025-09-18 14:41:06', 'LT-1758206431-9015');

-- --------------------------------------------------------

--
-- Table structure for table `Route`
--

CREATE TABLE `Route` (
  `ID` int(11) NOT NULL,
  `Origin` varchar(50) NOT NULL,
  `Destination` varchar(50) NOT NULL,
  `Stops` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `Route`
--

INSERT INTO `Route` (`ID`, `Origin`, `Destination`, `Stops`) VALUES
(1, 'Badulla', 'Matara', 'Ella,Wellawaya,Thanamalvila,Lunugamvehera,Tangalle,Dickwella,Devinuwara'),
(2, 'Matara', 'Badulla', 'Devinuwara,Dickwella,Tangalle,Lunugamvehera,Thanamalvila,Wellawaya,Ella');

-- --------------------------------------------------------

--
-- Table structure for table `Schedule`
--

CREATE TABLE `Schedule` (
  `ID` int(11) NOT NULL,
  `BusID` int(11) DEFAULT NULL,
  `DepartureTime` datetime NOT NULL,
  `ArrivalTime` datetime NOT NULL,
  `Fare` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `Schedule`
--

INSERT INTO `Schedule` (`ID`, `BusID`, `DepartureTime`, `ArrivalTime`, `Fare`) VALUES
(1, 1, '2025-08-01 06:00:00', '2025-08-01 09:30:00', 450.00),
(2, 1, '2025-08-01 14:00:00', '2025-08-01 17:30:00', 450.00),
(3, 1, '2025-08-02 06:00:00', '2025-08-02 09:30:00', 450.00),
(4, 1, '2025-08-02 14:00:00', '2025-08-02 17:30:00', 450.00),
(5, 1, '2025-08-03 06:00:00', '2025-08-03 09:30:00', 450.00),
(6, 1, '2025-08-03 14:00:00', '2025-08-03 17:30:00', 450.00),
(7, 1, '2025-08-04 06:00:00', '2025-08-04 09:30:00', 450.00),
(8, 1, '2025-08-04 14:00:00', '2025-08-04 17:30:00', 450.00),
(9, 1, '2025-08-05 06:00:00', '2025-08-05 09:30:00', 450.00),
(10, 1, '2025-08-05 14:00:00', '2025-08-05 17:30:00', 450.00),
(11, 1, '2025-08-06 06:00:00', '2025-08-06 09:30:00', 450.00),
(12, 1, '2025-08-06 14:00:00', '2025-08-06 17:30:00', 450.00),
(13, 1, '2025-08-07 06:00:00', '2025-08-07 09:30:00', 450.00),
(14, 1, '2025-08-07 14:00:00', '2025-08-07 17:30:00', 450.00),
(15, 1, '2025-08-08 06:00:00', '2025-08-08 09:30:00', 450.00),
(16, 1, '2025-08-08 14:00:00', '2025-08-08 17:30:00', 450.00),
(17, 1, '2025-08-09 06:00:00', '2025-08-09 09:30:00', 450.00),
(18, 1, '2025-08-09 14:00:00', '2025-08-09 17:30:00', 450.00),
(19, 1, '2025-08-10 06:00:00', '2025-08-10 09:30:00', 450.00),
(20, 1, '2025-08-10 14:00:00', '2025-08-10 17:30:00', 450.00),
(21, 2, '2025-08-01 08:00:00', '2025-08-01 11:30:00', 500.00),
(22, 2, '2025-08-01 16:00:00', '2025-08-01 19:30:00', 500.00),
(23, 2, '2025-08-02 08:00:00', '2025-08-02 11:30:00', 500.00),
(24, 2, '2025-08-02 16:00:00', '2025-08-02 19:30:00', 500.00),
(25, 2, '2025-08-03 08:00:00', '2025-08-03 11:30:00', 500.00),
(26, 2, '2025-08-03 16:00:00', '2025-08-03 19:30:00', 500.00),
(27, 2, '2025-08-04 08:00:00', '2025-08-04 11:30:00', 500.00),
(28, 2, '2025-08-04 16:00:00', '2025-08-04 19:30:00', 500.00),
(29, 2, '2025-08-05 08:00:00', '2025-08-05 11:30:00', 500.00),
(30, 2, '2025-08-05 16:00:00', '2025-08-05 19:30:00', 500.00),
(31, 2, '2025-08-06 08:00:00', '2025-08-06 11:30:00', 500.00),
(32, 2, '2025-08-06 16:00:00', '2025-08-06 19:30:00', 500.00),
(33, 2, '2025-08-07 08:00:00', '2025-08-07 11:30:00', 500.00),
(34, 2, '2025-08-07 16:00:00', '2025-08-07 19:30:00', 500.00),
(35, 2, '2025-08-08 08:00:00', '2025-08-08 11:30:00', 500.00),
(36, 2, '2025-08-08 16:00:00', '2025-08-08 19:30:00', 500.00),
(37, 2, '2025-08-09 08:00:00', '2025-08-09 11:30:00', 500.00),
(38, 2, '2025-08-09 16:00:00', '2025-08-09 19:30:00', 500.00),
(39, 2, '2025-08-10 08:00:00', '2025-08-10 11:30:00', 500.00),
(40, 2, '2025-08-10 16:00:00', '2025-08-10 19:30:00', 500.00),
(51, 6, '2025-08-01 09:00:00', '2025-08-01 12:30:00', 460.00),
(52, 6, '2025-08-01 17:00:00', '2025-08-01 20:30:00', 460.00),
(53, 6, '2025-08-02 09:00:00', '2025-08-02 12:30:00', 460.00),
(54, 6, '2025-08-02 17:00:00', '2025-08-02 20:30:00', 460.00),
(55, 6, '2025-08-03 09:00:00', '2025-08-03 12:30:00', 460.00),
(56, 6, '2025-08-03 17:00:00', '2025-08-03 20:30:00', 460.00),
(57, 1, '2025-08-11 06:00:00', '2025-08-11 09:30:00', 450.00),
(58, 1, '2025-08-11 14:00:00', '2025-08-11 17:30:00', 450.00),
(59, 1, '2025-08-12 06:00:00', '2025-08-12 09:30:00', 450.00),
(60, 1, '2025-08-12 14:00:00', '2025-08-12 17:30:00', 450.00),
(61, 1, '2025-08-13 06:00:00', '2025-08-13 09:30:00', 450.00),
(62, 1, '2025-08-13 14:00:00', '2025-08-13 17:30:00', 450.00),
(63, 1, '2025-08-14 06:00:00', '2025-08-14 09:30:00', 450.00),
(64, 1, '2025-08-14 14:00:00', '2025-08-14 17:30:00', 450.00),
(65, 1, '2025-08-15 06:00:00', '2025-08-15 09:30:00', 450.00),
(66, 1, '2025-08-15 14:00:00', '2025-08-15 17:30:00', 450.00),
(67, 1, '2025-08-16 06:00:00', '2025-08-16 09:30:00', 450.00),
(68, 1, '2025-08-16 14:00:00', '2025-08-16 17:30:00', 450.00),
(69, 1, '2025-08-17 06:00:00', '2025-08-17 09:30:00', 450.00),
(70, 1, '2025-08-17 14:00:00', '2025-08-17 17:30:00', 450.00),
(71, 1, '2025-08-18 06:00:00', '2025-08-18 09:30:00', 450.00),
(72, 1, '2025-08-18 14:00:00', '2025-08-18 17:30:00', 450.00),
(73, 1, '2025-08-19 06:00:00', '2025-08-19 09:30:00', 450.00),
(74, 1, '2025-08-19 14:00:00', '2025-08-19 17:30:00', 450.00),
(75, 1, '2025-08-20 06:00:00', '2025-08-20 09:30:00', 450.00),
(76, 1, '2025-08-20 14:00:00', '2025-08-20 17:30:00', 450.00),
(77, 1, '2025-08-21 06:00:00', '2025-08-21 09:30:00', 450.00),
(78, 1, '2025-08-21 14:00:00', '2025-08-21 17:30:00', 450.00),
(79, 1, '2025-08-22 06:00:00', '2025-08-22 09:30:00', 450.00),
(80, 1, '2025-08-22 14:00:00', '2025-08-22 17:30:00', 450.00),
(81, 1, '2025-08-23 06:00:00', '2025-08-23 09:30:00', 450.00),
(82, 1, '2025-08-23 14:00:00', '2025-08-23 17:30:00', 450.00),
(83, 1, '2025-08-24 06:00:00', '2025-08-24 09:30:00', 450.00),
(84, 1, '2025-08-24 14:00:00', '2025-08-24 17:30:00', 450.00),
(85, 1, '2025-08-25 06:00:00', '2025-08-25 09:30:00', 450.00),
(86, 1, '2025-08-25 14:00:00', '2025-08-25 17:30:00', 450.00),
(87, 1, '2025-08-26 06:00:00', '2025-08-26 09:30:00', 450.00),
(88, 1, '2025-08-26 14:00:00', '2025-08-26 17:30:00', 450.00),
(89, 1, '2025-08-27 06:00:00', '2025-08-27 09:30:00', 450.00),
(90, 1, '2025-08-27 14:00:00', '2025-08-27 17:30:00', 450.00),
(91, 1, '2025-08-28 06:00:00', '2025-08-28 09:30:00', 450.00),
(92, 1, '2025-08-28 14:00:00', '2025-08-28 17:30:00', 450.00),
(93, 1, '2025-08-29 06:00:00', '2025-08-29 09:30:00', 450.00),
(94, 1, '2025-08-29 14:00:00', '2025-08-29 17:30:00', 450.00),
(95, 1, '2025-08-30 06:00:00', '2025-08-30 09:30:00', 450.00),
(96, 1, '2025-08-30 14:00:00', '2025-08-30 17:30:00', 450.00),
(97, 1, '2025-08-31 06:00:00', '2025-08-31 09:30:00', 450.00),
(98, 1, '2025-08-31 14:00:00', '2025-08-31 17:30:00', 450.00),
(99, 2, '2025-08-11 08:00:00', '2025-08-11 11:30:00', 500.00),
(100, 2, '2025-08-11 16:00:00', '2025-08-11 19:30:00', 500.00),
(101, 2, '2025-08-12 08:00:00', '2025-08-12 11:30:00', 500.00),
(102, 2, '2025-08-12 16:00:00', '2025-08-12 19:30:00', 500.00),
(103, 2, '2025-08-13 08:00:00', '2025-08-13 11:30:00', 500.00),
(104, 2, '2025-08-13 16:00:00', '2025-08-13 19:30:00', 500.00),
(105, 2, '2025-08-14 08:00:00', '2025-08-14 11:30:00', 500.00),
(106, 2, '2025-08-14 16:00:00', '2025-08-14 19:30:00', 500.00),
(107, 2, '2025-08-15 08:00:00', '2025-08-15 11:30:00', 500.00),
(108, 2, '2025-08-15 16:00:00', '2025-08-15 19:30:00', 500.00),
(109, 2, '2025-08-16 08:00:00', '2025-08-16 11:30:00', 500.00),
(110, 2, '2025-08-16 16:00:00', '2025-08-16 19:30:00', 500.00),
(111, 2, '2025-08-17 08:00:00', '2025-08-17 11:30:00', 500.00),
(112, 2, '2025-08-17 16:00:00', '2025-08-17 19:30:00', 500.00),
(113, 2, '2025-08-18 08:00:00', '2025-08-18 11:30:00', 500.00),
(114, 2, '2025-08-18 16:00:00', '2025-08-18 19:30:00', 500.00),
(115, 2, '2025-08-19 08:00:00', '2025-08-19 11:30:00', 500.00),
(116, 2, '2025-08-19 16:00:00', '2025-08-19 19:30:00', 500.00),
(117, 2, '2025-08-20 08:00:00', '2025-08-20 11:30:00', 500.00),
(118, 2, '2025-08-20 16:00:00', '2025-08-20 19:30:00', 500.00),
(119, 2, '2025-08-21 08:00:00', '2025-08-21 11:30:00', 500.00),
(120, 2, '2025-08-21 16:00:00', '2025-08-21 19:30:00', 500.00),
(121, 2, '2025-08-22 08:00:00', '2025-08-22 11:30:00', 500.00),
(122, 2, '2025-08-22 16:00:00', '2025-08-22 19:30:00', 500.00),
(123, 2, '2025-08-23 08:00:00', '2025-08-23 11:30:00', 500.00),
(124, 2, '2025-08-23 16:00:00', '2025-08-23 19:30:00', 500.00),
(125, 2, '2025-08-24 08:00:00', '2025-08-24 11:30:00', 500.00),
(126, 2, '2025-08-24 16:00:00', '2025-08-24 19:30:00', 500.00),
(127, 2, '2025-08-25 08:00:00', '2025-08-25 11:30:00', 500.00),
(128, 2, '2025-08-25 16:00:00', '2025-08-25 19:30:00', 500.00),
(129, 2, '2025-08-26 08:00:00', '2025-08-26 11:30:00', 500.00),
(130, 2, '2025-08-26 16:00:00', '2025-08-26 19:30:00', 500.00),
(131, 2, '2025-08-27 08:00:00', '2025-08-27 11:30:00', 500.00),
(132, 2, '2025-08-27 16:00:00', '2025-08-27 19:30:00', 500.00),
(133, 2, '2025-08-28 08:00:00', '2025-08-28 11:30:00', 500.00),
(134, 2, '2025-08-28 16:00:00', '2025-08-28 19:30:00', 500.00),
(135, 2, '2025-08-29 08:00:00', '2025-08-29 11:30:00', 500.00),
(136, 2, '2025-08-29 16:00:00', '2025-08-29 19:30:00', 500.00),
(137, 2, '2025-08-30 08:00:00', '2025-08-30 11:30:00', 500.00),
(138, 2, '2025-08-30 16:00:00', '2025-08-30 19:30:00', 500.00),
(139, 2, '2025-08-31 08:00:00', '2025-08-31 11:30:00', 500.00),
(140, 2, '2025-08-31 16:00:00', '2025-08-31 19:30:00', 500.00),
(193, 6, '2025-08-04 09:00:00', '2025-08-04 12:30:00', 460.00),
(194, 6, '2025-08-04 17:00:00', '2025-08-04 20:30:00', 460.00),
(195, 6, '2025-08-05 09:00:00', '2025-08-05 12:30:00', 460.00),
(196, 6, '2025-08-05 17:00:00', '2025-08-05 20:30:00', 460.00),
(197, 6, '2025-08-06 09:00:00', '2025-08-06 12:30:00', 460.00),
(198, 6, '2025-08-06 17:00:00', '2025-08-06 20:30:00', 460.00),
(199, 6, '2025-08-07 09:00:00', '2025-08-07 12:30:00', 460.00),
(200, 6, '2025-08-07 17:00:00', '2025-08-07 20:30:00', 460.00),
(201, 6, '2025-08-08 09:00:00', '2025-08-08 12:30:00', 460.00),
(202, 6, '2025-08-08 17:00:00', '2025-08-08 20:30:00', 460.00),
(203, 6, '2025-08-09 09:00:00', '2025-08-09 12:30:00', 460.00),
(204, 6, '2025-08-09 17:00:00', '2025-08-09 20:30:00', 460.00),
(205, 6, '2025-08-10 09:00:00', '2025-08-10 12:30:00', 460.00),
(206, 6, '2025-08-10 17:00:00', '2025-08-10 20:30:00', 460.00),
(207, 6, '2025-08-11 09:00:00', '2025-08-11 12:30:00', 460.00),
(208, 6, '2025-08-11 17:00:00', '2025-08-11 20:30:00', 460.00),
(209, 6, '2025-08-12 09:00:00', '2025-08-12 12:30:00', 460.00),
(210, 6, '2025-08-12 17:00:00', '2025-08-12 20:30:00', 460.00),
(211, 6, '2025-08-13 09:00:00', '2025-08-13 12:30:00', 460.00),
(212, 6, '2025-08-13 17:00:00', '2025-08-13 20:30:00', 460.00),
(213, 6, '2025-08-14 09:00:00', '2025-08-14 12:30:00', 460.00),
(214, 6, '2025-08-14 17:00:00', '2025-08-14 20:30:00', 460.00),
(215, 6, '2025-08-15 09:00:00', '2025-08-15 12:30:00', 460.00),
(216, 6, '2025-08-15 17:00:00', '2025-08-15 20:30:00', 460.00),
(217, 6, '2025-08-16 09:00:00', '2025-08-16 12:30:00', 460.00),
(218, 6, '2025-08-16 17:00:00', '2025-08-16 20:30:00', 460.00),
(219, 6, '2025-08-17 09:00:00', '2025-08-17 12:30:00', 460.00),
(220, 6, '2025-08-17 17:00:00', '2025-08-17 20:30:00', 460.00),
(221, 6, '2025-08-18 09:00:00', '2025-08-18 12:30:00', 460.00),
(222, 6, '2025-08-18 17:00:00', '2025-08-18 20:30:00', 460.00),
(223, 6, '2025-08-19 09:00:00', '2025-08-19 12:30:00', 460.00),
(224, 6, '2025-08-19 17:00:00', '2025-08-19 20:30:00', 460.00),
(225, 6, '2025-08-20 09:00:00', '2025-08-20 12:30:00', 460.00),
(226, 6, '2025-08-20 17:00:00', '2025-08-20 20:30:00', 460.00),
(227, 6, '2025-08-21 09:00:00', '2025-08-21 12:30:00', 460.00),
(228, 6, '2025-08-21 17:00:00', '2025-08-21 20:30:00', 460.00),
(229, 6, '2025-08-22 09:00:00', '2025-08-22 12:30:00', 460.00),
(230, 6, '2025-08-22 17:00:00', '2025-08-22 20:30:00', 460.00),
(231, 6, '2025-08-23 09:00:00', '2025-08-23 12:30:00', 460.00),
(232, 6, '2025-08-23 17:00:00', '2025-08-23 20:30:00', 460.00),
(233, 6, '2025-08-24 09:00:00', '2025-08-24 12:30:00', 460.00),
(234, 6, '2025-08-24 17:00:00', '2025-08-24 20:30:00', 460.00),
(235, 6, '2025-08-25 09:00:00', '2025-08-25 12:30:00', 460.00),
(236, 6, '2025-08-25 17:00:00', '2025-08-25 20:30:00', 460.00),
(237, 6, '2025-08-26 09:00:00', '2025-08-26 12:30:00', 460.00),
(238, 6, '2025-08-26 17:00:00', '2025-08-26 20:30:00', 460.00),
(239, 6, '2025-08-27 09:00:00', '2025-08-27 12:30:00', 460.00),
(240, 6, '2025-08-27 17:00:00', '2025-08-27 20:30:00', 460.00),
(241, 6, '2025-08-28 09:00:00', '2025-08-28 12:30:00', 460.00),
(242, 6, '2025-08-28 17:00:00', '2025-08-28 20:30:00', 460.00),
(243, 6, '2025-08-29 09:00:00', '2025-08-29 12:30:00', 460.00),
(244, 6, '2025-08-29 17:00:00', '2025-08-29 20:30:00', 460.00),
(245, 6, '2025-08-30 09:00:00', '2025-08-30 12:30:00', 460.00),
(246, 6, '2025-08-30 17:00:00', '2025-08-30 20:30:00', 460.00),
(247, 6, '2025-08-31 09:00:00', '2025-08-31 12:30:00', 460.00),
(248, 6, '2025-08-31 17:00:00', '2025-08-31 20:30:00', 460.00),
(374, 1, '2025-09-01 06:00:00', '2025-09-01 12:30:00', 580.00),
(375, 1, '2025-09-01 14:00:00', '2025-09-01 20:30:00', 580.00),
(376, 2, '2025-09-01 07:00:00', '2025-09-01 13:30:00', 640.00),
(377, 2, '2025-09-01 15:00:00', '2025-09-01 21:30:00', 640.00),
(378, 1, '2025-09-02 05:30:00', '2025-09-02 12:00:00', 580.00),
(379, 1, '2025-09-02 13:30:00', '2025-09-02 20:00:00', 580.00),
(380, 2, '2025-09-02 06:30:00', '2025-09-02 13:00:00', 640.00),
(381, 2, '2025-09-02 14:30:00', '2025-09-02 21:00:00', 640.00),
(382, 1, '2025-09-03 05:30:00', '2025-09-03 12:00:00', 580.00),
(383, 1, '2025-09-03 13:30:00', '2025-09-03 20:00:00', 580.00),
(384, 2, '2025-09-03 06:30:00', '2025-09-03 13:00:00', 640.00),
(385, 2, '2025-09-03 14:30:00', '2025-09-03 21:00:00', 640.00),
(386, 1, '2025-09-04 05:30:00', '2025-09-04 12:00:00', 580.00),
(387, 1, '2025-09-04 13:30:00', '2025-09-04 20:00:00', 580.00),
(388, 2, '2025-09-04 06:30:00', '2025-09-04 13:00:00', 640.00),
(389, 2, '2025-09-04 14:30:00', '2025-09-04 21:00:00', 640.00),
(390, 1, '2025-09-05 05:30:00', '2025-09-05 12:00:00', 580.00),
(391, 1, '2025-09-05 13:30:00', '2025-09-05 20:00:00', 580.00),
(392, 2, '2025-09-05 06:30:00', '2025-09-05 13:00:00', 640.00),
(393, 2, '2025-09-05 14:30:00', '2025-09-05 21:00:00', 640.00),
(394, 1, '2025-09-06 05:30:00', '2025-09-06 12:00:00', 580.00),
(395, 1, '2025-09-06 13:30:00', '2025-09-06 20:00:00', 580.00),
(396, 2, '2025-09-06 06:30:00', '2025-09-06 13:00:00', 640.00),
(397, 2, '2025-09-06 14:30:00', '2025-09-06 21:00:00', 640.00),
(398, 1, '2025-09-07 06:00:00', '2025-09-07 12:30:00', 580.00),
(399, 1, '2025-09-07 14:00:00', '2025-09-07 20:30:00', 580.00),
(400, 2, '2025-09-07 07:00:00', '2025-09-07 13:30:00', 640.00),
(401, 2, '2025-09-07 15:00:00', '2025-09-07 21:30:00', 640.00),
(402, 3, '2025-09-01 06:00:00', '2025-09-01 12:30:00', 580.00),
(403, 3, '2025-09-01 14:00:00', '2025-09-01 20:30:00', 580.00),
(404, 4, '2025-09-01 07:00:00', '2025-09-01 13:30:00', 640.00),
(405, 4, '2025-09-01 15:00:00', '2025-09-01 21:30:00', 640.00),
(406, 3, '2025-09-02 05:30:00', '2025-09-02 12:00:00', 580.00),
(407, 3, '2025-09-02 13:30:00', '2025-09-02 20:00:00', 580.00),
(408, 4, '2025-09-02 06:30:00', '2025-09-02 13:00:00', 640.00),
(409, 4, '2025-09-02 14:30:00', '2025-09-02 21:00:00', 640.00),
(410, 3, '2025-09-03 05:30:00', '2025-09-03 12:00:00', 580.00),
(411, 3, '2025-09-03 13:30:00', '2025-09-03 20:00:00', 580.00),
(412, 4, '2025-09-03 06:30:00', '2025-09-03 13:00:00', 640.00),
(413, 4, '2025-09-03 14:30:00', '2025-09-03 21:00:00', 640.00),
(414, 3, '2025-09-04 05:30:00', '2025-09-04 12:00:00', 580.00),
(415, 3, '2025-09-04 13:30:00', '2025-09-04 20:00:00', 580.00),
(416, 4, '2025-09-04 06:30:00', '2025-09-04 13:00:00', 640.00),
(417, 4, '2025-09-04 14:30:00', '2025-09-04 21:00:00', 640.00),
(418, 3, '2025-09-05 05:30:00', '2025-09-05 12:00:00', 580.00),
(419, 3, '2025-09-05 13:30:00', '2025-09-05 20:00:00', 580.00),
(420, 4, '2025-09-05 06:30:00', '2025-09-05 13:00:00', 640.00),
(421, 4, '2025-09-05 14:30:00', '2025-09-05 21:00:00', 640.00),
(422, 3, '2025-09-06 05:30:00', '2025-09-06 12:00:00', 580.00),
(423, 3, '2025-09-06 13:30:00', '2025-09-06 20:00:00', 580.00),
(424, 4, '2025-09-06 06:30:00', '2025-09-06 13:00:00', 640.00),
(425, 4, '2025-09-06 14:30:00', '2025-09-06 21:00:00', 640.00),
(426, 3, '2025-09-07 06:00:00', '2025-09-07 12:30:00', 580.00),
(427, 3, '2025-09-07 14:00:00', '2025-09-07 20:30:00', 580.00),
(428, 4, '2025-09-07 07:00:00', '2025-09-07 13:30:00', 640.00),
(429, 4, '2025-09-07 15:00:00', '2025-09-07 21:30:00', 640.00),
(430, 1, '2025-10-01 05:30:00', '2025-10-01 12:00:00', 580.00),
(431, 1, '2025-10-01 13:30:00', '2025-10-01 20:00:00', 580.00),
(432, 2, '2025-10-01 06:30:00', '2025-10-01 13:00:00', 640.00),
(433, 2, '2025-10-01 14:30:00', '2025-10-01 21:00:00', 640.00),
(434, 1, '2025-10-02 05:30:00', '2025-10-02 12:00:00', 580.00),
(435, 1, '2025-10-02 13:30:00', '2025-10-02 20:00:00', 580.00),
(436, 2, '2025-10-02 06:30:00', '2025-10-02 13:00:00', 640.00),
(437, 2, '2025-10-02 14:30:00', '2025-10-02 21:00:00', 640.00),
(438, 1, '2025-10-03 05:30:00', '2025-10-03 12:00:00', 580.00),
(439, 1, '2025-10-03 13:30:00', '2025-10-03 20:00:00', 580.00),
(440, 2, '2025-10-03 06:30:00', '2025-10-03 13:00:00', 640.00),
(441, 2, '2025-10-03 14:30:00', '2025-10-03 21:00:00', 640.00),
(442, 1, '2025-10-04 06:00:00', '2025-10-04 12:30:00', 580.00),
(443, 1, '2025-10-04 14:00:00', '2025-10-04 20:30:00', 580.00),
(444, 2, '2025-10-04 07:00:00', '2025-10-04 13:30:00', 640.00),
(445, 2, '2025-10-04 15:00:00', '2025-10-04 21:30:00', 640.00),
(446, 1, '2025-10-05 06:00:00', '2025-10-05 12:30:00', 580.00),
(447, 1, '2025-10-05 14:00:00', '2025-10-05 20:30:00', 580.00),
(448, 2, '2025-10-05 07:00:00', '2025-10-05 13:30:00', 640.00),
(449, 2, '2025-10-05 15:00:00', '2025-10-05 21:30:00', 640.00),
(450, 1, '2025-10-06 05:30:00', '2025-10-06 12:00:00', 580.00),
(451, 1, '2025-10-06 13:30:00', '2025-10-06 20:00:00', 580.00),
(452, 2, '2025-10-06 06:30:00', '2025-10-06 13:00:00', 640.00),
(453, 2, '2025-10-06 14:30:00', '2025-10-06 21:00:00', 640.00),
(454, 1, '2025-10-07 05:30:00', '2025-10-07 12:00:00', 580.00),
(455, 1, '2025-10-07 13:30:00', '2025-10-07 20:00:00', 580.00),
(456, 2, '2025-10-07 06:30:00', '2025-10-07 13:00:00', 640.00),
(457, 2, '2025-10-07 14:30:00', '2025-10-07 21:00:00', 640.00),
(458, 3, '2025-10-01 05:30:00', '2025-10-01 12:00:00', 580.00),
(459, 3, '2025-10-01 13:30:00', '2025-10-01 20:00:00', 580.00),
(460, 4, '2025-10-01 06:30:00', '2025-10-01 13:00:00', 640.00),
(461, 4, '2025-10-01 14:30:00', '2025-10-01 21:00:00', 640.00),
(462, 3, '2025-10-02 05:30:00', '2025-10-02 12:00:00', 580.00),
(463, 3, '2025-10-02 13:30:00', '2025-10-02 20:00:00', 580.00),
(464, 4, '2025-10-02 06:30:00', '2025-10-02 13:00:00', 640.00),
(465, 4, '2025-10-02 14:30:00', '2025-10-02 21:00:00', 640.00),
(466, 3, '2025-10-03 05:30:00', '2025-10-03 12:00:00', 580.00),
(467, 3, '2025-10-03 13:30:00', '2025-10-03 20:00:00', 580.00),
(468, 4, '2025-10-03 06:30:00', '2025-10-03 13:00:00', 640.00),
(469, 4, '2025-10-03 14:30:00', '2025-10-03 21:00:00', 640.00),
(470, 3, '2025-10-04 06:00:00', '2025-10-04 12:30:00', 580.00),
(471, 3, '2025-10-04 14:00:00', '2025-10-04 20:30:00', 580.00),
(472, 4, '2025-10-04 07:00:00', '2025-10-04 13:30:00', 640.00),
(473, 4, '2025-10-04 15:00:00', '2025-10-04 21:30:00', 640.00),
(474, 3, '2025-10-05 06:00:00', '2025-10-05 12:30:00', 580.00),
(475, 3, '2025-10-05 14:00:00', '2025-10-05 20:30:00', 580.00),
(476, 4, '2025-10-05 07:00:00', '2025-10-05 13:30:00', 640.00),
(477, 4, '2025-10-05 15:00:00', '2025-10-05 21:30:00', 640.00),
(478, 3, '2025-10-06 05:30:00', '2025-10-06 12:00:00', 580.00),
(479, 3, '2025-10-06 13:30:00', '2025-10-06 20:00:00', 580.00),
(480, 4, '2025-10-06 06:30:00', '2025-10-06 13:00:00', 640.00),
(481, 4, '2025-10-06 14:30:00', '2025-10-06 21:00:00', 640.00),
(482, 3, '2025-10-07 05:30:00', '2025-10-07 12:00:00', 580.00),
(483, 3, '2025-10-07 13:30:00', '2025-10-07 20:00:00', 580.00),
(484, 4, '2025-10-07 06:30:00', '2025-10-07 13:00:00', 640.00),
(485, 4, '2025-10-07 14:30:00', '2025-10-07 21:00:00', 640.00),
(486, 1, '2025-09-01 06:00:00', '2025-09-01 12:30:00', 580.00),
(487, 1, '2025-09-01 14:00:00', '2025-09-01 20:30:00', 580.00),
(488, 2, '2025-09-01 07:00:00', '2025-09-01 13:30:00', 640.00),
(489, 2, '2025-09-01 15:00:00', '2025-09-01 21:30:00', 640.00),
(490, 1, '2025-09-02 05:30:00', '2025-09-02 12:00:00', 580.00),
(491, 1, '2025-09-02 13:30:00', '2025-09-02 20:00:00', 580.00),
(492, 2, '2025-09-02 06:30:00', '2025-09-02 13:00:00', 640.00),
(493, 2, '2025-09-02 14:30:00', '2025-09-02 21:00:00', 640.00),
(494, 1, '2025-09-03 05:30:00', '2025-09-03 12:00:00', 580.00),
(495, 1, '2025-09-03 13:30:00', '2025-09-03 20:00:00', 580.00),
(496, 2, '2025-09-03 06:30:00', '2025-09-03 13:00:00', 640.00),
(497, 2, '2025-09-03 14:30:00', '2025-09-03 21:00:00', 640.00),
(498, 1, '2025-09-04 05:30:00', '2025-09-04 12:00:00', 580.00),
(499, 1, '2025-09-04 13:30:00', '2025-09-04 20:00:00', 580.00),
(500, 2, '2025-09-04 06:30:00', '2025-09-04 13:00:00', 640.00),
(501, 2, '2025-09-04 14:30:00', '2025-09-04 21:00:00', 640.00),
(502, 1, '2025-09-05 05:30:00', '2025-09-05 12:00:00', 580.00),
(503, 1, '2025-09-05 13:30:00', '2025-09-05 20:00:00', 580.00),
(504, 2, '2025-09-05 06:30:00', '2025-09-05 13:00:00', 640.00),
(505, 2, '2025-09-05 14:30:00', '2025-09-05 21:00:00', 640.00),
(506, 1, '2025-09-06 05:30:00', '2025-09-06 12:00:00', 580.00),
(507, 1, '2025-09-06 13:30:00', '2025-09-06 20:00:00', 580.00),
(508, 2, '2025-09-06 06:30:00', '2025-09-06 13:00:00', 640.00),
(509, 2, '2025-09-06 14:30:00', '2025-09-06 21:00:00', 640.00),
(510, 1, '2025-09-07 06:00:00', '2025-09-07 12:30:00', 580.00),
(511, 1, '2025-09-07 14:00:00', '2025-09-07 20:30:00', 580.00),
(512, 2, '2025-09-07 07:00:00', '2025-09-07 13:30:00', 640.00),
(513, 2, '2025-09-07 15:00:00', '2025-09-07 21:30:00', 640.00),
(514, 1, '2025-09-08 06:00:00', '2025-09-08 12:30:00', 580.00),
(515, 1, '2025-09-08 14:00:00', '2025-09-08 20:30:00', 580.00),
(516, 2, '2025-09-08 07:00:00', '2025-09-08 13:30:00', 640.00),
(517, 2, '2025-09-08 15:00:00', '2025-09-08 21:30:00', 640.00),
(518, 1, '2025-09-09 05:30:00', '2025-09-09 12:00:00', 580.00),
(519, 1, '2025-09-09 13:30:00', '2025-09-09 20:00:00', 580.00),
(520, 2, '2025-09-09 06:30:00', '2025-09-09 13:00:00', 640.00),
(521, 2, '2025-09-09 14:30:00', '2025-09-09 21:00:00', 640.00),
(522, 1, '2025-09-10 05:30:00', '2025-09-10 12:00:00', 580.00),
(523, 1, '2025-09-10 13:30:00', '2025-09-10 20:00:00', 580.00),
(524, 2, '2025-09-10 06:30:00', '2025-09-10 13:00:00', 640.00),
(525, 2, '2025-09-10 14:30:00', '2025-09-10 21:00:00', 640.00),
(526, 1, '2025-09-11 05:30:00', '2025-09-11 12:00:00', 580.00),
(527, 1, '2025-09-11 13:30:00', '2025-09-11 20:00:00', 580.00),
(528, 2, '2025-09-11 06:30:00', '2025-09-11 13:00:00', 640.00),
(529, 2, '2025-09-11 14:30:00', '2025-09-11 21:00:00', 640.00),
(530, 1, '2025-09-12 05:30:00', '2025-09-12 12:00:00', 580.00),
(531, 1, '2025-09-12 13:30:00', '2025-09-12 20:00:00', 580.00),
(532, 2, '2025-09-12 06:30:00', '2025-09-12 13:00:00', 640.00),
(533, 2, '2025-09-12 14:30:00', '2025-09-12 21:00:00', 640.00),
(534, 1, '2025-09-13 05:30:00', '2025-09-13 12:00:00', 580.00),
(535, 1, '2025-09-13 13:30:00', '2025-09-13 20:00:00', 580.00),
(536, 2, '2025-09-13 06:30:00', '2025-09-13 13:00:00', 640.00),
(537, 2, '2025-09-13 14:30:00', '2025-09-13 21:00:00', 640.00),
(538, 1, '2025-09-14 06:00:00', '2025-09-14 12:30:00', 580.00),
(539, 1, '2025-09-14 14:00:00', '2025-09-14 20:30:00', 580.00),
(540, 2, '2025-09-14 07:00:00', '2025-09-14 13:30:00', 640.00),
(541, 2, '2025-09-14 15:00:00', '2025-09-14 21:30:00', 640.00),
(542, 1, '2025-09-15 05:30:00', '2025-09-15 12:00:00', 580.00),
(543, 1, '2025-09-15 13:30:00', '2025-09-15 20:00:00', 580.00),
(544, 2, '2025-09-15 06:30:00', '2025-09-15 13:00:00', 640.00),
(545, 2, '2025-09-15 14:30:00', '2025-09-15 21:00:00', 640.00),
(546, 1, '2025-09-16 05:30:00', '2025-09-16 12:00:00', 580.00),
(547, 1, '2025-09-16 13:30:00', '2025-09-16 20:00:00', 580.00),
(548, 2, '2025-09-16 06:30:00', '2025-09-16 13:00:00', 640.00),
(549, 2, '2025-09-16 14:30:00', '2025-09-16 21:00:00', 640.00),
(550, 1, '2025-09-17 05:30:00', '2025-09-17 12:00:00', 580.00),
(551, 1, '2025-09-17 13:30:00', '2025-09-17 20:00:00', 580.00),
(552, 2, '2025-09-17 06:30:00', '2025-09-17 13:00:00', 640.00),
(553, 2, '2025-09-17 14:30:00', '2025-09-17 21:00:00', 640.00),
(554, 1, '2025-09-18 05:30:00', '2025-09-18 12:00:00', 580.00),
(555, 1, '2025-09-18 13:30:00', '2025-09-18 20:00:00', 580.00),
(556, 2, '2025-09-18 06:30:00', '2025-09-18 13:00:00', 640.00),
(557, 2, '2025-09-18 14:30:00', '2025-09-18 21:00:00', 640.00),
(558, 1, '2025-09-19 05:30:00', '2025-09-19 12:00:00', 580.00),
(559, 1, '2025-09-19 13:30:00', '2025-09-19 20:00:00', 580.00),
(560, 2, '2025-09-19 06:30:00', '2025-09-19 13:00:00', 640.00),
(561, 2, '2025-09-19 14:30:00', '2025-09-19 21:00:00', 640.00),
(562, 1, '2025-09-20 05:30:00', '2025-09-20 12:00:00', 580.00),
(563, 1, '2025-09-20 13:30:00', '2025-09-20 20:00:00', 580.00),
(564, 2, '2025-09-20 06:30:00', '2025-09-20 13:00:00', 640.00),
(565, 2, '2025-09-20 14:30:00', '2025-09-20 21:00:00', 640.00),
(566, 1, '2025-09-21 06:00:00', '2025-09-21 12:30:00', 580.00),
(567, 1, '2025-09-21 14:00:00', '2025-09-21 20:30:00', 580.00),
(568, 2, '2025-09-21 07:00:00', '2025-09-21 13:30:00', 640.00),
(569, 2, '2025-09-21 15:00:00', '2025-09-21 21:30:00', 640.00),
(570, 1, '2025-09-22 05:30:00', '2025-09-22 12:00:00', 580.00),
(571, 1, '2025-09-22 13:30:00', '2025-09-22 20:00:00', 580.00),
(572, 2, '2025-09-22 06:30:00', '2025-09-22 13:00:00', 640.00),
(573, 2, '2025-09-22 14:30:00', '2025-09-22 21:00:00', 640.00),
(574, 1, '2025-09-23 05:30:00', '2025-09-23 12:00:00', 580.00),
(575, 1, '2025-09-23 13:30:00', '2025-09-23 20:00:00', 580.00),
(576, 2, '2025-09-23 06:30:00', '2025-09-23 13:00:00', 640.00),
(577, 2, '2025-09-23 14:30:00', '2025-09-23 21:00:00', 640.00),
(578, 1, '2025-09-24 05:30:00', '2025-09-24 12:00:00', 580.00),
(579, 1, '2025-09-24 13:30:00', '2025-09-24 20:00:00', 580.00),
(580, 2, '2025-09-24 06:30:00', '2025-09-24 13:00:00', 640.00),
(581, 2, '2025-09-24 14:30:00', '2025-09-24 21:00:00', 640.00),
(582, 1, '2025-09-25 05:30:00', '2025-09-25 12:00:00', 580.00),
(583, 1, '2025-09-25 13:30:00', '2025-09-25 20:00:00', 580.00),
(584, 2, '2025-09-25 06:30:00', '2025-09-25 13:00:00', 640.00),
(585, 2, '2025-09-25 14:30:00', '2025-09-25 21:00:00', 640.00),
(586, 1, '2025-09-26 05:30:00', '2025-09-26 12:00:00', 580.00),
(587, 1, '2025-09-26 13:30:00', '2025-09-26 20:00:00', 580.00),
(588, 2, '2025-09-26 06:30:00', '2025-09-26 13:00:00', 640.00),
(589, 2, '2025-09-26 14:30:00', '2025-09-26 21:00:00', 640.00),
(590, 1, '2025-09-27 05:30:00', '2025-09-27 12:00:00', 580.00),
(591, 1, '2025-09-27 13:30:00', '2025-09-27 20:00:00', 580.00),
(592, 2, '2025-09-27 06:30:00', '2025-09-27 13:00:00', 640.00),
(593, 2, '2025-09-27 14:30:00', '2025-09-27 21:00:00', 640.00),
(594, 1, '2025-09-28 06:00:00', '2025-09-28 12:30:00', 580.00),
(595, 1, '2025-09-28 14:00:00', '2025-09-28 20:30:00', 580.00),
(596, 2, '2025-09-28 07:00:00', '2025-09-28 13:30:00', 640.00),
(597, 2, '2025-09-28 15:00:00', '2025-09-28 21:30:00', 640.00),
(598, 1, '2025-09-29 05:30:00', '2025-09-29 12:00:00', 580.00),
(599, 1, '2025-09-29 13:30:00', '2025-09-29 20:00:00', 580.00),
(600, 2, '2025-09-29 06:30:00', '2025-09-29 13:00:00', 640.00),
(601, 2, '2025-09-29 14:30:00', '2025-09-29 21:00:00', 640.00),
(602, 1, '2025-09-30 05:30:00', '2025-09-30 12:00:00', 580.00),
(603, 1, '2025-09-30 13:30:00', '2025-09-30 20:00:00', 580.00),
(604, 2, '2025-09-30 06:30:00', '2025-09-30 13:00:00', 640.00),
(605, 2, '2025-09-30 14:30:00', '2025-09-30 21:00:00', 640.00),
(606, 3, '2025-09-01 06:00:00', '2025-09-01 12:30:00', 580.00),
(607, 3, '2025-09-01 14:00:00', '2025-09-01 20:30:00', 580.00),
(608, 4, '2025-09-01 07:00:00', '2025-09-01 13:30:00', 640.00),
(609, 4, '2025-09-01 15:00:00', '2025-09-01 21:30:00', 640.00),
(610, 3, '2025-09-02 05:30:00', '2025-09-02 12:00:00', 580.00),
(611, 3, '2025-09-02 13:30:00', '2025-09-02 20:00:00', 580.00),
(612, 4, '2025-09-02 06:30:00', '2025-09-02 13:00:00', 640.00),
(613, 4, '2025-09-02 14:30:00', '2025-09-02 21:00:00', 640.00),
(614, 3, '2025-09-03 05:30:00', '2025-09-03 12:00:00', 580.00),
(615, 3, '2025-09-03 13:30:00', '2025-09-03 20:00:00', 580.00),
(616, 4, '2025-09-03 06:30:00', '2025-09-03 13:00:00', 640.00),
(617, 4, '2025-09-03 14:30:00', '2025-09-03 21:00:00', 640.00),
(618, 3, '2025-09-04 05:30:00', '2025-09-04 12:00:00', 580.00),
(619, 3, '2025-09-04 13:30:00', '2025-09-04 20:00:00', 580.00),
(620, 4, '2025-09-04 06:30:00', '2025-09-04 13:00:00', 640.00),
(621, 4, '2025-09-04 14:30:00', '2025-09-04 21:00:00', 640.00),
(622, 3, '2025-09-05 05:30:00', '2025-09-05 12:00:00', 580.00),
(623, 3, '2025-09-05 13:30:00', '2025-09-05 20:00:00', 580.00),
(624, 4, '2025-09-05 06:30:00', '2025-09-05 13:00:00', 640.00),
(625, 4, '2025-09-05 14:30:00', '2025-09-05 21:00:00', 640.00),
(626, 3, '2025-09-06 05:30:00', '2025-09-06 12:00:00', 580.00),
(627, 3, '2025-09-06 13:30:00', '2025-09-06 20:00:00', 580.00),
(628, 4, '2025-09-06 06:30:00', '2025-09-06 13:00:00', 640.00),
(629, 4, '2025-09-06 14:30:00', '2025-09-06 21:00:00', 640.00),
(630, 3, '2025-09-07 06:00:00', '2025-09-07 12:30:00', 580.00),
(631, 3, '2025-09-07 14:00:00', '2025-09-07 20:30:00', 580.00),
(632, 4, '2025-09-07 07:00:00', '2025-09-07 13:30:00', 640.00),
(633, 4, '2025-09-07 15:00:00', '2025-09-07 21:30:00', 640.00),
(634, 3, '2025-09-08 06:00:00', '2025-09-08 12:30:00', 580.00),
(635, 3, '2025-09-08 14:00:00', '2025-09-08 20:30:00', 580.00),
(636, 4, '2025-09-08 07:00:00', '2025-09-08 13:30:00', 640.00),
(637, 4, '2025-09-08 15:00:00', '2025-09-08 21:30:00', 640.00),
(638, 3, '2025-09-09 05:30:00', '2025-09-09 12:00:00', 580.00),
(639, 3, '2025-09-09 13:30:00', '2025-09-09 20:00:00', 580.00),
(640, 4, '2025-09-09 06:30:00', '2025-09-09 13:00:00', 640.00),
(641, 4, '2025-09-09 14:30:00', '2025-09-09 21:00:00', 640.00),
(642, 3, '2025-09-10 05:30:00', '2025-09-10 12:00:00', 580.00),
(643, 3, '2025-09-10 13:30:00', '2025-09-10 20:00:00', 580.00),
(644, 4, '2025-09-10 06:30:00', '2025-09-10 13:00:00', 640.00),
(645, 4, '2025-09-10 14:30:00', '2025-09-10 21:00:00', 640.00),
(646, 3, '2025-09-11 05:30:00', '2025-09-11 12:00:00', 580.00),
(647, 3, '2025-09-11 13:30:00', '2025-09-11 20:00:00', 580.00),
(648, 4, '2025-09-11 06:30:00', '2025-09-11 13:00:00', 640.00),
(649, 4, '2025-09-11 14:30:00', '2025-09-11 21:00:00', 640.00),
(650, 3, '2025-09-12 05:30:00', '2025-09-12 12:00:00', 580.00),
(651, 3, '2025-09-12 13:30:00', '2025-09-12 20:00:00', 580.00),
(652, 4, '2025-09-12 06:30:00', '2025-09-12 13:00:00', 640.00),
(653, 4, '2025-09-12 14:30:00', '2025-09-12 21:00:00', 640.00),
(654, 3, '2025-09-13 05:30:00', '2025-09-13 12:00:00', 580.00),
(655, 3, '2025-09-13 13:30:00', '2025-09-13 20:00:00', 580.00),
(656, 4, '2025-09-13 06:30:00', '2025-09-13 13:00:00', 640.00),
(657, 4, '2025-09-13 14:30:00', '2025-09-13 21:00:00', 640.00),
(658, 3, '2025-09-14 06:00:00', '2025-09-14 12:30:00', 580.00),
(659, 3, '2025-09-14 14:00:00', '2025-09-14 20:30:00', 580.00),
(660, 4, '2025-09-14 07:00:00', '2025-09-14 13:30:00', 640.00),
(661, 4, '2025-09-14 15:00:00', '2025-09-14 21:30:00', 640.00),
(662, 3, '2025-09-15 05:30:00', '2025-09-15 12:00:00', 580.00),
(663, 3, '2025-09-15 13:30:00', '2025-09-15 20:00:00', 580.00),
(664, 4, '2025-09-15 06:30:00', '2025-09-15 13:00:00', 640.00),
(665, 4, '2025-09-15 14:30:00', '2025-09-15 21:00:00', 640.00),
(666, 3, '2025-09-16 05:30:00', '2025-09-16 12:00:00', 580.00),
(667, 3, '2025-09-16 13:30:00', '2025-09-16 20:00:00', 580.00),
(668, 4, '2025-09-16 06:30:00', '2025-09-16 13:00:00', 640.00),
(669, 4, '2025-09-16 14:30:00', '2025-09-16 21:00:00', 640.00),
(670, 3, '2025-09-17 05:30:00', '2025-09-17 12:00:00', 580.00),
(671, 3, '2025-09-17 13:30:00', '2025-09-17 20:00:00', 580.00),
(672, 4, '2025-09-17 06:30:00', '2025-09-17 13:00:00', 640.00),
(673, 4, '2025-09-17 14:30:00', '2025-09-17 21:00:00', 640.00),
(674, 3, '2025-09-18 05:30:00', '2025-09-18 12:00:00', 580.00),
(675, 3, '2025-09-18 13:30:00', '2025-09-18 20:00:00', 580.00),
(676, 4, '2025-09-18 06:30:00', '2025-09-18 13:00:00', 640.00),
(677, 4, '2025-09-18 14:30:00', '2025-09-18 21:00:00', 640.00),
(678, 3, '2025-09-19 05:30:00', '2025-09-19 12:00:00', 580.00),
(679, 3, '2025-09-19 13:30:00', '2025-09-19 20:00:00', 580.00),
(680, 4, '2025-09-19 06:30:00', '2025-09-19 13:00:00', 640.00),
(681, 4, '2025-09-19 14:30:00', '2025-09-19 21:00:00', 640.00),
(682, 3, '2025-09-20 05:30:00', '2025-09-20 12:00:00', 580.00),
(683, 3, '2025-09-20 13:30:00', '2025-09-20 20:00:00', 580.00),
(684, 4, '2025-09-20 06:30:00', '2025-09-20 13:00:00', 640.00),
(685, 4, '2025-09-20 14:30:00', '2025-09-20 21:00:00', 640.00),
(686, 3, '2025-09-21 06:00:00', '2025-09-21 12:30:00', 580.00),
(687, 3, '2025-09-21 14:00:00', '2025-09-21 20:30:00', 580.00),
(688, 4, '2025-09-21 07:00:00', '2025-09-21 13:30:00', 640.00),
(689, 4, '2025-09-21 15:00:00', '2025-09-21 21:30:00', 640.00),
(690, 3, '2025-09-22 05:30:00', '2025-09-22 12:00:00', 580.00),
(691, 3, '2025-09-22 13:30:00', '2025-09-22 20:00:00', 580.00),
(692, 4, '2025-09-22 06:30:00', '2025-09-22 13:00:00', 640.00),
(693, 4, '2025-09-22 14:30:00', '2025-09-22 21:00:00', 640.00),
(694, 3, '2025-09-23 05:30:00', '2025-09-23 12:00:00', 580.00),
(695, 3, '2025-09-23 13:30:00', '2025-09-23 20:00:00', 580.00),
(696, 4, '2025-09-23 06:30:00', '2025-09-23 13:00:00', 640.00),
(697, 4, '2025-09-23 14:30:00', '2025-09-23 21:00:00', 640.00),
(698, 3, '2025-09-24 05:30:00', '2025-09-24 12:00:00', 580.00),
(699, 3, '2025-09-24 13:30:00', '2025-09-24 20:00:00', 580.00),
(700, 4, '2025-09-24 06:30:00', '2025-09-24 13:00:00', 640.00),
(701, 4, '2025-09-24 14:30:00', '2025-09-24 21:00:00', 640.00),
(702, 3, '2025-09-25 05:30:00', '2025-09-25 12:00:00', 580.00),
(703, 3, '2025-09-25 13:30:00', '2025-09-25 20:00:00', 580.00),
(704, 4, '2025-09-25 06:30:00', '2025-09-25 13:00:00', 640.00),
(705, 4, '2025-09-25 14:30:00', '2025-09-25 21:00:00', 640.00),
(706, 3, '2025-09-26 05:30:00', '2025-09-26 12:00:00', 580.00),
(707, 3, '2025-09-26 13:30:00', '2025-09-26 20:00:00', 580.00),
(708, 4, '2025-09-26 06:30:00', '2025-09-26 13:00:00', 640.00),
(709, 4, '2025-09-26 14:30:00', '2025-09-26 21:00:00', 640.00),
(710, 3, '2025-09-27 05:30:00', '2025-09-27 12:00:00', 580.00),
(711, 3, '2025-09-27 13:30:00', '2025-09-27 20:00:00', 580.00),
(712, 4, '2025-09-27 06:30:00', '2025-09-27 13:00:00', 640.00),
(713, 4, '2025-09-27 14:30:00', '2025-09-27 21:00:00', 640.00),
(714, 3, '2025-09-28 06:00:00', '2025-09-28 12:30:00', 580.00),
(715, 3, '2025-09-28 14:00:00', '2025-09-28 20:30:00', 580.00),
(716, 4, '2025-09-28 07:00:00', '2025-09-28 13:30:00', 640.00),
(717, 4, '2025-09-28 15:00:00', '2025-09-28 21:30:00', 640.00),
(718, 3, '2025-09-29 05:30:00', '2025-09-29 12:00:00', 580.00),
(719, 3, '2025-09-29 13:30:00', '2025-09-29 20:00:00', 580.00),
(720, 4, '2025-09-29 06:30:00', '2025-09-29 13:00:00', 640.00),
(721, 4, '2025-09-29 14:30:00', '2025-09-29 21:00:00', 640.00),
(722, 3, '2025-09-30 05:30:00', '2025-09-30 12:00:00', 580.00),
(723, 3, '2025-09-30 13:30:00', '2025-09-30 20:00:00', 580.00),
(724, 4, '2025-09-30 06:30:00', '2025-09-30 13:00:00', 640.00),
(725, 4, '2025-09-30 14:30:00', '2025-09-30 21:00:00', 640.00),
(726, 1, '2025-10-01 05:30:00', '2025-10-01 12:00:00', 580.00),
(727, 1, '2025-10-02 05:30:00', '2025-10-02 12:00:00', 580.00),
(728, 1, '2025-10-03 05:30:00', '2025-10-03 12:00:00', 580.00),
(729, 1, '2025-10-04 06:00:00', '2025-10-04 12:30:00', 580.00),
(730, 1, '2025-10-05 06:00:00', '2025-10-05 12:30:00', 580.00),
(731, 1, '2025-10-06 05:30:00', '2025-10-06 12:00:00', 580.00),
(732, 1, '2025-10-07 05:30:00', '2025-10-07 12:00:00', 580.00),
(733, 1, '2025-10-01 13:30:00', '2025-10-01 20:00:00', 580.00),
(734, 1, '2025-10-02 13:30:00', '2025-10-02 20:00:00', 580.00),
(735, 1, '2025-10-03 13:30:00', '2025-10-03 20:00:00', 580.00),
(736, 1, '2025-10-04 14:00:00', '2025-10-04 20:30:00', 580.00),
(737, 1, '2025-10-05 14:00:00', '2025-10-05 20:30:00', 580.00),
(738, 1, '2025-10-06 13:30:00', '2025-10-06 20:00:00', 580.00),
(739, 1, '2025-10-07 13:30:00', '2025-10-07 20:00:00', 580.00),
(740, 1, '2025-10-08 05:30:00', '2025-10-08 12:00:00', 580.00),
(741, 1, '2025-10-09 05:30:00', '2025-10-09 12:00:00', 580.00),
(742, 1, '2025-10-10 05:30:00', '2025-10-10 12:00:00', 580.00),
(743, 1, '2025-10-11 06:00:00', '2025-10-11 12:30:00', 580.00),
(744, 1, '2025-10-12 06:00:00', '2025-10-12 12:30:00', 580.00),
(745, 1, '2025-10-13 05:30:00', '2025-10-13 12:00:00', 580.00),
(746, 1, '2025-10-14 05:30:00', '2025-10-14 12:00:00', 580.00),
(747, 1, '2025-10-08 13:30:00', '2025-10-08 20:00:00', 580.00),
(748, 1, '2025-10-09 13:30:00', '2025-10-09 20:00:00', 580.00),
(749, 1, '2025-10-10 13:30:00', '2025-10-10 20:00:00', 580.00),
(750, 1, '2025-10-11 14:00:00', '2025-10-11 20:30:00', 580.00),
(751, 1, '2025-10-12 14:00:00', '2025-10-12 20:30:00', 580.00),
(752, 1, '2025-10-13 13:30:00', '2025-10-13 20:00:00', 580.00),
(753, 1, '2025-10-14 13:30:00', '2025-10-14 20:00:00', 580.00),
(754, 1, '2025-10-15 05:30:00', '2025-10-15 12:00:00', 580.00),
(755, 1, '2025-10-16 05:30:00', '2025-10-16 12:00:00', 580.00),
(756, 1, '2025-10-17 05:30:00', '2025-10-17 12:00:00', 580.00),
(757, 1, '2025-10-18 06:00:00', '2025-10-18 12:30:00', 580.00),
(758, 1, '2025-10-19 06:00:00', '2025-10-19 12:30:00', 580.00),
(759, 1, '2025-10-20 05:30:00', '2025-10-20 12:00:00', 580.00),
(760, 1, '2025-10-21 05:30:00', '2025-10-21 12:00:00', 580.00),
(761, 1, '2025-10-15 13:30:00', '2025-10-15 20:00:00', 580.00),
(762, 1, '2025-10-16 13:30:00', '2025-10-16 20:00:00', 580.00),
(763, 1, '2025-10-17 13:30:00', '2025-10-17 20:00:00', 580.00),
(764, 1, '2025-10-18 14:00:00', '2025-10-18 20:30:00', 580.00),
(765, 1, '2025-10-19 14:00:00', '2025-10-19 20:30:00', 580.00),
(766, 1, '2025-10-20 13:30:00', '2025-10-20 20:00:00', 580.00),
(767, 1, '2025-10-21 13:30:00', '2025-10-21 20:00:00', 580.00),
(768, 1, '2025-10-22 05:30:00', '2025-10-22 12:00:00', 580.00),
(769, 1, '2025-10-23 05:30:00', '2025-10-23 12:00:00', 580.00),
(770, 1, '2025-10-24 05:30:00', '2025-10-24 12:00:00', 580.00),
(771, 1, '2025-10-25 06:00:00', '2025-10-25 12:30:00', 580.00),
(772, 1, '2025-10-26 06:00:00', '2025-10-26 12:30:00', 580.00),
(773, 1, '2025-10-27 05:30:00', '2025-10-27 12:00:00', 580.00),
(774, 1, '2025-10-28 05:30:00', '2025-10-28 12:00:00', 580.00),
(775, 1, '2025-10-22 13:30:00', '2025-10-22 20:00:00', 580.00),
(776, 1, '2025-10-23 13:30:00', '2025-10-23 20:00:00', 580.00),
(777, 1, '2025-10-24 13:30:00', '2025-10-24 20:00:00', 580.00),
(778, 1, '2025-10-25 14:00:00', '2025-10-25 20:30:00', 580.00),
(779, 1, '2025-10-26 14:00:00', '2025-10-26 20:30:00', 580.00),
(780, 1, '2025-10-27 13:30:00', '2025-10-27 20:00:00', 580.00),
(781, 1, '2025-10-28 13:30:00', '2025-10-28 20:00:00', 580.00),
(782, 1, '2025-10-29 05:30:00', '2025-10-29 12:00:00', 580.00),
(783, 1, '2025-10-30 05:30:00', '2025-10-30 12:00:00', 580.00),
(784, 1, '2025-10-31 05:30:00', '2025-10-31 12:00:00', 580.00),
(785, 1, '2025-10-29 13:30:00', '2025-10-29 20:00:00', 580.00),
(786, 1, '2025-10-30 13:30:00', '2025-10-30 20:00:00', 580.00),
(787, 1, '2025-10-31 13:30:00', '2025-10-31 20:00:00', 580.00),
(788, 2, '2025-10-01 06:30:00', '2025-10-01 13:00:00', 640.00),
(789, 2, '2025-10-02 06:30:00', '2025-10-02 13:00:00', 640.00),
(790, 2, '2025-10-03 06:30:00', '2025-10-03 13:00:00', 640.00),
(791, 2, '2025-10-04 07:00:00', '2025-10-04 13:30:00', 640.00),
(792, 2, '2025-10-05 07:00:00', '2025-10-05 13:30:00', 640.00),
(793, 2, '2025-10-06 06:30:00', '2025-10-06 13:00:00', 640.00),
(794, 2, '2025-10-07 06:30:00', '2025-10-07 13:00:00', 640.00),
(795, 2, '2025-10-01 14:30:00', '2025-10-01 21:00:00', 640.00),
(796, 2, '2025-10-02 14:30:00', '2025-10-02 21:00:00', 640.00),
(797, 2, '2025-10-03 14:30:00', '2025-10-03 21:00:00', 640.00),
(798, 2, '2025-10-04 15:00:00', '2025-10-04 21:30:00', 640.00),
(799, 2, '2025-10-05 15:00:00', '2025-10-05 21:30:00', 640.00),
(800, 2, '2025-10-06 14:30:00', '2025-10-06 21:00:00', 640.00),
(801, 2, '2025-10-07 14:30:00', '2025-10-07 21:00:00', 640.00),
(802, 2, '2025-10-08 06:30:00', '2025-10-08 13:00:00', 640.00),
(803, 2, '2025-10-09 06:30:00', '2025-10-09 13:00:00', 640.00),
(804, 2, '2025-10-10 06:30:00', '2025-10-10 13:00:00', 640.00),
(805, 2, '2025-10-11 07:00:00', '2025-10-11 13:30:00', 640.00),
(806, 2, '2025-10-12 07:00:00', '2025-10-12 13:30:00', 640.00),
(807, 2, '2025-10-13 06:30:00', '2025-10-13 13:00:00', 640.00),
(808, 2, '2025-10-14 06:30:00', '2025-10-14 13:00:00', 640.00),
(809, 2, '2025-10-08 14:30:00', '2025-10-08 21:00:00', 640.00),
(810, 2, '2025-10-09 14:30:00', '2025-10-09 21:00:00', 640.00),
(811, 2, '2025-10-10 14:30:00', '2025-10-10 21:00:00', 640.00),
(812, 2, '2025-10-11 15:00:00', '2025-10-11 21:30:00', 640.00),
(813, 2, '2025-10-12 15:00:00', '2025-10-12 21:30:00', 640.00),
(814, 2, '2025-10-13 14:30:00', '2025-10-13 21:00:00', 640.00),
(815, 2, '2025-10-14 14:30:00', '2025-10-14 21:00:00', 640.00),
(816, 2, '2025-10-15 06:30:00', '2025-10-15 13:00:00', 640.00),
(817, 2, '2025-10-16 06:30:00', '2025-10-16 13:00:00', 640.00),
(818, 2, '2025-10-17 06:30:00', '2025-10-17 13:00:00', 640.00),
(819, 2, '2025-10-18 07:00:00', '2025-10-18 13:30:00', 640.00),
(820, 2, '2025-10-19 07:00:00', '2025-10-19 13:30:00', 640.00),
(821, 2, '2025-10-20 06:30:00', '2025-10-20 13:00:00', 640.00),
(822, 2, '2025-10-21 06:30:00', '2025-10-21 13:00:00', 640.00),
(823, 2, '2025-10-15 14:30:00', '2025-10-15 21:00:00', 640.00),
(824, 2, '2025-10-16 14:30:00', '2025-10-16 21:00:00', 640.00),
(825, 2, '2025-10-17 14:30:00', '2025-10-17 21:00:00', 640.00),
(826, 2, '2025-10-18 15:00:00', '2025-10-18 21:30:00', 640.00),
(827, 2, '2025-10-19 15:00:00', '2025-10-19 21:30:00', 640.00),
(828, 2, '2025-10-20 14:30:00', '2025-10-20 21:00:00', 640.00),
(829, 2, '2025-10-21 14:30:00', '2025-10-21 21:00:00', 640.00),
(830, 2, '2025-10-22 06:30:00', '2025-10-22 13:00:00', 640.00),
(831, 2, '2025-10-23 06:30:00', '2025-10-23 13:00:00', 640.00),
(832, 2, '2025-10-24 06:30:00', '2025-10-24 13:00:00', 640.00),
(833, 2, '2025-10-25 07:00:00', '2025-10-25 13:30:00', 640.00),
(834, 2, '2025-10-26 07:00:00', '2025-10-26 13:30:00', 640.00),
(835, 2, '2025-10-27 06:30:00', '2025-10-27 13:00:00', 640.00),
(836, 2, '2025-10-28 06:30:00', '2025-10-28 13:00:00', 640.00),
(837, 2, '2025-10-22 14:30:00', '2025-10-22 21:00:00', 640.00),
(838, 2, '2025-10-23 14:30:00', '2025-10-23 21:00:00', 640.00),
(839, 2, '2025-10-24 14:30:00', '2025-10-24 21:00:00', 640.00),
(840, 2, '2025-10-25 15:00:00', '2025-10-25 21:30:00', 640.00),
(841, 2, '2025-10-26 15:00:00', '2025-10-26 21:30:00', 640.00),
(842, 2, '2025-10-27 14:30:00', '2025-10-27 21:00:00', 640.00),
(843, 2, '2025-10-28 14:30:00', '2025-10-28 21:00:00', 640.00),
(844, 2, '2025-10-29 06:30:00', '2025-10-29 13:00:00', 640.00),
(845, 2, '2025-10-30 06:30:00', '2025-10-30 13:00:00', 640.00),
(846, 2, '2025-10-31 06:30:00', '2025-10-31 13:00:00', 640.00),
(847, 2, '2025-10-29 14:30:00', '2025-10-29 21:00:00', 640.00),
(848, 2, '2025-10-30 14:30:00', '2025-10-30 21:00:00', 640.00),
(849, 2, '2025-10-31 14:30:00', '2025-10-31 21:00:00', 640.00),
(850, 3, '2025-10-01 05:30:00', '2025-10-01 12:00:00', 580.00),
(851, 3, '2025-10-02 05:30:00', '2025-10-02 12:00:00', 580.00),
(852, 3, '2025-10-03 05:30:00', '2025-10-03 12:00:00', 580.00),
(853, 3, '2025-10-04 06:00:00', '2025-10-04 12:30:00', 580.00),
(854, 3, '2025-10-05 06:00:00', '2025-10-05 12:30:00', 580.00),
(855, 3, '2025-10-06 05:30:00', '2025-10-06 12:00:00', 580.00),
(856, 3, '2025-10-07 05:30:00', '2025-10-07 12:00:00', 580.00),
(857, 3, '2025-10-01 13:30:00', '2025-10-01 20:00:00', 580.00),
(858, 3, '2025-10-02 13:30:00', '2025-10-02 20:00:00', 580.00),
(859, 3, '2025-10-03 13:30:00', '2025-10-03 20:00:00', 580.00),
(860, 3, '2025-10-04 14:00:00', '2025-10-04 20:30:00', 580.00),
(861, 3, '2025-10-05 14:00:00', '2025-10-05 20:30:00', 580.00),
(862, 3, '2025-10-06 13:30:00', '2025-10-06 20:00:00', 580.00),
(863, 3, '2025-10-07 13:30:00', '2025-10-07 20:00:00', 580.00),
(864, 3, '2025-10-08 05:30:00', '2025-10-08 12:00:00', 580.00),
(865, 3, '2025-10-09 05:30:00', '2025-10-09 12:00:00', 580.00),
(866, 3, '2025-10-10 05:30:00', '2025-10-10 12:00:00', 580.00),
(867, 3, '2025-10-11 06:00:00', '2025-10-11 12:30:00', 580.00),
(868, 3, '2025-10-12 06:00:00', '2025-10-12 12:30:00', 580.00),
(869, 3, '2025-10-13 05:30:00', '2025-10-13 12:00:00', 580.00),
(870, 3, '2025-10-14 05:30:00', '2025-10-14 12:00:00', 580.00),
(871, 3, '2025-10-08 13:30:00', '2025-10-08 20:00:00', 580.00),
(872, 3, '2025-10-09 13:30:00', '2025-10-09 20:00:00', 580.00),
(873, 3, '2025-10-10 13:30:00', '2025-10-10 20:00:00', 580.00),
(874, 3, '2025-10-11 14:00:00', '2025-10-11 20:30:00', 580.00),
(875, 3, '2025-10-12 14:00:00', '2025-10-12 20:30:00', 580.00),
(876, 3, '2025-10-13 13:30:00', '2025-10-13 20:00:00', 580.00),
(877, 3, '2025-10-14 13:30:00', '2025-10-14 20:00:00', 580.00),
(878, 3, '2025-10-15 05:30:00', '2025-10-15 12:00:00', 580.00),
(879, 3, '2025-10-16 05:30:00', '2025-10-16 12:00:00', 580.00),
(880, 3, '2025-10-17 05:30:00', '2025-10-17 12:00:00', 580.00),
(881, 3, '2025-10-18 06:00:00', '2025-10-18 12:30:00', 580.00),
(882, 3, '2025-10-19 06:00:00', '2025-10-19 12:30:00', 580.00),
(883, 3, '2025-10-20 05:30:00', '2025-10-20 12:00:00', 580.00),
(884, 3, '2025-10-21 05:30:00', '2025-10-21 12:00:00', 580.00),
(885, 3, '2025-10-15 13:30:00', '2025-10-15 20:00:00', 580.00),
(886, 3, '2025-10-16 13:30:00', '2025-10-16 20:00:00', 580.00),
(887, 3, '2025-10-17 13:30:00', '2025-10-17 20:00:00', 580.00),
(888, 3, '2025-10-18 14:00:00', '2025-10-18 20:30:00', 580.00),
(889, 3, '2025-10-19 14:00:00', '2025-10-19 20:30:00', 580.00),
(890, 3, '2025-10-20 13:30:00', '2025-10-20 20:00:00', 580.00),
(891, 3, '2025-10-21 13:30:00', '2025-10-21 20:00:00', 580.00),
(892, 3, '2025-10-22 05:30:00', '2025-10-22 12:00:00', 580.00),
(893, 3, '2025-10-23 05:30:00', '2025-10-23 12:00:00', 580.00),
(894, 3, '2025-10-24 05:30:00', '2025-10-24 12:00:00', 580.00),
(895, 3, '2025-10-25 06:00:00', '2025-10-25 12:30:00', 580.00),
(896, 3, '2025-10-26 06:00:00', '2025-10-26 12:30:00', 580.00),
(897, 3, '2025-10-27 05:30:00', '2025-10-27 12:00:00', 580.00),
(898, 3, '2025-10-28 05:30:00', '2025-10-28 12:00:00', 580.00),
(899, 3, '2025-10-22 13:30:00', '2025-10-22 20:00:00', 580.00),
(900, 3, '2025-10-23 13:30:00', '2025-10-23 20:00:00', 580.00),
(901, 3, '2025-10-24 13:30:00', '2025-10-24 20:00:00', 580.00),
(902, 3, '2025-10-25 14:00:00', '2025-10-25 20:30:00', 580.00),
(903, 3, '2025-10-26 14:00:00', '2025-10-26 20:30:00', 580.00),
(904, 3, '2025-10-27 13:30:00', '2025-10-27 20:00:00', 580.00),
(905, 3, '2025-10-28 13:30:00', '2025-10-28 20:00:00', 580.00),
(906, 3, '2025-10-29 05:30:00', '2025-10-29 12:00:00', 580.00),
(907, 3, '2025-10-30 05:30:00', '2025-10-30 12:00:00', 580.00),
(908, 3, '2025-10-31 05:30:00', '2025-10-31 12:00:00', 580.00),
(909, 3, '2025-10-29 13:30:00', '2025-10-29 20:00:00', 580.00),
(910, 3, '2025-10-30 13:30:00', '2025-10-30 20:00:00', 580.00),
(911, 3, '2025-10-31 13:30:00', '2025-10-31 20:00:00', 580.00),
(912, 4, '2025-10-01 06:30:00', '2025-10-01 13:00:00', 640.00),
(913, 4, '2025-10-02 06:30:00', '2025-10-02 13:00:00', 640.00),
(914, 4, '2025-10-03 06:30:00', '2025-10-03 13:00:00', 640.00),
(915, 4, '2025-10-04 07:00:00', '2025-10-04 13:30:00', 640.00),
(916, 4, '2025-10-05 07:00:00', '2025-10-05 13:30:00', 640.00),
(917, 4, '2025-10-06 06:30:00', '2025-10-06 13:00:00', 640.00),
(918, 4, '2025-10-07 06:30:00', '2025-10-07 13:00:00', 640.00),
(919, 4, '2025-10-01 14:30:00', '2025-10-01 21:00:00', 640.00),
(920, 4, '2025-10-02 14:30:00', '2025-10-02 21:00:00', 640.00),
(921, 4, '2025-10-03 14:30:00', '2025-10-03 21:00:00', 640.00),
(922, 4, '2025-10-04 15:00:00', '2025-10-04 21:30:00', 640.00),
(923, 4, '2025-10-05 15:00:00', '2025-10-05 21:30:00', 640.00),
(924, 4, '2025-10-06 14:30:00', '2025-10-06 21:00:00', 640.00),
(925, 4, '2025-10-07 14:30:00', '2025-10-07 21:00:00', 640.00),
(926, 4, '2025-10-08 06:30:00', '2025-10-08 13:00:00', 640.00),
(927, 4, '2025-10-09 06:30:00', '2025-10-09 13:00:00', 640.00),
(928, 4, '2025-10-10 06:30:00', '2025-10-10 13:00:00', 640.00),
(929, 4, '2025-10-11 07:00:00', '2025-10-11 13:30:00', 640.00),
(930, 4, '2025-10-12 07:00:00', '2025-10-12 13:30:00', 640.00),
(931, 4, '2025-10-13 06:30:00', '2025-10-13 13:00:00', 640.00),
(932, 4, '2025-10-14 06:30:00', '2025-10-14 13:00:00', 640.00),
(933, 4, '2025-10-08 14:30:00', '2025-10-08 21:00:00', 640.00),
(934, 4, '2025-10-09 14:30:00', '2025-10-09 21:00:00', 640.00),
(935, 4, '2025-10-10 14:30:00', '2025-10-10 21:00:00', 640.00),
(936, 4, '2025-10-11 15:00:00', '2025-10-11 21:30:00', 640.00),
(937, 4, '2025-10-12 15:00:00', '2025-10-12 21:30:00', 640.00),
(938, 4, '2025-10-13 14:30:00', '2025-10-13 21:00:00', 640.00),
(939, 4, '2025-10-14 14:30:00', '2025-10-14 21:00:00', 640.00),
(940, 4, '2025-10-15 06:30:00', '2025-10-15 13:00:00', 640.00),
(941, 4, '2025-10-16 06:30:00', '2025-10-16 13:00:00', 640.00),
(942, 4, '2025-10-17 06:30:00', '2025-10-17 13:00:00', 640.00),
(943, 4, '2025-10-18 07:00:00', '2025-10-18 13:30:00', 640.00),
(944, 4, '2025-10-19 07:00:00', '2025-10-19 13:30:00', 640.00),
(945, 4, '2025-10-20 06:30:00', '2025-10-20 13:00:00', 640.00),
(946, 4, '2025-10-21 06:30:00', '2025-10-21 13:00:00', 640.00),
(947, 4, '2025-10-15 14:30:00', '2025-10-15 21:00:00', 640.00),
(948, 4, '2025-10-16 14:30:00', '2025-10-16 21:00:00', 640.00),
(949, 4, '2025-10-17 14:30:00', '2025-10-17 21:00:00', 640.00),
(950, 4, '2025-10-18 15:00:00', '2025-10-18 21:30:00', 640.00),
(951, 4, '2025-10-19 15:00:00', '2025-10-19 21:30:00', 640.00),
(952, 4, '2025-10-20 14:30:00', '2025-10-20 21:00:00', 640.00),
(953, 4, '2025-10-21 14:30:00', '2025-10-21 21:00:00', 640.00),
(954, 4, '2025-10-22 06:30:00', '2025-10-22 13:00:00', 640.00),
(955, 4, '2025-10-23 06:30:00', '2025-10-23 13:00:00', 640.00),
(956, 4, '2025-10-24 06:30:00', '2025-10-24 13:00:00', 640.00),
(957, 4, '2025-10-25 07:00:00', '2025-10-25 13:30:00', 640.00),
(958, 4, '2025-10-26 07:00:00', '2025-10-26 13:30:00', 640.00),
(959, 4, '2025-10-27 06:30:00', '2025-10-27 13:00:00', 640.00),
(960, 4, '2025-10-28 06:30:00', '2025-10-28 13:00:00', 640.00),
(961, 4, '2025-10-22 14:30:00', '2025-10-22 21:00:00', 640.00),
(962, 4, '2025-10-23 14:30:00', '2025-10-23 21:00:00', 640.00),
(963, 4, '2025-10-24 14:30:00', '2025-10-24 21:00:00', 640.00),
(964, 4, '2025-10-25 15:00:00', '2025-10-25 21:30:00', 640.00),
(965, 4, '2025-10-26 15:00:00', '2025-10-26 21:30:00', 640.00),
(966, 4, '2025-10-27 14:30:00', '2025-10-27 21:00:00', 640.00),
(967, 4, '2025-10-28 14:30:00', '2025-10-28 21:00:00', 640.00),
(968, 4, '2025-10-29 06:30:00', '2025-10-29 13:00:00', 640.00),
(969, 4, '2025-10-30 06:30:00', '2025-10-30 13:00:00', 640.00),
(970, 4, '2025-10-31 06:30:00', '2025-10-31 13:00:00', 640.00),
(971, 4, '2025-10-29 14:30:00', '2025-10-29 21:00:00', 640.00),
(972, 4, '2025-10-30 14:30:00', '2025-10-30 21:00:00', 640.00),
(973, 4, '2025-10-31 14:30:00', '2025-10-31 21:00:00', 640.00),
(974, 1, '2025-09-01 06:00:00', '2025-09-01 12:30:00', 580.00),
(975, 1, '2025-09-01 14:00:00', '2025-09-01 20:30:00', 580.00),
(976, 2, '2025-09-01 07:00:00', '2025-09-01 13:30:00', 640.00),
(977, 2, '2025-09-01 15:00:00', '2025-09-01 21:30:00', 640.00),
(978, 1, '2025-09-02 05:30:00', '2025-09-02 12:00:00', 580.00),
(979, 1, '2025-09-02 13:30:00', '2025-09-02 20:00:00', 580.00),
(980, 2, '2025-09-02 06:30:00', '2025-09-02 13:00:00', 640.00),
(981, 2, '2025-09-02 14:30:00', '2025-09-02 21:00:00', 640.00),
(982, 1, '2025-09-03 05:30:00', '2025-09-03 12:00:00', 580.00),
(983, 1, '2025-09-03 13:30:00', '2025-09-03 20:00:00', 580.00),
(984, 2, '2025-09-03 06:30:00', '2025-09-03 13:00:00', 640.00),
(985, 2, '2025-09-03 14:30:00', '2025-09-03 21:00:00', 640.00),
(986, 1, '2025-09-04 05:30:00', '2025-09-04 12:00:00', 580.00),
(987, 1, '2025-09-04 13:30:00', '2025-09-04 20:00:00', 580.00),
(988, 2, '2025-09-04 06:30:00', '2025-09-04 13:00:00', 640.00),
(989, 2, '2025-09-04 14:30:00', '2025-09-04 21:00:00', 640.00),
(990, 1, '2025-09-05 05:30:00', '2025-09-05 12:00:00', 580.00),
(991, 1, '2025-09-05 13:30:00', '2025-09-05 20:00:00', 580.00),
(992, 2, '2025-09-05 06:30:00', '2025-09-05 13:00:00', 640.00),
(993, 2, '2025-09-05 14:30:00', '2025-09-05 21:00:00', 640.00);
INSERT INTO `Schedule` (`ID`, `BusID`, `DepartureTime`, `ArrivalTime`, `Fare`) VALUES
(994, 1, '2025-09-06 05:30:00', '2025-09-06 12:00:00', 580.00),
(995, 1, '2025-09-06 13:30:00', '2025-09-06 20:00:00', 580.00),
(996, 2, '2025-09-06 06:30:00', '2025-09-06 13:00:00', 640.00),
(997, 2, '2025-09-06 14:30:00', '2025-09-06 21:00:00', 640.00),
(998, 1, '2025-09-07 06:00:00', '2025-09-07 12:30:00', 580.00),
(999, 1, '2025-09-07 14:00:00', '2025-09-07 20:30:00', 580.00),
(1000, 2, '2025-09-07 07:00:00', '2025-09-07 13:30:00', 640.00),
(1001, 2, '2025-09-07 15:00:00', '2025-09-07 21:30:00', 640.00),
(1002, 1, '2025-09-08 06:00:00', '2025-09-08 12:30:00', 580.00),
(1003, 1, '2025-09-08 14:00:00', '2025-09-08 20:30:00', 580.00),
(1004, 2, '2025-09-08 07:00:00', '2025-09-08 13:30:00', 640.00),
(1005, 2, '2025-09-08 15:00:00', '2025-09-08 21:30:00', 640.00),
(1006, 1, '2025-09-09 05:30:00', '2025-09-09 12:00:00', 580.00),
(1007, 1, '2025-09-09 13:30:00', '2025-09-09 20:00:00', 580.00),
(1008, 2, '2025-09-09 06:30:00', '2025-09-09 13:00:00', 640.00),
(1009, 2, '2025-09-09 14:30:00', '2025-09-09 21:00:00', 640.00),
(1010, 1, '2025-09-10 05:30:00', '2025-09-10 12:00:00', 580.00),
(1011, 1, '2025-09-10 13:30:00', '2025-09-10 20:00:00', 580.00),
(1012, 2, '2025-09-10 06:30:00', '2025-09-10 13:00:00', 640.00),
(1013, 2, '2025-09-10 14:30:00', '2025-09-10 21:00:00', 640.00),
(1014, 1, '2025-09-11 05:30:00', '2025-09-11 12:00:00', 580.00),
(1015, 1, '2025-09-11 13:30:00', '2025-09-11 20:00:00', 580.00),
(1016, 2, '2025-09-11 06:30:00', '2025-09-11 13:00:00', 640.00),
(1017, 2, '2025-09-11 14:30:00', '2025-09-11 21:00:00', 640.00),
(1018, 1, '2025-09-12 05:30:00', '2025-09-12 12:00:00', 580.00),
(1019, 1, '2025-09-12 13:30:00', '2025-09-12 20:00:00', 580.00),
(1020, 2, '2025-09-12 06:30:00', '2025-09-12 13:00:00', 640.00),
(1021, 2, '2025-09-12 14:30:00', '2025-09-12 21:00:00', 640.00),
(1022, 1, '2025-09-13 05:30:00', '2025-09-13 12:00:00', 580.00),
(1023, 1, '2025-09-13 13:30:00', '2025-09-13 20:00:00', 580.00),
(1024, 2, '2025-09-13 06:30:00', '2025-09-13 13:00:00', 640.00),
(1025, 2, '2025-09-13 14:30:00', '2025-09-13 21:00:00', 640.00),
(1026, 1, '2025-09-14 06:00:00', '2025-09-14 12:30:00', 580.00),
(1027, 1, '2025-09-14 14:00:00', '2025-09-14 20:30:00', 580.00),
(1028, 2, '2025-09-14 07:00:00', '2025-09-14 13:30:00', 640.00),
(1029, 2, '2025-09-14 15:00:00', '2025-09-14 21:30:00', 640.00),
(1030, 1, '2025-09-15 05:30:00', '2025-09-15 12:00:00', 580.00),
(1031, 1, '2025-09-15 13:30:00', '2025-09-15 20:00:00', 580.00),
(1032, 2, '2025-09-15 06:30:00', '2025-09-15 13:00:00', 640.00),
(1033, 2, '2025-09-15 14:30:00', '2025-09-15 21:00:00', 640.00),
(1034, 1, '2025-09-16 05:30:00', '2025-09-16 12:00:00', 580.00),
(1035, 1, '2025-09-16 13:30:00', '2025-09-16 20:00:00', 580.00),
(1036, 2, '2025-09-16 06:30:00', '2025-09-16 13:00:00', 640.00),
(1037, 2, '2025-09-16 14:30:00', '2025-09-16 21:00:00', 640.00),
(1038, 1, '2025-09-17 05:30:00', '2025-09-17 12:00:00', 580.00),
(1039, 1, '2025-09-17 13:30:00', '2025-09-17 20:00:00', 580.00),
(1040, 2, '2025-09-17 06:30:00', '2025-09-17 13:00:00', 640.00),
(1041, 2, '2025-09-17 14:30:00', '2025-09-17 21:00:00', 640.00),
(1042, 1, '2025-09-18 05:30:00', '2025-09-18 12:00:00', 580.00),
(1043, 1, '2025-09-18 13:30:00', '2025-09-18 20:00:00', 580.00),
(1044, 2, '2025-09-18 06:30:00', '2025-09-18 13:00:00', 640.00),
(1045, 2, '2025-09-18 14:30:00', '2025-09-18 21:00:00', 640.00),
(1046, 1, '2025-09-19 05:30:00', '2025-09-19 12:00:00', 580.00),
(1047, 1, '2025-09-19 13:30:00', '2025-09-19 20:00:00', 580.00),
(1048, 2, '2025-09-19 06:30:00', '2025-09-19 13:00:00', 640.00),
(1049, 2, '2025-09-19 14:30:00', '2025-09-19 21:00:00', 640.00),
(1050, 1, '2025-09-20 05:30:00', '2025-09-20 12:00:00', 580.00),
(1051, 1, '2025-09-20 13:30:00', '2025-09-20 20:00:00', 580.00),
(1052, 2, '2025-09-20 06:30:00', '2025-09-20 13:00:00', 640.00),
(1053, 2, '2025-09-20 14:30:00', '2025-09-20 21:00:00', 640.00),
(1054, 1, '2025-09-21 06:00:00', '2025-09-21 12:30:00', 580.00),
(1055, 1, '2025-09-21 14:00:00', '2025-09-21 20:30:00', 580.00),
(1056, 2, '2025-09-21 07:00:00', '2025-09-21 13:30:00', 640.00),
(1057, 2, '2025-09-21 15:00:00', '2025-09-21 21:30:00', 640.00),
(1058, 1, '2025-09-22 05:30:00', '2025-09-22 12:00:00', 580.00),
(1059, 1, '2025-09-22 13:30:00', '2025-09-22 20:00:00', 580.00),
(1060, 2, '2025-09-22 06:30:00', '2025-09-22 13:00:00', 640.00),
(1061, 2, '2025-09-22 14:30:00', '2025-09-22 21:00:00', 640.00),
(1062, 1, '2025-09-23 05:30:00', '2025-09-23 12:00:00', 580.00),
(1063, 1, '2025-09-23 13:30:00', '2025-09-23 20:00:00', 580.00),
(1064, 2, '2025-09-23 06:30:00', '2025-09-23 13:00:00', 640.00),
(1065, 2, '2025-09-23 14:30:00', '2025-09-23 21:00:00', 640.00),
(1066, 1, '2025-09-24 05:30:00', '2025-09-24 12:00:00', 580.00),
(1067, 1, '2025-09-24 13:30:00', '2025-09-24 20:00:00', 580.00),
(1068, 2, '2025-09-24 06:30:00', '2025-09-24 13:00:00', 640.00),
(1069, 2, '2025-09-24 14:30:00', '2025-09-24 21:00:00', 640.00),
(1070, 1, '2025-09-25 05:30:00', '2025-09-25 12:00:00', 580.00),
(1071, 1, '2025-09-25 13:30:00', '2025-09-25 20:00:00', 580.00),
(1072, 2, '2025-09-25 06:30:00', '2025-09-25 13:00:00', 640.00),
(1073, 2, '2025-09-25 14:30:00', '2025-09-25 21:00:00', 640.00),
(1074, 1, '2025-09-26 05:30:00', '2025-09-26 12:00:00', 580.00),
(1075, 1, '2025-09-26 13:30:00', '2025-09-26 20:00:00', 580.00),
(1076, 2, '2025-09-26 06:30:00', '2025-09-26 13:00:00', 640.00),
(1077, 2, '2025-09-26 14:30:00', '2025-09-26 21:00:00', 640.00),
(1078, 1, '2025-09-27 05:30:00', '2025-09-27 12:00:00', 580.00),
(1079, 1, '2025-09-27 13:30:00', '2025-09-27 20:00:00', 580.00),
(1080, 2, '2025-09-27 06:30:00', '2025-09-27 13:00:00', 640.00),
(1081, 2, '2025-09-27 14:30:00', '2025-09-27 21:00:00', 640.00),
(1082, 1, '2025-09-28 06:00:00', '2025-09-28 12:30:00', 580.00),
(1083, 1, '2025-09-28 14:00:00', '2025-09-28 20:30:00', 580.00),
(1084, 2, '2025-09-28 07:00:00', '2025-09-28 13:30:00', 640.00),
(1085, 2, '2025-09-28 15:00:00', '2025-09-28 21:30:00', 640.00),
(1086, 1, '2025-09-29 05:30:00', '2025-09-29 12:00:00', 580.00),
(1087, 1, '2025-09-29 13:30:00', '2025-09-29 20:00:00', 580.00),
(1088, 2, '2025-09-29 06:30:00', '2025-09-29 13:00:00', 640.00),
(1089, 2, '2025-09-29 14:30:00', '2025-09-29 21:00:00', 640.00),
(1090, 1, '2025-09-30 05:30:00', '2025-09-30 12:00:00', 580.00),
(1091, 1, '2025-09-30 13:30:00', '2025-09-30 20:00:00', 580.00),
(1092, 2, '2025-09-30 06:30:00', '2025-09-30 13:00:00', 640.00),
(1093, 2, '2025-09-30 14:30:00', '2025-09-30 21:00:00', 640.00),
(1094, 3, '2025-09-01 06:00:00', '2025-09-01 12:30:00', 580.00),
(1095, 3, '2025-09-01 14:00:00', '2025-09-01 20:30:00', 580.00),
(1096, 4, '2025-09-01 07:00:00', '2025-09-01 13:30:00', 640.00),
(1097, 4, '2025-09-01 15:00:00', '2025-09-01 21:30:00', 640.00),
(1098, 3, '2025-09-02 05:30:00', '2025-09-02 12:00:00', 580.00),
(1099, 3, '2025-09-02 13:30:00', '2025-09-02 20:00:00', 580.00),
(1100, 4, '2025-09-02 06:30:00', '2025-09-02 13:00:00', 640.00),
(1101, 4, '2025-09-02 14:30:00', '2025-09-02 21:00:00', 640.00),
(1102, 3, '2025-09-03 05:30:00', '2025-09-03 12:00:00', 580.00),
(1103, 3, '2025-09-03 13:30:00', '2025-09-03 20:00:00', 580.00),
(1104, 4, '2025-09-03 06:30:00', '2025-09-03 13:00:00', 640.00),
(1105, 4, '2025-09-03 14:30:00', '2025-09-03 21:00:00', 640.00),
(1106, 3, '2025-09-04 05:30:00', '2025-09-04 12:00:00', 580.00),
(1107, 3, '2025-09-04 13:30:00', '2025-09-04 20:00:00', 580.00),
(1108, 4, '2025-09-04 06:30:00', '2025-09-04 13:00:00', 640.00),
(1109, 4, '2025-09-04 14:30:00', '2025-09-04 21:00:00', 640.00),
(1110, 3, '2025-09-05 05:30:00', '2025-09-05 12:00:00', 580.00),
(1111, 3, '2025-09-05 13:30:00', '2025-09-05 20:00:00', 580.00),
(1112, 4, '2025-09-05 06:30:00', '2025-09-05 13:00:00', 640.00),
(1113, 4, '2025-09-05 14:30:00', '2025-09-05 21:00:00', 640.00),
(1114, 3, '2025-09-06 05:30:00', '2025-09-06 12:00:00', 580.00),
(1115, 3, '2025-09-06 13:30:00', '2025-09-06 20:00:00', 580.00),
(1116, 4, '2025-09-06 06:30:00', '2025-09-06 13:00:00', 640.00),
(1117, 4, '2025-09-06 14:30:00', '2025-09-06 21:00:00', 640.00),
(1118, 3, '2025-09-07 06:00:00', '2025-09-07 12:30:00', 580.00),
(1119, 3, '2025-09-07 14:00:00', '2025-09-07 20:30:00', 580.00),
(1120, 4, '2025-09-07 07:00:00', '2025-09-07 13:30:00', 640.00),
(1121, 4, '2025-09-07 15:00:00', '2025-09-07 21:30:00', 640.00),
(1122, 3, '2025-09-08 06:00:00', '2025-09-08 12:30:00', 580.00),
(1123, 3, '2025-09-08 14:00:00', '2025-09-08 20:30:00', 580.00),
(1124, 4, '2025-09-08 07:00:00', '2025-09-08 13:30:00', 640.00),
(1125, 4, '2025-09-08 15:00:00', '2025-09-08 21:30:00', 640.00),
(1126, 3, '2025-09-09 05:30:00', '2025-09-09 12:00:00', 580.00),
(1127, 3, '2025-09-09 13:30:00', '2025-09-09 20:00:00', 580.00),
(1128, 4, '2025-09-09 06:30:00', '2025-09-09 13:00:00', 640.00),
(1129, 4, '2025-09-09 14:30:00', '2025-09-09 21:00:00', 640.00),
(1130, 3, '2025-09-10 05:30:00', '2025-09-10 12:00:00', 580.00),
(1131, 3, '2025-09-10 13:30:00', '2025-09-10 20:00:00', 580.00),
(1132, 4, '2025-09-10 06:30:00', '2025-09-10 13:00:00', 640.00),
(1133, 4, '2025-09-10 14:30:00', '2025-09-10 21:00:00', 640.00),
(1134, 3, '2025-09-11 05:30:00', '2025-09-11 12:00:00', 580.00),
(1135, 3, '2025-09-11 13:30:00', '2025-09-11 20:00:00', 580.00),
(1136, 4, '2025-09-11 06:30:00', '2025-09-11 13:00:00', 640.00),
(1137, 4, '2025-09-11 14:30:00', '2025-09-11 21:00:00', 640.00),
(1138, 3, '2025-09-12 05:30:00', '2025-09-12 12:00:00', 580.00),
(1139, 3, '2025-09-12 13:30:00', '2025-09-12 20:00:00', 580.00),
(1140, 4, '2025-09-12 06:30:00', '2025-09-12 13:00:00', 640.00),
(1141, 4, '2025-09-12 14:30:00', '2025-09-12 21:00:00', 640.00),
(1142, 3, '2025-09-13 05:30:00', '2025-09-13 12:00:00', 580.00),
(1143, 3, '2025-09-13 13:30:00', '2025-09-13 20:00:00', 580.00),
(1144, 4, '2025-09-13 06:30:00', '2025-09-13 13:00:00', 640.00),
(1145, 4, '2025-09-13 14:30:00', '2025-09-13 21:00:00', 640.00),
(1146, 3, '2025-09-14 06:00:00', '2025-09-14 12:30:00', 580.00),
(1147, 3, '2025-09-14 14:00:00', '2025-09-14 20:30:00', 580.00),
(1148, 4, '2025-09-14 07:00:00', '2025-09-14 13:30:00', 640.00),
(1149, 4, '2025-09-14 15:00:00', '2025-09-14 21:30:00', 640.00),
(1150, 3, '2025-09-15 05:30:00', '2025-09-15 12:00:00', 580.00),
(1151, 3, '2025-09-15 13:30:00', '2025-09-15 20:00:00', 580.00),
(1152, 4, '2025-09-15 06:30:00', '2025-09-15 13:00:00', 640.00),
(1153, 4, '2025-09-15 14:30:00', '2025-09-15 21:00:00', 640.00),
(1154, 3, '2025-09-16 05:30:00', '2025-09-16 12:00:00', 580.00),
(1155, 3, '2025-09-16 13:30:00', '2025-09-16 20:00:00', 580.00),
(1156, 4, '2025-09-16 06:30:00', '2025-09-16 13:00:00', 640.00),
(1157, 4, '2025-09-16 14:30:00', '2025-09-16 21:00:00', 640.00),
(1158, 3, '2025-09-17 05:30:00', '2025-09-17 12:00:00', 580.00),
(1159, 3, '2025-09-17 13:30:00', '2025-09-17 20:00:00', 580.00),
(1160, 4, '2025-09-17 06:30:00', '2025-09-17 13:00:00', 640.00),
(1161, 4, '2025-09-17 14:30:00', '2025-09-17 21:00:00', 640.00),
(1162, 3, '2025-09-18 05:30:00', '2025-09-18 12:00:00', 580.00),
(1163, 3, '2025-09-18 13:30:00', '2025-09-18 20:00:00', 580.00),
(1164, 4, '2025-09-18 06:30:00', '2025-09-18 13:00:00', 640.00),
(1165, 4, '2025-09-18 14:30:00', '2025-09-18 21:00:00', 640.00),
(1166, 3, '2025-09-19 05:30:00', '2025-09-19 12:00:00', 580.00),
(1167, 3, '2025-09-19 13:30:00', '2025-09-19 20:00:00', 580.00),
(1168, 4, '2025-09-19 06:30:00', '2025-09-19 13:00:00', 640.00),
(1169, 4, '2025-09-19 14:30:00', '2025-09-19 21:00:00', 640.00),
(1170, 3, '2025-09-20 05:30:00', '2025-09-20 12:00:00', 580.00),
(1171, 3, '2025-09-20 13:30:00', '2025-09-20 20:00:00', 580.00),
(1172, 4, '2025-09-20 06:30:00', '2025-09-20 13:00:00', 640.00),
(1173, 4, '2025-09-20 14:30:00', '2025-09-20 21:00:00', 640.00),
(1174, 3, '2025-09-21 06:00:00', '2025-09-21 12:30:00', 580.00),
(1175, 3, '2025-09-21 14:00:00', '2025-09-21 20:30:00', 580.00),
(1176, 4, '2025-09-21 07:00:00', '2025-09-21 13:30:00', 640.00),
(1177, 4, '2025-09-21 15:00:00', '2025-09-21 21:30:00', 640.00),
(1178, 3, '2025-09-22 05:30:00', '2025-09-22 12:00:00', 580.00),
(1179, 3, '2025-09-22 13:30:00', '2025-09-22 20:00:00', 580.00),
(1180, 4, '2025-09-22 06:30:00', '2025-09-22 13:00:00', 640.00),
(1181, 4, '2025-09-22 14:30:00', '2025-09-22 21:00:00', 640.00),
(1182, 3, '2025-09-23 05:30:00', '2025-09-23 12:00:00', 580.00),
(1183, 3, '2025-09-23 13:30:00', '2025-09-23 20:00:00', 580.00),
(1184, 4, '2025-09-23 06:30:00', '2025-09-23 13:00:00', 640.00),
(1185, 4, '2025-09-23 14:30:00', '2025-09-23 21:00:00', 640.00),
(1186, 3, '2025-09-24 05:30:00', '2025-09-24 12:00:00', 580.00),
(1187, 3, '2025-09-24 13:30:00', '2025-09-24 20:00:00', 580.00),
(1188, 4, '2025-09-24 06:30:00', '2025-09-24 13:00:00', 640.00),
(1189, 4, '2025-09-24 14:30:00', '2025-09-24 21:00:00', 640.00),
(1190, 3, '2025-09-25 05:30:00', '2025-09-25 12:00:00', 580.00),
(1191, 3, '2025-09-25 13:30:00', '2025-09-25 20:00:00', 580.00),
(1192, 4, '2025-09-25 06:30:00', '2025-09-25 13:00:00', 640.00),
(1193, 4, '2025-09-25 14:30:00', '2025-09-25 21:00:00', 640.00),
(1194, 3, '2025-09-26 05:30:00', '2025-09-26 12:00:00', 580.00),
(1195, 3, '2025-09-26 13:30:00', '2025-09-26 20:00:00', 580.00),
(1196, 4, '2025-09-26 06:30:00', '2025-09-26 13:00:00', 640.00),
(1197, 4, '2025-09-26 14:30:00', '2025-09-26 21:00:00', 640.00),
(1198, 3, '2025-09-27 05:30:00', '2025-09-27 12:00:00', 580.00),
(1199, 3, '2025-09-27 13:30:00', '2025-09-27 20:00:00', 580.00),
(1200, 4, '2025-09-27 06:30:00', '2025-09-27 13:00:00', 640.00),
(1201, 4, '2025-09-27 14:30:00', '2025-09-27 21:00:00', 640.00),
(1202, 3, '2025-09-28 06:00:00', '2025-09-28 12:30:00', 580.00),
(1203, 3, '2025-09-28 14:00:00', '2025-09-28 20:30:00', 580.00),
(1204, 4, '2025-09-28 07:00:00', '2025-09-28 13:30:00', 640.00),
(1205, 4, '2025-09-28 15:00:00', '2025-09-28 21:30:00', 640.00),
(1206, 3, '2025-09-29 05:30:00', '2025-09-29 12:00:00', 580.00),
(1207, 3, '2025-09-29 13:30:00', '2025-09-29 20:00:00', 580.00),
(1208, 4, '2025-09-29 06:30:00', '2025-09-29 13:00:00', 640.00),
(1209, 4, '2025-09-29 14:30:00', '2025-09-29 21:00:00', 640.00),
(1210, 3, '2025-09-30 05:30:00', '2025-09-30 12:00:00', 580.00),
(1211, 3, '2025-09-30 13:30:00', '2025-09-30 20:00:00', 580.00),
(1212, 4, '2025-09-30 06:30:00', '2025-09-30 13:00:00', 640.00),
(1213, 4, '2025-09-30 14:30:00', '2025-09-30 21:00:00', 640.00),
(1214, 1, '2025-10-01 05:30:00', '2025-10-01 12:00:00', 580.00),
(1215, 1, '2025-10-02 05:30:00', '2025-10-02 12:00:00', 580.00),
(1216, 1, '2025-10-03 05:30:00', '2025-10-03 12:00:00', 580.00),
(1217, 1, '2025-10-04 06:00:00', '2025-10-04 12:30:00', 580.00),
(1218, 1, '2025-10-05 06:00:00', '2025-10-05 12:30:00', 580.00),
(1219, 1, '2025-10-06 05:30:00', '2025-10-06 12:00:00', 580.00),
(1220, 1, '2025-10-07 05:30:00', '2025-10-07 12:00:00', 580.00),
(1221, 1, '2025-10-01 13:30:00', '2025-10-01 20:00:00', 580.00),
(1222, 1, '2025-10-02 13:30:00', '2025-10-02 20:00:00', 580.00),
(1223, 1, '2025-10-03 13:30:00', '2025-10-03 20:00:00', 580.00),
(1224, 1, '2025-10-04 14:00:00', '2025-10-04 20:30:00', 580.00),
(1225, 1, '2025-10-05 14:00:00', '2025-10-05 20:30:00', 580.00),
(1226, 1, '2025-10-06 13:30:00', '2025-10-06 20:00:00', 580.00),
(1227, 1, '2025-10-07 13:30:00', '2025-10-07 20:00:00', 580.00),
(1228, 1, '2025-10-08 05:30:00', '2025-10-08 12:00:00', 580.00),
(1229, 1, '2025-10-09 05:30:00', '2025-10-09 12:00:00', 580.00),
(1230, 1, '2025-10-10 05:30:00', '2025-10-10 12:00:00', 580.00),
(1231, 1, '2025-10-11 06:00:00', '2025-10-11 12:30:00', 580.00),
(1232, 1, '2025-10-12 06:00:00', '2025-10-12 12:30:00', 580.00),
(1233, 1, '2025-10-13 05:30:00', '2025-10-13 12:00:00', 580.00),
(1234, 1, '2025-10-14 05:30:00', '2025-10-14 12:00:00', 580.00),
(1235, 1, '2025-10-08 13:30:00', '2025-10-08 20:00:00', 580.00),
(1236, 1, '2025-10-09 13:30:00', '2025-10-09 20:00:00', 580.00),
(1237, 1, '2025-10-10 13:30:00', '2025-10-10 20:00:00', 580.00),
(1238, 1, '2025-10-11 14:00:00', '2025-10-11 20:30:00', 580.00),
(1239, 1, '2025-10-12 14:00:00', '2025-10-12 20:30:00', 580.00),
(1240, 1, '2025-10-13 13:30:00', '2025-10-13 20:00:00', 580.00),
(1241, 1, '2025-10-14 13:30:00', '2025-10-14 20:00:00', 580.00),
(1242, 1, '2025-10-15 05:30:00', '2025-10-15 12:00:00', 580.00),
(1243, 1, '2025-10-16 05:30:00', '2025-10-16 12:00:00', 580.00),
(1244, 1, '2025-10-17 05:30:00', '2025-10-17 12:00:00', 580.00),
(1245, 1, '2025-10-18 06:00:00', '2025-10-18 12:30:00', 580.00),
(1246, 1, '2025-10-19 06:00:00', '2025-10-19 12:30:00', 580.00),
(1247, 1, '2025-10-20 05:30:00', '2025-10-20 12:00:00', 580.00),
(1248, 1, '2025-10-21 05:30:00', '2025-10-21 12:00:00', 580.00),
(1249, 1, '2025-10-15 13:30:00', '2025-10-15 20:00:00', 580.00),
(1250, 1, '2025-10-16 13:30:00', '2025-10-16 20:00:00', 580.00),
(1251, 1, '2025-10-17 13:30:00', '2025-10-17 20:00:00', 580.00),
(1252, 1, '2025-10-18 14:00:00', '2025-10-18 20:30:00', 580.00),
(1253, 1, '2025-10-19 14:00:00', '2025-10-19 20:30:00', 580.00),
(1254, 1, '2025-10-20 13:30:00', '2025-10-20 20:00:00', 580.00),
(1255, 1, '2025-10-21 13:30:00', '2025-10-21 20:00:00', 580.00),
(1256, 1, '2025-10-22 05:30:00', '2025-10-22 12:00:00', 580.00),
(1257, 1, '2025-10-23 05:30:00', '2025-10-23 12:00:00', 580.00),
(1258, 1, '2025-10-24 05:30:00', '2025-10-24 12:00:00', 580.00),
(1259, 1, '2025-10-25 06:00:00', '2025-10-25 12:30:00', 580.00),
(1260, 1, '2025-10-26 06:00:00', '2025-10-26 12:30:00', 580.00),
(1261, 1, '2025-10-27 05:30:00', '2025-10-27 12:00:00', 580.00),
(1262, 1, '2025-10-28 05:30:00', '2025-10-28 12:00:00', 580.00),
(1263, 1, '2025-10-22 13:30:00', '2025-10-22 20:00:00', 580.00),
(1264, 1, '2025-10-23 13:30:00', '2025-10-23 20:00:00', 580.00),
(1265, 1, '2025-10-24 13:30:00', '2025-10-24 20:00:00', 580.00),
(1266, 1, '2025-10-25 14:00:00', '2025-10-25 20:30:00', 580.00),
(1267, 1, '2025-10-26 14:00:00', '2025-10-26 20:30:00', 580.00),
(1268, 1, '2025-10-27 13:30:00', '2025-10-27 20:00:00', 580.00),
(1269, 1, '2025-10-28 13:30:00', '2025-10-28 20:00:00', 580.00),
(1270, 1, '2025-10-29 05:30:00', '2025-10-29 12:00:00', 580.00),
(1271, 1, '2025-10-30 05:30:00', '2025-10-30 12:00:00', 580.00),
(1272, 1, '2025-10-31 05:30:00', '2025-10-31 12:00:00', 580.00),
(1273, 1, '2025-10-29 13:30:00', '2025-10-29 20:00:00', 580.00),
(1274, 1, '2025-10-30 13:30:00', '2025-10-30 20:00:00', 580.00),
(1275, 1, '2025-10-31 13:30:00', '2025-10-31 20:00:00', 580.00),
(1276, 2, '2025-10-01 06:30:00', '2025-10-01 13:00:00', 640.00),
(1277, 2, '2025-10-02 06:30:00', '2025-10-02 13:00:00', 640.00),
(1278, 2, '2025-10-03 06:30:00', '2025-10-03 13:00:00', 640.00),
(1279, 2, '2025-10-04 07:00:00', '2025-10-04 13:30:00', 640.00),
(1280, 2, '2025-10-05 07:00:00', '2025-10-05 13:30:00', 640.00),
(1281, 2, '2025-10-06 06:30:00', '2025-10-06 13:00:00', 640.00),
(1282, 2, '2025-10-07 06:30:00', '2025-10-07 13:00:00', 640.00),
(1283, 2, '2025-10-01 14:30:00', '2025-10-01 21:00:00', 640.00),
(1284, 2, '2025-10-02 14:30:00', '2025-10-02 21:00:00', 640.00),
(1285, 2, '2025-10-03 14:30:00', '2025-10-03 21:00:00', 640.00),
(1286, 2, '2025-10-04 15:00:00', '2025-10-04 21:30:00', 640.00),
(1287, 2, '2025-10-05 15:00:00', '2025-10-05 21:30:00', 640.00),
(1288, 2, '2025-10-06 14:30:00', '2025-10-06 21:00:00', 640.00),
(1289, 2, '2025-10-07 14:30:00', '2025-10-07 21:00:00', 640.00),
(1290, 2, '2025-10-08 06:30:00', '2025-10-08 13:00:00', 640.00),
(1291, 2, '2025-10-09 06:30:00', '2025-10-09 13:00:00', 640.00),
(1292, 2, '2025-10-10 06:30:00', '2025-10-10 13:00:00', 640.00),
(1293, 2, '2025-10-11 07:00:00', '2025-10-11 13:30:00', 640.00),
(1294, 2, '2025-10-12 07:00:00', '2025-10-12 13:30:00', 640.00),
(1295, 2, '2025-10-13 06:30:00', '2025-10-13 13:00:00', 640.00),
(1296, 2, '2025-10-14 06:30:00', '2025-10-14 13:00:00', 640.00),
(1297, 2, '2025-10-08 14:30:00', '2025-10-08 21:00:00', 640.00),
(1298, 2, '2025-10-09 14:30:00', '2025-10-09 21:00:00', 640.00),
(1299, 2, '2025-10-10 14:30:00', '2025-10-10 21:00:00', 640.00),
(1300, 2, '2025-10-11 15:00:00', '2025-10-11 21:30:00', 640.00),
(1301, 2, '2025-10-12 15:00:00', '2025-10-12 21:30:00', 640.00),
(1302, 2, '2025-10-13 14:30:00', '2025-10-13 21:00:00', 640.00),
(1303, 2, '2025-10-14 14:30:00', '2025-10-14 21:00:00', 640.00),
(1304, 2, '2025-10-15 06:30:00', '2025-10-15 13:00:00', 640.00),
(1305, 2, '2025-10-16 06:30:00', '2025-10-16 13:00:00', 640.00),
(1306, 2, '2025-10-17 06:30:00', '2025-10-17 13:00:00', 640.00),
(1307, 2, '2025-10-18 07:00:00', '2025-10-18 13:30:00', 640.00),
(1308, 2, '2025-10-19 07:00:00', '2025-10-19 13:30:00', 640.00),
(1309, 2, '2025-10-20 06:30:00', '2025-10-20 13:00:00', 640.00),
(1310, 2, '2025-10-21 06:30:00', '2025-10-21 13:00:00', 640.00),
(1311, 2, '2025-10-15 14:30:00', '2025-10-15 21:00:00', 640.00),
(1312, 2, '2025-10-16 14:30:00', '2025-10-16 21:00:00', 640.00),
(1313, 2, '2025-10-17 14:30:00', '2025-10-17 21:00:00', 640.00),
(1314, 2, '2025-10-18 15:00:00', '2025-10-18 21:30:00', 640.00),
(1315, 2, '2025-10-19 15:00:00', '2025-10-19 21:30:00', 640.00),
(1316, 2, '2025-10-20 14:30:00', '2025-10-20 21:00:00', 640.00),
(1317, 2, '2025-10-21 14:30:00', '2025-10-21 21:00:00', 640.00),
(1318, 2, '2025-10-22 06:30:00', '2025-10-22 13:00:00', 640.00),
(1319, 2, '2025-10-23 06:30:00', '2025-10-23 13:00:00', 640.00),
(1320, 2, '2025-10-24 06:30:00', '2025-10-24 13:00:00', 640.00),
(1321, 2, '2025-10-25 07:00:00', '2025-10-25 13:30:00', 640.00),
(1322, 2, '2025-10-26 07:00:00', '2025-10-26 13:30:00', 640.00),
(1323, 2, '2025-10-27 06:30:00', '2025-10-27 13:00:00', 640.00),
(1324, 2, '2025-10-28 06:30:00', '2025-10-28 13:00:00', 640.00),
(1325, 2, '2025-10-22 14:30:00', '2025-10-22 21:00:00', 640.00),
(1326, 2, '2025-10-23 14:30:00', '2025-10-23 21:00:00', 640.00),
(1327, 2, '2025-10-24 14:30:00', '2025-10-24 21:00:00', 640.00),
(1328, 2, '2025-10-25 15:00:00', '2025-10-25 21:30:00', 640.00),
(1329, 2, '2025-10-26 15:00:00', '2025-10-26 21:30:00', 640.00),
(1330, 2, '2025-10-27 14:30:00', '2025-10-27 21:00:00', 640.00),
(1331, 2, '2025-10-28 14:30:00', '2025-10-28 21:00:00', 640.00),
(1332, 2, '2025-10-29 06:30:00', '2025-10-29 13:00:00', 640.00),
(1333, 2, '2025-10-30 06:30:00', '2025-10-30 13:00:00', 640.00),
(1334, 2, '2025-10-31 06:30:00', '2025-10-31 13:00:00', 640.00),
(1335, 2, '2025-10-29 14:30:00', '2025-10-29 21:00:00', 640.00),
(1336, 2, '2025-10-30 14:30:00', '2025-10-30 21:00:00', 640.00),
(1337, 2, '2025-10-31 14:30:00', '2025-10-31 21:00:00', 640.00),
(1338, 3, '2025-10-01 05:30:00', '2025-10-01 12:00:00', 580.00),
(1339, 3, '2025-10-02 05:30:00', '2025-10-02 12:00:00', 580.00),
(1340, 3, '2025-10-03 05:30:00', '2025-10-03 12:00:00', 580.00),
(1341, 3, '2025-10-04 06:00:00', '2025-10-04 12:30:00', 580.00),
(1342, 3, '2025-10-05 06:00:00', '2025-10-05 12:30:00', 580.00),
(1343, 3, '2025-10-06 05:30:00', '2025-10-06 12:00:00', 580.00),
(1344, 3, '2025-10-07 05:30:00', '2025-10-07 12:00:00', 580.00),
(1345, 3, '2025-10-01 13:30:00', '2025-10-01 20:00:00', 580.00),
(1346, 3, '2025-10-02 13:30:00', '2025-10-02 20:00:00', 580.00),
(1347, 3, '2025-10-03 13:30:00', '2025-10-03 20:00:00', 580.00),
(1348, 3, '2025-10-04 14:00:00', '2025-10-04 20:30:00', 580.00),
(1349, 3, '2025-10-05 14:00:00', '2025-10-05 20:30:00', 580.00),
(1350, 3, '2025-10-06 13:30:00', '2025-10-06 20:00:00', 580.00),
(1351, 3, '2025-10-07 13:30:00', '2025-10-07 20:00:00', 580.00),
(1352, 3, '2025-10-08 05:30:00', '2025-10-08 12:00:00', 580.00),
(1353, 3, '2025-10-09 05:30:00', '2025-10-09 12:00:00', 580.00),
(1354, 3, '2025-10-10 05:30:00', '2025-10-10 12:00:00', 580.00),
(1355, 3, '2025-10-11 06:00:00', '2025-10-11 12:30:00', 580.00),
(1356, 3, '2025-10-12 06:00:00', '2025-10-12 12:30:00', 580.00),
(1357, 3, '2025-10-13 05:30:00', '2025-10-13 12:00:00', 580.00),
(1358, 3, '2025-10-14 05:30:00', '2025-10-14 12:00:00', 580.00),
(1359, 3, '2025-10-08 13:30:00', '2025-10-08 20:00:00', 580.00),
(1360, 3, '2025-10-09 13:30:00', '2025-10-09 20:00:00', 580.00),
(1361, 3, '2025-10-10 13:30:00', '2025-10-10 20:00:00', 580.00),
(1362, 3, '2025-10-11 14:00:00', '2025-10-11 20:30:00', 580.00),
(1363, 3, '2025-10-12 14:00:00', '2025-10-12 20:30:00', 580.00),
(1364, 3, '2025-10-13 13:30:00', '2025-10-13 20:00:00', 580.00),
(1365, 3, '2025-10-14 13:30:00', '2025-10-14 20:00:00', 580.00),
(1366, 3, '2025-10-15 05:30:00', '2025-10-15 12:00:00', 580.00),
(1367, 3, '2025-10-16 05:30:00', '2025-10-16 12:00:00', 580.00),
(1368, 3, '2025-10-17 05:30:00', '2025-10-17 12:00:00', 580.00),
(1369, 3, '2025-10-18 06:00:00', '2025-10-18 12:30:00', 580.00),
(1370, 3, '2025-10-19 06:00:00', '2025-10-19 12:30:00', 580.00),
(1371, 3, '2025-10-20 05:30:00', '2025-10-20 12:00:00', 580.00),
(1372, 3, '2025-10-21 05:30:00', '2025-10-21 12:00:00', 580.00),
(1373, 3, '2025-10-15 13:30:00', '2025-10-15 20:00:00', 580.00),
(1374, 3, '2025-10-16 13:30:00', '2025-10-16 20:00:00', 580.00),
(1375, 3, '2025-10-17 13:30:00', '2025-10-17 20:00:00', 580.00),
(1376, 3, '2025-10-18 14:00:00', '2025-10-18 20:30:00', 580.00),
(1377, 3, '2025-10-19 14:00:00', '2025-10-19 20:30:00', 580.00),
(1378, 3, '2025-10-20 13:30:00', '2025-10-20 20:00:00', 580.00),
(1379, 3, '2025-10-21 13:30:00', '2025-10-21 20:00:00', 580.00),
(1380, 3, '2025-10-22 05:30:00', '2025-10-22 12:00:00', 580.00),
(1381, 3, '2025-10-23 05:30:00', '2025-10-23 12:00:00', 580.00),
(1382, 3, '2025-10-24 05:30:00', '2025-10-24 12:00:00', 580.00),
(1383, 3, '2025-10-25 06:00:00', '2025-10-25 12:30:00', 580.00),
(1384, 3, '2025-10-26 06:00:00', '2025-10-26 12:30:00', 580.00),
(1385, 3, '2025-10-27 05:30:00', '2025-10-27 12:00:00', 580.00),
(1386, 3, '2025-10-28 05:30:00', '2025-10-28 12:00:00', 580.00),
(1387, 3, '2025-10-22 13:30:00', '2025-10-22 20:00:00', 580.00),
(1388, 3, '2025-10-23 13:30:00', '2025-10-23 20:00:00', 580.00),
(1389, 3, '2025-10-24 13:30:00', '2025-10-24 20:00:00', 580.00),
(1390, 3, '2025-10-25 14:00:00', '2025-10-25 20:30:00', 580.00),
(1391, 3, '2025-10-26 14:00:00', '2025-10-26 20:30:00', 580.00),
(1392, 3, '2025-10-27 13:30:00', '2025-10-27 20:00:00', 580.00),
(1393, 3, '2025-10-28 13:30:00', '2025-10-28 20:00:00', 580.00),
(1394, 3, '2025-10-29 05:30:00', '2025-10-29 12:00:00', 580.00),
(1395, 3, '2025-10-30 05:30:00', '2025-10-30 12:00:00', 580.00),
(1396, 3, '2025-10-31 05:30:00', '2025-10-31 12:00:00', 580.00),
(1397, 3, '2025-10-29 13:30:00', '2025-10-29 20:00:00', 580.00),
(1398, 3, '2025-10-30 13:30:00', '2025-10-30 20:00:00', 580.00),
(1399, 3, '2025-10-31 13:30:00', '2025-10-31 20:00:00', 580.00),
(1400, 4, '2025-10-01 06:30:00', '2025-10-01 13:00:00', 640.00),
(1401, 4, '2025-10-02 06:30:00', '2025-10-02 13:00:00', 640.00),
(1402, 4, '2025-10-03 06:30:00', '2025-10-03 13:00:00', 640.00),
(1403, 4, '2025-10-04 07:00:00', '2025-10-04 13:30:00', 640.00),
(1404, 4, '2025-10-05 07:00:00', '2025-10-05 13:30:00', 640.00),
(1405, 4, '2025-10-06 06:30:00', '2025-10-06 13:00:00', 640.00),
(1406, 4, '2025-10-07 06:30:00', '2025-10-07 13:00:00', 640.00),
(1407, 4, '2025-10-01 14:30:00', '2025-10-01 21:00:00', 640.00),
(1408, 4, '2025-10-02 14:30:00', '2025-10-02 21:00:00', 640.00),
(1409, 4, '2025-10-03 14:30:00', '2025-10-03 21:00:00', 640.00),
(1410, 4, '2025-10-04 15:00:00', '2025-10-04 21:30:00', 640.00),
(1411, 4, '2025-10-05 15:00:00', '2025-10-05 21:30:00', 640.00),
(1412, 4, '2025-10-06 14:30:00', '2025-10-06 21:00:00', 640.00),
(1413, 4, '2025-10-07 14:30:00', '2025-10-07 21:00:00', 640.00),
(1414, 4, '2025-10-08 06:30:00', '2025-10-08 13:00:00', 640.00),
(1415, 4, '2025-10-09 06:30:00', '2025-10-09 13:00:00', 640.00),
(1416, 4, '2025-10-10 06:30:00', '2025-10-10 13:00:00', 640.00),
(1417, 4, '2025-10-11 07:00:00', '2025-10-11 13:30:00', 640.00),
(1418, 4, '2025-10-12 07:00:00', '2025-10-12 13:30:00', 640.00),
(1419, 4, '2025-10-13 06:30:00', '2025-10-13 13:00:00', 640.00),
(1420, 4, '2025-10-14 06:30:00', '2025-10-14 13:00:00', 640.00),
(1421, 4, '2025-10-08 14:30:00', '2025-10-08 21:00:00', 640.00),
(1422, 4, '2025-10-09 14:30:00', '2025-10-09 21:00:00', 640.00),
(1423, 4, '2025-10-10 14:30:00', '2025-10-10 21:00:00', 640.00),
(1424, 4, '2025-10-11 15:00:00', '2025-10-11 21:30:00', 640.00),
(1425, 4, '2025-10-12 15:00:00', '2025-10-12 21:30:00', 640.00),
(1426, 4, '2025-10-13 14:30:00', '2025-10-13 21:00:00', 640.00),
(1427, 4, '2025-10-14 14:30:00', '2025-10-14 21:00:00', 640.00),
(1428, 4, '2025-10-15 06:30:00', '2025-10-15 13:00:00', 640.00),
(1429, 4, '2025-10-16 06:30:00', '2025-10-16 13:00:00', 640.00),
(1430, 4, '2025-10-17 06:30:00', '2025-10-17 13:00:00', 640.00),
(1431, 4, '2025-10-18 07:00:00', '2025-10-18 13:30:00', 640.00),
(1432, 4, '2025-10-19 07:00:00', '2025-10-19 13:30:00', 640.00),
(1433, 4, '2025-10-20 06:30:00', '2025-10-20 13:00:00', 640.00),
(1434, 4, '2025-10-21 06:30:00', '2025-10-21 13:00:00', 640.00),
(1435, 4, '2025-10-15 14:30:00', '2025-10-15 21:00:00', 640.00),
(1436, 4, '2025-10-16 14:30:00', '2025-10-16 21:00:00', 640.00),
(1437, 4, '2025-10-17 14:30:00', '2025-10-17 21:00:00', 640.00),
(1438, 4, '2025-10-18 15:00:00', '2025-10-18 21:30:00', 640.00),
(1439, 4, '2025-10-19 15:00:00', '2025-10-19 21:30:00', 640.00),
(1440, 4, '2025-10-20 14:30:00', '2025-10-20 21:00:00', 640.00),
(1441, 4, '2025-10-21 14:30:00', '2025-10-21 21:00:00', 640.00),
(1442, 4, '2025-10-22 06:30:00', '2025-10-22 13:00:00', 640.00),
(1443, 4, '2025-10-23 06:30:00', '2025-10-23 13:00:00', 640.00),
(1444, 4, '2025-10-24 06:30:00', '2025-10-24 13:00:00', 640.00),
(1445, 4, '2025-10-25 07:00:00', '2025-10-25 13:30:00', 640.00),
(1446, 4, '2025-10-26 07:00:00', '2025-10-26 13:30:00', 640.00),
(1447, 4, '2025-10-27 06:30:00', '2025-10-27 13:00:00', 640.00),
(1448, 4, '2025-10-28 06:30:00', '2025-10-28 13:00:00', 640.00),
(1449, 4, '2025-10-22 14:30:00', '2025-10-22 21:00:00', 640.00),
(1450, 4, '2025-10-23 14:30:00', '2025-10-23 21:00:00', 640.00),
(1451, 4, '2025-10-24 14:30:00', '2025-10-24 21:00:00', 640.00),
(1452, 4, '2025-10-25 15:00:00', '2025-10-25 21:30:00', 640.00),
(1453, 4, '2025-10-26 15:00:00', '2025-10-26 21:30:00', 640.00),
(1454, 4, '2025-10-27 14:30:00', '2025-10-27 21:00:00', 640.00),
(1455, 4, '2025-10-28 14:30:00', '2025-10-28 21:00:00', 640.00),
(1456, 4, '2025-10-29 06:30:00', '2025-10-29 13:00:00', 640.00),
(1457, 4, '2025-10-30 06:30:00', '2025-10-30 13:00:00', 640.00),
(1458, 4, '2025-10-31 06:30:00', '2025-10-31 13:00:00', 640.00),
(1459, 4, '2025-10-29 14:30:00', '2025-10-29 21:00:00', 640.00),
(1460, 4, '2025-10-30 14:30:00', '2025-10-30 21:00:00', 640.00),
(1461, 4, '2025-10-31 14:30:00', '2025-10-31 21:00:00', 640.00);

-- --------------------------------------------------------

--
-- Table structure for table `Seat`
--

CREATE TABLE `Seat` (
  `ID` int(11) NOT NULL,
  `BusID` int(11) DEFAULT NULL,
  `SeatNumber` varchar(6) NOT NULL,
  `Status` enum('available','booked') DEFAULT 'available',
  `GenderPreference` enum('male','female','other') NOT NULL,
  `IsLadySeat` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `Seat`
--

INSERT INTO `Seat` (`ID`, `BusID`, `SeatNumber`, `Status`, `GenderPreference`, `IsLadySeat`) VALUES
(1, 1, 'A1', 'available', 'male', 0),
(2, 1, 'A2', 'available', 'female', 1),
(3, 1, 'A3', 'available', 'male', 0),
(4, 1, 'A4', 'available', 'female', 1),
(5, 1, 'B1', 'available', 'male', 0),
(6, 1, 'B2', 'available', 'female', 1),
(7, 1, 'B3', 'available', 'male', 0),
(8, 1, 'B4', 'available', 'female', 1),
(9, 1, 'C1', 'available', 'other', 0),
(10, 1, 'C2', 'available', 'other', 0),
(11, 1, 'C3', 'available', 'other', 0),
(12, 1, 'C4', 'available', 'other', 0),
(13, 1, 'D1', 'available', 'male', 0),
(14, 1, 'D2', 'available', 'female', 1),
(15, 1, 'D3', 'available', 'male', 0),
(16, 1, 'D4', 'available', 'female', 1),
(17, 1, 'E1', 'available', 'male', 0),
(18, 1, 'E2', 'available', 'female', 1),
(19, 1, 'E3', 'available', 'male', 0),
(20, 1, 'E4', 'available', 'female', 1),
(21, 2, 'A1', 'available', 'male', 0),
(22, 2, 'A2', 'available', 'female', 1),
(23, 2, 'A3', 'available', 'male', 0),
(24, 2, 'A4', 'available', 'female', 1),
(25, 2, 'B1', 'available', 'male', 0),
(26, 2, 'B2', 'available', 'female', 1),
(27, 2, 'B3', 'available', 'male', 0),
(28, 2, 'B4', 'available', 'female', 1),
(29, 2, 'C1', 'available', 'other', 0),
(30, 2, 'C2', 'available', 'other', 0),
(31, 2, 'C3', 'available', 'other', 0),
(32, 2, 'C4', 'available', 'other', 0),
(33, 2, 'D1', 'available', 'male', 0),
(34, 2, 'D2', 'available', 'female', 1),
(35, 2, 'D3', 'available', 'male', 0),
(36, 2, 'D4', 'available', 'female', 1),
(37, 2, 'E1', 'available', 'male', 0),
(38, 2, 'E2', 'available', 'female', 1),
(39, 2, 'E3', 'available', 'male', 0),
(40, 2, 'E4', 'available', 'female', 1),
(41, 2, 'F1', 'available', 'other', 0),
(42, 2, 'F2', 'available', 'other', 0);

-- --------------------------------------------------------

--
-- Table structure for table `User`
--

CREATE TABLE `User` (
  `ID` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Email` varchar(60) NOT NULL,
  `PasswordHash` varchar(255) NOT NULL,
  `PhoneNumber` varchar(10) NOT NULL,
  `Role` enum('administrator','guest user','registered user') NOT NULL DEFAULT 'registered user',
  `RegistrationDate` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `User`
--

INSERT INTO `User` (`ID`, `Name`, `Email`, `PasswordHash`, `PhoneNumber`, `Role`, `RegistrationDate`) VALUES
(1, 'Test user', 'user@user.com', '$2y$12$oJk0s2wywihq2yHL1GYIHusfszbwtw2Rdj6KtlnqFYJhDkN3xd0g6', '0712345678', 'registered user', '2025-07-29 22:01:02'),
(2, 'Mary Fernando', 'mary.fernando@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0772345678', 'registered user', '2025-07-29 22:01:02'),
(3, 'David Perera', 'david.perera@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0773456789', 'registered user', '2025-07-29 22:01:02'),
(4, 'Sarah Jayawardena', 'sarah.jay@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0774567890', 'registered user', '2025-07-29 22:01:02'),
(5, 'Guest User 1', 'guest1@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0775678901', 'guest user', '2025-07-29 22:01:02'),
(6, 'Admin User', 'admin.user@lankatransit.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0776789012', 'administrator', '2025-07-29 22:01:02'),
(7, 'ruvini kanchana', 'ruvinikanchana2021al@gmail.com', '$2y$10$ZPkljhKAI1mTi4gkWrqvTeNGyBA2iAO6ZXHE5zizYN5r3A0M8vAcK', '0000000000', 'registered user', '2025-07-30 18:27:16'),
(8, 'fgfgx', 'fgfgx@demo.com', '$2y$12$c.8LzUZea8MPUR33JSThHeR2MWx6ptISZxjzjta1jol1kFkbtRCYi', '5675676868', 'guest user', '2025-07-31 02:04:32'),
(9, 'dfved', 'dfved@demo.com', '$2y$10$ifIV5hNEUe84tJ9foKLysO7SIDyUaCglGGXXdeUHrSXb3dJyacZLy', '1234567890', 'guest user', '2025-07-31 05:24:00'),
(10, 'ytfy', 'ytfy@demo.com', '$2y$10$U/6xZuzsSrWnqSgjDqJSYO/rUtJ8hjiOik7C1IFMJqRgBIBolSnJq', '1234567890', 'guest user', '2025-07-31 05:25:38'),
(11, 'wrygfw', 'wrygfw@demo.com', '$2y$10$FP.nE3wCEi3RXBZnLZ6ivu9I4chVmu16GGg5cd3vFqohbmX/5wBEC', '1234567890', 'guest user', '2025-07-31 05:35:26'),
(12, 'testj', 'sjanani56@gmail.com', '$2y$10$xsfQslThAdP4.9whjNbfQ.5P/OiykAcwx9xOcX3/4nhx3vuxEbfea', '0000000000', 'registered user', '2025-07-31 05:40:13'),
(13, 'tftr6', 'tftr6@demo.com', '$2y$10$tejNsWdgdWq7B53oAlXkIeEyfwxNwQhDwfjQR4gXACA4ljv1hlWy6', '1234567890', 'guest user', '2025-07-31 06:50:49'),
(14, 'tftr6', 'tftr61@demo.com', '$2y$10$cXsrVdxZnioA.7zCw2d5l.fRewywuL5I4tu8VRgH4zihLYSYsiTLG', '1234567890', 'guest user', '2025-07-31 06:50:53'),
(15, 'ruvini', 'cst22029@std.uwu.ac.lk', '$2y$10$1Y8GzOZ7hlkBETiwwVfU8./kJ5mo.5FDe5fFDvgj711x0gc9fRbYq', '0000000000', 'registered user', '2025-07-31 06:53:25'),
(16, 'tuser', 'tuser@user.com', '$2y$10$1alq7lTy0E.c184St3PhKehhUUz7vYylOiLwYFBhx8m/w12ecafGu', '0000000000', 'registered user', '2025-08-08 10:49:38'),
(17, 'tuser', 'tuser@demo.com', '$2y$10$RKSkClg0x4V6Z8JqolMcGuO33Ro390t0qAOV6X0/V0abd7Ht5UtXq', '1234567890', 'guest user', '2025-08-08 10:51:44'),
(18, 'tuser', 'tuser1@demo.com', '$2y$10$9EGkPr.Suu88TFYQf2H.eOlfUBp6ArZrhEvW7XLG3WE1ztN0U.VSW', '1234567890', 'guest user', '2025-08-08 11:08:56'),
(19, 'user', 'user@demo.com', '$2y$10$9qQ8LCDWTtgZB.Fy3.fOPeOGp6THAd8UuWbikqJuFEhEfg3UDpWvy', '1234567890', 'guest user', '2025-08-13 06:52:55'),
(20, 'user', 'user1@demo.com', '$2y$10$yYgUctA65wGy.xY3ZXje/uRKyJvxmm21GcdoPZMqB3ilvJhYUZECW', '1234567890', 'guest user', '2025-08-13 07:01:30'),
(21, 'test', 'test@gmail.com', '$2y$12$i3AnJ6/QPMF1Wk3AnYedf.bt7lOBiaEP2XDVroqUQg0zYcg2uShF2', '0000000000', 'registered user', '2025-08-14 05:14:40'),
(22, 'testt', 'testt@gmail.com', '$2y$12$MSEMsyxvPS67L7y7sS53KuWmlTwy25ZX.D8CElk084sW5UTENdkra', '0768795467', 'registered user', '2025-08-14 05:18:56'),
(23, 'ruvini', 'ruvini@gmail.com', '$2y$12$/Yp2VHhqj2G9cTOMcborsO98oX14KHm2mhytEtRzQwTGqSGb8l2Bm', '0715675467', 'registered user', '2025-08-14 06:51:20'),
(24, 'Dasun Theekshana', 'test@demo.com', '$2y$12$zNbYothWCP1JHfMSKliUYeGTyv//Bg5j/6wTL1cjr0QyqBMVYJr7C', '0785248886', 'guest user', '2025-08-14 11:50:25'),
(25, 'Dasun Theekshana', 'dasuntheekshana12@gmail.com', '$2y$12$r5v0HVkalzPAYMr4zn5.b.6GI9VgrcNLlyW681pHc15Q/ytEhpjla', '0785440067', 'registered user', '2025-08-14 14:21:05'),
(26, 'Theekshana', 'dasuntheekshana.personal@gmail.com', '$2y$12$aTJMbQgslAsBv291o11nB.NrR/KeJvQ0NMFzsTq7PtIRzJN.wZFo2', '0716756785', 'registered user', '2025-08-14 14:56:20'),
(27, 'dsththt', 'dsththt@demo.com', '$2y$12$rwlSgvvlAHICEwNpyUHDreYPtsnvYH8SGe46kdJZzFuq1mfz0JgfW', '0731245634', 'guest user', '2025-08-17 02:59:21'),
(28, 'dtys', 'dtys@demo.com', '$2y$12$SDvYpAxDuNtm.BrseMYSTOEeANC.qSdak6oA/xnt30RufJZK.i.nG', '0777777777', 'guest user', '2025-08-17 03:00:15'),
(29, 'uthpala dewindhi', 'example@gmail.com', '$2y$10$R/bZdjVu/A9KD1tg4Fgk9ulAkg19LzW5pqcnqbBtQ5TOqhtxaptRm', '0212132324', 'registered user', '2025-08-18 16:31:34'),
(30, 'kgkugku', 'kgkugku@demo.com', '$2y$12$FFA.d3pEyolf8DDuQoTSKu8Qu1Jcn5l1mnF1hP50rVwtl7r9KI1Iq', '0712345678', 'guest user', '2025-08-19 14:18:55'),
(31, 'asd', 'asd@demo.com', '$2y$12$kqqDSgexT0ppzg9PqKL0cuETjipelkAqQHoOTCtvh22kCjJ/A88lS', '0712345678', 'guest user', '2025-09-04 09:13:18'),
(32, 'Test User', 'testuser@demo.com', '$2y$12$IR3Y2pjysD32yrTMHpHfre.Q2VOI6LCUMbJHjKtrCpapcRKwc6cpS', '0771234567', 'guest user', '2025-09-04 09:22:40'),
(33, 'John Doe', 'johndoe@demo.com', '$2y$12$/Hq2.lapO.LVPe7WhSblt.xAKqb8Lwfy72A0SuIoTi.F3w.rDCbTq', '0771234567', 'guest user', '2025-09-04 09:23:40'),
(34, 'asd', 'asd1@demo.com', '$2y$12$RHlgpRbQuF4H8/.PKqD6muyhcG0Hhn/1AWkeBo9KY8rtz.qFsaAE2', '0712345678', 'guest user', '2025-09-04 09:27:52'),
(35, 'Jane Smith', 'janesmith@demo.com', '$2y$12$ERmR34GQbllxZpH.sOB8GOuZreJhNGTzi2LSnYgFmMjVNBedYUMPa', '0777654321', 'guest user', '2025-09-04 09:35:20'),
(36, 'Reverted Test User', 'revertedtestuser@demo.com', '$2y$12$zOb9UEec5MIcxiJVBKWAluNjs9jO6Zw0Fz3ldpQZz6Iwte2ax.q1G', '0765432109', 'guest user', '2025-09-04 09:40:52'),
(37, 'Jane Smith', 'janesmith1@demo.com', '$2y$12$wQOGwXE4i.36KCQ9xlYJHuiTFwBtKzeP4ilWo5pp8fzNCWyH9udsK', '0771234568', 'guest user', '2025-09-04 09:49:51'),
(38, 'Sarah Wilson', 'sarahwilson@demo.com', '$2y$12$FebywXye2bga2vlhysNoUO6sAu2iNeK7XIFEj7C.b9ygxYD/eDGPK', '0771234570', 'guest user', '2025-09-04 09:55:46'),
(39, 'asdf', 'asdf@demo.com', '$2y$12$sd9OTtq0SmJWdgyeUA7VZuu9x/.nVtXiNsX7YskSsBQslCv69cOQ.', '0712345678', 'guest user', '2025-09-04 09:58:58'),
(40, 'fghg', 'fghg@demo.com', '$2y$12$nMMmbYWO11iUVMlxMwQGwuqTrFYiD1pCrvy5O6yuii1vOxtKPUlMi', '0712345678', 'guest user', '2025-09-04 10:10:47'),
(41, 'agdsa', 'agdsa@demo.com', '$2y$12$NJNShXhk.M4EvheyvVmhgO44tXg5eCuyEAXaI5SmCsPo7aAGKaitO', '0711111111', 'guest user', '2025-09-04 10:42:51'),
(42, 'asf', 'asf@demo.com', '$2y$12$sO6hIN27AA/qSVYbKbsGq.vwVcOzgbLR5gvBy7zQv.rDVGUJBxqd2', '0711111111', 'guest user', '2025-09-05 08:02:38'),
(43, 'TCYUTS', 'tcyuts@demo.com', '$2y$12$iSVAFmX8VYjfZhdhjxr.4uIzf03YXfRgJ/o6leYSbyxnyn0u5Id8G', '0711111111', 'guest user', '2025-09-05 08:04:17'),
(44, 'EWWEAG', 'ewweag@demo.com', '$2y$12$FfE5qNUATeSZRZd8N8uBU.S1GjuM7kF.DdOMocM7t.gfEXDQWXgre', '0711111111', 'guest user', '2025-09-05 08:08:54'),
(45, 'asf', 'asf1@demo.com', '$2y$12$/pLdhFrHT888T6iyClk1i.4ofWeGzSgulzy0iyRC1b.f/LvHZoJei', '0711111111', 'guest user', '2025-09-05 08:10:04'),
(46, 'dasvds', 'dasvds@demo.com', '$2y$12$53hYUMA2Kr85okqvbf6RIOGF3ElXVcmRZyebJaBk9irkgnpeKl6yi', '0711111111', 'guest user', '2025-09-05 10:11:41'),
(47, 'gsdfb', 'gsdfb@demo.com', '$2y$12$Rt4EfnKRuUvCNftI1KOmPOXd.MAMoxJ1JLWRsJ7wRXHm9rNXEs3ey', '0711111111', 'guest user', '2025-09-05 10:16:30'),
(48, 'test1', 'test11@gmail.com', '$2y$12$D7yl/zJj0FK58hcSM9Xktu5B./3c98W94pR7ZD0xamA2sfSuHjx5y', '0786567898', 'registered user', '2025-09-15 05:38:57'),
(49, 'test', 'test1@demo.com', '$2y$12$X1XE23ZTOGsNXcpLac1vOepVf9W9TK1KOmNIPHYY7HOp9fRypGDhe', '0785248886', 'guest user', '2025-09-15 17:47:48'),
(50, 'test', 'test2@demo.com', '$2y$12$B5iGelsIbMgCx7VgexHZKuDLKEnX9dM1cTeT7fOObIiCYP6..E8UW', '0785248886', 'guest user', '2025-09-15 17:54:21'),
(51, 'test driver', 'testdriver@demo.com', '$2y$12$NeuRcWYSOF5qDKnihlaZO.Nb3tcx/eCiOB.0jd1P3Ebjnpj55KNDi', '0785248886', 'guest user', '2025-09-15 17:55:33'),
(52, 'test', 'test3@demo.com', '$2y$12$EkRmhK8kLkXWoYIuztUayuzqpgBKCMvm81RztVbbwaFE8cPqmgU5.', '0785248886', 'guest user', '2025-09-15 17:58:06'),
(53, 'Dasun Theekshana', 'dasuntheekshana@demo.com', '$2y$12$PeKL8FxXmGKaK0PDr1sFzODhB40E770E8ILFPY7kUqXR4/f2fkfLS', '0785248886', 'guest user', '2025-09-15 18:32:29'),
(54, 'yfiyi', 'yfiyi@demo.com', '$2y$12$Wmbmuf8hX55z0wnNe2NE2uZq9wy/sRAycFUmyZk.CwrNdrqnnMy2W', '0712345678', 'guest user', '2025-09-18 14:41:04'),
(55, 'asfsa', 'asfsa@demo.com', '$2y$10$cvMgRJxRpvsiBW28bhaXuObDHz90.wbHICj2yR5hDIvQqB56Byi1S', '0705322596', 'guest user', '2025-09-24 01:02:26'),
(56, 'asfsa', 'asfsa1@demo.com', '$2y$10$7W3yP9I2/AQGfg7w4G/.0OqRGT9ycE05x4/gDeTnD88B22j2K841W', '0705322596', 'guest user', '2025-09-24 01:32:23'),
(57, 'user', 'user2@demo.com', '$2y$10$sOUdr5CtCKPH42cuCRx8CeZe114oltBRwtp3.gVWcjiT0egWwWXMu', '0712345678', 'guest user', '2025-09-24 02:20:22'),
(58, 'user', 'user3@demo.com', '$2y$10$wFFH75eYBpO7HHP1/pmixeDZkS/sSRDVRKhZedc9SZ2/LTECiLkU2', '0712345678', 'guest user', '2025-09-24 02:39:08'),
(59, 'user', 'user4@demo.com', '$2y$10$Xz56i7YE3BcaJm2GpDDA4OyyoYWr4Eq.gL85rBfdVhWhNlriO89jS', '0712345678', 'guest user', '2025-09-24 02:41:28'),
(60, 'user', 'user5@demo.com', '$2y$10$MZ5b74wTDuwfzUF3S708d.89nmn27rbryNouja565FpgWcYecOiN6', '0712345678', 'guest user', '2025-09-24 05:42:55'),
(61, 'user', 'user6@demo.com', '$2y$10$3PYMhVDnST6neNsXxhgrT.ZvNp4EDqEbaky3kf4R.58Tt//KPfP0i', '0712345678', 'guest user', '2025-09-24 05:44:58'),
(62, 'kasuni', 'kavishkanavodr@gmail.com', '$2y$10$QzalVvAUl/Ir2guHcz8vxumoVktX3FOT8teSOXmhZkzeG6Xp4fzRG', '0764974805', 'registered user', '2025-09-24 11:05:45');

-- --------------------------------------------------------

--
-- Table structure for table `User_2`
--

CREATE TABLE `User_2` (
  `user_id` int(11) NOT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `User_2`
--

INSERT INTO `User_2` (`user_id`, `reset_token`, `token_expiry`, `created_at`, `updated_at`) VALUES
(1, 'ff88cc695edc9fc13ce5f043cdb9fc33566340a861f20bf39440cef227d9e331', '2025-07-31 08:38:16', '2025-07-29 22:01:02', '2025-07-31 05:38:17'),
(2, NULL, NULL, '2025-07-29 22:01:02', '2025-07-29 22:01:02'),
(3, NULL, NULL, '2025-07-29 22:01:02', '2025-07-29 22:01:02'),
(4, NULL, NULL, '2025-07-29 22:01:02', '2025-07-29 22:01:02'),
(5, NULL, NULL, '2025-07-29 22:01:02', '2025-07-29 22:01:02'),
(6, NULL, NULL, '2025-07-29 22:01:02', '2025-07-29 22:01:02'),
(7, '00eb5004dd86f5e80f75bd54ab727bc974192ad244999ca572aee35bdfeeaad7', '2025-07-30 21:33:44', '2025-07-30 18:28:10', '2025-07-30 18:33:47'),
(12, '671a23bcb90129896b299bda72a95dd40dfd6ab2850da99d72b35eefd33ee934', '2025-07-31 08:40:39', '2025-07-31 05:40:41', '2025-07-31 05:40:41'),
(15, '80acfa96def4541de50f01a9fe9cb982c1f730ff8c023d1ad4f65bca3f17ae53', '2025-07-31 09:55:30', '2025-07-31 06:55:31', '2025-07-31 06:55:31'),
(21, '75d58150d237ec7988102ed78a7f3f29e65aaf8f051bed15b1bab94b87fcb2b7', '2025-08-14 15:16:32', '2025-08-14 14:16:32', '2025-08-14 14:16:32'),
(25, 'd9d15bfae9d5c69af384b9db2411bdbb220652341001584cec0c1cad91c565d2', '2025-08-15 07:17:13', '2025-08-15 06:14:32', '2025-08-15 06:17:14');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Admin`
--
ALTER TABLE `Admin`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indexes for table `Admin_2`
--
ALTER TABLE `Admin_2`
  ADD PRIMARY KEY (`admin_id`),
  ADD KEY `idx_admin2_created_at` (`created_at`);

--
-- Indexes for table `Announcements`
--
ALTER TABLE `Announcements`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `idx_announcements_created_at` (`created_at`);

--
-- Indexes for table `Booking`
--
ALTER TABLE `Booking`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `idx_booking_user` (`UserId`),
  ADD KEY `idx_booking_bus` (`BusID`),
  ADD KEY `idx_booking_phone` (`PhoneNumber`),
  ADD KEY `idx_booking_travel_date` (`TravelDate`),
  ADD KEY `idx_booking_origin_dest` (`Origin`,`Destination`);

--
-- Indexes for table `BookingCancellation`
--
ALTER TABLE `BookingCancellation`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `idx_booking_cancellation_booking` (`BookingID`),
  ADD KEY `idx_booking_cancellation_user` (`UserID`),
  ADD KEY `idx_booking_cancellation_status` (`Status`),
  ADD KEY `idx_booking_cancellation_requested_at` (`RequestedAt`),
  ADD KEY `idx_booking_cancellation_processed_by` (`ProcessedBy`);

--
-- Indexes for table `Booking_2`
--
ALTER TABLE `Booking_2`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `idx_booking2_gender` (`gender`),
  ADD KEY `idx_booking2_created_at` (`created_at`);

--
-- Indexes for table `Bus`
--
ALTER TABLE `Bus`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `BusNumber` (`BusNumber`),
  ADD KEY `RouteId` (`RouteId`),
  ADD KEY `AdminId` (`AdminId`),
  ADD KEY `idx_bus_capacity` (`Capacity`);

--
-- Indexes for table `Feedback`
--
ALTER TABLE `Feedback`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `UserId` (`UserId`),
  ADD KEY `BusId` (`BusId`),
  ADD KEY `idx_feedback_booking` (`BookingId`);

--
-- Indexes for table `Incident`
--
ALTER TABLE `Incident`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `UserId` (`UserId`),
  ADD KEY `AdminId` (`AdminId`),
  ADD KEY `BookingId` (`BookingId`),
  ADD KEY `idx_incident_route` (`RouteId`);

--
-- Indexes for table `Location`
--
ALTER TABLE `Location`
  ADD PRIMARY KEY (`UniqueID`);

--
-- Indexes for table `Payment`
--
ALTER TABLE `Payment`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `idx_payment_booking` (`BookingId`);

--
-- Indexes for table `Route`
--
ALTER TABLE `Route`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `idx_route_origin_dest` (`Origin`,`Destination`);

--
-- Indexes for table `Schedule`
--
ALTER TABLE `Schedule`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `idx_schedule_bus` (`BusID`),
  ADD KEY `idx_schedule_departure` (`DepartureTime`);

--
-- Indexes for table `Seat`
--
ALTER TABLE `Seat`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `unique_seat_per_bus` (`BusID`,`SeatNumber`),
  ADD KEY `idx_seat_bus` (`BusID`);

--
-- Indexes for table `User`
--
ALTER TABLE `User`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indexes for table `User_2`
--
ALTER TABLE `User_2`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `idx_user2_reset_token` (`reset_token`),
  ADD KEY `idx_user2_token_expiry` (`token_expiry`),
  ADD KEY `idx_user2_created_at` (`created_at`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Admin`
--
ALTER TABLE `Admin`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `Announcements`
--
ALTER TABLE `Announcements`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `Booking`
--
ALTER TABLE `Booking`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=322;

--
-- AUTO_INCREMENT for table `BookingCancellation`
--
ALTER TABLE `BookingCancellation`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `Bus`
--
ALTER TABLE `Bus`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `Feedback`
--
ALTER TABLE `Feedback`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=223;

--
-- AUTO_INCREMENT for table `Incident`
--
ALTER TABLE `Incident`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `Location`
--
ALTER TABLE `Location`
  MODIFY `UniqueID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `Payment`
--
ALTER TABLE `Payment`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=191;

--
-- AUTO_INCREMENT for table `Route`
--
ALTER TABLE `Route`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `Schedule`
--
ALTER TABLE `Schedule`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1462;

--
-- AUTO_INCREMENT for table `Seat`
--
ALTER TABLE `Seat`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `User`
--
ALTER TABLE `User`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Feedback`
--
ALTER TABLE `Feedback`
  ADD CONSTRAINT `fk_feedback_booking` FOREIGN KEY (`BookingId`) REFERENCES `Booking` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;