<?php
require_once __DIR__ . '/../inc/config.php';

global $messages;
global $ExtraModule;
global $PI;

if(!empty($_GET['id'])) {
	$id = clean($_GET['id'], 'int');
} else {
	$id = 0;
}

$product = (new RconShop\Product())->get($id);

if(empty($product) || !RconShop\Product::isEnabled($product->status)) {
	show_error_page();
}

$product->image = RconShop\Product::getImageUrl($product->image);
$tarifs = (new RconShop\Tarif())->getList($product->id);
foreach($tarifs as $key => $tarif) {
	$tarif->price = $tarif->price . $messages['RUB'];
}

$CommandParam = new RconShop\CommandParam();

$params = $CommandParam->getList($product->id);
$category = (new RconShop\Category())->get($product->category_id);
$categoryId = $product->category_id;
$serverId = $category->server_id;
$servers = (new RconShop\Category())->getServers();
$categories = (new RconShop\Category())->getList($serverId, true);

foreach($params as $param) {
	$param->slug = $CommandParam->getSlugFromName($param->name);
}

tpl()->load_template(Template::DOWN_TO_ROOT . tpl()->getRelativeExtraModuleDir(MODULE_NAME) . 'elements/categories.tpl');
tpl()->compile('categories');
tpl()->clear();

$Page = (new Page())
	->setAsset(
		$ExtraModule->moduleAssetUrl('actions/ajax.js'),
		Page::SCRIPT_ASSET
	)
	->setAsset(
		$ExtraModule->moduleAssetUrl('templates/{template}/css/primary.css'),
		Page::STYLE_ASSET
	)
	->substituteToTitle($product->title)
	->setBreadCrumbs(
		[
			$PI->to_nav('main'),
			$PI->to_nav('rcon_shop'),
			$PI->to_nav('rcon_shop_product', 1, 0, $product->title)
		]
	)
	->collectPage(
		'product.tpl',
		[
			'id' => $product->id,
			'title' => $product->title,
			'image' => $product->image,
			'description' => $product->description,
			'isHasTarifs' => $product->is_has_tarifs,
			'categoryName' => $categories[array_search($categoryId, array_column($categories, 'id'))]->title,
			'serverName' => $servers[array_search($serverId, array_column($servers, 'id'))]->name,
			'categories' => tpl()->result['categories']
		]
	);