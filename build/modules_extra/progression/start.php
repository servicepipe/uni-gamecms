<?PHP
	require($_SERVER['DOCUMENT_ROOT'] . "/inc/start.php");
	
	define('config', [
		'root' => $_SERVER['DOCUMENT_ROOT'] . '/modules_extra/progression/',
		'templates' => $_SERVER['DOCUMENT_ROOT'] . '/modules_extra/progression/templates/' . configs()->template . '/',
	]);