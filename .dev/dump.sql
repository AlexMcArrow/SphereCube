-- MySQL dump 10.13  Distrib 5.5.62, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: spherecube
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
  `card_name` varchar(400) COLLATE utf8mb4_bin NOT NULL,
  `user_id` varchar(36) COLLATE utf8mb4_bin NOT NULL,
  `active` int(1) unsigned NOT NULL DEFAULT 0,
  `ts` int(15) unsigned NOT NULL,
  PRIMARY KEY (`card_id`),
  KEY `card_ibfk_1` (`user_id`),
  KEY `active` (`active`),
  CONSTRAINT `card_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `card`
--

LOCK TABLES `card` WRITE;
/*!40000 ALTER TABLE `card` DISABLE KEYS */;
INSERT INTO `card` VALUES ('1ae9d0e2-5f40-11eb-8d14-02004c4f4f50','Server 1','1111111-1111-1111-1111-111111111111',1,1611856067),('1cc5e6bc-5f40-11eb-8d14-02004c4f4f50','NAS Storage on Rack 1','1111111-1111-1111-1111-111111111111',1,1611860962),('1dfe69ea-5f40-11eb-8d14-02004c4f4f50','Server 3','1111111-1111-1111-1111-111111111111',1,1611856067),('1f912a9b-5f40-11eb-8d14-02004c4f4f50','Server 4','1111111-1111-1111-1111-111111111111',1,1611856067),('207f47b9-5f40-11eb-8d14-02004c4f4f50','Server 5','1111111-1111-1111-1111-111111111111',1,1611856067),('ac7e17c1-5f40-11eb-8d14-02004c4f4f50','Rack (#23 - user segment)','1111111-1111-1111-1111-111111111111',1,1611862083);
/*!40000 ALTER TABLE `card` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`dev`@`localhost`*/ /*!50003 TRIGGER `beforecardchange` BEFORE UPDATE ON `spherecube`.`card` FOR EACH ROW BEGIN

	SET new.ts = UNIX_TIMESTAMP();

    END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`dev`@`localhost`*/ /*!50003 TRIGGER `aftercardchange` AFTER UPDATE ON `spherecube`.`card` FOR EACH ROW

BEGIN

  if (

    old.card_name != new.card_name

    OR old.active != new.active

  )

  then

  INSERT INTO `cardhist` (

    `cardhist_id`,

    `card_id`,

    `user_id`,

    `name`,

    `active`,

    `ts`

  )

  VALUES

    (

      uuid (),

      old.card_id,

      old.user_id,

      old.card_name,

      old.active,

      old.ts

    );

  end if;

END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `cardfield`
--

DROP TABLE IF EXISTS `cardfield`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cardfield` (
  `cardfield_id` varchar(36) COLLATE utf8mb4_bin NOT NULL,
  `cardfield_name` varchar(400) COLLATE utf8mb4_bin NOT NULL,
  `cardfield_type` varchar(400) COLLATE utf8mb4_bin NOT NULL DEFAULT 'text',
  PRIMARY KEY (`cardfield_id`),
  KEY `cardfield_type` (`cardfield_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cardfield`
--

LOCK TABLES `cardfield` WRITE;
/*!40000 ALTER TABLE `cardfield` DISABLE KEYS */;
INSERT INTO `cardfield` VALUES ('151f62cc-0870-4574-8bc1-ca8a4ebfed5f','Info text','text'),('6327f2f4-5ed3-11eb-8d14-02004c4f4f50','Linked to card','link'),('a10241f7-5f40-11eb-8d14-02004c4f4f50','Type','text'),('a10341f7-5f40-11eb-8d14-02004c4f4f50','Memo','text'),('a10541f7-5f40-11eb-8d14-02004c4f4f50','IP','text');
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
  `user_id` varchar(36) COLLATE utf8mb4_bin NOT NULL,
  `value` varchar(12000) COLLATE utf8mb4_bin NOT NULL,
  `active` int(1) unsigned NOT NULL DEFAULT 0,
  `ts` int(15) unsigned NOT NULL,
  PRIMARY KEY (`cardfieldvalue_id`),
  KEY `active` (`active`),
  KEY `card_id` (`card_id`),
  KEY `cardfield_id` (`cardfield_id`),
  KEY `ts` (`ts`),
  KEY `cardfieldvalue_ibfk_4` (`user_id`),
  CONSTRAINT `cardfieldvalue_ibfk_1` FOREIGN KEY (`card_id`) REFERENCES `card` (`card_id`) ON UPDATE CASCADE,
  CONSTRAINT `cardfieldvalue_ibfk_3` FOREIGN KEY (`cardfield_id`) REFERENCES `cardfield` (`cardfield_id`) ON UPDATE CASCADE,
  CONSTRAINT `cardfieldvalue_ibfk_4` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cardfieldvalue`
--

LOCK TABLES `cardfieldvalue` WRITE;
/*!40000 ALTER TABLE `cardfieldvalue` DISABLE KEYS */;
INSERT INTO `cardfieldvalue` VALUES ('bb26065b-27cb-4678-89ea-89142d75601a','1cc5e6bc-5f40-11eb-8d14-02004c4f4f50','a10341f7-5f40-11eb-8d14-02004c4f4f50','1111111-1111-1111-1111-111111111111','Info text',1,1611904795),('da2d2dca-618f-11eb-869a-02004c4f4f50','ac7e17c1-5f40-11eb-8d14-02004c4f4f50','a10241f7-5f40-11eb-8d14-02004c4f4f50','1111111-1111-1111-1111-111111111111','#23 - user segment',1,1611908504),('da2f535d-618f-11eb-869a-02004c4f4f50','1ae9d0e2-5f40-11eb-8d14-02004c4f4f50','a10241f7-5f40-11eb-8d14-02004c4f4f50','1111111-1111-1111-1111-111111111111','server',1,1611856089),('da2f548f-618f-11eb-869a-02004c4f4f50','1cc5e6bc-5f40-11eb-8d14-02004c4f4f50','a10241f7-5f40-11eb-8d14-02004c4f4f50','1111111-1111-1111-1111-111111111111','server',1,1611856089),('da2f5511-618f-11eb-869a-02004c4f4f50','1dfe69ea-5f40-11eb-8d14-02004c4f4f50','a10241f7-5f40-11eb-8d14-02004c4f4f50','1111111-1111-1111-1111-111111111111','server',1,1611856089),('da2f558d-618f-11eb-869a-02004c4f4f50','1f912a9b-5f40-11eb-8d14-02004c4f4f50','a10241f7-5f40-11eb-8d14-02004c4f4f50','1111111-1111-1111-1111-111111111111','server',1,1611856089),('da2f5609-618f-11eb-869a-02004c4f4f50','207f47b9-5f40-11eb-8d14-02004c4f4f50','a10241f7-5f40-11eb-8d14-02004c4f4f50','1111111-1111-1111-1111-111111111111','server',1,1611856089),('da2f5684-618f-11eb-869a-02004c4f4f50','1ae9d0e2-5f40-11eb-8d14-02004c4f4f50','a10541f7-5f40-11eb-8d14-02004c4f4f50','1111111-1111-1111-1111-111111111111','42.3.10.43',1,1611860278),('da2f5703-618f-11eb-869a-02004c4f4f50','ac7e17c1-5f40-11eb-8d14-02004c4f4f50','6327f2f4-5ed3-11eb-8d14-02004c4f4f50','1111111-1111-1111-1111-111111111111','1cc5e6bc-5f40-11eb-8d14-02004c4f4f50',1,1611856089),('da2f5782-618f-11eb-869a-02004c4f4f50','1cc5e6bc-5f40-11eb-8d14-02004c4f4f50','a10541f7-5f40-11eb-8d14-02004c4f4f50','1111111-1111-1111-1111-111111111111','58.43.43.84',1,1611856089),('da2f57f8-618f-11eb-869a-02004c4f4f50','1dfe69ea-5f40-11eb-8d14-02004c4f4f50','a10541f7-5f40-11eb-8d14-02004c4f4f50','1111111-1111-1111-1111-111111111111','54.16.39.26',1,1611856089),('da2f586d-618f-11eb-869a-02004c4f4f50','1f912a9b-5f40-11eb-8d14-02004c4f4f50','a10541f7-5f40-11eb-8d14-02004c4f4f50','1111111-1111-1111-1111-111111111111','13.106.12.101',1,1611856089),('da2f58e3-618f-11eb-869a-02004c4f4f50','207f47b9-5f40-11eb-8d14-02004c4f4f50','a10541f7-5f40-11eb-8d14-02004c4f4f50','1111111-1111-1111-1111-111111111111','108.116.20.106',1,1611856089),('da2f5958-618f-11eb-869a-02004c4f4f50','1dfe69ea-5f40-11eb-8d14-02004c4f4f50','6327f2f4-5ed3-11eb-8d14-02004c4f4f50','1111111-1111-1111-1111-111111111111','1cc5e6bc-5f40-11eb-8d14-02004c4f4f50',1,1611856089),('da2f59cb-618f-11eb-869a-02004c4f4f50','ac7e17c1-5f40-11eb-8d14-02004c4f4f50','6327f2f4-5ed3-11eb-8d14-02004c4f4f50','1111111-1111-1111-1111-111111111111','1dfe69ea-5f40-11eb-8d14-02004c4f4f50',1,1611856089),('da2f5a40-618f-11eb-869a-02004c4f4f50','ac7e17c1-5f40-11eb-8d14-02004c4f4f50','6327f2f4-5ed3-11eb-8d14-02004c4f4f50','1111111-1111-1111-1111-111111111111','1f912a9b-5f40-11eb-8d14-02004c4f4f50',1,1611856089),('da2f5ab3-618f-11eb-869a-02004c4f4f50','ac7e17c1-5f40-11eb-8d14-02004c4f4f50','6327f2f4-5ed3-11eb-8d14-02004c4f4f50','1111111-1111-1111-1111-111111111111','207f47b9-5f40-11eb-8d14-02004c4f4f50',1,1611856089);
/*!40000 ALTER TABLE `cardfieldvalue` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`dev`@`localhost`*/ /*!50003 TRIGGER `beforefieldvaluechange` BEFORE UPDATE ON `spherecube`.`cardfieldvalue` FOR EACH ROW BEGIN

	SET new.ts = UNIX_TIMESTAMP();

    END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`dev`@`localhost`*/ /*!50003 TRIGGER `afterfieldvaluechange` AFTER UPDATE ON `spherecube`.`cardfieldvalue` FOR EACH ROW

BEGIN

  if (

    old.value != new.value

    or old.cardfield_id != new.cardfield_id

    OR old.active != new.active

  )

  then

  INSERT INTO `cardfieldvaluehist` (

    `cardfieldvaluehist_id`,

    `cardfieldvalue_id`,

    `user_id`,

    `cardfield_id`,

    `value`,

    `active`,

    `ts`

  )

  VALUES

    (

      uuid (),

      old.cardfieldvalue_id,

      old.user_id,

      old.cardfield_id,

      old.value,

      old.active,

      old.ts

    );

  end if;

END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `cardfieldvaluehist`
--

DROP TABLE IF EXISTS `cardfieldvaluehist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cardfieldvaluehist` (
  `cardfieldvaluehist_id` varchar(36) COLLATE utf8mb4_bin NOT NULL,
  `cardfieldvalue_id` varchar(36) COLLATE utf8mb4_bin NOT NULL,
  `user_id` varchar(36) COLLATE utf8mb4_bin NOT NULL,
  `cardfield_id` varchar(36) COLLATE utf8mb4_bin NOT NULL,
  `value` varchar(12000) COLLATE utf8mb4_bin NOT NULL,
  `active` int(1) unsigned NOT NULL,
  `ts` int(15) unsigned NOT NULL,
  PRIMARY KEY (`cardfieldvaluehist_id`),
  KEY `ts` (`ts`),
  KEY `cardfieldvaluehist_ibfk_1` (`cardfieldvalue_id`),
  KEY `cardfieldvaluehist_ibfk_2` (`user_id`),
  KEY `cardfield_id` (`cardfield_id`),
  CONSTRAINT `cardfieldvaluehist_ibfk_1` FOREIGN KEY (`cardfieldvalue_id`) REFERENCES `cardfieldvalue` (`cardfieldvalue_id`) ON UPDATE CASCADE,
  CONSTRAINT `cardfieldvaluehist_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON UPDATE CASCADE,
  CONSTRAINT `cardfieldvaluehist_ibfk_3` FOREIGN KEY (`cardfield_id`) REFERENCES `cardfield` (`cardfield_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cardfieldvaluehist`
--

LOCK TABLES `cardfieldvaluehist` WRITE;
/*!40000 ALTER TABLE `cardfieldvaluehist` DISABLE KEYS */;
/*!40000 ALTER TABLE `cardfieldvaluehist` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cardhist`
--

DROP TABLE IF EXISTS `cardhist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cardhist` (
  `cardhist_id` varchar(36) COLLATE utf8mb4_bin NOT NULL,
  `card_id` varchar(36) COLLATE utf8mb4_bin NOT NULL,
  `user_id` varchar(36) COLLATE utf8mb4_bin NOT NULL,
  `name` varchar(400) COLLATE utf8mb4_bin NOT NULL,
  `active` int(1) unsigned NOT NULL,
  `ts` int(15) unsigned NOT NULL,
  PRIMARY KEY (`cardhist_id`),
  KEY `ts` (`ts`),
  KEY `card_id` (`card_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `cardhist_ibfk_1` FOREIGN KEY (`card_id`) REFERENCES `card` (`card_id`),
  CONSTRAINT `cardhist_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cardhist`
--

LOCK TABLES `cardhist` WRITE;
/*!40000 ALTER TABLE `cardhist` DISABLE KEYS */;
/*!40000 ALTER TABLE `cardhist` ENABLE KEYS */;
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
  `plugin_type` varchar(400) COLLATE utf8mb4_bin DEFAULT NULL,
  `plugin_class` varchar(400) COLLATE utf8mb4_bin DEFAULT NULL,
  `plugin_desc` varchar(4000) COLLATE utf8mb4_bin NOT NULL,
  `active` int(1) unsigned NOT NULL,
  PRIMARY KEY (`plugin_name`,`plugin_version`),
  KEY `hook_type` (`plugin_type`),
  KEY `active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plugin`
--

LOCK TABLES `plugin` WRITE;
/*!40000 ALTER TABLE `plugin` DISABLE KEYS */;
INSERT INTO `plugin` VALUES ('Card','1.0.0','card','\\Plugin\\Card\\Model','Base Card model',1),('Link','1.0.0','link','\\Plugin\\Link\\Plugin','Fields such as links allow you to associate a card with another.',1),('Text','1.0.0','text','\\Plugin\\Text\\Plugin','Standart text fields',1),('Ts','1.0.0','ts','\\Plugin\\Ts\\Plugin','Standart timestamp',1),('User','1.0.0','user','\\Plugin\\User\\Model','Base User model',1);
/*!40000 ALTER TABLE `plugin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role`
--

DROP TABLE IF EXISTS `role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role` (
  `role_id` varchar(36) COLLATE utf8mb4_bin NOT NULL,
  `role_name` varchar(400) COLLATE utf8mb4_bin NOT NULL,
  `active` int(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`role_id`),
  KEY `active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role`
--

LOCK TABLES `role` WRITE;
/*!40000 ALTER TABLE `role` DISABLE KEYS */;
/*!40000 ALTER TABLE `role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rolepermit`
--

DROP TABLE IF EXISTS `rolepermit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rolepermit` (
  `role_id` varchar(36) COLLATE utf8mb4_bin NOT NULL,
  `permit` varchar(400) COLLATE utf8mb4_bin NOT NULL,
  KEY `rolepermit_ibfk_1` (`role_id`),
  CONSTRAINT `rolepermit_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `role` (`role_id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rolepermit`
--

LOCK TABLES `rolepermit` WRITE;
/*!40000 ALTER TABLE `rolepermit` DISABLE KEYS */;
/*!40000 ALTER TABLE `rolepermit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `user_id` varchar(36) COLLATE utf8mb4_bin NOT NULL,
  `user_name` varchar(400) COLLATE utf8mb4_bin NOT NULL,
  `user_login` varchar(400) COLLATE utf8mb4_bin NOT NULL,
  `user_hash` varchar(400) COLLATE utf8mb4_bin NOT NULL,
  `active` int(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_login` (`user_login`),
  KEY `active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES ('1111111-1111-1111-1111-111111111111','Administrator','admin','admin',1);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `userrole`
--

DROP TABLE IF EXISTS `userrole`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `userrole` (
  `user_id` varchar(36) COLLATE utf8mb4_bin NOT NULL,
  `role_id` varchar(36) COLLATE utf8mb4_bin NOT NULL,
  KEY `userrole_ibfk_1` (`user_id`),
  KEY `userrole_ibfk_2` (`role_id`),
  CONSTRAINT `userrole_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON UPDATE CASCADE,
  CONSTRAINT `userrole_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `role` (`role_id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `userrole`
--

LOCK TABLES `userrole` WRITE;
/*!40000 ALTER TABLE `userrole` DISABLE KEYS */;
/*!40000 ALTER TABLE `userrole` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'spherecube'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-05-15 18:13:51
