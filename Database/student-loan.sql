-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 14, 2025 at 09:04 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.1.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `student-loan`
--

-- --------------------------------------------------------

--
-- Table structure for table `failed_verifications`
--

CREATE TABLE `failed_verifications` (
  `id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `verification_code` varchar(10) NOT NULL,
  `captcha` varchar(10) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `timestamp` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `failed_verifications`
--

INSERT INTO `failed_verifications` (`id`, `student_id`, `email`, `verification_code`, `captcha`, `ip_address`, `location`, `timestamp`) VALUES
(9, '123456789', 'mshanto213052@bscse.uiu.ac.bd', '23145', '12345', '127.0.0.1', 'Dhaka, Bangladesh', '2025-01-13 06:28:35'),
(10, '012345675', 'mshanto213052@bscse.uiu.ac.bd', '12345', '12345', '127.0.0.1', 'Barisal, Bangladesh', '2025-01-13 16:03:06'),
(11, '012345675', 'mshanto213052@bscse.uiu.ac.bd', '12345', '12345', '127.0.0.1', 'Rajshahi, Bangladesh', '2025-01-13 16:07:38'),
(12, '123456789', 'mshanto213052@bscse.uiu.ac.bd', '12345', '12345', '127.0.0.1', 'Barisal, Bangladesh', '2025-01-13 16:08:12'),
(13, '011213052', 'mshanto213052@bscse.uiu.ac.bd', '123456', '12345', '127.0.0.1', 'Dhaka, Bangladesh', '2025-01-13 18:53:40'),
(14, '011213052', 'mshanto213052@bscse.uiu.ac.bd', '12345', '12345', '127.0.0.1', 'Comilla, Bangladesh', '2025-01-14 10:55:47'),
(15, '011213052', 'mshanto213052@bscse.uiu.ac.bd', '12345', '12345', '127.0.0.1', 'Barisal, Bangladesh', '2025-01-14 17:06:36'),
(16, '011213052', 'mshanto213052@bscse.uiu.ac.bd', ' 947133', '12345', '127.0.0.1', 'Khulna, Bangladesh', '2025-01-28 09:22:07'),
(17, '011213052', 'mshanto213052@bscse.uiu.ac.bd', ' 947133', '12345', '127.0.0.1', 'Comilla, Bangladesh', '2025-01-28 09:22:21'),
(18, '011213052', 'mshanto213052@bscse.uiu.ac.bd', ' 947133', '21345', '127.0.0.1', 'Rajshahi, Bangladesh', '2025-01-28 09:22:28');

-- --------------------------------------------------------

--
-- Table structure for table `flood_data`
--

CREATE TABLE `flood_data` (
  `id` int(11) NOT NULL,
  `location_name` varchar(255) NOT NULL,
  `latitude` decimal(10,7) NOT NULL,
  `longitude` decimal(10,7) NOT NULL,
  `flood_date` date NOT NULL,
  `rainfall_mm` decimal(10,2) DEFAULT NULL,
  `river_flow_rate` decimal(10,2) DEFAULT NULL,
  `river_water_level` decimal(10,2) DEFAULT NULL,
  `temperature_celsius` decimal(5,2) DEFAULT NULL,
  `humidity_percentage` decimal(5,2) DEFAULT NULL,
  `wind_speed_kmph` decimal(5,2) DEFAULT NULL,
  `soil_moisture` decimal(10,2) DEFAULT NULL,
  `damage_cost_estimate` decimal(15,2) DEFAULT NULL,
  `evacuations` int(11) DEFAULT NULL,
  `deaths` int(11) DEFAULT NULL,
  `affected_area_sqkm` decimal(10,2) DEFAULT NULL,
  `warning_issued` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `flood_data`
--

INSERT INTO `flood_data` (`id`, `location_name`, `latitude`, `longitude`, `flood_date`, `rainfall_mm`, `river_flow_rate`, `river_water_level`, `temperature_celsius`, `humidity_percentage`, `wind_speed_kmph`, `soil_moisture`, `damage_cost_estimate`, `evacuations`, `deaths`, `affected_area_sqkm`, `warning_issued`, `created_at`, `updated_at`) VALUES
(1, 'Cox\'s Bazar', '21.4272000', '92.0058000', '2023-09-10', '450.75', '2000.50', '8.20', '29.70', '90.50', '25.00', '70.50', '8000000.00', 15000, 350, '300.25', 1, '2025-01-13 07:33:07', '2025-01-13 07:33:07'),
(2, 'Khulna', '22.8456000', '89.5403000', '2024-06-20', '370.60', '1500.45', '7.80', '31.20', '87.00', '22.30', '60.00', '2000000.00', 6000, 200, '150.50', 0, '2025-01-13 07:33:07', '2025-01-13 09:44:22'),
(3, 'Barisal', '22.7010000', '90.3535000', '2023-07-18', '380.40', '1400.20', '7.00', '30.00', '85.70', '21.50', '50.50', '2500000.00', 8000, 180, '180.75', 1, '2025-01-13 07:33:07', '2025-01-13 07:33:07'),
(4, 'Patuakhali', '22.3596000', '90.3299000', '2024-08-05', '410.20', '1750.60', '8.10', '30.50', '89.30', '23.50', '65.00', '6000000.00', 12000, 300, '220.90', 1, '2025-01-13 07:33:07', '2025-01-13 07:33:07'),
(5, 'Satkhira', '22.7175000', '89.0706000', '2023-06-15', '360.80', '1300.80', '7.30', '29.50', '84.00', '20.00', '55.50', '1800000.00', 5000, 120, '110.75', 1, '2025-01-13 07:33:07', '2025-01-13 07:33:07');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text DEFAULT NULL,
  `media_path` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `user_id`, `title`, `content`, `media_path`, `created_at`, `updated_at`) VALUES
(71, 1, 'Need 1,000 Taka for My Research Project Supplies', 'Greetings, friends\r\nI am working on a research project, and I need 1,000 taka to buy some necessary supplies for my experiment. These supplies are vital to the success of my project. I’m reaching out to anyone who can help me with this amount.\r\nI will be extremely thankful for any support! Your generosity will be put to good use in my studies. Thank you!', '', '2025-01-08 18:19:07', '2025-01-08 18:19:07'),
(73, 1, 'need help', 'i need 2000 taka', '', '2025-01-28 09:22:49', '2025-01-28 09:22:49');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `failed_verifications`
--
ALTER TABLE `failed_verifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ip_address` (`ip_address`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `flood_data`
--
ALTER TABLE `flood_data`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
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
-- AUTO_INCREMENT for table `failed_verifications`
--
ALTER TABLE `failed_verifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `flood_data`
--
ALTER TABLE `flood_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
