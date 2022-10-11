<?PHP
	class MoneyTransfer {
		public static function GetUserData($uid) {
			$uid = clean($uid, "int");
			$sth = pdo()->query("SELECT * FROM `users` WHERE `id`='$uid' LIMIT 1");
			
			if($sth->rowCount()) {
				return $sth->fetch(PDO::FETCH_OBJ);
			}
			
			return null;
		}
		
		public static function SetUserMoney($uid, $count) {
			return pdo()->prepare("UPDATE `users` SET `shilings`=:count WHERE `id`=:uid LIMIT 1")->execute([
				':uid' => $uid,
				':count' => $count
			]);
		}
		
		public static function GetButton($uid) {
			$uid = clean($uid, "int");
			$dir = $_SERVER['DOCUMENT_ROOT'] . '/modules_extra/money_transfer/templates/' . configs()->template . '/tpl/';
			
			if(file_exists($dir . 'button.tpl')) {
				$temp = file_get_contents($dir . 'button.tpl');
				return str_replace('{uid}', $uid, $temp);
			}
			
			return '';
		}
	}