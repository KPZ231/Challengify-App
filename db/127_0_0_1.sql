-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Czas generowania: 09 Cze 2025, 15:54
-- Wersja serwera: 10.4.20-MariaDB
-- Wersja PHP: 7.3.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `challengify`


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
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) -- Dodano PRIMARY KEY tutaj
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Zrzut danych tabeli `badges`
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
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) -- Dodano PRIMARY KEY tutaj
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Zrzut danych tabeli `categories`
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
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) -- DODAŁEM TĘ LINIĘ
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `phinxlog`
--

CREATE TABLE `phinxlog` (
  `version` bigint(20) NOT NULL,
  `migration_name` varchar(100) DEFAULT NULL,
  `start_time` timestamp NULL DEFAULT NULL,
  `end_time` timestamp NULL DEFAULT NULL,
  `breakpoint` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`version`) -- Dodano PRIMARY KEY tutaj, aby Phinxlog miało klucz podstawowy
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `rate_limits`
--

CREATE TABLE `rate_limits` (
  `id` int(11) NOT NULL,
  `key` varchar(100) NOT NULL,
  `ip` varchar(45) NOT NULL,
  `attempts` int(11) DEFAULT 0,
  `last_attempt` datetime NOT NULL,
  PRIMARY KEY (`id`) -- Dodano PRIMARY KEY tutaj
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Zrzut danych tabeli `rate_limits`
--

INSERT INTO `rate_limits` (`id`, `key`, `ip`, `attempts`, `last_attempt`) VALUES
(1, 'login', '::1', 0, '2025-06-09 13:52:49'),
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
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) -- Dodano PRIMARY KEY tutaj
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
  `updated_at` datetime DEFAULT NULL,
  `username_changes` int(11) NOT NULL DEFAULT 0 COMMENT 'Number of times user has changed their username',
  `notification_email` BOOLEAN NOT NULL DEFAULT 1,
  `notification_push` BOOLEAN NOT NULL DEFAULT 0,
  `notification_sms` BOOLEAN NOT NULL DEFAULT 0,
  `notification_time` VARCHAR(5) NOT NULL DEFAULT '18:00',
  `weekly_summary` BOOLEAN NOT NULL DEFAULT 1,
  `monthly_summary` BOOLEAN NOT NULL DEFAULT 0,
  `profile_visibility` ENUM('public', 'followers', 'private') NOT NULL DEFAULT 'public',
  `language` VARCHAR(5) NOT NULL DEFAULT 'en',
  `timezone` VARCHAR(64) NOT NULL DEFAULT 'UTC',
  `auto_timezone` BOOLEAN NOT NULL DEFAULT 1,
  `messaging_permission` ENUM('all', 'followers', 'none') NOT NULL DEFAULT 'all',
  PRIMARY KEY (`id`) -- Dodano PRIMARY KEY tutaj
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Zrzut danych tabeli `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `first_name`, `last_name`, `avatar`, `bio`, `role`, `email_verified_at`, `remember_token`, `reputation`, `login_attempts`, `last_attempt_time`, `created_at`, `updated_at`, `username_changes`) VALUES
-- Usunięto wiersz z pustym 'id' - 'admin2'. Jeśli potrzebujesz tego użytkownika, dodaj mu prawidłowy UUID.
('8707f81a-a15c-439c-a8c8-e03e0b8bd057', 'KPZ2311', 'kapieksperimental@gmail.com', '$2y$12$A8f9ym/UeDURXIZoGwmskuqatDl91PmdYKzoXv0MMLPyD/wyx.6Xq', NULL, NULL, '9ce9b216-8e5b-4b4e-8c52-eb442b8c9238.png', NULL, 'admin', NULL, NULL, 0, 0, NULL, '2025-06-08 20:52:49', '2025-06-08 20:52:49', 0),
('ec626984-44a8-11f0-aafb-74563c6dd840', 'admin', 'admin@challengify.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, NULL, NULL, 'admin', NULL, NULL, 0, 0, NULL, '2025-06-08 22:41:15', NULL, 0);

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
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) -- Dodano PRIMARY KEY tutaj
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) -- Dodano PRIMARY KEY tutaj
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `comments` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `submission_id` char(36) NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_comments_user_id` (`user_id`),
  KEY `fk_comments_submission_id` (`submission_id`),
  CONSTRAINT `fk_comments_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_comments_submission_id` FOREIGN KEY (`submission_id`) REFERENCES `submissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `user_followers` (
  `follower_id` char(36) NOT NULL,
  `following_id` char(36) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`follower_id`,`following_id`),
  CONSTRAINT `fk_follower_user_id` FOREIGN KEY (`follower_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_following_user_id` FOREIGN KEY (`following_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `badges`
--
ALTER TABLE `badges`
  ADD UNIQUE KEY `name` (`name`); -- PRIMARY KEY jest już w CREATE TABLE

--
-- Indeksy dla tabeli `categories`
--
ALTER TABLE `categories`
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `slug` (`slug`); -- PRIMARY KEY jest już w CREATE TABLE

--
-- Indeksy dla tabeli `challenges`
--
ALTER TABLE `challenges`
  ADD UNIQUE KEY `title` (`title`),
  ADD KEY `fk_challenges_user_id` (`user_id`),
  ADD KEY `fk_challenges_category_id` (`category_id`); -- PRIMARY KEY jest już w CREATE TABLE

--
-- Indeksy dla tabeli `phinxlog`
--
ALTER TABLE `phinxlog`
  -- PRIMARY KEY jest już w CREATE TABLE
  ;

--
-- Indeksy dla tabeli `rate_limits`
--
ALTER TABLE `rate_limits`
  ADD UNIQUE KEY `key_ip` (`key`,`ip`); -- PRIMARY KEY jest już w CREATE TABLE

--
-- Indeksy dla tabeli `submissions`
--
ALTER TABLE `submissions`
  ADD UNIQUE KEY `user_challenge` (`user_id`,`challenge_id`),
  ADD KEY `fk_submissions_challenge_id` (`challenge_id`); -- PRIMARY KEY jest już w CREATE TABLE

--
-- Indeksy dla tabeli `users`
--
ALTER TABLE `users`
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`); -- PRIMARY KEY jest już w CREATE TABLE

--
-- Indeksy dla tabeli `user_badges`
--
ALTER TABLE `user_badges`
  ADD UNIQUE KEY `user_badge` (`user_id`,`badge_id`),
  ADD KEY `fk_user_badges_badge_id` (`badge_id`); -- PRIMARY KEY jest już w CREATE TABLE

--
-- Indeksy dla tabeli `votes`
--
ALTER TABLE `votes`
  ADD UNIQUE KEY `user_submission` (`user_id`,`submission_id`),
  ADD KEY `fk_votes_submission_id` (`submission_id`); -- PRIMARY KEY jest już w CREATE TABLE

--
-- AUTO_INCREMENT dla zrzuconych tabel
--

--
-- AUTO_INCREMENT dla tabeli `rate_limits`
--
ALTER TABLE `rate_limits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ograniczenia dla zrzutów tabel
--

--
-- Ograniczenia dla tabeli `challenges`
--
ALTER TABLE `challenges`
  ADD CONSTRAINT `fk_challenges_category_id` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_challenges_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ograniczenia dla tabeli `submissions`
--
ALTER TABLE `submissions`
  ADD CONSTRAINT `fk_submissions_challenge_id` FOREIGN KEY (`challenge_id`) REFERENCES `challenges` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_submissions_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ograniczenia dla tabeli `user_badges`
--
ALTER TABLE `user_badges`
  ADD CONSTRAINT `fk_user_badges_badge_id` FOREIGN KEY (`badge_id`) REFERENCES `badges` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_user_badges_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ograniczenia dla tabeli `votes`
--
ALTER TABLE `votes`
  ADD CONSTRAINT `fk_votes_submission_id` FOREIGN KEY (`submission_id`) REFERENCES `submissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_votes_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;