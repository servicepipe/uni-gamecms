CREATE TABLE `sortition` (
  `name` varchar(255) DEFAULT NULL,
  `ending` int(11) DEFAULT NULL,
  `price` float DEFAULT NULL,
  `participants` int(11) DEFAULT NULL,
  `how_old` INT(3) NULL DEFAULT '0',
  `prize` text,
  `own_prize` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `sortition__participants` (
  `id` int(11) NOT NULL,
  `user_id` int(7) DEFAULT NULL,
  `contribution` float DEFAULT NULL,
  `winner` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `sortition__participants`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `sortition__participants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
  
ALTER TABLE `sortition` ADD `show_participants` INT(1) NULL DEFAULT '2' AFTER `own_prize`;
ALTER TABLE `sortition` ADD `end_type` INT(1) NULL DEFAULT '1' AFTER `show_participants`;
ALTER TABLE `sortition` ADD `finished` INT(1) NOT NULL DEFAULT '2' AFTER `end_type`;