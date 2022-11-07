<?php
//Нельзя трогать
$module = array(
	'name' => 'buy_key', 
	'to_head' => "<script src=\"$site_host/modules_extra/buy_key/ajax/ajax.js?v={cache}\"></script>", 
	'tpl_dir' => "../../../modules_extra/buy_key/templates/$conf->template/tpl/", 
	'tpl_dir_admin' => "../../../modules_extra/buy_key/templates/admin/tpl/", 
);
?>