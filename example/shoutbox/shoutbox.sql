# HeidiSQL Dump 
#
# --------------------------------------------------------
# Host:                         127.0.0.1
# Database:                     shoutbox
# Server version:               5.1.37-community-log
# Server OS:                    Win32
# Target compatibility:         ANSI SQL
# HeidiSQL version:             4.0
# Date/time:                    2010-02-16 21:29:10
# --------------------------------------------------------

/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ANSI,NO_BACKSLASH_ESCAPES';*/
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;*/


#
# Database structure for database 'shoutbox'
#

CREATE DATABASE /*!32312 IF NOT EXISTS*/ "shoutbox" /*!40100 DEFAULT CHARACTER SET latin1 */;

USE "shoutbox";


#
# Table structure for table 'shouts'
#

CREATE TABLE /*!32312 IF NOT EXISTS*/ "shouts" (
  "id" int(10) unsigned NOT NULL AUTO_INCREMENT,
  "name" varchar(100) NOT NULL,
  "shout" varchar(255) NOT NULL,
  PRIMARY KEY ("id"),
  UNIQUE KEY "id" ("id"),
  KEY "id_2" ("id")
) AUTO_INCREMENT=12;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE;*/
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;*/
