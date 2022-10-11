<?PHP
	require($_SERVER['DOCUMENT_ROOT'] . '/inc/start.php');
	
	if(empty($_POST['phpaction']) || !is_auth()) {
		result(['alert' => 'error']);
	}
	
	if($_SESSION['token'] != clean($_POST['token'])) {
		result(['alert' => 'error']);
	}
	
	$Autoloader->addNamespace(
		Autoloader::CORE_NAMESPACE, [
			$_SERVER['DOCUMENT_ROOT'] . '/modules_extra/money_transfer/library/'
		]
	);

	$Autoloader->register();
	
	if(isset($_POST['transfer'])) {
		$count	= clean($_POST['count'], "int");
		
		if(empty($count) || $count < 0) {
			result(['alert' => 'error', 'message' => 'Неверная сумма']);
		}
		
		if($_SESSION['id'] == $_POST['uid']) {
			result(['alert' => 'warning', 'message' => 'Нельзя переводить самому себе']);
		}
		
		/* Отправитель */
		$_user1	= MoneyTransfer::GetUserData($_SESSION['id']);
		
		if(empty($_user1)) {
			result(['alert' => 'warning', 'message' => 'Неверный индекс отправителя']);
		}
		
		/* Получать */
		$uid	= clean($_POST['uid'], "int");
		$_user2 = MoneyTransfer::GetUserData($uid);
		
		if(empty($_user2)) {
			result(['alert' => 'warning', 'message' => 'Неверный индекс получателя']);
		}
		
		if($_user1->shilings >= $count) {
			incNotifications();
			
			if(MoneyTransfer::SetUserMoney($_SESSION['id'], $_user1->shilings - $count) && MoneyTransfer::SetUserMoney($uid, $_user2->shilings + $count)) {
				send_noty(pdo(), "Вы перевели пользователю " . $_user2->login . "$count руб.", $_SESSION['id'], 1);
				send_noty(pdo(), $_user1->login . " перевёл(-а) на ваш счёт $count руб.", $uid, 1);
				
				result(['alert' => 'success', 'message' => 'Перевод прошёл успешно!']);
			}
			else {
				result(['alert' => 'error', 'message' => 'Ошибка перевода']);
			}
		}
		else {
			result(['alert' => 'warning', 'message' => 'Недостаточно средств']);
		}
	}
	
	if(isset($_POST['getButton'])) {
		result([
			'content' => MoneyTransfer::GetButton($_POST['uid'])
		]);
	}