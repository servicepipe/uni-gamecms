<div class="block">
	<div class="block_head">
	На сайте <div id="green_circle_online"></div><span id="users_online_number"></span> <small>чел.</small>

	</div>
	<div id="online_users">
		{func Widgets:online_users()}
	</div>
</div>

{*
<div class="block" id="birthday_boys">
	<div class="block_head">
		Дни рождения
	</div>
	<div id="online_users">
		{func Widgets:birthday_boys()}
	</div>
</div>
*}

{if($conf->top_donators == '1')}
	<div class="block">
		<div class="block_head">
			Топ донатеров
		</div>
		<div id="online_users">
			{func Widgets:top_donators($conf->top_donators_count)}
		</div>
	</div>
{/if}

<div class="block">
	<div class="block_head">
		Топ пользователей
	</div>
	<div id="top_users">
		{func Widgets:top_users('5')}
	</div>
</div>

<div class="block">
	<div class="block_head">
		Последнее на форуме
	</div>
	<div id="last_activity">
		{func Widgets:last_forum_activity('5')}
	</div>
</div>