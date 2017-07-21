CREATE TABLE `sessions` (
  `id` char(32) CHARSET 'ascii' NOT NULL,
  `timestamp` int unsigned NOT NULL,
  `data` longtext CHARSET 'utf8mb4' NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;
