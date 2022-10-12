<div class="col-lg-12">
	<form id="form_vacancy_send" class="block">
		<div class="block_head">Подача заявки</div>
		
		<label for="server" class="mb-0"><h5>Сервер</h5></label>
		<select name="server" class="form-control" required>
			{servers}
		</select>
		
		<label for="vacancy" class="mb-0"><h5>Вакансия</h5></label>
		<select name="vacancy" class="form-control" required>
			{vacancy}
		</select>
		
		<div id="custom"></div>
		
		<div class="form-check mt-4">
			<input class="form-check-input" id="readyrules" type="checkbox">
			<label class="form-check-label" for="readyrules">
				Я ознакомлен(-а) с <a target="_blank" href="../pages/rules">правилами</a> проекта и согласен(-а) с ними.
			</label>
		</div>
		
		<button type="submit" class="btn btn-primary mt-2" disabled>Отправить</button>
	</form>
</div>