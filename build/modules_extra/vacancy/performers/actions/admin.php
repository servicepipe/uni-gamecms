<?PHP
	require_once($_SERVER['DOCUMENT_ROOT'] . '/modules_extra/vacancy/start.php');
	
	if(empty($_POST['phpaction']) || $_POST['token'] != $_SESSION['token'] || !is_admin()) {
		result(['alert' => 'error']);
	}
	
	if(isset($_POST['SetConfigs'])) {
		$key = clean($_POST['key']);
		
		if(empty($key)) {
			result(['alert' => 'error', 'message' => 'Неверные параметры']);
		}
		
		$value = clean($_POST['value']);
		
		if(empty($value)) {
			result(['alert' => 'error', 'message' => 'Неверные параметры']);
		}
		
		if(pdo()->prepare("UPDATE `vacancy__configs` SET `$key`=:value LIMIT 1")->execute([
			':value' => $value
		])) {
			result(['alert' => 'success', 'message' => 'Конфигурации сохранены']);
		}
		
		result(['alert' => 'warning', 'message' => 'Произошла ошибка']);
	}
	
	if(isset($_POST['GetDataList'])) {
		$sid = clean($_POST['sid'], "int");
		
		if(empty($sid)) {
			result(['alert' => 'error', 'message' => 'Неверный индекс сервера']);
		}
		
		result([
			'alert' => 'success',
			'fields' => VacancyAdmin::GetFields($sid),
			'vacancy' => VacancyAdmin::GetVacancy($sid)
		]);
	}
	
	if(isset($_POST['RemoveVacancy'])) {
		$vid = clean($_POST['vid'], "int");
		
		if(empty($vid)) {
			result(['alert' => 'error', 'message' => 'Неверный индекс Вакансии']);
		}
		
		$sid = clean($_POST['sid'], "int");
		
		if(empty($sid)) {
			result(['alert' => 'error', 'message' => 'Неверный индекс Сервера']);
		}
		
		pdo()->exec("DELETE FROM `vacancy__list` WHERE `id`='$vid' LIMIT 1");
		result(['content' => VacancyAdmin::GetVacancy($sid)]);
	}
	
	if(isset($_POST['addVacancy'])) {
		$sid = clean($_POST['sid'], "int");
		
		if(empty($sid)) {
			result(['alert' => 'error', 'message' => 'Неверный индекс Сервера']);
		}
		
		$name = clean($_POST['title']);
		
		if(empty($name)) {
			result(['alert' => 'warning', 'message' => 'Укажите все параметры']);
		}
		
		if(VacancyAdmin::addVacancy($sid, $name)) {
			result(['content' => VacancyAdmin::GetVacancy($sid)]);
		}
	}
	
	if(isset($_POST['RemoveField'])) {
		$fid = clean($_POST['fid'], "int");
		
		if(empty($fid)) {
			result(['alert' => 'error', 'message' => 'Неверный индекс Поля']);
		}
		
		$sid = clean($_POST['sid'], "int");
		
		if(empty($sid)) {
			result(['alert' => 'error', 'message' => 'Неверный индекс Сервера']);
		}
		
		pdo()->exec("DELETE FROM `vacancy__names` WHERE `id`='$fid' LIMIT 1");
		result(['content' => VacancyAdmin::GetFields($sid)]);
	}
	
	if(isset($_POST['addField'])) {
		$sid = clean($_POST['sid'], "int");
		
		if(empty($sid)) {
			result(['alert' => 'error', 'message' => 'Неверный индекс Сервера']);
		}
		
		$title = clean($_POST['title']);
		
		if(empty($title)) {
			result(['alert' => 'warning', 'message' => 'Укажите заголовок']);
		}
		
		$name = clean($_POST['code']);
		
		if(empty($name)) {
			result(['alert' => 'warning', 'message' => 'Укажите кодовое словое']);
		}
		
		$placeholder = clean($_POST['placeholder']);
		
		if(empty($placeholder)) {
			result(['alert' => 'warning', 'message' => 'Укажите подсказку']);
		}
		
		if(VacancyAdmin::addField($sid, $title, $name, $placeholder)) {
			result(['content' => VacancyAdmin::GetFields($sid)]);
		}
	}