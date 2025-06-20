-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Cze 09, 2025 at 12:44 AM
-- Wersja serwera: 10.4.32-MariaDB
-- Wersja PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `challengify`
--
CREATE DATABASE IF NOT EXISTS `challengify` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `challengify`;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `badges`
--

CREATE TABLE `badges` (
  `id` char(36) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `criteria` text NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `badges`
--

INSERT INTO `badges` (`id`, `name`, `description`, `image`, `criteria`, `created_at`, `updated_at`) VALUES
('ec615dcd-44a8-11f0-aafb-74563c6dd840', 'Newcomer', 'Complete your first challenge', '/images/badges/newcomer.png', 'Submit at least 1 challenge', '2025-06-08 22:41:15', NULL),
('ec61651f-44a8-11f0-aafb-74563c6dd840', 'Regular', 'Complete 5 challenges', '/images/badges/regular.png', 'Submit at least 5 challenges', '2025-06-08 22:41:15', NULL),
('ec616595-44a8-11f0-aafb-74563c6dd840', 'Prolific', 'Complete 10 challenges', '/images/badges/prolific.png', 'Submit at least 10 challenges', '2025-06-08 22:41:15', NULL),
('ec6165c1-44a8-11f0-aafb-74563c6dd840', 'Appreciated', 'Receive 5 upvotes', '/images/badges/appreciated.png', 'Get at least 5 upvotes on your submissions', '2025-06-08 22:41:15', NULL),
('ec6165ee-44a8-11f0-aafb-74563c6dd840', 'Popular', 'Receive 25 upvotes', '/images/badges/popular.png', 'Get at least 25 upvotes on your submissions', '2025-06-08 22:41:15', NULL),
('ec616611-44a8-11f0-aafb-74563c6dd840', 'Superstar', 'Receive 100 upvotes', '/images/badges/superstar.png', 'Get at least 100 upvotes on your submissions', '2025-06-08 22:41:15', NULL),
('ec616638-44a8-11f0-aafb-74563c6dd840', 'Bronze', 'Earn 10 reputation points', '/images/badges/bronze.png', 'Earn at least 10 reputation points', '2025-06-08 22:41:15', NULL),
('ec616658-44a8-11f0-aafb-74563c6dd840', 'Silver', 'Earn 50 reputation points', '/images/badges/silver.png', 'Earn at least 50 reputation points', '2025-06-08 22:41:15', NULL),
('ec61667b-44a8-11f0-aafb-74563c6dd840', 'Gold', 'Earn 100 reputation points', '/images/badges/gold.png', 'Earn at least 100 reputation points', '2025-06-08 22:41:15', NULL);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `categories`
--

CREATE TABLE `categories` (
  `id` char(36) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `slug` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `slug`, `created_at`, `updated_at`) VALUES
('ec602ddd-44a8-11f0-aafb-74563c6dd840', 'Creative Writing', 'Write short stories, poems, and more.', 'creative-writing', '2025-06-08 22:41:15', NULL),
('ec60355b-44a8-11f0-aafb-74563c6dd840', 'Mobile Photography', 'Take themed photos with your mobile device.', 'mobile-photography', '2025-06-08 22:41:15', NULL),
('ec6035cc-44a8-11f0-aafb-74563c6dd840', 'DIY & Crafts', 'Create simple items from recycled materials.', 'diy-crafts', '2025-06-08 22:41:15', NULL),
('ec603604-44a8-11f0-aafb-74563c6dd840', 'Healthy Habits', 'Small daily challenges for better health.', 'healthy-habits', '2025-06-08 22:41:15', NULL),
('ec60362d-44a8-11f0-aafb-74563c6dd840', 'Practical Skills', 'Learn something new in just minutes.', 'practical-skills', '2025-06-08 22:41:15', NULL);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `challenges`
--

CREATE TABLE `challenges` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `category_id` char(36) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `difficulty` enum('easy','medium','hard') DEFAULT 'medium',
  `rules` text DEFAULT NULL,
  `submission_guidelines` text DEFAULT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `status` enum('draft','active','completed','cancelled') DEFAULT 'draft',
  `image` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `phinxlog`
--

CREATE TABLE `phinxlog` (
  `version` bigint(20) NOT NULL,
  `migration_name` varchar(100) DEFAULT NULL,
  `start_time` timestamp NULL DEFAULT NULL,
  `end_time` timestamp NULL DEFAULT NULL,
  `breakpoint` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `rate_limits`
--

CREATE TABLE `rate_limits` (
  `id` int(11) NOT NULL,
  `key` varchar(100) NOT NULL,
  `ip` varchar(45) NOT NULL,
  `attempts` int(11) DEFAULT 0,
  `last_attempt` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rate_limits`
--

INSERT INTO `rate_limits` (`id`, `key`, `ip`, `attempts`, `last_attempt`) VALUES
(1, 'login', '::1', 0, '2025-06-08 22:29:38'),
(2, 'register', '::1', 0, '2025-06-08 22:16:33');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `submissions`
--

CREATE TABLE `submissions` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `challenge_id` char(36) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `content` text NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `status` enum('draft','submitted','approved','rejected') DEFAULT 'submitted',
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `users`
--

CREATE TABLE `users` (
  `id` char(36) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `email_verified_at` datetime DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `reputation` int(11) DEFAULT 0,
  `login_attempts` int(11) DEFAULT 0,
  `last_attempt_time` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `first_name`, `last_name`, `avatar`, `bio`, `role`, `email_verified_at`, `remember_token`, `reputation`, `login_attempts`, `last_attempt_time`, `created_at`, `updated_at`) VALUES
('', 'admin2', 'kduda@zscl.pl', '$2y$12$4QXixtlZB7LWDmXzI9jCy.q7rZOENCKx2z2wEdGDLyyiAVv1mY/J2', NULL, NULL, NULL, NULL, 'user', NULL, NULL, 0, 0, NULL, '2025-06-08 22:16:33', '2025-06-08 22:16:33'),
('8707f81a-a15c-439c-a8c8-e03e0b8bd057', 'KPZ2311', 'kapieksperimental@gmail.com', '$2y$12$A8f9ym/UeDURXIZoGwmskuqatDl91PmdYKzoXv0MMLPyD/wyx.6Xq', NULL, NULL, NULL, NULL, 'user', NULL, NULL, 0, 0, NULL, '2025-06-08 20:52:49', '2025-06-08 20:52:49'),
('ec626984-44a8-11f0-aafb-74563c6dd840', 'admin', 'admin@challengify.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, NULL, NULL, 'admin', NULL, NULL, 0, 0, NULL, '2025-06-08 22:41:15', NULL);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `user_badges`
--

CREATE TABLE `user_badges` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `badge_id` char(36) NOT NULL,
  `awarded_at` datetime NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `votes`
--

CREATE TABLE `votes` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `submission_id` char(36) NOT NULL,
  `vote_type` enum('upvote','downvote') NOT NULL,
  `comment` text DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `badges`
--
ALTER TABLE `badges`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indeksy dla tabeli `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indeksy dla tabeli `challenges`
--
ALTER TABLE `challenges`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `title` (`title`),
  ADD KEY `fk_challenges_user_id` (`user_id`),
  ADD KEY `fk_challenges_category_id` (`category_id`);

--
-- Indeksy dla tabeli `phinxlog`
--
ALTER TABLE `phinxlog`
  ADD PRIMARY KEY (`version`);

--
-- Indeksy dla tabeli `rate_limits`
--
ALTER TABLE `rate_limits`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `key_ip` (`key`,`ip`);

--
-- Indeksy dla tabeli `submissions`
--
ALTER TABLE `submissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_challenge` (`user_id`,`challenge_id`),
  ADD KEY `fk_submissions_challenge_id` (`challenge_id`);

--
-- Indeksy dla tabeli `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeksy dla tabeli `user_badges`
--
ALTER TABLE `user_badges`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_badge` (`user_id`,`badge_id`),
  ADD KEY `fk_user_badges_badge_id` (`badge_id`);

--
-- Indeksy dla tabeli `votes`
--
ALTER TABLE `votes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_submission` (`user_id`,`submission_id`),
  ADD KEY `fk_votes_submission_id` (`submission_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `rate_limits`
--
ALTER TABLE `rate_limits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `challenges`
--
ALTER TABLE `challenges`
  ADD CONSTRAINT `fk_challenges_category_id` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_challenges_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `submissions`
--
ALTER TABLE `submissions`
  ADD CONSTRAINT `fk_submissions_challenge_id` FOREIGN KEY (`challenge_id`) REFERENCES `challenges` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_submissions_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_badges`
--
ALTER TABLE `user_badges`
  ADD CONSTRAINT `fk_user_badges_badge_id` FOREIGN KEY (`badge_id`) REFERENCES `badges` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_user_badges_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `votes`
--
ALTER TABLE `votes`
  ADD CONSTRAINT `fk_votes_submission_id` FOREIGN KEY (`submission_id`) REFERENCES `submissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_votes_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
