<?php
$module = array('name'          => 'digital_store',
                'to_head_admin' => "<script src=\"$site_host/modules_extra/digital_store/ajax/ajax.js?v={cache}\"></script>
<link rel=\"stylesheet\" href=\"$site_host/modules_extra/digital_store/templates/admin/css/primary.css?v={cache}\">",
                'to_head'       => "",
                'tpl_dir'       => "../../../modules_extra/digital_store/templates/$conf->template/tpl/",
                'tpl_dir_admin' => "../../../modules_extra/digital_store/templates/admin/tpl/");

class DigitalStore {
	private $pdo;
	private $tpl;
	private $module;

	function __construct($module, $pdo = null, $tpl = null) {
		$this->module = $module;
		if(isset($pdo)) {
			$this->pdo = $pdo;
		}
		if(isset($tpl)) {
			$this->tpl = $tpl;
		}
	}

	public function get_categories($category) {
		$class = "";
		if($category == 0) {
			$class = "active";
		}
		$categories = "<li class=\"$class\"><a href=\"../digital_store\">Все</a></li>";

		$STH = $this->pdo->query("SELECT * FROM `digital_store__categories`");
		$STH->setFetchMode(PDO::FETCH_OBJ);
		while($row = $STH->fetch()) {
			$class = "";
			if($row->id == $category) {
				$class = "active";
			}
			$categories .= "<li class=\"$class\"><a href=\"../digital_store?category=$row->id\">$row->name</a></li>";
		}

		return $categories;
	}

	public function get_count_of_product_keys($product) {
		$STH = $this->pdo->prepare("SELECT COUNT(*) as count FROM `digital_store__keys` WHERE `product`=:product AND `pay` = 0");
		$STH->setFetchMode(PDO::FETCH_OBJ);
		$STH->execute(array( ':product' => $product ));
		$count = $STH->fetch();

		return $count->count;
	}

	public function get_products($category) {
		$this->tpl->result['local_content'] = "";

		if($category == 0) {
			$STH = $this->pdo->query("SELECT `id`,`name`,`image`,`price` FROM `digital_store__products`");
			$STH->setFetchMode(PDO::FETCH_OBJ);
		} else {
			$STH = $this->pdo->prepare("SELECT `id`,`name`,`image`,`price` FROM `digital_store__products` WHERE `category`=:category");
			$STH->setFetchMode(PDO::FETCH_OBJ);
			$STH->execute(array(':category' => $category));
		}

		while($row = $STH->fetch()) {
			$this->tpl->load_template($this->module['tpl_dir'].'product_card.tpl');
			$this->tpl->set("{id}", $row->id);
			$this->tpl->set("{name}", $row->name);
			$this->tpl->set("{image}", $row->image);
			$this->tpl->set("{price}", $row->price);
			$this->tpl->set("{count}", $this->get_count_of_product_keys($row->id));
			$this->tpl->compile('local_content');
			$this->tpl->clear();
		}

		if(empty($this->tpl->result['local_content'])) {
			$this->tpl->result['local_content'] = "<center style='padding-left: 50%;'>Категория пуста</center>";
		}

		return $this->tpl->result['local_content'];
	}
}