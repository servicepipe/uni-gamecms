<div class="col-lg-9 order-is-first">
	<div class="block">
		<div class="block_head">Магазин игровых скинов</div>
		
		<div class="row">
			<div class="col-lg-6">
				<div class="form-group">
					<label for="skins_server" class="h6">Выберите сервер</label>
					<select id="skins_server" class="form-control" OnChange="select_server();">{servers}</select>
				</div>
				<div class="form-group" id="skins_list"></div>
				
				<button id="buy" class="btn btn-primary btn-block" OnClick="buy();" disabled>Купить</button>
				
				<div id="result_buy"></div>
			</div>
			
			<div class="col-lg-6">
				<div class="form-group">
					<label class="h6">Изображение скина</label>
					<div class="with_code noty-block" id="skins_info"></div>
				</div>
			</div>
		</div>
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

<script src="../../../modules_extra/skins_store/ajax/main.js?v={cache}"></script>