<?php
include_once "../../../inc/start.php";
include_once "../../../inc/protect.php";
include_once "../base/config.php";
if(empty($_POST['phpaction'])) {
	log_error("Прямой вызов actions.php");
	exit(json_encode(array('status' => '2')));
}
if($conf->token == 1 && ($_SESSION['token'] != clean($_POST['token'], null))) {
	log_error("Неверный токен");
	exit(json_encode(array('status' => '2')));
}

if(isset($_POST['get_services'])) {
	$id = checkJs($_POST['id'], "int");
	if(empty($id)) {
		exit ();
	}

	$data     = '';
	$i        = 0;
	$services = array();
	$STH      = $pdo->query("SELECT `id`, `type` FROM `sk_services` WHERE `server` = '$id' ORDER BY `id`");
	$STH->setFetchMode(PDO::FETCH_OBJ);
	while($row = $STH->fetch()) {
		if(!in_array($row->type, $services)) {
			if($i == 0) {
				$data .= '<script>sk_get_tarifs('.$row->type.');</script>';
			}
			$services[$i] = $row->type;
			$i++;
		}
	}

	for($i = 0; $i < count($services); $i++) {
		if($services[$i] == '1') {
			$data .= '<option value="1">'.$services_data['1']['name'].'</option>';
		}
		if($services[$i] == '2') {
			$data .= '<option value="2">'.$services_data['2']['name'].'</option>';
		}
		if($services[$i] == '3') {
			$data .= '<option value="3">'.$services_data['3']['name'].'</option>';
		}
		if($services[$i] == '4') {
			$data .= '<option value="4">'.$services_data['4']['name'].'</option>';
		}
	}

	exit(json_encode(array('status' => '1', 'data' => $data)));
}
if(isset($_POST['get_tarifs'])) {
	$id   = checkJs($_POST['id'], "int");
	$type = checkJs($_POST['type'], "int");
	if(empty($id) || empty($type)) {
		exit ();
	}

	$STH = $pdo->query("SELECT `discount` FROM `servers` WHERE `id` = '$id' LIMIT 1");
	$STH->setFetchMode(PDO::FETCH_OBJ);
	$row             = $STH->fetch();
	$server_discount = $row->discount;

	$STH = $pdo->query("SELECT discount FROM config__prices LIMIT 1");
	$STH->setFetchMode(PDO::FETCH_OBJ);
	$disc     = $STH->fetch();
	$discount = $disc->discount;

	$data = '';
	$STH  = $pdo->query("SELECT `id`, `price`, `number` FROM `sk_services` WHERE `server` = '$id' AND `type` = '$type' ORDER BY `price`");
	$STH->setFetchMode(PDO::FETCH_OBJ);
	while($row = $STH->fetch()) {

		if(isset($user->proc)) {
			$user_proc = $user->proc;
		} else {
			$user_proc = 0;
		}

		$proc  = calculate_discount($server_discount, $discount, $user_proc);
		$price = calculate_price($row->price, $proc);

		if($price != $row->price) {
			$data .= '<option value="'.$row->id.'">'.$row->number.' - '.$price.' '.$messages['RUB'].' (с учетом скидки)</option>';
		} else {
			$data .= '<option value="'.$row->id.'">'.$row->number.' - '.$price.' '.$messages['RUB'].'</option>';
		}
	}

	exit(json_encode(array('status' => '1', 'data' => $data)));
}

if(isset($_POST['shop_key'])) {
	if(!is_auth()) {
		exit(json_encode(array('status' => '4')));
	}

	$server = checkJs($_POST['server'], "int");
	$tarif  = checkJs($_POST['tarif'], "int");

	if(empty($server) || empty($tarif)) {
		exit(json_encode(array('status' => '2', 'info' => '')));
	}

	$STH = $pdo->prepare("SELECT id, ip, port, name, sk_host, sk_user, sk_pass, sk_db, sk_code, discount FROM servers WHERE id=:id LIMIT 1");
	$STH->setFetchMode(PDO::FETCH_OBJ);
	$STH->execute(array(':id' => $server));
	$server = $STH->fetch();
	if(empty($server->id) || empty($server->sk_host)) {
		exit(json_encode(array('status' => '2', 'info' => '')));
	}
	if(!$pdo2 = db_connect($server->sk_host, $server->sk_db, $server->sk_user, $server->sk_pass)) {
		exit(json_encode(array('status' => '2', 'info' => 'Ошибка подключения к базе данных!')));
	}
	set_names($pdo2, $server->sk_code);

	$STH = $pdo->prepare("SELECT id, shilings, proc FROM users WHERE id=:id LIMIT 1");
	$STH->setFetchMode(PDO::FETCH_OBJ);
	$STH->execute(array(':id' => $_SESSION['id']));
	$row = $STH->fetch();
	if(empty($row->id)) {
		exit(json_encode(array('status' => '2', 'info' => '')));
	}
	$proc     = $row->proc;
	$shilings = $row->shilings;

	$STH = $pdo->prepare("SELECT sk_services.price, sk_services.number, sk_services.type FROM sk_services WHERE sk_services.server=:server AND sk_services.id=:tarif LIMIT 1");
	$STH->setFetchMode(PDO::FETCH_OBJ);
	$STH->execute(array(':server' => $server->id, ':tarif' => $tarif));
	$row = $STH->fetch();
	if(empty($row->price)) {
		exit(json_encode(array('status' => '2', 'info' => '')));
	}
	$price  = $row->price;
	$number = $row->number;
	$type   = $row->type;

	$STH = $pdo->query("SELECT discount FROM config__prices LIMIT 1");
	$STH->setFetchMode(PDO::FETCH_OBJ);
	$disc     = $STH->fetch();
	$discount = $disc->discount;

	$proc  = calculate_discount($server->discount, $discount, $user->proc);
	$price = calculate_price($price, $proc);

	if($shilings < $price) {
		$price_delta = round_shilings($price - $shilings);
		exit (json_encode(array('status' => '2',
								'info'   => 'У Вас недостаточно средств.<br><a href="../purse?price='.$price_delta.'">Пополните баланс на '.$price_delta.$messages['RUB'].'.</a>')));
	}
	$shilings = round_shilings($shilings - $price);

	$key = crate_pass(20, 2);
	$STH = $pdo2->prepare("SELECT key_name FROM table_keys WHERE key_name=:key LIMIT 1");
	$STH->setFetchMode(PDO::FETCH_OBJ);
	$STH->execute(array(':key' => $key));
	$row = $STH->fetch();
	if(isset($row->key_name)) {
		$key = crate_pass(21, 2);
	}

	$STH = $pdo2->prepare("SELECT sid FROM keys_servers WHERE address=:address LIMIT 1");
	$STH->setFetchMode(PDO::FETCH_OBJ);
	$STH->execute(array(':address' => $server->ip.":".$server->port));
	$row = $STH->fetch();
	if(empty($row->sid)) {
		exit (json_encode(array('status' => '2', 'info' => '')));
	} else {
		$sid = $row->sid;
	}

	$name = $services_data[$type]['name_2'];
	$type = $services_data[$type]['type'];

	$STH = $pdo2->prepare("INSERT INTO table_keys (key_name,type,expires,uses,sid,param1,active) VALUES (:key_name, :type, :expires, :uses, :sid, :param1, :active)");
	$STH->execute(array(':key_name' => $key, ':type' => $type, ':expires' => '0', ':uses' => '1', ':sid' => $sid, ':param1' => $number, ':active' => '1'));

	$date = date("Y-m-d H:i:s");
	$STH  = $pdo->prepare("INSERT INTO money__actions (date,shilings,author,type) VALUES (:date, :shilings, :author, :type)");
	$STH->execute(array('date' => $date, 'shilings' => -$price, 'author' => $_SESSION['id'], 'type' => '16'));

	$STH = $pdo->prepare("UPDATE `users` SET `shilings`=:shilings WHERE `id`=:id LIMIT 1");
	$STH->execute(array(':shilings' => $shilings, ':id' => $_SESSION['id']));

	$mess = "Поздравляем Вас с успешной покупкой <b>".$number."</b> ".$name." на сервере <b>".$server->name."</b><br>";
	$mess .= "Использование ключа: введите в консоль <b>key ".$key."</b>";
	$STH  = $pdo->prepare("INSERT INTO notifications (message,date,user_id,type) VALUES (:message, :date, :user_id, :type)");
	$STH->execute(array('message' => $mess, 'date' => $date, 'user_id' => $_SESSION['id'], 'type' => '2'));

	$mess2 = "Куплено ".$number." ".$name." на сервере ".$server->name." пользователем: <a href='../profile?id=".$_SESSION['id']."'>".$_SESSION['login']."</a>\r\n";
	$mess2 .= "Его ключ: <b>".$key."</b> \r\n";

	$STH = $pdo->prepare("INSERT INTO notifications (message,date,user_id,type) VALUES (:message, :date, :user_id, :type)");
	$STH->execute(array('message' => $mess2, 'date' => $date, 'user_id' => '1', 'type' => '2'));

	$file_name = get_log_file_name("shop_key");
	if(file_exists($_SERVER['DOCUMENT_ROOT']."/logs/".$file_name)) {
		$i = "a";
	} else {
		$i = "w";
	}
	$file = fopen($_SERVER['DOCUMENT_ROOT']."/logs/".$file_name, $i);
	fwrite($file, "[".$date." | Пользователь: ".$_SESSION['login']." - ".$_SESSION['id']."] : [Куплено ".$number." ".$name." на сервере ".$server->name." за ".$price."р, его ключ: ".$key."] \r\n");
	fclose($file);

	exit(json_encode(array('status' => '3', 'info' => $mess, 'shilings' => $shilings)));
}

if(!is_admin()) {
	exit(json_encode(array('status' => '2', 'data' => 'Досутпно только администратору')));
}

if(isset($_POST['load_servers'])) {
	$i   = 0;
	$STH = $pdo->query("SELECT name,ip,port,id,sk_host,sk_code,sk_user,sk_pass,sk_db FROM servers WHERE type = '4' ORDER BY trim");
	$STH->setFetchMode(PDO::FETCH_OBJ);
	while($row = $STH->fetch()) {
		?>
		<div class="col-md-6">
			<form id="serv_<?php echo $row->id ?>" class="block">
				<div class="block_head">
					<?php echo $row->name ?> (<?php echo $row->ip ?>:<?php echo $row->port ?>)
				</div>

				<div class="form-group">
					<label>
						<h4>
							db хост
						</h4>
					</label>
					<input value="<?php echo $row->sk_host ?>" type="text" class="form-control" name="sk_host" maxlength="64" autocomplete="off">
				</div>
				<div class="form-group">
					<label>
						<h4>
							db логин
						</h4>
					</label>
					<input value="<?php echo $row->sk_user ?>" type="text" class="form-control" name="sk_user" maxlength="32" autocomplete="off">
				</div>
				<div class="form-group">
					<label>
						<h4>
							db пароль
						</h4>
					</label>
					<input value="<?php echo $row->sk_pass ?>" type="password" class="form-control" name="sk_pass" maxlength="32" autocomplete="off">
				</div>
				<div class="form-group">
					<label>
						<h4>
							db база
						</h4>
					</label>
					<input value="<?php echo $row->sk_db ?>" type="text" class="form-control" name="sk_db" maxlength="32" autocomplete="off">
				</div>
				<div class="form-group">
					<label>
						<h4>
							Кодировка
						</h4>
					</label><br>
					<select class="form-control" name="sk_code">
						<option value="0" <?php if($row->sk_code == '0') { ?> selected <?php } ?>>Своя</option>
						<option value="1" <?php if($row->sk_code == '1') { ?> selected <?php } ?>>utf-8</option>
						<option value="2" <?php if($row->sk_code == '2') { ?> selected <?php } ?>>latin1</option>
					</select>
				</div>

				<div class="mt-10">
					<div id="edit_serv_result<?php echo $row->id ?>" class="mt-10"></div>
					<button onclick="sk_edit_server('<?php echo $row->id ?>', 0);" type="button" class="btn2">Сохранить</button>
					<button type="button" class="btn2 btn-cancel" onclick="sk_edit_server('<?php echo $row->id ?>', 1);">Очистить</button>
				</div>
			</form>
		</div>
		<?php
		if($i % 2 == 1) {
			echo "<div class='clearfix'></div>";
		}
		$i++;
	}

	if($i == 0) {
		exit ('Серверов нет');
	}
}
if(isset($_POST['edit_server'])) {
	foreach($_POST as $key => $value) {
		switch($key) {
			case 'id':
				$$key = check($value, "int");
				break;
			case 'sk_code':
				$$key = check($value, "int");
				break;
			default:
				$$key = check($value, null);
				break;
		}
	}

	if(empty($sk_code)) {
		$sk_code = 0;
	}
	if(empty($id)) {
		exit (json_encode(array('status' => '2')));
	}

	if($_POST['clean'] == '1') {
		$sk_host = '0';
		$sk_user = '0';
		$sk_pass = '0';
		$sk_db   = '0';
		$sk_code = '0';
	} else {
		if(empty($sk_host) or empty($sk_user) or empty($sk_pass) or empty($sk_db)) {
			exit('<p class="text-danger">Заполните поля: db хост, db логин, db пароль</p><script>setTimeout(show_error, 500);</script>');
		} else {
			if(!$pdo2 = db_connect($sk_host, $sk_db, $sk_user, $sk_pass)) {
				exit('<p class="text-danger">Ошибка подключения к базе данных!</p><script>setTimeout(show_error, 500);</script>');
			}
			if(!check_table('table_keys', $pdo2)) {
				exit('<p class="text-danger">Не найдена таблица table_keys в базе данных.</p><script>setTimeout(show_error, 500);</script>');
			}
			if(!check_table('keys_servers', $pdo2)) {
				exit('<p class="text-danger">Не найдена таблица keys_servers в базе данных.</p><script>setTimeout(show_error, 500);</script>');
			}
		}

		$STH = $pdo2->query("SHOW COLUMNS FROM table_keys");
		$STH->execute();
		$row          = $STH->fetchAll();
		$if['active'] = 0;
		for($i = 0; $i < count($row); $i++) {
			if($row[$i]['Field'] == 'active') {
				$if['active']++;
			}
		}
		if($if['active'] == 0) {
			$pdo2->exec("ALTER TABLE table_keys ADD active INT(1) NOT NULL DEFAULT '0' AFTER sid;");
		}

		$STH = $pdo->prepare("SELECT ip, port FROM servers WHERE id=:id LIMIT 1");
		$STH->setFetchMode(PDO::FETCH_OBJ);
		$STH->execute(array(':id' => $id));
		$row = $STH->fetch();
		if(empty($row->ip)) {
			exit (json_encode(array('status' => '2')));
		} else {
			$address = $row->ip.":".$row->port;
		}

		$STH = $pdo2->prepare("SELECT `sid` FROM `keys_servers` WHERE `address`=:address LIMIT 1");
		$STH->setFetchMode(PDO::FETCH_OBJ);
		$STH->execute(array(':address' => $address));
		$row = $STH->fetch();
		if(empty($row->sid)) {
			$STH = $pdo2->prepare("INSERT INTO `keys_servers` (`address`) values (:address)");
			$STH->execute(array(':address' => $address));
		}
	}

	$STH = $pdo->prepare("UPDATE servers SET sk_host=:sk_host,sk_user=:sk_user,sk_pass=:sk_pass,sk_db=:sk_db,sk_code=:sk_code WHERE id='$id' LIMIT 1");
	if($STH->execute(array('sk_host' => $sk_host, 'sk_user' => $sk_user, 'sk_pass' => $sk_pass, 'sk_db' => $sk_db, 'sk_code' => $sk_code)) == '1') {
		exit('<p class="text-success">Сервер успешно изменен</p><script>setTimeout(show_ok, 500);</script>');
	}
}

if(isset($_POST['load_tarifs'])) {
	$id   = checkJs($_POST['id'], "int");
	$type = checkJs($_POST['type'], "int");
	if(empty($id) || empty($type)) {
		exit ();
	}
	$STH = $pdo->prepare("SELECT id, number, price FROM sk_services WHERE server = :id AND type = :type");
	$STH->execute(array(':id' => $id, ':type' => $type));
	$tarifs = $STH->fetchAll();
	$count  = count($tarifs);
	if($count != 0) {
		for($i = 0; $i < $count; $i++) {
			?>
			<tr id="tarif<?php echo $tarifs[$i]['id'] ?>">
				<td width="1%"><?php echo $i + 1; ?></td>
				<td>
					<input value="<?php echo $tarifs[$i]['number'] ?>" class="form-control" type="text" maxlength="50" id="number<?php echo $tarifs[$i]['id'] ?>" placeholder="Значение" autocomplete="off">
				</td>
				<td>
					<input value="<?php echo $tarifs[$i]['price'] ?>" class="form-control" type="number" maxlength="6" id="price<?php echo $tarifs[$i]['id'] ?>" placeholder="Цена" autocomplete="off">
				</td>
				<td width="30%">
					<div class="btn-group" role="group">
						<button onclick="sk_edit_tarif (<?php echo $tarifs[$i]['id'] ?>);" class="btn btn-default" style="padding: 0px 10px;" type="button">
							<span class="glyphicon glyphicon-pencil"></span></button>
						<button onclick="sk_dell_tarif (<?php echo $tarifs[$i]['id'] ?>);" class="btn btn-default" style="padding: 0px 10px;" type="button">
							<span class="glyphicon glyphicon-trash"></span></button>
					</div>
				</td>
			</tr>
			<?php
		}
	} else {
		?>
		<tr>
			<td colspan="10">
				Тарифов нет
			</td>
		</tr>
		<?php
	}
	exit();
}
if(isset($_POST['add_tarif'])) {
	$server = check($_POST['server'], "int");
	$price  = check($_POST['price'], "int");
	$type   = check($_POST['type'], "int");
	$number = check($_POST['number'], null);

	if(empty($server)) {
		exit (json_encode(array('status' => '2', 'input' => 'server', 'reply' => 'Заполните!')));
	}

	if(empty($server)) {
		exit (json_encode(array('status' => '2', 'input' => 'server', 'reply' => 'Заполните!')));
	}

	if(empty($type) and $type != 0) {
		exit (json_encode(array('status' => '2', 'input' => 'type', 'reply' => 'Заполните!')));
	}
	if($type != 1 && $type != 2 && $type != 3 && $type != 4) {
		exit (json_encode(array('status' => '2', 'input' => 'type', 'reply' => 'Неверное значение')));
	}

	if(empty($price)) {
		exit (json_encode(array('status' => '2', 'input' => 'price', 'reply' => 'Заполните!')));
	}
	if(mb_strlen($price, 'UTF-8') > 6) {
		exit (json_encode(array('status' => '2', 'input' => 'price', 'reply' => 'Не более 6 символов!')));
	}

	if(empty($number) and $number != 0) {
		exit (json_encode(array('status' => '2', 'input' => 'number', 'reply' => 'Заполните!')));
	}

	$data = array('server' => $server, 'price' => $price, 'number' => $number, 'type' => $type);
	$STH  = $pdo->prepare("INSERT INTO sk_services (server,price,number,type) VALUES (:server, :price, :number, :type)");
	if($STH->execute($data) == '1') {
		exit(json_encode(array('status' => '1')));
	}
}
if(isset($_POST['edit_tarif'])) {
	$id     = check($_POST['id'], "int");
	$number = check($_POST['number'], null);
	$price  = check($_POST['price'], "int");

	if(empty($id)) {
		exit(json_encode(array('status' => '2')));
	}

	if(empty($number)) {
		exit (json_encode(array('status' => '2', 'input' => 'number', 'reply' => 'Заполните!')));
	}

	if(empty($price)) {
		exit (json_encode(array('status' => '2', 'input' => 'price', 'reply' => 'Заполните!')));
	}

	if(mb_strlen($price, 'UTF-8') > 6) {
		exit (json_encode(array('status' => '2', 'input' => 'price', 'reply' => 'Не более 6 символов!')));
	}

	$STH = $pdo->prepare("UPDATE sk_services SET number=:number,price=:price WHERE id='$id' LIMIT 1");
	if($STH->execute(array('number' => $number, 'price' => $price)) == '1') {
		exit(json_encode(array('status' => '1')));
	}
}
if(isset($_POST['dell_tarif'])) {
	$id = checkJs($_POST['id'], "int");
	if(empty($id)) {
		exit (json_encode(array('status' => '2')));
	}
	$pdo->exec("DELETE FROM sk_services WHERE id='$id'");
	exit(json_encode(array('status' => '1')));
}
?>