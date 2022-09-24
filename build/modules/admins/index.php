<?php
if($page->privacy == 1 && !is_auth()) {
	show_error_page('not_auth');
}

if(isset($_GET['server'])){
	$server = clean($_GET['server'], "int");
} else {
	$server = 0;
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
$tpl->set("{other}", '');
$tpl->set("{token}", $token);
$tpl->set("{cache}", $conf->cache);
$tpl->set("{template}", $conf->template);
$tpl->set("{site_host}", $site_host);
$tpl->compile( 'content' );
$tpl->clear();

$menu = $tpl->get_menu($pdo);

$nav = array(
	$PI->to_nav('main', 0, 0),
	$PI->to_nav('admins', 1, 0)
);
$nav = $tpl->get_nav($nav, 'elements/nav_li.tpl');

if(isset($_SESSION['id'])) {
	include_once "inc/authorized.php";
} else {
	include_once "inc/not_authorized.php";
}

if($server == 0) {
	$servers = '<li class="active"><a href="../admins">' . $messages['All'] . '</a></li>';
} else {
	$servers = '<li><a href="../admins">' . $messages['All'] . '</a></li>';
}
$STH = $pdo->query("SELECT `id`, `name` FROM `servers` WHERE `type`!=0 AND `united` = '0' ORDER BY `trim`"); $STH->setFetchMode(PDO::FETCH_OBJ);
while($row = $STH->fetch()) {
	if($row->id == $server){
		$servers .= '<li class="active"><a href="../admins?server='.$row->id.'">'.$row->name.'</a></li>';
	} else {
		$servers .= '<li><a href="../admins?server='.$row->id.'">'.$row->name.'</a></li>';
	}
}

$tpl->load_template('/home/admins.tpl');
$tpl->set("{site_host}", $site_host);
$tpl->set("{server}", $server);
$tpl->set("{servers}", $servers);
$tpl->set("{template}", $conf->template);
$tpl->compile( 'content' );
$tpl->clear();
?>