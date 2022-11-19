<div class="col-lg-7 order-is-last vacancy-chat">
	<div class="block">
		{if(isset($_SESSION['id']) && '{status-id}' == '2' && '{access}' == '1')}
		<form id="form_vacancy_message">
			<input name="vid" type="hidden" value="{vid}">
			<textarea name="message" placeholder="Напишите сообщение..."></textarea>
			<input class="btn btn-outline-primary" type="submit" value="Написать">
		</form>
		{/if}
		
		<div class="messages">{messages}</div>
	</div>
</div>

<div class="col-lg-5 order-is-first">
	{if(is_worthy("g") && '{status-id}' == '2')}
	<div class="block">
		<div class="block_head">
			Панель упавления
		</div>
		
		<div class="d-flex justify-content-center">
			<button data-vacancy-success="{vid}" class="btn w-50 btn-success mr-2">Одобрить</button>
			<button data-vacancy-rejection="{vid}" class="btn w-50 btn-danger mr-2">Отказать</button>
		</div>
	</div>
	{/if}
	
	<div class="block">
		<div class="block_head">
			Информация
		</div>
		<ul>
			<li><span class="h">Статус:</span> <span class="{class}">{status}</span></li>
			{if('{status-id}' == '3')}
			<li><span class="h">Причина:</span> {reason}</li>
			<hr>
			{else}
			<hr>
			{/if}
			<li><span class="h">Сервер:</span> {server_name}</li>
			<li><span class="h">Вакансия:</span> {vacancy}</li>
			<li><span class="h">Автор:</span> <a href="/profile?id={uid}" target="_blank" title="Группа: {gp_name}" style="color: {gp_color};">{author}</a></li>
			<li><span class="h">Дата создания:</span> {date}</li>
			{info}
		</ul>
	</div>
</div>