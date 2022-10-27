<?php
include_once "../../../inc/start.php";
include_once "../../../inc/protect.php";

if(empty($_POST['phpaction'])) {
	log_error("Прямой вызов (donation_widget/actions_a.php)");
	exit(json_encode(['status' => '2']));
}

if(!is_admin()) {
	exit(json_encode(['status' => '2', 'data' => 'Доступно только администратору']));
}

if($conf->token == 1 && ($_SESSION['token'] != clean($_POST['token'], null))) {
	log_error("Неверный токен (donation_widget/actions_a.php)");
	exit(json_encode(['status' => '2', 'info' => 'Неверный токен']));
}

if(isset($_POST['edit_value'])) {
	$field = check($_POST['field'], null);
	$value = check($_POST['value'], null);
	$id    = checkJs($_POST['id'], "int");

	if($field == "raising") {
		$table = "dw__config";
		$useid = false;
	} else {
		$table = "dw__raisings";
		$useid = true;
	}

	if($field == "target" && $value < 0) {
		exit('<p class="text-danger">Число не может быть отрицательным!</p>');
	}
	if($field == "target" && $value > 999999) {
		exit('<p class="text-danger">Укажите число не более 999999!</p>');
	}

	$field = preg_replace('/[^A-Za-z_]/', '', $field);

	$STH = $pdo->prepare("UPDATE $table SET $field = '$value' " . ($useid ? "WHERE id = $id " : "") . " LIMIT 1");
	$STH->execute();
	exit('<p class="text-success">Изменено!</p>');
}

if(isset($_POST['load_raisings'])) {
	$STH = $pdo->query("SELECT raising FROM dw__config LIMIT 1");
	$row     = $STH->fetch(PDO::FETCH_COLUMN);
	$raising = (!empty($row)) ? $row : 0;

	$data = "<option value='0'>Выключено</option>";
	$STH  = $pdo->query("SELECT * FROM dw__raisings ORDER BY id DESC");
	$STH->setFetchMode(PDO::FETCH_OBJ);
	while($row = $STH->fetch()) {
		$data .= '<option value="' . $row->id . '"' . ($row->id == $raising ? " selected" : "") . '>#'
			. $row->id . ($row->id == $raising ? " (текущий)" : "") . ': "' . $row->message
			. '" - ' . $row->target . $messages['RUB']
			. '</option>';
	}
	exit($data);
}

if(isset($_POST['load_raising_info'])) {
	$id = checkJs($_POST['id'], "int");

	if($id == "0") {
		exit(json_encode(['status' => '1', 'message' => '', 'target' => '', 'stopdate' => '']));
	}

	$STH = $pdo->query("SELECT message, target, stopdate FROM dw__raisings WHERE id = $id LIMIT 1");
	$row = $STH->fetch(PDO::FETCH_OBJ);

	$message = (isset($row->message) ? $row->message : "");

	exit(json_encode(['status' => '1', 'message' => $message, 'target' => $row->target, 'stopdate' => $row->stopdate]));
}

if(isset($_POST['raising_act'])) {
	$action = checkJs($_POST['action'], "int");
	$id     = checkJs($_POST['id'], "int");

	if($action == 1) {
		$STH = $pdo->exec(
			"INSERT INTO dw__raisings (id) VALUES (NULL); " .
			"SET @lastID := LAST_INSERT_ID(); " .
			"UPDATE dw__config SET raising = @lastID;"
		);
	} elseif($action == 2) {
		$STH = $pdo->exec(
			"DELETE FROM dw__raisings WHERE id = $id; " .
			"DELETE FROM dw__donations WHERE fid = $id;"
		);
	} else {
		exit('<p class="text-error">Неверное действие!</p>');
	}
}

if(isset($_POST['change_config_value'])) {
	$attr = check($_POST['attr'], null);
	$value = check($_POST['value'], null);

	$attr = preg_replace('/[^A-Za-z_]/', '', $attr);

	$STH = $pdo->prepare("UPDATE dw__config SET `$attr`=:value WHERE id='1' LIMIT 1");
	$STH->execute([':value' => $value]);

	exit();
}