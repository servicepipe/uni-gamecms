<div class="col-lg-9 order-is-first">
	<div class="block">
		<div class="product-block">
			<div class="product-bg" style="background-image: url('../{image}')"></div>
			<div class="product-content">
				<h2>
					{name}
				</h2>
				<span>
					Цена <b class="price">{price} руб.</b> Осталось <b class="count"><span id="keys_count">{count}</span> шт.</b>
				</span>
				<button class="btn btn-primary" onclick="buy_product_key({id}, {price});">Купить товар</button>
			</div>
		</div>

		<div class="with_code">
			{description}
		</div>
	</div>
</div>

<div class="col-lg-3 order-is-last">
	<div class="block">
		<div class="block_head">
			Категории
		</div>
		<div class="vertical-navigation">
			<ul>
				{categories}
			</ul>
		</div>
	</div>

	{if(is_auth())}
	{include file="/home/navigation.tpl"}
	{include file="/home/sidebar_secondary.tpl"}
	{else}
	{include file="/index/authorization.tpl"}
	{include file="/index/sidebar_secondary.tpl"}
	{/if}
</div>

<div id="buy_modal" class="modal fade">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">Информация о покупке</h4>
			</div>
			<div class="modal-body" id="buy_modal_data">
			</div>
		</div>
	</div>
</div>