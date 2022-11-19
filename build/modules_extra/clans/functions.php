<?PHP
	function IsFlags($uid, $cid, $flag) {
		$pos = strpos(Clans::GetFlags($cid, $uid), $flag);
		return ($pos !== false);
	}
	
	function generation_name2($name = null) {
		if(empty($name)):
			return rand_string(12);
		endif;

		$pathinfo = pathinfo($name, PATHINFO_EXTENSION);
		return rand_string(12) . ".$pathinfo";
	}
	
	function rand_string($length = 8) {
		return RandString($length);
	}
	
	function RandString($length = 8) {
		return substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, $length);
	}
	
	function file_uploads2($dir = null, $file = null) {
		if(empty($dir) || empty($file)):
			return ['alert' => 'error', 'message' => 'Не указаны параметры'];
		endif;

		if(0 < $file['error']):
			return ['alert' => 'error', 'message' => 'Ошибка файла', 'code' => $file['error']];
		endif;

		$name = generation_name2($file['name']);
		$full_dir = "$dir/$name";

		if(!move_uploaded_file($file['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $full_dir)):
			return false;
		endif;

		return ['alert' => 'success', 'name' => $name, 'full_dir' => $full_dir];
	}