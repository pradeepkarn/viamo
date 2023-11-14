-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 14, 2023 at 05:42 AM
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
-- Database: `pk_viamo`
--

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` bigint(20) NOT NULL,
  `transacted_to` bigint(20) UNSIGNED NOT NULL,
  `transacted_by` bigint(20) UNSIGNED NOT NULL,
  `purchase_amt` double NOT NULL DEFAULT 0,
  `amount` double NOT NULL,
  `trn_num` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `trn_group` int(1) NOT NULL COMMENT '1: pv commission, 2: direct bonus',
  `trn_type` int(1) UNSIGNED NOT NULL COMMENT '1: credit, 2: debit',
  `status` int(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '1: active, 2: cancelled'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `transacted_to`, `transacted_by`, `purchase_amt`, `amount`, `trn_num`, `created_at`, `updated_at`, `trn_group`, `trn_type`, `status`) VALUES
(43, 0, 1, 0, 53.360000610352, 'TRN655201F073621', '2023-11-13 16:31:04', '2023-11-13 16:31:04', 2, 1, 1),
(44, 0, 1, 0, 2, 'TRN655209BADAA28', '2023-11-13 17:04:18', '2023-11-13 17:04:18', 2, 1, 1),
(45, 0, 1, 0, 25.48, 'TRN65520A1849F25', '2023-11-13 17:05:52', '2023-11-13 17:05:52', 2, 1, 1),
(46, 0, 1, 42.919998168945, 12.74, 'TRN65520AEC5C09C', '2023-11-13 17:09:24', '2023-11-13 17:09:24', 2, 1, 1),
(47, 0, 1, 67.28, 4, 'TRN65520BAD236F7', '2023-11-13 17:12:37', '2023-11-13 17:12:37', 2, 1, 1),
(48, 1, 1, 42.92, 12.74, 'TRN65520C0ED8B4E', '2023-11-13 17:14:14', '2023-11-13 17:14:14', 2, 1, 1),
(49, 1, 1, 104.4, 15, 'TRN65520D4DE4E9A', '2023-11-13 17:19:33', '2023-11-13 17:19:33', 2, 1, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
