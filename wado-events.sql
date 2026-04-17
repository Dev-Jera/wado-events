-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: wado-events
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_name_unique` (`name`),
  UNIQUE KEY `categories_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Music','music','Concerts, live sessions, and performance nights.','2026-04-07 04:18:53','2026-04-07 04:18:53'),(2,'Conference','conference','Professional events, summits, and networking sessions.','2026-04-07 04:18:53','2026-04-07 04:18:53'),(3,'Film','film','Premieres, screenings, and cinema experiences.','2026-04-07 04:18:53','2026-04-07 04:18:53'),(4,'Sports','sports','Matches, tournaments, and fan experiences.','2026-04-07 04:24:57','2026-04-07 04:24:57'),(5,'Church','church','Fellowship gatherings, worship services, and ministry events.','2026-04-07 04:24:57','2026-04-07 04:24:57'),(6,'Charity','charity','Giving drives, support events, and social impact gatherings.','2026-04-07 04:24:57','2026-04-07 04:24:57'),(7,'Musical Concerts','musical-concerts','Live performances, concerts, and entertainment experiences.','2026-04-07 04:24:57','2026-04-07 04:24:57'),(8,'Fundraising','fundraising','Campaign launches, donor drives, and community fundraising events.','2026-04-07 04:24:57','2026-04-07 04:24:57'),(9,'Educational','educational','Learning sessions, workshops, trainings, and academic events.','2026-04-07 04:24:57','2026-04-07 04:24:57'),(10,'Gaming','gaming','Esports, tournaments, gaming nights, and interactive competitions.','2026-04-07 04:24:57','2026-04-07 04:24:57'),(11,'Conferences','conferences','Professional conferences, summits, and business networking sessions.','2026-04-07 04:24:57','2026-04-07 04:24:57'),(12,'Kids Events','kids-events','Children-focused activities, fun days, and family entertainment.','2026-04-07 04:24:57','2026-04-07 04:24:57'),(13,'Wellness','wellness','Health, fitness, mindfulness, and wellbeing experiences.','2026-04-07 04:24:57','2026-04-07 04:24:57'),(14,'Family','family','Family-friendly outings, celebrations, and shared experiences.','2026-04-07 04:24:57','2026-04-07 04:24:57'),(15,'Corporate Events','corporate-events','Company meetings, launches, networking, and staff experiences.','2026-04-07 04:24:57','2026-04-07 04:24:57'),(16,'Free Events','free-events','Open-access community events with free entry.','2026-04-07 04:24:57','2026-04-07 04:24:57'),(17,'Comedy','comedy','Stand-up shows, comic performances, and laughter-filled nights.','2026-04-07 04:24:57','2026-04-07 04:24:57');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event_artists`
--

DROP TABLE IF EXISTS `event_artists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_artists` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `sort_order` int(10) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `event_artists_event_id_foreign` (`event_id`),
  CONSTRAINT `event_artists_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event_artists`
--

LOCK TABLES `event_artists` WRITE;
/*!40000 ALTER TABLE `event_artists` DISABLE KEYS */;
INSERT INTO `event_artists` VALUES (1,4,'Moses Bliss',1,'2026-04-07 13:34:30','2026-04-07 13:34:30');
/*!40000 ALTER TABLE `event_artists` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event_bookmarks`
--

DROP TABLE IF EXISTS `event_bookmarks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_bookmarks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `event_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `event_bookmarks_user_id_event_id_unique` (`user_id`,`event_id`),
  KEY `event_bookmarks_event_id_foreign` (`event_id`),
  CONSTRAINT `event_bookmarks_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `event_bookmarks_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event_bookmarks`
--

LOCK TABLES `event_bookmarks` WRITE;
/*!40000 ALTER TABLE `event_bookmarks` DISABLE KEYS */;
/*!40000 ALTER TABLE `event_bookmarks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event_gate_agent`
--

DROP TABLE IF EXISTS `event_gate_agent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_gate_agent` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `event_gate_agent_event_id_user_id_unique` (`event_id`,`user_id`),
  KEY `event_gate_agent_user_id_foreign` (`user_id`),
  CONSTRAINT `event_gate_agent_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `event_gate_agent_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event_gate_agent`
--

LOCK TABLES `event_gate_agent` WRITE;
/*!40000 ALTER TABLE `event_gate_agent` DISABLE KEYS */;
INSERT INTO `event_gate_agent` VALUES (1,3,4,'2026-04-16 09:39:24','2026-04-16 09:39:24');
/*!40000 ALTER TABLE `event_gate_agent` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `events` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `category_id` bigint(20) unsigned DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `venue` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `starts_at` datetime NOT NULL,
  `ends_at` datetime DEFAULT NULL,
  `ticket_price` decimal(12,2) NOT NULL,
  `capacity` int(10) unsigned NOT NULL,
  `tickets_available` int(10) unsigned NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'draft',
  `image_url` varchar(255) DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `events_slug_unique` (`slug`),
  KEY `events_user_id_foreign` (`user_id`),
  KEY `events_category_id_foreign` (`category_id`),
  CONSTRAINT `events_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `events_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `events`
--

LOCK TABLES `events` WRITE;
/*!40000 ALTER TABLE `events` DISABLE KEYS */;
INSERT INTO `events` VALUES (1,1,1,'Live Music Night','live-music-night','Kampala Serena Hotel','Kampala','Uganda','An energetic live music showcase featuring emerging artists, premium sound, and a full night of performances.','2026-04-22 19:00:00','2026-04-22 23:00:00',50000.00,350,350,'published','/images/music.jpg',1,'2026-04-07 04:18:53','2026-04-15 13:09:33'),(2,1,2,'Business Connect 2026','business-connect-2026','Speke Resort','Kampala','Uganda','A full-day networking and insights conference bringing entrepreneurs, investors, and operators together.','2026-04-29 09:00:00','2026-04-29 17:30:00',150000.00,500,500,'published','/images/conference.jpg',1,'2026-04-07 04:18:53','2026-04-15 13:09:33'),(3,1,3,'Film Premiere Weekend','film-premiere-weekend','Century Cinemax','Kampala','Uganda','A premiere screening experience with red carpet arrivals, exclusive previews, and filmmaker conversations.','2026-05-06 18:30:00','2026-05-06 22:00:00',30000.00,220,220,'published','/images/movie.jpg',0,'2026-04-07 04:18:53','2026-04-15 13:09:33'),(4,7,5,'My greate Price','my-greate-price-eygah2','Phaneroo grounds Naguru','Kampala','Uganda','Ahh okay — this is just about where to get your font files from and what that message actually means. Let me break it down simply 👇\n\n🧠 What “iBrand font files” means\n\n“iBrand” is just a custom font name your project is expecting.\n\n👉 It’s NOT something you download randomly\n👉 It must come from whoever owns that brand','2026-04-09 07:00:00','2026-04-23 00:00:00',0.00,1009000000,1009000000,'published','/storage/event-images/01KNMCR22N1VSEG2B1MD7MF9VJ.jfif',1,'2026-04-07 13:34:30','2026-04-16 08:31:57'),(5,6,2,'Organisation Demo Summit','organisation-demo-summit','UMA Multipurpose Hall','Kampala','Uganda','Seeded event owned by organisation account for role POV testing.','2026-04-25 10:00:00','2026-04-25 18:00:00',100000.00,250,250,'published','/images/conference.jpg',0,'2026-04-15 13:09:33','2026-04-15 13:09:33');
/*!40000 ALTER TABLE `events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `login_attempts`
--

DROP TABLE IF EXISTS `login_attempts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `login_attempts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `succeeded` tinyint(1) NOT NULL DEFAULT 0,
  `user_agent` varchar(500) DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `failure_reason` varchar(120) DEFAULT NULL,
  `attempted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `login_attempts_ip_address_index` (`ip_address`),
  KEY `login_attempts_email_index` (`email`),
  KEY `login_attempts_attempted_at_index` (`attempted_at`),
  KEY `login_attempts_succeeded_attempted_at_index` (`succeeded`,`attempted_at`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `login_attempts`
--

LOCK TABLES `login_attempts` WRITE;
/*!40000 ALTER TABLE `login_attempts` DISABLE KEYS */;
INSERT INTO `login_attempts` VALUES (1,'agent@wado.test','127.0.0.1',1,'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',5,NULL,'2026-04-16 11:31:26');
/*!40000 ALTER TABLE `login_attempts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_04_06_170000_add_role_to_users_table',1),(5,'2026_04_06_171000_create_events_table',1),(6,'2026_04_06_180000_create_categories_table',1),(7,'2026_04_06_181000_add_category_id_to_events_table',1),(8,'2026_04_06_190000_create_ticket_categories_table',1),(9,'2026_04_06_200000_create_event_artists_table',1),(10,'2026_04_07_090000_create_tickets_table',1),(11,'2026_04_07_130000_add_qr_fields_to_tickets_table',2),(12,'2026_04_07_140000_add_phone_to_users_table',3),(13,'2026_04_07_141000_create_ticket_scan_logs_table',3),(14,'2026_04_07_150000_create_payment_transactions_table',4),(15,'2026_04_10_000000_add_holder_name_to_tickets_and_payment_transactions',5),(16,'2026_04_10_000001_create_event_bookmarks_table',6),(17,'2026_04_15_120000_create_notifications_table',7),(18,'2026_04_15_131000_add_refund_request_fields_to_payment_transactions_table',8),(19,'2026_04_15_140000_add_profile_image_to_users_table',9),(20,'2026_04_15_200000_add_walkin_audit_fields_to_payment_transactions_table',10),(21,'2026_04_16_120000_create_event_gate_agent_table',11),(22,'2026_04_16_200000_create_login_attempts_table',12);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) unsigned NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_transactions`
--

DROP TABLE IF EXISTS `payment_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payment_transactions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `event_id` bigint(20) unsigned NOT NULL,
  `ticket_category_id` bigint(20) unsigned NOT NULL,
  `holder_name` varchar(255) DEFAULT NULL,
  `ticket_id` bigint(20) unsigned DEFAULT NULL,
  `idempotency_key` varchar(64) NOT NULL,
  `payment_provider` varchar(20) DEFAULT NULL,
  `sales_channel` varchar(20) DEFAULT NULL,
  `collected_by_user_id` bigint(20) unsigned DEFAULT NULL,
  `collected_at` timestamp NULL DEFAULT NULL,
  `collector_reference` varchar(120) DEFAULT NULL,
  `phone_number` varchar(40) DEFAULT NULL,
  `quantity` int(10) unsigned NOT NULL,
  `unit_price` decimal(12,2) NOT NULL,
  `total_amount` decimal(12,2) NOT NULL,
  `currency` varchar(8) NOT NULL DEFAULT 'UGX',
  `status` varchar(20) NOT NULL,
  `provider_reference` varchar(120) DEFAULT NULL,
  `provider_status` varchar(60) DEFAULT NULL,
  `provider_payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`provider_payload`)),
  `webhook_payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`webhook_payload`)),
  `expires_at` timestamp NULL DEFAULT NULL,
  `callback_received_at` timestamp NULL DEFAULT NULL,
  `confirmed_at` timestamp NULL DEFAULT NULL,
  `failed_at` timestamp NULL DEFAULT NULL,
  `refunded_at` timestamp NULL DEFAULT NULL,
  `refund_requested_at` timestamp NULL DEFAULT NULL,
  `refund_request_status` varchar(40) DEFAULT NULL,
  `refund_request_reason` text DEFAULT NULL,
  `ticket_issued_at` timestamp NULL DEFAULT NULL,
  `last_error` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payment_transactions_idempotency_key_unique` (`idempotency_key`),
  KEY `payment_transactions_user_id_foreign` (`user_id`),
  KEY `payment_transactions_event_id_foreign` (`event_id`),
  KEY `payment_transactions_ticket_category_id_foreign` (`ticket_category_id`),
  KEY `payment_transactions_ticket_id_foreign` (`ticket_id`),
  KEY `payment_transactions_status_index` (`status`),
  KEY `payment_transactions_provider_reference_index` (`provider_reference`),
  KEY `payment_transactions_expires_at_index` (`expires_at`),
  KEY `payment_transactions_collected_by_user_id_foreign` (`collected_by_user_id`),
  CONSTRAINT `payment_transactions_collected_by_user_id_foreign` FOREIGN KEY (`collected_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payment_transactions_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payment_transactions_ticket_category_id_foreign` FOREIGN KEY (`ticket_category_id`) REFERENCES `ticket_categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payment_transactions_ticket_id_foreign` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payment_transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_transactions`
--

LOCK TABLES `payment_transactions` WRITE;
/*!40000 ALTER TABLE `payment_transactions` DISABLE KEYS */;
INSERT INTO `payment_transactions` VALUES (2,4,4,25,NULL,NULL,'7B624CB6-BD1F-4D48-92E6-2760B2E15813','mtn',NULL,NULL,NULL,NULL,'0788862158',1,5000.00,5000.00,'UGX','FAILED',NULL,NULL,NULL,NULL,'2026-04-09 10:36:17',NULL,NULL,'2026-04-09 10:31:17',NULL,NULL,NULL,NULL,NULL,'MARZEPAY_BASE_URL is not configured.','2026-04-09 10:31:17','2026-04-09 10:31:17');
/*!40000 ALTER TABLE `payment_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ticket_categories`
--

DROP TABLE IF EXISTS `ticket_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ticket_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `ticket_count` int(10) unsigned NOT NULL,
  `tickets_remaining` int(10) unsigned NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `sort_order` int(10) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ticket_categories_event_id_foreign` (`event_id`),
  CONSTRAINT `ticket_categories_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ticket_categories`
--

LOCK TABLES `ticket_categories` WRITE;
/*!40000 ALTER TABLE `ticket_categories` DISABLE KEYS */;
INSERT INTO `ticket_categories` VALUES (25,4,'VIP',5000.00,9000000,9000000,'Snacks and drinks ',1,'2026-04-07 13:34:30','2026-04-09 10:31:17'),(26,4,'Ordinary',2000.00,1000000000,1000000000,NULL,2,'2026-04-07 13:34:30','2026-04-07 13:34:30'),(27,1,'VIP',80000.00,120,120,'Priority entrance and premium seating.',0,'2026-04-15 13:09:33','2026-04-15 13:09:33'),(28,1,'Ordinary',50000.00,230,230,'Standard event access.',1,'2026-04-15 13:09:33','2026-04-15 13:09:33'),(29,2,'Executive',250000.00,120,120,'Front seating and VIP networking lounge.',0,'2026-04-15 13:09:33','2026-04-15 13:09:33'),(30,2,'Standard',150000.00,380,380,'Full conference access.',1,'2026-04-15 13:09:33','2026-04-15 13:09:33'),(31,3,'Red Carpet',60000.00,40,40,'Red carpet access and premium screening seats.',0,'2026-04-15 13:09:33','2026-04-15 13:09:33'),(32,3,'Ordinary',30000.00,180,180,'General screening access.',1,'2026-04-15 13:09:33','2026-04-15 13:09:33'),(33,5,'General',100000.00,250,250,'Organisation demo ticket class.',0,'2026-04-15 13:09:33','2026-04-15 13:09:33');
/*!40000 ALTER TABLE `ticket_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ticket_scan_logs`
--

DROP TABLE IF EXISTS `ticket_scan_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ticket_scan_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` bigint(20) unsigned DEFAULT NULL,
  `staff_user_id` bigint(20) unsigned DEFAULT NULL,
  `ticket_code` varchar(255) DEFAULT NULL,
  `scanned_payload` text DEFAULT NULL,
  `device_id` varchar(120) DEFAULT NULL,
  `result` varchar(40) NOT NULL,
  `message` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `scanned_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ticket_scan_logs_ticket_id_foreign` (`ticket_id`),
  KEY `ticket_scan_logs_staff_user_id_foreign` (`staff_user_id`),
  KEY `ticket_scan_logs_ticket_code_index` (`ticket_code`),
  KEY `ticket_scan_logs_result_index` (`result`),
  KEY `ticket_scan_logs_scanned_at_index` (`scanned_at`),
  CONSTRAINT `ticket_scan_logs_staff_user_id_foreign` FOREIGN KEY (`staff_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ticket_scan_logs_ticket_id_foreign` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ticket_scan_logs`
--

LOCK TABLES `ticket_scan_logs` WRITE;
/*!40000 ALTER TABLE `ticket_scan_logs` DISABLE KEYS */;
INSERT INTO `ticket_scan_logs` VALUES (1,NULL,1,'ADO-1-2-BPEIT9','','scanner-filzwwfp','rejected','Ticket not found [gate_event_id=2]','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-04-10 04:44:38','2026-04-10 04:44:38','2026-04-10 04:44:38'),(2,NULL,1,'WADO-1-2-BPEIT9','{\"v\":2,\"code\":\"WADO-1-2-BPEIT9\",\"event_id\":2,\"sig\":\"7cf1870f18dc80370ef0255ac841aa69215d9d21b151322951cd0a848c3186bf\"}','scanner-filzwwfp','valid','Ticket verified and marked used [gate_event_id=2]','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-04-10 05:48:35','2026-04-10 05:48:35','2026-04-10 05:48:35'),(3,NULL,1,'WADO-1-2-BPEIT9','{\"v\":2,\"code\":\"WADO-1-2-BPEIT9\",\"event_id\":2,\"sig\":\"7cf1870f18dc80370ef0255ac841aa69215d9d21b151322951cd0a848c3186bf\"}','scanner-filzwwfp','already-used','Ticket already used [gate_event_id=2]','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-04-10 05:48:56','2026-04-10 05:48:56','2026-04-10 05:48:56'),(4,NULL,1,'WADO-XT47IZA9NC','{\"v\":2,\"code\":\"WADO-XT47IZA9NC\",\"event_id\":2,\"sig\":\"538fa114fa78f8f64a7ae7d0928cc2d416cd096b0c6ee2d9d01241e712a13ddd\"}','scanner-filzwwfp','valid','Ticket verified and marked used [gate_event_id=2]','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-04-10 05:52:01','2026-04-10 05:52:01','2026-04-10 05:52:01'),(5,NULL,1,'WADO-DKOPKN58AQ','{\"v\":2,\"code\":\"WADO-DKOPKN58AQ\",\"event_id\":2,\"sig\":\"dbdf78f212c6612e2766aa33b54fa1e80a31c5756b16ca4f0a72358737fef4fd\"}','scanner-filzwwfp','valid','Ticket verified and marked used [gate_event_id=2]','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-04-10 06:34:37','2026-04-10 06:34:37','2026-04-10 06:34:37'),(6,NULL,1,'WADO-DKOPKN58AQ','{\"v\":2,\"code\":\"WADO-DKOPKN58AQ\",\"event_id\":2,\"sig\":\"dbdf78f212c6612e2766aa33b54fa1e80a31c5756b16ca4f0a72358737fef4fd\"}','scanner-filzwwfp','already-used','Ticket already used [gate_event_id=2]','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-04-10 06:35:07','2026-04-10 06:35:07','2026-04-10 06:35:07'),(7,NULL,1,'WADO-DKOPKN58AQ','{\"v\":2,\"code\":\"WADO-DKOPKN58AQ\",\"event_id\":2,\"sig\":\"dbdf78f212c6612e2766aa33b54fa1e80a31c5756b16ca4f0a72358737fef4fd\"}','scanner-filzwwfp','already-used','Ticket already used [gate_event_id=2]','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-04-10 06:36:26','2026-04-10 06:36:26','2026-04-10 06:36:26'),(8,NULL,1,'WADO-DKOPKN58AQ','{\"v\":2,\"code\":\"WADO-DKOPKN58AQ\",\"event_id\":2,\"sig\":\"dbdf78f212c6612e2766aa33b54fa1e80a31c5756b16ca4f0a72358737fef4fd\"}','scanner-filzwwfp','already-used','Ticket already used [gate_event_id=2]','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-04-10 06:36:29','2026-04-10 06:36:29','2026-04-10 06:36:29'),(9,NULL,1,'WADO-1-1-KN1BNV','{\"v\":2,\"code\":\"WADO-1-1-KN1BNV\",\"event_id\":1,\"sig\":\"a175cb3b7d5017ad9a18c2efc189492194e4548b220d1934b32b6686eb7743ca\"}','scanner-filzwwfp','valid','Ticket verified and marked used [gate_event_id=1]','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-04-10 08:22:56','2026-04-10 08:22:56','2026-04-10 08:22:56');
/*!40000 ALTER TABLE `ticket_scan_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tickets`
--

DROP TABLE IF EXISTS `tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tickets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `event_id` bigint(20) unsigned NOT NULL,
  `ticket_category_id` bigint(20) unsigned NOT NULL,
  `holder_name` varchar(255) DEFAULT NULL,
  `payer_name` varchar(255) DEFAULT NULL,
  `ticket_code` varchar(255) NOT NULL,
  `qr_code_path` varchar(255) DEFAULT NULL,
  `quantity` int(10) unsigned NOT NULL,
  `unit_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `payment_provider` varchar(20) NOT NULL DEFAULT 'free',
  `status` varchar(40) NOT NULL DEFAULT 'confirmed',
  `purchased_at` datetime NOT NULL,
  `used_at` datetime DEFAULT NULL,
  `dismissed_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tickets_ticket_code_unique` (`ticket_code`),
  KEY `tickets_user_id_foreign` (`user_id`),
  KEY `tickets_event_id_foreign` (`event_id`),
  KEY `tickets_ticket_category_id_foreign` (`ticket_category_id`),
  CONSTRAINT `tickets_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tickets_ticket_category_id_foreign` FOREIGN KEY (`ticket_category_id`) REFERENCES `ticket_categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tickets_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tickets`
--

LOCK TABLES `tickets` WRITE;
/*!40000 ALTER TABLE `tickets` DISABLE KEYS */;
/*!40000 ALTER TABLE `tickets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `profile_image_path` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'customer',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin User','admin@wado.test',NULL,'users/profile-images/01KPBGRJ99E5A62ZMQ14PVCE73.jfif','2026-04-15 13:09:31','$2y$12$fkZFJ.LF4YLS/puCQqoeROyGHqUpcxblKxf5y13rGXVDQsc1lttgS','super_admin','JTEIdRzqTbGVRmzImfhiMjAFgyKdsjkBYNlCxElnBh6nWXwzdoVOjHxl468V','2026-04-07 04:18:53','2026-04-16 13:07:38'),(2,'Test User','test@example.com',NULL,NULL,'2026-04-15 13:09:32','$2y$12$1Pni70ctK3OSLPlQyYSfROIWsCkuFAzyP.R1DJaOFF93MFCccHZji','customer','Bzt3opFM4A','2026-04-07 04:18:53','2026-04-15 13:09:32'),(3,'Jera B','aloyobrendaojera@gmail.com',NULL,NULL,NULL,'$2y$12$YlNrGGPY4UkGqg8LaF5iTOs7DO4B87q2J7nzlqKMBC295BJg0sOIe','customer',NULL,'2026-04-07 05:59:19','2026-04-07 05:59:19'),(4,'Edgar','edgarssekalembe@gmail.com','0788862158','users/profile-images/01KPB46Z2K2XG3FA1C6NB8PQ0G.jfif',NULL,'$2y$12$MQ2Z7HJhI06Kg9so/ACR8Oa9RUm8ORR4/xln7EJ8FLjAnLIsFOqVa','gate_agent',NULL,'2026-04-09 10:20:42','2026-04-16 09:28:18'),(5,'Gate Agent Demo','agent@wado.test','+256700111222',NULL,'2026-04-15 13:09:32','$2y$12$DANxVfsftZckWBAsfKCTJOcXexxTttSxqKxzTLqeMRdrbv8O7OH/a','gate_agent','fIBHj8Jq11NXhAejhA9Ub1nhmGBUj5dttRNPQGVe2PPjZOyg3hSzRy9cDzf6','2026-04-15 13:09:32','2026-04-15 13:09:32'),(6,'Organisation Owner Demo','org@wado.test','+256700333444',NULL,'2026-04-15 13:09:33','$2y$12$DDkERTkUaFAB0AQu8WzYROvYj28Rn2r4cMPwX9OJfY2n2gG.xrNzq','customer','Hl1gosJsGs','2026-04-15 13:09:33','2026-04-15 13:09:33'),(7,'Lakicha Reachel','lakichareachel@gmail.com','0788177190','users/profile-images/01KPB0RFRSGAHVHF25YFYWRVQ9.jpeg',NULL,'$2y$12$8ehNlWY0hozJKrgaTF6HXucNfWYhmdnp/dutqIVd8IkyY919PXrsq','customer',NULL,'2026-04-16 08:12:32','2026-04-16 08:27:58');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-04-17 14:46:09
