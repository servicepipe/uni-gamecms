<?PHP
	require_once($_SERVER['DOCUMENT_ROOT'] . '/inc/start.php');
	require_once(__DIR__ . '/functions.php');
	
	$Autoloader->addNamespace(
		Autoloader::CORE_NAMESPACE, [
			$_SERVER['DOCUMENT_ROOT'] . '/inc/classes/',
			$_SERVER['DOCUMENT_ROOT'] . '/modules_extra/clans/library/'
		]
	);

	$Autoloader->register();