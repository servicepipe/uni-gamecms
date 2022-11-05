<?php
include_once "modules_extra/digital_store/base/config.php";

if(isset($_GET['id'])) {
	$product = clean($_GET['id'], "int");

	$STH = $pdo->prepare("SELECT * FROM `digital_store__products` WHERE `id`=:id LIMIT 1");
	$STH->setFetchMode(PDO::FETCH_OBJ);
	$STH->execute(array(':id' => $product));
	$product = $STH->fetch();
	if(empty($product->id)) {
		show_error_page();
	}
} else {
	show_error_page();
}

$tpl->load_template('elements/title.tpl');
$tpl->set("{title}", $PI->compile_str($page->title, $product->name));
$tpl->set("{name}", $conf->name);
$tpl->compile('title');
$tpl->clear();

$tpl->load_template('head.tpl');
$tpl->set("{title}", $tpl->result['title']);
$tpl->set("{site_name}", $conf->name);
$tpl->set("{image}", $page->image);
$tpl->set("{robots}", $page->robots);
$tpl->set("{type}", $page->kind);
$tpl->set("{description}", $PI->compile_str($page->description, $product->name));
$tpl->set("{keywords}", $PI->compile_str($page->keywords, $product->name));
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
$categories = $DS->get_categories($product->category);

$tpl->load_template($module['tpl_dir'].'product.tpl');
$tpl->set("{site_host}", $site_host);
$tpl->set("{template}", $conf->template);
$tpl->set("{categories}", $categories);
$tpl->set("{id}", $product->id);
$tpl->set("{name}", $product->name);
$tpl->set("{image}", $product->image);
$tpl->set("{price}", $product->price);
$tpl->set("{description}", $product->description);
$tpl->set("{count}", $DS->get_count_of_product_keys($product->id));
$tpl->compile('content');
$tpl->clear();