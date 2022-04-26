-- phpMyAdmin SQL Dump
-- version 4.9.5
-- https://www.phpmyadmin.net/
--
-- Anamakine: localhost
-- Üretim Zamanı: 26 Nis 2022, 23:30:51
-- Sunucu sürümü: 5.7.23-0ubuntu0.18.04.1
-- PHP Sürümü: 7.4.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `path_db`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `company`
--

CREATE TABLE `company` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `company`
--

INSERT INTO `company` (`id`, `name`) VALUES
(5, 'ABC Company');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `customer`
--

CREATE TABLE `customer` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jwt` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jwt_expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `customer`
--

INSERT INTO `customer` (`id`, `name`, `username`, `password`, `jwt`, `jwt_expires`) VALUES
(13, 'Broderick Price', 'broderick_price', '4297f44b13955235245b2497399d7a93', 'NTc3MjAyMjA0MjYyMDEyNDY=', '2022-05-26 00:52:11'),
(14, 'Kaya Herzog', 'kaya_herzog', '4297f44b13955235245b2497399d7a93', NULL, NULL),
(15, 'Erin Bahringer I', 'erin_bahringer_i', '4297f44b13955235245b2497399d7a93', NULL, NULL);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Tablo döküm verisi `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20220425211026', '2022-04-25 21:27:38', 596),
('DoctrineMigrations\\Version20220425213346', '2022-04-25 21:34:02', 339),
('DoctrineMigrations\\Version20220425214110', '2022-04-25 21:41:23', 319),
('DoctrineMigrations\\Version20220426184029', '2022-04-26 18:40:45', 389),
('DoctrineMigrations\\Version20220426200957', '2022-04-26 20:10:04', 327),
('DoctrineMigrations\\Version20220426201454', '2022-04-26 20:15:00', 336),
('DoctrineMigrations\\Version20220426201842', '2022-04-26 20:19:10', 361);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `shipping_date` datetime NOT NULL,
  `order_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `orders`
--

INSERT INTO `orders` (`id`, `customer_id`, `shipping_date`, `order_code`, `address`, `price`) VALUES
(6, 13, '2022-05-29 01:43:40', '7017', '27513 Batz Pike Suite 948\nLake Ewell, IA 81708-4146', 147060);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `order_item`
--

CREATE TABLE `order_item` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `order_item`
--

INSERT INTO `order_item` (`id`, `order_id`, `product_id`, `quantity`) VALUES
(7, 6, 35, 1),
(8, 6, 36, 1),
(10, 6, 37, 1),
(11, 6, 33, 13);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `product`
--

CREATE TABLE `product` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `product`
--

INSERT INTO `product` (`id`, `name`, `description`, `price`) VALUES
(33, 'PlayStation3', 'officia perferendis temporibus vitae ea et doloremque consequatur vitae quos optio sed exercitationem consequuntur', 9290),
(34, 'Samsung Galaxy A30', 'laboriosam ea qui sit ab sed molestias excepturi sint odit assumenda', 9730),
(35, 'Yamaha Dragstar XVS', 'nostrum id vero sint officiis consequatur quo et aspernatur doloribus provident sit explicabo', 9390),
(36, 'Xiaomi Note8', 'qui porro tempore aut aut vitae qui sed nihil qui ut', 7920),
(37, 'Honda Shadow 750', 'velit quaerat dolor quasi sed at atque enim mollitia iste velit', 8980);

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `company`
--
ALTER TABLE `company`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Tablo için indeksler `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `order_item`
--
ALTER TABLE `order_item`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `company`
--
ALTER TABLE `company`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Tablo için AUTO_INCREMENT değeri `customer`
--
ALTER TABLE `customer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Tablo için AUTO_INCREMENT değeri `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Tablo için AUTO_INCREMENT değeri `order_item`
--
ALTER TABLE `order_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Tablo için AUTO_INCREMENT değeri `product`
--
ALTER TABLE `product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
