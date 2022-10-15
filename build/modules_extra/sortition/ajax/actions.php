<?php
include_once "../../../inc/start.php";
include_once "../../../inc/protect.php";
include_once "../base/config.php";
if(empty($_POST['phpaction'])) {
	log_error("Прямой вызов actions.php");
	exit(json_encode(array('status' => '2')));
}

if(isset($_POST['get_sortition_lite'])) {
	include_once "../base/start.php";

	if($exists == 1) {
		if(empty($sortition->price)) {
			$sortition->price = 0;
		} else {
			$sortition->price = $sortition->price.$messages['RUB'];
		}

		if($sortition->participants != 0) {
			$STH = $pdo->query("SELECT `id` FROM `sortition__participants`");
			$STH->execute();
			$row = $STH->fetchAll();
			$declared_participants = count($row);
		} else {
			$declared_participants = 0;
		}

		if($sortition->finished == 1) {
			$declared = 0;
		} else {
			if(!is_auth()) {
				$declared = 0;
			} else {
				$STH = $pdo->prepare("SELECT `id` FROM `sortition__participants` WHERE `user_id`=:user_id LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
				$STH->execute(array( ':user_id' => $_SESSION['id'] ));
				$row = $STH->fetch();
				if(isset($row->id)) {
					$declared = 1;
				} else {
					$declared = 0;
				}
			}
		}
	} else {
		$declared = 0;
		$declared_participants = 0;
		$sortition->ending = 0;
		$sortition->price = 0;
		$sortition->name = '';
		$sortition->participants = 0;
		$sortition->finished = 2;
	}

	$tpl = new Template;
	$tpl->dir = '../../../templates/'.$conf->template.'/tpl/';
	$tpl->load_template($module['tpl_dir']."sortition_lite.tpl");
	$tpl->set("{site_host}", $site_host);
	$tpl->set("{template}", $conf->template);
	$tpl->set("{finished}", $sortition->finished);
	$tpl->set("{name}", $sortition->name);
	$tpl->set("{declared}", $declared);
	$tpl->set("{participants}", $sortition->participants);
	$tpl->set("{declared_participants}", $declared_participants);
	$tpl->set("{price}", $sortition->price);
	$tpl->set("{exists}", $exists);
	$tpl->compile( 'content' );
	$tpl->show($tpl->result['content']);
	$tpl->global_clear();
	exit();
}
if(isset($_POST['get_sortition'])) {
	include_once "../base/start.php";

	if($exists == 1) {
		if(empty($sortition->price)) {
			$sortition->price = 0;
		} else {
			$sortition->price = $sortition->price.$messages['RUB'];
		}

		if($sortition->participants != 0) {
			$STH = $pdo->query("SELECT `id` FROM `sortition__participants`");
			$STH->execute();
			$row = $STH->fetchAll();
			$declared_participants = count($row);
		} else {
			$declared_participants = 0;
		}

		if($sortition->finished == 1) {
			$declared = 0;
		} else {
			if(!is_auth()) {
				$declared = 0;
			} else {
				$STH = $pdo->prepare("SELECT `id` FROM `sortition__participants` WHERE `user_id`=:user_id LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
				$STH->execute(array( ':user_id' => $_SESSION['id'] ));
				$row = $STH->fetch();
				if(isset($row->id)) {
					$declared = 1;
				} else {
					$declared = 0;
				}
			}
		}
	} else {
		$declared = 0;
		$declared_participants = 0;
		$sortition->ending = 0;
		$sortition->price = 0;
		$sortition->name = '';
		$sortition->participants = 0;
		$sortition->show_participants = 2;
		$sortition->finished = 2;
	}

	$tpl = new Template;
	$tpl->dir = '../../../templates/'.$conf->template.'/tpl/';
	$tpl->load_template($module['tpl_dir']."sortition.tpl");
	$tpl->set("{site_host}", $site_host);
	$tpl->set("{template}", $conf->template);
	$tpl->set("{finished}", $sortition->finished);
	$tpl->set("{name}", $sortition->name);
	$tpl->set("{declared}", $declared);
	$tpl->set("{participants}", $sortition->participants);
	$tpl->set("{declared_participants}", $declared_participants);
	$tpl->set("{price}", $sortition->price);
	$tpl->set("{show_participants}", $sortition->show_participants);
	$tpl->set("{exists}", $exists);
	$tpl->compile( 'content' );
	$tpl->show($tpl->result['content']);
	$tpl->global_clear();
	exit();
}
if(isset($_POST['get_ending_time'])) {
	$STH = $pdo->query("SELECT `ending`, `end_type`, `participants` FROM `sortition` LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
	$sortition = $STH->fetch();

	if($sortition->end_type == 1) {
		if($sortition->ending > time()) {
			$seconds = $sortition->ending-time();
			$days = (int)($seconds / (24 * 3600));
			$seconds -= $days * 24 * 3600;
			$hours = (int)($seconds / 3600);
			$seconds -= $hours * 3600;
			$minutes = (int)($seconds / 60);

			if($days < 10) { $days = '0'.$days; }
			if($hours < 10) { $hours = '0'.$hours; }
			if($minutes < 10) { $minutes = '0'.$minutes; }

			exit("<span>$days<i>дней</i></span><span>$hours<i>часов</i></span><span>$minutes<i>минут</i></span>");
		} else {
			exit("<span>00<i>дней</i></span><span>00<i>часов</i></span><span>00<i>минут</i></span><script>reset_page();</script>");
		}
	} else {
		$STH = $pdo->query("SELECT `id` FROM `sortition__participants`");
		$STH->execute();
		$row = $STH->fetchAll();
		$count = count($row);

		if($count < $sortition->participants) {
			$count = $sortition->participants - $count;
			exit("<span>$count<i>участник(а|ов)</i></span>");
		} else {
			exit("<script>reset_page();</script>");
		}
	}
}
if(isset($_POST['get_prizes'])) {
	$STH = $pdo->query("SELECT `prize`,`own_prize` FROM `sortition` LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
	$row = $STH->fetch();

	$place = 0;
	if($row->own_prize == 1) {
		$prizes = unserialize($row->prize);
		?>
			<div class='well well-sm mb-0 with_code'> <?php echo $prizes['description']; ?> </div>
		<?php
	} else {
		$prize_types = get_prizes_types($pdo);
		$prizes = unserialize($row->prize);
		$count = count($prizes);
		if(isset($prizes[$count-1]['place'])) {
			$places = $prizes[$count-1]['place'];
		} else {
			$places = 0;
		}

		for ($i=0; $i < $places; $i++) {
			$place++;
			?>
			<h5><?php echo $place; ?> место:</h5>
			<?php
			for ($j=0; $j < $count; $j++) { 
				if($prizes[$j]['place'] == $place) {
					if($prizes[$j]['type'] == 1 || $prizes[$j]['type'] == 5 || $prizes[$j]['type'] == 6) {
						$params = get_types_params($prizes[$j]['type']);

						$STH = $pdo->prepare("SELECT `servers`.`name` AS `server_name`,`$params[2]`.`name`,`$params[2]`.`text`, `$params[3]`.`time` FROM `$params[2]` 
							LEFT JOIN `$params[3]` ON `$params[3]`.`service`=`$params[2]`.`id`
							LEFT JOIN `servers` ON `servers`.`id`=`$params[2]`.`server` WHERE `$params[2]`.`id`=:service AND `servers`.`id`=:server AND `$params[3]`.`id`=:tarif LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
						$STH->execute(array( ':service' => $prizes[$j]['service'], ':server' => $prizes[$j]['server'], ':tarif' => $prizes[$j]['tarif'] ));
						$row = $STH->fetch();

						if($row->time == 0) {
							$row->time = 'Навсегда';
						} else {
							$row->time = $row->time.' дня(ей)';
						}

						?>
						<div class="prize" data-target="#prize<?php echo $place; ?>_<?php echo $j; ?>" data-toggle="modal">
							<i class="fa fa-gamepad" aria-hidden="true"></i>
							<span>
								Услуга: <?php echo $row->name; ?>
							</span>
							<span>
								<small>Тариф: <?php echo $row->time; ?></small>
							</span>
							<span>
								<small>Сервер: <?php echo $row->server_name; ?></small>
							</span>
						</div>
						<div id="prize<?php echo $place; ?>_<?php echo $j; ?>" class="modal fade">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-header">
										<h4 class="modal-title" style="margin-top: 0px;">
											<?php echo $row->name; ?>
											<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
										</h4>
									</div>
									<div class="modal-body">
										Призом является услуга <b><?php echo $row->name; ?></b> с тарифом <b><?php echo $row->time; ?></b> на сервере <b><?php echo $row->server_name; ?></b>
										<hr>
										<b>Описание услуги</b>
										<div class="with_code">
											<?php echo $row->text; ?>
										</div>
									</div>
								</div>
							</div>
						</div>
						<?php
					}
					if($prizes[$j]['type'] == 2) {
						?>
						<div class="prize" data-target="#prize<?php echo $place; ?>_<?php echo $j; ?>" data-toggle="modal">
							<i class="fa fa-money" aria-hidden="true"></i>
							<span>
								<?php echo $prizes[$j]['money']; ?> рублей
							</span>
							<span>
								<small>На Ваш баланс</small>
							</span>
						</div>
						</div>
						<div id="prize<?php echo $place; ?>_<?php echo $j; ?>" class="modal fade">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-header">
										<h4 class="modal-title" style="margin-top: 0px;">
											<?php echo $prizes[$j]['money']; ?> рублей
											<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
										</h4>
									</div>
									<div class="modal-body">
										Призом является <b><?php echo $prizes[$j]['money']; ?></b> рублей на Ваш баланс на данном проекте.
									</div>
								</div>
							</div>
						</div>
						<?php
					}
					if($prizes[$j]['type'] == 3) {
						?>
						<div class="prize" data-target="#prize<?php echo $place; ?>_<?php echo $j; ?>" data-toggle="modal">
							<i class="fa fa-certificate" aria-hidden="true"></i>
							<span>
								<?php echo $prizes[$j]['percent']; ?>% скидка
							</span>
							<span>
								<small>На все услуги</small>
							</span>
						</div>

						<div id="prize<?php echo $place; ?>_<?php echo $j; ?>" class="modal fade">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-header">
										<h4 class="modal-title" style="margin-top: 0px;">
											<?php echo $prizes[$j]['percent']; ?>% скидка
											<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
										</h4>
									</div>
									<div class="modal-body">
										Призом является <b><?php echo $prizes[$j]['percent']; ?>%</b> скидка на все услуги на данном проекте.
									</div>
								</div>
							</div>
						</div>
						<?php
					}
					if($prizes[$j]['type'] == 4) {
						$STH = $pdo->prepare("SELECT `servers`.`name` AS `server_name`, `sk_services`.`number`, `sk_services`.`type` FROM `sk_services` 
							LEFT JOIN `servers` ON `servers`.`id`=`sk_services`.`server` WHERE `sk_services`.`id`=:tarif AND `servers`.`id`=:server LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
						$STH->execute(array( ':server' => $prizes[$j]['server'], ':tarif' => $prizes[$j]['tarif'] ));
						$row = $STH->fetch();

						?>
						<div class="prize" data-target="#prize<?php echo $place; ?>_<?php echo $j; ?>" data-toggle="modal">
							<i class="fa fa-database" aria-hidden="true"></i>
							<span>
								<?php echo $row->number; ?> (<?php echo $services_data[$row->type]['name']; ?>)
							</span>
							<span>
								<small>Сервер: <?php echo $row->server_name; ?></small>
							</span>
						</div>
						<div id="prize<?php echo $place; ?>_<?php echo $j; ?>" class="modal fade">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-header">
										<h4 class="modal-title" style="margin-top: 0px;">
											<?php echo $row->number; ?> <?php echo $services_data[$row->type]['name']; ?>
											<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
										</h4>
									</div>
									<div class="modal-body">
										Призом является <b><?php echo $row->number; ?></b>(<?php echo $services_data[$row->type]['name']; ?>) на сервере <b><?php echo $row->server_name; ?></b>
									</div>
								</div>
							</div>
						</div>
						<?php
					}
				}
			}
		}
	}
	exit();
}
if(isset($_POST['get_participants'])) {
	$STH = $pdo->query("SELECT `show_participants` FROM `sortition` LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
	$sortition = $STH->fetch();
	if($sortition->show_participants == 1) {
		$i = 0;
		$STH = $pdo->query("SELECT `users`.`id`, `users`.`login`, `users`.`avatar` FROM `sortition__participants` LEFT JOIN `users` ON `users`.`id`=`sortition__participants`.`user_id`"); $STH->setFetchMode(PDO::FETCH_OBJ);
		while($row = $STH->fetch()) { 
			$i++;
			?>
				<a title="<?php echo $row->login; ?>" href="../profile?id=<?php echo $row->id; ?>" target="_blank">
					<img src="../<?php echo $row->avatar; ?>" alt="<?php echo $row->login; ?>"> <span><?php echo $row->login; ?></span>
				</a>
			<?php
		}
		if($i == 0) {
			exit("<p>Участников нет</p>");
		}
	}

	exit();
}
if(isset($_POST['get_winners'])) {
	$STH = $pdo->query("SELECT `finished` FROM `sortition` LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
	$sortition = $STH->fetch();
	if($sortition->finished == 1) {
		$color[0] = 240;
		$color[1] = 80;
		$color[2] = 10;
		$STH = $pdo->query("SELECT `sortition__participants`.`winner`, `users`.`id`, `users`.`login`, `users`.`avatar` FROM `sortition__participants`
		INNER JOIN `users` ON `sortition__participants`.`user_id`=`users`.`id` 
		WHERE `sortition__participants`.`winner` is NOT NULL AND `sortition__participants`.`winner`!='0' ORDER BY `sortition__participants`.`winner`"); $STH->setFetchMode(PDO::FETCH_OBJ);
		while($row = $STH->fetch()) {
			$color[0] = $color[0] - 20;
			if($color[0] < 0) {
				$color[0] = 250;
			}
			$color[1] = $color[1] - 0;
			$color[2] = $color[2] + 20;
			if($color[2] > 255) {
				$color[2] = 10;
			}
			?>
				<a style="background: rgb(<?php echo $color[0]; ?>, <?php echo $color[1]; ?>, <?php echo $color[2]; ?>);" title="<?php echo $row->login; ?>" href="../profile?id=<?php echo $row->id; ?>" target="_blank">
					<i><?php echo $row->winner; ?> место</i>
					<img src="../<?php echo $row->avatar; ?>" alt="<?php echo $row->login; ?>">
					<span><?php echo $row->login; ?></span>
				</a>
			<?php
		}
	}
	exit();
}
if(isset($_POST['participate']) && isset($_SESSION['id'])) {
	$STH = $pdo->query("SELECT `ending`, `participants`, `price`, `how_old`, `finished` FROM `sortition` LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
	$sortition = $STH->fetch();

	if($sortition->finished == 2) {
		$STH = $pdo->query("SELECT `id` FROM `sortition__participants`");
		$STH->execute();
		$row = $STH->fetchAll();
		$participants = count($row);

		$STH = $pdo->prepare("SELECT `shilings`, `regdate` FROM `users` WHERE `id`=:id LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
		$STH->execute(array( ':id' => $_SESSION['id'] ));
		$user = $STH->fetch();

		if(!empty($sortition->how_old) and $sortition->how_old != 0) {
			$regdate = strtotime($user->regdate);
			if(time() - $regdate < $sortition->how_old*24*60*60) {
				exit('<p class="text-danger">Чтобы принять участие в розыгрыше, с момента Вашей регистрации должно пройти более чем '.$sortition->how_old.' дня(ей)</p>');
			}
		}

		if(empty($sortition->participants) || $sortition->participants == 0 || $participants < $sortition->participants) {
			$STH = $pdo->prepare("SELECT `id` FROM `sortition__participants` WHERE `user_id`=:user_id LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
			$STH->execute(array( ':user_id' => $_SESSION['id'] ));
			$temp = $STH->fetch();

			if(empty($temp->id)) {
				if($sortition->price != 0) {
					if($user->shilings - $sortition->price < 0) {
						$price_delta = round($sortition->price - $user->shilings, 2);
						exit('<p class="text-danger">У Вас недостаточно средств <span class="m-icon icon-bank"></span><br><a href="../purse?price='.$price_delta.'">Пополните баланс на '.$price_delta.$messages['RUB'].'.</a></p>');
					}

					$STH = $pdo->prepare("UPDATE `users` SET `shilings`=:shilings WHERE `id`=:id LIMIT 1");
					$STH->execute(array( ':shilings' => round($user->shilings - $sortition->price, 2), ':id' => $_SESSION['id'] ));
				}

				$STH = $pdo->prepare("INSERT INTO `sortition__participants` (`user_id`,`contribution`) values (:user_id, :contribution)");
				$STH->execute(array( ':user_id' => $_SESSION['id'], ':contribution' => $sortition->price ));

				exit('<p class="text-success">Ваша заявка успешно принята.</p><script>setTimeout(reset_page, 1500);</script>');
			} else {
				exit('<p class="text-danger">Вы уже приняли участие!</p>');
			}
		} else {
			exit('<p class="text-danger">Все места заняты.</p>');
		}
	} else {
		exit('<p class="text-danger">Розыгрыш уже завершен.</p>');
	}
}


if(!is_admin()){
	exit(json_encode(array( 'status' => '2', 'data' => 'Досутпно только администратору' )));
}
if(isset($_POST['get_prizes_adm'])) {
	$STH = $pdo->query("SELECT `prize`, `finished`, `own_prize` FROM `sortition` LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
	$sortition = $STH->fetch();
	$place = 0;
	if(!empty($sortition->prize) && $sortition->own_prize == 2) {
		$prize_types = get_prizes_types($pdo);
		$prizes = unserialize($sortition->prize);
		$count = count($prizes);
		if(isset($prizes[$count-1]['place'])) {
			$places = $prizes[$count-1]['place'];
		} else {
			$places = 0;
		}

		for ($i=0; $i < $places; $i++) {
			$place++;
			$prize_count = 0;
		?>
		<div id="prizes_div_<?php echo $place; ?>" class="prize-block">
			<b>Приз(ы) для <?php echo $place; ?> победителя (<a onclick="dell_place(<?php echo $place; ?>)" class="c-p">Удалить</a>)</b>
			<input type="hidden" value="0" id="prize_count_<?php echo $place; ?>">
			<div id="prizes_<?php echo $place; ?>">
			<?php
			for ($j=0; $j < $count; $j++) { 
				if($prizes[$j]['place'] == $place) {
					$prize_count++;
					?>
					<input type="hidden" name="prizetype_<?php echo $place; ?>_<?php echo $prize_count; ?>" id="prizetype_<?php echo $place; ?>_<?php echo $prize_count; ?>" value="<?php echo $prizes[$j]['type']; ?>">
					<?php
					if($prizes[$j]['type'] == 1 || $prizes[$j]['type'] == 5 || $prizes[$j]['type'] == 6) {
						$params = get_types_params($prizes[$j]['type']);
						$STH = $pdo->query("SELECT `id`,`name` FROM `servers` WHERE `$params[1]`!='0'");
						$STH->execute();
						$servers = $STH->fetchAll();
						if(count($servers) == 0) {
							?>
							<div class="input-group w-100" id="prize_line_<?php echo $place; ?>_<?php echo $prize_count; ?>">
								<span class="input-group-btn w-33">
									<select name="server<?php echo $place; ?>_<?php echo $prize_count; ?>" id="server<?php echo $place; ?>_<?php echo $prize_count; ?>" class="form-control">
									</select>
								</span>
								<span class="input-group-btn w-33">
									<select name="service<?php echo $place; ?>_<?php echo $prize_count; ?>" id="service<?php echo $place; ?>_<?php echo $prize_count; ?>" class="form-control">
									</select>
								</span>
								<span class="input-group-btn w-33">
									<select name="tarif<?php echo $place; ?>_<?php echo $prize_count; ?>" id="tarif<?php echo $place; ?>_<?php echo $prize_count; ?>" class="form-control">
									</select>
								</span>
								<span class="input-group-btn">
									<button onclick="dell_prize_line(<?php echo $place; ?>, <?php echo $prize_count; ?>)" class="btn btn-default" type="button">Удалить</button>
								</span>
							</div>
							<?php
						} else {
							$srv = $prizes[$j]['server'];
							$STH = $pdo->query("SELECT `id`, `name` FROM `$params[2]` WHERE `server`='$srv'");
							$STH->execute();
							$services = $STH->fetchAll();
							?>
							<div class="input-group w-100" id="prize_line_<?php echo $place; ?>_<?php echo $prize_count; ?>">
								<span class="input-group-btn w-33">
									<select name="server<?php echo $place; ?>_<?php echo $prize_count; ?>" id="server<?php echo $place; ?>_<?php echo $prize_count; ?>" class="form-control" onchange="get_services_prize(<?php echo $place; ?>, <?php echo $prize_count; ?>, <?php echo $prizes[$j]['type']; ?>);">
										<?php for ($l = 0; $l < count($servers); $l++) { ?>
											<?php if($servers[$l]['id'] == $prizes[$j]['server']) { ?>
												<option value="<?php echo $servers[$l]['id']; ?>" selected><?php echo $servers[$l]['name']; ?></option>
											<?php } else { ?>
												<option value="<?php echo $servers[$l]['id']; ?>"><?php echo $servers[$l]['name']; ?></option>
											<?php } ?>
										<?php } ?>
									</select>
								</span>
								<span class="input-group-btn w-33">
									<select name="service<?php echo $place; ?>_<?php echo $prize_count; ?>" id="service<?php echo $place; ?>_<?php echo $prize_count; ?>" class="form-control" onchange="get_tarifs_prize(<?php echo $place; ?>, <?php echo $prize_count; ?>, <?php echo $prizes[$j]['type']; ?>);">
										<?php for ($l = 0; $l < count($services); $l++) { ?>
											<?php if($services[$l]['id'] == $prizes[$j]['service']) { ?>
												<option value="<?php echo $services[$l]['id']; ?>" selected><?php echo $services[$l]['name']; ?></option>
											<?php } else { ?>
												<option value="<?php echo $services[$l]['id']; ?>"><?php echo $services[$l]['name']; ?></option>
											<?php } ?>
										<?php } ?>
									</select>
								</span>
								<span class="input-group-btn w-33">
									<select name="tarif<?php echo $place; ?>_<?php echo $prize_count; ?>" id="tarif<?php echo $place; ?>_<?php echo $prize_count; ?>" class="form-control">
										<?php
											if(count($services) != 0) {
												$srv = $prizes[$j]['service'];
												$STH = $pdo->query("SELECT `id`, `time` FROM `$params[3]` WHERE `service`='$srv' ORDER BY `time`");
												$STH->execute();
												$tarifs = $STH->fetchAll();
												for ($l=0; $l < count($tarifs); $l++) {
													if($tarifs[$l]['time'] == 0) {
														$tarifs[$l]['time'] = 'Навсегда';
													} else {
														$tarifs[$l]['time'] = $tarifs[$l]['time'].' дня(ей)';
													}
												?>
													<?php if($tarifs[$l]['id'] == $prizes[$j]['tarif']) { ?>
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
								<span class="input-group-btn">
									<button onclick="dell_prize_line(<?php echo $place; ?>, <?php echo $prize_count; ?>)" class="btn btn-default" type="button">Удалить</button>
								</span>
							</div>
							<?php
						}
					}
					if($prizes[$j]['type'] == 2) {
						?>
						<div class="input-group w-100" id="prize_line_<?php echo $place; ?>_<?php echo $prize_count; ?>">
							<span class="input-group-btn w-100">
								<input class="form-control" name="money<?php echo $place; ?>_<?php echo $prize_count; ?>" id="money<?php echo $place; ?>_<?php echo $prize_count; ?>" placeholder="Сумма" value="<?php echo $prizes[$j]['money']; ?>" type="number">
							</span>
							<span class="input-group-btn">
								<button onclick="dell_prize_line(<?php echo $place; ?>, <?php echo $prize_count; ?>)" class="btn btn-default" type="button">Удалить</button>
							</span>
						</div>
						<?php
					}
					if($prizes[$j]['type'] == 3) {
						?>
						<div class="input-group w-100" id="prize_line_<?php echo $place; ?>_<?php echo $prize_count; ?>">
							<span class="input-group-btn w-100">
								<input class="form-control" name="percent<?php echo $place; ?>_<?php echo $prize_count; ?>" id="percent<?php echo $place; ?>_<?php echo $prize_count; ?>" placeholder="Значение в %" value="<?php echo $prizes[$j]['percent']; ?>" type="number" maxlength="2">
							</span>
							<span class="input-group-btn">
								<button onclick="dell_prize_line(<?php echo $place; ?>, <?php echo $prize_count; ?>)" class="btn btn-default" type="button">Удалить</button>
							</span>
						</div>
						<?php
					}
					if($prizes[$j]['type'] == 4) {
						$STH = $pdo->query("SELECT `id`,`name` FROM `servers` WHERE `sk_host`!='0'");
						$STH->execute();
						$servers = $STH->fetchAll();
						if(count($servers) == 0) {
							?>
							<div class="input-group w-100" id="prize_line_<?php echo $place; ?>_<?php echo $prize_count; ?>">
								<span class="input-group-btn w-50">
									<select name="server<?php echo $place; ?>_<?php echo $prize_count; ?>" id="server<?php echo $place; ?>_<?php echo $prize_count; ?>" class="form-control">
									</select>
								</span>
								<span class="input-group-btn w-50">
									<select name="tarif<?php echo $place; ?>_<?php echo $prize_count; ?>" id="tarif<?php echo $place; ?>_<?php echo $prize_count; ?>" class="form-control">
									</select>
								</span>
								<span class="input-group-btn">
									<button onclick="dell_prize_line(<?php echo $place; ?>, <?php echo $prize_count; ?>)" class="btn btn-default" type="button">Удалить</button>
								</span>
							</div>
							<?php
						} else {
							$srv = $prizes[$j]['server'];
							$STH = $pdo->query("SELECT `id`, `number`, `type` FROM `sk_services` WHERE `server`='$srv' ORDER BY `type`");
							$STH->execute();
							$services = $STH->fetchAll();
							?>
							<div class="input-group w-100" id="prize_line_<?php echo $place; ?>_<?php echo $prize_count; ?>">
								<span class="input-group-btn w-50">
									<select name="server<?php echo $place; ?>_<?php echo $prize_count; ?>" id="server<?php echo $place; ?>_<?php echo $prize_count; ?>" class="form-control" onchange="get_services_prize2(<?php echo $place; ?>, <?php echo $prize_count; ?>, <?php echo $prizes[$j]['type']; ?>);">
										<?php for ($l = 0; $l < count($servers); $l++) { ?>
											<?php if($servers[$l]['id'] == $prizes[$j]['server']) { ?>
												<option value="<?php echo $servers[$l]['id']; ?>" selected><?php echo $servers[$l]['name']; ?></option>
											<?php } else { ?>
												<option value="<?php echo $servers[$l]['id']; ?>"><?php echo $servers[$l]['name']; ?></option>
											<?php } ?>
										<?php } ?>
									</select>
								</span>
								<span class="input-group-btn w-50">
									<select name="tarif<?php echo $place; ?>_<?php echo $prize_count; ?>" id="tarif<?php echo $place; ?>_<?php echo $prize_count; ?>" class="form-control">
										<?php for ($l = 0; $l < count($services); $l++) { ?>
											<?php if($services[$l]['id'] == $prizes[$j]['tarif']) { ?>
												<option value="<?php echo $services[$l]['id']; ?>" selected><?php echo $services[$l]['number']; ?> (<?php echo $services_data[$services[$l]['type']]['name']; ?>)</option>
											<?php } else { ?>
												<option value="<?php echo $services[$l]['id']; ?>"><?php echo $services[$l]['number']; ?> (<?php echo $services_data[$services[$l]['type']]['name']; ?>)</option>
											<?php } ?>
										<?php } ?>
									</select>
								</span>
								<span class="input-group-btn">
									<button onclick="dell_prize_line(<?php echo $place; ?>, <?php echo $prize_count; ?>)" class="btn btn-default" type="button">Удалить</button>
								</span>
							</div>
							<?php
						}
					}
					?>
					<script>$('#prize_count_<?php echo $place; ?>').val('<?php echo $prize_count; ?>');</script>
					<?php
				}
			}
			?>
			</div>
			<div class="input-group">
				<span class="input-group-btn">
					<button class="btn btn-default" type="button" onclick="get_prize_line(<?php echo $place; ?>);">Добавить</button>
				</span>
				<select class="form-control" id="prize_type_<?php echo $place; ?>">
				<?php if($prize_types[1] == 1) { ?>
				<option value="1">Услугу</option>
				<?php } ?>
				<?php if($prize_types[2] == 1) { ?>
				<option value="2">Денежный приз</option>
				<?php } ?>
				<?php if($prize_types[3] == 1) { ?>
				<option value="3">Скидку</option>
				<?php } ?>
				<?php if($prize_types[4] == 1) { ?>
				<option value="4">Приз из shop_key</option>
				<?php } ?>
				<?php if($prize_types[5] == 1) { ?>
				<option value="5">Приз из buy_key</option>
				<?php } ?>
				<?php if($prize_types[6] == 1) { ?>
				<option value="6">Приз из vip_key_ws</option>
				<?php } ?>
				</select>
			</div>
		</div>
		<?php
		}
	}
	?>
	<script>
		places = Number(<?php echo $place; ?>) + 1;
		$('#place_i').html(places);
		$('#place_count').val(places);
	</script>
	<?php
	exit();
}
if(isset($_POST['get_prize_line'])) {
	$prize_count = check($_POST['prize_count'], "int");
	$prize_type = check($_POST['prize_type'], "int");
	$place = check($_POST['place'], "int");
	if (empty($prize_count)) {
		$prize_count = 0;
	}
	if (empty($prize_type)) {
		$prize_type = 1;
	}
	if (empty($place)) {
		exit();
	}

	$prize_count++;
	?>
	<input type="hidden" name="prizetype_<?php echo $place; ?>_<?php echo $prize_count; ?>" id="prizetype_<?php echo $place; ?>_<?php echo $prize_count; ?>" value="<?php echo $prize_type; ?>">
	<?php
	if($prize_type == 1 || $prize_type == 5 || $prize_type == 6) {
		$params = get_types_params($prize_type);
		$STH = $pdo->query("SELECT `id`,`name` FROM `servers` WHERE `$params[1]`!='0'");
		$STH->execute();
		$servers = $STH->fetchAll();
		if(count($servers) == 0) {
			?>
			<div class="input-group w-100" id="prize_line_<?php echo $place; ?>_<?php echo $prize_count; ?>">
				<span class="input-group-btn w-33">
					<select name="server<?php echo $place; ?>_<?php echo $prize_count; ?>" id="server<?php echo $place; ?>_<?php echo $prize_count; ?>" class="form-control">
					</select>
				</span>
				<span class="input-group-btn w-33">
					<select name="service<?php echo $place; ?>_<?php echo $prize_count; ?>" id="service<?php echo $place; ?>_<?php echo $prize_count; ?>" class="form-control">
					</select>
				</span>
				<span class="input-group-btn w-33">
					<select name="tarif<?php echo $place; ?>_<?php echo $prize_count; ?>" id="tarif<?php echo $place; ?>_<?php echo $prize_count; ?>" class="form-control">
					</select>
				</span>
				<span class="input-group-btn">
					<button onclick="dell_prize_line(<?php echo $place; ?>, <?php echo $prize_count; ?>)" class="btn btn-default" type="button">Удалить</button>
				</span>
			</div>
			<?php
		} else {
			$j = $servers['0']['id'];
			$STH = $pdo->query("SELECT `id`, `name` FROM `$params[2]` WHERE `server`='$j'");
			$STH->execute();
			$services = $STH->fetchAll();
			?>
			<div class="input-group w-100" id="prize_line_<?php echo $place; ?>_<?php echo $prize_count; ?>">
				<span class="input-group-btn w-33">
					<select name="server<?php echo $place; ?>_<?php echo $prize_count; ?>" id="server<?php echo $place; ?>_<?php echo $prize_count; ?>" class="form-control" onchange="get_services_prize(<?php echo $place; ?>, <?php echo $prize_count; ?>, <?php echo $prize_type; ?>);">
						<?php for ($i=0; $i < count($servers); $i++) { ?>
							<option value="<?php echo $servers[$i]['id']; ?>"><?php echo $servers[$i]['name']; ?></option>
						<?php } ?>
					</select>
				</span>
				<span class="input-group-btn w-33">
					<select name="service<?php echo $place; ?>_<?php echo $prize_count; ?>" id="service<?php echo $place; ?>_<?php echo $prize_count; ?>" class="form-control" onchange="get_tarifs_prize(<?php echo $place; ?>, <?php echo $prize_count; ?>, <?php echo $prize_type; ?>);">
						<?php for ($i=0; $i < count($services); $i++) { ?>
							<option value="<?php echo $services[$i]['id']; ?>"><?php echo $services[$i]['name']; ?></option>
						<?php } ?>
					</select>
				</span>
				<span class="input-group-btn w-33">
					<select name="tarif<?php echo $place; ?>_<?php echo $prize_count; ?>" id="tarif<?php echo $place; ?>_<?php echo $prize_count; ?>" class="form-control">
						<?php
							if(count($services) != 0) {
								$j = $services['0']['id'];
								$STH = $pdo->query("SELECT `id`, `time` FROM `$params[3]` WHERE `service`='$j' ORDER BY `time`");
								$STH->execute();
								$tarifs = $STH->fetchAll();
								for ($i=0; $i < count($tarifs); $i++) {
									if($tarifs[$i]['time'] == 0) {
										$tarifs[$i]['time'] = 'Навсегда';
									} else {
										$tarifs[$i]['time'] = $tarifs[$i]['time'].' дня(ей)';
									}
								?>
									<option value="<?php echo $tarifs[$i]['id']; ?>"><?php echo $tarifs[$i]['time']; ?></option>
								<?php
								}
							}
						?>
					</select>
				</span>
				<span class="input-group-btn">
					<button onclick="dell_prize_line(<?php echo $place; ?>, <?php echo $prize_count; ?>)" class="btn btn-default" type="button">Удалить</button>
				</span>
			</div>
			<?php
		}
	}
	if($prize_type == 2) {
		?>
		<div class="input-group w-100" id="prize_line_<?php echo $place; ?>_<?php echo $prize_count; ?>">
			<span class="input-group-btn w-100">
				<input class="form-control" name="money<?php echo $place; ?>_<?php echo $prize_count; ?>" id="money<?php echo $place; ?>_<?php echo $prize_count; ?>" placeholder="Сумма" value="" type="number">
			</span>
			<span class="input-group-btn">
				<button onclick="dell_prize_line(<?php echo $place; ?>, <?php echo $prize_count; ?>)" class="btn btn-default" type="button">Удалить</button>
			</span>
		</div>
		<?php
	}
	if($prize_type == 3) {
		?>
		<div class="input-group w-100" id="prize_line_<?php echo $place; ?>_<?php echo $prize_count; ?>">
			<span class="input-group-btn w-100">
				<input class="form-control" name="percent<?php echo $place; ?>_<?php echo $prize_count; ?>" id="percent<?php echo $place; ?>_<?php echo $prize_count; ?>" placeholder="Значение в %" value="" type="number" maxlength="2">
			</span>
			<span class="input-group-btn">
				<button onclick="dell_prize_line(<?php echo $place; ?>, <?php echo $prize_count; ?>)" class="btn btn-default" type="button">Удалить</button>
			</span>
		</div>
		<?php
	}
	if($prize_type == 4) {
		$STH = $pdo->query("SELECT `id`,`name` FROM `servers` WHERE `sk_host`!='0'");
		$STH->execute();
		$servers = $STH->fetchAll();
		if(count($servers) == 0) {
			?>
			<div class="input-group w-100" id="prize_line_<?php echo $place; ?>_<?php echo $prize_count; ?>">
				<span class="input-group-btn w-50">
					<select name="server<?php echo $place; ?>_<?php echo $prize_count; ?>" id="server<?php echo $place; ?>_<?php echo $prize_count; ?>" class="form-control">
					</select>
				</span>
				<span class="input-group-btn w-50">
					<select name="tarif<?php echo $place; ?>_<?php echo $prize_count; ?>" id="tarif<?php echo $place; ?>_<?php echo $prize_count; ?>" class="form-control">
					</select>
				</span>
				<span class="input-group-btn">
					<button onclick="dell_prize_line(<?php echo $place; ?>, <?php echo $prize_count; ?>)" class="btn btn-default" type="button">Удалить</button>
				</span>
			</div>
			<?php
		} else {
			$j = $servers['0']['id'];
			$STH = $pdo->query("SELECT `id`, `number`, `type` FROM `sk_services` WHERE `server`='$j' ORDER BY `type`");
			$STH->execute();
			$services = $STH->fetchAll();
			?>
			<div class="input-group w-100" id="prize_line_<?php echo $place; ?>_<?php echo $prize_count; ?>">
				<span class="input-group-btn w-50">
					<select name="server<?php echo $place; ?>_<?php echo $prize_count; ?>" id="server<?php echo $place; ?>_<?php echo $prize_count; ?>" class="form-control" onchange="get_services_prize2(<?php echo $place; ?>, <?php echo $prize_count; ?>, <?php echo $prize_type; ?>);">
						<?php for ($i=0; $i < count($servers); $i++) { ?>
							<option value="<?php echo $servers[$i]['id']; ?>"><?php echo $servers[$i]['name']; ?></option>
						<?php } ?>
					</select>
				</span>
				<span class="input-group-btn w-50">
					<select name="tarif<?php echo $place; ?>_<?php echo $prize_count; ?>" id="tarif<?php echo $place; ?>_<?php echo $prize_count; ?>" class="form-control">
						<?php for ($i=0; $i < count($services); $i++) { ?>
							<option value="<?php echo $services[$i]['id']; ?>"><?php echo $services[$i]['number']; ?> (<?php echo $services_data[$services[$i]['type']]['name']; ?>)</option>
						<?php } ?>
					</select>
				</span>
				<span class="input-group-btn">
					<button onclick="dell_prize_line(<?php echo $place; ?>, <?php echo $prize_count; ?>)" class="btn btn-default" type="button">Удалить</button>
				</span>
			</div>
			<?php
		}
	}
	?>
	<script>$('#prize_count_<?php echo $place; ?>').val('<?php echo $prize_count; ?>');</script>
	<?php
	exit();
}
if(isset($_POST['get_services_prize'])) {
	$type = check($_POST['type'], "int");
	$server = check($_POST['server'], "int");
	if (empty($server) || empty($type)) {
		exit();
	}

	$params = get_types_params($type);
	$STH = $pdo->query("SELECT `id`, `name` FROM `$params[2]` WHERE `server`='$server'");
	$STH->execute();
	$services = $STH->fetchAll();
	for ($i=0; $i < count($services); $i++) {
		?>
		<option value="<?php echo $services[$i]['id']; ?>"><?php echo $services[$i]['name']; ?></option>
		<?php
	}
	exit();
}
if(isset($_POST['get_services_prize2'])) {
	$server = check($_POST['server'], "int");
	if (empty($server)) {
		exit();
	}

	$STH = $pdo->query("SELECT `id`, `number`, `type` FROM `sk_services` WHERE `server`='$server' ORDER BY `type`");
	$STH->execute();
	$services = $STH->fetchAll();
	for ($i=0; $i < count($services); $i++) {
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
if(isset($_POST['get_tarifs_prize'])) {
	$type = check($_POST['type'], "int");
	$service = check($_POST['service'], "int");
	if (empty($service) || empty($type)) {
		exit();
	}

	$params = get_types_params($type);
	$STH = $pdo->query("SELECT `id`, `time` FROM `$params[3]` WHERE `service`='$service' ORDER BY `time`");
	$STH->execute();
	$tarifs = $STH->fetchAll();
	for ($i=0; $i < count($tarifs); $i++) {
		if($tarifs[$i]['time'] == 0) {
			$tarifs[$i]['time'] = 'Навсегда';
		} else {
			$tarifs[$i]['time'] = $tarifs[$i]['time'].' дня(ей)';
		}
		?>
		<option value="<?php echo $tarifs[$i]['id']; ?>"><?php echo $tarifs[$i]['time']; ?></option>
		<?php
	}
	exit();
}
if(isset($_POST['load_participants_list'])) {
	$type = check($_POST['type'], "int");
	if (empty($type)) {
		$type = 1;
	}
	$i=0;
	if($type == 1) {
		$STH = $pdo->query("SELECT `sortition__participants`.`contribution`, `users`.`id`, `users`.`login`, `users`.`avatar` FROM `sortition__participants`
		INNER JOIN `users` ON `sortition__participants`.`user_id`=`users`.`id`"); $STH->setFetchMode(PDO::FETCH_OBJ);
		$STH->execute();
		while($row = $STH->fetch()) { 
			$i++;
			?>
			<tr id="participant<?php echo $row->id; ?>">
				<td><?php echo $i; ?></td>
				<td>
					<a target="_blank" href="../profile?id=<?php echo $row->id; ?>">
						<img src="../<?php echo $row->avatar; ?>" alt="<?php echo $row->login; ?>"> 
						<?php echo $row->login; ?>
					</a>
					- <a class="c-p" onclick="dell_participant('<?php echo $row->id; ?>');">Удалить участника</a>
				</td>
				<td><?php echo $row->contribution; ?></td>
			</tr>
			<?php
		}
	} else {
		$STH = $pdo->query("SELECT `sortition__participants`.`contribution`, `sortition__participants`.`winner`, `users`.`id`, `users`.`login`, `users`.`avatar` FROM `sortition__participants`
		INNER JOIN `users` ON `sortition__participants`.`user_id`=`users`.`id` 
		WHERE `sortition__participants`.`winner` is NOT NULL AND `sortition__participants`.`winner`!='0' ORDER BY `sortition__participants`.`winner`"); $STH->setFetchMode(PDO::FETCH_OBJ);
		while($row = $STH->fetch()) { 
			$i++;
			?>
			<tr>
				<td>1</td>
				<td>
					<a target="_blank" href="../profile?id=<?php echo $row->id; ?>">
						<img src="../<?php echo $row->avatar; ?>" alt="<?php echo $row->login; ?>"> 
						<?php echo $row->login; ?>
					</a>
				</td>
				<td><?php echo $row->winner; ?></td>
				<td><?php echo $row->contribution; ?></td>
			</tr>
			<?php
		}
	}
	if($i==0) {
		?>
		<tr>
			<td colspan="10">Информация отсутствует</td>
		</tr>
		<?php
	}
	exit();
}
if(isset($_POST['dell_participant'])) {
	$id = check($_POST['id'], "int");
	if(empty($id)) {
		$id = 0;
	}

	$STH = $pdo->prepare("DELETE FROM `sortition__participants` WHERE `user_id`=:user_id LIMIT 1");
	$STH->execute(array( ':user_id' => $id ));
}

if(isset($_POST['save_sortition'])) {
	$type = check($_POST['type'], "int");
	$name = check($_POST['name'], null);
	$price = check($_POST['price'], "float");
	$participants = check($_POST['participants'], "int");
	$show_participants = check($_POST['show_participants'], "int");
	$ending = check($_POST['ending'], null);
	$own_prize = check($_POST['own_prize'], "int");
	$how_old = check($_POST['how_old'], "int");
	$end_type = check($_POST['end_type'], "int");
	$count_of_winners = check($_POST['count_of_winners'], "int");

	if (empty($type) || ($type != 1 && $type != 2)) {
		exit (json_encode(array('status' => '2')));
	}
	if (empty($how_old)) {
		$how_old = 0;
	}
	if (empty($end_type)) {
		$end_type = 1;
	}
	if (empty($show_participants)) {
		$show_participants = 1;
	}
	if (empty($name)) {
		exit (json_encode(array('status' => '2', 'input' => 'name', 'reply' => 'Заполните')));
	}
	if (empty($price)) {
		$price = 0;
	}
	if (empty($participants)) {
		$participants = 0;
	}

	if($end_type == 1) {
		$ending = strtotime($ending);
		if ($ending < time()) {
			exit (json_encode(array('status' => '2', 'input' => 'ending', 'reply' => 'Неверная дата!')));
		}
	} else {
		$ending =  time()+60*24*60*60;
		if ($participants == 0) {
			exit (json_encode(array('status' => '2', 'input' => 'participants', 'reply' => 'Не менее 1')));
		}
	}

	if($own_prize == 2) {
		$prize = array();
		$i = 0;
		foreach($_POST as $key => $value) {
			if(substr($key, 0, 9) == "prizetype") {
				$param = explode("_", $key);

				$j = $param[1];
				$l = $param[2];

				if($i == 0) {
					$place = 1;
					$place_i = $j;
				} else {
					if($place_i != $j) {
						$place++;
						$place_i = $j;
					}
				}

				$prize[$i]['type'] = check($value, "int");
				$prize[$i]['place'] = $place;

				if($value == 1 || $value == 5 || $value == 6) {
					if(!empty($_POST['server'.$j.'_'.$l])) {
						$prize[$i]['server'] = check($_POST['server'.$j.'_'.$l], "int");
					} else {
						exit (json_encode(array('status' => '2', 'input' => 'server'.$j.'_'.$l, 'reply' => 'Заполните')));
					}
					if(!empty($_POST['service'.$j.'_'.$l])) {
						$prize[$i]['service'] = check($_POST['service'.$j.'_'.$l], "int");
					} else {
						exit (json_encode(array('status' => '2', 'input' => 'service'.$j.'_'.$l, 'reply' => 'Заполните')));
					}
					if(!empty($_POST['tarif'.$j.'_'.$l])) {
						$prize[$i]['tarif'] = check($_POST['tarif'.$j.'_'.$l], "int");
					} else {
						exit (json_encode(array('status' => '2', 'input' => 'tarif'.$j.'_'.$l, 'reply' => 'Заполните')));
					}
				}
				if($value == 2) {
					if(!empty($_POST['money'.$j.'_'.$l])) {
						$prize[$i]['money'] = check($_POST['money'.$j.'_'.$l], "int");
					} else {
						exit (json_encode(array('status' => '2', 'input' => 'money'.$j.'_'.$l, 'reply' => 'Заполните')));
					}
				}
				if($value == 3) {
					if(!empty($_POST['percent'.$j.'_'.$l])) {
						$prize[$i]['percent'] = check($_POST['percent'.$j.'_'.$l], "int");
						if($prize[$i]['percent'] > 99) {
							exit (json_encode(array('status' => '2', 'input' => 'percent'.$j.'_'.$l, 'reply' => 'Не более 99')));
						}
					} else {
						exit (json_encode(array('status' => '2', 'input' => 'percent'.$j.'_'.$l, 'reply' => 'Заполните')));
					}
				}
				if($value == 4) {
					if(!empty($_POST['server'.$j.'_'.$l])) {
						$prize[$i]['server'] = check($_POST['server'.$j.'_'.$l], "int");
					} else {
						exit (json_encode(array('status' => '2', 'input' => 'server'.$j.'_'.$l, 'reply' => 'Заполните')));
					}
					if(!empty($_POST['tarif'.$j.'_'.$l])) {
						$prize[$i]['tarif'] = check($_POST['tarif'.$j.'_'.$l], "int");
					} else {
						exit (json_encode(array('status' => '2', 'input' => 'tarif'.$j.'_'.$l, 'reply' => 'Заполните')));
					}
				}
				if(!empty($prize[$i]['type'])) {
					$i++;
				}
			}
		}
		if(empty($prize)) {
			exit(json_encode(array('status' => '2')));
		} else {
			$prize = serialize($prize);
		}
	} else {
		if (empty($count_of_winners)) {
			exit (json_encode(array('status' => '2', 'input' => 'count_of_winners', 'reply' => 'Заполните')));
		}

		if($count_of_winners > 99) {
			$count_of_winners = 99;
		}

		include_once '../../../inc/classes/HTMLPurifier/HTMLPurifier.auto.php';
		$prize = $Purifier->purify($_POST['text']);
		$prize = find_img_mp3($prize, 1, 1);

		if (empty($prize)) {
			exit (json_encode(array('status' => '2', 'input' => 'text', 'reply' => 'Заполните')));
		}

		$prizes = array();
		$prizes['description'] = $prize;
		$prizes['count_of_winners'] = $count_of_winners;

		$prize = serialize($prizes);
	}

	if($type == 1) {
		$STH = $pdo->prepare("INSERT INTO `sortition` (`name`,`how_old`,`ending`,`price`,`participants`,`show_participants`,`prize`,`own_prize`,`end_type`) values (:name,:how_old,:ending,:price,:participants,:show_participants,:prize,:own_prize,:end_type)");
		$STH->execute(array( ':name' => $name, ':how_old' => $how_old, ':ending' => $ending, ':price' => $price, ':participants' => $participants, ':show_participants' => $show_participants, ':prize' => $prize, ':own_prize' => $own_prize, ':end_type' => $end_type ));
	} else {
		$STH = $pdo->prepare("UPDATE `sortition` SET `name`=:name, `how_old`=:how_old, `ending`=:ending, `price`=:price, `participants`=:participants, `show_participants`=:show_participants, `prize`=:prize, `own_prize`=:own_prize, `end_type`=:end_type LIMIT 1");
		$STH->execute(array( ':name' => $name, ':how_old' => $how_old, ':ending' => $ending, ':price' => $price, ':participants' => $participants, ':show_participants' => $show_participants, ':prize' => $prize, ':own_prize' => $own_prize, ':end_type' => $end_type ));
	}

	exit(json_encode(array('status' => '1')));
}
if(isset($_POST['dell_sortition'])) {
	$pdo->exec("DELETE FROM `sortition`");
	$pdo->exec("DELETE FROM `sortition__participants`");
}
?>