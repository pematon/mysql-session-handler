DROP TABLE IF EXISTS `sessions`;

CREATE TABLE `sessions` (
  `id` varchar(64) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `data` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
