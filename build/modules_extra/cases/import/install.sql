CREATE TABLE `cases` (
  `id` int(4) NOT NULL,
  `name` varchar(256) DEFAULT NULL,
  `price` float NOT NULL,
  `image` int(4) NOT NULL,
  `subjects` text NOT NULL,
  `trim` int(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `cases__images` (
  `id` int(4) NOT NULL,
  `url` varchar(512) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `cases__images` (`id`, `url`) VALUES
(1, 'modules_extra/cases/templates/_cases_images/1.png'),
(2, 'modules_extra/cases/templates/_cases_images/2.png'),
(3, 'modules_extra/cases/templates/_cases_images/3.png'),
(4, 'modules_extra/cases/templates/_cases_images/4.png'),
(5, 'modules_extra/cases/templates/_cases_images/5.png'),
(6, 'modules_extra/cases/templates/_cases_images/6.png'),
(7, 'modules_extra/cases/templates/_cases_images/7.png'),
(8, 'modules_extra/cases/templates/_cases_images/8.png'),
(9, 'modules_extra/cases/templates/_cases_images/9.png'),
(10, 'modules_extra/cases/templates/_cases_images/10.png'),
(11, 'modules_extra/cases/templates/_cases_images/11.png'),
(12, 'modules_extra/cases/templates/_cases_images/12.png'),
(13, 'modules_extra/cases/templates/_cases_images/13.png');

CREATE TABLE `cases__wins` (
  `id` int(6) NOT NULL,
  `case_id` int(4) NOT NULL,
  `item` text NOT NULL,
  `user_id` int(7) NOT NULL,
  `time` int(11) NOT NULL,
  `finished` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `cases`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `cases__images`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `cases__wins`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `cases`
  MODIFY `id` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `cases__images`
  MODIFY `id` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

ALTER TABLE `cases__wins`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;