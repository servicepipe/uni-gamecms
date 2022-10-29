<?php
if(!is_admin()){
	show_error_page('not_adm');
}

if (isset($_GET['id'])) {
	$id = clean($_GET['id'],"int");
} else {
	$id = 0;
}

if(empty($id)) {
	show_error_page('not_settings');
}

$STH = $pdo->prepare("SELECT `cases`.`id`, `cases`.`name`, `cases__images`.`url` AS 'image', `cases`.`image` AS 'image_id', `cases`.`price` FROM `cases` 
		LEFT JOIN `cases__images` ON `cases`.`image`=`cases__images`.`id` WHERE `cases`.`id`=:id"); $STH->setFetchMode(PDO::FETCH_OBJ);
$STH->execute(array( ':id' => $id ));
$case = $STH->fetch();

if(empty($case->id)){
	show_error_page();
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
	$PI->to_nav('admin_cases', 0, 0),
	$PI->to_nav('admin_case', 1, 0)
);
$nav = $tpl->get_nav($nav, 'elements/nav_li.tpl', 1);

$tpl->load_template('page_top.tpl');
$tpl->set("{nav}", $nav);
$tpl->compile( 'content' );
$tpl->clear();

$subjects_types = get_subjects_types($pdo);

$tpl->load_template($module['tpl_dir_admin'].'case.tpl');
$tpl->set("{site_host}", $site_host);
$tpl->set("{token}", $token);
$tpl->set("{id}", $case->id);
$tpl->set("{name}", $case->name);
$tpl->set("{price}", $case->price);
$tpl->set("{image}", $case->image);
$tpl->set("{image_id}", $case->image_id);
$tpl->compile( 'content' );
$tpl->clear();

$tpl->load_template('bottom.tpl');
$tpl->set("{site_host}", $site_host);
$tpl->compile( 'content' );
$tpl->clear();
?>