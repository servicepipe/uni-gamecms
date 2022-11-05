<?php
include_once "../../../inc/start.php";
include_once "../../../inc/protect.php";
include_once "../../../modules_extra/digital_store/base/config.php";

if(empty($_POST['phpaction'])) {
	log_error("Прямой вызов actions.php");
	exit(json_encode(array('status' => '2', 'Прямой вызов')));
}

if($conf->token == 1 && ($_SESSION['token'] != clean($_POST['token'], null))) {
	log_error("Неверный токен");
	exit(json_encode(array('status' => '2', 'Ошибка токена')));
}

if(isset($_POST['laod_digital_store'])) {
	$category = clean($_POST['category'], "int");

	if(empty($category)) {
		$category = 0;
	}

	$tpl = new Template;
	$tpl->dir = '../../../templates/'.$conf->template.'/tpl/';

	$DS = new DigitalStore($module, $pdo, $tpl);

	$tpl->show($DS->get_products($category));
	$tpl->global_clear();

	exit();
}

if(isset($_POST['buy_product_key'])) {
	$product = clean($_POST['product'], "int");

	if(empty($product)) {
		exit(json_encode(array('status' => 2, 'data' => 'Продукт не найден.')));
	}

	if(!is_auth()) {
		exit(json_encode(array('status' => 2, 'data' => 'Авторизуйтесь, для покупки товара.')));
	}

	$STH = $pdo->prepare("SELECT `id`, `shilings`, `proc` FROM `users` WHERE `id`=:id LIMIT 1");
	$STH->setFetchMode(PDO::FETCH_OBJ);
	$STH->execute(array(':id' => $_SESSION['id']));
	$row = $STH->fetch();
	if(empty($row->id)) {
		exit(json_encode(array('status' => '2', 'data' => 'Ошибка.')));
	}
	$proc = $row->proc;
	$shilings = $row->shilings;

	$STH = $pdo->prepare("SELECT * FROM `digital_store__products` WHERE `id`=:id LIMIT 1");
	$STH->setFetchMode(PDO::FETCH_OBJ);
	$STH->execute(array(':id' => $product));
	$product = $STH->fetch();
	if(empty($product->id)) {
		exit(json_encode(array('status' => 2, 'data' => 'Продукт не найден.')));
	}

	$STH = $pdo->prepare("SELECT * FROM `digital_store__keys` WHERE `product`=:product AND `pay` = '0' LIMIT 1");
	$STH->setFetchMode(PDO::FETCH_OBJ);
	$STH->execute(array(':product' => $product->id));
	$key = $STH->fetch();
	if(empty($key->id)) {
		exit(json_encode(array('status' => 2, 'data' => 'Все содержимое продукта раскуплено.')));
	}

	$STH = $pdo->query("SELECT `discount` FROM `config__prices` LIMIT 1");
	$STH->setFetchMode(PDO::FETCH_OBJ);
	$disc = $STH->fetch();
	$discount = $disc->discount;

	$proc = calculate_discount(0, $discount, $proc);
	$price = calculate_price($product->price, $proc);

	if($shilings < $price) {
		$price_delta = round_shilings($price - $shilings);
		exit (json_encode(array('status' => '2',
		                        'data'   => 'У Вас недостаточно средств.<br><a href="../purse?price='.$price_delta.'">Пополните баланс на '.$price_delta.$messages['RUB'].'.</a>')));
	}
	$shilings = round_shilings($shilings - $price);

	$date = date("Y-m-d H:i:s");
	$STH = $pdo->prepare("INSERT INTO `money__actions` (`date`,`shilings`,`author`,`type`) values (:date, :shilings, :author, :type)");
	$STH->execute(array('date' => $date, 'shilings' => -$price, 'author' => $_SESSION['id'], 'type' => '19'));

	$pay = get_ai($pdo, 'money__actions') - 1;

	$STH = $pdo->prepare("UPDATE `users` SET `shilings`=:shilings WHERE `id`=:id LIMIT 1");
	$STH->execute(array(':shilings' => $shilings, ':id' => $_SESSION['id']));

	$STH = $pdo->prepare("UPDATE `digital_store__keys` SET pay=:pay WHERE `id`=:id LIMIT 1");
	$STH->execute(array(':pay' => $pay, ':id' => $key->id));

	$mess = "Поздравляем Вас с успешной покупкой <b><a href='../digital_store/product?id=$product->id'>$product->name</a></b> в магазине цифровых товаров!<br><div class='buy-product-key-info'>$key->content</div>";
	$STH = $pdo->prepare("INSERT INTO `notifications` (`message`,`date`,`user_id`,`type`) values (:message, :date, :user_id, :type)");
	$STH->execute(array('message' => $mess, 'date' => $date, 'user_id' => $_SESSION['id'], 'type' => '2'));

	sendmail(
		$user->email,
		"Покупка " . $product->name,
		"Поздравляем Вас с успешной покупкой <b><a href='" . $full_site_host
			. "digital_store/product?id=" . $product->id . "'>" . $product->name
			. "</a></b> в магазине цифровых товаров!<br>" . $key->content,
		$pdo
	);

	$mess2 = "Совершена покупка <b><a href='../digital_store/product?id=$product->id'>$product->name</a></b> в магазине цифровых товаров пользователем: <b><a href='../profile?id=".$_SESSION['id']."'>".$_SESSION['login']."</a></b>";

	$STH = $pdo->prepare("INSERT INTO `notifications` (`message`,`date`,`user_id`,`type`) values (:message, :date, :user_id, :type)");
	$STH->execute(array('message' => $mess2, 'date' => $date, 'user_id' => '1', 'type' => '2'));

	$DS = new DigitalStore($module, $pdo);

	exit(json_encode(array('status' => '1', 'data' => $mess, 'shilings' => $shilings, 'count' => $DS->get_count_of_product_keys($product->id))));
}

if(!is_admin()) {
	exit();
}
/*
 * Операции с категориями
 */
if(isset($_POST['add_category'])) {
	$name = clean($_POST['name'], null);

	if(empty($name) || mb_strlen($name, "UTF-8") > 256) {
		exit(json_encode(array('status' => '2', 'input' => 'category_name', 'reply' => 'Не более 256 символов')));
	}

	$STH = $pdo->prepare("SELECT `id` FROM `digital_store__categories` WHERE `name`=:name LIMIT 1");
	$STH->setFetchMode(PDO::FETCH_OBJ);
	$STH->execute(array(':name' => $name));
	$row = $STH->fetch();
	if(!empty($row->id)) {
		exit(json_encode(array('status' => '2', 'input' => 'category_name', 'data' => 'Категория с таким названием уже создана!')));
	}

	$STH = $pdo->prepare("INSERT INTO `digital_store__categories` (`name`) values (:name)");
	$STH->execute(array('name' => $name));

	exit(json_encode(array('status' => '1')));
}
if(isset($_POST['dell_category'])) {
	$id = clean($_POST['id'], "int");

	if(empty($id)) {
		exit();
	}

	$STH = $pdo->prepare("DELETE FROM `digital_store__categories` WHERE `id`=:id LIMIT 1");
	$STH->execute(array(':id' => $id));

	$STH = $pdo->prepare("SELECT `id` FROM `digital_store__products` WHERE `category`=:category");
	$STH->setFetchMode(PDO::FETCH_OBJ);
	$STH->execute(array( ':category' => $id ));
	while($row = $STH->fetch()) {
		$STH2 = $pdo->prepare("DELETE FROM `digital_store__keys` WHERE `product`=:product");
		$STH2->execute(array( ':product' => $row->id ));
	}

	$STH = $pdo->prepare("DELETE FROM `digital_store__products` WHERE `category`=:category");
	$STH->execute(array(':category' => $id));

	exit();
}
if(isset($_POST['load_categories'])) {
	$i = 0;
	$data = "";
	$data_2 = "";

	$STH = $pdo->query("SELECT * FROM `digital_store__categories`");
	$STH->setFetchMode(PDO::FETCH_OBJ);
	while($row = $STH->fetch()) {
		$i++;
		$data .= "<button class=\"btn btn-default mr-5 mb-5\" tooltip=\"yes\" title=\"Удалить\" onclick=\"dell_category($row->id);\">$row->name <span style=\"top: 3px;\" class=\"glyphicon glyphicon-remove\"></span></button>";
		$data_2 .= "<option value=\"$row->id\">$row->name</option>";
	}
	if($i == 0) {
		$data = "Категорий нет";
		$data_2 = "<option>Категорий нет</option>";
	}

	exit(json_encode(array('data' => $data, 'data_2' => $data_2)));
}

/*
 * Операции с продуктами
 */
if(isset($_POST['load_products'])) {
	$category = clean($_POST['category'], "int");

	$STH = $pdo->prepare("SELECT * FROM `digital_store__products` WHERE `category`=:category");
	$STH->setFetchMode(PDO::FETCH_OBJ);
	$STH->execute(array(':category' => $category));
	while($row = $STH->fetch()) {
		?>
		<div class="col-lg-6">
			<div class="block">
				<div class="block_head">
					<?php echo $row->name; ?>
				</div>

				<input type="text" class="form-control" maxlength="256" id="name<?php echo $row->id; ?>" placeholder="Введите название" value="<?php echo $row->name; ?>">
				<input type="text" class="form-control mt-10" maxlength="11" id="price<?php echo $row->id; ?>" placeholder="Введите цену" value="<?php echo $row->price; ?>">
				<div class="mt-10 mb-10" style="border: 1px solid rgb(204, 204, 204);">
					<img style="display: block; width: 300px; height: 200px; margin: 10px auto;" src="../../<?php echo $row->image; ?>" alt="<?php echo $row->name; ?>">
					<input type="file" class="input-file" style="margin: -1px; width: calc( 100% + 2px );" maxlength="256" id="image<?php echo $row->id; ?>">
				</div>
				<textarea id="description<?php echo $row->id; ?>" class="form-control maxMinW100" rows="5"><?php echo $row->description; ?></textarea>

				<button class="btn btn-default mt-10" onclick="product_action(<?php echo $row->id; ?>);">Изменить</button>
				<a href="../admin/digital_store_product?id=<?php echo $row->id; ?>" class="btn btn-default mt-10 ml-5">Настройка содержимого</a>
				<button class="btn btn-default mt-10 ml-5" onclick="dell_product(<?php echo $row->id; ?>);">Удалить</button>

				<script>
                    $(document).ready(function () {
                        init_tinymce('description<?php echo $row->id; ?>', '<?php echo md5($conf->code); ?>', 'full');
                    });
				</script>
			</div>
		</div>
		<?php
	}

	exit();
}

if(isset($_POST['product_action'])) {
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

	$data = array('id'       => array('content' => checkJs($_POST['id'], "int"), 'length' => 7),
	              'category' => array('content' => checkJs($_POST['category'], "int"), 'length' => 6),
	              'name'     => array('content' => check($_POST['name'], null), 'length' => 256),
	              'price'    => array('content' => check($_POST['price'], "float"), 'length' => 11));

	foreach($data as $key => $value) {
		if(empty($value['content']) && $key != 'id') {
			exit(json_encode(array('status' => '2', 'input' => $key, 'data' => 'Заполните!')));
		}
		if(mb_strlen($value['content'], 'UTF-8') > $value['length']) {
			exit(json_encode(array('status' => '2', 'input' => $key, 'data' => 'Не более '.$value['length'].' символов!')));
		}
	}

	$data['description']['content'] = HTMLPurifier()->purify($_POST['description']);
	$data['description']['content'] = find_img_mp3($data['description']['content'], rand(1, 250), 1);

	$STH = $pdo->prepare("SELECT `id` FROM `digital_store__categories` WHERE `id`=:id LIMIT 1");
	$STH->setFetchMode(PDO::FETCH_OBJ);
	$STH->execute(array(':id' => $data['category']['content']));
	$row = $STH->fetch();
	if(empty($row->id)) {
		exit(json_encode(array('status' => '2', 'input' => 'category', 'data' => 'Категория не найдена')));
	}

	$STH = $pdo->prepare("SELECT `id` FROM `digital_store__products` WHERE `name`=:name LIMIT 1");
	$STH->setFetchMode(PDO::FETCH_OBJ);
	$STH->execute(array(':name' => $data['name']['content']));
	$row = $STH->fetch();
	if((!empty($row->id) && empty($data['id']['content'])) || (!empty($row->id) && !empty($data['id']['content']) && $data['id']['content'] != $row->id)) {
		exit(json_encode(array('status' => '2', 'input' => 'name', 'data' => 'Продукт с таким названием уже существует')));
	}

	$image_path = 'modules_extra/digital_store/templates/_images/';

	if(!empty($data['id']['content'])) {
		$STH = $pdo->prepare("SELECT `image` FROM `digital_store__products` WHERE `id`=:id LIMIT 1");
		$STH->setFetchMode(PDO::FETCH_OBJ);
		$STH->execute(array(':id' => $data['id']['content']));
		$row = $STH->fetch();

		$old_image = $row->image;
	}

	if(empty($_FILES['image']['name'])) {
		if(empty($data['id']['content'])) {
			$data['image']['content'] = $image_path.'none.jpg';
		} else {
			$data['image']['content'] = $old_image;
		}
	} else {
		if(if_img($_FILES['image']['name'])) {
			$data['image']['content'] = $image_path.time().'.jpg';
			move_uploaded_file($_FILES['image']['tmp_name'], '../../../'.$data['image']['content']);

			if(!empty($data['id']['content'])) {
				if($old_image != $image_path.'none.jpg') {
					unlink('../../../'.$old_image);
				}
			}
		} else {
			exit(json_encode(array('status' => '2', 'input' => 'image', 'data' => 'Изображение должено быть в формате JPG, GIF или PNG!')));
		}
	}

	unset($_POST);
	$db_data = array();

	foreach($data as $key => $value) {
		$db_data[$key] = $value['content'];
	}

	if(empty($data['id']['content'])) {
		unset($db_data['id']);
		$STH = $pdo->prepare("INSERT INTO `digital_store__products` (`name`, `image`, `category`, `price`, `description`) values (:name, :image, :category, :price, :description)");
	} else {
		$STH = $pdo->prepare("UPDATE `digital_store__products` SET name=:name, image=:image, category=:category, price=:price, description=:description WHERE `id`=:id LIMIT 1");
	}
	$STH->execute($db_data);

	exit(json_encode(array('status' => 1)));
}

if(isset($_POST['dell_product'])) {
	$id = clean($_POST['id'], "int");

	if(empty($id)) {
		exit();
	}

	$STH = $pdo->prepare("DELETE FROM `digital_store__products` WHERE `id`=:id LIMIT 1");
	$STH->execute(array(':id' => $id));

	$STH = $pdo->prepare("DELETE FROM `digital_store__keys` WHERE `product`=:product");
	$STH->execute(array( ':product' => $id ));

	exit();
}

/*
 * Операции с содержимым продукта
 */
if(isset($_POST['load_product_keys'])) {
	$product = clean($_POST['product'], "int");
	$i = 0;

	$STH = $pdo->prepare("SELECT `id`, `content` FROM `digital_store__keys` WHERE `product`=:product AND `pay` = 0");
	$STH->setFetchMode(PDO::FETCH_OBJ);
	$STH->execute(array(':product' => $product));
	while($row = $STH->fetch()) {
		$i++;
		if($i != 1) {
			?>
			<hr>
			<?php
		}
		?>
		<div id="product_key<?php echo $row->id; ?>">
			<b>#<?php echo $row->id; ?></b>
			<textarea id="content<?php echo $row->id; ?>" class="form-control maxMinW100" rows="5"><?php echo $row->content; ?></textarea>

			<button class="btn btn-default mt-10" onclick="product_key_action(<?php echo $row->id; ?>);">Изменить</button>
			<button class="btn btn-default mt-10 ml-5" onclick="dell_product_key(<?php echo $row->id; ?>);">Удалить</button>

			<script>
                $(document).ready(function () {
                    init_tinymce('content<?php echo $row->id; ?>', '<?php echo md5($conf->code); ?>', 'full');
                });
			</script>
		</div>
		<?php
	}

	if($i == 0) {
		exit("Содержимого нет");
	}

	exit();
}

if(isset($_POST['product_key_action'])) {
	$id = clean($_POST['id'], "int");
	$product = clean($_POST['product'], "int");

	if(empty($id)) {
		$id = '';
	}

	if(empty($product)) {
		exit(json_encode(array('status' => '2', 'input' => 'content', 'data' => 'Пустой ID продукта!')));
	}

	$content = HTMLPurifier()->purify($_POST['content']);
	$content = find_img_mp3($content, rand(1, 250), 1);

	if(empty($content)) {
		exit(json_encode(array('status' => '2', 'input' => 'content', 'data' => 'Введите текст!')));
	}

	$STH = $pdo->prepare("SELECT `id` FROM `digital_store__products` WHERE `id`=:id LIMIT 1");
	$STH->setFetchMode(PDO::FETCH_OBJ);
	$STH->execute(array(':id' => $product));
	$row = $STH->fetch();
	if(empty($row->id)) {
		exit(json_encode(array('status' => '2', 'input' => 'content', 'data' => 'Продукт, в который вы добавляете содержимое - не найден.')));
	}

	if(empty($id)) {
		$STH = $pdo->prepare("INSERT INTO `digital_store__keys` (`content`, `product`) values (:content, :product)");
		$STH->execute(array('content' => $content, 'product' => $product));
	} else {
		$STH = $pdo->prepare("UPDATE `digital_store__keys` SET content=:content, product=:product WHERE `id`=:id LIMIT 1");
		$STH->execute(array('content' => $content, 'product' => $product, 'id' => $id));
	}

	exit(json_encode(array('status' => 1)));
}

if(isset($_POST['dell_product_key'])) {
	$id = clean($_POST['id'], "int");

	if(empty($id)) {
		exit();
	}

	$STH = $pdo->prepare("DELETE FROM `digital_store__keys` WHERE `id`=:id LIMIT 1");
	$STH->execute(array(':id' => $id));

	exit();
}

if(isset($_POST['load_sales'])) {
	$load_val = checkJs($_POST['load_val'], "int");

	if(empty($load_val)) {
		$load_val = 1;
	}

	$limit = 30;
	$start = ($load_val - 1) * $limit;
	$i = $start;
	$l = 0;

	$STH = $pdo->query("SELECT `digital_store__keys`.`id` AS 'key_id', `money__actions`.`author`, `digital_store__keys`.`content`, `money__actions`.`date`, `money__actions`.`shilings`, `users`.`avatar`, `users`.`login`,`digital_store__products`.`name`, `digital_store__products`.`id` AS 'product_id', `digital_store__products`.`image` FROM `digital_store__keys` 
	INNER JOIN `digital_store__products` ON `digital_store__keys`.`product` = `digital_store__products`.`id`
	INNER JOIN `money__actions` ON `digital_store__keys`.`pay` = `money__actions`.`id`
	INNER JOIN `users` ON `money__actions`.`author` = `users`.`id`
	ORDER BY `money__actions`.`date` DESC LIMIT $start , $limit");
	$STH->setFetchMode(PDO::FETCH_OBJ);
	while($sale = $STH->fetch()) {
		$i++;
		$l++;
		?>
		<tr>
			<td><?php echo $i; ?></td>
			<td>
				<a target="_blank" href="../digital_store/product?id=<?php echo $sale->product_id ?>" title="<?php echo $sale->name; ?>">
					<img src="../<?php echo $sale->image; ?>" alt="<?php echo $sale->name; ?>">
					<?php echo $sale->name; ?>
				</a>
			</td>
			<td>
				<button class="btn btn-default btn-sm" data-toggle="modal" data-target="#key_modal<?php echo $sale->key_id; ?>">#<?php echo $sale->key_id; ?></button>
				<div id="key_modal<?php echo $sale->key_id; ?>" class="modal fade">
					<div class="modal-dialog modal-lg">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
								<h4 class="modal-title">Информация о покупке</h4>
							</div>
							<div class="modal-body">
								<div class="buy-product-key-info">
									<?php echo $sale->content; ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</td>
			<td><?php echo abs($sale->shilings); ?> <?php echo $messages['RUB']; ?></td>
			<td>
				<a target="_blank" href="../admin/edit_user?id=<?php echo $sale->author ?>" title="<?php echo $sale->login; ?>">
					<img src="../<?php echo $sale->avatar; ?>" alt="<?php echo $sale->login; ?>">
					<?php echo $sale->login; ?>
				</a>
			</td>
			<td><?php echo expand_date($sale->date, 7); ?></td>
		</tr>
		<?php
	}
	if(($load_val > 0) and ($l > $limit - 1)) {
		$load_val++;
		exit ('<tr id="loader'.$load_val.'" class="c-p" onclick="load_sales(\''.$load_val.'\');"><td colspan="10">Подгрузить записи</td></tr>');
	}
	exit();
}