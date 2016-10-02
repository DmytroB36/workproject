CREATE TABLE `students` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `family_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `sex` tinyint(1) DEFAULT NULL,
  `age` tinyint(4) DEFAULT NULL,
  `group_name` varchar(3) COLLATE utf8_unicode_ci NOT NULL,
  `faculty` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `first_name` (`first_name`),
  KEY `group_name` (`group_name`),
  KEY `faculty` (`faculty`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
