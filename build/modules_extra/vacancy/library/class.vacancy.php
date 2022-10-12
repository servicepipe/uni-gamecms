<?PHP
	class Vacancy {
		var $temp;
		
		public static function conf($tpl = null) {
			$row = pdo()->query("SELECT * FROM `vacancy__configs` LIMIT 1")->fetch(PDO::FETCH_OBJ);
			
			return (object)[
				'patch' => "/modules_extra/vacancy/",
				'templates' => "../../../modules_extra/vacancy/templates/" . (isset($tpl) ? "admin" : configs()->template) . "/tpl/",
				'styles' => "/modules_extra/vacancy/templates/" . (isset($tpl) ? "admin" : configs()->template) . "/css/",
				'tpl' => $_SERVER['DOCUMENT_ROOT'] . '/modules_extra/vacancy/templates/' . (isset($tpl) ? "admin" : configs()->template) . '/tpl/',
				'limit' => $row->limit_vacancy,
				'next_days' => $row->next_days
			];
		}
		
		public static function add($sid, $vacancy, $info) {
			if(pdo()->prepare("INSERT INTO `vacancy`(`uid`, `sid`, `vacancy`, `info`, `date`) VALUES (:uid, :sid, :vacancy, :info, :date)")->execute([
				':uid' => $_SESSION['id'],
				':sid' => $sid,
				':vacancy' => $vacancy,
				':info' => json_encode($info),
				':date' => date("Y-m-d H:i:s")
			])) {
				return pdo()->lastInsertId();
			}
			
			return 0;
		}
		
		public static function GetList($page = 1, $sid = null) {
			$limit = self::conf()->limit;
			$start = ($page * $limit) - $limit;
			
			$sth = pdo()->query("SELECT * FROM `vacancy` " . (isset($sid) ? "WHERE `sid`='$sid'" : "") . " ORDER BY `id` DESC LIMIT $start, $limit");
			
			if(!$sth->rowCount()) {
				return "<center>Список заявок пуст.</center>";
			}
			
			$buf = "";
			
			while($row = $sth->fetch(PDO::FETCH_OBJ)) {
				$status = self::ChangeStatus($row->status);
				$user = self::GetUserData($row->uid);
				$group = get_groups(pdo())[$user->rights];
				
				$buf .= file_get_contents(self::conf()->tpl . 'elements/ui.tpl');
				$buf = str_replace('{id}', $row->id, $buf);
				$buf = str_replace('{uid}', $row->uid, $buf);
				$buf = str_replace('{date}', expand_date($row->date), $buf);
				$buf = str_replace('{status}', $status['name'], $buf);
				$buf = str_replace('{class}', $status['class'], $buf);
				$buf = str_replace('{name}', self::GetValidName($row->uid), $buf);
				$buf = str_replace('{vacancy}', self::GetVacancyName($row->vacancy), $buf);
				$buf = str_replace("{gp_color}", $group['color'], $buf);
				$buf = str_replace("{gp_name}", $group['name'], $buf);
			}
			
			return $buf;
		}
		
		public static function ChangeStatus($id) {
			switch($id) {
				case 1: return ['class' => 'text-success', 'name' => 'Одобрено'];
				case 2: return ['class' => 'text-primary', 'name' => 'Не рассмотрена'];
				default: return ['class' => 'text-danger', 'name' => 'Отказано'];
			}
		}
		
		public static function GetUserData($uid) {
			return (new Users(pdo()))->getUserData(pdo(), $uid);
		}
		
		public static function GetValidName($uid) {
			$user = self::GetUserData($uid);
			
			if(isset($user->name) && $user->name != '---') {
				return $user->name;
			}
			else if(isset($user->nickname) && $user->nickname != '---') {
				return $user->nickname;
			}
			
			return $user->login;
		}
		
		public static function IsValidVacancy($id) {
			$id = clean($id, "int");
			
			if(empty($id)) {
				return false;
			}
			
			$sth = pdo()->query("SELECT * FROM `vacancy` WHERE `id`='$id' LIMIT 1");
			
			if($sth->rowCount()) {
				return true;
			}
			
			return false;
		}
		
		public static function GetVacancy($id) {
			if(!self::IsValidVacancy($id)) {
				return show_error_page('not_page');
			}
			
			$sth = pdo()->query("SELECT * FROM `vacancy` WHERE `id`='$id' LIMIT 1");
			return $sth->fetch(PDO::FETCH_OBJ);
		}
		
		public static function GetVacancyName($id) {
			$id = clean($id, "int");
			
			if(empty($id)) {
				return 'Неизвестно';
			}
			
			$sth = pdo()->query("SELECT * FROM `vacancy__list` WHERE `id`='$id' LIMIT 1");
			
			if(!$sth->rowCount()) {
				return 'Неизвестно';
			}
			
			return $sth->fetch(PDO::FETCH_OBJ)->name;
		}
		
		public static function SendMessage($vid, $message) {
			$vid = clean($vid, "int");
			
			if(empty($vid)) {
				result(['alert' => 'error',	'message' => 'Неверный индекс Вакансии.']);
			}
			
			$message = clean($message);
			
			if(empty($message)) {
				result(['alert' => 'error',	'message' => 'Сначала введите текст.']);
			}
			
			return pdo()->prepare("INSERT INTO `vacancy__messages`(`uid`, `vid`, `message`, `date`) VALUES (:uid, :vid, :message, :date)")->execute([
				':uid' => $_SESSION['id'],
				':vid' => $vid,
				':message' => htmlspecialchars($message),
				':date' => date("Y-m-d H:i:s")
			]);
		}
		
		public static function GetMessages($vid) {
			$vid = clean($vid, "int");
			
			if(empty($vid)) {
				result(['alert' => 'error', 'message' => 'Неверный индекс Вакансии.']);
			}
			
			$sth = pdo()->query("SELECT * FROM `vacancy__messages` WHERE `vid`='$vid' ORDER BY `id` DESC");
			
			if(!$sth->rowCount()) {
				return "<center>Сообщений нет.</center>";
			}
			
			$buf = "";
			
			while($row = $sth->fetch(PDO::FETCH_OBJ)) {
				$user = self::GetUserData($row->uid);
				$group = get_groups(pdo())[$user->rights];
			
				$buf .= file_get_contents(self::conf()->tpl . 'elements/message.tpl');
				$buf = str_replace("{id}", $row->id, $buf);
				$buf = str_replace("{uid}", $row->uid, $buf);
				$buf = str_replace("{name}", self::GetValidName($row->uid), $buf);
				$buf = str_replace("{avatar}", $user->avatar, $buf);
				$buf = str_replace("{date}", expand_date($row->date), $buf);
				$buf = str_replace("{message}", htmlspecialchars_decode($row->message), $buf);
				$buf = str_replace("{gp_color}", $group['color'], $buf);
				$buf = str_replace("{gp_name}", $group['name'], $buf);
			}
			
			return $buf;
		}
		
		public static function Parse($info) {
			$info = json_decode($info, true);
			
			if(isset($info)) {
				$buf = "<hr>";
				foreach($info as $key => $value) {
					if(strripos($key, '_title') === false) {
						$buf .= "<li><span class='h'>" . self::SearchArray($info, $key . '_title') . ":</span> " . $value . "</li>";
					}
				}
				
				return $buf;
			}
		}
		
		public static function SearchArray($arr, $search) {
			foreach($arr as $key => $value) {
				if($search == $key) {
					return $value;
				}
			}
			
			return null;
		}
		
		public static function GetServers() {
			$sth = pdo()->query("SELECT * FROM `servers` WHERE 1");
			
			if(!$sth->rowCount()) {
				return "<option disabled selected>Серверов нет</option>";
			}
			
			$buf = "<option selected disabled>Выберите сервер</option>";
			while($row = $sth->fetch(PDO::FETCH_OBJ)) {
				$buf .= "<option value='" . $row->id . "'>" . $row->name . " - " . $row->address . "</option>";
			}
			
			return $buf;
		}
		

		public static function GetVacancies($sid) {
			$sid = clean($sid, "int");
			
			if(empty($sid)) {
				return show_error_page('not_page');
			}
			
			$sth = pdo()->query("SELECT * FROM `vacancy__list` WHERE `sid`='$sid'");
			
			if(!$sth->rowCount()) {
				return "<option disabled selected>Ваканский нет</option>";
			}
			
			$buf = "<option selected disabled>Выберите вакансию</option>";
			while($row = $sth->fetch(PDO::FETCH_OBJ)) {
				$buf .= "<option value='" . $row->id . "'>" . $row->name . "</option>";
			}
			
			return $buf;
		}
		
		public static function GetCustoms($sid) {
			$sid = clean($sid, "int");
			
			if(empty($sid)) {
				return "";
			}
			
			$sth = pdo()->query("SELECT * FROM `vacancy__names` WHERE `sid`='$sid'");
			
			if(!$sth->rowCount()) {
				return "";
			}
			
			$buf = "";
			while($row = $sth->fetch(PDO::FETCH_OBJ)) {
				$buf .= "<label class='mb-0' for='" . $row->name . "'><h5>" . $row->title . "</h5></label>";
				$buf .= "<input class='form-control' name='" . $row->name . "' placeholder='" . $row->placeholder . "' autocomplete='off' required>";
				$buf .= "<input type='hidden' name='" . $row->name . "_title' value='" . $row->title . "'>";
			}
			
			return $buf;
		}
		
		public static function GetNamesForId($name) {
			$name = clean($name);
			
			if(empty($name)) {
				return null;
			}
			
			$sth = pdo()->query("SELECT * FROM `vacancy__names` WHERE `name`='$name' LIMIT 1");
			
			if(!$sth->rowCount()) {
				return null;
			}
			
			return $sth->fetch(PDO::FETCH_OBJ);
		}
		
		public static function GetListServers($sid = 0) {
			$sth = pdo()->query("SELECT * FROM `servers` WHERE 1");
			
			$buf = "<li><a href='/vacancy' class='" . (($sid == 0) ? "active" : "") . "'>Все сервера</a></li>";
			
			if(!$sth->rowCount()) {
				return $buf;
			}
			
			while($row = $sth->fetch(PDO::FETCH_OBJ)) {
				$buf .= "<li><a href='/vacancy?sid=" . $row->id . "' class='" . (($sid == $row->id) ? "active" : "") . "'>" . $row->name . "</a></li>";
			}
			
			return $buf;
		}
		
		public static function SetVacancyStatus($vid, $status, $reason = 'none') {
			return pdo()->prepare("UPDATE `vacancy` SET `status`=:status, `reason`=:reason WHERE `id`=:vid")->execute([
				':status' => $status,
				':vid' => $vid,
				':reason' => $reason
			]);
		}
		
		public static function IsExists($sid, $uid) {
			$sid = clean($sid, "int");
			
			if(empty($sid)) {
				result(['alert' => 'error', 'message' => 'Неверный индекс сервера']);
			}
			
			$uid = clean($uid, "int");
			
			if(empty($uid)) {
				result(['alert' => 'error', 'message' => 'Неверный индекс пользователя']);
			}
			
			$sth = pdo()->query("SELECT * FROM `vacancy` WHERE `sid`='$sid' and `uid`='$uid' ORDER BY `id` DESC LIMIT 1");
			
			if(!$sth->rowCount()) {
				return false;
			}
			
			$row = $sth->fetch(PDO::FETCH_OBJ);
			$_ending = strtotime("+" . self::conf()->next_days . " day", false);
			$_create = strtotime($row->date);
			
			if(time() >= ($_create + $_ending)) {
				return true;
			}
			
			return [
				'date' => ($_create + $_ending)
			];
		}
		
		public static function rowVacancy($sid = null) {
			$sid = clean($sid, "int");
			
			if(empty($sid)) {
				return pdo()->query("SELECT * FROM `vacancy` WHERE 1")->rowCount();
			}
			
			return pdo()->query("SELECT * FROM `vacancy` WHERE `sid`='$sid'")->rowCount();
		}
	}