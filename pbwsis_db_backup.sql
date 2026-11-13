-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: pbwsis_db
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
-- Table structure for table `audits`
--

DROP TABLE IF EXISTS `audits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audits` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_type` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `event` varchar(255) NOT NULL,
  `auditable_type` varchar(255) NOT NULL,
  `auditable_id` bigint(20) unsigned NOT NULL,
  `old_values` text DEFAULT NULL,
  `new_values` text DEFAULT NULL,
  `url` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(1023) DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `audits_auditable_type_auditable_id_index` (`auditable_type`,`auditable_id`),
  KEY `audits_user_id_user_type_index` (`user_id`,`user_type`)
) ENGINE=InnoDB AUTO_INCREMENT=82 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audits`
--

LOCK TABLES `audits` WRITE;
/*!40000 ALTER TABLE `audits` DISABLE KEYS */;
INSERT INTO `audits` VALUES (1,'App\\Models\\User',1,'updated','App\\Models\\Ingredient',1,'{\"quantity\":\"15.00\"}','{\"quantity\":19}','http://127.0.0.1:8000/purchases','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-09-30 06:44:06','2026-09-30 06:44:06'),(2,'App\\Models\\User',1,'updated','App\\Models\\Ingredient',2,'{\"quantity\":\"20.50\"}','{\"quantity\":30.5}','http://127.0.0.1:8000/purchases','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-09-30 06:52:00','2026-09-30 06:52:00'),(3,'App\\Models\\User',1,'updated','App\\Models\\Ingredient',1,'{\"quantity\":\"19.00\"}','{\"quantity\":119}','http://127.0.0.1:8000/purchases','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-09-30 06:53:23','2026-09-30 06:53:23'),(4,'App\\Models\\User',1,'updated','App\\Models\\Ingredient',1,'{\"quantity\":\"119.00\"}','{\"quantity\":147}','http://127.0.0.1:8000/purchases','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-01 05:51:28','2026-10-01 05:51:28'),(5,'App\\Models\\User',1,'updated','App\\Models\\Ingredient',1,'{\"quantity\":\"147.00\"}','{\"quantity\":167}','http://127.0.0.1:8000/purchases','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-01 05:51:54','2026-10-01 05:51:54'),(6,'App\\Models\\User',1,'updated','App\\Models\\Ingredient',1,'{\"quantity\":\"167.00\"}','{\"quantity\":171}','http://127.0.0.1:8000/purchases','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-01 05:52:14','2026-10-01 05:52:14'),(7,'App\\Models\\User',1,'created','App\\Models\\Sale',1,'[]','{\"order_number\":\"ORD-202610-0001\",\"sale_date\":\"2026-10-01 13:53:23\",\"subtotal\":540,\"vat_amount\":57.86,\"discount_type\":null,\"discount_amount\":0,\"total_amount\":540,\"order_channel\":\"Dine-in\",\"payment_method\":\"Cash\",\"sale_id\":1}','http://127.0.0.1:8000/sales','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-01 05:53:23','2026-10-01 05:53:23'),(8,'App\\Models\\User',1,'created','App\\Models\\Sale',2,'[]','{\"order_number\":\"ORD-202610-0002\",\"sale_date\":\"2026-10-01 13:54:11\",\"subtotal\":2340,\"vat_amount\":250.71,\"discount_type\":null,\"discount_amount\":0,\"total_amount\":2340,\"order_channel\":\"Dine-in\",\"payment_method\":\"Cash\",\"sale_id\":2}','http://127.0.0.1:8000/sales','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-01 05:54:11','2026-10-01 05:54:11'),(9,'App\\Models\\User',1,'created','App\\Models\\VatSetting',1,'[]','{\"name\":\"Standard VAT\",\"rate\":12,\"is_inclusive\":true,\"is_active\":true,\"id\":1}','http://127.0.0.1:8000/vat','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-01 05:54:43','2026-10-01 05:54:43'),(10,'App\\Models\\User',1,'created','App\\Models\\Discount',1,'[]','{\"name\":\"Senior\\/PWD\",\"type\":\"percentage\",\"value\":\"20\",\"is_active\":true,\"id\":1}','http://127.0.0.1:8000/discounts','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-01 05:58:23','2026-10-01 05:58:23'),(11,'App\\Models\\User',1,'created','App\\Models\\Sale',3,'[]','{\"order_number\":\"ORD-202610-0003\",\"sale_date\":\"2026-10-01 13:58:41\",\"subtotal\":455,\"vat_amount\":40.04,\"discount_type\":null,\"discount_amount\":81.25,\"total_amount\":373.75,\"order_channel\":\"Dine-in\",\"payment_method\":\"Cash\",\"sale_id\":3}','http://127.0.0.1:8000/sales','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-01 05:58:41','2026-10-01 05:58:41'),(12,'App\\Models\\User',1,'created','App\\Models\\Sale',4,'[]','{\"order_number\":\"ORD-202610-0004\",\"sale_date\":\"2026-10-01 14:21:32\",\"subtotal\":720,\"vat_amount\":77.14,\"discount_type\":null,\"discount_amount\":0,\"total_amount\":720,\"order_channel\":\"Dine-in\",\"payment_method\":\"Cash\",\"sale_id\":4}','http://127.0.0.1:8000/sales','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-01 06:21:32','2026-10-01 06:21:32'),(13,'App\\Models\\User',1,'updated','App\\Models\\Ingredient',1,'{\"quantity\":\"171.00\",\"pieces_per_box\":0,\"max_capacity\":\"100.00\",\"reorder_level\":\"5.00\"}','{\"quantity\":\"171\",\"pieces_per_box\":\"10\",\"max_capacity\":\"100\",\"reorder_level\":\"5\"}','http://127.0.0.1:8000/ingredients/1','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-06 01:26:41','2026-10-06 01:26:41'),(14,'App\\Models\\User',1,'updated','App\\Models\\Ingredient',2,'{\"quantity\":\"30.50\",\"pieces_per_box\":0,\"max_capacity\":\"50.00\",\"reorder_level\":\"25.00\"}','{\"quantity\":\"30.5\",\"pieces_per_box\":\"10\",\"max_capacity\":\"50\",\"reorder_level\":\"25\"}','http://127.0.0.1:8000/ingredients/2','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-06 01:27:00','2026-10-06 01:27:00'),(15,'App\\Models\\User',1,'updated','App\\Models\\Ingredient',1,'{\"quantity\":\"171.00\",\"max_capacity\":\"100.00\",\"reorder_level\":\"5.00\"}','{\"quantity\":\"171\",\"max_capacity\":\"100\",\"reorder_level\":\"5\"}','http://127.0.0.1:8000/ingredients/1','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-06 01:28:18','2026-10-06 01:28:18'),(16,'App\\Models\\User',1,'updated','App\\Models\\Ingredient',3,'{\"quantity\":\"15.00\",\"pieces_per_box\":0,\"max_capacity\":\"20.00\",\"reorder_level\":\"10.00\"}','{\"quantity\":\"15\",\"pieces_per_box\":\"10\",\"max_capacity\":\"20\",\"reorder_level\":\"10\"}','http://127.0.0.1:8000/ingredients/3','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-06 01:28:25','2026-10-06 01:28:25'),(19,'App\\Models\\User',1,'created','App\\Models\\Sale',7,'[]','{\"order_number\":\"ORD-202610-0005\",\"sale_date\":\"2026-10-06 09:48:00\",\"subtotal\":360,\"vat_amount\":38.57,\"discount_type\":null,\"discount_amount\":0,\"total_amount\":360,\"order_channel\":\"Take-out\",\"payment_method\":\"Cash\",\"sale_id\":7}','http://127.0.0.1:8000/sales','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-06 01:48:00','2026-10-06 01:48:00'),(20,'App\\Models\\User',1,'updated','App\\Models\\Ingredient',2,'{\"quantity\":\"30.50\"}','{\"quantity\":10.5}','http://127.0.0.1:8000/sales','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-06 01:48:00','2026-10-06 01:48:00'),(24,'App\\Models\\User',1,'created','App\\Models\\Sale',11,'[]','{\"order_number\":\"ORD-202610-0006\",\"sale_date\":\"2026-10-06 09:57:17\",\"subtotal\":665,\"vat_amount\":71.25,\"discount_type\":null,\"discount_amount\":0,\"total_amount\":665,\"order_channel\":\"Dine-in\",\"payment_method\":\"Cash\",\"sale_id\":11}','http://127.0.0.1:8000/sales','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-06 01:57:17','2026-10-06 01:57:17'),(25,'App\\Models\\User',1,'updated','App\\Models\\Ingredient',1,'{\"quantity\":\"171.00\"}','{\"quantity\":101}','http://127.0.0.1:8000/sales','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-06 01:57:17','2026-10-06 01:57:17'),(27,'App\\Models\\User',1,'created','App\\Models\\Sale',13,'[]','{\"order_number\":\"ORD-202610-0007\",\"sale_date\":\"2026-10-06 10:18:26\",\"subtotal\":190,\"vat_amount\":20.36,\"discount_type\":null,\"discount_amount\":0,\"total_amount\":190,\"order_channel\":\"Dine-in\",\"payment_method\":\"Cash\",\"sale_id\":13}','http://127.0.0.1:8000/sales','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-06 02:18:27','2026-10-06 02:18:27'),(28,'App\\Models\\User',1,'updated','App\\Models\\Ingredient',1,'{\"quantity\":\"101.00\"}','{\"quantity\":81}','http://127.0.0.1:8000/sales','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-06 02:18:28','2026-10-06 02:18:28'),(29,'App\\Models\\User',1,'created','App\\Models\\Sale',14,'[]','{\"order_number\":\"ORD-202610-0008\",\"sale_date\":\"2026-10-06 10:18:41\",\"subtotal\":190,\"vat_amount\":20.36,\"discount_type\":null,\"discount_amount\":0,\"total_amount\":190,\"order_channel\":\"Dine-in\",\"payment_method\":\"Cash\",\"sale_id\":14}','http://127.0.0.1:8000/sales','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-06 02:18:41','2026-10-06 02:18:41'),(30,'App\\Models\\User',1,'updated','App\\Models\\Ingredient',1,'{\"quantity\":\"81.00\"}','{\"quantity\":61}','http://127.0.0.1:8000/sales','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-06 02:18:42','2026-10-06 02:18:42'),(31,'App\\Models\\User',1,'created','App\\Models\\Sale',15,'[]','{\"order_number\":\"ORD-202610-0009\",\"sale_date\":\"2026-10-06 10:33:15\",\"subtotal\":750,\"vat_amount\":80.36,\"discount_type\":null,\"discount_amount\":0,\"total_amount\":750,\"order_channel\":\"Dine-in\",\"payment_method\":\"Cash\",\"sale_id\":15}','http://127.0.0.1:8000/sales','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-06 02:33:15','2026-10-06 02:33:15'),(32,'App\\Models\\User',1,'updated','App\\Models\\Ingredient',2,'{\"quantity\":\"10.50\"}','{\"quantity\":0.5}','http://127.0.0.1:8000/sales','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-06 02:33:15','2026-10-06 02:33:15'),(33,'App\\Models\\User',1,'updated','App\\Models\\Ingredient',1,'{\"quantity\":\"61.00\"}','{\"quantity\":1}','http://127.0.0.1:8000/sales','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-06 02:33:15','2026-10-06 02:33:15'),(34,'App\\Models\\User',1,'updated','App\\Models\\Ingredient',1,'{\"quantity\":\"1.00\",\"max_capacity\":\"100.00\",\"reorder_level\":\"5.00\"}','{\"quantity\":\"1\",\"max_capacity\":\"100\",\"reorder_level\":\"5\"}','http://127.0.0.1:8000/ingredients/1','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-06 02:41:26','2026-10-06 02:41:26'),(35,'App\\Models\\User',1,'updated','App\\Models\\Ingredient',2,'{\"quantity\":\"0.50\",\"max_capacity\":\"50.00\",\"reorder_level\":\"25.00\"}','{\"quantity\":\"0.5\",\"max_capacity\":\"50\",\"reorder_level\":\"25\"}','http://127.0.0.1:8000/ingredients/2','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-06 02:41:29','2026-10-06 02:41:29'),(36,'App\\Models\\User',1,'updated','App\\Models\\Ingredient',1,'{\"quantity\":\"1.00\",\"max_capacity\":\"100.00\",\"reorder_level\":\"5.00\"}','{\"quantity\":\"100\",\"max_capacity\":\"100\",\"reorder_level\":\"5\"}','http://127.0.0.1:8000/ingredients/1','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-06 02:43:00','2026-10-06 02:43:00'),(37,'App\\Models\\User',1,'updated','App\\Models\\Ingredient',2,'{\"quantity\":\"0.50\",\"max_capacity\":\"50.00\",\"reorder_level\":\"25.00\"}','{\"quantity\":\"99.5\",\"max_capacity\":\"50\",\"reorder_level\":\"25\"}','http://127.0.0.1:8000/ingredients/2','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-06 02:43:42','2026-10-06 02:43:42'),(38,'App\\Models\\User',1,'created','App\\Models\\Sale',16,'[]','{\"order_number\":\"ORD-202610-0010\",\"sale_date\":\"2026-10-06 10:44:15\",\"subtotal\":1005,\"vat_amount\":107.68,\"discount_type\":null,\"discount_amount\":0,\"total_amount\":1005,\"order_channel\":\"Dine-in\",\"payment_method\":\"Cash\",\"sale_id\":16}','http://127.0.0.1:8000/sales','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-06 02:44:15','2026-10-06 02:44:15'),(39,'App\\Models\\User',1,'updated','App\\Models\\Ingredient',1,'{\"quantity\":\"100.00\"}','{\"quantity\":70}','http://127.0.0.1:8000/sales','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-06 02:44:15','2026-10-06 02:44:15'),(40,'App\\Models\\User',1,'updated','App\\Models\\Ingredient',2,'{\"quantity\":\"99.50\"}','{\"quantity\":59.5}','http://127.0.0.1:8000/sales','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-06 02:44:15','2026-10-06 02:44:15'),(41,'App\\Models\\User',1,'updated','App\\Models\\Ingredient',1,'{\"quantity\":\"70.00\",\"max_capacity\":\"100.00\",\"reorder_level\":\"5.00\"}','{\"quantity\":\"70\",\"max_capacity\":\"100\",\"reorder_level\":\"5\"}','http://127.0.0.1:8000/ingredients/1','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-06 02:57:23','2026-10-06 02:57:23'),(42,'App\\Models\\User',1,'updated','App\\Models\\Ingredient',1,'{\"quantity\":\"70.00\",\"max_capacity\":\"100.00\",\"reorder_level\":\"5.00\"}','{\"quantity\":\"70\",\"max_capacity\":\"100\",\"reorder_level\":\"5\"}','http://127.0.0.1:8000/ingredients/1','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-06 02:57:28','2026-10-06 02:57:28'),(43,'App\\Models\\User',1,'created','App\\Models\\Sale',17,'[]','{\"order_number\":\"ORD-202610-0011\",\"sale_date\":\"2026-10-06 10:57:41\",\"subtotal\":465,\"vat_amount\":49.82,\"discount_type\":null,\"discount_amount\":0,\"total_amount\":465,\"order_channel\":\"Dine-in\",\"payment_method\":\"Cash\",\"sale_id\":17}','http://127.0.0.1:8000/sales','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-06 02:57:41','2026-10-06 02:57:41'),(44,'App\\Models\\User',1,'updated','App\\Models\\Ingredient',1,'{\"quantity\":\"70.00\"}','{\"quantity\":40}','http://127.0.0.1:8000/sales','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-06 02:57:41','2026-10-06 02:57:41'),(45,'App\\Models\\User',1,'updated','App\\Models\\Ingredient',2,'{\"quantity\":\"59.50\"}','{\"quantity\":49.5}','http://127.0.0.1:8000/sales','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-06 02:57:41','2026-10-06 02:57:41'),(46,'App\\Models\\User',1,'deleted','App\\Models\\User',2,'{\"users_id\":2,\"username\":\"cashier_01\",\"password\":\"$2y$12$IuIAWRO2MQxD0q\\/1CU92y.8RSAmpoz314Flze.HIOTyBL00JYL1dK\",\"role\":\"Cashier\",\"contact_number\":\"09987654321\",\"remember_token\":null}','[]','http://127.0.0.1:8000/admin/users/2','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-06 22:45:05','2026-10-06 22:45:05'),(47,'App\\Models\\User',1,'created','App\\Models\\User',3,'[]','{\"username\":\"shayna majuka\",\"password\":\"$2y$12$K5yjImK8TOSL1MTfkvLi8.2HZqpxkenxMREiVFJZ5HJOHSoTbMVQi\",\"role\":\"Cashier\",\"contact_number\":\"09123456789\",\"users_id\":3}','http://127.0.0.1:8000/admin/users','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-06 22:47:05','2026-10-06 22:47:05'),(48,'App\\Models\\User',1,'deleted','App\\Models\\User',3,'{\"users_id\":3,\"username\":\"shayna majuka\",\"password\":\"$2y$12$K5yjImK8TOSL1MTfkvLi8.2HZqpxkenxMREiVFJZ5HJOHSoTbMVQi\",\"role\":\"Cashier\",\"contact_number\":\"09123456789\",\"remember_token\":null}','[]','http://127.0.0.1:8000/admin/users/3','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-06 22:53:08','2026-10-06 22:53:08'),(49,'App\\Models\\User',1,'created','App\\Models\\User',4,'[]','{\"username\":\"shayna majuka\",\"password\":\"$2y$12$PbgjfMulltv.mAQu0PM6K.x731Q8WDqLQALWscdBBrsYNVQvpy21m\",\"role\":\"Cashier\",\"contact_number\":\"09876554234\",\"users_id\":4}','http://127.0.0.1:8000/admin/users','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-06 22:53:49','2026-10-06 22:53:49'),(50,'App\\Models\\User',1,'created','App\\Models\\User',5,'[]','{\"username\":\"dekster oranting\",\"password\":\"$2y$12$SmYNStE6SPmwg8IPZ7J60Ol3wwJWCc0iSvnfrmvsvATfC\\/apf7eZe\",\"role\":\"Staff\",\"contact_number\":\"09312832163\",\"users_id\":5}','http://127.0.0.1:8000/admin/users','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-06 22:54:16','2026-10-06 22:54:16'),(51,'App\\Models\\User',1,'updated','App\\Models\\Ingredient',2,'{\"quantity\":\"49.50\"}','{\"quantity\":149.5}','http://127.0.0.1:8000/purchases','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-07 05:36:27','2026-10-07 05:36:27'),(52,'App\\Models\\User',1,'updated','App\\Models\\Ingredient',2,'{\"quantity\":\"149.50\",\"pieces_per_box\":10,\"max_capacity\":\"50.00\",\"reorder_level\":\"25.00\"}','{\"quantity\":\"150.5\",\"pieces_per_box\":\"20\",\"max_capacity\":\"50\",\"reorder_level\":\"25\"}','http://127.0.0.1:8000/ingredients/2','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-07 05:38:23','2026-10-07 05:38:23'),(53,'App\\Models\\User',1,'updated','App\\Models\\Ingredient',1,'{\"quantity\":\"40.00\"}','{\"quantity\":140}','http://127.0.0.1:8000/purchases','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-07 05:39:09','2026-10-07 05:39:09'),(54,'App\\Models\\User',1,'updated','App\\Models\\Ingredient',1,'{\"quantity\":\"140.00\",\"pieces_per_box\":10,\"max_capacity\":\"100.00\",\"reorder_level\":\"5.00\"}','{\"quantity\":\"140\",\"pieces_per_box\":\"50\",\"max_capacity\":\"100\",\"reorder_level\":\"50\"}','http://127.0.0.1:8000/ingredients/1','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-07 05:39:20','2026-10-07 05:39:20'),(55,'App\\Models\\User',1,'created','App\\Models\\Sale',18,'[]','{\"order_number\":\"ORD-202610-0012\",\"sale_date\":\"2026-10-07 14:08:06\",\"subtotal\":275,\"vat_amount\":29.46,\"discount_type\":null,\"discount_amount\":0,\"total_amount\":275,\"order_channel\":\"Dine-in\",\"payment_method\":\"Cash\",\"sale_id\":18}','http://127.0.0.1:8000/sales','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-07 06:08:06','2026-10-07 06:08:06'),(56,'App\\Models\\User',1,'updated','App\\Models\\Ingredient',1,'{\"quantity\":\"140.00\"}','{\"quantity\":138}','http://127.0.0.1:8000/sales','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-07 06:08:06','2026-10-07 06:08:06'),(57,'App\\Models\\User',1,'updated','App\\Models\\Ingredient',2,'{\"quantity\":\"150.50\"}','{\"quantity\":145.5}','http://127.0.0.1:8000/sales','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-07 06:08:06','2026-10-07 06:08:06'),(58,'App\\Models\\User',1,'deleted','App\\Models\\User',4,'{\"users_id\":4,\"username\":\"shayna majuka\",\"password\":\"$2y$12$PbgjfMulltv.mAQu0PM6K.x731Q8WDqLQALWscdBBrsYNVQvpy21m\",\"role\":\"Cashier\",\"contact_number\":\"09876554234\",\"remember_token\":null}','[]','http://127.0.0.1:8000/admin/users/4','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-07 06:09:44','2026-10-07 06:09:44'),(59,'App\\Models\\User',1,'deleted','App\\Models\\User',5,'{\"users_id\":5,\"username\":\"dekster oranting\",\"password\":\"$2y$12$SmYNStE6SPmwg8IPZ7J60Ol3wwJWCc0iSvnfrmvsvATfC\\/apf7eZe\",\"role\":\"Staff\",\"contact_number\":\"09312832163\",\"remember_token\":null}','[]','http://127.0.0.1:8000/admin/users/5','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-07 06:09:46','2026-10-07 06:09:46'),(60,'App\\Models\\User',1,'updated','App\\Models\\User',1,'{\"username\":\"admin_owner\"}','{\"username\":\"ADMIN_OWNER\"}','http://127.0.0.1:8000/admin/users/1','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-07 06:32:47','2026-10-07 06:32:47'),(61,'App\\Models\\User',1,'updated','App\\Models\\User',1,'{\"username\":\"ADMIN_OWNER\"}','{\"username\":\"OWNER\"}','http://127.0.0.1:8000/admin/users/1','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-07 06:33:03','2026-10-07 06:33:03'),(62,'App\\Models\\User',1,'updated','App\\Models\\User',1,'{\"username\":\"OWNER\"}','{\"username\":\"ADMIN\\/OWNER\"}','http://127.0.0.1:8000/admin/users/1','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-07 06:33:16','2026-10-07 06:33:16'),(63,'App\\Models\\User',1,'created','App\\Models\\User',6,'[]','{\"username\":\"TEST CASHIER\",\"password\":\"$2y$12$E7dndFWEb7sMzqzj5r2MXO\\/cTmVxvTFJY707ice5WBfT.51OeqJkC\",\"role\":\"Cashier\",\"contact_number\":null,\"users_id\":6}','http://127.0.0.1:8000/admin/users','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-07 06:39:28','2026-10-07 06:39:28'),(64,'App\\Models\\User',1,'updated','App\\Models\\User',6,'{\"contact_number\":null}','{\"contact_number\":\"09236213521\"}','http://127.0.0.1:8000/admin/users/6','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-07 06:39:34','2026-10-07 06:39:34'),(65,'App\\Models\\User',1,'created','App\\Models\\User',7,'[]','{\"username\":\"TEST STAFF\",\"password\":\"$2y$12$7Mk1LzoUF3ribp3WWZT6YO62vEeple5t649rMwOL13aKivnP7ze9.\",\"role\":\"Staff\",\"contact_number\":\"09123456323\",\"users_id\":7}','http://127.0.0.1:8000/admin/users','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-07 06:39:54','2026-10-07 06:39:54'),(66,'App\\Models\\User',6,'created','App\\Models\\Sale',19,'[]','{\"order_number\":\"ORD-202610-0013\",\"sale_date\":\"2026-10-07 14:58:42\",\"subtotal\":370,\"vat_amount\":39.64,\"discount_type\":null,\"discount_amount\":0,\"total_amount\":370,\"order_channel\":\"Dine-in\",\"payment_method\":\"Cash\",\"sale_id\":19}','http://127.0.0.1:8000/sales','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-07 06:58:42','2026-10-07 06:58:42'),(67,'App\\Models\\User',6,'updated','App\\Models\\Ingredient',1,'{\"quantity\":\"138.00\"}','{\"quantity\":134}','http://127.0.0.1:8000/sales','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-07 06:58:42','2026-10-07 06:58:42'),(68,'App\\Models\\User',6,'updated','App\\Models\\Ingredient',2,'{\"quantity\":\"145.50\"}','{\"quantity\":140.5}','http://127.0.0.1:8000/sales','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-07 06:58:42','2026-10-07 06:58:42'),(75,'App\\Models\\User',6,'created','App\\Models\\Sale',22,'[]','{\"order_number\":\"ORD-202610-0014\",\"sale_date\":\"2026-10-08 05:08:17\",\"subtotal\":370,\"vat_amount\":39.64,\"discount_type\":null,\"discount_amount\":0,\"total_amount\":370,\"order_channel\":\"Dine-in\",\"payment_method\":\"Cash\",\"sale_id\":22}','http://127.0.0.1:8000/sales','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-07 21:08:17','2026-10-07 21:08:17'),(76,'App\\Models\\User',6,'updated','App\\Models\\Ingredient',1,'{\"quantity\":\"134.00\"}','{\"quantity\":130}','http://127.0.0.1:8000/sales','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-07 21:08:17','2026-10-07 21:08:17'),(77,'App\\Models\\User',6,'updated','App\\Models\\Ingredient',2,'{\"quantity\":\"140.50\"}','{\"quantity\":135.5}','http://127.0.0.1:8000/sales','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-07 21:08:18','2026-10-07 21:08:18'),(78,'App\\Models\\User',1,'updated','App\\Models\\User',7,'{\"password\":\"$2y$12$7Mk1LzoUF3ribp3WWZT6YO62vEeple5t649rMwOL13aKivnP7ze9.\"}','{\"password\":\"$2y$12$w7H9XmAMs5X\\/gmbDmDjLK.\\/9SjaCJgqrynfbhLXoC074.6xwZKPay\"}','http://127.0.0.1:8000/admin/users/7','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-07 21:12:57','2026-10-07 21:12:57'),(79,'App\\Models\\User',6,'created','App\\Models\\Sale',23,'[]','{\"order_number\":\"ORD-202610-0015\",\"sale_date\":\"2026-10-08 06:24:27\",\"subtotal\":455,\"vat_amount\":48.75,\"discount_type\":null,\"discount_amount\":0,\"total_amount\":455,\"order_channel\":\"Dine-in\",\"payment_method\":\"Cash\",\"sale_id\":23}','http://127.0.0.1:8000/sales','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-07 22:24:27','2026-10-07 22:24:27'),(80,'App\\Models\\User',6,'updated','App\\Models\\Ingredient',1,'{\"quantity\":\"30.00\"}','{\"quantity\":28}','http://127.0.0.1:8000/sales','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-07 22:24:27','2026-10-07 22:24:27'),(81,'App\\Models\\User',6,'updated','App\\Models\\Ingredient',2,'{\"quantity\":\"135.50\"}','{\"quantity\":125.5}','http://127.0.0.1:8000/sales','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-07 22:24:27','2026-10-07 22:24:27');
/*!40000 ALTER TABLE `audits` ENABLE KEYS */;
UNLOCK TABLES;

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
INSERT INTO `cache` VALUES ('laravel-cache-admin_owner|127.0.0.1','i:1;',1794490869),('laravel-cache-admin_owner|127.0.0.1:timer','i:1794490869;',1794490869);
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
  `category_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `category_name` varchar(255) NOT NULL,
  `is_discountable` tinyint(1) NOT NULL DEFAULT 1,
  `has_size_small` tinyint(1) NOT NULL DEFAULT 0,
  `has_size_medium` tinyint(1) NOT NULL DEFAULT 0,
  `has_size_large` tinyint(1) NOT NULL DEFAULT 0,
  `has_sugar_level` tinyint(1) NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`category_id`),
  UNIQUE KEY `categories_category_name_unique` (`category_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `discounts`
--

DROP TABLE IF EXISTS `discounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `discounts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` enum('percentage','fixed') NOT NULL,
  `value` decimal(8,2) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `discounts`
--

LOCK TABLES `discounts` WRITE;
/*!40000 ALTER TABLE `discounts` DISABLE KEYS */;
INSERT INTO `discounts` VALUES (1,'Senior/PWD','percentage',20.00,1,'2026-10-01 05:58:23','2026-10-01 05:58:23');
/*!40000 ALTER TABLE `discounts` ENABLE KEYS */;
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
-- Table structure for table `ingredients`
--

DROP TABLE IF EXISTS `ingredients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ingredients` (
  `ingredient_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ingredient_name` varchar(255) NOT NULL,
  `quantity` decimal(10,2) NOT NULL DEFAULT 0.00,
  `pieces_per_box` int(11) NOT NULL DEFAULT 0,
  `unit` varchar(255) NOT NULL,
  `max_capacity` decimal(10,2) NOT NULL,
  `reorder_level` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `total_pieces` int(11) NOT NULL,
  PRIMARY KEY (`ingredient_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ingredients`
--

LOCK TABLES `ingredients` WRITE;
/*!40000 ALTER TABLE `ingredients` DISABLE KEYS */;
INSERT INTO `ingredients` VALUES (1,'Sugar',28.00,50,'kg',100.00,50.00,'2026-09-30 06:26:49','2026-10-07 22:24:27',0),(2,'Raw Chicken Wings',125.50,20,'kg',50.00,25.00,'2026-09-30 06:36:31','2026-10-07 22:24:27',0),(3,'Frying Oil',15.00,10,'liters',20.00,10.00,'2026-09-30 06:36:31','2026-10-06 01:28:25',0);
/*!40000 ALTER TABLE `ingredients` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_07_14_000000_create_categories_table',1),(5,'2026_07_15_041111_create_products_table',1),(6,'2026_07_15_043447_create_ingredients_table',1),(7,'2026_07_17_044805_create_product_ingredients_table',1),(8,'2026_07_23_145126_create_sales_table',1),(9,'2026_07_23_145131_create_sale_details_table',1),(10,'2026_07_24_152244_create_wastages_table',1),(11,'2026_08_04_125432_create_suppliers_table',1),(12,'2026_08_04_125433_create_vat_settings_table',1),(13,'2026_08_04_125434_create_discounts_table',1),(14,'2026_08_22_102329_create_orders_table',1),(15,'2026_08_22_102330_create_order_items_table',1),(16,'2026_08_23_140240_add_order_number_to_sales_table',1),(17,'2026_08_24_094743_add_image_to_products_table',1),(18,'2026_08_27_090047_add_subtotal_and_vat_to_sales_table',1),(19,'2026_09_18_070857_create_audits_table',1),(20,'2026_09_27_034150_add_options_to_categories_table',1),(21,'2026_09_28_031035_add_total_pieces_to_ingredients_table',1),(22,'2026_09_29_140825_create_purchases_table',1),(23,'2026_10_06_090046_add_pieces_per_box_to_ingredients_table',2);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_items` (
  `order_item_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(8,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`order_item_id`),
  KEY `order_items_order_id_foreign` (`order_id`),
  KEY `order_items_product_id_foreign` (`product_id`),
  CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders` (
  `order_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_number` varchar(255) NOT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `table_number` varchar(255) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('held','completed','cancelled') NOT NULL DEFAULT 'held',
  `kot_status` enum('pending','preparing','ready','served') NOT NULL DEFAULT 'pending',
  `payment_status` enum('unpaid','paid') NOT NULL DEFAULT 'unpaid',
  `payment_method` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`order_id`),
  UNIQUE KEY `orders_order_number_unique` (`order_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
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
-- Table structure for table `product_ingredients`
--

DROP TABLE IF EXISTS `product_ingredients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_ingredients` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL,
  `ingredient_id` bigint(20) unsigned NOT NULL,
  `quantity_needed` decimal(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_ingredients_product_id_foreign` (`product_id`),
  KEY `product_ingredients_ingredient_id_foreign` (`ingredient_id`),
  CONSTRAINT `product_ingredients_ingredient_id_foreign` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients` (`ingredient_id`) ON DELETE CASCADE,
  CONSTRAINT `product_ingredients_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_ingredients`
--

LOCK TABLES `product_ingredients` WRITE;
/*!40000 ALTER TABLE `product_ingredients` DISABLE KEYS */;
INSERT INTO `product_ingredients` VALUES (1,1,2,100.00,NULL,NULL),(2,2,1,100.00,NULL,NULL);
/*!40000 ALTER TABLE `product_ingredients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `product_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_name` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `price` decimal(8,2) NOT NULL,
  `stock_quantity` int(11) NOT NULL DEFAULT 0,
  `status` varchar(255) NOT NULL DEFAULT 'Available',
  `category_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`product_id`),
  KEY `products_category_id_foreign` (`category_id`),
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,'Classic Buffalo Wings (6 pcs)',NULL,180.00,50,'Available',NULL,'2026-09-30 06:36:31','2026-09-30 06:36:31'),(2,'Classic Milk Tea (Large)',NULL,95.00,100,'Available',NULL,'2026-09-30 06:36:31','2026-09-30 06:36:31');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchases`
--

DROP TABLE IF EXISTS `purchases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `purchases` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` bigint(20) unsigned NOT NULL,
  `ingredient_id` bigint(20) unsigned NOT NULL,
  `quantity_received` int(11) NOT NULL,
  `unit_cost` decimal(10,2) NOT NULL,
  `total_cost` decimal(10,2) NOT NULL,
  `purchase_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchases_supplier_id_foreign` (`supplier_id`),
  KEY `purchases_ingredient_id_foreign` (`ingredient_id`),
  CONSTRAINT `purchases_ingredient_id_foreign` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients` (`ingredient_id`) ON DELETE CASCADE,
  CONSTRAINT `purchases_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchases`
--

LOCK TABLES `purchases` WRITE;
/*!40000 ALTER TABLE `purchases` DISABLE KEYS */;
INSERT INTO `purchases` VALUES (1,1,1,5,15.00,75.00,'2026-09-30','2026-09-30 06:28:09','2026-09-30 06:28:09'),(2,1,1,4,10.00,40.00,'2026-09-30','2026-09-30 06:44:06','2026-09-30 06:44:06'),(3,1,2,10,100.00,1000.00,'2026-09-30','2026-09-30 06:52:00','2026-09-30 06:52:00'),(4,1,1,100,100.00,10000.00,'2026-09-30','2026-09-30 06:53:23','2026-09-30 06:53:23'),(5,1,1,28,100.00,2800.00,'2026-10-01','2026-10-01 05:51:28','2026-10-01 05:51:28'),(6,1,1,20,100.00,2000.00,'2026-10-01','2026-10-01 05:51:54','2026-10-01 05:51:54'),(7,1,1,4,100.00,400.00,'2026-10-01','2026-10-01 05:52:14','2026-10-01 05:52:14'),(8,1,2,100,150.00,15000.00,'2026-10-07','2026-10-07 05:36:26','2026-10-07 05:36:26'),(9,1,1,100,150.00,15000.00,'2026-10-07','2026-10-07 05:39:09','2026-10-07 05:39:09');
/*!40000 ALTER TABLE `purchases` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sale_details`
--

DROP TABLE IF EXISTS `sale_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sale_details` (
  `sale_detail_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sale_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `quantity` int(11) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`sale_detail_id`),
  KEY `sale_details_sale_id_foreign` (`sale_id`),
  KEY `sale_details_product_id_foreign` (`product_id`),
  CONSTRAINT `sale_details_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE,
  CONSTRAINT `sale_details_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`sale_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sale_details`
--

LOCK TABLES `sale_details` WRITE;
/*!40000 ALTER TABLE `sale_details` DISABLE KEYS */;
INSERT INTO `sale_details` VALUES (1,1,1,3,540.00,'2026-10-01 05:53:23','2026-10-01 05:53:23'),(2,2,1,13,2340.00,'2026-10-01 05:54:11','2026-10-01 05:54:11'),(3,3,2,1,95.00,'2026-10-01 05:58:41','2026-10-01 05:58:41'),(4,3,1,2,360.00,'2026-10-01 05:58:41','2026-10-01 05:58:41'),(5,4,1,4,720.00,'2026-10-01 06:21:32','2026-10-01 06:21:32'),(6,7,1,2,360.00,'2026-10-06 01:48:00','2026-10-06 01:48:00'),(7,11,2,7,665.00,'2026-10-06 01:57:17','2026-10-06 01:57:17'),(8,13,2,2,190.00,'2026-10-06 02:18:28','2026-10-06 02:18:28'),(9,14,2,2,190.00,'2026-10-06 02:18:42','2026-10-06 02:18:42'),(10,15,1,1,180.00,'2026-10-06 02:33:15','2026-10-06 02:33:15'),(11,15,2,6,570.00,'2026-10-06 02:33:15','2026-10-06 02:33:15'),(12,16,2,3,285.00,'2026-10-06 02:44:15','2026-10-06 02:44:15'),(13,16,1,4,720.00,'2026-10-06 02:44:15','2026-10-06 02:44:15'),(14,17,2,3,285.00,'2026-10-06 02:57:41','2026-10-06 02:57:41'),(15,17,1,1,180.00,'2026-10-06 02:57:41','2026-10-06 02:57:41'),(16,18,2,1,95.00,'2026-10-07 06:08:06','2026-10-07 06:08:06'),(17,18,1,1,180.00,'2026-10-07 06:08:06','2026-10-07 06:08:06'),(18,19,2,2,190.00,'2026-10-07 06:58:42','2026-10-07 06:58:42'),(19,19,1,1,180.00,'2026-10-07 06:58:42','2026-10-07 06:58:42'),(24,22,2,2,190.00,'2026-10-07 21:08:17','2026-10-07 21:08:17'),(25,22,1,1,180.00,'2026-10-07 21:08:17','2026-10-07 21:08:17'),(26,23,2,1,95.00,'2026-10-07 22:24:27','2026-10-07 22:24:27'),(27,23,1,2,360.00,'2026-10-07 22:24:27','2026-10-07 22:24:27');
/*!40000 ALTER TABLE `sale_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales`
--

DROP TABLE IF EXISTS `sales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sales` (
  `sale_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_number` varchar(255) DEFAULT NULL,
  `sale_date` datetime NOT NULL,
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `vat_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL,
  `discount_type` varchar(255) DEFAULT NULL,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_method` varchar(255) DEFAULT NULL,
  `order_channel` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`sale_id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales`
--

LOCK TABLES `sales` WRITE;
/*!40000 ALTER TABLE `sales` DISABLE KEYS */;
INSERT INTO `sales` VALUES (1,'ORD-202610-0001','2026-10-01 13:53:23',540.00,57.86,540.00,NULL,0.00,'Cash','Dine-in','2026-10-01 05:53:23','2026-10-01 05:53:23'),(2,'ORD-202610-0002','2026-10-01 13:54:11',2340.00,250.71,2340.00,NULL,0.00,'Cash','Dine-in','2026-10-01 05:54:11','2026-10-01 05:54:11'),(3,'ORD-202610-0003','2026-10-01 13:58:41',455.00,40.04,373.75,NULL,81.25,'Cash','Dine-in','2026-10-01 05:58:41','2026-10-01 05:58:41'),(4,'ORD-202610-0004','2026-10-01 14:21:32',720.00,77.14,720.00,NULL,0.00,'Cash','Dine-in','2026-10-01 06:21:32','2026-10-01 06:21:32'),(7,'ORD-202610-0005','2026-10-06 09:48:00',360.00,38.57,360.00,NULL,0.00,'Cash','Take-out','2026-10-06 01:48:00','2026-10-06 01:48:00'),(11,'ORD-202610-0006','2026-10-06 09:57:17',665.00,71.25,665.00,NULL,0.00,'Cash','Dine-in','2026-10-06 01:57:17','2026-10-06 01:57:17'),(13,'ORD-202610-0007','2026-10-06 10:18:26',190.00,20.36,190.00,NULL,0.00,'Cash','Dine-in','2026-10-06 02:18:26','2026-10-06 02:18:26'),(14,'ORD-202610-0008','2026-10-06 10:18:41',190.00,20.36,190.00,NULL,0.00,'Cash','Dine-in','2026-10-06 02:18:41','2026-10-06 02:18:41'),(15,'ORD-202610-0009','2026-10-06 10:33:15',750.00,80.36,750.00,NULL,0.00,'Cash','Dine-in','2026-10-06 02:33:15','2026-10-06 02:33:15'),(16,'ORD-202610-0010','2026-10-06 10:44:15',1005.00,107.68,1005.00,NULL,0.00,'Cash','Dine-in','2026-10-06 02:44:15','2026-10-06 02:44:15'),(17,'ORD-202610-0011','2026-10-06 10:57:41',465.00,49.82,465.00,NULL,0.00,'Cash','Dine-in','2026-10-06 02:57:41','2026-10-06 02:57:41'),(18,'ORD-202610-0012','2026-10-07 14:08:06',275.00,29.46,275.00,NULL,0.00,'Cash','Dine-in','2026-10-07 06:08:06','2026-10-07 06:08:06'),(19,'ORD-202610-0013','2026-10-07 14:58:42',370.00,39.64,370.00,NULL,0.00,'Cash','Dine-in','2026-10-07 06:58:42','2026-10-07 06:58:42'),(22,'ORD-202610-0014','2026-10-08 05:08:17',370.00,39.64,370.00,NULL,0.00,'Cash','Dine-in','2026-10-07 21:08:17','2026-10-07 21:08:17'),(23,'ORD-202610-0015','2026-10-08 06:24:27',455.00,48.75,455.00,NULL,0.00,'Cash','Dine-in','2026-10-07 22:24:27','2026-10-07 22:24:27');
/*!40000 ALTER TABLE `sales` ENABLE KEYS */;
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
INSERT INTO `sessions` VALUES ('2uBu1S2pJCimPJ58vpA2RiAbyAItsacFDp0v1LRq',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.141.0 Chrome/150.0.7871.250 Electron/43.7.7 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiaEJlajJQZmdQNVF5aFdDSm12MDJVZnVDaWFQNlNOQlZ1V3piSU9DdSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fX0=',1791440175),('gJQsxE3gRYWTdFIblKZj7eRb4uFu9ygs09qZmrNH',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiNXY3dWQ1TFlPbHJaNDZuaVFNSEFuMWhYcHBOTzN3UENrTjhMbFBsUyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fX0=',1791440671),('q9Id5U98DVAUgK6dlejNo6531B4zVQ0VoDjYp6ng',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoibWlNWXF4cUl0azlUa0NidmdkeWM2dnoyMzZuMnd5UlJiV09QVWlzUSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1794490810),('SQ4CYQOvbjXXkivRX2jjh37QKn53FxRlVjJ59V4D',7,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','YTo1OntzOjY6Il90b2tlbiI7czo0MDoiRG5vRGY5YWticmhTajZUekJ1OVRtZTFIRmNDOGNmSVRHSFhHU0ppeSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MTp7aTowO3M6Nzoic3VjY2VzcyI7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjc7czo3OiJzdWNjZXNzIjtzOjMzOiJLaXRjaGVuIGludmVudG9yeSBhY2Nlc3MgZ3JhbnRlZC4iO30=',1791436252);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `suppliers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suppliers`
--

LOCK TABLES `suppliers` WRITE;
/*!40000 ALTER TABLE `suppliers` DISABLE KEYS */;
INSERT INTO `suppliers` VALUES (1,'Test Supplier','John','123456','supplier@test.com',NULL,'active','2026-09-30 06:27:52','2026-09-30 06:27:52');
/*!40000 ALTER TABLE `suppliers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `users_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'Cashier',
  `contact_number` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`users_id`),
  UNIQUE KEY `users_username_unique` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'ADMIN/OWNER','$2y$12$mzpNZMZXzpQkN8FLS7kKXudwqjC0x73j.EST9VS1MzaCKiLJFYbum','Owner','09123456789',NULL,'2026-09-30 06:36:30','2026-10-07 06:33:16'),(6,'TEST CASHIER','$2y$12$E7dndFWEb7sMzqzj5r2MXO/cTmVxvTFJY707ice5WBfT.51OeqJkC','Cashier','09236213521',NULL,'2026-10-07 06:39:28','2026-10-07 06:39:34'),(7,'TEST STAFF','$2y$12$w7H9XmAMs5X/gmbDmDjLK./9SjaCJgqrynfbhLXoC074.6xwZKPay','Staff','09123456323',NULL,'2026-10-07 06:39:54','2026-10-07 21:12:57');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vat_settings`
--

DROP TABLE IF EXISTS `vat_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vat_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT 'Standard VAT',
  `rate` decimal(5,2) NOT NULL DEFAULT 12.00,
  `is_inclusive` tinyint(1) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vat_settings`
--

LOCK TABLES `vat_settings` WRITE;
/*!40000 ALTER TABLE `vat_settings` DISABLE KEYS */;
INSERT INTO `vat_settings` VALUES (1,'Standard VAT',12.00,1,1,'2026-10-01 05:54:43','2026-10-01 05:54:43');
/*!40000 ALTER TABLE `vat_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wastages`
--

DROP TABLE IF EXISTS `wastages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wastages` (
  `wastage_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ingredient_id` bigint(20) unsigned NOT NULL,
  `quantity_wasted` decimal(10,2) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `wastage_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`wastage_id`),
  KEY `wastages_ingredient_id_foreign` (`ingredient_id`),
  CONSTRAINT `wastages_ingredient_id_foreign` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients` (`ingredient_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wastages`
--

LOCK TABLES `wastages` WRITE;
/*!40000 ALTER TABLE `wastages` DISABLE KEYS */;
INSERT INTO `wastages` VALUES (1,1,100.00,'expired','2026-10-08','2026-10-07 21:57:18','2026-10-07 21:57:18');
/*!40000 ALTER TABLE `wastages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'pbwsis_db'
--

--
-- Dumping routines for database 'pbwsis_db'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-12 22:05:55
