<div class="col-lg-9 order-is-first">
	{if('{empty}' == '0')}
	<div class="block block-search">
		<div class="block_head">Игровые звания</div>

		{if('{error}' == '')}
		<div class="input-search">
			<i class="fas fa-search" onclick="search_ban({server})"></i>
			<input type="text" class="form-control" id="search_ban" placeholder="Введите nick / steam_id / ip">
			<script> set_enter('#search_ban', 'search_ban({server})'); </script>
		</div>

		<div class="table-responsive mb-0">
			<table class="table table-bordered">
				<thead>
					<tr>
						<td>#</td>
						<td>Ник</td>
						<td>Звание</td>
						<td>Опыт</td>
						<td>Бонусы</td>
						<td>Последнее подключение</td>
					</tr>
				</thead>
				<tbody id="list">
					<tr>
						<td colspan="10">
							<div class="loader"></div>
							<script>load_aes_list("{start}", "{server}", 0, 1);</script>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		{else}
		<div class="empty-element">
			{error}
		</div>
		{/if}
	</div>

	<div id="pagination2">{pagination}</div>
	{else}
	<div class="block">
		<div class="block_head">
			Игровые звания
		</div>
		<div class="empty-element">
			Сервера не привязаны к источникам информации.
		</div>
	</div>
	{/if}
</div>

<div class="col-lg-3 order-is-last">
	{if('{empty}' == '0')}
	<div class="block">
		<div class="block_head">
			Сервера
		</div>
		<div class="vertical-navigation">
			<ul>
				{servers}
			</ul>
		</div>
	</div>
	{/if}

	{if(is_auth())}
		{include file="/home/navigation.tpl"}
		{include file="/home/sidebar_secondary.tpl"}
	{else}
		{include file="/index/authorization.tpl"}
		{include file="/index/sidebar_secondary.tpl"}
	{/if}
</div>