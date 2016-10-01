-- MySQL dump 10.13  Distrib 5.7.15, for Linux (x86_64)
--
-- Host: 127.0.0.1    Database: longinusapp
-- ------------------------------------------------------
-- Server version	5.7.13-0ubuntu0.16.04.2

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
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `create_date` datetime DEFAULT NULL,
  `update_date` datetime DEFAULT NULL,
  `delete_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'animals','2016-07-16 14:46:00',NULL,NULL),(2,'peoples','2016-07-16 14:46:00',NULL,NULL),(3,'vehicles','2016-07-16 14:46:00',NULL,NULL),(4,'objects','2016-07-16 14:46:00',NULL,NULL);
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `category_properties`
--

DROP TABLE IF EXISTS `category_properties`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `category_properties` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_category` bigint(20) NOT NULL,
  `property_name` varchar(255) NOT NULL,
  `property_value` text,
  `create_date` datetime DEFAULT NULL,
  `update_date` datetime DEFAULT NULL,
  `delete_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_category_properties_categories_idx` (`id_category`),
  CONSTRAINT `fk_category_properties_categories` FOREIGN KEY (`id_category`) REFERENCES `categories` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `category_properties`
--

LOCK TABLES `category_properties` WRITE;
/*!40000 ALTER TABLE `category_properties` DISABLE KEYS */;
INSERT INTO `category_properties` VALUES (1,1,'type','[{\"name\":\"dog\",\"breed\":[{\"name\":\"Chihuaua\"},{\"name\":\"Pastor Alemao\"},{\"name\":\"Husky\"},{\"name\":\"Hotweiller\"}],\"color\":[{\"name\":\"black\"},{\"name\":\"white\"},{\"name\":\"brown\"}]},{\"name\":\"cat\",\"color\":[{\"name\":\"black\"},{\"name\":\"white\"},{\"name\":\"brown\"}]},{\"name\":\"bird\",\"color\":[{\"name\":\"black\"},{\"name\":\"white\"},{\"name\":\"yellow\"}]},{\"name\":\"other\"}]','2016-08-13 00:00:00','2016-08-13 00:00:00','2016-08-13 00:00:00');
/*!40000 ALTER TABLE `category_properties` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pictures`
--

DROP TABLE IF EXISTS `pictures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pictures` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_post` bigint(20) NOT NULL,
  `path` varchar(255) NOT NULL,
  `create_date` datetime DEFAULT NULL,
  `update_date` datetime DEFAULT NULL,
  `delete_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_pictures_posts_idx` (`id_post`),
  CONSTRAINT `fk_pictures_posts` FOREIGN KEY (`id_post`) REFERENCES `posts` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pictures`
--

LOCK TABLES `pictures` WRITE;
/*!40000 ALTER TABLE `pictures` DISABLE KEYS */;
/*!40000 ALTER TABLE `pictures` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `post_category_data`
--

DROP TABLE IF EXISTS `post_category_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `post_category_data` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_category_properties` bigint(20) NOT NULL,
  `id_post` bigint(20) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_post_category_category_properties_idx` (`id_category_properties`),
  KEY `fk_post_category_data_post_idx` (`id_post`),
  CONSTRAINT `fk_post_category_category_properties` FOREIGN KEY (`id_category_properties`) REFERENCES `category_properties` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_post_category_data_post` FOREIGN KEY (`id_post`) REFERENCES `posts` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `post_category_data`
--

LOCK TABLES `post_category_data` WRITE;
/*!40000 ALTER TABLE `post_category_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `post_category_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `posts` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `type` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `latitude` float(10,6) NOT NULL,
  `longitude` float(10,6) NOT NULL,
  `id_category` bigint(20) NOT NULL,
  `id_user` bigint(20) NOT NULL,
  `create_date` datetime DEFAULT NULL,
  `update_date` datetime DEFAULT NULL,
  `delete_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_posts_category_idx` (`id_category`),
  KEY `fk_posts_user_idx` (`id_user`),
  CONSTRAINT `fk_posts_category` FOREIGN KEY (`id_category`) REFERENCES `categories` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_posts_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posts`
--

LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
INSERT INTO `posts` VALUES (1,'Teste','Teste de feed de post',1,1,-25.445641,-49.360237,1,2,'2016-07-16 15:02:00',NULL,NULL);
/*!40000 ALTER TABLE `posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `role` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `facebook` varchar(255) DEFAULT NULL,
  `google` varchar(255) DEFAULT NULL,
  `twitter` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `create_date` datetime DEFAULT NULL,
  `update_date` datetime DEFAULT NULL,
  `delete_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,2,'admin','admin@admin.com','','','','dccd96c256bc7dd39bae41a405f25e43','','2016-07-16 11:35:00',NULL,NULL),(2,1,'João','joao@outlook.com','','','','dccd96c256bc7dd39bae41a405f25e43','1122233344','2016-07-16 12:17:56',NULL,NULL),(3,1,'','','','','','d41d8cd98f00b204e9800998ecf8427e','','2016-07-30 11:18:02',NULL,NULL),(4,1,'teste','teste@teste','','','','202cb962ac59075b964b07152d234b70','0415555555','2016-07-30 11:25:06',NULL,NULL),(5,1,'Rafael Tschannerl','tschannerl@gmail.com','','','','26e16c93272f2cf910d5ed6853a39dd2','4188063819','2016-07-30 11:43:13',NULL,NULL);
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

-- Dump completed on 2016-10-01  6:31:19
