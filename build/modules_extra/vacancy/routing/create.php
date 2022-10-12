<?PHP
	require_once($_SERVER['DOCUMENT_ROOT'] . '/modules_extra/vacancy/start.php');
	
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
		$PI->to_nav("vacancy_create", 1, 0)
	];

	$nav = tpl()->get_nav($nav, "elements/nav_li.tpl");

	include_once(isset($_SESSION['id']) ? "inc/authorized.php" : "inc/not_authorized.php");
		
	tpl()
	->load_template(Vacancy::conf()->templates . "create.tpl")
	->set("{site_host}", $site_host)
	->set("{template}", $conf->template)
	->set("{servers}", Vacancy::GetServers())
	->set("{vacancy}", "<option selected disabled>Выберите вакансию</option>")
	->compile("content")
	->clear();