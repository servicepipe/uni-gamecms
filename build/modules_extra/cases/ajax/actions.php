<?php
include_once "../../../inc/start.php";
include_once "../../../inc/protect.php";
include_once "../base/config.php";

if(empty($_POST['phpaction'])) {
	log_error("Прямой вызов actions.php");
	exit(json_encode(array('status' => '2')));
}

if(isset($_POST['get_case_banner'])) {
	$tpl = new Template;
	$tpl->dir = '../../../templates/'.$conf->template.'/tpl/';
	$tpl->load_template($module['tpl_dir']."banner.tpl");
	$tpl->set("{template}", $conf->template);
	$tpl->compile( 'content' );
	$tpl->show($tpl->result['content']);
	$tpl->global_clear();
	exit();
}
if(isset($_POST['load_cases'])) {
	$tpl = new Template;
	$tpl->dir = '../../../templates/'.$conf->template.'/tpl/';
	$tpl->result['content'] = '';

	$STH = $pdo->query("SELECT `cases`.`id`, `cases`.`name`, `cases__images`.`url` AS 'image', `cases`.`price` FROM `cases` 
		LEFT JOIN `cases__images` ON `cases`.`image`=`cases__images`.`id` ORDER BY `cases`.`trim`"); $STH->setFetchMode(PDO::FETCH_OBJ);
	while($row = $STH->fetch()) { 
		$tpl->load_template($module['tpl_dir']."elements/case.tpl");
		$tpl->set("{site_host}", $site_host);
		$tpl->set("{template}", $conf->template);
		$tpl->set("{id}", $row->id);
		$tpl->set("{name}", $row->name);
		$tpl->set("{image}", $row->image);
		$tpl->set("{price}", $row->price);
		$tpl->compile( 'content' );
	}

	if($tpl->result['content'] == '') {
		$tpl->result['content'] = '<p>Кейсов нет</p>';
	}

	$tpl->show($tpl->result['content']);
	$tpl->global_clear();
	exit();
}
if(isset($_POST['load_subjects'])) {
	$case = check($_POST['case_id'], "int");

	if(empty($case)) {
		exit('<p>Кейс пуст</p>');
	}

	$STH = $pdo->prepare("SELECT `subjects` FROM `cases` WHERE `id`=:id LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
	$STH->execute(array( ':id' => $case ));
	$row = $STH->fetch();

	if(empty($row->subjects)) {
		exit('<p>Кейс пуст</p>');
	}
	
	$subjects_types = get_subjects_types($pdo);
	$subjects = unserialize($row->subjects);
	$count = count($subjects);
	$place = 0;
	if(isset($subjects[$count-1]['place'])) {
		$places = $subjects[$count-1]['place'];
	} else {
		$places = 0;
	}
	$glob_i = 1;
	for ($i=1; $i <= $places; $i++) {
		$place++;
		$class = get_item_class($subjects[$glob_i]['chance']);
	?>
	<div class="subject-block <?php echo $class; ?>">
		<div class="b-top"></div>
		<div class="b-bottom"></div>
		<div class="b-left"></div>
		<div class="b-right"></div>
		<div class="subject-services">
			<div class="subject-fix">
			<?php
			for ($j=0; $j < $count; $j++) { 
				if($subjects[$j]['place'] == $place) {
					$glob_i++;
					if($subjects[$j]['type'] == 1 || $subjects[$j]['type'] == 5 || $subjects[$j]['type'] == 6 || $subjects[$j]['type'] == 7) {
						$params = get_types_params($subjects[$j]['type']);

						$STH = $pdo->prepare("SELECT `servers`.`name` AS `server_name`,`$params[2]`.`name`,`$params[2]`.`text`, `$params[3]`.`time` FROM `$params[2]` 
							LEFT JOIN `$params[3]` ON `$params[3]`.`service`=`$params[2]`.`id`
							LEFT JOIN `servers` ON `servers`.`id`=`$params[2]`.`server` WHERE `$params[2]`.`id`=:service AND `servers`.`id`=:server AND `$params[3]`.`id`=:tarif LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
						$STH->execute(array( ':service' => $subjects[$j]['service'], ':server' => $subjects[$j]['server'], ':tarif' => $subjects[$j]['tarif'] ));
						$row = $STH->fetch();

						if($row->time == 0) {
							$row->time = 'Навсегда';
						} else {
							$row->time = $row->time.' дня(ей)';
						}

						?>
						<div class="subject" data-target="#subject<?php echo $place; ?>_<?php echo $j; ?>" data-toggle="modal">
							<span>
								«<?php echo $row->name; ?>»
							</span>
							<span>
								<?php echo $row->server_name; ?>
							</span>
							<span>
								<?php echo $row->time; ?>
							</span>
						</div>
						<div id="subject<?php echo $place; ?>_<?php echo $j; ?>" class="modal fade">
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
					if($subjects[$j]['type'] == 2) {
						?>
						<div class="subject" data-target="#subject<?php echo $place; ?>_<?php echo $j; ?>" data-toggle="modal">
							<span>
								«<?php echo $subjects[$j]['money']; ?> руб»
							</span>
							<span>
								на Ваш баланс
							</span>
						</div>
						<div id="subject<?php echo $place; ?>_<?php echo $j; ?>" class="modal fade">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-header">
										<h4 class="modal-title" style="margin-top: 0px;">
											<?php echo $subjects[$j]['money']; ?> рублей
											<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
										</h4>
									</div>
									<div class="modal-body">
										Призом является <b><?php echo $subjects[$j]['money']; ?></b> рублей на Ваш баланс на данном проекте.
									</div>
								</div>
							</div>
						</div>
						<?php
					}
					if($subjects[$j]['type'] == 3) {
						?>
						<div class="subject" data-target="#subject<?php echo $place; ?>_<?php echo $j; ?>" data-toggle="modal">
							<span>
								«<?php echo $subjects[$j]['percent']; ?>% скидка»
							</span>
							<span>
								на все услуги
							</span>
						</div>

						<div id="subject<?php echo $place; ?>_<?php echo $j; ?>" class="modal fade">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-header">
										<h4 class="modal-title" style="margin-top: 0px;">
											<?php echo $subjects[$j]['percent']; ?>% скидка
											<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
										</h4>
									</div>
									<div class="modal-body">
										Призом является <b><?php echo $subjects[$j]['percent']; ?>%</b> скидка на все услуги на данном проекте.
									</div>
								</div>
							</div>
						</div>
						<?php
					}
					if($subjects[$j]['type'] == 4) {
						$STH = $pdo->prepare("SELECT `servers`.`name` AS `server_name`, `sk_services`.`number`, `sk_services`.`type` FROM `sk_services` 
							LEFT JOIN `servers` ON `servers`.`id`=`sk_services`.`server` WHERE `sk_services`.`id`=:tarif AND `servers`.`id`=:server LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
						$STH->execute(array( ':server' => $subjects[$j]['server'], ':tarif' => $subjects[$j]['tarif'] ));
						$row = $STH->fetch();

						?>
						<div class="subject" data-target="#subject<?php echo $place; ?>_<?php echo $j; ?>" data-toggle="modal">
							<span>
								«<?php echo $row->number; ?> - <?php echo $services_data[$row->type]['name']; ?>»
							</span>
							<span>
								<?php echo $row->server_name; ?>
							</span>
						</div>
						<div id="subject<?php echo $place; ?>_<?php echo $j; ?>" class="modal fade">
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
			?>
			</div>
		</div>
	</div>
	<?php
	}
	exit();
}
if(isset($_POST['load_roulette'])) {
	$case = check($_POST['case_id'], "int");

	if(empty($case)) {
		exit();
	}

	$STH = $pdo->prepare("SELECT `subjects` FROM `cases` WHERE `id`=:id LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
	$STH->execute(array( ':id' => $case ));
	$row = $STH->fetch();

	if(empty($row->subjects)) {
		exit();
	}
	
	$subjects_types = get_subjects_types($pdo);
	$subjects = unserialize($row->subjects);
	$count = count($subjects);
	$place = 0;
	if(isset($subjects[$count-1]['place'])) {
		$places = $subjects[$count-1]['place'];
	} else {
		$places = 0;
	}
	$glob_i = 1;
	for ($i=1; $i <= $places; $i++) {
		$place++;
		$class = get_item_class($subjects[$glob_i]['chance']);
	?>
	<div class="subject-block <?php echo $class; ?>" data-value="<?php echo $place-1; ?>">
		<div class="b-top"></div>
		<div class="b-bottom"></div>
		<div class="b-left"></div>
		<div class="b-right"></div>
		<div class="subject-services">
			<div class="subject-fix">
			<?php
			for ($j=0; $j < $count; $j++) { 
				if($subjects[$j]['place'] == $place) {
					$glob_i++;
					if($subjects[$j]['type'] == 1 || $subjects[$j]['type'] == 5 || $subjects[$j]['type'] == 6 || $subjects[$j]['type'] == 7) {
						$params = get_types_params($subjects[$j]['type']);

						$STH = $pdo->prepare("SELECT `servers`.`name` AS `server_name`,`$params[2]`.`name`,`$params[2]`.`text`, `$params[3]`.`time` FROM `$params[2]` 
							LEFT JOIN `$params[3]` ON `$params[3]`.`service`=`$params[2]`.`id`
							LEFT JOIN `servers` ON `servers`.`id`=`$params[2]`.`server` WHERE `$params[2]`.`id`=:service AND `servers`.`id`=:server AND `$params[3]`.`id`=:tarif LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
						$STH->execute(array( ':service' => $subjects[$j]['service'], ':server' => $subjects[$j]['server'], ':tarif' => $subjects[$j]['tarif'] ));
						$row = $STH->fetch();

						if($row->time == 0) {
							$row->time = 'Навсегда';
						} else {
							$row->time = $row->time.' дня(ей)';
						}

						?>
						<div class="subject">
							<span>
								«<?php echo $row->name; ?>»
							</span>
							<span>
								<?php echo $row->server_name; ?>
							</span>
							<span>
								<?php echo $row->time; ?>
							</span>
						</div>
						<?php
					}
					if($subjects[$j]['type'] == 2) {
						?>
						<div class="subject">
							<span>
								«<?php echo $subjects[$j]['money']; ?> руб»
							</span>
							<span>
								на Ваш баланс
							</span>
						</div>
						<?php
					}
					if($subjects[$j]['type'] == 3) {
						?>
						<div class="subject">
							<span>
								«<?php echo $subjects[$j]['percent']; ?>% скидка»
							</span>
							<span>
								на все услуги
							</span>
						</div>
						<?php
					}
					if($subjects[$j]['type'] == 4) {
						$STH = $pdo->prepare("SELECT `servers`.`name` AS `server_name`, `sk_services`.`number`, `sk_services`.`type` FROM `sk_services` 
							LEFT JOIN `servers` ON `servers`.`id`=`sk_services`.`server` WHERE `sk_services`.`id`=:tarif AND `servers`.`id`=:server LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
						$STH->execute(array( ':server' => $subjects[$j]['server'], ':tarif' => $subjects[$j]['tarif'] ));
						$row = $STH->fetch();

						?>
						<div class="subject">
							<span>
								«<?php echo $row->number; ?> - <?php echo $services_data[$row->type]['name']; ?>»
							</span>
							<span>
								<?php echo $row->server_name; ?>
							</span>
						</div>
						<?php
					}
				}
			}
			?>
			</div>
		</div>
	</div>
	<?php
	}
	exit();
}
if(isset($_POST['open_case'])) {
	$id = check($_POST['case_id'], "int");

	if (empty($id)) {
		exit(json_encode(array('status' => '2', 'data' => 'Кейс не найден, попробуй обновить страницу')));
	}

	if(!is_auth()){
		exit(json_encode(array( 'status' => '2', 'data' => 'Авторизуйтесь, чтобы открыть кейс.')));
	}

	$STH = $pdo->prepare("SELECT `id` FROM `cases__wins` WHERE `user_id`=:user_id AND `finished` != '1' LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
	$STH->execute(array( ':user_id' => $_SESSION['id'] ));
	$row = $STH->fetch();
	if(isset($row->id)) {
		exit(json_encode(array( 'status' => '2', 'data' => 'Не так быстро... Обнови страницу и попробуй заново.')));
	}

	$STH = $pdo->prepare("SELECT `id`, `date`, `count` FROM `last_actions` WHERE `user_id`=:user_id AND `action_type`=:action_type LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
	$STH->execute(array( ':user_id' => $_SESSION['id'], ':action_type' => 5 ));
	$row = $STH->fetch();

	if(empty($row->id)) {
		$STH = $pdo->prepare("INSERT INTO `last_actions` (`date`,`user_id`,`action_type`,`count`) values (:date, :user_id, :action_type, :count)");  
		$STH->execute(array( ':date' => time(), ':user_id' => $_SESSION['id'], ':action_type' => 5, ':count' => 0 ));
	} else {
		if($row->count > $conf->violations_number) {
			exit('Flood: pass a bot check ['.$conf->captcha."]");
		}

		if((time() - $row->date) < $conf->violations_delta) {
			$row->count++;

			$STH = $pdo->prepare("UPDATE `last_actions` SET `date`=:date, `count`=:count WHERE `id`=:id LIMIT 1");
			$STH->execute(array( ':date' => time(), ':count' => $row->count, ':id' => $row->id ));
		} else {
			$STH = $pdo->prepare("UPDATE `last_actions` SET `date`=:date WHERE `id`=:id LIMIT 1");
			$STH->execute(array( ':date' => time(), ':id' => $row->id ));
		}
	}

	$STH = $pdo->prepare("SELECT * FROM `cases` WHERE `id`=:id LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
	$STH->execute(array( ':id' => $id ));
	$case = $STH->fetch();

	if(empty($case->id)) {
		exit(json_encode(array('status' => '2', 'data' => 'Кейс не найден, попробуй обновить страницу')));
	}

	$STH = $pdo->prepare("SELECT `id`, `shilings` FROM `users` WHERE `id`=:id LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
	$STH->execute(array( ':id' => $_SESSION['id'] ));
	$user = $STH->fetch();
	if (empty($user->id)){
		exit (json_encode(array('status' => '2', 'data' => 'Пользователь не найден')));
	}

	include '../../../inc/classes/Random/random.php';

	$item_percent = random_int(1, 100);
	$subjects = unserialize($case->subjects);
	$count = count($subjects);
	
	if(isset($subjects[$count-1]['place'])) {
		$places = $subjects[$count-1]['place'];
	} else {
		exit (json_encode(array('status' => '2', 'data' => 'Предметы не найдены.')));
	}

	if ($user->shilings < $case->price){
		$price_delta = round_shilings($case->price - $user->shilings, 2);
		exit (json_encode(array('status' => '2', 'data' => 'У Вас недостаточно средств <span class="m-icon icon-bank"></span><br><a href="../purse?price='.$price_delta.'">Пополните баланс на '.$price_delta.$messages['RUB'].'.</a>')));
	}
	$shilings = round_shilings($user->shilings - $case->price, 2);

	$STH = $pdo->prepare("UPDATE `users` SET `shilings`=:shilings WHERE `id`=:id LIMIT 1");
	$STH->execute(array( ':shilings' => $shilings, ':id' => $_SESSION['id'] ));

	$STH = $pdo->prepare("INSERT INTO `money__actions` (date,shilings,author,type) values (:date, :shilings, :author, :type)");  
	$STH->execute(array( 'date' => date("Y-m-d H:i:s"), 'shilings' => -$case->price, 'author' => $_SESSION['id'], 'type' => '18' ));

	$place = 0;
	$items = array(0 => array('percent' => 0));

	for ($i=1; $i <= $places; $i++) {
		$place++;
		for ($j=0; $j < $count; $j++) { 
			if($subjects[$j]['place'] == $place) {
				$items[$place]['percent'] = $items[$place-1]['percent'] + $subjects[$j]['chance'];
				if($item_percent <= $items[$place]['percent']) {
					$item = $place - 1;
					break(2);
				}
				break;
			}
		}
	}

	$l = 0;
	for ($i=1; $i < $count; $i++) { 
		if($subjects[$i]['place'] == $place) {
			$prize[$l] = $subjects[$i];
			$l++;
		}
	}

	$prize = serialize($prize);
	$time = time();

	$STH = $pdo->prepare("INSERT INTO `cases__wins` (`case_id`,`item`,`user_id`,`time`) values (:case_id, :item, :user_id, :time)");  
	$STH->execute(array( ':case_id' => $id, ':item' => $prize, ':user_id' => $_SESSION['id'], ':time' => $time ));

	$STH = $pdo->prepare("SELECT `id` FROM `cases__wins` WHERE `case_id`=:case_id AND `item`=:item AND `user_id`=:user_id AND `time`=:time LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
	$STH->execute(array( ':case_id' => $id, ':item' => $prize, ':user_id' => $_SESSION['id'], ':time' => $time ));
	$row = $STH->fetch();

	exit(json_encode(array('status' => '1', 'item' => $item, 'win_id' => $row->id)));
}
if(isset($_POST['show_prize'])) {
	$id = check($_POST['id'], "int");

	if (empty($id)) {
		exit();
	}

	if(!is_auth()){
		exit();
	}

	$STH = $pdo->prepare("SELECT `cases__wins`.*, `cases`.`name`,`users`.`login`,`users`.`rights` FROM `cases__wins` 
		LEFT JOIN `cases` ON `cases__wins`.`case_id` = `cases`.`id` 
		LEFT JOIN `users` ON `cases__wins`.`user_id` = `users`.`id` 
		WHERE `cases__wins`.`id`=:id AND `cases__wins`.`user_id`=:user_id LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
	$STH->execute(array( ':id' => $id, ':user_id' => $_SESSION['id'] ));
	$prize = $STH->fetch();
	if(empty($prize->id)) {
		exit();
	}

	if($prize->finished == 0) {
		include_once "../base/start.php";
	}

	$subjects_types = get_subjects_types($pdo);
	$subjects = unserialize($prize->item);
	$count = count($subjects);
	$class = get_item_class($subjects[0]['chance']);
	?>
	<div class="subject-block <?php echo $class; ?>">
		<div class="b-top"></div>
		<div class="b-bottom"></div>
		<div class="b-left"></div>
		<div class="b-right"></div>
		<div class="subject-services">
			<div class="subject-fix">
			<?php
			for ($j=0; $j < $count; $j++) {
				if($subjects[$j]['type'] == 1 || $subjects[$j]['type'] == 5 || $subjects[$j]['type'] == 6 || $subjects[$j]['type'] == 7) {
					$params = get_types_params($subjects[$j]['type']);

					$STH = $pdo->prepare("SELECT `servers`.`name` AS `server_name`,`$params[2]`.`name`,`$params[2]`.`text`, `$params[3]`.`time` FROM `$params[2]` 
						LEFT JOIN `$params[3]` ON `$params[3]`.`service`=`$params[2]`.`id`
						LEFT JOIN `servers` ON `servers`.`id`=`$params[2]`.`server` WHERE `$params[2]`.`id`=:service AND `servers`.`id`=:server AND `$params[3]`.`id`=:tarif LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
					$STH->execute(array( ':service' => $subjects[$j]['service'], ':server' => $subjects[$j]['server'], ':tarif' => $subjects[$j]['tarif'] ));
					$row = $STH->fetch();

					if($row->time == 0) {
						$row->time = 'Навсегда';
					} else {
						$row->time = $row->time.' дня(ей)';
					}

					?>
					<div class="subject">
						<span>
							«<?php echo $row->name; ?>»
						</span>
						<span>
							<?php echo $row->server_name; ?>
						</span>
						<span>
							<?php echo $row->time; ?>
						</span>
					</div>
					<?php
				}
				if($subjects[$j]['type'] == 2) {
					?>
					<div class="subject">
						<span>
							«<?php echo $subjects[$j]['money']; ?> руб»
						</span>
						<span>
							на Ваш баланс
						</span>
					</div>
					<?php
				}
				if($subjects[$j]['type'] == 3) {
					?>
					<div class="subject">
						<span>
							«<?php echo $subjects[$j]['percent']; ?>% скидка»
						</span>
						<span>
							на все услуги
						</span>
					</div>
					<?php
				}
				if($subjects[$j]['type'] == 4) {
					$STH = $pdo->prepare("SELECT `servers`.`name` AS `server_name`, `sk_services`.`number`, `sk_services`.`type` FROM `sk_services` 
						LEFT JOIN `servers` ON `servers`.`id`=`sk_services`.`server` WHERE `sk_services`.`id`=:tarif AND `servers`.`id`=:server LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
					$STH->execute(array( ':server' => $subjects[$j]['server'], ':tarif' => $subjects[$j]['tarif'] ));
					$row = $STH->fetch();

					?>
					<div class="subject">
						<span>
							«<?php echo $row->number; ?> - <?php echo $services_data[$row->type]['name']; ?>»
						</span>
						<span>
							<?php echo $row->server_name; ?>
						</span>
					</div>
					<?php
				}
			}
			?>
			</div>
		</div>
	</div>
	<?php
	exit();
}
if(isset($_POST['get_my_cases'])) {
	if(!is_auth()){
		exit();
	}

	$i = 0;
	$STH = $pdo->prepare("SELECT `cases__wins`.`item`, `cases__wins`.`time`, `cases`.`name`, `cases`.`price`, `cases__images`.`url` FROM `cases__wins`
		LEFT JOIN `cases` ON `cases__wins`.`case_id` = `cases`.`id`
		LEFT JOIN `cases__images` ON `cases`.`image` = `cases__images`.`id`
		WHERE `cases__wins`.`user_id`=:user_id AND `cases__wins`.`finished`='1' ORDER BY `cases__wins`.`id` DESC LIMIT 10"); $STH->setFetchMode(PDO::FETCH_OBJ);
	$STH->execute(array( ':user_id' => $_SESSION['id'] ));
	while($prize = $STH->fetch()) { 
		$i++;
		?>
		<div class="opened-case">
			<div class="case-img">
				<img src="../<?php echo $prize->url; ?>" alt="<?php echo $prize->name; ?>">
			</div>
			<div class="case-info">
				<h3><?php echo $prize->name; ?> - <?php echo $prize->price; ?><?php echo $messages['RUB']; ?></h3>
				<small><?php echo expand_date($prize->time, 7); ?></small>
				<?php
					$subjects_types = get_subjects_types($pdo);
					$subjects = unserialize($prize->item);
					$count = count($subjects);
			
					for ($j=0; $j < $count; $j++) {
						if($subjects[$j]['type'] == 1 || $subjects[$j]['type'] == 5 || $subjects[$j]['type'] == 6 || $subjects[$j]['type'] == 7) {
							$params = get_types_params($subjects[$j]['type']);

							$STH2 = $pdo->prepare("SELECT `servers`.`name` AS `server_name`,`$params[2]`.`name`,`$params[2]`.`text`, `$params[3]`.`time` FROM `$params[2]` 
								LEFT JOIN `$params[3]` ON `$params[3]`.`service`=`$params[2]`.`id`
								LEFT JOIN `servers` ON `servers`.`id`=`$params[2]`.`server` WHERE `$params[2]`.`id`=:service AND `servers`.`id`=:server AND `$params[3]`.`id`=:tarif LIMIT 1"); $STH2->setFetchMode(PDO::FETCH_OBJ);
							$STH2->execute(array( ':service' => $subjects[$j]['service'], ':server' => $subjects[$j]['server'], ':tarif' => $subjects[$j]['tarif'] ));
							$row = $STH2->fetch();

							if($row->time == 0) {
								$row->time = 'Навсегда';
							} else {
								$row->time = $row->time.' дня(ей)';
							}

							?>
							<div class="subject">
								<b>«<?php echo $row->name; ?>»</b> на <?php echo $row->server_name; ?> - <?php echo $row->time; ?>
							</div>
							<?php
						}
						if($subjects[$j]['type'] == 2) {
							?>
							<div class="subject">
								<b>«<?php echo $subjects[$j]['money']; ?> руб»</b> на Ваш баланс
							</div>
							<?php
						}
						if($subjects[$j]['type'] == 3) {
							?>
							<div class="subject">
								<b>«<?php echo $subjects[$j]['percent']; ?>% скидка»</b> на все услуги
							</div>
							<?php
						}
						if($subjects[$j]['type'] == 4) {
							$STH2 = $pdo->prepare("SELECT `servers`.`name` AS `server_name`, `sk_services`.`number`, `sk_services`.`type` FROM `sk_services` 
								LEFT JOIN `servers` ON `servers`.`id`=`sk_services`.`server` WHERE `sk_services`.`id`=:tarif AND `servers`.`id`=:server LIMIT 1"); $STH2->setFetchMode(PDO::FETCH_OBJ);
							$STH2->execute(array( ':server' => $subjects[$j]['server'], ':tarif' => $subjects[$j]['tarif'] ));
							$row = $STH2->fetch();

							?>
							<div class="subject">
								<b>«<?php echo $row->number; ?> - <?php echo $services_data[$row->type]['name']; ?>»</b> на <?php echo $row->server_name; ?>
							</div>
							<?php
						}
					}
				?>
			</div>
			<div class="clearfix"></div>
		</div>
		<?php
	}
	if($i == 0) {
		?>
		<p>Кейсы еще не открывались</p>
		<?php
	}
	exit();
}
if(!is_admin()){
	exit(json_encode(array( 'status' => '2', 'data' => 'Досутпно только администратору' )));
}
if(isset($_POST['save_case'])) {
	$id = check($_POST['case_id'], "int");
	$name = check($_POST['name'], null);
	$price = check($_POST['price'], "float");
	$image = check($_POST['image'], "int");

	if (empty($id)) {
		$id = 0;
	}
	if (empty($name)) {
		exit (json_encode(array('status' => '2', 'input' => 'name_'.$id, 'reply' => 'Заполните')));
	}
	if (empty($price)) {
		exit (json_encode(array('status' => '2', 'input' => 'price_'.$id, 'reply' => 'Заполните')));
	}
	if (empty($image)) {
		exit (json_encode(array('status' => '2', 'input' => 'image_'.$id, 'reply' => 'Заполните')));
	}
	/*
	if(empty($_POST['chance_0'])) {
		exit (json_encode(array('status' => '2', 'input' => 'subjects_'.$id.' #chance_0', 'reply' => 'Заполните')));
	}
	*/

	$subjects = array(0 => array('chance' => 0, 'place' => 0, 'type' => 0));
	$chance_sum = 0;
	$i = 1;
	foreach($_POST as $key => $value) {
		if(substr($key, 0, 13) == "subject_type_") {
			$param = explode("_", $key);

			$j = $param[2];
			$l = $param[3];

			if(empty($_POST['chance_'.$j])) {
				exit (json_encode(array('status' => '2', 'input' => 'subjects_'.$id.' #chance_'.$j, 'reply' => 'Заполните')));
			}
			
			$subjects[$i]['type'] = check($value, "int");
			$subjects[$i]['chance'] = check($_POST['chance_'.$j], "int");

			if($i == 1) {
				$place = 1;
				$place_i = $j;
				$chance_sum += $subjects[$i]['chance'];
			} else {
				if($place_i != $j) {
					$place++;
					$place_i = $j;
					$chance_sum += $subjects[$i]['chance'];
				}
			}

			$subjects[$i]['place'] = $place;
			
			if($value == 1 || $value == 5 || $value == 6 || $value == 7) {
				if(!empty($_POST['server'.$j.'_'.$l])) {
					$subjects[$i]['server'] = check($_POST['server'.$j.'_'.$l], "int");
				} else {
					exit (json_encode(array('status' => '2', 'input' => 'subjects_'.$id.' #server'.$j.'_'.$l, 'reply' => 'Заполните')));
				}
				if(!empty($_POST['service'.$j.'_'.$l])) {
					$subjects[$i]['service'] = check($_POST['service'.$j.'_'.$l], "int");
				} else {
					exit (json_encode(array('status' => '2', 'input' => 'subjects_'.$id.' #service'.$j.'_'.$l, 'reply' => 'Заполните')));
				}
				if(!empty($_POST['tarif'.$j.'_'.$l])) {
					$subjects[$i]['tarif'] = check($_POST['tarif'.$j.'_'.$l], "int");
				} else {
					exit (json_encode(array('status' => '2', 'input' => 'subjects_'.$id.' #tarif'.$j.'_'.$l, 'reply' => 'Заполните')));
				}
			}
			if($value == 2) {
				if(!empty($_POST['money'.$j.'_'.$l])) {
					$subjects[$i]['money'] = check($_POST['money'.$j.'_'.$l], "float");
				} else {
					exit (json_encode(array('status' => '2', 'input' => 'subjects_'.$id.' #money'.$j.'_'.$l, 'reply' => 'Заполните')));
				}
			}
			if($value == 3) {
				if(!empty($_POST['percent'.$j.'_'.$l])) {
					$subjects[$i]['percent'] = check($_POST['percent'.$j.'_'.$l], "int");
					if($subjects[$i]['percent'] > 99) {
						exit (json_encode(array('status' => '2', 'input' => 'subjects_'.$id.' #percent'.$j.'_'.$l, 'reply' => 'Не более 99')));
					}
				} else {
					exit (json_encode(array('status' => '2', 'input' => 'subjects_'.$id.' #percent'.$j.'_'.$l, 'reply' => 'Заполните')));
				}
			}
			if($value == 4) {
				if(!empty($_POST['server'.$j.'_'.$l])) {
					$subjects[$i]['server'] = check($_POST['server'.$j.'_'.$l], "int");
				} else {
					exit (json_encode(array('status' => '2', 'input' => 'subjects_'.$id.' #server'.$j.'_'.$l, 'reply' => 'Заполните')));
				}
				if(!empty($_POST['tarif'.$j.'_'.$l])) {
					$subjects[$i]['tarif'] = check($_POST['tarif'.$j.'_'.$l], "int");
				} else {
					exit (json_encode(array('status' => '2', 'input' => 'subjects_'.$id.' #tarif'.$j.'_'.$l, 'reply' => 'Заполните')));
				}
			}
			if(!empty($subjects[$i]['type'])) {
				$i++;
			}
		}
	}

	if($place < 3) {
		exit (json_encode(array('status' => '2', 'reply' => 'Кейс должен содержать не менее 3х предметов.')));
	}

	if($chance_sum != 100) {
		exit(json_encode(array('status' => '3')));
	}

	if(empty($subjects)) {
		exit(json_encode(array('status' => '2')));
	} else {
		$subjects = serialize($subjects);
	}

	if($id == 0) {
		$STH = $pdo->query("SELECT `trim` FROM `cases` ORDER BY `trim` DESC LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
		$row = $STH->fetch();
		if(isset($row->trim)) {
			$trim = $row->trim+1;
		} else {
			$trim = 1;
		}

		$STH = $pdo->prepare("INSERT INTO `cases` (`name`,`price`,`image`,`subjects`,`trim`) values (:name,:price,:image,:subjects,:trim)");
		$STH->execute(array( ':name' => $name, ':price' => $price, ':image' => $image, ':subjects' => $subjects, ':trim' => $trim ));
	} else {
		$STH = $pdo->prepare("UPDATE `cases` SET `name`=:name, `price`=:price, `image`=:image, `subjects`=:subjects WHERE `id`=:id LIMIT 1");
		$STH->execute(array( ':name' => $name, ':price' => $price, ':image' => $image, ':subjects' => $subjects, ':id' => $id ));
	}

	exit(json_encode(array('status' => '1')));
}
if(isset($_POST['dell_case'])) {
	$id = check($_POST['case_id'], "int");

	$STH = $pdo->prepare("DELETE FROM `cases` WHERE `id`=:id LIMIT 1");
	$STH->execute(array( ':id' => $id ));
	exit();
}
if(isset($_POST['load_case_image'])) {
	$case_id = check($_POST['case_id'], "int");

	if(empty($case_id)) {
		$case_id = 0;
	}

	if (empty($_FILES['image']['name'])) {
		exit('<script>show_input_error("image", "", null);setTimeout(show_error, 500);</script>');
	}
	if (!if_img($_FILES['image']['name'])) {
		exit('<p class="text-danger">Изображение должено быть в формате JPG,GIF,BMP или PNG</p><script>show_input_error("image", "", null);setTimeout(show_error, 500);</script>');
	}

	$path = 'modules_extra/cases/templates/_cases_images/';

	$STH = $pdo->query("SELECT `id` FROM `cases__images` ORDER BY `id` DESC LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
	$row = $STH->fetch();

	if(empty($row->id)) {
		$image = $path."1.png";
	} else {
		$row->id++;
		$image = $path.$row->id.".png";
	}

	if (!move_uploaded_file($_FILES['image']['tmp_name'], '../../../'. $image)) {
		exit('<p class="text-danger">Ошибка загрузки файла!</p>');
	}

	$STH = $pdo->prepare("INSERT INTO `cases__images` (`url`) values (:url)");  
	$STH->execute(array( ':url' => $image ));

	exit('<script>get_cases_images('.$case_id.');</script>');
}
if(isset($_POST['get_cases_images'])) {
	$case_id = check($_POST['case_id'], "int");

	$STH = $pdo->query("SELECT `id`, `url` FROM `cases__images`"); $STH->setFetchMode(PDO::FETCH_OBJ);
	while($row = $STH->fetch()) { 
		?>
		<div class="case-image" id="case_image_<?php echo $row->id; ?>">
			<a href="../<?php echo $row->url; ?>" data-lightbox="<?php echo $case_id; ?>"><img src="../<?php echo $row->url; ?>?cache=<?php echo rand(1,1000); ?>"></a>
			<span>
				<a onclick="set_case_image(<?php echo $case_id; ?>, <?php echo $row->id; ?>);" data-image-url="<?php echo $full_site_host.$row->url; ?>">Выбрать</a>
				<a onclick="dell_case_image(<?php echo $row->id; ?>);">Удалить</a>
			</span>
		</div>
		<?php
	}
	exit();
}
if(isset($_POST['dell_case_image'])) {
	$id = check($_POST['id'], "int");

	$STH = $pdo->prepare("SELECT `url` FROM `cases__images` WHERE `id`=:id LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
	$STH->execute(array( ':id' => $id ));
	$row = $STH->fetch();

	if(isset($row->url)) {
		unlink('../../../'.$row->url);

		$STH = $pdo->prepare("DELETE FROM `cases__images` WHERE `id`=:id LIMIT 1");
		$STH->execute(array( ':id' => $id ));

		exit(json_encode(array('status' => '1')));
	} else {
		exit(json_encode(array('status' => '1')));
	}
}
if(isset($_POST['get_cases'])) {
	$i = 0;
	$STH = $pdo->query("SELECT `cases`.`id`, `cases`.`name`, `cases__images`.`url` AS 'image', `cases`.`price` FROM `cases` 
		LEFT JOIN `cases__images` ON `cases`.`image`=`cases__images`.`id` ORDER BY `cases`.`trim`"); $STH->setFetchMode(PDO::FETCH_OBJ);
	while($row = $STH->fetch()) {
		$i++;
	?>
	<div class="col-md-3" id="case_<?php echo $row->id; ?>">
		<div class="block">
			<div class="block_head">
				<?php echo $row->name; ?> - <?php echo $row->price; ?><?php echo $messages['RUB']; ?>
			</div>
			<img src="../<?php echo $row->image; ?>"><br>
		
			<div class="btn-group">
				<a href="../admin/case?id=<?php echo $row->id; ?>" class="btn btn-default">
					<span class="glyphicon glyphicon-pencil" tooltip="yes" title="Редактировать"></span>
				</a>
				<a onclick="dell_case('<?php echo $row->id; ?>');" class="btn btn-default">
					<span class="glyphicon glyphicon-trash" tooltip="yes" title="Удалить"></span>
				</a>
				<a class="btn btn-default" onclick="up_case('<?php echo $row->id; ?>');">
					<span class="glyphicon glyphicon-chevron-up" tooltip="yes" title="Поднять"></span>
				</a>
				<a class="btn btn-default" onclick="down_case('<?php echo $row->id; ?>');">
					<span class="glyphicon glyphicon-chevron-down" tooltip="yes" title="Опустить"></span>
				</a>
			</div>
		</div>
	</div>
	<?php
	}
	if($i == 0) {
	?>
		<div class="col-md-12">
			<p>Кейсов нет</p>
		</div>
	<?php
	}
	exit();
}
if(isset($_POST['up_case'])) {
	$id = checkJs($_POST['case_id'], "int");

	$STH = $pdo->query("SELECT `trim` from `cases` WHERE `id`='$id' LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
	$row = $STH->fetch(); 
	if (empty($row->trim)) {
		exit(json_encode(array('status' => '2')));
	}
	if ($row->trim == 1) {
		exit(json_encode(array('status' => '1')));
	}

	$STH = $pdo->prepare("UPDATE `cases` SET `trim`=:trim WHERE `trim`=:trim2 LIMIT 1");
	$STH->execute(array('trim' => $row->trim, 'trim2' => $row->trim-1));

	$STH = $pdo->prepare("UPDATE `cases` SET `trim`=:trim WHERE `id`=:id LIMIT 1");
	$STH->execute(array('trim' => $row->trim-1, 'id' => $id));

	exit(json_encode(array('status' => '1')));
}
if(isset($_POST['down_case'])) {
	$id = checkJs($_POST['case_id'], "int");

	$STH = $pdo->query("SELECT `trim` from `cases` WHERE id='$id' LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
	$row = $STH->fetch(); 
	if (empty($row->trim)) {
		exit(json_encode(array('status' => '2')));
	}

	$STH = $pdo->query("SELECT `trim` from `cases` ORDER BY `trim` DESC LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
	$tmp = $STH->fetch();
	if ($row->trim == $tmp->trim) {
		exit(json_encode(array('status' => '1')));
	}

	$STH = $pdo->prepare("UPDATE `cases` SET `trim`=:trim WHERE `trim`=:trim2 LIMIT 1");
	$STH->execute(array('trim' => $row->trim, 'trim2' => $row->trim+1));

	$STH = $pdo->prepare("UPDATE `cases` SET `trim`=:trim WHERE `id`=:id LIMIT 1");
	$STH->execute(array('trim' => $row->trim+1, 'id' => $id));

	exit(json_encode(array('status' => '1')));
}
if(isset($_POST['get_subjects'])) {
	$case = checkJs($_POST['case'], "int");
	$place = 0;

	if(empty($case)) {
		$case = 0;
	} else {
		$STH = $pdo->prepare("SELECT `subjects` FROM `cases` WHERE `id`=:id LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
		$STH->execute(array( ':id' => $case ));
		$row = $STH->fetch();
	}

	if(isset($row->subjects)) {
		$subjects_types = get_subjects_types($pdo);
		$subjects = unserialize($row->subjects);
		$count = count($subjects);
		if(isset($subjects[$count-1]['place'])) {
			$places = $subjects[$count-1]['place'];
		} else {
			$places = 0;
		}
		$glob_i = 0;
		/*
		?>
		<div id="subject_0" class="subject-block">
			<b>Пустой предмет</b>
			<input class="form-control" name="chance_<?php echo $glob_i; ?>" id="chance_<?php echo $glob_i; ?>" placeholder="Шанс выпадения (0 - 100%)" value="<?php echo $subjects[$glob_i]['chance']; ?>" type="number" onchange="calculate_chance_sum(<?php echo $case; ?>);">
		</div>
		<?php
		*/

		$glob_i++;
		for ($i=1; $i <= $places; $i++) {
			$place++;
			$subject_count = 0;
		?>
		<div id="subject_<?php echo $place; ?>" class="subject-block">
			<b>Предмет (<a onclick="dell_subject(<?php echo $case; ?>, <?php echo $place; ?>)" class="c-p">Удалить</a>)</b>
			<input type="hidden" value="0" id="count_<?php echo $place; ?>">
			<input class="form-control" name="chance_<?php echo $place; ?>" id="chance_<?php echo $place; ?>" placeholder="Шанс выпадения (0 - 100%)" value="<?php echo $subjects[$glob_i]['chance']; ?>" type="number" onchange="calculate_chance_sum(<?php echo $case; ?>);">
			<div id="services_<?php echo $place; ?>">
			<?php
			for ($j=0; $j < $count; $j++) { 
				if($subjects[$j]['place'] == $place) {
					$subject_count++;
					$glob_i++;
					?>
					<input type="hidden" name="subject_type_<?php echo $place; ?>_<?php echo $subject_count; ?>" id="subject_type_<?php echo $place; ?>_<?php echo $subject_count; ?>" value="<?php echo $subjects[$j]['type']; ?>">
					<?php
					if($subjects[$j]['type'] == 1 || $subjects[$j]['type'] == 5 || $subjects[$j]['type'] == 6|| $subjects[$j]['type'] == 7) {
						$params = get_types_params($subjects[$j]['type']);
						$STH = $pdo->query("SELECT `id`,`name` FROM `servers` WHERE `$params[1]`!='0'");
						$STH->execute();
						$servers = $STH->fetchAll();
						if(count($servers) == 0) {
							?>
							<div class="input-group w-100" id="subject_line_<?php echo $place; ?>_<?php echo $subject_count; ?>">
								<span class="input-group-btn w-33">
									<select name="server<?php echo $place; ?>_<?php echo $subject_count; ?>" id="server<?php echo $place; ?>_<?php echo $subject_count; ?>" class="form-control">
									</select>
								</span>
								<span class="input-group-btn w-33">
									<select name="service<?php echo $place; ?>_<?php echo $subject_count; ?>" id="service<?php echo $place; ?>_<?php echo $subject_count; ?>" class="form-control">
									</select>
								</span>
								<span class="input-group-btn w-33">
									<select name="tarif<?php echo $place; ?>_<?php echo $subject_count; ?>" id="tarif<?php echo $place; ?>_<?php echo $subject_count; ?>" class="form-control">
									</select>
								</span>
								<span class="input-group-btn">
									<button onclick="dell_subject_line(<?php echo $case; ?>, <?php echo $place; ?>, <?php echo $subject_count; ?>)" class="btn btn-default" type="button">Удалить</button>
								</span>
							</div>
							<?php
						} else {
							$srv = $subjects[$j]['server'];
							$STH = $pdo->query("SELECT `id`, `name` FROM `$params[2]` WHERE `server`='$srv'");
							$STH->execute();
							$services = $STH->fetchAll();
							?>
							<div class="input-group w-100" id="subject_line_<?php echo $place; ?>_<?php echo $subject_count; ?>">
								<span class="input-group-btn w-33">
									<select name="server<?php echo $place; ?>_<?php echo $subject_count; ?>" id="server<?php echo $place; ?>_<?php echo $subject_count; ?>" class="form-control" onchange="get_services_subject(<?php echo $case; ?>, <?php echo $place; ?>, <?php echo $subject_count; ?>, <?php echo $subjects[$j]['type']; ?>);">
										<?php for ($l = 0; $l < count($servers); $l++) { ?>
											<?php if($servers[$l]['id'] == $subjects[$j]['server']) { ?>
												<option value="<?php echo $servers[$l]['id']; ?>" selected><?php echo $servers[$l]['name']; ?></option>
											<?php } else { ?>
												<option value="<?php echo $servers[$l]['id']; ?>"><?php echo $servers[$l]['name']; ?></option>
											<?php } ?>
										<?php } ?>
									</select>
								</span>
								<span class="input-group-btn w-33">
									<select name="service<?php echo $place; ?>_<?php echo $subject_count; ?>" id="service<?php echo $place; ?>_<?php echo $subject_count; ?>" class="form-control" onchange="get_tarifs_subject(<?php echo $case; ?>, <?php echo $place; ?>, <?php echo $subject_count; ?>, <?php echo $subjects[$j]['type']; ?>);">
										<?php for ($l = 0; $l < count($services); $l++) { ?>
											<?php if($services[$l]['id'] == $subjects[$j]['service']) { ?>
												<option value="<?php echo $services[$l]['id']; ?>" selected><?php echo $services[$l]['name']; ?></option>
											<?php } else { ?>
												<option value="<?php echo $services[$l]['id']; ?>"><?php echo $services[$l]['name']; ?></option>
											<?php } ?>
										<?php } ?>
									</select>
								</span>
								<span class="input-group-btn w-33">
									<select name="tarif<?php echo $place; ?>_<?php echo $subject_count; ?>" id="tarif<?php echo $place; ?>_<?php echo $subject_count; ?>" class="form-control">
										<?php
											if(count($services) != 0) {
												$srv = $subjects[$j]['service'];
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
													<?php if($tarifs[$l]['id'] == $subjects[$j]['tarif']) { ?>
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
									<button onclick="dell_subject_line(<?php echo $case; ?>, <?php echo $place; ?>, <?php echo $subject_count; ?>)" class="btn btn-default" type="button">Удалить</button>
								</span>
							</div>
							<?php
						}
					}
					if($subjects[$j]['type'] == 2) {
						?>
						<div class="input-group w-100" id="subject_line_<?php echo $place; ?>_<?php echo $subject_count; ?>">
							<span class="input-group-btn w-100">
								<input class="form-control" name="money<?php echo $place; ?>_<?php echo $subject_count; ?>" id="money<?php echo $place; ?>_<?php echo $subject_count; ?>" placeholder="Сумма" value="<?php echo $subjects[$j]['money']; ?>" type="text">
							</span>
							<span class="input-group-btn">
								<button onclick="dell_subject_line(<?php echo $case; ?>, <?php echo $place; ?>, <?php echo $subject_count; ?>)" class="btn btn-default" type="button">Удалить</button>
							</span>
						</div>
						<?php
					}
					if($subjects[$j]['type'] == 3) {
						?>
						<div class="input-group w-100" id="subject_line_<?php echo $place; ?>_<?php echo $subject_count; ?>">
							<span class="input-group-btn w-100">
								<input class="form-control" name="percent<?php echo $place; ?>_<?php echo $subject_count; ?>" id="percent<?php echo $place; ?>_<?php echo $subject_count; ?>" placeholder="Значение в %" value="<?php echo $subjects[$j]['percent']; ?>" type="number" maxlength="2">
							</span>
							<span class="input-group-btn">
								<button onclick="dell_subject_line(<?php echo $case; ?>, <?php echo $place; ?>, <?php echo $subject_count; ?>)" class="btn btn-default" type="button">Удалить</button>
							</span>
						</div>
						<?php
					}
					if($subjects[$j]['type'] == 4) {
						$STH = $pdo->query("SELECT `id`,`name` FROM `servers` WHERE `sk_host`!='0'");
						$STH->execute();
						$servers = $STH->fetchAll();
						if(count($servers) == 0) {
							?>
							<div class="input-group w-100" id="subject_line_<?php echo $place; ?>_<?php echo $subject_count; ?>">
								<span class="input-group-btn w-50">
									<select name="server<?php echo $place; ?>_<?php echo $subject_count; ?>" id="server<?php echo $place; ?>_<?php echo $subject_count; ?>" class="form-control">
									</select>
								</span>
								<span class="input-group-btn w-50">
									<select name="tarif<?php echo $place; ?>_<?php echo $subject_count; ?>" id="tarif<?php echo $place; ?>_<?php echo $subject_count; ?>" class="form-control">
									</select>
								</span>
								<span class="input-group-btn">
									<button onclick="dell_subject_line(<?php echo $case; ?>, <?php echo $place; ?>, <?php echo $subject_count; ?>)" class="btn btn-default" type="button">Удалить</button>
								</span>
							</div>
							<?php
						} else {
							$srv = $subjects[$j]['server'];
							$STH = $pdo->query("SELECT `id`, `number`, `type` FROM `sk_services` WHERE `server`='$srv' ORDER BY `type`");
							$STH->execute();
							$services = $STH->fetchAll();
							?>
							<div class="input-group w-100" id="subject_line_<?php echo $place; ?>_<?php echo $subject_count; ?>">
								<span class="input-group-btn w-50">
									<select name="server<?php echo $place; ?>_<?php echo $subject_count; ?>" id="server<?php echo $place; ?>_<?php echo $subject_count; ?>" class="form-control" onchange="get_services_subject2(<?php echo $case; ?>, <?php echo $place; ?>, <?php echo $subject_count; ?>, <?php echo $subjects[$j]['type']; ?>);">
										<?php for ($l = 0; $l < count($servers); $l++) { ?>
											<?php if($servers[$l]['id'] == $subjects[$j]['server']) { ?>
												<option value="<?php echo $servers[$l]['id']; ?>" selected><?php echo $servers[$l]['name']; ?></option>
											<?php } else { ?>
												<option value="<?php echo $servers[$l]['id']; ?>"><?php echo $servers[$l]['name']; ?></option>
											<?php } ?>
										<?php } ?>
									</select>
								</span>
								<span class="input-group-btn w-50">
									<select name="tarif<?php echo $place; ?>_<?php echo $subject_count; ?>" id="tarif<?php echo $place; ?>_<?php echo $subject_count; ?>" class="form-control">
										<?php for ($l = 0; $l < count($services); $l++) { ?>
											<?php if($services[$l]['id'] == $subjects[$j]['tarif']) { ?>
												<option value="<?php echo $services[$l]['id']; ?>" selected><?php echo $services[$l]['number']; ?> (<?php echo $services_data[$services[$l]['type']]['name']; ?>)</option>
											<?php } else { ?>
												<option value="<?php echo $services[$l]['id']; ?>"><?php echo $services[$l]['number']; ?> (<?php echo $services_data[$services[$l]['type']]['name']; ?>)</option>
											<?php } ?>
										<?php } ?>
									</select>
								</span>
								<span class="input-group-btn">
									<button onclick="dell_subject_line(<?php echo $case; ?>, <?php echo $place; ?>, <?php echo $subject_count; ?>)" class="btn btn-default" type="button">Удалить</button>
								</span>
							</div>
							<?php
						}
					}
					?>
					<script>$('#subjects_<?php echo $case; ?> #count_<?php echo $place; ?>').val('<?php echo $subject_count; ?>');</script>
					<?php
				}
			}
			?>
			</div>
			<div class="input-group">
				<span class="input-group-btn">
					<button class="btn btn-default" type="button" onclick="get_subject_line(<?php echo $case; ?>, <?php echo $place; ?>);">Добавить</button>
				</span>
				<select class="form-control" id="type_<?php echo $place; ?>">
				<?php if($subjects_types[1] == 1) { ?>
				<option value="1">Услугу</option>
				<?php } ?>
				<?php if($subjects_types[2] == 1) { ?>
				<option value="2">Денежный приз</option>
				<?php } ?>
				<?php if($subjects_types[3] == 1) { ?>
				<option value="3">Скидку</option>
				<?php } ?>
				<?php if($subjects_types[4] == 1) { ?>
				<option value="4">Приз из shop_key (Riko)</option>
				<?php } ?>
				<?php if($subjects_types[5] == 1) { ?>
				<option value="5">Приз из buy_key (Riko)</option>
				<?php } ?>
				<?php if($subjects_types[6] == 1) { ?>
				<option value="6">Приз из vip_key (WS)</option>
				<?php } ?>
				<?php if($subjects_types[7] == 1) { ?>
				<option value="7">Приз из vip_key (MyArena)</option>
				<?php } ?>
				</select>
			</div>
		</div>
		<?php
		}
	} else {
		/*
		?>
		<div id="subject_0" class="subject-block">
			<b>Пустой предмет</b>
			<input class="form-control" name="chance_0" id="chance_0" placeholder="Шанс выпадения (0 - 100%)" value="" type="number" onchange="calculate_chance_sum(<?php echo $case; ?>);">
		</div>
		<?php
		*/
	}
	?>
	<script>
		$('#subject_count_<?php echo $case; ?>').val(Number(<?php echo $place; ?>) + 1);
	</script>
	<?php
	exit();
}
if(isset($_POST['get_subject_line'])) {
	$case = check($_POST['case_id'], "int");
	$count = check($_POST['count'], "int");
	$type = check($_POST['type'], "int");
	$place = check($_POST['place'], "int");

	if (empty($count)) {
		$count = 0;
	}
	if (empty($type)) {
		$type = 1;
	}
	if (empty($place)) {
		exit();
	}

	$count++;
	?>
	<input type="hidden" name="subject_type_<?php echo $place; ?>_<?php echo $count; ?>" id="subject_type_<?php echo $place; ?>_<?php echo $count; ?>" value="<?php echo $type; ?>">
	<?php
	if($type == 1 || $type == 5 || $type == 6 || $type == 7) {
		$params = get_types_params($type);
		$STH = $pdo->query("SELECT `id`,`name` FROM `servers` WHERE `$params[1]`!='0'");
		$STH->execute();
		$servers = $STH->fetchAll();
		if(count($servers) == 0) {
			?>
			<div class="input-group w-100" id="subject_line_<?php echo $place; ?>_<?php echo $count; ?>">
				<span class="input-group-btn w-33">
					<select name="server<?php echo $place; ?>_<?php echo $count; ?>" id="server<?php echo $place; ?>_<?php echo $count; ?>" class="form-control">
					</select>
				</span>
				<span class="input-group-btn w-33">
					<select name="service<?php echo $place; ?>_<?php echo $count; ?>" id="service<?php echo $place; ?>_<?php echo $count; ?>" class="form-control">
					</select>
				</span>
				<span class="input-group-btn w-33">
					<select name="tarif<?php echo $place; ?>_<?php echo $count; ?>" id="tarif<?php echo $place; ?>_<?php echo $count; ?>" class="form-control">
					</select>
				</span>
				<span class="input-group-btn">
					<button onclick="dell_subject_line(<?php echo $case; ?>, <?php echo $place; ?>, <?php echo $count; ?>)" class="btn btn-default" type="button">Удалить</button>
				</span>
			</div>
			<?php
		} else {
			$j = $servers['0']['id'];
			$STH = $pdo->query("SELECT `id`, `name` FROM `$params[2]` WHERE `server`='$j'");
			$STH->execute();
			$services = $STH->fetchAll();
			?>
			<div class="input-group w-100" id="subject_line_<?php echo $place; ?>_<?php echo $count; ?>">
				<span class="input-group-btn w-33">
					<select name="server<?php echo $place; ?>_<?php echo $count; ?>" id="server<?php echo $place; ?>_<?php echo $count; ?>" class="form-control" onchange="get_services_subject(<?php echo $case; ?>, <?php echo $place; ?>, <?php echo $count; ?>, <?php echo $type; ?>);">
						<?php for ($i=0; $i < count($servers); $i++) { ?>
							<option value="<?php echo $servers[$i]['id']; ?>"><?php echo $servers[$i]['name']; ?></option>
						<?php } ?>
					</select>
				</span>
				<span class="input-group-btn w-33">
					<select name="service<?php echo $place; ?>_<?php echo $count; ?>" id="service<?php echo $place; ?>_<?php echo $count; ?>" class="form-control" onchange="get_tarifs_subject(<?php echo $case; ?>, <?php echo $place; ?>, <?php echo $count; ?>, <?php echo $type; ?>);">
						<?php for ($i=0; $i < count($services); $i++) { ?>
							<option value="<?php echo $services[$i]['id']; ?>"><?php echo $services[$i]['name']; ?></option>
						<?php } ?>
					</select>
				</span>
				<span class="input-group-btn w-33">
					<select name="tarif<?php echo $place; ?>_<?php echo $count; ?>" id="tarif<?php echo $place; ?>_<?php echo $count; ?>" class="form-control">
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
					<button onclick="dell_subject_line(<?php echo $case; ?>, <?php echo $place; ?>, <?php echo $count; ?>)" class="btn btn-default" type="button">Удалить</button>
				</span>
			</div>
			<?php
		}
	}
	if($type == 2) {
		?>
		<div class="input-group w-100" id="subject_line_<?php echo $place; ?>_<?php echo $count; ?>">
			<span class="input-group-btn w-100">
				<input class="form-control" name="money<?php echo $place; ?>_<?php echo $count; ?>" id="money<?php echo $place; ?>_<?php echo $count; ?>" placeholder="Сумма" value="" type="text">
			</span>
			<span class="input-group-btn">
				<button onclick="dell_subject_line(<?php echo $case; ?>, <?php echo $place; ?>, <?php echo $count; ?>)" class="btn btn-default" type="button">Удалить</button>
			</span>
		</div>
		<?php
	}
	if($type == 3) {
		?>
		<div class="input-group w-100" id="subject_line_<?php echo $place; ?>_<?php echo $count; ?>">
			<span class="input-group-btn w-100">
				<input class="form-control" name="percent<?php echo $place; ?>_<?php echo $count; ?>" id="percent<?php echo $place; ?>_<?php echo $count; ?>" placeholder="Значение в %" value="" type="number" maxlength="2">
			</span>
			<span class="input-group-btn">
				<button onclick="dell_subject_line(<?php echo $case; ?>, <?php echo $place; ?>, <?php echo $count; ?>)" class="btn btn-default" type="button">Удалить</button>
			</span>
		</div>
		<?php
	}
	if($type == 4) {
		$STH = $pdo->query("SELECT `id`,`name` FROM `servers` WHERE `sk_host`!='0'");
		$STH->execute();
		$servers = $STH->fetchAll();
		if(count($servers) == 0) {
			?>
			<div class="input-group w-100" id="subject_line_<?php echo $place; ?>_<?php echo $count; ?>">
				<span class="input-group-btn w-50">
					<select name="server<?php echo $place; ?>_<?php echo $count; ?>" id="server<?php echo $place; ?>_<?php echo $count; ?>" class="form-control">
					</select>
				</span>
				<span class="input-group-btn w-50">
					<select name="tarif<?php echo $place; ?>_<?php echo $count; ?>" id="tarif<?php echo $place; ?>_<?php echo $count; ?>" class="form-control">
					</select>
				</span>
				<span class="input-group-btn">
					<button onclick="dell_subject_line(<?php echo $case; ?>, <?php echo $place; ?>, <?php echo $count; ?>)" class="btn btn-default" type="button">Удалить</button>
				</span>
			</div>
			<?php
		} else {
			$j = $servers['0']['id'];
			$STH = $pdo->query("SELECT `id`, `number`, `type` FROM `sk_services` WHERE `server`='$j' ORDER BY `type`");
			$STH->execute();
			$services = $STH->fetchAll();
			?>
			<div class="input-group w-100" id="subject_line_<?php echo $place; ?>_<?php echo $count; ?>">
				<span class="input-group-btn w-50">
					<select name="server<?php echo $place; ?>_<?php echo $count; ?>" id="server<?php echo $place; ?>_<?php echo $count; ?>" class="form-control" onchange="get_services_subject2(<?php echo $case; ?>, <?php echo $place; ?>, <?php echo $count; ?>, <?php echo $type; ?>);">
						<?php for ($i=0; $i < count($servers); $i++) { ?>
							<option value="<?php echo $servers[$i]['id']; ?>"><?php echo $servers[$i]['name']; ?></option>
						<?php } ?>
					</select>
				</span>
				<span class="input-group-btn w-50">
					<select name="tarif<?php echo $place; ?>_<?php echo $count; ?>" id="tarif<?php echo $place; ?>_<?php echo $count; ?>" class="form-control">
						<?php for ($i=0; $i < count($services); $i++) { ?>
							<option value="<?php echo $services[$i]['id']; ?>"><?php echo $services[$i]['number']; ?> (<?php echo $services_data[$services[$i]['type']]['name']; ?>)</option>
						<?php } ?>
					</select>
				</span>
				<span class="input-group-btn">
					<button onclick="dell_subject_line(<?php echo $case; ?>, <?php echo $place; ?>, <?php echo $count; ?>)" class="btn btn-default" type="button">Удалить</button>
				</span>
			</div>
			<?php
		}
	}
	?>
	<script>$('#subjects_<?php echo $case; ?> #count_<?php echo $place; ?>').val('<?php echo $count; ?>');</script>
	<?php
	exit();
}
if(isset($_POST['get_services_subject'])) {
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
if(isset($_POST['get_services_subject2'])) {
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
if(isset($_POST['get_tarifs_subject'])) {
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

if(isset($_POST['get_open_cases'])) {
	$load_val = checkJs($_POST['load_val'], "int");
	if (empty($load_val)){
		$load_val = 1;
	}

	$limit = 30;
	$start = ($load_val-1)*$limit;
	$i = $start;
	$l = 0;

	$STH = $pdo->query("SELECT `cases__wins`.`item`, `cases__wins`.`case_id`, `cases__wins`.`user_id`, `cases__wins`.`time`, `cases`.`name`, `cases`.`price`, `cases__images`.`url`, `users`.`login`, `users`.`avatar` FROM `cases__wins`
		LEFT JOIN `cases` ON `cases__wins`.`case_id` = `cases`.`id`
		LEFT JOIN `users` ON `cases__wins`.`user_id` = `users`.`id`
		LEFT JOIN `cases__images` ON `cases`.`image` = `cases__images`.`id`
		ORDER BY `cases__wins`.`id` DESC LIMIT ".$start.", ".$limit); $STH->setFetchMode(PDO::FETCH_OBJ);
	while($prize = $STH->fetch()) { 
		$i++;
		$l++;
		?>
		<tr>
			<td><?php echo $i; ?></td>
			<td>
				<a target="_blank" href="../admin/case?id=<?php echo $prize->case_id ?>">
					<img src="../<?php echo $prize->url; ?>" alt="<?php echo $prize->name; ?>"> <?php echo $prize->name; ?>
				</a>
			</td>
			<td>
				<a target="_blank" href="../admin/edit_user?id=<?php echo $prize->user_id ?>">
					<img src="../<?php echo $prize->avatar ?>" alt="<?php echo $prize->login ?>"> <?php echo $prize->login ?>
				</a>
			</td>
			<td><?php echo $prize->price; ?><?php echo $messages['RUB']; ?></td>
			<td>
				<?php
					$subjects_types = get_subjects_types($pdo);
					$subjects = unserialize($prize->item);
					$count = count($subjects);

					for ($j=0; $j < $count; $j++) {
						?>
						<div class="subject">
						<?php
						if($subjects[$j]['type'] == 1 || $subjects[$j]['type'] == 5 || $subjects[$j]['type'] == 6 || $subjects[$j]['type'] == 7) {
							$params = get_types_params($subjects[$j]['type']);

							$STH2 = $pdo->prepare("SELECT `servers`.`name` AS `server_name`,`$params[2]`.`name`,`$params[2]`.`text`, `$params[3]`.`time` FROM `$params[2]` 
								LEFT JOIN `$params[3]` ON `$params[3]`.`service`=`$params[2]`.`id`
								LEFT JOIN `servers` ON `servers`.`id`=`$params[2]`.`server` WHERE `$params[2]`.`id`=:service AND `servers`.`id`=:server AND `$params[3]`.`id`=:tarif LIMIT 1"); $STH2->setFetchMode(PDO::FETCH_OBJ);
							$STH2->execute(array( ':service' => $subjects[$j]['service'], ':server' => $subjects[$j]['server'], ':tarif' => $subjects[$j]['tarif'] ));
							$row = $STH2->fetch();

							if($row->time == 0) {
								$row->time = 'Навсегда';
							} else {
								$row->time = $row->time.' дня(ей)';
							}
							?>
							<b><?php echo $row->name; ?></b> на <?php echo $row->server_name; ?> - <?php echo $row->time; ?>
							<?php
						}
						if($subjects[$j]['type'] == 2) {
							?>
							<b><?php echo $subjects[$j]['money']; ?> руб</b>
							<?php
						}
						if($subjects[$j]['type'] == 3) {
							?>
							<b><?php echo $subjects[$j]['percent']; ?>% скидка</b>
							<?php
						}
						if($subjects[$j]['type'] == 4) {
							$STH2 = $pdo->prepare("SELECT `servers`.`name` AS `server_name`, `sk_services`.`number`, `sk_services`.`type` FROM `sk_services` 
								LEFT JOIN `servers` ON `servers`.`id`=`sk_services`.`server` WHERE `sk_services`.`id`=:tarif AND `servers`.`id`=:server LIMIT 1"); $STH2->setFetchMode(PDO::FETCH_OBJ);
							$STH2->execute(array( ':server' => $subjects[$j]['server'], ':tarif' => $subjects[$j]['tarif'] ));
							$row = $STH2->fetch();

							?>
							<b><?php echo $row->number; ?> - <?php echo $services_data[$row->type]['name']; ?></b> на <?php echo $row->server_name; ?>
							<?php
						}
						?>
						</div>
						<?php
					}
				?>
			</td>
			<td>
				<span class="<?php echo get_item_class($subjects[0]['chance']); ?>">
					<?php echo $subjects[0]['chance']; ?>%
				</span>
			</td>
			<td><?php echo expand_date($prize->time, 7); ?></td>
		</tr>
		<?php
	}
	if (($load_val > 0) and ($l > $limit - 1)){
		$load_val++;
		exit ('<tr id="loader'.$load_val.'" class="c-p" onclick="get_open_cases(\''.$load_val.'\');"><td colspan="10">Подгрузить записи</td></tr>');
	}
	exit();
}
?>