-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 17, 2026 at 09:44 PM
-- Server version: 10.11.14-MariaDB-0ubuntu0.24.04.1
-- PHP Version: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cfwrp_v2`
--

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `food_categories`
--

CREATE TABLE `food_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `food_categories`
--

INSERT INTO `food_categories` (`id`, `name`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Vegetarian', 1, '2026-10-17 16:37:52', '2026-10-17 16:37:52'),
(2, 'Non-vegetarian', 1, '2026-10-17 16:37:52', '2026-10-17 16:37:52');

-- --------------------------------------------------------

--
-- Table structure for table `food_listings`
--

CREATE TABLE `food_listings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `donor_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `description` text DEFAULT NULL,
  `quantity_value` decimal(10,2) DEFAULT NULL,
  `quantity_unit` enum('kg','servings') DEFAULT NULL,
  `photo_path` varchar(255) DEFAULT NULL,
  `pickup_address` varchar(255) NOT NULL,
  `city` varchar(100) NOT NULL DEFAULT 'Lahore',
  `latitude` decimal(10,7) NOT NULL,
  `longitude` decimal(10,7) NOT NULL,
  `expiry_at` datetime NOT NULL,
  `status` enum('AVAILABLE','CLAIMED','DELIVERED','EXPIRED','CANCELLED') NOT NULL DEFAULT 'AVAILABLE',
  `claimed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `claimed_at` datetime DEFAULT NULL,
  `delivered_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `food_listings`
--

INSERT INTO `food_listings` (`id`, `donor_id`, `title`, `category_id`, `description`, `quantity_value`, `quantity_unit`, `photo_path`, `pickup_address`, `city`, `latitude`, `longitude`, `expiry_at`, `status`, `claimed_by`, `claimed_at`, `delivered_at`, `created_at`, `updated_at`) VALUES
(1, 1, '20 Alo gobi box', 1, NULL, 1.00, 'servings', NULL, 'House # 3, Street # 1, Nazir Colony, Kot Fareed Road, Sargodha', 'Lahore', 32.0788650, 72.6809580, '2026-10-20 02:00:00', 'CLAIMED', 2, '2026-10-17 20:11:22', NULL, '2026-10-17 15:07:41', '2026-10-17 15:11:22'),
(2, 1, '10 Daal Chawal', 1, NULL, 1.00, 'servings', NULL, 'House # 3, Street # 1, Nazir Colony, Kot Fareed Road, Sargodha', 'Lahore', 32.0788650, 72.6809580, '2026-10-19 16:00:00', 'CLAIMED', 8, '2026-10-17 21:33:49', NULL, '2026-10-17 15:08:05', '2026-10-17 16:33:49'),
(3, 4, 'Tinker test', 1, NULL, 5.00, 'kg', NULL, '12 Food Street, Gulberg', 'Lahore', 31.5120000, 74.3450000, '2026-10-18 21:37:59', 'CANCELLED', NULL, NULL, NULL, '2026-10-17 16:37:59', '2026-10-17 16:37:59');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
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
(5, '2026_08_19_061054_add_role_and_profile_fields_to_users_table', 1),
(6, '2026_08_19_061101_create_food_listings_table', 1),
(7, '2026_09_07_152522_create_food_table', 1),
(8, '2026_10_18_000001_step1_users_and_listings_foundation', 2),
(9, '2026_10_18_000002_drop_food_table', 2),
(10, '2026_10_18_100000_create_verification_documents_table', 3),
(11, '2026_10_18_110000_step3_food_categories_and_listing_fields', 4);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` enum('donor','recipient','admin') NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `business_name` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `ngo_name` varchar(255) DEFAULT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(100) NOT NULL DEFAULT 'Lahore',
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `role`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `business_name`, `phone`, `ngo_name`, `contact_person`, `address`, `city`, `latitude`, `longitude`, `is_verified`, `is_active`) VALUES
(1, 'Donor 1', 'donor1@gmail.com', 'donor', NULL, '$2y$10$4ViY9T2aJ5iqPeDl9hUZa.FcQKjzwg.okl3iq0dAJ/16jKv9yrja2', NULL, '2026-10-17 15:06:47', '2026-10-17 15:06:47', 'Donor 1 Pizza', '03213240681', NULL, NULL, 'House # 3, Street # 1, Nazir Colony, Kot Fareed Road, Sargodha', 'Lahore', 32.0788650, 72.6809580, 0, 1),
(2, 'NGO 1', 'ngo1@gmail.com', 'recipient', NULL, '$2y$10$7Vv4MYNFzZ82Q20.uFQh2O/GE5ijF7adO1o/oBLsE7AmCjl0Kqc0q', NULL, '2026-10-17 15:09:32', '2026-10-17 15:09:32', NULL, '03213240681', 'Care NGO', 'Ali Khan', 'House # 3, Street # 1, Nazir Colony, Kot Fareed Road, Sargodha', 'Lahore', 32.0769430, 72.6782260, 0, 1),
(3, 'Portal Admin', 'admin@food.test', 'admin', NULL, '$2y$10$xSVZnZ/4jvgbVEGh1i4u1O/VVd5PhFmQrtUqr/4atLkMWIrZKAjTS', NULL, '2026-10-17 16:28:31', '2026-10-17 16:30:22', NULL, '03000000001', NULL, NULL, 'Admin office, Mall Road', 'Lahore', 31.5204000, 74.3587000, 1, 1),
(4, 'Demo Donor', 'donor@food.test', 'donor', NULL, '$2y$10$xSVZnZ/4jvgbVEGh1i4u1O/VVd5PhFmQrtUqr/4atLkMWIrZKAjTS', NULL, '2026-10-17 16:28:31', '2026-10-17 16:44:08', 'Green Kitchen Restaurant', '03000000002', NULL, NULL, '12 Food Street, Gulberg', 'Lahore', 31.5138530, 74.3477640, 0, 1),
(5, 'Demo NGO', 'ngo@food.test', 'recipient', NULL, '$2y$10$xSVZnZ/4jvgbVEGh1i4u1O/VVd5PhFmQrtUqr/4atLkMWIrZKAjTS', NULL, '2026-10-17 16:28:31', '2026-10-17 16:30:22', NULL, '03000000003', 'City Food Relief', 'Sara Khan', '45 Hope Avenue, Model Town', 'Lahore', 31.4850000, 74.3200000, 1, 1),
(6, 'Blocked User', 'blocked@food.test', 'donor', NULL, '$2y$10$73397kTRFHrSf2Yr3fV7suOfY22lOER1UCCyinFj7XZf0KKM0QOaa', NULL, '2026-10-17 16:28:49', '2026-10-17 16:28:49', 'X', NULL, NULL, NULL, 'A', 'Lahore', 1.0000000, 1.0000000, 1, 0),
(8, 'New NGO', 'newngo@food.test', 'recipient', NULL, '$2y$10$t/8X2pRRP9lr1Vzb9r1q1.epuJLKDGjJqX2gvgdnG4PXPxJt48NgC', NULL, '2026-10-17 16:33:45', '2026-10-17 16:33:50', NULL, NULL, 'Test Relief', NULL, 'Addr', 'Lahore', 31.4900000, 74.3200000, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `verification_documents`
--

CREATE TABLE `verification_documents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `original_name` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `admin_note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `verification_documents`
--

INSERT INTO `verification_documents` (`id`, `user_id`, `file_path`, `original_name`, `status`, `admin_note`, `created_at`, `updated_at`) VALUES
(1, 8, 'verification-documents/8/XGcqqlpErzr3JGhpafp5ZMnS8MXmeWUDboW3KZeS.pdf', 'verify.pdf', 'approved', NULL, '2026-10-17 16:33:47', '2026-10-17 16:33:48'),
(2, 4, 'verification-documents/4/AvWkMA1on04emFXDcYCSVtEEy9dvMlXzjs8JnfKU.pdf', 'testgrest.pdf', 'pending', NULL, '2026-10-17 16:36:27', '2026-10-17 16:36:27');

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
-- Indexes for table `food_categories`
--
ALTER TABLE `food_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `food_categories_name_unique` (`name`);

--
-- Indexes for table `food_listings`
--
ALTER TABLE `food_listings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `food_listings_donor_id_foreign` (`donor_id`),
  ADD KEY `food_listings_claimed_by_foreign` (`claimed_by`),
  ADD KEY `food_listings_status_expiry_at_index` (`status`,`expiry_at`),
  ADD KEY `food_listings_category_id_foreign` (`category_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `verification_documents`
--
ALTER TABLE `verification_documents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `verification_documents_user_id_unique` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `food_categories`
--
ALTER TABLE `food_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `food_listings`
--
ALTER TABLE `food_listings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `verification_documents`
--
ALTER TABLE `verification_documents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `food_listings`
--
ALTER TABLE `food_listings`
  ADD CONSTRAINT `food_listings_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `food_categories` (`id`),
  ADD CONSTRAINT `food_listings_claimed_by_foreign` FOREIGN KEY (`claimed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `food_listings_donor_id_foreign` FOREIGN KEY (`donor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `verification_documents`
--
ALTER TABLE `verification_documents`
  ADD CONSTRAINT `verification_documents_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
