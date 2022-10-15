<?php
require_once __DIR__ . '/../../../inc/start.php';

$AjaxResponse = new AjaxResponse();

if(!isPostRequest() || !isRightToken()) {
	$AjaxResponse->status(false)->alert('Ошибка')->send();
}

if(isset($_POST['site_stats'])){
	$type = check($_POST['type'], "int");

	$STH = pdo()->prepare("SELECT `id`, `data` FROM `config__strings` WHERE `id`=:id LIMIT 1"); $STH->setFetchMode(PDO::FETCH_OBJ);
	$STH->execute(array( ':id' => 4 ));
	$row = $STH->fetch();

	if(isset($row->id)) {
		$data = unserialize($row->data);
	}

	if(empty($row->id) || $data['date']!= date("Y-m-d")) {
		//пользователи
		$STH = pdo()->query("SELECT COUNT(*) FROM `users`");
		$users = $STH->fetchColumn();

		//новости
		$STH = pdo()->query("SELECT COUNT(*) FROM `news`");
		$news = $STH->fetchColumn();

		//комментарии к новостям
		$STH = pdo()->query("SELECT COUNT(*) FROM `news__comments`");
		$news__comments = $STH->fetchColumn();

		//тем на форуме
		$STH = pdo()->query("SELECT COUNT(*) FROM `forums__topics`");
		$forums__topics = $STH->fetchColumn();

		//сообщений на форуме
		$STH = pdo()->query("SELECT COUNT(*) FROM `forums__messages`");
		$forums__messages = $STH->fetchColumn();

		//сообщений в лс
		$STH = pdo()->query("SELECT COUNT(*) FROM `pm__messages`");
		$pm__messages = $STH->fetchColumn();

		//заявок на разбан
		$STH = pdo()->query("SELECT COUNT(*) FROM `bans`");
		$bans_apps = $STH->fetchColumn();

		//сообщений в чате
		$STH = pdo()->query("SELECT COUNT(*) FROM `chat`");
		$chat = $STH->fetchColumn();

		//админов
		$STH = pdo()->query("SELECT COUNT(*) FROM `admins`");
		$admins = $STH->fetchColumn();

		//серверов
		$STH = pdo()->query("SELECT COUNT(*) FROM `servers`");
		$servers = $STH->fetchColumn();

		//банов в бан листах
		$bans = 0;
		$STH = pdo()->query("SELECT `id`,`ip`,`port`,`db_host`,`db_user`,`db_pass`,`db_db`,`db_prefix`,`type` FROM `servers` WHERE `type`!=0 and `type`!=1"); $STH->setFetchMode(PDO::FETCH_OBJ);
		while($banlist = $STH->fetch()) {
			if($pdo2 = db_connect($banlist->db_host, $banlist->db_db, $banlist->db_user, $banlist->db_pass)) {
				if ($banlist->type == '2' || $banlist->type == '3' || $banlist->type == '5') {
					$address = $banlist->ip.':'.$banlist->port;
					$table = set_prefix($banlist->db_prefix, 'bans');
					$bans += get_rows_count($pdo2, $table, "`server_ip` = '$address'");
				} else {
					$table = set_prefix($banlist->db_prefix, 'servers');
					$STH2 = $pdo2->query("SELECT sid FROM $table WHERE ip='$banlist->ip' and port='$banlist->port' LIMIT 1"); $STH2->setFetchMode(PDO::FETCH_OBJ);  
					$sid = $STH2->fetch();

					$table = set_prefix($banlist->db_prefix, 'bans');
					$bans += get_rows_count($pdo2, $table, "`sid` = '$sid->sid'");
				}
			}
		}

		//игроков в статистике
		$stats = 0;
		$STH = pdo()->query("SELECT `id`,`st_db_host`,`st_db_user`,`st_db_pass`,`st_db_db`,`st_type`,`st_db_table`,`ip`,`port` FROM `servers` WHERE `st_type`!=0"); $STH->setFetchMode(PDO::FETCH_OBJ);
		while($statslist = $STH->fetch()) {
			if($pdo2 = db_connect($statslist->st_db_host, $statslist->st_db_db, $statslist->st_db_user, $statslist->st_db_pass)) {
				if($statslist->st_type == '1' or $statslist->st_type == '2') {
					$STH2 = $pdo2->query("SELECT COUNT(*) as count FROM csstats_players WHERE frags!=0");
				} elseif($statslist->st_type == '3') {
					$STH2 = $pdo2->query("SELECT COUNT(*) as count FROM $statslist->st_db_table WHERE kills!=0");
				} elseif($statslist->st_type == '4') {
					$STH2 = $pdo2->prepare("SELECT `game` FROM `hlstats_Servers` WHERE `address`=:address AND `port`=:port LIMIT 1"); $STH2->setFetchMode(PDO::FETCH_OBJ);
					$STH2->execute(array( ':address' => $statslist->ip, ':port' => $statslist->port ));
					$game = $STH2->fetch();

					if(empty($game->game)) {
						$game = 'csgo';
					} else {
						$game = $game->game;
					}

					$STH2 = $pdo2->query("SELECT COUNT(*) as count FROM hlstats_Players WHERE kills!=0 AND game='$game'");
				} elseif($statslist->st_type == '5') {
					$STH2 = $pdo2->query("SELECT COUNT(*) as count FROM $statslist->st_db_table WHERE kills!=0");
				} elseif($statslist->st_type == '6') {
					$STH2 = $pdo2->query("SELECT COUNT(*) as count FROM $statslist->st_db_table");
				}

				$STH2->setFetchMode(PDO::FETCH_ASSOC);
				$count = $STH2->fetch();
				$stats += $count['count'];
			}
		}

		if(empty($row->id)) {
			$data = array(
				'date' => date("Y-m-d"),
				'users' => array($users, $users),
				'news' => array($news, $news),
				'news__comments' => array($news__comments, $news__comments),
				'forums__topics' => array($forums__topics, $forums__topics),
				'forums__messages' => array($forums__messages, $forums__messages),
				'pm__messages' => array($pm__messages, $pm__messages),
				'chat' => array($chat, $chat),
				'bans_apps' => array($bans_apps, $bans_apps),
				'admins' => array($admins, $admins),
				'servers' => array($servers, $servers),
				'servers_bans' => array($bans, $bans),
				'servers_stats' => array($stats, $stats)
			);

			$STH = pdo()->prepare("INSERT INTO `config__strings` (`id`,`data`) values (:id, :data)");  
			$STH->execute(array( ':id' => 4, ':data' => serialize($data) ));
		} else {
			$data = array(
				'date' => date("Y-m-d"),
				'users' => array($users, $data['users'][0]),
				'news' => array($news, $data['news'][0]),
				'news__comments' => array($news__comments, $data['news__comments'][0]),
				'forums__topics' => array($forums__topics, $data['forums__topics'][0]),
				'forums__messages' => array($forums__messages, $data['forums__messages'][0]),
				'pm__messages' => array($pm__messages, $data['pm__messages'][0]),
				'chat' => array($chat, $data['chat'][0]),
				'bans_apps' => array($bans_apps, $data['bans_apps'][0]),
				'admins' => array($admins, $data['admins'][0]),
				'servers' => array($servers, $data['servers'][0]),
				'servers_bans' => array($bans, $data['servers_bans'][0]),
				'servers_stats' => array($stats, $data['servers_stats'][0])
			);

			$STH = pdo()->prepare("UPDATE `config__strings` SET `data`=:data WHERE `id`=:id LIMIT 1");
			$STH->execute(array( ':data' => serialize($data), ':id' => 4 ));
		}
	} else {
		$data = unserialize($row->data);
	}

	$tpl = new Template;
	$tpl->dir = '../../../modules_extra/site_stats/templates/'.configs()->template.'/tpl/';
	if($type == 1) {
		$tpl->load_template('stats.tpl');
	} else {
		$tpl->load_template('stats_col.tpl');
	}
	$tpl->result['content'] = '';
	$tpl->set("{users}", $data['users'][0]);
	$tpl->set("{users_diff}", $data['users'][0] - $data['users'][1]);
	$tpl->set("{news}", $data['news'][0]);
	$tpl->set("{news_diff}", $data['news'][0] - $data['news'][1]);
	$tpl->set("{news__comments}", $data['news__comments'][0]);
	$tpl->set("{news__comments_diff}", $data['news__comments'][0] - $data['news__comments'][1]);
	$tpl->set("{forums__topics}", $data['forums__topics'][0]);
	$tpl->set("{forums__topics_diff}", $data['forums__topics'][0] - $data['forums__topics'][1]);
	$tpl->set("{forums__messages}", $data['forums__messages'][0]);
	$tpl->set("{forums__messages_diff}", $data['forums__messages'][0] - $data['forums__messages'][1]);
	$tpl->set("{pm__messages}", $data['pm__messages'][0]);
	$tpl->set("{pm__messages_diff}", $data['pm__messages'][0] - $data['pm__messages'][1]);
	$tpl->set("{chat}", $data['chat'][0]);
	$tpl->set("{chat_diff}", $data['chat'][0] - $data['chat'][1]);
	$tpl->set("{bans_apps}", $data['bans_apps'][0]);
	$tpl->set("{bans_apps_diff}", $data['bans_apps'][0] - $data['bans_apps'][1]);
	$tpl->set("{admins}", $data['admins'][0]);
	$tpl->set("{admins_diff}", $data['admins'][0] - $data['admins'][1]);
	$tpl->set("{servers}", $data['servers'][0]);
	$tpl->set("{servers_diff}", $data['servers'][0] - $data['servers'][1]);
	$tpl->set("{servers_bans}", $data['servers_bans'][0]);
	$tpl->set("{servers_bans_diff}", $data['servers_bans'][0] - $data['servers_bans'][1]);
	$tpl->set("{servers_stats}", $data['servers_stats'][0]);
	$tpl->set("{servers_stats_diff}", $data['servers_stats'][0] - $data['servers_stats'][1]);
	$tpl->compile( 'content' );
	$tpl->show($tpl->result['content']);
	$tpl->global_clear();
}