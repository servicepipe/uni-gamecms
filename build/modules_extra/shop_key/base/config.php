<?php
$services_data = array(
	"1" => array(
		'type' => 'shop_credits',
		'name' => 'Кредиты',
		'name_2' => 'кредитов',
	),
	"2" => array(
		'type' => 'wcs_gold',
		'name' => 'Голд',
		'name_2' => 'голдов',
	),
	"3" => array(
		'type' => 'wcs_p_race',
		'name' => 'Раса',
		'name_2' => 'расы',
	),
	"4" => array(
		'type' => 'wcs_bank_lvl',
		'name' => 'Уровень Bank lvl',
		'name_2' => 'уровня bank lvl',
	)
);

//Нельзя трогать
$module = array(
	'name' => 'shop_key', 
	'to_head' => "<script src=\"$site_host/modules_extra/shop_key/ajax/ajax.js?v={cache}\"></script>", 
	'tpl_dir' => "../../../modules_extra/shop_key/templates/$conf->template/tpl/", 
	'tpl_dir_admin' => "../../../modules_extra/shop_key/templates/admin/tpl/", 
);
?>