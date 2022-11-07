<?PHP
	if(!is_admin()) {
		show_error_page('not_adm');
	}

	include_once("{$_SERVER['DOCUMENT_ROOT']}/modules_extra/skins_store/base/config.php");
	
	$tpl->load_template('elements/title.tpl');
	$tpl->set("{title}", $page->title);
	$tpl->set("{name}", $conf->name);
	$tpl->compile('title');
	$tpl->clear();

	$tpl->load_template('head.tpl');
	$tpl->set("{title}", $tpl->result['title']);
	$tpl->set("{image}", $page->image);
	$tpl->set("{other}", $module_name_execute['to_head']);
	$tpl->set("{token}", $token);
	$tpl->set("{cache}", $conf->cache);
	$tpl->set("{template}", $conf->template);
	$tpl->set("{site_host}", $site_host);
	$tpl->compile('content');
	$tpl->clear();

	$tpl->load_template('top.tpl');
	$tpl->set("{site_host}", $site_host);
	$tpl->set("{site_name}", $conf->name);
	$tpl->compile('content');
	$tpl->clear();

	$tpl->load_template('menu.tpl');
	$tpl->set("{site_host}", $site_host);
	$tpl->compile('content');
	$tpl->clear();

	$nav = array($PI->to_nav('admin', 0, 0),
				 $PI->to_nav('skins_store', 0, 0),
				 $PI->to_nav('admin_skins_store', 1, 0));
	$nav = $tpl->get_nav($nav, 'elements/nav_li.tpl', 1);

	$tpl->load_template('page_top.tpl');
	$tpl->set("{nav}", $nav);
	$tpl->compile('content');
	$tpl->clear();
	
	$tpl->load_template($module_name_execute['tpl_dir_admin'].'index.tpl');
	$tpl->set("{site_host}", $site_host);
	
	$tpl->set("{skins_list}", $skins->get_admin_skins());
	$tpl->set("{servers}", $skins->get_all_servers());
	
	$tpl->compile('content');
	$tpl->clear();

	$tpl->load_template('bottom.tpl');
	$tpl->set("{site_host}", $site_host);
	$tpl->compile('content');
	$tpl->clear();