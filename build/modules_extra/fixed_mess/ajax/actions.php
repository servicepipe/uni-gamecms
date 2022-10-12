<?php
include_once "../../../inc/start.php";
include_once "../../../inc/protect.php";

if (empty($_POST['phpaction'])) {
	log_error("Прямой вызов actions.php");
	exit(json_encode(array('status' => '2')));
}
if($conf->token == 1 && ($_SESSION['token'] != clean($_POST['token'],null))) {
	log_error("Неверный токен");
	exit(json_encode(array('status' => '2')));
}

if(isset($_POST['chat_load_fixed_message'])) {
	$fixed_mess = $conf->fixed_message;
    if(!$fixed_mess) exit();
	else {
        $users_groups = get_groups($pdo);
        
        $tpl = new Template;
        $tpl->dir = '../../../modules_extra/fixed_mess/templates/'.$conf->template.'/';
		$tpl->result['chat'] = '';
        
        $STH = $pdo->query("SELECT chat.*, users.login, users.avatar, users.rights FROM chat LEFT JOIN users ON chat.user_id = users.id WHERE chat.id = ".$fixed_mess);
        $STH->execute();
        if($row = $STH->fetch()) {
            $date = expand_date($row['message_date'],8);
            $tpl->load_template('fixed_message.tpl');
            $tpl->set("{id}", $row['id']);
            $tpl->set("{user_id}", $row['user_id']);
            $tpl->set("{login}", $row['login']);
            $tpl->set("{avatar}", $full_site_host.$row['avatar']);
            $tpl->set("{date_full}", $date['full']);
            $tpl->set("{date_short}", $date['short']);
            $tpl->set("{text}", $row['message_text']);
            $tpl->set("{gp_name}", $users_groups[$row['rights']]['name']);
            $tpl->set("{gp_color}", $users_groups[$row['rights']]['color']);
            $tpl->set(
                "{gp_rights}",
                $users_groups[
                    (array_key_exists('rights', $_SESSION))
                    ? $_SESSION['rights']
                    : 0
                ]['rights']
            );
            $tpl->compile( 'chat' );
            $tpl->clear();
            
            $tpl->show($tpl->result['chat']);
            $tpl->global_clear();
            exit();
        }
	}
    exit();
}

if (isset($_POST['fixed_chat_message'])) {
	$id = check($_POST['id'],"int");
	$key = check($_POST['key'],"int");

	if (!is_worthy("w")){
		exit(json_encode(array('status' => '3')));
	}
    
	if($key == 1){
		$STH = $pdo->prepare("UPDATE `config` SET `fixed_message`=:message LIMIT 1");
		$STH->execute(array(':message' => $id));
		exit(json_encode(array('status' => '1')));
	}
	if($key == 2){
		$STH = $pdo->prepare("UPDATE `config` SET `fixed_message`=:message LIMIT 1");
		$STH->execute(array(':message' => 0));
		exit(json_encode(array('status' => '2')));
	}
	
	exit(json_encode(array('status' => '3')));
}
?>