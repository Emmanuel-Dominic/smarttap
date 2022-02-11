-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 28, 2022 at 04:39 PM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 8.1.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `smarttap`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `user_id` int(11) NOT NULL,
  `meter_no` varchar(256) NOT NULL,
  `meter_reading` int(11) NOT NULL,
  `amount` int(25) NOT NULL DEFAULT '0',
  `payment` int(25) NOT NULL DEFAULT '0',
  `balance` varchar(255) NOT NULL DEFAULT '0',
  `ph` float NOT NULL,
  `tds` int(11) NOT NULL,
  `turbidity` int(11) NOT NULL,
  `temperature` int(11) NOT NULL,
  `pressure` int(11) NOT NULL,
  `security` varchar(11) NOT NULL,
  `gps_cordinates` varchar(256) NOT NULL,
  `battery_life` varchar(4) NOT NULL,
  `category` enum('post-paid','pre-paid') NOT NULL,
  `status` enum('1','0') NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`user_id`, `meter_no`, `meter_reading`, `amount`, `payment`, `balance`, `ph`, `tds`, `turbidity`, `temperature`, `pressure`, `security`, `gps_cordinates`, `battery_life`, `category`, `status`, `created_at`) VALUES
(2, '2026550143', 0, 0, 0, '0', 0, 0, 0, 0, 0, '', '0.4608Â° N, 34.1115Â° E', '70', 'post-paid', '1', '2021-12-11 18:42:04');

-- --------------------------------------------------------

--
-- Table structure for table `clearance`
--

CREATE TABLE `clearance` (
  `id` int(1) NOT NULL,
  `clearance_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `clearance`
--

INSERT INTO `clearance` (`id`, `clearance_date`, `updated_at`) VALUES
(1, '2022-02-11 09:16:53', '2022-02-11 17:16:53');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(15) NOT NULL,
  `email` varchar(40) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `card_no` varchar(50) NOT NULL,
  `meter_reading` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `payment` int(25) NOT NULL DEFAULT '0',
  `balance` varchar(255) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('1','0') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `user_id`, `name`, `username`, `email`, `phone`, `card_no`, `meter_reading`, `amount`, `payment`, `balance`, `created_at`, `status`) VALUES
(1, 2, 'Josephine Kisakye', 'josephine', 'kisakyejosephine79@gmail.com', '0786493670', ' 69b4375a', 0, 0, 0, '0', '2019-05-08 17:28:44', '0');

-- --------------------------------------------------------

--
-- Table structure for table `parameters`
--

CREATE TABLE `parameters` (
  `id` int(11) NOT NULL,
  `meter_no` varchar(255) NOT NULL,
  `meter_reading1` varchar(11) NOT NULL,
  `meter_reading2` varchar(11) NOT NULL,
  `ph` float NOT NULL,
  `tds` varchar(11) NOT NULL,
  `turbidity` varchar(11) NOT NULL,
  `temperature` varchar(11) NOT NULL,
  `pressure` int(11) NOT NULL,
  `security` varchar(11) NOT NULL,
  `rfid_no` varchar(255) NOT NULL,
  `status1` enum('1','0') NOT NULL,
  `status2` enum('1','0') NOT NULL,
  `gps_cordinates` varchar(255) NOT NULL,
  `battery_life` varchar(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `parameters`
--

INSERT INTO `parameters` (`id`, `meter_no`, `meter_reading1`, `meter_reading2`, `ph`, `tds`, `turbidity`, `temperature`, `pressure`, `security`, `rfid_no`, `status1`, `status2`, `gps_cordinates`, `battery_life`, `created_at`) VALUES
(1, '2026550143', '0', '0', 0, '0', '0', '0', 0, '0', '69B4375A', '1', '1', '0.4608Â° N, 34.1115Â° E', '70', '2022-01-05 07:39:32');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `pay_id` int(11) NOT NULL,
  `order_reference` varchar(100) NOT NULL,
  `user_id` int(11) NOT NULL,
  `paid_amount` int(11) NOT NULL,
  `payment_type` varchar(30) NOT NULL,
  `payment_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `rfid`
--

CREATE TABLE `rfid` (
  `no` int(11) NOT NULL,
  `rfid_val` varchar(10) NOT NULL,
  `time_in` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `rfid`
--

INSERT INTO `rfid` (`no`, `rfid_val`, `time_in`) VALUES
(1, 'nul', '2022-01-04 08:15:43');

-- --------------------------------------------------------

--
-- Table structure for table `sensor_values`
--

CREATE TABLE `sensor_values` (
  `id` int(11) NOT NULL,
  `meter_no` varchar(256) NOT NULL,
  `time_in` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ph` varchar(10) NOT NULL,
  `tds` varchar(10) NOT NULL,
  `turbility` varchar(10) NOT NULL,
  `temperature` varchar(10) NOT NULL,
  `ldr` varchar(10) NOT NULL,
  `push_button` varchar(10) NOT NULL,
  `meter_reading1` varchar(256) NOT NULL,
  `meter_reading2` varchar(256) NOT NULL,
  `rfid_no` varchar(256) NOT NULL,
  `status1` int(11) NOT NULL,
  `status2` int(11) NOT NULL,
  `gps_cordinates` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `sensor_values`
--

INSERT INTO `sensor_values` (`id`, `meter_no`, `time_in`, `ph`, `tds`, `turbility`, `temperature`, `ldr`, `push_button`, `meter_reading1`, `meter_reading2`, `rfid_no`, `status1`, `status2`, `gps_cordinates`) VALUES
(1, '', '2021-09-06 11:58:55', '6.7', '4.6', '5.4', '6.8', '4.9', '1', '', '', '', 0, 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(15) NOT NULL,
  `email` varchar(40) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','client') NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `email`, `phone`, `location`, `password`, `role`, `created_at`) VALUES
(1, 'Eric Karta', 'Eric', 'eric@example.com', '2026550143', 'Busia', 'admin123', 'admin', '2019-05-08 17:32:00'),
(2, 'Malvin Juwan', 'Malvin', 'malvin@gmail.com', '0778344349', 'Tororo', '123', 'client', '2019-05-08 17:29:27');

-- --------------------------------------------------------

--
-- Table structure for table `wallet`
--

CREATE TABLE `wallet` (
  `wallet_id` int(11) NOT NULL,
  `payment_reference` varchar(100) NOT NULL,
  `user_id` int(11) NOT NULL,
  `wallet_amount` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `first_deposit` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_deposit` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `wallet`
--

INSERT INTO `wallet` (`wallet_id`, `payment_reference`, `user_id`, `wallet_amount`, `type`, `first_deposit`, `last_deposit`) VALUES
(1, 'Wallet61ee5ce040b9b', 2, 45000, 'wallet', '2022-01-24 09:32:33', '2022-01-24 09:32:33');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `parameters`
--
ALTER TABLE `parameters`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`pay_id`);

--
-- Indexes for table `rfid`
--
ALTER TABLE `rfid`
  ADD PRIMARY KEY (`no`);

--
-- Indexes for table `sensor_values`
--
ALTER TABLE `sensor_values`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wallet`
--
ALTER TABLE `wallet`
  ADD PRIMARY KEY (`wallet_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `parameters`
--
ALTER TABLE `parameters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1755;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `pay_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `rfid`
--
ALTER TABLE `rfid`
  MODIFY `no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2915;

--
-- AUTO_INCREMENT for table `sensor_values`
--
ALTER TABLE `sensor_values`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1689;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `wallet`
--
ALTER TABLE `wallet`
  MODIFY `wallet_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accounts`
--
ALTER TABLE `accounts`
  ADD CONSTRAINT `accounts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
