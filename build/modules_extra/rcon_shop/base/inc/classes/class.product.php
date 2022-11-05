<?php

namespace RconShop;

use PDO;

class Product
{
	const IMAGES_PATH     = 'storage/images/';
	const DEFAULT_IMAGE   = 'none.jpg';
	const STATUS_ENABLED  = 1;
	const STATUS_DISABLED = 2;

	public function get($id)
	{
		$STH = pdo()->prepare(
			"SELECT * FROM rcon_shop__products WHERE id=:id LIMIT 1"
		);
		$STH->execute([':id' => $id]);

		return $STH->fetch(PDO::FETCH_OBJ);
	}

	public function getByTitle($title)
	{
		$STH = pdo()->prepare(
			"SELECT * FROM rcon_shop__products WHERE title=:title LIMIT 1"
		);
		$STH->execute([':title' => $title]);

		return $STH->fetch(PDO::FETCH_OBJ);
	}

	public function add(
		$category,
		$title,
		$status,
		$isHasTarifs,
		$image,
		$description
	) {
		pdo()->prepare(
			"INSERT INTO rcon_shop__products (category_id, title, image, description, is_has_tarifs, status) values (:category_id, :title, :image, :description, :is_has_tarifs, :status)"
		)->execute(
			[
				'category_id'   => $category,
				'title'         => $title,
				'image'         => $image,
				'description'   => $description,
				'is_has_tarifs' => $isHasTarifs,
				'status'        => $status
			]
		);
	}

	public function update(
		$id,
		$title,
		$status,
		$isHasTarifs,
		$image,
		$description
	) {
		pdo()->prepare(
			"UPDATE rcon_shop__products SET title=:title, image=:image, description=:description, is_has_tarifs=:is_has_tarifs, status=:status WHERE id=:id LIMIT 1"
		)->execute(
			[
				'id'            => $id,
				'title'         => $title,
				'image'         => $image,
				'description'   => $description,
				'is_has_tarifs' => $isHasTarifs,
				'status'        => $status
			]
		);
	}

	public function getList($categoryId = 0)
	{
		$STH = pdo()->prepare(
			"SELECT * FROM rcon_shop__products WHERE category_id=:category_id"
		);
		$STH->execute([':category_id' => $categoryId]);

		return $STH->fetchAll(PDO::FETCH_OBJ);
	}

	public static function isEnabled($status)
	{
		return $status == self::STATUS_ENABLED;
	}

	public function getListByServer($serverId)
	{
		$STH = pdo()->prepare(
			"SELECT 
					    rcon_shop__products.* 
					FROM rcon_shop__products 
					    INNER JOIN 
					        rcon_shop__categories 
					            ON rcon_shop__products.category_id = rcon_shop__categories.id 
					WHERE rcon_shop__categories.server_id=:server_id"
		);
		$STH->execute([':server_id' => $serverId]);

		return $STH->fetchAll(PDO::FETCH_OBJ);
	}

	public function remove($id)
	{
		pdo()->prepare(
			"DELETE FROM rcon_shop__products WHERE id=:id LIMIT 1"
		)->execute([':id' => $id]);

		(new Tarif())->removeProductTarifs($id);
		(new CommandParam())->removeProductCommandParams($id);
	}

	public function validateTitle($title)
	{
		$exception = $this->registerInputException('title');

		if(empty($title)) {
			$exception->setMessage('Не должно быть пустым');

			throw $exception;
		}

		if(empty($title) || isStringLengthMore($title, 255)) {
			$exception->setMessage('Не более 255 символов');

			throw $exception;
		}
	}

	public function validateCategory($categoryId)
	{
		$exception = $this->registerInputException('category');

		$category = (new Category())->get($categoryId);

		if(!$category) {
			$exception->setMessage('Категория не существует');

			throw $exception;
		}
	}

	public function validateStatus($status)
	{
		$exception = $this->registerInputException('status');

		if($status != 1 && $status != 2) {
			$exception->setMessage('Неверный статус');

			throw $exception;
		}
	}

	public function validateIsHasTarifs($isHasTarifs)
	{
		$exception = $this->registerInputException('isHasTarifs');

		if($isHasTarifs != 1 && $isHasTarifs != 2) {
			$exception->setMessage('Неверное значение');

			throw $exception;
		}
	}

	public function validateImage($images)
	{
		$exception = $this->registerInputException('status');

		if($this->isHasImage($images) && !if_img($images['image']['name'])) {
			$exception->setMessage('Должено быть в формате JPG, GIF или PNG!');

			throw $exception;
		}
	}

	public function isHasImage($images)
	{
		return (!empty($images['image']) && !empty($images['image']['name']));
	}

	public function uploadImage($images)
	{
		$imageName = self::DEFAULT_IMAGE;

		if($this->isHasImage($images)) {
			$imageName = time() . '.jpg';

			global $ExtraModule;

			move_uploaded_file(
				$images['image']['tmp_name'],
				$ExtraModule->getDirectory(self::IMAGES_PATH) . $imageName
			);
		}

		return self::IMAGES_PATH . $imageName;
	}

	public static function getImageUrl($image) {
		return \ExtraModule::MODULES_URL . MODULE_NAME . '/' . $image;
	}

	public function removeImage($imagePath)
	{
		if($imagePath != self::IMAGES_PATH . self::DEFAULT_IMAGE) {
			global $ExtraModule;
			unlink($ExtraModule->getDirectory($imagePath));
		}
	}

	private function registerInputException($inputName) {
		$exception = new \InputValidationException();
		$exception->setInput($inputName);

		return $exception;
	}
}