-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 13 Jul 2025 pada 11.01
-- Versi server: 10.4.27-MariaDB
-- Versi PHP: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `triak_coffee`
--

DELIMITER $$
--
-- Prosedur
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetRuleBasedRecommendation` (IN `p_jenis_kopi` ENUM('Arabika','Robusta'), IN `p_metode_penyajian` ENUM('V60','French Press','Espresso','Tubruk','Cold Brew','Moka Pot'), IN `p_profil_rasa` ENUM('Fruity','Chocolate','Nutty','Earthy','Floral','Citrus','Caramel','Bitter Chocolate','Honey','Spicy','Bitter','Sweet'), IN `p_proses` ENUM('Washed','Honey','Natural'), OUT `p_recommended_roast` ENUM('light','medium','medium-dark','dark'), OUT `p_rule_applied` VARCHAR(255))   BEGIN
    -- Rule 1: Arabika + V60 + Fruity + Washed → Light Roast
    IF p_jenis_kopi = 'Arabika' AND p_metode_penyajian = 'V60' AND p_profil_rasa = 'Fruity' AND p_proses = 'Washed' THEN
        SET p_recommended_roast = 'light';
        SET p_rule_applied = 'Rule 1';
    -- Rule 2: Arabika + French Press + Chocolate + Honey → Medium Roast
    ELSEIF p_jenis_kopi = 'Arabika' AND p_metode_penyajian = 'French Press' AND p_profil_rasa = 'Chocolate' AND p_proses = 'Honey' THEN
        SET p_recommended_roast = 'medium';
        SET p_rule_applied = 'Rule 2';
    -- Rule 3: Arabika + Espresso + Nutty + Natural → Medium Roast
    ELSEIF p_jenis_kopi = 'Arabika' AND p_metode_penyajian = 'Espresso' AND p_profil_rasa = 'Nutty' AND p_proses = 'Natural' THEN
        SET p_recommended_roast = 'medium';
        SET p_rule_applied = 'Rule 3';
    -- Rule 4: Robusta + Tubruk + Earthy + Natural → Dark Roast
    ELSEIF p_jenis_kopi = 'Robusta' AND p_metode_penyajian = 'Tubruk' AND p_profil_rasa = 'Earthy' AND p_proses = 'Natural' THEN
        SET p_recommended_roast = 'dark';
        SET p_rule_applied = 'Rule 4';
    -- Rule 5: Robusta + Cold Brew + Fruity + Honey → Medium Roast
    ELSEIF p_jenis_kopi = 'Robusta' AND p_metode_penyajian = 'Cold Brew' AND p_profil_rasa = 'Fruity' AND p_proses = 'Honey' THEN
        SET p_recommended_roast = 'medium';
        SET p_rule_applied = 'Rule 5';
    -- Add more rules as needed...
    -- Rule 17: Arabika + V60 + Floral + Washed → Light Roast
    ELSEIF p_jenis_kopi = 'Arabika' AND p_metode_penyajian = 'V60' AND p_profil_rasa = 'Floral' AND p_proses = 'Washed' THEN
        SET p_recommended_roast = 'light';
        SET p_rule_applied = 'Rule 17';
    -- Rule 19: Arabika + Espresso + Spicy + Honey → Medium-Dark Roast
    ELSEIF p_jenis_kopi = 'Arabika' AND p_metode_penyajian = 'Espresso' AND p_profil_rasa = 'Spicy' AND p_proses = 'Honey' THEN
        SET p_recommended_roast = 'medium-dark';
        SET p_rule_applied = 'Rule 19';
    -- Rule 20: Robusta + Cold Brew + Sweet + Honey → Medium Roast
    ELSEIF p_jenis_kopi = 'Robusta' AND p_metode_penyajian = 'Cold Brew' AND p_profil_rasa = 'Sweet' AND p_proses = 'Honey' THEN
        SET p_recommended_roast = 'medium';
        SET p_rule_applied = 'Rule 20';
    -- Default rule if no match
    ELSE
        SET p_recommended_roast = 'medium';
        SET p_rule_applied = 'Default Rule';
    END IF;
END$$

--
-- Fungsi
--
CREATE DEFINER=`root`@`localhost` FUNCTION `GetCoffeeMatchScore` (`coffee_jenis` VARCHAR(10), `coffee_profil` VARCHAR(20), `coffee_proses` VARCHAR(10), `coffee_roast` VARCHAR(15), `user_jenis` VARCHAR(10), `user_profil` VARCHAR(20), `user_proses` VARCHAR(10), `recommended_roast` VARCHAR(15)) RETURNS INT(11) DETERMINISTIC READS SQL DATA BEGIN
    DECLARE score INT DEFAULT 0;
    
    -- Perfect match for recommended roast and jenis
    IF coffee_roast = recommended_roast AND coffee_jenis = user_jenis THEN
        SET score = 100;
    -- Match recommended roast
    ELSEIF coffee_roast = recommended_roast THEN
        SET score = 90;
    -- Match jenis kopi
    ELSEIF coffee_jenis = user_jenis THEN
        SET score = 80;
    -- Match profil rasa
    ELSEIF coffee_profil = user_profil THEN
        SET score = 70;
    -- Match proses
    ELSEIF coffee_proses = user_proses THEN
        SET score = 60;
    -- Default score
    ELSE
        SET score = 50;
    END IF;
    
    RETURN score;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `coffee_statistics`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `coffee_statistics` (
`id` int(11)
,`name` varchar(100)
,`jenis_kopi` enum('Arabika','Robusta')
,`roast_level` enum('light','medium','medium-dark','dark')
,`profil_rasa` enum('Fruity','Chocolate','Nutty','Earthy','Floral','Citrus','Caramel','Bitter Chocolate','Honey','Spicy','Bitter','Sweet')
,`proses` enum('Washed','Honey','Natural')
,`price` decimal(10,2)
,`total_reviews` bigint(21)
,`average_rating` decimal(14,4)
,`favorites_count` decimal(22,0)
);

-- --------------------------------------------------------

--
-- Struktur dari tabel `coffee_types`
--

CREATE TABLE `coffee_types` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `jenis_kopi` enum('Arabika','Robusta') NOT NULL,
  `roast_level` enum('light','medium','medium-dark','dark') NOT NULL,
  `flavor_notes` text DEFAULT NULL,
  `profil_rasa` enum('Fruity','Chocolate','Nutty','Earthy','Floral','Citrus','Caramel','Bitter Chocolate','Honey','Spicy','Bitter','Sweet') NOT NULL,
  `proses` enum('Washed','Honey','Natural') NOT NULL,
  `brewing_method` text DEFAULT NULL,
  `origin` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `coffee_types`
--

INSERT INTO `coffee_types` (`id`, `name`, `description`, `jenis_kopi`, `roast_level`, `flavor_notes`, `profil_rasa`, `proses`, `brewing_method`, `origin`, `price`, `image_url`, `created_at`) VALUES
(1, 'Ethiopian Yirgacheffe', 'Bright and floral with citrus notes', 'Arabika', 'light', 'Citrus, Floral, Tea-like', 'Floral', 'Washed', 'V60, Pour-over, Aeropress', 'Ethiopia', '18.50', 'https://images.pexels.com/photos/894695/pexels-photo-894695.jpeg', '2025-07-04 14:43:41'),
(2, 'Colombian Supremo', 'Well-balanced with chocolate and caramel notes', 'Arabika', 'medium', 'Chocolate, Caramel, Nuts', 'Chocolate', 'Honey', 'French Press, Drip', 'Colombia', '16.00', 'https://images.pexels.com/photos/1695052/pexels-photo-1695052.jpeg', '2025-07-04 14:43:41'),
(3, 'Guatemalan Antigua', 'Full-bodied with smoky and spicy notes', 'Arabika', 'medium', 'Smoky, Spicy, Dark Chocolate', 'Spicy', 'Honey', 'Espresso, French Press', 'Guatemala', '17.25', 'https://images.pexels.com/photos/1695052/pexels-photo-1695052.jpeg', '2025-07-04 14:43:41'),
(4, 'Sumatra Mandheling', 'Bold and earthy with herbal notes', 'Robusta', 'dark', 'Earthy, Herbal, Bold', 'Earthy', 'Natural', 'Tubruk, French Press', 'Indonesia', '15.75', 'https://images.pexels.com/photos/894695/pexels-photo-894695.jpeg', '2025-07-04 14:43:41'),
(5, 'Brazilian Santos', 'Nutty and sweet with caramel undertones', 'Arabika', 'medium', 'Nutty, Sweet, Caramel', 'Nutty', 'Honey', 'Moka Pot, Espresso', 'Brazil', '14.50', 'https://images.pexels.com/photos/1695052/pexels-photo-1695052.jpeg', '2025-07-04 14:43:41'),
(6, 'Java Robusta', 'Strong and bitter with earthy finish', 'Robusta', 'dark', 'Bitter, Strong, Earthy', 'Bitter', 'Natural', 'Espresso, Tubruk', 'Indonesia', '13.00', 'https://images.pexels.com/photos/894695/pexels-photo-894695.jpeg', '2025-07-04 14:43:41'),
(7, 'Kenya AA', 'Wine-like acidity with fruity notes', 'Arabika', 'light', 'Wine, Fruity, Bright', 'Fruity', 'Washed', 'V60, Pour-over', 'Kenya', '19.00', 'https://images.pexels.com/photos/894695/pexels-photo-894695.jpeg', '2025-07-04 14:43:41'),
(8, 'Costa Rica Tarrazú', 'Citrusy and bright with honey sweetness', 'Arabika', 'medium', 'Citrus, Honey, Bright', 'Citrus', 'Honey', 'V60, Aeropress', 'Costa Rica', '16.75', 'https://images.pexels.com/photos/1695052/pexels-photo-1695052.jpeg', '2025-07-04 14:43:41'),
(9, 'Panama Geisha', 'Floral and tea-like with honey notes', 'Arabika', 'light', 'Floral, Tea, Honey', 'Honey', 'Washed', 'V60, Pour-over', 'Panama', '25.00', 'https://images.pexels.com/photos/894695/pexels-photo-894695.jpeg', '2025-07-04 14:43:41'),
(10, 'Vietnam Robusta', 'Strong and earthy with sweet undertones', 'Robusta', 'medium', 'Strong, Earthy, Sweet', 'Sweet', 'Honey', 'Cold Brew, French Press', 'Vietnam', '12.50', 'https://images.pexels.com/photos/1695052/pexels-photo-1695052.jpeg', '2025-07-04 14:43:41'),
(11, 'Jamaica Blue Mountain', 'Mild and smooth with caramel notes', 'Arabika', 'medium', 'Mild, Smooth, Caramel', 'Caramel', 'Honey', 'Cold Brew, Drip', 'Jamaica', '35.00', 'https://images.pexels.com/photos/894695/pexels-photo-894695.jpeg', '2025-07-04 14:43:41'),
(12, 'Yemen Mocha', 'Wine-like with fruity and earthy notes', 'Arabika', 'medium', 'Wine, Fruity, Earthy', 'Fruity', 'Washed', 'Cold Brew, French Press', 'Yemen', '22.00', 'https://images.pexels.com/photos/1695052/pexels-photo-1695052.jpeg', '2025-07-04 14:43:41');

-- --------------------------------------------------------

--
-- Struktur dari tabel `proses_roasting`
--

CREATE TABLE `proses_roasting` (
  `id` int(11) NOT NULL,
  `density_level` varchar(20) DEFAULT NULL,
  `apparent_density` varchar(30) DEFAULT NULL,
  `initial_temp` int(11) DEFAULT NULL,
  `final_temp` varchar(20) DEFAULT NULL,
  `agtron` varchar(20) DEFAULT NULL,
  `roast_level` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `proses_roasting`
--

INSERT INTO `proses_roasting` (`id`, `density_level`, `apparent_density`, `initial_temp`, `final_temp`, `agtron`, `roast_level`) VALUES
(1, 'Low', '< 650', 150, '180 - 190', '75 - 65', 'Very-light to medium-light'),
(2, 'Medium', '651 - 700', 160, '185 - 195', '70 - 58', 'Light to medium'),
(3, 'High', '701 - 750', 170, '190 - 205', '65 - 55', 'Medium-light to medium-high'),
(4, 'Very High', '> 750', 190, '200 - 220', '60 - 45', 'Medium-light to dark');

-- --------------------------------------------------------

--
-- Struktur dari tabel `recommendations`
--

CREATE TABLE `recommendations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `coffee_id` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT 0,
  `review` text DEFAULT NULL,
  `recommended_roast` enum('light','medium','medium-dark','dark') DEFAULT NULL,
  `rule_applied` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `recommendations`
--

INSERT INTO `recommendations` (`id`, `user_id`, `coffee_id`, `rating`, `review`, `recommended_roast`, `rule_applied`, `created_at`) VALUES
(4, 3, 5, 4, NULL, NULL, NULL, '2025-07-07 05:50:07'),
(5, 3, 2, 4, NULL, NULL, NULL, '2025-07-07 05:50:12'),
(6, 3, 3, 4, NULL, NULL, NULL, '2025-07-07 05:50:15');

-- --------------------------------------------------------

--
-- Struktur dari tabel `recommendation_history`
--

CREATE TABLE `recommendation_history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `jenis_kopi` varchar(20) DEFAULT NULL,
  `metode_penyajian` varchar(50) DEFAULT NULL,
  `profil_rasa` varchar(30) DEFAULT NULL,
  `proses` varchar(20) DEFAULT NULL,
  `recommended_roast` enum('light','medium','medium-dark','dark') DEFAULT NULL,
  `rule_applied` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `recommendation_history`
--

INSERT INTO `recommendation_history` (`id`, `user_id`, `jenis_kopi`, `metode_penyajian`, `profil_rasa`, `proses`, `recommended_roast`, `rule_applied`, `created_at`) VALUES
(2, 3, 'Arabika', 'French Press', 'Earthy', 'Honey', 'medium', 'Default Rule', '2025-07-08 04:17:15'),
(3, 3, 'Arabika', 'V60', 'Fruity', 'Washed', 'light', 'Rule 1', '2025-07-08 04:17:49'),
(4, 3, 'Robusta', 'Espresso', 'Chocolate', 'Natural', 'dark', 'Partial Match: Rule 8', '2025-07-08 06:02:40'),
(5, 3, 'Robusta', 'French Press', 'Fruity', 'Washed', 'medium', 'Default Rule', '2025-07-08 06:02:50'),
(6, 3, 'Arabika', 'V60', 'Fruity', 'Natural', 'medium', 'Default Rule', '2025-07-09 02:37:58'),
(7, 3, 'Arabika', 'V60', 'Fruity', 'Washed', 'light', 'Rule 1', '2025-07-09 02:38:01'),
(8, 3, 'Arabika', 'Tubruk', 'Caramel', 'Natural', 'medium', 'Default Rule', '2025-07-10 07:50:01'),
(9, 3, 'Arabika', 'V60', 'Fruity', 'Washed', 'light', 'Rule 1', '2025-07-12 05:36:24'),
(10, 3, 'Robusta', 'Espresso', 'Earthy', 'Natural', 'dark', 'Rule 8', '2025-07-13 02:06:28');

-- --------------------------------------------------------

--
-- Struktur dari tabel `roasting_rules`
--

CREATE TABLE `roasting_rules` (
  `id` int(11) NOT NULL,
  `rule_name` varchar(100) NOT NULL,
  `jenis_kopi` enum('Arabika','Robusta') NOT NULL,
  `metode_penyajian` enum('V60','French Press','Espresso','Tubruk','Cold Brew','Moka Pot') NOT NULL,
  `profil_rasa` enum('Fruity','Chocolate','Nutty','Earthy','Floral','Citrus','Caramel','Bitter Chocolate','Honey','Spicy','Bitter','Sweet') NOT NULL,
  `proses` enum('Washed','Honey','Natural') NOT NULL,
  `recommended_roast` enum('light','medium','medium-dark','dark') NOT NULL,
  `roasting_temperature` int(11) DEFAULT NULL,
  `roasting_time` int(11) DEFAULT NULL,
  `roasting_notes` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `roasting_rules`
--

INSERT INTO `roasting_rules` (`id`, `rule_name`, `jenis_kopi`, `metode_penyajian`, `profil_rasa`, `proses`, `recommended_roast`, `roasting_temperature`, `roasting_time`, `roasting_notes`, `is_active`, `created_at`) VALUES
(1, 'Rule 1', 'Arabika', 'V60', 'Fruity', 'Washed', 'light', 195, 12, 'Light roast untuk mempertahankan keasaman dan aroma floral', 1, '2025-07-05 06:12:08'),
(2, 'Rule 2', 'Arabika', 'French Press', 'Chocolate', 'Honey', 'medium', 205, 15, 'Medium roast untuk mengembangkan rasa chocolate', 1, '2025-07-05 06:12:08'),
(3, 'Rule 3', 'Arabika', 'Espresso', 'Nutty', 'Natural', 'medium', 210, 14, 'Medium roast untuk espresso dengan karakter nutty', 1, '2025-07-05 06:12:08'),
(4, 'Rule 4', 'Robusta', 'Tubruk', 'Earthy', 'Natural', 'dark', 220, 18, 'Dark roast untuk robusta dengan karakter earthy yang kuat', 1, '2025-07-05 06:12:08'),
(5, 'Rule 5', 'Robusta', 'Cold Brew', 'Fruity', 'Honey', 'medium', 200, 16, 'Medium roast untuk cold brew dengan sentuhan fruity', 1, '2025-07-05 06:12:08'),
(6, 'Rule 6', 'Arabika', 'Moka Pot', 'Floral', 'Washed', 'medium', 200, 13, 'Medium roast untuk moka pot dengan aroma floral', 1, '2025-07-05 06:12:08'),
(7, 'Rule 7', 'Arabika', 'V60', 'Citrus', 'Honey', 'medium', 198, 13, 'Medium roast untuk V60 dengan karakter citrus', 1, '2025-07-05 06:12:08'),
(8, 'Rule 8', 'Robusta', 'Espresso', 'Earthy', 'Natural', 'dark', 225, 20, 'Dark roast untuk espresso robusta yang bold', 1, '2025-07-05 06:12:08'),
(9, 'Rule 9', 'Arabika', 'Cold Brew', 'Caramel', 'Honey', 'medium', 202, 14, 'Medium roast untuk cold brew dengan rasa caramel', 1, '2025-07-05 06:12:08'),
(10, 'Rule 10', 'Robusta', 'French Press', 'Bitter Chocolate', 'Honey', 'medium', 208, 16, 'Medium roast untuk french press dengan rasa bitter chocolate', 1, '2025-07-05 06:12:08'),
(11, 'Rule 11', 'Arabika', 'V60', 'Honey', 'Washed', 'light', 192, 11, 'Light roast untuk mempertahankan sweetness honey', 1, '2025-07-05 06:12:08'),
(12, 'Rule 12', 'Arabika', 'Moka Pot', 'Nutty', 'Honey', 'medium', 205, 14, 'Medium roast untuk moka pot dengan karakter nutty', 1, '2025-07-05 06:12:08'),
(13, 'Rule 13', 'Robusta', 'Tubruk', 'Earthy', 'Natural', 'dark', 220, 18, 'Dark roast untuk tubruk robusta yang kuat', 1, '2025-07-05 06:12:08'),
(14, 'Rule 14', 'Arabika', 'Cold Brew', 'Fruity', 'Washed', 'medium', 198, 15, 'Medium roast untuk cold brew dengan karakter fruity', 1, '2025-07-05 06:12:08'),
(15, 'Rule 15', 'Robusta', 'Espresso', 'Bitter', 'Natural', 'dark', 230, 22, 'Dark roast untuk espresso robusta yang bitter', 1, '2025-07-05 06:12:08'),
(16, 'Rule 16', 'Arabika', 'French Press', 'Chocolate', 'Honey', 'medium', 205, 15, 'Medium roast untuk french press dengan rasa chocolate', 1, '2025-07-05 06:12:08'),
(17, 'Rule 17', 'Arabika', 'V60', 'Floral', 'Washed', 'light', 190, 10, 'Light roast untuk mempertahankan karakter floral', 1, '2025-07-05 06:12:08'),
(18, 'Rule 18', 'Robusta', 'Moka Pot', 'Earthy', 'Natural', 'dark', 218, 17, 'Dark roast untuk moka pot robusta', 1, '2025-07-05 06:12:08'),
(19, 'Rule 19', 'Arabika', 'Espresso', 'Spicy', 'Honey', 'medium', 215, 16, 'Medium-dark roast untuk espresso dengan karakter spicy', 1, '2025-07-05 06:12:08'),
(20, 'Rule 20', 'Robusta', 'Cold Brew', 'Sweet', 'Honey', 'medium', 200, 15, 'Medium roast untuk cold brew robusta yang sweet', 1, '2025-07-05 06:12:08');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `created_at`) VALUES
(2, 'admin', 'admin@triakcoffee.com', '$2y$10$cL18m6UwEtOfQAcn4Sz1/.eo0Kg9LuSVR7FC0CSLm/DyffLIEJmt6', 'admin', '2025-07-05 03:50:51'),
(3, 'user', 'user@gmail.com', '$2y$10$7W489i5TjOwaee6pWlwHMeBBMvU6bbN31OyChW3cNcDgoAxDvrUWO', 'user', '2025-07-05 04:02:03');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user_preferences`
--

CREATE TABLE `user_preferences` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `jenis_kopi` enum('Arabika','Robusta') DEFAULT NULL,
  `metode_penyajian` enum('V60','French Press','Espresso','Tubruk','Cold Brew','Moka Pot') DEFAULT NULL,
  `profil_rasa` enum('Fruity','Chocolate','Nutty','Earthy','Floral','Citrus','Caramel','Bitter Chocolate','Honey','Spicy','Bitter','Sweet') DEFAULT NULL,
  `proses` enum('Washed','Honey','Natural') DEFAULT NULL,
  `preferred_roast` enum('light','medium','medium-dark','dark') DEFAULT NULL,
  `preferred_brewing` varchar(100) DEFAULT NULL,
  `flavor_preference` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `user_preferences`
--

INSERT INTO `user_preferences` (`id`, `user_id`, `jenis_kopi`, `metode_penyajian`, `profil_rasa`, `proses`, `preferred_roast`, `preferred_brewing`, `flavor_preference`) VALUES
(2, 3, 'Robusta', 'Espresso', 'Earthy', 'Natural', 'dark', NULL, '');

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `user_statistics`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `user_statistics` (
`id` int(11)
,`username` varchar(50)
,`email` varchar(100)
,`created_at` timestamp
,`total_reviews` bigint(21)
,`average_rating` decimal(14,4)
,`last_review_date` timestamp
,`jenis_kopi` enum('Arabika','Robusta')
,`metode_penyajian` enum('V60','French Press','Espresso','Tubruk','Cold Brew','Moka Pot')
,`profil_rasa` enum('Fruity','Chocolate','Nutty','Earthy','Floral','Citrus','Caramel','Bitter Chocolate','Honey','Spicy','Bitter','Sweet')
,`proses` enum('Washed','Honey','Natural')
,`preferred_roast` enum('light','medium','medium-dark','dark')
);

-- --------------------------------------------------------

--
-- Struktur untuk view `coffee_statistics`
--
DROP TABLE IF EXISTS `coffee_statistics`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `coffee_statistics`  AS SELECT `ct`.`id` AS `id`, `ct`.`name` AS `name`, `ct`.`jenis_kopi` AS `jenis_kopi`, `ct`.`roast_level` AS `roast_level`, `ct`.`profil_rasa` AS `profil_rasa`, `ct`.`proses` AS `proses`, `ct`.`price` AS `price`, count(`r`.`id`) AS `total_reviews`, avg(`r`.`rating`) AS `average_rating`, sum(case when `r`.`rating` >= 4 then 1 else 0 end) AS `favorites_count` FROM (`coffee_types` `ct` left join `recommendations` `r` on(`ct`.`id` = `r`.`coffee_id`)) GROUP BY `ct`.`id``id`  ;

-- --------------------------------------------------------

--
-- Struktur untuk view `user_statistics`
--
DROP TABLE IF EXISTS `user_statistics`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `user_statistics`  AS SELECT `u`.`id` AS `id`, `u`.`username` AS `username`, `u`.`email` AS `email`, `u`.`created_at` AS `created_at`, count(`r`.`id`) AS `total_reviews`, avg(`r`.`rating`) AS `average_rating`, max(`r`.`created_at`) AS `last_review_date`, `up`.`jenis_kopi` AS `jenis_kopi`, `up`.`metode_penyajian` AS `metode_penyajian`, `up`.`profil_rasa` AS `profil_rasa`, `up`.`proses` AS `proses`, `up`.`preferred_roast` AS `preferred_roast` FROM ((`users` `u` left join `recommendations` `r` on(`u`.`id` = `r`.`user_id`)) left join `user_preferences` `up` on(`u`.`id` = `up`.`user_id`)) WHERE `u`.`role` = 'user' GROUP BY `u`.`id``id`  ;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `coffee_types`
--
ALTER TABLE `coffee_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_coffee_jenis_profil` (`jenis_kopi`,`profil_rasa`),
  ADD KEY `idx_coffee_roast_level` (`roast_level`);

--
-- Indeks untuk tabel `proses_roasting`
--
ALTER TABLE `proses_roasting`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `recommendations`
--
ALTER TABLE `recommendations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `coffee_id` (`coffee_id`),
  ADD KEY `idx_recommendations_user_coffee` (`user_id`,`coffee_id`),
  ADD KEY `idx_recommendations_rating` (`rating`);

--
-- Indeks untuk tabel `recommendation_history`
--
ALTER TABLE `recommendation_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `roasting_rules`
--
ALTER TABLE `roasting_rules`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeks untuk tabel `user_preferences`
--
ALTER TABLE `user_preferences`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_preferences_user` (`user_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `coffee_types`
--
ALTER TABLE `coffee_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `proses_roasting`
--
ALTER TABLE `proses_roasting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `recommendations`
--
ALTER TABLE `recommendations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `recommendation_history`
--
ALTER TABLE `recommendation_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `roasting_rules`
--
ALTER TABLE `roasting_rules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `user_preferences`
--
ALTER TABLE `user_preferences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `recommendations`
--
ALTER TABLE `recommendations`
  ADD CONSTRAINT `recommendations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `recommendations_ibfk_2` FOREIGN KEY (`coffee_id`) REFERENCES `coffee_types` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `recommendation_history`
--
ALTER TABLE `recommendation_history`
  ADD CONSTRAINT `recommendation_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `user_preferences`
--
ALTER TABLE `user_preferences`
  ADD CONSTRAINT `user_preferences_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
