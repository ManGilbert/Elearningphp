-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 10, 2026 at 05:32 PM
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
-- Database: `coursehub`
--

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `youtube_link` varchar(255) DEFAULT NULL,
  `image_url` varchar(255) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `name`, `description`, `category`, `youtube_link`, `image_url`, `created_by`, `created_at`) VALUES
(1, 'Python Programming', 'Learn Python programming from beginner to advanced.', 'Backend', 'https://www.youtube.com/embed/_uQrJ0TkZlc', '69aff33a299db.jpg', NULL, '2026-03-09 16:24:20'),
(2, 'ASP.NET Development', 'Build dynamic web applications using ASP.NET Core.', 'Web Dev', 'https://www.youtube.com/embed/1p3x7S3J8vU', '69b00b0c506e8.jpg', NULL, '2026-03-09 16:24:20'),
(3, 'Bootstrap Framework', 'Create responsive websites using Bootstrap 5.', 'Frontend', 'https://www.youtube.com/embed/4sosXZsdy-s', '69b01149f0bda.jpg', NULL, '2026-03-09 16:24:20'),
(4, 'C# Programming', 'Learn C# and .NET for desktop and web applications.', 'Programming', 'https://www.youtube.com/embed/GhQdlIFylQ8', '69b00b74951cb.png', NULL, '2026-03-09 16:24:20'),
(5, 'JavaScript Course', 'Master JavaScript and modern web development.', 'Frontend', 'https://www.youtube.com/embed/W6NZfCO5SIk', '69b00eff62558.jpg', NULL, '2026-03-09 16:24:20'),
(6, 'Django Framework', 'Build powerful web apps using Django and Python.', 'Backend', 'https://www.youtube.com/embed/F5mRW0jo-U4', '69b01130597ff.jpg', NULL, '2026-03-09 16:24:20'),
(11, 'HTML & CSS', 'HTML for structuring web pages while css for properiety styling pages', 'web development', 'https://www.youtube.com/watch?v=TOlL02slaag', '69b045f5ecc65.jpg', 7, '2026-03-10 16:25:26');

-- --------------------------------------------------------

--
-- Table structure for table `my_courses`
--

CREATE TABLE `my_courses` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `enrolled_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `progress` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `my_courses`
--

INSERT INTO `my_courses` (`id`, `user_id`, `course_id`, `enrolled_at`, `progress`) VALUES
(4, 5, 1, '2026-03-10 09:29:46', 0),
(5, 11, 2, '2026-03-10 09:45:23', 0),
(6, 11, 1, '2026-03-10 11:24:40', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(100) NOT NULL,
  `role` enum('student','admin') NOT NULL DEFAULT 'student',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('active','blocked') NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `role`, `created_at`, `status`) VALUES
(5, 'Tekereza Emmanel', 'emmy@gmail.com', '11', 'student', '2026-03-09 16:50:38', 'blocked'),
(7, 'Imanishimwe Gilbert', 'admin@admin.com', '22', 'admin', '2026-03-09 17:12:13', 'active'),
(11, 'Nahirwe Jean Paul', 'paul@gmail.com', '11', 'student', '2026-03-10 09:43:47', 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `my_courses`
--
ALTER TABLE `my_courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`course_id`),
  ADD KEY `course_id` (`course_id`);

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
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `my_courses`
--
ALTER TABLE `my_courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `my_courses`
--
ALTER TABLE `my_courses`
  ADD CONSTRAINT `my_courses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `my_courses_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
