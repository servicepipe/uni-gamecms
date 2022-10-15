<style>
	#site_stats small {
		position: absolute;
		margin-left: 3px;
		color: rgb(209, 87, 77);
		font-weight: bold;
		line-height: 10px;
	}
	#site_stats small a {
		color: rgb(209, 87, 77);
	}
</style>
<div class="block">
	<div class="block_head">
		Статистика
	</div>
	<p>
		Всего пользователей: <b>{users}</b>
		{if({users_diff} > 0)}
			<small title="Изменение за сутки" tooltip="yes">
				<a target="_blank" href="../events?class=3">+{users_diff}</a>
			</small>
		{/if} <br>

		Личных сообщений: <b>{pm__messages}</b>
		{if({pm__messages_diff} > 0)}
			<small title="Изменение за сутки" tooltip="yes">
				+{pm__messages_diff}
			</small>
		{/if} <br>

		Сообщений в чате: <b>{chat}</b>
		{if({chat_diff} > 0)}
			<small title="Изменение за сутки" tooltip="yes">
				+{chat_diff}
			</small>
		{/if} <br>

		Заявок на разбан: <b>{bans_apps}</b>
		{if({bans_apps_diff} > 0)}
			<small title="Изменение за сутки" tooltip="yes">
				<a target="_blank" href="../bans/">+{bans_apps_diff}</a>
			</small>
		{/if}
	</p>
	<hr>
	<p>
		Тем на форуме: <b>{forums__topics}</b>
		{if({forums__topics_diff} > 0)}
			<small title="Изменение за сутки" tooltip="yes">
				<a target="_blank" href="../events?class=4">+{forums__topics_diff}</a>
			</small>
		{/if} <br>

		Ответов на форуме: <b>{forums__messages}</b>
		{if({forums__messages_diff} > 0)}
			<small title="Изменение за сутки" tooltip="yes">
				<a target="_blank" href="../events?class=5">+{forums__messages_diff}</a>
			</small>
		{/if} <br>

		Новостей: <b>{news}</b>
		{if({news_diff} > 0)}
			<small title="Изменение за сутки" tooltip="yes">
				<a target="_blank" href="../events?class=1">+{news_diff}</a>
			</small>
		{/if} <br>

		Комментариев к новостям: <b>{news__comments}</b>
		{if({news__comments_diff} > 0)}
			<small title="Изменение за сутки" tooltip="yes">
				+{news__comments_diff}
			</small>
		{/if}
	</p>
	<hr>
	<p>
		Игровых серверов: <b>{servers}</b>
		{if({servers_diff} > 0)}
			<small title="Изменение за сутки" tooltip="yes">
				+{servers_diff}
			</small>
		{/if} <br>

		Привилегированных: <b>{admins}</b>
		{if({admins_diff} > 0)}
			<small title="Изменение за сутки" tooltip="yes">
				+{admins_diff}
			</small>
		{/if} <br>

		Банов на серверах: <b>{servers_bans}</b>
		{if({servers_bans_diff} > 0)}
			<small title="Изменение за сутки" tooltip="yes">
				+{servers_bans_diff}
			</small>
		{/if} <br>

		Игроков в статистике: <b>{servers_stats}</b>
		{if({servers_stats_diff} > 0)}
			<small title="Изменение за сутки" tooltip="yes">
				+{servers_stats_diff}
			</small>
		{/if}
	</p>
</div>