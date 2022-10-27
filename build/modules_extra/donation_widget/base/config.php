<?php
//Нельзя трогать
if(file_exists($_SERVER["DOCUMENT_ROOT"] . "/modules_extra/donation_widget/base/config.php")) {
	include_once $_SERVER["DOCUMENT_ROOT"] . "/modules_extra/donation_widget/base/config.php";
}

$module = [
	'name'          => 'donation_widget',
	'to_head'       => "<script src=\"$site_host/modules_extra/donation_widget/ajax/ajax.js?v={cache}\"></script>",
	'tpl_dir'       => "../../../modules_extra/donation_widget/templates/$conf->template/tpl/",
	'tpl_dir_admin' => "../../../modules_extra/donation_widget/templates/admin/tpl/",
];