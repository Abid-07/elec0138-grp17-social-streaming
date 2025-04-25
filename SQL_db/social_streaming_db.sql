-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 21, 2025 at 12:43 AM
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
-- Database: `social_streaming_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `video_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `video_id`, `user_id`, `comment_text`, `created_at`) VALUES
(1, 1, 1, 'First time commenter!', '2025-03-15 15:45:04'),
(2, 5, 1, '3RIGJ4OIJG', '2025-03-16 14:43:28'),
(3, 1, 9, 'Great video!', '2025-04-15 19:25:15'),
(4, 7, 9, 'Testing commenting!', '2025-04-20 20:04:39');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0,
  `ai_flag` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `message`, `sent_at`, `is_read`, `ai_flag`) VALUES
(1, 1, 3, 'Hello my friend', '2025-03-15 21:42:58', 1, NULL),
(2, 1, 2, 'dmdckmwkl', '2025-03-15 23:15:50', 0, NULL),
(3, 1, 2, 'Hello my friend!', '2025-03-15 23:18:34', 0, NULL),
(4, 1, 2, 'hi', '2025-03-15 23:19:18', 0, NULL),
(5, 1, 2, 'hi', '2025-03-15 23:19:35', 0, NULL),
(6, 1, 3, 'hi brother!', '2025-03-15 23:22:02', 1, NULL),
(7, 1, 3, 'hi brother!', '2025-03-15 23:22:11', 1, NULL),
(8, 1, 2, 'Hello mate this is a new message', '2025-03-15 23:27:28', 0, NULL),
(9, 3, 1, 'Yello my friend!', '2025-03-15 23:57:44', 1, NULL),
(10, 3, 1, 'Yello my friend!', '2025-03-15 23:57:51', 1, NULL),
(11, 3, 1, 'How are you doing my friend?', '2025-03-16 00:14:42', 1, NULL),
(12, 3, 1, 'How is the crypto?', '2025-03-16 00:18:26', 1, NULL),
(13, 1, 3, 'Not bad my guy', '2025-03-16 00:19:46', 1, NULL),
(14, 1, 3, 'Can you send me the money?', '2025-03-16 00:22:51', 1, NULL),
(15, 3, 1, 'Yeah just give me time!', '2025-03-16 00:28:55', 1, NULL),
(16, 1, 3, 'Last chance!', '2025-03-16 00:30:14', 1, NULL),
(17, 3, 1, 'Ok fine! Here you go!', '2025-03-16 00:36:38', 1, NULL),
(18, 1, 3, 'good', '2025-03-16 00:48:27', 1, NULL),
(19, 1, 3, 'SEND ME MY MONEY', '2025-03-16 14:44:03', 1, NULL),
(20, 3, 1, 'rfhjenjvennerln NO', '2025-03-16 14:44:51', 1, NULL),
(21, 1, 3, 'This is an automated message sent from Python!', '2025-03-26 16:18:55', 1, NULL),
(22, 8, 3, 'This is an automated message sent from Python!', '2025-03-26 16:22:41', 1, NULL),
(23, 8, 3, 'Dear Michael, your account has been compromised. Please verify your details here: [malicious_link]', '2025-03-26 16:23:37', 1, NULL),
(24, 8, 3, 'Dear David Johnson, your account has been compromised. Please verify your details here: [malicious_link]\n\nThe following is a list of addresses, and you should know the address of your chosen domain. If you cannot locate the address of your choice, you can create a new one by going to https://[id]:00/[address]:00/address/ [email protected]', '2025-03-26 16:25:03', 1, NULL),
(25, 8, 3, 'Dear Michael Johnson,\n\n    We have exciting updates for you! Your **Comedy** subscription has been upgraded to **Premium**. \n    Click below to enjoy exclusive content from top creators like Goldmines, Movieclips, YRF. \n\n    Don\'t miss out on new videos, hot releases, and special perks only available for premium users.\n\n    [malicious_link]\n\n    Best regards,\n    Site Admin\n    \n\n\nMILWAUKEE, WI - JANUARY 18: Jennifer Lopez attends the \"Happy Endings\" musical performance on February 17, 2012 in Milwaukee, Wisconsin. (Photo', '2025-03-26 22:53:24', 1, NULL),
(26, 1, 9, 'Hello root, please confirm your account at: http://192.168.1.48/', '2025-04-08 20:01:53', 1, NULL),
(27, 9, 1, 'Yeah you can also check out: http://192.168.1.48/login.php', '2025-04-08 20:04:03', 1, NULL),
(28, 9, 1, 'Yeah you can also check out: http://192.168.1.48/login.php', '2025-04-08 20:19:38', 1, NULL),
(29, 9, 1, 'Hello. Please confirm your email at: http://192.168.1.48/', '2025-04-08 20:20:55', 1, NULL),
(30, 1, 9, 'http://192.168.1.48/', '2025-04-08 20:37:16', 1, NULL),
(31, 9, 1, 'Check it out: http://192.168.1.48/social_streaming', '2025-04-08 20:41:40', 1, NULL),
(32, 1, 9, 'http://192.168.1.48/', '2025-04-08 20:46:35', 1, NULL),
(33, 1, 9, 'check out: http://192.168.1.48/', '2025-04-08 20:58:17', 1, NULL),
(34, 1, 3, 'http://192.168.1.48/', '2025-04-08 21:05:54', 1, NULL),
(35, 1, 9, 'http://192.168.1.48/', '2025-04-08 21:14:14', 1, NULL),
(36, 1, 9, 'http://192.168.1.48/', '2025-04-08 21:17:10', 1, NULL),
(37, 1, 8, '\"Your account is suspended, click http://192.168.1.1/login\"', '2025-04-08 21:22:56', 0, 'Unknown'),
(38, 1, 8, 'Please confirm at http://192.168.1.1/login', '2025-04-08 21:26:43', 0, 'Unknown'),
(39, 1, 8, 'Please confirm at http://192.168.1.1/login, newnew', '2025-04-08 21:28:00', 0, 'Unknown'),
(40, 1, 9, 'Please confirm at http://192.168.1.1/login', '2025-04-08 21:30:15', 1, 'Unknown'),
(41, 1, 9, 'http://192.168.1.48/', '2025-04-08 21:32:39', 1, 'Unknown'),
(42, 1, 8, 'http://192.168.1.48/', '2025-04-08 21:35:21', 0, 'Unknown'),
(43, 1, 8, 'http://192.168.1.48/', '2025-04-08 21:40:31', 0, 'Unknown'),
(44, 1, 3, 'http://192.168.1.48/', '2025-04-08 21:43:55', 1, 'Unknown'),
(45, 1, 8, 'new message http://192.168.1.48', '2025-04-08 21:46:45', 0, 'Unknown'),
(46, 1, 9, 'http://192.168.1.48/', '2025-04-08 21:53:17', 1, NULL),
(47, 1, 8, 'http://192.168.1.48/', '2025-04-08 21:59:18', 0, NULL),
(48, 1, 8, 'http://192.168.1.48', '2025-04-08 22:00:57', 0, NULL),
(49, 1, 8, 'http://192.168.1.48', '2025-04-08 22:02:49', 0, NULL),
(50, 1, 8, 'http://192.168.1.48', '2025-04-08 22:06:46', 0, 'Phishing Email'),
(51, 9, 1, 'Hey, check this out: http://192.168.1.48/', '2025-04-08 22:16:44', 1, 'Phishing Email'),
(52, 1, 9, 'Hey, i\'m not silly, this is correct: https://google.com/accounts/', '2025-04-08 22:18:13', 1, NULL),
(53, 1, 9, 'check this link: http://192.168.1.48', '2025-04-08 22:21:16', 1, NULL),
(54, 1, 9, 'check this link: 192.168.1.48', '2025-04-08 22:22:24', 1, 'Phishing Email'),
(55, 1, 2, 'beware, this is a url: http://192.168.1.48', '2025-04-08 22:25:55', 0, 'Phishing Email'),
(56, 1, 8, 'http://192.168.1.48', '2025-04-08 22:26:48', 0, 'Phishing Email'),
(57, 3, 9, 'Hey, this is a reminder to check out these videos before they expire. Half Price Now! http://192.168.1.48/social_streaming/view_comments.php!', '2025-04-20 18:44:54', 1, 'Phishing Email'),
(58, 9, 1, 'Hi! I loved your last video!', '2025-04-20 18:52:31', 0, 'Safe Email');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `user_id`, `content`, `created_at`, `image`) VALUES
(1, 1, 'This is the first forum post!', '2025-03-15 16:01:37', NULL),
(2, 1, 'New day!', '2025-03-15 16:12:04', '1742055124_dog.jpeg'),
(3, 1, 'HOW ARE YOU', '2025-03-16 14:43:09', NULL),
(4, 1, 'What a great day!', '2025-03-27 15:48:27', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `purchases`
--

CREATE TABLE `purchases` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `video_id` int(11) NOT NULL,
  `purchase_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchases`
--

INSERT INTO `purchases` (`id`, `user_id`, `video_id`, `purchase_date`) VALUES
(1, 1, 6, '2025-03-18 00:35:18'),
(2, 1, 6, '2025-03-18 00:35:43'),
(3, 1, 1, '2025-03-18 00:41:43'),
(4, 3, 5, '2025-03-18 00:42:48'),
(5, 3, 6, '2025-03-18 00:45:31'),
(6, 9, 6, '2025-03-18 00:47:00'),
(7, 8, 6, '2025-03-18 00:50:11'),
(8, 13, 3, '2025-03-18 00:54:03'),
(9, 13, 6, '2025-03-18 00:58:19'),
(10, 1, 7, '2025-04-10 23:04:26'),
(11, 9, 7, '2025-04-15 17:29:57');

-- --------------------------------------------------------

--
-- Table structure for table `used_tokens`
--

CREATE TABLE `used_tokens` (
  `jti` varchar(64) NOT NULL,
  `used_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `used_tokens`
--

INSERT INTO `used_tokens` (`jti`, `used_at`) VALUES
('7e84e9757ae2f02060a19604584195f7', '2025-04-08 11:54:20'),
('ed11b5fe05d1abc1e880efdef5f73722', '2025-04-20 19:41:56'),
('f1b27800ad1d5af448e7abdc40704c05', '2025-04-20 19:41:08');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `bio` text DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `stream_video` varchar(255) DEFAULT NULL,
  `credits` int(11) DEFAULT 0,
  `name` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `subscription_type` varchar(50) DEFAULT NULL,
  `preferred_genre` varchar(50) DEFAULT NULL,
  `last_login` date DEFAULT NULL,
  `mfa_secret` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `bio`, `profile_picture`, `stream_video`, `credits`, `name`, `country`, `subscription_type`, `preferred_genre`, `last_login`, `mfa_secret`) VALUES
(1, 'victim', 'testuser@example.com', '$2y$10$b1rZ6sXwCBneyrB1IMjnA.1g/2yLwR5RwlFj1HvSan6Gpiu1Dz7yq', 'I like fishing!', 'Screenshot 2025-01-16 132957.png', 'uploads/videos/1_live_stream.mp4', 2251, NULL, NULL, NULL, NULL, NULL, 'MYIWCQJBEN5JPXC4'),
(2, 'testuser', 'testuser@example.com', '$2y$10$tnexpnHf5SQDRxN.rQ7zuONIX.Jp16Z12t9GL9Qb3tmi8bB.G3IjO', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'testuser2', 'testuser2@example.com', '$2y$10$60bFCPaI5MO276s7xDvCIenGLZGxy97SCHttaEIw2bwd.X0Fdg4l.', 'I am the second user.', 'Screenshot 2025-01-16 150232.png', NULL, 899, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 'testuser2', 'testuser2@example.com', '$2y$10$GtKLSwNMFxtjm7hq9hyEyOClzjnBTIoj/JJZ5CNubyPfGQUOCVrve', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(5, 'testuser2', 'testuser2@example.com', '$2y$10$i7FHdB7dl8HvW8zb/Xtdm.33Fd/7f/yFtCtJsFniHOwHxj2n0b0he', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(7, '', '', '$2y$10$yahKoU0HSc4nVXgczk6I6Obk67MlCo8WcCD1dew4IoGSztqJur/TK', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(8, 'admin', 'admin@example.com', '$2y$10$jt/Om4JRM8Uxzco9qxwal.TXCp9MLtaityfSs/8Y7dGl.8BAaBlIm', NULL, NULL, NULL, 200, NULL, NULL, NULL, NULL, NULL, NULL),
(9, 'attacker', 'root@example.com', '$2y$10$UGzgnRmDDhew9FTpSJaFeeipL8OVDHLusgwtDGkqFNbwnvL3HP2AG', NULL, NULL, NULL, 1000, NULL, NULL, NULL, NULL, NULL, 'ZBE676IC7NSSJTG7'),
(10, 'guest', 'guest@example.com', '$2y$10$CGDLsYQVgkKv3PRK1A0k.e4Hv4vJC0xbR4tS2yOBX4u4wAIj4ZGRq', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(11, 'kaliuser', 'kaliuser@example.com', '$2y$10$wDIGlFVLf1YcDQM39ClOHeGOPv7DNWULRtoGbjjihJSZXKBG5W.h2', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(12, 'test3', 'zceetwe@ucl.ac.uk', '$2y$10$J0o5ysHqxBgU8OeGvdOxTOzDMiabhvYs13ly1/.b35RY5SJgSrY5C', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(13, 'newuser', 'new@new.com', '$2y$10$9b6xFFkEdWo2u32dP82R6OIvVK6k/RNPO6xig3ag.zRZL/VTqQqYi', NULL, NULL, NULL, 200, NULL, NULL, NULL, NULL, NULL, NULL),
(96854, 'jamesmartinez1', 'j.martinez@viewtube.org', 'JamesM18#', NULL, NULL, NULL, 0, 'James Martinez', 'France', 'Premium', 'Drama', '2024-05-12', NULL),
(96855, 'johnmiller2', 'j.miller@mail.com', 'JohnM23@', NULL, NULL, NULL, 0, 'John Miller', 'USA', 'Premium', 'Sci-Fi', '2025-02-05', NULL),
(96856, 'emmadavis3', 'e.davis@viewtube.org', 'EmmaD60!', NULL, NULL, NULL, 0, 'Emma Davis', 'UK', 'Basic', 'Comedy', '2025-01-24', NULL),
(96857, 'emmamiller4', 'e.miller@streamhub.net', 'EmmaM44@', NULL, NULL, NULL, 0, 'Emma Miller', 'USA', 'Premium', 'Documentary', '2024-03-25', NULL),
(96858, 'janesmith5', 'j.smith@webstream.net', 'JaneS68!', NULL, NULL, NULL, 0, 'Jane Smith', 'USA', 'Standard', 'Drama', '2025-01-14', NULL),
(96859, 'davidjohnson6', 'd.johnson@streamhub.net', 'DavidJ21@', NULL, NULL, NULL, 0, 'David Johnson', 'USA', 'Standard', 'Romance', '2025-02-03', NULL),
(96860, 'johnhernandez7', 'j.hernandez@mail.com', 'JohnH57#', NULL, NULL, NULL, 0, 'John Hernandez', 'Canada', 'Standard', 'Romance', '2025-01-05', NULL),
(96861, 'katiehernandez8', 'k.hernandez@webstream.net', 'KatieH68#', NULL, NULL, NULL, 0, 'Katie Hernandez', 'USA', 'Standard', 'Sci-Fi', '2024-10-30', NULL),
(96862, 'jameswilliams9', 'j.williams@webstream.net', 'JamesW39!', NULL, NULL, NULL, 0, 'James Williams', 'UK', 'Basic', 'Action', '2024-04-16', NULL),
(96863, 'alexdavis10', 'a.davis@viewtube.org', 'AlexD55#', NULL, NULL, NULL, 0, 'Alex Davis', 'Mexico', 'Standard', 'Horror', '2024-07-03', NULL),
(96864, 'janemiller11', 'j.miller@webstream.net', 'JaneM26#', NULL, NULL, NULL, 0, 'Jane Miller', 'Japan', 'Standard', 'Action', '2024-09-11', NULL),
(96865, 'janemartinez12', 'j.martinez@flixmail.com', 'JaneM62!', NULL, NULL, NULL, 0, 'Jane Martinez', 'USA', 'Standard', 'Action', '2024-06-12', NULL),
(96866, 'alexmartinez13', 'a.martinez@streamhub.net', 'AlexM65!', NULL, NULL, NULL, 0, 'Alex Martinez', 'Australia', 'Premium', 'Comedy', '2024-08-08', NULL),
(96867, 'alexsmith14', 'a.smith@webstream.net', 'AlexS35#', NULL, NULL, NULL, 0, 'Alex Smith', 'Germany', 'Premium', 'Sci-Fi', '2024-10-02', NULL),
(96868, 'michaeljones15', 'm.jones@flixmail.com', 'MichaelJ68#', NULL, NULL, NULL, 0, 'Michael Jones', 'USA', 'Premium', 'Sci-Fi', '2024-07-25', NULL),
(96869, 'chrismiller16', 'c.miller@flixmail.com', 'ChrisM39@', NULL, NULL, NULL, 0, 'Chris Miller', 'Australia', 'Premium', 'Comedy', '2024-10-11', NULL),
(96870, 'chrisdavis17', 'c.davis@streamhub.net', 'ChrisD74#', NULL, NULL, NULL, 0, 'Chris Davis', 'UK', 'Standard', 'Drama', '2024-07-03', NULL),
(96871, 'emmawilliams18', 'e.williams@streamhub.net', 'EmmaW59!', NULL, NULL, NULL, 0, 'Emma Williams', 'Canada', 'Premium', 'Documentary', '2024-08-08', NULL),
(96872, 'alexjohnson19', 'a.johnson@viewtube.org', 'AlexJ63!', NULL, NULL, NULL, 0, 'Alex Johnson', 'Brazil', 'Premium', 'Action', '2024-08-08', NULL),
(96873, 'johnjones20', 'j.jones@streamhub.net', 'JohnJ67!', NULL, NULL, NULL, 0, 'John Jones', 'Canada', 'Standard', 'Comedy', '2024-12-07', NULL),
(96874, 'michaelwilliams21', 'm.williams@mail.com', 'MichaelW47#', NULL, NULL, NULL, 0, 'Michael Williams', 'France', 'Standard', 'Horror', '2025-01-21', NULL),
(96875, 'jamesmartinez22', 'j.martinez@flixmail.com', 'JamesM46@', NULL, NULL, NULL, 0, 'James Martinez', 'UK', 'Premium', 'Comedy', '2024-04-19', NULL),
(96876, 'sarahdavis23', 's.davis@flixmail.com', 'SarahD40!', NULL, NULL, NULL, 0, 'Sarah Davis', 'UK', 'Basic', 'Drama', '2024-04-08', NULL),
(96877, 'johnsmith24', 'j.smith@viewtube.org', 'JohnS46!', NULL, NULL, NULL, 0, 'John Smith', 'UK', 'Basic', 'Horror', '2024-10-16', NULL),
(96878, 'alexbrown25', 'a.brown@viewtube.org', 'AlexB37!', NULL, NULL, NULL, 0, 'Alex Brown', 'Canada', 'Basic', 'Horror', '2024-05-26', NULL),
(96879, 'chrisjohnson26', 'c.johnson@mail.com', 'ChrisJ26#', NULL, NULL, NULL, 0, 'Chris Johnson', 'Brazil', 'Premium', 'Sci-Fi', '2024-08-23', NULL),
(96880, 'jamesjones27', 'j.jones@viewtube.org', 'JamesJ17@', NULL, NULL, NULL, 0, 'James Jones', 'Mexico', 'Standard', 'Documentary', '2024-09-29', NULL),
(96881, 'michaeljohnson28', 'm.johnson@flixmail.com', 'MichaelJ37@', NULL, NULL, NULL, 0, 'Michael Johnson', 'France', 'Premium', 'Comedy', '2025-01-07', NULL),
(96882, 'michaeljohnson29', 'm.johnson@streamhub.net', 'MichaelJ59!', NULL, NULL, NULL, 0, 'Michael Johnson', 'Mexico', 'Basic', 'Drama', '2024-11-03', NULL),
(96883, 'sarahsmith30', 's.smith@streamhub.net', 'SarahS67@', NULL, NULL, NULL, 0, 'Sarah Smith', 'Australia', 'Basic', 'Action', '2025-02-11', NULL),
(96884, 'chrisjones31', 'c.jones@webstream.net', 'ChrisJ14#', NULL, NULL, NULL, 0, 'Chris Jones', 'Germany', 'Premium', 'Drama', '2024-08-06', NULL),
(96885, 'johnwilliams32', 'j.williams@flixmail.com', 'JohnW27@', NULL, NULL, NULL, 0, 'John Williams', 'Canada', 'Standard', 'Sci-Fi', '2025-02-01', NULL),
(96886, 'janebrown33', 'j.brown@flixmail.com', 'JaneB59#', NULL, NULL, NULL, 0, 'Jane Brown', 'UK', 'Standard', 'Sci-Fi', '2024-07-21', NULL),
(96887, 'daviddavis34', 'd.davis@mail.com', 'DavidD56#', NULL, NULL, NULL, 0, 'David Davis', 'Mexico', 'Basic', 'Documentary', '2024-07-11', NULL),
(96888, 'emmahernandez35', 'e.hernandez@streamhub.net', 'EmmaH52#', NULL, NULL, NULL, 0, 'Emma Hernandez', 'India', 'Basic', 'Sci-Fi', '2024-12-04', NULL),
(96889, 'michaeljones36', 'm.jones@flixmail.com', 'MichaelJ26@', NULL, NULL, NULL, 0, 'Michael Jones', 'UK', 'Basic', 'Comedy', '2024-06-06', NULL),
(96890, 'sarahwilliams37', 's.williams@webstream.net', 'SarahW64#', NULL, NULL, NULL, 0, 'Sarah Williams', 'Germany', 'Basic', 'Documentary', '2024-06-28', NULL),
(96891, 'davidmartinez38', 'd.martinez@viewtube.org', 'DavidM80!', NULL, NULL, NULL, 0, 'David Martinez', 'Brazil', 'Premium', 'Drama', '2024-08-08', NULL),
(96892, 'emmasmith39', 'e.smith@streamhub.net', 'EmmaS70#', NULL, NULL, NULL, 0, 'Emma Smith', 'USA', 'Standard', 'Comedy', '2024-10-27', NULL),
(96893, 'sarahhernandez40', 's.hernandez@viewtube.org', 'SarahH15!', NULL, NULL, NULL, 0, 'Sarah Hernandez', 'Brazil', 'Premium', 'Horror', '2024-04-30', NULL),
(96894, 'johnjones41', 'j.jones@streamhub.net', 'JohnJ23!', NULL, NULL, NULL, 0, 'John Jones', 'Germany', 'Standard', 'Romance', '2024-10-29', NULL),
(96895, 'jameswilliams42', 'j.williams@streamhub.net', 'JamesW40@', NULL, NULL, NULL, 0, 'James Williams', 'Canada', 'Premium', 'Romance', '2024-10-02', NULL),
(96896, 'davidbrown43', 'd.brown@webstream.net', 'DavidB35!', NULL, NULL, NULL, 0, 'David Brown', 'Canada', 'Premium', 'Documentary', '2024-06-08', NULL),
(96897, 'chrisgarcia44', 'c.garcia@flixmail.com', 'ChrisG68@', NULL, NULL, NULL, 0, 'Chris Garcia', 'Japan', 'Basic', 'Documentary', '2024-04-12', NULL),
(96898, 'chrisjones45', 'c.jones@streamhub.net', 'ChrisJ50!', NULL, NULL, NULL, 0, 'Chris Jones', 'USA', 'Basic', 'Action', '2024-03-30', NULL),
(96899, 'johnjohnson46', 'j.johnson@flixmail.com', 'JohnJ45@', NULL, NULL, NULL, 0, 'John Johnson', 'India', 'Standard', 'Drama', '2024-11-01', NULL),
(96900, 'sarahsmith47', 's.smith@viewtube.org', 'SarahS16#', NULL, NULL, NULL, 0, 'Sarah Smith', 'Brazil', 'Premium', 'Documentary', '2024-04-13', NULL),
(96901, 'sarahgarcia48', 's.garcia@mail.com', 'SarahG40@', NULL, NULL, NULL, 0, 'Sarah Garcia', 'Australia', 'Basic', 'Comedy', '2024-10-19', NULL),
(96902, 'michaelbrown49', 'm.brown@mail.com', 'MichaelB57!', NULL, NULL, NULL, 0, 'Michael Brown', 'Australia', 'Premium', 'Sci-Fi', '2024-09-18', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `videos`
--

CREATE TABLE `videos` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `filename` varchar(255) NOT NULL,
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `file_path` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `price_in_credits` int(11) NOT NULL DEFAULT 0,
  `is_live` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `videos`
--

INSERT INTO `videos` (`id`, `user_id`, `title`, `description`, `filename`, `upload_date`, `file_path`, `uploaded_at`, `price_in_credits`, `is_live`) VALUES
(1, 1, 'Fish are captivating!', 'This is my debut!', '3637206605-preview.mp4', '2025-03-08 21:32:16', '', '2025-03-09 03:53:17', 0, 0),
(3, 1, 'Cheeky', '3rd time lucky!', '1097349773-preview.mp4', '2025-03-08 21:36:00', '', '2025-03-09 03:53:17', 0, 0),
(4, 3, 'Joining the Trend', '', '1097349773-preview.mp4', '2025-03-08 23:32:45', '', '2025-03-09 03:53:17', 0, 0),
(5, 3, 'Copy cat', '', '1097349773-preview.mp4', '2025-03-09 04:04:49', '', '2025-03-09 04:04:49', 0, 0),
(6, 1, 'PAID ', '', '16166-269541539_tiny.mp4', '2025-03-17 23:17:58', 'uploads/videos/16166-269541539_tiny.mp4', '2025-03-17 23:17:58', 200, 0),
(7, 9, 'jejnsdeckedo', '', '16166-269541539_tiny.mp4', '2025-03-29 22:40:05', 'uploads/videos/16166-269541539_tiny.mp4', '2025-03-29 22:40:05', 200, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `video_id` (`video_id`);

--
-- Indexes for table `used_tokens`
--
ALTER TABLE `used_tokens`
  ADD PRIMARY KEY (`jti`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `videos`
--
ALTER TABLE `videos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=96903;

--
-- AUTO_INCREMENT for table `videos`
--
ALTER TABLE `videos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchases`
--
ALTER TABLE `purchases`
  ADD CONSTRAINT `purchases_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `purchases_ibfk_2` FOREIGN KEY (`video_id`) REFERENCES `videos` (`id`);

--
-- Constraints for table `videos`
--
ALTER TABLE `videos`
  ADD CONSTRAINT `videos_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
