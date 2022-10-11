<?
include_once "../../../inc/start.php";
include_once "../../../inc/protect.php";
if (empty($_POST['phpaction'])) {
	log_error("Прямой вызов инклуда | actions.php"); 
	exit(json_encode(array('status' => '2')));
}


if(isset($_POST['get_servers'])){
	if(isset($_POST['type'])) {
		$type = check($_POST['type'], "int");
	} else {
		$type = 0;
	}
	
	update_monitoring($pdo);
	$i=0;

	$global_now = 0;
	$global_max = 0;

	$tpl = new Template;
	$tpl->dir = '../../../templates/'.$conf->template.'/tpl/';
	if($type == 1) {
		$STH = $pdo->query("SELECT `monitoring`.*, `servers`.`rcon` FROM `monitoring` LEFT JOIN `servers` ON `monitoring`.`sid`=`servers`.`id` ORDER BY `monitoring`.`id`"); $STH->setFetchMode(PDO::FETCH_OBJ);
	} else {
		$STH = $pdo->query("SELECT * FROM monitoring ORDER BY id"); $STH->setFetchMode(PDO::FETCH_OBJ);
	}

	$tpl->result['content'] = '';
	while($row = $STH->fetch()) {
		if($row->players_now > $row->players_max) {
			$row->players_now = $row->players_max;
		}
		if ($row->players_max != 0) {
			$percentage = $row->players_now/$row->players_max*100;
		} else {
			$percentage = 0;
		}
		if ($percentage<=25){
			$color = 'info';
		} elseif ($percentage<=50){
			$color = 'success';
		} elseif ($percentage<=75){
			$color = 'warning';
		} elseif ($percentage<=100){
			$color = 'danger';
		}
		if (($row->map != '0') and file_exists('../../../files/maps_imgs/'.$row->map.'.jpg')){
			$map = '/files/maps_imgs/'.$row->map.'.jpg';
		} else {
			$map = '/files/maps_imgs/none.jpg';
		}
		if ($row->map == '0') {
			$row->map = "Не определено";
		}
		if ($row->name == '0') {
			$row->name = "Не определено";
		}

		if ($row->type > 1){
			$disp1 = 'disp-b';
			$disp2 = 'disp-n';
		} else {
			$disp1 = 'disp-n';
			$disp2 = 'disp-b';
		}
		$i++;
		if($type == 1) {
			$tpl->load_template('elements/server.tpl');
			$tpl->set("{rcon}", $row->rcon);
		} else {
			$tpl->load_template('elements/server_not_auth.tpl');
		}

		$global_now += $row->players_now;
		$global_max += $row->players_max;

		$tpl->set("{name}", $row->name);
		$tpl->set("{map_img}", $map);
		$tpl->set("{map_name}", $row->map);
		$tpl->set("{percentage}", $percentage);
		$tpl->set("{color}", $color);
		$tpl->set("{max}", $row->players_max);
		$tpl->set("{now}", $row->players_now);
		$tpl->set("{address}", $row->address);
		$tpl->set("{ip}", $row->ip);
		$tpl->set("{port}", $row->port);
		$tpl->set("{id}", $row->sid); 
		$tpl->set("{disp1}", $disp1);
		$tpl->set("{disp2}", $disp2);
		$tpl->set("{site_host}", $site_host);
		$tpl->set("{template}", $conf->template);
		$tpl->set("{game}", $row->game);
		$tpl->set("{i}", $i);
		$tpl->compile( 'content' );
		$tpl->clear();
	}

	if($i == 0){
		exit('<tr><td colspan="10">Серверов нет</td></tr>');
	} else {
		$tpl->show($tpl->result['content']);
		$tpl->global_clear();

		$tpl->result['content'] = '';
		if(isset($global_now) && $global_now != 0) {
			$percentage = $global_now/$global_max*100;
		} else {
			$percentage = 0;
		}
		if($percentage<=25){
			$color = 'info';
		} elseif($percentage<=50){
			$color = 'success';
		} elseif($percentage<=75){
			$color = 'warning';
		} elseif($percentage<=100){
			$color = 'danger';
		}

		if($type == 1) {
			$tpl->load_template('../../../modules_extra/online_line/templates/'.$conf->template.'/tpl/line.tpl');
		} else {
			$tpl->load_template('../../../modules_extra/online_line/templates/'.$conf->template.'/tpl/line_not_auth.tpl');
		}
		$tpl->set("{color}", $color);
		$tpl->set("{percentage}", $percentage);
		$tpl->set("{global_now}", $global_now);
		$tpl->set("{global_max}", $global_max);
		$tpl->compile( 'content' );
		$tpl->show($tpl->result['content']);
		$tpl->global_clear();
	}
}
exit();
?>