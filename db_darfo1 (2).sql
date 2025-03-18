-- phpMyAdmin SQL Dump
-- version 5.0.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 18, 2025 at 03:42 AM
-- Server version: 10.4.14-MariaDB
-- PHP Version: 7.4.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_darfo1`
--

-- --------------------------------------------------------

--
-- Table structure for table `barangays`
--

CREATE TABLE `barangays` (
  `barangay_code` int(11) NOT NULL,
  `barangay_name` varchar(255) NOT NULL,
  `municipality_code` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `barangays`
--

INSERT INTO `barangays` (`barangay_code`, `barangay_name`, `municipality_code`) VALUES
(12801001, 'Adams (Pob.)', 12801000),
(12802001, 'Bani', 12802000),
(12802003, 'Cabaruan', 12802000),
(12802004, 'Cabulalaan', 12802000),
(12802013, 'Libtong', 12803000),
(12802014, 'Macupit', 12802000),
(12803002, 'Alogoog', 12803000),
(12803005, 'Balbaldez', 12803000),
(12804012, 'San Lorenzo (Pob.)', 12804000),
(12810002, 'San Isidro', 12810000),
(12813001, 'Pacifico', 12813000),
(12813011, 'Valdez', 12813000),
(12813013, 'Santiago', 12813000),
(12814008, 'Naguillan', 12814000),
(12815012, 'Poblacion 1', 12815000),
(12815016, 'Subec', 12815000),
(12816002, 'Cabagoan', 12816000),
(12816006, 'Dolores', 12816000),
(12816013, 'Nanguyudan', 12816000),
(12816015, 'Pambaran', 12816000),
(12816017, 'Paratong', 12816000),
(12816018, 'Pasil', 12816000),
(12817004, 'Caruan', 12817000),
(12817016, 'Ngabangab', 12817000),
(12818013, 'Lagandit', 12818000),
(12818015, 'Loing (Pob.)', 12818000),
(12819013, 'Liliputen', 12819000),
(12820003, 'San Agustin', 12820000),
(12820015, 'San Marcos', 12820000),
(12901001, 'Alilem Daya (Pob.)', 12901000),
(12901002, 'Amilongan', 12901000),
(12902002, 'Banbanaal', 12902000),
(12903011, 'Guimod', 12903000),
(12903012, 'Lingsat', 12903000),
(12903013, 'Malingeb', 12903000),
(12905023, 'Pug-os', 12905000),
(12909003, 'Bidbiday', 12909000),
(12911008, 'Poblacion Sur', 12911000),
(12912022, 'Maratudo', 12912000),
(12913001, 'Balaweg', 12913000),
(12913002, 'Bandril', 12913000),
(12913008, 'Mapisi', 12913000),
(12913009, 'Mission', 12913000),
(12913011, 'Poblacion West', 12913000),
(12914015, 'Lanipao', 12914000),
(12914016, 'Lungog', 12914000),
(12914017, 'Margaay', 12914000),
(12915002, 'Cayus', 12915000),
(12915003, 'Patungcaleo', 12915000),
(12915004, 'Malideg', 12915000),
(12915005, 'Namitpit', 12915000),
(12915006, 'Patiacan', 12915000),
(12915007, 'Legleg (Pob.)', 12915000),
(12915008, 'Suagayan', 12915000),
(12916015, 'Maligcong', 12916000),
(12916017, 'Poblacion Norte', 12916000),
(12917002, 'Kalumsing', 12917000),
(12917003, 'Lancuas', 12917000),
(12917005, 'Paltoc', 12917000),
(12918008, 'San Pablo', 12918000),
(12918010, 'Villa Quirino', 12918000),
(12928003, 'Borobor', 12928000),
(13302002, 'Basca', 13302000),
(13302005, 'Macabato', 13302000),
(13303013, 'Cabarsican', 13303000),
(13303017, 'Casiaman', 13303000),
(13304004, 'Cardiz', 13304000),
(13307005, 'Ballay', 13307000),
(13309015, 'Santiago Sur', 13309000),
(13311005, 'Ambaracao Sur', 13311000),
(13312002, 'Ambangonan', 13312000),
(13312010, 'Poblacion East', 13312000),
(13312012, 'Saytan', 13312000),
(13312013, 'Tavora East', 13312000),
(13313013, 'Cataguingtingan', 13313000),
(13315002, 'Amontoc', 13315000),
(13315017, 'Lon-oy', 13315000),
(13315020, 'Polipol', 13315000),
(13316005, 'Bambanay', 13316000),
(13316012, 'Calincamasan', 13316000),
(13316013, 'Casilagan', 13316000),
(13317003, 'Balaoc', 13317000),
(13318002, 'Corrooy', 13318000),
(13318013, 'Sapdaan', 13318000),
(13318014, 'Sasaba', 13318000),
(15504007, 'Curareng', 15504000),
(15519016, 'Tambac', 15519000),
(15520013, 'Potol', 15520000);

-- --------------------------------------------------------

--
-- Table structure for table `municipalities`
--

CREATE TABLE `municipalities` (
  `municipality_code` int(11) NOT NULL,
  `municipality_name` varchar(255) NOT NULL,
  `province_code` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `municipalities`
--

INSERT INTO `municipalities` (`municipality_code`, `municipality_name`, `province_code`) VALUES
(12801000, 'Adams', 12800000),
(12802000, 'Bacarra', 12800000),
(12803000, 'Badoc', 12800000),
(12804000, 'Bangui', 12800000),
(12810000, 'Dumalneg', 12800000),
(12813000, 'Marcos', 12800000),
(12814000, 'Nueva Era', 12800000),
(12815000, 'Pagudpud', 12800000),
(12816000, 'Paoay', 12800000),
(12817000, 'Pasuquin', 12800000),
(12818000, 'Piddig', 12800000),
(12819000, 'Pinili', 12800000),
(12820000, 'San Nicolas', 12800000),
(12901000, 'Alilem', 12900000),
(12902000, 'Banayoyo', 12900000),
(12903000, 'Bantay', 12900000),
(12905000, 'Cabugao', 12900000),
(12909000, 'Galimuyod', 12900000),
(12911000, 'Lidlidda', 12900000),
(12912000, 'Magsingal', 12900000),
(12913000, 'Nagbukel', 12900000),
(12914000, 'Narvacan', 12900000),
(12915000, 'Quirino', 12900000),
(12916000, 'Salcedo', 12900000),
(12917000, 'San Emilio', 12900000),
(12918000, 'San Esteban', 12900000),
(12928000, 'Santo Domingo', 12900000),
(13302000, 'Aringay', 13300000),
(13303000, 'Bacnotan', 13300000),
(13304000, 'Bagulin', 13300000),
(13307000, 'Bauang', 13300000),
(13309000, 'Caba', 13300000),
(13311000, 'Naguilian', 13300000),
(13312000, 'Pugo', 13300000),
(13313000, 'Rosario', 13300000),
(13315000, 'San Gabriel', 13300000),
(13316000, 'San Juan', 13300000),
(13317000, 'Santo Tomas', 13300000),
(13318000, 'Santol', 13300000),
(15504000, 'Alcala', 15500000),
(15519000, 'Dasol', 15500000),
(15520000, 'Infanta', 15500000);

-- --------------------------------------------------------

--
-- Table structure for table `provinces`
--

CREATE TABLE `provinces` (
  `province_code` int(11) NOT NULL,
  `province_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `provinces`
--

INSERT INTO `provinces` (`province_code`, `province_name`) VALUES
(12800000, 'Ilocos Norte'),
(12900000, 'Ilocos Sur'),
(13300000, 'La Union'),
(15500000, 'Pangasinan');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_beneficiary`
--

CREATE TABLE `tbl_beneficiary` (
  `beneficiary_id` int(11) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `mname` varchar(255) DEFAULT NULL,
  `lname` varchar(255) NOT NULL,
  `province_name` varchar(255) NOT NULL,
  `municipality_name` varchar(255) NOT NULL,
  `barangay_name` varchar(255) NOT NULL,
  `StreetPurok` varchar(100) NOT NULL,
  `station_id` int(8) NOT NULL,
  `coop_id` int(8) NOT NULL,
  `beneficiary_type` varchar(100) NOT NULL,
  `birthdate` date NOT NULL,
  `Sex` varchar(100) NOT NULL,
  `rsbsa_no` varchar(15) DEFAULT NULL,
  `if_applicable` varchar(100) NOT NULL,
  `contact_no` char(11) NOT NULL,
  `beneficiary_category` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_beneficiary`
--

INSERT INTO `tbl_beneficiary` (`beneficiary_id`, `fname`, `mname`, `lname`, `province_name`, `municipality_name`, `barangay_name`, `StreetPurok`, `station_id`, `coop_id`, `beneficiary_type`, `birthdate`, `Sex`, `rsbsa_no`, `if_applicable`, `contact_no`, `beneficiary_category`) VALUES
(14, 'KUTON', 'ako', 'TALTALON', 'Ilocos Sur', 'Quirino', 'Patungcaleo', '102 gegettt', 1, 0, 'Fisher', '2025-03-22', 'Female', '111111111111111', '', '09686434688', 'Individual'),
(16, 'gangster', 'ako', 'zae', 'Ilocos Norte', 'Piddig', 'Lagandit', '', 1, 10, 'School', '2025-03-18', 'Male', '', '', '09686434364', 'Group'),
(17, 'gangster', 'ako', 'zae', 'Ilocos Norte', 'Piddig', 'Lagandit', '', 1, 0, 'Farmer', '2025-03-18', 'Male', '', '', '09686434664', 'Individual'),
(18, 'Vien Daryl', 'Sagantiyoc', 'Saliganan', 'La Union', 'Bacnotan', 'Casiaman', '', 1, 11, 'Cluster', '2025-02-28', 'Male', '', '', '09686434664', 'Group'),
(19, 'Vien Daryl', 'Sagantiyoc', 'Saliganan', 'La Union', 'Bacnotan', 'Casiaman', '', 1, 0, 'Farmer', '2025-03-18', 'Male', '', '', '09686434645', 'Individual');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_cooperative`
--

CREATE TABLE `tbl_cooperative` (
  `coop_id` int(8) NOT NULL,
  `cooperative_name` varchar(255) NOT NULL,
  `station_id` int(8) NOT NULL,
  `province_name` varchar(255) NOT NULL,
  `municipality_name` varchar(255) NOT NULL,
  `barangay_name` varchar(255) NOT NULL,
  `archived_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_cooperative`
--

INSERT INTO `tbl_cooperative` (`coop_id`, `cooperative_name`, `station_id`, `province_name`, `municipality_name`, `barangay_name`, `archived_at`) VALUES
(8, 'UTYDFGFGaaaaa12215556789', 1, 'La Union', 'Rosario', 'Gumot-Nagcolaran', '2025-03-05 00:12:23'),
(9, 'pangetKA', 1, 'Ilocos Sur', 'Narvacan', 'Margaay', NULL),
(10, 'VIEN', 1, 'Ilocos Sur', 'Narvacan', 'Margaay', NULL),
(11, 'Onlyraps', 1, 'La Union', 'Santol', 'Corrooy', '2025-03-04 02:12:40');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_distribution`
--

CREATE TABLE `tbl_distribution` (
  `distribution_id` int(11) NOT NULL,
  `intervention_id` int(11) NOT NULL,
  `beneficiary_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `seed_id` int(11) NOT NULL,
  `station_id` int(8) NOT NULL,
  `distribution_date` date NOT NULL,
  `archived_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_distribution`
--

INSERT INTO `tbl_distribution` (`distribution_id`, `intervention_id`, `beneficiary_id`, `quantity`, `seed_id`, `station_id`, `distribution_date`, `archived_at`) VALUES
(1, 1, 6, 1, 1, 1, '2025-03-13', NULL),
(2, 1, 6, 1, 5, 1, '2025-03-13', NULL),
(3, 1, 6, 1, 5, 1, '2025-03-13', NULL),
(4, 1, 6, 2, 5, 1, '2025-04-06', NULL),
(5, 1, 6, 1, 5, 1, '2025-03-09', NULL),
(6, 1, 6, 1, 5, 1, '2025-03-13', NULL),
(7, 1, 7, 1, 5, 1, '2025-03-14', NULL),
(8, 1, 14, 14, 5, 1, '2025-03-18', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_intervention_inventory`
--

CREATE TABLE `tbl_intervention_inventory` (
  `intervention_id` int(8) NOT NULL,
  `int_type_id` int(8) NOT NULL,
  `description` varchar(255) NOT NULL,
  `quantity` int(8) NOT NULL,
  `quantity_left` int(8) NOT NULL,
  `seed_id` int(8) NOT NULL,
  `unit_id` int(8) NOT NULL,
  `station_id` int(8) NOT NULL,
  `archived_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_intervention_inventory`
--

INSERT INTO `tbl_intervention_inventory` (`intervention_id`, `int_type_id`, `description`, `quantity`, `quantity_left`, `seed_id`, `unit_id`, `station_id`, `archived_at`) VALUES
(1, 1, 'hh', 100, 0, 1, 1, 1, NULL),
(2, 2, 'hh', 100, 64, 2, 1, 2, NULL),
(3, 3, 'hh', 100, 77, 3, 1, 2, NULL),
(6, 1, '1gg', 1000, 976, 5, 1, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_intervention_type`
--

CREATE TABLE `tbl_intervention_type` (
  `int_type_id` int(8) NOT NULL,
  `intervention_name` varchar(100) NOT NULL,
  `station_id` int(8) NOT NULL,
  `archived_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_intervention_type`
--

INSERT INTO `tbl_intervention_type` (`int_type_id`, `intervention_name`, `station_id`, `archived_at`) VALUES
(1, 'Fruit Tree Seedlings', 1, NULL),
(2, 'Fisheries', 2, NULL),
(3, 'Fruit Tree Seedlings', 2, NULL),
(4, 'Goat12345', 1, '2025-03-04 01:22:37');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_seed_type`
--

CREATE TABLE `tbl_seed_type` (
  `seed_id` int(8) NOT NULL,
  `seed_name` varchar(100) NOT NULL,
  `int_type_id` int(8) NOT NULL,
  `station_id` int(8) NOT NULL,
  `archived_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_seed_type`
--

INSERT INTO `tbl_seed_type` (`seed_id`, `seed_name`, `int_type_id`, `station_id`, `archived_at`) VALUES
(1, 'Avocado', 1, 1, NULL),
(2, 'bangus', 2, 2, NULL),
(3, 'Avocado', 3, 2, NULL),
(4, 'Boer', 4, 1, '2025-03-04 01:27:26'),
(5, 'Mango', 1, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_station`
--

CREATE TABLE `tbl_station` (
  `station_id` int(8) NOT NULL,
  `station_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_station`
--

INSERT INTO `tbl_station` (`station_id`, `station_name`) VALUES
(1, 'Ilocos Sur Research Center (ISREC)'),
(2, 'Ilocos Norte Research & Experiment Center (INREC) - Batac'),
(3, 'Ilocos Norte Research & Experiment Center (INREC) - Dingras'),
(4, 'Ilocos Integrated Agricultural Research Center (ILIARC)'),
(5, 'Pangasinan Research & Experiment Center (PREC) - Sta. Barbara'),
(6, 'Pangasinan Research & Experiment Center (PREC) - Sual');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_unit`
--

CREATE TABLE `tbl_unit` (
  `unit_id` int(8) NOT NULL,
  `unit_name` varchar(100) NOT NULL,
  `station_id` int(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_unit`
--

INSERT INTO `tbl_unit` (`unit_id`, `unit_name`, `station_id`) VALUES
(1, 'Sacks', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user`
--

CREATE TABLE `tbl_user` (
  `uid` int(8) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `ulevel` varchar(50) NOT NULL,
  `fname` varchar(100) NOT NULL,
  `mname` varchar(100) NOT NULL,
  `lname` varchar(100) NOT NULL,
  `status` varchar(50) NOT NULL,
  `station_id` int(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_user`
--

INSERT INTO `tbl_user` (`uid`, `username`, `password`, `ulevel`, `fname`, `mname`, `lname`, `status`, `station_id`) VALUES
(27, 'admin', '$2y$10$JzMMti86wOByIoclwYpCUOtLa6SRZrUQfI1fq11BgY1wnNQ54VggO', 'Admin', 'Vien Daryl', 'Sagantiyoc', 'Saliganan', '0', 1),
(42, 'admin123', '$2y$10$Idn4v6O6ev0O7Ra39L3F1.nGQevZxwnurz1O3O.tVH.TGVHoCXCYC', 'Admin', 'Rhenz Eldrian', 'Mendoza', 'Lanuza', '0', 2),
(52, 'admin', '$2y$10$xBkYolhShiIBf6R6b47zduNoy5FFMGgcHGLbI09ajyeJby9GylnFC', 'Viewer', 'Tirador', 'ng Kaning', 'Lamig', '3', 1),
(53, 'isrec', '$2y$10$lr6psXYGXOJGjFeOWdvB6e02i3tvaLeDeCWUeETmRyzp1b30k1zR.', 'ISREC', 'raf', 'raf', 'Pangit', '0', 3),
(54, 'admin11', '$2y$10$cHRfI46lpCN8IWzxXbNl3una6s3sK5Ov1y9FGeHryTVFyow1ro7UC', 'Admin', 'Nicole', 'Susaya', 'Talua', '0', 3);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barangays`
--
ALTER TABLE `barangays`
  ADD PRIMARY KEY (`barangay_code`),
  ADD KEY `municipality_code` (`municipality_code`);

--
-- Indexes for table `municipalities`
--
ALTER TABLE `municipalities`
  ADD PRIMARY KEY (`municipality_code`),
  ADD KEY `province_code` (`province_code`);

--
-- Indexes for table `provinces`
--
ALTER TABLE `provinces`
  ADD PRIMARY KEY (`province_code`);

--
-- Indexes for table `tbl_beneficiary`
--
ALTER TABLE `tbl_beneficiary`
  ADD PRIMARY KEY (`beneficiary_id`);

--
-- Indexes for table `tbl_cooperative`
--
ALTER TABLE `tbl_cooperative`
  ADD PRIMARY KEY (`coop_id`);

--
-- Indexes for table `tbl_distribution`
--
ALTER TABLE `tbl_distribution`
  ADD PRIMARY KEY (`distribution_id`);

--
-- Indexes for table `tbl_intervention_inventory`
--
ALTER TABLE `tbl_intervention_inventory`
  ADD PRIMARY KEY (`intervention_id`);

--
-- Indexes for table `tbl_intervention_type`
--
ALTER TABLE `tbl_intervention_type`
  ADD PRIMARY KEY (`int_type_id`);

--
-- Indexes for table `tbl_seed_type`
--
ALTER TABLE `tbl_seed_type`
  ADD PRIMARY KEY (`seed_id`);

--
-- Indexes for table `tbl_station`
--
ALTER TABLE `tbl_station`
  ADD PRIMARY KEY (`station_id`);

--
-- Indexes for table `tbl_unit`
--
ALTER TABLE `tbl_unit`
  ADD PRIMARY KEY (`unit_id`);

--
-- Indexes for table `tbl_user`
--
ALTER TABLE `tbl_user`
  ADD PRIMARY KEY (`uid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_beneficiary`
--
ALTER TABLE `tbl_beneficiary`
  MODIFY `beneficiary_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `tbl_cooperative`
--
ALTER TABLE `tbl_cooperative`
  MODIFY `coop_id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tbl_distribution`
--
ALTER TABLE `tbl_distribution`
  MODIFY `distribution_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tbl_intervention_inventory`
--
ALTER TABLE `tbl_intervention_inventory`
  MODIFY `intervention_id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tbl_intervention_type`
--
ALTER TABLE `tbl_intervention_type`
  MODIFY `int_type_id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_seed_type`
--
ALTER TABLE `tbl_seed_type`
  MODIFY `seed_id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tbl_station`
--
ALTER TABLE `tbl_station`
  MODIFY `station_id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tbl_unit`
--
ALTER TABLE `tbl_unit`
  MODIFY `unit_id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_user`
--
ALTER TABLE `tbl_user`
  MODIFY `uid` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `barangays`
--
ALTER TABLE `barangays`
  ADD CONSTRAINT `barangays_ibfk_1` FOREIGN KEY (`municipality_code`) REFERENCES `municipalities` (`municipality_code`);

--
-- Constraints for table `municipalities`
--
ALTER TABLE `municipalities`
  ADD CONSTRAINT `municipalities_ibfk_1` FOREIGN KEY (`province_code`) REFERENCES `provinces` (`province_code`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
