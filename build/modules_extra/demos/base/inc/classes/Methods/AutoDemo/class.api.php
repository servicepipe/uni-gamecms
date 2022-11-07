<?php

namespace Demos\Methods\AutoDemo;

use PDO;
use Exception;
use Demos\Methods\AutoDemo;

class Api
{
	private $server;

	public function __construct($key)
	{
		$this->server = $this->getServerByKey($key);

		if(!$this->server) {
			throw new Exception('Server not found');
		}
	}

	public function execute($action)
	{
		$methodName = 'action' . lcfirst($action);

		if(!method_exists($this, $methodName)) {
			throw new Exception('Action not found');
		}

		return $this->$methodName();
	}

	private function actionConfig()
	{
		$maxUploadSize = min(
			ini_get('post_max_size'),
			ini_get('upload_max_filesize')
		);

		$maxUploadSizeInBytes = $this->sizeToBytes($maxUploadSize);

		$intMaxC = 100 * 1024 * 1024;

		if($maxUploadSizeInBytes > $intMaxC) {
			$maxUploadSizeInBytes = $intMaxC;
		}

		return ['chunkSize' => $maxUploadSizeInBytes];
	}

	private function actionUpload()
	{
		$demoId = getPageParam('demo_id', null);
		$source = $this->getSource();

		if(empty($demoId)) {
			throw new Exception('Empty demo id');
		}

		if(empty($source)) {
			throw new Exception('Empty source');
		}

		(new AutoDemo())->saveChunk($this->server, $demoId, $source);

		http_response_code(201);

		return ['message' => 'Completed'];
	}

	private function actionFinish()
	{
		$source = json_decode($this->getSource(), true);

		if(empty($source)) {
			throw new Exception('Empty source');
		}

		(new AutoDemo())->saveDemo($this->server, $source);

		http_response_code(201);

		return ['message' => 'Completed'];
	}

	private function getServerByKey($key)
	{
		$STH = pdo()->prepare(
			"SELECT *, server_id as id FROM servers__demos WHERE swu_key=:swu_key LIMIT 1"
		);
		$STH->execute([':swu_key' => $key]);
		return $STH->fetch(PDO::FETCH_OBJ);
	}

	private function getSource()
	{
		return file_get_contents('php://input');
	}

	private function sizeToBytes($size)
	{
		$bytes = intval($size);
		switch (strtolower(substr($size, -1)))
		{
			case 'g':
				$bytes *= 1024;

			case 'm':
				$bytes *= 1024;

			case 'k':
				$bytes *= 1024;
		}

		return $bytes;
	}
}