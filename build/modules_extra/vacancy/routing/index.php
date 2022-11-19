<?PHP
	require_once($_SERVER['DOCUMENT_ROOT'] . '/modules_extra/vacancy/start.php');
	
	if(isset($_GET['id'])) {
		$vacancy = Vacancy::GetVacancy($_GET['id']);
		
		tpl()
		->load_template("elements/title.tpl")
		->set("{title}", $page->title)
		->set("{name}", $conf->name)
		->compile("title")
		->clear();
		
		tpl()
		->load_template("head.tpl")
		->set("{title}", tpl()->result['title'])
		->set("{site_name}", $conf->name)
		->set("{image}", $page->image)
		->set("{robots}", $page->robots)
		->set("{type}", $page->kind)
		->set("{description}", $page->description)
		->set("{keywords}", $page->keywords)
		->set("{url}", $page->full_url)
		->set("{other}", "<link rel='stylesheet' href='". Vacancy::conf()->styles ."style.css?v={cache}'><script src='" . Vacancy::conf()->patch . "performers/main.js?v={cache}'></script>")
		->set("{token}", $token)
		->set("{cache}", $conf->cache)
		->set("{template}", $conf->template)
		->set("{site_host}", $site_host)
		->compile("content")
		->clear();
		
		$menu = tpl()->get_menu(pdo());

		$nav = [
			$PI->to_nav("main", 0, 0),
			$PI->to_nav("vacancy", 0, 0),
			$PI->to_nav("vacancy_index", 1, 0)
		];
		
		$nav = tpl()->get_nav($nav, "elements/nav_li.tpl");
		$nav = str_replace('{username}', Vacancy::GetValidName($vacancy->uid), $nav);

		include_once(isset($_SESSION['id']) ? "inc/authorized.php" : "inc/not_authorized.php");
		$status = Vacancy::ChangeStatus($vacancy->status);
		
		$user = Vacancy::GetUserData($vacancy->uid);
		$group = get_groups(pdo())[$user->rights];
		
		tpl()
		->load_template(Vacancy::conf()->templates . "view.tpl")
		->set("{site_host}", $site_host)
		->set("{template}", $conf->template)
		->set("{class}", $status['class'])
		->set("{status}", $status['name'])
		->set("{status-id}", $vacancy->status)
		->set("{date}", expand_date($vacancy->date))
		->set("{uid}", $vacancy->uid)
		->set("{vid}", $vacancy->id)
		->set("{author}", Vacancy::GetValidName($vacancy->uid))
		->set("{server_name}", Vacancy::GetServerName($vacancy->sid))
		->set("{vacancy}", Vacancy::GetVacancyName($vacancy->vacancy))
		->set("{messages}", Vacancy::GetMessages($vacancy->id))
		->set("{info}", Vacancy::Parse($vacancy->info))
		->set("{reason}", $vacancy->reason)
		->set("{gp_name}", $group['name'])
		->set("{gp_color}", $group['color'])
		->set("{access}", ($vacancy->uid == $_SESSION['id'] || is_worthy("g")) ? '1' : '0')
		->compile("content")
		->clear();
	}
	else {
		tpl()
		->load_template("elements/title.tpl")
		->set("{title}", $page->title)
		->set("{name}", $conf->name)
		->compile("title")
		->clear();
		
		tpl()
		->load_template("head.tpl")
		->set("{title}", tpl()->result['title'])
		->set("{site_name}", $conf->name)
		->set("{image}", $page->image)
		->set("{robots}", $page->robots)
		->set("{type}", $page->kind)
		->set("{description}", $page->description)
		->set("{keywords}", $page->keywords)
		->set("{url}", $page->full_url)
		->set("{other}", "<link rel='stylesheet' href='". Vacancy::conf()->styles ."style.css?v={cache}'><script src='" . Vacancy::conf()->patch . "performers/main.js?v={cache}'></script>")
		->set("{token}", $token)
		->set("{cache}", $conf->cache)
		->set("{template}", $conf->template)
		->set("{site_host}", $site_host)
		->compile("content")
		->clear();
		
		$menu = tpl()->get_menu(pdo());

		$nav = [
			$PI->to_nav("main", 0, 0),
			$PI->to_nav("vacancy", 1, 0)
		];

		$nav = tpl()->get_nav($nav, "elements/nav_li.tpl");

		include_once(isset($_SESSION['id']) ? "inc/authorized.php" : "inc/not_authorized.php");
		
		tpl()
		->load_template(Vacancy::conf()->templates . "index.tpl")
		->set("{site_host}", $site_host)
		->set("{template}", $conf->template)
		->set("{vacancy}", Vacancy::GetList((isset($_GET['page']) ? $_GET['page'] : 1), (isset($_GET['sid']) ? $_GET['sid'] : NULL)))
		->set("{pagination}", tpl()->get_paginator((empty($_GET['page']) ? 1 : $_GET['page']), Vacancy::rowVacancy(empty($_GET['sid']) ? NULL : $_GET['sid']), Vacancy::conf()->limit, 3, '/vacancy?' . (empty($_GET['sid']) ? NULL : ("sid=" . $_GET['sid'] . "&"))))
		->set("{servers}", Vacancy::GetListServers(isset($_GET['sid']) ? $_GET['sid'] : 0))
		->compile("content")
		->clear();
	}
	
	