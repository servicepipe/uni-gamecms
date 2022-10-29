<div class="col-lg-9 order-is-first">
	<div class="cases-main-block">
		{if(is_auth())}
		<a onclick="get_my_cases();" class="icon-open-cases" tooltip="yes" data-original-title="Открытые мною кейсы"></a>
		{/if}
		<h2>Магазин кейсов</h2>
		<div class="info-block">

			<h3>Что такое магазин кейсов?</h3>
			<p>В этом магазине ты можешь покупать кейсы, открывать их и выйгрывать крутые призы на нашем проекте: деньги, скидки, всевозможные привилегии на игровых серверах.</p>
			<h3>А если у меня есть привилегии?</h3>
			<p>Не беда, если тебе выпадет привилегия, которая у тебя уже есть, то ты получишь продление собственной привилегии на срок выпавшей. Если же выпадет услуга, которой у тебя еще нет, то она суммируется с твоей.</p>
			<h3>Как открыть кейс?</h3>
			<ul>
				<li>Выбери подходящий тебе кейс.</li>
				<li>Оплати и открой его.</li>
				<li>Забери приз.</li>
			</ul>
		</div>
		<div id="cases" class="row">
			<div class="loader"></div>
			<script>load_cases();</script>
		</div>
	</div>
</div>

<div class="modal fade" id="my_cases">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Последние 10 открытых кейсов</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" id="my_cases_area"></div>
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