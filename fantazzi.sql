-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 19, 2017 at 10:50 PM
-- Server version: 10.1.24-MariaDB
-- PHP Version: 7.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fantazzi`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(2) NOT NULL,
  `email` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(32) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id_admin`, `email`, `password`, `name`, `last_name`) VALUES
(1, 'stefan.vukasinovic994@gmail.com', 'vuksha', 'Stefan', 'Vukašinović');

-- --------------------------------------------------------

--
-- Table structure for table `clubs`
--

CREATE TABLE `clubs` (
  `id_club` tinyint(2) NOT NULL,
  `club_name` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `club_shortname` varchar(3) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `clubs`
--

INSERT INTO `clubs` (`id_club`, `club_name`, `club_shortname`) VALUES
(1, 'AFC Bournemouth', 'BOU'),
(2, 'Arsenal', 'ARS'),
(3, 'Brighton & Hove Albion', 'BHA'),
(4, 'Burnley', 'BUR'),
(5, 'Chelsea', 'CHE'),
(6, 'Crystal Palace', 'CRY'),
(7, 'Everton', 'EVE'),
(8, 'Huddersfield Town', 'HUD'),
(9, 'Leicester City', 'LEI'),
(10, 'Liverpool', 'LIV'),
(11, 'Manchester City', 'MCI'),
(12, 'Manchester United', 'MUN'),
(13, 'Newcastle United', 'NEW'),
(14, 'Southampton', 'SOU'),
(15, 'Stoke City', 'STO'),
(16, 'Swansea', 'SWA'),
(17, 'Tottenham Hotspur', 'TOT'),
(18, 'Watford', 'WAT'),
(19, 'West Brom', 'WBA'),
(20, 'West Ham', 'WHU');

-- --------------------------------------------------------

--
-- Table structure for table `fixtures`
--

CREATE TABLE `fixtures` (
  `id_fixture` smallint(3) NOT NULL,
  `gw` tinyint(2) NOT NULL,
  `home` tinyint(2) NOT NULL,
  `away` tinyint(2) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `result_updated` tinyint(1) NOT NULL DEFAULT '0',
  `points_updated` tinyint(1) NOT NULL DEFAULT '0',
  `home_score` tinyint(2) DEFAULT NULL,
  `away_score` tinyint(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `fixtures`
--

INSERT INTO `fixtures` (`id_fixture`, `gw`, `home`, `away`, `date`, `time`, `result_updated`, `points_updated`, `home_score`, `away_score`) VALUES
(15, 1, 2, 9, '2017-08-11', '20:45:00', 1, 1, 1, 1),
(16, 1, 18, 10, '2017-08-12', '13:30:00', 1, 1, 1, 1),
(17, 2, 5, 4, '2017-08-12', '16:00:00', 0, 0, NULL, NULL),
(18, 2, 6, 8, '2017-08-12', '16:00:00', 0, 0, NULL, NULL),
(19, 2, 7, 15, '2017-08-12', '16:00:00', 0, 0, NULL, NULL),
(20, 2, 14, 16, '2017-08-12', '16:00:00', 0, 0, NULL, NULL),
(21, 2, 19, 1, '2017-08-12', '16:00:00', 0, 0, NULL, NULL),
(22, 2, 3, 11, '2017-08-12', '18:30:00', 0, 0, NULL, NULL),
(23, 2, 13, 17, '2017-08-13', '14:30:00', 0, 0, NULL, NULL),
(24, 2, 12, 20, '2017-08-13', '17:00:00', 0, 0, NULL, NULL),
(25, 2, 16, 12, '2017-08-19', '13:30:00', 0, 0, NULL, NULL),
(26, 2, 1, 18, '2017-08-19', '16:00:00', 0, 0, NULL, NULL),
(27, 2, 4, 19, '2017-08-19', '16:00:00', 0, 0, NULL, NULL),
(28, 2, 9, 3, '2017-08-19', '16:00:00', 0, 0, NULL, NULL),
(29, 2, 10, 6, '2017-08-19', '16:00:00', 0, 0, NULL, NULL),
(30, 2, 14, 20, '2017-08-19', '16:00:00', 0, 0, NULL, NULL),
(31, 2, 15, 2, '2017-08-19', '18:30:00', 0, 0, NULL, NULL),
(32, 2, 8, 13, '2017-08-20', '14:30:00', 0, 0, NULL, NULL),
(33, 2, 17, 5, '2017-08-20', '17:00:00', 0, 0, NULL, NULL),
(34, 2, 11, 7, '2017-08-21', '21:00:00', 0, 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `formations`
--

CREATE TABLE `formations` (
  `id_formation` tinyint(1) NOT NULL,
  `description` varchar(5) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `formations`
--

INSERT INTO `formations` (`id_formation`, `description`) VALUES
(4, '3-4-3'),
(5, '3-5-2'),
(2, '4-3-3'),
(1, '4-4-2'),
(3, '4-5-1'),
(8, '5-2-3'),
(7, '5-3-2'),
(6, '5-4-1');

-- --------------------------------------------------------

--
-- Table structure for table `gameweek`
--

CREATE TABLE `gameweek` (
  `id_gw` tinyint(2) NOT NULL,
  `gw` varchar(12) COLLATE utf8_unicode_ci NOT NULL COMMENT 'active gw->myteam gw selection'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `gameweek`
--

INSERT INTO `gameweek` (`id_gw`, `gw`) VALUES
(1, 'GW1'),
(10, 'GW10'),
(11, 'GW11'),
(12, 'GW12'),
(13, 'GW13'),
(14, 'GW14'),
(15, 'GW15'),
(16, 'GW16'),
(17, 'GW17'),
(18, 'GW18'),
(19, 'GW19'),
(2, 'GW2'),
(20, 'GW20'),
(21, 'GW21'),
(22, 'GW22'),
(23, 'GW23'),
(24, 'GW24'),
(25, 'GW25'),
(26, 'GW26'),
(27, 'GW27'),
(28, 'GW28'),
(29, 'GW29'),
(3, 'GW3'),
(30, 'GW30'),
(31, 'GW31'),
(32, 'GW32'),
(33, 'GW33'),
(34, 'GW34'),
(35, 'GW35'),
(36, 'GW36'),
(37, 'GW37'),
(38, 'GW38'),
(4, 'GW4'),
(5, 'GW5'),
(6, 'GW6'),
(7, 'GW7'),
(8, 'GW8'),
(9, 'GW9'),
(39, 'POSTSEASON');

-- --------------------------------------------------------

--
-- Table structure for table `gw_status`
--

CREATE TABLE `gw_status` (
  `id_status` int(11) NOT NULL,
  `active_gw` tinyint(2) NOT NULL COMMENT 'gw to show for myteam.php',
  `points_gw` tinyint(2) DEFAULT NULL COMMENT 'gw to show for points.php',
  `game_updating` tinyint(1) NOT NULL COMMENT '0-false 1-true',
  `leagues_updated` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'for current points gw',
  `players_stats_updated` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'all players points in current gw',
  `inserted_teams_active_gw` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `gw_status`
--

INSERT INTO `gw_status` (`id_status`, `active_gw`, `points_gw`, `game_updating`, `leagues_updated`, `players_stats_updated`, `inserted_teams_active_gw`) VALUES
(1, 2, 1, 0, 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

CREATE TABLE `login` (
  `id_user` int(7) NOT NULL,
  `email` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `team_name` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `bank` double NOT NULL,
  `free_transfers` tinyint(2) NOT NULL DEFAULT '3' COMMENT '0,1,2-FT 10-unlimited',
  `picked_team` int(1) NOT NULL COMMENT '0-team not picked(after registration) 1-team picked',
  `registration_gw` tinyint(2) NOT NULL COMMENT 'gw of squad selection'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `login`
--

INSERT INTO `login` (`id_user`, `email`, `password`, `name`, `last_name`, `team_name`, `bank`, `free_transfers`, `picked_team`, `registration_gw`) VALUES
(8, 'shawshenk94@hotmail.com', 'vuksha', 'Stefan', 'Vukasinovic', 'Vuksha`s XI', 2, 1, 1, 1),
(9, 'vesna.vukasinovic94@live.com', 'vesna', 'Vesna', 'Vukasinovic', 'Ettihad', 3.5, 1, 1, 1),
(10, 'pera@dsf', 'pera', 'Petar', 'Zaklanovic', 'PERA', 0, 10, 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `players`
--

CREATE TABLE `players` (
  `id_player` smallint(3) NOT NULL,
  `club` varchar(3) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `position` varchar(3) COLLATE utf8_unicode_ci NOT NULL,
  `price` double NOT NULL,
  `status` int(11) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `players`
--

INSERT INTO `players` (`id_player`, `club`, `name`, `last_name`, `position`, `price`, `status`) VALUES
(1, 'BOU', 'Asmir', 'Begović', 'GKP', 5, 1),
(2, 'BOU', 'Artur', 'Boruc', 'GKP', 5, 1),
(3, 'BOU', 'Simon', 'Francis', 'DEF', 5, 1),
(4, 'BOU', 'Adam', 'Smith', 'DEF', 5.5, 1),
(5, 'BOU', 'Steve', 'Cook', 'DEF', 5, 1),
(6, 'BOU', 'Charlie', 'Daniels', 'DEF', 5.5, 1),
(7, 'BOU', 'Nathan', 'Ake', 'DEF', 5, 1),
(8, 'BOU', 'Andrew', 'Surman', 'MID', 5, 1),
(9, 'BOU', 'Marc', 'Pugh', 'MID', 5.5, 1),
(10, 'BOU', 'Harry', 'Arter', 'MID', 5.5, 1),
(11, 'BOU', 'Junior', 'Stanislas', 'MID', 6, 1),
(12, 'BOU', 'Max', 'Gradel', 'MID', 6.5, 1),
(13, 'BOU', 'Dan', 'Gosling', 'MID', 5, 1),
(14, 'BOU', 'Jermaine', 'Defoe', 'FWD', 8.5, 1),
(15, 'BOU', 'Callum', 'Wilson', 'FWD', 6, 1),
(16, 'BOU', 'Benik', 'Afobe', 'FWD', 5.5, 1),
(17, 'BOU', 'Lewis', 'Grabban', 'FWD', 5, 1),
(18, 'BOU', 'Joshua', 'King', 'FWD', 8.5, 1),
(26, 'ARS', 'Petr', 'Cech', 'GKP', 5.5, 1),
(27, 'ARS', 'David', 'Ospina', 'GKP', 5, 1),
(28, 'ARS', 'Kieran', 'Gibbs', 'DEF', 5.5, 1),
(29, 'ARS', 'Hector', 'Bellerín', 'DEF', 6.5, 1),
(30, 'ARS', 'Laurent', 'Koscielny', 'DEF', 6, 1),
(31, 'ARS', 'Shkodran', 'Mustafi', 'DEF', 6, 1),
(32, 'ARS', 'Nacho', 'Monreal', 'DEF', 6, 1),
(33, 'ARS', 'Gabriel', 'Paulista', 'DEF', 5.5, 1),
(34, 'ARS', 'Mesut', 'Özil', 'MID', 9, 1),
(35, 'ARS', 'Alexis', 'Sánchez', 'MID', 12, 1),
(36, 'ARS', 'Aaron', 'Ramsey', 'MID', 6.5, 1),
(37, 'ARS', 'Granit', 'Xhaka', 'MID', 5.5, 1),
(38, 'ARS', 'Francis', 'Coquelin', 'MID', 4.5, 1),
(39, 'ARS', 'Mohamed', 'Elneny', 'MID', 5, 1),
(40, 'ARS', 'Theo', 'Walcott', 'MID', 8, 1),
(41, 'ARS', 'Olivier', 'Giroud', 'FWD', 8, 1),
(42, 'ARS', 'Danny', 'Welbeck', 'FWD', 8, 1),
(43, 'ARS', 'Alexandre', 'Lacazette', 'FWD', 10, 1),
(44, 'ARS', 'Sead', 'Kolašinac', 'DEF', 6, 1),
(45, 'ARS', 'Lucas', 'Perez', 'FWD', 7, 1),
(65, 'BUR', 'Tom', 'Heaton', 'GKP', 5, 1),
(67, 'BUR', 'Matthew', 'Lowton', 'DEF', 4.5, 1),
(68, 'BUR', 'Michael', 'Keane', 'DEF', 5, 1),
(69, 'BUR', 'Steven', 'Ward', 'DEF', 4.5, 1),
(70, 'BUR', 'Ben', 'Mee', 'DEF', 4, 1),
(71, 'BUR', 'Charlie', 'Taylor', 'DEF', 4, 1),
(72, 'BUR', 'George', 'Boyd', 'MID', 5, 1),
(73, 'BUR', 'Scott', 'Arfield', 'MID', 5, 1),
(74, 'BUR', 'Jeff', 'Hendrick', 'MID', 5, 1),
(75, 'BUR', 'Jonathan', 'Walters', 'MID', 5, 1),
(76, 'BUR', 'Steven', 'Defour', 'MID', 5, 1),
(77, 'BUR', 'Jack', 'Cork', 'MID', 4, 1),
(78, 'BUR', 'Andre', 'Gray', 'FWD', 6.5, 1),
(79, 'BUR', 'Sam', 'Vokes', 'FWD', 6, 1),
(80, 'BUR', 'Ashley', 'Barnes', 'FWD', 5.5, 1),
(83, 'CHE', 'Thibaut', 'Courtois', 'GKP', 5.5, 1),
(84, 'CHE', 'Willy', 'Caballero', 'DEF', 5, 1),
(85, 'CHE', 'Gary', 'Cahill', 'DEF', 6.5, 1),
(90, 'CHE', 'César', 'Azpilicueta', 'DEF', 6.5, 1),
(91, 'CHE', 'Marcos', 'Alonso', 'DEF', 7, 1),
(92, 'CHE', 'Victor', 'Moses', 'DEF', 6.5, 1),
(93, 'CHE', 'David', 'Luiz', 'DEF', 6, 1),
(94, 'CHE', 'Kurt', 'Zouma', 'DEF', 5.5, 1),
(95, 'CHE', 'Eden', 'Hazard', 'MID', 11.5, 1),
(96, 'CHE', 'Cesc', 'Fabregas', 'MID', 9, 1),
(97, 'CHE', '', 'Pedro', 'MID', 8.5, 1),
(98, 'CHE', '', 'Wilian', 'MID', 8, 1),
(99, 'CHE', 'N\'golo', 'Kante', 'MID', 5, 1),
(104, 'CHE', 'Michy', 'Batshuayi', 'FWD', 8.5, 1),
(109, 'CRY', 'Wayne', 'Hennesey', 'GKP', 5, 1),
(111, 'CRY', 'Patrick', 'Van Aanholt', 'DEF', 5.5, 1),
(112, 'CRY', 'Joel', 'Ward', 'DEF', 5, 1),
(113, 'CRY', 'Scott', 'Dann', 'DEF', 5, 1),
(114, 'CRY', 'Damien', 'Delaney', 'DEF', 5, 1),
(115, 'CRY', 'Martin', 'Kelly', 'DEF', 5, 1),
(118, 'CRY', 'Andros', 'Townsend', 'MID', 6.5, 1),
(119, 'CRY', 'Wilfried', 'Zaha', 'MID', 8.5, 1),
(120, 'CRY', 'James', 'McArthur', 'MID', 5.5, 1),
(121, 'CRY', 'Yohan', 'Cabaye', 'MID', 6, 1),
(122, 'CRY', 'Luka', 'Milivojević', 'MID', 5, 1),
(123, 'CRY', 'Jordon', 'Mutch', 'MID', 5, 1),
(127, 'CRY', 'Christian', 'Benteke', 'FWD', 8.5, 1),
(128, 'CRY', 'Connor', 'Wickham', 'FWD', 5.5, 1),
(129, 'CRY', 'Loic', 'Remy', 'FWD', 6.5, 1),
(130, 'EVE', 'Jordan', 'Pickford', 'GKP', 5, 1),
(131, 'EVE', 'Joel', 'Robles', 'GKP', 4.5, 1),
(132, 'EVE', 'Marteen', 'Stekelenburg', 'GKP', 4.5, 1),
(133, 'EVE', 'Leighton', 'Baines', 'DEF', 6, 1),
(134, 'EVE', 'Seamus', 'Coleman', 'DEF', 6.5, 1),
(135, 'EVE', 'Ashley', 'Williams', 'DEF', 5.5, 1),
(136, 'EVE', 'Phil', 'Jagielka', 'DEF', 5, 1),
(137, 'EVE', 'Ramiro', 'Funes Mori', 'DEF', 5, 1),
(138, 'EVE', 'Mason', 'Holgate', 'DEF', 4.5, 1),
(141, 'EVE', 'Kevin', 'Mirallas', 'MID', 6.5, 1),
(142, 'EVE', 'Gareth', 'Barry', 'MID', 4.5, 1),
(143, 'EVE', 'Tom', 'Davies', 'MID', 5.5, 1),
(144, 'EVE', 'Yannick', 'Bolasie', 'MID', 6.5, 0),
(145, 'EVE', 'Morgan', 'Schneiderlin', 'MID', 5, 1),
(146, 'EVE', 'Davy', 'Klaassen', 'MID', 7.5, 1),
(150, 'EVE', 'Wayne', 'Rooney', 'FWD', 7.5, 1),
(151, 'EVE', 'Sandro', 'Ramirez', 'FWD', 7.5, 1),
(166, 'LEI', 'Kasper', 'Schmeichel', 'GKP', 5, 1),
(167, 'LEI', 'Ben', 'Hamer', 'GKP', 4.5, 1),
(168, 'LEI', 'Harry', 'Maguire', 'DEF', 5, 1),
(169, 'LEI', 'Wes', 'Morgan', 'DEF', 5, 1),
(170, 'LEI', 'Danny', 'Simpson', 'DEF', 5, 1),
(171, 'LEI', 'Robert', 'Huth', 'DEF', 5, 1),
(172, 'LEI', 'Christian', 'Fuchs', 'DEF', 5.5, 1),
(173, 'LEI', 'Riyad', 'Mahrez', 'MID', 8.5, 1),
(174, 'LEI', 'Marc', 'Albrighton', 'MID', 6, 1),
(175, 'LEI', 'Danny', 'Drinkwater', 'MID', 5, 1),
(176, 'LEI', 'Demarai', 'Gray', 'MID', 5.5, 1),
(177, 'LEI', 'Victor', 'Iborra', 'MID', 6, 1),
(178, 'LEI', 'Ahmed', 'Musa', 'MID', 6.5, 1),
(185, 'LEI', 'Jamie', 'Vardy', 'FWD', 8.5, 1),
(186, 'LEI', 'Shinji', 'Okazaki', 'FWD', 5.5, 1),
(187, 'LEI', 'Islam', 'Slimani', 'FWD', 7, 1),
(188, 'HUD', 'Jonas', 'Lösl', 'GKP', 4.5, 1),
(189, 'HUD', 'Joel', 'Coleman', 'GKP', 4, 1),
(190, 'HUD', 'Tommy', 'Smith', 'DEF', 5, 1),
(191, 'HUD', 'Chris', 'Löwe', 'DEF', 4.5, 1),
(192, 'HUD', 'Christofer', 'Schindler', 'DEF', 4.5, 1),
(193, 'HUD', 'Michael', 'Hefele', 'DEF', 4.5, 1),
(194, 'HUD', 'Martin', 'Cranie', 'DEF', 4, 1),
(195, 'HUD', 'Scott', 'Malone', 'DEF', 4.5, 1),
(196, 'HUD', 'Aaron', 'Mooy', 'MID', 5.5, 1),
(197, 'HUD', 'Rajiv', 'van La Parra', 'MID', 5, 1),
(198, 'HUD', 'Jonathan', 'Hogg', 'MID', 4.5, 1),
(199, 'HUD', 'Tom', 'Ince', 'MID', 6, 1),
(200, 'HUD', 'Kasey', 'Palmer', 'MID', 5.5, 1),
(201, 'HUD', 'Elias', 'Kachunga', 'MID', 6, 1),
(202, 'HUD', 'Danny', 'Williams', 'MID', 5, 1),
(203, 'HUD', 'Dean', 'Whitehead', 'MID', 4.5, 1),
(204, 'HUD', 'Jack', 'Payne', 'MID', 4.5, 1),
(205, 'HUD', 'Philip', 'Billing', 'MID', 4.5, 1),
(206, 'HUD', 'Joe', 'Lolley', 'MID', 4.5, 1),
(207, 'HUD', 'Steve', 'Mounie', 'FWD', 6, 1),
(208, 'HUD', 'Collin', 'Quaner', 'FWD', 4.5, 1),
(209, 'HUD', 'Laurent', 'Depoitre', 'FWD', 5.5, 1),
(210, 'HUD', 'Nahki', 'Wells', 'FWD', 5, 1),
(211, 'LIV', 'Simon', 'Mignolet', 'GKP', 5, 1),
(212, 'LIV', 'Loris', 'Karius', 'GKP', 5, 1),
(213, 'LIV', 'Dejan', 'Lovren', 'DEF', 5.5, 1),
(214, 'LIV', 'James', 'Milner', 'DEF', 6.5, 1),
(215, 'LIV', 'Nathaniel', 'Clyne', 'DEF', 5.5, 1),
(216, 'LIV', 'Joel', 'Matip', 'DEF', 5.5, 1),
(217, 'LIV', 'Ragnar', 'Klavan', 'DEF', 4.5, 1),
(218, 'LIV', 'Joe', 'Gomez', 'DEF', 4.5, 1),
(219, 'LIV', 'Philippe', 'Coutinho', 'MID', 9, 1),
(220, 'LIV', 'Mohamed', 'Salah', 'MID', 9, 1),
(221, 'LIV', 'Sadio', 'Mané', 'MID', 9.5, 1),
(222, 'LIV', 'Georginio', 'Wijnaldum', 'MID', 7, 1),
(223, 'LIV', 'Adam', 'Lallana', 'MID', 7.5, 1),
(224, 'LIV', 'Jordan', 'Henderson', 'MID', 5.5, 1),
(225, 'LIV', 'Emre', 'Can', 'MID', 5, 1),
(226, 'LIV', 'Marko', 'Grujić', 'MID', 4.5, 1),
(227, 'LIV', 'Roberto', 'Firmino', 'FWD', 8.5, 1),
(228, 'LIV', 'Divock', 'Origi', 'FWD', 7.5, 1),
(229, 'LIV', 'Daniel', 'Sturridge', 'FWD', 8, 1),
(230, 'LIV', 'Dominic', 'Solanke', 'FWD', 5, 1),
(231, 'LIV', 'Danny', 'Ings', 'FWD', 5.5, 1),
(232, 'MCI', 'Claudio', 'Bravo', 'GKP', 5, 1),
(233, 'MCI', '', 'Ederson', 'GKP', 5.5, 1),
(234, 'MCI', 'Kyle', 'Walker', 'DEF', 6.5, 1),
(235, 'MCI', 'Vincent', 'Kompany', 'DEF', 6, 1),
(236, 'MCI', 'Aleksandar', 'Kolarov', 'DEF', 5.5, 1),
(237, 'MCI', 'Nicolas', 'Otamendi', 'DEF', 5.5, 1),
(238, 'MCI', 'John', 'Stones', 'DEF', 5.5, 1),
(239, 'MCI', 'Kevin', 'De Bruyne', 'MID', 10, 1),
(240, 'MCI', 'Raheem', 'Sterling', 'MID', 8, 1),
(241, 'MCI', '', 'David Silva', 'MID', 8, 1),
(242, 'MCI', '', 'Bernardo Silva', 'MID', 8, 1),
(243, 'MCI', 'Leroy', 'Sané', 'MID', 8.5, 1),
(244, 'MCI', 'Yaya', 'Touré', 'MID', 6.5, 1),
(245, 'MCI', '', 'Fernandinho', 'MID', 5, 1),
(246, 'MCI', 'Ilkay', 'Gündogan', 'MID', 5.5, 1),
(247, 'MCI', 'Fabian', 'Delph', 'MID', 4.5, 1),
(248, 'MCI', 'Gabriel', 'Jesus', 'FWD', 10.5, 1),
(249, 'MCI', 'Sergio', 'Agüero', 'FWD', 11.5, 1),
(250, 'MCI', 'Kelechi', 'Iheanacho', 'FWD', 7, 1),
(251, 'MUN', 'David', 'de Gea', 'GKP', 5.5, 1),
(252, 'MUN', 'Sergio', 'Romero', 'GKP', 5, 1),
(253, 'MUN', 'Joel', 'Pereira', 'GKP', 4, 1),
(254, 'MUN', 'Antonio', 'Valencia', 'DEF', 6.5, 1),
(255, 'MUN', 'Eric', 'Bailly', 'DEF', 6, 1),
(256, 'MUN', 'Daley', 'Blind', 'DEF', 5.5, 1),
(257, 'MUN', 'Marcos', 'Rojo', 'DEF', 5.5, 1),
(258, 'MUN', 'Mateo', 'Darmian', 'DEF', 5.5, 1),
(259, 'MUN', 'Phil', 'Jones', 'DEF', 5, 1),
(260, 'MUN', 'Chris', 'Smalling', 'DEF', 5.5, 1),
(261, 'MUN', 'Victor', 'Lindelöf', 'DEF', 5.5, 1),
(262, 'MUN', 'Luke', 'Shaw', 'DEF', 5, 1),
(263, 'MUN', 'Paul', 'Pogba', 'MID', 8, 1),
(264, 'MUN', 'Juan', 'Mata', 'MID', 7, 1),
(265, 'MUN', 'Anthony', 'Martial', 'MID', 8, 1),
(266, 'MUN', 'Henrikh', 'Mkhitaryan', 'MID', 8, 1),
(267, 'MUN', 'Jesse', 'Lingard', 'MID', 6, 1),
(268, 'MUN', 'Ander', 'Herrera', 'MID', 5.5, 1),
(269, 'MUN', 'Marrouane', 'Fellaini', 'MID', 5, 1),
(270, 'MUN', 'Michael', 'Carrick', 'MID', 4.5, 1),
(271, 'MUN', 'Romelu', 'Lukaku', 'FWD', 11.5, 1),
(272, 'MUN', 'Marcus', 'Rashford', 'FWD', 7.5, 1),
(273, 'NEW', 'Rob', 'Elliot', 'GKP', 4, 1),
(274, 'NEW', 'Karl', 'Darlow', 'GKP', 4.5, 1),
(275, 'NEW', 'DeAndre', 'Yedlin', 'DEF', 4.5, 1),
(276, 'NEW', 'Ciaran', 'Clark', 'DEF', 4.5, 1),
(277, 'NEW', 'Jamaal', 'Lascelles', 'DEF', 4.5, 1),
(278, 'NEW', 'Paul', 'Dummet', 'DEF', 4.5, 1),
(279, 'NEW', 'Grant', 'Hanley', 'DEF', 4, 1),
(280, 'NEW', 'Jesús', 'Gámez', 'DEF', 4, 1),
(281, 'NEW', 'Florian', 'Lejeune', 'DEF', 4.5, 1),
(282, 'NEW', 'Masadio', 'Haidara', 'DEF', 4, 1),
(283, 'NEW', 'Matt', 'Richie', 'MID', 6, 1),
(284, 'NEW', 'Jonjo', 'Shelvey', 'MID', 5.5, 1),
(285, 'NEW', 'Mohamed', 'Diamé', 'MID', 5, 1),
(286, 'NEW', 'Jack', 'Colback', 'MID', 4.5, 1),
(287, 'NEW', 'Christian', 'Atsu', 'MID', 5, 1),
(288, 'NEW', 'Siem', 'de Jong', 'MID', 5, 1),
(289, 'NEW', 'Rolando', 'Aarons', 'MID', 4.5, 1),
(290, 'NEW', 'Dwight', 'Gayle', 'FWD', 6.5, 1),
(291, 'NEW', 'Ayoze', 'Pérez', 'FWD', 5.5, 1),
(292, 'NEW', 'Aleksandar', 'Mitrović', 'FWD', 5, 1),
(293, 'NEW', 'Daryl', 'Murphy', 'FWD', 4.5, 1),
(294, 'SOU', 'Alex', 'McCarthy', 'GKP', 4.5, 1),
(295, 'SOU', 'Fraser', 'Forster', 'GKP', 5, 1),
(296, 'SOU', 'Ryan', 'Bertrand', 'DEF', 5.5, 1),
(297, 'SOU', 'Cédric', 'Soares', 'DEF', 5, 1),
(298, 'SOU', 'Maya', 'Yoshida', 'DEF', 5, 1),
(299, 'SOU', 'Virgil', 'van Dijk', 'DEF', 5.5, 1),
(300, 'SOU', 'Jack', 'Stephens', 'DEF', 5, 1),
(301, 'SOU', 'Matt', 'Targett', 'DEF', 4.5, 1),
(302, 'SOU', 'Sam', 'McQueen', 'DEF', 4.5, 1),
(303, 'SOU', 'Jan', 'Bednarek', 'DEF', 4.5, 1),
(304, 'SOU', 'Nathan', 'Redmond', 'MID', 6.5, 1),
(305, 'SOU', 'Dušan', 'Tadić', 'MID', 6.5, 1),
(306, 'SOU', 'James', 'Ward-Prowse', 'MID', 5.5, 1),
(307, 'SOU', 'Oriol', 'Romeu', 'MID', 4.5, 1),
(308, 'SOU', 'Steven', 'Davis', 'MID', 5, 1),
(309, 'SOU', 'Sofiane', 'Boufal', 'MID', 6, 1),
(310, 'SOU', 'Jordie', 'Clasie', 'MID', 4.5, 1),
(311, 'SOU', 'Pierre', 'Höjbjerg', 'MID', 4.5, 1),
(312, 'SOU', 'Charlie', 'Austin', 'FWD', 6.5, 1),
(313, 'SOU', 'Shane', 'Long', 'FWD', 6, 1),
(314, 'SOU', 'Manolo', 'Gabbiadini', 'FWD', 7, 1),
(315, 'TOT', 'Hugo', 'Lloris', 'GKP', 5.5, 1),
(316, 'TOT', 'Michael', 'Vorm', 'GKP', 5, 1),
(317, 'TOT', 'Jan', 'Verthonghen', 'DEF', 6, 1),
(318, 'TOT', 'Tobby', 'Alderweireld', 'DEF', 6, 1),
(319, 'TOT', 'Ben', 'Davies', 'DEF', 5.5, 1),
(320, 'TOT', 'Danny', 'Rose', 'DEF', 6.5, 1),
(321, 'TOT', 'Kieran', 'Trippier', 'DEF', 5.5, 1),
(322, 'TOT', 'Kevin', 'Wimmer', 'DEF', 4.5, 1),
(323, 'TOT', 'Dele', 'Alli', 'MID', 9.5, 1),
(324, 'TOT', 'Christian', 'Eriksen', 'MID', 9.5, 1),
(325, 'TOT', 'Heung-Min', 'Son', 'MID', 8, 1),
(326, 'TOT', 'Victor', 'Wanyama', 'MID', 5, 1),
(327, 'TOT', 'Eric', 'Dier', 'MID', 5, 1),
(328, 'TOT', 'Mousa', 'Dembéle', 'MID', 5, 1),
(329, 'TOT', 'Moussa', 'Sissoko', 'MID', 5.5, 1),
(330, 'TOT', 'Harry', 'Winks', 'MID', 5, 1),
(331, 'TOT', 'Erik', 'Lamela', 'MID', 6.5, 1),
(332, 'TOT', 'Harry', 'Kane', 'FWD', 12.5, 1),
(333, 'TOT', 'Vincent', 'Janssen', 'FWD', 7.5, 1),
(334, 'STO', 'Jack', 'Butland', 'GKP', 5, 1),
(335, 'STO', 'Lee', 'Grant', 'GKP', 4.5, 1),
(336, 'STO', 'Eric', 'Pieters', 'DEF', 5, 1),
(337, 'STO', 'Ryan', 'Shawcross', 'DEF', 5, 1),
(338, 'STO', 'Geoff', 'Cameron', 'DEF', 4.5, 1),
(339, 'STO', 'Glenn', 'Johnson', 'DEF', 4.5, 1),
(340, 'STO', 'Phil', 'Bardsley', 'DEF', 4.5, 1),
(341, 'STO', 'Joe', 'Allen', 'MID', 6.5, 1),
(342, 'STO', 'Darren', 'Fletcher', 'MID', 5, 1),
(343, 'STO', 'Xherdan', 'Shaqiri', 'MID', 6, 1),
(344, 'STO', 'Glenn', 'Whelan', 'MID', 4.5, 1),
(345, 'STO', 'Charlie', 'Adam', 'MID', 5, 1),
(346, 'STO', 'Ryan', 'Sobhi', 'MID', 5, 1),
(347, 'STO', 'Marko', 'Arnautović', 'MID', 7, 1),
(348, 'STO', 'Bojan', 'Krkić', 'MID', 5.5, 1),
(349, 'STO', 'Ibrahim', 'Afellay', 'MID', 5, 1),
(350, 'STO', 'Peter', 'Crouch', 'FWD', 5, 1),
(351, 'STO', 'Mame Biram', 'Diouf', 'FWD', 5.5, 1),
(352, 'STO', 'Saido', 'Berahino', 'FWD', 6, 1),
(364, 'SWA', 'Lukasz', 'Fabianski', 'GKP', 4.5, 1),
(365, 'SWA', 'Kristoffer', 'Nordfeldt', 'GKP', 4, 1),
(366, 'SWA', 'Alfie', 'Mawson', 'DEF', 5, 1),
(367, 'SWA', 'Kyle', 'Naughton', 'DEF', 4.5, 1),
(368, 'SWA', 'Federico', 'Fernández', 'DEF', 4.5, 1),
(369, 'SWA', 'Martin', 'Olsson', 'DEF', 5, 1),
(370, 'SWA', 'Stephen', 'Kingsley', 'DEF', 4.5, 1),
(371, 'SWA', 'Angel', 'Rangel', 'DEF', 4, 1),
(372, 'SWA', 'Mike', 'van der Hoorn', 'DEF', 4.5, 1),
(373, 'SWA', 'Leroy', 'Fer', 'MID', 5.5, 1),
(374, 'SWA', 'Gylfi', 'Sigurdsson', 'MID', 8.5, 1),
(375, 'SWA', 'Wayne', 'Routledge', 'MID', 5.5, 1),
(376, 'SWA', 'Tom', 'Carroll', 'MID', 4.5, 1),
(377, 'SWA', 'Sueng-yueng', 'Ki', 'MID', 5, 1),
(378, 'SWA', 'Modou', 'Barrow', 'MID', 5, 1),
(379, 'SWA', 'Luciano', 'Narsingh', 'MID', 5, 1),
(380, 'SWA', 'Jefferson', 'Montero', 'MID', 5, 1),
(381, 'SWA', 'Leon', 'Britton', 'MID', 4.5, 1),
(382, 'SWA', 'Fernando', 'Llorente', 'FWD', 7.5, 1),
(383, 'SWA', 'Jordan', 'Ayew', 'FWD', 5, 1),
(384, 'SWA', 'Tammy', 'Abraham', 'FWD', 5.5, 1),
(385, 'WAT', 'Heurelho', 'Gomes', 'GKP', 4.5, 1),
(386, 'WAT', 'Costel', 'Pantilimon', 'GKP', 4.5, 1),
(387, 'WAT', 'José', 'Holebas', 'DEF', 5, 1),
(388, 'WAT', 'Daryl', 'Janmaat', 'DEF', 5, 1),
(389, 'WAT', 'Miguel', 'Brittos', 'DEF', 4.5, 1),
(390, 'WAT', 'Sebastian', 'Prödl', 'DEF', 4.5, 1),
(391, 'WAT', 'Younes', 'Kaboul', 'DEF', 4.5, 1),
(392, 'WAT', 'Craig', 'Cathcart', 'DEF', 4.5, 1),
(393, 'WAT', 'Francisko', 'Femenía', 'DEF', 4.5, 1),
(394, 'WAT', 'Ettiene', 'Capoue', 'MID', 5.5, 1),
(395, 'WAT', 'Nordin', 'Amrabat', 'MID', 5, 1),
(396, 'WAT', 'Tom', 'Cleverley', 'MID', 5, 1),
(397, 'WAT', 'Valon', 'Behrami', 'MID', 4.5, 1),
(398, 'WAT', 'Roberto', 'Pereyra', 'MID', 6, 1),
(399, 'WAT', 'Isaac', 'Success', 'MID', 5.5, 1),
(401, 'WAT', 'Troy', 'Deeney', 'FWD', 6.5, 1),
(402, 'WAT', 'Stefano', 'Okaka', 'FWD', 5.5, 1),
(403, 'WBA', 'Ben', 'Foster', 'GKP', 4.5, 1),
(404, 'WBA', 'Boaz', 'Myhill', 'GKP', 4, 1),
(405, 'WBA', 'Gareth', 'McAuley', 'DEF', 5, 1),
(406, 'WBA', 'Craig', 'Dawson', 'DEF', 5, 1),
(407, 'WBA', 'Jonny', 'Evans', 'DEF', 5, 1),
(408, 'WBA', 'Alan Romeo', 'Nyom', 'DEF', 5, 1),
(409, 'WBA', 'Ahmed', 'Hegazi', 'DEF', 4.5, 1),
(410, 'WBA', 'Matt', 'Philips', 'MID', 6, 1),
(411, 'WBA', 'Nacer', 'Chadli', 'MID', 6, 1),
(412, 'WBA', 'Chris', 'Brunt', 'MID', 5.5, 1),
(413, 'WBA', 'James', 'Morrison', 'MID', 5.5, 1),
(414, 'WBA', 'Jake', 'Livermore', 'MID', 5, 1),
(415, 'WBA', 'Claudio', 'Yacob', 'MID', 4.5, 1),
(416, 'WBA', 'Salomón', 'Rondón', 'FWD', 6.5, 1),
(417, 'WBA', 'Jay', 'Rodriguez', 'FWD', 6, 1),
(418, 'WBA', 'Hal', 'Robson-Kanu', 'FWD', 5, 1),
(419, 'WHU', 'Joe', 'Hart', 'GKP', 5, 1),
(420, 'WHU', 'Darren', 'Randolph', 'GKP', 4.5, 1),
(421, 'WHU', '', 'Adrián', 'GKP', 4.5, 1),
(422, 'WHU', 'Winston', 'Reid', 'DEF', 5, 1),
(423, 'WHU', 'Jose', 'Fonte', 'DEF', 5, 1),
(424, 'WHU', 'James', 'Collins', 'DEF', 4.5, 1),
(425, 'WHU', 'Aaron', 'Cresswell', 'DEF', 5, 1),
(426, 'WHU', 'Pablo', 'Zabaleta', 'DEF', 5, 1),
(427, 'WHU', 'Angelo', 'Ogbonna', 'DEF', 4.5, 1),
(428, 'WHU', 'Arthur', 'Masuaku', 'DEF', 4.5, 1),
(429, 'WHU', 'Sam', 'Byram', 'DEF', 4.5, 1),
(430, 'WHU', 'Robert', 'Snodgrass', 'MID', 6, 1),
(431, 'WHU', 'André', 'Ayew', 'MID', 7, 1),
(432, 'WHU', 'Manuel', 'Lanzini', 'MID', 7, 1),
(433, 'WHU', 'Michail', 'Antonio', 'MID', 7.5, 1),
(434, 'WHU', 'Cheikhou', 'Kouyaté', 'MID', 5, 1),
(435, 'WHU', 'Mark', 'Noble', 'MID', 5.5, 1),
(436, 'WHU', 'Pedro', 'Obiang', 'MID', 4.5, 1),
(437, 'WHU', 'Sofiane', 'Feghouli', 'MID', 5.5, 1),
(438, 'WHU', 'Andy', 'Carroll', 'FWD', 6, 1),
(439, 'WHU', 'Ashley', 'Fletcher', 'FWD', 4.5, 1),
(440, 'WHU', 'Diafra', 'Sakho', 'FWD', 5.5, 1);

-- --------------------------------------------------------

--
-- Table structure for table `players_stats`
--

CREATE TABLE `players_stats` (
  `id_stat` int(6) NOT NULL,
  `id_player` smallint(3) NOT NULL,
  `id_fixture` smallint(3) NOT NULL,
  `mins_played` tinyint(2) NOT NULL,
  `points_scored` tinyint(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `players_stats`
--

INSERT INTO `players_stats` (`id_stat`, `id_player`, `id_fixture`, `mins_played`, `points_scored`) VALUES
(28, 26, 15, 90, 2),
(29, 29, 15, 90, 2),
(30, 33, 15, 90, 2),
(31, 32, 15, 90, 2),
(32, 31, 15, 90, 2),
(33, 44, 15, 90, 2),
(34, 36, 15, 80, 2),
(35, 37, 15, 90, 2),
(36, 34, 15, 90, 7),
(37, 35, 15, 90, 2),
(38, 42, 15, 90, 2),
(39, 40, 15, 10, 7),
(40, 166, 15, 90, 2),
(41, 169, 15, 90, 2),
(42, 171, 15, 90, 2),
(43, 172, 15, 90, 1),
(44, 168, 15, 90, 2),
(45, 178, 15, 90, 2),
(46, 173, 15, 90, 10),
(47, 177, 15, 90, 2),
(48, 175, 15, 90, 2),
(49, 185, 15, 90, 5),
(50, 187, 15, 90, 2),
(51, 27, 15, 0, 0),
(52, 28, 15, 0, 0),
(53, 30, 15, 0, 0),
(54, 38, 15, 0, 0),
(55, 39, 15, 0, 0),
(56, 41, 15, 0, 0),
(57, 43, 15, 0, 0),
(58, 45, 15, 0, 0),
(59, 167, 15, 0, 0),
(60, 170, 15, 0, 0),
(61, 174, 15, 0, 0),
(62, 176, 15, 0, 0),
(63, 186, 15, 0, 0),
(64, 385, 16, 90, 2),
(65, 390, 16, 90, 2),
(66, 392, 16, 90, 2),
(67, 389, 16, 90, 2),
(68, 388, 16, 90, 2),
(69, 387, 16, 90, 2),
(70, 396, 16, 90, 2),
(71, 394, 16, 90, 2),
(72, 398, 16, 90, 2),
(73, 401, 16, 90, 5),
(74, 402, 16, 90, 9),
(75, 211, 16, 90, 2),
(76, 216, 16, 90, 2),
(77, 215, 16, 90, 2),
(78, 214, 16, 90, 2),
(79, 213, 16, 90, 1),
(80, 223, 16, 90, 2),
(81, 219, 16, 90, 9),
(82, 224, 16, 90, 6),
(83, 221, 16, 90, 2),
(84, 227, 16, 90, 2),
(85, 220, 16, 90, 2),
(86, 212, 16, 0, 0),
(87, 217, 16, 0, 0),
(88, 218, 16, 0, 0),
(89, 222, 16, 0, 0),
(90, 225, 16, 0, 0),
(91, 226, 16, 0, 0),
(92, 228, 16, 0, 0),
(93, 229, 16, 0, 0),
(94, 230, 16, 0, 0),
(95, 231, 16, 0, 0),
(96, 386, 16, 0, 0),
(97, 391, 16, 0, 0),
(98, 393, 16, 0, 0),
(99, 395, 16, 0, 0),
(100, 397, 16, 0, 0),
(101, 399, 16, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `positions`
--

CREATE TABLE `positions` (
  `id_position` tinyint(1) NOT NULL,
  `position_name` varchar(12) COLLATE utf8_unicode_ci NOT NULL,
  `position_shortname` varchar(3) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `positions`
--

INSERT INTO `positions` (`id_position`, `position_name`, `position_shortname`) VALUES
(1, 'Goalkeeper', 'GKP'),
(2, 'Defender', 'DEF'),
(3, 'Midfielder', 'MID'),
(4, 'Forward', 'FWD');

-- --------------------------------------------------------

--
-- Table structure for table `users_leagues`
--

CREATE TABLE `users_leagues` (
  `id_league` int(11) NOT NULL,
  `league_name` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `players_number` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `users_leagues`
--

INSERT INTO `users_leagues` (`id_league`, `league_name`, `players_number`) VALUES
(1, 'Overall', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users_league_points`
--

CREATE TABLE `users_league_points` (
  `id` int(11) NOT NULL,
  `id_user` int(7) NOT NULL,
  `id_league` int(11) NOT NULL,
  `user_points` smallint(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `users_league_points`
--

INSERT INTO `users_league_points` (`id`, `id_user`, `id_league`, `user_points`) VALUES
(1, 8, 1, -2),
(2, 9, 1, 33),
(3, 10, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `users_teams`
--

CREATE TABLE `users_teams` (
  `id_team` int(11) NOT NULL,
  `id_user` int(7) NOT NULL,
  `gw` tinyint(2) NOT NULL,
  `formation` tinyint(1) NOT NULL,
  `gk1` smallint(3) NOT NULL,
  `gk2` smallint(3) NOT NULL,
  `def1` smallint(3) NOT NULL,
  `def2` smallint(3) NOT NULL,
  `def3` smallint(3) NOT NULL,
  `def4` smallint(3) NOT NULL,
  `def5` smallint(3) NOT NULL,
  `mid1` smallint(3) NOT NULL,
  `mid2` smallint(3) NOT NULL,
  `mid3` smallint(3) NOT NULL,
  `mid4` smallint(3) NOT NULL,
  `mid5` smallint(3) NOT NULL,
  `fwd1` smallint(3) NOT NULL,
  `fwd2` smallint(3) NOT NULL,
  `fwd3` smallint(3) NOT NULL,
  `sub_1` smallint(3) NOT NULL,
  `sub_2` smallint(3) NOT NULL,
  `sub_3` smallint(3) NOT NULL,
  `captain` smallint(3) NOT NULL,
  `vice_captain` smallint(3) NOT NULL,
  `transfers` int(11) NOT NULL DEFAULT '0',
  `transfer_cost` int(11) NOT NULL DEFAULT '0',
  `points` smallint(3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `users_teams`
--

INSERT INTO `users_teams` (`id_team`, `id_user`, `gw`, `formation`, `gk1`, `gk2`, `def1`, `def2`, `def3`, `def4`, `def5`, `mid1`, `mid2`, `mid3`, `mid4`, `mid5`, `fwd1`, `fwd2`, `fwd3`, `sub_1`, `sub_2`, `sub_3`, `captain`, `vice_captain`, `transfers`, `transfer_cost`, `points`) VALUES
(9, 8, 1, 4, 65, 1, 67, 69, 7, 94, 169, 35, 95, 173, 174, 120, 42, 128, 18, 120, 94, 169, 128, 18, 4, -16, -2),
(11, 9, 1, 4, 2, 65, 170, 171, 33, 114, 7, 40, 173, 121, 119, 98, 104, 42, 16, 114, 98, 7, 173, 40, 0, 0, 33),
(18, 8, 2, 4, 65, 1, 67, 69, 7, 94, 169, 35, 95, 173, 174, 120, 42, 128, 18, 120, 94, 169, 128, 18, 0, 0, NULL),
(19, 9, 2, 4, 2, 65, 170, 171, 33, 114, 7, 121, 173, 40, 119, 98, 104, 42, 16, 114, 98, 7, 173, 40, 0, 0, NULL),
(20, 10, 2, 1, 188, 130, 44, 70, 69, 170, 254, 246, 266, 305, 347, 323, 43, 416, 249, 249, 323, 254, 43, 246, 0, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users_transfers`
--

CREATE TABLE `users_transfers` (
  `id_transfer` bigint(20) NOT NULL,
  `id_user` int(7) NOT NULL,
  `gw` tinyint(2) NOT NULL,
  `transfer_in` smallint(3) NOT NULL,
  `transfer_out` smallint(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `users_transfers`
--

INSERT INTO `users_transfers` (`id_transfer`, `id_user`, `gw`, `transfer_in`, `transfer_out`) VALUES
(7, 8, 1, 42, 127),
(8, 8, 1, 128, 41),
(9, 8, 1, 69, 68),
(10, 8, 1, 35, 97);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `clubs`
--
ALTER TABLE `clubs`
  ADD PRIMARY KEY (`id_club`),
  ADD UNIQUE KEY `club_name` (`club_name`),
  ADD UNIQUE KEY `club_shortname` (`club_shortname`);

--
-- Indexes for table `fixtures`
--
ALTER TABLE `fixtures`
  ADD PRIMARY KEY (`id_fixture`),
  ADD KEY `gw` (`gw`),
  ADD KEY `home` (`home`),
  ADD KEY `away` (`away`);

--
-- Indexes for table `formations`
--
ALTER TABLE `formations`
  ADD PRIMARY KEY (`id_formation`),
  ADD UNIQUE KEY `description` (`description`);

--
-- Indexes for table `gameweek`
--
ALTER TABLE `gameweek`
  ADD PRIMARY KEY (`id_gw`),
  ADD UNIQUE KEY `gw_des` (`gw`);

--
-- Indexes for table `gw_status`
--
ALTER TABLE `gw_status`
  ADD PRIMARY KEY (`id_status`),
  ADD KEY `active_gw` (`active_gw`),
  ADD KEY `points_gw` (`points_gw`);

--
-- Indexes for table `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `registration_gw` (`registration_gw`);

--
-- Indexes for table `players`
--
ALTER TABLE `players`
  ADD PRIMARY KEY (`id_player`),
  ADD KEY `position` (`position`),
  ADD KEY `club` (`club`);

--
-- Indexes for table `players_stats`
--
ALTER TABLE `players_stats`
  ADD PRIMARY KEY (`id_stat`),
  ADD KEY `id_player` (`id_player`),
  ADD KEY `id_fixture` (`id_fixture`);

--
-- Indexes for table `positions`
--
ALTER TABLE `positions`
  ADD PRIMARY KEY (`id_position`),
  ADD UNIQUE KEY `position` (`position_name`),
  ADD UNIQUE KEY `position_short` (`position_shortname`);

--
-- Indexes for table `users_leagues`
--
ALTER TABLE `users_leagues`
  ADD PRIMARY KEY (`id_league`);

--
-- Indexes for table `users_league_points`
--
ALTER TABLE `users_league_points`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_league` (`id_league`);

--
-- Indexes for table `users_teams`
--
ALTER TABLE `users_teams`
  ADD PRIMARY KEY (`id_team`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `gw` (`gw`),
  ADD KEY `gk1` (`gk1`),
  ADD KEY `gk2` (`gk2`),
  ADD KEY `def1` (`def1`),
  ADD KEY `def2` (`def2`),
  ADD KEY `def3` (`def3`),
  ADD KEY `def4` (`def4`),
  ADD KEY `def5` (`def5`),
  ADD KEY `mid1` (`mid1`),
  ADD KEY `mid2` (`mid2`),
  ADD KEY `mid3` (`mid3`),
  ADD KEY `mid4` (`mid4`),
  ADD KEY `mid5` (`mid5`),
  ADD KEY `fwd1` (`fwd1`),
  ADD KEY `fwd2` (`fwd2`),
  ADD KEY `fwd3` (`fwd3`),
  ADD KEY `formation` (`formation`),
  ADD KEY `captain` (`captain`),
  ADD KEY `vice_captain` (`vice_captain`),
  ADD KEY `sub_1` (`sub_1`),
  ADD KEY `sub_2` (`sub_2`),
  ADD KEY `sub_3` (`sub_3`);

--
-- Indexes for table `users_transfers`
--
ALTER TABLE `users_transfers`
  ADD PRIMARY KEY (`id_transfer`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `gw` (`gw`),
  ADD KEY `transfer_in` (`transfer_in`),
  ADD KEY `transfer_out` (`transfer_out`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `clubs`
--
ALTER TABLE `clubs`
  MODIFY `id_club` tinyint(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
--
-- AUTO_INCREMENT for table `fixtures`
--
ALTER TABLE `fixtures`
  MODIFY `id_fixture` smallint(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;
--
-- AUTO_INCREMENT for table `formations`
--
ALTER TABLE `formations`
  MODIFY `id_formation` tinyint(1) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `gameweek`
--
ALTER TABLE `gameweek`
  MODIFY `id_gw` tinyint(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;
--
-- AUTO_INCREMENT for table `gw_status`
--
ALTER TABLE `gw_status`
  MODIFY `id_status` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `login`
--
ALTER TABLE `login`
  MODIFY `id_user` int(7) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `players`
--
ALTER TABLE `players`
  MODIFY `id_player` smallint(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=441;
--
-- AUTO_INCREMENT for table `players_stats`
--
ALTER TABLE `players_stats`
  MODIFY `id_stat` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;
--
-- AUTO_INCREMENT for table `positions`
--
ALTER TABLE `positions`
  MODIFY `id_position` tinyint(1) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `users_leagues`
--
ALTER TABLE `users_leagues`
  MODIFY `id_league` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `users_league_points`
--
ALTER TABLE `users_league_points`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `users_teams`
--
ALTER TABLE `users_teams`
  MODIFY `id_team` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
--
-- AUTO_INCREMENT for table `users_transfers`
--
ALTER TABLE `users_transfers`
  MODIFY `id_transfer` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `fixtures`
--
ALTER TABLE `fixtures`
  ADD CONSTRAINT `fixtures_ibfk_1` FOREIGN KEY (`gw`) REFERENCES `gameweek` (`id_gw`),
  ADD CONSTRAINT `fixtures_ibfk_2` FOREIGN KEY (`home`) REFERENCES `clubs` (`id_club`),
  ADD CONSTRAINT `fixtures_ibfk_3` FOREIGN KEY (`away`) REFERENCES `clubs` (`id_club`);

--
-- Constraints for table `gw_status`
--
ALTER TABLE `gw_status`
  ADD CONSTRAINT `gw_status_ibfk_1` FOREIGN KEY (`active_gw`) REFERENCES `gameweek` (`id_gw`),
  ADD CONSTRAINT `gw_status_ibfk_2` FOREIGN KEY (`points_gw`) REFERENCES `gameweek` (`id_gw`);

--
-- Constraints for table `login`
--
ALTER TABLE `login`
  ADD CONSTRAINT `login_ibfk_1` FOREIGN KEY (`registration_gw`) REFERENCES `gameweek` (`id_gw`);

--
-- Constraints for table `players`
--
ALTER TABLE `players`
  ADD CONSTRAINT `players_ibfk_2` FOREIGN KEY (`position`) REFERENCES `positions` (`position_shortname`),
  ADD CONSTRAINT `players_ibfk_3` FOREIGN KEY (`club`) REFERENCES `clubs` (`club_shortname`);

--
-- Constraints for table `players_stats`
--
ALTER TABLE `players_stats`
  ADD CONSTRAINT `players_stats_ibfk_1` FOREIGN KEY (`id_player`) REFERENCES `players` (`id_player`),
  ADD CONSTRAINT `players_stats_ibfk_2` FOREIGN KEY (`id_fixture`) REFERENCES `fixtures` (`id_fixture`);

--
-- Constraints for table `users_league_points`
--
ALTER TABLE `users_league_points`
  ADD CONSTRAINT `users_league_points_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `login` (`id_user`),
  ADD CONSTRAINT `users_league_points_ibfk_2` FOREIGN KEY (`id_league`) REFERENCES `users_leagues` (`id_league`);

--
-- Constraints for table `users_teams`
--
ALTER TABLE `users_teams`
  ADD CONSTRAINT `users_teams_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `login` (`id_user`),
  ADD CONSTRAINT `users_teams_ibfk_10` FOREIGN KEY (`mid1`) REFERENCES `players` (`id_player`),
  ADD CONSTRAINT `users_teams_ibfk_11` FOREIGN KEY (`mid2`) REFERENCES `players` (`id_player`),
  ADD CONSTRAINT `users_teams_ibfk_12` FOREIGN KEY (`mid3`) REFERENCES `players` (`id_player`),
  ADD CONSTRAINT `users_teams_ibfk_13` FOREIGN KEY (`mid4`) REFERENCES `players` (`id_player`),
  ADD CONSTRAINT `users_teams_ibfk_14` FOREIGN KEY (`mid5`) REFERENCES `players` (`id_player`),
  ADD CONSTRAINT `users_teams_ibfk_15` FOREIGN KEY (`fwd1`) REFERENCES `players` (`id_player`),
  ADD CONSTRAINT `users_teams_ibfk_16` FOREIGN KEY (`fwd2`) REFERENCES `players` (`id_player`),
  ADD CONSTRAINT `users_teams_ibfk_17` FOREIGN KEY (`fwd3`) REFERENCES `players` (`id_player`),
  ADD CONSTRAINT `users_teams_ibfk_18` FOREIGN KEY (`formation`) REFERENCES `formations` (`id_formation`),
  ADD CONSTRAINT `users_teams_ibfk_19` FOREIGN KEY (`captain`) REFERENCES `players` (`id_player`),
  ADD CONSTRAINT `users_teams_ibfk_2` FOREIGN KEY (`gw`) REFERENCES `gameweek` (`id_gw`),
  ADD CONSTRAINT `users_teams_ibfk_20` FOREIGN KEY (`vice_captain`) REFERENCES `players` (`id_player`),
  ADD CONSTRAINT `users_teams_ibfk_21` FOREIGN KEY (`sub_1`) REFERENCES `players` (`id_player`),
  ADD CONSTRAINT `users_teams_ibfk_22` FOREIGN KEY (`sub_2`) REFERENCES `players` (`id_player`),
  ADD CONSTRAINT `users_teams_ibfk_23` FOREIGN KEY (`sub_3`) REFERENCES `players` (`id_player`),
  ADD CONSTRAINT `users_teams_ibfk_3` FOREIGN KEY (`gk1`) REFERENCES `players` (`id_player`),
  ADD CONSTRAINT `users_teams_ibfk_4` FOREIGN KEY (`gk2`) REFERENCES `players` (`id_player`),
  ADD CONSTRAINT `users_teams_ibfk_5` FOREIGN KEY (`def1`) REFERENCES `players` (`id_player`),
  ADD CONSTRAINT `users_teams_ibfk_6` FOREIGN KEY (`def2`) REFERENCES `players` (`id_player`),
  ADD CONSTRAINT `users_teams_ibfk_7` FOREIGN KEY (`def3`) REFERENCES `players` (`id_player`),
  ADD CONSTRAINT `users_teams_ibfk_8` FOREIGN KEY (`def4`) REFERENCES `players` (`id_player`),
  ADD CONSTRAINT `users_teams_ibfk_9` FOREIGN KEY (`def5`) REFERENCES `players` (`id_player`);

--
-- Constraints for table `users_transfers`
--
ALTER TABLE `users_transfers`
  ADD CONSTRAINT `users_transfers_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `login` (`id_user`),
  ADD CONSTRAINT `users_transfers_ibfk_2` FOREIGN KEY (`gw`) REFERENCES `gameweek` (`id_gw`),
  ADD CONSTRAINT `users_transfers_ibfk_3` FOREIGN KEY (`transfer_in`) REFERENCES `players` (`id_player`),
  ADD CONSTRAINT `users_transfers_ibfk_4` FOREIGN KEY (`transfer_out`) REFERENCES `players` (`id_player`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
