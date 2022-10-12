<?PHP
	class VacancyAdmin extends Vacancy {
		public static function GetFields($sid) {
			$buf = file_get_contents(self::conf('admin')->tpl . 'elements/fields.tpl');
			$sth = pdo()->query("SELECT * FROM `vacancy__names` WHERE `sid`='$sid' ORDER BY `id` DESC");
			
			if(!$sth->rowCount()) {
				return str_replace("{fields-list}", "<tr><td colspan='4'><center>Нет бланков</center></td></tr>", $buf);
			}
			
			$bufi = "";
			while($row = $sth->fetch(PDO::FETCH_OBJ)) {
				$bufi .= "<tr><td>" . $row->title . "</td><td>" . $row->name . "</td><td>" . $row->placeholder . "</td><td><button data-field='" . $row->id . "' class='btn btn-default w-100'><i class='glyphicon glyphicon-trash'></i></button></td></tr>";
			}
			
			return str_replace("{fields-list}", $bufi, $buf);
		}
		
		public static function GetVacancy($sid) {
			$buf = file_get_contents(self::conf('admin')->tpl . 'elements/vacancy.tpl');
			$sth = pdo()->query("SELECT * FROM `vacancy__list` WHERE `sid`='$sid' ORDER BY `id` DESC");
			
			if(!$sth->rowCount()) {
				return str_replace("{vacancy-list}", "<tr><td colspan='2'><center>Нет вакансий</center></td></tr>", $buf);
			}
			
			$bufi = "";
			while($row = $sth->fetch(PDO::FETCH_OBJ)) {
				$bufi .= "<tr><td>" . $row->name . "</td><td><button data-vacancy='" . $row->id . "' class='btn btn-default w-100'><i class='glyphicon glyphicon-trash'></i></button></td></tr>";
			}
			
			return str_replace("{vacancy-list}", $bufi, $buf);
		}
		
		public static function addVacancy($sid, $name) {
			return pdo()->prepare("INSERT INTO `vacancy__list`(`sid`, `name`) VALUES (:sid, :name)")->execute([
				':sid' => $sid,
				':name' => $name
			]);
		}
		
		public static function addField($sid, $title, $name, $placeholder) {
			return pdo()->prepare("INSERT INTO `vacancy__names`(`sid`, `title`, `name`, `placeholder`) VALUES (:sid, :title, :name, :placeholder)")->execute([
				':sid' => $sid,
				':title' => $title,
				':name' => $name,
				':placeholder' => $placeholder
			]);
		}
	}