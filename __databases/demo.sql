DROP TABLE IF EXISTS `demo_file`;
CREATE TABLE `demo_file` (
  `id` mediumint(9) unsigned NOT NULL AUTO_INCREMENT,
  `id_user` varchar(255) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `fileURL` varchar(255) NOT NULL DEFAULT '',
  `created` timestamp DEFAULT CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `demo_log`;
CREATE TABLE `demo_log` (
  `id` mediumint(9) unsigned NOT NULL AUTO_INCREMENT,
  `id_user` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `page` varchar(255) NOT NULL DEFAULT '',
  `querystring` varchar(255) NOT NULL DEFAULT '',
  `method` varchar(255) NOT NULL DEFAULT '',
  `parameters` varchar(255) NOT NULL DEFAULT '',
  `ipaddress` varchar(255) NOT NULL DEFAULT '',
  `browser` varchar(255) NOT NULL DEFAULT '',
  `created` timestamp DEFAULT CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `ipaddress` (`ipaddress`),
  KEY `browser` (`browser`)
) ENGINE=MyISAM AUTO_INCREMENT=937 DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `demo_reset`;
CREATE TABLE `demo_reset` (
  `id` mediumint(9) unsigned NOT NULL AUTO_INCREMENT,
  `token` varchar(255) NOT NULL DEFAULT '',
  `id_user` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) DEFAULT NULL,
  `expires` datetime DEFAULT NULL,
  `ind_used` int(1) DEFAULT '0',
  `created` timestamp DEFAULT CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `email` (`email`),
  KEY `expires` (`expires`),
  KEY `ind_used` (`ind_used`)
) ENGINE=MyISAM AUTO_INCREMENT=61 DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `demo_user`;
CREATE TABLE `demo_user` (
  `id` mediumint(9) unsigned NOT NULL AUTO_INCREMENT,
  `first` varchar(50) NOT NULL DEFAULT '',
  `last` varchar(50) NOT NULL DEFAULT '',
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `password_plaintext` varchar(255) DEFAULT NULL,
  `fileURL` varchar(255) DEFAULT NULL,
  `created` timestamp DEFAULT CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `email` (`email`),
  KEY `password` (`password`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;
