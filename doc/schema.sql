-- Adminer 4.1.0 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';

DROP TABLE IF EXISTS `posts`;
DROP TABLE IF EXISTS `feeds`;
DROP TABLE IF EXISTS `planets`;
DROP TABLE IF EXISTS `settings`;

CREATE TABLE `planets` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `feeds` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `planetId` bigint(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `url` varchar(255) NOT NULL,
  `isEnabled` tinyint(3) NOT NULL DEFAULT '1',
  `title` varchar(100) NOT NULL,
  `lang` varchar(10) NOT NULL,
  `lastUpdate` datetime NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `feeds_ibfk_1` FOREIGN KEY (`planetId`) REFERENCES `planets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `posts` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `feedId` bigint(20) NOT NULL,
  `remoteId` varchar(32) NOT NULL,
  `title` text NOT NULL,
  `url` text NOT NULL,
  `pubDate` datetime NOT NULL,
  `content` mediumtext NOT NULL,
  `author` varchar(50) NOT NULL,
  `category` varchar(50) NOT NULL,
  `comments` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `feedId_remoteId` (`feedId`,`remoteId`),
  KEY `pubDate` (`pubDate`),
  CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`feedId`) REFERENCES `feeds` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `settings` (
  `name` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;