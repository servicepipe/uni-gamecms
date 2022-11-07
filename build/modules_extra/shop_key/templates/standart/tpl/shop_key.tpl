<div class="col-lg-9 order-is-first">
	<div class="block">
		<div class="block_head">
			Покупка ресурсов
		</div>
		{if('{servers}' == '0')}
			Магазин пуст
		{else}
			{if('{discount}' != '0')}
				<div class="noty-block success">
					<h4>Внимание! Действует скидка!</h4>
					На все услуги действует скидка в размере {discount}%
				</div>
			{/if}
			<div id="buy_service_area">
				<script>
				function local_change_serv() {
					var server = $('#store_server option:selected').val();
					sk_get_services(server);
				}
				function local_change_service() {
					var service = $('#store_services option:selected').val();
					sk_get_tarifs(service);
				}
				</script>

				<div class="form-group">
					<label>
						<h4>
							Выберите сервер
						</h4>
					</label>
					<select class="form-control" id="store_server" onchange="local_change_serv();">
						{servers}
					</select>
				</div>

				<div class="form-group">
					<label>
						<h4>
							Выберите товар
						</h4>
					</label>
					<select class="form-control" id="store_services" onchange="local_change_service();"></select>
				</div>

				<div class="form-group">
					<label>
						<h4>
							Выберите тариф
						</h4>
					</label>
					<select class="form-control" id="store_tarifs"></select>
				</div>

				{if(is_auth())}
				<div class="form-group">
					<div id="buy_result" class="mt-3"></div>
					<div id="button" class="mt-3">
						<button id="store_buy_btn" class="btn btn-primary" onclick="shop_key();">Купить</button>
					</div>
				</div>	
				{else}
				<div class="noty-block error">
					<p>Авторизуйтесь, чтобы приобрести товар</p>
				</div>
				{/if}
			</div>
			<script>local_change_serv();</script>
		{/if}
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