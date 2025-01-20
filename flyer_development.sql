-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 20, 2025 at 08:59 AM
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
-- Database: `flyer_development`
--

-- --------------------------------------------------------

--
-- Table structure for table `approvals`
--

CREATE TABLE `approvals` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `project_id` int(11) DEFAULT NULL,
  `design_id` int(11) DEFAULT NULL,
  `draft_id` int(11) DEFAULT NULL,
  `status` enum('approved','rejected') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_closed` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chat_messages`
--

INSERT INTO `chat_messages` (`id`, `project_id`, `user_id`, `message`, `created_at`, `is_closed`) VALUES
(1, 1, 3, 'D lite vegetable oil 2lt correct price is 99.99', '2024-11-26 09:09:12', 0),
(2, 1, 3, 'spelling for teste should be taste', '2024-11-26 09:14:13', 0),
(3, 1, 1, 'please expeditite the process', '2024-11-26 09:53:43', 0),
(4, 1, 2, 'update price', '2024-11-26 09:57:39', 0),
(5, 2, 3, '@designer upload file please', '2024-11-26 10:09:36', 0),
(6, 1, 4, 'All correct', '2024-11-26 10:16:47', 0),
(7, 1, 2, '@management please aproave for print', '2024-11-26 10:27:27', 0),
(8, 1, 1, '@designer go aahead', '2024-11-26 10:29:55', 0),
(9, 1, 2, 'ok do it', '2024-11-27 10:03:56', 0),
(10, 1, 1, 'hurry', '2024-11-27 10:11:07', 0),
(11, 1, 2, 'alright', '2024-11-27 10:12:22', 0),
(12, 2, 2, 'lets see', '2024-11-27 10:12:41', 0),
(13, 2, 3, 'please upload @designer', '2024-11-27 10:25:50', 0),
(14, 1, 1, 'price wron on this', '2024-11-27 13:08:12', 0),
(15, 1, 1, 'hi @', '2024-11-27 14:01:25', 0),
(16, 1, 1, 'okj', '2024-11-27 14:02:23', 0),
(17, 1, 2, 'price is wrong on sunlight mall', '2024-11-27 14:08:50', 0),
(18, 4, 2, 'promo file please', '2024-11-27 14:38:55', 0),
(19, 1, 1, 'proof readers please check correct', '2024-11-28 13:55:20', 0),
(20, 1, 4, 'ok', '2024-11-28 14:01:45', 0),
(21, 1, 2, 'ok', '2024-11-28 14:02:01', 0),
(22, 1, 4, 'ok', '2024-11-28 14:02:16', 0),
(23, 1, 3, 'ok', '2024-11-28 14:02:32', 0),
(24, 1, 1, 'sgrg', '2024-11-28 14:38:41', 0),
(25, 1, 4, 'rfsd', '2024-11-28 14:39:02', 0),
(26, 2, 3, 'sfdgs', '2024-11-28 14:39:25', 0),
(27, 2, 3, 'sdfgsd', '2024-11-28 14:39:30', 0),
(28, 1, 3, 'sffs', '2024-11-28 15:50:05', 0),
(29, 1, 3, 'ggg', '2024-12-24 14:42:27', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `check_views`
--

CREATE TABLE `check_views` (
  `user_id` int(11) NOT NULL,
  `last_viewed` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `check_views`
--

INSERT INTO `check_views` (`user_id`, `last_viewed`) VALUES
(1, '2025-01-14 13:36:45');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `project_id` int(11) DEFAULT NULL,
  `design_id` int(11) DEFAULT NULL,
  `draft_id` int(11) DEFAULT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `designs`
--

CREATE TABLE `designs` (
  `id` int(11) NOT NULL,
  `project_id` int(11) DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `version` int(11) NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `drafts`
--

CREATE TABLE `drafts` (
  `id` int(11) NOT NULL,
  `project_id` int(11) DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `version` int(11) NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `file_versions`
--

CREATE TABLE `file_versions` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `version_number` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_by` int(11) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `flyer_data`
--

CREATE TABLE `flyer_data` (
  `id` int(11) NOT NULL,
  `product_code` varchar(50) DEFAULT NULL,
  `product_name` varchar(100) DEFAULT NULL,
  `category_name` varchar(100) DEFAULT NULL,
  `catalog_name` varchar(100) DEFAULT NULL,
  `bulk_price` varchar(20) DEFAULT NULL,
  `current_price` decimal(10,2) DEFAULT NULL,
  `promo_price` decimal(10,2) DEFAULT NULL,
  `page_number` varchar(20) DEFAULT NULL,
  `project_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `message_views`
--

CREATE TABLE `message_views` (
  `user_id` int(11) NOT NULL,
  `last_viewed` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `message_views`
--

INSERT INTO `message_views` (`user_id`, `last_viewed`) VALUES
(1, '2024-11-27 10:04:00');

-- --------------------------------------------------------

--
-- Table structure for table `product_data`
--

CREATE TABLE `product_data` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `product_code` varchar(50) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `category_name` varchar(100) DEFAULT NULL,
  `catalogue_name` varchar(100) NOT NULL,
  `bulk_price` varchar(50) DEFAULT NULL,
  `current_sp` decimal(10,2) NOT NULL,
  `promo_sp` decimal(10,2) NOT NULL,
  `page_number` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_data`
--

INSERT INTO `product_data` (`id`, `project_id`, `product_code`, `product_name`, `category_name`, `catalogue_name`, `bulk_price`, `current_sp`, `promo_sp`, `page_number`) VALUES
(2815, 2, '12211', 'UMOYO KOMBUCHA GING DRNK 250', 'COLD DRINK - FRUIT DRINK - WATER', 'COLD DRINK - FRUIT DRINK - WATER', ' ANY 3 FOR K26.99 ', 0.00, 26.99, 'No'),
(2816, 2, '12210', 'UMOYO KOMBUCHA ORIG DRNK 250', 'COLD DRINK - FRUIT DRINK - WATER', 'COLD DRINK - FRUIT DRINK - WATER', '', 11.99, 0.00, 'No'),
(2817, 2, '8906', 'FRUITOP GRANADILLA 1.5L', 'COLD DRINK - FRUIT DRINK - WATER', 'FRUITOP JUICE 1.5LT', '', 40.99, 27.99, '1'),
(2818, 2, '9060', 'FRUITOP GRAPE 1.5L', 'COLD DRINK - FRUIT DRINK - WATER', 'FRUITOP JUICE 1.5LT', '', 40.99, 27.99, '1'),
(2819, 2, '8907', 'FRUITOP ORANGE 1.5L', 'COLD DRINK - FRUIT DRINK - WATER', 'FRUITOP JUICE 1.5LT', '', 40.99, 27.99, '1'),
(2820, 2, '8905', 'FRUITOP PINEAPPLE 1.5L', 'COLD DRINK - FRUIT DRINK - WATER', 'FRUITOP JUICE 1.5LT', '', 40.99, 27.99, '1'),
(2821, 2, '1500', 'PUREJOY CRANBERRY 500ML', 'COLD DRINK - FRUIT DRINK - WATER', 'PUREJOY JUICE 500ML', '', 34.99, 24.99, 'No'),
(2822, 2, '1503', 'PUREJOY GUAVA 500ML', 'COLD DRINK - FRUIT DRINK - WATER', 'PUREJOY JUICE 500ML', '', 34.99, 24.99, 'No'),
(2823, 2, '1501', 'PUREJOY MANGO 500ML', 'COLD DRINK - FRUIT DRINK - WATER', 'PUREJOY JUICE 500ML', '', 34.99, 24.99, 'No'),
(2824, 2, '1495', 'PUREJOY ORANGE 500ML', 'COLD DRINK - FRUIT DRINK - WATER', 'PUREJOY JUICE 500ML', '', 34.99, 24.99, 'No'),
(2825, 2, '1491', 'PUREJOY PINEAPPLE 500ML', 'COLD DRINK - FRUIT DRINK - WATER', 'PUREJOY JUICE 500ML', '', 34.99, 24.99, 'No'),
(2826, 2, '1492', 'PUREJOY TROPICAL 500ML', 'COLD DRINK - FRUIT DRINK - WATER', 'PUREJOY JUICE 500ML', '', 34.99, 24.99, 'No'),
(2827, 2, '13482', 'RHODES NECTAR APPLE 1LT', 'COLD DRINK - FRUIT DRINK - WATER', 'RHODES NECTAR JUICE 1LT', '', 51.99, 29.99, '1'),
(2828, 2, '13486', 'RHODES NECTAR GUAVA 1LT', 'COLD DRINK - FRUIT DRINK - WATER', 'RHODES NECTAR JUICE 1LT', '', 51.99, 29.99, '1'),
(2829, 2, '13483', 'RHODES NECTAR MEDITERRANE 1LT', 'COLD DRINK - FRUIT DRINK - WATER', 'RHODES NECTAR JUICE 1LT', '', 51.99, 29.99, '1'),
(2830, 2, '13484', 'RHODES NECTAR RED GRAPE 1LT', 'COLD DRINK - FRUIT DRINK - WATER', 'RHODES NECTAR JUICE 1LT', '', 51.99, 29.99, '1'),
(2831, 2, '13485', 'RHODES NECTAR TROPICAL 1LT', 'COLD DRINK - FRUIT DRINK - WATER', 'RHODES NECTAR JUICE 1LT', '', 51.99, 29.99, '1'),
(2832, 2, '12346', 'MIRINDA ORANGE 500ML', 'COLD DRINK - FRUIT DRINK - WATER', 'MIRINDA/PEPSI/MOUNTAIN DEW/7UP 500ML', ' 12 PACK K99.99 ', 10.49, 99.99, '2'),
(2833, 2, '12347', 'MIRINDA FRUITY 500ML', 'COLD DRINK - FRUIT DRINK - WATER', 'MIRINDA/PEPSI/MOUNTAIN DEW/7UP 500ML', ' 12 PACK K99.99 ', 10.49, 0.00, '2'),
(2834, 2, '12350', 'PEPSI 500ML PET', 'COLD DRINK - FRUIT DRINK - WATER', 'MIRINDA/PEPSI/MOUNTAIN DEW/7UP 500ML', ' 12 PACK K99.99 ', 10.49, 0.00, '2'),
(2835, 2, '12720', 'PEPSI MAX 500ML PET', 'COLD DRINK - FRUIT DRINK - WATER', 'MIRINDA/PEPSI/MOUNTAIN DEW/7UP 500ML', ' 12 PACK K99.99 ', 10.49, 0.00, '2'),
(2836, 2, '12348', 'MOUNTAIN DEW 500ML', 'COLD DRINK - FRUIT DRINK - WATER', 'MIRINDA/PEPSI/MOUNTAIN DEW/7UP 500ML', ' 12 PACK K99.99 ', 10.49, 0.00, '2'),
(2837, 2, '12345', '7 UP 500 ML PET', 'COLD DRINK - FRUIT DRINK - WATER', 'MIRINDA/PEPSI/MOUNTAIN DEW/7UP 500ML', ' 12 PACK K99.99 ', 10.49, 0.00, '2'),
(2838, 2, '13608', 'YESS JUICE FRUIT PUNCH 430ML', 'COLD DRINK - FRUIT DRINK - WATER', 'YESS JUICE 430ML', ' 12 PACK K55.99 ', 7.49, 55.99, 'No'),
(2839, 2, '13607', 'YESS JUICE GUAVA 430ML', 'COLD DRINK - FRUIT DRINK - WATER', 'YESS JUICE 430ML', ' 12 PACK K55.99 ', 7.49, 0.00, 'No'),
(2840, 2, '13605', 'YESS JUICE PINEAPPLE 430ML', 'COLD DRINK - FRUIT DRINK - WATER', 'YESS JUICE 430ML', ' 12 PACK K55.99 ', 7.49, 0.00, 'No'),
(2841, 2, '13606', 'YESS JUICE MANGO 430ML', 'COLD DRINK - FRUIT DRINK - WATER', 'YESS JUICE 430ML', ' 12 PACK K55.99 ', 7.49, 0.00, 'No'),
(2842, 2, '9122', 'VATRA 500ML', 'COLD DRINK - FRUIT DRINK - WATER', 'VATRA 500ML', ' 12 PACK K45.99 ', 5.49, 44.99, 'No'),
(2843, 2, '13487', 'LIFEWAY SHOT ALOE VERA 200ML', 'COLD DRINK - FRUIT DRINK - WATER', 'LIFEWAY SHOT 200ML', ' 12 PACK K89.99 ', 10.49, 89.99, '2'),
(2844, 2, '13490', 'LIFEWAY SHOT B LEMON GRAS 200', 'COLD DRINK - FRUIT DRINK - WATER', 'LIFEWAY SHOT 200ML', '', 10.49, 0.00, '2'),
(2845, 2, '13491', 'LIFEWAY SHOT BEETROOT 200ML', 'COLD DRINK - FRUIT DRINK - WATER', 'LIFEWAY SHOT 200ML', '', 10.49, 0.00, '2'),
(2846, 2, '13488', 'LIFEWAY SHOT GINGER TANGA 200', 'COLD DRINK - FRUIT DRINK - WATER', 'LIFEWAY SHOT 200ML', '', 10.49, 0.00, '2'),
(2847, 2, '13489', 'LIFEWAY SHOT TAMARND KAWA 20O', 'COLD DRINK - FRUIT DRINK - WATER', 'LIFEWAY SHOT 200ML', '', 10.49, 0.00, '2'),
(2848, 2, '13357', 'BROTHERS COSMO MOCKT CAN 330M', 'COLD DRINK - FRUIT DRINK - WATER', 'BROTHERS MOCKTAILS 330ML', ' 6 FOR K59.99 ', 13.49, 59.99, 'No'),
(2849, 2, '13359', 'BROTHERS MOJITO MOCKT CAN 330', 'COLD DRINK - FRUIT DRINK - WATER', '', '', 13.49, 0.00, 'No'),
(2850, 2, '13360', 'BROTHERS STRW LIM MOC CAN 330', 'COLD DRINK - FRUIT DRINK - WATER', '', '', 13.49, 0.00, 'No'),
(2851, 2, '12968', 'NESTLE CREMORA COF CREAM 750G', 'DAIRY PRODUCTS', 'NESTLE CREMORA COFFEE 750GM', '', 116.99, 89.99, '1'),
(2852, 2, '7973', 'CAD BOURNVILLE DARK CHO 150GM', 'CONFECTIONARY & CHOCOLATES', 'CADBURY DAIRY MILK 150GM', '', 64.99, 38.99, '2'),
(2853, 2, '3082', 'CAD DAIRY MILK 150GM', 'CONFECTIONARY & CHOCOLATES', 'CADBURY DAIRY MILK 150GM', '', 64.99, 38.99, '1'),
(2854, 2, '850', 'CAD DAIRYMILK FRUIT N NT 150G', 'CONFECTIONARY & CHOCOLATES', 'CADBURY DAIRY MILK 150GM', '', 64.99, 38.99, '1'),
(2855, 2, '3007', 'CAD DAIRYMILK TOPDECK 150GM', 'CONFECTIONARY & CHOCOLATES', 'CADBURY DAIRY MILK 150GM', '', 64.99, 38.99, '1'),
(2856, 2, '838', 'CAD DAIRYMILK WHOLE NUT 150GM', 'CONFECTIONARY & CHOCOLATES', 'CADBURY DAIRY MILK 150GM', '', 64.99, 38.99, '1'),
(2857, 2, '3454', 'CAD MINT CRISP 150 GM', 'CONFECTIONARY & CHOCOLATES', 'CADBURY DAIRY MILK 150GM', '', 64.99, 38.99, '1'),
(2858, 2, '4646', 'CAD LUNCH BAR DREAM CHOC 48GM', 'CONFECTIONARY & CHOCOLATES', 'CADBURY LUNCH BAR 48GM', '', 23.99, 14.99, '2'),
(2859, 2, '854', 'CAD LUNCH BAR- BIG 48GM', 'CONFECTIONARY & CHOCOLATES', 'CADBURY LUNCH BAR 48GM', '', 23.99, 14.99, '2'),
(2860, 2, '864', 'CAD PS CARA MILK 48 GM', 'CONFECTIONARY & CHOCOLATES', 'CADBURY LUNCH BAR 48GM', '', 23.99, 14.99, '2'),
(2861, 2, '9280', 'CAD PS CDM 48 GM', 'CONFECTIONARY & CHOCOLATES', 'CADBURY LUNCH BAR 48GM', '', 23.99, 14.99, '2'),
(2862, 2, '13194', 'WINDMILL LEMON CREAMS 200GM', 'BISCUITS', 'WINDMILL LEMON CREAMS BISCUIT 200GM', ' 3 FOR K35.99 ', 20.99, 35.99, '2'),
(2863, 2, '13193', 'WINDMILL MARIE BISCUITS 140GM', 'BISCUITS', 'WINDMILL MARIE BISCUIT 140GM', ' 3 FOR K35.99 ', 20.99, 35.99, 'No'),
(2864, 2, '9169', 'COOKIE JOE SHORTIES 110GM', 'BISCUITS', 'COOKIE JOE SHORTIES BISCUIT 110GM', ' 5 FOR K29.99 ', 9.99, 29.99, '2'),
(2865, 2, '11314', 'COOKIE JOE GINGER COOKIE 90GM', 'BISCUITS', 'COOKIES JOE GINGER BISCUIT 90GM', ' 5 FOR K29.99 ', 8.99, 29.99, 'No'),
(2866, 2, '7460', 'ULTRA MARGARINE LOW FAT BRICK', 'DAIRY PRODUCTS', 'ULTRA MARGARINE LOW FAT BRICK', '', 25.99, 16.99, '2'),
(2867, 2, '672', 'QUICK BREW TEA BAGS 100PCS', 'TEA & COFFEE', 'QUICK BREW TEA BAGS 100S', '', 84.99, 59.99, '2'),
(2868, 2, '6989', 'JACOBS KRONUNG 200GM', 'TEA & COFFEE', 'JACOBS KRONUNG 200GM', '', 349.99, 249.99, '1'),
(2869, 2, '13584', 'UMOYO T BAG EUCAL & LEMON 20S', 'TEA & COFFEE', 'UMOYO TEA BAG 20S', '', 38.99, 28.99, 'NO'),
(2870, 2, '13586', 'UMOYO T BAG GING & LEOMGR 20S', 'TEA & COFFEE', 'UMOYO TEA BAG  20S', '', 38.99, 28.99, 'NO'),
(2871, 2, '13585', 'UMOYO T BAG MOR LEM & GIN 20S', 'TEA & COFFEE', 'UMOYO TEA BAG  20S', '', 38.99, 28.99, 'NO'),
(2872, 2, '13583', 'UMOYO T BAG PURE GINGER 20S', 'TEA & COFFEE', 'UMOYO TEA BAG  20S', '', 38.99, 28.99, 'NO'),
(2873, 2, '13587', 'UMOYO T BAG TUR & BLK PEP 20S', 'TEA & COFFEE', 'UMOYO TEA BAG  20S', '', 38.99, 28.99, 'NO'),
(2874, 2, '8563', 'D LITE VEGETABLE OIL 2LT', 'EDIBLE OIL & GHEE', 'D LITE VEGETABLE OIL 2LT', '', 123.99, 99.99, '1'),
(2875, 2, '5066', 'ZAMGOLD VEGETABLE OIL 5LT', 'EDIBLE OIL & GHEE', 'ZAMGOLD 5LT', '', 0.00, 264.99, '2'),
(2876, 2, '13448', 'NESTLE MILO 500 GM POUCH', 'BABY FOOD- HEALTH FOOD & HEALT', 'NESTLE MILO POUCH 500GM', '', 139.99, 89.99, '2'),
(2877, 2, '13619', 'JUNGLE OATS 2KG PLS', 'BABY FOOD- HEALTH FOOD & HEALT', 'JUNGLE OATS 2KG', '', 109.99, 69.99, '1'),
(2878, 2, '13121', 'WILDERBEE HONEY 500GM', 'BABY FOOD- HEALTH FOOD & HEALT', 'WILDERBEE HONEY 500GM', '', 44.99, 29.99, '2'),
(2879, 2, '13591', 'UMOYO INST PORR SUG FREE 500G', 'BABY FOOD- HEALTH FOOD & HEALT', 'UMOYO INSTANT PORRIDGE 500GM', '', 46.99, 39.99, 'No'),
(2880, 2, '2567', 'D LITE GROWN UP BANANA 500GM', 'BABY FOOD- HEALTH FOOD & HEALT', 'D LITE GROWN UP 500GM', '', 50.99, 35.99, '2'),
(2881, 2, '2550', 'D LITE GROWN UP CHOC 500GM', 'BABY FOOD- HEALTH FOOD & HEALT', 'D LITE GROWN UP 500GM', '', 50.99, 35.99, '2'),
(2882, 2, '2555', 'D LITE GROWN UP STRBRY 500GM', 'BABY FOOD- HEALTH FOOD & HEALT', 'D LITE GROWN UP 500GM', '', 50.99, 35.99, '2'),
(2883, 2, '2580', 'D LITE GROWN UP WHEAT 500GM', 'BABY FOOD- HEALTH FOOD & HEALT', 'D LITE GROWN UP 500GM', '', 50.99, 35.99, '2'),
(2884, 2, '3249', 'RHODES TOM PASTE 115GM CUP', 'INSTANT FOOD - READY TO EAT', 'RHODES TOMATO PASTE 115GM', '', 19.99, 12.99, 'No'),
(2885, 2, '12385', 'BIGTREE CORN FLAKES 1KG', 'BABY FOOD- HEALTH FOOD & HEALT', 'BIGTREE CORNFLAKES 1KG', '', 106.99, 79.99, '1'),
(2886, 2, '12825', 'BIGTREE BINTO BEEF NOODLE 70G', 'INSTANT FOOD - READY TO EAT', 'BIGTREE BINTO NOODLE 70GM', ' 6 for K29.99 ', 7.49, 29.99, '2'),
(2887, 2, '12823', 'BIGTREE BINTO CHCKN NOODLE 70', 'INSTANT FOOD - READY TO EAT', 'BIGTREE BINTO NOODLE 70GM', '', 7.49, 0.00, '2'),
(2888, 2, '12824', 'BIGTREE BINTO VEG NOODLE 70G', 'INSTANT FOOD - READY TO EAT', 'BIGTREE BINTO NOODLE 70GM', '', 7.49, 0.00, '2'),
(2889, 2, '12981', 'GOLDEN FOODS SMOOTH P/BUT 1KG', 'INSTANT FOOD - READY TO EAT', 'GOLDEN FOODS SMOOTH PEANUT BUTTER 1KG', '', 72.99, 49.99, '1'),
(2890, 2, '13653', 'BEST BUY BAKED BEANS 410GM', 'INSTANT FOOD - READY TO EAT', 'BEST BUY BAKED BEANS 410GM', '', 24.99, 14.99, '2'),
(2891, 2, '3159', 'GOLDEN BEEF SOYA PIECES90GM', 'INSTANT FOOD - READY TO EAT', 'GOLDEN BEEF SOYA 90GM', ' 3 FOR K14.99 ', 8.49, 14.99, 'No'),
(2892, 2, '3154', 'GOLDEN CHIKEN SOYA PIECES 90G', 'INSTANT FOOD - READY TO EAT', 'GOLDEN BEEF SOYA 90GM', '', 8.49, 0.00, 'No'),
(2893, 2, '3179', 'BIC 1 5S NORMAL POUCH', 'COSMETICS', 'BIC 5S', '', 30.99, 24.99, 'No'),
(2894, 2, '2359', 'BIC 1 LADY 5S', 'COSMETICS', 'BIC 5S', '', 30.99, 24.99, 'No'),
(2895, 2, '1559', 'BIC 1 SENSITIVE 5PCS', 'COSMETICS', 'BIC 5S', '', 30.99, 24.99, 'No'),
(2896, 2, '872', 'LAYS CARIBBEAN ONION 105GM', 'SAVOURY & DRY FRUITS', 'LAYS 105GM', '', 38.99, 27.99, '1'),
(2897, 2, '3257', 'LAYS SALTED 105GM', 'SAVOURY & DRY FRUITS', 'LAYS 105GM', '', 38.99, 27.99, '1'),
(2898, 2, '876', 'LAYS SPRING ONION N CHE 105GM', 'SAVOURY & DRY FRUITS', 'LAYS 105GM', '', 38.99, 27.99, '1'),
(2899, 2, '12393', 'LAYS SWEET&SMOKY AMER BBQ 105', 'SAVOURY & DRY FRUITS', 'LAYS 105GM', '', 38.99, 27.99, '1'),
(2900, 2, '877', 'LAYS THAI SWEET CHILLI 105GM', 'SAVOURY & DRY FRUITS', 'LAYS 105GM', '', 38.99, 27.99, '1'),
(2901, 2, '12729', 'SEBAS EMILIOS CRN PUF CHE 200', 'SAVOURY & DRY FRUITS', 'SEBAS EMILIOS CORN PUFF 200', '', 20.99, 9.99, '2'),
(2902, 2, '12728', 'SEBAS EMILIOS CRN PUF TOM 200', 'SAVOURY & DRY FRUITS', 'SEBAS EMILIOS CORN PUFF 200', '', 20.99, 9.99, '2'),
(2903, 2, '13122', 'HUGG DRY COMFORT SIZE 2- 32S', 'SANITARY NAPKINS - DIAPERS', 'HUGGIES DRY COMFORT', '', 221.99, 169.99, 'No'),
(2904, 2, '13123', 'HUGG DRY COMFORT SIZE 3- 30S', 'SANITARY NAPKINS - DIAPERS', 'HUGGIES DRY COMFORT', '', 221.99, 169.99, 'No'),
(2905, 2, '13124', 'HUGG DRY COMFORT SIZE 4- 28S', 'SANITARY NAPKINS - DIAPERS', 'HUGGIES DRY COMFORT', '', 218.99, 169.99, 'No'),
(2906, 2, '13125', 'HUGG DRY COMFORT SIZE 5- 26S', 'SANITARY NAPKINS - DIAPERS', 'HUGGIES DRY COMFORT', '', 218.99, 169.99, 'No'),
(2907, 2, '9288', 'HINDS BARBEQUE SPICE 65GM', 'SPICES', 'HIND MIX SPICES', '', 25.99, 15.99, '2'),
(2908, 2, '9289', 'HINDS CHICKEN SPICE 85GM', 'SPICES', 'HIND MIX SPICES', '', 25.99, 15.99, '2'),
(2909, 2, '9290', 'HINDS CHIP SEASONING 80GM', 'SPICES', 'HIND MIX SPICES', '', 25.99, 15.99, '2'),
(2910, 2, '9292', 'HINDS HERBS PARSLEY 12GM', 'SPICES', 'HIND MIX SPICES', '', 25.99, 15.99, '2'),
(2911, 2, '9294', 'HINDS MIXED HERBS 18GM', 'SPICES', 'HIND MIX SPICES', '', 25.99, 15.99, '2'),
(2912, 2, '9963', 'HINDS PAPRIKA SPICE 55GM', 'SPICES', 'HIND MIX SPICES', '', 25.99, 15.99, '2'),
(2913, 2, '10067', 'HINDS POTATO SPICE 60GM', 'SPICES', 'HIND MIX SPICES', '', 25.99, 15.99, '2'),
(2914, 2, '11116', 'HINDS SPICE FOR RICE 90GM', 'SPICES', 'HIND MIX SPICES', '', 25.99, 15.99, '2'),
(2915, 2, '11117', 'HINDS SPICE PORT CHICKEN 75GM', 'SPICES', 'HIND MIX SPICES', '', 25.99, 15.99, '2'),
(2916, 2, '11185', 'HINDS STEAK & CHOPS SPICE 80G', 'SPICES', 'HIND MIX SPICES', '', 25.99, 15.99, '2'),
(2917, 2, '11375', 'HINDS TURMERIC SPICE 60GM', 'SPICES', 'HIND MIX SPICES', '', 25.99, 15.99, '2'),
(2918, 2, '2817', 'BISTO ORG GRAVY PWDR 250GM', 'SPICES', 'BISTO ORIGINAL SPICES 250GM', '', 29.99, 15.99, '2'),
(2919, 2, '6815', 'ROYCO CHICKEN MCHUZI MIX 200G', 'SPICES', 'ROYCO MXHUZI MIX 200GM', '', 35.99, 22.99, '2'),
(2920, 2, '4894', 'ROYCO SPICY BEEF 200GM', 'SPICES', 'SPICES', '', 35.99, 22.99, '2'),
(2921, 2, '4273', 'DADDIES CHILLI GARLIC 375ML', 'JAM - JELLY - KETCHUP - SAUCES', 'DADDIES CHILLI SAUCE 375ML', '', 22.99, 16.99, 'No'),
(2922, 2, '7513', 'DADDIES CHILLI SAUCE 375 ML', 'JAM - JELLY - KETCHUP - SAUCES', 'DADDIES CHILLI SAUCE 375ML', '', 22.99, 16.99, 'No'),
(2923, 2, '4274', 'DADDIES GARLIC SAUCE 375ML', 'JAM - JELLY - KETCHUP - SAUCES', 'DADDIES CHILLI SAUCE 375ML', '', 22.99, 16.99, 'No'),
(2924, 2, '4272', 'DADDIES SWEET CHILI SAUCE 375', 'JAM - JELLY - KETCHUP - SAUCES', 'DADDIES CHILLI SAUCE 375ML', '', 22.99, 16.99, 'No'),
(2925, 2, '7514', 'DADDIES TOMATO KETCHUP 375 ML', 'JAM - JELLY - KETCHUP - SAUCES', 'DADDIES CHILLI SAUCE 375ML', '', 22.99, 16.99, 'No'),
(2926, 2, '12785', 'SUNLIGH PWD REGU 2 IN 1 5KG', 'WASHING AID & DETERGENTS', 'SUNLIGHT POWDER 5KG', '', 290.99, 189.99, '1'),
(2927, 2, '5684', 'HANDY ANDY EUCALYPTUS 750 ML', 'CLEANING MATERIALS', 'HANDY ANDY 750ML', '', 52.99, 32.99, '2'),
(2928, 2, '2857', 'HANDY ANDY LAVENDER 750ML', 'CLEANING MATERIALS', 'HANDY ANDY 750ML', '', 52.99, 32.99, '2'),
(2929, 2, '699', 'HANDY ANDY LEMON 750ML', 'CLEANING MATERIALS', 'HANDY ANDY 750ML', '', 52.99, 32.99, '2'),
(2930, 2, '2791', 'HANDY ANDY POTPOURRI 750ML', 'CLEANING MATERIALS', 'HANDY ANDY 750ML', '', 52.99, 32.99, '2'),
(2931, 2, '4924', 'FLYING FISH NRB 330ML', 'BEERS & CIDERS', 'FLYING FISH NRB 330ML', '6 Pack K99.99', 22.99, 99.99, '2'),
(2932, 2, '2520', 'CARLING BLACK LABEL NRB 330ML', 'BEERS & CIDERS', 'CARLING BLACK LABEL NRB 330ML', '6 Pack K79.99', 18.49, 79.99, '2'),
(2933, 2, '8991', 'CORONA EXTRA 355 ML', 'BEERS & CIDERS', 'CORONA EXTRA 335ML', '6 PACK K139.99', 29.99, 139.99, '2'),
(2934, 2, '2114', 'JAMESON IRISH WHISKY 750ML', 'SPIRITS & WINES', 'JAMESON IRISH WHISKY 750ML', '', 533.99, 399.99, '2'),
(2935, 2, '2468', 'AMARULA 750ML', 'SPIRITS & WINES', 'AMRULA 750ML', '', 297.99, 229.99, '2'),
(2936, 2, '11222', 'CASTLE LITE CAN 500ML', 'BEERS & CIDERS', '', '', 0.00, 99.99, 'No'),
(2937, 2, '13133', 'OH SO HEAVEN FB BERRY BUBB 2L', 'BATH SOAPS - SHOWER GEL & HAND', 'OH SO HEAVEN FOAM BATH 2LT', '', 120.99, 89.99, '2'),
(2938, 2, '13135', 'OH SO HEAVEN FB BUBB OVER 2L', 'BATH SOAPS - SHOWER GEL & HAND', 'OH SO HEAVEN FOAM BATH 2LT', '', 120.99, 89.99, '2'),
(2939, 2, '13134', 'OH SO HEAVEN FB CREME CARE 2L', 'BATH SOAPS - SHOWER GEL & HAND', 'OH SO HEAVEN FOAM BATH 2LT', '', 120.99, 89.99, '2'),
(2940, 2, '13137', 'OH SO HEAVEN FB ISLAND BLI 2L', 'BATH SOAPS - SHOWER GEL & HAND', 'OH SO HEAVEN FOAM BATH 2LT', '', 120.99, 89.99, '2'),
(2941, 2, '13136', 'OH SO HEAVEN FB RELAX ROSE 2L', 'BATH SOAPS - SHOWER GEL & HAND', 'OH SO HEAVEN FOAM BATH 2LT', '', 120.99, 89.99, '2'),
(2942, 2, '5783', 'HYGIENIX SOAP CITRUS 175GM', 'BATH SOAPS - SHOWER GEL & HAND', 'HYGIENIX SOAP 175GM', '', 20.99, 14.99, '2'),
(2943, 2, '5791', 'HYGIENIX SOAP SENSITIVE 175GM', 'BATH SOAPS - SHOWER GEL & HAND', 'HYGIENIX SOAP 175GM', '', 20.99, 14.99, '2'),
(2944, 2, '5784', 'HYGIENIX SOAP HERBAL 175GM', 'BATH SOAPS - SHOWER GEL & HAND', 'HYGIENIX SOAP 175GM', '', 20.99, 14.99, '2'),
(2945, 2, '5785', 'HYGIENIX SOAP ICY COOL 175GM', 'BATH SOAPS - SHOWER GEL & HAND', 'HYGIENIX SOAP 175GM', '', 20.99, 14.99, '2'),
(2946, 2, '13241', 'HYGIENIX SOAP EVENTONE 175GM', 'BATH SOAPS - SHOWER GEL & HAND', 'HYGIENIX SOAP 175GM', '', 20.99, 14.99, '2'),
(2947, 2, '5786', 'HYGIENIX SOAP ACTIVE 175GM', 'BATH SOAPS - SHOWER GEL & HAND', 'HYGIENIX SOAP 175GM', '', 20.99, 14.99, '2'),
(2948, 2, '13240', 'HYGIENIX SOAP CHARCOAL 175GM', 'BATH SOAPS - SHOWER GEL & HAND', 'HYGIENIX SOAP 175GM', '', 20.99, 14.99, '2'),
(2949, 2, '5796', 'HYGIENIX SOAP ORIGINAL 175GM', 'BATH SOAPS - SHOWER GEL & HAND', 'HYGIENIX SOAP 175GM', '', 20.99, 14.99, '2'),
(2950, 2, '797', 'DOOM ODOURLESS MULTI IN 300ML', 'INSECTISIDE & MOSQUITO REPELLN', 'DOOM SUPER/DOURLESS 300ML', '', 49.99, 29.99, '1'),
(2951, 2, '795', 'DOOM SUPER MULTI INSECT 300ML', 'INSECTISIDE & MOSQUITO REPELLN', 'DOOM SUPER/DOURLESS 300ML', '', 49.99, 29.99, '1'),
(2952, 2, '13179', 'COMPACT TISSUE RED 18S', 'PAPER PRODUCTS', 'COMPAQ TISSUE RED 18S', '', 215.99, 159.99, '1'),
(2953, 2, '7434', 'COLGATE TB ZIGZAG 1PC', 'DENTAL CARE - ORAL CARE', 'COLGATE TOOTHBRUSH ZIGZAG 1S', '', 35.99, 19.99, '2');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `status` enum('design_pending','design_approved','draft_review','final_review','approved_for_print','completed') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `project_date` date NOT NULL,
  `comment` text DEFAULT NULL,
  `pdf_file_path` varchar(255) DEFAULT NULL,
  `current_draft` int(11) NOT NULL DEFAULT 1,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `name`, `status`, `created_at`, `project_date`, `comment`, `pdf_file_path`, `current_draft`, `updated_at`) VALUES
(1, 'Black friday 2024', '', '2024-11-23 10:43:38', '2024-11-28', 'running from 28th to 30th November 2024', 'uploads/pdfs/Black friday flyer compress _ final2_compressed1.pdf', 5, '2024-11-28 14:14:05'),
(2, 'November monthend promo', '', '2024-11-26 10:08:29', '2024-11-11', 'date: from 23 th june to 12th month 2024', 'uploads/pdfs/timing poster.pdf', 3, '2024-11-28 15:20:00'),
(4, 'Christmas promotion', '', '2024-11-27 14:02:55', '2024-12-26', '', NULL, 1, '2024-11-28 15:48:24'),
(6, 'secrete santa sale 2', 'design_pending', '2024-11-29 10:53:08', '2023-09-07', '', NULL, 1, '2024-11-29 10:53:08'),
(7, 'secrete santa sale 2', 'design_pending', '2024-11-29 12:13:19', '2023-08-16', '', NULL, 1, '2024-11-29 12:13:19'),
(8, 'january monthend ', 'design_pending', '2025-01-11 11:12:09', '2025-01-21', 'flyer', NULL, 1, '2025-01-11 11:12:09');

-- --------------------------------------------------------

--
-- Table structure for table `project_checks`
--

CREATE TABLE `project_checks` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `checker_role` enum('designer','proofreader','management') NOT NULL,
  `check_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('checked','pending','rejected') NOT NULL DEFAULT 'pending',
  `draft_number` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `project_checks`
--

INSERT INTO `project_checks` (`id`, `project_id`, `user_id`, `checker_role`, `check_date`, `status`, `draft_number`) VALUES
(1, 1, 3, 'designer', '2024-11-26 09:09:39', 'rejected', 2),
(3, 1, 4, 'designer', '2024-11-26 10:16:32', 'checked', 3),
(4, 2, 3, 'designer', '2024-11-26 10:17:23', 'checked', 1),
(5, 1, 3, 'designer', '2024-11-26 10:23:14', 'checked', 3),
(6, 1, 3, 'designer', '2024-11-26 10:28:19', 'checked', 4),
(7, 1, 3, 'designer', '2024-11-27 14:07:46', 'checked', 5),
(8, 1, 2, 'designer', '2024-11-27 14:09:08', 'checked', 5),
(9, 1, 4, 'designer', '2024-11-27 14:25:01', 'checked', 5),
(14, 2, 3, 'designer', '2024-11-28 14:29:41', 'checked', 3);

-- --------------------------------------------------------

--
-- Table structure for table `project_views`
--

CREATE TABLE `project_views` (
  `user_id` int(11) NOT NULL,
  `last_viewed` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `project_views`
--

INSERT INTO `project_views` (`user_id`, `last_viewed`) VALUES
(1, '2025-01-14 13:36:45'),
(2, '2025-01-11 11:39:30');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('designer','proof_reader','management') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(1, 'Pervious Chileya', '$2y$10$9CBgJxN6O0nGj8a7acOOEOESwfWOLeAnxCa.jk572JIYsiiKzmbQy', 'management'),
(2, 'Perviousch', '$2y$10$CBSZISCrk9mFcfPl6ZvtberyV7JH4G3c3gw/pFXAGtf8mefqE8ax2', 'designer'),
(3, 'Perviousch1', '$2y$10$31dMI03X8WnduYuYDL9VluS73L3oWrFNc/.GcwpdJYTmTphB7b1g2', 'proof_reader'),
(4, 'Perviousch2', '$2y$10$LxFUgXjGPbMPo6zHpWyPV.16Gki2REWmdIYYOmy5G.w/ucVvMPF2i', 'proof_reader');

-- --------------------------------------------------------

--
-- Table structure for table `viewed_messages`
--

CREATE TABLE `viewed_messages` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message_id` int(11) NOT NULL,
  `viewed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `viewed_messages`
--

INSERT INTO `viewed_messages` (`id`, `user_id`, `message_id`, `viewed_at`) VALUES
(1, 1, 5, '2024-11-27 10:10:22'),
(2, 1, 4, '2024-11-27 10:10:25'),
(3, 1, 7, '2024-11-27 10:10:34'),
(4, 1, 9, '2024-11-27 10:10:41'),
(5, 1, 8, '2024-11-27 10:10:45'),
(6, 1, 6, '2024-11-27 10:10:49'),
(7, 1, 2, '2024-11-27 10:10:50'),
(8, 1, 3, '2024-11-27 10:10:50'),
(9, 1, 1, '2024-11-27 10:10:51'),
(10, 1, 10, '2024-11-27 10:11:10'),
(11, 1, 13, '2024-11-27 10:30:56'),
(12, 1, 12, '2024-11-27 12:18:17'),
(13, 1, 16, '2024-11-27 14:02:31'),
(14, 1, 15, '2024-11-27 14:02:36'),
(15, 1, 11, '2024-11-27 14:02:39'),
(16, 1, 14, '2024-11-27 14:02:40'),
(17, 1, 17, '2024-11-27 14:20:50'),
(18, 1, 18, '2024-11-28 15:52:22'),
(19, 1, 19, '2024-11-28 15:52:23'),
(20, 1, 20, '2024-11-28 15:52:23'),
(21, 1, 21, '2024-11-28 15:52:23'),
(22, 1, 22, '2024-11-28 15:52:24'),
(23, 1, 23, '2024-11-28 15:52:24'),
(24, 1, 24, '2024-11-28 15:52:25'),
(25, 1, 25, '2024-11-28 15:52:25'),
(26, 1, 26, '2024-11-28 15:52:26'),
(27, 1, 28, '2024-11-29 10:53:21'),
(28, 1, 27, '2024-11-29 10:53:22');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `approvals`
--
ALTER TABLE `approvals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `design_id` (`design_id`),
  ADD KEY `draft_id` (`draft_id`);

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `check_views`
--
ALTER TABLE `check_views`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `design_id` (`design_id`),
  ADD KEY `draft_id` (`draft_id`);

--
-- Indexes for table `designs`
--
ALTER TABLE `designs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `drafts`
--
ALTER TABLE `drafts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `file_versions`
--
ALTER TABLE `file_versions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uploaded_by` (`uploaded_by`),
  ADD KEY `idx_project_version` (`project_id`,`version_number`);

--
-- Indexes for table `flyer_data`
--
ALTER TABLE `flyer_data`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `message_views`
--
ALTER TABLE `message_views`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `product_data`
--
ALTER TABLE `product_data`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `project_checks`
--
ALTER TABLE `project_checks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_check` (`project_id`,`user_id`,`draft_number`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `project_views`
--
ALTER TABLE `project_views`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `viewed_messages`
--
ALTER TABLE `viewed_messages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_view` (`user_id`,`message_id`),
  ADD KEY `message_id` (`message_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `approvals`
--
ALTER TABLE `approvals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `designs`
--
ALTER TABLE `designs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `drafts`
--
ALTER TABLE `drafts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `file_versions`
--
ALTER TABLE `file_versions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_data`
--
ALTER TABLE `product_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3093;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `project_checks`
--
ALTER TABLE `project_checks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `viewed_messages`
--
ALTER TABLE `viewed_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `approvals`
--
ALTER TABLE `approvals`
  ADD CONSTRAINT `approvals_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `approvals_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`),
  ADD CONSTRAINT `approvals_ibfk_3` FOREIGN KEY (`design_id`) REFERENCES `designs` (`id`),
  ADD CONSTRAINT `approvals_ibfk_4` FOREIGN KEY (`draft_id`) REFERENCES `drafts` (`id`);

--
-- Constraints for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD CONSTRAINT `chat_messages_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`),
  ADD CONSTRAINT `chat_messages_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`),
  ADD CONSTRAINT `comments_ibfk_3` FOREIGN KEY (`design_id`) REFERENCES `designs` (`id`),
  ADD CONSTRAINT `comments_ibfk_4` FOREIGN KEY (`draft_id`) REFERENCES `drafts` (`id`);

--
-- Constraints for table `designs`
--
ALTER TABLE `designs`
  ADD CONSTRAINT `designs_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`);

--
-- Constraints for table `drafts`
--
ALTER TABLE `drafts`
  ADD CONSTRAINT `drafts_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`);

--
-- Constraints for table `file_versions`
--
ALTER TABLE `file_versions`
  ADD CONSTRAINT `file_versions_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`),
  ADD CONSTRAINT `file_versions_ibfk_2` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `flyer_data`
--
ALTER TABLE `flyer_data`
  ADD CONSTRAINT `flyer_data_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`);

--
-- Constraints for table `product_data`
--
ALTER TABLE `product_data`
  ADD CONSTRAINT `product_data_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`);

--
-- Constraints for table `project_checks`
--
ALTER TABLE `project_checks`
  ADD CONSTRAINT `project_checks_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`),
  ADD CONSTRAINT `project_checks_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `viewed_messages`
--
ALTER TABLE `viewed_messages`
  ADD CONSTRAINT `viewed_messages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `viewed_messages_ibfk_2` FOREIGN KEY (`message_id`) REFERENCES `chat_messages` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
