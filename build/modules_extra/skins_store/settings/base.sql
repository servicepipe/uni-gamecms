CREATE TABLE `skins__purchases` (
  `id` int(9) NOT NULL,
  `user_id` int(9) NOT NULL,
  `skin_id` int(9) NOT NULL,
  `server_id` int(9) NOT NULL,
  `model_name_t` varchar(255) NOT NULL,
  `model_name_ct` varchar(255) NOT NULL,
  `price` int(9) NOT NULL,
  `nickname` varchar(33) NOT NULL,
  `password` varchar(256) NOT NULL,
  `timeleft` int(15) NOT NULL,
  `enable` int(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Структура таблицы `skins__store`
--

CREATE TABLE `skins__store` (
  `id` int(9) NOT NULL,
  `server_id` int(9) NOT NULL,
  `name` varchar(33) NOT NULL,
  `price` int(9) NOT NULL,
  `model_name_t` varchar(33) NOT NULL,
  `model_name_ct` varchar(33) NOT NULL,
  `image` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


INSERT INTO `modules` (`name`, `tpls`, `active`, `info`, `files`, `client_key`) VALUES
('skins_store', 'none', 2, 'Данный модуль позволит Вам добавить дополнительный магазин для покупки игровых скинов на Ваших серверах.<br>\r\nМодуль позволяет создать скины под каждый сервер индивидуально.\r\n<hr>\r\n<a class=\"btn btn-default btn-sm f-l mr-5\" href=\"/admin/skins_store\" target=\"_blank\">Настройки модуля</a><a class=\"btn btn-default btn-sm f-l mr-5\" href=\"/store/skins\" target=\"_blank\">Страница магазина</a>\r\n<div class=\"clearfix\"></div>', '', 'unigamcms.ru');

INSERT INTO `pages` (`file`, `url`, `name`, `title`, `description`, `keywords`, `kind`, `image`, `robots`, `privacy`, `type`, `active`, `page`, `class`) VALUES
('modules_extra/skins_store/base/index.php', 'store/skins', 'skins_store', 'Магазин игровых скинов', 'Магазин игровых скинов', 'Магазин игровых скинов', 1, 'files/miniatures/standart.jpg', 1, 1, 1, 1, 0, 0),
('modules_extra/skins_store/base/admin/index.php', 'admin/skins_store', 'admin_skins_store', 'Настройки', 'none', 'none', 1, 'files/miniatures/standart.jpg', 0, 0, 2, 1, 0, 0);


--
-- Индексы таблицы `skins__purchases`
--
ALTER TABLE `skins__purchases`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `skins__store`
--
ALTER TABLE `skins__store`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для таблицы `skins__purchases`
--
ALTER TABLE `skins__purchases`
  MODIFY `id` int(9) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `skins__store`
--
ALTER TABLE `skins__store`
  MODIFY `id` int(9) NOT NULL AUTO_INCREMENT;
COMMIT;
