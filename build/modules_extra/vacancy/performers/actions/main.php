<?PHP
	require_once($_SERVER['DOCUMENT_ROOT'] . '/modules_extra/vacancy/start.php');
	
	if(empty($_POST['phpaction']) || $_POST['token'] != $_SESSION['token'] || empty($_SESSION['id'])) {
		result(['alert' => 'error']);
	}
	
	if(isset($_POST['addVacansy'])) {
		$sid = clean($_POST['server'], "int");
		
		if(empty($sid)) {
			result(['alert' => 'warning', 'message' => 'Неверный индекс Вакансии']);
		}
		
		$exists = Vacancy::IsExists($sid, $_SESSION['id']);
		if(isset($exists['date'])) {
			result(['alert' => 'warning', 'message' => 'Вы уже подавали заявку.<br>Попробуйте ' . expand_date($exists['date'])]);
		}
		
		$vacancy = clean($_POST['vacancy'], "int");
		
		if(empty($vacancy)) {
			result(['alert' => 'warning', 'message' => 'Выберите вакансию']);
		}
		
		unset($_POST['token']);
		unset($_POST['phpaction']);
		unset($_POST['server']);
		unset($_POST['vacancy']);
		unset($_POST['addVacansy']);
		
		$result = Vacancy::add($sid, $vacancy, $_POST);
		
		if($result) {
			result(['alert' => 'success', 'id' => $result]);
		}
		
		result(['alert' => 'error']);
	}
	
	if(isset($_POST['SendMessage'])) {
		$vid = clean($_POST['vid']);
		
		if(empty($vid)) {
			result(['alert' => 'error', 'message' => 'Неверный индекс Вакансии']);
		}
	
		$message = clean($_POST['message']);
		
		if(empty($message)) {
			result(['alert' => 'warning', 'message' => 'Сначала введите сообщение']);
		}
		
		if(Vacancy::SendMessage($vid, $message)) {
			result(['alert' => 'info', 'content' => Vacancy::GetMessages($vid)]);
		}
	}
	
	if(isset($_POST['GetVacancies'])) {
		result([
			'content' => Vacancy::GetVacancies($_POST['sid']),
			'custom' => Vacancy::GetCustoms($_POST['sid'])
		]);
	}
	
	if(isset($_POST['VacancySuccess'])) {
		$vid = clean($_POST['vid'], "int");
		
		if(empty($vid)) {
			result(['alert' => 'error', 'message' => 'Неверный индекс Вакансии']);
		}
		
		if(!is_worthy("g")) {
			result(['alert' => 'error', 'message' => 'Недостаточно прав']);
		}
		
		if(Vacancy::SetVacancyStatus($vid, 1)) {
			result(['alert' => 'success']);
		}
		
		result(['alert' => 'error', 'message' => 'Прозошла ошибка']);
	}
	
	if(isset($_POST['VacancyRejection'])) {
		$vid = clean($_POST['vid'], "int");
		
		if(empty($vid)) {
			result(['alert' => 'error', 'message' => 'Неверный индекс Вакансии']);
		}
		
		$reason = clean($_POST['reason']);
		
		if(empty($reason)) {
			result(['alert' => 'warning', 'message' => 'Вы не указали причину Отказа']);
		}
		
		if(!is_worthy("g")) {
			result(['alert' => 'error', 'message' => 'Недостаточно прав']);
		}
		
		if(Vacancy::SetVacancyStatus($vid, 3, $reason)) {
			result(['alert' => 'success']);
		}
		
		result(['alert' => 'error', 'message' => 'Прозошла ошибка']);
	}