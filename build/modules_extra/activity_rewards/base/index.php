<?php
if($page->privacy == 1 && !is_auth()) {
	show_error_page('not_auth');
}

include_once __DIR__ . '/config.php';

$tpl->load_template('elements/title.tpl');
$tpl->set("{title}", $page->title);
$tpl->set("{name}", $conf->name);
$tpl->compile( 'title' );
$tpl->clear();

$tpl->load_template('head.tpl');
$tpl->set("{title}", $tpl->result['title']);
$tpl->set("{site_name}", $conf->name);
$tpl->set("{image}", $page->image);
$tpl->set("{robots}", $page->robots);
$tpl->set("{type}", $page->kind);
$tpl->set("{description}", $page->description);
$tpl->set("{keywords}", $page->keywords);
$tpl->set("{url}", $page->full_url);
$tpl->set("{other}", $module['to_head']);
$tpl->set("{token}", $token);
$tpl->set("{cache}", $conf->cache);
$tpl->set("{template}", $conf->template);
$tpl->set("{site_host}", $site_host);
$tpl->compile( 'content' );
$tpl->clear();

$menu = $tpl->get_menu($pdo);

$nav = array(
	$PI->to_nav('main', 0, 0),
	$PI->to_nav('activity_rewards', 1, 0)
);
$nav = $tpl->get_nav($nav, 'elements/nav_li.tpl');

if(isset($_SESSION['id'])) {
	include_once "inc/authorized.php";
} else {
	include_once "inc/not_authorized.php";
}

if(is_auth()) {
	$STH = $pdo->prepare("SELECT days_in_a_row FROM activity_rewards__participants WHERE user_id=:user_id LIMIT 1");
	$STH->setFetchMode(PDO::FETCH_OBJ);
	$STH->execute([':user_id' => $_SESSION['id']]);
	$row = $STH->fetch();

	$daysInARow = (empty($row->days_in_a_row)) ? 1 : $row->days_in_a_row;
} else {
	$daysInARow = 1;
}

$userDonateAmount = getUserDonateAmount($pdo, $_SESSION['id']);

if($userDonateAmount < $activityRewardsConfig->amount_of_money) {
	$amountOfMoneyDelta = round_shilings($activityRewardsConfig->amount_of_money - $userDonateAmount);
} else {
	$amountOfMoneyDelta = 0;
}

$tpl->load_template($module['tpl_dir']."index.tpl");
$tpl->set("{site_host}", $site_host);
$tpl->set("{template}", $conf->template);
$tpl->set("{daysInARow}", $daysInARow);
$tpl->set("{isReIssue}", $activityRewardsConfig->is_re_issue);
$tpl->set("{isNeedMoneyActivity}", $activityRewardsConfig->is_need_money_activity);
$tpl->set("{amountOfMoney}", $activityRewardsConfig->amount_of_money);
$tpl->set("{amountOfMoneyDelta}", $amountOfMoneyDelta);
$tpl->compile( 'content' );
$tpl->clear();
?>