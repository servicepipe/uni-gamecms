<div class="col-lg-9 order-is-first">
	<div class="cases-main-block">
		{if(is_auth())}
		<a onclick="get_my_cases();" class="icon-open-cases" tooltip="yes" data-original-title="Открытые мною кейсы"></a>
		{/if}
		<a href="../cases" class="icon-back-to-cases" tooltip="yes" data-original-title="Назад к списку кейсов"></a>

		<h2>{name}</h2>
		<div class="roulette">
			{if(is_auth())}
			<div id="sound-point" class="sound-on" onclick="roulette_sound();"></div>
			{/if}
			<div class="roulette-slider">
				<div class="r-left"></div>
				<div class="r-right"></div>
				<div class="r-side"></div>
				<div class="r-side2"></div>
				<div class="top-arr"></div>
				<div class="bottom-arr"></div>
				<div class="roulette-area">
					<div id="roulette"></div>	
				</div>
			</div>
		</div>
		{if(is_auth())}
		<button id="open-case" class="open-case" onclick="open_case({id});">Открыть кейс за {price} руб.</button>
		{else}
		<button class="open-case" data-toggle="modal" data-target="#authorization">Авторизуйтесь</button>
		{/if}
		<div id="case-subjects">
			<br>
			<br>
			<h2>Содержимое кейса</h2>
			<div id="subjects">
				<script>load_subjects({id});</script>
			</div>
		</div>
	</div>
</div>
<div id="case_modals">
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

	<div class="modal fade" id="open_case_result">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">Ошибка</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body" id="open_case_result_area"></div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="prize">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<h4>Твой выигрыш:</h4>
					<div id="prize_area"></div>
					<p>Более подробную информацию о призе мы отправили уведомлением тебе в профиль.</p>
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

<script src="../modules_extra/cases/templates/{template}/js/ui.js?v={cache}"></script>
<script src="../modules_extra/cases/templates/{template}/js/roulette.js?v={cache}"></script>
<script>load_roulette({id});</script>