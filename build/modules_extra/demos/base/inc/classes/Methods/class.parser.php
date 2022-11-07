<?php

namespace Demos\Methods;

use Demos\CustomDemosTable;

abstract class Parser extends CustomDemosTable implements Method
{
	const UPDATE_FREQUENCY = 60;

	public function renew($server)
	{
		if(!$this->isTimeToRenew($server->last_demo)) {
			return;
		}

		$this->removeDemos($server->id);

		$demos = $this->parse($server->hltv_url);

		if($demos) {
			foreach($demos as $demo) {
				$this->insertDemo(
					$demo['id'],
					$demo['file'],
					$demo['size'],
					$demo['map'],
					$server->id,
					$demo['createdAt']
				);
			}
		}

		$this->updateTimeStamp($server->id);
	}

	private function updateTimeStamp($serverId)
	{
		pdo()->prepare(
			"UPDATE servers__demos SET last_demo=:last_demo WHERE server_id=:server_id LIMIT 1"
		)->execute(['last_demo' => time(), 'server_id' => $serverId]);
	}

	private function isTimeToRenew($lastTimeStamp)
	{
		if(time() - $lastTimeStamp > self::UPDATE_FREQUENCY * 60) {
			return true;
		} else {
			return false;
		}
	}
}