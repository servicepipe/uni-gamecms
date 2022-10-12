<?php
include_once "modules_extra/aes_plugin/base/config.php";

if(isset($_GET['page'])){
	$number = $_GET['page'];
	$number = clean($number,"int");
} else {
	$number = 0;
}
$STH = $pdo->query("SELECT bans_lim FROM config__secondary LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
$row = $STH->fetch();
$limit = $row->bans_lim;

if($number){
	$start = ($number - 1) * $limit;
}else{
	$number = 0;
	$start = 0;
}

if(isset($_GET['server'])){
	$server = clean($_GET['server'], "int");
	$STH = $pdo->query("SELECT id,ip,port,aes_host,aes_user,aes_pass,aes_db,aes_table FROM servers WHERE aes_host!='' and id='$server' LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);  
} else {
	$STH = $pdo->query("SELECT id,ip,port,aes_host,aes_user,aes_pass,aes_db,aes_table FROM servers WHERE aes_host!='' ORDER BY trim LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);  
}
$row = $STH->fetch();
if(empty($row->id)){
	$empty = 1;
	$server = '';
	$error = '';
	$count = 0;
	$stages = '';
	$page_name = '';
} else {
	$empty = 0;
	$server = $row->id;
	$page_name = "../aes_list?server=".$row->id."&";
	$aes_host = $row->aes_host;
	$aes_user = $row->aes_user;
	$aes_pass = $row->aes_pass;
	$aes_db = $row->aes_db;
	$aes_table= $row->aes_table;

	$error = "";
	if(!$pdo2 = db_connect($aes_host, $aes_db, $aes_user, $aes_pass)) {
		$error = $messages['Unable_connect_to_db'];
	}
	if($error == ""){
		$STH = $pdo2->query("SHOW COLUMNS FROM $aes_table");
		$STH->execute();
		$row = $STH->fetchAll();
		$if['lastJoin'] = 0;
		for ($i=0; $i < count($row); $i++) {
			if ($row[$i]['Field'] == 'lastJoin') {
				$if['lastJoin']++;
			}
		}

		if ($if['lastJoin'] == 0) {
			$STH = $pdo2->query("SELECT COUNT(*) as count FROM $aes_table WHERE exp > '1'");
		} else {
			$STH = $pdo2->query("SELECT COUNT(*) as count FROM $aes_table WHERE experience > '1'");
		}
		$STH->setFetchMode(PDO::FETCH_ASSOC);
		$row = $STH->fetch();
		$count = $row['count'];
	} else {
		$count = 0;
	}
	$stages = 3;
	if(($number*$limit - $count) > $limit){
		header('Location: ../aes_list');
		exit();
	}
}

$tpl->load_template('elements/title.tpl');
$tpl->set("{title}", $page->title);
$tpl->set("{name}", $conf->name);
$tpl->compile( 'title' );
$tpl->clear();

$tpl->load_template('head.tpl');
$tpl->set("{title}", $tpl->result['title']);
$tpl->set("{site_name}", $conf->name);
$tpl->set("{image}", $page->image);
$tpl->set("{robots}", $page->robots);
$tpl->set("{type}", $page->kind);
$tpl->set("{description}", $page->description);
$tpl->set("{keywords}", $page->keywords);
$tpl->set("{url}", $page->full_url);
$tpl->set("{other}", $module['to_head']);
$tpl->set("{token}", $token);
$tpl->set("{cache}", $conf->cache);
$tpl->set("{template}", $conf->template);
$tpl->set("{site_host}", $site_host);
$tpl->compile( 'content' );
$tpl->clear();

$menu = $tpl->get_menu($pdo);

$nav = array(
	$PI->to_nav('main', 0, 0),
	$PI->to_nav('aes_list', 1, 0)
);
$nav = $tpl->get_nav($nav, 'elements/nav_li.tpl');

if(isset($_SESSION['id'])) {
	include_once "inc/authorized.php";
} else {
	include_once "inc/not_authorized.php";
}

$i = 0;
$data = '';
$STH = $pdo->query("SELECT id,name FROM servers WHERE aes_host!='' ORDER BY trim"); $STH->setFetchMode(PDO::FETCH_OBJ);
while($row = $STH->fetch()) {
	if($row->id == $server){
		$data .= '<li class="active"><a href="../aes_list?server='.$row->id.'">'.$row->name.'</a></li>';
	} else {
		if($i == 0 and empty($server)){
			$data .= '<li class="active"><a href="../aes_list?server='.$row->id.'">'.$row->name.'</a></li>';
		} else {
			$data .= '<li><a href="../aes_list?server='.$row->id.'">'.$row->name.'</a></li>';
		}
	}
	$i++;
}

$tpl->load_template($module['tpl_dir'].'index.tpl');
$tpl->set("{site_host}", $site_host);
$tpl->set("{template}", $conf->template); 
$tpl->set("{page}", $number);
$tpl->set("{start}", $start);
$tpl->set("{server}", $server);
$tpl->set("{error}", $error);
$tpl->set("{empty}", $empty);
$tpl->set("{servers}", $data);
$tpl->set("{pagination}", $tpl->get_paginator($number,$count,$limit,$stages,$page_name)); 
$tpl->compile( 'content' );
$tpl->clear();
?>