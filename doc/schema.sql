-- Adminer 4.1.0 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';

DROP TABLE IF EXISTS `blogs`;
CREATE TABLE `blogs` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `url` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `posts`;
CREATE TABLE `posts` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `blogId` bigint(20) NOT NULL,
  `remoteId` bigint(20) NOT NULL,
  `title` varchar(100) NOT NULL,
  `url` varchar(255) NOT NULL,
  `creationDate` datetime NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `blogId_remoteId` (`blogId`,`remoteId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;