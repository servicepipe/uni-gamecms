DROP TABLE `config__updates`;
CREATE TABLE IF NOT EXISTS `config__updates` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `url` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
INSERT INTO `config__updates` (`id`, `name`, `url`) VALUES
(1, 'Основной сервер', 'api.unigamecms.ru'),
(2, 'Резервный сервер', 'api.gamehost.pm'),
(3, 'Прокси сервер', 'api-proxy.unigamecms.ru');
UPDATE `config__secondary` SET `version`='5.7.8' WHERE 1;