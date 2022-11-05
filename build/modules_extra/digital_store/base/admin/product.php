<?php
if(!is_admin()) {
	show_error_page('not_adm');
}

if(isset($_GET['id'])) {
	$id = clean($_GET['id'], "int");
} else {
	show_error_page('not_settings');
}

$STH = $pdo->prepare("SELECT * FROM `digital_store__products` WHERE `id`=:id LIMIT 1");
$STH->setFetchMode(PDO::FETCH_OBJ);
$STH->execute(array( ':id' => $id ));
$product = $STH->fetch();
if(empty($product->id)) {
	show_error_page();
}

include_once "modules_extra/digital_store/base/config.php";

$tpl->load_template('elements/title.tpl');
$tpl->set("{title}", $PI->compile_str($page->title, $product->name));
$tpl->set("{name}", $conf->name);
$tpl->compile('title');
$tpl->clear();

$tpl->load_template('head.tpl');
$tpl->set("{title}", $tpl->result['title']);
$tpl->set("{image}", $page->image);
$tpl->set("{other}", $module['to_head_admin']."<script src=\"{site_host}modules/editors/tinymce/tinymce.min.js\"></script>");
$tpl->set("{token}", $token);
$tpl->set("{cache}", $conf->cache);
$tpl->set("{template}", $conf->template);
$tpl->set("{site_host}", $site_host);
$tpl->compile('content');
$tpl->clear();

$tpl->load_template('top.tpl');
$tpl->set("{site_host}", $site_host);
$tpl->set("{site_name}", $conf->name);
$tpl->compile('content');
$tpl->clear();

$tpl->load_template('menu.tpl');
$tpl->set("{site_host}", $site_host);
$tpl->compile('content');
$tpl->clear();

$nav = array($PI->to_nav('admin', 0, 0),
             $PI->to_nav('admin_modules', 0, 0),
             $PI->to_nav('admin_digital_store', 0, 0),
             $PI->to_nav('admin_digital_store_product', 1, 0, $product->name));
$nav = $tpl->get_nav($nav, 'elements/nav_li.tpl', 1);

$tpl->load_template('page_top.tpl');
$tpl->set("{nav}", $nav);
$tpl->compile('content');
$tpl->clear();

$tpl->load_template($module['tpl_dir_admin'].'product.tpl');
$tpl->set("{site_host}", $site_host);
$tpl->set("{product_id}", $product->id);
$tpl->compile('content');
$tpl->clear();

$tpl->load_template('bottom.tpl');
$tpl->set("{site_host}", $site_host);
$tpl->compile('content');
$tpl->clear();