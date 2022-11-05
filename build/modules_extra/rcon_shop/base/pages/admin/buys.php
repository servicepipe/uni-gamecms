<?php
require_once __DIR__ . '/../../inc/config.php';

global $messages;
global $ExtraModule;
global $PI;

if(!empty($_GET['category'])) {
	$categoryId = clean($_GET['category'], 'int');
} else {
	$categoryId = 0;
}

if(!empty($_GET['server'])) {
	$serverId = clean($_GET['server'], 'int');
} else {
	$serverId = 0;
}


$Category = new RconShop\Category();


$categories = $Category->getList($serverId, true);

foreach($categories as $category) {
	tpl()->load_template('elements/option.tpl');
	tpl()->set("{id}", $category->id);
	tpl()->set("{title}", $category->title);
	tpl()->set("{selected}", ($serverId == $category->id) ? 'selected' : '');
	tpl()->compile('categories');
	tpl()->clear();
}

$servers = $Category->getServers();
$generalServer = new \stdClass();
$generalServer->id = 0;
$generalServer->name = $messages['All'];
array_unshift($servers, $generalServer);

foreach($servers as $server) {
	tpl()->load_template('elements/option.tpl');
	tpl()->set("{id}", $server->id);
	tpl()->set("{title}", $server->name);
	tpl()->set("{selected}", ($serverId == $server->id) ? 'selected' : '');
	tpl()->compile('servers');
	tpl()->clear();
}

$Page = (new Page())
	->setAsset(
		$ExtraModule->moduleAssetUrl('actions/admin/ajax.js'),
		Page::SCRIPT_ASSET
	)
	->setBreadCrumbs(
		[
			$PI->to_nav('admin'),
			$PI->to_nav('admin_modules'),
			$PI->to_nav('admin_rcon_shop'),
			$PI->to_nav('admin_rcon_shop_buys', 1)
		]
	)
	->collectPage(
		'buys.tpl',
		[
			'servers'    => tpl()->result['servers'],
			'categories' => tpl()->result['categories']
		]
	);
