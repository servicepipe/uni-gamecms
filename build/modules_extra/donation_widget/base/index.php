<?php
include_once "modules_extra/donation_widget/base/config.php";

$tpl->load_template('elements/title.tpl');
$tpl->set("{title}", $page->title);
$tpl->set("{name}", $conf->name);
$tpl->compile('title');
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
$tpl->compile('content');
$tpl->clear();

$menu = $tpl->get_menu($pdo);

$nav = [
	$PI->to_nav('main', 0, 0),
	$PI->to_nav('donation', 1, 0)
];
$nav = $tpl->get_nav($nav, 'elements/nav_li.tpl');

if(isset($_SESSION['id'])) {
	include_once "inc/authorized.php";
} else {
	include_once "inc/not_authorized.php";
}

// load widget configuration
$STH = $pdo->query("SELECT enabled, raising FROM dw__config LIMIT 1");
$STH->setFetchMode(PDO::FETCH_OBJ);
$STH->execute();
$dwconf = $STH->fetch();

$started = 1;

if($dwconf->enabled == 2 || empty($dwconf->raising)) {
	$started = 2;
}

$tpl->load_template($module['tpl_dir'] . "index.tpl");
$tpl->set("{site_host}", $site_host);
$tpl->set("{template}", $conf->template);
$tpl->set("{started}", $started);
$tpl->compile("content");
$tpl->clear();
?>