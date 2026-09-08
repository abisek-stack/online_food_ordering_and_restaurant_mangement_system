-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 13, 2025 at 04:21 PM
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
-- Database: `user_registration`
--

-- --------------------------------------------------------

--
-- Table structure for table `discounts`
--

CREATE TABLE `discounts` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `discount_type` enum('percent','fixed') DEFAULT 'percent',
  `value` decimal(10,2) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` int(11) NOT NULL,
  `category` varchar(100) NOT NULL COMMENT 'e.g. rent, gas, salaries, utilities',
  `amount` decimal(10,2) NOT NULL,
  `expense_date` date NOT NULL DEFAULT curdate(),
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_items`
--

CREATE TABLE `inventory_items` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `unit` varchar(20) NOT NULL COMMENT 'e.g., kg, g, liter, pcs',
  `current_stock` decimal(10,3) NOT NULL DEFAULT 0.000,
  `avg_price_per_unit` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Average cost price',
  `min_level` decimal(10,3) NOT NULL DEFAULT 5.000,
  `max_level` decimal(10,3) NOT NULL DEFAULT 100.000
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory_items`
--

INSERT INTO `inventory_items` (`id`, `name`, `unit`, `current_stock`, `avg_price_per_unit`, `min_level`, `max_level`) VALUES
(1, 'Wheat Flour (Maida)', 'kg', 15.520, 90.00, 5.000, 50.000),
(2, 'Potatoes (Boiled)', 'kg', 7.415, 51.00, 2.000, 20.000),
(3, 'Onion', 'kg', 15.245, 35.00, 3.000, 30.000),
(4, 'Green Chilies', 'kg', 0.651, 45.00, 0.200, 2.000),
(5, 'Coriander Leaves', 'kg', 0.187, 100.00, 0.100, 1.000),
(6, 'Salt', 'kg', 4.389, 25.00, 1.000, 10.000),
(7, 'Oil', 'liter', 11.740, 200.00, 2.000, 20.000),
(8, 'Chhola (Chickpea Curry)', 'kg', 2.490, 150.00, 1.000, 10.000),
(9, 'Cabbage', 'kg', 8.035, 45.00, 2.000, 25.000),
(10, 'Carrot', 'kg', 5.540, 115.00, 1.000, 15.000),
(11, 'Garlic', 'kg', 1.090, 165.00, 0.500, 5.000),
(12, 'Ginger', 'kg', 2.390, 110.00, 0.500, 5.000),
(13, 'Chicken', 'kg', 7.695, 374.00, 3.000, 20.000),
(14, 'Soy Sauce', 'liter', 2.100, 225.00, 0.500, 5.000),
(15, 'Vinegar', 'liter', 1.500, 125.00, 0.500, 5.000),
(16, 'Buff Meat', 'kg', 9.300, 500.00, 3.000, 20.000),
(17, 'Noodles', 'kg', 5.790, 120.00, 2.000, 20.000),
(18, 'Pepper', 'kg', 0.330, 800.00, 0.200, 3.000),
(19, 'Water', 'liter', 43.900, 5.00, 10.000, 100.000),
(20, 'Capsicum', 'kg', 1.050, 120.00, 0.500, 5.000),
(21, 'Rice (Cooked)', 'kg', 9.850, 100.00, 3.000, 30.000),
(22, 'Peas', 'kg', 2.500, 110.00, 0.500, 8.000),
(23, 'Egg', 'unit', 27.370, 20.00, 5.000, 50.000),
(24, 'Sukuti (Dried Mutton)', 'kg', 0.950, 1500.00, 0.500, 4.000),
(25, 'Dal / Lentils', 'kg', 4.800, 140.00, 2.000, 15.000),
(26, 'Ghee', 'liter', 1.250, 1200.00, 0.500, 5.000),
(27, 'Spices', 'kg', 1.000, 500.00, 0.500, 5.000),
(28, 'Mutton Meat', 'kg', 3.500, 1200.00, 1.000, 10.000),
(29, 'Tomato', 'kg', 7.150, 80.00, 2.000, 20.000),
(30, 'Chili', 'kg', 0.450, 50.00, 0.200, 2.000),
(31, 'Lemon', 'kg', 0.998, 150.00, 0.500, 4.000),
(32, 'Chilly Sauce', 'liter', 0.495, 250.00, 0.200, 3.000),
(33, 'Tea Leaves', 'kg', 0.300, 600.00, 0.200, 2.000),
(34, 'Sugar', 'kg', 8.500, 100.00, 2.000, 20.000),
(35, 'Milk', 'liter', 3.200, 120.00, 1.000, 10.000),
(36, 'Masala', 'kg', 0.600, 800.00, 0.200, 2.000),
(37, 'Coffee Powder', 'kg', 0.250, 900.00, 0.100, 1.000),
(38, 'Bottled Soft Drink', 'unit', 15.000, 100.00, 6.000, 50.000),
(39, 'Bottled Water', 'unit', 30.000, 40.00, 12.000, 100.000),
(40, 'Curd / Yogurt', 'kg', 1.500, 250.00, 0.500, 5.000),
(41, 'Banana', 'kg', 2.500, 100.00, 1.000, 10.000),
(42, 'Ice Cream', 'liter', 3.000, 800.00, 1.000, 10.000),
(43, 'Dhido', 'kg', 0.400, 300.00, 0.200, 2.000);

-- --------------------------------------------------------

--
-- Table structure for table `inventory_transactions`
--

CREATE TABLE `inventory_transactions` (
  `id` int(11) NOT NULL,
  `inventory_item_id` int(11) NOT NULL,
  `quantity_changed` decimal(10,3) NOT NULL COMMENT 'Positive for purchase, negative for sale/waste',
  `transaction_type` enum('purchase','sale','waste','adjustment') NOT NULL,
  `related_order_id` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL CHECK (`price` >= 0),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `name`, `price`, `is_active`, `created_at`) VALUES
(1, 'Aloo Paratha (2 Pcs.) with Chhola', 60.00, 1, '2025-09-04 18:47:13'),
(2, 'Plain Paratha (2 Pcs.) with Chhola', 50.00, 1, '2025-09-04 18:47:13'),
(3, 'Roti (3 Pcs.) with Chhola', 45.00, 1, '2025-09-04 18:47:13'),
(4, 'Plain Paratha (1 Pc.)', 25.00, 1, '2025-09-04 18:47:13'),
(5, 'Aloo Paratha (1 Pc.)', 25.00, 1, '2025-09-04 18:47:13'),
(6, 'Veg (Steam) Momo', 100.00, 1, '2025-09-04 18:47:13'),
(7, 'Veg (Fry) Momo', 110.00, 1, '2025-09-04 18:47:13'),
(8, 'Veg (Chilly) Momo', 120.00, 1, '2025-09-04 18:47:13'),
(9, 'Chicken (Steam) Momo', 110.00, 1, '2025-09-04 18:47:13'),
(10, 'Chicken (Fry) Momo', 120.00, 1, '2025-09-04 18:47:13'),
(11, 'Chicken (Chilly) Momo', 130.00, 1, '2025-09-04 18:47:13'),
(12, 'Buff (Steam) Momo', 120.00, 1, '2025-09-04 18:47:13'),
(13, 'Buff (Fry) Momo', 130.00, 1, '2025-09-04 18:47:13'),
(14, 'Buff (Chilly) Momo', 140.00, 1, '2025-09-04 18:47:13'),
(15, 'Chicken Chowmins', 120.00, 1, '2025-09-04 18:47:13'),
(16, 'Buff Chowmins', 130.00, 1, '2025-09-04 18:47:13'),
(17, 'Veg Chowmins', 110.00, 1, '2025-09-04 18:47:13'),
(18, 'Egg Chowmins', 115.00, 1, '2025-09-04 18:47:13'),
(19, 'Mix Chowmins', 140.00, 1, '2025-09-04 18:47:13'),
(20, 'Mix Thukpa', 110.00, 1, '2025-09-04 18:47:13'),
(21, 'Buff Thukpa', 100.00, 1, '2025-09-04 18:47:13'),
(22, 'Chicken Thukpa', 100.00, 1, '2025-09-04 18:47:13'),
(23, 'Veg Thukpa', 90.00, 1, '2025-09-04 18:47:13'),
(24, 'Veg Fry Rice', 110.00, 1, '2025-09-04 18:47:13'),
(25, 'Mix Fry Rice', 130.00, 1, '2025-09-04 18:47:13'),
(26, 'Chicken Fry Rice', 120.00, 1, '2025-09-04 18:47:13'),
(27, 'Buff Fry Rice', 120.00, 1, '2025-09-04 18:47:13'),
(28, 'Egg Fry Rice', 115.00, 1, '2025-09-04 18:47:13'),
(29, 'Mutton Thali (Sukuti)', 160.00, 1, '2025-09-04 18:47:13'),
(30, 'Chicken Thali', 150.00, 1, '2025-09-04 18:47:13'),
(31, 'Roti Thali', 140.00, 1, '2025-09-04 18:47:13'),
(32, 'Local Chicken with Rice/Dhido', 170.00, 1, '2025-09-04 18:47:13'),
(33, 'Mutton Curry', 120.00, 1, '2025-09-04 18:47:13'),
(34, 'Chicken Curry', 110.00, 1, '2025-09-04 18:47:13'),
(35, 'Sukuti Sadeko', 130.00, 1, '2025-09-04 18:47:13'),
(36, 'Chicken Chilly', 140.00, 1, '2025-09-04 18:47:13'),
(37, 'Buff Chilly', 150.00, 1, '2025-09-04 18:47:13'),
(38, 'Sukuti Fry', 150.00, 1, '2025-09-04 18:47:13'),
(39, 'Black Tea', 30.00, 1, '2025-09-04 18:47:13'),
(40, 'Milk Tea', 40.00, 1, '2025-09-04 18:47:13'),
(41, 'Masala Tea', 40.00, 1, '2025-09-04 18:47:13'),
(42, 'Black Coffee', 40.00, 1, '2025-09-04 18:47:13'),
(43, 'Milk Coffee', 50.00, 1, '2025-09-04 18:47:13'),
(44, 'Soft Drink', 50.00, 1, '2025-09-04 18:47:13'),
(45, 'Water (Small)', 20.00, 1, '2025-09-04 18:47:13'),
(46, 'Water (Big)', 30.00, 1, '2025-09-04 18:47:13'),
(47, 'Plain Sweet Lassi', 60.00, 1, '2025-09-04 18:47:13'),
(48, 'Banana Lassi', 70.00, 1, '2025-09-04 18:47:13'),
(49, 'Lassi w/ Ice Cream', 80.00, 1, '2025-09-04 18:47:13');

-- --------------------------------------------------------

--
-- Table structure for table `menu_item_recipes`
--

CREATE TABLE `menu_item_recipes` (
  `id` int(11) NOT NULL,
  `menu_item_id` int(11) NOT NULL,
  `inventory_item_id` int(11) NOT NULL,
  `quantity_used` decimal(10,3) NOT NULL COMMENT 'Amount of inventory item used per menu item'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_item_recipes`
--

INSERT INTO `menu_item_recipes` (`id`, `menu_item_id`, `inventory_item_id`, `quantity_used`) VALUES
(330, 1, 1, 0.200),
(331, 1, 2, 0.150),
(332, 1, 3, 0.050),
(333, 1, 4, 0.002),
(334, 1, 5, 0.010),
(335, 1, 6, 0.005),
(336, 1, 7, 0.015),
(337, 1, 8, 0.100),
(338, 2, 1, 0.180),
(339, 2, 6, 0.005),
(340, 2, 7, 0.015),
(341, 2, 8, 0.100),
(342, 3, 1, 0.150),
(343, 3, 6, 0.005),
(344, 3, 8, 0.100),
(345, 4, 1, 0.090),
(346, 4, 6, 0.003),
(347, 4, 7, 0.008),
(348, 5, 1, 0.090),
(349, 5, 2, 0.075),
(350, 5, 3, 0.025),
(351, 5, 4, 0.001),
(352, 5, 5, 0.005),
(353, 5, 6, 0.003),
(354, 5, 7, 0.008),
(355, 6, 1, 0.050),
(356, 6, 9, 0.030),
(357, 6, 10, 0.020),
(358, 6, 3, 0.015),
(359, 6, 11, 0.005),
(360, 6, 12, 0.005),
(361, 6, 5, 0.005),
(362, 6, 6, 0.003),
(363, 6, 7, 0.005),
(364, 7, 1, 0.050),
(365, 7, 9, 0.030),
(366, 7, 10, 0.020),
(367, 7, 3, 0.015),
(368, 7, 11, 0.005),
(369, 7, 12, 0.005),
(370, 7, 5, 0.005),
(371, 7, 6, 0.003),
(372, 7, 7, 0.015),
(373, 8, 1, 0.050),
(374, 8, 9, 0.030),
(375, 8, 10, 0.020),
(376, 8, 3, 0.015),
(377, 8, 11, 0.005),
(378, 8, 12, 0.005),
(379, 8, 5, 0.005),
(380, 8, 6, 0.003),
(381, 8, 7, 0.010),
(382, 8, 4, 0.005),
(383, 8, 13, 0.005),
(384, 8, 14, 0.005),
(385, 9, 1, 0.050),
(386, 9, 15, 0.040),
(387, 9, 3, 0.015),
(388, 9, 11, 0.005),
(389, 9, 12, 0.005),
(390, 9, 5, 0.005),
(391, 9, 6, 0.003),
(392, 9, 7, 0.005),
(393, 10, 1, 0.050),
(394, 10, 15, 0.040),
(395, 10, 3, 0.015),
(396, 10, 11, 0.005),
(397, 10, 12, 0.005),
(398, 10, 5, 0.005),
(399, 10, 6, 0.003),
(400, 10, 7, 0.015),
(401, 11, 1, 0.050),
(402, 11, 15, 0.040),
(403, 11, 3, 0.015),
(404, 11, 11, 0.005),
(405, 11, 12, 0.005),
(406, 11, 5, 0.005),
(407, 11, 6, 0.003),
(408, 11, 7, 0.010),
(409, 11, 4, 0.005),
(410, 11, 13, 0.005),
(411, 11, 14, 0.005),
(412, 12, 1, 0.050),
(413, 12, 16, 0.040),
(414, 12, 3, 0.015),
(415, 12, 11, 0.005),
(416, 12, 12, 0.005),
(417, 12, 5, 0.005),
(418, 12, 6, 0.003),
(419, 12, 7, 0.005),
(420, 13, 1, 0.050),
(421, 13, 16, 0.040),
(422, 13, 3, 0.015),
(423, 13, 11, 0.005),
(424, 13, 12, 0.005),
(425, 13, 5, 0.005),
(426, 13, 6, 0.003),
(427, 13, 7, 0.015),
(428, 14, 1, 0.050),
(429, 14, 16, 0.040),
(430, 14, 3, 0.015),
(431, 14, 11, 0.005),
(432, 14, 12, 0.005),
(433, 14, 5, 0.005),
(434, 14, 6, 0.003),
(435, 14, 7, 0.010),
(436, 14, 4, 0.005),
(437, 14, 13, 0.005),
(438, 14, 14, 0.005),
(439, 15, 17, 0.100),
(440, 15, 15, 0.050),
(441, 15, 9, 0.030),
(442, 15, 10, 0.020),
(443, 15, 3, 0.015),
(444, 15, 11, 0.005),
(445, 15, 12, 0.005),
(446, 15, 13, 0.005),
(447, 15, 7, 0.010),
(448, 15, 6, 0.003),
(449, 16, 17, 0.100),
(450, 16, 16, 0.050),
(451, 16, 9, 0.030),
(452, 16, 10, 0.020),
(453, 16, 3, 0.015),
(454, 16, 11, 0.005),
(455, 16, 12, 0.005),
(456, 16, 13, 0.005),
(457, 16, 7, 0.010),
(458, 16, 6, 0.003),
(459, 17, 17, 0.100),
(460, 17, 9, 0.040),
(461, 17, 10, 0.030),
(462, 17, 3, 0.020),
(463, 17, 11, 0.005),
(464, 17, 12, 0.005),
(465, 17, 13, 0.005),
(466, 17, 7, 0.010),
(467, 17, 6, 0.003),
(468, 18, 17, 0.100),
(469, 18, 18, 0.050),
(470, 18, 9, 0.030),
(471, 18, 10, 0.020),
(472, 18, 3, 0.015),
(473, 18, 11, 0.005),
(474, 18, 12, 0.005),
(475, 18, 13, 0.005),
(476, 18, 7, 0.010),
(477, 18, 6, 0.003),
(478, 19, 17, 0.100),
(479, 19, 15, 0.030),
(480, 19, 16, 0.030),
(481, 19, 9, 0.017),
(482, 19, 10, 0.017),
(483, 19, 3, 0.017),
(484, 19, 11, 0.005),
(485, 19, 12, 0.005),
(486, 19, 13, 0.005),
(487, 19, 7, 0.010),
(488, 19, 6, 0.003),
(489, 20, 17, 0.100),
(490, 20, 15, 0.030),
(491, 20, 16, 0.030),
(492, 20, 9, 0.017),
(493, 20, 10, 0.017),
(494, 20, 3, 0.017),
(495, 20, 11, 0.005),
(496, 20, 12, 0.005),
(497, 20, 6, 0.003),
(498, 20, 7, 0.010),
(499, 20, 19, 0.250),
(500, 21, 17, 0.100),
(501, 21, 16, 0.060),
(502, 21, 9, 0.017),
(503, 21, 10, 0.017),
(504, 21, 3, 0.017),
(505, 21, 11, 0.005),
(506, 21, 12, 0.005),
(507, 21, 6, 0.003),
(508, 21, 7, 0.010),
(509, 21, 19, 0.250),
(510, 22, 17, 0.100),
(511, 22, 15, 0.060),
(512, 22, 9, 0.017),
(513, 22, 10, 0.017),
(514, 22, 3, 0.017),
(515, 22, 11, 0.005),
(516, 22, 12, 0.005),
(517, 22, 6, 0.003),
(518, 22, 7, 0.010),
(519, 22, 19, 0.250),
(520, 23, 17, 0.100),
(521, 23, 9, 0.020),
(522, 23, 10, 0.020),
(523, 23, 3, 0.020),
(524, 23, 20, 0.020),
(525, 23, 11, 0.005),
(526, 23, 12, 0.005),
(527, 23, 6, 0.003),
(528, 23, 7, 0.010),
(529, 23, 19, 0.250),
(530, 24, 21, 0.150),
(531, 24, 10, 0.018),
(532, 24, 9, 0.018),
(533, 24, 22, 0.018),
(534, 24, 3, 0.018),
(535, 24, 11, 0.005),
(536, 24, 12, 0.005),
(537, 24, 7, 0.010),
(538, 24, 13, 0.005),
(539, 24, 6, 0.003),
(540, 25, 21, 0.150),
(541, 25, 15, 0.030),
(542, 25, 16, 0.030),
(543, 25, 23, 0.070),
(544, 25, 11, 0.005),
(545, 25, 12, 0.005),
(546, 25, 7, 0.010),
(547, 25, 13, 0.005),
(548, 25, 6, 0.003),
(549, 26, 21, 0.150),
(550, 26, 15, 0.060),
(551, 26, 23, 0.070),
(552, 26, 11, 0.005),
(553, 26, 12, 0.005),
(554, 26, 7, 0.010),
(555, 26, 13, 0.005),
(556, 26, 6, 0.003),
(557, 27, 21, 0.150),
(558, 27, 16, 0.060),
(559, 27, 23, 0.070),
(560, 27, 11, 0.005),
(561, 27, 12, 0.005),
(562, 27, 7, 0.010),
(563, 27, 13, 0.005),
(564, 27, 6, 0.003),
(565, 28, 21, 0.150),
(566, 28, 18, 0.050),
(567, 28, 23, 0.070),
(568, 28, 11, 0.005),
(569, 28, 12, 0.005),
(570, 28, 7, 0.010),
(571, 28, 13, 0.005),
(572, 28, 6, 0.003),
(573, 29, 21, 0.200),
(574, 29, 24, 0.100),
(575, 29, 25, 0.050),
(576, 29, 9, 0.025),
(577, 29, 10, 0.025),
(578, 29, 26, 0.010),
(579, 29, 6, 0.005),
(580, 30, 21, 0.200),
(581, 30, 15, 0.100),
(582, 30, 25, 0.050),
(583, 30, 23, 0.050),
(584, 30, 26, 0.010),
(585, 30, 6, 0.005),
(586, 31, 27, 0.150),
(587, 31, 25, 0.100),
(588, 31, 23, 0.050),
(589, 31, 26, 0.005),
(590, 32, 21, 0.200),
(591, 32, 28, 0.100),
(592, 32, 23, 0.050),
(593, 32, 26, 0.010),
(594, 32, 6, 0.005),
(595, 33, 29, 0.100),
(596, 33, 3, 0.020),
(597, 33, 11, 0.005),
(598, 33, 12, 0.005),
(599, 33, 7, 0.010),
(600, 33, 6, 0.005),
(601, 33, 19, 0.100),
(602, 34, 15, 0.100),
(603, 34, 3, 0.020),
(604, 34, 11, 0.005),
(605, 34, 12, 0.005),
(606, 34, 7, 0.010),
(607, 34, 6, 0.005),
(608, 34, 19, 0.100),
(609, 35, 24, 0.100),
(610, 35, 3, 0.010),
(611, 35, 30, 0.010),
(612, 35, 4, 0.005),
(613, 35, 7, 0.005),
(614, 36, 15, 0.100),
(615, 36, 11, 0.005),
(616, 36, 12, 0.005),
(617, 36, 4, 0.010),
(618, 36, 7, 0.010),
(619, 36, 6, 0.003),
(620, 37, 16, 0.100),
(621, 37, 11, 0.005),
(622, 37, 12, 0.005),
(623, 37, 4, 0.010),
(624, 37, 7, 0.010),
(625, 37, 6, 0.003),
(626, 38, 24, 0.100),
(627, 38, 11, 0.005),
(628, 38, 12, 0.005),
(629, 38, 7, 0.010),
(630, 38, 6, 0.003),
(631, 39, 31, 0.002),
(632, 39, 19, 0.150),
(633, 39, 32, 0.005),
(634, 40, 31, 0.002),
(635, 40, 33, 0.050),
(636, 40, 19, 0.100),
(637, 40, 32, 0.005),
(638, 41, 31, 0.002),
(639, 41, 33, 0.050),
(640, 41, 19, 0.100),
(641, 41, 34, 0.002),
(642, 41, 32, 0.005),
(643, 42, 35, 0.005),
(644, 42, 19, 0.150),
(645, 43, 35, 0.005),
(646, 43, 33, 0.050),
(647, 43, 19, 0.100),
(648, 44, 36, 0.200),
(649, 45, 19, 0.200),
(650, 46, 19, 0.500),
(651, 47, 37, 0.150),
(652, 47, 32, 0.010),
(653, 48, 37, 0.150),
(654, 48, 38, 0.050),
(655, 48, 32, 0.010),
(656, 49, 37, 0.150),
(657, 49, 39, 0.050),
(658, 49, 32, 0.010);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `location` varchar(255) NOT NULL,
  `order_total` decimal(10,2) NOT NULL CHECK (`order_total` >= 0),
  `status` enum('PENDING','CONFIRMED','PREPARING','OUT_FOR_DELIVERY','COMPLETED','CANCELLED') NOT NULL DEFAULT 'PENDING',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `coupon_code` varchar(50) DEFAULT NULL,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount_percent` decimal(5,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `menu_item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL CHECK (`quantity` > 0),
  `unit_price` decimal(10,2) NOT NULL CHECK (`unit_price` >= 0),
  `line_total` decimal(10,2) GENERATED ALWAYS AS (`quantity` * `unit_price`) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `payment_method` enum('cash','online','credit') NOT NULL DEFAULT 'cash',
  `amount` decimal(10,2) NOT NULL,
  `status` enum('paid','pending','failed') NOT NULL DEFAULT 'paid',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payroll`
--

CREATE TABLE `payroll` (
  `id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `month_year` varchar(20) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `role` varchar(50) NOT NULL,
  `salary` decimal(10,2) NOT NULL,
  `join_date` date NOT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_details`
--

CREATE TABLE `user_details` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `contact_no` varchar(20) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'Customer',
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_details`
--

INSERT INTO `user_details` (`id`, `first_name`, `last_name`, `gender`, `contact_no`, `age`, `email`, `username`, `password`, `role`, `phone`, `address`) VALUES
(5, 'dfa', 'asf', 'male', '32423', 19, 'safasdf@gmail.com', 'abishek00', '$2y$10$IoADwgeWKo6p/nkctLK.5uUjPml6aomwxSZdpwrczUI7EZyW.rK0K', 'Customer', NULL, NULL),
(6, 'q', 'q', 'male', '2', 1, 'asdfasdfa@gmail.com', '1', '$2y$10$HDZf2wHuwsdr8iERdw9P2.cGHkxtK.RQ6j/2oDKaSZgDSh6bgBs92', 'Customer', NULL, NULL),
(7, 'Abhishek', 'Dhimal', 'male', '9827064592', 20, 'abisekdhimal5@gmail.com', 'abishek77', '$2y$10$XcpJwqo6rqrmb2oN/UbHSuE5bBytnQWEFgjvvipIU/QOcQIfjSiNW', 'Admin', NULL, NULL),
(8, 'f', 'f', 'male', '4', 19, 'adddd@gmail.com', 'f', '$2y$10$Q4Io8DoSJ0Qi.u26RxlR4ODmaY40EvB.a/66EcdT7HuDZ0utp4eOa', 'Customer', NULL, NULL),
(9, 'ee', 'ee', 'female', '2', 19, 'sdfgsd@gmail.com', 'e', '$2y$10$uWcClnVvHUN2GFWjFrcXYea.DqwBa.xE4ZPIC0Ql52iCfOJpuQDU2', 'Customer', NULL, NULL),
(10, 'nabin', 'sdf', 'male', '3434', 20, 'sadfa@gmail.com', 'nabin33', '$2y$10$Um.tpte8HXhd5ilHykZSweOTXncTWtaqiTdBmIJwyXQV0uA0iwAce', 'Customer', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `discounts`
--
ALTER TABLE `discounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_expenses_user` (`created_by`);

--
-- Indexes for table `inventory_items`
--
ALTER TABLE `inventory_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `inventory_transactions`
--
ALTER TABLE `inventory_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inventory_item_id` (`inventory_item_id`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `is_active` (`is_active`);

--
-- Indexes for table `menu_item_recipes`
--
ALTER TABLE `menu_item_recipes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `menu_item_id` (`menu_item_id`),
  ADD KEY `inventory_item_id` (`inventory_item_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`,`created_at`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `menu_item_id` (`menu_item_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_payments_order` (`order_id`);

--
-- Indexes for table `payroll`
--
ALTER TABLE `payroll`
  ADD PRIMARY KEY (`id`),
  ADD KEY `staff_id` (`staff_id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_details`
--
ALTER TABLE `user_details`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `discounts`
--
ALTER TABLE `discounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `inventory_items`
--
ALTER TABLE `inventory_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `inventory_transactions`
--
ALTER TABLE `inventory_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=319;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `menu_item_recipes`
--
ALTER TABLE `menu_item_recipes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=659;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `payroll`
--
ALTER TABLE `payroll`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_details`
--
ALTER TABLE `user_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `fk_expenses_user` FOREIGN KEY (`created_by`) REFERENCES `user_details` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `menu_item_recipes`
--
ALTER TABLE `menu_item_recipes`
  ADD CONSTRAINT `fk_recipe_inventory` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_recipe_menu` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `user_details` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_oi_menu` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`),
  ADD CONSTRAINT `fk_oi_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_payments_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payroll`
--
ALTER TABLE `payroll`
  ADD CONSTRAINT `payroll_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
