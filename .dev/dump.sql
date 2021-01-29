/*
SQLyog Ultimate v13.1.1 (64 bit)
MySQL - 10.4.12-MariaDB : Database - spherecube
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`spherecube` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin */;

USE `spherecube`;

/*Table structure for table `card` */

DROP TABLE IF EXISTS `card`;

CREATE TABLE `card` (
  `card_id` varchar(36) COLLATE utf8mb4_bin NOT NULL,
  `card_name` varchar(400) COLLATE utf8mb4_bin NOT NULL,
  `user_id` varchar(36) COLLATE utf8mb4_bin NOT NULL,
  `active` int(1) unsigned NOT NULL DEFAULT 0,
  `ts` int(15) unsigned NOT NULL,
  PRIMARY KEY (`card_id`),
  KEY `card_ibfk_1` (`user_id`),
  KEY `active` (`active`),
  FULLTEXT KEY `card_name` (`card_name`),
  CONSTRAINT `card_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

/*Table structure for table `cardfield` */

DROP TABLE IF EXISTS `cardfield`;

CREATE TABLE `cardfield` (
  `cardfield_id` varchar(36) COLLATE utf8mb4_bin NOT NULL,
  `cardfield_name` varchar(400) COLLATE utf8mb4_bin NOT NULL,
  `cardfield_type` varchar(400) COLLATE utf8mb4_bin NOT NULL DEFAULT 'text',
  PRIMARY KEY (`cardfield_id`),
  KEY `cardfield_type` (`cardfield_type`),
  FULLTEXT KEY `cardfield_name` (`cardfield_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

/*Table structure for table `cardfieldvalue` */

DROP TABLE IF EXISTS `cardfieldvalue`;

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
  FULLTEXT KEY `value` (`value`),
  CONSTRAINT `cardfieldvalue_ibfk_1` FOREIGN KEY (`card_id`) REFERENCES `card` (`card_id`) ON UPDATE CASCADE,
  CONSTRAINT `cardfieldvalue_ibfk_3` FOREIGN KEY (`cardfield_id`) REFERENCES `cardfield` (`cardfield_id`) ON UPDATE CASCADE,
  CONSTRAINT `cardfieldvalue_ibfk_4` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

/*Table structure for table `cardfieldvaluehist` */

DROP TABLE IF EXISTS `cardfieldvaluehist`;

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

/*Table structure for table `cardhist` */

DROP TABLE IF EXISTS `cardhist`;

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

/*Table structure for table `hook` */

DROP TABLE IF EXISTS `hook`;

CREATE TABLE `hook` (
  `hook_name` varchar(50) COLLATE utf8mb4_bin NOT NULL,
  `hook_version` varchar(20) COLLATE utf8mb4_bin NOT NULL,
  `hook_type` varchar(400) COLLATE utf8mb4_bin DEFAULT NULL,
  `hook_class` varchar(400) COLLATE utf8mb4_bin DEFAULT NULL,
  `hook_desc` varchar(4000) COLLATE utf8mb4_bin NOT NULL,
  `active` int(1) unsigned NOT NULL,
  PRIMARY KEY (`hook_name`,`hook_version`),
  KEY `hook_type` (`hook_type`),
  KEY `active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

/*Table structure for table `role` */

DROP TABLE IF EXISTS `role`;

CREATE TABLE `role` (
  `role_id` varchar(36) COLLATE utf8mb4_bin NOT NULL,
  `role_name` varchar(400) COLLATE utf8mb4_bin NOT NULL,
  `active` int(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`role_id`),
  KEY `active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

/*Table structure for table `rolepermit` */

DROP TABLE IF EXISTS `rolepermit`;

CREATE TABLE `rolepermit` (
  `role_id` varchar(36) COLLATE utf8mb4_bin NOT NULL,
  `permit` varchar(400) COLLATE utf8mb4_bin NOT NULL,
  KEY `rolepermit_ibfk_1` (`role_id`),
  CONSTRAINT `rolepermit_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `role` (`role_id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

/*Table structure for table `user` */

DROP TABLE IF EXISTS `user`;

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

/*Table structure for table `userrole` */

DROP TABLE IF EXISTS `userrole`;

CREATE TABLE `userrole` (
  `user_id` varchar(36) COLLATE utf8mb4_bin NOT NULL,
  `role_id` varchar(36) COLLATE utf8mb4_bin NOT NULL,
  KEY `userrole_ibfk_1` (`user_id`),
  KEY `userrole_ibfk_2` (`role_id`),
  CONSTRAINT `userrole_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON UPDATE CASCADE,
  CONSTRAINT `userrole_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `role` (`role_id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

/* Trigger structure for table `card` */

DELIMITER $$

/*!50003 DROP TRIGGER*//*!50032 IF EXISTS */ /*!50003 `beforecardchange` */$$

/*!50003 CREATE */ /*!50003 TRIGGER `beforecardchange` BEFORE UPDATE ON `card` FOR EACH ROW BEGIN
	SET new.ts = UNIX_TIMESTAMP();
    END */$$


DELIMITER ;

/* Trigger structure for table `card` */

DELIMITER $$

/*!50003 DROP TRIGGER*//*!50032 IF EXISTS */ /*!50003 `aftercardchange` */$$

/*!50003 CREATE */ /*!50003 TRIGGER `aftercardchange` AFTER UPDATE ON `card` FOR EACH ROW 
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
END */$$


DELIMITER ;

/* Trigger structure for table `cardfieldvalue` */

DELIMITER $$

/*!50003 DROP TRIGGER*//*!50032 IF EXISTS */ /*!50003 `beforefieldvaluechange` */$$

/*!50003 CREATE */ /*!50003 TRIGGER `beforefieldvaluechange` BEFORE UPDATE ON `cardfieldvalue` FOR EACH ROW BEGIN
	SET new.ts = UNIX_TIMESTAMP();
    END */$$


DELIMITER ;

/* Trigger structure for table `cardfieldvalue` */

DELIMITER $$

/*!50003 DROP TRIGGER*//*!50032 IF EXISTS */ /*!50003 `afterfieldvaluechange` */$$

/*!50003 CREATE */ /*!50003 TRIGGER `afterfieldvaluechange` AFTER UPDATE ON `cardfieldvalue` FOR EACH ROW 
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
END */$$


DELIMITER ;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
