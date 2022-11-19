<div class="col-lg-9 order-is-first">
	<h5 class="block_header position-absolute">Список кланов</h5>
	<div class="d-flex justify-content-end mb-2">
		<button type="button" class="btn btn-sm btn-default mr-2" data-toggle="collapse" data-target="#info" aria-expanded="false" aria-controls="info">
			<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-info" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
				<path d="M8.93 6.588l-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588z"/>
				<circle cx="8" cy="4.5" r="1"/>
			</svg>
		</button>
		{if(isset($_SESSION['id']))}
		<button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#create">Создать клан</button>
		{/if}
	</div>
	<div class="collapse" id="info">
		<div class="block" style="white-space: pre-line;">
			Сброс рейтинга осуществляется автоматически 1 числа каждого месяца
			При сбросе кланы вознаграждаются:

			1 место - 300 руб. на баланс клана.
			2 место - 200 руб. на баланс клана.
			3 место - 100 руб. на баланс клана.
		</div>
	</div>
	<div class="block">
		<ul class="clans">
			{clans}
		</ul>
	</div>
</div>

<div class="col-lg-3 order-is-last">
	{if(is_auth())}
		{include file="/home/navigation.tpl"}
		{include file="/home/sidebar_secondary.tpl"}
	{else}
		{include file="/index/authorization.tpl"}
		{include file="/home/sidebar_secondary.tpl"}
	{/if}
</div>

{if(isset($_SESSION['id']))}
<div class="modal fade" id="create" tabindex="-1" role="dialog" aria-labelledby="create">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="create">Создание клана</h4>
			</div>
			<form id="form_clan_create" class="modal-body">
				<input type="text" name="name" class="form-control" placeholder="Введите название" autocomplete="off">
				<button class="btn btn-primary w-100">Создать [100 руб.]</button>
			</form>
		</div>
	</div>
</div>
{/if}