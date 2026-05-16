-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: projectfile
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
) ENGINE=InnoDB AUTO_INCREMENT=130 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (38,'Business & Management','business-management','Operations, entrepreneurship, leadership, marketing, strategy, and organizational studies.','2026-05-09 04:29:22','2026-05-09 04:34:29'),(42,'Arts & Humanities','arts-humanities','Creative work, language, culture, history, philosophy, religion, and heritage.','2026-05-09 04:29:22','2026-05-09 04:34:29'),(52,'Accounting & Taxation','accounting-taxation','Accounting systems, auditing, taxation, reporting, and financial controls.','2026-05-09 04:34:29','2026-05-09 04:34:29'),(53,'Agriculture & Food Systems','agriculture-food-systems','Agriculture, agribusiness, food production, nutrition, and food security.','2026-05-09 04:34:29','2026-05-09 04:34:29'),(54,'Architecture, Planning & Design','architecture-planning-design','Architecture, urban planning, interior design, product design, and spatial studies.','2026-05-09 04:34:29','2026-05-09 04:34:29'),(55,'Biological & Life Sciences','biological-life-sciences','Biology, microbiology, genetics, ecology, biotechnology, and life science research.','2026-05-09 04:34:29','2026-05-09 04:34:29'),(56,'Communication & Media Studies','communication-media-studies','Journalism, public relations, broadcasting, advertising, film, and digital media.','2026-05-09 04:34:29','2026-05-09 04:34:29'),(57,'Computer Science & Information Technology','computer-science-information-technology','Software, hardware, networks, cybersecurity, AI, and digital systems.','2026-05-09 04:34:29','2026-05-09 04:34:29'),(58,'Data, Statistics & Analytics','data-statistics-analytics','Statistics, data science, research analytics, business intelligence, and measurement.','2026-05-09 04:34:29','2026-05-09 04:34:29'),(59,'Earth, Space & Physical Sciences','earth-space-physical-sciences','Physics, chemistry, geology, astronomy, materials science, and physical science research.','2026-05-09 04:34:29','2026-05-09 04:34:29'),(60,'Economics & Development Studies','economics-development-studies','Economic systems, development, trade, markets, poverty, and livelihoods.','2026-05-09 04:34:29','2026-05-09 04:34:29'),(61,'Education & Learning','education-learning','Teaching, curriculum, learning systems, educational policy, and training.','2026-05-09 04:34:29','2026-05-09 04:34:29'),(62,'Engineering & Manufacturing','engineering-manufacturing','Civil, mechanical, electrical, chemical, industrial, and production engineering.','2026-05-09 04:34:29','2026-05-09 04:34:29'),(63,'Environment, Climate & Sustainability','environment-climate-sustainability','Climate, conservation, environmental management, resources, and sustainability.','2026-05-09 04:34:29','2026-05-09 04:34:29'),(64,'Finance, Banking & Insurance','finance-banking-insurance','Finance, banking, investment, risk, insurance, and capital markets.','2026-05-09 04:34:29','2026-05-09 04:34:29'),(65,'Health, Medicine & Public Health','health-medicine-public-health','Medicine, nursing, pharmacy, public health, healthcare delivery, and wellbeing.','2026-05-09 04:34:29','2026-05-09 04:34:29'),(66,'Hospitality, Tourism & Events','hospitality-tourism-events','Tourism, travel, hotels, recreation, events, and destination management.','2026-05-09 04:34:29','2026-05-09 04:34:29'),(67,'Human Resources & Workplace Studies','human-resources-workplace-studies','People management, labor relations, workplace culture, and employee development.','2026-05-09 04:34:30','2026-05-09 04:34:30'),(68,'International Relations & Security Studies','international-relations-security-studies','Diplomacy, conflict, peace studies, defense, migration, and global affairs.','2026-05-09 04:34:30','2026-05-09 04:34:30'),(69,'Law, Governance & Public Policy','law-governance-public-policy','Law, regulation, governance, public administration, policy, and civic systems.','2026-05-09 04:34:30','2026-05-09 04:34:30'),(70,'Library, Archives & Information Science','library-archives-information-science','Libraries, records, archives, knowledge management, and information access.','2026-05-09 04:34:30','2026-05-09 04:34:30'),(71,'Linguistics & Languages','linguistics-languages','Language studies, translation, literacy, communication, and applied linguistics.','2026-05-09 04:34:30','2026-05-09 04:34:30'),(72,'Logistics, Transport & Supply Chain','logistics-transport-supply-chain','Logistics, procurement, transport systems, warehousing, and distribution.','2026-05-09 04:34:30','2026-05-09 04:34:30'),(73,'Mathematics & Quantitative Studies','mathematics-quantitative-studies','Mathematics, modeling, operations research, and quantitative methods.','2026-05-09 04:34:30','2026-05-09 04:34:30'),(74,'Music, Theatre & Performing Arts','music-theatre-performing-arts','Music, theatre, dance, performance, production, and creative practice.','2026-05-09 04:34:30','2026-05-09 04:34:30'),(75,'Natural Resources & Energy','natural-resources-energy','Oil, gas, renewable energy, mining, water, forestry, and resource management.','2026-05-09 04:34:30','2026-05-09 04:34:30'),(76,'Psychology & Behavioral Science','psychology-behavioral-science','Psychology, behavior, cognition, mental health, and human development.','2026-05-09 04:34:30','2026-05-09 04:34:30'),(77,'Religion, Ethics & Philosophy','religion-ethics-philosophy','Religious studies, ethics, philosophy, values, and belief systems.','2026-05-09 04:34:30','2026-05-09 04:34:30'),(78,'Social Sciences & Community Development','social-sciences-community-development','Sociology, anthropology, gender studies, community work, and social change.','2026-05-09 04:34:30','2026-05-09 04:34:30'),(79,'Sports, Recreation & Wellness','sports-recreation-wellness','Sports science, coaching, fitness, recreation, wellness, and physical education.','2026-05-09 04:34:30','2026-05-09 04:34:30'),(80,'Visual Arts, Fashion & Creative Industries','visual-arts-fashion-creative-industries','Fine art, fashion, crafts, photography, graphics, and creative businesses.','2026-05-09 04:34:30','2026-05-09 04:34:30'),(81,'Vocational, Technical & Applied Skills','vocational-technical-applied-skills','Technical trades, applied skills, workshop practice, and vocational training.','2026-05-09 04:34:30','2026-05-09 04:34:30'),(82,'Interdisciplinary & General Studies','interdisciplinary-general-studies','Projects that combine multiple fields or do not fit a single category.','2026-05-09 04:34:30','2026-05-09 04:34:30');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2014_10_12_000000_create_users_table',1),(2,'2014_10_12_100000_create_password_reset_tokens_table',1),(3,'2019_08_19_000000_create_failed_jobs_table',1),(4,'2019_12_14_000001_create_personal_access_tokens_table',1),(5,'2024_02_13_024513_create_projects_table',1),(6,'2026_05_09_000001_add_role_to_users_table',2),(7,'2026_05_09_000002_create_categories_and_tags_tables',2),(8,'2026_05_09_000003_expand_projects_table',2),(9,'2026_05_09_000004_merge_site_control_role_into_super_admin',3);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
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
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `project_tag`
--

DROP TABLE IF EXISTS `project_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `project_tag` (
  `project_id` bigint(20) unsigned NOT NULL,
  `tag_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`project_id`,`tag_id`),
  KEY `project_tag_tag_id_foreign` (`tag_id`),
  CONSTRAINT `project_tag_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `project_tag_tag_id_foreign` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `project_tag`
--

LOCK TABLES `project_tag` WRITE;
/*!40000 ALTER TABLE `project_tag` DISABLE KEYS */;
/*!40000 ALTER TABLE `project_tag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `projects` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint(20) unsigned DEFAULT NULL,
  `student_name` varchar(255) DEFAULT NULL,
  `supervisor` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `project_type` varchar(255) DEFAULT NULL,
  `abstract` text DEFAULT NULL,
  `keywords` text DEFAULT NULL,
  `completion_year` int(11) DEFAULT NULL,
  `pdf_file` varchar(255) DEFAULT NULL,
  `pdf_path` varchar(255) DEFAULT NULL,
  `pdf_original_name` varchar(255) DEFAULT NULL,
  `pdf_mime` varchar(255) DEFAULT NULL,
  `pdf_size` bigint(20) unsigned DEFAULT NULL,
  `file_hash` varchar(64) DEFAULT NULL,
  `pdf_text` longtext DEFAULT NULL,
  `uploaded_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `projects_file_hash_unique` (`file_hash`),
  KEY `projects_category_id_foreign` (`category_id`),
  KEY `projects_uploaded_by_foreign` (`uploaded_by`),
  KEY `projects_title_index` (`title`),
  KEY `projects_student_name_index` (`student_name`),
  KEY `projects_supervisor_index` (`supervisor`),
  KEY `projects_project_type_index` (`project_type`),
  KEY `projects_completion_year_index` (`completion_year`),
  CONSTRAINT `projects_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `projects_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=91 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `projects`
--

LOCK TABLES `projects` WRITE;
/*!40000 ALTER TABLE `projects` DISABLE KEYS */;
INSERT INTO `projects` VALUES (1,NULL,'Mike','Kelvin','Ways to confuse you','2019',NULL,NULL,2019,'1778280965.pdf',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-08 21:56:05','2026-05-08 21:56:05',NULL);
/*!40000 ALTER TABLE `projects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tags`
--

DROP TABLE IF EXISTS `tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tags` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tags_name_unique` (`name`),
  UNIQUE KEY `tags_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=161 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tags`
--

LOCK TABLES `tags` WRITE;
/*!40000 ALTER TABLE `tags` DISABLE KEYS */;
INSERT INTO `tags` VALUES (13,'Artificial Intelligence','artificial-intelligence','2026-05-09 04:29:22','2026-05-09 04:29:22'),(14,'Machine Learning','machine-learning','2026-05-09 04:29:22','2026-05-09 04:29:22'),(15,'Web Development','web-development','2026-05-09 04:29:22','2026-05-09 04:29:22'),(16,'Mobile Application','mobile-application','2026-05-09 04:29:22','2026-05-09 04:29:22'),(17,'Data Analysis','data-analysis','2026-05-09 04:29:22','2026-05-09 04:29:22'),(18,'Cybersecurity','cybersecurity','2026-05-09 04:29:22','2026-05-09 04:29:22'),(19,'Cloud Computing','cloud-computing','2026-05-09 04:29:22','2026-05-09 04:29:22'),(20,'UI/UX','uiux','2026-05-09 04:29:22','2026-05-09 04:29:22'),(21,'Automation','automation','2026-05-09 04:29:22','2026-05-09 04:29:22'),(22,'Research','research','2026-05-09 04:29:22','2026-05-09 04:29:22'),(23,'Case Study','case-study','2026-05-09 04:29:22','2026-05-09 04:29:22'),(24,'Sustainability','sustainability','2026-05-09 04:29:22','2026-05-09 04:29:22'),(25,'E-commerce','e-commerce','2026-05-09 04:29:22','2026-05-09 04:29:22'),(26,'Information System','information-system','2026-05-09 04:29:22','2026-05-09 04:29:22'),(27,'Community Impact','community-impact','2026-05-09 04:29:22','2026-05-09 04:29:22'),(30,'Academic Performance','academic-performance','2026-05-09 04:34:30','2026-05-09 04:34:30'),(31,'Accounting','accounting','2026-05-09 04:34:30','2026-05-09 04:34:30'),(32,'Agribusiness','agribusiness','2026-05-09 04:34:30','2026-05-09 04:34:30'),(33,'Agriculture','agriculture','2026-05-09 04:34:30','2026-05-09 04:34:30'),(34,'Audit','audit','2026-05-09 04:34:30','2026-05-09 04:34:30'),(35,'Banking','banking','2026-05-09 04:34:30','2026-05-09 04:34:30'),(36,'Biology','biology','2026-05-09 04:34:30','2026-05-09 04:34:30'),(37,'Branding','branding','2026-05-09 04:34:30','2026-05-09 04:34:30'),(38,'Broadcasting','broadcasting','2026-05-09 04:34:30','2026-05-09 04:34:30'),(39,'Budgeting','budgeting','2026-05-09 04:34:30','2026-05-09 04:34:30'),(40,'Business Strategy','business-strategy','2026-05-09 04:34:30','2026-05-09 04:34:30'),(41,'Child Development','child-development','2026-05-09 04:34:30','2026-05-09 04:34:30'),(42,'Climate Change','climate-change','2026-05-09 04:34:30','2026-05-09 04:34:30'),(43,'Community Development','community-development','2026-05-09 04:34:30','2026-05-09 04:34:30'),(44,'Community Health','community-health','2026-05-09 04:34:30','2026-05-09 04:34:30'),(45,'Conflict Resolution','conflict-resolution','2026-05-09 04:34:30','2026-05-09 04:34:30'),(46,'Consumer Behavior','consumer-behavior','2026-05-09 04:34:30','2026-05-09 04:34:30'),(47,'Corporate Governance','corporate-governance','2026-05-09 04:34:30','2026-05-09 04:34:30'),(48,'Creative Practice','creative-practice','2026-05-09 04:34:30','2026-05-09 04:34:30'),(49,'Criminal Justice','criminal-justice','2026-05-09 04:34:30','2026-05-09 04:34:30'),(50,'Crop Production','crop-production','2026-05-09 04:34:30','2026-05-09 04:34:30'),(51,'Curriculum','curriculum','2026-05-09 04:34:30','2026-05-09 04:34:30'),(52,'Customer Experience','customer-experience','2026-05-09 04:34:30','2026-05-09 04:34:30'),(53,'Design','design','2026-05-09 04:34:30','2026-05-09 04:34:30'),(54,'Digital Media','digital-media','2026-05-09 04:34:30','2026-05-09 04:34:30'),(55,'Disability Studies','disability-studies','2026-05-09 04:34:30','2026-05-09 04:34:30'),(56,'Economic Development','economic-development','2026-05-09 04:34:30','2026-05-09 04:34:30'),(57,'Education Policy','education-policy','2026-05-09 04:34:30','2026-05-09 04:34:30'),(58,'Energy','energy','2026-05-09 04:34:30','2026-05-09 04:34:30'),(59,'Engineering Design','engineering-design','2026-05-09 04:34:30','2026-05-09 04:34:30'),(60,'Entrepreneurship','entrepreneurship','2026-05-09 04:34:30','2026-05-09 04:34:30'),(61,'Environmental Management','environmental-management','2026-05-09 04:34:30','2026-05-09 04:34:30'),(62,'Ethics','ethics','2026-05-09 04:34:30','2026-05-09 04:34:30'),(63,'Event Management','event-management','2026-05-09 04:34:30','2026-05-09 04:34:30'),(64,'Fashion','fashion','2026-05-09 04:34:30','2026-05-09 04:34:30'),(65,'Finance','finance','2026-05-09 04:34:30','2026-05-09 04:34:30'),(66,'Food Security','food-security','2026-05-09 04:34:30','2026-05-09 04:34:30'),(67,'Gender Studies','gender-studies','2026-05-09 04:34:30','2026-05-09 04:34:30'),(68,'Governance','governance','2026-05-09 04:34:30','2026-05-09 04:34:30'),(69,'Healthcare Delivery','healthcare-delivery','2026-05-09 04:34:30','2026-05-09 04:34:30'),(70,'History','history','2026-05-09 04:34:30','2026-05-09 04:34:30'),(71,'Hospitality','hospitality','2026-05-09 04:34:30','2026-05-09 04:34:30'),(72,'Human Resources','human-resources','2026-05-09 04:34:30','2026-05-09 04:34:30'),(73,'Innovation','innovation','2026-05-09 04:34:30','2026-05-09 04:34:30'),(74,'Insurance','insurance','2026-05-09 04:34:30','2026-05-09 04:34:30'),(75,'International Relations','international-relations','2026-05-09 04:34:30','2026-05-09 04:34:30'),(76,'Language','language','2026-05-09 04:34:30','2026-05-09 04:34:30'),(77,'Law','law','2026-05-09 04:34:30','2026-05-09 04:34:30'),(78,'Leadership','leadership','2026-05-09 04:34:30','2026-05-09 04:34:30'),(79,'Library Science','library-science','2026-05-09 04:34:30','2026-05-09 04:34:30'),(80,'Logistics','logistics','2026-05-09 04:34:30','2026-05-09 04:34:30'),(81,'Manufacturing','manufacturing','2026-05-09 04:34:30','2026-05-09 04:34:30'),(82,'Marketing','marketing','2026-05-09 04:34:30','2026-05-09 04:34:30'),(83,'Mental Health','mental-health','2026-05-09 04:34:30','2026-05-09 04:34:30'),(84,'Microbiology','microbiology','2026-05-09 04:34:30','2026-05-09 04:34:30'),(85,'Music','music','2026-05-09 04:34:30','2026-05-09 04:34:30'),(86,'Nutrition','nutrition','2026-05-09 04:34:30','2026-05-09 04:34:30'),(87,'Operations Management','operations-management','2026-05-09 04:34:30','2026-05-09 04:34:30'),(88,'Peace Studies','peace-studies','2026-05-09 04:34:30','2026-05-09 04:34:30'),(89,'Performance','performance','2026-05-09 04:34:30','2026-05-09 04:34:30'),(90,'Pharmacy','pharmacy','2026-05-09 04:34:30','2026-05-09 04:34:30'),(91,'Policy Analysis','policy-analysis','2026-05-09 04:34:30','2026-05-09 04:34:30'),(92,'Procurement','procurement','2026-05-09 04:34:30','2026-05-09 04:34:30'),(93,'Public Administration','public-administration','2026-05-09 04:34:30','2026-05-09 04:34:30'),(94,'Public Health','public-health','2026-05-09 04:34:30','2026-05-09 04:34:30'),(95,'Public Relations','public-relations','2026-05-09 04:34:30','2026-05-09 04:34:30'),(96,'Renewable Energy','renewable-energy','2026-05-09 04:34:30','2026-05-09 04:34:30'),(97,'Risk Management','risk-management','2026-05-09 04:34:30','2026-05-09 04:34:30'),(98,'Rural Development','rural-development','2026-05-09 04:34:30','2026-05-09 04:34:30'),(99,'Small Business','small-business','2026-05-09 04:34:30','2026-05-09 04:34:30'),(100,'Social Impact','social-impact','2026-05-09 04:34:30','2026-05-09 04:34:30'),(101,'Software','software','2026-05-09 04:34:30','2026-05-09 04:34:30'),(102,'Sports Science','sports-science','2026-05-09 04:34:30','2026-05-09 04:34:30'),(103,'Statistics','statistics','2026-05-09 04:34:30','2026-05-09 04:34:30'),(104,'Supply Chain','supply-chain','2026-05-09 04:34:30','2026-05-09 04:34:30'),(105,'Taxation','taxation','2026-05-09 04:34:30','2026-05-09 04:34:30'),(106,'Teaching Methods','teaching-methods','2026-05-09 04:34:30','2026-05-09 04:34:30'),(107,'Theatre','theatre','2026-05-09 04:34:30','2026-05-09 04:34:30'),(108,'Tourism','tourism','2026-05-09 04:34:30','2026-05-09 04:34:30'),(109,'Training','training','2026-05-09 04:34:30','2026-05-09 04:34:30'),(110,'Translation','translation','2026-05-09 04:34:30','2026-05-09 04:34:30'),(111,'Transport','transport','2026-05-09 04:34:30','2026-05-09 04:34:30'),(112,'Urban Planning','urban-planning','2026-05-09 04:34:30','2026-05-09 04:34:30'),(113,'Water Resources','water-resources','2026-05-09 04:34:30','2026-05-09 04:34:30'),(114,'Wellbeing','wellbeing','2026-05-09 04:34:30','2026-05-09 04:34:30'),(115,'Women Empowerment','women-empowerment','2026-05-09 04:34:30','2026-05-09 04:34:30'),(116,'Youth Development','youth-development','2026-05-09 04:34:30','2026-05-09 04:34:30');
/*!40000 ALTER TABLE `tags` ENABLE KEYS */;
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
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(40) NOT NULL DEFAULT 'user',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_role_index` (`role`)
) ENGINE=InnoDB AUTO_INCREMENT=174 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (37,'Kelvin Ajala','kelvinajala007@gmail.com',NULL,'$2y$12$CY.ftknSXdFmPQyBnytLz.a.VZfb6r0/nOY8NWvlzBeieKzLxCPVS','super_admin',NULL,'2026-05-09 03:54:02','2026-05-09 03:54:02'),(38,'Gideon','ikelvinmiguel@gmail.com',NULL,'$2y$12$Q0tYHUN.2sVSKfmqmVz0keWXex2fr3QRoOzO1.uJyOCpKVsUa0kPi','admin',NULL,'2026-05-09 03:56:21','2026-05-09 04:09:56');
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

-- Dump completed on 2026-05-15 23:44:12
