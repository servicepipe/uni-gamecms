CREATE TABLE `vacancy` (
  `id` int NOT NULL,
  `uid` int NOT NULL,
  `sid` int NOT NULL,
  `vacancy` int NOT NULL,
  `info` text NOT NULL,
  `status` int NOT NULL DEFAULT '2',
  `reason` varchar(256) NOT NULL DEFAULT 'none',
  `date` varchar(64) NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `vacancy__configs` (
  `id` int NOT NULL,
  `next_days` int NOT NULL DEFAULT '31',
  `limit_vacancy` int NOT NULL DEFAULT '12'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `vacancy__configs` (`id`, `next_days`, `limit_vacancy`) VALUES
(1, 7, 12);

CREATE TABLE `vacancy__list` (
  `id` int NOT NULL,
  `sid` int NOT NULL,
  `name` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `vacancy__messages` (
  `id` int NOT NULL,
  `uid` int NOT NULL,
  `vid` int NOT NULL,
  `message` text NOT NULL,
  `date` varchar(64) NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `vacancy__names` (
  `id` int NOT NULL,
  `sid` int NOT NULL,
  `title` varchar(64) NOT NULL,
  `name` varchar(32) NOT NULL,
  `placeholder` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `vacancy`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `vacancy__configs`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `vacancy__list`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `vacancy__messages`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `vacancy__names`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `vacancy`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `vacancy__configs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `vacancy__list`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `vacancy__messages`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `vacancy__names`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;