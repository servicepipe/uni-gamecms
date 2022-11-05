<?php

namespace RconShop;

use PDO;

class Buy
{
	function add($productId, $tarifId, $userId, $command, $answer)
	{
		pdo()->prepare(
			"INSERT INTO rcon_shop__buys (product_id, tarif_id, user_id, command, answer, date) values (:product_id, :tarif_id, :user_id, :command, :answer, :date)"
		)->execute(
			[
				'product_id' => $productId,
				'tarif_id'   => $tarifId,
				'user_id'    => $userId,
				'command'    => $command,
				'answer'     => $answer,
				'date'       => time()
			]
		);
	}

	public function get($id)
	{
		$STH = pdo()->prepare(
			"SELECT * FROM rcon_shop__buys WHERE id=:id LIMIT 1"
		);
		$STH->execute([':id' => $id]);

		return $STH->fetch(PDO::FETCH_OBJ);
	}

	public function getList($serverId, $categoryId, $limit)
	{
		$where = [];

		if($serverId != 0) {
			$where['rcon_shop__categories.server_id'] = $serverId;
		}

		if($categoryId != 0) {
			$where['rcon_shop__categories.id'] = $categoryId;
		}

		if($limit != 0) {
			$limitExpression = ' LIMIT ' . $limit;
		} else {
			$limitExpression = '';
		}

		if(!empty($where)) {
			$whereExpression = 'WHERE 1=1';
			foreach($where as $key => $item) {
				$whereExpression .= ' AND ' . $key . ' = ' . $item;
			}
		} else {
			$whereExpression = '';
		}

		return pdo()
			->query(
				"SELECT
							    users.id as user_id,
							    users.avatar as user_avatar,
							    users.login as user_login,
							    rcon_shop__products.id as product_id,
    							rcon_shop__products.title,
							    rcon_shop__tarifs.price,
							    rcon_shop__buys.date,
							    rcon_shop__buys.command,
							    rcon_shop__buys.answer,
							    rcon_shop__buys.id AS id
							FROM
							    rcon_shop__buys
									INNER JOIN users
							            ON  rcon_shop__buys.user_id = users.id
									INNER JOIN rcon_shop__products
									    ON rcon_shop__products.id = rcon_shop__buys.product_id
									INNER JOIN rcon_shop__tarifs
									    ON rcon_shop__tarifs.id = rcon_shop__buys.tarif_id
									INNER JOIN rcon_shop__categories
									    ON rcon_shop__categories.id = rcon_shop__products.category_id
							$whereExpression
							ORDER BY rcon_shop__buys.date DESC
							$limitExpression"
			)
			->fetchAll(PDO::FETCH_OBJ);
	}
}
