INSERT INTO `feeds` (`name`, `url`, `isEnabled`) VALUES
('Mémoire secondaire',	'http://www.memoiresecondaire.fr/?feed=rss2',	1),
('Black Book Editions',	'http://www.black-book-editions.fr/rss.xml',	1),
('Outsider',	'http://outsider.rolepod.net/feed/',	1),
('La Cellule',	'http://feeds.feedburner.com/cellulis',	1),
('Radio Rôliste',	'http://feeds.feedburner.com/feed-radio-roliste?format=xml',	1),
('Tartofrez',	'http://www.tartofrez.com/?feed=rss2',	1),
('7emecercle.com',	'http://www.7emecercle.com/website/?feed=rss2',	1),
('Guide du Rôliste Galactique',	'http://www.legrog.org/informations/syndication/accueil',	1),
('Ludopathes Editeurs',	'http://www.ludopathes.com/blog/wordpress/wordpress/feed',1),
('Editions Sans-Détour',	'http://sans-detour.com/index.php/rss.html',	1),
('Edge Entertainment',	'http://www.edgeent.com/home/feed',	1),
('Places to Go, People to Be',	'http://feeds.feedburner.com/PTGPTBvf',	1),
('Mr Frankenstein',	'http://www.misterfrankenstein.com/wordpress/?feed=rss2',	1),
('Jeepee Online',	'http://www.jeepeeonline.be/feeds/posts/default?alt=rss',	1),
('Fumble Zone',	'http://fumblezone.net/index.php?feed/atom',	1);

INSERT INTO `tags` (`id`, `name`) VALUES
(1,	'jdr'),
(2,	'podcast');

INSERT INTO `feed_x_tag` (`feedId`, `tagId`) VALUES
(1,	1),
(2,	1),
(3,	1),
(5,	1),
(5,	2);