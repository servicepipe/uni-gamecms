<?php
//Нельзя трогать
if(file_exists($_SERVER["DOCUMENT_ROOT"]."/modules_extra/shop_key/base/config.php")) {
	include_once $_SERVER["DOCUMENT_ROOT"]."/modules_extra/shop_key/base/config.php";
}

$module = array(
	'name' => 'cases', 
	'to_head' => "", 
	'to_head_admin' => "<script src=\"$site_host/modules_extra/cases/ajax/ajax.js?v={cache}\"></script><link rel=\"stylesheet\" href=\"$site_host/modules_extra/cases/templates/admin/css/style.css?v={cache}\">", 
	'tpl_dir' => "../../../modules_extra/cases/templates/$conf->template/tpl/", 
	'tpl_dir_admin' => "../../../modules_extra/cases/templates/admin/tpl/", 
);

function get_subjects_types($pdo) {
	$subjects_types[1] = 1;
	$subjects_types[2] = 1;
	$subjects_types[3] = 1;
	$subjects_types[4] = 0;
	$subjects_types[5] = 0;
	$subjects_types[6] = 0;
	$subjects_types[7] = 0;
	$STH = $pdo->query("SELECT `name` FROM `modules`"); $STH->setFetchMode(PDO::FETCH_OBJ);
	while($row = $STH->fetch()) {
		if($row->name == 'shop_key') {
			$subjects_types[4] = 1;
		}
		if($row->name == 'buy_key') {
			$subjects_types[5] = 1;
		}
		if($row->name == 'vip_key_ws') {
			$subjects_types[6] = 1;
		}
		if($row->name == 'vip_key') {
			$subjects_types[7] = 1;
		}
	}
	return $subjects_types;
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
	if($type == 7) {
		$params[1] = 'vkb_host';
		$params[2] = 'vkb_services';
		$params[3] = 'vkb_services_times';
		$params[4] = '7';
	}
	return $params;
}
function get_item_class($chance) {
	if($chance <= 10) {
		$class = 'orange';
	} elseif($chance <= 20) {
		$class = 'red';
	} elseif($chance <= 40) {
		$class = 'purple';
	} elseif($chance <= 50) {
		$class = 'blue';
	} else {
		$class = 'military';
	}
	return $class;
}
?>