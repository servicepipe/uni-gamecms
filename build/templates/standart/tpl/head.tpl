	<head>
		<meta charset="utf-8">
		<title>{title}</title>

		<link rel="stylesheet" href="{site_host}templates/{template}/css/main.css?v={cache}">
        	{if($theme == 1)}
		<link rel="stylesheet" href="{site_host}templates/{template}/css/theme_dark.css?v={cache}">
        	{/if}
        	{if($theme == 2)}
		<link rel="stylesheet" href="{site_host}templates/{template}/css/theme_ghost.css?v={cache}">
        	{/if}

		<link rel="shortcut icon" href="{site_host}templates/{template}/img/favicon.ico?v={cache}">
		<link rel="image_src" href="{image}">

		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta name="robots" content="{robots}">
		<meta name="revisit" content="1">
		<meta name="description" content="{description}">
		<meta name="keywords" content="{keywords}">
		<meta name="document-state" content="dynamic">
		<meta name="author" content="unigamecms.ru">

		<meta property="og:title" content="{title}">
		<meta property="og:description" content="{description}">
		<meta property="og:type" content="{type}">
		<meta property="og:image" content="{image}">
		<meta property="og:site_name" content="{site_name}">
		<meta property="og:url" content="{url}">

		<meta name="dc.title" content="{title}">
		<meta name="dc.rights" content="Это движок системы сайта, функционал движка направлен на удовлетворение нужд владельцев различных игровых сообществ и их игроков">
		<meta name="dc.creator" content="unigamecms.ru">
		<meta name="dc.language" content="RU">

		<script src="{site_host}templates/{template}/js/jquery.js?v={cache}"></script>
		<script src="{site_host}templates/{template}/js/nprogress.js?v={cache}"></script>
		<script src="{site_host}templates/{template}/js/noty.js?v={cache}"></script>
		<script src="{site_host}templates/{template}/js/mix.js?v={cache}"></script>
		<script src="{site_host}templates/{template}/js/bootstrap.js?v={cache}"></script>
		<script src="{site_host}templates/{template}/js/primary.js?v={cache}"></script>

		<script src="{site_host}ajax/performers/functions.min.js?v={cache}"></script>
		<script src="{site_host}ajax/performers/main.min.js?v={cache}"></script>

		<link rel="stylesheet" href="{site_host}files/jquery-confirm/css/jquery-confirm.css?v={cache}">
		<link href="{site_host}files/toasts/toasty.min.css?v={cache}" rel="stylesheet">

		<!-- Google Translate -->
		<link rel="stylesheet" href="{site_host}templates/{template}/css/google-translate.css">
  		<script src="{site_host}templates/{template}/js/jquery.cookie.min.js"></script>
  		<script src="{site_host}templates/{template}/js/google-translate.js"></script>
		<!-- Google Translate -->

		{if($conf->new_year == 1 || $conf->win_day == 1)}
		<link rel="stylesheet" href="{site_host}templates/{template}/css/holiday.css">
		<script src="{site_host}templates/{template}/js/holiday.js"></script>
		{/if}

		{files}
		{other}

		{if(isModuleActive('activity_rewards'))}	
		<button class="smart-roulette__gift smart-roulette__gift_left d-none d-sm-block"> <a href="../activity_rewards"><img src="/templates/standart/img/gift.svg" width="80" alt="Подарок" title="Награды за твое посещение"></a></button>
		{/if}
	</head>
	<body>
		{if($conf->new_year == 1)}
			{include file="/elements/new_year.tpl"}
		{/if}
		{if($conf->win_day == 1)}
			{include file="/elements/win_day.tpl"}
		{/if}

		<input id="token" type="hidden" value="{token}">

		<div id="global_result">
			<span class="m-icon icon-ok result_ok disp-n"></span>
			<span class="m-icon icon-remove result_error disp-n"></span>
			<span class="m-icon icon-ok result_ok_b disp-n"></span>
			<span class="m-icon icon-remove result_error_b disp-n"></span>
		</div>
		<div id="result_player"></div>

		<div class="d-none d-sm-block">
		<div class="language">
   			<img src="{site_host}templates/{template}/img/lang/lang__ru.png" alt="ru" data-google-lang="ru" class="language__img">
   			<img src="{site_host}templates/{template}/img/lang/lang__en.png" alt="en" data-google-lang="en" class="language__img">
   			<img src="{site_host}templates/{template}/img/lang/lang__de.png" alt="de" data-google-lang="de" class="language__img">
   			<img src="{site_host}templates/{template}/img/lang/lang__fr.png" alt="fr" data-google-lang="fr" class="language__img">
   			<img src="{site_host}templates/{template}/img/lang/lang__pt.png" alt="pt" data-google-lang="pt" class="language__img">
   			<img src="{site_host}templates/{template}/img/lang/lang__es.png" alt="es" data-google-lang="es" class="language__img">
   			<img src="{site_host}templates/{template}/img/lang/lang__it.png" alt="it" data-google-lang="it" class="language__img">
   			<img src="{site_host}templates/{template}/img/lang/lang__zh.png" alt="zh" data-google-lang="zh-CN" class="language__img">
   			<img src="{site_host}templates/{template}/img/lang/lang__ar.png" alt="ar" data-google-lang="ar" class="language__img">
   			<img src="{site_host}templates/{template}/img/lang/lang__nl.png" alt="nl" data-google-lang="nl" class="language__img">
   			<img src="{site_host}templates/{template}/img/lang/lang__sv.png" alt="sv" data-google-lang="sv" class="language__img">
		</div>
	</div>