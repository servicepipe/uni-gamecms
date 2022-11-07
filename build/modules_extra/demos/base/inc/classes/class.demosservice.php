<?php

namespace Demos;

use PDO;
use Exception;

class DemosService
{
	const METHOD_AUTO_RECORDER = 1;
	const METHOD_AUTO_DEMO     = 2;
	const METHOD_MYARENA_HLTV  = 3;
	const METHOD_CSSERV_HLTV   = 4;

	private $server;
	private $methodService;

	public function __construct($server)
	{
		$this->server = $server;
		$this->methodService = self::getMethodClass($server->work_method);
	}

	public function getCount()
	{
		return $this->methodService->getCount($this->server);
	}

	public static function renewAll()
	{
		$STH = pdo()->query(
			"SELECT 
						    servers__demos.*,
						    servers.id,
    						servers.ip,
						    servers.port 
						FROM 
						    servers 
						        INNER JOIN servers__demos ON servers.id = servers__demos.server_id"
		);
		while($server = $STH->fetch(PDO::FETCH_OBJ)) {
			self::getMethodClass($server->work_method)->renew($server);
		}
	}

	public function getDemos($start, $limit, $map)
	{
		return $this->methodService->getDemos(
			$this->server,
			$start,
			$limit,
			$map
		);
	}

	private static function getMethodClass($methodNumber)
	{
		switch($methodNumber) {
			case self::METHOD_AUTO_RECORDER:
				return new Methods\AutoRecorder();
			case self::METHOD_AUTO_DEMO:
				return new Methods\AutoDemo();
			case self::METHOD_MYARENA_HLTV:
				return new Methods\MyarenaHLTV();
			case self::METHOD_CSSERV_HLTV:
				return new Methods\CsservHLTV();
			default:
				throw new Exception('Unexpected value');
		}
	}
}