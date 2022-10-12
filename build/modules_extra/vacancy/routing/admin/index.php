<?PHP
	require_once($_SERVER['DOCUMENT_ROOT'] . '/modules_extra/vacancy/start.php');
	
	if(!is_admin()):
		show_error_page("not_adm");
	endif;
	
	tpl()
	->load_template("elements/title.tpl")
	->set("{title}", $page->title)
	->set("{name}", $conf->name)
	->compile("title")
	->clear();

	tpl()
	->load_template("head.tpl")
	->set("{title}", tpl()->result['title'])
	->set("{image}", $page->image)
	->set("{other}", "<script src='" . Vacancy::conf()->patch . "performers/admin.js?v={cache}'></script>")
	->set("{token}", $token)
	->set("{cache}", $conf->cache)
	->set("{template}", $conf->template)
	->set("{site_host}", $site_host)
	->compile("content")
	->clear();

	tpl()
	->load_template("top.tpl")
	->set("{site_host}", $site_host)
	->set("{site_name}", $conf->name)
	->compile("content")
	->clear();

	tpl()
	->load_template("menu.tpl")
	->set("{site_host}", $site_host)
	->compile("content")
	->clear();

	$nav = [
		$PI->to_nav("admin", 0, 0),
		$PI->to_nav("admin_vacancy", 1, 0)
	];

	$nav = tpl()->get_nav($nav, "elements/nav_li.tpl", 1);

	tpl()
	->load_template("page_top.tpl")
	->set("{nav}", $nav)
	->compile("content")
	->clear();

	tpl()
	->load_template(Vacancy::conf("admin")->templates . "index.tpl")
	->set("{site_host}", $site_host)
	->set("{template}", $conf->template)
	->set("{servers}", Vacancy::GetServers())
	->set("{limit_vacancy}", Vacancy::conf()->limit)
	->set("{next_days}", Vacancy::conf()->next_days)
	->compile("content")
	->clear();

	tpl()
	->load_template("bottom.tpl")
	->set("{site_host}", $site_host)
	->compile("content")
	->clear();