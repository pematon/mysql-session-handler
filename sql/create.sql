DROP TABLE IF EXISTS `sessions`;

CREATE TABLE `sessions` (
  `id` binary(16) NOT NULL,
  `timestamp` int unsigned NOT NULL,
  `data` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
