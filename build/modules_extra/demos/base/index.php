<?php

require_once __DIR__ . '/inc/config.php';

global $tpl;
global $messages;
global $PI;
global $module;

if(page()->privacy == 1 && !is_auth()) {
	show_error_page('not_auth');
}

try {
	Demos\DemosService::renewAll();
} catch(Exception $exception) {
	log_error('DEMOS: ' . $exception->getMessage());
}

$paginator = [];

$server_id          = getPageParam('server');
$paginator['page']  = getPageParam('page');
$paginator['limit'] = getLimit('bans_lim');
$paginator['start'] = getPageStartPosition($paginator['page'], $paginator['limit']);

if($server_id) {
	$STH = pdo()->prepare(
		"SELECT 
    				servers.id,
  					servers.ip,
				    servers.port,
				    servers.name,
				    servers__demos.* 
				FROM 
				    servers 
				        INNER JOIN servers__demos ON servers.id = servers__demos.server_id 
				WHERE servers.id = :server_id LIMIT 1"
	);
	$STH->execute([':server_id' => $server_id]);
} else {
	$STH = pdo()->query(
		"SELECT 
    					servers.id,
    					servers.ip,
					    servers.port,
					    servers.name,
					    servers__demos.* 
					FROM 
					    servers 
					        INNER JOIN servers__demos ON servers.id = servers__demos.server_id 
					ORDER BY servers.trim LIMIT 1"
	);
}
$server = $STH->fetch(PDO::FETCH_OBJ);
if(empty($server->id)) {
	$error                  = 'empty';
	$paginator['count']     = 0;
	$paginator['page_name'] = '';
} else {
	$error                  = '';
	$server_id              = $server->id;
	$paginator['page_name'] = '../demos?server=' . $server->id . '&';

	try {
		$DemosService = new Demos\DemosService($server);

		$paginator['count'] = $DemosService->getCount();
	} catch(Exception $exception) {
		$paginator['count'] = 0;
		$error = $exception->getMessage();
	}

	resetIfPaginationIncorrect(
		$paginator['page'],
		$paginator['limit'],
		$paginator['count'],
		'../demos'
	);
}

$tpl->load_template('elements/title.tpl');
$tpl->set("{title}", page()->title);
$tpl->set("{name}", configs()->name);
$tpl->compile('title');
$tpl->clear();

$tpl->load_template('head.tpl');
$tpl->set("{title}", $tpl->result['title']);
$tpl->set("{site_name}", configs()->name);
$tpl->set("{image}", page()->image);
$tpl->set("{robots}", page()->robots);
$tpl->set("{type}", page()->kind);
$tpl->set("{description}", page()->description);
$tpl->set("{keywords}", page()->keywords);
$tpl->set("{url}", page()->full_url);
$tpl->set("{other}", $module['to_head']);
$tpl->set("{token}", token());
$tpl->set("{cache}", configs()->cache);
$tpl->set("{template}", configs()->template);
$tpl->set("{site_host}", $site_host);
$tpl->compile('content');
$tpl->clear();

$menu = $tpl->get_menu(pdo());

$nav = [
	$PI->to_nav('main', 0, 0),
	$PI->to_nav('demos', 1, 0)
];
$nav = $tpl->get_nav($nav, 'elements/nav_li.tpl');

if(is_auth()) {
	include_once __DIR__ . '/../../../inc/authorized.php';
} else {
	include_once __DIR__ . '/../../../inc/not_authorized.php';
}



$tpl->result['categories'] = '';
$STH = pdo()->query(
	"SELECT 
				    servers.id,
				    servers.ip,
				    servers.port,
				    servers.name 
				FROM 
				    servers 
				        INNER JOIN servers__demos ON servers.id = servers__demos.server_id 
				ORDER BY servers.trim"
);
while($row = $STH->fetch(PDO::FETCH_OBJ)) {
	if(empty($server_id)) {
		$server_id = $row->id;
	}

	tpl()->compileCategory(
		$row->name,
		'../demos?server=' . $row->id,
		$row->id == $server_id,
		'demos'
	);
}

$tpl->load_template($module['tpl_dir'] . 'index.tpl');
$tpl->set("{template}", configs()->template);
$tpl->set("{page}", $paginator['page']);
$tpl->set("{start}", $paginator['start']);
$tpl->set("{server}", $server_id);
$tpl->set("{error}", $error);
$tpl->set("{servers}", $tpl->result['categories']);
$tpl->set("{pagination}", $tpl->get_paginator($paginator['page'], $paginator['count'], $paginator['limit'], 3, $paginator['page_name']));
$tpl->compile('content');
$tpl->clear();