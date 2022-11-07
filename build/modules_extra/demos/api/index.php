<?php

require_once __DIR__ . '/../../../inc/start.php';
include_once __DIR__ . '/../base/inc/config.php';

try {
	$action = getPageParam('action', null);
	$key = getPageParam('key', null);

	if(!$action) {
		show_error_page('not_settings');
	}

	if($action == 'renew') {
		Demos\DemosService::renewAll();
	}

	$result = (new Demos\Methods\AutoDemo\Api($key))->execute($action);

	exit(json_encode(array_merge($result, ['status' => 'success'])));
} catch(Exception $exception) {
	log_error('DEMOS: ' . $exception->getMessage());

	http_response_code(500);

	exit(
		json_encode(
			['status' => 'error', 'message' => $exception->getMessage()]
		)
	);
}