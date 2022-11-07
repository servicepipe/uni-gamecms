ALTER TABLE `servers` ADD `sk_host` VARCHAR(64) NOT NULL DEFAULT '0' AFTER `pass_prifix`, ADD `sk_user` VARCHAR(32) NOT NULL DEFAULT '0' AFTER `sk_host`, ADD `sk_pass` VARCHAR(32) NOT NULL DEFAULT '0' AFTER `sk_user`, ADD `sk_db` VARCHAR(32) NOT NULL DEFAULT '0' AFTER `sk_pass`;
ALTER TABLE `servers` ADD `sk_code` INT(1) NOT NULL DEFAULT '0' AFTER `sk_db`;
  
CREATE TABLE IF NOT EXISTS `sk_services` (
  `id` int(4) NOT NULL,
  `server` int(3) NOT NULL,
  `price` FLOAT NOT NULL,
  `number` int(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `sk_services`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `sk_services`
  MODIFY `id` int(4) NOT NULL AUTO_INCREMENT;
  
ALTER TABLE `sk_services` ADD `type` INT(1) NOT NULL DEFAULT '1' AFTER `number`;
ALTER TABLE `sk_services` CHANGE `number` `number` VARCHAR(50) NOT NULL;