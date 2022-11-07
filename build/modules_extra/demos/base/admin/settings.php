<?PHP
	if(!is_admin()) {
		show_error_page('not_adm');
	}

	require_once __DIR__ . '/../inc/config.php';

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
	->set("{other}", $module['to_head'])
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
		$PI->to_nav("admin_modules", 0, 0),
		$PI->to_nav("admin_demos", 1, 0)
	];

	$nav = tpl()->get_nav($nav, "elements/nav_li.tpl", 1);

	tpl()
	->load_template("page_top.tpl")
	->set("{nav}", $nav)
	->compile("content")
	->clear();
	
	tpl()
	->load_template($module['tpl_dir_admin'] . 'index.tpl')
	->set("{site_host}", $site_host)
	->set("{template}", $conf->template)
	->compile("content")
	->clear();
	
	tpl()
	->load_template("bottom.tpl")
	->set("{site_host}", $site_host)
	->compile("content")
	->clear();