<div class="col-lg-9 order-is-first">
	<div class="block">
		<div class="block_head">
			Награды за ежедневные посещения сайта, у вас сейчас: <span class="day-in-a-row">{daysInARow}</span>
		</div>

		{if('{isNeedMoneyActivity}' == '1' && '{amountOfMoneyDelta}' != '0')}
		<div class="noty-block success">
			Для получения наград необходимо,
			<b><a href="../purse?pirce={amountOfMoneyDelta}">пополнить баланс</a></b>
			еще на {amountOfMoneyDelta}{{$messages['RUB']}}.
		</div>
		{/if}

		<div id="activity-rewards">
			<div class="loader"></div>
			<script>getRewardsWidget('#activity-rewards');</script>
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