INSERT INTO `feeds` (`name`, `url`, `type`, `isEnabled`) VALUES
('Mémoire secondaire',	'http://www.memoiresecondaire.fr/?feed=rss2',	2,	1),
('Black Book Editions',	'http://www.black-book-editions.fr/rss.xml',	2,	1),
('Outsider',	'http://outsider.rolepod.net/feed/',	2,	1),
('La Cellule',	'http://feeds.feedburner.com/cellulis',	2,	1),
('Radio Rôliste',	'http://feeds.feedburner.com/feed-radio-roliste?format=xml',	2,	1),
('Tartofrez',	'http://www.tartofrez.com/?feed=rss2',	2,	1),
('7emecercle.com',	'http://www.7emecercle.com/website/?feed=rss2',	2,	1),
('Guide du Rôliste Galactique',	'http://www.legrog.org/informations/syndication/accueil',	2,	1),
('Ludopathes Editeurs',	'http://www.ludopathes.com/blog/wordpress/wordpress/feed',	2,	1),
('Editions Sans-Détour',	'http://sans-detour.com/index.php/rss.html',	2,	1),
('Edge Entertainment',	'http://www.edgeent.com/home/feed',	2,	1),
('Places to Go, People to Be',	'http://feeds.feedburner.com/PTGPTBvf',	2,	1),
('Mr Frankenstein',	'http://www.misterfrankenstein.com/wordpress/?feed=rss2',	2,	1),
('Jeepee Online',	'http://www.jeepeeonline.be/feeds/posts/default?alt=rss',	2,	1),
('Tentacules',	'http://www.tentacules.net/toc/_code/toc.rss.xml',	2,	0),
('Fumble Zone',	'http://fumblezone.net/index.php?feed/atom',	0,	1);

INSERT INTO `tags` (`id`, `name`) VALUES
(1,	'jdr'),
(2,	'podcast');

INSERT INTO `feed_x_tag` (`feedId`, `tagId`) VALUES
(1,	1),
(2,	1),
(3,	1),
(5,	1),
(5,	2);