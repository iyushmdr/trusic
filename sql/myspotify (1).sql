-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 21, 2024 at 01:50 PM
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
-- Database: `myspotify`
--

-- --------------------------------------------------------

--
-- Table structure for table `favourites`
--

CREATE TABLE `favourites` (
  `uid` int(11) NOT NULL,
  `songID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_vietnamese_ci;

--
-- Dumping data for table `favourites`
--

INSERT INTO `favourites` (`uid`, `songID`) VALUES
(10, 14),
(10, 16),
(10, 18),
(10, 19),
(10, 20);

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `id` int(11) NOT NULL,
  `groupName` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_vietnamese_ci;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `groupName`) VALUES
(1, 'admin'),
(2, 'user');

-- --------------------------------------------------------

--
-- Table structure for table `play_history`
--

CREATE TABLE `play_history` (
  `id` int(11) NOT NULL,
  `songId` int(11) DEFAULT NULL,
  `userId` int(11) DEFAULT NULL,
  `playedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `singers`
--

CREATE TABLE `singers` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `info` varchar(255) NOT NULL,
  `image` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_vietnamese_ci;

--
-- Dumping data for table `singers`
--

INSERT INTO `singers` (`id`, `name`, `info`, `image`) VALUES
(4, 'ajng', 'jajajaja', 'images/singers/1656230782392161.jpg'),
(5, 'reeflex', 'lolo', 'images/singers/download.jfif'),
(8, 'sirak', 'lolol', 'images/singers/Annotation 2020-09-04 161236.png'),
(9, 'Unknown', 'Random Beatiful Songs', 'images/singers/lofi.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `songs`
--

CREATE TABLE `songs` (
  `id` int(11) NOT NULL,
  `title` varchar(50) NOT NULL,
  `filePath` varchar(50) NOT NULL,
  `imgPath` varchar(50) NOT NULL,
  `dateAdded` timestamp NOT NULL DEFAULT current_timestamp(),
  `singerID` int(11) NOT NULL,
  `genreID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_vietnamese_ci;

--
-- Dumping data for table `songs`
--

INSERT INTO `songs` (`id`, `title`, `filePath`, `imgPath`, `dateAdded`, `singerID`, `genreID`) VALUES
(14, 'Pixabay', 'music/pixabay.mp3', 'images/piano.jpg', '2021-06-03 10:38:34', 9, 0),
(15, 'Midnight', 'music/midnight.mp3', 'images/Midnight_Mist.jpg', '2021-06-03 10:38:58', 9, 1),
(16, 'Electronica', 'music/electronica.mp3', 'images/lofi.jpg', '2021-06-03 10:39:21', 9, 1),
(17, 'chopsuey', 'music/lit asf.mp3', 'images/7af8cd66b3743bd89049656e9447d7a8.jpg', '2023-03-15 10:29:18', 4, 1),
(18, 'tremor', 'music/SnapInsta.io - Dimitri Vegas, Martin Garrix,', 'images/download.png', '2023-03-15 10:30:01', 5, 1),
(19, 'lol', 'music/bewokekindabyajng.mp3', 'images/lofi.jpg', '2023-03-15 10:32:47', 4, 1),
(20, 'llol', 'music/ofmymindajngunfinished.mp3', 'images/download.jfif', '2023-03-15 10:48:49', 4, 2),
(21, 'chill', 'music/chill.mp3', 'images/Annotation 2020-09-04 161236.png', '2023-03-18 08:59:28', 4, 2),
(22, 'drill', 'music/2022newdrill.mp3', 'images/Annotation 2020-09-04 161236.png', '2023-03-18 09:18:21', 4, 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `groupID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_vietnamese_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `groupID`) VALUES
(10, 'ajng', 'bd5492efb194236b6069a5f6ff6bcbc3', 1),
(11, 'ajng2', '816d1282ea43ac018e40821ff16bc689', 2),
(12, 'ajng3', '8f3a105c50f947774834b587bd8fc737', 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `favourites`
--
ALTER TABLE `favourites`
  ADD PRIMARY KEY (`uid`,`songID`),
  ADD KEY `favourites_ibfk_2` (`songID`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `play_history`
--
ALTER TABLE `play_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `songId` (`songId`),
  ADD KEY `userId` (`userId`);

--
-- Indexes for table `singers`
--
ALTER TABLE `singers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `songs`
--
ALTER TABLE `songs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `singerID` (`singerID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `groupID` (`groupID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `play_history`
--
ALTER TABLE `play_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `singers`
--
ALTER TABLE `singers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `songs`
--
ALTER TABLE `songs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `favourites`
--
ALTER TABLE `favourites`
  ADD CONSTRAINT `favourites_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favourites_ibfk_2` FOREIGN KEY (`songID`) REFERENCES `songs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `play_history`
--
ALTER TABLE `play_history`
  ADD CONSTRAINT `play_history_ibfk_1` FOREIGN KEY (`songId`) REFERENCES `songs` (`id`),
  ADD CONSTRAINT `play_history_ibfk_2` FOREIGN KEY (`userId`) REFERENCES `users` (`id`);

--
-- Constraints for table `songs`
--
ALTER TABLE `songs`
  ADD CONSTRAINT `songs_ibfk_1` FOREIGN KEY (`singerID`) REFERENCES `singers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`groupID`) REFERENCES `groups` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
