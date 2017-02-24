CREATE TABLE `sessions` (
  `id` char(32) CHARSET 'ascii' NOT NULL,
  `timestamp` int unsigned NOT NULL,
  `data` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
