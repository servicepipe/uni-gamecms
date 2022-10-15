<?php
//Нельзя трогать
if(file_exists($_SERVER["DOCUMENT_ROOT"]."/modules_extra/shop_key/base/config.php")) {
	include_once $_SERVER["DOCUMENT_ROOT"]."/modules_extra/shop_key/base/config.php";
}

$module = array(
	'name' => 'sortition', 
	'to_head' => "", 
	'to_head_admin' => "<script src=\"$site_host/modules_extra/sortition/ajax/ajax.js?v={cache}\"></script><link rel=\"stylesheet\" href=\"$site_host/modules_extra/sortition/templates/$conf->template/css/style.css?v={cache}\">", 
	'tpl_dir' => "../../../modules_extra/sortition/templates/$conf->template/tpl/", 
	'tpl_dir_admin' => "../../../modules_extra/sortition/templates/admin/tpl/", 
);

$winners_count = 1;

function get_prizes_types($pdo) {
	$prize_types[1] = 1;
	$prize_types[2] = 1;
	$prize_types[3] = 1;
	$prize_types[4] = 0;
	$prize_types[5] = 0;
	$prize_types[6] = 0;
	$STH = $pdo->query("SELECT `name` FROM `modules`"); $STH->setFetchMode(PDO::FETCH_OBJ);
	while($row = $STH->fetch()) {
		if($row->name == 'shop_key') {
			$prize_types[4] = 1;
		}
		if($row->name == 'buy_key') {
			$prize_types[5] = 1;
		}
		if($row->name == 'vip_key_ws') {
			$prize_types[6] = 1;
		}
	}
	return $prize_types;
}
function get_winners($pdo, &$users_groups, $winners_count) {
	$STH = $pdo->query("SELECT COUNT(*) FROM `sortition__participants`");
	$count = $STH->fetchColumn();

	if($count == 0) {
		$STH = $pdo->prepare("UPDATE `sortition` SET `ending`=:ending LIMIT 1");
		$STH->execute(array( ':ending' => $sortition->ending+24*60*60 ));

		write_log('Не удалось определить победителя для розыгрыша "'.$sortition->name.'". Розыгрыш продлен на сутки.');
		return array('error' => 'yes');
	}

	if($count < $winners_count) {
		$winners_count = $count;
	}

	$winners = array();

	$now = 1;
	$attempt = 0;
	while ($now <= $winners_count) {
		$STH = $pdo->query("SELECT `users`.`dell`, `users`.`rights`, `users`.`login`, `sortition__participants`.`id`, `sortition__participants`.`contribution`, `sortition__participants`.`winner`, `sortition__participants`.`user_id` FROM `sortition__participants` 
		LEFT JOIN `users` ON `sortition__participants`.`user_id` = `users`.`id` WHERE `sortition__participants`.`winner` is NULL or `sortition__participants`.`winner`='0' ORDER BY rand() LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
		$row = $STH->fetch();

		if(($attempt < 5) && (empty($row->rights) || ($row->dell == 1) || is_worthy("z", $row->rights) || is_worthy("x", $row->rights))){
			$attempt++;
		} else {
			$STH = $pdo->prepare("SELECT `user_id` FROM `sortition__participants` WHERE `winner`=:winner LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
			$STH->execute(array( ':winner' => $now ));
			$check = $STH->fetch();
			if(empty($check->user_id)) {
				$STH = $pdo->prepare("UPDATE `sortition__participants` SET `winner`=:winner WHERE `user_id`=:user_id LIMIT 1");
				$STH->execute(array(':winner' => $now, ':user_id' => $row->user_id ));

				$winners[$now]['id'] = $row->user_id;
				$winners[$now]['login'] = $row->login;
				$now++;
			}
		}
	}

	return $winners;
}
function gernerate_admin_name($pdo, $server) {
	$i = 0;
	$nick = 'unknown';
	do {
		if($i != 0) {
			$nick = $nick.'('.$i.')';
		}
		$STH = $pdo->prepare("SELECT `id` FROM `admins` WHERE `name`=:name AND `server`=:server LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
		$STH->execute(array( ':name' => $nick, ':server' => $server ));
		$row = $STH->fetch();
		if(isset($row->id)) {
			$temp = null;
		} else {
			$temp = 1;
		}
		$i++;
	} while (empty($temp));

	return $nick;
}
function gernerate_admin_steam($pdo, $server) {
	$i = 0;
	$steam = 'STEAM_0:1:';
	$id = 111111111;
	do {
		if($i != 0) {
			$id = $id+$i;
		}
		$STH = $pdo->prepare("SELECT `id` FROM `admins` WHERE `name`=:name AND `server`=:server LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
		$STH->execute(array( ':name' => $steam.$id, ':server' => $server ));
		$row = $STH->fetch();
		if(isset($row->id)) {
			$temp = null;
		} else {
			$temp = 1;
		}
		$i++;
	} while (empty($temp));

	return $steam.$id;
}
function get_types_params($type) {
	$params = array();
	if($type == 1) {
		$params[1] = 'type';
		$params[2] = 'services';
		$params[3] = 'services__tarifs';
		$params[4] = '1';
	}
	if($type == 5) {
		$params[1] = 'bk_host';
		$params[2] = 'bk_services';
		$params[3] = 'bk_services_times';
		$params[4] = '5';
	}
	if($type == 6) {
		$params[1] = 'vk_host';
		$params[2] = 'vk_services';
		$params[3] = 'vk_services_times';
		$params[4] = '6';
	}
	return $params;
}
?>