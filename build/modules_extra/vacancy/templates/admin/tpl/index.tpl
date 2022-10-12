<div class="page">
	<div class="col-md-4">
		<div class="panel panel-default">
			<div class="panel-heading">Сервер</div>
			<div class="panel-body">
				<select name="server" class="form-control">{servers}</select>
			</div>
		</div>
		
		<div class="panel panel-default">
			<div class="panel-heading">Конфигурации</div>
			<div class="panel-body">
				<label class="mt-4 mb-0" for="">Лимит товаров на странице</label>
				<div class="input-group">
					<span class="input-group-btn">
						<button onclick="SendConfigs('limit_vacancy', $('#limit_vacancy').val());" class="btn btn-default" type="button">Изменить</button>
					</span>
					
					<input id="limit_vacancy" type="text" class="form-control" maxlength="9" autocomplete="off" placeholder="Введите число" value="{limit_vacancy}">
				</div>
				
				<label class="mt-4 mb-0" for="">Следующая заявка</label>
				<div class="input-group">
					<span class="input-group-btn">
						<button onclick="SendConfigs('next_days', $('#next_days').val());" class="btn btn-default" type="button">Изменить</button>
					</span>
					
					<input id="next_days" type="text" class="form-control" maxlength="9" autocomplete="off" placeholder="Введите число" value="{next_days}">
				</div>
				<small>Через сколько пользователь сможет создать следующую заявку. Указывается в днях.</small>
			</div>
		</div>
	</div>
	
	<div class="col-md-8">
		<div class="panel panel-default">
			<div class="panel-heading">Вакансии</div>
			<div class="panel-body" id="vacancy">
				<center>Выберите сервер</center>
			</div>
		</div>
		
		<div class="panel panel-default">
			<div class="panel-heading">Дополнительные поля</div>
			<div class="panel-body" id="fields">
				<center>Выберите сервер</center>
			</div>
		</div>
	</div>
</div>