-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 12, 2026 at 11:04 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

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
-- Table structure for table `food_listings`
--

CREATE TABLE `food_listings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `donor_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `category` enum('veg','non_veg') NOT NULL,
  `quantity` varchar(255) NOT NULL,
  `photo_path` varchar(255) DEFAULT NULL,
  `pickup_address` varchar(255) NOT NULL,
  `city` varchar(255) DEFAULT NULL,
  `latitude` decimal(10,7) NOT NULL,
  `longitude` decimal(10,7) NOT NULL,
  `expiry_at` datetime NOT NULL,
  `status` enum('AVAILABLE','CLAIMED','DELIVERED','EXPIRED') NOT NULL DEFAULT 'AVAILABLE',
  `moderation_status` varchar(20) NOT NULL DEFAULT 'PENDING',
  `rejection_reason` varchar(255) DEFAULT NULL,
  `claimed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `claimed_at` datetime DEFAULT NULL,
  `delivered_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `moderated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `moderated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `food_listings`
--

INSERT INTO `food_listings` (`id`, `donor_id`, `title`, `category`, `quantity`, `photo_path`, `pickup_address`, `city`, `latitude`, `longitude`, `expiry_at`, `status`, `moderation_status`, `rejection_reason`, `claimed_by`, `claimed_at`, `delivered_at`, `created_at`, `updated_at`, `moderated_by`, `moderated_at`) VALUES
(1, 3, 'Lunch Box', 'veg', '20', NULL, 'MM Alam Road, Lahore', 'Lahore', 31.5497000, 74.3436000, '2026-11-05 12:30:00', 'EXPIRED', 'APPROVED', NULL, NULL, NULL, NULL, '2026-11-03 02:25:36', '2026-11-12 02:53:22', 2, '2026-11-12 02:53:22'),
(2, 3, 'Lunch boxes', 'veg', '20', NULL, 'Modal Town, Lahore', 'Lahore', 31.4936460, 74.3325520, '2026-11-04 16:50:00', 'AVAILABLE', 'APPROVED', NULL, NULL, NULL, NULL, '2026-11-03 02:45:01', '2026-11-03 02:46:44', NULL, NULL),
(3, 3, 'New 12-11 Food listing', 'veg', '20', 'food-photos/KPy4ZbJP5hKlQjPIIsKB9esS0iELJdPDDZAwrbie.jpg', 'Modal Town, Lahore', 'Lahore', 31.4936460, 74.3325520, '2026-11-20 00:50:00', 'CLAIMED', 'APPROVED', NULL, 4, '2026-11-12 05:51:16', NULL, '2026-11-12 00:48:32', '2026-11-12 00:51:16', NULL, NULL),
(4, 15, 'Viva demo lunch 20261112130202', 'veg', '25 meals', NULL, 'MM Alam Road, Gulberg, Lahore', 'Lahore', 31.5497000, 74.3436000, '2026-11-15 13:02:31', 'AVAILABLE', 'APPROVED', NULL, NULL, NULL, NULL, '2026-11-12 03:02:32', '2026-11-12 03:03:06', 2, '2026-11-12 03:02:42'),
(5, 17, 'Viva demo lunch 20261112130336', 'veg', '25 meals', NULL, 'MM Alam Road, Gulberg, Lahore', 'Lahore', 31.5497000, 74.3436000, '2026-11-15 13:04:22', 'DELIVERED', 'APPROVED', NULL, 18, '2026-11-12 08:05:52', '2026-11-12 08:06:16', '2026-11-12 03:04:23', '2026-11-12 03:06:16', 2, '2026-11-12 03:04:42');

-- --------------------------------------------------------

--
-- Table structure for table `listing_feedback`
--

CREATE TABLE `listing_feedback` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `food_listing_id` bigint(20) UNSIGNED NOT NULL,
  `recipient_id` bigint(20) UNSIGNED NOT NULL,
  `rating` tinyint(3) UNSIGNED NOT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(7, '2026_09_07_152522_create_food_table', 2),
(8, '2026_11_02_000001_add_admin_role_to_users_table', 2),
(9, '2026_11_03_000001_drop_legacy_food_table', 3),
(10, '2026_11_03_000002_add_portal_phase2_tables_and_columns', 3),
(11, '2026_11_03_100000_add_verification_and_listing_moderation', 3);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `title`, `body`, `read_at`, `created_at`, `updated_at`) VALUES
(1, 3, 'Your food was claimed', 'City Food Relief NGO claimed \"New 12-11 Food listing\".', '2026-11-12 00:51:32', '2026-11-12 00:51:16', '2026-11-12 00:51:32'),
(2, 4, 'New food nearby', 'Lunch Box is available about 4.4 km away.', NULL, '2026-11-12 02:53:22', '2026-11-12 02:53:22'),
(3, 3, 'Listing approved', '\"Lunch Box\" is now visible to NGOs in search.', NULL, '2026-11-12 02:53:22', '2026-11-12 02:53:22'),
(4, 7, 'NGO verified', 'Your NGO document was approved. You can search and claim food.', NULL, '2026-11-12 02:53:41', '2026-11-12 02:53:41'),
(5, 12, 'NGO verified', 'Your NGO document was approved. You can search and claim food.', NULL, '2026-11-12 02:57:37', '2026-11-12 02:57:37'),
(6, 14, 'NGO verified', 'Your NGO document was approved. You can search and claim food.', NULL, '2026-11-12 03:00:07', '2026-11-12 03:00:07'),
(7, 4, 'New food nearby', 'Viva demo lunch 20261112130202 is available about 4.4 km away.', NULL, '2026-11-12 03:02:42', '2026-11-12 03:02:42'),
(8, 7, 'New food nearby', 'Viva demo lunch 20261112130202 is available about 4.4 km away.', NULL, '2026-11-12 03:02:42', '2026-11-12 03:02:42'),
(9, 12, 'New food nearby', 'Viva demo lunch 20261112130202 is available about 4.4 km away.', NULL, '2026-11-12 03:02:42', '2026-11-12 03:02:42'),
(10, 14, 'New food nearby', 'Viva demo lunch 20261112130202 is available about 4.4 km away.', NULL, '2026-11-12 03:02:42', '2026-11-12 03:02:42'),
(11, 15, 'Listing approved', '\"Viva demo lunch 20261112130202\" is now visible to NGOs in search.', NULL, '2026-11-12 03:02:42', '2026-11-12 03:02:42'),
(12, 16, 'NGO verified', 'Your NGO document was approved. You can search and claim food.', NULL, '2026-11-12 03:03:02', '2026-11-12 03:03:02'),
(13, 4, 'New food nearby', 'Viva demo lunch 20261112130336 is available about 4.4 km away.', NULL, '2026-11-12 03:04:42', '2026-11-12 03:04:42'),
(14, 7, 'New food nearby', 'Viva demo lunch 20261112130336 is available about 4.4 km away.', NULL, '2026-11-12 03:04:42', '2026-11-12 03:04:42'),
(15, 12, 'New food nearby', 'Viva demo lunch 20261112130336 is available about 4.4 km away.', NULL, '2026-11-12 03:04:42', '2026-11-12 03:04:42'),
(16, 14, 'New food nearby', 'Viva demo lunch 20261112130336 is available about 4.4 km away.', NULL, '2026-11-12 03:04:42', '2026-11-12 03:04:42'),
(17, 16, 'New food nearby', 'Viva demo lunch 20261112130336 is available about 4.4 km away.', NULL, '2026-11-12 03:04:42', '2026-11-12 03:04:42'),
(18, 17, 'Listing approved', '\"Viva demo lunch 20261112130336\" is now visible to NGOs in search.', NULL, '2026-11-12 03:04:42', '2026-11-12 03:04:42'),
(19, 18, 'NGO verified', 'Your NGO document was approved. You can search and claim food.', NULL, '2026-11-12 03:05:26', '2026-11-12 03:05:26'),
(20, 17, 'Your food was claimed', 'Viva Relief NGO claimed \"Viva demo lunch 20261112130336\".', NULL, '2026-11-12 03:05:52', '2026-11-12 03:05:52'),
(21, 18, 'Pickup marked delivered', 'The donor marked \"Viva demo lunch 20261112130336\" as delivered. You can leave feedback under My claims.', NULL, '2026-11-12 03:06:16', '2026-11-12 03:06:16');

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
  `role` enum('donor','recipient','admin','volunteer') NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `verification_status` varchar(20) NOT NULL DEFAULT 'approved',
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
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `role`, `is_active`, `verification_status`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `business_name`, `phone`, `ngo_name`, `contact_person`, `address`, `latitude`, `longitude`) VALUES
(1, 'Pickup Volunteer', 'volunteer@foodportal.test', 'volunteer', 1, 'approved', NULL, '$2y$10$bcuRdf8B4ORY6akY/lOQq.OBua6Y3ZOWeYysXBVwwwppI17ZrDHHi', NULL, '2026-11-03 01:32:36', '2026-11-12 02:12:07', NULL, NULL, NULL, NULL, 'City pickup hub', 31.5204000, 74.3587000),
(2, 'Portal Admin', 'admin@foodportal.test', 'admin', 1, 'approved', NULL, '$2y$10$R7TUS/IoAH038lW.eowKFuwxdhBxMepDiZMAJovQoE/ecLQaNwUgq', NULL, '2026-11-03 01:35:10', '2026-11-12 02:12:07', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'Demo Donor', 'donor@foodportal.test', 'donor', 1, 'approved', NULL, '$2y$10$eJONMpevGHo6NPEXXCUHreS87oLcmE39kq06FGfemSfQb75HNK9pi', NULL, '2026-11-03 02:22:18', '2026-11-12 02:12:07', 'Green Kitchen Cafe', '03001234567', NULL, NULL, 'MM Alam Road, Lahore', 31.5497000, 74.3436000),
(4, 'City Food Relief', 'ngo@foodportal.test', 'recipient', 1, 'approved', NULL, '$2y$10$iiyVIhJqgQ2TKVb5vcm7eemassApdL6VrrkMWYlnCd1Z5mQBo.XJO', NULL, '2026-11-03 02:22:18', '2026-11-12 02:12:07', NULL, NULL, 'City Food Relief NGO', 'Ayesha Khan', 'Model Town, Lahore', 31.5100000, 74.3400000),
(5, 'Viva Demo Donor', 'viva-donor-20261112125157@demo.test', 'donor', 1, 'approved', NULL, '$2y$10$9YtgysyLHfJfduTmAli.Q.j6agYOWOOeXmx8J2m4axl.Y0Ii5Uaaa', NULL, '2026-11-12 02:52:10', '2026-11-12 02:52:10', 'Viva Kitchen Lahore', '03001112233', NULL, NULL, 'MM Alam Road, Gulberg, Lahore', 31.5497000, 74.3436000),
(6, 'Viva Demo Donor', 'viva-donor-20261112125252@demo.test', 'donor', 1, 'approved', NULL, '$2y$10$t2qXqim2efzlwKaylTyJc.5lO5pQ358WrT1T1FMjkuP6IvQMAu/Ju', NULL, '2026-11-12 02:53:06', '2026-11-12 02:53:06', 'Viva Kitchen Lahore', '03001112233', NULL, NULL, 'MM Alam Road, Gulberg, Lahore', 31.5497000, 74.3436000),
(7, 'Viva Demo NGO', 'viva-ngo-20261112125252@demo.test', 'recipient', 1, 'approved', NULL, '$2y$10$2EILu.t.26oeq/O6KOi5zeoXzMyjw01pcrQbv.XRvmKQhC9LdZZ3C', NULL, '2026-11-12 02:53:31', '2026-11-12 02:53:41', NULL, '03009998877', 'Viva Relief NGO', 'Ali Khan', 'Model Town, Lahore', 31.5100000, 74.3400000),
(8, 'Viva Demo Donor', 'viva-donor-20261112125406@demo.test', 'donor', 1, 'approved', NULL, '$2y$10$kL2MtDq/JlHVdFiHwqYStuQ7BrIVeuv4gluJ7ZoPsY6OjbEaBRvcW', NULL, '2026-11-12 02:54:19', '2026-11-12 02:54:19', 'Viva Kitchen Lahore', '03001112233', NULL, NULL, 'MM Alam Road, Gulberg, Lahore', 31.5497000, 74.3436000),
(9, 'Viva Demo Donor', 'viva-donor-20261112125447@demo.test', 'donor', 1, 'approved', NULL, '$2y$10$peFI8axP5Zv1U4MJlvfHzeNJdGERZw/mtbXVQOHYEVUqIc8i0Xevi', NULL, '2026-11-12 02:54:59', '2026-11-12 02:54:59', 'Viva Kitchen Lahore', '03001112233', NULL, NULL, 'MM Alam Road, Gulberg, Lahore', 31.5497000, 74.3436000),
(10, 'Viva Demo Donor', 'viva-donor-20261112125543@demo.test', 'donor', 1, 'approved', NULL, '$2y$10$nm08ft/2/8C/AV.Dzt3J0uCfAA144hw.LPsJ2zbxZHuVnz38j/lBi', NULL, '2026-11-12 02:55:55', '2026-11-12 02:55:55', 'Viva Kitchen Lahore', '03001112233', NULL, NULL, 'MM Alam Road, Gulberg, Lahore', 31.5497000, 74.3436000),
(11, 'Viva Demo Donor', 'viva-donor-20261112125642@demo.test', 'donor', 1, 'approved', NULL, '$2y$10$ojm2q6wmn3T5wLnKmZdAYeMezAlHkSpU9PqHaRmOgCp.q6uNFEhve', NULL, '2026-11-12 02:56:54', '2026-11-12 02:56:54', 'Viva Kitchen Lahore', '03001112233', NULL, NULL, 'MM Alam Road, Gulberg, Lahore', 31.5497000, 74.3436000),
(12, 'Viva Demo NGO', 'viva-ngo-20261112125642@demo.test', 'recipient', 1, 'approved', NULL, '$2y$10$xr8rRpYTzROGenigkVl7/edO/nejSz9AZV2Jjv5ipBocFnDF/E0Wq', NULL, '2026-11-12 02:57:26', '2026-11-12 02:57:37', NULL, '03009998877', 'Viva Relief NGO', 'Ali Khan', 'Model Town, Lahore', 31.5100000, 74.3400000),
(13, 'Viva Demo Donor', 'viva-donor-20261112125910@demo.test', 'donor', 1, 'approved', NULL, '$2y$10$1tSyR/fhgHNx5XqavlPi0eF9T1m35BRiS8/7OggxwKowaUsbBS9hG', NULL, '2026-11-12 02:59:24', '2026-11-12 02:59:24', 'Viva Kitchen Lahore', '03001112233', NULL, NULL, 'MM Alam Road, Gulberg, Lahore', 31.5497000, 74.3436000),
(14, 'Viva Demo NGO', 'viva-ngo-20261112125910@demo.test', 'recipient', 1, 'approved', NULL, '$2y$10$vG5E8KStgl6XHWb8OBD1e.r9NGRNWgpD6PqfE2185oz9NxfjXyzUu', NULL, '2026-11-12 02:59:55', '2026-11-12 03:00:11', NULL, '03009998877', 'Viva Relief NGO', 'Ali Khan', 'Model Town, Lahore', 31.5100000, 74.3400000),
(15, 'Viva Demo Donor', 'viva-donor-20261112130202@demo.test', 'donor', 1, 'approved', NULL, '$2y$10$EySKkluBj8KN48T7giY0kOzeiBkyspfHlHk6urm9bUi82XEVbWxwq', NULL, '2026-11-12 03:02:13', '2026-11-12 03:02:13', 'Viva Kitchen Lahore', '03001112233', NULL, NULL, 'MM Alam Road, Gulberg, Lahore', 31.5497000, 74.3436000),
(16, 'Viva Demo NGO', 'viva-ngo-20261112130202@demo.test', 'recipient', 1, 'approved', NULL, '$2y$10$MFxvwlx2MghfVEqvgB1.fOb0jihT3S5TZsb6RdZZSvlrA7s.uP3du', NULL, '2026-11-12 03:02:53', '2026-11-12 03:03:07', NULL, '03009998877', 'Viva Relief NGO', 'Ali Khan', 'Model Town, Lahore', 31.5100000, 74.3400000),
(17, 'Viva Demo Donor', 'viva-donor-20261112130336@demo.test', 'donor', 1, 'approved', NULL, '$2y$10$8l7DQGP4TjDxJjLhl9xaQOFHP/56HJlrKqrZmSTmMiwY/P6Go8Lyy', NULL, '2026-11-12 03:03:57', '2026-11-12 03:03:57', 'Viva Kitchen Lahore', '03001112233', NULL, NULL, 'MM Alam Road, Gulberg, Lahore', 31.5497000, 74.3436000),
(18, 'Viva Demo NGO', 'viva-ngo-20261112130336@demo.test', 'recipient', 1, 'approved', NULL, '$2y$10$B3XCv8YTs40QfcSVu8DbieUpU5AK8qJ7QJd7YLdgaUXos6W.T53/6', NULL, '2026-11-12 03:05:03', '2026-11-12 03:05:35', NULL, '03009998877', 'Viva Relief NGO', 'Ali Khan', 'Model Town, Lahore', 31.5100000, 74.3400000);

-- --------------------------------------------------------

--
-- Table structure for table `verification_documents`
--

CREATE TABLE `verification_documents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `original_name` varchar(255) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `admin_note` text DEFAULT NULL,
  `reviewed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `verification_documents`
--

INSERT INTO `verification_documents` (`id`, `user_id`, `file_path`, `original_name`, `status`, `admin_note`, `reviewed_by`, `reviewed_at`, `created_at`, `updated_at`) VALUES
(1, 7, 'ngo-documents/HaIRDtBHoYnBvwZKMhN1AdSQQJcyRL5qFK6d5eVx.png', 'ngo-verification.png', 'approved', NULL, 2, '2026-11-12 02:53:41', '2026-11-12 02:53:31', '2026-11-12 02:53:41'),
(2, 12, 'ngo-documents/bQdjPfCEhFVkGJhO26B709aTI2hPiz2iZAlxWDAc.png', 'ngo-verification.png', 'approved', NULL, 2, '2026-11-12 02:57:37', '2026-11-12 02:57:27', '2026-11-12 02:57:37'),
(3, 14, 'ngo-documents/lcbXPtLVeswyMUdWZQScilSyp2VjIHTuoW838RjX.png', 'ngo-verification.png', 'approved', NULL, 2, '2026-11-12 03:00:07', '2026-11-12 02:59:55', '2026-11-12 03:00:11'),
(4, 16, 'ngo-documents/M4VgxlgliFzGZFnbcu8XLkNySAee8N3g8VBYehqt.png', 'ngo-verification.png', 'approved', NULL, 2, '2026-11-12 03:03:02', '2026-11-12 03:02:53', '2026-11-12 03:03:07'),
(5, 18, 'ngo-documents/tY7K2hwaX2yX8QmyHifNOI5OYmiFIiiHcT7uYgBM.png', 'ngo-verification.png', 'approved', NULL, 2, '2026-11-12 03:05:26', '2026-11-12 03:05:03', '2026-11-12 03:05:35');

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
-- Indexes for table `food_listings`
--
ALTER TABLE `food_listings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `food_listings_donor_id_foreign` (`donor_id`),
  ADD KEY `food_listings_claimed_by_foreign` (`claimed_by`),
  ADD KEY `food_listings_status_expiry_at_index` (`status`,`expiry_at`),
  ADD KEY `food_listings_moderated_by_foreign` (`moderated_by`);

--
-- Indexes for table `listing_feedback`
--
ALTER TABLE `listing_feedback`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `listing_feedback_food_listing_id_recipient_id_unique` (`food_listing_id`,`recipient_id`),
  ADD KEY `listing_feedback_recipient_id_foreign` (`recipient_id`);

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
  ADD KEY `notifications_user_id_foreign` (`user_id`);

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
  ADD KEY `verification_documents_user_id_foreign` (`user_id`),
  ADD KEY `verification_documents_reviewed_by_foreign` (`reviewed_by`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `food_listings`
--
ALTER TABLE `food_listings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `listing_feedback`
--
ALTER TABLE `listing_feedback`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `verification_documents`
--
ALTER TABLE `verification_documents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `food_listings`
--
ALTER TABLE `food_listings`
  ADD CONSTRAINT `food_listings_claimed_by_foreign` FOREIGN KEY (`claimed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `food_listings_donor_id_foreign` FOREIGN KEY (`donor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `food_listings_moderated_by_foreign` FOREIGN KEY (`moderated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `listing_feedback`
--
ALTER TABLE `listing_feedback`
  ADD CONSTRAINT `listing_feedback_food_listing_id_foreign` FOREIGN KEY (`food_listing_id`) REFERENCES `food_listings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `listing_feedback_recipient_id_foreign` FOREIGN KEY (`recipient_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `verification_documents`
--
ALTER TABLE `verification_documents`
  ADD CONSTRAINT `verification_documents_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `verification_documents_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
