<?php
if(empty($prize) && isset($pdo) && is_auth()) {
	$STH = $pdo->prepare("SELECT `cases__wins`.*, `cases`.`name`,`users`.`login`,`users`.`rights` FROM `cases__wins` 
			LEFT JOIN `cases` ON `cases__wins`.`case_id` = `cases`.`id` 
			LEFT JOIN `users` ON `cases__wins`.`user_id` = `users`.`id` 
			WHERE (:time - `cases__wins`.`time`) > 20 AND `cases__wins`.`finished` = '0' AND `user_id`=:user_id LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
	$STH->execute(array( ':time' => time(), ':user_id' => $_SESSION['id'] ));
	$prize = $STH->fetch();
}

if(isset($prize->id)) {
	ignore_user_abort(1);
	set_time_limit(0);

	$STH = $pdo->prepare("UPDATE `cases__wins` SET `finished`=:finished WHERE `id`=:id LIMIT 1");
	$STH->execute(array( ':finished' => '2', ':id' => $prize->id ));

	include_once $_SERVER["DOCUMENT_ROOT"]."/inc/notifications.php";
	$noty = 'Поздравляем Вас! Ваш приз из кейса <b>'.$prize->name.'</b>:<br>';

	$prizes = unserialize($prize->item);
	for ($j=0; $j < count($prizes); $j++) {
		$params = get_types_params($prizes[$j]['type']);
		if($prizes[$j]['type'] == 1 || $prizes[$j]['type'] == 5 || $prizes[$j]['type'] == 6 || $prizes[$j]['type'] == 7) {
			$STH = $pdo->prepare("SELECT `servers`.`name` AS `server_name`,`servers`.`type` AS `server_type`,`servers`.`discount`,`servers`.`binds`,`$params[2]`.`name`, `$params[3]`.`time`, `$params[3]`.`price` FROM `$params[2]` 
				LEFT JOIN `$params[3]` ON `$params[3]`.`service`=`$params[2]`.`id`
				LEFT JOIN `servers` ON `servers`.`id`=`$params[2]`.`server` WHERE `$params[2]`.`id`=:service AND `servers`.`id`=:server AND `$params[3]`.`id`=:tarif LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
			$STH->execute(array( ':service' => $prizes[$j]['service'], ':server' => $prizes[$j]['server'], ':tarif' => $prizes[$j]['tarif'] ));
			$prize_info = $STH->fetch();

			if($prize_info->time == 0) {
				$time = 'Навсегда';
			} else {
				$time = $prize_info->time.' дня(ей)';
			}

			$noty .= ' - Услуга: <b>'.$prize_info->name.'</b> с тарифом <b>'.$time.'</b> на сервере <b>'.$prize_info->server_name.'</b><br>';

			if($prizes[$j]['type'] == 1) {
				//выдача услуги
				//инфа об услуге
				$STH = $pdo->prepare("SELECT `users_group`,`discount`,`sb_group`FROM `services` WHERE `id`=:id LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
				$STH->execute(array( ':id' => $prizes[$j]['service'] ));
				$service = $STH->fetch();

				//инфа о глобальной скидке
				$STH = $pdo->query("SELECT `discount` FROM `config__prices` LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
				$glob = $STH->fetch();

				//инфа о тарифе
				$STH = $pdo->prepare("SELECT `id`,`time`,`price`,`discount` FROM `services__tarifs` WHERE `id`=:id LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
				$STH->execute(array( ':id' => $prizes[$j]['tarif'] ));
				$tarif = $STH->fetch();

				//подготавливаем данные для админки
				$AM = new AdminsManager;
				$admin['ending_date'] = $AM->get_ending_date($tarif->time);
				$admin['bought_date'] = date("Y-m-d H:i:s");
				$tarif->price = calculate_price($tarif->price, calculate_discount($prize_info->discount, $glob->discount, 0, $service->discount, $tarif->discount));
				$admin['irretrievable'] = calculate_return($tarif->price, $tarif->time);

				//думаем что делать с привигелией
				$adding_type['insert'] = 0; //записать новую на рандомный идентификатор
				$adding_type['extension'] = 0; //продлить уже имеющуюся привилегию на срок выйгранной
				$adding_type['merge'] = 0; //выполнить слияние

				//проверяем имеются ли у юзера незаблокированные услуги на нужном сервере
				$STH = $pdo->prepare("SELECT `id`, `name` FROM `admins` WHERE `server`=:server AND `user_id`=:user_id AND `active`='1' AND `pause`='0'"); $STH->setFetchMode(PDO::FETCH_OBJ);
				$STH->execute(array( ':server' => $prizes[$j]['server'], ':user_id' => $prize->user_id ));
				while($row = $STH->fetch()) {
					//как минимум одна услуга попалась, проверим та же ли это услуга, что мы выдаем и не бесконечный ли у нее срок
					$STH2 = $pdo->prepare("SELECT `admins__services`.`id` FROM `admins__services`
					INNER JOIN `services__tarifs` ON `services__tarifs`.`id` = `admins__services`.`service_time` 
					WHERE `admins__services`.`admin_id`=:admin_id AND `admins__services`.`service`=:service AND `services__tarifs`.`time` != '0' LIMIT 1"); $STH2->setFetchMode(PDO::FETCH_OBJ);
					$STH2->execute(array( ':admin_id' => $row->id, ':service' => $prizes[$j]['service'] ));
					$row2 = $STH2->fetch();
					if(isset($row2->id)) {
						//услуга такая есть и срок не бесконечный - продлим ее
						$adding_type['extension'] = $row2->id;
						$admin['name'] = $row->name;
						break;
					} else {
						$STH2 = $pdo->prepare("SELECT `admins__services`.`id` FROM `admins__services`
						INNER JOIN `services__tarifs` ON `services__tarifs`.`id` = `admins__services`.`service_time` 
						WHERE `admins__services`.`admin_id`=:admin_id AND `admins__services`.`service`=:service LIMIT 1"); $STH2->setFetchMode(PDO::FETCH_OBJ);
						$STH2->execute(array( ':admin_id' => $row->id, ':service' => $prizes[$j]['service'] ));
						$row2 = $STH2->fetch();
						if(empty($row2->id)) {
							//такой привилегии у него еще нет, проверим можно ли провести слияние
							if ($prize_info->server_type == 4 && $service->sb_group != '') {
								$STH2 = $pdo->query("SELECT `admins__services`.`id` FROM `admins__services` LEFT JOIN `services` ON `admins__services`.`service` = `services`.`id` WHERE `services`.`sb_group`!='' AND `admins__services`.`admin_id` = '$row->id' LIMIT 1"); $STH2->setFetchMode(PDO::FETCH_OBJ);
								$row2 = $STH2->fetch();
								if(empty($row2->id)) {
									$adding_type['merge'] = $row->id;
									$admin['name'] = $row->name;
									break;
								}
							} else {
								$adding_type['merge'] = $row->id;
								$admin['name'] = $row->name;
								break;
							}
						}
					}
				}

				//ничего кроме добавления новой не остается
				if($adding_type['extension'] == 0 && $adding_type['merge'] == 0) {
					$adding_type['insert'] = 1;
				}

				if($adding_type['merge'] != 0 || $adding_type['insert'] == 1) {
					if($service->users_group != 0 && true === false) { //пока убираем
						$STH = $pdo->prepare("SELECT `admins__services`.`previous_group` FROM `admins__services` 
							LEFT JOIN `admins` ON `admins`.`id` = `admins__services`.`admin_id` WHERE `admins`.`user_id`=:user_id AND `admins__services`.`previous_group`!='0' LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
						$STH->execute(array( ':user_id' => $prize->user_id ));
						$row = $STH->fetch();

						if(isset($row->previous_group)) {
							$admin['previous_group'] = $row->previous_group;
						} else {
							error_log($prize->rights);
							$admin['previous_group'] = $prize->rights;
						}

						$STH = $pdo->prepare("UPDATE `users` SET `rights`=:rights WHERE `id`=:id LIMIT 1");
						$STH->execute(array( ':rights' => $service->users_group, ':id' => $prize->user_id ));
					} else {
						$admin['previous_group'] = 0;
					}
				}

				//добавляем админа
				if($adding_type['insert'] == 1) {
					$binds = explode(';', $prize_info->binds);
					if($prize_info->server_type == 4) {
						if ($binds[1] == 1) {
							$admin['type'] = 2;
						} else {
							$admin['type'] = 3;
						}
					} else {
						if ($binds[0] == 1) {
							$admin['type'] = 1;
						} elseif ($binds[1] == 1) {
							$admin['type'] = 2;
						} else {
							$admin['type'] = 3;
						}
					}

					if ($admin['type'] == '1'){
						$admin['type'] = 'a';
						$admin['name'] = gernerate_admin_name($pdo, $prizes[$j]['server']);
						$admin['pass'] = crate_pass(6, 1);
						$admin['pass_md5'] = md5($admin['pass']);
					} elseif ($admin['type'] == '2'){
						$admin['type'] = 'ce';
						$admin['name'] = gernerate_admin_steam($pdo, $prizes[$j]['server']);
						$admin['pass'] = '';
						$admin['pass_md5'] = '';
					} elseif ($admin['type'] == '3'){
						$admin['type'] = 'ca';
						$admin['name'] = gernerate_admin_steam($pdo, $prizes[$j]['server']);
						$admin['pass'] = crate_pass(6, 1);
						$admin['pass_md5'] = md5($admin['pass']);
					}

					//запись нового админа
					$STH = $pdo->prepare("INSERT INTO admins (name,pass,pass_md5,type,server,user_id) values (:name, :pass, :pass_md5, :type, :server, :user_id)");
					$STH->execute(array( 'name' => $admin['name'], 'pass' => $admin['pass'], 'pass_md5' => $admin['pass_md5'], 'type' => $admin['type'], 'server' => $prizes[$j]['server'], 'user_id' => $prize->user_id ));

					$STH = $pdo->prepare("SELECT `id` FROM `admins` WHERE `name`=:name and `server`=:server LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
					$STH->execute(array( ':name' => $admin['name'], ':server' => $prizes[$j]['server'] ));
					$row = $STH->fetch();
					$admin['id'] = $row->id;

					if ($admin['type'] == 'a'){
						$noty .= '&nbsp&nbsp&nbsp Ник: <b>'.$admin['name'].'</b>, пароль <b>'.$admin['pass'].'</b> Настроить услугу Вы можете в личном профиле в настройках услуг.<br>';
					} elseif ($admin['type'] == 'ce'){
						$noty .= '&nbsp&nbsp&nbsp SteamID: <b>'.$admin['name'].'</b>. Настроить услугу Вы можете в личном профиле в настройках услуг.<br>';
					} elseif ($admin['type'] == 'ca'){
						$noty .= '&nbsp&nbsp&nbsp SteamID: <b>'.$admin['name'].'</b>, пароль <b>'.$admin['pass'].'</b> Настроить услугу Вы можете в личном профиле в настройках услуг.<br>';
					}
				}

				//слияние прав
				if($adding_type['merge'] != 0) {
					$admin['id'] = $adding_type['merge'];
					$noty .= '&nbsp&nbsp&nbsp Мы выполнили слияние данной услуги с Вашими на идентификаторе: <b>'.$admin['name'].'</b><br>';
				}

				if($adding_type['merge'] != 0 || $adding_type['insert'] == 1) {
					$STH = $pdo->prepare("INSERT INTO `admins__services` (`admin_id`,`service`,`service_time`,`bought_date`,`ending_date`,`irretrievable`,`previous_group`) values (:admin_id, :service, :service_time, :bought_date, :ending_date, :irretrievable, :previous_group)");
					$STH->execute(array( ':admin_id' => $admin['id'], ':service' => $prizes[$j]['service'], ':service_time' => $prizes[$j]['tarif'], ':bought_date' => $admin['bought_date'], ':ending_date' => $admin['ending_date'], ':irretrievable' => $admin['irretrievable'], ':previous_group' => $admin['previous_group'] ));
				}

				//продление прав
				if($adding_type['extension'] != 0) {
					$STH = $pdo->prepare("SELECT `admin_id`,`ending_date`,`irretrievable` FROM `admins__services` WHERE `id`=:id LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
					$STH->execute(array( ':id' => $adding_type['extension'] ));
					$row = $STH->fetch();

					$admin['id'] = $row->admin_id;

					if ($tarif->time != 0) {
						$admin['ending_date'] = date("Y-m-d H:i:s", strtotime($row->ending_date) + $tarif->time*24*3600);

						$old_left = floor((strtotime($row->ending_date)-time())/3600/24);
						$old_full_price = $old_left*$row->irretrievable;
						$admin['irretrievable'] = calculate_return($tarif->price+$old_full_price, $tarif->time+$old_left); 
					}

					$STH = $pdo->prepare("UPDATE `admins__services` SET `ending_date`=:ending_date, `service_time`=:service_time, `irretrievable`=:irretrievable WHERE `id`=:id LIMIT 1");
					$STH->execute(array( ':ending_date' => $admin['ending_date'], ':service_time' => $prizes[$j]['tarif'], ':irretrievable' => $admin['irretrievable'], ':id' => $adding_type['extension'] ));

					$noty .= '&nbsp&nbsp&nbsp Мы продлили Вашу услугу с идентификатором <b>'.$admin['name'].'</b> на срок выйгранной услуги.<br>';
				}

				if($AM->checking_server_status($pdo, $prizes[$j]['server'])) {
					if ($prize_info->server_type == 1 or $prize_info->server_type == 3){
						$AM->export_to_users_ini($pdo, $prizes[$j]['server'], 'CASES');
					}
					if ($prize_info->server_type == 2 or $prize_info->server_type == 4){
						$AM->export_admin($pdo, $admin['id'], $prizes[$j]['server'], 'CASES');
					}
				} else {
					send_noty($pdo, "[КЕЙСЫ]: Не удалось экспортировать администратора на сервер ".$prize_info->server_name.". Восстановите соединение с хранилищем сервера и выполните экспорт в админ центре движка.", 0, 2);
				}

				unset($AM);
			}
			if($prizes[$j]['type'] == 5) {
				$STH = $pdo->prepare("SELECT `id`, `ip`, `port`, `name`, `bk_host`, `bk_user`, `bk_pass`, `bk_db`, `bk_code`, `discount` FROM `servers` WHERE `id`=:id LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
				$STH->execute(array( ':id' => $prizes[$j]['server'] ));
				$server = $STH->fetch();
				if(!empty($server->id) && !empty($server->bk_host)){
					if($pdo2 = db_connect($server->bk_host, $server->bk_db, $server->bk_user, $server->bk_pass)) {
						set_names($pdo2, $server->bk_code);

						$key = crate_pass(20, 2);
						$STH = $pdo2->prepare("SELECT `key_name` FROM `table_keys` WHERE `key_name`=:key LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
						$STH->execute(array( ':key' => $key ));
						$row = $STH->fetch();
						if(isset($row->key_name)) {
							$key = crate_pass(21, 2);
						}

						$STH = $pdo2->prepare("SELECT `sid` FROM `keys_servers` WHERE `address`=:address LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
						$STH->execute(array( ':address' => $server->ip.":".$server->port ));
						$row = $STH->fetch();
						if(empty($row->sid)) {
							$error = 1;
						} else {
							$sid = $row->sid;
							$STH = $pdo2->prepare("INSERT INTO `table_keys` (`key_name`,`type`,`expires`,`uses`,`sid`,`param1`,`param2`,`active`) values (:key_name, :type, :expires, :uses, :sid, :param1, :param2, :active)");
							$STH->execute(array( ':key_name' => $key, ':type' => 'vip_add', ':expires' => '0', ':uses' => '1', ':sid' => $sid, ':param1' => $prize_info->name, ':param2' => $prize_info->time*24*60*60, ':active' => '1' ));
						}

						$noty .= '&nbsp&nbsp&nbsp Чтобы активировать услугу, зайдите на сервер и введите в консоль <b>key '.$key.'</b><br>';
					} else {
						$error = 1;
					}
				} else {
					$error = 1;
				}
				if(isset($error) && $error == 1) {
					send_noty($pdo, '[КЕЙСЫ]: Не удалось сгенерировать ключ для приза из buy_key для пользователя <a target="_blank" href="../profile?id='.$prize->user_id.'">'.$prize->login.'</a>', 0, 2);
				}
			}
			if($prizes[$j]['type'] == 6) {
				$STH = $pdo->prepare("SELECT `id`, `ip`, `port`, `name`, `vk_host`, `vk_user`, `vk_pass`, `vk_db`, `vk_code`, `discount` FROM `servers` WHERE `id`=:id LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
				$STH->execute(array( ':id' => $prizes[$j]['server'] ));
				$server = $STH->fetch();
				if(!empty($server->id) && !empty($server->vk_host)){
					if($pdo2 = db_connect($server->vk_host, $server->vk_db, $server->vk_user, $server->vk_pass)) {
						set_names($pdo2, $server->vk_code);

						$key = crate_pass(20, 2);
						$STH = $pdo2->prepare("SELECT `key` FROM `vip_keys_tab` WHERE `key`=:key LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
						$STH->execute(array( ':key' => $key ));
						$row = $STH->fetch();
						if(isset($row->key)) {
							$key = crate_pass(21, 2);
						}

						$STH = $pdo2->prepare("INSERT INTO `vip_keys_tab` (`key`,`vip_group`,`vip_min`,`cmd`,`active`) values (:key, :vip_group, :vip_min, :cmd, :active)");
						$STH->execute(array( ':key' => $key, ':vip_group' => $prize_info->name, ':vip_min' => $prize_info->time*24*60, ':cmd' => '-', ':active' => '0' ));

						$noty .= '&nbsp&nbsp&nbsp Чтобы активировать услугу, зайдите на сервер и введите в консоль <b>sm_vipkey '.$key.'</b><br>';
					} else {
						$error = 1;
					}
				} else {
					$error = 1;
				}
				if(isset($error) && $error == 1) {
					send_noty($pdo, '[КЕЙСЫ]: Не удалось сгенерировать ключ для приза из vip_key_ws для пользователя <a target="_blank" href="../profile?id='.$prize->user_id.'">'.$prize->login.'</a>', 0, 2);
				}
			}
			if($prizes[$j]['type'] == 7) {
				$STH = $pdo->prepare("SELECT `id`, `ip`, `port`, `name`, `vkb_host`, `vkb_login`, `vkb_pass`, `vkb_string`, `vkb_port`, `discount` FROM `servers` WHERE `id`=:id LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
				$STH->execute(array( ':id' => $prizes[$j]['server'] ));
				$server = $STH->fetch();

				$SM = new ServersManager;

				if(!empty($server->id) && !empty($server->vkb_host) && ($ftp_connection = $SM->ftp_connection($server->vkb_host, $server->vkb_port, $server->vkb_login, $server->vkb_pass, 'CASES')) && $SM->find_users_file($ftp_connection, $server->vkb_string)){

					$remote_file = $server->vkb_string;
					$local_file = $_SERVER["DOCUMENT_ROOT"].'/files/temp/vip_key_vkb'.rand().'.txt';

					if(($file = fopen($local_file, 'w+')) && ftp_fget($ftp_connection, $file, $remote_file, FTP_ASCII, 0)){
					
						$STH = $pdo->prepare("SELECT `$params[2]`.`flags` FROM `$params[2]` WHERE `$params[2]`.`id`=:service LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
						$STH->execute(array( ':service' => $prizes[$j]['service'] ));
						$flags = $STH->fetch();

						$key = crate_pass(8, 1);
						$time = $prize_info->time * 24;
						$flags = $flags->flags;

						if($time == 0) {
							$time = 'never';
						}

						fwrite($file, "\r\n".'"'.$key.'"="'.$flags.'"="'.$time.'"');
						fclose($file);

						$file = fopen($local_file, 'r');
						if (ftp_fput($ftp_connection, $remote_file, $file, FTP_ASCII, 0)) {
							fclose($file);
							unlink($local_file);
							$SM->close_ftp($ftp_connection);
							$noty .= '&nbsp&nbsp&nbsp Чтобы активировать услугу, зайдите на сервер и введите в консоль <b>vip_code '.$key.'</b><br>';
						} else {
							$error = 1;
						}
					} else {
						$error = 1;
					}
				} else {
					$error = 1;
				}
				if(isset($error) && $error == 1) {
					send_noty($pdo, '[КЕЙСЫ]: Не удалось сгенерировать ключ для приза из vip_key для пользователя <a target="_blank" href="../profile?id='.$prize->user_id.'">'.$prize->login.'</a>', 0, 2);
				}
			}
		}
		if($prizes[$j]['type'] == 2) {
			$noty .= ' - Деньги на Ваш баланс: <b>'.$prizes[$j]['money'].'</b> рублей<br>';

			$STH = $pdo->prepare("SELECT `id`,`shilings` FROM `users` WHERE `id`=:id LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
			$STH->execute(array( ':id' => $prize->user_id ));
			$row = $STH->fetch();
			if (!empty($row->id)){
				$STH = $pdo->prepare("UPDATE `users` SET `shilings`=:shilings WHERE `id`=:id LIMIT 1");
				$STH->execute(array( ':id' => $prize->user_id, 'shilings' => $row->shilings+$prizes[$j]['money'] ));

				$STH = $pdo->prepare("INSERT INTO `money__actions` (date,shilings,author,type) values (:date, :shilings, :author, :type)");
				$STH->execute(array( 'date' => date("Y-m-d H:i:s"),'shilings' => $prizes[$j]['money'],'author' => $prize->user_id, 'type' => '17' ));
			}
		}
		if($prizes[$j]['type'] == 3) {
			$STH = $pdo->prepare("SELECT `proc` FROM `users` WHERE `id`=:id LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
			$STH->execute(array( ':id' => $prize->user_id ));
			$row = $STH->fetch();
			if($row->proc > $prizes[$j]['percent']) {
				$noty .= ' - Скидка на все услуги: <b>'.$prizes[$j]['percent'].'%</b>, но Ваша текущая скидка в <b>'.$row->proc.'%</b>, поэтому мы сохранили её.<br>';
			} else {
				$noty .= ' - Скидка на все услуги: <b>'.$prizes[$j]['percent'].'%</b><br>';

				$STH = $pdo->prepare("UPDATE `users` SET `proc`=:proc WHERE `id`=:id LIMIT 1");
				$STH->execute(array( ':id' => $prize->user_id, 'proc' => $prizes[$j]['percent'] ));
			}
		}
		if($prizes[$j]['type'] == 4) {
			$STH = $pdo->prepare("SELECT `servers`.`name` AS `server_name`, `sk_services`.`number`, `sk_services`.`type` FROM `sk_services` 
				LEFT JOIN `servers` ON `servers`.`id`=`sk_services`.`server` WHERE `sk_services`.`id`=:tarif AND `servers`.`id`=:server LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
			$STH->execute(array( ':server' => $prizes[$j]['server'], ':tarif' => $prizes[$j]['tarif'] ));
			$prize_info = $STH->fetch();

			$noty .= ' - '.$services_data[$prize_info->type]['name'].': <b>'.$prize_info->number.'</b> на сервере <b>'.$prize_info->server_name.'</b><br>';

			$STH = $pdo->prepare("SELECT `id`, `ip`, `port`, `name`, `sk_host`, `sk_user`, `sk_pass`, `sk_db`, `sk_code`, `discount` FROM `servers` WHERE `id`=:id LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
			$STH->execute(array( ':id' => $prizes[$j]['server'] ));
			$server = $STH->fetch();
			if(!empty($server->id) && !empty($server->sk_host)){
				if($pdo2 = db_connect($server->sk_host, $server->sk_db, $server->sk_user, $server->sk_pass)) {
					set_names($pdo2, $server->sk_code);

					$key = crate_pass(20, 2);
					$STH = $pdo2->prepare("SELECT `key_name` FROM `table_keys` WHERE `key_name`=:key LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
					$STH->execute(array( ':key' => $key ));
					$row = $STH->fetch();
					if(isset($row->key_name)) {
						$key = crate_pass(21, 2);
					}

					$STH = $pdo2->prepare("SELECT `sid` FROM `keys_servers` WHERE `address`=:address LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
					$STH->execute(array( ':address' => $server->ip.":".$server->port ));
					$row = $STH->fetch();
					if(empty($row->sid)) {
						$error = 1;
					} else {
						$sid = $row->sid;
						$STH = $pdo2->prepare("INSERT INTO `table_keys` (`key_name`,`type`,`expires`,`uses`,`sid`,`param1`,`active`) values (:key_name, :type, :expires, :uses, :sid, :param1, :active)");
						$STH->execute(array( ':key_name' => $key, ':type' => $services_data[$prize_info->type]['type'], ':expires' => '0', ':uses' => '1', ':sid' => $sid, ':param1' => $prize_info->number, ':active' => '1' ));

						$noty .= '&nbsp&nbsp&nbsp Чтобы активировать приз, зайдите на сервер и введите в консоль <b>key '.$key.'</b><br>';
					}
				} else {
					$error = 1;
				}
			} else {
				$error = 1;
			}
			if(isset($error) && $error == 1) {
				send_noty($pdo, '[КЕЙСЫ]: Не удалось сгенерировать ключ для приза из shop_key для пользователя <a target="_blank" href="../profile?id='.$prize->user_id.'">'.$prize->login.'</a>', 0, 2);
			}
		}
	}

	send_noty($pdo, $noty, $prize->user_id, 2);

	$STH = $pdo->prepare("UPDATE `cases__wins` SET `finished`=:finished WHERE `id`=:id LIMIT 1");
	$STH->execute(array( ':finished' => '1', ':id' => $prize->id ));
}