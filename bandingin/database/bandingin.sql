-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 09, 2026 at 02:27 AM
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
-- Database: `bandingin`
--

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `platform` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `favorites`
--

INSERT INTO `favorites` (`id`, `user_id`, `product_id`, `platform`, `created_at`) VALUES
(93, 2, 1, 'shopee', '2026-04-08 10:46:01'),
(94, 2, 1, 'lazada', '2026-04-08 10:46:09'),
(97, 2, 6, 'shopee', '2026-04-08 10:51:09'),
(100, 6, 9, 'blibli', '2026-04-08 11:32:45'),
(101, 6, 5, 'lazada', '2026-04-08 12:51:16'),
(102, 6, 5, 'shopee', '2026-04-08 12:51:19'),
(104, 6, 1, 'tokopedia', '2026-04-08 15:32:15'),
(107, 6, 1, 'lazada', '2026-04-08 17:39:42'),
(108, 6, 6, 'shopee', '2026-04-08 18:20:47');

-- --------------------------------------------------------

--
-- Table structure for table `platforms`
--

CREATE TABLE `platforms` (
  `id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `platforms`
--

INSERT INTO `platforms` (`id`, `name`) VALUES
(1, 'Shopee'),
(2, 'Tokopedia'),
(3, 'Lazada'),
(4, 'Blibli');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `image`, `category`) VALUES
(1, 'Apple iPhone 15 128GB', 'iphone15.jpg', 'Smartphone'),
(2, 'ASUS ROG Strix G15 Gaming Laptop', 'rog_g15.jpg', 'Laptop'),
(3, 'Nike Air Max SC', 'nike_airmax.jpg', 'Sepatu'),
(4, 'Meja Belajar Minimalis Kayu', 'meja_belajar.jpg', 'Furniture'),
(5, 'Samsung Galaxy S24', 'samsung_s24.jpg', 'Smartphone'),
(6, 'AirPods Pro', 'airpods_pro.jpg', 'Audio'),
(7, 'Xiaomi Redmi Note 13', 'xiaomi_redmi.jpg', 'Smartphone'),
(8, 'Tas Ransel 40L', 'tas_ransel.jpg', 'Fashion'),
(9, 'PS5 Slim', 'ps5_slim.jpg', 'Gaming');

-- --------------------------------------------------------

--
-- Table structure for table `product_prices`
--

CREATE TABLE `product_prices` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `platform_id` int(11) DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_prices`
--

INSERT INTO `product_prices` (`id`, `product_id`, `platform_id`, `price`, `link`) VALUES
(1, 1, 1, 12499000, 'https://shopee.co.id/search?keyword=iphone%2015%20128gb'),
(2, 1, 2, 12999000, 'https://www.tokopedia.com/search?q=iphone%2015%20128gb'),
(3, 1, 3, 12899000, 'https://www.lazada.co.id/catalog/?q=iphone%2015%20128gb'),
(4, 1, 4, 13100000, 'https://www.blibli.com/jual/iphone-15-128gb'),
(9, 2, 1, 24500000, 'https://shopee.co.id/search?keyword=asus%20rog%20strix%20g15'),
(10, 2, 2, 24850000, 'https://www.tokopedia.com/search?q=asus%20rog%20strix%20g15'),
(11, 2, 3, 24700000, 'https://www.lazada.co.id/catalog/?q=asus%20rog%20strix%20g15'),
(12, 2, 4, 24900000, 'https://www.blibli.com/jual/asus-rog-strix-g15'),
(13, 3, 1, 1299000, 'https://shopee.co.id/search?keyword=nike%20air%20max%20sc'),
(14, 3, 2, 1325000, 'https://www.tokopedia.com/search?q=nike%20air%20max%20sc'),
(15, 3, 3, 1340000, 'https://www.lazada.co.id/catalog/?q=nike%20air%20max%20sc'),
(16, 3, 4, 1350000, 'https://www.blibli.com/jual/nike-air-max-sc'),
(17, 4, 1, 420000, 'https://shopee.co.id/search?keyword=meja%20belajar%20minimalis'),
(18, 4, 2, 450000, 'https://www.tokopedia.com/search?q=meja%20belajar%20minimalis'),
(19, 4, 3, 440000, 'https://www.lazada.co.id/catalog/?q=meja%20belajar%20minimalis'),
(20, 4, 4, 460000, 'https://www.blibli.com/jual/meja-belajar-minimalis'),
(21, 5, 1, 8299000, 'https://shopee.co.id/search?keyword=samsung%20galaxy%20s24'),
(22, 5, 2, 8499000, 'https://www.tokopedia.com/search?q=samsung%20galaxy%20s24'),
(23, 5, 3, 8350000, 'https://www.lazada.co.id/catalog/?q=samsung%20galaxy%20s24'),
(24, 5, 4, 8600000, 'https://www.blibli.com/jual/samsung-galaxy-s24'),
(25, 6, 1, 3299000, 'https://shopee.co.id/search?keyword=airpods%20pro'),
(26, 6, 2, 3499000, 'https://www.tokopedia.com/search?q=airpods%20pro'),
(27, 6, 3, 3350000, 'https://www.lazada.co.id/catalog/?q=airpods%20pro'),
(28, 6, 4, 3600000, 'https://www.blibli.com/jual/airpods-pro'),
(29, 7, 1, 2099000, 'https://shopee.co.id/search?keyword=xiaomi%20redmi%20note%2013'),
(30, 7, 2, 2199000, 'https://www.tokopedia.com/search?q=xiaomi%20redmi%20note%2013'),
(31, 7, 3, 2150000, 'https://www.lazada.co.id/catalog/?q=xiaomi%20redmi%20note%2013'),
(32, 7, 4, 2299000, 'https://www.blibli.com/jual/xiaomi-redmi-note-13'),
(33, 8, 1, 299000, 'https://shopee.co.id/search?keyword=tas%20ransel%2040l'),
(34, 8, 2, 350000, 'https://www.tokopedia.com/search?q=tas%20ransel%2040l'),
(35, 8, 3, 320000, 'https://www.lazada.co.id/catalog/?q=tas%20ransel%2040l'),
(36, 8, 4, 375000, 'https://www.blibli.com/jual/tas-ransel-40l'),
(37, 9, 1, 8799000, 'https://shopee.co.id/search?keyword=ps5%20slim'),
(38, 9, 2, 8999000, 'https://www.tokopedia.com/search?q=ps5%20slim'),
(39, 9, 3, 8850000, 'https://www.lazada.co.id/catalog/?q=ps5%20slim'),
(40, 9, 4, 9100000, 'https://www.blibli.com/jual/ps5-slim');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `remember_token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `nama_lengkap`, `email`, `password`, `created_at`, `remember_token`) VALUES
(2, 'admin', 'Admin', 'admin@test.com', '$2a$10$HjUiAKNfIGZTdzDfNPhk5OAqqvtNErB6k4YuUpir7x6wvM/62PamS', '2026-03-25 14:14:20', NULL),
(3, 'rayfalakbar', 'rayfal akbar keren', 'rayfalmayvand@gmail.com', '$2y$10$MUqF3xAWEU2cDfXStf1im.nzQEVbWkjKMEUoUjROsHvmcOwJZqSWy', '2026-04-07 14:41:32', NULL),
(4, 'david', 'david adda', 'davidadalah@gmail.com', '$2y$10$jV7Q7QfYXs.f.DDRWw2cBupAqyX/NBgpecvm4iRXQSHJxaXvCH.uK', '2026-04-07 14:45:33', NULL),
(5, 'repal', 'repalgemoy', 'repal@gmail.com', '$2y$10$X5ZbW.4CQ1GF5AAfntn2S.1HYU2NpuJ2qYgdg/27ncWgMd6SozJbK', '2026-04-08 06:36:33', NULL),
(6, 'lutfi', 'lutfi kyut', 'lutfifirman@gmail.com', '$2y$10$N76hPGnq8g4/cRzMO7Mwv.kCVUXOFm8w9bO0D/fqT/lEWu9CLBAFO', '2026-04-08 10:48:06', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `platforms`
--
ALTER TABLE `platforms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_prices`
--
ALTER TABLE `product_prices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_product_platform` (`product_id`,`platform_id`),
  ADD KEY `platform_id` (`platform_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=109;

--
-- AUTO_INCREMENT for table `platforms`
--
ALTER TABLE `platforms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `product_prices`
--
ALTER TABLE `product_prices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `product_prices`
--
ALTER TABLE `product_prices`
  ADD CONSTRAINT `product_prices_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `product_prices_ibfk_2` FOREIGN KEY (`platform_id`) REFERENCES `platforms` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
