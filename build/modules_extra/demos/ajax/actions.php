<?php
require_once __DIR__ . '/../../../inc/start.php';
require_once __DIR__ . '/../../../inc/protect.php';
include_once __DIR__ . '/../base/inc/config.php';

$AjaxResponse = new AjaxResponse();

if(!isPostRequest() || !isRightToken()) {
	$AjaxResponse->status(false)->alert('Ошибка')->send();
}


if(isset($_POST['load_demos'])) {
	$start = checkStart($_POST['start']);
	$server_id = checkJs($_POST['server'], "int");
	$map = checkJs($_POST['map']);

	if(empty($server_id) || ((empty($start) and $start != 0))) {
		exit();
	}

	$limit = getLimit('bans_lim');

	$STH = pdo()->prepare(
			"SELECT 
					    servers.id,
   					 	servers.ip,
					    servers.port,
					    servers.name,
					    servers__demos.* 
					FROM 
					    servers 
					        INNER JOIN servers__demos ON servers.id = servers__demos.server_id 
					WHERE servers.id = :server_id LIMIT 1"
	);
	$STH->execute([':server_id' => $server_id]);
	$server = $STH->fetch(PDO::FETCH_OBJ);

	if(empty($server->id)) {
		exit();
	}

	try {
		$DemosService = new Demos\DemosService($server);
		$demos = $DemosService->getDemos($start, $limit, $map);
	} catch(Exception $exception) {
		exit('<div class="empty-element">' . $exception->getMessage() . '</div>');
	}

	global $module;

	$tpl = new Template;
	$tpl->dir = $module['tpl_dir'];

	foreach($demos as $demo) {
		$tpl->load_template('line.tpl');

		if(file_exists('../../../files/maps_imgs/' . $demo->map . '.jpg')) {
			$mapImage = '/files/maps_imgs/' . $demo->map . '.jpg';
		} else {
			$mapImage = '/files/maps_imgs/none.jpg';
		}

		$tpl->set("{created_at}", expand_date($demo->created_at));
		$tpl->set("{map}", $demo->map);
		$tpl->set("{map_image}", $mapImage);
		$tpl->set("{link}", $demo->link);
		$tpl->set("{size}", calculate_size($demo->size));
		$tpl->compile('content');
		$tpl->clear();
	}

	if(empty($tpl->result['content'])) {
		exit('<div class="empty-element">Записей нет</div>');
	} else {
		$tpl->show($tpl->result['content']);
		$tpl->global_clear();
	}
	exit();
}

if(isset($_POST['load_servers']) && is_admin()) {
	$i = 0;

	global $full_site_host;

	$STH = pdo()->query(
			"SELECT 
						    servers.id AS server_id,
						    servers.name,
						    servers.ip,
						    servers.port,
						    servers__demos.server_id AS settings_id,
						    servers__demos.work_method,
						    servers__demos.hltv_url,
						    servers__demos.swu_key,
						    servers__demos.ftp_host,
						    servers__demos.ftp_login,
						    servers__demos.ftp_pass,
						    servers__demos.ftp_port,
						    servers__demos.ftp_string,
						    servers__demos.db_host,
						    servers__demos.db_user,
						    servers__demos.db_pass,
						    servers__demos.db_db,
						    servers__demos.db_code,
						    servers__demos.db_table,
						    servers__demos.url,
						    servers__demos.shelf_life 
						FROM 
						    servers 
						        LEFT JOIN servers__demos ON servers.id = servers__demos.server_id 
						ORDER BY servers.trim");
	$STH->setFetchMode(PDO::FETCH_OBJ);
	while($row = $STH->fetch()) {
		?>
		<div class="col-md-6">
			<div id="server_<?php echo $row->server_id ?>" class="block">
				<div class="block_head">
					<?php echo $row->name ?> (<?php echo $row->ip ?>:<?php echo $row->port ?>)
				</div>

				<b>Способ интеграции</b>
				<select id="work_method<?php echo $row->server_id ?>" class="form-control mb-5" onchange="selectWorkMethod(<?php echo $row->server_id ?>);">
					<option value="1" <?php if($row->work_method == 1) {echo "selected";} ?>>Через плагин Auto recorder</option>
					<option value="2" <?php if($row->work_method == 2) {echo "selected";} ?>>Через плагин [AutoDemo] Simple Web Uploader</option>
					<option value="3" <?php if($row->work_method == 3) {echo "selected";} ?>>Парсинг Myarena HLTV</option>
					<option value="4" <?php if($row->work_method == 4) {echo "selected";} ?>>Парсинг Csserv HLTV</option>
				</select>
				<br>

				<div id="work-methods-<?php echo $row->server_id ?>">
					<div data-work-method-1>
						<div class="bs-callout bs-callout-info mb-10">
							<p>
								<a target="_blank" href="https://gamecms.ru/wiki/demos">
									<span class="glyphicon glyphicon-link"></span> Скачать плагин
								</a>
							</p>
						</div>

						<b>Введите ссылку до папки с демо записями</b>
						<input value="<?php echo $row->url ?>" type="text" class="form-control mb-5" id="url<?php echo $row->server_id ?>" maxlength="512" autocomplete="off" placeholder="Пример: http://site.ru/demos/">
						<br>
						<b>Введите данные от базы с демо записями</b>
						<input value="<?php echo $row->db_host ?>" type="text" class="form-control mb-5" id="db_host<?php echo $row->server_id ?>" maxlength="64" autocomplete="off" placeholder="Хост базы данных">
						<input value="<?php echo $row->db_db ?>" type="text" class="form-control mb-5" id="db_db<?php echo $row->server_id ?>" maxlength="32" autocomplete="off" placeholder="Название базы данных">
						<input value="<?php echo $row->db_user ?>" type="text" class="form-control mb-5" id="db_user<?php echo $row->server_id ?>" maxlength="32" autocomplete="off" placeholder="Имя пользователя">
						<input value="<?php echo $row->db_pass ?>" type="password" class="form-control mb-5" id="db_pass<?php echo $row->server_id ?>" maxlength="32" autocomplete="off" placeholder="Пароль пользователя">
						<input value="<?php echo $row->db_table ?>" type="text" class="form-control mb-5" id="db_table<?php echo $row->server_id ?>" maxlength="32" autocomplete="off" placeholder="Название таблицы: auto_recorder">
						<select class="form-control" id="db_code<?php echo $row->server_id ?>">
							<option value="0" <?php if($row->db_code == 0) {echo "selected";} ?> >
								Кодировка: стандартная
							</option>
							<option value="1" <?php if($row->db_code == 1) {echo "selected";} ?> >
								Кодировка: utf-8
							</option>
							<option value="2" <?php if($row->db_code == 2) {echo "selected";} ?> >
								Кодировка: latin1
							</option>
						</select>
						<br>
						<b>Введите данные от FTP-сервера, где сохраняются демо записи</b>
						<input value="<?php echo $row->ftp_host ?>" type="text" class="form-control mb-5" id="ftp_host<?php echo $row->server_id ?>" maxlength="64" autocomplete="off" placeholder="Хост">
						<input value="<?php echo $row->ftp_login ?>" type="text" class="form-control mb-5" id="ftp_login<?php echo $row->server_id ?>" maxlength="32" autocomplete="off" placeholder="Имя пользователя">
						<input value="<?php echo $row->ftp_pass ?>" type="password" class="form-control mb-5" id="ftp_pass<?php echo $row->server_id ?>" maxlength="32" autocomplete="off" placeholder="Пароль пользователя">
						<input value="<?php echo $row->ftp_port ?>" type="number" class="form-control mb-5" id="ftp_port<?php echo $row->server_id ?>" maxlength="5" autocomplete="off" placeholder="Порт: 21">
						<input value="<?php echo $row->ftp_string ?>" type="text" class="form-control mb-5" id="ftp_string<?php echo $row->server_id ?>" maxlength="255" autocomplete="off" placeholder="Путь до каталога с демо записями">
						<br>
					</div>

					<div class="bs-callout bs-callout-info mb-10" data-work-method-2>
						<p>
							<a target="_blank" href="https://gamecms.ru/wiki/demos">
								<span class="glyphicon glyphicon-link"></span> Скачать плагин
							</a>
						</p>
					</div>

					<div data-work-method-1 data-work-method-2>
						<b>Сколько дней хранить демо записи</b>
						<input
								value="<?php echo $row->shelf_life ?>"
								type="number"
								class="form-control mb-10"
								id="shelf_life<?php echo $row->server_id ?>"
								maxlength="3"
								autocomplete="off"
								placeholder="Количество дней"
						>
					</div>

					<div data-work-method-2>
						<b>URL для API</b>
						<input
								value="<?php echo $full_site_host ?>modules_extra/demos/api/index.php"
								type="text"
								disabled
								class="form-control mb-10"
						>
						<b>Ключ API</b>
						<input
								value="<?php echo empty($row->swu_key) ? md5(crate_pass(10, 1)) : $row->swu_key; ?>"
								type="text"
								class="form-control mb-10"
								id="swu_key<?php echo $row->server_id ?>"
								maxlength="256"
								autocomplete="off"
								placeholder="Введите случайную строку (буквы и цифры)"
						>
					</div>

					<div data-work-method-3 data-work-method-4>
						<b>URL на HLTV</b>
						<input
								value="<?php echo $row->hltv_url ?>"
								type="text"
								class="form-control"
								id="hltv_url<?php echo $row->server_id ?>"
								maxlength="512"
								autocomplete="off"
								placeholder="Введите ссылку на HLTV"
						>
					</div>

					<div class="bs-callout bs-callout-info mb-5 mt-10" data-work-method-3>
						<h5>Пример URL</h5>
						<p>https://www.myarena.ru/hltvdemos.php?home=0000&frame=1</p>
					</div>

					<div class="bs-callout bs-callout-info mb-5 mt-10" data-work-method-4>
						<h5>Пример URL</h5>
						<p>https://hltv.csserv.ru/000.000.000.000_00000/hltv/demos_list.php</p>
					</div>
				</div>

				<div id="edit_serv_result<?php echo $row->server_id ?>" class="mt-10"></div>
				<button onclick="demos_edit_server(<?php echo $row->server_id ?>, 0);" type="button" class="btn2">
					Сохранить
				</button>
				<button type="button" class="btn2 btn-cancel" onclick="demos_edit_server(<?php echo $row->server_id ?>, 1);">Очистить</button>
			</div>

			<script>
				selectWorkMethod(<?php echo $row->server_id ?>);
			</script>
		</div>
		<?php
		if($i % 2 == 1) {
			echo "<div class='clearfix'></div>";
		}
		$i++;
	}

	if($i == 0) {
		exit ('Серверов нет');
	}
}
if(isset($_POST['edit_server']) && is_admin()) {
	if($_POST['work_method'] == 1) {
		$data = ['server_id'  => ['content' => checkJs($_POST['server_id'], "int"),  'length'  => 3],
		         'work_method'=> ['content' => checkJs($_POST['work_method'], "int"),'length'  => 1],
		         'url'        => ['content' => check($_POST['url'], null),                  'length'  => 512],
		         'shelf_life' => ['content' => check($_POST['shelf_life'], "int"),          'length'  => 3],
		         'db_host'    => ['content' => check($_POST['db_host'], null),              'length'  => 64],
		         'db_user'    => ['content' => check($_POST['db_user'], null),              'length'  => 32],
		         'db_pass'    => ['content' => check($_POST['db_pass'], null),              'length'  => 32],
		         'db_db'      => ['content' => check($_POST['db_db'], null),                'length'  => 32],
		         'db_table'   => ['content' => check($_POST['db_table'], null),             'length'  => 32],
		         'db_code'    => ['content' => check($_POST['db_code'], "int"),             'length'  => 1],
		         'ftp_host'   => ['content' => check($_POST['ftp_host'], null),             'length'  => 64],
		         'ftp_login'  => ['content' => check($_POST['ftp_login'], null),            'length'  => 32],
		         'ftp_pass'   => ['content' => check($_POST['ftp_pass'], null),             'length'  => 32],
		         'ftp_port'   => ['content' => check($_POST['ftp_port'], "int"),            'length'  => 5],
		         'ftp_string' => ['content' => check($_POST['ftp_string'], null),           'length'  => 255]
		];
	}

	if($_POST['work_method'] == 2) {
		$data = ['server_id'  => ['content' => checkJs($_POST['server_id'], "int"),  'length'  => 3],
		         'work_method'=> ['content' => checkJs($_POST['work_method'], "int"),'length'  => 1],
		         'shelf_life' => ['content' => check($_POST['shelf_life'], "int"),          'length'  => 3],
		         'swu_key'    => ['content' => check($_POST['swu_key'], null),              'length'  => 256]
		];
	}

	if($_POST['work_method'] == 3 || $_POST['work_method'] == 4) {
		$data = ['server_id'  => ['content' => checkJs($_POST['server_id'], "int"),  'length'  => 3],
		         'work_method'=> ['content' => checkJs($_POST['work_method'], "int"),'length'  => 1],
		         'hltv_url'   => ['content' => check($_POST['hltv_url'], null),             'length'  => 512],
		];
	}

	if(empty($data['server_id']['content'])) {
		exit (json_encode(['status' => '2']));
	}

	if($_POST['clean'] == '1') {
		$STH = pdo()->prepare("DELETE FROM servers__demos WHERE server_id=:server_id LIMIT 1");
		$STH->execute([':server_id' => $data['server_id']['content']]);

		exit(json_encode(['status' => '1']));
	}

	foreach($data as $key => $value) {
		if(empty($value['content']) && $key != 'db_code') {
			exit (
				json_encode(
					['status' => '2', 'input' => $key, 'data' => 'Заполните!']
				)
			);
		}
		if(mb_strlen($value['content'], 'UTF-8') > $value['length']) {
			exit (
				json_encode(
					[
						'status' => '2',
						'input'  => $key,
						'data'   => 'Не более ' . $value['length'] . ' символов!'
					]
				)
			);
		}
	}

	$db_data = [];

	foreach($data as $key => $value) {
		$db_data[$key] = $value['content'];
	}

	if($_POST['work_method'] == 1) {
		$SM = new ServersManager;

		if(!$pdo2 = db_connect($data['db_host']['content'], $data['db_db']['content'], $data['db_user']['content'], $data['db_pass']['content'])) {
			exit (
				json_encode(
					[
						'status' => '2',
						'alert'  => 'Не удалось подключиться к базе данных'
					]
				)
			);
		}
		set_names($pdo2, $data['db_code']['content']);

		if(!check_table($data['db_table']['content'], $pdo2)) {
			exit (
				json_encode(
					[
						'status' => '2',
						'alert'  => 'Указанная таблица не обнаружена в базе данных'
					]
				)
			);
		}
		if(!check_column($data['db_table']['content'], $pdo2, 'demo_name')) {
			exit (
				json_encode(
					['status' => '2', 'alert' => 'Неверная структура таблицы']
				)
			);
		}
		if(!check_column($data['db_table']['content'], $pdo2, 'size')) {
			$pdo2->exec("ALTER TABLE ".$data['db_table']['content']."  ADD size INT( 6 ) NOT NULL DEFAULT '0' AFTER address");
		}

		$STH = $pdo2->query("SELECT demo_name FROM ".$data['db_table']['content']." ORDER BY id DESC LIMIT 1");
		$STH->setFetchMode(PDO::FETCH_OBJ);
		$row = $STH->fetch();
		if(empty($row->demo_name)) {
			exit(json_encode(['status' => '2', 'alert' => 'Таблица пуста']));
		}

		if(substr($data['ftp_string']['content'], -1) != '/') {
			$data['ftp_string']['content'] .= '/';
		}
		if(substr($data['ftp_string']['content'], 0, 1) == '/') {
			$data['ftp_string']['content'] = substr($data['ftp_string']['content'], 1);
		}

		if(!$ftp_connection = $SM->ftp_connection($data['ftp_host']['content'], $data['ftp_port']['content'], $data['ftp_login']['content'], $data['ftp_pass']['content'], 'DEMOS_MODULE')) {
			exit(
				json_encode(
					[
						'status' => '2',
						'alert'  => 'Не удалось подключиться к FTP серверу'
					]
				)
			);
		}
		if(!$SM->find_users_file($ftp_connection, $data['ftp_string']['content'].$row->demo_name.'.dem')) {
			exit(
				json_encode(
					[
						'status' => '2',
						'alert'  => 'Не удалось обнаружить файлы демо записей на FTP сервере по заданному пути: ' . $data['ftp_string']['content'] . $row->demo_name . '.dem'
					]
				)
			);
		}
		$SM->close_ftp($ftp_connection);

		if(substr($data['url']['content'], -1) != '/') {
			$data['url']['content'] .= '/';
		}
		if(!@fopen($data['url']['content'].'/'.$row->demo_name.'.dem', "r")) {
			exit(
				json_encode(
					[
						'status' => '2',
						'alert'  => 'Не удалось обнаружить файлы демо записей по указанной ссылке'
					]
				)
			);
		}

		$STH = pdo()->prepare("SELECT server_id FROM servers__demos WHERE server_id=:server_id LIMIT 1");
		$STH->setFetchMode(PDO::FETCH_OBJ);
		$STH->execute([':server_id' => $data['server_id']['content']]);
		$row = $STH->fetch();
		if(isset($row->server_id)) {
			$STH = pdo()->prepare("UPDATE servers__demos SET work_method = :work_method, ftp_host = :ftp_host, ftp_login = :ftp_login, ftp_pass = :ftp_pass, ftp_port = :ftp_port, ftp_string = :ftp_string, db_host = :db_host, db_user = :db_user, db_pass = :db_pass, db_db = :db_db, db_table = :db_table, db_code = :db_code, url = :url, shelf_life = :shelf_life WHERE server_id=:server_id LIMIT 1");
			$STH->execute($db_data);
		} else {
			$STH = pdo()->prepare("INSERT INTO servers__demos (server_id, work_method, ftp_host, ftp_login, ftp_pass, ftp_port, ftp_string, db_host, db_user, db_pass, db_db, db_table, db_code, url, shelf_life) values (:server_id, :work_method, :ftp_host, :ftp_login, :ftp_pass, :ftp_port, :ftp_string, :db_host, :db_user, :db_pass, :db_db, :db_table, :db_code, :url, :shelf_life)");
			$STH->execute($db_data);
		}
    }

	if($_POST['work_method'] == 2) {
		$STH = pdo()->prepare("SELECT server_id FROM servers__demos WHERE server_id=:server_id LIMIT 1");
		$STH->setFetchMode(PDO::FETCH_OBJ);
		$STH->execute([':server_id' => $data['server_id']['content']]);
		$row = $STH->fetch();
		if(isset($row->server_id)) {
			$STH = pdo()->prepare("UPDATE servers__demos SET work_method = :work_method, swu_key = :swu_key, shelf_life = :shelf_life WHERE server_id=:server_id LIMIT 1");
			$STH->execute($db_data);
		} else {
			$STH = pdo()->prepare("INSERT INTO servers__demos (server_id, work_method, shelf_life, swu_key) values (:server_id, :work_method, :shelf_life, :swu_key)");
			$STH->execute($db_data);
		}
	}

	if($_POST['work_method'] == 3 || $_POST['work_method'] == 4) {
		$STH = pdo()->prepare("SELECT server_id FROM servers__demos WHERE server_id=:server_id LIMIT 1");
		$STH->setFetchMode(PDO::FETCH_OBJ);
		$STH->execute([':server_id' => $data['server_id']['content']]);
		$row = $STH->fetch();
		if(isset($row->server_id)) {
			$STH = pdo()->prepare("UPDATE servers__demos SET work_method = :work_method, hltv_url = :hltv_url WHERE server_id=:server_id LIMIT 1");
			$STH->execute($db_data);
		} else {
			$STH = pdo()->prepare("INSERT INTO servers__demos (server_id, work_method, hltv_url) values (:server_id, :work_method, :hltv_url)");
			$STH->execute($db_data);
		}
	}

	exit(json_encode(['status' => '1']));
}