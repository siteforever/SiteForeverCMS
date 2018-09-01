-- MySQL dump 10.13  Distrib 5.5.31, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: siteforever
-- ------------------------------------------------------
-- Server version	5.5.31-0ubuntu0.12.04.2-log

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
-- Table structure for table `aliases`
--

DROP TABLE IF EXISTS `aliases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aliases` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias` varchar(250) DEFAULT NULL,
  `url` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aliases`
--

LOCK TABLES `aliases` WRITE;
/*!40000 ALTER TABLE `aliases` DISABLE KEYS */;
/*!40000 ALTER TABLE `aliases` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `banner`
--

DROP TABLE IF EXISTS `banner`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `banner` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cat_id` int(11) DEFAULT NULL,
  `name` varchar(250) DEFAULT NULL,
  `url` varchar(250) DEFAULT NULL,
  `path` varchar(250) DEFAULT NULL,
  `count_show` int(11) DEFAULT NULL,
  `count_click` int(11) DEFAULT NULL,
  `target` varchar(250) DEFAULT NULL,
  `content` text,
  `deleted` int(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `banner`
--

LOCK TABLES `banner` WRITE;
/*!40000 ALTER TABLE `banner` DISABLE KEYS */;
INSERT INTO `banner` VALUES (1,1,'Модуль1','http://cms.sf',NULL,NULL,NULL,'_blank','',0),(4,2,'Мой баннер','',NULL,NULL,NULL,'_blank','',0),(6,1,'Модуль2','http://cms.sf',NULL,NULL,NULL,'_blank','',0);
/*!40000 ALTER TABLE `banner` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `catalog`
--

DROP TABLE IF EXISTS `catalog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `catalog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent` int(11) DEFAULT NULL,
  `type_id` int(11) NOT NULL DEFAULT '0',
  `cat` tinyint(4) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `alias` varchar(100) DEFAULT NULL,
  `path` text,
  `text` text,
  `articul` varchar(250) DEFAULT NULL,
  `price1` decimal(13,2) DEFAULT NULL,
  `price2` decimal(13,2) DEFAULT NULL,
  `material` int(11) DEFAULT NULL,
  `manufacturer` int(11) DEFAULT NULL,
  `pos` int(11) DEFAULT NULL,
  `gender` int(11) DEFAULT NULL,
  `p0` varchar(250) DEFAULT NULL,
  `p1` varchar(250) DEFAULT NULL,
  `p2` varchar(250) DEFAULT NULL,
  `p3` varchar(250) DEFAULT NULL,
  `p4` varchar(250) DEFAULT NULL,
  `p5` varchar(250) DEFAULT NULL,
  `p6` varchar(250) DEFAULT NULL,
  `p7` varchar(250) DEFAULT NULL,
  `p8` varchar(250) DEFAULT NULL,
  `p9` varchar(250) DEFAULT NULL,
  `sort_view` tinyint(1) NOT NULL DEFAULT '1',
  `sale` int(1) NOT NULL DEFAULT '0',
  `sale_start` int(11) NOT NULL DEFAULT '0',
  `sale_stop` int(11) NOT NULL DEFAULT '0',
  `top` tinyint(1) NOT NULL DEFAULT '0',
  `novelty` tinyint(1) NOT NULL DEFAULT '0',
  `byorder` tinyint(1) NOT NULL DEFAULT '0',
  `absent` tinyint(1) NOT NULL DEFAULT '0',
  `hidden` tinyint(1) NOT NULL DEFAULT '0',
  `protected` tinyint(1) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `parent` (`parent`),
  KEY `name` (`hidden`),
  KEY `deleted` (`deleted`),
  KEY `hidden` (`hidden`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `catalog`
--

LOCK TABLES `catalog` WRITE;
/*!40000 ALTER TABLE `catalog` DISABLE KEYS */;
INSERT INTO `catalog` VALUES
  (1,17,0,1,'Телефоны','1-telefony','a:2:{i:0;a:2:{s:2:\"id\";s:2:\"17\";s:4:\"name\";s:14:\"Каталог\";}i:1;a:2:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:16:\"Телефоны\";}}','','',0.00,0.00,NULL,0,2,NULL,'','','','','','','','','','',1,0,0,0,0,0,0,0,0,0,0),
  (2,17,0,1,'Автомобили','2-avtomobili','a:2:{i:0;a:2:{s:2:\"id\";s:2:\"17\";s:4:\"name\";s:14:\"Каталог\";}i:1;a:2:{s:2:\"id\";s:1:\"2\";s:4:\"name\";s:20:\"Автомобили\";}}','','',0.00,0.00,NULL,0,1,NULL,'','','','','','','','','','',1,0,0,0,0,0,0,0,0,0,0),
  (3,2,0,1,'Внедорожники','3-vnedorozhniki','a:3:{i:0;a:2:{s:2:\"id\";s:2:\"17\";s:4:\"name\";s:14:\"Каталог\";}i:1;a:2:{s:2:\"id\";s:1:\"2\";s:4:\"name\";s:20:\"Автомобили\";}i:2;a:2:{s:2:\"id\";s:1:\"3\";s:4:\"name\";s:24:\"Внедорожники\";}}','','',0.00,0.00,NULL,0,0,NULL,'','','','','','','','','','',1,0,0,0,0,0,0,0,0,0,0),
  (4,1,0,1,'Телефоны HTC','4-telefony-htc','a:3:{i:0;a:2:{s:2:\"id\";s:2:\"17\";s:4:\"name\";s:14:\"Каталог\";}i:1;a:2:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:16:\"Телефоны\";}i:2;a:2:{s:2:\"id\";s:1:\"4\";s:4:\"name\";s:20:\"Телефоны HTC\";}}','','',0.00,0.00,NULL,0,2,NULL,'Цвет','Вес','','','','','','','','',1,0,0,0,0,0,0,0,0,0,0),
  (5,1,0,1,'Телефоны Nokia','5-telefony-nokia','a:3:{i:0;a:2:{s:2:\"id\";s:2:\"17\";s:4:\"name\";s:14:\"Каталог\";}i:1;a:2:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:16:\"Телефоны\";}i:2;a:2:{s:2:\"id\";s:1:\"5\";s:4:\"name\";s:22:\"Телефоны Nokia\";}}','','',0.00,0.00,NULL,0,1,NULL,'Разрешение экрана','Карта памяти','Поддержка WiFi','','','','','','','',1,0,0,0,0,0,0,0,0,0,0),
  (6,1,0,1,'Телефоны Apple','6-telefony-apple','a:3:{i:0;a:2:{s:2:\"id\";s:2:\"17\";s:4:\"name\";s:14:\"Каталог\";}i:1;a:2:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:16:\"Телефоны\";}i:2;a:2:{s:2:\"id\";s:1:\"6\";s:4:\"name\";s:22:\"Телефоны Apple\";}}','','',0.00,0.00,NULL,0,0,NULL,'','','','','','','','','','',1,0,0,0,0,0,0,0,0,0,0),
  (7,4,1,0,'HTC Evo 3D','7-htc-evo-3d','a:4:{i:0;a:2:{s:2:\"id\";s:2:\"17\";s:4:\"name\";s:14:\"Каталог\";}i:1;a:2:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:16:\"Телефоны\";}i:2;a:2:{s:2:\"id\";s:1:\"4\";s:4:\"name\";s:20:\"Телефоны HTC\";}i:3;a:2:{s:2:\"id\";s:1:\"7\";s:4:\"name\";s:10:\"HTC Evo 3D\";}}','<p>\r\n	А вот по мнению аналитиков фокус-группа интегрирована. Отсюда естественно следует, что торговая марка специфицирует продуктовый ассортимент, не считаясь с затратами. Взаимодействие корпорации и клиента последовательно порождает популярный нестандартный подход, отвоевывая рыночный сегмент. Стимулирование сбыта, на первый взгляд, категорически специфицирует культурный медиаплан, осознав маркетинг как часть производства.</p>','',15000.00,10000.00,0,5,NULL,2,'','','','','','','','','','',1,0,-10800,-10800,0,0,0,0,0,0,0),
  (8,3,4,0,'Jeep Cheerokee','8-jeep-cheerokee','a:4:{i:0;a:2:{s:2:\"id\";s:2:\"17\";s:4:\"name\";s:14:\"Каталог\";}i:1;a:2:{s:2:\"id\";s:1:\"2\";s:4:\"name\";s:20:\"Автомобили\";}i:2;a:2:{s:2:\"id\";s:1:\"3\";s:4:\"name\";s:24:\"Внедорожники\";}i:3;a:2:{s:2:\"id\";s:1:\"8\";s:4:\"name\";s:14:\"Jeep Cheerokee\";}}','<p>\r\n	Product placement упорядочивает нишевый проект, не считаясь с затратами. CTR восстанавливает медиабизнес, работая над проектом. Соц-дем характеристика аудитории требовальна к креативу. Медиаплан ригиден как никогда. Еще Траут показал, что ассортиментная политика предприятия масштабирует конструктивный мониторинг активности, учитывая современные тенденции. Эволюция мерчандайзинга все еще интересна для многих.</p>','3',1500.00,1300.00,0,6,NULL,2,'красный','10','','','','','','','','',1,0,-10800,-10800,0,0,0,0,0,0,0),
  (9,4,1,0,'HTC One X','9-htc-one-x','a:4:{i:0;a:2:{s:2:\"id\";s:2:\"17\";s:4:\"name\";s:14:\"Каталог\";}i:1;a:2:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:16:\"Телефоны\";}i:2;a:2:{s:2:\"id\";s:1:\"4\";s:4:\"name\";s:20:\"Телефоны HTC\";}i:3;a:2:{s:2:\"id\";s:1:\"9\";s:4:\"name\";s:9:\"HTC One X\";}}','<p>\r\n	Рекламное сообщество определяет ребрендинг, расширяя долю рынка. Каждая сфера рынка, как следует из вышесказанного, многопланово специфицирует ролевой целевой трафик, осознавая социальную ответственность бизнеса. Ассортиментная политика предприятия стремительно искажает презентационный материал, учитывая современные тенденции. Традиционный канал, отбрасывая подробности, непосредственно ускоряет имидж, используя опыт предыдущих кампаний.</p>','',17000.00,12000.00,0,5,NULL,2,'','','','','','','','','','',1,0,-10800,-10800,0,0,0,0,0,0,0),
  (10,4,1,0,'HTC Sensation','10-htc-sensation','a:4:{i:0;a:2:{s:2:\"id\";s:2:\"17\";s:4:\"name\";s:14:\"Каталог\";}i:1;a:2:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:16:\"Телефоны\";}i:2;a:2:{s:2:\"id\";s:1:\"4\";s:4:\"name\";s:20:\"Телефоны HTC\";}i:3;a:2:{s:2:\"id\";s:2:\"10\";s:4:\"name\";s:13:\"HTC Sensation\";}}','','',18000.00,12500.00,0,5,NULL,2,'','','','','','','','','','',1,0,-10800,-10800,0,0,0,0,0,0,0),
  (11,6,1,0,'iPhone 4S','11-iphone-4s','a:4:{i:0;a:2:{s:2:\"id\";s:2:\"17\";s:4:\"name\";s:14:\"Каталог\";}i:1;a:2:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:16:\"Телефоны\";}i:2;a:2:{s:2:\"id\";s:1:\"6\";s:4:\"name\";s:22:\"Телефоны Apple\";}i:3;a:2:{s:2:\"id\";s:2:\"11\";s:4:\"name\";s:9:\"iPhone 4S\";}}','','',0.00,0.00,0,7,NULL,2,'','','','','','','','','','',1,0,0,0,0,0,0,0,0,0,0),
  (12,6,1,0,'iPhone 3GS','12-iphone-3gs','a:4:{i:0;a:2:{s:2:\"id\";s:2:\"17\";s:4:\"name\";s:14:\"Каталог\";}i:1;a:2:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:16:\"Телефоны\";}i:2;a:2:{s:2:\"id\";s:1:\"6\";s:4:\"name\";s:22:\"Телефоны Apple\";}i:3;a:2:{s:2:\"id\";s:2:\"12\";s:4:\"name\";s:10:\"iPhone 3GS\";}}','','',0.00,0.00,0,7,NULL,2,'','','','','','','','','','',1,0,-10800,-10800,0,0,0,0,0,0,0),
  (13,6,1,0,'iPhone 4','13-iphone-4','a:4:{i:0;a:2:{s:2:\"id\";s:2:\"17\";s:4:\"name\";s:14:\"Каталог\";}i:1;a:2:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:16:\"Телефоны\";}i:2;a:2:{s:2:\"id\";s:1:\"6\";s:4:\"name\";s:22:\"Телефоны Apple\";}i:3;a:2:{s:2:\"id\";s:2:\"13\";s:4:\"name\";s:8:\"iPhone 4\";}}','','',0.00,0.00,0,7,NULL,2,'','','','','','','','','','',1,0,-10800,-10800,0,0,0,0,0,0,0),
  (14,5,1,0,'Nokia 500','14-nokia-500','a:4:{i:0;a:2:{s:2:\"id\";s:2:\"17\";s:4:\"name\";s:14:\"Каталог\";}i:1;a:2:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:16:\"Телефоны\";}i:2;a:2:{s:2:\"id\";s:1:\"5\";s:4:\"name\";s:22:\"Телефоны Nokia\";}i:3;a:2:{s:2:\"id\";s:2:\"14\";s:4:\"name\";s:9:\"Nokia 500\";}}','','',0.00,0.00,0,8,0,2,'','','','','','','','','','',1,0,-10800,-10800,0,0,0,0,0,0,0),
  (15,5,1,0,'Nokia N9','15-nokia-n9','a:4:{i:0;a:2:{s:2:\"id\";s:2:\"17\";s:4:\"name\";s:14:\"Каталог\";}i:1;a:2:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:16:\"Телефоны\";}i:2;a:2:{s:2:\"id\";s:1:\"5\";s:4:\"name\";s:22:\"Телефоны Nokia\";}i:3;a:2:{s:2:\"id\";s:2:\"15\";s:4:\"name\";s:8:\"Nokia N9\";}}','','',0.00,0.00,4,8,0,2,'','','','','','','','','','',1,0,-10800,-10800,0,0,0,0,0,0,0),
  (17,0,0,1,'Каталог','17-catalog','a:1:{i:0;a:2:{s:2:\"id\";s:2:\"17\";s:4:\"name\";s:14:\"Каталог\";}}','','',0.00,0.00,NULL,0,5,NULL,'','','','','','','','','','',1,0,0,0,0,0,0,0,0,0,0),
  (20,17,0,1,'Велосипеды','20-velosipedy','a:2:{i:0;a:2:{s:2:\"id\";s:2:\"17\";s:4:\"name\";s:14:\"Каталог\";}i:1;a:2:{s:2:\"id\";s:2:\"20\";s:4:\"name\";s:20:\"Велосипеды\";}}',NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,0,0,0,0,0,0,0,0,0,0),
  (28,20,0,1,'Горные','28-gornye','a:3:{i:0;a:2:{s:2:\"id\";s:2:\"17\";s:4:\"name\";s:14:\"Каталог\";}i:1;a:2:{s:2:\"id\";s:2:\"20\";s:4:\"name\";s:20:\"Велосипеды\";}i:2;a:2:{s:2:\"id\";s:2:\"28\";s:4:\"name\";s:12:\"Горные\";}}',NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,0,0,0,0,0,0,0,0,0,0),
  (29,20,0,1,'Городские','29-gorodskie','a:3:{i:0;a:2:{s:2:\"id\";s:2:\"17\";s:4:\"name\";s:14:\"Каталог\";}i:1;a:2:{s:2:\"id\";s:2:\"20\";s:4:\"name\";s:20:\"Велосипеды\";}i:2;a:2:{s:2:\"id\";s:2:\"29\";s:4:\"name\";s:18:\"Городские\";}}',NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,0,0,0,0,0,0,0,0,0,0),
  (30,20,0,1,'Дорожные','30-dorozhnye','a:3:{i:0;a:2:{s:2:\"id\";s:2:\"17\";s:4:\"name\";s:14:\"Каталог\";}i:1;a:2:{s:2:\"id\";s:2:\"20\";s:4:\"name\";s:20:\"Велосипеды\";}i:2;a:2:{s:2:\"id\";s:2:\"30\";s:4:\"name\";s:16:\"Дорожные\";}}',NULL,NULL,NULL,NULL,NULL,NULL,2,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,0,0,0,0,0,0,0,0,0,0),
  (31,28,0,1,'Хардтейл','31-hardteil','a:4:{i:0;a:2:{s:2:\"id\";s:2:\"17\";s:4:\"name\";s:14:\"Каталог\";}i:1;a:2:{s:2:\"id\";s:2:\"20\";s:4:\"name\";s:20:\"Велосипеды\";}i:2;a:2:{s:2:\"id\";s:2:\"28\";s:4:\"name\";s:12:\"Горные\";}i:3;a:2:{s:2:\"id\";s:2:\"31\";s:4:\"name\";s:16:\"Хардтейл\";}}',NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,0,0,0,0,0,0,0,0,0,0),
  (32,28,0,1,'Двухподвес','32-dvuhpodves','a:4:{i:0;a:2:{s:2:\"id\";s:2:\"17\";s:4:\"name\";s:14:\"Каталог\";}i:1;a:2:{s:2:\"id\";s:2:\"20\";s:4:\"name\";s:20:\"Велосипеды\";}i:2;a:2:{s:2:\"id\";s:2:\"28\";s:4:\"name\";s:12:\"Горные\";}i:3;a:2:{s:2:\"id\";s:2:\"32\";s:4:\"name\";s:20:\"Двухподвес\";}}','','',0.00,0.00,0,0,1,2,'','','','','','','','','','',1,0,1366733978,1366733978,0,0,0,0,0,0,0),
  (33,32,5,0,'TERRA 918 disk','33-terra-918-disk','a:5:{i:0;a:2:{s:2:\"id\";s:2:\"17\";s:4:\"name\";s:14:\"Каталог\";}i:1;a:2:{s:2:\"id\";s:2:\"20\";s:4:\"name\";s:20:\"Велосипеды\";}i:2;a:2:{s:2:\"id\";s:2:\"28\";s:4:\"name\";s:12:\"Горные\";}i:3;a:2:{s:2:\"id\";s:2:\"32\";s:4:\"name\";s:20:\"Двухподвес\";}i:4;a:2:{s:2:\"id\";s:2:\"33\";s:4:\"name\";s:14:\"TERRA 918 disk\";}}','<p>\r\n	Описание велосипеда.</p>','',9200.00,0.00,0,9,NULL,2,'','','','','','','','','','',1,0,-10800,-10800,0,0,0,0,0,0,0),
  (34,32,5,0,'4212','34-4212','a:5:{i:0;a:2:{s:2:\"id\";s:2:\"17\";s:4:\"name\";s:14:\"Каталог\";}i:1;a:2:{s:2:\"id\";s:2:\"20\";s:4:\"name\";s:20:\"Велосипеды\";}i:2;a:2:{s:2:\"id\";s:2:\"28\";s:4:\"name\";s:12:\"Горные\";}i:3;a:2:{s:2:\"id\";s:2:\"32\";s:4:\"name\";s:20:\"Двухподвес\";}i:4;a:2:{s:2:\"id\";s:2:\"34\";s:4:\"name\";s:4:\"4212\";}}','<p>\r\n	Привет МиР!</p>\r\n<p>\r\n	Трололо!</p>','',28390.00,0.00,0,9,NULL,2,'','','','','','','','','','',1,0,-10800,-10800,0,0,0,0,0,0,0),
  (35,4,1,0,'HTC Sensation XE','35-htc-sensation-xe','a:4:{i:0;a:2:{s:2:\"id\";s:2:\"17\";s:4:\"name\";s:14:\"Каталог\";}i:1;a:2:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:16:\"Телефоны\";}i:2;a:2:{s:2:\"id\";s:1:\"4\";s:4:\"name\";s:20:\"Телефоны HTC\";}i:3;a:2:{s:2:\"id\";s:2:\"35\";s:4:\"name\";s:16:\"HTC Sensation XE\";}}','','',15000.00,8000.00,4,5,NULL,2,'','','','','','','','','','',1,0,-10800,-10800,0,0,0,0,0,0,0),
  (36,NULL,3,NULL,NULL,'36-','a:1:{i:0;a:2:{s:2:\"id\";s:2:\"36\";s:4:\"name\";N;}}',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,0,0,0,0,0,0,0,0,0,1);
/*!40000 ALTER TABLE `catalog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `catalog_gallery`
--

DROP TABLE IF EXISTS `catalog_gallery`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `catalog_gallery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cat_id` int(11) DEFAULT NULL,
  `image` varchar(250) DEFAULT NULL,
  `thumb` varchar(250) DEFAULT NULL,
  `hidden` tinyint(4) DEFAULT NULL,
  `main` tinyint(4) DEFAULT NULL,
  `pos` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=87 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `catalog_gallery`
--

LOCK TABLES `catalog_gallery` WRITE;
/*!40000 ALTER TABLE `catalog_gallery` DISABLE KEYS */;
INSERT INTO `catalog_gallery` VALUES (7,7,'/files/catalog/gallery/0007/7_20110627_EVO_3D_3Views_full1200x800.jpg','/files/catalog/gallery/0007/7_20110627_EVO_3D_3Views_full1200x800.jpg',NULL,1,NULL),(8,8,'/files/catalog/gallery/0008/8_img_model.jpg','/files/catalog/gallery/0008/8_img_model.jpg',NULL,1,NULL),(10,9,'/files/catalog/gallery/0009/10_slide-1-white.png','/files/catalog/gallery/0009/10_slide-1-white.png',NULL,0,NULL),(11,9,'/files/catalog/gallery/0009/11_slide-4.png','/files/catalog/gallery/0009/11_slide-4.png',NULL,0,NULL),(12,9,'/files/catalog/gallery/0009/12_slide-5.png','/files/catalog/gallery/0009/12_slide-5.png',NULL,1,NULL),(13,9,'/files/catalog/gallery/0009/13_slide-6.jpg','/files/catalog/gallery/0009/13_slide-6.jpg',NULL,0,NULL),(14,10,'/files/catalog/gallery/0010/14_ksp2.png','/files/catalog/gallery/0010/14_ksp2.png',NULL,0,NULL),(15,10,'/files/catalog/gallery/0010/15_ksp1.png','/files/catalog/gallery/0010/15_ksp1.png',NULL,0,NULL),(16,10,'/files/catalog/gallery/0010/16_ksp3.png.jpg','/files/catalog/gallery/0010/16_ksp3.png.jpg',NULL,1,NULL),(17,11,'/files/catalog/gallery/0011/17_i.jpg','/files/catalog/gallery/0011/17_i.jpg',NULL,1,NULL),(18,11,'/files/catalog/gallery/0011/18_2.jpg','/files/catalog/gallery/0011/18_2.jpg',NULL,NULL,NULL),(19,11,'/files/catalog/gallery/0011/19_3.jpg','/files/catalog/gallery/0011/19_3.jpg',NULL,NULL,NULL),(20,12,'/files/catalog/gallery/0012/20_1.jpg','/files/catalog/gallery/0012/20_1.jpg',NULL,0,1),(21,12,'/files/catalog/gallery/0012/21_2.jpg','/files/catalog/gallery/0012/21_2.jpg',NULL,0,2),(22,12,'/files/catalog/gallery/0012/22_3.jpg','/files/catalog/gallery/0012/22_3.jpg',NULL,1,0),(23,12,'/files/catalog/gallery/0012/23_4.jpg','/files/catalog/gallery/0012/23_4.jpg',NULL,0,3),(24,13,'/files/catalog/gallery/0013/24_1.jpg','/files/catalog/gallery/0013/24_1.jpg',NULL,1,NULL),(25,13,'/files/catalog/gallery/0013/25_2.jpg','/files/catalog/gallery/0013/25_2.jpg',NULL,0,NULL),(26,13,'/files/catalog/gallery/0013/26_3.jpg','/files/catalog/gallery/0013/26_3.jpg',NULL,0,NULL),(27,13,'/files/catalog/gallery/0013/27_4.jpg','/files/catalog/gallery/0013/27_4.jpg',NULL,0,NULL),(28,14,'/files/catalog/gallery/0014/28_5195061.jpg','/files/catalog/gallery/0014/28_5195061.jpg',NULL,1,NULL),(29,14,'/files/catalog/gallery/0014/29_5195063.jpg','/files/catalog/gallery/0014/29_5195063.jpg',NULL,0,NULL),(30,14,'/files/catalog/gallery/0014/30_5195069.jpg','/files/catalog/gallery/0014/30_5195069.jpg',NULL,0,NULL),(31,14,'/files/catalog/gallery/0014/31_5195071.jpg','/files/catalog/gallery/0014/31_5195071.jpg',NULL,0,NULL),(32,15,'/files/catalog/gallery/0015/32_5434718.jpg','/files/catalog/gallery/0015/32_5434718.jpg',NULL,0,0),(33,15,'/files/catalog/gallery/0015/33_5434722.jpg','/files/catalog/gallery/0015/33_5434722.jpg',NULL,0,2),(34,15,'/files/catalog/gallery/0015/34_5434740.jpg','/files/catalog/gallery/0015/34_5434740.jpg',NULL,0,1),(35,15,'/files/catalog/gallery/0015/35_5434742.jpg','/files/catalog/gallery/0015/35_5434742.jpg',NULL,0,4),(36,15,'/files/catalog/gallery/0015/36_5434744.jpg','/files/catalog/gallery/0015/36_5434744.jpg',NULL,1,3),(37,33,'/files/catalog/gallery/0033/37_data_4321.jpg','/files/catalog/gallery/0033/37_data_4321.jpg',NULL,1,NULL),(38,34,'/files/catalog/gallery/0034/38_data_4159.jpg','/files/catalog/gallery/0034/38_data_4159.jpg',NULL,1,NULL),(86,35,'/files/catalog/gallery/000035/86-35-htc-sensation-xe.jpg','/files/catalog/gallery/000035/86-76958f.jpg',0,1,100);
/*!40000 ALTER TABLE `catalog_gallery` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `category_banner`
--

DROP TABLE IF EXISTS `category_banner`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `category_banner` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) DEFAULT NULL,
  `deleted` int(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `category_banner`
--

LOCK TABLES `category_banner` WRITE;
/*!40000 ALTER TABLE `category_banner` DISABLE KEYS */;
INSERT INTO `category_banner` VALUES (1,'Реклама',0),(2,'Партнер №1',0);
/*!40000 ALTER TABLE `category_banner` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `delivery`
--

DROP TABLE IF EXISTS `delivery`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `delivery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) DEFAULT NULL,
  `desc` text,
  `cost` decimal(13,2) DEFAULT NULL,
  `active` tinyint(1) DEFAULT NULL,
  `pos` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delivery`
--

LOCK TABLES `delivery` WRITE;
/*!40000 ALTER TABLE `delivery` DISABLE KEYS */;
INSERT INTO `delivery` VALUES (1,'Курьер',NULL,200.00,1,1),(2,'Почта России','',700.00,1,2),(3,'Самовывоз',NULL,0.00,1,0),(4,'Доставка DHL','',3000.00,1,3);
/*!40000 ALTER TABLE `delivery` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gallery`
--

DROP TABLE IF EXISTS `gallery`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gallery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) DEFAULT NULL,
  `alias` varchar(250) NOT NULL,
  `name` varchar(250) DEFAULT NULL,
  `link` varchar(250) DEFAULT NULL,
  `description` text,
  `image` varchar(250) DEFAULT NULL,
  `pos` int(11) DEFAULT NULL,
  `main` tinyint(4) DEFAULT NULL,
  `hidden` tinyint(4) DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gallery`
--

LOCK TABLES `gallery` WRITE;
/*!40000 ALTER TABLE `gallery` DISABLE KEYS */;
INSERT INTO `gallery` VALUES (24,7,'krasnyi-cvetok','Красный цветок','','<p>Код, согласно традиционным представлениям, теоретически возможен. Гомеостаз понимает эмпирический эскапизм, хотя Уотсон это отрицал. Аномия просветляет потребительский онтогенез речи, тем не менее как только ортодоксальность окончательно возобладает, даже эта маленькая лазейка будет закрыта.</p>\r\n<p>НЛП позволяет вам точно определить какие изменения в субьективном опыте надо произвести, чтобы воспитание самопроизвольно. Эскапизм, конечно, интегрирует конвергентный эскапизм, как и предсказывают практические аспекты использования принципов гештальпсихологии в области восприятия, обучения, развития психики, социальных взаимоотношений.</p>\r\n<p>Конформизм просветляет возрастной код, в частности, \"тюремные психозы\", индуцируемые при различных психопатологических типологиях.</p>','/files/gallery/0007/24_Chrysanthemum.jpg',2,0,0,0),(25,7,'peizazh','Пейзаж','','<p style=\"text-align: left;\">\r\n	Бессознательное, в представлении Морено, интегрирует конвергентный код, что лишний раз подтверждает правоту З.Фрейда. Репрезентативная система интегрирует понимающий психоанализ, это обозначено Ли Россом как фундаментальная ошибка атрибуции, которая прослеживается во многих экспериментах.</p>\r\n<p style=\"text-align: left;\">\r\n	Дело в том, что психосоматика дает филосовский объект, как и предсказывает теория о бесполезном знании. Сновидение выбирает автоматизм, хотя Уотсон это отрицал. Как было показано выше, эгоцентризм аннигилирует гендер, хотя Уотсон это отрицал.</p>','/files/gallery/0007/25_Desert.jpg',0,0,0,0),(26,7,'belyi-cvetok','Белый цветок','','<p>\r\n	Мышление стабильно. Акцентуация, конечно, откровенна. Стратификация представляет собой кризис, следовательно основной закон психофизики: ощущение изменяется пропорционально логарифму раздражителя .</p>\r\n<p>\r\n	Акцентуированная личность, на первый взгляд, откровенна. Парадигма традиционно понимает гомеостаз, также это подчеркивается в труде Дж.Морено \"Театр Спонтанности\". НЛП позволяет вам точно определить какие изменения в субьективном опыте надо произвести, чтобы воспитание вызывает ассоцианизм, как и предсказывают практические аспекты использования принципов гештальпсихологии в области восприятия, обучения, развития психики, социальных взаимоотношений.</p>','/files/gallery/0007/26_Hydrangeas.jpg',1,0,0,0),(27,8,'meduza','Медуза','','<p>Однако Э.Дюркгейм утверждал, что агрессия отражает ассоцианизм, таким образом осуществляется своего рода связь с темнотой бессознательного. Инсайт интуитивно понятен.</p>\r\n<p>После того как тема сформулирована, стимул понимает экспериментальный интеракционизм, это обозначено Ли Россом как фундаментальная ошибка атрибуции, которая прослеживается во многих экспериментах.</p>\r\n<p>Представленный контент-анализ является психолингвистическим в своей основе, таким образом импульс понимает объект, независимо от психического состояния пациента. Выготский разработал, ориентируясь на методологию марксизма, учение которое утверждает что, тест дает филосовский конформизм, таким образом, стратегия поведения, выгодная отдельному человеку, ведет к коллективному проигрышу.</p>','/files/gallery/0008/27_Jellyfish.jpg',0,0,0,0),(28,8,'koala','Коала','','<p>Филогенез отталкивает субъект, следовательно основной закон психофизики: ощущение изменяется пропорционально логарифму раздражителя . Психическая саморегуляция, согласно традиционным представлениям, просветляет интеллект, следовательно основной закон психофизики: ощущение изменяется пропорционально логарифму раздражителя.</p>\r\n<p>Предсознательное дает психоанализ, что отмечают такие крупнейшие ученые как Фрейд, Адлер, Юнг, Эриксон, Фромм. Восприятие абсурдно отражает субъект, к тому же этот вопрос касается чего-то слишком общего.</p>\r\n<p>Восприятие иллюстрирует эриксоновский гипноз, что вызвало развитие функционализма и сравнительно-психологических исследований поведения. Бессознательное последовательно просветляет стимул, таким образом, стратегия поведения, выгодная отдельному человеку, ведет к коллективному проигрышу.</p>','/files/gallery/0008/28_Koala.jpg',1,0,0,0),(29,8,'zamok','Замок','','<p>Как мы уже знаем, фрустрация интегрирует эмпирический стимул, хотя Уотсон это отрицал. Эгоцентризм отталкивает опасный контраст, Гоббс одним из первых осветил эту проблему с позиций психологии. Все это побудило нас обратить внимание на то, что гетерогенность осознаёт кризис, к тому же этот вопрос касается чего-то слишком общего.</p>\r\n<p>Как отмечает Жан Пиаже, конформизм последовательно аннигилирует ускоряющийся страх, что вызвало развитие функционализма и сравнительно-психологических исследований поведения.</p>\r\n<p>Очевидно, что бессознательное многопланово представляет собой потребительский контраст, и это неудивительно, если речь о персонифицированном характере первичной социализации.</p>','/files/gallery/0008/29_Lighthouse.jpg',2,0,0,0),(30,8,'pingviny','Пингвины','','<p>Ассоциация, как бы это ни казалось парадоксальным, притягивает филосовский архетип, как и предсказывают практические аспекты использования принципов гештальпсихологии в области восприятия, обучения, развития психики, социальных взаимоотношений.</p>\r\n<p>Структурный голод столь же важен для жизни, как и сознание выбирает закон, в полном соответствии с основными законами развития человека. Репрезентативная система, по определению, концептуально представляет собой девиантный конформизм, Гоббс одним из первых осветил эту проблему с позиций психологии.</p>\r\n<p>Гомеостаз мгновенно вызывает субъект, что вызвало развитие функционализма и сравнительно-психологических исследований поведения. Перцепция, конечно, иллюстрирует бихевиоризм, это обозначено Ли Россом как фундаментальная ошибка атрибуции, которая прослеживается во многих экспериментах.</p>','/files/gallery/0008/30_Penguins.jpg',3,0,0,0),(31,8,'tyuljpany','Тюльпаны','','<p>Идентификация, в представлении Морено, неустойчиво начинает конформизм, тем не менее как только ортодоксальность окончательно возобладает, даже эта маленькая лазейка будет закрыта.</p>\r\n<p>После того как тема сформулирована, действие параллельно отражает институциональный психоанализ, это обозначено Ли Россом как фундаментальная ошибка атрибуции, которая прослеживается во многих экспериментах. Апперцепция последовательно притягивает гештальт, здесь описывается централизующий процесс или создание нового центра личности.</p>\r\n<p>Этот концепт элиминирует концепт &laquo;нормального&raquo;, однако толпа вызывает позитивистский стимул, как и предсказывают практические аспекты использования принципов гештальпсихологии в области восприятия, обучения, развития психики, социальных взаимоотношений.</p>','/files/gallery/0008/31_Tulips.jpg',4,0,0,0),(38,7,'stiv','Стив',NULL,NULL,'/files/gallery/0007/38_t_hero.png',3,0,0,1),(40,7,'skidka','Скидка',NULL,NULL,'/files/gallery/0007/40_z_095b503c.jpg',5,0,0,1);
/*!40000 ALTER TABLE `gallery` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gallery_category`
--

DROP TABLE IF EXISTS `gallery_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gallery_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) DEFAULT NULL,
  `middle_method` tinyint(4) DEFAULT '1',
  `middle_width` int(11) DEFAULT '200',
  `middle_height` int(11) DEFAULT '200',
  `thumb_method` tinyint(4) DEFAULT '1',
  `thumb_width` int(11) DEFAULT '100',
  `thumb_height` int(11) DEFAULT '100',
  `target` varchar(10) DEFAULT NULL,
  `image` varchar(250) DEFAULT NULL,
  `perpage` int(11) DEFAULT NULL,
  `color` varchar(20) DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gallery_category`
--

LOCK TABLES `gallery_category` WRITE;
/*!40000 ALTER TABLE `gallery_category` DISABLE KEYS */;
INSERT INTO `gallery_category` VALUES (7,'Портфолио',1,400,400,1,200,200,'_self','/files/gallery/0007/25_Desert.jpg',20,'ffffff',0),(8,'Картинки',1,400,400,1,200,200,'_gallery','/files/gallery/0008/27_Jellyfish.jpg',20,'ffffff',0);
/*!40000 ALTER TABLE `gallery_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `guestbook`
--

DROP TABLE IF EXISTS `guestbook`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `guestbook` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `link` int(11) DEFAULT NULL,
  `name` varchar(250) DEFAULT NULL,
  `email` varchar(250) DEFAULT NULL,
  `site` varchar(250) DEFAULT NULL,
  `city` varchar(250) DEFAULT NULL,
  `date` int(11) DEFAULT NULL,
  `ip` varchar(15) DEFAULT NULL,
  `message` text,
  `answer` text,
  `hidden` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `guestbook`
--

LOCK TABLES `guestbook` WRITE;
/*!40000 ALTER TABLE `guestbook` DISABLE KEYS */;
INSERT INTO `guestbook` VALUES (1,44,'Николай','keltanas@gmail.com',NULL,NULL,1331671233,'127.0.0.1','Привет мир!',NULL,0),(2,44,'Юзер','keltanas@gmail.com',NULL,NULL,1331672650,'127.0.0.1','Лорем\r\nИпсум',NULL,0),(3,44,'Хакер','keltanas@gmail.com',NULL,NULL,1331672701,'127.0.0.1','Ща все сломаю\r\nconsole.log(\'password\')','',0),(4,44,'Юзер','user@example.vom',NULL,NULL,1331749547,'127.0.0.1','Привет други!\r\nПроверка гостевой.',NULL,0),(5,44,'Юзер','user@example.vom',NULL,NULL,1331749622,'192.168.1.2','Привет други!\r\nПроверка гостевой.','',0),(6,44,'Поликарп','polikarp@mail.tv',NULL,NULL,1331749785,'127.0.0.1','Хелло пипл!\r\nЧе тут происходит?','',0),(7,44,'Nikolay','nikolay@ermin.ry',NULL,NULL,1334351085,'127.0.0.1','Привет! Классная CMS я считаю!\r\nКак можно ее скачать?','Ее можно скачать на гитхабе',0);
/*!40000 ALTER TABLE `guestbook` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `log`
--

DROP TABLE IF EXISTS `log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL,
  `object` varchar(250) NOT NULL,
  `action` varchar(250) NOT NULL,
  `timestamp` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `manufacturers`
--

DROP TABLE IF EXISTS `manufacturers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `manufacturers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) DEFAULT NULL,
  `phone` varchar(250) DEFAULT NULL,
  `email` varchar(250) DEFAULT NULL,
  `site` varchar(250) NOT NULL,
  `address` text,
  `image` varchar(250) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `manufacturers`
--

LOCK TABLES `manufacturers` WRITE;
/*!40000 ALTER TABLE `manufacturers` DISABLE KEYS */;
INSERT INTO `manufacturers` VALUES (6,'Jeep','','','','','/files/manufacturers/jeep.jpg',''),(5,'HTC','','keltanas@gmail.com','','','/files/manufacturers/HTC-Logo.jpg',''),(7,'Apple','','','','','/files/manufacturers/apple-logo.jpg',''),(8,'Nokia','','','','','/files/manufacturers/nokia-logo.jpg',''),(9,'Forward','','','','','/files/manufacturers/Forward-logo.jpg','');
/*!40000 ALTER TABLE `manufacturers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `material`
--

DROP TABLE IF EXISTS `material`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `material` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `active` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `material`
--

LOCK TABLES `material` WRITE;
/*!40000 ALTER TABLE `material` DISABLE KEYS */;
INSERT INTO `material` VALUES (1,'Натуральная кожа','',1),(2,'Искуственная кожа','',1),(3,'Эко-кожа','',1),(4,'Пластик','',1),(5,'Аллюминий','',1);
/*!40000 ALTER TABLE `material` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `metro`
--

DROP TABLE IF EXISTS `metro`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `metro` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `city_id` int(10) NOT NULL,
  `lat` decimal(10,6) DEFAULT NULL,
  `lng` decimal(10,6) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=226 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `metro`
--

LOCK TABLES `metro` WRITE;
/*!40000 ALTER TABLE `metro` DISABLE KEYS */;
INSERT INTO `metro` VALUES (1,'Авиамоторная',1,55.751579,37.716976),(2,'Автозаводская',1,55.707748,37.657497),(3,'Академическая',1,55.687790,37.573387),(4,'Александровский Сад',1,55.752258,37.610222),(5,'Алексеевская',1,55.807789,37.638718),(6,'Алтуфьево',1,55.897923,37.587139),(7,'Аннино',1,55.583691,37.596783),(8,'Арбатская (ар.)',1,55.755787,37.617634),(9,'Арбатская (фил.)',1,55.755787,37.617634),(10,'Аэропорт',1,55.800671,37.532200),(11,'Бабушкинская',1,55.869858,37.664242),(12,'Багратионовская',1,55.743786,37.497715),(13,'Баррикадная',1,55.760754,37.581234),(14,'Бауманская',1,55.772366,37.678825),(15,'Беговая',1,55.773590,37.547138),(16,'Белорусская',1,55.776920,37.584126),(17,'Беляево',1,55.642654,37.526272),(18,'Бибирево',1,55.883926,37.603630),(19,'Библиотека имени Ленина',1,55.751389,37.609722),(20,'Битцевский Парк',1,55.599167,37.556946),(21,'Боровицкая',1,55.752304,37.612877),(22,'Ботанический Сад',1,55.845478,37.638409),(23,'Братиславская',1,55.659416,37.750462),(24,'Бульвар Дмитрия Донского',1,55.570244,37.577145),(25,'Бунинская аллея',1,55.537945,37.515362),(26,'Варшавская',1,55.653545,37.620480),(27,'ВДНХ',1,55.820732,37.640697),(28,'Владыкино',1,55.847183,37.589916),(29,'Водный Стадион',1,55.839844,37.486820),(30,'Войковская',1,55.818790,37.498028),(31,'Волгоградский Проспект',1,55.724899,37.687134),(32,'Волжская',1,55.690865,37.754219),(33,'Волоколамская (стр.)',1,55.755787,37.617634),(34,'Воробьевы горы',1,55.710308,37.559219),(35,'Выхино',1,55.715805,37.818024),(36,'Горчакова ул.',1,55.541962,37.531132),(37,'Деловой центр',1,55.749222,37.543285),(38,'Динамо',1,55.789749,37.558189),(39,'Дмитровская',1,55.807999,37.581066),(40,'Добрынинская',1,55.728992,37.622787),(41,'Домодедовская',1,55.610634,37.718033),(42,'Дубровка',1,55.717850,37.676556),(43,'Измайловская',1,55.787731,37.781597),(44,'Калужская',1,55.656601,37.539955),(45,'Кантемировская',1,55.635803,37.656513),(46,'Каховская',1,55.652985,37.598343),(47,'Каширская',1,55.655067,37.648666),(48,'Киевская',1,55.743305,37.565807),(49,'Китай-Город',1,55.755367,37.632343),(50,'Кожуховская',1,55.706142,37.685642),(51,'Коломенская',1,55.677906,37.663727),(52,'Комсомольская',1,55.775448,37.655964),(53,'Коньково',1,55.633553,37.519413),(54,'Красногвардейская',1,55.613853,37.744473),(55,'Краснопресненская',1,55.760216,37.577251),(56,'Красносельская',1,55.779964,37.666084),(57,'Красные Ворота',1,55.768875,37.649067),(58,'Крестьянская застава',1,55.732269,37.665592),(59,'Кропоткинская',1,55.745346,37.603348),(60,'Крылатское',1,55.756790,37.408096),(61,'Кузнецкий Мост',1,55.761509,37.624149),(62,'Кузьминки',1,55.705429,37.765682),(63,'Кунцевская',1,55.730698,37.445919),(64,'Курская',1,55.758183,37.661484),(65,'Кутузовская',1,55.740040,37.534569),(66,'Ленинский Проспект',1,55.707661,37.586185),(67,'Лубянка',1,55.759342,37.626850),(68,'Люблино',1,55.676300,37.761852),(69,'Марксистская',1,55.740913,37.656425),(70,'Марьино',1,55.650089,37.743809),(71,'Маяковская',1,55.769936,37.596046),(72,'Медведково',1,55.887074,37.661388),(73,'Международная',1,55.748329,37.532825),(74,'Менделеевская',1,55.781910,37.598583),(75,'Митино (стр.)',1,55.872944,37.451054),(76,'Молодежная',1,55.740807,37.416832),(77,'Нагатинская',1,55.682728,37.621819),(78,'Нагорная',1,55.672981,37.610760),(79,'Нахимовский Проспект',1,55.662846,37.605583),(80,'Новогиреево',1,55.751801,37.816002),(81,'Новокузнецкая',1,55.742382,37.629257),(82,'Новослободская',1,55.779514,37.601166),(83,'Новые Черёмушки',1,55.670261,37.554600),(84,'Октябрьская',1,55.730255,37.612240),(85,'Октябрьское Поле',1,55.793526,37.493404),(86,'Орехово',1,55.613449,37.694496),(87,'Отрадное',1,55.863319,37.604694),(88,'Охотный Ряд',1,55.756706,37.615906),(89,'Павелецкая',1,55.730663,37.636787),(90,'Парк Культуры',1,55.735645,37.594002),(91,'Парк Победы',1,55.736301,37.517002),(92,'Партизанская',1,55.788437,37.749626),(93,'Первомайская',1,55.794617,37.799335),(94,'Перово',1,55.751095,37.785938),(95,'Петровско-Разумовская',1,55.836391,37.575424),(96,'Печатники',1,55.692928,37.727657),(97,'Пионерская',1,55.736027,37.467033),(98,'Планерная',1,55.860649,37.436306),(99,'Площадь Ильича',1,55.747047,37.680367),(100,'Площадь Революции',1,55.756542,37.621658),(101,'Полежаевская',1,55.777554,37.518940),(102,'Полянка',1,55.736771,37.618443),(103,'Пражская',1,55.611889,37.603813),(104,'Преображенская Площадь',1,55.796104,37.715588),(105,'Пролетарская',1,55.731724,37.665592),(106,'Проспект Вернадского',1,55.676716,37.505573),(107,'Проспект Мира',1,55.780720,37.633446),(108,'Профсоюзная',1,55.677929,37.562840),(109,'Пушкинская',1,55.765953,37.604179),(110,'Речной Вокзал',1,55.855015,37.476139),(111,'Рижская',1,55.792484,37.636097),(112,'Римская',1,55.746445,37.680157),(113,'Рязанский Проспект',1,55.716949,37.793243),(114,'Савеловская',1,55.794029,37.589176),(115,'Свиблово',1,55.855206,37.652699),(116,'Севастопольская',1,55.651352,37.598354),(117,'Семеновская',1,55.783100,37.719341),(118,'Серпуховская',1,55.726791,37.625240),(119,'Скобелевская',1,55.547405,37.555481),(120,'Смоленская (ар.)',1,55.755787,37.617634),(121,'Смоленская (фил.)',1,55.755787,37.617634),(122,'Сокол',1,55.804844,37.515484),(123,'Сокольники',1,55.789284,37.679726),(124,'Спортивная',1,55.723099,37.563766),(125,'Старокачаловская',1,55.569706,37.584190),(126,'Строгино (стр.)',1,55.806946,37.498055),(127,'Студенческая',1,55.738907,37.548126),(128,'Сухаревская',1,55.772308,37.632507),(129,'Сходненская',1,55.850266,37.439934),(130,'Таганская',1,55.740425,37.653362),(131,'Тверская',1,55.765038,37.605007),(132,'Театральная',1,55.758747,37.617695),(133,'Текстильщики',1,55.708691,37.730728),(134,'Теплый Стан',1,55.618874,37.507046),(135,'Тимирязевская',1,55.819046,37.575466),(136,'Третьяковская',1,55.740696,37.625576),(137,'Тульская',1,55.708702,37.622494),(138,'Тургеневская',1,55.766014,37.636921),(139,'Тушинская',1,55.826923,37.437359),(140,'Улица 1905 года',1,55.764763,37.561371),(141,'Улица Академика Янгеля',1,55.595482,37.601173),(142,'Улица Подбельского',1,55.814461,37.734020),(143,'Университет',1,55.692574,37.534542),(144,'Ушакова Адмирала',1,55.545261,37.542072),(145,'Филевский Парк',1,55.739540,37.483265),(146,'Фили',1,55.746048,37.514874),(147,'Фрунзенская',1,55.727463,37.580502),(148,'Царицыно',1,55.621056,37.669456),(149,'Цветной Бульвар',1,55.771656,37.620575),(150,'Черкизовская',1,55.803844,37.744694),(151,'Чертановская',1,55.640709,37.605751),(152,'Чеховская',1,55.765865,37.608139),(153,'Чистые Пруды',1,55.764904,37.638344),(154,'Чкаловская',1,55.756001,37.658749),(155,'Шаболовская',1,55.718826,37.607914),(156,'Шоссе Энтузиастов',1,55.758167,37.751667),(157,'Щелковская',1,55.809608,37.798588),(158,'Щукинская',1,55.808510,37.464344),(159,'Электрозаводская',1,55.782024,37.705219),(160,'Юго-Западная',1,55.663681,37.483196),(161,'Южная',1,55.622299,37.608994),(162,'Ясенево',1,55.606220,37.533340),(163,'Девяткино',2,60.050182,30.443045),(164,'Гражданский проспект',2,60.034969,30.418224),(165,'Академическая',2,60.012806,30.396044),(166,'Политехническая',2,60.008942,30.370907),(167,'Площадь Мужества',2,59.999828,30.366159),(168,'Лесная',2,59.984947,30.344259),(169,'Выборгская',2,59.971649,30.348478),(170,'Площадь Ленина',2,59.957260,30.355383),(171,'Чернышевская',2,59.944530,30.359919),(172,'Площадь Восстания',2,59.930279,30.361069),(173,'Владимирская',2,59.927628,30.347898),(174,'Пушкинская',2,59.920650,30.329599),(175,'Балтийская',2,59.907211,30.299578),(176,'Нарвская',2,59.901218,30.274908),(177,'Кировский завод',2,59.879688,30.261921),(178,'Автово',2,59.867325,30.261337),(179,'Ленинский проспект',2,59.851170,30.268274),(180,'Проспект Ветеранов',2,59.841835,30.251949),(181,'Парнас',2,60.066990,30.333839),(182,'Проспект Просвещения',2,60.051456,30.332544),(183,'Озерки',2,60.037098,30.321495),(184,'Удельная',2,60.016697,30.315607),(185,'Пионерская',2,60.002487,30.296759),(186,'Чёрная речка',2,59.985455,30.300833),(187,'Петроградская',2,59.966389,30.311293),(188,'Горьковская',2,59.956112,30.318890),(189,'Невский проспект',2,59.935051,30.329725),(190,'Сенная площадь',2,59.927135,30.320316),(191,'Технологический институт',2,59.916512,30.318485),(192,'Фрунзенская',2,59.906273,30.317450),(193,'Московские ворота',2,59.891788,30.317873),(194,'Электросила',2,59.879189,30.318659),(195,'Парк Победы',2,59.866344,30.321802),(196,'Московская',2,59.851341,30.321548),(197,'Звёздная',2,59.833241,30.349428),(198,'Купчино',2,59.829781,30.375702),(199,'Приморская',2,59.948521,30.234470),(200,'Василеостровская',2,59.942577,30.278254),(201,'Гостиный двор',2,59.933933,30.333410),(202,'Маяковская',2,59.931366,30.354645),(203,'Площадь Александра Невского-1',2,NULL,NULL),(204,'Елизаровская',2,59.896690,30.423656),(205,'Ломоносовская',2,59.877342,30.441715),(206,'Пролетарская',2,59.865215,30.470264),(207,'Обухово',2,59.848709,30.457743),(208,'Рыбацкое',2,59.830986,30.501259),(209,'Спасская',2,59.927135,30.320316),(210,'Достоевская',2,59.928234,30.346029),(211,'Лиговский проспект',2,59.920811,30.355055),(212,'Площадь Александра Невского-2',2,NULL,NULL),(213,'Новочеркасская',2,59.929092,30.411915),(214,'Ладожская',2,59.932430,30.439274),(215,'Проспект Большевиков',2,59.919838,30.466757),(216,'Улица Дыбенко',2,59.907417,30.483311),(217,'Комендантский проспект',2,60.008591,30.258663),(218,'Старая Деревня',2,59.989433,30.255163),(219,'Крестовский остров',2,59.971821,30.259436),(220,'Чкаловская',2,59.961033,30.292006),(221,'Спортивная',2,59.952026,30.291338),(222,'Садовая',2,59.926739,30.317753),(223,'Звенигородская',2,59.920650,30.329599),(224,'Волковская',2,59.896023,30.357540),(225,'Трубная',1,55.767445,37.622059);
/*!40000 ALTER TABLE `metro` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `module`
--

DROP TABLE IF EXISTS `module`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `module` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `path` varchar(250) NOT NULL,
  `config` blob NOT NULL,
  `desc` text NOT NULL,
  `pos` int(11) NOT NULL,
  `active` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `module`
--

LOCK TABLES `module` WRITE;
/*!40000 ALTER TABLE `module` DISABLE KEYS */;
INSERT INTO `module` VALUES (1,'System','Module/System','','',0,1),(2,'Banner','Module/Banner','','',4,1),(3,'Market','Module/Market','','',7,1),(4,'Catalog','Module/Catalog','','',6,1),(5,'Gallery','Module/Gallery','','',5,1),(6,'Guestbook','Module/Guestbook','','',3,1),(7,'Page','Module/Page','','',1,1),(8,'News','Module/News','','',2,1);
/*!40000 ALTER TABLE `module` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `news`
--

DROP TABLE IF EXISTS `news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cat_id` int(11) DEFAULT NULL,
  `author_id` int(11) DEFAULT NULL,
  `alias` varchar(250) NOT NULL DEFAULT '',
  `name` varchar(250) DEFAULT NULL,
  `image` varchar(250) DEFAULT '',
  `main` tinyint(1) DEFAULT '0',
  `priority` tinyint(1) DEFAULT '0',
  `notice` text,
  `text` text,
  `date` int(11) DEFAULT NULL,
  `title` varchar(250) DEFAULT NULL,
  `keywords` varchar(250) DEFAULT NULL,
  `description` varchar(250) DEFAULT NULL,
  `hidden` tinyint(4) DEFAULT NULL,
  `protected` tinyint(4) DEFAULT NULL,
  `deleted` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `news`
--

LOCK TABLES `news` WRITE;
/*!40000 ALTER TABLE `news` DISABLE KEYS */;
INSERT INTO `news` VALUES (1,1,1,'1-sluchainyi-argument-perigeliya-v-xxi-veke','Случайный аргумент перигелия в XXI веке','',0,0,'<p>Проверка работы</p>','<p>Проверка работы</p>\r\n<p>Полное описание</p>',1294174800,'Случайный аргумент перигелия в XXI веке','Случайный аргумент перигелия в XXI веке','Случайный аргумент перигелия в XXI веке',0,0,0),(2,1,1,'2-novostj-2','Новость 2','',0,0,'<p>\n	Нотис 2</p>\n','<p>\n	Инфа 2</p>\n',1295038800,'Новость 2','','',0,0,0),(3,1,1,'3-siteforevercms-poteryala-sovmestimostj-s-php-5-2','SiteForeverCMS потеряла совместимость с PHP 5.2','',0,0,'<p>\r\n	Все мы знаем, что уже давно вышла версия PHP 5.3, а еще в начале весны вышла версия PHP 5.4 с множеством доработок, использование которых делает приложения более мощными, и при этом упрощает работу программиста.</p>','<p>\r\n	Все мы знаем, что уже давно вышла версия PHP 5.3, а еще в начале весны вышла версия PHP 5.4 с множеством доработок, использование которых делает приложения более мощными, и при этом упрощает работу программиста.</p>\r\n<p>\r\n	Использование всех преимуществ новых возможнослей делает программный код более гибким, но устраняет совместимость приложений с ранними версиями PHP 5.2 и более ранних, которые, к тому же, уже не поддерживаются разработчиками.</p>\r\n<p>\r\n	Более того, все современные дистрибутивы Linux содержат в своих репозиториях свежие версии. Поэтому не должно возниктуть проблем с запуском SiteForever на Ваших серверах и VDS. В настоящее время не поддерживают PHP 5.3 только очень ленивые провайдеры, которые, в силу своей лени не заслуживают своих клиентов.</p>\r\n<p>\r\n	В связи со всем этим, мы считаем не целесообразным поддерживать совместимость с устаревшими версиями PHP и делаем шаг вперед. Теперь минимальная версия для работы SiteForeverCMS считается 5.3.10.</p>',1344369600,'SiteForeverCMS потеряла совместимость с PHP 5.2','','',0,0,0),(4,3,1,'4-moya-pervaya-statjya','Моя первая статья','',0,0,'<p>\r\n	Партисипативное планирование недостижимо. Ретроконверсия национального наследия категорически синхронизирует культурный инструмент маркетинга, используя опыт предыдущих кампаний. Интересно&nbsp;отметить,&nbsp;что социальная ответственность тормозит эмпирический системный анализ, не считаясь с затратами.</p>','<p>\r\n	Партисипативное планирование недостижимо. Ретроконверсия национального наследия категорически синхронизирует культурный инструмент маркетинга, используя опыт предыдущих кампаний. Интересно&nbsp;отметить,&nbsp;что социальная ответственность тормозит эмпирический системный анализ, не считаясь с затратами. Концепция развития стабилизирует повседневный стратегический рыночный план, полагаясь на инсайдерскую информацию. Отсюда&nbsp;естественно&nbsp;следует,&nbsp;что позиционирование на рынке оправдывает принцип&nbsp;восприятия, расширяя долю рынка. Презентационный материал,&nbsp;конечно, консолидирует межличностный клиентский спрос, оптимизируя бюджеты.</p>\r\n<p>\r\n	Российская специфика, согласно Ф.Котлеру, отражает product placement, отвоевывая свою долю рынка. Взаимодействие корпорации и клиента, безусловно, вполне выполнимо. Повышение жизненных стандартов,&nbsp;как&nbsp;следует&nbsp;из&nbsp;вышесказанного, вполне выполнимо. Потребление позиционирует из ряда вон выходящий мониторинг активности, не считаясь с затратами.</p>\r\n<p>\r\n	Селекция бренда настроено позитивно. Медийная связь требовальна к креативу. Тактика выстраивания отношений с коммерсчекими агентами искажает инвестиционный продукт, расширяя долю рынка. Партисипативное планирование амбивалентно. Можно&nbsp;предположить,&nbsp;что баланс спроса и предложения позиционирует нишевый проект, осознав маркетинг как часть производства. Пул лояльных изданий ригиден как никогда.</p>',1339617600,'Моя первая статья','','',0,0,0),(5,3,1,'5-pochemu-po-prezhnemu-vostrebovana-informacionnaya-svyazj-s-potrebitelem','Почему по-прежнему востребована информационная связь с потребителем?','',0,0,'<p>Партисипативное планирование недостижимо. Ретроконверсия национального наследия категорически синхронизирует культурный инструмент маркетинга, используя опыт предыдущих кампаний. Интересно&nbsp;отметить,&nbsp;что социальная ответственность тормозит эмпирический системный анализ, не считаясь с затратами. Концепция развития стабилизирует повседневный стратегический рыночный план, полагаясь на инсайдерскую информацию.</p>','<p>Партисипативное планирование недостижимо. Ретроконверсия национального наследия категорически синхронизирует культурный инструмент маркетинга, используя опыт предыдущих кампаний. Интересно&nbsp;отметить,&nbsp;что социальная ответственность тормозит эмпирический системный анализ, не считаясь с затратами. Концепция развития стабилизирует повседневный стратегический рыночный план, полагаясь на инсайдерскую информацию. Отсюда&nbsp;естественно&nbsp;следует,&nbsp;что позиционирование на рынке оправдывает принцип&nbsp;восприятия, расширяя долю рынка. Презентационный материал,&nbsp;конечно, консолидирует межличностный клиентский спрос, оптимизируя бюджеты.</p>\r\n<p>Российская специфика, согласно Ф.Котлеру, отражает product placement, отвоевывая свою долю рынка. Взаимодействие корпорации и клиента, безусловно, вполне выполнимо. Повышение жизненных стандартов,&nbsp;как&nbsp;следует&nbsp;из&nbsp;вышесказанного, вполне выполнимо. Потребление позиционирует из ряда вон выходящий мониторинг активности, не считаясь с затратами.</p>\r\n<p>Селекция бренда настроено позитивно. Медийная связь требовальна к креативу. Тактика выстраивания отношений с коммерсчекими агентами искажает инвестиционный продукт, расширяя долю рынка. Партисипативное планирование амбивалентно. Можно&nbsp;предположить,&nbsp;что баланс спроса и предложения позиционирует нишевый проект, осознав маркетинг как часть производства. Пул лояльных изданий ригиден как никогда.</p>',1339617600,'Почему по-прежнему востребована информационная связь с потребителем?','','',0,0,0),(6,1,1,'6-pochemu-nablyudaema-molekula','Почему наблюдаема молекула','',0,0,'<p>\r\n	При облучении инфракрасным лазером возмущение плотности ортогонально. Колебание ортогонально. Исследователями из разных лабораторий неоднократно наблюдалось, как расслоение пространственно неоднородно. В ряде недавних экспериментов кристалл инвариантен относительно сдвига.</p>','<p>\r\n	При облучении инфракрасным лазером возмущение плотности ортогонально. Колебание ортогонально. Исследователями из разных лабораторий неоднократно наблюдалось, как расслоение пространственно неоднородно. В ряде недавних экспериментов кристалл инвариантен относительно сдвига.</p>\r\n<p>\r\n	Колебание, при адиабатическом изменении параметров, недетерминировано возбуждает адронный электрон одинаково по всем направлениям. В условиях электромагнитных помех, неизбежных при полевых измерениях, не всегда можно опредлить, когда именно темная материя спонтанно переворачивает ультрафиолетовый осциллятор - все дальнейшее далеко выходит за рамки текущего исследования и не будет здесь рассматриваться. Эксимер, как можно показать с помощью не совсем тривиальных вычислений, масштабирует магнит, и этот процесс может повторяться многократно. Возмущение плотности нейтрализует спиральный экситон, хотя этот факт нуждается в дальнейшей тщательной экспериментальной проверке. Квантовое состояние, по данным астрономических наблюдений, когерентно представляет собой электрон, поскольку любое другое поведение нарушало бы изотропность пространства.</p>\r\n<p>\r\n	Луч, как неоднократно наблюдалось при постоянном воздействии ультрафиолетового облучения, квантово разрешен. Молекула, в отличие от классического случая, спонтанно выталкивает резонатор одинаково по всем направлениям. Многочисленные расчеты предсказывают, а эксперименты подтверждают, что возмущение плотности отрицательно заряжено. Кристаллическая решетка, несмотря на некоторую вероятность коллапса, эллиптично синхронизует сверхпроводник, генерируя периодические импульсы синхротронного излучения. Гетерогенная структура зеркально синхронизует электронный атом, и этот процесс может повторяться многократно. Жидкость индуцирует погранслой одинаково по всем направлениям.</p>',1344456000,'Почему наблюдаема молекула','','',0,0,0),(7,3,1,'7-siteforever-twitter-bootstrap','SiteForever && Twitter Bootstrap','',0,0,'<p>\r\n	Интереса ради решил разобраться, что такое <a href=\"http://twitter.github.com/bootstrap/\" target=\"_blank\">Twitter Bootstrap</a> и с чем его едят.</p>\r\n<p>\r\n	Сухое копание документации произвело не большое впечатление. Поэтому, чтобы лучше понять, что хорошего есть в этом, поднявшем шум фреймворке, я сделал отдельную ветку своей CMS и начал прикручивать к ней сабж.</p>','<p>\r\n	Интереса ради решил разобраться, что такое <a href=\"http://twitter.github.com/bootstrap/\" target=\"_blank\">Twitter Bootstrap</a> и с чем его едят.</p>\r\n<p>\r\n	Сухое копание документации произвело не большое впечатление. Поэтому, чтобы лучше понять, что хорошего есть в этом, поднявшем шум фреймворке, я сделал отдельную ветку своей CMS и начал прикручивать к ней сабж.</p>\r\n<p>\r\n	На мой вкус, компоненты, которые предоставляет Twitter, в чем-то оказались интереснее аналогов в jQuery UI. Многое работает уже из коробки, поэтому нужно меньше плясать с бубном, чтобы, например, подтянуть табы в загруженном через AJAX контенте.</p>\r\n<p>\r\n	А пока пара скринов того, что получилось после скрещивания SiteForeverCMS и TwitterBootwtrap:</p>\r\n<p>\r\n	Общий вид структуры разделов. <a href=\"http://ic.pics.livejournal.com/keltanas/18017332/4509/original.png\" target=\"_blank\"><img alt=\"cms_siteforever_page_admin_twitter_bootstrap\" height=\"551\" src=\"http://ic.pics.livejournal.com/keltanas/18017332/4509/original.png\" style=\"border-width: 0px; border-style: solid;\" title=\"cms_siteforever_page_admin_twitter_bootstrap\" width=\"600\" /></a></p>\r\n<p>\r\n	Форма редактирования раздела с \"блэкджеком\"... <a href=\"http://ic.pics.livejournal.com/keltanas/18017332/4746/original.png\" target=\"_blank\"><img alt=\"cms_siteforever_page_admin_twitter_bootstrap_edit\" height=\"410\" src=\"http://ic.pics.livejournal.com/keltanas/18017332/4746/original.png\" style=\"border-width: 0px; border-style: solid;\" title=\"cms_siteforever_page_admin_twitter_bootstrap_edit\" width=\"600\" /></a></p>',1345579200,'SiteForever && Twitter Bootstrap','SiteForever, Twitter Bootstrap','',0,0,0),(8,1,1,'8-horoshaya-novostj','Хорошая новость','',0,0,'','<p>\r\n	Космический мусор, а там действительно могли быть видны звезды, о чем свидетельствует Фукидид оценивает непреложный надир – у таких объектов рукава столь фрагментарны и обрывочны, что их уже нельзя назвать спиральными.</p>\r\n<p>\r\n	Перигелий решает эффективный диаметp – у таких объектов рукава столь фрагментарны и обрывочны, что их уже нельзя назвать спиральными. Каллисто неизменяем. Кульминация, и это следует подчеркнуть, пространственно оценивает Млечный Путь, это довольно часто наблюдается у сверхновых звезд второго типа.</p>\r\n<p>\r\n	Математический горизонт, в первом приближении, изменяем. Эклиптика традиционно перечеркивает маятник Фуко, учитывая, что в одном парсеке 3,26 световых года.</p>',1352318400,'Хорошая новость','','',0,0,0),(9,3,1,'9-privet-mir','Привет мир','',0,0,'<p>\r\n	Мир, я тебя обожаю!</p>','<p>\r\n	Мир, я тебя обожаю!</p>',1359835200,'','','',0,0,0);
/*!40000 ALTER TABLE `news` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `news_cats`
--

DROP TABLE IF EXISTS `news_cats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `news_cats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) DEFAULT NULL,
  `description` text,
  `show_content` tinyint(1) DEFAULT NULL,
  `show_list` tinyint(1) DEFAULT NULL,
  `type_list` tinyint(1) DEFAULT NULL,
  `per_page` tinyint(1) DEFAULT NULL,
  `hidden` tinyint(1) DEFAULT NULL,
  `protected` tinyint(1) DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `news_cats`
--

LOCK TABLES `news_cats` WRITE;
/*!40000 ALTER TABLE `news_cats` DISABLE KEYS */;
INSERT INTO `news_cats` VALUES (1,'Новости','',0,1,1,10,0,0,0),(2,'Статьи','Статьи и публикации',1,1,1,5,0,0,0),(3,'Блог','',0,1,1,10,0,0,0);
/*!40000 ALTER TABLE `news_cats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order`
--

DROP TABLE IF EXISTS `order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` tinyint(4) NOT NULL,
  `paid` int(11) NOT NULL,
  `delivery_id` int(11) NOT NULL DEFAULT '0',
  `payment_id` int(11) NOT NULL DEFAULT '0',
  `date` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `comment` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `user_id` (`date`,`user_id`),
  KEY `date` (`date`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order`
--

LOCK TABLES `order` WRITE;
/*!40000 ALTER TABLE `order` DISABLE KEYS */;
INSERT INTO `order` VALUES (1,1,0,0,0,1347401761,1,'Николай','Ермин','admin@ermin.ru','+7 904 511 11 38','выфвфыв',''),(2,1,0,0,0,1347454409,1,'Николай','Ермин','admin@ermin.ru','+7 904 511 11 38','ввв ленинград ру',''),(3,1,0,0,0,1347456137,1,'Николай','Ермин','admin@ermin.ru','+7 904 511 11 38','куецукецеуке',''),(4,1,0,0,0,1347456633,0,'asdsad','asdsad','asdsada@adas.ru','54646464498','dfasdfsdfsdfsdf',''),(5,1,1,0,0,1347571675,0,'Николай','Ермин','ermin84@yandex.ru','+7 904 511-11-38','Санкт-Петербург, Россия, пр. Кондратьевский, д. 15, к. 3, Станция метро пл. Ленина','БЦ Fernan Leger. Ориентир - Fitness Family'),(6,1,0,3,4,1347637497,0,'dy','','000@mail.ru','8126664581','123',''),(7,1,1,1,4,1347653992,1,'Николай','Ермин','ermin84@yandex.ru','+7 904 511-11-38','Санкт-Петербург, Россия, пр. Кондратьевский, д. 15, к. 3, Станция метро пл. Ленина','БЦ Fernan Leger. Ориентир - Fitness Family'),(8,1,1,1,4,1347654042,0,'Николай','Ермин','ermin84@yandex.ru','+7 904 511-11-38','Санкт-Петербург, Россия, пр. Кондратьевский, д. 15, к. 3, Станция метро пл. Ленина','БЦ Fernan Leger. Ориентир - Fitness Family'),(9,1,1,3,4,1347695177,0,'псчрпарпаврпа','рпаврпаврпав','jhgf@gmail.com','+ 7 812 1234567','рпаврпаврпав','рпаврпаврпав'),(10,1,1,3,4,1347695325,0,'21341234','12341234eqwerqwer','oritey@gmail.com','79629193394','qreqerqerqwerrqewqer',''),(11,1,0,3,4,1347700946,0,'вр','','12@mail.ru','8123516464','вр',''),(12,1,0,3,1,1347701036,0,'sdeg','','12@mail.ru','8126451531','sr',''),(13,10,1,1,4,1347786407,0,'Николай','Ермин','ermin84@yandex.ru','+7 904 511-11-38','Санкт-Петербург, Россия, пр. Кондратьевский, д. 15, к. 3, Станция метро пл. Ленина','БЦ Fernan Leger. Ориентир - Fitness Family');
/*!40000 ALTER TABLE `order` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_pos`
--

DROP TABLE IF EXISTS `order_pos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_pos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ord_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `articul` varchar(250) DEFAULT NULL,
  `details` text,
  `currency` varchar(10) DEFAULT NULL,
  `item` varchar(10) DEFAULT NULL,
  `cat_id` int(11) DEFAULT NULL,
  `price` decimal(13,2) DEFAULT NULL,
  `count` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ord_id` (`ord_id`),
  KEY `cat_id` (`cat_id`),
  KEY `articul` (`articul`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_pos`
--

LOCK TABLES `order_pos` WRITE;
/*!40000 ALTER TABLE `order_pos` DISABLE KEYS */;
INSERT INTO `order_pos` VALUES (1,1,NULL,'BUB, одна из звезд в MUNNYWORLD DIY','',NULL,NULL,18,800.00,1,1),(2,1,NULL,'MEGA MUNNY ГЛЯНЦЕВЫЙ ГОЛУБОЙ','',NULL,NULL,24,1300.00,2,1),(3,2,NULL,'BUB, одна из звезд в MUNNYWORLD DIY','',NULL,NULL,18,800.00,1,1),(4,2,NULL,'MEGA MUNNY ГЛЯНЦЕВЫЙ ГОЛУБОЙ','',NULL,NULL,24,1300.00,1,1),(5,3,NULL,'14','',NULL,NULL,14,900.00,1,1),(6,3,NULL,'24','',NULL,NULL,24,1300.00,1,1),(7,4,NULL,'BUB, одна из звезд в MUNNYWORLD DIY','',NULL,NULL,18,800.00,1,1),(8,4,NULL,'MEGA MUNNY ГЛЯНЦЕВЫЙ ГОЛУБОЙ','',NULL,NULL,24,1300.00,1,1),(9,5,NULL,'BUB, одна из звезд в MUNNYWORLD DIY','',NULL,NULL,18,800.00,1,1),(10,6,NULL,'KIDROBOT BOTS MINI SERIES','',NULL,NULL,6,0.00,5,1),(11,7,NULL,'BUB, одна из звезд в MUNNYWORLD DIY','',NULL,NULL,18,800.00,1,1),(12,8,NULL,'MUNNYWORLD MEGA MUNNY ЗЕЛЕНЫЙ','',NULL,NULL,22,1500.00,1,1),(13,9,NULL,'MUNNYWORLD MEGA MUNNY ЗЕЛЕНЫЙ','',NULL,NULL,22,1500.00,1,1),(14,10,NULL,'MUNNYWORLD MEGA MUNNY ЗЕЛЕНЫЙ','',NULL,NULL,22,1500.00,1,1),(15,11,NULL,'BUB, одна из звезд в MUNNYWORLD DIY','',NULL,NULL,18,800.00,7,1),(16,12,NULL,'BUB, одна из звезд в MUNNYWORLD DIY','',NULL,NULL,18,800.00,1,1),(17,13,NULL,'BUB, одна из звезд в MUNNYWORLD DIY','',NULL,NULL,18,800.00,2,1);
/*!40000 ALTER TABLE `order_pos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_status`
--

DROP TABLE IF EXISTS `order_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_status`
--

LOCK TABLES `order_status` WRITE;
/*!40000 ALTER TABLE `order_status` DISABLE KEYS */;
INSERT INTO `order_status` VALUES (-1,'Отменен'),(1,'Новый'),(10,'Отгружен');
/*!40000 ALTER TABLE `order_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment`
--

DROP TABLE IF EXISTS `payment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `desc` text NOT NULL,
  `module` varchar(255) NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment`
--

LOCK TABLES `payment` WRITE;
/*!40000 ALTER TABLE `payment` DISABLE KEYS */;
INSERT INTO `payment` VALUES (1,'Наличными курьеру','<p>\r\n	Нличными курьеру при получении</p>','basket',1),(2,'Банковской картой курьеру','<p>\r\n	Банковской картой курьеру при получении</p>','basket',1),(3,'На почте при получении','<p>\r\n	На почтовом отделении, наличными, при получении.</p>','basket',1),(4,'Через Робокассу','<p>\r\n	Оплатить банковской картой или эл. деньгами через интернет.</p>','robokassa',1);
/*!40000 ALTER TABLE `payment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_field`
--

DROP TABLE IF EXISTS `product_field`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_field` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_type_id` int(11) NOT NULL,
  `type` varchar(250) NOT NULL,
  `name` varchar(250) NOT NULL,
  `unit` varchar(250) NOT NULL,
  `pos` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_field`
--

LOCK TABLES `product_field` WRITE;
/*!40000 ALTER TABLE `product_field` DISABLE KEYS */;
INSERT INTO `product_field` VALUES (3,1,'string','WiFi','',3),(4,2,'datetime','Год издания','',0),(5,2,'string','Автор','',0),(6,2,'string','ISBN','',0),(7,3,'string','Исполнитель','',0),(8,3,'datetime','Дата выхода','',0),(9,3,'text','Список композиций','',0),(10,1,'string','BlueTooth','',2),(13,1,'string','Разрешение','px',0),(14,1,'string','Встроенная память','Gb',1),(15,5,'string','Материал рамы','',0),(16,5,'string','Задние тормоза','',0),(17,5,'string','Передние тормоза','',0);
/*!40000 ALTER TABLE `product_field` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_property`
--

DROP TABLE IF EXISTS `product_property`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_property` (
  `product_id` int(11) NOT NULL,
  `product_field_id` int(11) NOT NULL,
  `value_string` varchar(255) DEFAULT NULL,
  `value_text` blob,
  `value_int` int(11) DEFAULT NULL,
  `value_datetime` datetime DEFAULT NULL,
  `pos` int(11) DEFAULT '0',
  PRIMARY KEY (`product_id`,`product_field_id`),
  KEY `product_id` (`product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_property`
--

LOCK TABLES `product_property` WRITE;
/*!40000 ALTER TABLE `product_property` DISABLE KEYS */;
INSERT INTO `product_property` VALUES (12,13,'960x480',NULL,NULL,NULL,0),(12,14,'16',NULL,NULL,NULL,1),(12,3,'Да',NULL,NULL,NULL,3),(13,13,'960x480',NULL,NULL,NULL,0),(13,14,'32',NULL,NULL,NULL,1),(13,3,'Есть',NULL,NULL,NULL,3),(34,15,'Аллюминий',NULL,NULL,NULL,0),(34,16,'Крутые',NULL,NULL,NULL,0),(34,17,'Еще круче',NULL,NULL,NULL,0),(33,15,'Пластик',NULL,NULL,NULL,0),(33,16,'',NULL,NULL,NULL,0),(33,17,'',NULL,NULL,NULL,0),(15,3,'g,n',NULL,NULL,NULL,3),(15,10,'',NULL,NULL,NULL,2),(15,13,'940x560',NULL,NULL,NULL,0),(15,14,'32',NULL,NULL,NULL,1),(14,3,'Да',NULL,NULL,NULL,3),(14,10,'',NULL,NULL,NULL,2),(14,13,'800x600',NULL,NULL,NULL,0),(14,14,'16',NULL,NULL,NULL,1),(35,3,'Да',NULL,NULL,NULL,3),(35,10,'Есть',NULL,NULL,NULL,2),(35,13,NULL,NULL,NULL,NULL,0),(35,14,NULL,NULL,NULL,NULL,1),(7,3,'Да',NULL,NULL,NULL,3),(7,10,'Есть',NULL,NULL,NULL,2),(7,13,NULL,NULL,NULL,NULL,0),(7,14,NULL,NULL,NULL,NULL,1);
/*!40000 ALTER TABLE `product_property` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_type`
--

DROP TABLE IF EXISTS `product_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_type`
--

LOCK TABLES `product_type` WRITE;
/*!40000 ALTER TABLE `product_type` DISABLE KEYS */;
INSERT INTO `product_type` VALUES (1,'Телефон'),(2,'Книга'),(3,'Диск MP3'),(4,'Автомобиль'),(5,'Велосипед');
/*!40000 ALTER TABLE `product_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `routes`
--

DROP TABLE IF EXISTS `routes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `routes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pos` int(11) DEFAULT NULL,
  `alias` varchar(200) DEFAULT NULL,
  `controller` varchar(50) NOT NULL DEFAULT 'page',
  `action` varchar(50) NOT NULL DEFAULT 'index',
  `active` tinyint(4) DEFAULT NULL,
  `protected` tinyint(4) DEFAULT NULL,
  `system` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `routes`
--

LOCK TABLES `routes` WRITE;
/*!40000 ALTER TABLE `routes` DISABLE KEYS */;
INSERT INTO `routes` VALUES (1,0,'','test','test',1,0,0);
/*!40000 ALTER TABLE `routes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `search`
--

DROP TABLE IF EXISTS `search`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `search` (
  `alias` varchar(250) NOT NULL,
  `object` varchar(50) NOT NULL DEFAULT 'page',
  `module` varchar(50) NOT NULL DEFAULT 'default',
  `controller` varchar(50) NOT NULL DEFAULT 'page',
  `action` varchar(50) NOT NULL DEFAULT 'index',
  `title` varchar(250) NOT NULL,
  `keywords` varchar(250) NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY (`alias`),
  FULLTEXT KEY `ft` (`title`,`keywords`,`content`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `search`
--

LOCK TABLES `search` WRITE;
/*!40000 ALTER TABLE `search` DISABLE KEYS */;
INSERT INTO `search` VALUES ('news/sluchainyi-argument-perigeliya-v-XXI-veke','news','default','news','index','Случайный аргумент перигелия в XXI веке','Случайный аргумент перигелия в XXI веке','Проверка работы Проверка работы\r\nПолное описание'),('news/novostj-2','news','default','news','index','Новость 2','','\n	Нотис 2\n \n	Инфа 2\n'),('news/SiteForeverCMS-poteryala-sovmestimostj-s-PHP-5-2','news','default','news','index','SiteForeverCMS потеряла совместимость с PHP 5.2','','Все мы знаем, что уже давно вышла версия PHP 5.3, а еще в начале весны вышла версия PHP 5.4 с множеством доработок, использование которых делает приложения более мощными, и при этом упрощает работу программиста. Все мы знаем, что уже давно вышла версия PHP 5.3, а еще в начале весны вышла версия PHP 5.4 с множеством доработок, использование которых делает приложения более мощными, и при этом упрощает работу программиста.\r\nИспользование всех преимуществ новых возможнослей делает программный код более гибким, но устраняет совместимость приложений с ранними версиями PHP 5.2 и более ранних, которые, к тому же, уже не поддерживаются разработчиками.\r\nБолее того, все современные дистрибутивы Linux содержат в своих репозиториях свежие версии. Поэтому не должно возниктуть проблем с запуском SiteForever на Ваших серверах и VDS. В настоящее время не поддерживают PHP 5.3 только очень ленивые провайдеры, которые, в силу своей лени не заслуживают своих клиентов.\r\nВ связи со всем этим, мы считаем не целесообразным поддерживать совместимость с устаревшими версиями PHP и делаем шаг вперед. Теперь минимальная версия для работы SiteForeverCMS считается 5.3.10.'),('blog/moya-pervaya-statjya','news','default','news','index','Моя первая статья','','\r\n	Партисипативное планирование недостижимо. Ретроконверсия национального наследия категорически синхронизирует культурный инструмент маркетинга, используя опыт предыдущих кампаний. Интересно&nbsp;отметить,&nbsp;что социальная ответственность тормозит эмпирический системный анализ, не считаясь с затратами. \r\n	Партисипативное планирование недостижимо. Ретроконверсия национального наследия категорически синхронизирует культурный инструмент маркетинга, используя опыт предыдущих кампаний. Интересно&nbsp;отметить,&nbsp;что социальная ответственность тормозит эмпирический системный анализ, не считаясь с затратами. Концепция развития стабилизирует повседневный стратегический рыночный план, полагаясь на инсайдерскую информацию. Отсюда&nbsp;естественно&nbsp;следует,&nbsp;что позиционирование на рынке оправдывает принцип&nbsp;восприятия, расширяя долю рынка. Презентационный материал,&nbsp;конечно, консолидирует межличностный клиентский спрос, оптимизируя бюджеты.\r\n\r\n	Российская специфика, согласно Ф.Котлеру, отражает product placement, отвоевывая свою долю рынка. Взаимодействие корпорации и клиента, безусловно, вполне выполнимо. Повышение жизненных стандартов,&nbsp;как&nbsp;следует&nbsp;из&nbsp;вышесказанного, вполне выполнимо. Потребление позиционирует из ряда вон выходящий мониторинг активности, не считаясь с затратами.\r\n\r\n	Селекция бренда настроено позитивно. Медийная связь требовальна к креативу. Тактика выстраивания отношений с коммерсчекими агентами искажает инвестиционный продукт, расширяя долю рынка. Партисипативное планирование амбивалентно. Можно&nbsp;предположить,&nbsp;что баланс спроса и предложения позиционирует нишевый проект, осознав маркетинг как часть производства. Пул лояльных изданий ригиден как никогда.'),('blog/pochemu-po-prezhnemu-vostrebovana-informacionnaya-svyazj-s-potrebitelem','news','default','news','index','Почему по-прежнему востребована информационная связь с потребителем?','','Партисипативное планирование недостижимо. Ретроконверсия национального наследия категорически синхронизирует культурный инструмент маркетинга, используя опыт предыдущих кампаний. Интересно&nbsp;отметить,&nbsp;что социальная ответственность тормозит эмпирический системный анализ, не считаясь с затратами. Концепция развития стабилизирует повседневный стратегический рыночный план, полагаясь на инсайдерскую информацию. Партисипативное планирование недостижимо. Ретроконверсия национального наследия категорически синхронизирует культурный инструмент маркетинга, используя опыт предыдущих кампаний. Интересно&nbsp;отметить,&nbsp;что социальная ответственность тормозит эмпирический системный анализ, не считаясь с затратами. Концепция развития стабилизирует повседневный стратегический рыночный план, полагаясь на инсайдерскую информацию. Отсюда&nbsp;естественно&nbsp;следует,&nbsp;что позиционирование на рынке оправдывает принцип&nbsp;восприятия, расширяя долю рынка. Презентационный материал,&nbsp;конечно, консолидирует межличностный клиентский спрос, оптимизируя бюджеты.\r\nРоссийская специфика, согласно Ф.Котлеру, отражает product placement, отвоевывая свою долю рынка. Взаимодействие корпорации и клиента, безусловно, вполне выполнимо. Повышение жизненных стандартов,&nbsp;как&nbsp;следует&nbsp;из&nbsp;вышесказанного, вполне выполнимо. Потребление позиционирует из ряда вон выходящий мониторинг активности, не считаясь с затратами.\r\nСелекция бренда настроено позитивно. Медийная связь требовальна к креативу. Тактика выстраивания отношений с коммерсчекими агентами искажает инвестиционный продукт, расширяя долю рынка. Партисипативное планирование амбивалентно. Можно&nbsp;предположить,&nbsp;что баланс спроса и предложения позиционирует нишевый проект, осознав маркетинг как часть производства. Пул лояльных изданий ригиден как никогда.'),('news/pochemu-nablyudaema-molekula','news','default','news','index','Почему наблюдаема молекула','','При облучении инфракрасным лазером возмущение плотности ортогонально. Колебание ортогонально. Исследователями из разных лабораторий неоднократно наблюдалось, как расслоение пространственно неоднородно. В ряде недавних экспериментов кристалл инвариантен относительно сдвига. При облучении инфракрасным лазером возмущение плотности ортогонально. Колебание ортогонально. Исследователями из разных лабораторий неоднократно наблюдалось, как расслоение пространственно неоднородно. В ряде недавних экспериментов кристалл инвариантен относительно сдвига.\r\nКолебание, при адиабатическом изменении параметров, недетерминировано возбуждает адронный электрон одинаково по всем направлениям. В условиях электромагнитных помех, неизбежных при полевых измерениях, не всегда можно опредлить, когда именно темная материя спонтанно переворачивает ультрафиолетовый осциллятор - все дальнейшее далеко выходит за рамки текущего исследования и не будет здесь рассматриваться. Эксимер, как можно показать с помощью не совсем тривиальных вычислений, масштабирует магнит, и этот процесс может повторяться многократно. Возмущение плотности нейтрализует спиральный экситон, хотя этот факт нуждается в дальнейшей тщательной экспериментальной проверке. Квантовое состояние, по данным астрономических наблюдений, когерентно представляет собой электрон, поскольку любое другое поведение нарушало бы изотропность пространства.\r\nЛуч, как неоднократно наблюдалось при постоянном воздействии ультрафиолетового облучения, квантово разрешен. Молекула, в отличие от классического случая, спонтанно выталкивает резонатор одинаково по всем направлениям. Многочисленные расчеты предсказывают, а эксперименты подтверждают, что возмущение плотности отрицательно заряжено. Кристаллическая решетка, несмотря на некоторую вероятность коллапса, эллиптично синхронизует сверхпроводник, генерируя периодические импульсы синхротронного излучения. Гетерогенная структура зеркально синхронизует электронный атом, и этот процесс может повторяться многократно. Жидкость индуцирует погранслой одинаково по всем направлениям.'),('blog/SiteForever-Twitter-Bootstrap','news','default','news','index','SiteForever && Twitter Bootstrap','SiteForever, Twitter Bootstrap','\r\n	Интереса ради решил разобраться, что такое Twitter Bootstrap и с чем его едят.\r\n\r\n	Сухое копание документации произвело не большое впечатление. Поэтому, чтобы лучше понять, что хорошего есть в этом, поднявшем шум фреймворке, я сделал отдельную ветку своей CMS и начал прикручивать к ней сабж. \r\n	Интереса ради решил разобраться, что такое Twitter Bootstrap и с чем его едят.\r\n\r\n	Сухое копание документации произвело не большое впечатление. Поэтому, чтобы лучше понять, что хорошего есть в этом, поднявшем шум фреймворке, я сделал отдельную ветку своей CMS и начал прикручивать к ней сабж.\r\n\r\n	На мой вкус, компоненты, которые предоставляет Twitter, в чем-то оказались интереснее аналогов в jQuery UI. Многое работает уже из коробки, поэтому нужно меньше плясать с бубном, чтобы, например, подтянуть табы в загруженном через AJAX контенте.\r\n\r\n	А пока пара скринов того, что получилось после скрещивания SiteForeverCMS и TwitterBootwtrap:\r\n\r\n	Общий вид структуры разделов. \r\n\r\n	Форма редактирования раздела с \"блэкджеком\"... '),('news','page','default','page','index','Новости','',' '),('about','page','default','page','index','О компании','Создание сайтов, Команда специалистов, Комплексный подход, Низкие цены',' '),('contacts','page','default','page','index','Контакты','',' '),('portfolio','page','default','page','index','Портфолио','',' '),('catalog','page','default','page','index','Каталог','',' '),('pictures','page','default','page','index','Картинки','',' '),('guest','page','default','page','index','Гостевая','',' '),('about/nasha-missiya','page','default','page','index','Наша миссия','',' '),('about/rekvizity','page','default','page','index','Реквизиты','',' '),('about/sotrudniki','page','default','page','index','Сотрудники','',' '),('blog','page','default','page','index','Блог','',' '),('catalog/telefony','page','default','page','index','Телефоны','',' '),('catalog/avtomobili','page','default','page','index','Автомобили','',' '),('catalog/velosipedy','page','default','page','index','Велосипеды','',' '),('catalog/telefony/telefony-nokia','page','default','page','index','Телефоны Nokia','',' '),('catalog/telefony/telefony-htc','page','default','page','index','Телефоны HTC','',' '),('catalog/telefony/telefony-apple','page','default','page','index','Телефоны Apple','',' '),('catalog/avtomobili/vnedorozhniki','page','default','page','index','Внедорожники','',' '),('catalog/velosipedy/gornye','page','default','page','index','Горные','',' '),('catalog/velosipedy/gorodskie','page','default','page','index','Городские','',' '),('catalog/velosipedy/dorozhnye','page','default','page','index','Дорожные','',' '),('catalog/velosipedy/gornye/hardteil','page','default','page','index','Хардтейл','',' '),('catalog/velosipedy/gornye/dvuhpodves','page','default','page','index','Двухподвес','',' '),('contacts/slogan','page','default','page','index','Слоган','',' '),('brends','page','default','page','index','Бренды','',' '),('catalog/telefony/telefony-htc/htc-evo-3d','catalog','default','catalog','index','HTC Evo 3D','','А вот по мнению аналитиков фокус-группа интегрирована. Отсюда естественно следует, что торговая марка специфицирует продуктовый ассортимент, не считаясь с затратами. Взаимодействие корпорации и клиента последовательно порождает популярный нестандартный подход, отвоевывая рыночный сегмент. Стимулирование сбыта, на первый взгляд, категорически специфицирует культурный медиаплан, осознав маркетинг как часть производства.'),('catalog/avtomobili/vnedorozhniki/jeep-cheerokee','catalog','default','catalog','index','Jeep Cheerokee','','Product placement упорядочивает нишевый проект, не считаясь с затратами. CTR восстанавливает медиабизнес, работая над проектом. Соц-дем характеристика аудитории требовальна к креативу. Медиаплан ригиден как никогда. Еще Траут показал, что ассортиментная политика предприятия масштабирует конструктивный мониторинг активности, учитывая современные тенденции. Эволюция мерчандайзинга все еще интересна для многих.'),('catalog/telefony/telefony-htc/htc-one-x','catalog','default','catalog','index','HTC One X','','Рекламное сообщество определяет ребрендинг, расширяя долю рынка. Каждая сфера рынка, как следует из вышесказанного, многопланово специфицирует ролевой целевой трафик, осознавая социальную ответственность бизнеса. Ассортиментная политика предприятия стремительно искажает презентационный материал, учитывая современные тенденции. Традиционный канал, отбрасывая подробности, непосредственно ускоряет имидж, используя опыт предыдущих кампаний.'),('catalog/telefony/telefony-htc/htc-sensation','catalog','default','catalog','index','HTC Sensation','',''),('catalog/telefony/telefony-apple/iphone-4s','catalog','default','catalog','index','iPhone 4S','',''),('catalog/telefony/telefony-apple/iphone-3gs','catalog','default','catalog','index','iPhone 3GS','',''),('catalog/telefony/telefony-apple/iphone-4','catalog','default','catalog','index','iPhone 4','',''),('catalog/telefony/telefony-nokia/nokia-500','catalog','default','catalog','index','Nokia 500','',''),('catalog/telefony/telefony-nokia/nokia-n9','catalog','default','catalog','index','Nokia N9','',''),('catalog/velosipedy/gornye/dvuhpodves/terra-918-disk','catalog','default','catalog','index','TERRA 918 disk','',''),('catalog/velosipedy/gornye/dvuhpodves/4212','catalog','default','catalog','index','4212','',''),('news/1-sluchainyi-argument-perigeliya-v-xxi-veke','news','default','news','index','Случайный аргумент перигелия в XXI веке','Случайный аргумент перигелия в XXI веке','Проверка работы Проверка работы\r\nПолное описание'),('news/2-novostj-2','news','default','news','index','Новость 2','','\n	Нотис 2\n \n	Инфа 2\n'),('news/3-siteforevercms-poteryala-sovmestimostj-s-php-5-2','news','default','news','index','SiteForeverCMS потеряла совместимость с PHP 5.2','','\r\n	Все мы знаем, что уже давно вышла версия PHP 5.3, а еще в начале весны вышла версия PHP 5.4 с множеством доработок, использование которых делает приложения более мощными, и при этом упрощает работу программиста. \r\n	Все мы знаем, что уже давно вышла версия PHP 5.3, а еще в начале весны вышла версия PHP 5.4 с множеством доработок, использование которых делает приложения более мощными, и при этом упрощает работу программиста.\r\n\r\n	Использование всех преимуществ новых возможнослей делает программный код более гибким, но устраняет совместимость приложений с ранними версиями PHP 5.2 и более ранних, которые, к тому же, уже не поддерживаются разработчиками.\r\n\r\n	Более того, все современные дистрибутивы Linux содержат в своих репозиториях свежие версии. Поэтому не должно возниктуть проблем с запуском SiteForever на Ваших серверах и VDS. В настоящее время не поддерживают PHP 5.3 только очень ленивые провайдеры, которые, в силу своей лени не заслуживают своих клиентов.\r\n\r\n	В связи со всем этим, мы считаем не целесообразным поддерживать совместимость с устаревшими версиями PHP и делаем шаг вперед. Теперь минимальная версия для работы SiteForeverCMS считается 5.3.10.'),('blog/4-moya-pervaya-statjya','news','default','news','index','Моя первая статья','','\r\n	Партисипативное планирование недостижимо. Ретроконверсия национального наследия категорически синхронизирует культурный инструмент маркетинга, используя опыт предыдущих кампаний. Интересно&nbsp;отметить,&nbsp;что социальная ответственность тормозит эмпирический системный анализ, не считаясь с затратами. \r\n	Партисипативное планирование недостижимо. Ретроконверсия национального наследия категорически синхронизирует культурный инструмент маркетинга, используя опыт предыдущих кампаний. Интересно&nbsp;отметить,&nbsp;что социальная ответственность тормозит эмпирический системный анализ, не считаясь с затратами. Концепция развития стабилизирует повседневный стратегический рыночный план, полагаясь на инсайдерскую информацию. Отсюда&nbsp;естественно&nbsp;следует,&nbsp;что позиционирование на рынке оправдывает принцип&nbsp;восприятия, расширяя долю рынка. Презентационный материал,&nbsp;конечно, консолидирует межличностный клиентский спрос, оптимизируя бюджеты.\r\n\r\n	Российская специфика, согласно Ф.Котлеру, отражает product placement, отвоевывая свою долю рынка. Взаимодействие корпорации и клиента, безусловно, вполне выполнимо. Повышение жизненных стандартов,&nbsp;как&nbsp;следует&nbsp;из&nbsp;вышесказанного, вполне выполнимо. Потребление позиционирует из ряда вон выходящий мониторинг активности, не считаясь с затратами.\r\n\r\n	Селекция бренда настроено позитивно. Медийная связь требовальна к креативу. Тактика выстраивания отношений с коммерсчекими агентами искажает инвестиционный продукт, расширяя долю рынка. Партисипативное планирование амбивалентно. Можно&nbsp;предположить,&nbsp;что баланс спроса и предложения позиционирует нишевый проект, осознав маркетинг как часть производства. Пул лояльных изданий ригиден как никогда.'),('blog/5-pochemu-po-prezhnemu-vostrebovana-informacionnaya-svyazj-s-potrebitelem','news','default','news','index','Почему по-прежнему востребована информационная связь с потребителем?','','Партисипативное планирование недостижимо. Ретроконверсия национального наследия категорически синхронизирует культурный инструмент маркетинга, используя опыт предыдущих кампаний. Интересно&nbsp;отметить,&nbsp;что социальная ответственность тормозит эмпирический системный анализ, не считаясь с затратами. Концепция развития стабилизирует повседневный стратегический рыночный план, полагаясь на инсайдерскую информацию. Партисипативное планирование недостижимо. Ретроконверсия национального наследия категорически синхронизирует культурный инструмент маркетинга, используя опыт предыдущих кампаний. Интересно&nbsp;отметить,&nbsp;что социальная ответственность тормозит эмпирический системный анализ, не считаясь с затратами. Концепция развития стабилизирует повседневный стратегический рыночный план, полагаясь на инсайдерскую информацию. Отсюда&nbsp;естественно&nbsp;следует,&nbsp;что позиционирование на рынке оправдывает принцип&nbsp;восприятия, расширяя долю рынка. Презентационный материал,&nbsp;конечно, консолидирует межличностный клиентский спрос, оптимизируя бюджеты.\r\nРоссийская специфика, согласно Ф.Котлеру, отражает product placement, отвоевывая свою долю рынка. Взаимодействие корпорации и клиента, безусловно, вполне выполнимо. Повышение жизненных стандартов,&nbsp;как&nbsp;следует&nbsp;из&nbsp;вышесказанного, вполне выполнимо. Потребление позиционирует из ряда вон выходящий мониторинг активности, не считаясь с затратами.\r\nСелекция бренда настроено позитивно. Медийная связь требовальна к креативу. Тактика выстраивания отношений с коммерсчекими агентами искажает инвестиционный продукт, расширяя долю рынка. Партисипативное планирование амбивалентно. Можно&nbsp;предположить,&nbsp;что баланс спроса и предложения позиционирует нишевый проект, осознав маркетинг как часть производства. Пул лояльных изданий ригиден как никогда.'),('news/6-pochemu-nablyudaema-molekula','news','default','news','index','Почему наблюдаема молекула','','\r\n	При облучении инфракрасным лазером возмущение плотности ортогонально. Колебание ортогонально. Исследователями из разных лабораторий неоднократно наблюдалось, как расслоение пространственно неоднородно. В ряде недавних экспериментов кристалл инвариантен относительно сдвига. \r\n	При облучении инфракрасным лазером возмущение плотности ортогонально. Колебание ортогонально. Исследователями из разных лабораторий неоднократно наблюдалось, как расслоение пространственно неоднородно. В ряде недавних экспериментов кристалл инвариантен относительно сдвига.\r\n\r\n	Колебание, при адиабатическом изменении параметров, недетерминировано возбуждает адронный электрон одинаково по всем направлениям. В условиях электромагнитных помех, неизбежных при полевых измерениях, не всегда можно опредлить, когда именно темная материя спонтанно переворачивает ультрафиолетовый осциллятор - все дальнейшее далеко выходит за рамки текущего исследования и не будет здесь рассматриваться. Эксимер, как можно показать с помощью не совсем тривиальных вычислений, масштабирует магнит, и этот процесс может повторяться многократно. Возмущение плотности нейтрализует спиральный экситон, хотя этот факт нуждается в дальнейшей тщательной экспериментальной проверке. Квантовое состояние, по данным астрономических наблюдений, когерентно представляет собой электрон, поскольку любое другое поведение нарушало бы изотропность пространства.\r\n\r\n	Луч, как неоднократно наблюдалось при постоянном воздействии ультрафиолетового облучения, квантово разрешен. Молекула, в отличие от классического случая, спонтанно выталкивает резонатор одинаково по всем направлениям. Многочисленные расчеты предсказывают, а эксперименты подтверждают, что возмущение плотности отрицательно заряжено. Кристаллическая решетка, несмотря на некоторую вероятность коллапса, эллиптично синхронизует сверхпроводник, генерируя периодические импульсы синхротронного излучения. Гетерогенная структура зеркально синхронизует электронный атом, и этот процесс может повторяться многократно. Жидкость индуцирует погранслой одинаково по всем направлениям.'),('blog/7-siteforever-twitter-bootstrap','news','default','news','index','SiteForever && Twitter Bootstrap','SiteForever, Twitter Bootstrap','\r\n	Интереса ради решил разобраться, что такое Twitter Bootstrap и с чем его едят.\r\n\r\n	Сухое копание документации произвело не большое впечатление. Поэтому, чтобы лучше понять, что хорошего есть в этом, поднявшем шум фреймворке, я сделал отдельную ветку своей CMS и начал прикручивать к ней сабж. \r\n	Интереса ради решил разобраться, что такое Twitter Bootstrap и с чем его едят.\r\n\r\n	Сухое копание документации произвело не большое впечатление. Поэтому, чтобы лучше понять, что хорошего есть в этом, поднявшем шум фреймворке, я сделал отдельную ветку своей CMS и начал прикручивать к ней сабж.\r\n\r\n	На мой вкус, компоненты, которые предоставляет Twitter, в чем-то оказались интереснее аналогов в jQuery UI. Многое работает уже из коробки, поэтому нужно меньше плясать с бубном, чтобы, например, подтянуть табы в загруженном через AJAX контенте.\r\n\r\n	А пока пара скринов того, что получилось после скрещивания SiteForeverCMS и TwitterBootwtrap:\r\n\r\n	Общий вид структуры разделов. \r\n\r\n	Форма редактирования раздела с \"блэкджеком\"... '),('news/8-horoshaya-novostj','news','default','news','index','Хорошая новость','',' \r\n	Космический мусор, а там действительно могли быть видны звезды, о чем свидетельствует Фукидид оценивает непреложный надир – у таких объектов рукава столь фрагментарны и обрывочны, что их уже нельзя назвать спиральными.\r\n\r\n	Перигелий решает эффективный диаметp – у таких объектов рукава столь фрагментарны и обрывочны, что их уже нельзя назвать спиральными. Каллисто неизменяем. Кульминация, и это следует подчеркнуть, пространственно оценивает Млечный Путь, это довольно часто наблюдается у сверхновых звезд второго типа.\r\n\r\n	Математический горизонт, в первом приближении, изменяем. Эклиптика традиционно перечеркивает маятник Фуко, учитывая, что в одном парсеке 3,26 световых года.'),('blog/9-privet-mir','news','default','news','index','','','\r\n	Мир, я тебя обожаю! \r\n	Мир, я тебя обожаю!'),('catalog/telefony/telefony-htc/7-htc-evo-3d','catalog','default','catalog','index','HTC Evo 3D','','\r\n	А вот по мнению аналитиков фокус-группа интегрирована. Отсюда естественно следует, что торговая марка специфицирует продуктовый ассортимент, не считаясь с затратами. Взаимодействие корпорации и клиента последовательно порождает популярный нестандартный подход, отвоевывая рыночный сегмент. Стимулирование сбыта, на первый взгляд, категорически специфицирует культурный медиаплан, осознав маркетинг как часть производства.'),('catalog/avtomobili/vnedorozhniki/8-jeep-cheerokee','catalog','default','catalog','index','Jeep Cheerokee','','Product placement упорядочивает нишевый проект, не считаясь с затратами. CTR восстанавливает медиабизнес, работая над проектом. Соц-дем характеристика аудитории требовальна к креативу. Медиаплан ригиден как никогда. Еще Траут показал, что ассортиментная политика предприятия масштабирует конструктивный мониторинг активности, учитывая современные тенденции. Эволюция мерчандайзинга все еще интересна для многих.'),('catalog/telefony/telefony-htc/9-htc-one-x','catalog','default','catalog','index','HTC One X','','\r\n	Рекламное сообщество определяет ребрендинг, расширяя долю рынка. Каждая сфера рынка, как следует из вышесказанного, многопланово специфицирует ролевой целевой трафик, осознавая социальную ответственность бизнеса. Ассортиментная политика предприятия стремительно искажает презентационный материал, учитывая современные тенденции. Традиционный канал, отбрасывая подробности, непосредственно ускоряет имидж, используя опыт предыдущих кампаний.'),('catalog/telefony/telefony-htc/10-htc-sensation','catalog','default','catalog','index','HTC Sensation','',''),('catalog/telefony/telefony-apple/11-iphone-4s','catalog','default','catalog','index','iPhone 4S','',''),('catalog/telefony/telefony-apple/12-iphone-3gs','catalog','default','catalog','index','iPhone 3GS','',''),('catalog/telefony/telefony-apple/13-iphone-4','catalog','default','catalog','index','iPhone 4','',''),('catalog/telefony/telefony-nokia/14-nokia-500','catalog','default','catalog','index','Nokia 500','',''),('catalog/telefony/telefony-nokia/15-nokia-n9','catalog','default','catalog','index','Nokia N9','',''),('catalog/velosipedy/gornye/dvuhpodves/33-terra-918-disk','catalog','default','catalog','index','TERRA 918 disk','','\r\nПривет!'),('catalog/telefony/telefony-htc/35-htc-sensation-xe','catalog','default','catalog','index','HTC Sensation XE','','');
/*!40000 ALTER TABLE `search` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `session`
--

DROP TABLE IF EXISTS `session`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `session` (
  `sess_id` varchar(255) NOT NULL,
  `sess_data` text NOT NULL,
  `sess_time` int(11) NOT NULL,
  PRIMARY KEY (`sess_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `session`
--

LOCK TABLES `session` WRITE;
/*!40000 ALTER TABLE `session` DISABLE KEYS */;
INSERT INTO `session` VALUES ('0bg5re164o6o24tmqaiqeuehb5','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgxMzU1NDE7czoxOiJjIjtpOjEzNjgxMzU1Mzk7czoxOiJsIjtzOjE6IjAiO30=',1368135542),('0ggr85g7970ai3eptaosalg9r4','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgxMzUxODg7czoxOiJjIjtpOjEzNjgxMzUxODQ7czoxOiJsIjtzOjE6IjAiO30=',1368135188),('0h01dhs9o9acu0119v4otstas1','X3NmMl9hdHRyaWJ1dGVzfGE6Mjp7czo2OiJiYXNrZXQiO2E6MDp7fXM6NzoidXNlcl9pZCI7aTowO31fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjg1NzIzMDg7czoxOiJjIjtpOjEzNjg1NzIyNTM7czoxOiJsIjtzOjE6IjAiO30=',1368572311),('191es2paffllf54rs9q3q7unj6','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgxMzIwMzQ7czoxOiJjIjtpOjEzNjgxMzIwMzI7czoxOiJsIjtzOjE6IjAiO30=',1368132035),('19fiq19agle6g81rrnikpld967','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjg1NzgxNTU7czoxOiJjIjtpOjEzNjg1NzgxNTA7czoxOiJsIjtzOjE6IjAiO30=',1368578157),('1vhal8ehomblg0e71iqs3v9i81','X3NmMl9hdHRyaWJ1dGVzfGE6Mjp7czo2OiJiYXNrZXQiO2E6MDp7fXM6NzoidXNlcl9pZCI7aTowO31fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjg2Mzk3NTY7czoxOiJjIjtpOjEzNjg2Mzk3MzQ7czoxOiJsIjtzOjE6IjAiO30=',1368639757),('3avd72p35ioc04agn85r5ea2m5','X3NmMl9hdHRyaWJ1dGVzfGE6Mzp7czo2OiJiYXNrZXQiO2E6MDp7fXM6NzoidXNlcl9pZCI7czoxOiIxIjtzOjEyOiJjYXB0Y2hhX2NvZGUiO3M6NjoiRUZDRUdOIjt9X3NmMl9mbGFzaGVzfGE6MDp7fV9zZjJfbWV0YXxhOjM6e3M6MToidSI7aToxMzcwODU1NTI1O3M6MToiYyI7aToxMzcwNjUwOTkxO3M6MToibCI7czoxOiIwIjt9',1370855525),('3ffomnfirth6pbi0em584qmhc5','X3NmMl9hdHRyaWJ1dGVzfGE6Mjp7czo2OiJiYXNrZXQiO2E6MDp7fXM6MTI6ImNhcHRjaGFfY29kZSI7czo2OiJQRkFHRkMiO31fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjc0MzUxMTY7czoxOiJjIjtpOjEzNjc0MTk5NDM7czoxOiJsIjtzOjE6IjAiO30=',1367435117),('3gk444v6i5v545ft9jjgb43275','X3NmMl9hdHRyaWJ1dGVzfGE6Mjp7czo2OiJiYXNrZXQiO2E6MDp7fXM6NzoidXNlcl9pZCI7aTowO31fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgxNDA4NzI7czoxOiJjIjtpOjEzNjgxNDA4NjE7czoxOiJsIjtzOjE6IjAiO30=',1368140872),('3lf9834311qorkg0ink8jv3op4','X3NmMl9hdHRyaWJ1dGVzfGE6Mjp7czo2OiJiYXNrZXQiO2E6MDp7fXM6NzoidXNlcl9pZCI7aTowO31fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgyMDgxMDA7czoxOiJjIjtpOjEzNjgyMDgwNzc7czoxOiJsIjtzOjE6IjAiO30=',1368208100),('3tq0msfljrp293bhq2tbtj0hf5','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgxMjg4NzY7czoxOiJjIjtpOjEzNjgxMjg4NzQ7czoxOiJsIjtzOjE6IjAiO30=',1368128876),('40ta09t3ll1v1eqeqqj8i2qgb5','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgxMzUyMDE7czoxOiJjIjtpOjEzNjgxMzUxOTk7czoxOiJsIjtzOjE6IjAiO30=',1368135202),('4he5qqeogmd9drv125h738r890','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjg1NzgwMjA7czoxOiJjIjtpOjEzNjg1Nzc2MTE7czoxOiJsIjtzOjE6IjAiO30=',1368578022),('4hf0h0078p9h253fkn7oq0u0g5','X3NmMl9hdHRyaWJ1dGVzfGE6Mzp7czo2OiJiYXNrZXQiO2E6MDp7fXM6NzoidXNlcl9pZCI7czoxOiIxIjtzOjEyOiJjYXB0Y2hhX2NvZGUiO3M6NjoiQUJHRU5QIjt9X3NmMl9mbGFzaGVzfGE6MDp7fV9zZjJfbWV0YXxhOjM6e3M6MToidSI7aToxMzY3NTIyMzc1O3M6MToiYyI7aToxMzY2ODU3ODY2O3M6MToibCI7czoxOiIwIjt9',1367522375),('4j9cqfnm4qlmdbf3b3oust2en1','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgxMzYwNDk7czoxOiJjIjtpOjEzNjgxMzYwNDc7czoxOiJsIjtzOjE6IjAiO30=',1368136050),('4oqm1h8bt2mer6ndg9khc6q6d1','X3NmMl9hdHRyaWJ1dGVzfGE6Mjp7czo2OiJiYXNrZXQiO2E6MDp7fXM6NzoidXNlcl9pZCI7aTowO31fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgyMDA4NzI7czoxOiJjIjtpOjEzNjgyMDA4NDk7czoxOiJsIjtzOjE6IjAiO30=',1368200873),('4sdepd8l0e93mt3epndlvqk245','X3NmMl9hdHRyaWJ1dGVzfGE6Mjp7czo2OiJiYXNrZXQiO2E6MDp7fXM6NzoidXNlcl9pZCI7aTowO31fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjg0NzIzMjE7czoxOiJjIjtpOjEzNjg0NzIyOTc7czoxOiJsIjtzOjE6IjAiO30=',1368472321),('5c86tjgkkepl8ckbihp2vk39s3','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgxMzU2Mzg7czoxOiJjIjtpOjEzNjgxMzU2Mzc7czoxOiJsIjtzOjE6IjAiO30=',1368135639),('5e92q41750h98pedjsn0svae56','X3NmMl9hdHRyaWJ1dGVzfGE6Mjp7czo2OiJiYXNrZXQiO2E6MDp7fXM6NzoidXNlcl9pZCI7aTowO31fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgyMDE3NTU7czoxOiJjIjtpOjEzNjgyMDE3MzQ7czoxOiJsIjtzOjE6IjAiO30=',1368201756),('5lomquf2ul8p1ien0lnfg2p4n3','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgxMzI4MzQ7czoxOiJjIjtpOjEzNjgxMzI4MzE7czoxOiJsIjtzOjE6IjAiO30=',1368132834),('5pu7abi875figlfvasjfmrsd31','X3NmMl9hdHRyaWJ1dGVzfGE6Mzp7czo2OiJiYXNrZXQiO2E6MDp7fXM6MTI6ImNhcHRjaGFfY29kZSI7czo2OiJHT1BQSEIiO3M6NzoidXNlcl9pZCI7czoxOiIxIjt9X3NmMl9mbGFzaGVzfGE6MDp7fV9zZjJfbWV0YXxhOjM6e3M6MToidSI7aToxMzcwMjE2MjI0O3M6MToiYyI7aToxMzY3NjYxNzI3O3M6MToibCI7czoxOiIwIjt9',1370216225),('66po8mkoa5qq36o5ukbusednk7','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjc0MjM5NjA7czoxOiJjIjtpOjEzNjc0MjM5NDU7czoxOiJsIjtzOjE6IjAiO30=',1367423960),('69l9q290lsjds0r730c1cnlsp0','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgxMjkwNDc7czoxOiJjIjtpOjEzNjgxMjkwNDU7czoxOiJsIjtzOjE6IjAiO30=',1368129048),('6r85lrtr5nq6apdpchqtfp7oe4','X3NmMl9hdHRyaWJ1dGVzfGE6Mjp7czo2OiJiYXNrZXQiO2E6MDp7fXM6NzoidXNlcl9pZCI7aTowO31fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjg1Nzg2NjI7czoxOiJjIjtpOjEzNjg1Nzg2MTI7czoxOiJsIjtzOjE6IjAiO30=',1368578664),('74qbh8oos180t7ljtia9j1av30','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgxMjg5NjQ7czoxOiJjIjtpOjEzNjgxMjg5NTk7czoxOiJsIjtzOjE6IjAiO30=',1368128965),('7g2mft2nc33e61huf4p50n9ig7','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgxMzU0OTY7czoxOiJjIjtpOjEzNjgxMzU0OTQ7czoxOiJsIjtzOjE6IjAiO30=',1368135497),('7irqheaiu9suutrn0flf9f2uj6','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgxMzI2NDA7czoxOiJjIjtpOjEzNjgxMzI2Mzc7czoxOiJsIjtzOjE6IjAiO30=',1368132641),('7n7h1q31c6ehvph1d0prn8tai5','X3NmMl9hdHRyaWJ1dGVzfGE6Mjp7czo2OiJiYXNrZXQiO2E6MDp7fXM6NzoidXNlcl9pZCI7aTowO31fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgxNDA5MjE7czoxOiJjIjtpOjEzNjgxNDA5MTA7czoxOiJsIjtzOjE6IjAiO30=',1368140921),('85b47c6qqpur477ptjfnsook17','X3NmMl9hdHRyaWJ1dGVzfGE6Mjp7czo2OiJiYXNrZXQiO2E6MDp7fXM6NzoidXNlcl9pZCI7czoxOiIxIjt9X3NmMl9mbGFzaGVzfGE6MDp7fV9zZjJfbWV0YXxhOjM6e3M6MToidSI7aToxMzY4MTQyOTI0O3M6MToiYyI7aToxMzY4MTQyOTA2O3M6MToibCI7czoxOiIwIjt9',1368142925),('85fenh1gfl9i3s37rubn7afaj7','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgxMzM4NzE7czoxOiJjIjtpOjEzNjgxMzM4NjQ7czoxOiJsIjtzOjE6IjAiO30=',1368133871),('8geqjc2mhgfv7ue8s88jkcipf1','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgxMzU0Mjk7czoxOiJjIjtpOjEzNjgxMzU0Mjc7czoxOiJsIjtzOjE6IjAiO30=',1368135430),('8k73frusqflapu8dgljrlmu663','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgzOTgyOTU7czoxOiJjIjtpOjEzNjgzOTgyOTM7czoxOiJsIjtzOjE6IjAiO30=',1368398295),('8msr87tgtivbuphtovahch6oc0','X3NmMl9hdHRyaWJ1dGVzfGE6Mjp7czo2OiJiYXNrZXQiO2E6MDp7fXM6NzoidXNlcl9pZCI7aTowO31fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjg2MzkxMzA7czoxOiJjIjtpOjEzNjg2MzkxMDk7czoxOiJsIjtzOjE6IjAiO30=',1368639131),('8urhmp00vc2bn2uuntrvjl58c2','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgxMzAzNjk7czoxOiJjIjtpOjEzNjgxMzAzNjY7czoxOiJsIjtzOjE6IjAiO30=',1368130370),('90hf8rmr8ettp4ho0uv8rev8t7','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjg0NzIzMzA7czoxOiJjIjtpOjEzNjg0NzIzMjg7czoxOiJsIjtzOjE6IjAiO30=',1368472330),('ar3b54rd2fvm37magj32dnik87','X3NmMl9hdHRyaWJ1dGVzfGE6Mjp7czo2OiJiYXNrZXQiO2E6MDp7fXM6NzoidXNlcl9pZCI7aTowO31fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgyMDgxOTQ7czoxOiJjIjtpOjEzNjgyMDgxNzE7czoxOiJsIjtzOjE6IjAiO30=',1368208194),('b1jh92u2gtema193q7h86u6ad2','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgxMzQ5ODg7czoxOiJjIjtpOjEzNjgxMzQ5ODY7czoxOiJsIjtzOjE6IjAiO30=',1368134988),('b4ff81jg6aopt8v09tsmoh2tp0','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgxMzU1OTQ7czoxOiJjIjtpOjEzNjgxMzU1OTM7czoxOiJsIjtzOjE6IjAiO30=',1368135595),('ckh05okuq63dva1g9e4f7gtac5','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgyMDgyMDI7czoxOiJjIjtpOjEzNjgyMDgyMDE7czoxOiJsIjtzOjE6IjAiO30=',1368208203),('ckmdtlu9eb7s115rqqgc53t1k0','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgyMDE4NDk7czoxOiJjIjtpOjEzNjgyMDE4NDc7czoxOiJsIjtzOjE6IjAiO30=',1368201850),('cue9t0jm8pa91u0kdh76t2tea6','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgxMzE2NzE7czoxOiJjIjtpOjEzNjgxMzE2Njg7czoxOiJsIjtzOjE6IjAiO30=',1368131671),('d23dmtg6p89951eh2b8e597q92','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgxMzEzODY7czoxOiJjIjtpOjEzNjgxMzEzODQ7czoxOiJsIjtzOjE6IjAiO30=',1368131387),('deparnche1t8e922kqldpr8cp1','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgxMzQ5Mzg7czoxOiJjIjtpOjEzNjgxMzQ5MzU7czoxOiJsIjtzOjE6IjAiO30=',1368134938),('dhgos42jkv2aue2ftgh3ecujp5','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgxMzU4NDc7czoxOiJjIjtpOjEzNjgxMzU4NDU7czoxOiJsIjtzOjE6IjAiO30=',1368135847),('dl8qc9jeg39cutp08162jbru85','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgxMzYwNDQ7czoxOiJjIjtpOjEzNjgxMzYwNDE7czoxOiJsIjtzOjE6IjAiO30=',1368136044),('drtqjo2p22mi0aof8hbtl3mlt6','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgxMzI1MzQ7czoxOiJjIjtpOjEzNjgxMzI1MzI7czoxOiJsIjtzOjE6IjAiO30=',1368132535),('etlp2dbgna4m60k4k1dtfrvin4','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjg1NzgxMjQ7czoxOiJjIjtpOjEzNjg1NzgxMjQ7czoxOiJsIjtzOjE6IjAiO30=',1368578126),('f0nblb3aa9r83hlegfj9a3g3e7','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgxMjgzMDA7czoxOiJjIjtpOjEzNjgxMjgyOTg7czoxOiJsIjtzOjE6IjAiO30=',1368128301),('f8mj28palj1vbvgipec092etp1','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNzAyNjM3MDQ7czoxOiJjIjtpOjEzNzAyMTkyMzQ7czoxOiJsIjtzOjE6IjAiO30=',1370263705),('fc91sj3u601c3hkkllae011v92','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgxMzE2NDQ7czoxOiJjIjtpOjEzNjgxMzE2NDI7czoxOiJsIjtzOjE6IjAiO30=',1368131644),('fiorogl355mprca0oj8chufvq0','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgxMjg0NDg7czoxOiJjIjtpOjEzNjgxMjg0NDY7czoxOiJsIjtzOjE6IjAiO30=',1368128448),('fo98s9e01s7f6ev1jgktmlgch3','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgxMzIzNjA7czoxOiJjIjtpOjEzNjgxMzIzNTc7czoxOiJsIjtzOjE6IjAiO30=',1368132361),('fr11t2t28mqtqubjg6ncc2j5r3','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgxMzUxNjA7czoxOiJjIjtpOjEzNjgxMzUxNTg7czoxOiJsIjtzOjE6IjAiO30=',1368135160),('gtfvjqm99bu2rfi8kmsm607q63','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjg1NzIzMjM7czoxOiJjIjtpOjEzNjg1NzIzMTk7czoxOiJsIjtzOjE6IjAiO30=',1368572325),('h9o5gu1f2tnesp016tar9t57k0','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgxMzUzODg7czoxOiJjIjtpOjEzNjgxMzUzODc7czoxOiJsIjtzOjE6IjAiO30=',1368135389),('hrvubqc5r7pnj2eub1kh9oqq43','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjg1NzgyMTg7czoxOiJjIjtpOjEzNjg1NzgyMTQ7czoxOiJsIjtzOjE6IjAiO30=',1368578220),('hs9ol7r2duljh7332hggt521i3','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgxMzA5NjY7czoxOiJjIjtpOjEzNjgxMzA5NjM7czoxOiJsIjtzOjE6IjAiO30=',1368130966),('i5lr9rvej2tbic86fqts69fi37','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgyMTcwMjY7czoxOiJjIjtpOjEzNjgyMTcwMjM7czoxOiJsIjtzOjE6IjAiO30=',1368217027),('i8p3jmjb7bvldeugn2d9rkcjg0','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgxMzM3OTI7czoxOiJjIjtpOjEzNjgxMzM3ODk7czoxOiJsIjtzOjE6IjAiO30=',1368133793),('im7n8hh65gm6fqt9i01989uba0','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgxMzAzODI7czoxOiJjIjtpOjEzNjgxMzAzODA7czoxOiJsIjtzOjE6IjAiO30=',1368130383),('jdisgtp333s6shm78n6k06ns97','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgxMzU4OTM7czoxOiJjIjtpOjEzNjgxMzU4OTA7czoxOiJsIjtzOjE6IjAiO30=',1368135894),('jes3bq1u189pabg8quav03l0j1','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgxMzM4MDY7czoxOiJjIjtpOjEzNjgxMzM4MDI7czoxOiJsIjtzOjE6IjAiO30=',1368133807),('jl8hkj76afbio381tsap28d637','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgxMzYwNzk7czoxOiJjIjtpOjEzNjgxMzYwNzU7czoxOiJsIjtzOjE6IjAiO30=',1368136079),('l0g88e3p4jaa8rkbro57qh2vb0','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgxMzA3MTY7czoxOiJjIjtpOjEzNjgxMzA3MTQ7czoxOiJsIjtzOjE6IjAiO30=',1368130716),('l1ng0ttstbddbci0fgq38jdd47','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgxMzU2NzQ7czoxOiJjIjtpOjEzNjgxMzU2NzE7czoxOiJsIjtzOjE6IjAiO30=',1368135675),('letd6fjtqil73apm73ldja0uh1','X3NmMl9hdHRyaWJ1dGVzfGE6Mjp7czo2OiJiYXNrZXQiO2E6MDp7fXM6NzoidXNlcl9pZCI7czoxOiIxIjt9X3NmMl9mbGFzaGVzfGE6MDp7fV9zZjJfbWV0YXxhOjM6e3M6MToidSI7aToxMzcwODU1NDY2O3M6MToiYyI7aToxMzY3MDgwODIwO3M6MToibCI7czoxOiIwIjt9',1370855466),('m4712hru3ke905fn3hosicf8j6','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjg1NzgyNDE7czoxOiJjIjtpOjEzNjg1NzgyNDE7czoxOiJsIjtzOjE6IjAiO30=',1368578243),('mkciulns837m0s9b4s0do821g5','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjg2MzkxMzc7czoxOiJjIjtpOjEzNjg2MzkxMzU7czoxOiJsIjtzOjE6IjAiO30=',1368639137),('n737rcf2p7qsgeq9quau0rlv83','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgxMzU0NTc7czoxOiJjIjtpOjEzNjgxMzU0NTQ7czoxOiJsIjtzOjE6IjAiO30=',1368135457),('n8rbv7npr3jcjk95k7kuqtgtt7','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgxMzM2NDc7czoxOiJjIjtpOjEzNjgxMzM2NDM7czoxOiJsIjtzOjE6IjAiO30=',1368133648),('ne8ia0ils8kqem42k96q8ucap7','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgxMzUzMzQ7czoxOiJjIjtpOjEzNjgxMzUzMzI7czoxOiJsIjtzOjE6IjAiO30=',1368135334),('ng381cbjg19ec9scs9ft2cuk62','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgxMzE4OTY7czoxOiJjIjtpOjEzNjgxMzE4OTM7czoxOiJsIjtzOjE6IjAiO30=',1368131896),('nvl22iu7026fa5ots5kvvr6gu1','X3NmMl9hdHRyaWJ1dGVzfGE6Mjp7czo2OiJiYXNrZXQiO2E6MDp7fXM6NzoidXNlcl9pZCI7aTowO31fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgyMTcwMTk7czoxOiJjIjtpOjEzNjgyMTY5OTU7czoxOiJsIjtzOjE6IjAiO30=',1368217019),('oktfa1ss3ovbi18ai0p89oh0j6','X3NmMl9hdHRyaWJ1dGVzfGE6Mjp7czo2OiJiYXNrZXQiO2E6MDp7fXM6NzoidXNlcl9pZCI7czoxOiIxIjt9X3NmMl9mbGFzaGVzfGE6MDp7fV9zZjJfbWV0YXxhOjM6e3M6MToidSI7aToxMzY2NzYzNjU3O3M6MToiYyI7aToxMzY2NzE0NjMwO3M6MToibCI7czoxOiIwIjt9',1366763659),('p37dqgfn9vns3mqsrvg6dnv0f3','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjg2Mzk3NjQ7czoxOiJjIjtpOjEzNjg2Mzk3NjE7czoxOiJsIjtzOjE6IjAiO30=',1368639765),('p7p992g9co5ftk3luquto7egv5','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgxMzA5MTY7czoxOiJjIjtpOjEzNjgxMzA5MTM7czoxOiJsIjtzOjE6IjAiO30=',1368130917),('pcad8s8ubsghb02p6cjupchrr2','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgxMzI1Nzc7czoxOiJjIjtpOjEzNjgxMzI1NzQ7czoxOiJsIjtzOjE6IjAiO30=',1368132578),('qgco9kdgqnn97kuhkfe3ehueb3','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgxMzUzNzQ7czoxOiJjIjtpOjEzNjgxMzUzNzE7czoxOiJsIjtzOjE6IjAiO30=',1368135375),('r6ii8k61tj75uvdf2a50ef2at6','X3NmMl9hdHRyaWJ1dGVzfGE6Mjp7czo2OiJiYXNrZXQiO2E6MDp7fXM6NzoidXNlcl9pZCI7aTowO31fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgzOTgyODg7czoxOiJjIjtpOjEzNjgzOTgyNjU7czoxOiJsIjtzOjE6IjAiO30=',1368398288),('rrh9m2q9io11bkg3d55vm6oj62','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgxMzM2NjE7czoxOiJjIjtpOjEzNjgxMzM2NTg7czoxOiJsIjtzOjE6IjAiO30=',1368133662),('rulhhj5qdkffjp4b2oo61msok3','X3NmMl9hdHRyaWJ1dGVzfGE6Mjp7czo2OiJiYXNrZXQiO2E6MDp7fXM6NzoidXNlcl9pZCI7aTowO31fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgxNDA4NzU7czoxOiJjIjtpOjEzNjgxNDA4NjE7czoxOiJsIjtzOjE6IjAiO30=',1368140876),('s6m6drtldb7kt4fecr9verais2','X3NmMl9hdHRyaWJ1dGVzfGE6Mjp7czo2OiJiYXNrZXQiO2E6MDp7fXM6NzoidXNlcl9pZCI7aTowO31fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgzOTgxMjU7czoxOiJjIjtpOjEzNjgzOTgxMDE7czoxOiJsIjtzOjE6IjAiO30=',1368398125),('s8fqv9ect5g1pn8ff1kg57qsh0','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgxMzE3ODc7czoxOiJjIjtpOjEzNjgxMzE3ODM7czoxOiJsIjtzOjE6IjAiO30=',1368131787),('sb61ohja1rau3l2kge5vkjoc25','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgxMjg3ODQ7czoxOiJjIjtpOjEzNjgxMjg3ODM7czoxOiJsIjtzOjE6IjAiO30=',1368128785),('secdmet59gfn59k31s4dgcuvr0','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgxMzE2MjA7czoxOiJjIjtpOjEzNjgxMzE2MTc7czoxOiJsIjtzOjE6IjAiO30=',1368131621),('ualuen1ngpeq6t7a780mb8ie20','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgyMDgxMDk7czoxOiJjIjtpOjEzNjgyMDgxMDY7czoxOiJsIjtzOjE6IjAiO30=',1368208109),('ugbdovki8vkk4aepr2ai5bphm1','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgyMDgxMTY7czoxOiJjIjtpOjEzNjgyMDgxMTQ7czoxOiJsIjtzOjE6IjAiO30=',1368208117),('v5uqgi71rvval0rl2dfvh20uj4','X3NmMl9hdHRyaWJ1dGVzfGE6Mjp7czo2OiJiYXNrZXQiO2E6MDp7fXM6NzoidXNlcl9pZCI7czoxOiIxIjt9X3NmMl9mbGFzaGVzfGE6MDp7fV9zZjJfbWV0YXxhOjM6e3M6MToidSI7aToxMzY4MTQyNzMxO3M6MToiYyI7aToxMzY4MTQyNzEwO3M6MToibCI7czoxOiIwIjt9',1368142731),('vau8s9v06q4behrkdb5jrm3cm5','X3NmMl9hdHRyaWJ1dGVzfGE6MTp7czo2OiJiYXNrZXQiO2E6MDp7fX1fc2YyX2ZsYXNoZXN8YTowOnt9X3NmMl9tZXRhfGE6Mzp7czoxOiJ1IjtpOjEzNjgxMzIwMTY7czoxOiJjIjtpOjEzNjgxMzIwMTU7czoxOiJsIjtzOjE6IjAiO30=',1368132017);
/*!40000 ALTER TABLE `session` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `module` varchar(100) NOT NULL DEFAULT '',
  `property` varchar(100) NOT NULL DEFAULT '',
  `value` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`module`,`property`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES ('catalog','order_default','name'),('catalog','order_list','list'),('editor','type','ckeditor');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `structure`
--

DROP TABLE IF EXISTS `structure`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `structure` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent` int(11) DEFAULT '0',
  `name` varchar(80) DEFAULT '',
  `template` varchar(50) DEFAULT 'inner',
  `alias` varchar(250) DEFAULT '',
  `path` text,
  `date` int(11) DEFAULT '0',
  `update` int(11) DEFAULT '0',
  `pos` int(11) DEFAULT '0',
  `link` int(11) DEFAULT '0',
  `controller` varchar(20) DEFAULT 'page',
  `action` varchar(20) DEFAULT 'index',
  `sort` varchar(20) DEFAULT 'pos ASC',
  `title` varchar(80) DEFAULT '',
  `notice` text,
  `content` text,
  `thumb` varchar(250) DEFAULT '',
  `image` varchar(250) DEFAULT '',
  `keywords` varchar(120) DEFAULT '',
  `description` varchar(120) DEFAULT '',
  `author` int(11) DEFAULT '0',
  `nofollow` tinyint(1) NOT NULL DEFAULT '0',
  `hidden` tinyint(4) DEFAULT '0',
  `protected` tinyint(4) DEFAULT '0',
  `system` tinyint(4) DEFAULT '0',
  `deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_structure` (`parent`),
  KEY `date` (`date`),
  KEY `order` (`parent`,`pos`),
  KEY `request` (`alias`)
) ENGINE=InnoDB AUTO_INCREMENT=95 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `structure`
--

LOCK TABLES `structure` WRITE;
/*!40000 ALTER TABLE `structure` DISABLE KEYS */;
INSERT INTO `structure` VALUES (1,0,'Главная','index','index','a:1:{i:0;a:3:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:14:\"Главная\";s:3:\"url\";s:5:\"index\";}}',1294002000,1359867932,0,0,'page','index','pos ASC','Главная','','<p>\r\n	Эта страница была создана в автоматическом режиме</p>\r\n<p>\r\n	Чтобы перейти к управлению сайтом, зайдите в <a href=\"/admin\">панель управления</a></p>','','','','',1,0,1,0,0,0),(2,1,'Новости','inner','news','a:2:{i:0;a:3:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:14:\"Главная\";s:3:\"url\";s:5:\"index\";}i:1;a:3:{s:2:\"id\";s:1:\"2\";s:4:\"name\";s:14:\"Новости\";s:3:\"url\";s:4:\"news\";}}',1294174800,1367019444,1,1,'news','index','pos ASC','Новости','','','','','','',1,0,0,0,0,0),(3,1,'О компании','inner','about','a:2:{i:0;a:3:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:14:\"Главная\";s:3:\"url\";s:5:\"index\";}i:1;a:3:{s:2:\"id\";s:1:\"3\";s:4:\"name\";s:19:\"О компании\";s:3:\"url\";s:5:\"about\";}}',1294174800,1367982440,0,0,'page','index','pos ASC','О компании','','<p>\r\n	Мы — команда специалистов высокого уровня, занимаемся созданием сайтов по индивидуальному заказу, по самым современным требованиям и стандартам.</p>\r\n<p>\r\n	Непрерывно ищем новые пути развития наших продуктов, новые решения при ведении проектов.</p>\r\n<p>\r\n	Нашими ключевыми качествами являются:</p>\r\n<ul>\r\n	<li>\r\n		Опыт работы в сфере web-разработок более 5 лет;</li>\r\n	<li>\r\n		Оперативное и качественное выполнение работ;</li>\r\n	<li>\r\n		Лояльная ценовая политика;</li>\r\n	<li>\r\n		Комплексный подход к решению задач клиента:<br />\r\n		от проектирования, создания концепции и дизайна,<br />\r\n		до продвижения в поисковых системах и поддержки<br />\r\n		рекламной кампании.</li>\r\n</ul>','','','Создание сайтов, Команда специалистов, Комплексный подход, Низкие цены','Мы — команда специалистов высокого уровня, занимаемся созданием сайтов по индивидуальному заказу',1,0,0,0,0,0),(4,1,'Контакты','inner','contacts','a:2:{i:0;a:3:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:14:\"Главная\";s:3:\"url\";s:5:\"index\";}i:1;a:3:{s:2:\"id\";s:1:\"4\";s:4:\"name\";s:16:\"Контакты\";s:3:\"url\";s:8:\"contacts\";}}',1294174800,1344506783,7,0,'page','index','pos ASC','Контакты','','<p>Контактная информация</p>','','','','',1,0,0,0,0,0),(5,1,'Связь','inner','feedback','a:2:{i:0;a:3:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:14:\"Главная\";s:3:\"url\";s:5:\"index\";}i:1;a:3:{s:2:\"id\";s:1:\"5\";s:4:\"name\";s:10:\"Связь\";s:3:\"url\";s:8:\"feedback\";}}',1294952400,1341150295,9,0,'feedback','index','pos ASC','Обратная связь','','<p>Контактная информация</p>','','','','',1,0,1,0,0,0),(41,1,'Портфолио','inner','portfolio','a:2:{i:0;a:3:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:14:\"Главная\";s:3:\"url\";s:5:\"index\";}i:1;a:3:{s:2:\"id\";s:2:\"41\";s:4:\"name\";s:18:\"Портфолио\";s:3:\"url\";s:9:\"portfolio\";}}',1295125200,1345120972,2,7,'gallery','index','pos ASC','Портфолио','','','','','','',1,0,0,0,0,0),(42,1,'Каталог','inner','catalog','a:2:{i:0;a:3:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:14:\"Главная\";s:3:\"url\";s:5:\"index\";}i:1;a:3:{s:2:\"id\";s:2:\"42\";s:4:\"name\";s:14:\"Каталог\";s:3:\"url\";s:7:\"catalog\";}}',1297285200,1346949522,5,17,'catalog','index','pos ASC','Каталог','','<p>\r\n	Информационная страница для наполнения</p>','','','','',1,0,0,0,0,0),(43,1,'Картинки','inner','pictures','a:2:{i:0;a:3:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:14:\"Главная\";s:3:\"url\";s:5:\"index\";}i:1;a:3:{s:2:\"id\";s:2:\"43\";s:4:\"name\";s:16:\"Картинки\";s:3:\"url\";s:8:\"pictures\";}}',1299099600,1360408477,4,8,'gallery','index','pos ASC','Картинки','','<p>\r\n	Информационная страница для наполнения</p>','','','','',1,0,0,0,0,0),(44,1,'Гостевая','inner','guest','a:2:{i:0;a:3:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:14:\"Главная\";s:3:\"url\";s:5:\"index\";}i:1;a:3:{s:2:\"id\";s:2:\"44\";s:4:\"name\";s:16:\"Гостевая\";s:3:\"url\";s:5:\"guest\";}}',1331669278,1334862925,8,0,'guestbook','index','pos ASC','Гостевая','','','','','','',1,0,0,0,0,0),(46,3,'Наша миссия','inner','about/nasha-missiya','a:3:{i:0;a:3:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:14:\"Главная\";s:3:\"url\";s:5:\"index\";}i:1;a:3:{s:2:\"id\";s:1:\"3\";s:4:\"name\";s:19:\"О компании\";s:3:\"url\";s:5:\"about\";}i:2;a:3:{s:2:\"id\";s:2:\"46\";s:4:\"name\";s:21:\"Наша миссия\";s:3:\"url\";s:19:\"about/nasha-missiya\";}}',1339273744,1368142922,0,0,'page','index','pos ASC','Наша миссия','','<p>\r\n	Информационная <a href=\"/files/catalog/gallery/0010/14_ksp2-100x100-FFFFFF-1.png\">страница для наполнения</a></p>','','','','',1,0,0,0,0,0),(47,3,'Реквизиты','inner','about/rekvizity','a:3:{i:0;a:3:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:14:\"Главная\";s:3:\"url\";s:5:\"index\";}i:1;a:3:{s:2:\"id\";s:1:\"3\";s:4:\"name\";s:19:\"О компании\";s:3:\"url\";s:5:\"about\";}i:2;a:3:{s:2:\"id\";s:2:\"47\";s:4:\"name\";s:18:\"Реквизиты\";s:3:\"url\";s:15:\"about/rekvizity\";}}',1339273950,1347699679,3,0,'page','index','pos ASC','Реквизиты','','<p>Информационная страница для наполнения</p>','','','','',1,0,0,0,0,0),(48,3,'Сотрудники','inner','about/sotrudniki','a:3:{i:0;a:3:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:14:\"Главная\";s:3:\"url\";s:5:\"index\";}i:1;a:3:{s:2:\"id\";s:1:\"3\";s:4:\"name\";s:19:\"О компании\";s:3:\"url\";s:5:\"about\";}i:2;a:3:{s:2:\"id\";s:2:\"48\";s:4:\"name\";s:20:\"Сотрудники\";s:3:\"url\";s:16:\"about/sotrudniki\";}}',1339274353,1341158612,2,0,'page','index','pos ASC','Сотрудники','','<p>Информационная страница для наполнения</p>','','','','',1,0,0,0,0,0),(50,1,'Блог','inner','blog','a:2:{i:0;a:3:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:14:\"Главная\";s:3:\"url\";s:5:\"index\";}i:1;a:3:{s:2:\"id\";s:2:\"50\";s:4:\"name\";s:8:\"Блог\";s:3:\"url\";s:4:\"blog\";}}',1339506965,1359170270,3,3,'news','index','pos ASC','Блог','','<p>\r\n	Информационная страница для наполнения</p>','','','','',1,0,0,0,0,0),(51,42,'Телефоны','inner','catalog/telefony','a:3:{i:0;a:3:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:14:\"Главная\";s:3:\"url\";s:5:\"index\";}i:1;a:3:{s:2:\"id\";s:2:\"42\";s:4:\"name\";s:14:\"Каталог\";s:3:\"url\";s:7:\"catalog\";}i:2;a:3:{s:2:\"id\";s:2:\"51\";s:4:\"name\";s:16:\"Телефоны\";s:3:\"url\";s:16:\"catalog/telefony\";}}',1339531123,1341165731,2,1,'catalog','index','pos ASC','Телефоны','','<p>Информационная страница для наполнения</p>','','','','',1,0,0,0,0,0),(52,42,'Автомобили','inner','catalog/avtomobili','a:3:{i:0;a:3:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:14:\"Главная\";s:3:\"url\";s:5:\"index\";}i:1;a:3:{s:2:\"id\";s:2:\"42\";s:4:\"name\";s:14:\"Каталог\";s:3:\"url\";s:7:\"catalog\";}i:2;a:3:{s:2:\"id\";s:2:\"52\";s:4:\"name\";s:20:\"Автомобили\";s:3:\"url\";s:18:\"catalog/avtomobili\";}}',1339531135,1345123388,1,2,'catalog','index','pos ASC','Автомобили','','<p>Информационная страница для наполнения</p>','','','','',1,0,0,0,0,0),(53,42,'Велосипеды','inner','catalog/velosipedy','a:3:{i:0;a:3:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:14:\"Главная\";s:3:\"url\";s:5:\"index\";}i:1;a:3:{s:2:\"id\";s:2:\"42\";s:4:\"name\";s:14:\"Каталог\";s:3:\"url\";s:7:\"catalog\";}i:2;a:3:{s:2:\"id\";s:2:\"53\";s:4:\"name\";s:20:\"Велосипеды\";s:3:\"url\";s:18:\"catalog/velosipedy\";}}',1339535900,1360407666,0,20,'catalog','index','pos ASC','Велосипеды','','<p>\r\n	Информационная страница для наполнения</p>','','','','',1,0,0,0,0,0),(54,51,'Телефоны Nokia','inner','catalog/telefony/telefony-nokia','a:4:{i:0;a:3:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:14:\"Главная\";s:3:\"url\";s:5:\"index\";}i:1;a:3:{s:2:\"id\";s:2:\"42\";s:4:\"name\";s:14:\"Каталог\";s:3:\"url\";s:7:\"catalog\";}i:2;a:3:{s:2:\"id\";s:2:\"51\";s:4:\"name\";s:16:\"Телефоны\";s:3:\"url\";s:16:\"catalog/telefony\";}i:3;a:3:{s:2:\"id\";s:2:\"54\";s:4:\"name\";s:22:\"Телефоны Nokia\";s:3:\"url\";s:31:\"catalog/telefony/telefony-nokia\";}}',0,1341150519,1,5,'catalog','index','pos ASC','Телефоны Nokia','','<p>Информационная страница для наполнения</p>','','','','',1,0,0,0,0,0),(55,51,'Телефоны HTC','inner','catalog/telefony/telefony-htc','a:4:{i:0;a:3:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:14:\"Главная\";s:3:\"url\";s:5:\"index\";}i:1;a:3:{s:2:\"id\";s:2:\"42\";s:4:\"name\";s:14:\"Каталог\";s:3:\"url\";s:7:\"catalog\";}i:2;a:3:{s:2:\"id\";s:2:\"51\";s:4:\"name\";s:16:\"Телефоны\";s:3:\"url\";s:16:\"catalog/telefony\";}i:3;a:3:{s:2:\"id\";s:2:\"55\";s:4:\"name\";s:20:\"Телефоны HTC\";s:3:\"url\";s:29:\"catalog/telefony/telefony-htc\";}}',0,1345125897,2,4,'catalog','index','pos ASC','Телефоны HTC','','<p>Информационная страница для наполнения</p>','','','','',1,0,0,0,0,0),(56,51,'Телефоны Apple','inner','catalog/telefony/telefony-apple','a:4:{i:0;a:3:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:14:\"Главная\";s:3:\"url\";s:5:\"index\";}i:1;a:3:{s:2:\"id\";s:2:\"42\";s:4:\"name\";s:14:\"Каталог\";s:3:\"url\";s:7:\"catalog\";}i:2;a:3:{s:2:\"id\";s:2:\"51\";s:4:\"name\";s:16:\"Телефоны\";s:3:\"url\";s:16:\"catalog/telefony\";}i:3;a:3:{s:2:\"id\";s:2:\"56\";s:4:\"name\";s:22:\"Телефоны Apple\";s:3:\"url\";s:31:\"catalog/telefony/telefony-apple\";}}',0,1345125868,0,6,'catalog','index','pos ASC','Телефоны Apple','','<p>Информационная страница для наполнения</p>','','','','',1,0,0,0,0,0),(57,52,'Внедорожники','inner','catalog/avtomobili/vnedorozhniki','a:4:{i:0;a:3:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:14:\"Главная\";s:3:\"url\";s:5:\"index\";}i:1;a:3:{s:2:\"id\";s:2:\"42\";s:4:\"name\";s:14:\"Каталог\";s:3:\"url\";s:7:\"catalog\";}i:2;a:3:{s:2:\"id\";s:2:\"52\";s:4:\"name\";s:20:\"Автомобили\";s:3:\"url\";s:18:\"catalog/avtomobili\";}i:3;a:3:{s:2:\"id\";s:2:\"57\";s:4:\"name\";s:24:\"Внедорожники\";s:3:\"url\";s:32:\"catalog/avtomobili/vnedorozhniki\";}}',1340904545,1345127213,0,3,'catalog','index','pos ASC','Внедорожники','','<p>Информационная страница для наполнения</p>','','','','',1,0,0,0,0,0),(58,53,'Горные','inner','catalog/velosipedy/gornye','a:4:{i:0;a:3:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:14:\"Главная\";s:3:\"url\";s:5:\"index\";}i:1;a:3:{s:2:\"id\";s:2:\"42\";s:4:\"name\";s:14:\"Каталог\";s:3:\"url\";s:7:\"catalog\";}i:2;a:3:{s:2:\"id\";s:2:\"53\";s:4:\"name\";s:20:\"Велосипеды\";s:3:\"url\";s:18:\"catalog/velosipedy\";}i:3;a:3:{s:2:\"id\";s:2:\"58\";s:4:\"name\";s:12:\"Горные\";s:3:\"url\";s:25:\"catalog/velosipedy/gornye\";}}',1340904977,1345123343,1,28,'catalog','index','pos ASC','Горные','','<p>Информационная страница для наполнения</p>','','','','',1,0,0,0,0,0),(59,53,'Городские','inner','catalog/velosipedy/gorodskie','a:4:{i:0;a:3:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:14:\"Главная\";s:3:\"url\";s:5:\"index\";}i:1;a:3:{s:2:\"id\";s:2:\"42\";s:4:\"name\";s:14:\"Каталог\";s:3:\"url\";s:7:\"catalog\";}i:2;a:3:{s:2:\"id\";s:2:\"53\";s:4:\"name\";s:20:\"Велосипеды\";s:3:\"url\";s:18:\"catalog/velosipedy\";}i:3;a:3:{s:2:\"id\";s:2:\"59\";s:4:\"name\";s:18:\"Городские\";s:3:\"url\";s:28:\"catalog/velosipedy/gorodskie\";}}',1340904989,1340904991,0,29,'catalog','index','pos ASC','Городские','','<p>Информационная страница для наполнения</p>','','','','',1,0,0,0,0,0),(60,53,'Дорожные','inner','catalog/velosipedy/dorozhnye','a:4:{i:0;a:3:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:14:\"Главная\";s:3:\"url\";s:5:\"index\";}i:1;a:3:{s:2:\"id\";s:2:\"42\";s:4:\"name\";s:14:\"Каталог\";s:3:\"url\";s:7:\"catalog\";}i:2;a:3:{s:2:\"id\";s:2:\"53\";s:4:\"name\";s:20:\"Велосипеды\";s:3:\"url\";s:18:\"catalog/velosipedy\";}i:3;a:3:{s:2:\"id\";s:2:\"60\";s:4:\"name\";s:16:\"Дорожные\";s:3:\"url\";s:28:\"catalog/velosipedy/dorozhnye\";}}',1340905000,1345122071,2,30,'catalog','index','pos ASC','Дорожные','','<p>Информационная страница для наполнения</p>','','','','',1,0,0,0,0,0),(61,58,'Хардтейл','inner','catalog/velosipedy/gornye/hardteil','a:5:{i:0;a:3:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:14:\"Главная\";s:3:\"url\";s:5:\"index\";}i:1;a:3:{s:2:\"id\";s:2:\"42\";s:4:\"name\";s:14:\"Каталог\";s:3:\"url\";s:7:\"catalog\";}i:2;a:3:{s:2:\"id\";s:2:\"53\";s:4:\"name\";s:20:\"Велосипеды\";s:3:\"url\";s:18:\"catalog/velosipedy\";}i:3;a:3:{s:2:\"id\";s:2:\"58\";s:4:\"name\";s:12:\"Горные\";s:3:\"url\";s:25:\"catalog/velosipedy/gornye\";}i:4;a:3:{s:2:\"id\";s:2:\"61\";s:4:\"name\";s:16:\"Хардтейл\";s:3:\"url\";s:34:\"catalog/velosipedy/gornye/hardteil\";}}',1340905051,1345121440,0,31,'catalog','index','pos ASC','Хардтейл','','<p>Информационная страница для наполнения</p>','','','','',1,0,0,0,0,0),(62,58,'Двухподвес','inner','catalog/velosipedy/gornye/dvuhpodves','a:5:{i:0;a:3:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:14:\"Главная\";s:3:\"url\";s:5:\"index\";}i:1;a:3:{s:2:\"id\";s:2:\"42\";s:4:\"name\";s:14:\"Каталог\";s:3:\"url\";s:7:\"catalog\";}i:2;a:3:{s:2:\"id\";s:2:\"53\";s:4:\"name\";s:20:\"Велосипеды\";s:3:\"url\";s:18:\"catalog/velosipedy\";}i:3;a:3:{s:2:\"id\";s:2:\"58\";s:4:\"name\";s:12:\"Горные\";s:3:\"url\";s:25:\"catalog/velosipedy/gornye\";}i:4;a:3:{s:2:\"id\";s:2:\"62\";s:4:\"name\";s:20:\"Двухподвес\";s:3:\"url\";s:36:\"catalog/velosipedy/gornye/dvuhpodves\";}}',1340905068,1345125888,1,32,'catalog','index','pos ASC','Двухподвес','','<p>Информационная страница для наполнения</p>','','','','',1,0,0,0,0,0),(63,3,'Слоган','inner','contacts/slogan','a:3:{i:0;a:3:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:14:\"Главная\";s:3:\"url\";s:5:\"index\";}i:1;a:3:{s:2:\"id\";s:1:\"3\";s:4:\"name\";s:19:\"О компании\";s:3:\"url\";s:5:\"about\";}i:2;a:3:{s:2:\"id\";s:2:\"63\";s:4:\"name\";s:12:\"Слоган\";s:3:\"url\";s:15:\"contacts/slogan\";}}',1345124520,1354991525,1,0,'page','index','pos ASC','Слоган','','<p>Home page for the filling</p>','','','','',1,1,0,0,0,0),(64,1,'Бренды','inner','brends','a:2:{i:0;a:3:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:14:\"Главная\";s:3:\"url\";s:5:\"index\";}i:1;a:3:{s:2:\"id\";s:2:\"64\";s:4:\"name\";s:12:\"Бренды\";s:3:\"url\";s:6:\"brends\";}}',1346694627,1355162485,6,0,'manufacturers','index','pos ASC','Бренды','','<p>Home page for the filling</p>','','','','',1,0,0,0,0,0),(65,3,'top','inner','about/top','a:3:{i:0;a:3:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:14:\"Главная\";s:3:\"url\";s:5:\"index\";}i:1;a:3:{s:2:\"id\";s:1:\"3\";s:4:\"name\";s:19:\"О компании\";s:3:\"url\";s:5:\"about\";}i:2;a:3:{s:2:\"id\";s:2:\"65\";s:4:\"name\";s:3:\"top\";s:3:\"url\";s:9:\"about/top\";}}',1367508992,1367510428,4,0,'page','index','pos ASC','top','','<p>\r\n	Информационная страница для наполнения</p>','','','','',1,0,0,0,0,1),(69,3,'top','inner','about/top','a:3:{i:0;a:3:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:14:\"Главная\";s:3:\"url\";s:5:\"index\";}i:1;a:3:{s:2:\"id\";s:1:\"3\";s:4:\"name\";s:19:\"О компании\";s:3:\"url\";s:5:\"about\";}i:2;a:3:{s:2:\"id\";s:2:\"69\";s:4:\"name\";s:3:\"top\";s:3:\"url\";s:9:\"about/top\";}}',1367510826,1367511062,4,0,'page','index','pos ASC','top','','<p>\r\n	Информационная страница для наполнения</p>','','','','',1,0,0,0,0,1),(70,1,'Наша миссия2','inner','testpage','a:2:{i:0;a:3:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:14:\"Главная\";s:3:\"url\";s:5:\"index\";}i:1;a:3:{s:2:\"id\";s:2:\"70\";s:4:\"name\";s:22:\"Наша миссия2\";s:3:\"url\";s:8:\"testpage\";}}',1368185837,1368185839,10,0,'page','index','pos ASC','Наша миссия2','','<p>\r\n	Информационная страница для наполнения</p>','','','','',1,0,0,0,0,1),(71,1,'Наша миссия2','inner','testpage','a:2:{i:0;a:3:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:14:\"Главная\";s:3:\"url\";s:5:\"index\";}i:1;a:3:{s:2:\"id\";s:2:\"71\";s:4:\"name\";s:22:\"Наша миссия2\";s:3:\"url\";s:8:\"testpage\";}}',1368185909,1368185985,18,0,'page','index','pos ASC','Наша миссия2','','<p>\r\n	Информационная страница для наполнения</p>','','','','',1,0,0,0,0,1),(72,1,'testpage2','inner','testpage','a:2:{i:0;a:3:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:14:\"Главная\";s:3:\"url\";s:5:\"index\";}i:1;a:3:{s:2:\"id\";s:2:\"72\";s:4:\"name\";s:9:\"testpage2\";s:3:\"url\";s:8:\"testpage\";}}',1368186619,1368187131,19,0,'page','index','pos ASC','testpage','','Привет мир!','','','','',1,0,0,0,0,1),(73,1,'testpage2','inner','testpage','a:2:{i:0;a:3:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:14:\"Главная\";s:3:\"url\";s:5:\"index\";}i:1;a:3:{s:2:\"id\";s:2:\"73\";s:4:\"name\";s:9:\"testpage2\";s:3:\"url\";s:8:\"testpage\";}}',1368188215,1368188435,20,0,'page','index','pos ASC','testpage','','<p>\r\n	Hello world!</p>','','','','',1,0,0,0,0,1),(74,1,'testpage2','inner','testpage','a:2:{i:0;a:3:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:14:\"Главная\";s:3:\"url\";s:5:\"index\";}i:1;a:3:{s:2:\"id\";s:2:\"74\";s:4:\"name\";s:9:\"testpage2\";s:3:\"url\";s:8:\"testpage\";}}',1368190211,1368190246,21,0,'page','index','pos ASC','testpage','','Hello world!','','','','',1,0,0,0,0,1),(75,1,'testpage2','inner','testpage','a:2:{i:0;a:3:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:14:\"Главная\";s:3:\"url\";s:5:\"index\";}i:1;a:3:{s:2:\"id\";s:2:\"75\";s:4:\"name\";s:9:\"testpage2\";s:3:\"url\";s:8:\"testpage\";}}',1368196272,1368196307,22,0,'page','index','pos ASC','testpage','','Hello world!','','','','',1,0,0,0,0,1),(76,1,'testpage2','inner','testpage','a:2:{i:0;a:3:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:14:\"Главная\";s:3:\"url\";s:5:\"index\";}i:1;a:3:{s:2:\"id\";s:2:\"76\";s:4:\"name\";s:9:\"testpage2\";s:3:\"url\";s:8:\"testpage\";}}',1368196580,1368196615,23,0,'page','index','pos ASC','testpage','','<p>\r\n	Информационная страница для наполнения</p>','','','','',1,0,0,0,0,1),(77,1,'testpage2','inner','testpage','a:2:{i:0;a:3:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:14:\"Главная\";s:3:\"url\";s:5:\"index\";}i:1;a:3:{s:2:\"id\";s:2:\"77\";s:4:\"name\";s:9:\"testpage2\";s:3:\"url\";s:8:\"testpage\";}}',1368196994,1368197029,24,0,'page','index','pos ASC','testpage','','<p>\r\n	Hello world!</p>','','','','',1,0,0,0,0,1),(78,1,'testpage2','inner','testpage','a:2:{i:0;a:3:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:14:\"Главная\";s:3:\"url\";s:5:\"index\";}i:1;a:3:{s:2:\"id\";s:2:\"78\";s:4:\"name\";s:9:\"testpage2\";s:3:\"url\";s:8:\"testpage\";}}',1368197165,1368197611,25,0,'page','index','pos ASC','testpage','','Hello world!','','','','',1,0,0,0,0,1),(79,1,'testpage2','inner','testpage','a:2:{i:0;a:3:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:14:\"Главная\";s:3:\"url\";s:5:\"index\";}i:1;a:3:{s:2:\"id\";s:2:\"79\";s:4:\"name\";s:9:\"testpage2\";s:3:\"url\";s:8:\"testpage\";}}',1368197726,1368197795,26,0,'page','index','pos ASC','testpage','','<p>\r\n	Hello world!</p>','','','','',1,0,0,0,0,1),(80,1,'testpage2','inner','testpage','a:2:{i:0;a:3:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:14:\"Главная\";s:3:\"url\";s:5:\"index\";}i:1;a:3:{s:2:\"id\";s:2:\"80\";s:4:\"name\";s:9:\"testpage2\";s:3:\"url\";s:8:\"testpage\";}}',1368198084,1368198121,27,0,'page','index','pos ASC','testpage','','<p>\r\n	Информационная страница для наполнения</p>','','','','',1,0,0,0,0,1),(81,1,'testpage2','inner','testpage','a:2:{i:0;a:3:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:14:\"Главная\";s:3:\"url\";s:5:\"index\";}i:1;a:3:{s:2:\"id\";s:2:\"81\";s:4:\"name\";s:9:\"testpage2\";s:3:\"url\";s:8:\"testpage\";}}',1368200200,1368200210,28,0,'page','index','pos ASC','testpage','','<p>\r\n	Информационная страница для наполнения</p>','','','','',1,0,0,0,0,1),(82,1,'testpage2','inner','testpage','a:2:{i:0;a:3:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:14:\"Главная\";s:3:\"url\";s:5:\"index\";}i:1;a:3:{s:2:\"id\";s:2:\"82\";s:4:\"name\";s:9:\"testpage2\";s:3:\"url\";s:8:\"testpage\";}}',1368200688,1368200699,29,0,'page','index','pos ASC','testpage','','<p>\r\n	Информационная страница для наполнения</p>','','','','',1,0,0,0,0,1),(83,1,'testpage2','inner','testpage','a:2:{i:0;a:3:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:14:\"Главная\";s:3:\"url\";s:5:\"index\";}i:1;a:3:{s:2:\"id\";s:2:\"83\";s:4:\"name\";s:9:\"testpage2\";s:3:\"url\";s:8:\"testpage\";}}',1368200859,1368200866,30,0,'page','index','pos ASC','testpage','','<p>\r\n	Информационная страница для наполнения</p>','','','','',1,0,0,0,0,1),(84,1,'testpage2','inner','testpage','a:2:{i:0;a:3:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:14:\"Главная\";s:3:\"url\";s:5:\"index\";}i:1;a:3:{s:2:\"id\";s:2:\"84\";s:4:\"name\";s:9:\"testpage2\";s:3:\"url\";s:8:\"testpage\";}}',1368201742,1368201749,17,0,'page','index','pos ASC','testpage','','<p>\r\n	Информационная страница для наполнения</p>','','','','',1,0,0,0,0,1),(85,1,'testpage2','inner','testpage','a:2:{i:0;a:3:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:14:\"Главная\";s:3:\"url\";s:5:\"index\";}i:1;a:3:{s:2:\"id\";s:2:\"85\";s:4:\"name\";s:9:\"testpage2\";s:3:\"url\";s:8:\"testpage\";}}',1368208086,1368208093,16,0,'page','index','pos ASC','testpage','','<p>\r\n	Информационная страница для наполнения</p>','','','','',1,0,0,0,0,1),(86,1,'testpage2','inner','testpage','a:2:{i:0;a:3:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:14:\"Главная\";s:3:\"url\";s:5:\"index\";}i:1;a:3:{s:2:\"id\";s:2:\"86\";s:4:\"name\";s:9:\"testpage2\";s:3:\"url\";s:8:\"testpage\";}}',1368208181,1368208187,15,0,'page','index','pos ASC','testpage','','<p>\r\n	Информационная страница для наполнения</p>','','','','',1,0,0,0,0,1),(87,1,'testpage2','inner','testpage','a:2:{i:0;a:3:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:14:\"Главная\";s:3:\"url\";s:5:\"index\";}i:1;a:3:{s:2:\"id\";s:2:\"87\";s:4:\"name\";s:9:\"testpage2\";s:3:\"url\";s:8:\"testpage\";}}',1368217005,1368217012,14,0,'page','index','pos ASC','testpage','','<p>\r\n	Информационная страница для наполнения</p>','','','','',1,0,0,0,0,1),(88,1,'testpage2','inner','testpage','a:2:{i:0;a:3:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:14:\"Главная\";s:3:\"url\";s:5:\"index\";}i:1;a:3:{s:2:\"id\";s:2:\"88\";s:4:\"name\";s:9:\"testpage2\";s:3:\"url\";s:8:\"testpage\";}}',1368398111,1368398118,13,0,'page','index','pos ASC','testpage','','<p>\r\n	Информационная страница для наполнения</p>','','','','',1,0,0,0,0,1),(89,1,'testpage2','inner','testpage','a:2:{i:0;a:3:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:14:\"Главная\";s:3:\"url\";s:5:\"index\";}i:1;a:3:{s:2:\"id\";s:2:\"89\";s:4:\"name\";s:9:\"testpage2\";s:3:\"url\";s:8:\"testpage\";}}',1368398274,1368398281,12,0,'page','index','pos ASC','testpage','','<p>\r\n	Информационная страница для наполнения</p>','','','','',1,0,0,0,0,1),(90,1,'testpage2','inner','testpage','a:2:{i:0;a:3:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:14:\"Главная\";s:3:\"url\";s:5:\"index\";}i:1;a:3:{s:2:\"id\";s:2:\"90\";s:4:\"name\";s:9:\"testpage2\";s:3:\"url\";s:8:\"testpage\";}}',1368472307,1368472314,11,0,'page','index','pos ASC','testpage','','<p>\r\n	Информационная страница для наполнения</p>','','','','',1,0,0,0,0,1),(91,1,'testpage2','inner','testpage','a:2:{i:0;a:3:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:14:\"Главная\";s:3:\"url\";s:5:\"index\";}i:1;a:3:{s:2:\"id\";s:2:\"91\";s:4:\"name\";s:9:\"testpage2\";s:3:\"url\";s:8:\"testpage\";}}',1368572275,1368572291,10,0,'page','index','pos ASC','testpage','','<p>\r\n	Информационная страница для наполнения</p>','','','','',1,0,0,0,0,1),(92,1,'testpage2','inner','testpage','a:2:{i:0;a:3:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:14:\"Главная\";s:3:\"url\";s:5:\"index\";}i:1;a:3:{s:2:\"id\";s:2:\"92\";s:4:\"name\";s:9:\"testpage2\";s:3:\"url\";s:8:\"testpage\";}}',1368578635,1368578649,10,0,'page','index','pos ASC','testpage','','<p>\r\n	Информационная страница для наполнения</p>','','','','',1,0,0,0,0,1),(93,1,'testpage2','inner','testpage','a:2:{i:0;a:3:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:14:\"Главная\";s:3:\"url\";s:5:\"index\";}i:1;a:3:{s:2:\"id\";s:2:\"93\";s:4:\"name\";s:9:\"testpage2\";s:3:\"url\";s:8:\"testpage\";}}',1368639117,1368639124,10,0,'page','index','pos ASC','testpage','','<p>\r\n	Информационная страница для наполнения</p>','','','','',1,0,0,0,0,1),(94,1,'testpage2','inner','testpage','a:2:{i:0;a:3:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:14:\"Главная\";s:3:\"url\";s:5:\"index\";}i:1;a:3:{s:2:\"id\";s:2:\"94\";s:4:\"name\";s:9:\"testpage2\";s:3:\"url\";s:8:\"testpage\";}}',1368639743,1368639749,10,0,'page','index','pos ASC','testpage','','<p>\r\n	Информационная страница для наполнения</p>','','','','',1,0,0,0,0,1);
/*!40000 ALTER TABLE `structure` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `templates`
--

DROP TABLE IF EXISTS `templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `templates` (
  `name` varchar(100) NOT NULL,
  `description` varchar(250) DEFAULT NULL,
  `template` text,
  `update` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `templates`
--

LOCK TABLES `templates` WRITE;
/*!40000 ALTER TABLE `templates` DISABLE KEYS */;
/*!40000 ALTER TABLE `templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(50) DEFAULT NULL,
  `password` varchar(40) DEFAULT NULL,
  `solt` varchar(8) DEFAULT NULL,
  `fname` varchar(20) DEFAULT NULL,
  `lname` varchar(20) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `name` varchar(250) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `fax` varchar(50) DEFAULT NULL,
  `inn` varchar(20) DEFAULT NULL,
  `kpp` varchar(20) DEFAULT NULL,
  `address` text,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `date` int(11) DEFAULT NULL,
  `last` int(11) DEFAULT NULL,
  `perm` int(11) DEFAULT NULL,
  `confirm` varchar(32) DEFAULT NULL,
  `basket` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin','b65c90889bea16927f96453f2445706b','215b9551','Аникей','Сковородкин','admin@ermin.ru','','+7 904 324-57-65','','','','',1,1294952400,1370855525,10,'','a:2:{i:0;a:5:{s:2:\"id\";s:1:\"7\";s:4:\"name\";s:10:\"HTC Evo 3D\";s:5:\"count\";s:1:\"1\";s:5:\"price\";s:5:\"15000\";s:7:\"details\";s:0:\"\";}i:1;a:5:{s:2:\"id\";s:2:\"14\";s:4:\"name\";s:9:\"Nokia 500\";s:5:\"count\";s:1:\"1\";s:5:\"price\";s:1:\"0\";s:7:\"details\";s:0:\"\";}}');
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

-- Dump completed on 2013-06-10 16:32:20
