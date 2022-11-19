<?PHP
	require_once($_SERVER['DOCUMENT_ROOT'] . '/modules_extra/clans/start.php');
	
	$id = clean($_GET['id'], "int");
	
	if(empty($id)) {
		show_error_page("no_page");
	}
		
	if(!Clans::IsValid($id)) {
		show_error_page("no_page");
	}
		
	$clan = Clans::Get($id);
	
	tpl()
	->load_template("elements/title.tpl")
	->set("{title}", $page->title)
	->set("{name}", $clan->name)
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
	->set("{other}", "<link rel='stylesheet' href='". Clans::Conf()->Styles ."style.css?v={cache}'><script src='" . Clans::Conf()->Patch . "performers/main.js?v={cache}'></script>")
	->set("{token}", $token)
	->set("{cache}", $conf->cache)
	->set("{template}", $conf->template)
	->set("{site_host}", $site_host)
	->compile("content")
	->clear();
		
	$menu = tpl()->get_menu(pdo());
		
	$nav = [
		$PI->to_nav("main", 0, 0),
		$PI->to_nav("clans", 1, 0)
	];
		
	$nav = tpl()->get_nav($nav, "elements/nav_li.tpl");
	include_once(isset($_SESSION['id']) ? "inc/authorized.php" : "inc/not_authorized.php");
		
	tpl()
	->load_template(Clans::Conf()->Templates . "more.tpl")
	->set("{site_host}", $site_host)
	->set("{template}", $conf->template)
	->set("{cid}", $clan->id)
	->set("{name}", $clan->name)
	->set("{btn}", Clans::GetButton($clan->id))
	->set("{status}", empty($clan->status) ? "У данного клана нет статуса!" : $clan->status)
	->set("{logotype}", Clans::Conf()->Patch . 'uploads/images/logotype/' . $clan->logotype)
	->set("{cover}", Clans::Conf()->Patch . 'uploads/images/cover/' . $clan->cover)
	->set("{list}", Clans::GetUIPlayers($clan->id))
	->set("{chief-avatar}", convert_avatar($clan->uid, true))
	->set("{chief}", '<a href="/profile?id=' . usr($clan->uid)->id . '">' . usr($clan->uid)->login . '</a>')
	->set("{players}", Clans::GetRowCount($clan->id))
	->set("{max_players}", $clan->max_users)
	->set("{creator}", $clan->uid)
	->set("{flags}", Clans::GetFlags($clan->id, $_SESSION['id']))
	->set("{waits}", Clans::GetRowWaits($clan->id))
	->set("{balance}", $clan->balance)
	->set("{rating}", $clan->rating)
	->compile("content")
	->clear();