CREATE TABLE IF NOT EXISTS `agents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `agent` text NOT NULL,
  `browser` varchar(32) NOT NULL DEFAULT '',
  `os` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `domains` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` text NOT NULL,
  `domain` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `domain` (`domain`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `entries` (
  `remotehost` bigint(20) unsigned NOT NULL,
  `domain` bigint(20) unsigned NOT NULL,
  `date` datetime NOT NULL,
  `method` varchar(8) NOT NULL,
  `path` bigint(20) unsigned NOT NULL,
  `httpversion` varchar(4) NOT NULL,
  `retcode` smallint(5) unsigned NOT NULL,
  `size` bigint(20) unsigned NOT NULL,
  `referer` bigint(20) unsigned NOT NULL,
  `agent` bigint(20) unsigned NOT NULL,
  `file` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`remotehost`,`domain`,`date`),
  KEY `remotehost` (`remotehost`),
  KEY `domain` (`domain`),
  KEY `date` (`date`),
  KEY `path` (`path`),
  KEY `retcode` (`retcode`),
  KEY `referer` (`referer`),
  KEY `agent` (`agent`),
  KEY `file` (`file`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `files` (
  `filename` varchar(255) NOT NULL,
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `size` bigint(20) unsigned NOT NULL,
  `filetime` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `filename` (`filename`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `hosts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ip` varchar(16) NOT NULL,
  `host` varchar(255) NOT NULL DEFAULT '',
  `country` varchar(3) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `ip` (`ip`),
  KEY `host` (`host`),
  KEY `country` (`country`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `paths` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `path` varchar(765) NOT NULL DEFAULT '',
  `ext` varchar(16) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `path` (`path`),
  KEY `ext` (`ext`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `referers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `url` text NOT NULL,
  `query` varchar(128) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `query` (`query`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

