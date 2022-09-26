<div class="col-lg-9 order-is-first">
	<div class="block">
		<div class="block_head">
			Тикеты
		</div>

		<div class="noty-block info">
			<p>
			Прежде чем создать тикет, сформулируйте свой вопрос максимально корректно, приложите к телу письма скриншот, если это необходимо. Обязательно указывайте к какому конкретно серверу относится Ваш вопрос. Закрывайте тикет самостоятельно, если Ваша проблема решена, либо утеряла свою актуальность. Не дубликуйте один тикет несколько раз подряд, это не ускорит время ответа на Ваш вопрос. Проявляйте вежливость и терпение.
			</p>
		</div>

		<div class="table-responsive mb-0 mt-3">
			<table class="table table-bordered">
				<thead>
					<tr>
						<td>Тема</td>
						<td>Статус</td>
						<td>Дата</td>
					</tr>
				</thead>
				<tbody id="tickets">
					<tr>
						<td colspan="10">
							<div class="loader"></div>
							<script>load_tickets('{id}');</script>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>

<div class="col-lg-3 order-is-last">
	<div class="block">
		<a href="../support/add_ticket" class="btn btn-outline-primary btn-xl">Создать тикет</a>
		{if(is_worthy("p"))}
			<a href="../support/all_tickets" class="btn btn-outline-primary btn-xl mt-2">Тикеты</a>
		{/if}
	</div>

	{include file="/home/navigation.tpl"}
	{include file="/home/sidebar_secondary.tpl"}
</div>