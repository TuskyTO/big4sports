-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 06, 2025 at 07:09 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `app-db`
--

-- --------------------------------------------------------

--
-- Table structure for table `trivia`
--

CREATE TABLE `trivia` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `trivia_question` varchar(255) NOT NULL,
  `trivia_answer` varchar(255) NOT NULL,
  `difficulty` int(1) NOT NULL CHECK (`difficulty` between 1 and 10)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trivia`
--

INSERT INTO `trivia` (`id`, `username`, `trivia_question`, `trivia_answer`, `difficulty`) VALUES
(1, 'charlieknapp', 'Who won the first NFL Superbowl?', 'Green Bay Packers', 8),
(2, 'charlieknapp', 'What does NHL stand for?', 'National Hockey League', 1),
(4, 'egoldman', 'Who is the NBA all-time assist leader?', 'John Stockton', 6),
(5, 'egoldman', 'Who was the first pick in the 2003 NBA draft?', 'Lebron James', 6),
(7, 'conorselfridge', 'Who won the World Series in 2016?', 'Chicago Cubs', 6),
(8, 'conorselfridge', 'Who is the G.O.A.T of the NBA?', 'Michael Jordan', 1),
(16, 'triviamaster0', 'Who holds the record for most rushing yards in a season including playoffs?', 'Saquon Barkley', 5),
(18, 'samir1', 'How many players play defense on a baseball diamond?', '9', 2),
(19, 'samir1', 'What is the all-time AL HR record?', '62', 8);


-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`username`, `password`) VALUES
('charlieknapp', '$2y$10$hzf7s8W150UsHgATvq7B/OAl6kNjR2YqWJ379QJ8aJCYET3w4MyFa'),
('charlieknapp2', '$2y$10$IcXXUDIYsyqhytA/SQFPBex5TF.ZX6L8i1iIoriNDx4PwRDEH51u2'),
('conorselfridge', '$2y$10$HDYU.ask/ExRL4I3AWqTYOoIGwkKp93MJ78zQTqRV5EehieaDg3cy'),
('egoldman', '$2y$10$HcqC/NrmtB6GTKMbRqOy1O3nhzxnz6o2h3zbOgAozGMiqgYRYunii'),
('samir1', '$2y$10$XxaD1rb0AG7GsoDczgv1fOrFGoF4Ha3UcJSpVg7zv9nEHAkCkqrru'),
('samir2', '$2y$10$OjCUctNztd5L/B5yWWSwYeNwIJO4Wn13bJMBwL7nR7U58XOU2NWAO'),
('triviamaster0', '$2y$10$XIKk0XnmWHYOgvtgSYqH8Oos4Fg4Ru7t8lSRswn027s6RPeS1horW');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `trivia`
--
ALTER TABLE `trivia`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `trivia`
--
ALTER TABLE `trivia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `trivia`
--
ALTER TABLE `trivia`
  ADD CONSTRAINT `trivia_ibfk_1` FOREIGN KEY (`username`) REFERENCES `users` (`username`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
