<?php
include_once __DIR__ . '/../../../inc/start.php';
include_once __DIR__ . '/../../../inc/protect.php';
include_once __DIR__ . '/../base/config.php';

if(empty($_POST['phpaction'])) {
	log_error('Прямой вызов actions.php');
	exit(json_encode(['status' => '2']));
}

if($conf->token == 1 && ($_SESSION['token'] != clean($_POST['token'], null))) {
	log_error('Неверный токен');
	exit(json_encode(['status' => '2']));
}

if(isset($_POST['setActivity'])) {
	if(!is_auth()) {
		die;
	}

	if($activityRewardsConfig->is_need_money_activity == 1) {
		$amountOfMoney = getUserDonateAmount($pdo, $_SESSION['id']);

		if($amountOfMoney < $activityRewardsConfig->amount_of_money) {
			die;
		}
	}

	$STH = $pdo->prepare("SELECT * FROM activity_rewards__participants WHERE user_id=:user_id LIMIT 1");
	$STH->setFetchMode(PDO::FETCH_OBJ);
	$STH->execute([':user_id' => $_SESSION['id']]);
	$row = $STH->fetch();

	if(empty($row->id)) {
		$STH = $pdo->prepare(
				"INSERT INTO activity_rewards__participants (user_id,days_in_a_row,last_activity) 
						    VALUES (:user_id, :days_in_a_row, :last_activity)"
		);
		$STH->execute(
			[
					'user_id' => $_SESSION['id'],
					'days_in_a_row' => 1,
					'last_activity' => strtotime(date('Y-m-d'))
			]
		);
	} else {
		$delta = strtotime(date('Y-m-d')) - $row->last_activity;
		$dayTime = 24 * 60 * 60;

		if($delta > $dayTime) {
			$STH = $pdo->prepare(
				"UPDATE 
								    activity_rewards__participants 
								SET 
								    days_in_a_row=:days_in_a_row, 
								    last_activity=:last_activity 
								WHERE user_id=:user_id LIMIT 1"
			);
			$STH->execute(
				[
					':days_in_a_row'     => 1,
					':last_activity'     => strtotime(date('Y-m-d')),
					':user_id'           => $_SESSION['id']
				]
			);
		} elseif($delta == $dayTime) {
			$daysInARow = $row->days_in_a_row + 1;

			if($daysInARow > $row->days_in_a_row_max) {
				$daysInARowMax = $daysInARow;
			} else {
				$daysInARowMax = $row->days_in_a_row_max;
			}

			$STH = $pdo->prepare(
					"UPDATE 
								    activity_rewards__participants 
								SET 
								    days_in_a_row=:days_in_a_row, 
								    days_in_a_row_max=:days_in_a_row_max,
								    last_activity=:last_activity 
								WHERE user_id=:user_id LIMIT 1"
			);
			$STH->execute(
				[
					':days_in_a_row'     => $daysInARow,
					':days_in_a_row_max' => $daysInARowMax,
					':last_activity'     => strtotime(date('Y-m-d')),
					':user_id'           => $_SESSION['id']
				]
			);

			if(
					$activityRewardsConfig->is_re_issue == 1
						|| ($activityRewardsConfig->is_re_issue == 0 && $daysInARowMax == $daysInARow)
			) {
				$STH = $pdo->prepare("SELECT reward FROM activity_rewards WHERE days_in_a_row=:days_in_a_row LIMIT 1");
				$STH->setFetchMode(PDO::FETCH_OBJ);
				$STH->execute([':days_in_a_row' => $daysInARow]);
				$row = $STH->fetch();

				if(!empty($row->reward)) {
					$reward = unserialize($row->reward);
				}
			}
		}
	}

	if(isset($reward)) {
		ignore_user_abort(1);
		set_time_limit(0);

		include_once __DIR__ . '/../../../inc/notifications.php';

		$noty = 'Вы посещали наш сайт ' . $daysInARow . ' дня(ей) подряд и получаете за это награду: ';
		$prizeMessage = '<br>';

		$params = getTypesParams($reward['type']);
		if($reward['type'] == 1 || $reward['type'] == 5 || $reward['type'] == 6 || $reward['type'] == 7) {
			$STH = $pdo->prepare(
				"SELECT 
							    servers.name AS server_name,
							    servers.type AS server_type,
							    servers.discount,
							    servers.binds,
							    $params[2].name, 
							    $params[3].time, 
							    $params[3].price
							FROM 
							    $params[2] 
								LEFT JOIN $params[3] ON $params[3].service=$params[2].id
								LEFT JOIN servers ON servers.id=$params[2].server 
							WHERE 
							    $params[2].id=:service 
							  AND servers.id=:server 
							  AND $params[3].id=:tarif 
							LIMIT 1"
			);
			$STH->setFetchMode(PDO::FETCH_OBJ);
			$STH->execute(
				[
					':service' => $reward['service'],
					':server'  => $reward['server'],
					':tarif'   => $reward['tarif']
				]
			);
			$rewardInfo = $STH->fetch();

			if($rewardInfo->time == 0) {
				$time = 'Навсегда';
			} else {
				$time = $rewardInfo->time . ' дня(ей)';
			}

			$prizeMessage .= ' - Услуга: <b>' . $rewardInfo->name
				. '</b> с тарифом <b>' . $time
				. '</b> на сервере <b>' . $rewardInfo->server_name . '</b><br>';

			if($reward['type'] == 1) {
				//выдача услуги
				//инфа об услуге
				$STH = $pdo->prepare("SELECT users_group,discount,sb_group FROM services WHERE id=:id LIMIT 1");
				$STH->setFetchMode(PDO::FETCH_OBJ);
				$STH->execute([':id' => $reward['service']]);
				$service = $STH->fetch();

				//инфа о глобальной скидке
				$STH = $pdo->query("SELECT discount FROM config__prices LIMIT 1");
				$STH->setFetchMode(PDO::FETCH_OBJ);
				$glob = $STH->fetch();

				//инфа о тарифе
				$STH = $pdo->prepare("SELECT id,time,price,discount FROM services__tarifs WHERE id=:id LIMIT 1");
				$STH->setFetchMode(PDO::FETCH_OBJ);
				$STH->execute([':id' => $reward['tarif']]);
				$tarif = $STH->fetch();

				//подготавливаем данные для админки
				$AM                     = new AdminsManager;
				$admin['ending_date']   = $AM->get_ending_date($tarif->time);
				$admin['bought_date']   = date("Y-m-d H:i:s");
				$tarif->price           = calculate_price(
					$tarif->price,
					calculate_discount(
						$rewardInfo->discount,
						$glob->discount,
						0,
						$service->discount,
						$tarif->discount
					)
				);
				$admin['irretrievable'] = calculate_return($tarif->price, $tarif->time);

				//думаем что делать с привигелией
				$adding_type['insert']    = 0; //записать новую на рандомный идентификатор
				$adding_type['extension'] = 0; //продлить уже имеющуюся привилегию на срок выйгранной
				$adding_type['merge']     = 0; //выполнить слияние

				//проверяем имеются ли у юзера незаблокированные услуги на нужном сервере
				$STH = $pdo->prepare(
					"SELECT 
								    id, name 
								FROM admins 
								WHERE server=:server 
								  AND user_id=:user_id 
								  AND active='1' 
								  AND pause='0'"
				);
				$STH->setFetchMode(PDO::FETCH_OBJ);
				$STH->execute([':server' => $reward['server'], ':user_id' => $_SESSION['id']]);
				while($row = $STH->fetch()) {
					//как минимум одна услуга попалась, проверим та же ли это услуга, что мы выдаем и не бесконечный ли у нее срок
					$STH2 = $pdo->prepare(
						"SELECT 
									    admins__services.id 
									FROM 
									    admins__services
										INNER JOIN services__tarifs ON services__tarifs.id = admins__services.service_time 
									WHERE 
									    admins__services.admin_id=:admin_id 
									  AND admins__services.service=:service 
									  AND services__tarifs.time != '0' 
									LIMIT 1"
					);
					$STH2->setFetchMode(PDO::FETCH_OBJ);
					$STH2->execute([':admin_id' => $row->id, ':service' => $reward['service']]);
					$row2 = $STH2->fetch();
					if(isset($row2->id)) {
						//услуга такая есть и срок не бесконечный - продлим ее
						$adding_type['extension'] = $row2->id;
						$admin['name']            = $row->name;
						break;
					} else {
						$STH2 = $pdo->prepare(
							"SELECT 
										    admins__services.id 
										FROM admins__services
											INNER JOIN services__tarifs ON services__tarifs.id = admins__services.service_time 
										WHERE 
										    admins__services.admin_id=:admin_id 
										  AND admins__services.service=:service 
										LIMIT 1"
						);
						$STH2->setFetchMode(PDO::FETCH_OBJ);
						$STH2->execute([':admin_id' => $row->id, ':service' => $reward['service']]);
						$row2 = $STH2->fetch();
						if(empty($row2->id)) {
							//такой привилегии у него еще нет, проверим можно ли провести слияние
							if($rewardInfo->server_type == 4 && $service->sb_group != '') {
								$STH2 = $pdo->query(
									"SELECT 
												    admins__services.id 
												FROM admins__services 
												    LEFT JOIN services ON admins__services.service = services.id 
												WHERE 
												    services.sb_group!='' 
												  AND admins__services.admin_id = '$row->id' 
												LIMIT 1"
								);
								$STH2->setFetchMode(PDO::FETCH_OBJ);
								$row2 = $STH2->fetch();
								if(empty($row2->id)) {
									$adding_type['merge'] = $row->id;
									$admin['name']        = $row->name;
									break;
								}
							} else {
								$adding_type['merge'] = $row->id;
								$admin['name']        = $row->name;
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
					if($service->users_group != 0) {
						$STH = $pdo->prepare(
							"SELECT 
										    admins__services.previous_group 
										FROM admins__services 
											LEFT JOIN admins ON admins.id = admins__services.admin_id 
										WHERE 
										    admins.user_id=:user_id 
										  AND admins__services.previous_group!='0' 
										LIMIT 1"
						);
						$STH->setFetchMode(PDO::FETCH_OBJ);
						$STH->execute([':user_id' => $_SESSION['id']]);
						$row = $STH->fetch();

						if(isset($row->previous_group)) {
							$admin['previous_group'] = $row->previous_group;
						} else {
							$admin['previous_group'] = $_SESSION['rights'];
						}

						$STH = $pdo->prepare("UPDATE users SET rights=:rights WHERE id=:id LIMIT 1");
						$STH->execute([':rights' => $service->users_group, ':id' => $_SESSION['id']]);
					} else {
						$admin['previous_group'] = 0;
					}
				}

				//добавляем админа
				if($adding_type['insert'] == 1) {
					$binds = explode(';', $rewardInfo->binds);

					if($rewardInfo->server_type == 4) {
						if($binds[1] == 1) {
							$admin['type'] = 2;
						} else {
							$admin['type'] = 3;
						}
					} else {
						if($binds[0] == 1) {
							$admin['type'] = 1;
						} elseif($binds[1] == 1) {
							$admin['type'] = 2;
						} else {
							$admin['type'] = 3;
						}
					}

					if($admin['type'] == '1') {
						$admin['type']     = 'a';
						$admin['name']     = generateAdminName($pdo, $reward['server']);
						$admin['pass']     = crate_pass(6, 1);
						$admin['pass_md5'] = md5($admin['pass']);
					} elseif($admin['type'] == '2') {
						$admin['type']     = 'ce';
						$admin['name']     = generateAdminSteam($pdo, $reward['server']);
						$admin['pass']     = '';
						$admin['pass_md5'] = '';
					} elseif($admin['type'] == '3') {
						$admin['type']     = 'ca';
						$admin['name']     = generateAdminSteam($pdo, $reward['server']);
						$admin['pass']     = crate_pass(6, 1);
						$admin['pass_md5'] = md5($admin['pass']);
					}

					//запись нового админа
					$STH = $pdo->prepare(
						"INSERT INTO admins (name,pass,pass_md5,type,server,user_id) 
									VALUES (:name, :pass, :pass_md5, :type, :server, :user_id)"
					);
					$STH->execute(
						[
							'name'     => $admin['name'],
							'pass'     => $admin['pass'],
							'pass_md5' => $admin['pass_md5'],
							'type'     => $admin['type'],
							'server'   => $reward['server'],
							'user_id'  => $_SESSION['id']
						]
					);

					$STH = $pdo->prepare(
						"SELECT id FROM admins WHERE name=:name AND server=:server LIMIT 1"
					);
					$STH->setFetchMode(PDO::FETCH_OBJ);
					$STH->execute([':name' => $admin['name'], ':server' => $reward['server']]);
					$row         = $STH->fetch();
					$admin['id'] = $row->id;

					if($admin['type'] == 'a') {
						$prizeMessage .= '&nbsp&nbsp&nbsp Ник: <b>' . $admin['name']
							. '</b>, пароль <b>' . $admin['pass']
							. '</b> Настроить услугу Вы можете в личном профиле в настройках услуг.<br>';
					} elseif($admin['type'] == 'ce') {
						$prizeMessage .= '&nbsp&nbsp&nbsp SteamID: <b>' . $admin['name']
							. '</b>. Настроить услугу Вы можете в личном профиле в настройках услуг.<br>';
					} elseif($admin['type'] == 'ca') {
						$prizeMessage .= '&nbsp&nbsp&nbsp SteamID: <b>' . $admin['name']
							. '</b>, пароль <b>' . $admin['pass']
							. '</b> Настроить услугу Вы можете в личном профиле в настройках услуг.<br>';
					}
				}

				//слияние прав
				if($adding_type['merge'] != 0) {
					$admin['id']  = $adding_type['merge'];
					$prizeMessage .= '&nbsp&nbsp&nbsp Мы выполнили слияние данной услуги с Вашими на идентификаторе: <b>'
						. $admin['name'] . '</b><br>';
				}

				if($adding_type['merge'] != 0 || $adding_type['insert'] == 1) {
					$STH = $pdo->prepare(
						"INSERT INTO admins__services (admin_id,service,service_time,bought_date,ending_date,irretrievable,previous_group) 
									VALUES (:admin_id, :service, :service_time, :bought_date, :ending_date, :irretrievable, :previous_group)"
					);
					$STH->execute(
						[
							':admin_id'       => $admin['id'],
							':service'        => $reward['service'],
							':service_time'   => $reward['tarif'],
							':bought_date'    => $admin['bought_date'],
							':ending_date'    => $admin['ending_date'],
							':irretrievable'  => $admin['irretrievable'],
							':previous_group' => $admin['previous_group']
						]
					);
				}

				//продление прав
				if($adding_type['extension'] != 0) {
					$STH = $pdo->prepare(
						"SELECT admin_id,ending_date,irretrievable FROM admins__services WHERE id=:id LIMIT 1"
					);
					$STH->setFetchMode(PDO::FETCH_OBJ);
					$STH->execute([':id' => $adding_type['extension']]);
					$row = $STH->fetch();

					$admin['id'] = $row->admin_id;

					if($tarif->time != 0) {
						$admin['ending_date'] = date(
								"Y-m-d H:i:s",
								strtotime($row->ending_date) + $tarif->time * 24 * 3600
						);

						$old_left       = floor((strtotime($row->ending_date) - time()) / 3600 / 24);
						$old_full_price = $old_left * $row->irretrievable;

						$admin['irretrievable'] = calculate_return(
							$tarif->price + $old_full_price,
							$tarif->time + $old_left
						);
					}

					$STH = $pdo->prepare(
						"UPDATE 
									    admins__services 
									SET 
									    ending_date=:ending_date, 
									    service_time=:service_time, 
									    irretrievable=:irretrievable 
									WHERE id=:id LIMIT 1"
					);
					$STH->execute(
						[
							':ending_date'   => $admin['ending_date'],
							':service_time'  => $reward['tarif'],
							':irretrievable' => $admin['irretrievable'],
							':id'            => $adding_type['extension']
						]
					);

					$prizeMessage .= '&nbsp&nbsp&nbsp Мы продлили Вашу услугу с идентификатором <b>' . $admin['name']
						. '</b> на срок выйгранной услуги.<br>';
				}

				if($AM->checking_server_status($pdo, $reward['server'])) {
					if($rewardInfo->server_type == 1 || $rewardInfo->server_type == 3) {
						$AM->export_to_users_ini($pdo, $reward['server'], 'ACTIVITY_REWARDS');
					} else {
						$AM->export_admin($pdo, $admin['id'], $reward['server'], 'ACTIVITY_REWARDS');
					}
				} else {
					send_noty(
						$pdo,
						"[ACTIVITY_REWARDS]: Не удалось экспортировать администраторов на сервер "
						. $rewardInfo->server_name . ". Восстановите соединение с хранилищем сервера"
						. " и выполните экспорт в админ центре движка.",
						0,
						2
					);
				}

				unset($AM);
			}
			if($reward['type'] == 5) {
				$STH = $pdo->prepare(
					"SELECT id, ip, port, name, bk_host, bk_user, bk_pass, bk_db, bk_code, discount 
								FROM servers 
								WHERE id=:id 
								LIMIT 1"
				);
				$STH->setFetchMode(PDO::FETCH_OBJ);
				$STH->execute([':id' => $reward['server']]);
				$server = $STH->fetch();
				if(!empty($server->id) && !empty($server->bk_host)) {
					if($pdo2 = db_connect($server->bk_host, $server->bk_db, $server->bk_user, $server->bk_pass)) {
						set_names($pdo2, $server->bk_code);

						$key = crate_pass(20, 2);
						$STH = $pdo2->prepare("SELECT key_name FROM table_keys WHERE key_name=:key LIMIT 1");
						$STH->setFetchMode(PDO::FETCH_OBJ);
						$STH->execute([':key' => $key]);
						$row = $STH->fetch();
						if(isset($row->key_name)) {
							$key = crate_pass(21, 2);
						}

						$STH = $pdo2->prepare("SELECT sid FROM keys_servers WHERE address=:address LIMIT 1");
						$STH->setFetchMode(PDO::FETCH_OBJ);
						$STH->execute([':address' => $server->ip . ":" . $server->port]);
						$row = $STH->fetch();
						if(empty($row->sid)) {
							$error = 1;
						} else {
							$sid = $row->sid;
							$STH = $pdo2->prepare(
								"INSERT INTO table_keys (key_name,type,expires,uses,sid,param1,param2,active) 
											VALUES (:key_name, :type, :expires, :uses, :sid, :param1, :param2, :active)"
							);
							$STH->execute(
								[
									':key_name' => $key,
									':type'     => 'vip_add',
									':expires'  => '0',
									':uses'     => '1',
									':sid'      => $sid,
									':param1'   => $rewardInfo->name,
									':param2'   => $rewardInfo->time * 24 * 60 * 60,
									':active'   => '1'
								]
							);
						}

						$prizeMessage .= '&nbsp&nbsp&nbsp Чтобы активировать услугу, зайдите на сервер и введите'
							. ' в консоль <b>key ' . $key . '</b><br>';
					} else {
						$error = 1;
					}
				} else {
					$error = 1;
				}
				if(isset($error) && $error == 1) {
					send_noty(
						$pdo,
						'[ACTIVITY_REWARDS]: Не удалось сгенерировать ключ из buy_key для '
						. 'пользователя <a target="_blank" href="../profile?id=' . $_SESSION['id'] . '">'
						. $_SESSION['login'] . '</a>',
						0,
						2
					);
				}
			}
			if($reward['type'] == 6) {
				$STH = $pdo->prepare(
					"SELECT id, ip, port, name, vk_host, vk_user, vk_pass, vk_db, vk_code, discount 
								FROM servers 
								WHERE id=:id 
								LIMIT 1"
				);
				$STH->setFetchMode(PDO::FETCH_OBJ);
				$STH->execute([':id' => $reward['server']]);
				$server = $STH->fetch();
				if(!empty($server->id) && !empty($server->vk_host)) {
					if(
						$pdo2 = db_connect(
							$server->vk_host,
							$server->vk_db,
							$server->vk_user,
							$server->vk_pass
						)
					) {
						set_names($pdo2, $server->vk_code);

						$key = crate_pass(21, 2);
						$STH = $pdo2->prepare("SELECT key FROM vip_keys_tab WHERE key=:key LIMIT 1");
						$STH->setFetchMode(PDO::FETCH_OBJ);
						$STH->execute([':key' => $key]);
						$row = $STH->fetch();
						if(isset($row->key)) {
							$key = crate_pass(21, 2);
						}

						$STH = $pdo2->prepare(
							"INSERT INTO vip_keys_tab (key,vip_group,vip_min,cmd,active) 
										VALUES (:key, :vip_group, :vip_min, :cmd, :active)"
						);
						$STH->execute(
							[
								':key'       => $key,
								':vip_group' => $rewardInfo->name,
								':vip_min'   => $rewardInfo->time * 24 * 60,
								':cmd'       => '-',
								':active'    => '0'
							]
						);

						$prizeMessage .= '&nbsp&nbsp&nbsp Чтобы активировать услугу, зайдите на сервер и введите'
							. ' в консоль <b>sm_vipkey ' . $key . '</b><br>';
					} else {
						$error = 1;
					}
				} else {
					$error = 1;
				}
				if(isset($error) && $error == 1) {
					send_noty(
						$pdo,
						'[ACTIVITY_REWARDS]: Не удалось сгенерировать ключ из vip_key_ws'
						. ' для пользователя <a target="_blank" href="../profile?id=' . $_SESSION['id'] . '">'
						. $_SESSION['login'] . '</a>',
						0,
						2
					);
				}
			}
			if($reward['type'] == 7) {
				$STH = $pdo->prepare(
					"SELECT id, ip, port, name, vkb_host, vkb_login, vkb_pass, vkb_string, vkb_port, discount 
								FROM servers 
								WHERE id=:id 
								LIMIT 1"
				);
				$STH->setFetchMode(PDO::FETCH_OBJ);
				$STH->execute([':id' => $reward['server']]);
				$server = $STH->fetch();

				$SM = new ServersManager;

				if(
					!empty($server->id)
					&& !empty($server->vkb_host)
					&& (
						$ftp_connection = $SM->ftp_connection(
							$server->vkb_host,
							$server->vkb_port,
							$server->vkb_login,
							$server->vkb_pass,
							'ACTIVITY_REWARDS'
						)
					)
					&& $SM->find_users_file($ftp_connection, $server->vkb_string)
				) {
					$remote_file = $server->vkb_string;
					$local_file  = $_SERVER["DOCUMENT_ROOT"] . '/files/temp/vip_key_vkb' . rand() . '.txt';

					if(
						($file = fopen($local_file, 'w+'))
						&& ftp_fget($ftp_connection, $file, $remote_file, FTP_ASCII, 0)
					) {
						$STH = $pdo->prepare(
								"SELECT $params[2].flags 
											FROM $params[2] 
											WHERE $params[2].id=:service 
											LIMIT 1"
						);
						$STH->setFetchMode(PDO::FETCH_OBJ);
						$STH->execute([':service' => $reward['service']]);
						$flags = $STH->fetch();

						$key   = crate_pass(8, 1);
						$time  = $rewardInfo->time * 24;
						$flags = $flags->flags;

						if($time == 0) {
							$time = 'never';
						}

						fwrite($file, "\r\n" . '"' . $key . '"="' . $flags . '"="' . $time . '"');
						fclose($file);

						$file = fopen($local_file, 'r');
						if(ftp_fput($ftp_connection, $remote_file, $file, FTP_ASCII, 0)) {
							fclose($file);
							unlink($local_file);
							$SM->close_ftp($ftp_connection);
							$prizeMessage .= '&nbsp&nbsp&nbsp Чтобы активировать услугу, зайдите на сервер и введите'
								. ' в консоль <b>vip_code ' . $key . '</b><br>';
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
					send_noty(
						$pdo,
						'[ACTIVITY_REWARDS]: Не удалось сгенерировать ключ из vip_key для'
						. ' пользователя <a target="_blank" href="../profile?id=' . $_SESSION['id'] . '">'
						. $_SESSION['login'] . '</a>',
						0,
						2
					);
				}
			}
		}
		if($reward['type'] == 2) {
			$prizeMessage .= ' - Деньги на Ваш баланс: <b>' . $reward['money'] . '</b> рублей<br>';

			$STH = $pdo->prepare("SELECT id, shilings FROM users WHERE id=:id LIMIT 1");
			$STH->setFetchMode(PDO::FETCH_OBJ);
			$STH->execute([':id' => $_SESSION['id']]);
			$row = $STH->fetch();
			if(!empty($row->id)) {
				$STH = $pdo->prepare("UPDATE users SET shilings=:shilings WHERE id=:id LIMIT 1");
				$STH->execute([':id' => $_SESSION['id'], 'shilings' => $row->shilings + $reward['money']]);
				$STH = $pdo->prepare(
					"INSERT INTO money__actions (date,shilings,author,type) 
								VALUES (:date, :shilings, :author, :type)"
				);
				$STH->execute(
					[
						'date'     => date("Y-m-d H:i:s"),
						'shilings' => $reward['money'],
						'author'   => $_SESSION['id'],
						'type'     => 21
					]
				);
			}
		}
		if($reward['type'] == 3) {
			$prizeMessage .= ' - Скидка на все услуги: <b>' . $reward['percent'] . '%</b><br>';

			$STH = $pdo->prepare("UPDATE users SET proc=:proc WHERE id=:id LIMIT 1");
			$STH->execute([':id' => $_SESSION['id'], 'proc' => $reward['percent']]);
		}
		if($reward['type'] == 4) {
			$STH = $pdo->prepare(
				"SELECT 
							    servers.name AS server_name, 
							    sk_services.number, sk_services.type 
							FROM 
							    sk_services 
									LEFT JOIN servers ON servers.id=sk_services.server 
							WHERE 
							    sk_services.id=:tarif 
							  AND servers.id=:server 
							LIMIT 1"
			);
			$STH->setFetchMode(PDO::FETCH_OBJ);
			$STH->execute([':server' => $reward['server'], ':tarif' => $reward['tarif']]);
			$rewardInfo = $STH->fetch();

			$prizeMessage .= ' - ' . $services_data[$rewardInfo->type]['name'] . ': <b>' . $rewardInfo->number
				. '</b> на сервере <b>' . $rewardInfo->server_name . '</b><br>';

			$STH = $pdo->prepare(
				"SELECT id, ip, port, name, sk_host, sk_user, sk_pass, sk_db, sk_code, discount 
							FROM servers 
							WHERE id=:id 
							LIMIT 1"
			);
			$STH->setFetchMode(PDO::FETCH_OBJ);
			$STH->execute([':id' => $reward['server']]);
			$server = $STH->fetch();
			if(!empty($server->id) && !empty($server->sk_host)) {
				if($pdo2 = db_connect($server->sk_host, $server->sk_db, $server->sk_user, $server->sk_pass)) {
					set_names($pdo2, $server->sk_code);

					$key = crate_pass(20, 2);
					$STH = $pdo2->prepare("SELECT key_name FROM table_keys WHERE key_name=:key LIMIT 1");
					$STH->setFetchMode(PDO::FETCH_OBJ);
					$STH->execute([':key' => $key]);
					$row = $STH->fetch();
					if(isset($row->key_name)) {
						$key = crate_pass(21, 2);
					}

					$STH = $pdo2->prepare("SELECT sid FROM keys_servers WHERE address=:address LIMIT 1");
					$STH->setFetchMode(PDO::FETCH_OBJ);
					$STH->execute([':address' => $server->ip . ":" . $server->port]);
					$row = $STH->fetch();
					if(empty($row->sid)) {
						$error = 1;
					} else {
						$sid = $row->sid;
						$STH = $pdo2->prepare(
							"INSERT INTO table_keys (key_name,type,expires,uses,sid,param1,active) 
										VALUES (:key_name, :type, :expires, :uses, :sid, :param1, :active)"
						);
						$STH->execute(
							[
								':key_name' => $key,
								':type'     => $services_data[$rewardInfo->type]['type'],
								':expires'  => '0',
								':uses'     => '1',
								':sid'      => $sid,
								':param1'   => $rewardInfo->number,
								':active'   => '1'
							]
						);

						$prizeMessage .= '&nbsp&nbsp&nbsp Чтобы активировать, зайдите на сервер и введите '
							. 'в консоль <b>key ' . $key . '</b><br>';
					}
				} else {
					$error = 1;
				}
			} else {
				$error = 1;
			}
			if(isset($error) && $error == 1) {
				send_noty(
					$pdo,
					'[ACTIVITY_REWARDS]: Не удалось сгенерировать ключ из shop_key для пользователя '
					. '<a target="_blank" href="../profile?id=' . $_SESSION['id'] . '">' . $_SESSION['login'] . '</a>',
					0,
					2
				);
			}
		}

		send_noty($pdo, $noty . $prizeMessage, $_SESSION['id'], 2);

		?>
		<div id="activity-reward" class="modal fade">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
						<h4 class="modal-title">Награды за активность</h4>
					</div>
					<div class="modal-body">
						<?php echo $noty . $prizeMessage; ?>
					</div>
				</div>
			</div>
		</div>
		<script>$('#activity-reward').modal('show');</script>
		<?php
	}

	exit();
}

if(isset($_POST['getRewardsWidget'])) {
	if(is_auth()) {
		$STH = $pdo->prepare("SELECT days_in_a_row, days_in_a_row_max FROM activity_rewards__participants WHERE user_id=:user_id LIMIT 1");
		$STH->setFetchMode(PDO::FETCH_OBJ);
		$STH->execute([':user_id' => $_SESSION['id']]);
		$row = $STH->fetch();
	}

	$daysInARow = (empty($row->days_in_a_row_max)) ? 1 : $row->days_in_a_row_max;

	$STHRewards = $pdo->query("SELECT * FROM activity_rewards ORDER BY days_in_a_row");
	$STHRewards->setFetchMode(PDO::FETCH_OBJ);
	while($row = $STHRewards->fetch()) {
		$rewardsTypes = getRewardsTypes($pdo);
		$reward = unserialize($row->reward);
		?>
		<div class="reward <?php if($daysInARow >= $row->days_in_a_row) { echo 'active'; } ?>">
			<div class="received">Получено</div>
			<div class="reward-day">
				<span><?php echo $row->days_in_a_row; ?></span>
				<i>дня(ей)</i>
			</div>
			<div class="reward-content">
			<?php
			if($reward['type'] == 1 || $reward['type'] == 5 || $reward['type'] == 6 || $reward['type'] == 7) {
				$params = getTypesParams($reward['type']);

				$STH = $pdo->prepare(
					"SELECT 
								    servers.name AS server_name,
								    $params[2].name,
								    $params[2].text, 
								    $params[3].time 
								FROM 
								    $params[2] 
										LEFT JOIN $params[3] ON $params[3].service=$params[2].id
										LEFT JOIN servers ON servers.id=$params[2].server 
								WHERE 
								    $params[2].id=:service 
								  AND servers.id=:server 
								  AND $params[3].id=:tarif 
								LIMIT 1"
				);
				$STH->setFetchMode(PDO::FETCH_OBJ);
				$STH->execute(
					[
						':service' => $reward['service'],
						':server'  => $reward['server'],
						':tarif'   => $reward['tarif']
					]
				);
				$row = $STH->fetch();

				if($row->time == 0) {
					$row->time = 'Навсегда';
				} else {
					$row->time = $row->time . ' дня(ей)';
				}
				?>
					<span>
						Услуга: <?php echo $row->name; ?>
					</span>
					<span>
						<small>Тариф: <?php echo $row->time; ?>, Сервер: <?php echo $row->server_name; ?></small>
					</span>
				<?php
			}
			if($reward['type'] == 2) {
				?>
					<span>
						<?php echo $reward['money']; ?> рублей
					</span>
					<span>
						<small>На Ваш баланс</small>
					</span>
				<?php
			}
			if($reward['type'] == 3) {
				?>
					<span>
						<?php echo $reward['percent']; ?>% скидка
					</span>
					<span>
						<small>На все услуги</small>
					</span>
				<?php
			}
			if($reward['type'] == 4) {
				$STH = $pdo->prepare(
					"SELECT 
								    servers.name AS server_name, 
								    sk_services.number, 
								    sk_services.type 
								FROM sk_services 
								    LEFT JOIN servers ON servers.id=sk_services.server 
								WHERE sk_services.id=:tarif AND servers.id=:server 
								LIMIT 1"
				);
				$STH->setFetchMode(PDO::FETCH_OBJ);
				$STH->execute([':server' => $reward['server'], ':tarif' => $reward['tarif']]);
				$row = $STH->fetch();
				?>
					<span>
						<?php echo $row->number; ?> (<?php echo $services_data[$row->type]['name']; ?>)
					</span>
					<span>
						<small>Сервер: <?php echo $row->server_name; ?></small>
					</span>
				<?php
			}
			?>
			</div>
		</div>
		<?php
	}
	exit();
}

if(isset($_POST['getRewardsBanner'])) {
	$tpl = new Template;
	$tpl->dir = '../../../templates/'.$conf->template.'/tpl/';
	$tpl->load_template($module['tpl_dir']."banner.tpl");
	$tpl->set("{template}", $conf->template);
	$tpl->compile('content');
	$tpl->show($tpl->result['content']);
	$tpl->global_clear();
	exit();
}

if(!is_admin()) {
	exit(json_encode(['status' => '2', 'data' => 'Досутпно только администратору']));
}
if(isset($_POST['getRewards'])) {
	$lastId = -1;
	$rewardsTypes = getRewardsTypes($pdo);

	$STHRewards = $pdo->query("SELECT * FROM activity_rewards ORDER BY days_in_a_row");
	$STHRewards->setFetchMode(PDO::FETCH_OBJ);
	while($row = $STHRewards->fetch()) {
		$reward = unserialize($row->reward);
		?>
		<div class="card" id="reward<?php echo $row->id; ?>">
			<div class="card-header">
				<span>Награда</span>
                <a class="btn btn-danger btn-sm" onclick="dellReward(<?php echo $row->id; ?>)">Удалить</a>
            </div>
            <div class="card-body">
				<p class="card-text">Количество дней, которое пользователь должен заходить подряд</p>
				<input class="form-control" id="day-in-row<?php echo $row->id; ?>" name="day-in-row<?php echo $row->id; ?>" value="<?php echo $row->days_in_a_row; ?>" placeholder="Количество дней" type="number">
				<p class="card-text">Тип награды</p>
				<select class="form-control" id="type<?php echo $row->id; ?>" name="type<?php echo $row->id; ?>" onchange="getRewardLine(<?php echo $row->id; ?>)">
					<?php if($rewardsTypes[1] == 1) { ?>
						<option <?php if($reward['type'] == 1) {echo 'selected';} ?> value="1">Услугу</option>
					<?php } ?>
					<?php if($rewardsTypes[2] == 1) { ?>
						<option <?php if($reward['type'] == 2) {echo 'selected';} ?> value="2">Денежный приз</option>
					<?php } ?>
					<?php if($rewardsTypes[3] == 1) { ?>
						<option <?php if($reward['type'] == 3) {echo 'selected';} ?> value="3">Скидку</option>
					<?php } ?>
					<?php if($rewardsTypes[4] == 1) { ?>
						<option <?php if($reward['type'] == 4) {echo 'selected';} ?> value="4">Приз из shop_key (Riko)</option>
					<?php } ?>
					<?php if($rewardsTypes[5] == 1) { ?>
						<option <?php if($reward['type'] == 5) {echo 'selected';} ?> value="5">Приз из buy_key (Riko)</option>
					<?php } ?>
					<?php if($rewardsTypes[6] == 1) { ?>
						<option <?php if($reward['type'] == 6) {echo 'selected';} ?> value="6">Приз из vip_key (Riko)</option>
					<?php } ?>
					<?php if($rewardsTypes[7] == 1) { ?>
						<option <?php if($reward['type'] == 7) {echo 'selected';} ?> value="7">Приз из vip_key (MyArena)</option>
					<?php } ?>
				</select>
				<p class="card-text">Награда</p>
				<div class="input-group w-100" id="reward-line<?php echo $row->id; ?>">
				<?php
				if($reward['type'] == 1 || $reward['type'] == 5 || $reward['type'] == 6 || $reward['type'] == 7) {
					$params = getTypesParams($reward['type']);
					$STH    = $pdo->query("SELECT id,name FROM servers WHERE $params[1]!='0'");
					$STH->execute();
					$servers = $STH->fetchAll();
					if(count($servers) == 0) {
						?>
							<span class="input-group-btn w-33">
								<select name="server<?php echo $row->id; ?>" id="server<?php echo $row->id; ?>" class="form-control"></select>
							</span>
							<span class="input-group-btn w-33">
								<select name="service<?php echo $row->id; ?>" id="service<?php echo $row->id; ?>" class="form-control"></select>
							</span>
							<span class="input-group-btn w-33">
								<select name="tarif<?php echo $row->id; ?>" id="tarif<?php echo $row->id; ?>" class="form-control"></select>
							</span>
						<?php
					} else {
						$srv = $reward['server'];
						$STH = $pdo->query("SELECT id, name FROM $params[2] WHERE server='$srv'");
						$STH->execute();
						$services = $STH->fetchAll();
						?>
							<span class="input-group-btn w-33">
								<select name="server<?php echo $row->id; ?>" id="server<?php echo $row->id; ?>" class="form-control" onchange="getServicesReward(<?php echo $row->id; ?>, <?php echo $reward['type']; ?>);">
									<?php for($l = 0; $l < count($servers); $l++) { ?>
										<?php if($servers[$l]['id'] == $reward['server']) { ?>
											<option value="<?php echo $servers[$l]['id']; ?>" selected><?php echo $servers[$l]['name']; ?></option>
										<?php } else { ?>
											<option value="<?php echo $servers[$l]['id']; ?>"><?php echo $servers[$l]['name']; ?></option>
										<?php } ?>
									<?php } ?>
								</select>
							</span>
							<span class="input-group-btn w-33">
								<select name="service<?php echo $row->id; ?>" id="service<?php echo $row->id; ?>" class="form-control" onchange="getTariffsReward(<?php echo $row->id; ?>, <?php echo $reward['type']; ?>);">
									<?php for($l = 0; $l < count($services); $l++) { ?>
										<?php if($services[$l]['id'] == $reward['service']) { ?>
											<option value="<?php echo $services[$l]['id']; ?>" selected><?php echo $services[$l]['name']; ?></option>
										<?php } else { ?>
											<option value="<?php echo $services[$l]['id']; ?>"><?php echo $services[$l]['name']; ?></option>
										<?php } ?>
									<?php } ?>
								</select>
							</span>
							<span class="input-group-btn w-33">
								<select name="tarif<?php echo $row->id; ?>" id="tarif<?php echo $row->id; ?>" class="form-control">
									<?php
									if(count($services) != 0) {
										$srv = $reward['service'];
										$STH = $pdo->query(
											"SELECT id, time FROM $params[3] WHERE service='$srv' ORDER BY time"
										);
										$STH->execute();
										$tarifs = $STH->fetchAll();
										for($l = 0; $l < count($tarifs); $l++) {
											if($tarifs[$l]['time'] == 0) {
												$tarifs[$l]['time'] = 'Навсегда';
											} else {
												$tarifs[$l]['time'] = $tarifs[$l]['time'] . ' дня(ей)';
											}
											?>
											<?php if($tarifs[$l]['id'] == $reward['tarif']) { ?>
												<option value="<?php echo $tarifs[$l]['id']; ?>" selected><?php echo $tarifs[$l]['time']; ?></option>
											<?php } else { ?>
												<option value="<?php echo $tarifs[$l]['id']; ?>"><?php echo $tarifs[$l]['time']; ?></option>
											<?php } ?>
											<?php
										}
									}
									?>
								</select>
							</span>
						<?php
					}
				}
				if($reward['type'] == 2) {
					?>
						<span class="input-group-btn w-100">
							<input class="form-control" name="money<?php echo $row->id; ?>" id="money<?php echo $row->id; ?>" placeholder="Сумма" value="<?php echo $reward['money']; ?>" type="number">
						</span>
					<?php
				}
				if($reward['type'] == 3) {
					?>
						<span class="input-group-btn w-100">
							<input class="form-control" name="percent<?php echo $row->id; ?>" id="percent<?php echo $row->id; ?>" placeholder="Значение в %" value="<?php echo $reward['percent']; ?>" type="number" maxlength="2">
						</span>
					<?php
				}
				if($reward['type'] == 4) {
					$STH = $pdo->query("SELECT id,name FROM servers WHERE sk_host!='0'");
					$STH->execute();
					$servers = $STH->fetchAll();
					if(count($servers) == 0) {
						?>
							<span class="input-group-btn w-50">
								<select name="server<?php echo $row->id; ?>" id="server<?php echo $row->id; ?>" class="form-control"></select>
							</span>
							<span class="input-group-btn w-50">
								<select name="tarif<?php echo $row->id; ?>" id="tarif<?php echo $row->id; ?>" class="form-control"></select>
							</span>
						<?php
					} else {
						$srv = $reward['server'];
						$STH = $pdo->query(
							"SELECT id, number, type FROM sk_services WHERE server='$srv' ORDER BY type"
						);
						$STH->execute();
						$services = $STH->fetchAll();
						?>
							<span class="input-group-btn w-50">
								<select name="server<?php echo $row->id; ?>" id="server<?php echo $row->id; ?>" class="form-control" onchange="getShopKeyServicesReward(<?php echo $row->id; ?>, <?php echo $reward['type']; ?>);">
									<?php for($l = 0; $l < count($servers); $l++) { ?>
										<?php if($servers[$l]['id'] == $reward['server']) { ?>
											<option value="<?php echo $servers[$l]['id']; ?>" selected><?php echo $servers[$l]['name']; ?></option>
										<?php } else { ?>
											<option value="<?php echo $servers[$l]['id']; ?>"><?php echo $servers[$l]['name']; ?></option>
										<?php } ?>
									<?php } ?>
								</select>
							</span>
							<span class="input-group-btn w-50">
								<select name="tarif<?php echo $row->id; ?>" id="tarif<?php echo $row->id; ?>" class="form-control">
									<?php for($l = 0; $l < count($services); $l++) { ?>
										<?php if($services[$l]['id'] == $reward['tarif']) { ?>
											<option value="<?php echo $services[$l]['id']; ?>" selected><?php echo $services[$l]['number']; ?> (<?php echo $services_data[$services[$l]['type']]['name']; ?>)</option>
										<?php } else { ?>
											<option value="<?php echo $services[$l]['id']; ?>"><?php echo $services[$l]['number']; ?> (<?php echo $services_data[$services[$l]['type']]['name']; ?>)</option>
										<?php } ?>
									<?php } ?>
								</select>
							</span>
						<?php
					}
				}
				?>
				</div>
			</div>
		</div>
		<?php
		$lastId = $row->id;
	}
	?>
	<script>
	  <?php if($lastId == -1) { ?>
        addReward();
	  <?php } else { ?>
      $('#rewards-last-id').val(<?php echo $lastId + 1; ?>);
      <?php } ?>
	</script>
	<?php
	exit();
}
if(isset($_POST['getRewardLine'])) {
	$rewardType = check($_POST['rewardType'], "int");
	$rewardId   = check($_POST['rewardId'], "int");

	if(empty($rewardType)) {
		$rewardType = 1;
	}
	if(empty($rewardId)) {
		exit();
	}

	if($rewardType == 1 || $rewardType == 5 || $rewardType == 6 || $rewardType == 7) {
		$params = getTypesParams($rewardType);
		$STH    = $pdo->query("SELECT id,name FROM servers WHERE $params[1]!='0'");
		$STH->execute();
		$servers = $STH->fetchAll();
		if(count($servers) == 0) {
			?>
				<span class="input-group-btn w-33">
					<select name="server<?php echo $rewardId; ?>" id="server<?php echo $rewardId; ?>" class="form-control"></select>
				</span>
				<span class="input-group-btn w-33">
					<select name="service<?php echo $rewardId; ?>" id="service<?php echo $rewardId; ?>" class="form-control"></select>
				</span>
				<span class="input-group-btn w-33">
					<select name="tarif<?php echo $rewardId; ?>" id="tarif<?php echo $rewardId; ?>" class="form-control"></select>
				</span>
			<?php
		} else {
			$j   = $servers['0']['id'];
			$STH = $pdo->query("SELECT id, name FROM $params[2] WHERE server='$j'");
			$STH->execute();
			$services = $STH->fetchAll();
			?>
				<span class="input-group-btn w-33">
					<select name="server<?php echo $rewardId; ?>" id="server<?php echo $rewardId; ?>" class="form-control" onchange="getServicesReward(<?php echo $rewardId; ?>, <?php echo $rewardType; ?>);">
						<?php for($i = 0; $i < count($servers); $i++) { ?>
							<option value="<?php echo $servers[$i]['id']; ?>"><?php echo $servers[$i]['name']; ?></option>
						<?php } ?>
					</select>
				</span>
				<span class="input-group-btn w-33">
					<select name="service<?php echo $rewardId; ?>" id="service<?php echo $rewardId; ?>" class="form-control" onchange="getTariffsReward(<?php echo $rewardId; ?>, <?php echo $rewardType; ?>);">
						<?php for($i = 0; $i < count($services); $i++) { ?>
							<option value="<?php echo $services[$i]['id']; ?>"><?php echo $services[$i]['name']; ?></option>
						<?php } ?>
					</select>
				</span>
				<span class="input-group-btn w-33">
					<select name="tarif<?php echo $rewardId; ?>" id="tarif<?php echo $rewardId; ?>" class="form-control">
						<?php
						if(count($services) != 0) {
							$j   = $services['0']['id'];
							$STH = $pdo->query(
								"SELECT id, time FROM $params[3] WHERE service='$j' ORDER BY time"
							);
							$STH->execute();
							$tarifs = $STH->fetchAll();
							for($i = 0; $i < count($tarifs); $i++) {
								if($tarifs[$i]['time'] == 0) {
									$tarifs[$i]['time'] = 'Навсегда';
								} else {
									$tarifs[$i]['time'] = $tarifs[$i]['time'] . ' дня(ей)';
								}
								?>
								<option value="<?php echo $tarifs[$i]['id']; ?>"><?php echo $tarifs[$i]['time']; ?></option>
								<?php
							}
						}
						?>
					</select>
				</span>
			<?php
		}
	}
	if($rewardType == 2) {
		?>
			<span class="input-group-btn w-100">
				<input class="form-control" name="money<?php echo $rewardId; ?>" id="money<?php echo $rewardId; ?>" placeholder="Сумма" value="" type="number">
			</span>
		<?php
	}
	if($rewardType == 3) {
		?>
			<span class="input-group-btn w-100">
				<input class="form-control" name="percent<?php echo $rewardId; ?>" id="percent<?php echo $rewardId; ?>" placeholder="Значение в %" value="" type="number" maxlength="2">
			</span>
		<?php
	}
	if($rewardType == 4) {
		$STH = $pdo->query("SELECT id, name FROM servers WHERE sk_host!='0'");
		$STH->execute();
		$servers = $STH->fetchAll();
		if(count($servers) == 0) {
			?>
				<span class="input-group-btn w-50">
					<select name="server<?php echo $rewardId; ?>" id="server<?php echo $rewardId; ?>" class="form-control"></select>
				</span>
				<span class="input-group-btn w-50">
					<select name="tarif<?php echo $rewardId; ?>" id="tarif<?php echo $rewardId; ?>" class="form-control"></select>
				</span>
			<?php
		} else {
			$j   = $servers['0']['id'];
			$STH = $pdo->query("SELECT id, number, type FROM sk_services WHERE server='$j' ORDER BY type");
			$STH->execute();
			$services = $STH->fetchAll();
			?>
				<span class="input-group-btn w-50">
					<select name="server<?php echo $rewardId; ?>" id="server<?php echo $rewardId; ?>" class="form-control" onchange="getShopKeyServicesReward(<?php echo $rewardId; ?>, <?php echo $rewardType; ?>);">
						<?php for($i = 0; $i < count($servers); $i++) { ?>
							<option value="<?php echo $servers[$i]['id']; ?>"><?php echo $servers[$i]['name']; ?></option>
						<?php } ?>
					</select>
				</span>
				<span class="input-group-btn w-50">
					<select name="tarif<?php echo $rewardId; ?>" id="tarif<?php echo $rewardId; ?>" class="form-control">
						<?php for($i = 0; $i < count($services); $i++) { ?>
							<option value="<?php echo $services[$i]['id']; ?>"><?php echo $services[$i]['number']; ?> (<?php echo $services_data[$services[$i]['type']]['name']; ?>)</option>
						<?php } ?>
					</select>
				</span>
			<?php
		}
	}
	exit();
}
if(isset($_POST['getServicesReward'])) {
	$type   = check($_POST['type'], "int");
	$server = check($_POST['server'], "int");
	if(empty($server) || empty($type)) {
		exit();
	}

	$params = getTypesParams($type);
	$STH    = $pdo->query("SELECT id, name FROM $params[2] WHERE server='$server'");
	$STH->execute();
	$services = $STH->fetchAll();
	for($i = 0; $i < count($services); $i++) {
		?>
		<option value="<?php echo $services[$i]['id']; ?>"><?php echo $services[$i]['name']; ?></option>
		<?php
	}
	exit();
}
if(isset($_POST['getShopKeyServicesReward'])) {
	$server = check($_POST['server'], "int");
	if(empty($server)) {
		exit();
	}

	$STH = $pdo->query("SELECT id, number, type FROM sk_services WHERE server='$server' ORDER BY type");
	$STH->execute();
	$services = $STH->fetchAll();
	for($i = 0; $i < count($services); $i++) {
		?>
		<option value="<?php echo $services[$i]['id']; ?>"><?php echo $services[$i]['number']; ?> (<?php echo $services_data[$services[$i]['type']]['name']; ?>)</option>
		<?php
	}
	if(count($services) == 0) {
		?>
		<option value="0">Товара нет</option>
		<?php
	}
	exit();
}
if(isset($_POST['getTariffsReward'])) {
	$type    = check($_POST['type'], "int");
	$service = check($_POST['service'], "int");
	if(empty($service) || empty($type)) {
		exit();
	}

	$params = getTypesParams($type);
	$STH    = $pdo->query("SELECT id, time FROM $params[3] WHERE service='$service' ORDER BY time");
	$STH->execute();
	$tarifs = $STH->fetchAll();
	for($i = 0; $i < count($tarifs); $i++) {
		if($tarifs[$i]['time'] == 0) {
			$tarifs[$i]['time'] = 'Навсегда';
		} else {
			$tarifs[$i]['time'] = $tarifs[$i]['time'] . ' дня(ей)';
		}
		?>
		<option value="<?php echo $tarifs[$i]['id']; ?>"><?php echo $tarifs[$i]['time']; ?></option>
		<?php
	}
	exit();
}
if(isset($_POST['saveRewards'])) {

	$rewards = [];
	foreach($_POST as $key => $value) {
		if(substr($key, 0, 4) == 'type') {
			$id = substr($key, 4);
			$rewards[$id]['id']            = $id;
			$rewards[$id]['type']          = check($value, "int");

			if(!empty($_POST['day-in-row' . $id])) {
				$rewards[$id]['day-in-row'] = check($_POST['day-in-row' . $id], "int");

				if($rewards[$id]['day-in-row'] < 2) {
					exit (json_encode(['status' => '2', 'input' => 'day-in-row' . $id, 'reply' => 'Значение должно быть не менее 2']));
				}
			} else {
				exit (json_encode(['status' => '2', 'input' => 'day-in-row' . $id, 'reply' => 'Заполните']));
			}

			if($value == 1 || $value == 5 || $value == 6 || $value == 7) {
				if(!empty($_POST['server' . $id])) {
					$rewards[$id]['server'] = check($_POST['server' . $id], "int");
				} else {
					exit (json_encode(['status' => '2', 'input' => 'server' . $id, 'reply' => 'Заполните']));
				}
				if(!empty($_POST['service' . $id])) {
					$rewards[$id]['service'] = check($_POST['service' . $id], "int");
				} else {
					exit (json_encode(['status' => '2', 'input' => 'service' . $id, 'reply' => 'Заполните']));
				}
				if(!empty($_POST['tarif' . $id])) {
					$rewards[$id]['tarif'] = check($_POST['tarif' . $id], "int");
				} else {
					exit (json_encode(['status' => '2', 'input' => 'tarif' . $id, 'reply' => 'Заполните']));
				}
			}
			if($value == 2) {
				if(!empty($_POST['money' . $id])) {
					$rewards[$id]['money'] = check($_POST['money' . $id], "int");
				} else {
					exit (json_encode(['status' => '2', 'input' => 'money' . $id, 'reply' => 'Заполните']));
				}
			}
			if($value == 3) {
				if(!empty($_POST['percent' . $id])) {
					$rewards[$id]['percent'] = check($_POST['percent' . $id], "int");
					if($rewards[$id]['percent'] > 99) {
						exit (json_encode(['status' => '2', 'input' => 'percent' . $id, 'reply' => 'Не более 99']));
					}
				} else {
					exit (json_encode(['status' => '2', 'input' => 'percent' . $id, 'reply' => 'Заполните']));
				}
			}
			if($value == 4) {
				if(!empty($_POST['server' . $id])) {
					$rewards[$id]['server'] = check($_POST['server' . $id], "int");
				} else {
					exit (json_encode(['status' => '2', 'input' => 'server' . $id, 'reply' => 'Заполните']));
				}
				if(!empty($_POST['tarif' . $id])) {
					$rewards[$id]['tarif'] = check($_POST['tarif' . $id], "int");
				} else {
					exit (json_encode(['status' => '2', 'input' => 'tarif' . $id, 'reply' => 'Заполните']));
				}
			}
		}
	}

	foreach($rewards as $id => $reward) {
		if(array_search($reward['day-in-row'], array_column($rewards, 'day-in-row', 'id')) != $id) {
			exit (json_encode(['status' => '2', 'input' => 'day-in-row' . $id, 'reply' => 'Значение не должно совпадать со значением другой награды']));
		}
	}

	$pdo->exec("DELETE FROM activity_rewards");

	foreach($rewards as $id => $reward) {
		$STH = $pdo->prepare("INSERT INTO activity_rewards (days_in_a_row, reward) VALUES (:days_in_a_row, :reward)");
		$STH->execute([':days_in_a_row' => $reward['day-in-row'], ':reward' => serialize($reward)]);
	}

	exit(json_encode(['status' => 1]));
}
if(isset($_POST['dellActivityRewards'])) {
	$pdo->exec("DELETE FROM activity_rewards__participants");
	exit(json_encode(['status' => 1]));
}
if(isset($_POST['getActivityRewardsProgress'])) {
	$partNumber = checkJs($_POST['partNumber'], "int");

	if(empty($partNumber)) {
		$partNumber = 1;
	}

	$limit = 30;
	$start = ($partNumber - 1) * $limit;
	$i = $start;
	$l = 0;

	$STH = $pdo->query("SELECT * FROM activity_rewards ORDER BY days_in_a_row");
	$STH->setFetchMode(PDO::FETCH_OBJ);
	$rewards = $STH->fetchAll();
	$rewardsTypes = getRewardsTypes($pdo);

	$STHProgress = $pdo->query("SELECT 
								    activity_rewards__participants.*, 
								    users.login, 
								    users.avatar 
								FROM activity_rewards__participants
									LEFT JOIN users ON activity_rewards__participants.user_id = users.id
									ORDER BY activity_rewards__participants.days_in_a_row DESC LIMIT ".$start.", ".$limit);
	$STHProgress->setFetchMode(PDO::FETCH_OBJ);
	while($progress = $STHProgress->fetch()) {
		$i++;
		$l++;
		?>
		<tr>
			<td><?php echo $i; ?></td>
			<td>
				<a target="_blank" href="../admin/edit_user?id=<?php echo $progress->user_id ?>">
					<img src="../<?php echo $progress->avatar ?>" alt="<?php echo $progress->login ?>" width="30" height="30" class="brRs-50 mr-5"> <?php echo $progress->login ?>
				</a>
			</td>
			<td><?php echo $progress->days_in_a_row; ?></td>
			<td>
				<?php
					if(empty($rewards) || $progress->days_in_a_row_max < $rewards[0]->days_in_a_row) {
						echo 'Не получались';
					} else {
				?>
				<button class="btn btn-default btn-sm" data-target="#rewards<?php echo $progress->user_id ?>" data-toggle="modal">
					Отобразить
				</button>

				<div id="rewards<?php echo $progress->user_id ?>" class="modal fade">
					<div class="modal-dialog modal-lg">
						<div class="modal-content">
							<div class="modal-header">
								<h4 class="modal-title">Награды пользователя <?php echo $progress->login ?></h4>
							</div>
							<div class="modal-body">
								<div class="table-responsive mb-0">
									<table class="table table-bordered v-m">
										<thead>
										<tr>
											<td>За который день</td>
											<td>Награда</td>
										</tr>
										</thead>
										<tbody>
								<?php
								foreach($rewards as $rewardItem) {
									if($rewardItem->days_in_a_row > $progress->days_in_a_row_max) {
										break;
									}

									$reward = unserialize($rewardItem->reward);
									?>
									<tr>
										<td><?php echo $rewardItem->days_in_a_row; ?></td>
										<td>
											<?php
											if($reward['type'] == 1 || $reward['type'] == 5 || $reward['type'] == 6 || $reward['type'] == 7) {
												$params = getTypesParams($reward['type']);

												$STH = $pdo->prepare(
													"SELECT 
																    servers.name AS server_name,
																    $params[2].name,
																    $params[2].text, 
																    $params[3].time 
																FROM 
																    $params[2] 
																		LEFT JOIN $params[3] ON $params[3].service=$params[2].id
																		LEFT JOIN servers ON servers.id=$params[2].server 
																WHERE 
																    $params[2].id=:service 
																  AND servers.id=:server 
																  AND $params[3].id=:tarif 
																LIMIT 1"
												);
												$STH->setFetchMode(PDO::FETCH_OBJ);
												$STH->execute(
													[
														':service' => $reward['service'],
														':server'  => $reward['server'],
														':tarif'   => $reward['tarif']
													]
												);
												$row = $STH->fetch();

												if($row->time == 0) {
													$row->time = 'Навсегда';
												} else {
													$row->time = $row->time . ' дня(ей)';
												}
												?>
												Услуга: <?php echo $row->name; ?>
												<small>(Тариф: <?php echo $row->time; ?>, Сервер: <?php echo $row->server_name; ?>)</small>
												<?php
											}
											if($reward['type'] == 2) {
												?>
												<?php echo $reward['money']; ?> рублей
												<small>(На баланс)</small>
												<?php
											}
											if($reward['type'] == 3) {
												?>
												<?php echo $reward['percent']; ?>% скидка
												<small>На все услуги</small>
												<?php
											}
											if($reward['type'] == 4) {
												$STH = $pdo->prepare(
													"SELECT 
																    servers.name AS server_name, 
																    sk_services.number, 
																    sk_services.type 
																FROM sk_services 
																    LEFT JOIN servers ON servers.id=sk_services.server 
																WHERE sk_services.id=:tarif AND servers.id=:server 
																LIMIT 1"
												);
												$STH->setFetchMode(PDO::FETCH_OBJ);
												$STH->execute([':server' => $reward['server'], ':tarif' => $reward['tarif']]);
												$row = $STH->fetch();
												?>
												<?php echo $row->number; ?> (<?php echo $services_data[$row->type]['name']; ?>)
												<small>Сервер: <?php echo $row->server_name; ?></small>
												<?php
											}
											?>
										</td>
									</tr>
									<?php
								}
								?>
										</tbody>
									</table>
								</div>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
							</div>
						</div>
					</div>
				</div>
				<?php
					}
				?>
			</td>
			<td>

				<?php
				if(strtotime(date('Y-m-d')) - $progress->last_activity > 24 * 60 * 60) {
					$class = 'text-danger';
					$title = 'Прогресс будет сброшен';
				} else {
					$class = 'text-success';
					$title = 'Прогресс сохраняется';
				}
				?>
				<span class="<?php echo $class; ?> m-0" title="<?php echo $title; ?>" tooltip="yes">
					<?php echo expand_date($progress->last_activity, 2); ?>
				</span>
			</td>
		</tr>
		<?php
	}
	if($i == 0) {
		exit ('<tr><td colspan="10">Пусто</td></tr>');
	}
	if(($partNumber > 0) and ($l > $limit - 1)) {
		$partNumber++;
		exit ('<tr id="loader'.$partNumber.'" class="c-p" onclick="getActivityRewardsProgress(\''.$partNumber.'\');"><td colspan="10">Подгрузить записи</td></tr>');
	}
	exit();
}

if(isset($_POST['saveActivityRewardsConfig'])) {
	$isReIssue           = checkJs($_POST['isReIssue'], "int");
	$isNeedMoneyActivity = checkJs($_POST['isNeedMoneyActivity'], "int");
	$amountOfMoney       = checkJs($_POST['amountOfMoney'], "int");

	if(!in_array($isReIssue, [0, 1])) {
		$isReIssue = 0;
	}

	if(!in_array($isNeedMoneyActivity, [0, 1])) {
		$isNeedMoneyActivity = 0;
	}

	if(empty($amountOfMoney)) {
		$amountOfMoney = 10;
	}

	updateConfigValue($pdo, 'is_re_issue', $isReIssue);
	updateConfigValue($pdo, 'is_need_money_activity', $isNeedMoneyActivity);
	updateConfigValue($pdo, 'amount_of_money', $amountOfMoney);

	exit (json_encode(['status' => 1]));
}