<?php
if(!is_admin()) {
	show_error_page('not_adm');
}

include_once "modules_extra/donation_widget/base/config.php";

$tpl->load_template('elements/title.tpl');
$tpl->set("{title}", $page->title);
$tpl->set("{name}", $conf->name);
$tpl->compile('title');
$tpl->clear();

$tpl->load_template('head.tpl');
$tpl->set("{title}", $tpl->result['title']);
$tpl->set("{image}", $page->image);
$tpl->set(
	"{other}",
	$module['to_head'] .
	'<link rel="stylesheet" type="text/css" href="{site_host}templates/admin/css/timepicker.css" />' .
	'<script src="{site_host}templates/admin/js/timepicker/timepicker.js"></script>' .
	'<script src="{site_host}templates/admin/js/timepicker/jquery-ui-timepicker-addon.js"></script>' .
	'<script src="{site_host}templates/admin/js/timepicker/jquery-ui-timepicker-addon-i18n.min.js"></script>' .
	'<script src="{site_host}templates/admin/js/timepicker/jquery-ui-sliderAccess.js"></script>'
);
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

$nav = [
	$PI->to_nav('admin', 0, 0),
	$PI->to_nav('admin_modules', 0, 0),
	$PI->to_nav('admin_donation_widget', 1, 0)
];
$nav = $tpl->get_nav($nav, 'elements/nav_li.tpl', 1);

$tpl->load_template('page_top.tpl');
$tpl->set("{nav}", $nav);
$tpl->compile('content');
$tpl->clear();

$STH = $pdo->query("SELECT c.*, r.* FROM dw__config c LEFT JOIN dw__raisings r ON c.raising = r.id LIMIT 1");
$STH->setFetchMode(PDO::FETCH_OBJ);
$dw_conf = $STH->fetch();

$dw_enabled_act = get_active($dw_conf->enabled, 2);
$dw_comm_act = get_active($dw_conf->comments, 2);
$dw_autostop_act = get_active($dw_conf->autostop, 2);
$dw_list_act = get_active($dw_conf->showlist, 2);

$tpl->load_template($module['tpl_dir_admin'] . 'index.tpl');
$tpl->set("{site_host}", $site_host);
$tpl->set("{target}", (isset($dw_conf->target) ? $dw_conf->target : ""));
$tpl->set("{message}", (isset($dw_conf->message) ? $dw_conf->message : ""));
$tpl->set("{stopdate}", (isset($dw_conf->stopdate) ? $dw_conf->stopdate : ""));
$tpl->set("{listlimit}", (isset($dw_conf->listlimit) ? $dw_conf->listlimit : 0));
$tpl->set("{dw_enabled_act}", $dw_enabled_act[0]);
$tpl->set("{dw_enabled_act2}", $dw_enabled_act[1]);
$tpl->set("{dw_comm_act}", $dw_comm_act[0]);
$tpl->set("{dw_comm_act2}", $dw_comm_act[1]);
$tpl->set("{dw_autostop_act}", $dw_autostop_act[0]);
$tpl->set("{dw_autostop_act2}", $dw_autostop_act[1]);
$tpl->set("{dw_list_act}", $dw_list_act[0]);
$tpl->set("{dw_list_act2}", $dw_list_act[1]);
$tpl->compile('content');
$tpl->clear();

$tpl->load_template('bottom.tpl');
$tpl->set("{site_host}", $site_host);
$tpl->compile('content');
$tpl->clear();
?>