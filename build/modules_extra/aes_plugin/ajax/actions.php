<?php
include_once "../../../inc/start.php";
include_once "../../../inc/protect.php";
if (empty($_POST['phpaction'])) {
	log_error("Прямой вызов actions.php");
	exit(json_encode(array('status' => '2')));
}

if(isset($_POST['load_aes_list'])) {
	$start = checkStart($_POST['start']);
	$server = checkJs($_POST['server'], "int");
	$name = $_POST['name'];

	if(empty($server)){
		exit('<tr><td colspan="10">Ошибка: [Неизвестные переменные]</td></tr>');
	}
	if((empty($start) and $start!="0")) {
		exit('<tr><td colspan="10">Ошибка: [Неизвестные переменные]</td></tr>');
	}

	$STH = $pdo->query("SELECT bans_lim FROM config__secondary LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
	$row = $STH->fetch();
	$limit = $row->bans_lim;

	$STH = $pdo->query("SELECT id,ip,port,name,aes_host,aes_user,aes_pass,aes_db,aes_table,aes_code FROM servers WHERE aes_host!='' and id='$server' LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
	$row = $STH->fetch();

	$aes_host = $row->aes_host;
	$aes_user = $row->aes_user;
	$aes_pass = $row->aes_pass;
	$aes_db = $row->aes_db;
	$aes_table = $row->aes_table;
	$address = $row->ip.':'.$row->port;
	$server_name = $row->name;
	if(!$pdo2 = db_connect($aes_host, $aes_db, $aes_user, $aes_pass)) {
		exit('<tr><td colspan="10">Не удалось подключиться к базе данных</td></tr>');
	}
	set_names($pdo2, $row->aes_code);

	$STH = $pdo2->query("SHOW COLUMNS FROM $aes_table");
	$STH->execute();
	$row = $STH->fetchAll();
	$if['lastJoin'] = 0;
	for ($i=0; $i < count($row); $i++) {
		if ($row[$i]['Field'] == 'lastJoin') {
			$if['lastJoin']++;
		}
	}

	include_once "../../../modules_extra/aes_plugin/base/config.php";

	if ($if['lastJoin'] == 0) {
		if(empty($name)){
			$STH = $pdo2->query("SELECT `id`, `name`, `exp` AS 'experience', `bonus_count` AS 'bonus', `last_update` AS 'lastJoin' FROM $aes_table WHERE exp > '1' ORDER BY exp DESC LIMIT $start, $limit"); $STH->setFetchMode(PDO::FETCH_OBJ);
		} else {
			$STH = $pdo2->prepare("SELECT `id`, `name`, `exp` AS 'experience', `bonus_count` AS 'bonus', `last_update` AS 'lastJoin' FROM $aes_table WHERE ($aes_table.steamid LIKE :name or $aes_table.ip LIKE :name  or $aes_table.name LIKE :name ) ORDER BY exp DESC"); $STH->setFetchMode(PDO::FETCH_OBJ);
			$STH->execute(array(":name" => "%".$name."%"));
		}
	} else {
		if(empty($name)){
			$STH = $pdo2->query("SELECT * FROM $aes_table WHERE experience > '1' ORDER BY experience DESC LIMIT $start, $limit"); $STH->setFetchMode(PDO::FETCH_OBJ);
		} else {
			$STH = $pdo2->prepare("SELECT * FROM $aes_table WHERE ($aes_table.trackId LIKE :name or $aes_table.name LIKE :name ) ORDER BY experience DESC"); $STH->setFetchMode(PDO::FETCH_OBJ);
			$STH->execute(array(":name" => "%".$name."%"));
		}
	}

	$i = 0;
	$tpl = new Template;
	$tpl->dir = '../../../modules_extra/aes_plugin/templates/'.$conf->template.'/tpl/';
	while($row = $STH->fetch()) {
		$i++;

		if ($if['lastJoin'] == 0) {
			$j = 0;
			while (isset($levels[$j]) && $levels[$j] <= $row->experience) {
				$j++;
			}
			$rank = $ranks[$j-1];
		} else {
			$rank = $ranks[$row->level];
		}

		$tpl->load_template('line.tpl');
		$tpl->set("{i}", $i);
		$tpl->set("{name}", $row->name);
		$tpl->set("{rank}", $rank);
		$tpl->set("{bonus}", $row->bonus);
		$tpl->set("{exp}", $row->experience);
		$tpl->set("{lastjoin}", expand_date($row->lastJoin, 7));
		$tpl->compile( 'content' );
		$tpl->clear();
	}

	if($i == 0){
		exit("<tr><td colspan='10'>Статистики нет</td></tr>");
	} else {
		$tpl->show($tpl->result['content']);
		$tpl->global_clear();
	}
	exit();
}

if (isset($_POST['load_servers']) && is_admin()){
	$i=0;
	$STH = $pdo->query("SELECT `name`,`ip`,`port`,`id`,`aes_host`,`aes_code`,`aes_table`,`aes_user`,`aes_pass`,`aes_db` FROM servers WHERE type = 0 OR type = 1 OR type = 2 OR type = 3 OR type = 5 ORDER BY trim"); $STH->setFetchMode(PDO::FETCH_OBJ);
	while($row = $STH->fetch()) {
		?>
		<div class="col-md-6">
			<div id="serv_<?php echo $row->id ?>" class="block">
				<div class="block_head">
					<?php echo $row->name ?> (<?php echo $row->ip ?>:<?php echo $row->port ?>)
				</div>

				<div class="form-group">
					<label for="aes_host<?php echo $row->id ?>">
						<h4>
							db хост
						</h4>
					</label>
					<input value="<?php echo $row->aes_host ?>" type="text" class="form-control" id="aes_host<?php echo $row->id ?>" maxlength="64" autocomplete="off">
				</div>
				<div class="form-group">
					<label for="aes_user<?php echo $row->id ?>">
						<h4>
							db логин
						</h4>
					</label>
					<input value="<?php echo $row->aes_user ?>" type="text" class="form-control" id="aes_user<?php echo $row->id ?>" maxlength="32" autocomplete="off">
				</div>
				<div class="form-group">
					<label for="aes_pass<?php echo $row->id ?>">
						<h4>
							db пароль
						</h4>
					</label>
					<input value="<?php echo $row->aes_pass ?>" type="password" class="form-control" id="aes_pass<?php echo $row->id ?>" maxlength="32" autocomplete="off">
				</div>
				<div class="form-group">
					<label for="aes_db<?php echo $row->id ?>">
						<h4>
							db база
						</h4>
					</label>
					<input value="<?php echo $row->aes_db ?>" type="text" class="form-control" id="aes_db<?php echo $row->id ?>" maxlength="32" autocomplete="off">
				</div>
				<div class="form-group">
					<label for="aes_table<?php echo $row->id ?>">
						<h4>
							Название таблицы
						</h4>
					</label>
					<input value="<?php echo $row->aes_table ?>" type="text" class="form-control" id="aes_table<?php echo $row->id ?>" maxlength="32" autocomplete="off" placeholder="aes_stats">
				</div>
				<div class="form-group">
					<label for="aes_code<?php echo $row->id ?>">
						<h4>
							Кодировка
						</h4>
					</label><br>
					<select class="form-control" id="aes_code<?php echo $row->id ?>">
						<option value="0" <?php if ($row->aes_code == '0'){ ?> selected <?php } ?>>Своя</option>
						<option value="1" <?php if ($row->aes_code == '1'){ ?> selected <?php } ?>>utf-8</option>
						<option value="2" <?php if ($row->aes_code == '2'){ ?> selected <?php } ?>>latin1</option>
					</select>
				</div>

				<div class="mt-10">
					<div id="edit_serv_result<?php echo $row->id ?>" class="mt-10"></div>
					<button onclick="aes_edit_server('<?php echo $row->id ?>', 0);" type="button" class="btn2">Сохранить</button>
					<button type="button" class="btn2 btn-cancel" onclick="aes_edit_server('<?php echo $row->id ?>', 1);">Очистить</button>
				</div>
			</div>
		</div>
		<?php
		if($i % 2 == 1) {
			echo "<div class='clearfix'></div>";
		}
		$i++;
	}

	if ($i == 0){
		exit ('Серверов нет');
	}
}
if (isset($_POST['edit_server']) && is_admin()){
	$id = checkJs($_POST['id'],"int");
	$aes_host = check($_POST['aes_host'],null);
	$aes_user = check($_POST['aes_user'],null);
	$aes_pass = check($_POST['aes_pass'],null);
	$aes_db = check($_POST['aes_db'],null);
	$aes_code = checkJs($_POST['aes_code'],"int");
	$aes_table = check($_POST['aes_table'],null);

	if(empty($aes_code)) {
		$aes_code = 0;
	}
	if (empty($id)) {
		exit (json_encode(array('status' => '2')));
	}

	if ($_POST['clean'] == '1'){
		$aes_host = '';
		$aes_user = '';
		$aes_pass = '';
		$aes_db = '';
		$aes_code = '0';
		$aes_table = '';
	} else {
		if (empty($aes_host) or empty($aes_user) or empty($aes_pass) or empty($aes_db) or empty($aes_table)) {
			exit('<p class="text-danger">Заполните поля: db хост, db логин, db пароль, db таблица</p><script>setTimeout(show_error, 500);</script>');
		} else {
			if(!$pdo2 = db_connect($aes_host, $aes_db, $aes_user, $aes_pass)) {
				exit('<p class="text-danger">Ошибка подключения к базе данных!</p><script>setTimeout(show_error, 500);</script>');
			}
			if(!check_table($aes_table, $pdo2)) {
				exit('<p class="text-danger">Не найдена таблица с данным названием в базе данных.</p><script>setTimeout(show_error, 500);</script>');
			}
		}
	}

	$STH = $pdo->prepare("UPDATE servers SET aes_host=:aes_host,aes_user=:aes_user,aes_pass=:aes_pass,aes_db=:aes_db,aes_code=:aes_code,aes_table=:aes_table WHERE id='$id' LIMIT 1");
	if ($STH->execute(array( 'aes_host' => $aes_host, 'aes_user' => $aes_user, 'aes_pass' => $aes_pass, 'aes_db' => $aes_db, 'aes_code' => $aes_code, 'aes_table' => $aes_table )) == '1') {
		exit('<p class="text-success">Сервер успешно изменен</p><script>setTimeout(show_ok, 500);</script>');
	}
}
?>