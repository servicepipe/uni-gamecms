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

		{if($conf->new_year == 1 || $conf->win_day == 1)}
		<link rel="stylesheet" href="{site_host}templates/{template}/css/holiday.css">
		<script src="{site_host}templates/{template}/js/holiday.js"></script>
		{/if}

		{files}
		{other}
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