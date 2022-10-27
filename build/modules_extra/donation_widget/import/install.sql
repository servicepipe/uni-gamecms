CREATE TABLE IF NOT EXISTS `dw__config` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `enabled` int(1) NOT NULL DEFAULT 2,
  `raising` int(11) DEFAULT NULL COMMENT 'Current raising ID',
  `showlist` int(1) NOT NULL DEFAULT 2,
  `listlimit` int(2) NOT NULL DEFAULT 0,
  `comments` int(1) NOT NULL DEFAULT 2,
  `autostop` int(1) NOT NULL DEFAULT 2,
  PRIMARY KEY (`id`),
  KEY `raising` (`raising`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `dw__donations` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Donation ID',
  `pid` int(11) NOT NULL COMMENT 'Payment ID',
  `fid` int(11) NOT NULL COMMENT 'Fundrising ID',
  `comment` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`),
  KEY `fid` (`fid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `dw__raisings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message` varchar(250) NOT NULL DEFAULT '' COMMENT 'Target description',
  `target` int(11) NOT NULL DEFAULT 0 COMMENT 'Target amount',
  `stopdate` varchar(20) NOT NULL DEFAULT '0000.00.00 00:00' COMMENT 'End date of fundraising',
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- ALTER TABLE `dw__donations`
--   ADD CONSTRAINT `dw__donations_ibfk_1` FOREIGN KEY (`pid`) REFERENCES `money__actions` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
--   ADD CONSTRAINT `dw__donations_ibfk_2` FOREIGN KEY (`fid`) REFERENCES `dw__raisings` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
-- COMMIT;

INSERT INTO `dw__config` (`id`) VALUES (NULL);
INSERT INTO `money__actions_types` (`id`, `name`, `class`) VALUES ('20', 'Пожертвование проекту', 'danger');