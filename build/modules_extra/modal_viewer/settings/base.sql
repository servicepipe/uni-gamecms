CREATE TABLE IF NOT EXISTS `modal_viewer` (
	`id` int(3) NOT NULL,
	`title` varchar(255) NOT NULL,
	`text` text NOT NULL,
	`timelife` int(9) NOT NULL,
	`auth` int(3) NOT NULL DEFAULT '0',
	`enable` int(3) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `modal_viewer`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `modal_viewer`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT;