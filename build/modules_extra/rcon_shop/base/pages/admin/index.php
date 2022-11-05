<?php
require_once __DIR__ . '/../../inc/config.php';


global $ExtraModule;
global $PI;

$servers = (new ServersManager())->getList();

foreach($servers as $server) {
	if($server->rcon == 1) {
		tpl()->load_template('elements/option.tpl');
		tpl()->set("{id}", $server->id);
		tpl()->set("{title}", $server->name);
		tpl()->set("{selected}", '');
		tpl()->compile('servers');
		tpl()->clear();
	}
}

$Page = (new Page())
	->setAsset(
		$ExtraModule->moduleAssetUrl('actions/admin/ajax.js'),
		Page::SCRIPT_ASSET
	)
	->setAsset(
		'../modules/editors/tinymce/tinymce.min.js',
		Page::SCRIPT_ASSET
	)
	->setBreadCrumbs(
		[
			$PI->to_nav('admin'),
			$PI->to_nav('admin_modules'),
			$PI->to_nav('admin_rcon_shop', 1)
		]
	)
	->collectPage(
		'index.tpl',
		[
			'servers' => tpl()->result['servers'],
			'image'   => $ExtraModule->moduleAssetUrl(
				RconShop\Product::IMAGES_PATH . RconShop\Product::DEFAULT_IMAGE
			),
		]
	);
