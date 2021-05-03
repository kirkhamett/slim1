DROP TABLE IF EXISTS `facets`;
CREATE TABLE `facets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_facet_parent` (`parent_id`),
  CONSTRAINT `fk_facet_parent` FOREIGN KEY (`parent_id`) REFERENCES `facets` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO facets Values(1, 'Dames', null);
INSERT INTO facets values(2, 'Accessories', 1);
INSERT INTO facets values(3, 'Handschoenen', 2);
INSERT INTO facets values(4, 'Mutsen en Hoeden', 2);
INSERT INTO facets values(5, 'Portemonnees', 2);
INSERT INTO facets values(6, 'Riemen', 2);
INSERT INTO facets values(7, 'Sieraden & Horloges', 2);
INSERT INTO facets values(8, 'Armbanden', 7);
INSERT INTO facets values(9, 'Broches & pins', 7);
INSERT INTO facets values(10, 'Horlogen', 7);
INSERT INTO facets values(11, 'Kettingen', 7);
INSERT INTO facets values(12, 'Oorbellen', 7);
INSERT INTO facets values(13, 'Ringen', 7);
INSERT INTO facets values(14, 'Sjaals', 2);
INSERT INTO facets values(15, 'Tassen', 2);
INSERT INTO facets values(16, 'Clutches', 15);
INSERT INTO facets values(17, 'Handtassen', 15);
INSERT INTO facets values(18, 'Rugzakken', 15);



