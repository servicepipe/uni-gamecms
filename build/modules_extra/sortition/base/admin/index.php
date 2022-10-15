<?php
if(!is_admin()){
	show_error_page('not_adm');
}

include_once "modules_extra/sortition/base/config.php";
include_once "modules_extra/sortition/base/start.php";

$tpl->load_template('elements/title.tpl');
$tpl->set("{title}", $page->title);
$tpl->set("{name}", $conf->name);
$tpl->compile( 'title' );
$tpl->clear();

$tpl->load_template('head.tpl');
$tpl->set("{title}", $tpl->result['title']);
$tpl->set("{image}", $page->image);
$tpl->set("{other}", $module['to_head_admin'].'<script src="{site_host}modules/editors/tinymce/tinymce.min.js"></script>
<link rel="stylesheet" href="{site_host}templates/admin/css/tabs.css">
<link rel="stylesheet" href="{site_host}templates/admin/css/timepicker.css">
<link rel="stylesheet" href="{site_host}modules_extra/sortition/templates/admin/css/style.css">');
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
	$PI->to_nav('admin_sortition', 1, 0)
);
$nav = $tpl->get_nav($nav, 'elements/nav_li.tpl', 1);

$tpl->load_template('page_top.tpl');
$tpl->set("{nav}", $nav);
$tpl->compile( 'content' );
$tpl->clear();

$bank = 0;
if($exists == 1) {
	if($sortition->own_prize == 1) {
		$own_prize_data = unserialize($sortition->prize);
		$sortition->text = $own_prize_data['description'];
		$sortition->count_of_winners = $own_prize_data['count_of_winners'];
	} else {
		$sortition->text = '';
		$sortition->count_of_winners = '';
	}

	if($sortition->price != 0) {
		$STH = $pdo->query("SELECT `contribution` FROM `sortition__participants`"); $STH->setFetchMode(PDO::FETCH_OBJ);
		while($row = $STH->fetch()) { 
			$bank += $row->contribution;
		}
	}
}

$prize_types = get_prizes_types($pdo);

$tpl->load_template($module['tpl_dir_admin'].'index.tpl');
$tpl->set("{site_host}", $site_host);
$tpl->set("{exists}", $exists);
$tpl->set("{name}", $sortition->name);
$tpl->set("{ending}", $sortition->ending);
$tpl->set("{price}", $sortition->price);
$tpl->set("{text}", $sortition->text);
$tpl->set("{count_of_winners}", $sortition->count_of_winners);
$tpl->set("{participants}", $sortition->participants);
$tpl->set("{prize}", $sortition->prize);
$tpl->set("{own_prize}", $sortition->own_prize);
$tpl->set("{bank}", $bank.$messages['RUB']);
$tpl->set("{how_old}", $sortition->how_old);
$tpl->set("{show_participants}", $sortition->show_participants);
$tpl->set("{end_type}", $sortition->end_type);
$tpl->set("{finished}", $sortition->finished);
$tpl->compile( 'content' );
$tpl->clear();

$tpl->load_template('bottom.tpl');
$tpl->set("{site_host}", $site_host);
$tpl->compile( 'content' );
$tpl->clear();
?>