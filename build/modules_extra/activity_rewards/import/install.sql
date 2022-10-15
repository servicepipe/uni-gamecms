CREATE TABLE IF NOT EXISTS `activity_rewards` (
	`id` int(6) NOT NULL,
	`days_in_a_row` int(3) NOT NULL,
	`reward` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `activity_rewards`
	ADD PRIMARY KEY (`id`);

ALTER TABLE `activity_rewards`
	MODIFY `id` int(6) NOT NULL AUTO_INCREMENT;

CREATE TABLE IF NOT EXISTS `activity_rewards__participants` (
	`id` int(6) NOT NULL,
	`user_id` int(6) NOT NULL,
	`days_in_a_row` int(3) NOT NULL,
	`last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `activity_rewards__participants`
	ADD PRIMARY KEY (`id`);

ALTER TABLE `activity_rewards__participants`
	MODIFY `id` int(6) NOT NULL AUTO_INCREMENT;

ALTER TABLE `activity_rewards__participants` ADD `days_in_a_row_max` INT(3) NOT NULL DEFAULT '1' AFTER `days_in_a_row`;

CREATE TABLE IF NOT EXISTS `activity_rewards__config` (
	`id` int(11) NOT NULL,
	`slug` varchar(256) NOT NULL,
	`value` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `activity_rewards__config` (`id`, `slug`, `value`) VALUES
(1, 'is_re_issue', '1'),
(2, 'is_need_money_activity', '0'),
(3, 'amount_of_money', '10');

ALTER TABLE `activity_rewards__config`
	ADD PRIMARY KEY (`id`);

ALTER TABLE `activity_rewards__config`
	MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;