ALTER TABLE `servers` ADD `bk_host` VARCHAR(64) NOT NULL DEFAULT '0' AFTER `pass_prifix`, ADD `bk_user` VARCHAR(32) NOT NULL DEFAULT '0' AFTER `bk_host`, ADD `bk_pass` VARCHAR(32) NOT NULL DEFAULT '0' AFTER `bk_user`, ADD `bk_db` VARCHAR(32) NOT NULL DEFAULT '0' AFTER `bk_pass`;
ALTER TABLE `servers` ADD `bk_code` INT(1) NOT NULL DEFAULT '0' AFTER `bk_db`;

CREATE TABLE IF NOT EXISTS `bk_services` (
  `id` int(3) NOT NULL,
  `name` varchar(255) NOT NULL,
  `server` int(3) NOT NULL,
  `text` text NOT NULL,
  `trim` int(3) NOT NULL DEFAULT '0',
  `sale` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `bk_services`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `bk_services`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT;
  
CREATE TABLE IF NOT EXISTS `bk_services_times` (
  `id` int(4) NOT NULL,
  `service` int(3) NOT NULL,
  `price` FLOAT NOT NULL,
  `time` int(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `bk_services_times`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `bk_services_times`
  MODIFY `id` int(4) NOT NULL AUTO_INCREMENT;