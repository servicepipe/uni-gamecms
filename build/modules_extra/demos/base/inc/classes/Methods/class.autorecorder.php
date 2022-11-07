<?php

namespace Demos\Methods;

use PDO;
use Exception;

class AutoRecorder implements Method
{
	public function renew($server)
	{
		//Подключаемся к базе
		if(
			!$pdo2 = db_connect(
				$server->db_host,
				$server->db_db,
				$server->db_user,
				$server->db_pass
			)
		) {
			throw new Exception(
				"Не удалось подключиться к базе данных: $server->db_host | $server->db_db | $server->db_user"
			);
		}
		set_names($pdo2, $server->db_code);

		//Определяем последний id демки, по данному серверу
		$STH = $pdo2->prepare(
			"SELECT id FROM $server->db_table WHERE address=:address ORDER BY id DESC LIMIT 1"
		);
		$STH->execute([':address' => $server->ip . ":" . $server->port]);
		$row = $STH->fetch(PDO::FETCH_OBJ);
		if(empty($row->id)) {
			$last_demo_id = 0;
		} else {
			$last_demo_id = $row->id;
		}

		//Если есть демки, которые еще не проверяли заходим в тело условия
		if(($server->last_demo == 0 || $server->last_demo != $last_demo_id) && $last_demo_id !== 0) {
			//Если демки id демок почему то снизился, то обнуляем локальный счетчик
			if($server->last_demo > $last_demo_id) {
				$server->last_demo = 0;
			}

			$SM = new \ServersManager;
			//Подключаемся к фтп
			if(
				!$ftp_connection = $SM->ftp_connection(
					$server->ftp_host,
					$server->ftp_port,
					$server->ftp_login,
					$server->ftp_pass,
					'DEMOS_MODULE'
				)
			) {
				throw new Exception(
					"Не удалось подключиться к FTP: $server->ftp_host | $server->ftp_login"
				);
			}
			//Переходим в директорию
			if(!ftp_chdir($ftp_connection, $server->ftp_string)) {
				throw new Exception(
					"Не найдена директория: $server->ftp_string на FTP сервере: $server->ftp_host | $server->ftp_login"
				);
			}

			//Получаем список файлов демо записей на фтп
			$demos_list = ftp_nlist($ftp_connection, '.');

			//Выбираем все записи о демках, которые просрочились
			$STH = $pdo2->prepare(
				"SELECT 
						    id, 
						    demo_name 
						FROM 
						    $server->db_table 
						WHERE ((:now_time - record_end_time) > :time) AND address=:address"
			);
			$STH->execute(
				[
					':now_time' => time(),
					':time'     => $server->shelf_life * 24 * 60 * 60,
					':address'  => $server->ip . ":" . $server->port
				]
			);
			while($row = $STH->fetch(PDO::FETCH_OBJ)) {
				//Удаляем демку с фтп
				$file = $row->demo_name . '.dem';
				if(in_array($file, $demos_list)) {
					if(!ftp_delete($ftp_connection, $file)) {
						throw new Exception(
							"Не удалось удалить демо запись: $server->ftp_string/$file на FTP сервере: $server->ftp_host | $server->ftp_login"
						);
					}
				}
			}
			//Удаляем демки с бд
			$STH = $pdo2->prepare(
				"DELETE FROM $server->db_table WHERE ((:now_time - record_end_time) > :time) AND address=:address"
			);
			$STH->execute(
				[
					':now_time' => time(),
					':time'     => $server->shelf_life * 24 * 60 * 60,
					':address'  => $server->ip . ":" . $server->port
				]
			);

			//Обновляем список демо записей на фтп
			$demos_list = ftp_nlist($ftp_connection, '.');

			//Выбираем все демки, которые мы еще не обрабатывали
			$STH = $pdo2->prepare(
				"SELECT 
						    id, 
						    demo_name 
						FROM 
						    $server->db_table 
						WHERE id>:id AND address=:address"
			);
			$STH->execute(
				[
					':id' => $server->last_demo,
					':address' => $server->ip . ":" . $server->port
				]
			);
			while($row = $STH->fetch(PDO::FETCH_OBJ)) {
				$file = $row->demo_name . '.dem';

				//Если файл демо записи существует, то узнаем и записываем ее размер, иначе удаляем запись о ней из базы
				if(in_array($file, $demos_list)) {
					$size = ftp_size($ftp_connection, $file);
					if($size == -1) {
						$size = 0;
					}

					$STH2 = $pdo2->prepare(
						"UPDATE $server->db_table SET size=:size WHERE id=:id LIMIT 1"
					);
					$STH2->execute([':size' => $size, ':id' => $row->id]);
				} else {
					$STH2 = $pdo2->prepare(
						"DELETE FROM $server->db_table WHERE id=:id LIMIT 1"
					);
					$STH2->execute([':id' => $row->id]);
				}
			}

			//Закрываем фтп
			$SM->close_ftp($ftp_connection);

			//Записываем id последней обработанной демки
			$STH = pdo()->prepare(
				"UPDATE servers__demos SET last_demo=:last_demo WHERE server_id=:server_id LIMIT 1"
			);
			$STH->execute(
				[
					':last_demo' => $last_demo_id,
					':server_id' => $server->server_id
				]
			);
		}
	}

	public function getCount($server)
	{
		global $messages;

		if(
			!$pdo2 = db_connect(
				$server->db_host,
				$server->db_db,
				$server->db_user,
				$server->db_pass
			)
		) {
			throw new Exception($messages['errorConnectingToDatabase']);
		} else {
			$STH = $pdo2->prepare("SELECT COUNT(*) as count FROM $server->db_table WHERE address=:address LIMIT 1");
			$STH->execute([':address' => $server->ip . ':' . $server->port]);

			return $STH->fetchColumn();
		}
	}

	public function getDemos($server, $start, $limit, $map = null)
	{
		global $messages;

		if(
			!$pdo2 = db_connect(
				$server->db_host,
				$server->db_db,
				$server->db_user,
				$server->db_pass
			)
		) {
			throw new Exception($messages['errorConnectingToDatabase']);
		}
		set_names($pdo2, $server->db_code);

		if(empty($map)) {
			$STH = $pdo2->prepare(
				"SELECT 
						    id, 
						    demo_name as name, 
						    record_start_time as created_at, 
						    map, 
						    size,
    						'' as link
						FROM 
						    $server->db_table 
						WHERE 
						    address=:address ORDER BY id DESC LIMIT $start, $limit"
			);
			$STH->execute([':address' => $server->ip . ':' . $server->port]);
		} else {
			$STH = $pdo2->prepare(
				"SELECT 
					    	id, 
						    demo_name as name, 
						    record_start_time as created_at, 
						    map, 
						    size,
    						'' as link
						FROM 
						    $server->db_table 
						WHERE 
						    address=:address AND map LIKE :map ORDER BY id DESC LIMIT $start, $limit"
			);
			$STH->execute(
				[
					':address' => $server->ip . ':' . $server->port,
					':map'     => getNameLike($map)
				]
			);
		}

		$demos = $STH->fetchAll(PDO::FETCH_OBJ);

		foreach($demos as $key => $demo) {
			$demos[$key]->link = $server->url . $demo->name . '.dem';
		}

		return $demos;
	}
}