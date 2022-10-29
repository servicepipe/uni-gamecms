<?php
$ip = $_SERVER['REMOTE_ADDR'];
$ipInfo = json_decode(file_get_contents("http://ip-api.com/json/{$ip}?fields=status,message,country,countryCode"), true);

if(isset($ipInfo['country']) && $ipInfo['country'] != 'Kazakhstan' && $ipInfo['country'] != 'Russia' && $ipInfo['country'] != 'Serbia' && $ipInfo['country'] != 'Slovakia' && $ipInfo['country'] != 'Slovenia' && $ipInfo['country'] != 'Tajikistan' && $ipInfo['country'] != 'Turkmenistan' && $ipInfo['country'] != 'Ukraine' && $ipInfo['country'] != 'Turkey' && $ipInfo['country'] != 'Uzbekistan' && $ipInfo['country'] != 'Belarus' && $ipInfo['country'] != 'Latvia' && $ipInfo['country'] != 'Lithuania'&& $ipInfo['country'] != 'Moldova' && $ipInfo['country'] != 'Azerbaijan' && $ipInfo['country'] != 'Armenia' && $ipInfo['country'] != 'Bulgaria' && $ipInfo['country'] != 'Kyrgyzstan' && $ipInfo['country'] != 'Estonia' && $ipInfo['country'] != 'Georgia') {  
  //print('BLOCKED COUNTRY');	
    http_response_code(403);
die('
<head>
	<meta charset="UTF-8">
	<title>Доступ запрещен</title>

	<meta name="robots" content="none">
	<meta name="author" content="unigamecms.ru">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="stylesheet" href="./files/toasts/toasty.min.css">
	<link rel="shortcut icon" href="./templates/admin/img/favicon.ico?v={cache}">
	<link rel="image_src" href="{image}">

	<script src="./templates/admin/js/jquery.js?v={cache}"></script>
	<script src="./templates/admin/js/nprogress.js?v={cache}"></script>
	<script src="./templates/admin/js/secondary.js?v={cache}"></script>
	<script src="./templates/admin/js/bootstrap.js?v={cache}"></script>
	<script src="./ajax/performers/functions.min.js?v={cache}"></script>
	<script src="./ajax/performers/acp.min.js?v={cache}"></script>
</head>

<center><img src="./templates/admin/img/403.png"></center>

<center>Доступ запрещен</center>
');
}
?>
<!DOCTYPE html>
<html lang="ru">
		{content}
	</body>
</html>