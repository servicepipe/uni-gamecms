<?php
require_once __DIR__ . '/../../../../inc/start.php';
require_once __DIR__ . '/../../../../inc/protect.php';
require_once __DIR__ . '/../../base/inc/config.php';

global $conf;
global $pdo;

$AjaxResponse = new AjaxResponse();

if(!isPostRequest() || !isRightToken() || !is_admin()) {
	$AjaxResponse->status(false)->alert('Ошибка')->send();
}

if(isset($_POST['addCategory'])) {
	$title = clean($_POST['title']);
	$server = clean($_POST['server'], 'int');

	$Category = new RconShop\Category();

	try {
		$Category->validateServer($server);
		$Category->validateTitle($title, 0, $server);
	} catch(InputValidationException $exception) {
		$AjaxResponse->error($exception->getInput(), $exception->getMessage())->send();
	}

	$Category->add($title, $server);
	$AjaxResponse->send();
}

if(isset($_POST['updateCategory'])) {
	$title = clean($_POST['title']);
	$id = clean($_POST['id'], 'int');

	$Category = new RconShop\Category();

	try {
		$Category->validateTitle($title, $id);
	} catch(InputValidationException $exception) {
		$AjaxResponse->error($exception->getInput(), $exception->getMessage())->send();
	}

	$Category->update($title, $id);
	$AjaxResponse->send();
}

if(isset($_POST['removeCategory'])) {
	$id = clean($_POST['id'], 'int');

	(new RconShop\Category())->remove($id);
	$AjaxResponse->send();
}

if(isset($_POST['loadCategories']) || isset($_POST['loadCategoriesOptions'])) {
	$serverId = clean($_POST['server']);

	$categories = (new RconShop\Category())->getList($serverId);

	$tpl = tpl();
	if(isset($_POST['loadCategories'])) {
		$tpl->setExtraModuleAdminDir(MODULE_NAME);
	} else {
		$tpl->setCoreAdminDir();
	}

	foreach($categories as $category) {
		if(isset($_POST['loadCategories'])) {
			$tpl->load_template('elements/category.tpl');
		} else {
			$tpl->load_template('elements/option.tpl');
		}

		$tpl->set("{id}", $category->id);
		$tpl->set("{title}", $category->title);
		$tpl->set("{selected}", '');
		$tpl->compile('content');
		$tpl->clear();
	}

	$AjaxResponse->data($tpl->getShow($tpl->result['content']))->send();
}

if(isset($_POST['saveProduct'])) {
	if(empty($_POST['id'])) {
		$id = 0;
		$category = clean($_POST['category'], 'int');
	} else {
		$id = clean($_POST['id'], 'int');
		$category = 0;
	}

	$title = clean($_POST['title']);
	$status = clean($_POST['status'], 'int');
	$isHasTarifs = clean($_POST['isHasTarifs'], 'int');
	$price = clean($_POST['tarif-price'], 'float');
	$command = clean($_POST['tarif-command']);
	$description = HTMLPurifier()->purify($_POST['description']);

	try {
		$Product = new RconShop\Product();

		if(empty($id)) {
			$Product->validateCategory($category);
		}

		$Product->validateTitle($title);
		$Product->validateStatus($status);
		$Product->validateIsHasTarifs($status);
		$Product->validateImage($_FILES);

		$CommandParam = new RconShop\CommandParam();
		$commandParams = RconShop\CommandParam::getParamsFromPost($_POST);
		$CommandParam->validateParams($commandParams);

		$Tarif = new RconShop\Tarif();
		if($isHasTarifs == 1) {
			$tarifs = RconShop\Tarif::getTarifsFromPost($_POST);
			$Tarif->validateTarifs($tarifs, $commandParams);
		} else {
			$Tarif->validatePrice($price);
			$Tarif->validateCommand($command, $commandParams);
		}

		if(empty($id)) {
			$image = $Product->uploadImage($_FILES);
		} else {
			if($Product->isHasImage($_FILES)) {
				$Product->removeImage($Product->get($id)->image);
				$image = $Product->uploadImage($_FILES);
			} else {
				$image = $Product->get($id)->image;
			}

			(new RconShop\Tarif())->removeProductTarifs($id);
			(new RconShop\CommandParam())->removeProductCommandParams($id);
		}

		if(empty($id)) {
			$Product->add($category, $title, $status, $isHasTarifs, $image, $description);
			$productId = get_ai(pdo(), 'rcon_shop__products') - 1;
		} else {
			$productId = $id;
			$Product->update($productId, $title, $status, $isHasTarifs, $image, $description);
		}

		$CommandParam->addList($productId, $commandParams);

		if($isHasTarifs == 1) {
			$Tarif->addList($productId, $tarifs);
		} else {
			$Tarif->add($productId, $title, $price, $command);
		}
	} catch(InputValidationException $exception) {
		$AjaxResponse->error($exception->getInput(), $exception->getMessage())->send();
	}

	$AjaxResponse->send();
}

if(isset($_POST['loadProducts'])) {
	$category = clean($_POST['category'], "int");

	$products = (new RconShop\Product())->getList($category);

	$tpl = tpl();
	$tpl->setExtraModuleAdminDir(MODULE_NAME);

	foreach($products as $product) {
		$tpl->load_template('elements/product.tpl');
		$tpl->set("{id}", $product->id);
		$tpl->set("{title}", $product->title);
		$tpl->set("{status}", $product->status);
		$tpl->set("{isHasTarifs}", $product->is_has_tarifs);
		$tpl->compile('content');
		$tpl->clear();
	}

	$AjaxResponse->data($tpl->getShow($tpl->result['content']))->send();
}

if(isset($_POST['removeProduct'])) {
	$id = clean($_POST['id'], 'int');

	(new RconShop\Product())->remove($id);
	$AjaxResponse->send();
}

if(isset($_POST['loadBuys'])) {
	$serverId   = clean($_POST['server'], 'int');
	$categoryId = clean($_POST['category'], 'int');
	$limit      = clean($_POST['limit'], 'int');

	$tpl = tpl();
	$tpl->setExtraModuleAdminDir(MODULE_NAME);

	$buys = (new RconShop\Buy())->getList($serverId, $categoryId, $limit);

	$tpl->result['content'] = ' ';

	foreach($buys as $buy) {
		$tpl->load_template('elements/buy.tpl');
		$tpl->set("{id}", $buy->id);
		$tpl->set("{title}", $buy->title);
		$tpl->set("{price}", $buy->price);
		$tpl->set("{product_id}", $buy->product_id);
		$tpl->set("{command}", $buy->command);
		$tpl->set("{answer}", $buy->answer);
		$tpl->set("{date}", expand_date($buy->date, 7));
		$tpl->set("{user_id}", $buy->user_id);
		$tpl->set("{user_avatar}", $buy->user_avatar);
		$tpl->set("{user_login}", $buy->user_login);
		$tpl->compile('content');
		$tpl->clear();
	}

	$AjaxResponse->data($tpl->getShow($tpl->result['content']))->send();
}
