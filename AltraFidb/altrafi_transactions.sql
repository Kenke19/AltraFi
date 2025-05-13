-- MySQL dump 10.13  Distrib 8.0.41, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: altrafi
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `reference` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('pending','approved','cancelled') DEFAULT 'pending',
  `description` varchar(255) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `reference` (`reference`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transactions`
--

LOCK TABLES `transactions` WRITE;
/*!40000 ALTER TABLE `transactions` DISABLE KEYS */;
INSERT INTO `transactions` VALUES (1,1,'sdd5kyz65c',3000.00,'','Wallet funding via Paystack',NULL,'2025-05-10 18:48:04'),(2,1,'1hztz3qf28',80000.00,'','Wallet funding via Paystack',NULL,'2025-05-10 20:20:36'),(3,1,NULL,-20000.00,'pending','Sent to admin',NULL,'2025-05-10 20:24:18'),(4,2,NULL,20000.00,'pending','Received from kenke',NULL,'2025-05-10 20:24:18'),(5,2,'6su9fqqk3i',100000.00,'','Wallet funding via Paystack',NULL,'2025-05-11 20:35:13'),(6,4,'qoljodcr8e',50000.00,'','Wallet funding via Paystack',NULL,'2025-05-11 20:46:31'),(7,4,NULL,-20000.00,'pending','Sent to kenke',NULL,'2025-05-12 10:33:42'),(8,1,NULL,20000.00,'approved','Received from Rainah',NULL,'2025-05-12 10:33:42'),(9,4,'9npa71xb81',40000.00,'','Wallet funding via Paystack',NULL,'2025-05-12 10:34:09'),(10,4,'y6adqa9kw2',10000.00,'','Wallet funding via Paystack',NULL,'2025-05-12 14:41:32'),(11,4,'fz9mzmoafb',10000.00,'','Wallet funding via Paystack',NULL,'2025-05-12 18:00:41'),(15,2,NULL,10000.00,'approved','Received from admin2',NULL,'2025-05-12 18:23:18'),(16,7,'snqm6u8w41',40000.00,'','Wallet funding via Paystack',NULL,'2025-05-13 10:25:43'),(17,7,NULL,-2000.00,'approved','Sent to kenke',NULL,'2025-05-13 10:26:12'),(18,1,NULL,2000.00,'approved','Received from shola',NULL,'2025-05-13 10:26:12');
/*!40000 ALTER TABLE `transactions` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-05-13 11:43:00
