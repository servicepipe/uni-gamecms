<?php

if(file_exists(__DIR__ . '/../../../modules_extra/shop_key/base/config.php')) {
	include_once __DIR__ . '/../../../modules_extra/shop_key/base/config.php';
}

$module = [
	'name'          => 'activity_rewards',
	'to_head'       => '',
	'to_head_admin' => "<script src='$site_host/modules_extra/activity_rewards/ajax/ajax.js?v={cache}'></script>",
	'tpl_dir'       => "../../../modules_extra/activity_rewards/templates/$conf->template/tpl/",
	'tpl_dir_admin' => "../../../modules_extra/activity_rewards/templates/admin/tpl/",
];

function getRewardsTypes($pdo)
{
	$prize_types[1] = 1;
	$prize_types[2] = 1;
	$prize_types[3] = 1;
	$prize_types[4] = 0;
	$prize_types[5] = 0;
	$prize_types[6] = 0;
	$prize_types[7] = 0;
	$prize_types[8] = 1;
	$prize_types[9] = 1;

	$STH = $pdo->query("SELECT name FROM modules");
	$STH->setFetchMode(PDO::FETCH_OBJ);
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
		if($row->name == 'vip_key') {
			$prize_types[7] = 1;
		}
	}
	return $prize_types;
}

function generateAdminName($pdo, $server)
{
	$i            = 0;
	$nick         = 'unknown';
	$originalNick = $nick;
	do {
		if($i != 0) {
			$nick = $originalNick . '(' . $i . ')';
		}
		$STH = $pdo->prepare("SELECT id FROM admins WHERE name=:name AND server=:server LIMIT 1");
		$STH->setFetchMode(PDO::FETCH_OBJ);
		$STH->execute([':name' => $nick, ':server' => $server]);
		$row = $STH->fetch();
		if(isset($row->id)) {
			$temp = null;
		} else {
			$temp = 1;
		}
		$i++;
	} while(empty($temp));

	return $nick;
}

function generateAdminSteam($pdo, $server)
{
	$i     = 0;
	$steam = 'STEAM_0:1:';
	$id    = 111111111;
	do {
		if($i != 0) {
			$id = $id + $i;
		}
		$STH = $pdo->prepare("SELECT id FROM admins WHERE name=:name AND server=:server LIMIT 1");
		$STH->setFetchMode(PDO::FETCH_OBJ);
		$STH->execute([':name' => $steam . $id, ':server' => $server]);
		$row = $STH->fetch();
		if(isset($row->id)) {
			$temp = null;
		} else {
			$temp = 1;
		}
		$i++;
	} while(empty($temp));

	return $steam . $id;
}

function getTypesParams($type)
{
	$params = [];
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

function getConfig($pdo)
{
	$config = [];

	$STH = $pdo->query("SELECT * FROM activity_rewards__config");
	$STH->setFetchMode(PDO::FETCH_OBJ);
	while($row = $STH->fetch()) {
		$config[$row->slug] = $row->value;
	}

	return json_decode(json_encode($config));
}

function updateConfigValue($pdo, $slug, $value)
{
	$STH = $pdo->prepare("UPDATE activity_rewards__config SET value=:value WHERE slug=:slug LIMIT 1");
	return $STH->execute([':value' => $value, ':slug' => $slug]);
}

function getUserDonateAmount($pdo, $userId) {
	$amountOfMoney = 0;

	$STH = $pdo->prepare("SELECT shilings FROM money__actions WHERE author=:author AND type='1'");
	$STH->setFetchMode(PDO::FETCH_OBJ);
	$STH->execute([':author' => $userId]);
	while($row = $STH->fetch()) {
		$amountOfMoney += $row->shilings;
	}

	return $amountOfMoney;
}

$activityRewardsConfig = getConfig($pdo);