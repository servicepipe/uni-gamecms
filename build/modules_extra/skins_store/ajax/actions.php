<?PHP
	include_once("../../../inc/start.php");
	include_once("../../../inc/protect.php");
	include_once("../base/config.php");
	
	if(empty($_POST['phpactions']) || !is_auth() || $_SESSION['token'] != $_POST['token']) {
		exit(json_encode(['status' => '2']));
	}
	
	if(isset($_POST['load_skins_list'])) {
		exit(json_encode([
			'status' => '1',
			'body' => $skins->get_skins_server($_POST['server_id'])['body']
		]));
	}
	
	if(isset($_POST['load_skin_info'])) {
		$info = $skins->get_skins_info($_POST['skin_id']);
		
		exit(json_encode([
			'status' => '1',
			'info' => $info['image'],
			'price' => $info['price']
		]));
	}
	
	if(isset($_POST['buy'])) {
		if(empty($_POST['nickname']) || $_POST['nickname'] == '') {
			exit(json_encode([
				'status' => '2',
				'type' => 'warning',
				'message' => 'Не оставляйте поле STEAM ID пустым!'
			]));
		}
		
		if(empty($_POST['password']) || $_POST['password'] == '') {
			exit(json_encode([
				'status' => '2',
				'type' => 'warning',
				'message' => 'Не оставляйте поле Пароля пустым!'
			]));
		}
		
		$r = $skins->buy($_POST['skin_id'], $_SESSION['id'], $_POST['nickname'], $_POST['password']);
		
		if($r['result']) {
			exit(json_encode([
				'status' => '1',
				'type' => 'success',
				'message' => "Спасибо за покупку {$r['name']}"
			]));
		}
		
		exit(json_encode([
			'status' => '2',
			'type' => 'danger',
			'message' => 'Недостаточно средств для покупки'
		]));
	}