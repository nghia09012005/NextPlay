-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Dec 04, 2025 at 08:33 PM
-- Server version: 10.4.11-MariaDB
-- PHP Version: 7.4.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ltw_game_shop`
--
CREATE DATABASE IF NOT EXISTS `ltw_game_shop` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `ltw_game_shop`;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `uid` int(11) NOT NULL,
  `startdate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`uid`, `startdate`) VALUES
(3, '2025-01-01');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `uid` int(11) NOT NULL,
  `status` varchar(30) NOT NULL,
  `created_date` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`uid`, `status`, `created_date`) VALUES
(1, 'active', '2025-11-26'),
(2, 'active', '2025-11-26');

-- --------------------------------------------------------

--
-- Table structure for table `cart_game`
--

CREATE TABLE `cart_game` (
  `Gid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `cart_status` varchar(30) NOT NULL,
  `quantity` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `catId` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`catId`, `name`, `description`) VALUES
(1, 'Action', 'Action games'),
(2, 'Adventure', 'Adventure games'),
(3, 'RPG', 'Role-playing games where you assume the roles of characters in a fictional setting.'),
(6, 'Indie', 'Games created by individuals or smaller development teams.'),
(7, 'Simulation', 'Games designed to simulate real-world activities.'),
(8, 'MOBA', 'Multiplayer Online Battle Arena games.'),
(9, 'Casual', 'Games targeted at a mass audience of casual gamers.'),
(10, 'FPS', 'First-person shooter games.'),
(11, 'Horror', 'Games designed to scare the player.'),
(12, 'Sports', 'Games that simulate the practice of sports.');

-- --------------------------------------------------------

--
-- Table structure for table `contactmessages`
--

CREATE TABLE `contactmessages` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('new','read','replied') COLLATE utf8mb4_unicode_ci DEFAULT 'new',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contactmessages`
--

INSERT INTO `contactmessages` (`id`, `name`, `email`, `subject`, `message`, `status`, `created_at`) VALUES
(1, 'Huỳnh Minh Tiến', 'randy2032005@gmail.com', 'Web Lỏ', 'Admin ơi hãy sửa lại giúp em với, ban đầu em định nạp 50000 nhưng mà lỡ bấm lộn thành 500000 giờ má em chửi em quá trời, có cách nào để trả lại tiền cho em được không. Em cảm ơn!!!', 'new', '2025-12-04 19:03:10');

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `uid` int(11) NOT NULL,
  `balance` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`uid`, `balance`) VALUES
(1, '100.00'),
(2, '50.00'),
(3, NULL),
(4, NULL),
(5, NULL),
(7, NULL),
(24, '100.00'),
(25, '6210100.00'),
(26, '100.00'),
(28, '100.00'),
(29, '100.00'),
(31, '0.00'),
(36, '8845000.00'),
(37, '0.00'),
(38, '0.00');

-- --------------------------------------------------------

--
-- Table structure for table `game`
--

CREATE TABLE `game` (
  `Gid` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `version` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,0) NOT NULL,
  `adminid` int(11) DEFAULT NULL,
  `publisherid` int(11) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `tags` text DEFAULT NULL,
  `developer` varchar(255) DEFAULT NULL,
  `publisher` varchar(255) DEFAULT NULL,
  `release_date` varchar(50) DEFAULT NULL,
  `rating` float DEFAULT 0,
  `reviews` int(11) DEFAULT 0,
  `thumbnail` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `game`
--

INSERT INTO `game` (`Gid`, `name`, `version`, `description`, `price`, `adminid`, `publisherid`, `category`, `tags`, `developer`, `publisher`, `release_date`, `rating`, `reviews`, `thumbnail`) VALUES
(103, 'Elden Ring', NULL, 'THE NEW FANTASY ACTION RPG. Rise, Tarnished, and be guided by grace to brandish the power of the Elden Ring and become an Elden Lord in the Lands Between.', '990000', NULL, NULL, 'RPG', '[\"Souls-like\",\"Open World\"]', 'FromSoftware Inc.', 'Bandai Namco Entertainment', '25 Feb, 2022', 4.8, 520000, 'https://cdn.akamai.steamstatic.com/steam/apps/1245620/header.jpg'),
(104, 'Cyberpunk 2077', NULL, 'Cyberpunk 2077 is an open-world, action-adventure RPG set in the megalopolis of Night City, where you play as a cyberpunk mercenary wrapped up in a do-or-die fight for survival.', '850000', NULL, NULL, 'Action', '[\"Sci-fi\",\"FPS\"]', 'CD PROJEKT RED', 'CD PROJEKT RED', '10 Dec, 2020', 4.5, 600000, 'https://cdn.akamai.steamstatic.com/steam/apps/1091500/header.jpg'),
(105, 'God of War Ragnarök', NULL, 'Kratos and Atreus must journey to each of the Nine Realms in search of answers as Asgardian forces prepare for a prophesied battle that will end the world.', '1200000', NULL, NULL, 'Adventure', '[\"Story Rich\",\"Action\"]', 'Santa Monica Studio', 'PlayStation Publishing', '19 Sep, 2024', 4.9, 15000, 'https://cdn.akamai.steamstatic.com/steam/apps/2322010/header.jpg'),
(106, 'Hollow Knight', NULL, 'Forge your own path in Hollow Knight! An epic action adventure through a vast ruined kingdom of insects and heroes. Explore twisting caverns, battle tainted creatures and befriend bizarre bugs, all in a classic, hand-drawn 2D style.', '165000', NULL, NULL, 'Indie', '[\"Metroidvania\",\"2D\"]', 'Team Cherry', 'Team Cherry', '24 Feb, 2017', 4.9, 300000, 'https://cdn.akamai.steamstatic.com/steam/apps/367520/header.jpg'),
(107, 'Stardew Valley', NULL, 'You\'ve inherited your grandfather\'s old farm plot in Stardew Valley. Armed with hand-me-down tools and a few coins, you set out to begin your new life.', '165000', NULL, NULL, 'Simulation', '[\"Farming\",\"Relaxing\"]', 'ConcernedApe', 'ConcernedApe', '26 Feb, 2016', 4.9, 550000, 'https://cdn.akamai.steamstatic.com/steam/apps/413150/header.jpg'),
(108, 'Dota 2', NULL, 'Every day, millions of players worldwide enter battle as one of over a hundred Dota heroes. And no matter if it\'s their 10th hour of play or 1,000th, there\'s always something new to discover.', '0', NULL, NULL, 'MOBA', '[\"Multiplayer\",\"Strategy\"]', 'Valve', 'Valve', '9 Jul, 2013', 4.6, 2000000, 'https://cdn.akamai.steamstatic.com/steam/apps/570/header.jpg'),
(109, 'The Witcher 3: Wild Hunt', NULL, 'You are Geralt of Rivia, mercenary monster slayer. Before you stands a war-torn, monster-infested continent you can explore at will. Your current contract? Tracking down the Child of Prophecy, a living weapon that can alter the shape of the world.', '390000', NULL, NULL, 'RPG', '[\"Open World\",\"Story Rich\"]', 'CD PROJEKT RED', 'CD PROJEKT RED', '18 May, 2015', 4.9, 700000, 'https://cdn.akamai.steamstatic.com/steam/apps/292030/header.jpg'),
(110, 'Red Dead Redemption 2', NULL, 'Winner of over 175 Game of the Year Awards and recipient of over 250 perfect scores, RDR2 is the epic tale of outlaw Arthur Morgan and the infamous Van der Linde gang, on the run across America at the dawn of the modern age.', '1000000', NULL, NULL, 'Adventure', '[\"Open World\",\"Western\"]', 'Rockstar Games', 'Rockstar Games', '5 Dec, 2019', 4.8, 500000, 'https://cdn.akamai.steamstatic.com/steam/apps/1174180/header.jpg'),
(111, 'Grand Theft Auto V', NULL, 'Grand Theft Auto V for PC offers players the option to explore the award-winning world of Los Santos and Blaine County in resolutions of up to 4k and beyond, as well as the chance to experience the game running at 60 frames per second.', '450000', NULL, NULL, 'Action', '[\"Open World\",\"Crime\"]', 'Rockstar North', 'Rockstar Games', '14 Apr, 2015', 4.7, 1500000, 'https://cdn.akamai.steamstatic.com/steam/apps/271590/header.jpg'),
(112, 'Hades', NULL, 'Defy the god of the dead as you hack and slash out of the Underworld in this rogue-like dungeon crawler from the creators of Bastion, Transistor, and Pyre.', '220000', NULL, NULL, 'Indie', '[\"Roguelike\",\"Action\"]', 'Supergiant Games', 'Supergiant Games', '17 Sep, 2020', 4.9, 230000, 'https://cdn.akamai.steamstatic.com/steam/apps/1145360/header.jpg'),
(113, 'Terraria', NULL, 'Dig, fight, explore, build! Nothing is impossible in this action-packed adventure game. The world is your canvas and the ground itself is your paint.', '120000', NULL, NULL, 'Indie', '[\"Sandbox\",\"Survival\"]', 'Re-Logic', 'Re-Logic', '16 May, 2011', 4.8, 1000000, 'https://cdn.akamai.steamstatic.com/steam/apps/105600/header.jpg'),
(114, 'Among Us', NULL, 'An online and local party game of teamwork and betrayal for 4-15 players... in space!', '70000', NULL, NULL, 'Casual', '[\"Multiplayer\",\"Social Deduction\"]', 'Innersloth', 'Innersloth', '16 Nov, 2018', 4.5, 600000, 'https://cdn.akamai.steamstatic.com/steam/apps/945360/header.jpg'),
(115, 'Minecraft', NULL, 'Prepare for an adventure of limitless possibilities as you build, mine, battle mobs, and explore the ever-changing Minecraft landscape.', '650000', NULL, NULL, 'Simulation', '[\"Sandbox\",\"Survival\"]', 'Mojang Studios', 'Xbox Game Studios', '18 Nov, 2011', 4.8, 900000, 'https://image.api.playstation.com/vulcan/img/rnd/202010/2618/w48z6bzefZPrRcJHc7L8SO66.png'),
(116, 'Valorant', NULL, 'Blend your style and experience on a global, competitive stage. You have 13 rounds to attack and defend your side using sharp gunplay and tactical abilities.', '0', NULL, NULL, 'FPS', '[\"Multiplayer\",\"Tactical\"]', 'Riot Games', 'Riot Games', '2 Jun, 2020', 4.4, 300000, 'https://cdn.dribbble.com/users/2340268/screenshots/11924683/media/4c029671954593683833230552733979.jpg'),
(117, 'League of Legends', NULL, 'League of Legends is a team-based game with over 140 champions to make epic plays with.', '0', NULL, NULL, 'MOBA', '[\"Multiplayer\",\"Strategy\"]', 'Riot Games', 'Riot Games', '27 Oct, 2009', 4.5, 800000, 'https://cdn1.epicgames.com/offer/24b9b5e323bc40eea252a10cdd3b2f10/EGS_LeagueofLegends_RiotGames_S1_2560x1440-80471666c140f790f28dff68d72c384b'),
(118, 'Genshin Impact', NULL, 'Step into Teyvat, a vast world teeming with life and flowing with elemental energy. You and your sibling arrived here from another world.', '0', NULL, NULL, 'RPG', '[\"Open World\",\"Anime\"]', 'miHoYo', 'miHoYo', '28 Sep, 2020', 4.6, 400000, 'https://cdn1.epicgames.com/offer/879b0d8776ab46a59a129983ba78f0ce/genshin-impact-1_2560x1440-5556236553d7b53d4b2767f4c54b3706'),
(119, 'FIFA 23', NULL, 'Experience The World\'s Game with 19,000+ players, 700+ teams, 100+ stadiums, and over 30 leagues.', '1090000', NULL, NULL, 'Sports', '[\"Football\",\"Multiplayer\"]', 'EA Canada', 'Electronic Arts', '30 Sep, 2022', 4, 100000, 'https://cdn.akamai.steamstatic.com/steam/apps/1811260/header.jpg'),
(120, 'NBA 2K23', NULL, 'Rise to the occasion and realize your full potential in NBA 2K23. Prove yourself against the best players in the world.', '1000000', NULL, NULL, 'Sports', '[\"Basketball\",\"Simulation\"]', 'Visual Concepts', '2K', '9 Sep, 2022', 3.8, 50000, 'https://cdn.akamai.steamstatic.com/steam/apps/1919590/header.jpg'),
(121, 'Sekiro: Shadows Die Twice', NULL, 'Carve your own clever path to vengeance in the award winning adventure from developer FromSoftware, creators of Bloodborne and the Dark Souls series.', '1290000', NULL, NULL, 'Action', '[\"Souls-like\",\"Difficult\"]', 'FromSoftware Inc.', 'Activision', '22 Mar, 2019', 4.9, 200000, 'https://cdn.akamai.steamstatic.com/steam/apps/814380/header.jpg'),
(122, 'Dark Souls III', NULL, 'Dark Souls III continues to push the boundaries with the latest, ambitious chapter in the critically-acclaimed and genre-defining series.', '990000', NULL, NULL, 'RPG', '[\"Souls-like\",\"Dark Fantasy\"]', 'FromSoftware Inc.', 'Bandai Namco Entertainment', '12 Apr, 2016', 4.8, 350000, 'https://cdn.akamai.steamstatic.com/steam/apps/374320/header.jpg'),
(123, 'Resident Evil 4 Remake', NULL, 'Survival is just the beginning. Six years have passed since the biological disaster in Raccoon City. Leon S. Kennedy, one of the survivors, tracks the president\'s kidnapped daughter to a secluded European village.', '1300000', NULL, NULL, 'Horror', '[\"Action\",\"Survival Horror\"]', 'CAPCOM Co., Ltd.', 'CAPCOM Co., Ltd.', '24 Mar, 2023', 4.9, 120000, 'https://cdn.akamai.steamstatic.com/steam/apps/2050650/header.jpg'),
(124, 'Silent Hill 2', NULL, 'Having received a letter from his deceased wife, James heads to where they shared so many memories, in the hope of seeing her one more time: Silent Hill.', '1100000', NULL, NULL, 'Horror', '[\"Psychological Horror\"]', 'Bloober Team SA', 'Konami Digital Entertainment', 'TBA', 4.5, 0, 'https://cdn.akamai.steamstatic.com/steam/apps/2124490/header.jpg'),
(125, 'Dead Space', NULL, 'The sci-fi survival horror classic Dead Space returns, completely rebuilt from the ground up to offer a deeper and more immersive experience.', '1000000', NULL, NULL, 'Horror', '[\"Sci-fi\",\"Survival Horror\"]', 'Motive', 'Electronic Arts', '27 Jan, 2023', 4.8, 30000, 'https://cdn.akamai.steamstatic.com/steam/apps/1693980/header.jpg'),
(126, 'Final Fantasy VII Remake', NULL, 'Cloud Strife, an ex-SOLDIER operative, descends on the mako-powered city of Midgar. The world of the timeless classic FINAL FANTASY VII is reborn.', '1700000', NULL, NULL, 'RPG', '[\"JRPG\",\"Story Rich\"]', 'Square Enix', 'Square Enix', '17 Jun, 2022', 4.7, 40000, 'https://cdn.akamai.steamstatic.com/steam/apps/1462040/header.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `game_cat`
--

CREATE TABLE `game_cat` (
  `Gid` int(11) NOT NULL,
  `catId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `game_cat`
--

INSERT INTO `game_cat` (`Gid`, `catId`) VALUES
(103, 3),
(104, 1),
(105, 2),
(106, 6),
(107, 7),
(108, 8),
(109, 3),
(110, 2),
(111, 1),
(112, 6),
(113, 6),
(114, 9),
(115, 7),
(116, 10),
(117, 8),
(118, 3),
(119, 12),
(120, 12),
(121, 1),
(122, 3),
(123, 11),
(124, 11),
(125, 11),
(126, 3);

-- --------------------------------------------------------

--
-- Table structure for table `library`
--

CREATE TABLE `library` (
  `uid` int(11) NOT NULL,
  `libname` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `library`
--

INSERT INTO `library` (`uid`, `libname`) VALUES
(1, 'MyLibrary'),
(2, 'JaneLib'),
(25, 'Payed'),
(36, 'Payed');

-- --------------------------------------------------------

--
-- Table structure for table `lib_game`
--

CREATE TABLE `lib_game` (
  `Gid` int(11) NOT NULL,
  `libname` varchar(50) NOT NULL,
  `uid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `lib_game`
--

INSERT INTO `lib_game` (`Gid`, `libname`, `uid`) VALUES
(103, 'Payed', 36),
(107, 'Payed', 36),
(109, 'Payed', 25),
(124, 'Payed', 25);

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `author_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `views` int(11) DEFAULT 0,
  `category` varchar(100) DEFAULT NULL,
  `source` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`id`, `title`, `content`, `thumbnail`, `author_id`, `created_at`, `views`, `category`, `source`) VALUES
(1, 'Giải đấu NextPlay Championship 2025 chính thức khởi tranh', 'Sự kiện eSports lớn nhất năm đã trở lại với tổng giải thưởng lên đến 1 tỷ đồng. Các đội tuyển hàng đầu sẽ tranh tài...', 'https://images.unsplash.com/photo-1542751371-adc38448a05e?q=80&w=2070&auto=format&fit=crop', 25, '2025-12-01 08:00:00', 1218, 'Esports', 'NextPlay Esports'),
(2, 'Top 10 game nhập vai đáng chơi nhất tháng 12', 'Tổng hợp những tựa game RPG đình đám vừa ra mắt. Danh sách bao gồm những cái tên được mong chờ nhất...', 'https://images.unsplash.com/photo-1511512578047-dfb367046420?q=80&w=2071&auto=format&fit=crop', 25, '2025-11-28 09:30:00', 857, 'Review', 'GameK'),
(3, 'Bản cập nhật mới của Cyber Future có gì hot?', 'Nhà phát triển vừa tung ra bản vá lỗi lớn cùng DLC mở rộng bản đồ. Người chơi sẽ được trải nghiệm khu vực mới...', 'https://images.unsplash.com/photo-1552820728-8b83bb6b773f?q=80&w=2070&auto=format&fit=crop', 25, '2025-11-25 14:15:00', 2103, 'Tin Game', 'GenK'),
(4, 'Hướng dẫn build PC chơi game giá rẻ năm 2025', 'Tối ưu hiệu năng trên giá thành với cấu hình PC gaming tầm trung. Chỉ với 15 triệu đồng, bạn có thể chiến tốt...', 'https://images.unsplash.com/photo-1587202372775-e229f172b9d7?q=80&w=2070&auto=format&fit=crop', 25, '2025-11-20 10:00:00', 3501, 'Công nghệ', 'TinhTe'),
(5, 'Review: Lost Kingdom - Siêu phẩm hay bom xịt?', 'Đánh giá chi tiết tựa game được mong chờ nhất năm. Cốt truyện sâu sắc nhưng gameplay còn nhiều sạn...', 'https://images.unsplash.com/photo-1538481199705-c710c4e965fc?q=80&w=2165&auto=format&fit=crop', 25, '2025-11-15 16:45:00', 1508, 'Review', 'Game4V'),
(6, 'Cộng đồng game thủ Việt nói gì về sự kiện sắp tới?', 'Những ý kiến trái chiều xoay quanh việc thay đổi thể thức thi đấu. Nhiều người ủng hộ nhưng cũng không ít...', 'https://images.unsplash.com/photo-1493711662062-fa541adb3fc8?q=80&w=2070&auto=format&fit=crop', 25, '2025-11-10 11:20:00', 501, 'Cộng đồng', 'ThanhNien'),
(7, 'Sony công bố PlayStation 6: Cấu hình khủng khiếp', 'Những thông tin rò rỉ đầu tiên về thế hệ console tiếp theo. Sức mạnh xử lý đồ họa được nâng cấp gấp đôi...', 'https://images.unsplash.com/photo-1605901309584-818e25960b8f?q=80&w=2000&auto=format&fit=crop', 25, '2025-11-05 08:30:00', 5000, 'Công nghệ', 'Sony'),
(8, 'GTA VI lộ diện trailer mới: Đồ họa siêu thực', 'Rockstar Games tiếp tục khiến cộng đồng đứng ngồi không yên. Trailer mới hé lộ bối cảnh Vice City hiện đại...', 'https://images.unsplash.com/photo-1628260412297-a3377e45006f?q=80&w=2000&auto=format&fit=crop', 25, '2025-11-01 20:00:00', 8009, 'Tin Game', 'Rockstar');

-- --------------------------------------------------------

--
-- Table structure for table `pagecontent`
--

CREATE TABLE `pagecontent` (
  `id` int(11) NOT NULL,
  `page_key` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `section_key` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content_value` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pagecontent`
--

INSERT INTO `pagecontent` (`id`, `page_key`, `section_key`, `content_value`, `updated_at`) VALUES
(1, 'contact', 'address', '14/17 Khu phố Tây B, Dĩ An, TP.HCM', '2025-12-04 19:12:08'),
(2, 'contact', 'email', 'support@nextplay.com', '2025-12-04 19:06:28'),
(3, 'contact', 'hotline', '0948 467 394', '2025-12-04 19:12:22'),
(4, 'contact', 'facebook', 'https://www.facebook.com/mintieeen/', '2025-12-04 19:13:31'),
(5, 'contact', 'instagram', '#', '2025-12-04 19:06:28'),
(6, 'contact', 'twitter', '#', '2025-12-04 19:06:28'),
(7, 'contact', 'youtube', '#', '2025-12-04 19:06:28');

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `id` int(11) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`content`)),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `slug`, `title`, `content`, `updated_at`) VALUES
(1, 'about', 'Về Chúng Tôi', '{\"hero_title\":\"NEXTPLAY - KỶ NGUYÊN GAME MỚI\",\"hero_subtitle\":\"Nơi đam mê hội tụ, nơi game thủ tỏa sáng. Chúng tôi không chỉ bán game, chúng tôi kiến tạo trải nghiệm.\",\"intro_title\":\"Sứ Mệnh Của Chúng Tôi\",\"intro_text\":\"NextPlay ra đời với sứ mệnh kết nối cộng đồng game thủ Việt Nam với những tựa game đỉnh cao thế giới. Chúng tôi cam kết mang đến nền tảng phân phối game bản quyền uy tín, tốc độ và giá cả hợp lý nhất. Hơn cả một cửa hàng, NextPlay là ngôi nhà chung cho những ai yêu thích thế giới ảo, nơi bạn có thể tìm thấy niềm vui, sự thử thách và những người bạn đồng hành.\",\"intro_image\":\"https:\\/\\/images.unsplash.com\\/photo-1542751371-adc38448a05e?q=80&w=2070&auto=format&fit=crop\",\"features\":[{\"title\":\"Kho Game Khổng Lồ\",\"desc\":\"Hàng ngàn tựa game AAA và Indie được cập nhật liên tục, đáp ứng mọi gu chơi game.\",\"icon\":\"bi-controller\"},{\"title\":\"Bảo Mật Tuyệt Đối\",\"desc\":\"Hệ thống thanh toán và bảo vệ tài khoản chuẩn quốc tế, an tâm tuyệt đối khi giao dịch.\",\"icon\":\"bi-shield-check\"},{\"title\":\"Hỗ Trợ 24\\/7\",\"desc\":\"Đội ngũ hỗ trợ nhiệt tình, chuyên nghiệp, sẵn sàng giải đáp mọi thắc mắc bất kể ngày đêm.\",\"icon\":\"bi-headset\"}],\"stats\":[{\"value\":\"10K+\",\"label\":\"Người dùng tin tưởng\"},{\"value\":\"500+\",\"label\":\"Tựa game bản quyền\"},{\"value\":\"99%\",\"label\":\"Đánh giá hài lòng\"}]}', '2025-12-02 15:17:23');

-- --------------------------------------------------------

--
-- Table structure for table `publisher`
--

CREATE TABLE `publisher` (
  `uid` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `taxcode` varchar(50) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `publisher`
--

INSERT INTO `publisher` (`uid`, `description`, `taxcode`, `location`) VALUES
(4, 'Top game publisher', 'TX12345', 'New York'),
(30, '11', '1234567891011', '11');

-- --------------------------------------------------------

--
-- Table structure for table `receives_feedback`
--

CREATE TABLE `receives_feedback` (
  `feedback_time` date NOT NULL,
  `customerid` int(11) NOT NULL,
  `Gid` int(11) DEFAULT NULL,
  `publisherid` int(11) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `rating` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `receives_feedback`
--

INSERT INTO `receives_feedback` (`feedback_time`, `customerid`, `Gid`, `publisherid`, `content`, `rating`) VALUES
('2025-12-04', 25, 103, NULL, 'game hay đáng để trải nghiệm', 4);

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `customerid` int(11) NOT NULL,
  `news_id` int(11) NOT NULL,
  `review_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `content` text DEFAULT NULL,
  `rating` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `review`
--

INSERT INTO `review` (`customerid`, `news_id`, `review_time`, `content`, `rating`) VALUES
(1, 2, '2025-11-29 00:25:45', 'Có ai lập team tham gia không?', 5),
(1, 5, '2025-12-01 00:25:45', 'Có ai lập team tham gia không?', 5),
(2, 1, '2025-12-02 00:25:45', 'Sony làm tốt lắm, PS6 chắc chắn sẽ hot.', 5),
(2, 2, '2025-11-27 00:25:45', 'Cảm ơn admin đã chia sẻ thông tin.', 4),
(2, 3, '2025-12-02 00:25:45', 'Sony làm tốt lắm, PS6 chắc chắn sẽ hot.', 4),
(2, 4, '2025-11-25 00:25:45', 'Thông tin rất chi tiết, 10 điểm.', 5),
(2, 5, '2025-11-25 00:25:45', 'Thông tin rất chi tiết, 10 điểm.', 4),
(25, 5, '2025-12-03 06:41:54', 'game cơ chế quá dở', 1),
(25, 8, '2025-12-03 08:45:44', 'Game hay đáng để mong đợi', 5),
(36, 1, '2025-12-04 16:12:27', 'ngu quá', 3);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `uid` int(11) NOT NULL,
  `uname` varchar(255) NOT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `DOB` date DEFAULT NULL,
  `lname` varchar(255) DEFAULT NULL,
  `fname` varchar(255) DEFAULT NULL,
  `failed_attempts` int(11) DEFAULT 0,
  `lockout_time` datetime DEFAULT NULL,
  `password_changed_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`uid`, `uname`, `avatar`, `email`, `password`, `DOB`, `lname`, `fname`, `failed_attempts`, `lockout_time`, `password_changed_at`) VALUES
(1, 'john_doe', 'avatar1.png', 'john@example.com', 'pass123', '1990-01-01', 'Doe', 'John', 0, NULL, '2025-12-05 00:26:33'),
(2, 'jane_smith', 'avatar2.png', 'jane@example.com', 'pass456', '1992-05-10', 'Smith', 'Jane', 0, NULL, '2025-12-05 00:26:33'),
(3, 'admin1', NULL, 'admin1@example.com', 'adminpass', '1985-03-15', 'Admin', 'One', 0, NULL, '2025-12-05 00:26:33'),
(4, 'publisher1', NULL, 'pub1@example.com', 'pubpass', '1980-07-20', 'Publisher', 'One', 0, NULL, '2025-12-05 00:26:33'),
(5, 'MinTieeen', NULL, 'randy2032005@gmail.com', '$2y$10$oFFceR6mwmh1vu8cVgKEnOht6sevmXIWyQFNnX8cAuKnmGCtmpyrG', '2005-03-20', 'Huỳnh', 'Minh Tiến', 0, NULL, '2025-12-05 00:26:33'),
(7, 'minhtien', 'profile_7_692694190b0277.67205973.jpg', 'tien@gmail.com', '$2y$10$BDM4FPwU.KtfHRbU8i6QU.fO.U4TPKtTS9O7pwSSQR9K18JjOVDpe', '2005-03-20', 'Huỳnh', 'Minh Tiến', 0, NULL, '2025-12-05 00:26:33'),
(9, 'minhtien203', 'profile_9_6927164961f507.19706739.jpg', 'tien203@gmail.com', '$2y$10$gfbgAHU1BFietHvs13TY5eAadhVOCyh4lQrLTUYLibFeCkKOir6uy', '2005-03-20', 'minh', 'tiến', 0, NULL, '2025-12-05 00:26:33'),
(10, 'lu', 'profile_10_692d0e41340c81.98419979.png', 'lu@gmail.com', '$2y$10$MBj8p3T4s13PZj.JhpKyNeeA86qPtgDaQwAtj2mwZaeVwAy5b8wzS', '2005-03-20', 'Lu', 'Lu', 0, NULL, '2025-12-05 00:26:33'),
(24, 'testuser_692d6acf70bb9', NULL, 'test_692d6acf70bbc@example.com', '$2y$10$/wmV/kNdqQhkf/8B2GIvQuOeVTr57rvAbW5TYgDs1Lwg8ILP2HuMK', '2000-01-01', 'Test', 'User', 0, NULL, '2025-12-05 00:26:33'),
(25, 'minhtien2005', 'https://res.cloudinary.com/dlmaw4de5/image/upload/v1764854901/gy3v8vrd6uoukelxbor6.jpg', 'randy2@gmail.com', '$2y$10$WMtFyXVGMod/qu6YLgk12uW8hcLZHEBmhKW3bEC.9xS3B4nAt3Tk.', '2005-03-20', 'Huỳnh Minh', 'Tiến', 0, NULL, '2025-12-05 00:26:33'),
(26, 'mtien@gmail.com', NULL, 'mtien@gmail.com', 'tien2032005', '2005-03-20', 'Huỳnh', 'Minh Tiến', 0, NULL, '2025-12-05 00:26:33'),
(28, 'newuser1', NULL, 'newuser11@example.com', '$2y$10$4B1sISOfrE8S0v6IsbtVKeUYmyEaIamnjD3SIGf2xCpfNTNq2q6yS', '2000-01-01', 'Last', 'First', 0, NULL, '2025-12-05 00:26:33'),
(29, 'minhtien2', NULL, 'tien2@gmail.com', '$2y$10$hPH5nQWZ7QoWO3um/wmVxOvskjmHFALm9xU1C0DGfnOgD17BumRem', '2005-03-20', '1', '1', 0, NULL, '2025-12-05 00:26:33'),
(30, 'MinhTien Studio', NULL, 'mtienn@gmail.com', '$2y$10$Mt8geAaY0qHcciJftJikkO/8cqk6FLZWczA.vWtHQ2xxXNpoXOXFG', '2005-03-20', 'Huỳnh Minh', 'Tiến', 0, NULL, '2025-12-05 00:26:33'),
(31, 'tien', 'https://res.cloudinary.com/dlmaw4de5/image/upload/v1764863096/p2amdbvwoazomtgidzfi.jpg', 'tien2005@gmail.com', '$2y$10$WMtFyXVGMod/qu6YLgk12uW8hcLZHEBmhKW3bEC.9xS3B4nAt3Tk.', '2005-03-20', 'Minh', 'Tien', 0, NULL, '2025-12-05 00:26:33'),
(36, 'tien2032005', 'https://res.cloudinary.com/dlmaw4de5/image/upload/v1764863876/fkcvzvfkjhfnunx0uhcr.jpg', 'tien2032005@gmail.com', '$2y$10$WMtFyXVGMod/qu6YLgk12uW8hcLZHEBmhKW3bEC.9xS3B4nAt3Tk.', '2005-03-20', 'Minh', 'Tien', 0, NULL, '2025-12-05 00:26:33'),
(37, 'MinhTien2032005', NULL, 'tien.huynhminhcse@hcmut.edu.vn', '$2y$10$td4ESCCLhfgViQLOmHfQQOypaQSidfhO75c3oEoS2S/.15ZiHNPvK', '2005-03-20', 'Huỳnh', 'Minh Tiến', 0, NULL, '2025-12-05 00:26:33'),
(38, 'minhtien2003', NULL, 'rrr@gmail.com', '$2y$10$kiydZLU2f7f2afISytYZnujdB7O0Xm2ob./biT57EYgKoAumjnj7m', '2005-03-20', 'Minh', 'Tien', 6, '2025-12-04 19:29:24', '2025-12-05 00:37:48');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `uid` int(11) NOT NULL,
  `wishname` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`uid`, `wishname`) VALUES
(1, 'WishList1'),
(2, 'WishList2'),
(24, 'Cart'),
(25, 'Cart'),
(36, 'Cart'),
(37, 'Cart'),
(38, 'Cart');

-- --------------------------------------------------------

--
-- Table structure for table `wish_game`
--

CREATE TABLE `wish_game` (
  `Gid` int(11) NOT NULL,
  `wishname` varchar(50) NOT NULL,
  `uid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`uid`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`uid`,`status`);

--
-- Indexes for table `cart_game`
--
ALTER TABLE `cart_game`
  ADD PRIMARY KEY (`Gid`,`uid`,`cart_status`),
  ADD KEY `uid` (`uid`,`cart_status`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`catId`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `contactmessages`
--
ALTER TABLE `contactmessages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`uid`);

--
-- Indexes for table `game`
--
ALTER TABLE `game`
  ADD PRIMARY KEY (`Gid`),
  ADD KEY `adminid` (`adminid`),
  ADD KEY `publisherid` (`publisherid`);

--
-- Indexes for table `game_cat`
--
ALTER TABLE `game_cat`
  ADD PRIMARY KEY (`Gid`,`catId`),
  ADD KEY `catId` (`catId`);

--
-- Indexes for table `library`
--
ALTER TABLE `library`
  ADD PRIMARY KEY (`uid`,`libname`);

--
-- Indexes for table `lib_game`
--
ALTER TABLE `lib_game`
  ADD PRIMARY KEY (`Gid`,`libname`,`uid`),
  ADD KEY `uid` (`uid`,`libname`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`),
  ADD KEY `author_id` (`author_id`);

--
-- Indexes for table `pagecontent`
--
ALTER TABLE `pagecontent`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_content` (`page_key`,`section_key`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `publisher`
--
ALTER TABLE `publisher`
  ADD PRIMARY KEY (`uid`);

--
-- Indexes for table `receives_feedback`
--
ALTER TABLE `receives_feedback`
  ADD PRIMARY KEY (`feedback_time`,`customerid`),
  ADD KEY `customerid` (`customerid`),
  ADD KEY `Gid` (`Gid`),
  ADD KEY `publisherid` (`publisherid`);

--
-- Indexes for table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`customerid`,`news_id`),
  ADD KEY `news_id` (`news_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`uid`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`uid`,`wishname`);

--
-- Indexes for table `wish_game`
--
ALTER TABLE `wish_game`
  ADD PRIMARY KEY (`Gid`,`wishname`,`uid`),
  ADD KEY `uid` (`uid`,`wishname`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `catId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `contactmessages`
--
ALTER TABLE `contactmessages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `game`
--
ALTER TABLE `game`
  MODIFY `Gid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=127;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `pagecontent`
--
ALTER TABLE `pagecontent`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `publisher`
--
ALTER TABLE `publisher`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `admin_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `user` (`uid`);

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `customer` (`uid`);

--
-- Constraints for table `cart_game`
--
ALTER TABLE `cart_game`
  ADD CONSTRAINT `cart_game_ibfk_1` FOREIGN KEY (`Gid`) REFERENCES `game` (`Gid`),
  ADD CONSTRAINT `cart_game_ibfk_2` FOREIGN KEY (`uid`,`cart_status`) REFERENCES `cart` (`uid`, `status`);

--
-- Constraints for table `customer`
--
ALTER TABLE `customer`
  ADD CONSTRAINT `customer_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `user` (`uid`);

--
-- Constraints for table `game`
--
ALTER TABLE `game`
  ADD CONSTRAINT `game_ibfk_1` FOREIGN KEY (`adminid`) REFERENCES `admin` (`uid`),
  ADD CONSTRAINT `game_ibfk_2` FOREIGN KEY (`publisherid`) REFERENCES `publisher` (`uid`);

--
-- Constraints for table `game_cat`
--
ALTER TABLE `game_cat`
  ADD CONSTRAINT `game_cat_ibfk_1` FOREIGN KEY (`Gid`) REFERENCES `game` (`Gid`),
  ADD CONSTRAINT `game_cat_ibfk_2` FOREIGN KEY (`catId`) REFERENCES `category` (`catId`);

--
-- Constraints for table `library`
--
ALTER TABLE `library`
  ADD CONSTRAINT `library_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `customer` (`uid`);

--
-- Constraints for table `lib_game`
--
ALTER TABLE `lib_game`
  ADD CONSTRAINT `lib_game_ibfk_1` FOREIGN KEY (`Gid`) REFERENCES `game` (`Gid`),
  ADD CONSTRAINT `lib_game_ibfk_2` FOREIGN KEY (`uid`,`libname`) REFERENCES `library` (`uid`, `libname`);

--
-- Constraints for table `news`
--
ALTER TABLE `news`
  ADD CONSTRAINT `news_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `user` (`uid`);

--
-- Constraints for table `publisher`
--
ALTER TABLE `publisher`
  ADD CONSTRAINT `publisher_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `user` (`uid`);

--
-- Constraints for table `receives_feedback`
--
ALTER TABLE `receives_feedback`
  ADD CONSTRAINT `receives_feedback_ibfk_1` FOREIGN KEY (`customerid`) REFERENCES `customer` (`uid`),
  ADD CONSTRAINT `receives_feedback_ibfk_2` FOREIGN KEY (`Gid`) REFERENCES `game` (`Gid`),
  ADD CONSTRAINT `receives_feedback_ibfk_3` FOREIGN KEY (`publisherid`) REFERENCES `publisher` (`uid`);

--
-- Constraints for table `review`
--
ALTER TABLE `review`
  ADD CONSTRAINT `review_ibfk_1` FOREIGN KEY (`customerid`) REFERENCES `customer` (`uid`),
  ADD CONSTRAINT `review_ibfk_2` FOREIGN KEY (`news_id`) REFERENCES `news` (`id`);

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `customer` (`uid`);

--
-- Constraints for table `wish_game`
--
ALTER TABLE `wish_game`
  ADD CONSTRAINT `wish_game_ibfk_1` FOREIGN KEY (`Gid`) REFERENCES `game` (`Gid`),
  ADD CONSTRAINT `wish_game_ibfk_2` FOREIGN KEY (`uid`,`wishname`) REFERENCES `wishlist` (`uid`, `wishname`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
