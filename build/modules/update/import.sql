DROP TABLE `config__strings`;

CREATE TABLE IF NOT EXISTS `config__strings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `data` text NOT NULL,
  `comment` varchar(128) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

INSERT INTO `config__strings` (`id`, `data`, `comment`) VALUES
(1, '{%}&#039;{%};sp;{%};{%};sp;{%}&quot;{%};sp;{%}ツ{%};sp;{%}`{%};sp;', 'bad nicks'),
(2, 'a:8:{s:12:\"file_manager\";s:1:\"1\";s:18:\"file_manager_theme\";s:1:\"2\";s:13:\"file_max_size\";s:2:\"10\";s:7:\"ext_img\";s:20:\"jpg jpeg png gif bmp\";s:9:\"ext_music\";s:7:\"mp3 wav\";s:8:\"ext_misc\";s:7:\"zip rar\";s:8:\"ext_file\";s:7:\"txt log\";s:9:\"ext_video\";s:3:\"avi\";}', 'filemanager'),
(3, 'a:3:{i:0;a:4:{s:5:\"start\";d:200;s:3:\"end\";d:499;s:4:\"type\";s:1:\"1\";s:5:\"value\";d:20;}i:1;a:4:{s:5:\"start\";d:500;s:3:\"end\";d:999;s:4:\"type\";s:1:\"1\";s:5:\"value\";d:30;}i:2;a:4:{s:5:\"start\";d:1000;s:3:\"end\";d:1500;s:4:\"type\";s:1:\"1\";s:5:\"value\";d:50;}}', 'bonuses'),
(4, 'a:13:{s:4:\"date\";s:10:\"2022-09-27\";s:5:\"users\";a:2:{i:0;s:5:\"16017\";i:1;s:5:\"16010\";}s:4:\"news\";a:2:{i:0;s:1:\"7\";i:1;s:1:\"7\";}s:14:\"news__comments\";a:2:{i:0;s:2:\"10\";i:1;s:2:\"10\";}s:14:\"forums__topics\";a:2:{i:0;s:3:\"854\";i:1;s:3:\"854\";}s:16:\"forums__messages\";a:2:{i:0;s:4:\"9505\";i:1;s:4:\"9502\";}s:12:\"pm__messages\";a:2:{i:0;s:5:\"51273\";i:1;s:5:\"51258\";}s:4:\"chat\";a:2:{i:0;s:3:\"269\";i:1;s:3:\"264\";}s:9:\"bans_apps\";a:2:{i:0;s:3:\"479\";i:1;s:3:\"478\";}s:6:\"admins\";a:2:{i:0;s:3:\"243\";i:1;s:3:\"249\";}s:7:\"servers\";a:2:{i:0;s:2:\"11\";i:1;s:2:\"11\";}s:12:\"servers_bans\";a:2:{i:0;i:26830;i:1;i:26790;}s:13:\"servers_stats\";a:2:{i:0;i:41071;i:1;i:40957;}}', 'site_stats'),
(5, 'java;sp;ajax;sp;script;sp;document;sp;function;sp;', 'forbidden words');

ALTER TABLE `users` ADD `country` VARCHAR(128) NULL DEFAULT NULL AFTER `name`, ADD `city` VARCHAR(128) NULL DEFAULT NULL AFTER `country`;

UPDATE `config__secondary` SET `version`='5.7.9' WHERE 1;