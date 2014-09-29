INSERT INTO `feeds` (`id`, `name`, `url`, `type`, `isEnabled`) VALUES
(1,	'Mémoire secondaire',	'http://www.memoiresecondaire.fr/?feed=rss2',	2,	1),
(2,	'Black Book Editions',	'http://www.black-book-editions.fr/rss.xml',	2,	1),
(3,	'Outsider',	'http://outsider.rolepod.net/feed/',	2,	1),
(4,	'La Cellule',	'http://feeds.feedburner.com/cellulis',	2,	1),
(5,	'Radio Rôliste',	'http://feeds.feedburner.com/feed-radio-roliste?format=xml',	2,	1),
(6,	'Tartofrez',	'http://www.tartofrez.com/?feed=rss2',	2,	1),
(7,	'7emecercle.com',	'http://www.7emecercle.com/website/?feed=rss2',	2,	1),
(8,	'Guide du Rôliste Galactique',	'http://www.legrog.org/informations/syndication/accueil',	2,	1),
(9,	'Ludopathes Editeurs',	'http://www.ludopathes.com/blog/wordpress/wordpress/feed',	2,	1),
(10,	'Editions Sans-Détour',	'http://sans-detour.com/index.php/rss.html',	2,	1),
(11,	'Edge Entertainment',	'http://www.edgeent.com/home/feed',	2,	1),
(12,	'Places to Go, People to Be',	'http://feeds.feedburner.com/PTGPTBvf',	2,	1),
(13,	'Mr Frankenstein',	'http://www.misterfrankenstein.com/wordpress/?feed=rss2',	2,	1),
(14,	'Jeepee Online',	'http://www.jeepeeonline.be/feeds/posts/default?alt=rss',	2,	1),
(15,	'Tentacules',	'http://www.tentacules.net/toc/_code/toc.rss.xml',	2,	0),
(16,	'Fumble Zone',	'http://fumblezone.net/index.php?feed/atom',	0,	1);

INSERT INTO `tags` (`id`, `name`) VALUES
(1,	'jdr'),
(2,	'podcast');

INSERT INTO `feed_x_tag` (`feedId`, `tagId`) VALUES
(1,	1),
(2,	1),
(3,	1),
(5,	1),
(5,	2);