<?php

namespace RconShop;

use PDO;
use ServersManager;

class Category
{
	function validateTitle($title, $categoryId = 0, $serverId = 0)
	{
		$exception = $this->registerInputException('title');

		if(empty($title)) {
			$exception->setMessage('Поле не должно быть пустым');

			throw $exception;
		}

		if(empty($title) || isStringLengthMore($title, 255)) {
			$exception->setMessage('Не более 255 символов');

			throw $exception;
		}

		if($categoryId == 0 && $serverId != 0) {
			if($this->getByTitle($title, $serverId)) {
				$exception->setMessage('Категория с таким названием уже создана');

				throw $exception;
			}
		} else {
			$category = $this->get($categoryId);

			if(empty($category)) {
				$exception->setMessage('Категория не найдена');

				throw $exception;
			}

			$category = $this->getByTitle($title, $category->server_id);

			if(!empty($category) && $category->id != $categoryId) {
				$exception->setMessage('Категория с таким названием уже создана');

				throw $exception;
			}
		}
	}

	function validateServer($serverId)
	{
		$exception = $this->registerInputException('server');

		$server = (new ServersManager())->getServer($serverId);

		if(empty($server)) {
			$exception->setMessage('Настройте отправку ркон команд');

			throw $exception;
		}

		if($server->rcon != 1) {
			$exception->setMessage('Не настроена работа rcon команд');

			throw $exception;
		}
	}

	private function registerInputException($inputName) {
		$exception = new \InputValidationException();
		$exception->setInput($inputName);

		return $exception;
	}

	function add($title, $serverId)
	{
		pdo()->prepare(
			"INSERT INTO rcon_shop__categories (title, server_id) values (:title, :server_id)"
		)->execute(['title' => $title, 'server_id' => $serverId]);
	}

	function update($title, $id)
	{
		pdo()->prepare(
			"UPDATE rcon_shop__categories SET title=:title WHERE id=:id LIMIT 1"
		)->execute([':title' => $title, ':id' => $id]);
	}

	public function remove($id)
	{
		pdo()->prepare(
			"DELETE FROM rcon_shop__categories WHERE id=:id LIMIT 1"
		)->execute([':id' => $id]);

		$Product = new Product();
		$productsList = $Product->getList($id);

		foreach($productsList as $product) {
			$Product->remove($product->id);
		}
	}

	public function getByTitle($title, $serverId)
	{
		$STH = pdo()->prepare(
			"SELECT * FROM rcon_shop__categories WHERE title=:title AND server_id=:server_id LIMIT 1"
		);
		$STH->execute([':title' => $title, ':server_id' => $serverId]);

		return $STH->fetch(PDO::FETCH_OBJ);
	}

	public function get($id)
	{
		$STH = pdo()->prepare(
			"SELECT * FROM rcon_shop__categories WHERE id=:id LIMIT 1"
		);
		$STH->execute([':id' => $id]);

		return $STH->fetch(PDO::FETCH_OBJ);
	}

	public function getList($serverId = 0, $isNeedGeneralCategory = false)
	{
		if($serverId == 0) {
			$categories = pdo()->query("SELECT * FROM rcon_shop__categories");
		} else {
			$categories = pdo()->prepare(
				"SELECT * FROM rcon_shop__categories WHERE server_id=:server_id"
			);
			$categories->execute([':server_id' => $serverId]);
		}

		$categories = $categories->fetchAll(PDO::FETCH_OBJ);

		if($isNeedGeneralCategory) {
			global $messages;

			$generalCategory = new \stdClass();
			$generalCategory->id = 0;
			$generalCategory->title = $messages['All'];

			array_unshift($categories, $generalCategory);
		}

		return $categories;
	}

	public function getServers()
	{
		$servers = pdo()->query(
			"SELECT 
						    servers.id,
						    servers.name
						FROM 
						    servers 
						        INNER JOIN 
							        rcon_shop__categories 
							            on 
							                servers.id = rcon_shop__categories.server_id 
							GROUP BY servers.id");
		return $servers->fetchAll(PDO::FETCH_OBJ);
	}
}