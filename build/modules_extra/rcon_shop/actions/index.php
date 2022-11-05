<?php
require_once __DIR__ . '/../../../inc/start.php';
require_once __DIR__ . '/../../../inc/protect.php';
require_once __DIR__ . '/../base/inc/config.php';

global $messages;

$AjaxResponse = new AjaxResponse();

if(!isPostRequest() || !isRightToken() || !is_auth()) {
	$AjaxResponse->status(false)->alert('Ошибка')->send();
}

if(isset($_POST['buy'])) {
	$productId = clean($_POST['product'], 'int');
	$tarifId = clean($_POST['tarif'], 'int');

	$CommandParam = new RconShop\CommandParam();

	$product = (new RconShop\Product)->get($productId);
	$tarif = (new RconShop\Tarif())->get($tarifId);
	$params = $CommandParam->getList($productId);

	if(empty($product) || empty($tarif) || $product->id != $tarif->product_id) {
		$AjaxResponse->status(false)->alert('Ошибка')->send();
	}

	try {
		$userParams = $CommandParam->setUserParams($params, $_POST);
		$CommandParam->validateUserParams($userParams);
		$userParams = $CommandParam->putQuotesToUserParams($userParams);
	} catch(InputValidationException $exception) {
		$AjaxResponse->error($exception->getInput(), $exception->getMessage())->send();
	}

	$category = (new RconShop\Category())->get($product->category_id);
	$server = (new ServersManager())->getServer($category->server_id);

	$discount = calculate_discount(
			$server->discount,
			pdo()->query("SELECT discount FROM config__prices LIMIT 1")->fetch(PDO::FETCH_OBJ)->discount,
			user()->proc
	);
	$price = calculate_price($tarif->price, $discount);

	if(user()->shilings < $price) {
		$priceDelta = round_shilings($price - user()->shilings);
		$AjaxResponse->error(
				'data',
				"У Вас недостаточно средств. "
				. "<a href='../purse?price=$priceDelta'>"
				. "Пополните баланс на $priceDelta{$messages['RUB']}"
				. "</a>"
		)->send();
	}

	$SourceQuery = (new OurSourceQuery)->setServer($server);

	if(!$SourceQuery->isServerCanWorkWithRcon()) {
		$AjaxResponse->alert('Отправка rcon команды невозможна')->send();
	}

	try {
		$command = $CommandParam->collectCommand($tarif->command, $userParams);
		$answer = $SourceQuery->checkConnect()->auth()->send($command);
	} catch(Exception $exception) {
		$AjaxResponse
			->error('data', 'Ошибка, попробуйте позже: ' . $exception->getMessage())
			->send();
	}

	$SourceQuery->Disconnect();


	$shilings = round_shilings(user()->shilings - $price);

	pdo()
		->prepare("INSERT INTO money__actions (date,shilings,author,type) values (:date, :shilings, :author, :type)")
		->execute(['date' => date('Y-m-d H:i:s'), 'shilings' => -$price, 'author' => user()->id, 'type' => 22]);

	pdo()
		->prepare("UPDATE users SET shilings=:shilings WHERE id=:id LIMIT 1")
		->execute([':shilings' => $shilings, ':id' => user()->id]);

	(new RconShop\Buy())->add($product->id, $tarif->id, user()->id, $command, $answer);

	incNotifications();

	$notyForUser = "Поздравляем Вас с успешной покупкой <b>"
		. "<a href='../shop/product?id=$product->id'>$product->title</a></b>!";
	send_noty(pdo(), $notyForUser, user()->id, 2);

	$notyForAdmin = "Совершена покупка <b>"
		. "<a href='../shop/product?id=$product->id'>" . "$product->title</a>"
		. "</b> в магазине пользователем: <b>"
		. "<a href='../profile?id=" . user()->id ."'>".user()->login."</a>"
		. "</b>";
	send_noty(pdo(), $notyForAdmin, 0, 2);

	$AjaxResponse->data(['shilings' => $shilings,])->send();
}