<?php

namespace RconShop;

use PDO;

class Tarif
{
	public static function getTarifsFromPost($postData)
	{
		$tarifs = [];

		foreach($postData as $key => $value) {
			if (stripos($key, 'tarif-price') !== false) {
				$id = str_replace('tarif-price', '', $key);

				if($id != '') {
					$tarifs[$id] = [
						'price' => clean(empty($postData['tarif-price' . $id]) ? null : $postData['tarif-price' . $id]),
						'title' => clean(empty($postData['tarif-title' . $id]) ? null : $postData['tarif-title' . $id]),
						'command' => clean(empty($postData['tarif-command' . $id]) ? null : $postData['tarif-command' . $id]),
					];
				}
			}
		}

		return $tarifs;
	}

	public function get($id)
	{
		$STH = pdo()->prepare(
			"SELECT * FROM rcon_shop__tarifs WHERE id=:id LIMIT 1"
		);
		$STH->execute([':id' => $id]);

		return $STH->fetch(PDO::FETCH_OBJ);
	}

	public function getList($productId)
	{
		$STH = pdo()->prepare(
			"SELECT * FROM rcon_shop__tarifs WHERE product_id=:product_id"
		);
		$STH->execute([':product_id' => $productId]);

		return $STH->fetchAll(PDO::FETCH_OBJ);
	}

	public function remove($id)
	{
		pdo()->prepare(
			"DELETE FROM rcon_shop__tarifs WHERE id=:id LIMIT 1"
		)->execute([':id' => $id]);
	}

	public function removeProductTarifs($productId)
	{
		pdo()->prepare(
			"DELETE FROM rcon_shop__tarifs WHERE product_id=:product_id"
		)->execute([':product_id' => $productId]);
	}

	public function validatePrice($price, $id = null)
	{
		$exception = $this->registerInputException(
			'tarif-price' . (is_null($id) ? '' : $id)
		);

		if(empty($price)) {
			$exception->setMessage('Не должна быть пустой');

			throw $exception;
		}
	}

	function validateTitle($title, $id = null)
	{
		$exception = $this->registerInputException(
			'tarif-title' . (is_null($id) ? '' : $id)
		);

		if(empty($title)) {
			$exception->setMessage('Не должно быть пустым');

			throw $exception;
		}

		if(empty($title) || isStringLengthMore($title, 255)) {
			$exception->setMessage('Не более 255 символов');

			throw $exception;
		}
	}

	public function validateCommand($command, $commandParams, $id = null)
	{
		$exception = $this->registerInputException(
			'tarif-command' . (is_null($id) ? '' : $id)
		);

		if(empty($command)) {
			$exception->setMessage('Не должна быть пустой');

			throw $exception;
		}

		if(isStringLengthMore($command, 512)) {
			$exception->setMessage('Не более 512 символов');

			throw $exception;
		}

		foreach($commandParams as $param) {
			if(stristr($command, $param['name']) === false) {
				$exception->setMessage('Должна содержать переменную ' . $param['name']);
				throw $exception;
			}
		}
	}

	private function registerInputException($inputName) {
		$exception = new \InputValidationException();
		$exception->setInput($inputName);

		return $exception;
	}

	public function addList($productId, $tarifs)
	{
		foreach($tarifs as $tarif) {
			$this->add($productId, $tarif['title'], $tarif['price'], $tarif['command']);
		}
	}

	public function add($productId, $title, $price, $command)
	{
		pdo()->prepare(
			"INSERT INTO rcon_shop__tarifs (product_id, price, title, command) values (:product_id, :price, :title, :command)"
		)->execute(
			[
				'product_id' => $productId,
				'price'      => $price,
				'title'      => $title,
				'command'    => $command,
			]
		);
	}

	public function validateTarifs($tarifs, $commandParams)
	{
		try {
			foreach($tarifs as $id => $tarif) {
				$this->validatePrice($tarif['price'], $id);
				$this->validateTitle($tarif['title'], $id);
				$this->validateCommand($tarif['command'], $commandParams, $id);

				$tarifsCopy = $tarifs;
				unset($tarifsCopy[$id]);

				if(in_array($tarif['title'], array_column($tarifsCopy, 'title'))) {
					$exception = $this->registerInputException('tarif-title' . $id);
					$exception->setMessage('Такой тариф уже есть');
					throw $exception;
				}
			}
		} catch(\InputValidationException $exception) {
			throw $exception;
		}
	}
}