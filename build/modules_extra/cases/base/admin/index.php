<?php
if(!is_admin()){
	show_error_page('not_adm');
}

$STH = $pdo->query("SELECT `name` FROM `money__actions_types` WHERE `id`='17' LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
$row = $STH->fetch();
if(empty($row->name)) {
	$STH = $pdo->prepare("INSERT INTO `money__actions_types` (`id`,`name`,`class`) values (:id, :name, :class)");  
	$STH->execute(array( ':id' => '17', ':name' => 'Приз из кейса', ':class' => 'success' ));
}
$STH = $pdo->query("SELECT `name` FROM `money__actions_types` WHERE `id`='18' LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
$row = $STH->fetch();
if(empty($row->name)) {
	$STH = $pdo->prepare("INSERT INTO `money__actions_types` (`id`,`name`,`class`) values (:id, :name, :class)");  
	$STH->execute(array( ':id' => '18', ':name' => 'Открытие кейса', ':class' => 'danger' ));
}

include_once "modules_extra/cases/base/config.php";

$tpl->load_template('elements/title.tpl');
$tpl->set("{title}", $page->title);
$tpl->set("{name}", $conf->name);
$tpl->compile( 'title' );
$tpl->clear();

$tpl->load_template('head.tpl');
$tpl->set("{title}", $tpl->result['title']);
$tpl->set("{image}", $page->image);
$tpl->set("{other}", $module['to_head_admin']);
$tpl->set("{token}", $token);
$tpl->set("{cache}", $conf->cache);
$tpl->set("{template}", $conf->template);
$tpl->set("{site_host}", $site_host);
$tpl->compile( 'content' );
$tpl->clear();

$tpl->load_template('top.tpl');
$tpl->set("{site_host}", $site_host);
$tpl->set("{site_name}", $conf->name);
$tpl->compile( 'content' );
$tpl->clear();

$tpl->load_template('menu.tpl');
$tpl->set("{site_host}", $site_host);
$tpl->compile( 'content' );
$tpl->clear();

$nav = array(
	$PI->to_nav('admin', 0, 0),
	$PI->to_nav('admin_modules', 0, 0),
	$PI->to_nav('admin_cases', 1, 0)
);
$nav = $tpl->get_nav($nav, 'elements/nav_li.tpl', 1);

$tpl->load_template('page_top.tpl');
$tpl->set("{nav}", $nav);
$tpl->compile( 'content' );
$tpl->clear();

$subjects_types = get_subjects_types($pdo);

$STH = $pdo->query("SELECT `id`, `url` FROM `cases__images` ORDER BY `id` LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
$row = $STH->fetch();

$tpl->load_template($module['tpl_dir_admin'].'index.tpl');
$tpl->set("{token}", $token);
$tpl->set("{site_host}", $site_host);
$tpl->set("{image}", $row->url);
$tpl->set("{image_id}", $row->id);
$tpl->compile( 'content' );
$tpl->clear();

$tpl->load_template('bottom.tpl');
$tpl->set("{site_host}", $site_host);
$tpl->compile( 'content' );
$tpl->clear();
?>