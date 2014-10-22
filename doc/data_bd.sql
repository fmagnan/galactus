DELETE FROM settings;

INSERT INTO `settings` (`name`, `value`) VALUES
('title', 'Planet BD'),
('atom_link', 'http://planetbd.jeuxderoles.net/rss'),
('link', 'http://planetbd.jeuxderoles.net/'),
('description', 'L\'actualité de la Bande-Dessinée'),
('language', 'fr-fr'),
('generator', 'galactus: https://github.com/fmagnan/galactus');

DELETE FROM feeds;

INSERT INTO `feeds` (`name`, `url`, `isEnabled`) VALUES
('BD Gestion', 'http://www.bdgest.com/rss', 1),
('Cité BD', 'http://www.citebd.org/spip.php?page=backend', 1),
('Planète BD', 'http://feeds.feedburner.com/PlaneteBdLight', 1),
('Comic Book Resources', 'http://www.comicbookresources.com/feed.php?feed=all', 1),
('Sticky Pants', 'http://lycracacolle.canalblog.com/rss.xml', 1),
('Vidberg', 'http://feeds.feedburner.com/lemonde/vidberg', 1),
('Lapin', 'http://www.lapin.org/fluxrss.xml', 1),
('Réflexion de rat', 'http://reflexionderat.webcomics.fr/rss', 1),
('60 Giga', 'http://60-giga.webcomics.fr/rss', 1),
('Gally', 'http://gallybox.com/blog/feed/', 1),
('Paka', 'http://www.paka-blog.com/feed/', 1),
('Macadam Valley', 'http://macadamvalley.com/feed/', 1),
('Leaves', 'http://leaves.webcomics.fr/rss', 1),
('Mister Hyde', 'http://www.mister-hyde.com/feeds/posts/default', 1);

/*
('Glénat News', 'http://www.glenatbd.com/rss/rss2/news.rss', 0),
('Glénat Agenda', 'http://www.glenatbd.com/rss/rss2/agenda.rss', 0),
('Dargaud', 'http://www.dargaud.fr/rss/', 0),
('Casterman', 'http://bd.casterman.com/Agenda_RSS.cfm', 0),
('Centre Belge de la BD', 'http://www.cbbd.be/fr/flash-info/rss', 0),
('Du 9', 'http://www.du9.org/feed/', 0),
('Delcourt Infos', 'http://feeds.feedburner.com/Delcourt-Informations', 0),
('Delcourt Sorties', 'http://feeds.feedburner.com/Delcourt-SortiesBd', 0),
('Chroniques Comics', 'http://feeds.feedburner.com/PlaneteBdLight-ChroniquesComics', 0),
('Kana', 'http://www.kana.fr/rss/nouveautes.xml', 0),
('Lewis Trondheim', 'http://www.lewistrondheim.com/blog/rss/fil_rss.xml', 0),
*/