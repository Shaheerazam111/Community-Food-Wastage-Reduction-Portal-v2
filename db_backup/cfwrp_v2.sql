-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 17, 2026 at 10:37 PM
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
(1, 'Vegetarian', 1, '2026-10-17 17:01:16', '2026-10-17 17:01:16'),
(2, 'Non-vegetarian', 1, '2026-10-17 17:01:16', '2026-10-17 17:01:16');

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
(8, 25, '[Demo] 20 veg lunch boxes', 1, 'Fresh vegetarian lunch packs from today service.', 20.00, 'servings', NULL, '12 Food Street, Gulberg, Lahore', 'Lahore', 31.5120000, 74.3450000, '2026-10-18 08:20:41', 'AVAILABLE', NULL, NULL, NULL, '2026-10-17 17:20:41', '2026-10-17 17:20:41'),
(9, 25, '[Demo] 15 kg cooked rice', 1, 'Plain rice, kept warm. Bring containers.', 15.00, 'kg', NULL, '12 Food Street, Gulberg, Lahore', 'Lahore', 31.5095000, 74.3410000, '2026-10-18 04:20:41', 'AVAILABLE', NULL, NULL, NULL, '2026-10-17 17:20:41', '2026-10-17 17:20:41'),
(10, 25, '[Demo] 40 chicken biryani servings', 2, 'Non-veg biryani trays. Pickup before expiry.', 40.00, 'servings', NULL, 'Gulberg III, Lahore', 'Lahore', 31.5155000, 74.3520000, '2026-10-18 06:20:41', 'AVAILABLE', NULL, NULL, NULL, '2026-10-17 17:20:41', '2026-10-17 17:20:41'),
(11, 25, '[Demo] Cancelled listing (hidden)', 1, 'Should not appear in NGO search.', 5.00, 'servings', NULL, 'Gulberg, Lahore', 'Lahore', 31.5120000, 74.3450000, '2026-10-18 22:20:41', 'CANCELLED', NULL, NULL, NULL, '2026-10-17 17:20:41', '2026-10-17 17:20:41'),
(12, 25, '[Demo] Already claimed (hidden)', 1, 'Claimed by Demo NGO — for claims history.', 25.00, 'servings', NULL, 'Model Town, Lahore', 'Lahore', 31.4810000, 74.3250000, '2026-10-18 10:20:41', 'CLAIMED', 26, '2026-10-17 20:20:41', NULL, '2026-10-17 17:20:41', '2026-10-17 17:20:41'),
(13, 25, '[Demo] Delivered last week', 1, 'Finished pickup — donor stats only.', 10.00, 'kg', NULL, 'Gulberg, Lahore', 'Lahore', 31.5120000, 74.3450000, '2026-10-16 22:20:41', 'DELIVERED', 26, '2026-10-14 22:20:41', '2026-10-15 00:20:41', '2026-10-17 17:20:41', '2026-10-17 17:20:41'),
(14, 27, '[Demo] Karachi fish curry 12 kg', 2, 'Karachi-only listing for city filter test.', 12.00, 'kg', NULL, 'Clifton, Karachi', 'Karachi', 24.8138000, 67.0299000, '2026-10-18 07:20:41', 'AVAILABLE', NULL, NULL, NULL, '2026-10-17 17:20:41', '2026-10-17 17:20:41'),
(15, 27, '[Demo] Karachi veg daal 30 servings', 1, 'Another Karachi listing.', 30.00, 'servings', NULL, 'DHA Phase 6, Karachi', 'Karachi', 24.8050000, 67.0750000, '2026-10-18 05:20:41', 'AVAILABLE', NULL, NULL, NULL, '2026-10-17 17:20:41', '2026-10-17 17:20:41'),
(16, 25, '2 Shawarma', 2, 'testing notifications', 2.00, 'servings', 'food-listings/25/1EXExRIhvVBbk4OV7kEzxXUofnNWTXCg50iXAocZ.png', '12 Food Street, Gulberg', 'Lahore', 31.5120000, 74.3450000, '2026-10-19 03:35:00', 'CLAIMED', 26, '2026-10-17 22:36:13', NULL, '2026-10-17 17:35:40', '2026-10-17 17:36:13');

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
(8, '2026_10_18_000001_step1_users_and_listings_foundation', 1),
(9, '2026_10_18_000002_drop_food_table', 1),
(10, '2026_10_18_100000_create_verification_documents_table', 1),
(11, '2026_10_18_110000_step3_food_categories_and_listing_fields', 1),
(12, '2026_10_18_120000_create_notifications_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `food_listing_id` bigint(20) UNSIGNED DEFAULT NULL,
  `message` varchar(500) NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `food_listing_id`, `message`, `is_read`, `created_at`, `updated_at`) VALUES
(13, 26, 16, 'New food in Lahore: 2 Shawarma.', 0, '2026-10-17 17:35:40', '2026-10-17 17:35:40'),
(14, 25, 16, 'City Food Relief claimed 2 Shawarma.', 0, '2026-10-17 17:36:13', '2026-10-17 17:36:13');

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
(24, 'Portal Admin', 'admin@food.test', 'admin', NULL, '$2y$10$pClo.2DOsb8rDPuvrDnvWOIhzG6vwum9OPLVvaN9qbrV9qSdmJJqi', NULL, '2026-10-17 17:20:41', '2026-10-17 17:20:41', NULL, '03000000001', NULL, NULL, 'Admin office, Mall Road', 'Lahore', 31.5204000, 74.3587000, 1, 1),
(25, 'Demo Donor', 'donor@food.test', 'donor', NULL, '$2y$10$pClo.2DOsb8rDPuvrDnvWOIhzG6vwum9OPLVvaN9qbrV9qSdmJJqi', NULL, '2026-10-17 17:20:41', '2026-10-17 17:20:41', 'Green Kitchen Restaurant', '03000000002', NULL, NULL, '12 Food Street, Gulberg', 'Lahore', 31.5120000, 74.3450000, 1, 1),
(26, 'Demo NGO', 'ngo@food.test', 'recipient', NULL, '$2y$10$pClo.2DOsb8rDPuvrDnvWOIhzG6vwum9OPLVvaN9qbrV9qSdmJJqi', NULL, '2026-10-17 17:20:41', '2026-10-17 17:20:41', NULL, '03000000003', 'City Food Relief', 'Sara Khan', '45 Hope Avenue, Model Town', 'Lahore', 31.4850000, 74.3200000, 1, 1),
(27, 'Karachi Kitchen', 'donor.khi@food.test', 'donor', NULL, '$2y$10$RR21FUquJgncagoGfFcEaOJ5vMJf13Dr69V66Y/Plhd9qiVKDURqi', NULL, '2026-10-17 17:20:41', '2026-10-17 17:20:41', 'Harbor Kitchen Karachi', '03000000004', NULL, NULL, 'Clifton Block 5', 'Karachi', 24.8138000, 67.0299000, 1, 1),
(28, 'Pending NGO', 'ngo.pending@food.test', 'recipient', NULL, '$2y$10$RR21FUquJgncagoGfFcEaOJ5vMJf13Dr69V66Y/Plhd9qiVKDURqi', NULL, '2026-10-17 17:20:41', '2026-10-17 17:20:41', NULL, '03000000005', 'Waiting Relief', 'Ali Raza', 'Johar Town', 'Lahore', 31.4697000, 74.2728000, 0, 1);

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
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_food_listing_id_foreign` (`food_listing_id`),
  ADD KEY `notifications_user_id_is_read_created_at_index` (`user_id`,`is_read`,`created_at`);

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `verification_documents`
--
ALTER TABLE `verification_documents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

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
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_food_listing_id_foreign` FOREIGN KEY (`food_listing_id`) REFERENCES `food_listings` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `verification_documents`
--
ALTER TABLE `verification_documents`
  ADD CONSTRAINT `verification_documents_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
