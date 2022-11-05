<?php
require_once __DIR__ . '/../inc/config.php';

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

$servers = (new RconShop\Category())->getServers();

if(!empty($servers)) {
	if(!in_array($serverId, array_column($servers, 'id'))) {
		$serverId = $servers[0]->id;
	}

	$categories = (new RconShop\Category())->getList($serverId, true);
} else {
	$categories = [];
}

if(!empty($categories)) {
	if(!in_array($categoryId, array_column($categories, 'id'))) {
		$categoryId = $categories[0]->id;
	}

	if($categoryId == 0) {
		$products = (new RconShop\Product())->getListByServer($serverId);
	} else {
		$products = (new RconShop\Product())->getList($categoryId);
	}

	foreach($products as $key => $product) {
		if(RconShop\Product::isEnabled($product->status)) {
			$product->image = RconShop\Product::getImageUrl($product->image);
			$product->price = (new RconShop\Tarif())->getList($product->id)[0]->price . $messages['RUB'];
		} else {
			unset($products[$key]);
		}
	}
} else {
	$products = [];
}

tpl()->load_template(Template::DOWN_TO_ROOT . tpl()->getRelativeExtraModuleDir(MODULE_NAME) . 'elements/categories.tpl');
tpl()->compile('categories');
tpl()->clear();

$Page = (new Page())
	->setAsset(
		$ExtraModule->moduleAssetUrl('templates/{template}/css/primary.css'),
		Page::STYLE_ASSET
	)
	->setBreadCrumbs(
		[
			$PI->to_nav('main'),
			$PI->to_nav('rcon_shop', 1)
		]
	)
	->collectPage(
		'index.tpl',
		['categories' => tpl()->result['categories']]
	);