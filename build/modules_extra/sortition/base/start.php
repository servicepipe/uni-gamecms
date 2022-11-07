<?php
$STH = $pdo->query("SELECT * FROM `sortition` LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
$sortition = $STH->fetch();
if(isset($sortition->finished)) {
	$STH = $pdo->query("SELECT COUNT(*) FROM `sortition__participants`");
	$count = $STH->fetchColumn();

	if($sortition->finished == 2 && (($sortition->ending <= time() && $sortition->end_type == 1) || ($count >= $sortition->participants && $sortition->end_type == 2))) {
		//проводим розыгрыш

		if($sortition->own_prize == 1) {
			$prize = unserialize($sortition->prize);
			$winners_count = $prize['count_of_winners'];
			$prize = $prize['description'];
		} else {
			$prize = unserialize($sortition->prize);
			$winners_count = $prize[count($prize)-1]['place'];
		}

		$winners = get_winners($pdo, $users_groups, $winners_count);

		if(!isset($winners['error'])) {
			include_once $_SERVER["DOCUMENT_ROOT"]."/inc/notifications.php";
			$noty_admin = 'Розыгрыш "'.$sortition->name.'" окончен. Победитель(и) выбран(ы): <br>';	

			for ($i=1; $i <= count($winners); $i++) {
				$noty_admin .= '- '.$i.' место: <a target="_blank" href="../profile?id='.$winners[$i]['id'].'">'.$winners[$i]['login'].'</a><br>';

				if($sortition->own_prize == 1) {
					$noty_user = 'Поздравляем Вас! Вы заняли '.$i.' место в розыгрыше "'.$sortition->name.'"';
					$prize_str = '';
				} else {
					$noty_user = 'Поздравляем Вас! Вы заняли '.$i.' место в розыгрыше "'.$sortition->name.'" и получаете приз: ';
					$prize_str = '<br>';

					for ($j=0; $j < count($prize); $j++) {
						if($prize[$j]['place'] == $i) {
							$params = get_types_params($prize[$j]['type']);
							if($prize[$j]['type'] == 1 || $prize[$j]['type'] == 5 || $prize[$j]['type'] == 6) {
								$STH = $pdo->prepare("SELECT `servers`.`name` AS `server_name`,`servers`.`type` AS `server_type`,`servers`.`discount`,`$params[2]`.`name`, `$params[3]`.`time`, `$params[3]`.`price` FROM `$params[2]` 
									LEFT JOIN `$params[3]` ON `$params[3]`.`service`=`$params[2]`.`id`
									LEFT JOIN `servers` ON `servers`.`id`=`$params[2]`.`server` WHERE `$params[2]`.`id`=:service AND `servers`.`id`=:server AND `$params[3]`.`id`=:tarif LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
								$STH->execute(array( ':service' => $prize[$j]['service'], ':server' => $prize[$j]['server'], ':tarif' => $prize[$j]['tarif'] ));
								$prize_info = $STH->fetch();

								if($prize_info->time == 0) {
									$time = 'Навсегда';
								} else {
									$time = $prize_info->time.' дня(ей)';
								}

								$prize_str .= ' - Услуга: <b>'.$prize_info->name.'</b> с тарифом <b>'.$time.'</b> на сервере <b>'.$prize_info->server_name.'</b><br>';

								if($prize[$j]['type'] == 1) {
									$STH = $pdo->prepare("SELECT `users_group`, `discount` FROM `services` WHERE `id`=:id LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
									$STH->execute(array( ':id' => $prize[$j]['service'] ));
									$service = $STH->fetch();

									$STH = $pdo->query("SELECT discount FROM config__prices LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
									$row = $STH->fetch();

									$proc = calculate_discount($prize_info->discount, $row->discount, 0, $service->discount);
									$price = calculate_price($prize_info->price, $proc);
									$admin['irretrievable'] = calculate_return($price, $prize_info->time);

									if($prize_info->server_type == 4) {
										if ($conf->peg_2 == 1) {
											$admin['type'] = 2;
										} else {
											$admin['type'] = 3;
										}
									} else {
										if ($conf->peg_1 == 1) {
											$admin['type'] = 1;
										} elseif ($conf->peg_2 == 1) {
											$admin['type'] = 2;
										} else {
											$admin['type'] = 3;
										}
									}

									if ($admin['type'] == '1'){
										$admin['type'] = 'a';
										$admin['name'] = gernerate_admin_name($pdo, $prize[$j]['server']);
										$admin['pass'] = crate_pass(6, 1);
										$admin['pass_md5'] = md5($admin['pass']);
									} elseif ($admin['type'] == '2'){
										$admin['type'] = 'ce';
										$admin['name'] = gernerate_admin_steam($pdo, $prize[$j]['server']);
										$admin['pass'] = '';
										$admin['pass_md5'] = '';
									} elseif ($admin['type'] == '3'){
										$admin['type'] = 'ca';
										$admin['name'] = gernerate_admin_steam($pdo, $prize[$j]['server']);
										$admin['pass'] = crate_pass(6, 1);
										$admin['pass_md5'] = md5($admin['pass']);
									}

									$admin['bought_date'] = date("Y-m-d H:i:s");
									if ($time == 0) {
										$admin['ending_date'] = '0000-00-00 00:00:00';
									} else {
										$admin['ending_date'] = date("Y-m-d H:i:s", time()+$prize_info->time*24*60*60);
									}

									$STH = $pdo->prepare("INSERT INTO admins (name,pass,pass_md5,type,server,user_id) values (:name, :pass, :pass_md5, :type, :server, :user_id)");
									$STH->execute(array( 'name' => $admin['name'], 'pass' => $admin['pass'], 'pass_md5' => $admin['pass_md5'], 'type' => $admin['type'], 'server' => $prize[$j]['server'], 'user_id' => $winners[$i]['id'] ));

									$STH = $pdo->prepare("SELECT `id` FROM `admins` WHERE `name`=:name and `server`=:server LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
									$STH->execute(array( ':name' => $admin['name'], ':server' => $prize[$j]['server'] ));
									$row = $STH->fetch();
									$admin['id'] = $row->id;

									$STH = $pdo->prepare("INSERT INTO `admins__services` (`admin_id`,`service`,`service_time`,`bought_date`,`ending_date`,`irretrievable`) values (:admin_id, :service, :service_time, :bought_date, :ending_date, :irretrievable)");
									$STH->execute(array( ':admin_id' => $admin['id'], ':service' => $prize[$j]['service'], ':service_time' => $prize[$j]['tarif'], ':bought_date' => $admin['bought_date'], ':ending_date' => $admin['ending_date'], ':irretrievable' => $admin['irretrievable'] ));

									$AM = new AdminsManager;
									if ($prize_info->server_type == 1 or $prize_info->server_type == 3){
										$AM->export_to_users_ini($pdo, $prize[$j]['server'], 'BUY_SERVICE');
									}
									if ($prize_info->server_type == 2 or $prize_info->server_type == 4){
										$AM->export_admin($pdo, $admin['id'], $prize[$j]['server'], 'BUY_SERVICE');
									}
									unset($AM);

									if($service->users_group != 0 ) {
										$STH = $pdo->prepare("UPDATE `users` SET `rights`=:rights WHERE `id`=:id LIMIT 1");
										$STH->execute(array( ':rights' => $service->users_group, ':id' => $winners[$i]['id'] ));
									}

									if ($admin['type'] == 'a'){
										$prize_str .= '&nbsp&nbsp&nbsp Ник: <b>'.$admin['name'].'</b>, пароль <b>'.$admin['pass'].'</b> Настроить услугу Вы можете в личном профиле в настройках услуг.<br>';
									} elseif ($admin['type'] == 'ce'){
										$prize_str .= '&nbsp&nbsp&nbsp SteamID: <b>'.$admin['name'].'</b>. Настроить услугу Вы можете в личном профиле в настройках услуг.<br>';
									} elseif ($admin['type'] == 'ca'){
										$prize_str .= '&nbsp&nbsp&nbsp SteamID: <b>'.$admin['name'].'</b>, пароль <b>'.$admin['pass'].'</b> Настроить услугу Вы можете в личном профиле в настройках услуг.<br>';
									}
								}
								if($prize[$j]['type'] == 5) {
									$STH = $pdo->prepare("SELECT `id`, `ip`, `port`, `name`, `bk_host`, `bk_user`, `bk_pass`, `bk_db`, `bk_code`, `discount` FROM `servers` WHERE `id`=:id LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
									$STH->execute(array( ':id' => $prize[$j]['server'] ));
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

											$prize_str .= '&nbsp&nbsp&nbsp Чтобы активировать услугу, зайдите на сервер и введите в консоль <b>key '.$key.'</b><br>';
										} else {
											$error = 1;
										}
									} else {
										$error = 1;
									}
									if(isset($error) && $error == 1) {
										send_noty($pdo, 'Не удалось сгенерировать ключ для приза из buy_key для пользователя <a target="_blank" href="../profile?id='.$winners[$i]['id'].'">'.$winners[$i]['login'].'</a>', 0, 2);
									}
								}
								if($prize[$j]['type'] == 6) {
									$STH = $pdo->prepare("SELECT `id`, `ip`, `port`, `name`, `vk_host`, `vk_user`, `vk_pass`, `vk_db`, `vk_code`, `discount` FROM `servers` WHERE `id`=:id LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
									$STH->execute(array( ':id' => $prize[$j]['server'] ));
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

											$prize_str .= '&nbsp&nbsp&nbsp Чтобы активировать услугу, зайдите на сервер и введите в консоль <b>sm_vipkey '.$key.'</b><br>';
										} else {
											$error = 1;
										}
									} else {
										$error = 1;
									}
									if(isset($error) && $error == 1) {
										send_noty($pdo, 'Не удалось сгенерировать ключ для приза из vip_key_ws для пользователя <a target="_blank" href="../profile?id='.$winners[$i]['id'].'">'.$winners[$i]['login'].'</a>', 0, 2);
									}
								}
							}
							if($prize[$j]['type'] == 2) {
								$prize_str .= ' - Деньги на Ваш баланс: <b>'.$prize[$j]['money'].'</b> рублей<br>';

								$STH = $pdo->prepare("SELECT `id`,`shilings` FROM `users` WHERE `id`=:id LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
								$STH->execute(array( ':id' => $winners[$i]['id'] ));
								$row = $STH->fetch();
								if (!empty($row->id)){
									$STH = $pdo->prepare("UPDATE `users` SET `shilings`=:shilings WHERE `id`=:id LIMIT 1");
									$STH->execute(array( ':id' => $winners[$i]['id'], 'shilings' => $row->shilings+$prize[$j]['money'] ));
								}
							}
							if($prize[$j]['type'] == 3) {
								$prize_str .= ' - Скидка на все услуги: <b>'.$prize[$j]['percent'].'%</b><br>';

								$STH = $pdo->prepare("UPDATE `users` SET `proc`=:proc WHERE `id`=:id LIMIT 1");
								$STH->execute(array( ':id' => $winners[$i]['id'], 'proc' => $prize[$j]['percent'] ));
							}
							if($prize[$j]['type'] == 4) {
								$STH = $pdo->prepare("SELECT `servers`.`name` AS `server_name`, `sk_services`.`number`, `sk_services`.`type` FROM `sk_services` 
									LEFT JOIN `servers` ON `servers`.`id`=`sk_services`.`server` WHERE `sk_services`.`id`=:tarif AND `servers`.`id`=:server LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
								$STH->execute(array( ':server' => $prize[$j]['server'], ':tarif' => $prize[$j]['tarif'] ));
								$prize_info = $STH->fetch();

								$prize_str .= ' - '.$services_data[$prize_info->type]['name'].': <b>'.$prize_info->number.'</b> на сервере <b>'.$prize_info->server_name.'</b><br>';

								$STH = $pdo->prepare("SELECT `id`, `ip`, `port`, `name`, `sk_host`, `sk_user`, `sk_pass`, `sk_db`, `sk_code`, `discount` FROM `servers` WHERE `id`=:id LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
								$STH->execute(array( ':id' => $prize[$j]['server'] ));
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

											$prize_str .= '&nbsp&nbsp&nbsp Чтобы активировать приз, зайдите на сервер и введите в консоль <b>key '.$key.'</b><br>';
										}
									} else {
										$error = 1;
									}
								} else {
									$error = 1;
								}
								if(isset($error) && $error == 1) {
									send_noty($pdo, 'Не удалось сгенерировать ключ для приза из shop_key для пользователя <a target="_blank" href="../profile?id='.$winners[$i]['id'].'">'.$winners[$i]['login'].'</a>', 0, 2);
								}
							}
							if($prize[$j]['type'] == 7) {
								$prize_str .= ' - Поинты : <b>'.$prize[$j]['points'].'</b> шт<br>';

								$STH = $pdo->prepare("SELECT `id`,`playground` FROM `users` WHERE `id`=:id LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
								$STH->execute(array( ':id' => $winners[$i]['id'] ));
								$row = $STH->fetch();
								if (!empty($row->id)){
									$STH = $pdo->prepare("UPDATE `users` SET `playground`=:playground WHERE `id`=:id LIMIT 1");
									$STH->execute(array( ':id' => $winners[$i]['id'], 'playground' => $row->playground+$prize[$j]['points'] ));
								}
							}
							if($prize[$j]['type'] == 8) {
								$prize_str .= ' - Опыт : <b>'.$prize[$j]['exp'].'</b> шт<br>';

								$STH = $pdo->prepare("SELECT `id`,`experience` FROM `users` WHERE `id`=:id LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
								$STH->execute(array( ':id' => $winners[$i]['id'] ));
								$row = $STH->fetch();
								if (!empty($row->id)){
									$STH = $pdo->prepare("UPDATE `users` SET `experience`=:experience WHERE `id`=:id LIMIT 1");
									$STH->execute(array( ':id' => $winners[$i]['id'], 'experience' => $row->experience+$prize[$j]['exp'] ));
								}
							}
						}
					}
				}
				send_noty($pdo, $noty_user.$prize_str, $winners[$i]['id'], 2);
			}
			send_noty($pdo, $noty_admin, 0, 2);

			$STH = $pdo->prepare("UPDATE `sortition` SET `finished`=:finished LIMIT 1");
			$STH->execute(array( ':finished' => '1' ));
			$sortition->finished = 1;
		}
	}

	$sortition->ending = date('d.m.Y H:i', $sortition->ending);
	$exists = 1;
} else {
	$exists = 2;
	$sortition = new stdClass();
	$sortition->name = '';
	$sortition->ending = date('d.m.Y H:i', time()+5*24*60*60);
	$sortition->price = '';
	$sortition->participants = '';
	$sortition->prize = '';
	$sortition->own_prize = 2;
	$sortition->text = '';
	$sortition->count_of_winners = '';
	$sortition->how_old = '';
	$sortition->show_participants = 2;
	$sortition->end_type = 1;
	$sortition->finished = 2;
}
?>