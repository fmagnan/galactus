DELETE FROM planets;

INSERT INTO planets (`code`) VALUES ('php');
INSERT INTO planets (`code`) VALUES ('jdr');
INSERT INTO planets (`code`) VALUES ('bd');

DELETE FROM settings;

INSERT INTO `settings` (`name`, `value`) VALUES
('language', 'fr-fr'),
('generator', 'galactus: https://github.com/fmagnan/galactus');

INSERT INTO `settings` (`name`, `value`) VALUES
('php.title', 'Planet PHP FR'),
('php.atom_link', 'http://planet.myrtille.org/rss?code=jdr'),
('php.link', 'http://planet.myrtille.org/php'),
('php.description', 'Tous les blogs PHP francophones');

INSERT INTO `settings` (`name`, `value`) VALUES
('bd.title', 'Planet BD'),
('bd.atom_link', 'http://planet.myrtille.org/rss?code=jdr'),
('bd.link', 'http://planet.myrtille.org/bd'),
('bd.description', 'L\'actualité de la Bande-Dessinée');

INSERT INTO `settings` (`name`, `value`) VALUES
('jdr.title', 'Planet Jeux de Rôles'),
('jdr.atom_link', 'http://planet.myrtille.org/rss?code=jdr'),
('jdr.link', 'http://planet.myrtille.org/jdr'),
('jdr.description', 'L\'actualité du jeux de Rôles');

DELETE FROM feeds;

INSERT INTO `feeds` (`planetId`, `name`, `url`, `isEnabled`) VALUES
(1, 'Guillaume Bretou (PHPSymfony)', 'http://phpsymfony.com/index.php?feed/rss2', 1),
(1, 'Mathieu Robin', 'http://feeds.feedburner.com/Mathieurobincom', 1),
(1, 'Kévin Dunglas (Lapin Blanc)', 'http://dunglas.fr/category/programmation/php/feed/', 1),
(1, 'Hello Design', 'http://blog.hello-design.fr/index.php?feed/rss2', 1),
(1, 'Gilles Février', 'http://gfevrier.kelio.org/blog/feed/', 1),
(1, 'Pascal Martin (n+1).zéro', 'http://blog.pascal-martin.fr/feed/rss2', 1),
(1, 'Perrick Penet (onpk.net)', 'http://onpk.net/rss', 1),
(1, 'Franck Lefevre (Progi1984) - rootslabs', 'http://rootslabs.net/blog/feed', 1),
(1, 'Frédéric Hardy (mageekbox.net)', 'http://blog.mageekbox.net/?feed/rss2', 1),
(1, 'BlogDevPHP (Frantz Alexis)', 'http://www.blogdevphp.fr/feed/', 1),
(1, 'cd ~tigrou/pwet.fr/Blog', 'http://damien.pobel.fr/rss', 1),
(1, 'Mère Teresa\'s Blog', 'http://sarahhaim.wordpress.com/feed/', 1),
(1, 'Blount (programmation-web.net)', 'http://programmation-web.net/feed/', 1),
(1, 'WebRIVER', 'http://webriver.eu/rss.xml', 1),
(1, 'Poor Lonesome Coder', 'http://www.plcoder.net/rss.php?rss=Blog', 1),
(1, 'Pulsar Informatique', 'http://www.pulsar-informatique.com/actus-blog/latest?format=feed&type=rss', 1),
(1, 'Jean-François Lépine', 'http://blog.lepine.pro/atom.xml', 1),
(1, 'FoxMask\'z h0m3', 'http://www.foxmask.info/feed/', 1),
(1, 'BastNic\'s Blog', 'http://feeds.feedburner.com/bastnic', 1),
(1, 'De geek à directeur technique', 'http://www.geek-directeur-technique.com/feed', 1),
(1, 'Olivier Noël (alkannoide)', 'http://www.alkannoide.com/feed/', 1),
(1, 'Mehdi Kabab (piouPiouM)', 'http://feeds.pioupioum.fr/pioupioum-dev-blog', 1),
(1, 'Pierre Li Vigni (symblog.info)', 'http://symblog.info/feed/', 1),
(1, 'Anis Berejeb', 'http://www.berejeb.com/feed/', 1),
(1, 'Maxence Delannoy (Wiip)', 'http://wiip.fr/rss.xml', 1),
(1, 'Christophe Le Bot', 'http://blog.christophelebot.fr/feed/', 1),
(1, 'Ulrich (mon-code.net)', 'http://www.mon-code.net/feeds/rssGlobale', 1),
(1, 'Mikael Randy', 'http://www.mikaelrandy.fr/atom.xml', 1),
(1, 'Yohann Poiron', 'http://feeds2.feedburner.com/design-folio', 1),
(1, 'Roms Blog', 'http://www.romainbourdon.com/index.php/feed/', 1),
(1, 'Industrialisation PHP', 'http://feeds.feedburner.com/industrialisation-php', 1),
(1, 'Stéphane Bourzeix', 'http://www.bourzeix.com/weblog/feed/rss2', 1),
(1, 'Nexen.net', 'http://www.nexen.net/component/option,com_rss/Itemid,0/index2.php/feed,RSS2.0/no_html,1/', 1),
(1, 'Gerald\'s blog', 'http://www.croes.org/gerald/blog/feed/', 1),
(1, 'elePHPant', 'http://www.elephpant.com/rss.xml', 1),
(1, 'Jean-Marc Fontaine', 'http://flux.jmfontaine.net/jmfontaine-billets', 1),
(1, 'Formation CakePHP', 'http://feeds.feedburner.com/FormationCakephp', 1),
(1, 'Aurélien Gérits (Magix cjQuery)', 'http://magix-cjquery.com/feed/rss2', 1),
(1, 'Joris Berthelot (rand(0))', 'http://blog.eexit.net/rss/', 1),
(1, 'Stéphane Brun (sbnet)', 'http://www.sbnet.fr/feed/', 1),
(1, 'Florent Viel (luxifer)', 'https://blog.luxifer.fr/atom.xml', 1),
(1, 'Arnaud de Abreu (YrWeb)', 'http://yrweb.fr/feed', 1),
(1, 'Nicolas Thouvenin (touv.fr)', 'http://blog.touv.fr/rss.xml', 1),
(1, 'Questions des stagiaires', 'http://formatrice.wordpress.com/feed/', 1),
(1, 'Webozor', 'http://www.webozor.com/feed', 1),
(1, 'PHPSources', 'http://www.phpsources.org/articles-rss.xml', 1),
(1, 'j0k3r', 'http://feeds2.feedburner.com/J0k3rn3t', 1),
(1, 'Julien Pauli', 'http://blog.developpez.com/julienpauli/feed', 1),
(1, 'Etienne Voilliot', 'http://lapetitepausetechnique.net/feed/', 1),
(1, 'Brice Favre (peLmeL)', 'http://pelmel.org/dotclear.php/feed/atom', 1),
(1, 'Christophe de Saint Léger', 'http://lindev.fr/index.php?feed/rss2', 1),
(1, 'Yves Tannier (L\'appartement)', 'http://www.grafactory.net/blog/feed/rss2', 1),
(1, 'Fabien Pennequin (blogafab)', 'http://www.blogafab.com/feed/', 1),
(1, 'Sutekidane', 'http://feeds2.feedburner.com/sutekidane', 1),
(1, 'Loogaroo', 'http://feeds.feedburner.com/Loogaroo', 1),
(1, 'Gauthier Delamarre (Free Blog Ware)', 'http://freeblogware.org/?feed=rss2', 1),
(1, 'Benjamin Longearet (geekos)', 'http://feeds.feedburner.com/geekos/fr', 1),
(1, 'Samuel Martin (creaone)', 'http://blog.creaone.fr/feed/rss2', 1),
(1, 'jy[B]log', 'http://ljouanneau.com/blog/feed/atom', 1),
(1, 'Blog Technique Elao', 'http://www.elao.com/blog/feed', 1),
(1, 'Michael Bertocchi (duPot.org)', 'http://dupot.org/newsrss.rss', 1),
(1, 'Thibault Jouannic (miximum.fr)', 'http://www.miximum.fr/feeds/rss.xml', 1),
(1, 'Cyruss blog', 'http://www.cyruss.com/index.php?feed/atom', 1),
(1, 'Nicolas Hachet', 'http://blog.nicolashachet.com/feed/', 1),
(1, 'Alexis Metaireau (notmyidea.org)', 'http://blog.notmyidea.org/feeds/all.atom.xml', 1),
(1, 'Matthieu Huguet (BigOrNot?)', 'http://bigornot-fr.blogspot.com/feeds/posts/default?alt=rss', 1),
(1, 'Raphael Rougeron', 'http://blog.raphael-rougeron.com/feed/atom', 1),
(1, 'Chez Xavier', 'http://lacot.org/syndication.atom', 1),
(1, 'AFUP', 'http://www.afup.org/pages/site/rss.php', 1),
(1, 'ApéroPHP', 'http://aperophp.net/list.atom', 1),
(1, 'Julien Breux', 'http://www.julien-breux.com/feed', 1);

/* semblent morts
 ('Cherry on the ...', 'http://cherryonthe.popnews.com/rss', 1),
 ('Charles Rincheval (DigitalSpirit)', 'http://www.digitalspirit.org/blog/index.php/feed/rss2', 1),
 ('Martin Supiot (webaaz)', 'http://feeds2.feedburner.com/webaaz', 1),
 ('XHTML.net - Loïc', 'http://www.xhtml.net/', 1),
 ('Glagla Dot Org', 'http://www.glagla.org/weblog/', 1),
 ('Kamelot Blog', 'http://le.brol.de.moosh.be/blog/', 1),
 ('Arnaud "Narno" Ligny', 'http://www.narno.com/', 1),
 ('zenProg', 'http://zenprog.com/', 1),
 ('Olivier Hoareau (phppro.fr)', 'http://blog.phppro.fr/', 1),
 ('Hyla Project', 'http://blog.hyla-project.org/', 1),
 ('Pascal Cescato (expert-php)', 'http://www.expert-php.fr/', 1),
 ('Strat&geek', 'http://blog.strategeek.fr', 1),
 ('Alheim', 'http://alheim.fr/', 1),
 ('ZeFredz\'s Blog', 'http://zefredz.frimouvy.org/dotclear/', 1),
 ('Metagoto\'s blog', 'http://blog.runpac.com/', 1),
 ('Coding Style', 'http://www.codingstyle.fr/', 1),
 ('Nicolas Joseph (gege2061)', 'http://gege2061.homecomputing.fr/', 1),
 ('blognote-info.com', 'http://www.blognote-info.com/', 1),
 ('fchfch sur phptothemoon', 'http://phptothemoon.com/fr/blogs/', 1),
 ('Experimental Symfony', 'http://www.experimental-symfony.com/', 1),
 ('Arnaud Limbourg', 'http://www.limbourg.com/arnaud/blog/', 1),
 ('Coolforest', 'http://blog.coolforest.net/', 1),
*/

INSERT INTO `feeds` (`planetId`, `name`, `url`, `isEnabled`) VALUES
(2, 'Mémoire secondaire',	'http://www.memoiresecondaire.fr/?feed=rss2',	1),
(2, 'Black Book Editions',	'http://www.black-book-editions.fr/rss.xml',	1),
(2, 'Outsider',	'http://outsider.rolepod.net/feed/',	1),
(2, 'La Cellule',	'http://feeds.feedburner.com/cellulis',	1),
(2, 'Radio Rôliste',	'http://feeds.feedburner.com/feed-radio-roliste?format=xml',	1),
(2, 'Tartofrez',	'http://www.tartofrez.com/?feed=rss2',	1),
(2, '7emecercle.com',	'http://www.7emecercle.com/website/?feed=rss2',	1),
(2, 'Guide du Rôliste Galactique',	'http://www.legrog.org/informations/syndication/accueil',	1),
(2, 'Ludopathes Editeurs',	'http://www.ludopathes.com/blog/wordpress/wordpress/feed',1),
(2, 'Editions Sans-Détour',	'http://sans-detour.com/index.php/rss.html',	1),
(2, 'Edge Entertainment',	'http://www.edgeent.com/home/feed',	1),
(2, 'Places to Go, People to Be',	'http://feeds.feedburner.com/PTGPTBvf',	1),
(2, 'Mr Frankenstein',	'http://www.misterfrankenstein.com/wordpress/?feed=rss2',	1),
(2, 'Jeepee Online',	'http://www.jeepeeonline.be/feeds/posts/default?alt=rss',	1),
(2, 'Fumble Zone',	'http://fumblezone.net/index.php?feed/atom',	1);

INSERT INTO `feeds` (`planetId`, `name`, `url`, `isEnabled`) VALUES
(3, 'BD Gestion', 'http://www.bdgest.com/rss', 1),
(3, 'Cité BD', 'http://www.citebd.org/spip.php?page=backend', 1),
(3, 'Planète BD', 'http://feeds.feedburner.com/PlaneteBdLight', 1),
(3, 'Comic Book Resources', 'http://www.comicbookresources.com/feed.php?feed=all', 1),
(3, 'Sticky Pants', 'http://lycracacolle.canalblog.com/rss.xml', 1),
(3, 'Vidberg', 'http://feeds.feedburner.com/lemonde/vidberg', 1),
(3, 'Lapin', 'http://www.lapin.org/fluxrss.xml', 1),
(3, 'Réflexion de rat', 'http://reflexionderat.webcomics.fr/rss', 1),
(3, '60 Giga', 'http://60-giga.webcomics.fr/rss', 1),
(3, 'Gally', 'http://gallybox.com/blog/feed/', 1),
(3, 'Paka', 'http://www.paka-blog.com/feed/', 1),
(3, 'Macadam Valley', 'http://macadamvalley.com/feed/', 1),
(3, 'Leaves', 'http://leaves.webcomics.fr/rss', 1),
(3, 'Tu mourras moins bête', 'http://tumourrasmoinsbete.blogspot.fr/feeds/posts/default', 1),
(3, 'Boulet', 'http://www.bouletcorp.com/feed/', 1),
(3, 'Mister Hyde', 'http://www.mister-hyde.com/feeds/posts/default', 1);

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