-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 20, 2024 at 06:44 AM
-- Server version: 10.11.7-MariaDB-cll-lve
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u481419426_erp_setting_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `list_db`
--

CREATE TABLE `list_db` (
  `hostname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `psw` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `db` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `db_alias` varchar(255) NOT NULL,
  `isaktif` tinyint(4) DEFAULT NULL,
  `ishapus` tinyint(4) DEFAULT NULL,
  `ismain` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `list_db`
--

INSERT INTO `list_db` (`hostname`, `username`, `psw`, `db`, `db_alias`, `isaktif`, `ishapus`, `ismain`) VALUES
('localhost', 'u481419426_user_uat_boxit', '[hLm0>$cS0W8', 'u481419426_uat_erp_boxity', 'u481419426_uat_erp_boxity', 1, 0, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `list_db`
--
ALTER TABLE `list_db`
  ADD PRIMARY KEY (`db`) USING BTREE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
