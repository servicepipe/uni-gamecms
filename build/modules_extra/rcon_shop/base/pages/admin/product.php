<?php
require_once __DIR__ . '/../../inc/config.php';

global $ExtraModule;
global $PI;

if(empty($_GET['id'])) {
	show_error_page('not_settings');
}

$id = clean($_GET['id'], 'int');

$Product = new RconShop\Product();
$product = $Product->get($id);

$commandParams = (new RconShop\CommandParam())->getList($product->id);
$tarifs = (new RconShop\Tarif())->getList($product->id);

$Page = (new Page())
	->setAsset(
		$ExtraModule->moduleAssetUrl('actions/admin/ajax.js'),
		Page::SCRIPT_ASSET
	)
	->setAsset(
		'../modules/editors/tinymce/tinymce.min.js',
		Page::SCRIPT_ASSET
	)
	->substituteToTitle($product->title)
	->setBreadCrumbs(
		[
			$PI->to_nav('admin'),
			$PI->to_nav('admin_modules'),
			$PI->to_nav('admin_rcon_shop'),
			$PI->to_nav('admin_rcon_shop_product', 1, 0, $product->title)
		]
	)
	->collectPage(
		'product.tpl',
		[
			'id'          => $product->id,
			'category_id' => $product->category_id,
			'title'       => $product->title,
			'image'       => $ExtraModule->moduleAssetUrl($product->image),
			'description' => $product->description,
			'isHasTarifs' => $product->is_has_tarifs,
			'status'      => $product->status,
		]
	);