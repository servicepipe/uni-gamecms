<?php

namespace Demos;

use PDO;

class CustomDemosTable
{
	protected function insertDemo(
		$id,
		$file,
		$size,
		$map,
		$serverId,
		$createdAt
	) {
		pdo()->prepare(
			"INSERT INTO demos (id, file, size, map, server_id, created_at) values (:id, :file, :size, :map, :server_id, :created_at)"
		)->execute(
			[
				'id'         => empty($id) ? uuid4() : $id,
				'file'       => $file,
				'size'       => $size,
				'map'        => $map,
				'server_id'  => $serverId,
				'created_at' => $createdAt
			]
		);
	}

	protected function removeDemos($serverId)
	{
		pdo()
			->prepare("DELETE FROM demos WHERE server_id=:server_id")
			->execute([':server_id' => $serverId]);
	}

	public function getDemos($server, $start, $limit, $map = null)
	{
		if(empty($map)) {
			$STH = pdo()->prepare(
				"SELECT 
						    id, 
						    file as link, 
						    created_at, 
						    map, 
						    size, 
						    server_id
						FROM 
						    demos 
						WHERE 
						    server_id=:server_id ORDER BY created_at DESC LIMIT $start, $limit"
			);
			$STH->execute([':server_id' => $server->id]);
		} else {
			$STH = pdo()->prepare(
				"SELECT 
						    id, 
						    file as link, 
						    created_at, 
						    map, 
						    size
						FROM 
						    demos 
						WHERE 
						    server_id=:server_id AND map LIKE :map ORDER BY created_at DESC LIMIT $start, $limit"
			);
			$STH->execute(
				[
					':server_id' => $server->id,
					':map'       => getNameLike($map)
				]
			);
		}

		$demos = $STH->fetchAll(PDO::FETCH_OBJ);

		foreach($demos as $key => $demo) {
			if (stripos($demo->link, '://') === false) {
				$demos[$key]->link = $this->getDemoLink($demo);
			}
		}

		return $demos;
	}

	public function getCount($server)
	{
		$STH = pdo()->prepare(
			"SELECT COUNT(*) as count FROM demos WHERE server_id=:server_id LIMIT 1"
		);
		$STH->execute([':server_id' => $server->id]);

		return $STH->fetchColumn();
	}

	protected function getDemoLink($demo) {
		global $full_site_host;

		return $full_site_host . $demo->link;
	}
}