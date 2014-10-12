-- Adminer 4.1.0 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';

DROP TABLE IF EXISTS `feed_x_tag`;
DROP TABLE IF EXISTS `posts`;
DROP TABLE IF EXISTS `tags`;
DROP TABLE IF EXISTS `feeds`;

CREATE TABLE `feeds` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `url` varchar(255) NOT NULL,
  `isEnabled` tinyint(3) NOT NULL DEFAULT '1',
  `feedUri` varchar(255) NOT NULL,
  `lang` varchar(10) NOT NULL,
  `lastUpdate` datetime NOT NULL,
  `copyright` varchar (50) NOT NULL,
  `docs` varchar(255) NOT NULL,
  `generator` varchar (50) NOT NULL,

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `posts` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `feedId` bigint(20) NOT NULL,
  `remoteId` varchar(32) NOT NULL,
  `title` varchar(100) NOT NULL,
  `url` varchar(255) NOT NULL,
  `creationDate` datetime NOT NULL,
  `content` text NOT NULL,
  `author` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `feedId_remoteId` (`feedId`,`remoteId`),
  KEY `creationDate` (`creationDate`),
  CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`feedId`) REFERENCES `feeds` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `feed_x_tag` (
  `feedId` bigint(20) NOT NULL,
  `tagId` int(11) NOT NULL,
  PRIMARY KEY (`feedId`,`tagId`),
  KEY `tagId` (`tagId`),
  CONSTRAINT `feed_x_tag_ibfk_4` FOREIGN KEY (`tagId`) REFERENCES `tags` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `feed_x_tag_ibfk_3` FOREIGN KEY (`feedId`) REFERENCES `feeds` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;