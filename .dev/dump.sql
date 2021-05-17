-- MySQL dump 10.13  Distrib 5.5.62, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: spcu
-- ------------------------------------------------------
-- Server version	5.5.5-10.5.10-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `card`
--

DROP TABLE IF EXISTS `card`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `card` (
  `card_id` varchar(36) COLLATE utf8mb4_bin NOT NULL,
  `card_name` text COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`card_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `card`
--

LOCK TABLES `card` WRITE;
/*!40000 ALTER TABLE `card` DISABLE KEYS */;
INSERT INTO `card` VALUES ('1ae9d0e2-5f40-11eb-8d14-02004c4f4f50','Server 1'),('1cc5e6bc-5f40-11eb-8d14-02004c4f4f50','NAS Storage on Rack 106.10.250.20'),('1dfe69ea-5f40-11eb-8d14-02004c4f4f50','Server 3'),('1f912a9b-5f40-11eb-8d14-02004c4f4f50','Server 4'),('207f47b9-5f40-11eb-8d14-02004c4f4f50','Server 5'),('ac7e17c1-5f40-11eb-8d14-02004c4f4f50','Rack (#23 - user segment)');
/*!40000 ALTER TABLE `card` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cardfield`
--

DROP TABLE IF EXISTS `cardfield`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cardfield` (
  `cardfield_id` varchar(36) COLLATE utf8mb4_bin NOT NULL,
  `cardfield_name` text COLLATE utf8mb4_bin NOT NULL,
  `plugin_code` varchar(64) COLLATE utf8mb4_bin NOT NULL,
  `plugin_field` tinyint(3) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`cardfield_id`),
  KEY `plugin_code` (`plugin_code`) USING BTREE,
  KEY `plugin_field` (`plugin_field`) USING BTREE,
  KEY `cardfield_FK` (`plugin_code`,`plugin_field`),
  CONSTRAINT `cardfield_FK` FOREIGN KEY (`plugin_code`, `plugin_field`) REFERENCES `plugin` (`plugin_code`, `plugin_field`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cardfield`
--

LOCK TABLES `cardfield` WRITE;
/*!40000 ALTER TABLE `cardfield` DISABLE KEYS */;
INSERT INTO `cardfield` VALUES ('151f62cc-0870-4574-8bc1-ca8a4ebfed5f','Info text','text',1),('6327f2f4-5ed3-11eb-8d14-02004c4f4f50','Linked to','link',1),('a10241f7-5f40-11eb-8d14-02004c4f4f50','Type','text',1),('a10341f7-5f40-11eb-8d14-02004c4f4f50','Memo','text',1),('a10541f7-5f40-11eb-8d14-02004c4f4f50','IP','text',1);
/*!40000 ALTER TABLE `cardfield` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cardfieldvalue`
--

DROP TABLE IF EXISTS `cardfieldvalue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cardfieldvalue` (
  `cardfieldvalue_id` varchar(36) COLLATE utf8mb4_bin NOT NULL,
  `card_id` varchar(36) COLLATE utf8mb4_bin NOT NULL,
  `cardfield_id` varchar(36) COLLATE utf8mb4_bin NOT NULL,
  `value` text COLLATE utf8mb4_bin NOT NULL,
  `cardfieldvalue_pos` int(11) NOT NULL,
  PRIMARY KEY (`cardfieldvalue_id`),
  KEY `card_id` (`card_id`),
  KEY `cardfield_id` (`cardfield_id`),
  KEY `cardfieldvalue_cardfieldvalue_pos_IDX` (`cardfieldvalue_pos`) USING BTREE,
  CONSTRAINT `cardfieldvalue_ibfk_card` FOREIGN KEY (`card_id`) REFERENCES `card` (`card_id`) ON UPDATE CASCADE,
  CONSTRAINT `cardfieldvalue_ibfk_cardfield` FOREIGN KEY (`cardfield_id`) REFERENCES `cardfield` (`cardfield_id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cardfieldvalue`
--

LOCK TABLES `cardfieldvalue` WRITE;
/*!40000 ALTER TABLE `cardfieldvalue` DISABLE KEYS */;
INSERT INTO `cardfieldvalue` VALUES ('bb26065b-27cb-4678-89ea-89142d75601a','1cc5e6bc-5f40-11eb-8d14-02004c4f4f50','a10341f7-5f40-11eb-8d14-02004c4f4f50','Info text',3),('da2d2dca-618f-11eb-869a-02004c4f4f50','ac7e17c1-5f40-11eb-8d14-02004c4f4f50','a10241f7-5f40-11eb-8d14-02004c4f4f50','#23 - user segment',1),('da2f535d-618f-11eb-869a-02004c4f4f50','1ae9d0e2-5f40-11eb-8d14-02004c4f4f50','a10241f7-5f40-11eb-8d14-02004c4f4f50','server',1),('da2f548f-618f-11eb-869a-02004c4f4f50','1cc5e6bc-5f40-11eb-8d14-02004c4f4f50','a10241f7-5f40-11eb-8d14-02004c4f4f50','server',1),('da2f5511-618f-11eb-869a-02004c4f4f50','1dfe69ea-5f40-11eb-8d14-02004c4f4f50','a10241f7-5f40-11eb-8d14-02004c4f4f50','server',1),('da2f558d-618f-11eb-869a-02004c4f4f50','1f912a9b-5f40-11eb-8d14-02004c4f4f50','a10241f7-5f40-11eb-8d14-02004c4f4f50','server',1),('da2f5609-618f-11eb-869a-02004c4f4f50','207f47b9-5f40-11eb-8d14-02004c4f4f50','a10241f7-5f40-11eb-8d14-02004c4f4f50','server',1),('da2f5684-618f-11eb-869a-02004c4f4f50','1ae9d0e2-5f40-11eb-8d14-02004c4f4f50','a10541f7-5f40-11eb-8d14-02004c4f4f50','42.3.10.43',2),('da2f5703-618f-11eb-869a-02004c4f4f50','ac7e17c1-5f40-11eb-8d14-02004c4f4f50','6327f2f4-5ed3-11eb-8d14-02004c4f4f50','1cc5e6bc-5f40-11eb-8d14-02004c4f4f50',2),('da2f5782-618f-11eb-869a-02004c4f4f50','1cc5e6bc-5f40-11eb-8d14-02004c4f4f50','a10541f7-5f40-11eb-8d14-02004c4f4f50','58.43.43.84',2),('da2f57f8-618f-11eb-869a-02004c4f4f50','1dfe69ea-5f40-11eb-8d14-02004c4f4f50','a10541f7-5f40-11eb-8d14-02004c4f4f50','54.16.39.26',2),('da2f586d-618f-11eb-869a-02004c4f4f50','1f912a9b-5f40-11eb-8d14-02004c4f4f50','a10541f7-5f40-11eb-8d14-02004c4f4f50','13.106.12.101',2),('da2f58e3-618f-11eb-869a-02004c4f4f50','207f47b9-5f40-11eb-8d14-02004c4f4f50','a10541f7-5f40-11eb-8d14-02004c4f4f50','108.116.20.106',2),('da2f5958-618f-11eb-869a-02004c4f4f50','1dfe69ea-5f40-11eb-8d14-02004c4f4f50','6327f2f4-5ed3-11eb-8d14-02004c4f4f50','1cc5e6bc-5f40-11eb-8d14-02004c4f4f50',3),('da2f59cb-618f-11eb-869a-02004c4f4f50','ac7e17c1-5f40-11eb-8d14-02004c4f4f50','6327f2f4-5ed3-11eb-8d14-02004c4f4f50','1dfe69ea-5f40-11eb-8d14-02004c4f4f50',3),('da2f5a40-618f-11eb-869a-02004c4f4f50','ac7e17c1-5f40-11eb-8d14-02004c4f4f50','6327f2f4-5ed3-11eb-8d14-02004c4f4f50','1f912a9b-5f40-11eb-8d14-02004c4f4f50',4),('da2f5ab3-618f-11eb-869a-02004c4f4f50','ac7e17c1-5f40-11eb-8d14-02004c4f4f50','6327f2f4-5ed3-11eb-8d14-02004c4f4f50','207f47b9-5f40-11eb-8d14-02004c4f4f50',5);
/*!40000 ALTER TABLE `cardfieldvalue` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plugin`
--

DROP TABLE IF EXISTS `plugin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plugin` (
  `plugin_name` varchar(50) COLLATE utf8mb4_bin NOT NULL,
  `plugin_version` varchar(20) COLLATE utf8mb4_bin NOT NULL,
  `plugin_code` varchar(64) COLLATE utf8mb4_bin NOT NULL,
  `plugin_class` varchar(400) COLLATE utf8mb4_bin DEFAULT NULL,
  `plugin_desc` text COLLATE utf8mb4_bin NOT NULL,
  `plugin_model` tinyint(3) unsigned NOT NULL,
  `plugin_field` tinyint(3) unsigned NOT NULL,
  `plugin_meta` tinyint(3) unsigned NOT NULL,
  `active` int(1) unsigned NOT NULL,
  PRIMARY KEY (`plugin_name`,`plugin_version`),
  UNIQUE KEY `plugin_code_model` (`plugin_code`,`plugin_model`) USING BTREE,
  UNIQUE KEY `plugin_uniq` (`plugin_code`,`plugin_name`,`plugin_version`) USING BTREE,
  UNIQUE KEY `plugin_code_field` (`plugin_code`,`plugin_field`) USING BTREE,
  KEY `active` (`active`),
  KEY `plugin_model` (`plugin_model`) USING BTREE,
  KEY `plugin_field` (`plugin_field`) USING BTREE,
  KEY `plugin_meta` (`plugin_meta`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plugin`
--

LOCK TABLES `plugin` WRITE;
/*!40000 ALTER TABLE `plugin` DISABLE KEYS */;
INSERT INTO `plugin` VALUES ('Card','1.0.0','card','Card','Base Card model',1,0,0,1),('Link','1.0.0','link','Link','Fields such as links allow you to associate a card with another.',0,1,1,1),('Text','1.0.0','text','Text','Standart text fields',0,1,0,1),('Ts','1.0.0','ts','Ts','Standart timestamp',0,0,1,0),('User','1.0.0','user','User','Base User model',1,0,1,0);
/*!40000 ALTER TABLE `plugin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'spcu'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-05-17 18:35:28
