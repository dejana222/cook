-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 30, 2025 at 04:56 PM
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
-- Database: `kuvajmo`
--

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chat_messages`
--

INSERT INTO `chat_messages` (`id`, `user_id`, `message`, `created_at`) VALUES
(1, 1, 'cao', '2025-04-30 12:34:46'),
(2, 1, 'pozdrav', '2025-04-30 12:34:52'),
(3, 3, 'cc', '2025-04-30 12:36:16'),
(4, 3, 'c', '2025-04-30 12:38:12'),
(5, 3, 'ss', '2025-04-30 14:45:00');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `recipe_id` int(11) DEFAULT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `user_id`, `recipe_id`, `content`, `created_at`) VALUES
(2, 1, 2, 'bravo', '2025-04-25 16:27:55'),
(3, 2, 2, 'cc', '2025-04-28 16:24:23'),
(4, 2, 2, 'c', '2025-04-28 16:24:28'),
(5, 2, 2, 'dd', '2025-04-28 17:50:48'),
(6, 1, 4, 'super', '2025-04-29 11:07:39'),
(7, 1, 3, 'super', '2025-04-30 07:16:50');

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `recipe_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `favorites`
--

INSERT INTO `favorites` (`id`, `user_id`, `recipe_id`, `created_at`) VALUES
(1, 1, 4, '2025-04-25 21:47:11'),
(4, 1, 3, '2025-04-30 07:16:15');

-- --------------------------------------------------------

--
-- Table structure for table `ingredients`
--

CREATE TABLE `ingredients` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ingredients`
--

INSERT INTO `ingredients` (`id`, `name`, `created_at`) VALUES
(1, 's', '2025-04-25 14:14:12'),
(2, 'brašno', '2025-04-25 14:31:18'),
(3, 'šećer', '2025-04-25 14:31:18'),
(4, 'jaja', '2025-04-25 14:31:18'),
(5, 'mlijeko', '2025-04-25 14:31:18'),
(6, 'kvasac', '2025-04-25 14:31:18'),
(7, 'so', '2025-04-25 14:31:18'),
(8, 'ulje', '2025-04-25 14:31:18'),
(9, 'piletina', '2025-04-25 14:31:18'),
(10, 'luk', '2025-04-25 14:31:18'),
(11, 'paprika', '2025-04-25 14:31:18'),
(12, 'paradajz', '2025-04-25 14:31:18'),
(13, 'bijeli luk', '2025-04-25 14:31:18'),
(14, 'pavlaka', '2025-04-25 14:31:18'),
(15, 'tjestenina', '2025-04-25 14:31:18'),
(16, 'sir', '2025-04-25 14:31:18'),
(17, 'šunka', '2025-04-25 14:31:18'),
(18, 'krem sir', '2025-04-25 14:31:18'),
(19, 'krompir', '2025-04-25 14:31:18'),
(20, 'mrkva', '2025-04-25 14:31:18'),
(21, 'peršun', '2025-04-25 14:31:18'),
(22, 'meso', '2025-04-25 14:31:18'),
(23, 'crni biber', '2025-04-25 14:31:18'),
(24, 'mladi luk', '2025-04-25 14:31:18'),
(25, 'origano', '2025-04-25 14:31:18'),
(26, 'bosiljak', '2025-04-25 14:31:18');

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `recipe_id` int(11) DEFAULT NULL,
  `rating` tinyint(4) DEFAULT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ratings`
--

INSERT INTO `ratings` (`id`, `user_id`, `recipe_id`, `rating`, `created_at`) VALUES
(3, 1, 2, 5, '2025-04-25 16:27:43'),
(4, 2, 2, 5, '2025-04-28 17:50:41'),
(5, 1, 4, 5, '2025-04-29 11:07:31'),
(6, 1, 3, 5, '2025-04-30 07:16:32'),
(7, 3, 12, 5, '2025-04-30 13:56:21');

-- --------------------------------------------------------

--
-- Table structure for table `recipes`
--

CREATE TABLE `recipes` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `instructions` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recipes`
--

INSERT INTO `recipes` (`id`, `title`, `description`, `instructions`, `image`, `user_id`, `created_at`) VALUES
(2, 'Domaće kiflice', 'Mekane i mirisne kiflice idealne za doručak.', 'Pomiješati brašno, kvasac, šećer i so. \nDodati mlijeko, jaja i ulje, pa zamijesiti tijesto. \nOstaviti tijesto da naraste 1h. \nOblikovati kiflice, filovati navedenim ili željenim sastojcima i peći na 180°C oko 20 minuta.', 'kiflice.jpg', 1, '2025-04-25 14:31:18'),
(3, 'Pileći paprikaš', 'Tradicionalni paprikaš sa piletinom i pavlakom.', 'Ispržiti luk i bijeli luk na ulju.\nDodati piletinu i dinstati 10 minuta.\nUbaciti papriku, paradajz i začine, pa krčkati 30 minuta. Na kraju dodati pavlaku i promiješati.', 'paprikas.jpg', 1, '2025-04-25 14:31:18'),
(4, 'Kremasta tjestenina sa šunkom', 'Brza tjestenina sa kremastim sosom.', 'Skuvati tjesteninu prema uputstvu. \nNa ulju propržiti šunku i mladi luk. \nDodati krem sir i pavlaku, pa miješati dok se ne zgusne. Pomiješati sa tjesteninom i posuti sirom.', 'testenina.jpg', 1, '2025-04-25 14:31:18'),
(5, 'Krompir salata', 'Osvježavajuća salata od krompira i povrća.', 'Skuvati krompir i isjeći na kockice. Dodati sjeckanu mrkvu, luk i peršun. Začiniti uljem, soli i biberom, pa promiješati.', 'krompir.jpg', 1, '2025-04-25 14:31:18'),
(6, 'Italijanska pica', 'Domaća pica sa svježim sastojcima.', 'Zamijesiti tijesto od brašna, kvasca, vode, ulja i soli. Ostaviti tijesto da naraste 1h. Izvaljati u željeni oblik. Premazati paradajz sosom, dodati sir, šunku, origano i bosiljak. Peći na 220°C oko 15 minuta.', 'pica.jpg', 1, '2025-04-25 14:31:18'),
(12, 'Cupavci', 'Stari bakin recept', 'Odvojite belanca i žumanca. Belanca umutite sa polovinom šećera (100 g) u čvrst sneg i ostavite sa strane. Umutite i žumanca penasto sa preostalim šećerom i vanilin šećerom. Dodajte brašno pomešano sa praškom za pecivo, kao i mleko i sve dobro sjedinite mikserom. Na kraju lagano umešajte i sneg od belanaca, prevrćući špatulom.\r\n\r\n\r\nSmesu za biskvit izlijte u pleh (32x20 cm) koji ste prethodno malo nauljili i posuli brašnom, i pecite 20-25 minuta u prethodno zagrejanoj pećnici na 180 stepeni, dok biskvit ne dobije lepu zlatno žutu boju.\r\n\r\nProhladite ga i secite na kocke željene veličine. Moje su bile oko 3,5 -4 cm.\r\n\r\n\r\n\r\nStavite u šerpu mleko, čokoladu izlomljenu na kocke, kakao, šećer i margarin i zagrevajte uz mešanje dok se sve ne rastopi i sjedini u jednoličnu masu. Malo samo prohladite, pa kocke od biskvita kašikom spuštajte u još topli umak, da se namoče sa svih strana, a onda ih spuštajte u tanjir sa kokosom tako da se kokos lepo zalepi sa svih strana.\r\n\r\n\r\nČupavce poređajte na tacnu i stavite u frižider da se stegnu.', '681223fc7af9b.jpg', 3, '2025-04-30 13:22:04');

-- --------------------------------------------------------

--
-- Table structure for table `recipe_ingredient`
--

CREATE TABLE `recipe_ingredient` (
  `id` int(11) NOT NULL,
  `recipe_id` int(11) DEFAULT NULL,
  `ingredient_id` int(11) DEFAULT NULL,
  `quantity` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recipe_ingredient`
--

INSERT INTO `recipe_ingredient` (`id`, `recipe_id`, `ingredient_id`, `quantity`, `created_at`) VALUES
(9, 2, 9, '500g', '2025-04-25 14:31:18'),
(10, 2, 10, '2 komada', '2025-04-25 14:31:18'),
(11, 2, 11, '1 komad', '2025-04-25 14:31:18'),
(12, 2, 12, '2 komada', '2025-04-25 14:31:18'),
(13, 2, 13, '3 komada', '2025-04-25 14:31:18'),
(14, 2, 14, '200ml', '2025-04-25 14:31:18'),
(15, 2, 8, '2 kašike', '2025-04-25 14:31:18'),
(16, 2, 7, 'po ukusu', '2025-04-25 14:31:18'),
(17, 2, 23, 'po ukusu', '2025-04-25 14:31:18'),
(18, 4, 15, '300g', '2025-04-25 14:31:18'),
(19, 4, 17, '150g', '2025-04-25 14:31:18'),
(20, 3, 18, '100g', '2025-04-25 14:31:18'),
(21, 3, 14, '150ml', '2025-04-25 14:31:18'),
(22, 3, 16, '100g', '2025-04-25 14:31:18'),
(23, 3, 24, '1 struk', '2025-04-25 14:31:18'),
(24, 3, 8, '1 kašika', '2025-04-25 14:31:18'),
(25, 4, 19, '1kg', '2025-04-25 14:31:18'),
(26, 4, 20, '2 komada', '2025-04-25 14:31:18'),
(27, 4, 10, '1 komad', '2025-04-25 14:31:18'),
(28, 4, 21, '1 kašika', '2025-04-25 14:31:18'),
(29, 4, 8, '3 kašike', '2025-04-25 14:31:18'),
(30, 4, 7, 'po ukusu', '2025-04-25 14:31:18'),
(31, 4, 23, 'po ukusu', '2025-04-25 14:31:18'),
(32, 5, 2, '400g', '2025-04-25 14:31:18'),
(33, 5, 6, '1 kesica', '2025-04-25 14:31:18'),
(34, 5, 8, '2 kašike', '2025-04-25 14:31:18'),
(35, 5, 7, '1 kašičica', '2025-04-25 14:31:18'),
(36, 5, 12, '200g (sos)', '2025-04-25 14:31:18'),
(37, 5, 16, '150g', '2025-04-25 14:31:18'),
(38, 5, 17, '100g', '2025-04-25 14:31:18'),
(39, 5, 25, '1 kašičica', '2025-04-25 14:31:18'),
(40, 5, 26, '5 listova', '2025-04-25 14:31:18'),
(44, 12, 8, '150ml', '2025-04-30 13:22:04');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `created_at`) VALUES
(1, 'Dejana Zoric', 'anlaisuh@gmail.com', '$2y$10$NG9LeWiE1x.xKplh7nTLvum3yWVWNHgyBFL7ZFpXHCEQs9E8BxzjW', '2025-04-25 14:13:38'),
(2, 'Dejana Zoric', 'haha@gmail.com', '$2y$10$WcjO6mqXp1aEz0n8IDdVR.qTi6fss9403rNeUkdjMOlDo35CnWi3y', '2025-04-28 16:23:14'),
(3, 'Dejana', 'dejana@gmail.com', '$2y$10$6Q9lH/6X0DwUSZygrF8H3e0zCZuo5ToXimmFCuDBdHLPZF2MmoRgO', '2025-04-30 12:36:03');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `recipe_id` (`recipe_id`);

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_favorite` (`user_id`,`recipe_id`),
  ADD KEY `recipe_id` (`recipe_id`);

--
-- Indexes for table `ingredients`
--
ALTER TABLE `ingredients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`recipe_id`),
  ADD KEY `recipe_id` (`recipe_id`);

--
-- Indexes for table `recipes`
--
ALTER TABLE `recipes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `recipe_ingredient`
--
ALTER TABLE `recipe_ingredient`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recipe_id` (`recipe_id`),
  ADD KEY `ingredient_id` (`ingredient_id`);

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
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `ingredients`
--
ALTER TABLE `ingredients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `recipes`
--
ALTER TABLE `recipes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `recipe_ingredient`
--
ALTER TABLE `recipe_ingredient`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD CONSTRAINT `chat_messages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `ratings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ratings_ibfk_2` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `recipes`
--
ALTER TABLE `recipes`
  ADD CONSTRAINT `recipes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `recipe_ingredient`
--
ALTER TABLE `recipe_ingredient`
  ADD CONSTRAINT `recipe_ingredient_ibfk_1` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `recipe_ingredient_ibfk_2` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
