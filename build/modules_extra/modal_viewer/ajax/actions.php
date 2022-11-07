<?php
include_once "../../../inc/start.php";
include_once "../../../inc/protect.php";
include_once "../../../inc/functions.php";

/*ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);*/

if(empty($_POST['phpaction'])) {
	log_error("Прямой вызов actions.php");
	exit(json_encode(array('status' => '2')));
}

if($conf->token == 1 && ($_SESSION['token'] != clean($_POST['token'], null))) {
	log_error("Неверный токен");
	exit(json_encode(array('status' => '2')));
}

if($_POST['load_modal_viewer']) {	
	$STH = $pdo->query("SELECT * FROM `modal_viewer` WHERE `enable`='1'");
	$STH->setFetchMode(PDO::FETCH_OBJ);
			while($row = $STH->fetch()) {
				if(empty($_COOKIE["ModalViewer_{$row->id}"])) {
					if($row->auth == 1){
						echo '<div class="modal fade" id="show_modal_viewer" tabindex="-1" role="dialog" aria-labelledby="show_modal_viewer" aria-hidden="true">			
									<div class="modal-dialog" role="document">
										<div class="modal-content">
											<div class="modal-header">
												<h3 class="modal-title" id="show_modal_viewer">'.$row->title.'</h3>
												<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
											</div>
											<div class="modal-body">
												<font size="4" face="Tahoma">'.$row->text.'</font>
											</div>
										</div>
									</div>
								</div>';				
							setcookie("ModalViewer_{$row->id}", 1, time() + $row->timelife);
									
							break;
					}
					else
					if($row->auth == 2){
						if(is_auth()){
							echo '<div class="modal fade" id="show_modal_viewer" tabindex="-1" role="dialog" aria-labelledby="show_modal_viewer" aria-hidden="true">			
									<div class="modal-dialog" role="document">
										<div class="modal-content">
											<div class="modal-header">
												<h3 class="modal-title" id="show_modal_viewer">'.$row->title.'</h3>
												<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
											</div>
											<div class="modal-body">
												<font size="4" face="Tahoma">'.$row->text.'</font>
											</div>
										</div>
									</div>
								</div>';				
							setcookie("ModalViewer_{$row->id}", 1, time() + $row->timelife);
									
							break;
						}
					}
					else
					if($row->auth == 3){
						if(!is_auth()){
							echo '<div class="modal fade" id="show_modal_viewer" tabindex="-1" role="dialog" aria-labelledby="show_modal_viewer" aria-hidden="true">			
									<div class="modal-dialog" role="document">
										<div class="modal-content">
											<div class="modal-header">
												<h3 class="modal-title" id="show_modal_viewer">'.$row->title.'</h3>
												<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
											</div>
											<div class="modal-body">
												<font size="4" face="Tahoma">'.$row->text.'</font>
											</div>
										</div>
									</div>
								</div>';				
							setcookie("ModalViewer_{$row->id}", 1, time() + $row->timelife);
							
							break;
						}
					}
				}
			}
	return;	
}

if(isset($_POST['edit_modal'])) {
	$pid = clean($_POST['pid'], "int");
	$title = clean($_POST['title']);
	$texts = clean($_POST['texts']);
		
	if(empty($pid) || empty($title) || empty($texts)) {
		result(['alert' => 'warning', 'message' => 'Один из параметров пуст.']);
	}
		
	if(pdo()->prepare("UPDATE `modal_viewer` SET `title`=:title, `text`=:text WHERE `id`=:pid LIMIT 1")->execute([
		':pid' => $pid,
		':title' => $title,
		':text' => $texts
	])) {
		result(['alert' => 'success']);
	}
		
	result(['alert' => 'error']);
}

if(isset($_POST['remove_modal'])) {
	$pdo->query("DELETE FROM `modal_viewer` WHERE `id`='{$_POST['pid']}'");
	
	exit(json_encode(['status' => '1']));
}

if($_POST['add_modal_viewer']) {
	if(empty($_POST['textTitle']) || empty($_POST['textMessage'])) {
		echo "<span class=\"text-danger\">Заполните все поля!</span>";
		return;
	}
	
	if($pdo->query("INSERT INTO `modal_viewer`(`title`, `text`, `timelife`, `auth`, `enable`) VALUES ('{$_POST['textTitle']}', '{$_POST['textMessage']}', {$_POST['valueTimelife']}, {$_POST['valueAuth']}, 1)")) {
		echo "<span class=\"text-success\">Окно добавлено!</span><script>setInterval(function() {location.reload();}, 300);</script>";
		return;
	}
	
	echo "<span class=\"text-danger\">Произошила ошибка..</span>";
}