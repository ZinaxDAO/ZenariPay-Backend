-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 22, 2022 at 01:25 AM
-- Server version: 10.4.19-MariaDB
-- PHP Version: 7.4.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `zinapay`
--

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2022_11_19_195707_create_notifications_table', 1),
(6, '2022_11_19_200515_create_products_table', 1),
(7, '2022_11_19_201828_create_payment_link_buttons_table', 1),
(8, '2022_11_19_212404_create_wallet_addresses_table', 1),
(9, '2022_11_20_084150_create_orders_table', 1),
(10, '2022_11_20_145151_create_web_settings_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint(20) UNSIGNED NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('failed','pending','completed','processing','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `coin` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cryptoAmount` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `currency` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fiatAmount` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `feeInCrypto` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customerName` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customerEmail` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_link_buttons`
--

CREATE TABLE `payment_link_buttons` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `link_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `link_description` varchar(2000) COLLATE utf8mb4_unicode_ci NOT NULL,
  `link_type` enum('donation','standard') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'standard',
  `phone_number` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '0: not required, 1: required',
  `shipping_address` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '0: not required, 1: required',
  `redirect_website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_success_msg` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment_link_buttons`
--

INSERT INTO `payment_link_buttons` (`id`, `user_id`, `product_id`, `link_title`, `slug`, `link_description`, `link_type`, `phone_number`, `shipping_address`, `redirect_website`, `payment_success_msg`, `created_at`, `updated_at`, `deleted_at`) VALUES
(2, 3, 1, 'What is Lorem Ipsum', 'what-is-lorem-ipsumhfDd', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.', 'standard', '1', '0', 'https://zeenah.app', 'Payment received successfully', '2022-11-21 23:02:05', '2022-11-21 23:02:05', NULL),
(3, 3, 1, 'What is Lorem Ipsum', 'what-is-lorem-ipsum5qRu', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.', 'standard', '1', '0', 'https://zeenah.app', 'Payment received successfully', '2022-11-21 23:05:49', '2022-11-21 23:05:49', NULL),
(4, 3, 1, 'What is Product bags', 'what-is-product-bags0E60', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.', 'standard', '1', '0', 'https://zeenah.app', 'Payment received successfully', '2022-11-21 23:06:34', '2022-11-21 23:06:34', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\User', 2, 'API TOKEN', '859230eb0989bdd1219b30ec307cefbcb1fc89543a81659077fd8fff6e126b4b', '[\"*\"]', '2022-11-20 22:55:28', '2022-11-20 22:53:17', '2022-11-20 22:55:28'),
(2, 'App\\Models\\User', 3, 'API TOKEN', 'b54381ee29561e90bbea50017851dd389608702c6f4bec9c28b54a9ed6b8cddd', '[\"*\"]', '2022-11-21 23:14:12', '2022-11-20 22:57:43', '2022-11-21 23:14:12');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_currency` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `product_price` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`product_price`)),
  `product_social` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`product_social`)),
  `product_description` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `success_redirect` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '0 : No show success page, 1: Redirection to URL in product_website',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `user_id`, `product_name`, `product_currency`, `product_price`, `product_social`, `product_description`, `product_image`, `success_redirect`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 3, 'Joromy', 'USD', '50', NULL, 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.', 'http://127.0.0.1:8000/storage/app/public/products/ce0d6aaaf41ccfb6b899a491773a09f24b7312ec.jpg', '0', '2022-11-21 23:01:55', '2022-11-21 23:18:44', '2022-11-21 23:18:44'),
(2, 3, 'Joromy', 'USD', '50', NULL, 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.', 'http://127.0.0.1:8000/storage/app/public/products/c7b59e9289be3dbf7a0e9e18478748363742c1f5.jpg', '0', '2022-11-21 23:10:26', '2022-11-21 23:10:26', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `businessName` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `businessEmail` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `businessLogo` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `api_token` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `businessName`, `businessEmail`, `businessLogo`, `api_token`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(3, 'Woju Technologies', 'test@gmail.com', NULL, '2|oXnvqeDkQn6blH33PZ3R7iriKdWR5Vd9UphZx6tB', 'test@gmail.com', NULL, '$2y$10$Ll0Y9lbRQEVSBZPQxpCOIeoOjkQMzGIo6a7OmWvywxh7z80Tuwt/2', NULL, '2022-11-20 22:57:43', '2022-11-20 22:57:43');

-- --------------------------------------------------------

--
-- Table structure for table `wallet_addresses`
--

CREATE TABLE `wallet_addresses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `currency` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `wallet_address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_use` int(11) NOT NULL,
  `other` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`other`)),
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `last_used` timestamp NOT NULL DEFAULT '2022-11-20 21:29:10',
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wallet_addresses`
--

INSERT INTO `wallet_addresses` (`id`, `currency`, `wallet_address`, `total_use`, `other`, `created_at`, `updated_at`, `last_used`, `deleted_at`) VALUES
(3, 'BUSD', '0xF845f557b16F2399b9807129f40773F5c804fcf8', 0, '{\r\n\"id\":1\r\n}', '2022-11-20 23:18:02', '2022-11-20 23:18:05', '2022-11-20 21:29:10', NULL),
(4, 'BTC', 'n2FeFoEMMrmLty7KhU47VwE12qcn5GBAMn', 11, '{\"status\":\"success\",\"data\":{\"uid\":\"f162e0f8-be1d-407f-b4ab-69fcedb55298\",\"address\":\"n2FeFoEMMrmLty7KhU47VwE12qcn5GBAMn\",\"addressRef\":\"LDFmXifVwN\",\"autoForwardAddress\":null,\"network\":\"TESTNET\",\"assetType\":\"BTC\",\"addressType\":\"legacy\",\"isContract\":false,\"isChangeAddress\":false,\"derivationIndex\":5,\"guid\":\"ec9249bc-0c30-4325-919e-08924dfe92d5\",\"chain\":\"BITCOIN\",\"assetId\":\"5a108aa3-c76b-4531-8c43-51b6136285fb\",\"organizationId\":\"486d9918-1496-4574-b968-8059d8c3393d\",\"subAccountId\":null,\"used\":false,\"addressContractIdentifier\":null,\"deploymentParams\":null,\"lastUsedAt\":null,\"createdAt\":\"2022-11-21T15:25:25.442Z\"}}', '2022-11-21 14:25:25', '2022-11-21 21:26:00', '2022-11-21 21:26:00', NULL),
(5, 'BTC', 'mx7WRrWMg7R366uGKPmCXx41kA4XvBRbUP', 0, '{\"status\":\"success\",\"data\":{\"uid\":\"017d9936-f628-4be8-8608-2f290ae6492b\",\"address\":\"mx7WRrWMg7R366uGKPmCXx41kA4XvBRbUP\",\"addressRef\":\"4oW6MZxFgv\",\"autoForwardAddress\":null,\"network\":\"TESTNET\",\"assetType\":\"BTC\",\"addressType\":\"legacy\",\"isContract\":false,\"isChangeAddress\":false,\"derivationIndex\":6,\"guid\":\"5a839872-2763-4d12-9cc8-c8247f273ca5\",\"chain\":\"BITCOIN\",\"assetId\":\"5a108aa3-c76b-4531-8c43-51b6136285fb\",\"organizationId\":\"486d9918-1496-4574-b968-8059d8c3393d\",\"subAccountId\":null,\"used\":false,\"addressContractIdentifier\":null,\"deploymentParams\":null,\"lastUsedAt\":null,\"createdAt\":\"2022-11-21T23:36:01.625Z\"}}', '2022-11-21 22:36:00', '2022-11-21 22:36:00', '2022-11-20 21:29:10', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `web_settings`
--

CREATE TABLE `web_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `web_settings`
--

INSERT INTO `web_settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES
(1, 'website_title', 'ZinariPay', '2022-11-21 00:06:57', '2022-11-21 00:06:57'),
(2, 'website_logo', 'default.png', '2022-11-21 00:06:57', '2022-11-21 00:06:57'),
(3, 'gas_fee', '0.50', '2022-11-21 00:07:43', '2022-11-21 00:07:43');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orders_user_id_foreign` (`user_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `payment_link_buttons`
--
ALTER TABLE `payment_link_buttons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payment_link_buttons_user_id_foreign` (`user_id`),
  ADD KEY `payment_link_buttons_product_id_foreign` (`product_id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `wallet_addresses`
--
ALTER TABLE `wallet_addresses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `web_settings`
--
ALTER TABLE `web_settings`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_link_buttons`
--
ALTER TABLE `payment_link_buttons`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `wallet_addresses`
--
ALTER TABLE `wallet_addresses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `web_settings`
--
ALTER TABLE `web_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `payment_link_buttons`
--
ALTER TABLE `payment_link_buttons`
  ADD CONSTRAINT `payment_link_buttons_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `payment_link_buttons_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
