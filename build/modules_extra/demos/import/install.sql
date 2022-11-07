CREATE TABLE `demos` (
  `id` varchar(36) NOT NULL,
  `file` varchar(512) NOT NULL,
  `size` int NOT NULL,
  `map` varchar(128) NOT NULL,
  `server_id` int NOT NULL,
  `created_at` varchar(512) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `servers__demos` (
  `server_id` int NOT NULL,
  `work_method` int NOT NULL DEFAULT '1' COMMENT '1 - Auto recorder; 2 - [AutoDemo] Simple Web Uploader; 3 - Myarena HLTV; 4 - Csserv HLTV',
  `hltv_url` varchar(512) DEFAULT NULL,
  `swu_key` varchar(256) DEFAULT NULL,
  `ftp_host` varchar(64) DEFAULT NULL,
  `ftp_login` varchar(32) DEFAULT NULL,
  `ftp_pass` varchar(32) DEFAULT NULL,
  `ftp_port` int DEFAULT NULL,
  `ftp_string` varchar(255) DEFAULT NULL,
  `db_host` varchar(64) DEFAULT NULL,
  `db_user` varchar(32) DEFAULT NULL,
  `db_pass` varchar(32) DEFAULT NULL,
  `db_db` varchar(32) DEFAULT NULL,
  `db_table` varchar(32) DEFAULT NULL,
  `db_code` int NOT NULL DEFAULT '1',
  `url` varchar(512) DEFAULT NULL,
  `shelf_life` int NOT NULL DEFAULT '3',
  `last_demo` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `demos` ADD PRIMARY KEY (`id`);