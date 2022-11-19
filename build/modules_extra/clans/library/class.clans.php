<?PHP
	class Clans {
		public static function Conf($tpl = null) {
			return (object)[
				'Patch' => "/modules_extra/clans/",
				'Templates' => "../../../modules_extra/clans/templates/" . (isset($tpl) ? "admin" : configs()->template) . "/tpl/",
				'Styles' => "/modules_extra/clans/templates/" . (isset($tpl) ? "admin" : configs()->template) . "/css/",
				'Tpl' => $_SERVER['DOCUMENT_ROOT'] . '/modules_extra/clans/templates/' . (isset($tpl) ? "admin" : configs()->template) . '/tpl/'
			];
		}
		
		public static function configs() {
			return pdo()->query("SELECT * FROM `clans__configs` LIMIT 1")->fetch(PDO::FETCH_OBJ);
		}
		
		public static function Create($name) {
			if(self::configs()->price > usr($_SESSION['id'])->shilings) {
				result(['alert' => 'warning', 'message' => 'Недостаточно средств']);
			}
			
			$name = clean($name);
			
			if(empty($name)) {
				return false;
			}
			
			try {
				pdo()->prepare("INSERT INTO `clans`(`name`, `uid`, `date`) VALUES (:name, :uid, :date)")->execute([
					':name' => $name,
					':uid' => $_SESSION['id'],
					':date' => date("Y-m-d H:i:s")
				]);
				
				pdo()->prepare("UPDATE `users` SET `shilings`=:shilings WHERE `id`=:uid LIMIT 1")->execute([
					':shilings' => (usr($_SESSION['id'])->shilings - self::configs()->price),
					':uid' => $_SESSION['id']
				]);
			}
			catch(PDOException $e) {
				result(['alert' => 'error', 'message' => $e->getMessage()]);
			}
			
			if(self::Enter(self::GetLastId(), 1, 1)) {
				result(['alert' => 'success', 'id' => self::GetLastId()]);
			}
			
			return false;
		}
		
		public static function Enter($cid, $status = 2, $gid = 4) {
			pdo()->exec("DELETE FROM `clans__joined` WHERE `uid`='" . $_SESSION['id'] . "'");
			
			try {
				pdo()->prepare("INSERT INTO `clans__joined`(`cid`, `uid`, `status`, `gid`, `date`) VALUES (:cid, :uid, :status, :gid, :date)")->execute([
					':cid' => $cid,
					':uid' => $_SESSION['id'],
					':status' => $status,
					':gid' => $gid,
					':date' => date("Y-m-d H:i:s")
				]);
			}
			catch(PDOException $e) {
				result(['alert' => 'error', 'message' => $e->getMessage()]);
			}
			
			return true;
		}
		
		public static function IsValid($id) {
			$id = clean($id, "int");
			
			if(empty($id)) {
				return false;
			}
			
			return pdo()->query("SELECT * FROM `clans` WHERE `id`='$id' LIMIT 1")->rowCount();
		}
		
		public static function IsJoinedClan($cid) {
			$cid = clean($cid, "int");
			
			if(empty($cid)) {
				return 0;
			}
			
			$sth = pdo()->query("SELECT * FROM `clans__joined` WHERE `cid`='$cid' and `uid`='" . $_SESSION['id'] . "' LIMIT 1");
			
			if(!$sth->rowCount()) {
				return 0;
			}
			
			return $sth->fetch(PDO::FETCH_OBJ)->status;
		}
		
		public static function IsJoined($uid) {
			$uid = clean($uid, "int");
			
			if(empty($uid)) {
				return false;
			}
			
			return pdo()->query("SELECT * FROM `clans__joined` WHERE `uid`='$uid' and `status`='1' LIMIT 1")->rowCount();
		}

		public static function IsglavaClan($uid) {
            		$uid = clean($uid, "int");
			
			if(empty($uid)) {
				return false;
			}
			
			return pdo()->query("SELECT * FROM `clans` WHERE  `uid`='$uid' LIMIT 1")->rowCount();;
		}
		
		public static function GetLastId() {
			return pdo()->query("SELECT `id` FROM `clans` ORDER BY `id` DESC LIMIT 1")->fetch(PDO::FETCH_OBJ)->id;
		}
		
		public static function GetUIList() {
			$sth = pdo()->query("SELECT * FROM `clans` ORDER BY `rating` DESC");
			
			if(!$sth->rowCount()) {
				return '<center>Кланы еще не созданы</center>';
			}
			
			$temp = "";
			
			while($row = $sth->fetch(PDO::FETCH_OBJ)) {
				$temp .= file_get_contents(self::Conf()->Tpl . 'elements/list.tpl');
				$temp = str_replace('{name}', '<a href="/clans?id=' . $row->id . '">' . $row->name . '</a>', $temp);
				$temp = str_replace('{logotype}', self::Conf()->Patch . 'uploads/images/logotype/' . $row->logotype, $temp);
				$temp = str_replace('{date}', date("d.m.Y", strtotime($row->date)), $temp);
				$temp = str_replace('{rating}', $row->rating, $temp);
				$temp = str_replace('{players}', self::GetRowCount($row->id), $temp);
				$temp = str_replace('{max_players}', $row->max_users, $temp);
				
				$user = usr($row->uid);
				$temp = str_replace('{creator}', '<a href="/profile?id=' . $user->id . '">' . $user->login . '</a>', $temp);
				$temp = str_replace('{avatar}', convert_avatar($row->uid, true), $temp);
				$temp = str_replace('{group}', self::GetUserGroup($row->id, $row->uid), $temp);
			}
			
			return $temp;
		}
		
		public static function GetFlags($cid, $uid) {
			if(empty($uid)) {
				return '';
			}
			
			$cid = clean($cid, "int");
			
			if(empty($cid)) {
				return 'z';
			}
			
			$sth = pdo()->query("SELECT * FROM `clans__joined` WHERE `uid`='$uid' and `cid`='$cid' and `status`='1' LIMIT 1");
			
			if(!$sth->rowCount()) {
				return 'z';
			}
			
			return pdo()->query("SELECT * FROM `clans__groups` WHERE `id`='" . $sth->fetch(PDO::FETCH_OBJ)->gid . "' LIMIT 1")->fetch(PDO::FETCH_OBJ)->flags;
		}
		
		public static function GetUserGroup($cid, $uid = null) {
			$cid = clean($cid, "int");
			
			if(empty($cid)) {
				return null;
			}
			
			if(empty($uid)) {
				$uid = $_SESSION['id'];
			}
			
			$sth = pdo()->query("SELECT * FROM `clans__joined` WHERE `uid`='$uid' and `cid`='$cid' and `status`='1' LIMIT 1");
			
			if(!$sth->rowCount()) {
				return null;
			}
			
			return self::GetGroup($sth->fetch(PDO::FETCH_OBJ)->gid);
		}
		
		public static function GetGroup($id) {
			$id = clean($id, "int");
			
			if(empty($id)) {
				return null;
			}
			
			return pdo()->query("SELECT * FROM `clans__groups` WHERE `id`='$id' LIMIT 1")->fetch(PDO::FETCH_OBJ)->name;
		}
		
		public static function Get($id) {
			$id = clean($id, "int");
			
			if(empty($id)) {
				return null;
			}
			
			return pdo()->query("SELECT * FROM `clans` WHERE `id`='$id' LIMIT 1")->fetch(PDO::FETCH_OBJ);
		}
		
		public static function GetRowCount($id) {
			$id = clean($id, "int");
			
			if(empty($id)) {
				return 0;
			}
			
			return pdo()->query("SELECT * FROM `clans__joined` WHERE `cid`='$id' and `status`='1'")->rowCount();
		}
		
		public static function GetUIPlayers($cid) {
			$cid = clean($cid, "int");
			
			if(empty($cid)) {
				return '<center>Нет участников</center>';
			}
			
			$sth = pdo()->query("SELECT * FROM `clans__joined` WHERE `cid`='$cid' and `status`='1' ORDER BY `rating` DESC");
			
			if(!$sth->rowCount()) {
				return '<center>Нет участников</center>';
			}
			
			$temp = "";
			
			while($row = $sth->fetch(PDO::FETCH_OBJ)) {
				$temp .= file_get_contents(self::Conf()->Tpl . 'elements/player.tpl');
				
				$user = usr($row->uid);
				$temp = str_replace('{player}', '<a href="/profile?id=' . $user->id . '">' . $user->login . '</a>', $temp);
				$temp = str_replace('{avatar}', convert_avatar($row->uid, true), $temp);
				$temp = str_replace('{date}', date("d.m.Y (H:i)", strtotime($row->date)), $temp);
				$temp = str_replace('{uid}', $row->uid, $temp);
				$temp = str_replace('{rating}', $row->rating, $temp);
				$temp = str_replace('{nickname}', $user->nick, $temp);
				$temp = str_replace('{group}', self::GetUserGroup($row->cid, $row->uid), $temp);
				
				if(self::IsFinderLiked($row->uid)) {
					$temp = str_replace('{active}', 'active', $temp);
				}
				else {
					$temp = str_replace('{active}', '', $temp);
				}
			}
			
			return $temp;
		}
		
		public static function GetButton($cid) {
			switch(self::IsJoinedClan($cid)) {
				case '0': {
					return '<button type="button" class="btn btn-sm btn-primary" data-clan="' . $cid . '">Подать заявку</button>'; break;
				}
				case '1': {
					return '<button type="button" class="btn btn-sm btn-danger" data-clan="' . $cid . '">Покинуть</button>'; break;
				}
				case '2': {
					return '<button type="button" class="btn btn-sm btn-warning" data-clan="' . $cid . '">Отменить заявку</button>'; break;
				}
			}
		}
		
		public static function Logout($cid) {
			$cid = clean($cid, "int");
			
			if(empty($cid)) {
				return false;
			}
			
			return pdo()->query("DELETE FROM `clans__joined` WHERE `cid`='" . $cid . "' and `uid`='" . $_SESSION['id'] . "' LIMIT 1");
		}
		
		public static function GetRowWaits($cid) {
			$cid = clean($cid, "int");
			
			if(empty($cid)) {
				return 0;
			}
			
			return pdo()->query("SELECT * FROM `clans__joined` WHERE `cid`='$cid' and `status`='2'")->rowCount();
		}
		
		public static function GetListRole($cid) {
			$cid = clean($cid, "int");
			
			if(empty($cid)) {
				return 'Неизвестно';
			}
			
			$sth = pdo()->query("SELECT * FROM `clans__joined` WHERE `cid`='$cid' and `status`='1'");
			
			if(!$sth->rowCount()) {
				return '<center>Список пуст</center>';
			}
			
			while($row = $sth->fetch(PDO::FETCH_OBJ)) {
				$temp .= file_get_contents(self::Conf()->Tpl . 'elements/modal/role.tpl');
				
				$user = usr($row->uid);
				$temp = str_replace('{groups}', self::GetUIGroups($row->gid), $temp);
				$temp = str_replace('{name}', usr($row->uid)->login, $temp);
				$temp = str_replace('{id}', $row->id, $temp);
				$temp = str_replace('{uid}', $row->uid, $temp);
				$temp = str_replace('{cid}', $row->cid, $temp);
			}
			
			return $temp;
		}
		
		public static function GetListApplications($cid) {
			$cid = clean($cid, "int");
			
			if(empty($cid)) {
				return 'Неизвестно';
			}
			
			$sth = pdo()->query("SELECT * FROM `clans__joined` WHERE `cid`='$cid' and `status`='2'");
			
			if(!$sth->rowCount()) {
				return '<center>Список пуст</center>';
			}
			
			while($row = $sth->fetch(PDO::FETCH_OBJ)) {
				$temp .= file_get_contents(self::Conf()->Tpl . 'elements/modal/applications.tpl');
				
				$user = usr($row->uid);
				$temp = str_replace('{name}', usr($row->uid)->login, $temp);
				$temp = str_replace('{id}', $row->id, $temp);
				$temp = str_replace('{cid}', $cid, $temp);
			}
			
			return $temp;
		}
		
		public static function GetUIGroups($gid = null) {
			$sth = pdo()->query("SELECT * FROM `clans__groups` WHERE 1");
			
			$list = "";
			while($row = $sth->fetch(PDO::FETCH_OBJ)) {
				if(isset($gid) && $gid == $row->id) {
					$list .= "<option value=\"" . $row->id . "\" selected>" . $row->name . "</option>";
				}
				else {
					$list .= "<option value=\"" . $row->id . "\">" . $row->name . "</option>";
				}
			}
			
			return $list;
		}
		
		public static function accept($id) {
			pdo()->prepare("UPDATE `clans__joined` SET `status`='1' WHERE `id`=:id LIMIT 1")->execute([
				':id' => $id
			]);
		}
		
		public static function deny($id) {
			pdo()->prepare("DELETE FROM `clans__joined` WHERE `id`=:id LIMIT 1")->execute([
				':id' => $id
			]);
		}
		
		public static function ChangeStatus($cid, $message) {
			$cid = clean($cid, "int");
			
			if(empty($cid)) {
				result(['alert' => 'error', 'message' => 'Введите все параметры']);
			}
			
			$message = clean($message);
			
			if(empty($message)) {
				result(['alert' => 'error', 'message' => 'Введите текст']);
			}
			
			pdo()->prepare("UPDATE `clans` SET `status`=:status WHERE `id`=:cid LIMIT 1")->execute([
				':status' => $message,
				':cid' => $cid
			]);
		}
		
		public static function GetUserClan($uid) {
			$uid = clean($uid, "int");
			
			if(empty($uid)) {
				return null;
			}
			
			$sth = pdo()->query("SELECT * FROM `clans__joined` WHERE `uid`='$uid' and `status`='1' LIMIT 1");
			
			if(!$sth->rowCount()) {
				return null;
			}
			
			return $sth->fetch(PDO::FETCH_OBJ);
		}
		
		public static function GetShopItem($id) {
			$id = clean($id, "int");
			
			if(empty($id)) {
				return null;
			}
		
			$sth = pdo()->query("SELECT * FROM `clans__shop` WHERE `id`='$id' LIMIT 1");
			
			if(!$sth->rowCount()) {
				return null;
			}
			
			return $sth->fetch(PDO::FETCH_OBJ);
		}
		
		public static function GetShopItems() {
			$sth = pdo()->query("SELECT * FROM `clans__shop` WHERE 1");
			
			if(!$sth->rowCount()) {
				return '<center>Нет предметов в Магазине</center>';
			}
			
			$temp = "";
			while($row = $sth->fetch(PDO::FETCH_OBJ)) {
				$temp .= file_get_contents(self::Conf()->Tpl . 'elements/shop/items.tpl');
				
				$temp = str_replace('{image}', self::Conf()->Patch . 'uploads/images/shop/' . $row->image, $temp);
				$temp = str_replace('{name}', $row->name, $temp);
				$temp = str_replace('{price}', $row->price, $temp);
				$temp = str_replace('{id}', $row->id, $temp);
				$temp = str_replace('{currency}', self::configs()->currency, $temp);
			}
			
			return $temp;
		}
		
		public static function SetBalance($cid, $balance) {
			pdo()->prepare("UPDATE `clans` SET `balance`=:balance WHERE `id`=:id LIMIT 1")->execute([
				':balance' => $balance,
				':id' => $cid
			]);
		}
		
		public static function Update($cid, $key, $value) {
			return pdo()->query("UPDATE `clans` SET `$key`='$value' WHERE `id`='$cid' LIMIT 1");
		}
		
		public static function GiveLike($uid, $fid) {
			$userclan = Clans::GetUserClan($fid);
			
			pdo()->prepare("UPDATE `clans` SET `rating`=:rating WHERE `id`=:id LIMIT 1")->execute([
				':rating' => self::Get($userclan->cid)->rating + self::configs()->give_like, ':id' => $userclan->cid
			]);
			
			pdo()->prepare("UPDATE `clans__joined` SET `rating`=:rating WHERE `uid`=:uid LIMIT 1")->execute([
				':rating' => $userclan->rating + self::configs()->give_like, ':uid' => $fid
			]);
			
			pdo()->prepare("INSERT INTO `clans__likes`(`uid`, `fid`, `date`) VALUES (:uid, :fid, :date)")->execute([
				':uid' => $uid, ':fid' => $fid, ':date' => date("Y-m-d H:i:s")
			]);
		}
		
		public static function IsFinderLiked($fid) {
			$sth = pdo()->query("SELECT * FROM `clans__likes` WHERE `uid`='" . $_SESSION['id'] . "' and `fid`='$fid' ORDER BY `id` DESC LIMIT 1");
			
			if(!$sth->rowCount()) {
				return false;
			}
			
			$row = $sth->fetch(PDO::FETCH_OBJ);
			
			if((strtotime($row->date) + strtotime("+1 day", false)) > time()) {
				return true;
			}
			
			return false;
		}
		
		public static function IsLiked($uid) {
			$sth = pdo()->query("SELECT * FROM `clans__likes` WHERE `uid`='$uid' ORDER BY `id` DESC LIMIT 1");
			
			if(!$sth->rowCount()) {
				return false;
			}
			
			$row = $sth->fetch(PDO::FETCH_OBJ);
			
			if((strtotime($row->date) + strtotime("+1 day", false)) > time()) {
				return true;
			}
			
			return false;
		}
	}