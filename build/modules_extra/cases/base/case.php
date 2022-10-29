<?php
include_once "modules_extra/cases/base/config.php";
include_once "modules_extra/cases/base/start.php";

if($page->privacy == 1 && !is_auth()) {
	show_error_page('not_auth');
}

if (isset($_GET['id'])) {
	$id = clean($_GET['id'],"int");
} else {
	$id = 0;
}

if(empty($id)) {
	show_error_page('not_settings');
}

$STH = $pdo->prepare("SELECT `cases`.`id`, `cases`.`name`, `cases__images`.`url` AS 'image', `cases`.`price` FROM `cases` 
		LEFT JOIN `cases__images` ON `cases`.`image`=`cases__images`.`id` WHERE `cases`.`id`=:id"); $STH->setFetchMode(PDO::FETCH_OBJ);
$STH->execute(array( ':id' => $id ));
$case = $STH->fetch();

if(empty($case->id)){
	show_error_page();
}

$tpl->load_template('elements/title.tpl');
$tpl->set("{title}", $PI->compile_str($page->title, $case->name));
$tpl->set("{name}", $conf->name);
$tpl->compile( 'title' );
$tpl->clear();

$tpl->load_template('head.tpl');
$tpl->set("{title}", $tpl->result['title']);
$tpl->set("{site_name}", $conf->name);
$tpl->set("{image}", $page->image);
$tpl->set("{robots}", $page->robots);
$tpl->set("{type}", $page->kind);
$tpl->set("{description}", $PI->compile_str($page->description, $case->name));
$tpl->set("{keywords}", $PI->compile_str($page->keywords, $case->name));
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
	$PI->to_nav('cases', 0, 0),
	$PI->to_nav('case', 1, 0, $case->name)
);
$nav = $tpl->get_nav($nav, 'elements/nav_li.tpl');

if(isset($_SESSION['id'])) {
	include_once "inc/authorized.php";
} else {
	include_once "inc/not_authorized.php";
}

$tpl->load_template($module['tpl_dir']."case.tpl");
$tpl->set("{site_host}", $site_host);
$tpl->set("{template}", $conf->template);
$tpl->set("{id}", $case->id);
$tpl->set("{name}", $case->name);
$tpl->set("{price}", $case->price);
$tpl->set("{image}", $case->image);
$tpl->set("{cache}", $conf->cache);
$tpl->compile( 'content' );
$tpl->clear();
?>