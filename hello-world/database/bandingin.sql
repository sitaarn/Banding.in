-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 12, 2026 at 02:56 AM
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
  `product_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(4, 'Meja Belajar Minimalis Kayu', 'meja_belajar.jpg', 'Furniture');

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
(5, 1, 1, 12499000, 'https://shopee.co.id/search?keyword=iphone%2015%20128gb'),
(6, 1, 2, 12999000, 'https://www.tokopedia.com/search?q=iphone%2015%20128gb'),
(7, 1, 3, 12899000, 'https://www.lazada.co.id/catalog/?q=iphone%2015%20128gb'),
(8, 1, 4, 13100000, 'https://www.blibli.com/jual/iphone-15-128gb'),
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
(20, 4, 4, 460000, 'https://www.blibli.com/jual/meja-belajar-minimalis');

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
  ADD KEY `product_id` (`product_id`),
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `platforms`
--
ALTER TABLE `platforms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `product_prices`
--
ALTER TABLE `product_prices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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

-- =====================================================
-- TABEL: users
-- Untuk sistem autentikasi
-- =====================================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    foto_profil VARCHAR(255) DEFAULT NULL,
    remember_token VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- =====================================================
-- DATA AWAL: Admin User
-- Password: admin123 (sudah di-hash)
-- =====================================================
INSERT INTO users (username, email, password, nama_lengkap, role) VALUES
('admin', 'admin@untidar.ac.id', '$2y$10$KT.xkhTGfM6SfQ4paoZ0LubpGPZrRjck.EnxCi2Y5NF.czUu7vChq', 'Administrator', 'admin');

