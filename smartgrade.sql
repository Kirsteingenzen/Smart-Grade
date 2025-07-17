-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 03, 2025 at 04:56 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `smartgrade`
--

-- --------------------------------------------------------

--
-- Table structure for table `grades`
--

CREATE TABLE `grades` (
  `id` int(11) NOT NULL,
  `student_id` varchar(20) DEFAULT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `grade` decimal(5,2) NOT NULL,
  `semester` enum('1st','2nd') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grades`
--

INSERT INTO `grades` (`id`, `student_id`, `subject_id`, `grade`, `semester`, `created_at`, `updated_at`) VALUES
(1, '231-01423', 1, 85.50, '2nd', '2025-06-03 13:01:34', '2025-06-03 13:01:34'),
(2, '231-01423', 2, 90.00, '1st', '2025-06-03 13:01:34', '2025-06-03 13:01:34'),
(3, '231-01424', 1, 88.00, '2nd', '2025-06-03 13:01:34', '2025-06-03 13:01:34'),
(4, '231-01424', 2, 0.00, '1st', '2025-06-03 13:01:34', '2025-06-03 14:24:48'),
(5, '231-01423', 2, 99.00, '2nd', '2025-06-03 13:01:43', '2025-06-03 13:01:43'),
(8, '231-01423', 4, 0.00, '2nd', '2025-06-03 13:01:43', '2025-06-03 13:01:43'),
(10, '231-01423', 5, 0.00, '2nd', '2025-06-03 13:01:43', '2025-06-03 13:01:43'),
(11, '231-01423', 3, 0.00, '2nd', '2025-06-03 13:01:43', '2025-06-03 13:01:43'),
(12, '242-01423', 2, 90.00, '1st', '2025-06-03 13:06:16', '2025-06-03 13:06:16'),
(14, '242-01423', 4, 78.00, '1st', '2025-06-03 13:06:16', '2025-06-03 13:13:45'),
(15, '242-01423', 1, 97.70, '1st', '2025-06-03 13:06:16', '2025-06-03 13:13:45'),
(16, '242-01423', 5, 87.50, '1st', '2025-06-03 13:06:16', '2025-06-03 13:13:46'),
(17, '242-01423', 3, 86.90, '1st', '2025-06-03 13:06:16', '2025-06-03 13:13:46'),
(84, '231-01423', 4, 87.00, '1st', '2025-06-03 13:24:06', '2025-06-03 13:24:06'),
(85, '231-01423', 1, 97.67, '1st', '2025-06-03 13:24:06', '2025-06-03 13:24:16'),
(86, '231-01423', 5, 89.88, '1st', '2025-06-03 13:24:06', '2025-06-03 13:24:16'),
(87, '231-01423', 3, 97.66, '1st', '2025-06-03 13:24:06', '2025-06-03 13:24:16'),
(105, '231-01424', 4, 0.00, '1st', '2025-06-03 14:24:48', '2025-06-03 14:24:48'),
(106, '231-01424', 1, 0.00, '1st', '2025-06-03 14:24:48', '2025-06-03 14:24:48'),
(107, '231-01424', 5, 0.00, '1st', '2025-06-03 14:24:48', '2025-06-03 14:24:48'),
(108, '231-01424', 3, 0.00, '1st', '2025-06-03 14:24:48', '2025-06-03 14:24:48'),
(184, '231-02743', 2, 98.00, '1st', '2025-06-03 14:29:31', '2025-06-03 14:29:31'),
(185, '231-02743', 4, 97.00, '1st', '2025-06-03 14:29:31', '2025-06-03 14:29:31'),
(186, '231-02743', 1, 98.00, '1st', '2025-06-03 14:29:31', '2025-06-03 14:29:31'),
(187, '231-02743', 5, 96.00, '1st', '2025-06-03 14:29:31', '2025-06-03 14:29:31'),
(188, '231-02743', 3, 98.00, '1st', '2025-06-03 14:29:31', '2025-06-03 14:29:31'),
(229, '231-00001', 2, 98.00, '1st', '2025-06-03 14:52:28', '2025-06-03 14:52:28'),
(230, '231-00001', 4, 98.00, '1st', '2025-06-03 14:52:28', '2025-06-03 14:52:28'),
(231, '231-00001', 1, 96.70, '1st', '2025-06-03 14:52:28', '2025-06-03 14:52:28'),
(232, '231-00001', 5, 97.90, '1st', '2025-06-03 14:52:28', '2025-06-03 14:52:28'),
(233, '231-00001', 3, 89.70, '1st', '2025-06-03 14:52:28', '2025-06-03 14:52:28');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `password` varchar(255) NOT NULL,
  `contact_number` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `student_id`, `full_name`, `gender`, `password`, `contact_number`, `email`, `address`, `created_at`, `updated_at`) VALUES
(1, '231-01423', 'Regie Baquiran', 'Male', 'regie123', '09345678901', 'baquiran.regie@carsu.edu.ph', 'P - 2 Libertad, Butuan City', '2025-05-29 08:36:52', '2025-06-03 11:28:33'),
(2, '231-01424', 'Daniel Padilla', 'Female', 'daniel123', '09456789012', 'daniel.padilla@carsu.edu.ph', 'P - 1 Bonbon, Butuan City', '2025-05-29 08:36:52', '2025-06-03 11:29:31'),
(6, '242-01423', 'Nina Mae Baquiran', 'Male', 'nina123', '09123265122', 'nina.baquiran@carsu.edu.ph', 'P - 2 Libertad, Butuan City', '2025-06-03 01:16:58', '2025-06-03 11:30:17'),
(7, '231-00001', 'Robin Padilla', 'Male', 'robin123', '0912326555', 'robin.padilla@carsu.edu.ph', '789 Address St, City', '2025-06-03 12:20:57', '2025-06-03 12:20:57'),
(8, '231-02321', 'Anne Curtis', '', 'anne123', '09291588874', 'anne.kutis@carsu.edu.ph', 'P - 7 Silingan Namo, Butuan City', '2025-06-03 14:26:18', '2025-06-03 14:29:06'),
(9, '231-02743', 'Angelica Shane De Julian', 'Female', 'angelica123', '09380349658', 'angelicashane.dejulian@carsu.edu.ph', 'P - 7 Florida, Butuan City', '2025-06-03 14:27:40', '2025-06-03 14:28:26');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `subject_name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `subject_name`, `created_at`) VALUES
(1, 'Mathematics', '2025-05-29 08:36:52'),
(2, 'English', '2025-05-29 08:36:52'),
(3, 'Science', '2025-05-29 08:36:52'),
(4, 'History', '2025-05-29 08:36:52'),
(5, 'Physical Education', '2025-05-29 08:36:52');

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `sex` enum('Male','Female') NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`id`, `username`, `password`, `full_name`, `sex`, `email`, `contact_number`, `address`, `created_at`, `updated_at`) VALUES
(2, 'veverly', '123', 'Veverly Ewan', 'Female', 'veverly.ewan@carsu.edu.ph', '09234567890', 'P - 6 Ampayon, Butuan City', '2025-05-29 08:36:52', '2025-06-02 14:22:08'),
(3, 'kirstein', 'kirstein123', 'Kirstein Nojapa', 'Female', 'kirstein.nojapa@carsu.edu.ph', '09123456789', 'P - 2 Antongalon, Butuan City', '2025-06-02 14:27:10', '2025-06-02 14:27:10'),
(4, 'teacher1', 'password123', 'John Smiths', 'Male', 'john.smith@school.com', '09123456789', '789 Teacher St, City', '2025-06-02 14:28:01', '2025-06-02 14:29:54');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_grade` (`student_id`,`subject_id`,`semester`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_id` (`student_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `subject_name` (`subject_name`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `grades`
--
ALTER TABLE `grades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=234;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `grades`
--
ALTER TABLE `grades`
  ADD CONSTRAINT `grades_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `grades_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
