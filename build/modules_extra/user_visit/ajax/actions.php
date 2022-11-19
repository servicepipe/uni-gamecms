<?php
include_once "../../../inc/start.php";
include_once "../../../inc/protect.php";

if (empty($_POST['phpaction'])) {
	log_error("Прямой вызов actions.php");
	exit(json_encode(array('status' => '2')));
}
if($conf->token == 1 && ($_SESSION['token'] != clean($_POST['token'],null))) {
	log_error("Неверный токен");
	exit(json_encode(array('status' => '2')));
}

if (isset($_POST['user_visit'])) {
	$id = check($_POST['id'],"int");
	$date = date("Y-m-d");

	if($id == $_SESSION['id']){
		exit();
	}

	$STH = $pdo->query("SELECT * FROM users__visit WHERE user_id='$id' and user_visit='$_SESSION[id]' ORDER BY id DESC LIMIT 1"); 
	$STH->setFetchMode(PDO::FETCH_OBJ);
	$row = $STH->fetch();

	if(empty($row->date)){
		$STH2 = $pdo->prepare("INSERT INTO `users__visit` (`user_id`,`user_visit`,`date`) values (:id, :visit, :date)");  
		$STH2->execute(array( ':id' => $id, ':visit' => $_SESSION['id'], ':date' => $date ));
	}

	$STH3 = $pdo->query("DELETE FROM users__visit WHERE date < '$date'");
	exit();
}

if (isset($_POST['get_user_visit'])) {
	$id = check($_POST['id'],"int");

	$date = date("Y-m-d");
	$i = 0;
	$tpl = new Template;
	$tpl->dir = '../../../modules_extra/user_visit/templates/';
	$tpl->result['content'] = '';

	$STH = $pdo->query("SELECT users__visit.*, users.login, users.avatar, users.rights FROM users__visit LEFT JOIN users ON users__visit.user_visit = users.id WHERE user_id='$id' and date='$date'"); 
	$STH->setFetchMode(PDO::FETCH_OBJ); 
	
	while($row = $STH->fetch()) {
		$gp = $users_groups[$row->rights];
		$tpl->load_template('us_visit.tpl');
		$tpl->set("{login}", $row->login);
		$tpl->set("{id}", $row->user_visit);
		$tpl->set("{avatar}", $row->avatar);
		$tpl->set("{gp_color}", $gp['color']);
		$tpl->set("{gp_name}", $gp['name']);
		$tpl->compile( 'content' );
		$tpl->clear();
		$i++;
	}

	if ($i == 0){
		echo '<span>Сегодня никто не посещал</span>';
	} else {
		$tpl->show($tpl->result['content']);
		$tpl->global_clear();
	}
	
	exit();
}
?>