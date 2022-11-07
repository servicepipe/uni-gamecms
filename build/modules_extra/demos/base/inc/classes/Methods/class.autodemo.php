<?php

namespace Demos\Methods;

use Demos\CustomDemosTable;
use PclZip;
use PDO;
use Exception;

class AutoDemo extends CustomDemosTable implements Method
{
	private $demoName = null;

	const DEMOS_DIR = 'files/demos/';

	protected function getDemoLink($demo) {
		global $full_site_host;

		return $full_site_host . $this->getDemosDir($demo->server_id) . $demo->link;
	}

	public function renew($server)
	{
		$whereToRemove = [
			':server_id' => $server->id,
			':created_at' => time() - $server->shelf_life * 24 * 60 * 60
		];

		$STH = pdo()->prepare("SELECT * FROM demos WHERE server_id=:server_id AND created_at < :created_at");
		$STH->execute($whereToRemove);
		$demos = $STH->fetchAll(PDO::FETCH_OBJ);

		foreach($demos as $demo) {
			$demoFile = $this->getDemosPath($server->id) . $demo->file;
			if(file_exists($demoFile)) {
				unlink($demoFile);
			}
		}

		pdo()->prepare(
			"DELETE FROM demos WHERE server_id=:server_id AND created_at < :created_at"
		)->execute($whereToRemove);
	}

	public function saveChunk($server, $demoId, $source)
	{
		$demosPath = $this->getDemosPath($server->id);
		$this->createDirectoryIfNotExists($demosPath);

		$demoFile = $this->getDemoNameById($demoId);

		$fopen = fopen($demosPath . $demoFile, 'ab');
		fwrite($fopen, $source);
		fclose($fopen);
	}

	public function saveDemo($server, $source)
	{
		$map = $this->getMapName($source['play_map']);
		$this->setDemoName(
			$server->id,
			$map,
			date('Y-m-d_H-i-s', $source['start_time'])
		);

		$demosPath = $this->getDemosPath($server->id);
		$demoFile = $this->compressDemoFile($source['unique_id'], $demosPath);

		$size = filesize($demosPath . $demoFile);

		$this->insertDemo(
			$source['unique_id'],
			$demoFile,
			$size,
			$map,
			$server->id,
			$source['start_time']
		);
	}

	public function compressDemoFile($demoId, $demosPath)
	{
		$demoName = $this->getDemoName();

		rename(
			$demosPath . $this->getDemoNameById($demoId),
			$demosPath . $demoName
		);

		$compressedDemoName = str_replace('.dem', '.zip', $demoName);

		$archive = new PclZip($demosPath . $compressedDemoName);
		$result = $archive->create(
			[$demosPath . $demoName],
			PCLZIP_OPT_REMOVE_ALL_PATH
		);

		if($result == 0) {
			throw new Exception('Demo ' . $demoId . ' compressing error');
		}

		unlink($demosPath . $demoName);

		return $compressedDemoName;
	}

	private function getMapName($mapName) {
		$mapName = explode('/', $mapName);
		$mapName = end($mapName);

		return clean_str(str_replace(['-', '.'], ['_', '_'], $mapName));
	}

	private function getDemosPath($serverId)
	{
		return __DIR__ . '/../../../../../../' . $this->getDemosDir($serverId);
	}

	private function getDemosDir($serverId)
	{
		return self::DEMOS_DIR . $serverId . '/';
	}

	private function getDemoNameById($demoId) {
		return $demoId . '.dem';
	}

	private function getDemoName() {
		return $this->demoName;
	}

	private function setDemoName($serverId, $map, $date) {
		$this->demoName = $serverId . '_' . $date . '_' . $map . '.dem';
	}

	private function createDirectoryIfNotExists($path) {
		if(!is_dir($path)) {
			if(!createDirectory($path)) {
				throw new Exception('Directory (' . $path . ') creation error');
			}
		}
	}
}