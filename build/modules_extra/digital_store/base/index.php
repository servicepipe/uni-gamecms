<?php
include_once "modules_extra/digital_store/base/config.php";

if(isset($_GET['category'])) {
	$category = clean($_GET['category'], "int");

	$STH = $pdo->prepare("SELECT * FROM `digital_store__categories` WHERE `id`=:id LIMIT 1");
	$STH->setFetchMode(PDO::FETCH_OBJ);
	$STH->execute(array(':id' => $category));
	$row = $STH->fetch();
	if(empty($row->id)) {
		show_error_page();
	}
} else {
	$category = 0;
}

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

$nav = array($PI->to_nav('main', 0, 0),
             $PI->to_nav('digital_store', 1, 0));
$nav = $tpl->get_nav($nav, 'elements/nav_li.tpl');

if(isset($_SESSION['id'])) {
	include_once "inc/authorized.php";
} else {
	include_once "inc/not_authorized.php";
}

$DS = new DigitalStore($module, $pdo, $tpl);
$categories = $DS->get_categories($category);
$products = $DS->get_products($category);

$tpl->load_template($module['tpl_dir'].'index.tpl');
$tpl->set("{site_host}", $site_host);
$tpl->set("{template}", $conf->template);
$tpl->set("{categories}", $categories);
$tpl->set("{products}", $products);
$tpl->compile('content');
$tpl->clear();