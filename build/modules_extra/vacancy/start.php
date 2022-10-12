<?PHP
	require_once($_SERVER['DOCUMENT_ROOT'] . '/inc/start.php');
	
	$Autoloader->addNamespace(
		Autoloader::CORE_NAMESPACE, [
			$_SERVER['DOCUMENT_ROOT'] . '/inc/classes/',
			$_SERVER['DOCUMENT_ROOT'] . '/modules_extra/vacancy/library/'
		]
	);

	$Autoloader->register();