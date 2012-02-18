create database english default charset utf8;

use english;

CREATE TABLE `english` (
  `id` int unsigned NOT NULL AUTO_INCREMENT ,
  `english` varchar(255),
  `chinese` varchar(255),
  `example` varchar(255),
  `created_at` date,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;