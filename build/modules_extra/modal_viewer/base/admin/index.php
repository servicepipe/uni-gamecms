<?php
if(!is_admin()) {
	show_error_page('not_adm');
}

include_once "modules_extra/modal_viewer/base/config.php";

if(isset($_GET['delete'])) {
	$pdo->query("DELETE FROM `modal_viewer` WHERE `id`='{$_GET['window']}'");
}

$tpl->load_template('elements/title.tpl');
$tpl->set("{title}", $page->title);
$tpl->set("{name}", $conf->name);
$tpl->compile('title');
$tpl->clear();

$tpl->load_template('head.tpl');
$tpl->set("{title}", $tpl->result['title']);
$tpl->set("{image}", $page->image);
$tpl->set("{other}", $module['to_head']);
$tpl->set("{token}", $token);
$tpl->set("{cache}", $conf->cache);
$tpl->set("{template}", $conf->template);
$tpl->set("{site_host}", $site_host);
$tpl->compile('content');
$tpl->clear();

$tpl->load_template('top.tpl');
$tpl->set("{site_host}", $site_host);
$tpl->set("{site_name}", $conf->name);
$tpl->compile('content');
$tpl->clear();

$tpl->load_template('menu.tpl');
$tpl->set("{site_host}", $site_host);
$tpl->compile('content');
$tpl->clear();

$nav = array($PI->to_nav('admin', 0, 0),
             $PI->to_nav('admin_modules', 0, 0),
             $PI->to_nav('admin_modal_viewer', 1, 0));
$nav = $tpl->get_nav($nav, 'elements/nav_li.tpl', 1);

$tpl->load_template('page_top.tpl');
$tpl->set("{nav}", $nav);
$tpl->compile('content');
$tpl->clear();

	/* Загрузка окон */
$STH = $pdo->query("SELECT * FROM `modal_viewer` WHERE 1");
$STH->setFetchMode(PDO::FETCH_OBJ);
	/* Загрузка окон */

$modalLists = "<table class=\"table\"><thead><tr><th scope=\"col\">#</th><th scope=\"col\">Заголовок</th><th scope=\"col\">Сообщение</th><th scope=\"col\">Действие</th></tr></thead><tbody>";
while($row = $STH->fetch()) {
	$modalLists .= "<tr><th scope=\"row\">{$row->id}</th><td id=\"title{$row->id}\">{$row->title}</td><td id=\"texts{$row->id}\">{$row->text}</td>
	<td><button class=\"btn btn-default\" onclick=\"remove_modal({$row->id});\"><i class=\"glyphicon glyphicon-trash\"></i></button>
	<button class=\"btn btn-default\" onclick=\"Editor_modal({$row->id});\" id=\"Editor_modal{$row->id}\"><i id=\"onIcon{$row->id}\" class=\"glyphicon glyphicon-edit\"></i></button></td></tr>";		
}
$modalLists .= '</tbody></table>';

$tpl->load_template($module['tpl_dir_admin'].'index.tpl');
$tpl->set("{site_host}", $site_host);
$tpl->set("{list_modal_viewer}", $modalLists);
$tpl->compile('content');
$tpl->clear();

$tpl->load_template('bottom.tpl');
$tpl->set("{site_host}", $site_host);
$tpl->compile('content');
$tpl->clear();