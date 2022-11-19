<?PHP
	require_once($_SERVER['DOCUMENT_ROOT'] . '/modules_extra/clans/start.php');
	
	if(empty($_POST['phpaction']) || $_POST['token'] != $_SESSION['token'] || empty($_SESSION['id'])) {
		result(['alert' => 'error']);
	}
	
	/*
		Создание нового Клана
	*/
	if(isset($_POST['Create'])) {
	    if(Clans::IsglavaClan($_SESSION['id'])) {
			result(['alert' => 'warning', 'message' => 'У Вас уже есть Клан']);
		}
		
		if(Clans::IsJoined($_SESSION['id'])) {
			result(['alert' => 'warning', 'message' => 'Вы уже состоите в Клане']);
		}
		
		$name = clean($_POST['name']);
		
		if(empty($name)) {
			result(['alert' => 'warning', 'message' => 'Введите название']);
		}
		
		Clans::Create($name);
	}
	
	/*
		Действия с кнопкой Клана
	*/
	if(isset($_POST['ClanId'])) {
		$clanid = clean($_POST['ClanId']);
		
		if(empty($clanid)) {
			result(['alert' => 'warning', 'message' => 'Укажите индекс Клана']);
		}
		
		$clan = Clans::Get($clanid);
		
		switch(Clans::IsJoinedClan($clanid)) {
			case '0': {
                if(Clans::IsJoined($_SESSION['id'])) 
                {
			        result(['alert' => 'warning', 'message' => 'Вы уже состоите в Клане']);
		        }
				else if(Clans::Enter($clanid)) {
					result(['alert' => 'success', 'message' => 'Заявка на вступление отправлена.']);
				}
			}
			case '1': {
				if($clan->uid != $_SESSION['id']) {
					if(Clans::Logout($clanid)) {
						result(['alert' => 'success', 'message' => 'Клан успешно покинут.']);
					}
				}
				else {
					result(['alert' => 'warning', 'message' => 'Создатель Клана не может покинуть его.']);
				}
				
				break;
			}
			case '2': {
				if(Clans::Logout($clanid)) {
					result(['alert' => 'success', 'message' => 'Заявка на вступление отменена.']);
				}
			}
		}
	}
	
	/*
		Получение Ролей
	*/
	if(isset($_POST['roles'])) {
		$cid = clean($_POST['cid'], "int");
		
		if(empty($cid)) {
			result(['alert' => 'warning', 'message' => 'Введите все параметры']);
		}
		
		if(!IsFlags($_SESSION['id'], $cid, 'u')) {
			result(['alert' => 'warning', 'message' => 'Недостаточно прав']);
		}
		
		result(Clans::GetListRole($cid));
	}
	
	/*
		Получение Заявок
	*/
	if(isset($_POST['applications'])) {
		$cid = clean($_POST['cid'], "int");
		
		if(empty($cid)) {
			result(['alert' => 'warning', 'message' => 'Введите все параметры']);
		}
		
		if(!IsFlags($_SESSION['id'], $cid, 'a')) {
			result(['alert' => 'warning', 'message' => 'Недостаточно прав']);
		}
		
		result(Clans::GetListApplications($cid));
	}
	
	/*
		Принятие Заявки
	*/
	if(isset($_POST['accept'])) {
		$id = clean($_POST['id'], "int");
		$cid = clean($_POST['cid'], "int");
		
		if(empty($id) || empty($cid)) {
			result(['alert' => 'warning', 'message' => 'Введите все параметры']);
		}
		
		if(!IsFlags($_SESSION['id'], $cid, 'a')) {
			result(['alert' => 'warning', 'message' => 'Недостаточно прав']);
		}
		
		if(Clans::GetRowCount($cid) >= Clans::Get($cid)->max_users) {
			result(['alert' => 'warning', 'message' => 'Достигнут лимит участников Клана']);
		}
		
		Clans::accept($id);
		result(Clans::GetListApplications($cid));
	}
	
	/*
		Отказ Заявки
	*/
	if(isset($_POST['deny'])) {
		$id = clean($_POST['id'], "int");
		$cid = clean($_POST['cid'], "int");
		
		if(empty($id) || empty($cid)) {
			result(['alert' => 'warning', 'message' => 'Введите все параметры']);
		}
		
		if(!IsFlags($_SESSION['id'], $cid, 'a')) {
			result(['alert' => 'warning', 'message' => 'Недостаточно прав']);
		}
		
		Clans::deny($id);
		result(Clans::GetListApplications($cid));
	}
	
	/*
		Смена Роли
	*/
	if(isset($_POST['ChangeRole'])) {
		$id = clean($_POST['id'], "int");
		$group = clean($_POST['group'], "int");
		
		if(empty($id) || empty($group)) {
			result(['alert' => 'warning', 'message' => 'Введите все параметры']);
		}
		
		if(!IsFlags($_SESSION['id'], $_POST['cid'], 'u')) {
			result(['alert' => 'warning', 'message' => 'Недостаточно прав']);
		}
		
		switch($group) {
			case '1': {
				pdo()->prepare("UPDATE `clans__joined` SET `gid`='4' WHERE `cid`=:cid and `gid`='1' LIMIT 1")->execute([':cid' => $_POST['cid']]);
			
				pdo()->prepare("UPDATE `clans` SET `uid`=:uid WHERE `id`=:id LIMIT 1")->execute([
					':uid' => $_POST['uid'], ':id' => $_POST['cid']
				]);
				
				break;
			}
		}
		
		pdo()->prepare("UPDATE `clans__joined` SET `gid`=:group WHERE `id`=:id LIMIT 1")->execute([
			':group' => $group, ':id' => $id
		]);
		
		result(Clans::GetListRole($_POST['cid']));
	}
	
	/*
		Смена статуса
	*/
	if(isset($_POST['ChangeStatus'])) {
		if(!IsFlags($_SESSION['id'], $_POST['cid'], 'b')) {
			result(['alert' => 'warning', 'message' => 'Недостаточно прав']);
		}
	
		Clans::ChangeStatus($_POST['cid'], $_POST['message']);
	}
	
	/*
		Смена логотипа
	*/
	if(isset($_POST['ChangeLogotype'])) {
		if(!IsFlags($_SESSION['id'], $_POST['cid'], 'u')) {
			result(['alert' => 'warning', 'message' => 'Недостаточно прав']);
		}
	
		ignore_user_abort(true);
		set_time_limit(0);
		
		$result = file_uploads2(Clans::Conf()->Patch . 'uploads/images/logotype/', $_FILES['image']);
				
		if($result['alert']) {
			pdo()->prepare("UPDATE `clans` SET `logotype`=:logotype WHERE `id`=:cid LIMIT 1")->execute([
				':logotype' => $result['name'], ':cid' => $_POST['cid']
			]);			
		}
	}
	
	/*
		Смена обложки
	*/
	if(isset($_POST['ChangeCover'])) {
		if(!IsFlags($_SESSION['id'], $_POST['cid'], 'u')) {
			result(['alert' => 'warning', 'message' => 'Недостаточно прав']);
		}
	
		ignore_user_abort(true);
		set_time_limit(0);
		
		$result = file_uploads2(Clans::Conf()->Patch . 'uploads/images/cover/', $_FILES['image']);
		
		if($result['alert']) {
			pdo()->prepare("UPDATE `clans` SET `cover`=:cover WHERE `id`=:cid LIMIT 1")->execute([
				':cover' => $result['name'], ':cid' => $_POST['cid']
			]);
		}
	}
	
	/*
		Покупка предметов с Магазина
	*/
	if(isset($_POST['BuyItem'])) {
		$id = clean($_POST['id']);
		
		if(empty($id)) {
			result(['alert' => 'warning', 'message' => 'Заполните все данные']);
		}
		
		$userclan = Clans::GetUserClan($_SESSION['id']);
		
		if(empty($userclan)) {
			result(['alert' => 'warning', 'message' => 'Вы не в клане']);
		}
		
		if(!IsFlags($_SESSION['id'], $userclan->cid, 'b')) {
			result(['alert' => 'warning', 'message' => 'Недостаточно прав']);
		}
		
		$item = Clans::GetShopItem($id);
		
		switch($item->id) {
			case '1': {
				$clan = Clans::Get($userclan->cid);
				
				if($clan->balance >= $item->price) {
					Clans::SetBalance($clan->id, $clan->balance - $item->price);
					Clans::Update($clan->id, 'max_users', $clan->max_users + 10);
					
					result(['alert' => 'success', 'message' => 'Успешая покупка!']);
				}
				else {
					result(['alert' => 'warning', 'message' => 'Недостаточно средств!']);
				}
				
				break;
			}
		}
	}
	
	/*
		Добавление Рейтинга
	*/
	if(isset($_POST['GiveLike'])) {
		if(Clans::IsLiked($_SESSION['id'])) {
			result(['alert' => 'error', 'message' => 'Вы уже ставили сегодня лайк!']);
		}
		
		$uid = clean($_POST['uid'], "int");
		
		if(empty($uid)) {
			result(['alert' => 'error', 'message' => 'Укажите все данные']);
		}
		
		if($uid == $_SESSION['id']) {
			result(['alert' => 'error', 'message' => 'Вы не можете ставить себе Лайк']);
		}
		
		$userclan = Clans::GetUserClan($uid);
		
		if(empty($userclan)) {
			result(['alert' => 'error', 'message' => 'Пользователь не состоит в Клане']);
		}
		
		Clans::GiveLike($_SESSION['id'], $uid);
		
		result(['alert' => 'info', 'message' => 'Вы подняли Рейтинг пользователя']);
	}