<?php
if($page->privacy == 1 && !is_auth()) {
	show_error_page('not_auth');
}

include_once "modules_extra/shop_key/base/config.php";

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
	$PI->to_nav('shop_key', 1, 0)
);
$nav = $tpl->get_nav($nav, 'elements/nav_li.tpl');

if(isset($_SESSION['id'])) {
	include_once "inc/authorized.php";
} else {
	include_once "inc/not_authorized.php";
}

$servers = '';
$j = 0;
$STH = $pdo->query("SELECT `id`, `name` FROM `servers` WHERE `type` = '4' AND (`sk_host`!='' AND `sk_host`!='0') ORDER BY `trim`"); $STH->setFetchMode(PDO::FETCH_OBJ);  
while($row = $STH->fetch()) { 
	$j++;
	$servers .= '<option value="'.$row->id.'">'.$row->name.'</option>';
}
if($j == 0){
	$servers = 0;
}

$STH = $pdo->query("SELECT `discount` FROM `config__prices` LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);  
$disc = $STH->fetch();

$tpl->load_template($module['tpl_dir']."shop_key.tpl");
$tpl->set("{site_host}", $site_host);
$tpl->set("{template}", $conf->template);
$tpl->set("{discount}", $disc->discount);
$tpl->set("{servers}", $servers);
$tpl->compile( "content" );
$tpl->clear();
?>