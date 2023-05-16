-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 23, 2023 at 08:38 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hostelfinder`
--

-- --------------------------------------------------------

--
-- Table structure for table `add_property`
--

CREATE TABLE `add_property` (
  `property_id` int(10) NOT NULL,
  `province` varchar(50) NOT NULL,
  `zone` varchar(50) NOT NULL,
  `district` varchar(50) NOT NULL,
  `city` varchar(15) NOT NULL,
  `contact_no` bigint(10) NOT NULL,
  `property_type` varchar(50) NOT NULL,
  `estimated_price` bigint(10) NOT NULL,
  `bathroom` varchar(10) NOT NULL,
  `description` varchar(2000) NOT NULL,
  `latitude` decimal(9,6) NOT NULL,
  `longitude` decimal(9,6) NOT NULL,
  `owner_id` int(10) NOT NULL,
  `booked` varchar(10) NOT NULL DEFAULT 'No'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `add_property`
--

INSERT INTO `add_property` (`property_id`, `province`, `zone`, `district`, `city`, `contact_no`, `property_type`, `estimated_price`, `bathroom`, `description`, `latitude`, `longitude`, `owner_id`, `booked`) VALUES
(125, 'Bagmati', 'Bagmati', 'Bhaktapur', 'Suryabinayak', 1111111111, 'Single Room (AC)', 7000, 'Yes', 'We reinvented the hostel and redefined the hostel life! This revolutionary hostel mode influencing will not only just limit to the people’s living but also has profound impact on the transportation, education, social, employment and many other fields. High-quality modern life always belongs to very few rich people. We invent a revolutionary solution through the innovation, advanced technology, great and unique design, strong cost controlling that makes urban life standard bringing you 30 years ahead.', '27.692428', '85.282584', 2, 'Yes'),
(127, 'Bagmati', 'Bagmati', 'Kathmandu', 'Kalanki', 2555555555, 'Room Rent 1P', 8000, 'yes', 'We reinvented the hostel and redefined the hostel life! This revolutionary hostel mode influencing will not only just limit to the people’s living but also has profound impact on the transportation, education, social, employment and many other fields. High-quality modern life always belongs to very few rich people. We invent a revolutionary solution through the innovation, advanced technology, great and unique design, strong cost controlling that makes urban life standard bringing you 30 years ahead.', '27.692428', '85.282584', 2, 'Yes'),
(128, 'Bagmati', 'Bagmati', 'Lalitpur', 'Patan', 355555555, 'Premium Room for 2 Person', 10005, 'yes', 'We reinvented the hostel and redefined the hostel life! This revolutionary hostel mode influencing will not only just limit to the people’s living but also has profound impact on the transportation, education, social, employment and many other fields. High-quality modern life always belongs to very few rich people. We invent a revolutionary solution through the innovation, advanced technology, great and unique design, strong cost controlling that makes urban life standard bringing you 30 years ahead.', '27.692428', '85.282584', 2, 'Yes'),
(132, 'Bagmati', 'Bagmati', 'Kathmandu', 'Nagarjung', 10000000000, 'Flat Rent', 5000, 'Yes', 'This is a test where we perform test', '27.692428', '85.282584', 1, 'Yes');

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(10) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `email`, `password`) VALUES
(1, 'admin@gmail.com', '12345');

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

CREATE TABLE `booking` (
  `tenant_id` int(10) DEFAULT NULL,
  `property_id` int(11) DEFAULT NULL,
  `booking_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking`
--

INSERT INTO `booking` (`tenant_id`, `property_id`, `booking_id`) VALUES
(18, 124, 1),
(1, 126, 6),
(1, 125, 7),
(1, 124, 8),
(1, 127, 9),
(1, 128, 11);

-- --------------------------------------------------------

--
-- Table structure for table `chat`
--

CREATE TABLE `chat` (
  `owner_id` int(10) NOT NULL,
  `tenant_id` int(10) NOT NULL,
  `message` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chat`
--

INSERT INTO `chat` (`owner_id`, `tenant_id`, `message`) VALUES
(2, 1, 'HI Owner ! Whatsup!');

-- --------------------------------------------------------

--
-- Table structure for table `owner`
--

CREATE TABLE `owner` (
  `owner_id` int(10) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `phone_no` bigint(10) NOT NULL,
  `address` varchar(200) NOT NULL,
  `id_type` varchar(100) NOT NULL,
  `id_photo` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `owner`
--

INSERT INTO `owner` (`owner_id`, `full_name`, `email`, `password`, `phone_no`, `address`, `id_type`, `id_photo`) VALUES
(1, 'Light Zero', 'owner@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', 9801234567, 'Kalanki', 'Citizenship', 'owner-photo/khushi.jpg'),
(2, 'Light Zero', 'owner1@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', 123444444, 'Kathmandu', 'Citizenship', 'owner-photo/munna1.jpg'),
(4, 'Light', 'admin@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', 1234567890, 'bafal', 'Citizenship', 'owner-photo/9.png'),
(5, 'zzd', 'customer@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', 0, 'ew', 'Citizenship', 'owner-photo/9.png');

-- --------------------------------------------------------

--
-- Table structure for table `property_photo`
--

CREATE TABLE `property_photo` (
  `property_photo_id` int(12) NOT NULL,
  `p_photo` varchar(500) NOT NULL,
  `property_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `property_photo`
--

INSERT INTO `property_photo` (`property_photo_id`, `p_photo`, `property_id`) VALUES
(178, 'product-photo/room1.jpg', 125),
(180, 'product-photo/room2.jpg', 127),
(186, 'product-photo/new1.jpg', 128),
(198, 'product-photo/alen-rojnic-T1Yvmf4oleQ-unsplash.jpg', 132);

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `review_id` int(10) NOT NULL,
  `comment` varchar(500) NOT NULL,
  `rating` int(5) NOT NULL,
  `property_id` int(11) NOT NULL,
  `positive` int(11) DEFAULT NULL,
  `neutral` int(11) DEFAULT NULL,
  `negative` int(11) DEFAULT NULL,
  `compound` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `review`
--

INSERT INTO `review` (`review_id`, `comment`, `rating`, `property_id`, `positive`, `neutral`, `negative`, `compound`) VALUES
(12, 'Nice Environment', 5, 125, 74, 26, 0, 42),
(15, 'good hostel with great facilities\r\n', 0, 127, 70, 30, 0, 79),
(20, 'dfddsfsd', 5, 125, 0, 100, 0, 0),
(21, 'very very nice hostel recommended for everyone.', 5, 125, 52, 48, 0, 66),
(22, 'very very good', 5, 127, 63, 37, 0, 53),
(23, 'very very bad not good', 1, 128, 0, 35, 65, -69);

-- --------------------------------------------------------

--
-- Table structure for table `tenant`
--

CREATE TABLE `tenant` (
  `tenant_id` int(10) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `phone_no` bigint(10) NOT NULL,
  `address` varchar(200) NOT NULL,
  `id_type` varchar(100) NOT NULL,
  `id_photo` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tenant`
--

INSERT INTO `tenant` (`tenant_id`, `full_name`, `email`, `password`, `phone_no`, `address`, `id_type`, `id_photo`) VALUES
(1, 'Customer Name', 'customer@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', 9801234567, 'Bafal 13,Kathmandu', 'Citizenship', 'tenant-photo/mehedi.png'),
(2, 'Legend', 'legend@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', 1234567890, 'bafal', 'Student ID', 'tenant-photo/utpala-removebg-preview.png');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `add_property`
--
ALTER TABLE `add_property`
  ADD PRIMARY KEY (`property_id`),
  ADD KEY `owner_id` (`owner_id`);

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`booking_id`);

--
-- Indexes for table `chat`
--
ALTER TABLE `chat`
  ADD PRIMARY KEY (`owner_id`);

--
-- Indexes for table `owner`
--
ALTER TABLE `owner`
  ADD PRIMARY KEY (`owner_id`);

--
-- Indexes for table `property_photo`
--
ALTER TABLE `property_photo`
  ADD PRIMARY KEY (`property_photo_id`),
  ADD KEY `property_id` (`property_id`);

--
-- Indexes for table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `property_id` (`property_id`);

--
-- Indexes for table `tenant`
--
ALTER TABLE `tenant`
  ADD PRIMARY KEY (`tenant_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `add_property`
--
ALTER TABLE `add_property`
  MODIFY `property_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=133;

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `booking_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `owner`
--
ALTER TABLE `owner`
  MODIFY `owner_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `property_photo`
--
ALTER TABLE `property_photo`
  MODIFY `property_photo_id` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=199;

--
-- AUTO_INCREMENT for table `review`
--
ALTER TABLE `review`
  MODIFY `review_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `tenant`
--
ALTER TABLE `tenant`
  MODIFY `tenant_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `add_property`
--
ALTER TABLE `add_property`
  ADD CONSTRAINT `add_property_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `owner` (`owner_id`);

--
-- Constraints for table `property_photo`
--
ALTER TABLE `property_photo`
  ADD CONSTRAINT `property_photo_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `add_property` (`property_id`);

--
-- Constraints for table `review`
--
ALTER TABLE `review`
  ADD CONSTRAINT `review_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `add_property` (`property_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
