<?php

namespace RconShop;

use PDO;

class CommandParam
{
	public static function putQuotesToUserParams($userParams)
	{
		foreach($userParams as $key => $userParam) {
			$userParams[$key] = '"' . $userParam . '"';
		}

		return $userParams;
	}

	public static function getSlugFromName($name)
	{
		return 'param-' . str_replace(['{', '}'], '', $name);
	}

	private static function getNameFromSlug($name)
	{
		return '{'.$name.'}';
	}

	public function collectCommand($command, $params)
	{
		return str_replace(array_keys($params), array_values($params), $command);
	}

	public static function setUserParams($params, $data)
	{
		$userParams = [];

		foreach($params as $param) {
			$param->slug = self::getSlugFromName($param->name);

			if(!empty($data[$param->slug])) {
				$userParams[$param->name] = $data[$param->slug];
			} else {
				$userParams[$param->name] = '';
			}
		}

		return $userParams;
	}

	public function validateUserParams($userParams)
	{
		try {
			foreach($userParams as $name => $userParam) {
				$this->validateUserParam($userParam, $name);
			}
		} catch(\InputValidationException $exception) {
			throw $exception;
		}
	}

	private function validateUserParam($userParam, $name)
	{
		$exception = $this->registerInputException(self::getSlugFromName($name));

		if(empty($userParam)) {
			$exception->setMessage('Заполните');

			throw $exception;
		}

		if(!\ServerCommands::validateParam($userParam)) {
			$exception->setMessage('Неверное значение');

			throw $exception;
		}

		if(
			$name == '{steamid}'
			&& !\SteamIDOperations::ValidateSteamID($userParam)
		) {
			$exception->setMessage('Неверный STEAM ID');

			throw $exception;
		}
	}

	private function registerInputException($inputName) {
		$exception = new \InputValidationException();
		$exception->setInput($inputName);

		return $exception;
	}

	public function get($id)
	{
		$STH = pdo()->prepare(
			"SELECT * FROM rcon_shop__command_params WHERE id=:id LIMIT 1"
		);
		$STH->execute([':id' => $id]);

		return $STH->fetch(PDO::FETCH_OBJ);
	}

	public function getList($productId)
	{
		$STH = pdo()->prepare(
			"SELECT * FROM rcon_shop__command_params WHERE product_id=:product_id"
		);
		$STH->execute([':product_id' => $productId]);

		return $STH->fetchAll(PDO::FETCH_OBJ);
	}

	public function remove($id)
	{
		pdo()->prepare(
			"DELETE FROM rcon_shop__command_params WHERE id=:id LIMIT 1"
		)->execute([':id' => $id]);
	}

	public function removeProductCommandParams($productId)
	{
		pdo()->prepare(
			"DELETE FROM rcon_shop__command_params WHERE product_id=:product_id"
		)->execute([':product_id' => $productId]);
	}

	public function validateTitle($title, $id = null)
	{
		$exception = $this->registerInputException(
			'param-title' . (is_null($id) ? '' : $id)
		);

		if(empty($title)) {
			$exception->setMessage('Не должно быть пусто');

			throw $exception;
		}

		if(isStringLengthMore($title, 512)) {
			$exception->setMessage('Не более 512 символов');

			throw $exception;
		}
	}

	public static function getParamsFromPost($postData)
	{
		$commandParams = [];

		foreach($postData as $key => $value) {
			if (stripos($key, 'param-name') !== false) {
				$id = str_replace('param-name', '', $key);

				$commandParams[$id] = [
					'name' => clean(empty($postData['param-name' . $id]) ? null : $postData['param-name' . $id]),
					'title' => clean(empty($postData['param-title' . $id]) ? null : $postData['param-title' . $id]),
				];
			}
		}

		return $commandParams;
	}

	public function validateName($name, $id = null)
	{
		$exception = $this->registerInputException(
			'param-name' . (is_null($id) ? '' : $id)
		);

		if(empty($name)) {
			$exception->setMessage('Не должна быть пустой');

			throw $exception;
		}

		if(isStringLengthMore($name, 512)) {
			$exception->setMessage('Не более 512 символов');

			throw $exception;
		}

		if(!preg_match('/^{[A-Za-z0-9]+}$/', $name)) {
			$exception->setMessage('Должна иметь вид: {name}');

			throw $exception;
		}
	}

	public function validateParams($commandParams)
	{
		try {
			foreach($commandParams as $id => $commandParam) {
				$this->validateName($commandParam['name'], $id);
				$this->validateTitle($commandParam['title'], $id);

				$commandParamsCopy = $commandParams;
				unset($commandParamsCopy[$id]);

				if(
					in_array(
						$commandParam['name'],
						array_column($commandParamsCopy, 'name')
					)
				) {
					$exception = $this->registerInputException('param-name' . $id);
					$exception->setMessage('Такая переменная уже есть');
					throw $exception;
				}

				if(
					in_array(
						$commandParam['title'],
						array_column($commandParamsCopy, 'title')
					)
				) {
					$exception = $this->registerInputException('param-title' . $id);
					$exception->setMessage('Такая переменная уже есть');
					throw $exception;
				}
			}
		} catch(\InputValidationException $exception) {
			throw $exception;
		}
	}

	public function addList($productId, $commandParams)
	{
		foreach($commandParams as $CommandParam) {
			$this->add($productId, $CommandParam['title'], $CommandParam['name']);
		}
	}

	public function add($productId, $title, $name)
	{
		pdo()->prepare(
			"INSERT INTO rcon_shop__command_params (product_id, title, name) values (:product_id, :title, :name)"
		)->execute(
			[
				'product_id' => $productId,
				'title'      => $title,
				'name'       => $name,
			]
		);
	}
}