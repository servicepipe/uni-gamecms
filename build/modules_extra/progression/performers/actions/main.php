<?PHP
	require($_SERVER['DOCUMENT_ROOT'] . "/modules_extra/progression/start.php");
	
	if(empty($_POST['phpaction']) || $_POST['token'] != $_SESSION['token'] || empty($_SESSION['id'])) {
		result(['alert' => 'error']);
	}
	
	if(isset($_POST['GetProgressive'])) {
		if($_POST['uid'] != $_SESSION['id']) {
			result("<script>$('.progression').remove();</script>");
		}
		
		$_main = file_get_contents(config['templates'] . 'tpl/progression.tpl');
		$_user = usr($_SESSION['id']);
		
		if(empty($_user)) {
			result("<script>$('.progression').remove();</script>");
		}
		
		$progress = [
			[
				'name' => 'Заполнить Имя',
				'placeholder' => 'Как вас зовут? Как нам к вам обращаться?',
				'uri' => '/settings',
				'finished' => ($_user->name && $_user->name != '---')
			],
			[
				'name' => 'Добавить Telegram',
				'placeholder' => 'Добавьте свой Telegram, чтобы мы могли связаться с вами',
				'uri' => '/settings',
				'finished' => (isset($_user->telegram) && $_user->telegram)
			],
			[
				'name' => 'Добавить Discord',
				'placeholder' => 'Добавьте свой Discord, чтобы мы могли связаться с вами',
				'uri' => '/settings',
				'finished' => isset($_user->discord)
			],
			[
				'name' => 'Указать Ник на сервере',
				'placeholder' => 'Укажите свой Ник на сервере, чтобы игроки вас узнавали',
				'uri' => '/settings',
				'finished' => ($_user->nick && $_user->nick != '---')
			],
			[
				'name' => 'Указать Steam ID',
				'placeholder' => 'Укажиет свой Steam ID, чтобы получать подарки с нашего сайта',
				'uri' => '/settings',
				'finished' => ($_user->steam_id && $_user->steam_id != '0')
			],
			[
				'name' => 'Адрес страницы',
				'placeholder' => 'Придумайте адрес для своего профиля',
				'uri' => '/settings',
				'finished' => isset($_user->route)
			]
		];
		
		$_list = "";
		$_finished = 0;
		foreach($progress as $key => $value) {
			$_list .= file_get_contents(config['templates'] . 'tpl/elements/list.tpl');
			$_list = str_replace("{text}", $value['name'], $_list);
			$_list = str_replace("{placeholder}", $value['placeholder'], $_list);
			
			if($value['finished']) {
				$_finished++;
				$_list = str_replace("{uri}", "javascript:void(0);", $_list);
				$_list = str_replace("{finished}", "finished", $_list);
				$_list = str_replace("{icon}", file_get_contents(config['templates'] . 'tpl/elements/icon/check.tpl'), $_list);
			}
			else {
				$_list = str_replace("{uri}", $value['uri'], $_list);
				$_list = str_replace("{finished}", "", $_list);
				$_list = str_replace("{icon}", file_get_contents(config['templates'] . 'tpl/elements/icon/wait.tpl'), $_list);
			}
		}
		
		if($_finished == count($progress)) {
			result("<script>$('.progression').remove();</script>");
		}
		
		$_main = str_replace("{position}", 100 / (count($progress) / $_finished), $_main);
		result(str_replace("{progress}", $_list, $_main));
	}