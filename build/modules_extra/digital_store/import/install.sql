CREATE TABLE `digital_store__categories` (
  `id` int(6) NOT NULL,
  `name` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `digital_store__keys` (
  `id` int(11) NOT NULL,
  `content` text NOT NULL,
  `product` int(11) NOT NULL,
  `pay` int(7) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `digital_store__products` (
  `id` int(7) NOT NULL,
  `name` varchar(256) NOT NULL,
  `image` varchar(512) NOT NULL,
  `category` int(6) NOT NULL,
  `price` float NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `digital_store__categories`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `digital_store__keys`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `digital_store__products`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `digital_store__categories`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT;

ALTER TABLE `digital_store__keys`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `digital_store__products`
  MODIFY `id` int(7) NOT NULL AUTO_INCREMENT;