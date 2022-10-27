<?php
include_once "../../../inc/start.php";
include_once "../../../inc/protect.php";

if(empty($_POST['phpaction'])) {
	log_error("Прямой вызов donation_widget actions.php");
	exit(json_encode(['status' => '2']));
}

if($conf->token == 1 && ($_SESSION['token'] != clean($_POST['token'], null))) {
	log_error("Неверный токен");
	exit(json_encode(['status' => '2', 'info' => 'Неверный токен']));
}

if(isset($_POST['load_donations'])) {
	// load widget configuration
	$STH = $pdo->query("SELECT * FROM dw__config LIMIT 1");
	$STH->setFetchMode(PDO::FETCH_OBJ);
	$dwconf = $STH->fetch();

	if($dwconf->enabled == 2 || empty($dwconf->raising)) {
		exit(); // Raising not started, exit silently...
	}

	// load current raising info
	$STH = $pdo->prepare("SELECT * FROM dw__raisings WHERE id=:id LIMIT 1");
	$STH->setFetchMode(PDO::FETCH_OBJ);
	$STH->execute([':id' => $dwconf->raising]);
	$dwraise = $STH->fetch();

	// get donations amount in current raising
	$STH = $pdo->prepare(
		"SELECT 
						SUM(shilings) AS sum
					FROM 
					    dw__donations 
	                LEFT JOIN 
	                    money__actions 
	                ON 
	                    dw__donations.pid = money__actions.id 
					WHERE 
	                    dw__donations.fid = :fid"
	);
	$STH->execute([':fid' => $dwconf->raising]);
	$row        = $STH->fetch(PDO::FETCH_COLUMN);
	$donations  = (!empty($row)) ? $row : 0;
	$percent    = ($dwraise->target != 0) ? floor($donations / $dwraise->target * 100) : 0;
	$barpercent = ($percent > 100) ? 100 : $percent;
	$message    = (!empty($dwraise->message)) ? $dwraise->message : "На поддержку проекта";

	if(
		($dwconf->autostop == 1 && $percent >= 100)
		|| (
			$dwconf->autostop == 2
			&& strtotime(date("Y-m-d H:i:s")) > strtotime($dwraise->stopdate)
		)
	) {
		$completed = 1;
	} else {
		$completed = 0;
	}

	$tpl      = new Template;
	$tpl->dir = '../../../modules_extra/donation_widget/templates/' . $conf->template . '/tpl/';
	$tpl->result['donations'] = '';
	if($dwconf->showlist == 1) {
		$STH = $pdo->prepare(
			"SELECT 
						    d.comment, 
						    m.author, 
						    m.shilings,
    						users.login, 
						    users.avatar,
						    users.rights
						FROM 
   					 		dw__donations d 
        				LEFT JOIN 
        					money__actions m 
            			ON 
                			d.pid = m.id 
						INNER JOIN
   					 		users
						ON
							m.author = users.id
						WHERE 
    						d.fid = :fid
						ORDER BY d.id DESC" . (($dwconf->listlimit > 0) ? " LIMIT $dwconf->listlimit;" : ";")
		);
		$STH->setFetchMode(PDO::FETCH_OBJ);
		$STH->execute([':fid' => $dwconf->raising]);

		while($row = $STH->fetch()) {
			$tpl->load_template('donate_item.tpl');
			$tpl->set("{id}", $row->author);
			$tpl->set("{color}", $users_groups[$row->rights]['color']);
			$tpl->set("{name}", $users_groups[$row->rights]['name']);
			$tpl->set("{login}", $row->login);
			$tpl->set("{avatar}", $row->avatar);
			$tpl->set("{amount}", $row->shilings);
			$tpl->set("{comments}", $dwconf->comments);
			if($dwconf->comments == 1) {
				$tpl->set("{comment}", (!empty($row->comment)) ? $row->comment : 'Комментарий отсутствует');
			}
			$tpl->compile('donations');
			$tpl->clear();
		}

		if(empty($tpl->result['donations'])) {
			$tpl->result['donations'] = '<div class="donate_user_item">Нет донатов </div>';
		}
	}

	$tpl->load_template('widget.tpl');
	$tpl->set("{target_desc}", $message);
	$tpl->set("{target_amount}", $dwraise->target);
	$tpl->set("{autostop}", $dwconf->autostop);
	$tpl->set("{stopdate}", $dwraise->stopdate);
	$tpl->set("{curr_amount}", $donations);
	$tpl->set("{curr_percent}", $percent);
	$tpl->set("{curr_barpercent}", $barpercent);
	$tpl->set("{completed}", $completed);
	$tpl->set("{comments}", $dwconf->comments);
	$tpl->set("{showlist}", $dwconf->showlist);
	$tpl->set("{limit}", $dwconf->listlimit);
	$tpl->set("{donations}", $tpl->result['donations']);
	$tpl->compile('content');
	$tpl->clear();

	$tpl->show($tpl->result['content']);
	$tpl->global_clear();
	exit();
}

if(empty($_SESSION['id'])) {
	exit(json_encode(['status' => '2', 'info' => 'Ошибка: Доступно только авторизованным!']));
}

if(isset($_POST['donate'])) {
	$amount  = checkJs($_POST['amount'], "int");
	$comment = checkJs($_POST['comment'], null);

	$STH = $pdo->query(
		"SELECT 
					    c.*, 
					    r.target, 
					    r.stopdate 
					FROM 
					    dw__config c 
					LEFT JOIN 
					    dw__raisings r 
					ON 
					    c.raising = r.id 
					LIMIT 1"
	);
	$STH->setFetchMode(PDO::FETCH_OBJ);
	$dwconf = $STH->fetch();

	if($dwconf->enabled == 2 || empty($dwconf->raising)) {
		exit(json_encode(['status' => '2', 'info' => 'Сбор не начат!']));
	}

	$STH = $pdo->prepare(
		"SELECT 
						SUM(`shilings`) AS `sum` 
					FROM 
					    dw__donations 
					LEFT JOIN 
					    money__actions 
					ON 
						dw__donations.pid = money__actions.id 
					WHERE 
					    `dw__donations`.`fid` = :fid"
	);
	$STH->execute([':fid' => $dwconf->raising]);
	$dwconf->sum = $STH->fetch(PDO::FETCH_COLUMN);
	$percent = ($dwconf->target != 0) ? floor($dwconf->sum / $dwconf->target * 100) : 0;

	if(
		($dwconf->autostop == 1 && $percent >= 100)
		|| (
			$dwconf->autostop == 2
			&& strtotime(date("Y-m-d H:i:s")) > strtotime($dwconf->stopdate)
		)
	) {
		exit(json_encode(['status' => '2', 'info' => 'Сбор уже завершен!']));
	}

	if(empty($amount)) {
		exit(json_encode(['status' => '2', 'info' => 'Укажите сумму!']));
	}

	if (mb_strlen($comment, 'UTF-8') > 60) {
		exit(json_encode(['status' => '2', 'info' => 'Комментарий должен состоять не мболее чем из 60 символов']));
	}

	$STH = $pdo->prepare("SELECT id, shilings FROM users WHERE id=:id LIMIT 1");
	$STH->setFetchMode(PDO::FETCH_OBJ);
	$STH->execute([':id' => $_SESSION['id']]);
	$row = $STH->fetch();
	if(empty($row->id)) {
		exit(json_encode(['status' => '2', 'info' => 'Не удалось найти Вас в БД!']));
	}
	$shilings = $row->shilings;

	if($shilings < $amount) {
		exit (json_encode(['status' => '2', 'info' => 'У Вас недостаточно средств!']));
	}
	$shilings = $shilings - $amount;

	$date = date("Y-m-d H:i:s");
	$STH  = $pdo->prepare(
		"INSERT INTO money__actions (date, shilings, author, type) VALUES (:date, :shilings, :author, :type); " .
		"SET @lastID := LAST_INSERT_ID(); " .
		"INSERT INTO dw__donations (pid, fid, comment) VALUES (@lastID, :fid, :comment);"
	);
	$STH->execute(
		[
			'date'     => $date,
			'shilings' => $amount,
			'author'   => $_SESSION['id'],
			'type'     => '20',
			'fid'      => $dwconf->raising,
			'comment'  => $comment
		]
	);

	$STH = $pdo->prepare("UPDATE users SET shilings=:shilings WHERE id=:id LIMIT 1");
	$STH->execute([':shilings' => $shilings, ':id' => $_SESSION['id']]);

	$mess = "Благодарим вас за пожертвование в размере <b>" . $amount . "</b> рублей!";
	$STH  = $pdo->prepare("INSERT INTO notifications (message, date, user_id, type) VALUES (:message, :date, :user_id, :type)");
	$STH->execute(['message' => $mess, 'date' => $date, 'user_id' => $_SESSION['id'], 'type' => '2']);

	$mess2 = "Пользователь <a href='../profile?id=" . $_SESSION['id'] . "'>" . $_SESSION['login']
		. "</a> пожертвовал проекту <b>" . $amount . "</b> рублей.";
	$STH   = $pdo->prepare("INSERT INTO notifications (message, date, user_id, type) VALUES (:message, :date, :user_id, :type)");
	$STH->execute(['message' => $mess2, 'date' => $date, 'user_id' => '1', 'type' => '2']);

	exit(json_encode(['status' => '3', 'info' => $mess, 'shilings' => $shilings]));
}